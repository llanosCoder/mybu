<?php

$clase = new Clase();

Class Clase{

    protected $link, $sql_con;
    protected $categorias = array();
    protected $resultado = array();

    public function __construct(){
        session_start();
        require('../hosts.php');
        require('conexion_new.php');
        $hosteo = new Host();
        $hosteo->obtener_conexion(0);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $this->procesar();
    }
    
    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    
    protected function tiene_productos($cat){
        $consulta = "SELECT count(*) as cont FROM categoria_producto WHERE categoria_id = (SELECT categoria_id FROM categoria WHERE categoria_descripcion = '$cat')";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return false;
        }else{
            $row = $rs->fetch_assoc();
            if($row['cont'] > 0){
                return true;
            }else{
                return false; 
            }
        }
    }
    
    protected function procesar(){
        if($_SESSION['tipo_cuenta'] != 1){
            exit();
        }
        $this->categorias = $_POST['categorias'];
        $num_errores = 0;
        for($i = 0; $i < count($this->categorias); $i++){
            if(!$this->tiene_productos($this->categorias[$i])){
                if(!$this->eliminar_categoria($this->categorias[$i])){
                    $num_errores++;
                }
            }else{
                $num_errores++;
            }
        }
        $dato = array();
        if($num_errores >= count($this->categorias)){
            $dato['resultado'] = 0; //No se eliminó ninguna categoría
        }else{
            if($num_errores > 0){
                $dato['resultado'] = 2; //Se eliminaron algunas categorías
            }else{
                $dato['resultado'] = 1; //Se eliminaron todas las categorías
            }
        }
        $this->resultado = $dato;
    }
    
    protected function eliminar_categoria($categoria){
        $eliminar = $this->sql_con->prepare("DELETE FROM categoria WHERE categoria_descripcion = ?");
        $eliminar->bind_param('s', $categoria);
        $eliminar->execute();
        $insertadas = $this->sql_con->affected_rows;
        $eliminar->close();
        if($insertadas > 0){
            return true;
        }else{
            return false;
        }
    }
    
    public function __destruct(){
        echo json_encode($this->resultado);
    }
}

?>