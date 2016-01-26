<?php

    $trendings = new Trending();

    class Trending{
        
        protected $link, $sql_con;
        protected $datos = array(), $opcion;
        
        public function __construct(){
            session_start();
            require('../hosts.php');
            require('conexion_new.php');
            $hosteo = new Host();
            $hosteo->obtener_conexion(0);
            $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
            $this->opcion = $_POST['option'];
            switch($this->opcion){
                case 1:
                    $this->obtener_1();
                    break;
                case 2:
                    $this->obtener_2();
                    break;
                case 3:
                    $this->obtener_populares();
                    break;
            }
        }
        
        protected function set_conexion($host, $user, $pass, $bd){
            $conexion = new Conexion();
            $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
            $this->sql_con->set_charset('utf8');
        }
        
        protected function obtener_1(){
            $consulta = "SELECT t.tag_nombre as nombre, t.tag_codigo as codigo, v.venta_cantidad as ventas FROM tag t INNER JOIN venta_acumulada v ON t.tag_id = v.tag_id ORDER BY v.venta_cantidad DESC LIMIT 5";
            $this->sql_con->set_charset("utf8");
            $rs = $this->sql_con->query($consulta);
            if($rs === false) {
                trigger_error('Ha ocurrido un error');
            } else {
                while($row = $rs->fetch_assoc()){
                    $dato = array();
                    $dato['nombre'] = $row['nombre'];
                    $dato['codigo'] = $row['codigo'];
                    $dato['action'] = $row['ventas'];
                    array_push($this->datos, $dato);
                }
            }
        }
        
        protected function obtener_2(){
            $consulta = "SELECT t.tag_nombre as nombre, t.tag_codigo as codigo, count(tp.tag_id) as usos FROM tag t INNER JOIN tag_producto tp ON t.tag_id = tp.tag_id GROUP BY tp.tag_id ORDER BY usos DESC LIMIT 5";
            
            $result = $this->sql_con->query($consulta);
            if($result === false) {
                trigger_error('Ha ocurrido un error');
            } else {
                 while($row = $result->fetch_assoc()){
                    $dato = array();
                    $dato['nombre'] = $row['nombre'];
                    $dato['codigo'] = $row['codigo'];
                    $dato['action'] = $row['usos'];
                    array_push($this->datos, $dato);
                }
            }
        }
        
        public function __destruct(){
            echo json_encode($this->datos);
        }
        
    }

?>