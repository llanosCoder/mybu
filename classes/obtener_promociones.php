<?php
    $promocion = new Promociones();

    class Promociones{
        
        protected $link;
        protected $id;
        protected $datos = array();
        protected $sql_con;
        
        public function __construct(){
            session_start();
            require('../hosts.php');
            require('conexion_new.php');
            $hosteo = new Host();
            $hosteo->obtener_conexion(0);
            $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
            $this->obtener_promociones();
        }
        
        protected function set_conexion($host, $user, $pass, $bd){
            $conexion = new Conexion();
            $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
            $this->sql_con->set_charset('utf8');
        }
        
        protected function obtener_promociones(){
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'],$this->link['pass'],$this->link['bd']);
            //$this->sucursal_id = mysqli_real_escape_string($this->sql_con, $_POST['sId']);
            $this->sucursal_id = $_SESSION['sucursal'];
            $consulta = "SELECT p.promocion_id as pid, p.promocion_cantidad as cantidad, p.promocion_descripcion as descripcion, p.promocion_oferta_tipo as tipo_promocion, p.promocion_descuento as descuento, p.promocion_precio as precio, p.promocion_stock as stock, p.promocion_estado as estado, p.promocion_fecha_inicio as f_inicio, p.promocion_fecha_termino as f_termino, p.promocion_tipo as tipo FROM promocion p LEFT JOIN promocion_sucursal s
            ON s.promocion_id = p.promocion_id WHERE s.sucursal_id = $this->sucursal_id ORDER BY estado DESC, descripcion ASC";
             $this->sql_con->set_charset("utf8");
            $rs = $this->sql_con->query($consulta);
            if($rs === false) {
                trigger_error('Ha ocurrido un error');
            } else {
                while ($row = $rs->fetch_assoc()) {
                    $dato = array();
                    $dato['id'] = $row['pid'];
                    $dato['cantidad'] = $row['cantidad'];
                    $dato['descripcion'] = $row['descripcion'];
                    $dato['tipo_promocion'] = $row['tipo_promocion'];
                    $dato['descuento'] = $row['descuento'];
                    $dato['precio'] = $row['precio'];
                    $dato['stock'] = $row['stock'];
                    $dato['estado'] = $row['estado'];
                    $dato['f_inicio'] = $row['f_inicio'];
                    $dato['f_termino'] = $row['f_termino'];
                    array_push($this->datos, $dato);
                }
            }
        }
        
        public function __destruct(){
            echo json_encode($this->datos);
        }
    }
?>