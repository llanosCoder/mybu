<?php

$variable = new Clase();

class Clase{

    protected $link, $sql_con;
    protected $datos = array();
    protected $tipo_cuenta, $empresa;

    public function __construct(){
        $this->conexion();
        $this->procesar();
        $this->obtener_parametros();
        $this->procesar();
    }

    protected function conexion(){
        session_start();
        require('../hosts.php');
        require('conexion_new.php');
        $hosteo = new Host();
        $hosteo->obtener_conexion(1);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
    }
    
    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    
    protected function obtener_parametros(){
        $this->empresa = $_SESSION['empresa'];
        $this->tipo_cuenta = $_SESSION['tipo_cuenta'];
    }
    
    protected function procesar(){
        if($this->tipo_cuenta == 1 || $this->tipo_cuenta == 2){
            $consulta_solicitudes = "SELECT cs.categoria_solicitud_id as id, u.usuario_login as usuario, e.empresa_nombre as empresa, cs.categoria_solicitud_nombre as nombre, c.categoria_nombre as padre, cs.categoria_solicitud_fecha_creacion as fecha, cs.categoria_solicitud_estado as estado, cs.categoria_solicitud_rechazo_razon as razon FROM categoria_solicitud cs LEFT JOIN categoria c ON categoria_solicitud_padre = categoria_id INNER JOIN usuario u ON cs.usuario_id = u.usuario_id INNER JOIN empresa e ON cs.empresa_id = e.empresa_id";

            $rs_solicitudes = $this->sql_con->query($consulta_solicitudes);
            if($rs_solicitudes === false){
                trigger_error("Ha ocurrido un error");
                exit();
            }else{
                $this->datos['solicitudes'] = array();
                while($row_solicitudes = $rs_solicitudes->fetch_assoc()){
                    $dato = array();
                    $dato['id'] = $row_solicitudes['id'];
                    $dato['usuario'] = $row_solicitudes['usuario'];
                    $dato['empresa'] = $row_solicitudes['empresa'];
                    $dato['nombre'] = $row_solicitudes['nombre'];
                    $dato['padre'] = $row_solicitudes['padre'];
                    $dato['fecha'] = $row_solicitudes['fecha'];
                    $dato['estado'] = $row_solicitudes['estado'];
                    $dato['razon'] = $row_solicitudes['razon'];
                    array_push($this->datos['solicitudes'], $dato);
                }
                $this->datos['tipo_cuenta'] = $this->tipo_cuenta;
            }
        }
    }
    
    public function __destruct(){
        echo json_encode($this->datos);
    }

}

?>