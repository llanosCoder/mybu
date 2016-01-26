<?php

class AExcel{

    protected $sql_con, $link, $datos, $total_neto = 0, $total_costo = 0, $total_ganancias = 0;
    protected $excel;
    protected $abc = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];

    public function __construct() {
        session_start();
        require ('../hosts.php');
        require ('conexion_new.php');
    }

    protected function sanear_variables($variable) {
        return mysqli_real_escape_string($this->sql_con, $variable);
    }

    protected function set_excel() {
        require ('PHPExcel.php');
        $this->excel = new PHPExcel;
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

    protected function obtener_apertura_caja($user){
        $fecha = date('Y-m-d');
        $consulta = "SELECT registro_usuario_login_fecha as fecha FROM registro_usuario_login WHERE registro_usuario_login_fecha LIKE '$fecha%' AND usuario_id = $user ORDER BY fecha ASC LIMIT 1";
        $rs = $this->sql_con->query($consulta);
        if($rs === false) {
            trigger_error('Wrong SQL: ' . $consulta . ' Error: ' . $this->sql_con->error, E_USER_ERROR);
            exit;
        }
        $row = $rs->fetch_assoc();
        if($row['fecha'] != null && $row['fecha'] != ''){
            return $row['fecha'];
        }else{
            return '-';
        }
    }

    protected function obtener_ventas($f_inicio, $f_termino, $sucursal, $user) {
        $consulta = "SELECT v.venta_id as v_id, v.venta_bruto as bruto, v.venta_descuentos as descuentos, v.venta_neto as neto, ps.producto_sucursal_costo as costo, DATE_FORMAT(v.venta_fecha, '%d-%m-%Y') as fecha FROM venta v JOIN venta_producto vp ON v.venta_id = vp.venta_id JOIN producto_sucursal ps ON vp.producto_id = ps.producto_id WHERE v.venta_fecha >= '$f_inicio' AND v.venta_fecha <= '$f_termino' AND v.usuario_venta_id = $user AND ps.sucursal_id = $sucursal ORDER BY fecha ASC";
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

    protected function generar_plantilla($nombre) {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename='$nombre.xlsx'");
        header('Cache-Control: max-age=0');
        $this->excel = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $this->excel->save('php://output');
        exit;
    }

    protected function reemplazar_comas($in){
        return str_replace(",", "", $in);
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

    protected function calcular_totales(){
        foreach($this->datos['ventas'] as $indice=>$valor){
            $this->total_neto += $valor['neto'];
            $this->total_costo += $valor['costo'];
        }
        $this->total_ganancias = $this->total_neto - $this->total_costo;
    }

    public function cierre_caja($f_inicio, $f_termino) {
        $f_inicio = $this->sanear_variables($f_inicio);
        $f_termino = $this->sanear_variables($f_termino);
        $vendedor = $_SESSION['nombre'];
        $user = $_SESSION['id'];
        if($f_inicio == 0){
            $f_inicio = date('Y-m-d') . ' 00:00:00';
        } else {
            $f_inicio .= ' 00:00:00';
        }
        if($f_termino == 0) {
            $f_termino = date('Y-m-d') . ' 23:59:59';
        } else {
            $f_termino .= ' 23:59:59';
        }
        $sucursal = $_SESSION['sucursal'];
        $this->obtener_ventas($f_inicio, $f_termino, $sucursal, $user);
        $this->set_excel();
        $this->excel->getProperties()
            ->setCreator("mybu")
            ->setLastModifiedBy("mybu")
            ->setTitle("Cierre de caja")
            ->setSubject("Cierre de caja")
            ->setDescription("Informe de ventas del $f_inicio al $f_termino del vendedor $vendedor")
            ->setKeywords("Excel Office 2007 openxml php")
            ->setCategory("Informe Excel");
        $this->excel->setActiveSheetIndex(0)
            ->setCellValue('E1', 'Cierre de Caja');
        $this->excel->setActiveSheetIndex(0)
            ->setCellValue('E2', "Vendedor: " . $vendedor);
        $this->excel->setActiveSheetIndex(0)
            ->setCellValue('A3', 'Apertura de caja: ' . $this->obtener_apertura_caja($user));
        $this->excel->setActiveSheetIndex(0)
            ->setCellValue('G3', 'Cierre de caja: ' . date('Y-m-d H:i:s'));
        $this->calcular_totales();
        $this->excel->setActiveSheetIndex(0)
            ->setCellValue('B5', 'Total Neto: ' . $this->reemplazar_comas(number_format($this->total_neto)));
        $this->excel->setActiveSheetIndex(0)
            ->setCellValue('E5', 'Total Costos: ' . $this->reemplazar_comas(number_format($this->total_costo)));
        $this->excel->setActiveSheetIndex(0)
            ->setCellValue('H5', 'Total Ganancias: ' . $this->reemplazar_comas(number_format($this->total_ganancias)));
        $this->excel->setActiveSheetIndex(0)
            ->setCellValue('A7', "A continuación se muestra una tabla con el detalle de cada venta realizada por            $vendedor desde $f_inicio a $f_termino:");
        $this->excel->setActiveSheetIndex(0)
            ->setCellValue('B9' , '#');
        $this->excel->setActiveSheetIndex(0)
            ->setCellValue('C9' , 'Fecha/Hora');
        $this->excel->setActiveSheetIndex(0)
            ->setCellValue('D9' , 'Bruto');
        $this->excel->setActiveSheetIndex(0)
            ->setCellValue('E9' , 'Descuentos');
        $this->excel->setActiveSheetIndex(0)
            ->setCellValue('F9' , 'Neto');
        $this->excel->setActiveSheetIndex(0)
            ->setCellValue('G9' , 'Costo');
        $this->excel->setActiveSheetIndex(0)
            ->setCellValue('H9' , 'Acumulado');
        $this->excel->setActiveSheetIndex(0)
            ->setCellValue('I9' , 'Productos u.');
        $this->excel->setActiveSheetIndex(0)
            ->setCellValue('J9' , 'T. productos');
        $j = 10;
        $i = 0;
        $acumulado = 0;
        foreach ($this->datos['ventas'] as $indice=>$valor) {
            $acumulado += $valor['neto'];
            $this->excel->setActiveSheetIndex(0)
                ->setCellValue('B' . $j , $i);
            $this->excel->setActiveSheetIndex(0)
                ->setCellValue('C' . $j , $valor['fecha']);
            $this->excel->setActiveSheetIndex(0)
                ->setCellValue('D' . $j , $this->reemplazar_comas(number_format($valor['bruto'])));
            $this->excel->setActiveSheetIndex(0)
                ->setCellValue('E' . $j , $this->reemplazar_comas(number_format($valor['descuentos'])));
            $this->excel->setActiveSheetIndex(0)
                ->setCellValue('F' . $j , $this->reemplazar_comas(number_format($valor['neto'])));
            $this->excel->setActiveSheetIndex(0)
                ->setCellValue('G' . $j , $this->reemplazar_comas(number_format($valor['costo'])));
            $this->excel->setActiveSheetIndex(0)
                ->setCellValue('H' . $j , $this->reemplazar_comas(number_format($acumulado)));
            $productos_unicos = $this->obtener_productos_unicos_venta($valor['productos']);
            $this->excel->setActiveSheetIndex(0)
                ->setCellValue('I' . $j , $this->reemplazar_comas(number_format($productos_unicos)));
            $total_productos_venta = $this->obtener_total_productos_venta($valor['productos']);
            $this->excel->setActiveSheetIndex(0)
                ->setCellValue('J' . $j , $this->reemplazar_comas(number_format($total_productos_venta)));
            $j++;
        }
        $j--;
        $border_style= array('borders' => array('allborders' => array('style' =>
        PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => '000000'),)));
        $this->excel->getActiveSheet()->getStyle("B9:J" . $j)->applyFromArray($border_style);
        $this->excel->getActiveSheet()->setTitle('Cierre de caja');

        $this->generar_plantilla("Cierre de caja");
    }

    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }

    public function set_host($host){
        $hosteo = new Host();
        $hosteo->obtener_conexion($host);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
    }

    public function __destruct() {
    }

}

?>
