<?php

$excel = new Excel();

class Excel{

    protected $link, $sql_con;
    protected $sucursal;
    protected $datos = array(), $tabla = '';

    public function __construct(){
        session_start();
        header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=bodega.xls");  //File name extension was wrong
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
        $this->sucursal = $_SESSION['sucursal'];
        $this->obtener_productos();
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
        $this->tabla .= "</tbody></table>";
    }

    protected function crear_headers(){
        $this->tabla = "<table border='1'><thead><tr>";
        foreach($this->datos[0] as $indice=>$valor){
            $this->tabla .= "<th>$indice</th>";
        }
        $this->tabla .= "</tr></thead>";
    }

    protected function obtener_productos(){
        $consulta = "SELECT p.producto_codigo as Codigo, p.producto_nombre as Nombre, pm.producto_marca_nombre as Marca, p.producto_descripcion as Descripcion, s.sucursal_direccion as Direccion, ps.producto_sucursal_stock_real as Stock_Real, ps.producto_sucursal_stock_minimo as Stock_minimo, ps.producto_sucursal_costo as Precio_Costo, ps.producto_sucursal_precio_unitario as Precio_Unitario, ps.producto_sucursal_precio_mayorista as Precio_Mayorista FROM producto p JOIN producto_sucursal ps ON p.producto_id = ps.producto_id JOIN sucursal s ON ps.sucursal_id = s.sucursal_id JOIN producto_marca pm ON p.marca_id = pm.producto_marca_id WHERE s.sucursal_id = $this->sucursal";
        $rs = $this->sql_con->query($consulta);
        while($row = $rs->fetch_assoc()){
            $dato = array();
            foreach($row as $indice=>$valor){
                $dato[$indice] = $valor;
            }
            array_push($this->datos, $dato);
        }
    }

    public function __destruct(){
        echo $this->tabla;
    }

    }

?>
