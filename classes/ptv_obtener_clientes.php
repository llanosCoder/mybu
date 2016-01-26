<?php
    
    $clientes = new Clientes();
    
    class Clientes{
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
            $this->obtener_clientes();
        }
        protected function set_conexion($host, $user, $pass, $bd){
            $conexion = new Conexion();
            $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
            $this->sql_con->set_charset('utf8');
        }

        protected function obtener_clientes(){
            $consulta = "SELECT l.linea_credito_id as linea, c.cliente_rut as rut, c.cliente_nombre as nombre, c.cliente_apellido_paterno as paterno, c.cliente_apellido_materno as materno,
                          l.linea_credito_monto_autorizado as autorizado, l.linea_credito_fecha_facturacion as facturacion, l.linea_credito_fecha_pago as fechaPago, l.linea_credito_saldo_favor as aFavor
                          FROM `cliente` AS c
                          JOIN linea_credito AS l ON l.cliente_id=c.cliente_id";
            $this->sql_con->set_charset("utf8");
            $rs1 = $this->sql_con->query($consulta);
            if($rs1 === false) {
                trigger_error('Ha ocurrido un error');
            }
           while ($row = mysqli_fetch_array($rs1, MYSQLI_ASSOC)) {
              $dato['linea'] = $row['linea'];
              $dato['rut'] = $row['rut'];
              $dato['nombre'] = $row['nombre'];
              $dato['paterno'] = $row['paterno'];
              $dato['materno'] = $row['materno'];
              $dato['autorizado'] = $row['autorizado'];
              $dato['cupo'] = $this->obtener_cupo_credito($row['rut'],$row['autorizado']);
              $dato['facturacion'] = $row['facturacion'];
              $dato['fechaPago'] = $row['fechaPago'];
              $dato['aFavor'] = $row['aFavor'];
              array_push($this->datos, $dato);
            }

        }
        
        protected function obtener_cupo_credito($rut, $monto){
            //$this->set_host(0);
            $consulta = "SELECT SUM(cu.cuota_monto) AS usado FROM cuota cu JOIN venta_credito vc ON cu.venta_credito_id = vc.venta_credito_id JOIN linea_credito lc ON vc.linea_credito_id = lc.linea_credito_id JOIN cliente c ON lc.cliente_id = c.cliente_id WHERE cu.cuota_estado = 0 AND c.cliente_rut = '$rut'";
            $this->sql_con->set_charset("utf8");
            $result =$this->sql_con->query($consulta);
            if($result === false){
                trigger_error('Wrong SQL: ' . $consulta . ' Error: ' . $this->sql_con->error, E_USER_ERROR);
                
            }
            while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                $monto -= $row['usado'];
            }
            return $monto;
        }
        
        public function __destruct(){
            echo json_encode($this->datos);
        }
    }
?>