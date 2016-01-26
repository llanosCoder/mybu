<?php

    $orden = new Ordenes();
    
    class Ordenes{
    
        protected $link, $sql_con;
        protected $datos = array();
        protected $tipo_ordenes;
        protected $empresa;
        
        public function __construct(){
            session_start();
            require('../hosts.php');
            require('conexion_new.php');
            $hosteo = new Host();
            $hosteo->obtener_conexion(1);
            $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
            $this->obtener_ordenes();
        }
        
        protected function obtener_parametros(){
            $this->tipo_ordenes = $_POST['orden'];
            $this->empresa = $_SESSION['empresa'];
        }
        
        protected function set_conexion($host, $user, $pass, $bd){
            $conexion = new Conexion();
            $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
            $this->sql_con->set_charset('utf8');
        }
        
        protected function obtener_ordenes(){
            $this->obtener_parametros();
            $consulta_ordenes = $this->generar_consulta();
            $result_ordenes = $this->sql_con->query($consulta_ordenes);
            if($result_ordenes === false){
                trigger_error("Ha ocurrido un error");
            }else{
                while($row_ordenes = $result_ordenes->fetch_assoc()){
                    $dato = array();
                    foreach($row_ordenes as $indice=>$valor){
                        $dato[$indice] = $valor;
                    }
                    /*$dato['solicitante_id'] = $row_ordenes['solicitante_id'];
                    $dato['solicitante'] = $row_ordenes['solicitante'];
                    $dato['solicitada'] = $row_ordenes['solicitada'];
                    $dato['voucher'] = $row_ordenes['voucher'];
                    $dato['f_creacion'] = $row_ordenes['f_creacion'];
                    $dato['f_vencimiento'] = $row_ordenes['f_vencimiento'];
                    $dato['total_bruto'] = $row_ordenes['total_bruto'];
                    $dato['descuentos'] = $row_ordenes['descuentos'];
                    $dato['total'] = $row_ordenes['total'];
                    if(isset($row_ordenes['razon']))
                        $dato['razon'] = $row_ordenes['razon'];*/
                    array_push($this->datos, $dato);
                }
            }
        }
        
        protected function generar_consulta(){
            switch($this->tipo_ordenes){
                case 0:
                    $consulta = "SELECT e.empresa_id as solicitante_id, e.empresa_nombre as solicitante, oc.empresa_solicitada as solicitada, oc.orden_voucher as voucher, oc.orden_fecha_creacion as f_creacion, oc.orden_fecha_vencimiento as f_vencimiento, oc.orden_total_bruto as total_bruto, oc.orden_total_descuentos as descuentos, oc.orden_total as total FROM orden_compra oc INNER JOIN empresa e ON oc.empresa_solicitante = e.empresa_id WHERE oc.orden_estado = 0 AND oc.orden_fecha_vencimiento > NOW() AND empresa_solicitante = $this->empresa ORDER BY oc.orden_fecha_creacion";
                    break;
                    
                case 1:
                    $consulta = "SELECT e.empresa_id as solicitante_id, e.empresa_nombre as solicitante, oc.empresa_solicitada as solicitada, oc.orden_voucher as voucher, oc.orden_fecha_creacion as f_creacion, oc.orden_fecha_vencimiento as f_vencimiento, oc.orden_total_bruto as total_bruto, oc.orden_total_descuentos as descuentos, oc.orden_total as total FROM orden_compra oc INNER JOIN empresa e ON oc.empresa_solicitante = e.empresa_id WHERE oc.orden_estado = 0 AND oc.orden_fecha_vencimiento < NOW() AND empresa_solicitante = $this->empresa ORDER BY oc.orden_fecha_creacion";
                    break;
                case 2:
                    $consulta = "SELECT e.empresa_id as solicitante_id, e.empresa_nombre as solicitante, oc.empresa_solicitada as solicitada, oc.orden_voucher as voucher, oc.orden_fecha_creacion as f_creacion, oc.orden_fecha_vencimiento as f_vencimiento, oc.orden_total_bruto as total_bruto, oc.orden_total_descuentos as descuentos, oc.orden_total as total FROM orden_compra oc INNER JOIN empresa e ON oc.empresa_solicitante = e.empresa_id WHERE oc.orden_estado = 0 AND oc.orden_fecha_vencimiento < NOW() AND empresa_solicitada = $this->empresa ORDER BY oc.orden_fecha_creacion";
                    break;
                case 3:
                    $consulta = "SELECT e.empresa_id as solicitante_id, e.empresa_nombre as solicitante, oc.empresa_solicitada as solicitada, oc.orden_voucher as voucher, oc.orden_fecha_creacion as f_creacion, oc.orden_fecha_vencimiento as f_vencimiento, oc.orden_total_bruto as total_bruto, oc.orden_total_descuentos as descuentos, oc.orden_total as total FROM orden_compra oc INNER JOIN empresa e ON oc.empresa_solicitante = e.empresa_id WHERE oc.orden_estado = 0 AND oc.orden_fecha_vencimiento > NOW() AND empresa_solicitada = $this->empresa ORDER BY oc.orden_fecha_creacion";
                    break;
                case 4:
                    $hosteo = new Host();
                    $hosteo->obtener_conexion(0);
                    $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
                    $consulta = "SELECT e.empresa_id as solicitante_id, e.empresa_nombre as solicitante, oc.empresa_solicitada as solicitada, oc.orden_voucher as voucher, oc.orden_fecha_creacion as f_creacion, oc.orden_fecha_vencimiento as f_vencimiento, oc.orden_total_bruto as total_bruto, oc.orden_total_descuentos as descuentos, oc.orden_total as total, ore.orden_rechazo as razon FROM orden_compra oc LEFT JOIN orden_rechazo ore ON oc.orden_id = ore.orden_id LEFT JOIN empresa e ON oc.empresa_solicitante = e.empresa_id WHERE oc.orden_estado = 3 AND empresa_solicitada = $this->empresa ORDER BY oc.orden_fecha_creacion";
                    break;
                case 5:
                    $hosteo = new Host();
                    $hosteo->obtener_conexion(0);
                    $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
                    $consulta = "SELECT e.empresa_id as solicitante_id, e.empresa_nombre as solicitante, oc.empresa_solicitada as solicitada, oc.orden_voucher as voucher, oc.orden_fecha_creacion as f_creacion, oc.orden_fecha_vencimiento as f_vencimiento, oc.orden_total_bruto as total_bruto, oc.orden_total_descuentos as descuentos, oc.orden_total as total, ore.orden_rechazo as razon FROM orden_compra oc LEFT JOIN orden_rechazo ore ON oc.orden_id = ore.orden_id LEFT JOIN empresa e ON oc.empresa_solicitante = e.empresa_id WHERE oc.orden_estado = 3 AND empresa_solicitante = $this->empresa ORDER BY oc.orden_fecha_creacion";
                    break;
                case 6:
                    $consulta = "SELECT e.empresa_id as solicitante_id, e.empresa_nombre as solicitante, oc.empresa_solicitada as solicitada, oc.orden_voucher as voucher, oc.orden_fecha_creacion as f_creacion, oc.orden_fecha_vencimiento as f_vencimiento, oc.orden_total_bruto as total_bruto, oc.orden_total_descuentos as descuentos, oc.orden_total as total FROM orden_compra oc INNER JOIN empresa e ON oc.empresa_solicitante = e.empresa_id WHERE oc.orden_estado = 1 AND empresa_solicitada = $this->empresa ORDER BY  oc.orden_fecha_creacion";
                    break;
                case 7:
                    $consulta = "SELECT e.empresa_id as solicitante_id, e.empresa_nombre as solicitante, oc.empresa_solicitada as solicitada, oc.orden_voucher as voucher, oc.orden_fecha_creacion as f_creacion, oc.orden_fecha_vencimiento as f_vencimiento, oc.orden_total_bruto as total_bruto, oc.orden_total_descuentos as descuentos, oc.orden_total as total FROM orden_compra oc INNER JOIN empresa e ON oc.empresa_solicitante = e.empresa_id WHERE oc.orden_estado = 1 AND empresa_solicitante = $this->empresa ORDER BY oc.orden_fecha_creacion";
                    break;
                case 8:
                    $consulta = "SELECT e.empresa_id as solicitante_id, e.empresa_nombre as solicitante, oc.empresa_solicitada as solicitada, oc.orden_voucher as voucher, oc.orden_fecha_creacion as f_creacion, oc.orden_fecha_vencimiento as f_vencimiento, oc.orden_total_bruto as total_bruto, oc.orden_total_descuentos as descuentos, oc.orden_total as total FROM orden_compra oc INNER JOIN empresa e ON oc.empresa_solicitada = e.empresa_id WHERE oc.orden_estado = 4 AND empresa_solicitante = $this->empresa ORDER BY oc.orden_fecha_creacion";
                    break;
            }
            return $consulta;
        }
        
        public function __destruct(){
            echo json_encode($this->datos);
        }
    
    }

?>