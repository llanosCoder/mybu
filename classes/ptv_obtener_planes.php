<?php
    $planes = new Planes();

    class Planes{
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
            $this->obtener_planes();
        }
        protected function set_conexion($host, $user, $pass, $bd){
            $conexion = new Conexion();
            $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
            $this->sql_con->set_charset('utf8');
        }

        protected function obtener_planes(){
            $consulta = "SELECT p.plan_pago_id as id, p.plan_pago_nombre as nombre , p.plan_pago_codigo as codigo, p.plan_pago_cuota as cuota,
                        p.plan_pago_interes as interes FROM plan_pago as p";
            $this->sql_con->set_charset("utf8");
            $rs1 = $this->sql_con->query($consulta);
            if($rs1 === false) {
                trigger_error('Ha ocurrido un error');
            }

           while ($row = mysqli_fetch_array($rs1, MYSQLI_ASSOC)) {
              $dato['id'] = $row['id'];
              $dato['nombre'] = $row['nombre'];
              $dato['codigo'] = $row['codigo'];
              $dato['cuota'] = $row['cuota'];
              $dato['interes'] = $row['interes'];
              array_push($this->datos, $dato);
            }

        }

        public function __destruct(){
            echo json_encode($this->datos);
        }
    }
?>
