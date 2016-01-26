<?php

$tag = new Tags();

class Tags{

    protected $datos = array();
    protected $link, $sql_con;

    public function __construct(){
        session_start();
        require('../hosts.php');
        require('conexion_new.php');
        $hosteo = new Host();
        $hosteo->obtener_conexion(0);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $this->obtener_tags();
    }
    
    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    
    protected function obtener_tags(){
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'],$this->link['pass'],$this->link['bd']);
        $consulta_tags = "SELECT tag_id as id, tag_nombre as nombre, tag_codigo as codigo FROM tag ORDER BY tag_nombre";
        $this->sql_con->set_charset("utf8");
        $result_tag = $this->sql_con->query($consulta_tags);
        if($result_tag === false) {
            trigger_error('Ha ocurrido un error');
        } else {
            while($row_tag = $result_tag->fetch_assoc()){
                $dato = array();
                $dato['id'] = $row_tag['id'];
                $dato['nombre'] = $row_tag['nombre'];
                $dato['codigo'] = $row_tag['codigo'];
                array_push($this->datos, $dato);
            }
            $result_tag->close();
        }
    }
    
    public function __destruct(){
        echo json_encode($this->datos);
    }

}

?>