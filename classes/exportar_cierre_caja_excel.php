<?php

$excel = new Excel();

class Excel{

    protected $link, $sql_con;
    protected $sucursal;
    protected $datos = array(), $tabla = '';

    public function __construct(){
        session_start();
        header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=cierre_caja.xls");  //File name extension was wrong
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private",false);
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
        if(isset($_GET['f_inicio'])){
            $f_inicio = $_GET['f_inicio'];
        }
        if(isset($_GET['f_fin'])){
            $f_fin = $_GET['f_fin'];
        }
        if ($f_inicio == 0) {
            $f_inicio = date('Y-m-d') . ' 00:00:00';
        }
        if ($f_fin == 0) {
            $f_fin = date('Y-m-d') . ' 23:59:59';
        }
        $user = $_SESSION['id'];
        $this->obtener_productos($f_inicio, $f_fin, $user);
        $this->crear_headers();
        $this->tabla .= "<tbody>";
        for($i = 0; $i < count($this->datos); $i++){
            $this->tabla .= "<tr>";
            foreach($this->datos[$i] as $indice=>$valor){
                if($valor == '0'){
                    $this->tabla .= "<td style='color:red;'>";
                }else{
                    $this->tabla .= "<td>";
                }
                $this->tabla .= substr($valor, 0, 39);
                if(strlen($valor) > 39){
                    $this->tabla .= "...";
                }
                $this->tabla .= "</td>";
            }
            $this->tabla .= "</tr>";
        }
        if ($i == 0) {
            $this->tabla .= '<tr><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>';
        }
        $this->tabla .= "</tbody></table>";
    }

    protected function crear_headers(){
        $this->tabla = "<table border='1'><thead><tr>";
        $i = 0;
        foreach($this->datos[0] as $indice=>$valor){
            $this->tabla .= "<th>$indice</th>";
            $i++;
        }
        if ($i == 0) {
            $this->tabla .= '<th>Id_Venta</th><th>Bruto</th><th>Descuentos</th><th>Neto</th><th>Fecha_Venta</th>';
        }
        $this->tabla .= "</tr></thead>";
    }

    protected function obtener_productos($f_inicio, $f_fin, $user){
        $this->sucursal = $_SESSION['sucursal'];
        $consulta = "SELECT v.venta_id as Id_Venta, v.venta_bruto as Bruto, v.venta_descuentos as Descuentos, v.venta_neto as Neto, ps.producto_sucursal_costo as costo, DATE(v.venta_fecha) as Fecha_Venta FROM venta v JOIN venta_producto vp ON v.venta_id = vp.venta_id JOIN producto_sucursal ps ON vp.producto_id = ps.producto_id WHERE v.venta_fecha >= '$f_inicio' AND v.venta_fecha <= '$f_fin' AND v.usuario_venta_id = $user AND ps.sucursal_id = $this->sucursal ORDER BY venta_fecha ASC";
        $rs = $this->sql_con->query($consulta);
        while($row = $rs->fetch_assoc()){
            $dato = array();
            foreach($row as $indice=>$valor){
                $dato[$indice] = $valor;
            }
            if ($dato['costo'] == null) {
                $dato['costo'] = 0;
            }
            array_push($this->datos, $dato);
        }
    }

    public function __destruct(){
        echo $this->tabla;
    }

    }

?>
