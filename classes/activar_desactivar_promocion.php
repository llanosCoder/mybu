<?php
    $promocion = new Promociones();

    class Promociones{
        
        protected $link;
        protected $id;
        protected $estado;
        protected $resultado;
        protected $sql_con;
        
        public function __construct(){
            session_start();
            require('../hosts.php');
            require('conexion_new.php');
            $hosteo = new Host();
            $hosteo->obtener_conexion(0);
            $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
            $this->activar_desactivar_promocion();
        }
        
        protected function set_conexion($host, $user, $pass, $bd){
            $conexion = new Conexion();
            $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
            $this->sql_con->set_charset('utf8');
        }
        
        protected function activar_desactivar_promocion(){
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'],$this->link['pass'],$this->link['bd']);
            $this->id = mysqli_real_escape_string($this->sql_con, $_POST['pId']);
            $this->estado = mysqli_real_escape_string($this->sql_con, $_POST['estado']);
            if($this->estado == '0'){
                $nuevo_estado = '1';
            }else{
                $nuevo_estado = '0';
            }
            $consulta = "UPDATE promocion SET promocion_estado = $nuevo_estado WHERE promocion_id = $this->id";
            $rs = $this->sql_con->query($consulta);
            if($rs === false) {
                trigger_error('Ha ocurrido un error');
            } else {
                $this->resultado = $this->sql_con->affected_rows;
            }
        }
        
        public function __destruct(){
            echo $this->resultado;
        }
    }
?>