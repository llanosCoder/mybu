<?php

    $oferta = new Ofertas();    
    
    class Ofertas{
        
        protected $link;
        protected $resultado = 1;
        protected $sql_con;
        protected $datos = array();
        
        public function __construct(){
           session_start();
           require('../hosts.php');
           require('conexion_new.php');
           $hosteo = new Host();
           $hosteo->obtener_conexion(0);
           $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
           $this->eliminar_oferta();
            $hosteo = new Host();
           $hosteo->obtener_conexion(2);
           $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
           $this->eliminar_oferta_global();
            
        }
        
        protected function set_conexion($host, $user, $pass, $bd){
            $conexion = new Conexion();
            $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
            $this->sql_con->set_charset('utf8');
        }
        
        protected function eliminar_oferta(){
            $this->datos['oferta_id'] = mysqli_real_escape_string($this->sql_con, $_POST['pId']);
            $filas_afectadas = 0;
            $eliminar_oferta = $this->sql_con->prepare("DELETE FROM oferta WHERE oferta_id = ?");
            $eliminar_oferta->bind_param('i', $this->datos['oferta_id']);
            $eliminar_oferta->execute(); 
            $eliminacion_exitosa = $this->sql_con->affected_rows;
            if($eliminacion_exitosa > 0){
                $filas_afectadas++;
            }
            $eliminar_oferta->close();
            if($filas_afectadas > 0){
                $eliminar_oferta_sucursal = $this->sql_con->prepare("DELETE FROM oferta_sucursal WHERE oferta_id = ?");
                $eliminar_oferta_sucursal->bind_param('i', $this->datos['oferta_id']);
                $eliminar_oferta_sucursal->execute(); 
                $eliminacion_exitosa = $this->sql_con->affected_rows;
                if($eliminacion_exitosa > 0){
                    $filas_afectadas++;
                }
                $eliminar_oferta_sucursal->close();
            }
            $this->resultado = $filas_afectadas;
        }
        
        protected function eliminar_oferta_global(){
            $this->datos['oferta_id'] = mysqli_real_escape_string($this->sql_con, $_POST['pId']);
            $filas_afectadas = 0;
            $eliminar_oferta = $this->sql_con->prepare("DELETE FROM oferta WHERE oferta_id = ?");
            $eliminar_oferta->bind_param('i', $this->datos['oferta_id']);
            $eliminar_oferta->execute(); 
            $eliminacion_exitosa = $this->sql_con->affected_rows;
            if($eliminacion_exitosa > 0){
                $filas_afectadas++;
            }
            $eliminar_oferta->close();
            $this->resultado = $filas_afectadas;
        }
        
        public function __destruct(){
            echo $this->resultado;
        }
    }
?>