<?php

$localidad = new Localidades();

class Localidades{

    protected $link, $sql_con;
    protected $accion, $datos = array();

    public function __construct(){
        session_start();
        require('../hosts.php');
        require('conexion_new.php');
        $hosteo = new Host();
        $hosteo->obtener_conexion(1);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $this->procesar();
    }

    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    
    protected function procesar(){
        $this->accion = $_POST['accion'];
        switch($this->accion){
            case 1:
                $consulta = "SELECT pais_id as id, pais_nombre as nombre FROM pais ORDER BY pais_nombre ASC";
                $this->consultar($consulta);
            break;
            case 2:
                $pais = mysqli_real_escape_string($this->sql_con, $_POST['opcion']);
                $consulta = "SELECT region_id as id, region_nombre as nombre FROM region WHERE pais_id = $pais ORDER BY region_id ASC";
                $this->consultar($consulta);
            break;
            case 3:
                $region = mysqli_real_escape_string($this->sql_con, $_POST['opcion']);
                $consulta = "SELECT comuna_id as id, comuna_nombre as nombre FROM comuna WHERE region_id = $region ORDER BY comuna_nombre ASC";
                $this->consultar($consulta);
            break;
        }
    }
    
    protected function consultar($consulta){
        $rs = $this->sql_con->query($consulta);
        //echo $consulta;
        if($rs === false){
            trigger_error('Wrong SQL: ' . $consulta . ' Error: ' . $this->sql_con->error, E_USER_ERROR);
            trigger_error("Ha ocurrido un error");
        }else{
            $this->datos['resultado'] = array();
            while($row = $rs->fetch_assoc()){
                $dato = array();
                foreach($row as $indice=>$valor){
                    $dato[$indice] = $valor;
                }
                array_push($this->datos['resultado'], $dato);
            }
        }
    }
    
    public function __destruct(){
        echo json_encode($this->datos);
    }

}

?>