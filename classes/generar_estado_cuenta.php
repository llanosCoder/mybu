<?php

$credito = new Creditos();

class Creditos{

    protected $link, $sql_con;
    protected $accion, $usuario;
    protected $datos = array();
    protected $pdf, $x, $y, $mid = 100;
    protected $total_a_pagar = 0, $proxima_cuota = 0;
    protected $resultado = array();
    
    public function __construct(){
        session_start();
        require('../hosts.php');
        require('conexion_new.php');
        $hosteo = new Host();
        $hosteo->obtener_conexion(0);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $this->procesar();
    }

    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    
    protected function procesar(){
        $this->accion = mysqli_real_escape_string($this->sql_con, $_POST['accion']);
        switch($this->accion){
            case 1:
                $this->usuario = mysqli_real_escape_string($this->sql_con, $_POST['cId']);
                $u_id = $this->obtener_id_cliente($this->usuario);
                $this->obtener_estado_cuenta($u_id);
                $this->generar_PDF();
                break;
        }
    }
    
    protected function obtener_cupo_credito($rut, $monto){
        $consulta = "SELECT SUM(cu.cuota_monto) AS usado FROM cuota cu JOIN venta_credito vc ON cu.venta_credito_id = vc.venta_credito_id JOIN linea_credito lc ON vc.linea_credito_id = lc.linea_credito_id JOIN cliente c ON lc.cliente_id = c.cliente_id WHERE cu.cuota_estado = 0 AND c.cliente_rut = '$rut'";
        $rs = $this->sql_con->query($consulta);
        while($row = $rs->fetch_assoc()){
            $monto -= $row['usado'];
        }
        return $monto;
    }
    
    protected function obtener_estado_cuenta($usuario){
        $consulta = "SELECT lc.linea_credito_monto_autorizado as monto_autorizado, pc.plan_credito_costo_mantencion as costo_fijo, pc.plan_credito_costo_uso as costo_uso, lc.linea_credito_fecha_facturacion as f_facturacion, lc.linea_credito_fecha_pago as f_pago, lc.linea_credito_saldo_favor as saldo_favor, c.cliente_id as c_id, c.cliente_nombre as nombre, c.cliente_apellido_paterno as apaterno, c.cliente_apellido_materno as amaterno, c.cliente_rut as c_rut, c.cliente_correo as c_correo, e.empresa_rut as e_rut, e.empresa_nombre as e_nombre FROM linea_credito lc JOIN plan_credito pc ON lc.plan_credito_id = pc.plan_credito_id JOIN cliente c ON lc.cliente_id = c.cliente_id JOIN empresa e ON lc.empresa_id = e.empresa_id WHERE c.cliente_id = $usuario";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            trigger_error("Ha ocurrido un error");
        }else{
            $this->datos['datos_cuenta'] = array();
            while($row = $rs->fetch_assoc()){
                $dato = array();
                foreach($row as $indice=>$valor){
                    $dato[$indice] = $valor;
                }
                $dato['cupo'] = $this->obtener_cupo_credito($row['c_rut'], $row['monto_autorizado']);
                $dato['detalle_cuenta'] = $this->obtener_detalle_cuenta($row['c_id']);
                array_push($this->datos['datos_cuenta'], $dato);
            }
        }
    }
    
    protected function obtener_detalle_cuenta($cliente){
        $consulta = "SELECT vc.venta_credito_id as v_id, vc.venta_credito_estado as venta_estado, vc.venta_credito_fecha_otorgada as venta_fecha_otorgada, vc.venta_credito_total_bruto as venta_cuota, vc.venta_credito_tasa_interes as venta_interes, vc.venta_credito_valor_cuota_total as venta_valor_total, v.venta_bruto as bruto, v.venta_descuentos as descuentos, v.venta_neto as neto FROM venta_credito vc LEFT JOIN venta v ON vc.venta_credito_id = v.venta_id JOIN linea_credito lc ON vc.linea_credito_id = lc.linea_credito_id JOIN cliente c ON lc.cliente_id = c.cliente_id WHERE c.cliente_id = $cliente;";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            trigger_error('Wrong SQL: ' . $consulta . ' Error: ' . $this->sql_con->error, E_USER_ERROR);
        }else{
            $datos = array();
            while($row = $rs->fetch_assoc()){
                $dato = array();
                foreach($row as $indice=>$valor){
                    $dato[$indice] = $valor;
                }
                $dato['detalle_venta'] = array();
                $dato['detalle_venta'] = $this->obtener_detalle_venta($row['v_id']);
                $dato['detalle_pagos'] = array();
                $dato['detalle_pagos'] = $this->obtener_detalle_pagos($row['v_id']);
                array_push($datos, $dato);
            }
            return $datos;
        }
    }
    
    protected function obtener_detalle_venta($v_id){
        $consulta = "SELECT vp.producto_precio as precio_total_producto, vp.producto_cantidad as producto_cantidad, p.producto_nombre FROM venta_producto vp LEFT JOIN producto p ON vp.producto_id = p.producto_id WHERE vp.venta_id = $v_id";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            trigger_error("Ha ocurrido un error");
        }else{
            $datos = array();
            while($row = $rs->fetch_assoc()){
                $dato = array();
                foreach($row as $indice=>$valor){
                    $dato[$indice] = $valor;
                }
                array_push($datos, $dato);
            }
            return $datos;
        }
        
    }
    
    protected function obtener_detalle_pagos($v_id){
        $consulta = "SELECT cu.cuota_monto as cu_monto, cu.cuota_fecha_pago as cu_f_pago, cu.cuota_estado as cu_estado, cu.cuota_fecha_pagada as cu_f_pagada FROM cuota cu WHERE cu.venta_credito_id = $v_id AND cu.cuota_estado = 0";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            trigger_error("Ha ocurrido un error");
        }else{
            $datos = array();
            while($row = $rs->fetch_assoc()){
                $dato = array();
                foreach($row as $indice=>$valor){
                    $dato[$indice] = $valor;
                }
                array_push($datos, $dato);
            }
            return $datos;
        }
    }
    
    protected function generar_PDF(){
        require('fpdf/fpdf.php');
        $this->pdf=new FPDF();
        $this->pdf->AddPage();
        $this->Header();
        $this->pdf->SetFont('Times','B',14);
        $titulo = "Estado de cuenta";
        $this->pdf->SetXY($this->mid - (strlen($titulo) / 2), 10);
        $this->pdf->Write(1,$titulo);
        $this->pdf->SetFont('Times','',12);
        $empresa = $this->datos['datos_cuenta'][0]['e_nombre'];
        $this->pdf->SetXY($this->mid - (strlen($empresa) / 2), 20);
        $this->pdf->Write(1, $empresa);
        $rut = "RUT " . $this->a_rut($this->datos['datos_cuenta'][0]['e_rut']);
        $this->pdf->SetXY($this->mid - (strlen($rut) / 2), 25);
        $this->pdf->Write(1, $rut);
        $this->pdf->SetFont('Times','B',12);
        $this->pdf->SetXY(15,35);
        $this->pdf->Write(1, 'Datos personales:');
        $this->pdf->SetFont('Times','',12);
        $this->pdf->SetXY(15,40);
        $this->pdf->Write(1,'Nombre: ' . $this->datos['datos_cuenta'][0]['nombre'] . ' ' . $this->datos['datos_cuenta'][0]['apaterno'] . ' ' . $this->datos['datos_cuenta'][0]['amaterno']);
        $this->pdf->SetXY(15,45);
        $this->pdf->Write(1,$this->a_rut($this->datos['datos_cuenta'][0]['c_rut']));
        $this->pdf->SetFont('Times','B',12);
        $this->pdf->SetXY(15,55);
        /*$this->pdf->Write(1,'Datos empresa:');
        $this->pdf->SetFont('Times','',12);
        $this->pdf->SetXY(15,50);
        $this->pdf->Write(1,'Nombre empresa: ' . $this->datos['datos_cuenta'][0]['e_nombre']);
        $this->pdf->SetXY(15, 55);
        $this->pdf->Write(1,'Rut empresa: ' . $this->a_rut($this->datos['datos_cuenta'][0]['e_rut']));
        $this->pdf->SetFont('Times','B',12);
        $this->pdf->SetXY(15,65);*/
        $this->pdf->Write(1,'Datos cuenta:');
        $this->pdf->SetFont('Times','',12);
        $this->pdf->SetXY(15,60);
        $this->pdf->Write(1,'Cupo total de credito: $' . str_replace(',', '.', number_format($this->datos['datos_cuenta'][0]['monto_autorizado'])));
        $this->pdf->SetXY(15,65);
        $this->pdf->Write(1,'Cupo disponible: $' . str_replace(',', '.', number_format($this->datos['datos_cuenta'][0]['cupo'])));
        $this->calcular_ventas();
        $this->x = 15;
        $this->y = 75;
        $this->imprimir_totales();
        $this->pdf->SetFont('Times','B',12);
        $this->sumar_y(10);
        $this->pdf->SetXY($this->x,$this->y);
        $this->pdf->Write(1,'Detalle de compras:');
        
        $this->imprimir_ventas();
        $fecha = date('Y_m_d_H_i_s');
        $nombre = $this->datos['datos_cuenta'][0]['c_rut'] . '_' . $fecha;
        $this->pdf->Output("../files/estado_cuentas/$nombre.pdf","F");
        $this->resultado['estado_cuenta'] = $nombre;
        $this->resultado['mail'] = $this->datos['datos_cuenta'][0]['c_correo'];
        $this->resultado['resultado'] = 1;
    }
    
    protected function imprimir_ventas(){
        $i = 1;
        $j = 0;
        foreach($this->datos['datos_cuenta'][0]['detalle_cuenta'] as $indice=>$valor){
            if($valor['venta_estado'] == 0){
                $this->total_a_pagar += $valor['neto'];
                if($this->y > 200){
                    $this->y = 20;
                    $this->pdf->AddPage();
                    $this->Header();
                }
                $this->sumar_y(5);
                $this->pdf->SetXY($this->x,$this->y);
                $this->pdf->Line (15,$this->y,196,$this->y);
                $this->sumar_y(5);
                $this->pdf->SetFont('Times','B',12);
                $this->pdf->SetXY($this->x,$this->y);
                $this->pdf->Write(1,"Compra $i:");
                $i++;
                $this->sumar_y(5);
                $this->pdf->SetFont('Times','',12);
                $this->pdf->SetXY($this->x,$this->y);
                $this->pdf->Write(1,"Fecha compra: " . $valor['venta_fecha_otorgada']);
                $this->sumar_y(5);
                $this->pdf->SetXY($this->x,$this->y);
                $this->pdf->Write(1,"Total de la compra: $" . str_replace(',', '.', number_format($valor['bruto'])));
                $this->sumar_y(5);
                $this->pdf->SetXY($this->x,$this->y);
                $this->pdf->Write(1,"Total de descuentos: $" . str_replace(',', '.', number_format($valor['descuentos'])));
                $this->sumar_y(5);
                $this->pdf->SetXY($this->x,$this->y);
                $this->pdf->Write(1,"Total neto: $" . str_replace(',', '.', number_format($valor['neto'])));
                $this->imprimir_detalle_ventas($j);
            }
            $j++;
        }
    }
    
    protected function calcular_ventas(){
        $i = 1;
        $j = 0;
        foreach($this->datos['datos_cuenta'][0]['detalle_cuenta'] as $indice=>$valor){
            if($valor['venta_estado'] == 0){
                $this->total_a_pagar += $valor['neto'];
                $i++;
                $this->calcular_detalle_ventas($j);
            }
            $j++;
        }
    }
    
    protected function calcular_detalle_ventas($j){
        $i = 1;
        foreach($this->datos['datos_cuenta'][0]['detalle_cuenta'][$j]['detalle_pagos'] as $indice=>$valor){
            if($valor['cu_estado'] == 0 && $i == 1){
                $this->proxima_cuota += $valor['cu_monto'];
            }
            $i++;
        }
        $i = 1;
    }
    
    protected function imprimir_detalle_ventas($j){
        $i = 1;
        foreach($this->datos['datos_cuenta'][0]['detalle_cuenta'][$j]['detalle_pagos'] as $indice=>$valor){
            $this->sumar_y(5);
            $this->pdf->SetXY($this->x,$this->y);
            $this->pdf->Write(1,"Fecha de pago: " . $valor['cu_f_pago']);
            if($valor['cu_estado'] == 0 && $i == 1){
                $this->proxima_cuota += $valor['cu_monto'];
            }
            $this->sumar_y(5);
            $this->pdf->SetXY($this->x,$this->y);
            $this->pdf->Write(1,"Valor cuota: $" . str_replace(',', '.', number_format($valor['cu_monto'])));
            $i++;
        }
        $i = 1;
        foreach($this->datos['datos_cuenta'][0]['detalle_cuenta'][$j]['detalle_venta'] as $indice=>$valor){
            $this->sumar_y(10);
            $this->pdf->SetFont('Times','B',12);
            $this->pdf->SetXY($this->x,$this->y);
            $this->pdf->Write(1,"Producto: " . $valor['producto_nombre']);
            $i++;
            $this->sumar_y(5);
            $this->pdf->SetFont('Times','',12);
            $this->pdf->SetXY($this->x,$this->y);
            $this->pdf->Write(1,"Precio total producto: $" . str_replace(',', '.', number_format($valor['precio_total_producto'])));
            $this->sumar_y(5);
            $this->pdf->SetFont('Times','',12);
            $this->pdf->SetXY($this->x,$this->y);
            $this->pdf->Write(1,"Cantidad: " . str_replace(',', '.', number_format($valor['producto_cantidad'])));
        }
    }
    
    protected function imprimir_totales(){
        /*$this->sumar_y(20);
        $this->pdf->SetXY($this->x,$this->y);
        $this->pdf->Line (15,$this->y,196,$this->y);
        $this->sumar_y(5);
        $this->pdf->SetFont('Times','B',12);
        $this->pdf->SetXY($this->x,$this->y);
        $this->pdf->Write(1,"Resumen.");*/
        $this->sumar_y(5);
        $this->pdf->SetFont('Times','',12);
        $this->pdf->SetXY($this->x,$this->y);
        $total = $this->total_a_pagar - $this->datos['datos_cuenta'][0]['saldo_favor'];
        $this->pdf->Write(1,"Total utilizado: $" . str_replace(',', '.', number_format($total)));
        $this->sumar_y(5);
        $this->pdf->SetXY($this->x,$this->y);
        $total = $this->total_a_pagar - $this->datos['datos_cuenta'][0]['saldo_favor'];
        $cuota = $this->proxima_cuota - $this->datos['datos_cuenta'][0]['saldo_favor'];
        $this->pdf->Write(1,"Proxima cuota: $" . str_replace(',', '.', number_format($cuota)));
    }
    
    protected function Header(){
        //$this->pdf->Image('../src/aceite.png',15,8,15);
        $this->pdf->Image('../images/nfncloud.png',170,8,30);
    }
    
    protected function sumar_y($cant){
        if(($this->y + $cant) > 260){
            $this->pdf->AddPage();
            $this->Header();
            $this->y = 25;
        }else{
            $this->y += $cant;
        }
        
    }
    
    protected function obtener_id_cliente($rut){
        $consulta = "SELECT cliente_id as c_id FROM cliente WHERE cliente_rut = '$rut'";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            trigger_error("Ha ocurrido un error");
        }else{
            $row = $rs->fetch_assoc();
            return $row['c_id'];
        }
    }
    
    protected function a_rut($rut){
        $rut = str_replace('.', '', $rut);
        $rut = str_replace('-', '', $rut);
        $nuevo_rut = substr($rut, strlen($rut) - 1, 1) . '-';
        $j = 0;
        for($i = strlen($rut) - 2; $i >= 0; $i--){
            if($j == 3){
                $nuevo_rut .= '.';
                $j = 0;
            }
            $nuevo_rut .= substr($rut, $i, 1);
            $j++;
        }
        $nuevo_rut = strrev($nuevo_rut);
        return $nuevo_rut;
    }
    
    public function __destruct(){
        echo json_encode($this->resultado);
    }

}

?>