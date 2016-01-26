<?php
$compra = new Compras();

if(isset($_POST["set_venta"])){
    $b = $_POST["venta_bruto"];
    $d = $_POST["venta_descuentos"];
    $n = $_POST["venta_neto"];
    $compra->set_totales($b,$d,$n);
    $compra->procesar_compra();
    
}

if(isset($_POST["set_venta_producto"])){
    $v = $_POST["venta_id"];
    $p_id = $_POST["producto_id"];
    $p_precio = $_POST["producto_precio"];
    $p_ca = $_POST["producto_cantidad"];
    $p_promo = $_POST["producto_promocion"];
    $compra->procesar_venta_producto($v,$p_id,$p_precio,$p_ca,$p_promo);
    $compra->procesar_materia_prima($p_id,$p_ca);
}

if(isset($_POST["set_venta_medio"])){
    $v = $_POST["venta_id"];
    $m = $_POST["venta_monto"];
    $i = $_POST["venta_medio_pago_id"];
    $compra->procesar_venta_medio($v,$m,$i);
}
class Compras{

    protected $link, $sql_con;
    protected $resultado;
    protected $sucursal, $empresa, $usuario;
    public $total_bruto,$total_descuentos,$total_neto;
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
    
    public function set_totales($bruto,$descuentos,$neto){
        $this->total_bruto = $bruto;
        $this->total_descuentos = $descuentos;
        $this->total_neto = $neto;
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
    public function procesar_compra(){
        $insercion_compra = $this->sql_con->prepare("INSERT INTO venta VALUES(null, NOW(), ?, ?, ?, ?, ?, null)");
        $insercion_compra->bind_param('iiiii',
        $this->total_bruto,
        $this->total_descuentos,
        $this->total_neto,
        $this->empresa,
        $this->usuario);
        $insercion_compra->execute();
        $insercion_compra->close();
        $venta_id = mysqli_insert_id($this->sql_con);
        echo $venta_id;
    }
    public function procesar_venta_producto($v,$p_id,$p_precio,$p_ca,$p_promo){
        $insercion_compra = $this->sql_con->prepare("INSERT INTO venta_producto VALUES (null, ?, ?, ?, ?, ?, 0)");
        $insercion_compra->bind_param('iiiii',
          $v,
          $p_id,
          $p_precio,
          $p_ca,
          $p_promo);
        $insercion_compra->execute();
        $insercion_compra->close();
        $this->descontar_stock($p_id,$p_ca);
        echo "1";
    }
    public function procesar_venta_medio($v,$m,$i){
        $insercion_compra = $this->sql_con->prepare("INSERT INTO venta_pago VALUES (null, ?, ?, ?)");
        $insercion_compra->bind_param('iii',
          $v,
          $m,
          $i);
        $insercion_compra->execute();
        $insercion_compra->close();
        echo "2";
    }
    public function descontar_stock($p_id,$cantidad){
        $insercion_compra =$this->sql_con->prepare("UPDATE producto_sucursal SET producto_sucursal_stock_real = producto_sucursal_stock_real - ? WHERE producto_id = ? AND sucursal_id = ?");
        $insercion_compra->bind_param('iii',
          $cantidad,
          $p_id,    
          $this->sucursal);
        $insercion_compra->execute();
        //print_r($insercion_compra);
        $insercion_compra->close();
        
    }
    public function procesar_materia_prima($producto,$cantidad){
        
            $consulta = "SELECT materia_prima_producto.materia_prima_id as mid, materia_prima_producto.materia_prima_unidad as mud FROM `materia_prima_producto` JOIN materia_prima_sucursal ON materia_prima_sucursal.materia_prima_id=materia_prima_producto.materia_prima_id WHERE materia_prima_producto.producto_id=$producto AND materia_prima_sucursal.sucursal_id=$this->sucursal";
            $this->sql_con->set_charset("utf8");
            $rs1 = $this->sql_con->query($consulta);
            if($rs1 === false) {
                trigger_error('Ha ocurrido un error');
            }
            
           while ($row = mysqli_fetch_array($rs1, MYSQLI_ASSOC)) {
               $cantidad = $row['mud']*$cantidad;
               $mid = $row['mid'];
               $insercion_prima =$this->sql_con->prepare("UPDATE materia_prima_sucursal SET materia_prima_sucursal_stock_real=materia_prima_sucursal_stock_real-? WHERE materia_prima_id=? AND sucursal_id=?");
                $insercion_prima->bind_param('iii',
                  $cantidad,
                  $mid,    
                  $this->sucursal);
                $insercion_prima->execute();
                $insercion_prima->close();
            }
    }
}
?>