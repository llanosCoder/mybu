<?php

    $orden = new Ordenes();
    
    class Ordenes{
    
        protected $link, $sql_con;
        protected $datos = array();
        
        public function __construct(){
            require('conexion.php');
            $this->link = Conectarse();
            $this->obtener_ordenes();
        }
        
        protected function obtener_ordenes(){
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
            $this->sql_con->set_charset('utf8');
            $consulta_ordenes = "SELECT oc.orden_voucher as voucher, oc.orden_fecha_creacion as f_creacion, oc.orden_fecha_vencimiento as f_vencimiento, oc.orden_total_bruto as total_bruto, oc.orden_total_descuentos as descuentos, oc.orden_total as total FROM orden_compra oc WHERE oc.orden_estado = 0 AND oc.orden_fecha_vencimiento > NOW() ORDER BY oc.orden_fecha_creacion";
            $result_ordenes = $this->sql_con->query($consulta_ordenes);
            if($result_ordenes === false){
                trigger_error("Ha ocurrido un error");
            }else{
                while($row_ordenes = $result_ordenes->fetch_assoc()){
                    $dato = array();
                    $dato['voucher'] = $row_ordenes['voucher'];
                    $dato['f_creacion'] = $row_ordenes['f_creacion'];
                    $dato['f_vencimiento'] = $row_ordenes['f_vencimiento'];
                    $dato['total_bruto'] = $row_ordenes['total_bruto'];
                    $dato['descuentos'] = $row_ordenes['descuentos'];
                    $dato['total'] = $row_ordenes['total'];
                    array_push($this->datos, $dato);
                }
            }
        }
        
        public function __destruct(){
            echo json_encode($this->datos);
        }
    
    }

?>