<?php

$tag = new Tags();

class Tags{

    protected $link, $sql_con;
    protected $datos = array();
    protected $resultado = 0;
    
    public function __construct(){
        session_start();
        require('../hosts.php');
        require('conexion_new.php');
        $hosteo = new Host();
        $hosteo->obtener_conexion(0);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $this->administrar_tags();    
    }
    
    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    
    protected function administrar_tags(){
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'],$this->link['pass'],$this->link['bd']);
        
        $this->datos['id'] = mysqli_real_escape_string($this->sql_con, $_POST['tId']);
        $this->datos['nombre'] = mysqli_real_escape_string($this->sql_con, $_POST['nombre']);
        $this->datos['codigo'] = mysqli_real_escape_string($this->sql_con, $_POST['codigo']);
        include_once('sanear_string.php');
        $this->datos['codigo'] = sanear_string($this->datos['codigo']);
        if($this->datos['id'] == '0'){
            $consulta_tag = $this->sql_con->prepare("INSERT INTO tag VALUES (null, ?, ?);");
            $consulta_tag->bind_param('ss',  $this->datos['nombre'],
            $this->datos['codigo']);
            $consulta_tag->execute(); 
            if($this->sql_con->affected_rows > 0){
                $this->resultado = 1;
            }
            $consulta_tag->close();
        }else{
            $id = $this->datos['id'];
            $nombre = $this->datos['nombre'];
            $codigo = $this->datos['codigo'];
            $consulta_tag = "UPDATE tag SET tag_nombre = '$nombre', tag_codigo = '$codigo' WHERE tag_id = '$id';";
            if($this->sql_con->query($consulta_tag) === false) {
                  trigger_error('Wrong SQL: ' . $consulta_tag . ' Error: ' . $this->sql_con->error, E_USER_ERROR);
            } else {
                if($this->sql_con->affected_rows > 0){
                    $this->resultado = 1;
                }else{
                    $this->resultado = 2;
                }
            }
        }
    }
    
    public function __destruct(){
        echo $this->resultado;
    }
}

?>