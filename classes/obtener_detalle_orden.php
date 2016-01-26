<?php

$orden = new Ordenes();

class Ordenes{

    protected $link, $sql_con;
    protected $datos = array();
    protected $parametros = array(), $parametros_abrev = array(), $joins = array();
    protected $voucher, $sucursal;
    
    public function __construct(){
        session_start();
        require('../hosts.php');
        require('conexion_new.php');
        $hosteo = new Host();
        $hosteo->obtener_conexion(1);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $this->obtener_parametros();
        $this->obtener_comprador();
        $host = $this->obtener_origen_datos();
        $hosteo = new Host();
        $hosteo->obtener_conexion($host);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $this->obtener_productos_orden();
    }
    
    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    
    protected function obtener_origen_datos(){
            $consulta_origen = "SELECT host FROM empresa_conexion WHERE empresa_id = $this->proveedor";
            $rs_origen = $this->sql_con->query($consulta_origen);
            if($rs_origen === false){
                exit();
            }else{
                $row_origen = $rs_origen->fetch_assoc();
                return $row_origen['host'];
            }
        }
    
    protected function obtener_parametros(){
        $this->voucher = mysqli_real_escape_string($this->sql_con, $_POST['voucher']);
        $this->sucursal = $_SESSION['sucursal'];
        $this->parametros_abrev = $_POST['parametros'];
        foreach($this->parametros_abrev as $indice=>$valor){
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
                case 'stock_m':
                    $param = 'ps.producto_sucursal_stock_minimo';
                    $join = " INNER JOIN producto_sucursal ps ON op.producto_id = ps.producto_id AND ps.sucursal_id = $this->sucursal";
                    if(!$this->existe_join($join)){
                        array_push($this->joins, $join);
                        $agregar = true;
                    }
                    break;
                case 'cantidad':
                    $param = 'op.producto_cantidad';
                    $agregar = true;
                    break;
                case 'oferta':
                    $param = 'o.oferta_descripcion';
                    $join = " INNER JOIN oferta o ON op.orden_compra_oferta_id = o.oferta_id";
                    if(!$this->existe_join($join)){
                        array_push($this->joins, $join);
                        $agregar = true;
                    }
                    break;
                default:
                    array_splice($this->parametros_abrev, $indice, 1);
                    $agregar = false;
                    break;
            }
            if($agregar)
                array_push($this->parametros, $param);
        }
    }
    
    protected function obtener_comprador(){
        $consulta_empresa = "SELECT e.empresa_id as e_id, e.empresa_nombre as nombre, e.empresa_rut as rut, e.empresa_direccion as direccion, ciudad_nombre as ciudad, ec.empresa_telefono as telefono, ec.empresa_correo as correo FROM empresa e INNER JOIN ciudad ON empresa_ciudad = ciudad_id LEFT JOIN empresa_contacto ec ON e.empresa_id = ec.empresa_id WHERE e.empresa_id = (SELECT oc.empresa_solicitante FROM orden_compra oc WHERE oc.orden_voucher = '$this->voucher')";
        //echo $consulta_empresa;
        $result_empresa = $this->sql_con->query($consulta_empresa);
        if($result_empresa === false){
            trigger_error("Ha ocurrido un error");
            exit();
        }else{
            $empresa = $result_empresa->fetch_assoc();
            $this->datos['empresa'] = array();
            $dato = array();
            $this->proveedor = $empresa['e_id'];
            $dato['e_nombre'] = $empresa['nombre'];
            $dato['e_rut'] = $empresa['rut'];
            $dato['e_direccion'] = $empresa['direccion'];
            $dato['e_ciudad'] = $empresa['ciudad'];
            $dato['e_telefono'] = $empresa['telefono'];
            $dato['e_correo'] = $empresa['correo'];
            array_push($this->datos['empresa'], $dato);
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
            $this->datos['productos'] = array();
            while($row_productos = $result_productos->fetch_assoc()){
                $dato = array();
                for($i = 0; $i < count($this->parametros_abrev); $i++){
                    $dato[$this->parametros_abrev[$i]] = $row_productos[$this->parametros_abrev[$i]];
                }
                array_push($this->datos['productos'], $dato);
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