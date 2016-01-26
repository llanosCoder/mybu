<?php

$tag = new Tags();

class Tags{

    protected $link, $sql_con;
    protected $productos_array = array(), $tags, $tags_array = array();
    protected $resultado = 0;

    public function __construct(){
        session_start();
        require('../hosts.php');
        require('conexion_new.php');
        $hosteo = new Host();
        $hosteo->obtener_conexion(0);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $this->preparar();
    }
    
    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    
    protected function preparar(){
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'],$this->link['pass'],$this->link['bd']);
        $this->productos_array = $_POST['productos'];
        $this->tags_array = $_POST['tags'];
        //Limpieza de valores de entrada
        /*for($i = 0; $i < count($this->productos_array); $i++){
            $this->productos_array = mysqli_real_escape_string($this->sql_con, $this->productos_array[$i]);
            echo $i;    
        }*/
        for($i = 0; $i < count($this->tags_array); $i++){
            $this->tags_array[$i] = mysqli_real_escape_string($this->sql_con, $this->tags_array[$i]);
            if($this->tags_array == ''){
                array_splice($this->tags_array, $i, 1);
            }
        }
        
        //Verificar si tags existen
        for($i = 0; $i < count($this->tags_array); $i++){
            include_once('sanear_string.php');
            $tag_codigo = sanear_string($this->tags_array[$i]);
            $consulta_tag_codigo = "SELECT tag_id as id FROM tag WHERE tag_codigo='$tag_codigo'";
            $result_tag_codigo = $this->sql_con->query($consulta_tag_codigo);
            if($result_tag_codigo === false) {
                trigger_error('Ha ocurrido un error');
            } else {
                $id_tag = 0;
                while($row_tag_codigo = $result_tag_codigo->fetch_assoc()){
                    $id_tag = $row_tag_codigo['id'];
                }
                if($id_tag > 0){
                    $this->asignar_productos($id_tag);
                }else{
                    $insercion_tag = $this->sql_con->prepare("INSERT INTO tag VALUES (null, ?, ?)");
                    $insercion_tag->bind_param('ss', $tag_codigo,
                    $tag_codigo);
                    $insercion_tag->execute(); 
                    $id_tag = $this->sql_con->insert_id;
                    $insercion_tag->close();
                    $this->asignar_productos($id_tag);
                }
            }
        }
    }
    
    private function asignar_productos($id_tag){
        for($j = 0; $j < count($this->productos_array); $j++){
            $producto_codigo = $this->productos_array[$j];
            $consulta_producto_codigo = "SELECT producto_id as id FROM producto WHERE producto_codigo='$producto_codigo'";
            $result_producto_codigo = $this->sql_con->query($consulta_producto_codigo);
            if($result_producto_codigo === false) {
                trigger_error('Ha ocurrido un error');
            } else {
                while($row_producto_codigo = $result_producto_codigo->fetch_assoc()){
                    $producto_id = $row_producto_codigo['id'];
                    $consulta_duplicidad = "SELECT count(*) as cont FROM tag_producto WHERE producto_id=$producto_id AND tag_id=$id_tag";
                    $result_duplicidad = $this->sql_con->query($consulta_duplicidad);
                    if($result_duplicidad === false) {
                        trigger_error('Ha ocurrido un error');
                    } else {
                        $contador_inserciones = 0;
                        while($row_duplicidad = $result_duplicidad->fetch_assoc()){
                            if($row_duplicidad['cont'] == 0){
                                $insercion_tag_producto = $this->sql_con->prepare("INSERT INTO tag_producto VALUES (null, ?, ?)");
                                $insercion_tag_producto->bind_param('ii', $id_tag,
                                $producto_id);
                                $insercion_tag_producto->execute(); 
                                $insercion_tag_producto->close();
                                $this->resultado = 1;
                                $contador_inserciones++;
                            }
                        }
                        if($contador_inserciones == 0){
                            $this->resultado = 2;
                        }
                    }
                    
                }
            }
        }
    }
    
    public function __destruct(){
        echo $this->resultado;
    }
}

?>