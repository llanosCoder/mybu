<?php

    $filtro = new Filtros();
    
    class Filtros{
    
        protected $link, $sql_con, $datos = array();
        protected $conexion;
    
        public function __construct(){
            session_start();
            $fuente = $_POST['fuente'];
            require('../hosts.php');
            require('conexion_new.php');
            $hosteo = new Host();
            switch($fuente){
                case 1:
                    $hosteo->obtener_conexion(0);
                    break;
                case 2:
                    $hosteo->obtener_conexion(2);
                    break;
            }
            $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
            $this->obtener_filtros();
        }
        
        protected function set_conexion($host, $user, $pass, $bd){
            $conexion = new Conexion();
            $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
            $this->sql_con->set_charset('utf8');
        }
        
        protected function obtener_filtros(){
            
            $consulta_filtros = "SELECT filtro_codigo as codigo, filtro_nombre as nombre FROM filtro_oferta";
            $result_filtros = $this->sql_con->query($consulta_filtros);
            if($result_filtros === false){
                trigger_error("Ha ocurrido un error");
            }else{
                while($row = $result_filtros->fetch_assoc()){
                    $dato = array();
                    $dato['codigo'] = $row['codigo'];
                    $dato['nombre'] = $row['nombre'];
                    array_push($this->datos, $dato);
                }
            }
        }
        
        public function __destruct(){
            echo json_encode($this->datos);
        }
    
    }

?>