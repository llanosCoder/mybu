<?php

    $promocion = new Promociones();    
    
    class Promociones{
        
        protected $link;
        protected $resultado = 0;
        protected $sql_con;
        protected $datos = array();
        
        public function __construct(){
            session_start();
            require('../hosts.php');
            require('conexion_new.php');
            $hosteo = new Host();
            $hosteo->obtener_conexion(0);
            $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
            $this->eliminar_promocion();
        }
        
        protected function set_conexion($host, $user, $pass, $bd){
            $conexion = new Conexion();
            $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
            $this->sql_con->set_charset('utf8');
        }
        
        protected function eliminar_promocion(){
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'],$this->link['pass'],$this->link['bd']);
            $this->datos['tId'] = mysqli_real_escape_string($this->sql_con, $_POST['tId']);
            $this->sql_con->set_charset("utf8");
            $filas_afectadas = 0;
            $eliminar_promocion = $this->sql_con->prepare("DELETE FROM tag WHERE tag_id = ?");
            $eliminar_promocion->bind_param('i', $this->datos['tId']);
            $eliminar_promocion->execute(); 
            $eliminacion_exitosa = $this->sql_con->affected_rows;
            if($eliminacion_exitosa > 0){
                $filas_afectadas++;
            }
            if($filas_afectadas > 0){
                $this->resultado = 1;
            }
            $eliminar_promocion->close();
            $eliminar_asignados = $this->sql_con->prepare("DELETE FROM tag_producto WHERE tag_id = ?");
            $eliminar_asignados->bind_param('i', $this->datos['tId']);
            $eliminar_asignados->execute(); 
            $eliminar_asignados->close();
        }
        
        public function __destruct(){
            echo $this->resultado;
        }
    }
?>
            