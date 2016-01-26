<?php

$habilitador = new Habilitadores();

class Habilitadores{

    protected $link, $sql_con;
    protected $datos = array();
    protected $x = 15, $y = 0, $mid = 85, $pdf;
    
    protected function obtener_cupo_credito($rut, $monto){
        $consulta = "SELECT SUM(cu.cuota_monto) AS usado FROM cuota cu JOIN venta_credito vc ON cu.venta_credito_id = vc.venta_credito_id JOIN linea_credito lc ON vc.linea_credito_id = lc.linea_credito_id JOIN cliente c ON lc.cliente_id = c.cliente_id WHERE cu.cuota_estado = 0 AND c.cliente_rut = '$rut'";
        $rs = $this->sql_con->query($consulta);
        while($row = $rs->fetch_assoc()){
            $monto -= $row['usado'];
        }
        return $monto;
    }
    
    protected function obtener_datos_habilitador($habilitador){
        $consulta = "SELECT c.cliente_nombre as nombre, CONCAT(c.cliente_apellido_paterno, ' ', c.cliente_apellido_materno) as apellidos, c.cliente_rut as rut, lc.linea_credito_monto_autorizado as monto_autorizado FROM cliente c JOIN linea_credito lc ON c.cliente_id = lc.cliente_id WHERE cliente_rut = '$habilitador'";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return false;
        }else{
            $row = $rs->fetch_assoc();
            $dato = array();
            foreach($row as $indice=>$valor){
                $dato[$indice] = $valor;
            }
            $dato['cupo'] = $this->obtener_cupo_credito($row['rut'], $row['monto_autorizado']);
            return $dato;
        }
    }
    
    protected function obtener_habilitados($habilitador){
        $consulta = "SELECT SUM(cu.cuota_monto) as monto, cu.cuota_fecha_pago as fecha, vch.venta_credito_habilitado_rut as vch_rut FROM cuota cu JOIN venta_credito vc ON cu.venta_credito_id = vc.venta_credito_id JOIN linea_credito lc ON vc.linea_credito_id = lc.linea_credito_id JOIN cliente c ON lc.cliente_id = c.cliente_id JOIN venta_credito_habilitado vch ON vc.venta_credito_id = vch.venta_credito_id WHERE cu.cuota_estado = 0 AND c.cliente_rut = '$habilitador' GROUP BY vch.venta_credito_habilitado_rut, MONTH(cu.cuota_fecha_pago), DAY(cu.cuota_fecha_pago) ORDER BY cu.cuota_fecha_pago";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return false;
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
    
    protected function Header(){
        //$this->pdf->Image('../src/aceite.png',15,8,15);
        $this->pdf->Image('../images/nfncloud.png',170,8,30);
    }
    
    protected function cargar_reporte(){
        $habilitador = mysqli_real_escape_string($this->sql_con, $_POST['habilitador']);
        $datos_habilitador = $this->obtener_datos_habilitador($habilitador);
        $habilitados = $this->obtener_habilitados($habilitador);
        $this->datos['habilitador'] = $datos_habilitador;
        $this->datos['habilitados'] = $habilitados;
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
    
    protected function generar_reporte(){
        $habilitador = mysqli_real_escape_string($this->sql_con, $_POST['habilitador']);
        $datos_habilitador = $this->obtener_datos_habilitador($habilitador);
        $habilitados = $this->obtener_habilitados($habilitador);
        $nombre = date('Y_m_d_H_i_s_' . $datos_habilitador['rut']);
        require('fpdf/fpdf.php');
        $this->pdf=new FPDF();
        $this->pdf->AddPage();
        $this->Header();
        $this->pdf->SetFont('Times','B',14);
        $titulo = "Estado de cuenta de Habilitador";
        $this->y += 10;
        $this->pdf->SetXY($this->mid - (strlen($titulo) / 2), $this->y);
        $this->pdf->Write(1,$titulo);
        $this->mid = 100;
        $mensaje = 'Habilitador:';
        $this->pdf->SetFont('Times','B',12);
        $this->y += 20;
        $this->pdf->SetXY($this->mid - (strlen($mensaje) / 2), $this->y);
        $this->pdf->Write(1,$mensaje);
        $this->pdf->SetFont('Times','',12);
        $this->y += 10;
        $this->pdf->SetXY($this->x, $this->y);
        $this->pdf->Write(1,'Nombre: ' . utf8_decode($datos_habilitador['nombre'] . ' ' . $datos_habilitador['apellidos']));
        $this->y += 5;
        $this->pdf->SetXY($this->x, $this->y);
        $this->pdf->Write(1,'Rut: ' . $this->a_rut($datos_habilitador['rut']));
        $this->y += 5;
        $this->pdf->SetXY($this->x, $this->y);
        $this->pdf->Write(1,'Monto autorizado: $' . str_replace(',', '.', number_format($datos_habilitador['monto_autorizado'])));
        $this->y += 5;
        $this->pdf->SetXY($this->x, $this->y);
        $utilizado = $datos_habilitador['monto_autorizado'] - $datos_habilitador['cupo'];
        $this->pdf->Write(1,'Monto utilizado: $' . str_replace(',', '.', number_format($utilizado)));
        $this->y += 5;
        $this->pdf->SetXY($this->x, $this->y);
        $this->pdf->Write(1,'Cupo disponible: $' . str_replace(',', '.', number_format($datos_habilitador['cupo'])));
        $mensaje = 'Habilitados:';
        $this->pdf->SetFont('Times','B',12);
        $this->y += 20;
        $this->pdf->SetXY($this->mid - (strlen($mensaje) / 2), $this->y);
        $this->pdf->Write(1,$mensaje);
        $this->y += 10;
        $this->pdf->SetFont('Times','',12);
        $max_y = 230;
        for($i = 0; $i < count($habilitados); $i++){
            if($this->y > $max_y){
                $this->pdf->AddPage();
                $this->Header();
                $this->y = 30;
            }
            $this->pdf->SetXY($this->x, $this->y);
            $this->pdf->Write(1,'Rut: ' . $this->a_rut($habilitados[$i]['vch_rut']));
            $this->y += 5;
            $this->pdf->SetXY($this->x, $this->y);
            $this->pdf->Write(1,'Monto a cancelar: $' . str_replace(',', '.', number_format($habilitados[$i]['monto'])));
            $this->y += 5;
            $this->pdf->SetXY($this->x, $this->y);
            $this->pdf->Write(1,'Fecha de pago: ' . $habilitados[$i]['fecha']);
            $this->y += 10;
        }
        
        $this->pdf->Output("../files/estado_cuentas/$nombre.pdf","F");
        $this->datos['resultado'] = 1;
        $this->datos['url'] = "files/estado_cuentas/$nombre.pdf";
    }
    
    protected function procesar(){
        $accion = $_POST['accion'];
        switch ($accion) {
            case 1:
                $this->cargar_reporte();
                break;
            case 2:
                $this->generar_reporte();
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
        echo json_encode($this->datos);
    }
}

?>