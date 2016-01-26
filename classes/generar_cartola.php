<?php

$cartola = new Cartolas();

class Cartolas{

    protected $link, $sql_con;
    protected $mid = 95, $x = 15, $y = 10;
    protected $datos = array(), $resultado = array(), $pagos = array();
    
    protected function obtener_datos_cliente($rut){
        $consulta = "SELECT cliente_nombre as nombre, cliente_apellido_paterno as apaterno, cliente_apellido_materno as amaterno FROM cliente WHERE cliente_rut = ?";
        $rs = $this->sql_con->prepare($consulta);
        $rs->bind_param('s', $rut);
        $rs->execute();
        $result = $rs->get_result();
        foreach ($result as $indice=>$valor) {
            $this->datos[$indice] = $valor;
        }
        $rs->close();
    }
    
    protected function obtener_pagos($rut, $f_inicio, $f_fin){
        $consulta = "SELECT SUM(cu.cuota_monto) as monto, cu.cuota_fecha_pago as f_pago, cu.cuota_fecha_pagada as f_pagada FROM cuota cu JOIN venta_credito vc ON cu.venta_credito_id = vc.venta_credito_id JOIN linea_credito lc ON vc.linea_credito_id = lc.linea_credito_id JOIN cliente c ON lc.cliente_id = c.cliente_id WHERE c.cliente_rut = '$rut' AND cu.cuota_estado = 1 AND (cu.cuota_fecha_pagada BETWEEN '$f_inicio' AND '$f_fin') GROUP BY cu.cuota_fecha_pagada ORDER BY cu.cuota_fecha_pagada DESC";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return false;
        } else{
            while($row = $rs->fetch_assoc()){
                $dato = array();
                foreach($row as $indice=>$valor){
                    $dato[$indice] = $valor;
                }
                array_push($this->pagos, $dato);
            }
        }
    }
    
    protected function head_tabla(){
        $this->pdf->SetXY($this->x,$this->y);
        $this->pdf->SetFont('Times','',12);
        $this->pdf->Cell (10,5,"#" ,1,0, 'C');
        $this->pdf->Cell (30,5,"Fecha de pago" ,1,0, 'C');
        $this->pdf->Cell (40,5,"Monto cancelado" ,1,0, 'C');
    }
    
    protected function Header(){
        //$this->pdf->Image('../src/aceite.png',15,8,15);
        $this->pdf->Image('../images/nfncloud.png',170,8,30);
    }
    
    protected function nueva_pagina(){
        $this->pdf->AddPage();
        $this->Header();
        $this->y = 25;
    }
    
    protected function generar_cartola($rut, $f_inicio, $f_fin){
        require('fpdf/fpdf.php');
        $this->pdf=new FPDF();
        $this->pdf->AddPage();
        $this->Header();
        $this->pdf->SetFont('Times','B',14);
        $titulo = "Cartola de pagos";
        $this->pdf->SetXY($this->mid - (strlen($titulo) / 2), $this->y);
        $this->pdf->Write(1,$titulo);
        $this->pdf->SetFont('Times','',12);
        $this->y += 10;
        $this->pdf->SetXY($this->x, $this->y);
        $nombre_completo = utf8_decode($this->datos[0]['nombre'] . ' ' . $this->datos[0]['apaterno'] . ' ' . $this->datos[0]['amaterno']);
        $this->pdf->Write(1, "Nombre cliente: " . $nombre_completo);
        $this->y += 10;
        $this->pdf->SetXY($this->x, $this->y);
        $this->pdf->Write(1, "Empresa: " . utf8_decode($_SESSION['nombre_empresa']));
        $this->y += 15;
        $this->mid = 70;
        $this->pdf->SetXY($this->mid - (strlen($titulo) / 2), $this->y);
        $this->pdf->Write(1, "Pagos realizados desde " . $f_inicio . " hasta " . $f_fin);
        $this->obtener_pagos($rut, $f_inicio, $f_fin);
        $this->y += 10;
        $this->x = 70;
        $this->pdf->SetXY($this->x, $this->y);
        $this->head_tabla();
        $this->y += 5;
        $this->pdf->SetXY($this->x, $this->y);
        for($i = 0; $i < count($this->pagos); $i++){
            if($this->y > 220){
                $this->nueva_pagina();
                $this->head_tabla();
                $this->y += 5;
                $this->pdf->SetXY($this->x, $this->y);
            }
            $this->pdf->Cell (10,5, str_replace(',', '.', (number_format($i + 1))) ,1,0, 'C');
            $this->pdf->Cell (30,5, $this->pagos[$i]['f_pagada'] ,1,0, 'C');
            $this->pdf->Cell (40,5, '$' . str_replace(',', '.', (number_format($this->pagos[$i]['monto']))) ,1,0, 'C');
            $this->y += 5;
            $this->pdf->SetXY($this->x, $this->y);
        }
        $this->pdf->Output("../files/estado_cuentas/$rut.pdf","F");
        $this->resultado['resultado'] = 1;
        $this->resultado['url'] = "files/estado_cuentas/$rut.pdf";
    }
    
    protected function procesar(){
        $accion = $_POST['accion'];
        switch ($accion) {
            case 1:
                $rut = mysqli_real_escape_string($this->sql_con, $_POST['rut']);
                $f_inicio = mysqli_real_escape_string($this->sql_con, $_POST['f_inicio']);
                $f_fin = mysqli_real_escape_string($this->sql_con, $_POST['f_fin']);
                $this->obtener_datos_cliente($rut);
                $this->generar_cartola($rut, $f_inicio, $f_fin);
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
        echo json_encode($this->resultado);
    }
}

?>