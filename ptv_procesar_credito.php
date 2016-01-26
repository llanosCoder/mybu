<?php
$credito = new Credito();

if(isset($_POST["setVentaCredito"])){
    $vci = $_POST["ventaCreditoId"];
    $lci = $_POST["lineaCreditoId"];
    $vce = $_POST["ventaCreditoEstado"];
    $vctb = $_POST["ventaCreditoTotalBruto"];
    $vcti = $_POST["ventaCreditoTasaInteres"];
    $vcvct = $_POST["ventaCreditoValorCuotaTotal"];;
    $credito->procesar_venta_credito($vci,$lci,$vce,$vctb,$vcti,$vcvct);
}

if(isset($_POST["setCuotasCredito"])){
    $p = $_POST["periodos"];
    $vci = $_POST["ventaCreditoId"];
    $vct = $_POST["ventaCreditoTotal"];
    $cm = $_POST["cuotaMonto"];
    $cfp = $_POST["cuotaFechaPago"];
    $cff = $_POST["cuotaFechaFacturacion"];
    $ce = $_POST["cuotaEstado"];
    $cfpa = $_POST["cuotaFechaPagada"];;
    $credito->procesar_venta_cuotas($p,$vci,$cm,$cfp,$cff,$ce,$cfpa,$vct);
}

class Credito{

    protected $link, $sql_con;
    protected $resultado;
    protected $sucursal, $empresa, $usuario;
    protected $registro_tag, $host;

    public function __construct(){
        session_start();
        require('../hosts.php');
        require('conexion_new.php');
        $hosteo = new Host();
        $hosteo->obtener_conexion(0);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $this->obtener_parametros();
    }

    public function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    public function obtener_parametros(){
        $this->sucursal = $_SESSION['sucursal'];
        $this->empresa = $_SESSION['empresa'];
        $this->usuario = $_SESSION['id'];
    }
    public function procesar_venta_credito($vci,$lci,$vce,$vctb,$vcti,$vcvct,$vct){
        $insercion_compra = $this->sql_con->prepare("INSERT INTO venta_credito(venta_credito_id, linea_credito_id, venta_credito_estado, venta_credito_fecha_otorgada, venta_credito_total_bruto, venta_credito_tasa_interes, venta_credito_valor_cuota_total)
          VALUES (?,?,?,NOW(),?,?,?)");
        $insercion_compra->bind_param('iiiiii',
          $vci,
          $lci,
          $vce,
          $vctb,
          $vcti,
          $vcvct);
        $insercion_compra->execute();
        $insercion_compra->close();
        $this->descontarCupo($lci,$vct);
        echo "3";
    }
    public function descontarCupo($lci,$vct){
        $insercion_compra =$this->sql_con->prepare("UPDATE linea_credito SET linea_credito_cupo=linea_credito_cupo-? WHERE linea_credito_id=?");
        $insercion_compra->bind_param('ii',
          $vct,
          $lci);
        $insercion_compra->execute();
        print_r($insercion_compra);
        $insercion_compra->close();
    }
    public function procesar_venta_cuotas($p,$vci,$cm,$cfp,$cff,$ce,$cfpa){
      $ahora = time();
      $fechaPago = date("Y-m-").date("d", strtotime($cfp));
      $fechaPago = strtotime($fechaPago);
      $fechaFacturacion = date("Y-m-").date("d", strtotime($cff));
      $fechaFacturacion = strtotime($fechaFacturacion.' 23:59:59');
      $correlacionPago = 1;
      if($ahora>$fechaFacturacion){
        $correlacionPago = 2;
      }
      $primeraCuota = mktime(0, 0, 0, date("m")+$correlacionPago, date("d", strtotime($cfp)),   date("Y"));

      for ($i=0;$i<intval($p);$i++){
        $proximaCuota = mktime(0, 0, 0, date("m",$primeraCuota)+$i, date("d", $primeraCuota),   date("Y",$primeraCuota));
        $proximaCuota = date('Y-m-d',$proximaCuota);
        $insercion_compra = $this->sql_con->prepare("INSERT INTO cuota(cuota_id, venta_credito_id, cuota_monto, cuota_fecha_pago, cuota_estado, cuota_fecha_pagada)
          VALUES (null,?,?,?,?,?)");
        $insercion_compra->bind_param('iisii',
          $vci,
          $cm,
          $proximaCuota,
          $ce,
          $cfpa);
        $insercion_compra->execute();
        $insercion_compra->close();
      }
      echo "4";
    }
}
?>
