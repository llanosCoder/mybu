<?php
    $promociones = new Promociones();

    class Promociones{
        protected $link, $sql_con;
        protected $datos = array();

        public function __construct(){
            session_start();
            $this->sucursal = $_SESSION["sucursal"];
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
            $consulta = "SELECT p.promocion_especial_id as id, p.promocion_especial_descripcion as nombre, p.promocion_especial_porcentaje as porcentaje, p.promocion_especial_tipo_id as tipo
                         FROM `promocion_especial` as p WHERE p.promocion_especial_estado=1";
            $this->sql_con->set_charset("utf8");
            $rs1 = $this->sql_con->query($consulta);
            if($rs1 === false) {
                trigger_error('Ha ocurrido un error');
            }
           while ($row = mysqli_fetch_array($rs1, MYSQLI_ASSOC)) {
              $dato['id'] = $row['id'];
              $dato['nombre'] = $row['nombre'];
              $dato['porcentaje'] = $row['porcentaje'];
              $dato['tipo'] = $row['tipo'];
              array_push($this->datos, $dato);
            }

        }

        public function __destruct(){
            echo json_encode($this->datos);
        }
    }
?>
