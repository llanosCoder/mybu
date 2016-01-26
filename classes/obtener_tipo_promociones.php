<?php

    $tipo_promocion = new TipoPromociones();
    
    class TipoPromociones{
    
        protected $link, $sql_con;
        protected $datos = array();
        
        public function __construct(){
            session_start();
            require('../hosts.php');
            require('conexion_new.php');
            $hosteo = new Host();
            $hosteo->obtener_conexion(0);
            $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
            
            $this->obtener_tipo_promocion();
        }
        
       protected function set_conexion($host, $user, $pass, $bd){
           $conexion = new Conexion();
           $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
           $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
           $this->sql_con->set_charset('utf8');
       }
        
       protected function obtener_tipo_promocion(){
            $consulta = "SELECT tipo_oferta_promocion_id as id, tipo_oferta_promocion_nombre as nombre FROM tipo_oferta_promocion";
            $rs_consulta = $this->sql_con->query($consulta);
            if($rs_consulta === false){
                trigger_error("Ha ocurrido un error");
            }else{
                while($row_consulta = $rs_consulta->fetch_assoc()){
                    $dato = array();
                    foreach($row_consulta as $indice=>$valor){
                        $dato[$indice] = $row_consulta[$indice];
                    }
                    array_push($this->datos, $dato);
                }
            }
        }
        
        public function __destruct(){
            echo json_encode($this->datos);
        }
        
    }

?>