<?php

$clase = new Clase();

class Clase{

    protected $link, $sql_con;
    protected $pdf, $x = 15, $y = 35, $mid = 95, $datos = array();
    
    protected function Header(){
        //$this->pdf->Image('../src/aceite.png',15,8,15);
        $this->pdf->Image('../images/nfncloud.png',170,8,30);
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
    
    protected function head_tabla(){
        $this->pdf->SetXY($this->x,$this->y);
        $this->pdf->SetFont('Times','',12);
        $this->pdf->Cell (10,5,"#" ,1,0, 'C');
        $this->pdf->Cell (40,5,"Valor cuota" ,1,0, 'C');
        $this->pdf->Cell (40,5,"Fecha vencimiento" ,1,0, 'C');
        $this->pdf->Cell (30,5,"Fecha pagada" ,1,0, 'C');
    }
    
    protected function generar_informe_historial_cliente($rut){
        require('fpdf/fpdf.php');
        $this->pdf=new FPDF();
        $this->pdf->AddPage();
        $this->Header();
        $this->pdf->SetFont('Times','B',14);
        $titulo = "Historial de cliente";
        $this->pdf->SetXY($this->mid - (strlen($titulo) / 2), 10);
        $this->pdf->Write(1,$titulo);
        $this->pdf->SetFont('Times','',12);
        $this->pdf->SetXY($this->x, $this->y);
        $this->pdf->Write(1, "Nombre: " . $this->datos['datos_cliente']['nombre']);
        $this->pdf->SetXY($this->x + 130, $this->y);
        $this->pdf->Write(1, "Rut: " . $this->a_rut($this->datos['datos_cliente']['rut']));
        $this->y += 10;
        $this->pdf->SetXY($this->x, $this->y);
        $i = 1;
        foreach($this->datos['historial'] as $indice=>$valor){
            if($this->y > 220){
                $this->pdf->AddPage();
                $this->Header();
                $this->y = 35;
            }
            $this->y += 10;
            $this->pdf->SetFont('Times','B',12);
            $this->pdf->SetXY($this->x, $this->y);
            $this->pdf->Write(1, "Compra " . $i);
            $this->y += 5;
            $this->pdf->SetFont('Times','',12);
            $this->pdf->SetXY($this->x, $this->y);
            $this->pdf->Write(1, "Monto de la compra: $" . str_replace(",", ".", number_format($valor['neto'])));
            $this->y += 5;
            $this->pdf->SetXY($this->x, $this->y);
            $this->pdf->Write(1, "Fecha de la compra: " . $valor['v_fecha']);
            $j = 0;
            $this->x = 25;
            foreach($this->datos['historial'][$i - 1]['cuotas'] as $cuota){
                if($this->y > 220){
                    $this->pdf->AddPage();
                    $this->Header();
                    $this->y = 35;
                    $this->head_tabla();
                }
                if($j == 0){
                    $this->y += 5;
                    $this->pdf->SetFont('Times','B',12);
                    $this->pdf->SetXY($this->x, $this->y);
                    $this->pdf->Write(1, "Cuotas.");
                    $this->y += 5;
                    $this->head_tabla();
                }
                $this->y += 5;
                $this->pdf->SetFont('Times','',12);
                $this->pdf->SetXY($this->x, $this->y);
                $this->pdf->Cell (10,5,$j + 1 ,1,0, 'C');
                $this->pdf->Cell (40,5,"$" . str_replace(",", ".", number_format($cuota['monto'])) ,1,0, 'C');
                $this->pdf->Cell (40,5,$cuota['f_pago'] ,1,0, 'C');
                if($cuota['estado'] == 1){
                    $this->pdf->Cell (30,5,$cuota['f_pagada'] ,1,0, 'C');
                }else{
                    $this->pdf->Cell (30,5,'Pendiente' ,1,0, 'C');
                }
                
                $j++;
            }
            $this->x = 15;
            $i++;
        }
        $this->pdf->Output("../files/historiales_clientes/$rut.pdf","F");
        $this->datos['url'] = "files/historiales_clientes/$rut.pdf";
    }
    
    
    protected function obtener_info_cliente($rut){
        $consulta = "SELECT CONCAT(cliente_nombre, ' ', cliente_apellido_paterno, ' ', cliente_apellido_materno) AS nombre, cliente_rut AS rut FROM cliente WHERE cliente_rut = '$rut'";
        $rs = $this->sql_con->query($consulta);
        return $rs->fetch_assoc();
    }
    
    protected function obtener_historial_cliente($rut){
        $this->datos['datos_cliente'] = $this->obtener_info_cliente($rut);
        $consulta = "SELECT v.venta_id as v_id, v.venta_neto AS neto, DATE_FORMAT(v.venta_fecha, '%d/%m/%Y') as v_fecha FROM venta v JOIN venta_credito vc ON v.venta_id = vc.venta_credito_id JOIN linea_credito lc ON vc.linea_credito_id = lc.linea_credito_id JOIN cliente c ON lc.cliente_id = c.cliente_id WHERE cliente_rut = '$rut' ORDER BY v.venta_fecha DESC";
        $rs = $this->sql_con->query($consulta);
        $this->datos['historial'] = array();
        while ($row = $rs->fetch_assoc()){
            $dato = array();
            foreach($row as $indice=>$valor){
                $dato[$indice] = $valor;
            }
            $v_id = $row['v_id'];
            $consulta = "SELECT cu.cuota_monto AS monto, DATE_FORMAT(cu.cuota_fecha_pago, '%d/%m/%Y') AS f_pago, DATE_FORMAT(cu.cuota_fecha_pagada, '%d/%m/%Y') AS f_pagada, cu.cuota_estado as estado FROM cuota cu WHERE cu.venta_credito_id = $v_id ORDER BY cu.cuota_fecha_pago ASC";
            $rs2 = $this->sql_con->query($consulta);
            $dato['cuotas'] = array();
            while($row2 = $rs2->fetch_assoc()){
                $cuota = array();
                foreach($row2 as $indice2=>$valor2){
                    $cuota[$indice2] = $valor2;
                }
                array_push($dato['cuotas'], $cuota);
            }
            $rs2->close();
            array_push($this->datos['historial'], $dato);
        }
        $rs->close();
    }
    
    protected function procesar(){
        $accion = $_POST['accion'];
        switch($accion){
            case 1:
                $rut = mysqli_real_escape_string($this->sql_con, $_POST['rut']);
                $this->obtener_historial_cliente($rut);
                $this->generar_informe_historial_cliente($rut);
                break;
        }
    }
    
    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    
    public function __construct(){
        session_start();
        require('../hosts.php');
        require('conexion_new.php');
        $hosteo = new Host();
        $hosteo->obtener_conexion(0);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $this->procesar();
    }

    public function __destruct(){
        echo $this->datos['url'];
    }
}

?>