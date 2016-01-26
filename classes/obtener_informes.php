<?php

$informe = new Informes();

class Informes{

    protected $link, $sql_con;
    protected $usuario, $nombre_usuario, $datos = array(), $fecha;
    protected $pdf, $pages = 0;
    protected $total_neto = 0, $total_costo = 0, $total_ganancias = 0, $apertura_caja, $f_inicio = 0, $f_fin = 0;

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
        $this->usuario = $_SESSION['id'];
        $this->nombre_usuario = str_replace('&nbsp;', ' ', $_SESSION['nombre']);
        $this->accion = $_GET['accion'];
        $this->fecha = date('d-m-Y');
        if(isset($_GET['f_inicio'])){
            $this->f_inicio = $_GET['f_inicio'];
        }
        if(isset($_GET['f_fin'])){
            $this->f_fin = $_GET['f_fin'];
        }
        $this->cerrar_caja();
        $this->apertura_caja = $this->obtener_apertura_caja();
        switch($this->accion){
            case 1:
                $this->a_pdf();
            break;
        }
        //$this->a_pdf();
    }

    protected function a_pdf(){
        require_once('fpdf/fpdf.php');
        $this->pdf=new FPDF();
        $this->pdf->AddPage();
        $this->pages++;
        $this->Header();
        $this->pdf->SetFont('Times','B',14);
        $this->pdf->SetXY(90,10);
        $this->pdf->Write(1,"Cierre de caja");
        $this->pdf->SetXY(83,25);
        $this->pdf->SetFont('Times','',12);
        $this->pdf->Write(1,utf8_decode("Vendedor: " . $this->nombre_usuario));
        $this->pdf->SetXY(15,40);
        $this->pdf->Write(1, "Apertura de caja: " . $this->apertura_caja);
        $this->pdf->SetXY(130, 40);
        $this->pdf->Write(1, "Cierre de caja: " . date('Y-m-d H:i:s'));
        $this->hacer_totales();
        $this->pdf->SetFont('Times','',12);
        $mensaje = "A continuación se muestra una tabla con el detalle de cada venta realizada por $this->nombre_usuario";
        $this->pdf->SetXY(15,65);
        $this->pdf->Write(1,utf8_decode($mensaje));
        $mensaje = "el día $this->fecha:";
        $this->pdf->SetXY(15,70);
        $this->pdf->Write(1,utf8_decode($mensaje));
        $this->hacer_tabla();
        $this->pdf->Output("Planilla Descuento.pdf","I");
    }

    protected function hacer_totales(){
        $this->calcular_totales();
        $this->pdf->SetFont('Times','B',12);
        $this->pdf->SetXY(25, 50);
        $this->pdf->Write(1, 'Total neto: ');
        $this->pdf->SetFont('Times','',12);
        $this->pdf->Write(1, '$' . $this->reemplazar_comas(number_format($this->total_neto)));
        $this->pdf->SetFont('Times','B',12);
        $this->pdf->SetXY(80, 50);
        $this->pdf->Write(1, 'Total costos: ');
        $this->pdf->SetFont('Times','',12);
        $this->pdf->Write(1, '$' . $this->reemplazar_comas(number_format($this->total_costo)));
        $this->pdf->SetFont('Times','B',12);
        $this->pdf->SetXY(150, 50);
        $this->pdf->Write(1, 'Ganancias: ');
        $this->pdf->SetFont('Times','',12);
        $this->pdf->Write(1, '$' . $this->reemplazar_comas(number_format($this->total_ganancias)));
    }

    protected function calcular_totales(){
        foreach($this->datos['ventas'] as $indice=>$valor){
            $this->total_neto += $valor['neto'];
            $this->total_costo += $valor['costo'];
        }
        $this->total_ganancias = $this->total_neto - $this->total_costo;
    }

    protected function hacer_tabla(){
        $this->head_tabla(80);
        $x = 10;
        $y = 80;
        $this->pdf->SetXY($x,$y);
        $i = 0;
        $k = 0;
        $acumulado = 0;
        foreach($this->datos['ventas'] as $indice=>$valor){
            $k++;
            $i++;
            if(($this->pages == 1 && $k > 30) || $k > 40){
                $this->pdf->AddPage();
                $this->pages++;
                $this->Header();
                $this->head_tabla(30);
                $k = 0;
                $y = 30;
            }
            $y += 5;
            $acumulado += $valor['neto'];
            $this->pdf->SetXY($x,$y);
            $this->pdf->Cell (10,5, $this->reemplazar_comas(number_format($i)) ,1,0, 'C');
            $this->pdf->Cell (25,5, $valor['fecha'] ,1,0, 'C');
            $this->pdf->Cell (20,5, $this->reemplazar_comas(number_format($valor['bruto'])) ,1,0, 'C');
            $this->pdf->Cell (25,5, $this->reemplazar_comas(number_format($valor['descuentos'])) ,1,0, 'C');
            $this->pdf->Cell (20,5, $this->reemplazar_comas(number_format($valor['neto'])) ,1,0, 'C');
            $this->pdf->Cell (20,5, $this->reemplazar_comas(number_format($valor['costo'])) ,1,0, 'C');
            $this->pdf->Cell (25,5,$this->reemplazar_comas(number_format($acumulado)) ,1,0, 'C');
            $productos_unicos = $this->obtener_productos_unicos_venta($valor['productos']);
            $this->pdf->Cell (25,5, $this->reemplazar_comas(number_format($productos_unicos)) ,1,0, 'C');
            $total_productos_venta = $this->obtener_total_productos_venta($valor['productos']);
            $this->pdf->Cell (25,5, $this->reemplazar_comas(number_format($total_productos_venta)) ,1,0, 'C');
        }
    }

    protected function obtener_productos_unicos_venta($venta){
        $productos = array();
        foreach($venta as $indice=>$valor){
            if(!in_array($valor['codigo'], $productos)){
                array_push($productos, $valor['codigo']);
            }
        }
        return count($productos);
    }

    protected function obtener_total_productos_venta($venta){
        $total_productos = 0;
        foreach($venta as $indice=>$valor){
            $total_productos += $valor['cantidad'];
        }
        return $total_productos;
    }

    protected function head_tabla($y){
        $this->pdf->SetXY(10,$y);
        $this->pdf->SetFont('Times','',12);
        $this->pdf->Cell (10,5,"#" ,1,0, 'C');
        $this->pdf->Cell (25,5,"Fecha/Hora" ,1,0, 'C');
        $this->pdf->Cell (20,5,"Bruto" ,1,0, 'C');
        $this->pdf->Cell (25,5,"Descuentos" ,1,0, 'C');
        $this->pdf->Cell (20,5,"Neto" ,1,0, 'C');
        $this->pdf->Cell (20,5,"Costo" ,1,0, 'C');
        $this->pdf->Cell (25,5,"Acumulado" ,1,0, 'C');
        $this->pdf->Cell (25,5,"Productos u." ,1,0, 'C');
        $this->pdf->Cell (25,5,"T. productos" ,1,0, 'C');
    }

    protected function reemplazar_comas($in){
        return str_replace(",", ".", $in);
    }

    protected function Header(){
        //$this->pdf->Image('../src/aceite.png',15,8,15);
        $this->pdf->Image('../images/nfncloud.png',170,8,30);
    }

    protected function cerrar_caja(){
        $fecha = date('Y-m-d');
        $user = $_SESSION['id'];
        if($this->f_inicio == 0){
            $this->f_inicio = date('Y-m-d') . ' 00:00:00';
        } else {
            $this->f_inicio .= ' 00:00:00';
        }
        if($this->f_fin == 0) {
            $this->f_fin = date('Y-m-d') . ' 23:59:59';
        } else {
            $this->f_fin .= ' 23:59:59';
        }
        $sucursal = $_SESSION['sucursal'];
            $consulta = "SELECT v.venta_id as v_id, v.venta_bruto as bruto, v.venta_descuentos as descuentos, v.venta_neto as neto, ps.producto_sucursal_costo as costo, DATE_FORMAT(v.venta_fecha, '%d-%m-%Y') as fecha FROM venta v JOIN venta_producto vp ON v.venta_id = vp.venta_id JOIN producto_sucursal ps ON vp.producto_id = ps.producto_id WHERE v.venta_fecha >= '$this->f_inicio' AND v.venta_fecha <= '$this->f_fin' AND v.usuario_venta_id = $user AND ps.sucursal_id = $sucursal ORDER BY fecha ASC";
        //$consulta = "SELECT venta_id as v_id, venta_bruto as bruto, venta_descuentos as descuentos, venta_neto as neto, TIME(venta_fecha) as fecha FROM venta WHERE venta_fecha LIKE '$fecha%' AND usuario_venta_id = $user ORDER BY fecha ASC";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return false;
        }else{
            $this->datos['ventas'] = array();
            while($row = $rs->fetch_assoc()){
                $dato = array();
                foreach($row as $indice=>$valor){
                    if($indice != 'v_id'){
                        $dato[$indice] = $valor;
                    }
                }
                $dato['productos'] = array();
                $dato['productos'] = $this->obtener_productos($row['v_id']);
                array_push($this->datos['ventas'], $dato);
            }
        }
    }

    protected function obtener_productos($v_id){
        $consulta_productos = "SELECT p.producto_codigo as codigo, p.producto_nombre as nombre, vp.producto_cantidad as cantidad FROM producto p JOIN venta_producto vp ON p.producto_id = vp.producto_id WHERE vp.venta_id = $v_id";
        $rs_productos = $this->sql_con->query($consulta_productos);
        if($rs_productos === false){
            return false;
        }else{
            $datos = array();
            $dato = array();
            while($row_productos = $rs_productos->fetch_assoc()){
                foreach($row_productos as $indice=>$valor){
                    $dato[$indice] = $valor;
                }
                array_push($datos, $dato);
            }
            return $datos;
        }
    }

    protected function obtener_apertura_caja(){
        $fecha = date('Y-m-d');
        $consulta = "SELECT registro_usuario_login_fecha as fecha FROM registro_usuario_login WHERE registro_usuario_login_fecha LIKE '$fecha%' AND usuario_id = $this->usuario ORDER BY fecha ASC LIMIT 1";
        $rs = $this->sql_con->query($consulta);
        $row = $rs->fetch_assoc();
        if($row['fecha'] != null && $row['fecha'] != ''){
            return $row['fecha'];
        }else{
            return '-';
        }
    }

    public function __destruct(){
        echo json_encode($this->datos);
    }

}

?>
