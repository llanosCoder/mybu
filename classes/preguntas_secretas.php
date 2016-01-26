<?php

$pregunta = new Preguntas();

class Preguntas{

    protected $link, $sql_con;
    protected $datos = array();
    
    protected function obtener_preguntas_secretas(){
        $consulta = "SELECT pregunta_secreta_id as value, pregunta_secreta_nombre as pregunta FROM pregunta_secreta ORDER BY pregunta_secreta_id";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            trigger_error('Wrong SQL: ' . $consulta . ' Error: ' . $this->sql_con->error, E_USER_ERROR);
            $this->datos['resultado'] = 0;
        }else{
            $this->datos['resultado'] = 1;
            $this->datos['preguntas'] = array();
            while($row = $rs->fetch_assoc()){
                $dato = array();
                foreach($row as $indice=>$valor){
                    $dato[$indice] = $valor;
                }
                array_push($this->datos['preguntas'], $dato);
            }
        }
    }
    
    protected function procesar(){
        $accion = $_POST['accion'];
        switch ($accion) {
            case 1:
                $this->obtener_preguntas_secretas();
                break;
        }
    }
    
    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    
    public function __construct(){
        session_start();
        require('../hosts.php');
        require('conexion_new.php');
        $hosteo = new Host();
        $hosteo->obtener_conexion(1);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $this->procesar();
    }

    public function __destruct(){
        echo json_encode($this->datos);
    }
}

?>