<?php

$orden = new Ordenes();

class Ordenes{

    protected $link, $sql_con;
    protected $datos = array();
    protected $parametros = array(), $parametros_abrev = array(), $joins = array();
    protected $voucher, $sucursal = 1;
    
    public function __construct(){
        $this->conexion();
        $this->obtener_parametros();
        $this->obtener_productos_orden();
    }
    
    protected function conexion(){
        require("conexion.php");
        $this->link = Conectarse();
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset("utf8");
    }
    
    protected function obtener_parametros(){
        $this->voucher = mysqli_real_escape_string($this->sql_con, $_POST['voucher']);
        $this->parametros_abrev = $_POST['parametros'];
        foreach($this->parametros_abrev as $valor){
            switch($valor){
                case 'codigo':
                    $param = 'p.producto_codigo';
                    $join = " INNER JOIN producto p ON op.producto_id = p.producto_id";
                    if(!$this->existe_join($join)){
                        array_push($this->joins, $join);
                        $agregar = true;
                    }
                    break;
                case 'nombre':
                    $param = 'p.producto_nombre';
                    $join = " INNER JOIN producto p ON op.producto_id = p.producto_id";
                    if(!$this->existe_join($join)){
                        array_push($this->joins, $join);
                        $agregar = true;
                    }
                    break;
                case 'precio_m':
                    $param = 'op.producto_valor';
                    $agregar = true;
                    $join = " INNER JOIN orden_producto op ON oc.orden_id = op.orden_id";
                    if(!$this->existe_join($join)){
                        array_push($this->joins, $join);
                        $agregar = true;
                    }
                    break;
                case 'stock_r':
                    $param = 'ps.producto_sucursal_stock_real';
                    $join = " INNER JOIN producto_sucursal ps ON op.producto_id = ps.producto_id AND ps.sucursal_id = $this->sucursal";
                    if(!$this->existe_join($join)){
                        array_push($this->joins, $join);
                        $agregar = true;
                    }
                    break;
                case 'cantidad':
                    $param = 'count(*)';
                    $agregar = true;
                    break;
                case 'voucher':
                    $param = 'oc.orden_voucher';
                    $agregar = true;
                    break;
                default:
                    $agregar = false;
                    break;
            }
            if($agregar)
                array_push($this->parametros, $param);
        }
    }
    
    protected function obtener_productos_orden(){
        $consulta_productos = "SELECT ";
        for($i = 0; $i < count($this->parametros_abrev); $i++){
            $this->parametros_abrev[$i] = mysqli_real_escape_string($this->sql_con, $this->parametros_abrev[$i]);
            if($i > 0)
                $consulta_productos = $consulta_productos . ", ";
            $consulta_productos = $consulta_productos . $this->parametros[$i] . " as " . $this->parametros_abrev[$i];
        }
        $consulta_productos = $consulta_productos . " FROM orden_compra oc";
        foreach($this->joins as $join){
            $consulta_productos = $consulta_productos . $join;
        }
        $consulta_productos = $consulta_productos . " WHERE oc.orden_voucher = '$this->voucher' GROUP BY op.producto_id";
        //echo $consulta_productos;
        $result_productos = $this->sql_con->query($consulta_productos);
        if($result_productos === false){
            trigger_error("Ha ocurrido un error");
        }else{
            while($row_productos = $result_productos->fetch_assoc()){
                $dato = array();
                for($i = 0; $i < count($this->parametros_abrev); $i++){
                    $dato[$this->parametros_abrev[$i]] = $row_productos[$this->parametros_abrev[$i]];
                }
                array_push($this->datos, $dato);
            }
        }
    }
                               
    protected function existe_join($nuevo_join){
        $existe = false;
        foreach($this->joins as $joins){
            if($joins == $nuevo_join){
                $existe = true;
                continue;
            }
        }
        return $existe;
    }
    
    public function __destruct(){
        echo json_encode($this->datos);
    }

}

?>