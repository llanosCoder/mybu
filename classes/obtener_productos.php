<?php

$producto = new Productos();

class Productos{

    protected $link, $sql_con;
    protected $parametros = array(), $parametros_abrev = array(), $joins = array(), $datos = array(), $extras = array();
    protected $sucursal, $tipo_cuenta;

    public function __construct(){
        session_start();
        require('conexion_new.php');
        require('../hosts.php');
        $hosteo = new Host();
        $hosteo->obtener_conexion(0);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $this->obtener_parametros();
        $this->obtener_productos();
        if(count($this->extras) > 0)
            $this->push_extras();
    }
    
    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    
    
    protected function obtener_parametros(){
        $this->sucursal = $_SESSION['sucursal'];
        $this->tipo_cuenta = $_SESSION['tipo_cuenta'];
        $this->parametros_abrev = $_POST['parametros'];
        foreach($this->parametros_abrev as $indice=>$valor){
            switch($valor){
                case 'id':
                    $param = 'p.producto_id';
                    $agregar = true;
                    break;
                case 'codigo':
                    $param = 'p.producto_codigo';
                    $agregar = true;
                    break;
                case 'nombre':
                    $param = 'p.producto_nombre';
                    $agregar = true;
                    break;
                case 'marca':
                    $param = 'p.marca_id';
                    $agregar = true;
                    break;
                case 'modelo':
                    $param = 'p.producto_modelo';
                    $agregar = true;
                    break;
                case 'descripcion':
                    $param = 'p.producto_descripcion';
                    $agregar = true;
                    break;
                case 'imagen':
                    $param = 'p.producto_imagen';
                    $agregar = true;
                    break;
                case 'talla':
                    $param = 'p.producto_talla';
                    $agregar = true;
                    break;
                case 'peso':
                    $param = 'p.producto_peso';
                    $agregar = true;
                    break;
                case 'volumen':
                    $param = 'p.producto_volumen';
                    $agregar = true;
                    break;
                case 'dimension':
                    $param = 'p.producto_dimension';
                    $agregar = true;
                    break;
                case 'marca_nombre':
                    $param = 'm.producto_marca_nombre';
                    $join = " LEFT JOIN producto_marca m ON p.marca_id = m.producto_marca_id";
                    if(!$this->existe_join($join)){
                        array_push($this->joins, $join);
                        $agregar = true;
                    }
                    break;
                case 'precio_u':
                    $param = 'ps.producto_sucursal_precio_unitario';
                    $join = " INNER JOIN producto_sucursal ps ON ps.sucursal_id = $this->sucursal AND ps.producto_id = p.producto_id";
                    if(!$this->existe_join($join)){
                        array_push($this->joins, $join);
                        $agregar = true;
                    }
                    break;
                case 'precio_m':
                    $param = 'ps.producto_sucursal_precio_mayorista';
                    $join = " INNER JOIN producto_sucursal ps ON ps.sucursal_id = $this->sucursal AND ps.producto_id = p.producto_id";
                    if(!$this->existe_join($join)){
                        array_push($this->joins, $join);
                        $agregar = true;
                    }
                    break;
                case 'costo':
                    $param = 'ps.producto_sucursal_costo';
                    $join = " INNER JOIN producto_sucursal ps ON ps.sucursal_id = $this->sucursal AND ps.producto_id = p.producto_id";
                    if(!$this->existe_join($join)){
                        array_push($this->joins, $join);
                        $agregar = true;
                    }
                    break;
                case 'stock_r':
                    $param = 'ps.producto_sucursal_stock_real';
                    $join = " INNER JOIN producto_sucursal ps ON ps.sucursal_id = $this->sucursal AND ps.producto_id = p.producto_id";
                    if(!$this->existe_join($join)){
                        array_push($this->joins, $join);
                        $agregar = true;
                    }
                    break;
                case 'stock_m':
                    $param = 'ps.producto_sucursal_stock_minimo';
                    $join = " INNER JOIN producto_sucursal ps ON ps.sucursal_id = $this->sucursal AND ps.producto_id = p.producto_id";
                    if(!$this->existe_join($join)){
                        array_push($this->joins, $join);
                        $agregar = true;
                    }
                    break;
                case 'sucursal':
                    $param = 's.sucursal_direccion';
                    $join = " INNER JOIN sucursal s ON s.sucursal_id = $this->sucursal";
                    if(!$this->existe_join($join)){
                        array_push($this->joins, $join);
                        $agregar = true;
                    }
                    break;
                case 'promocion':
                    $param = 'pr.promocion_descripcion';
                    $join = " LEFT JOIN promocion_producto pp ON p.producto_id = pp.producto_id LEFT JOIN promocion pr ON pp.promocion_id = pr.promocion_id";
                    if(!$this->existe_join($join)){
                        array_push($this->joins, $join);
                        $agregar = true;
                    }
                    break;
                case 'tipo_cuenta':
                    $this->extras['tipo_cuenta'] = $this->tipo_cuenta;
                    array_splice($this->parametros_abrev, $indice, 1);
                    $agregar = false;
                default:
                    $agregar = false;
            }
            if($agregar){
                //$this->parametros_abrev[count($this->parametros_abrev)] = $param;
                array_push($this->parametros, $param);
            }
        }
        //$this->parametros_abrev = $_POST['parametros_abrev'];
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
    
    protected function obtener_productos(){
        $consulta_productos = "SELECT ";
        for($i = 0; $i < count($this->parametros_abrev); $i++){
            $this->parametros_abrev[$i] = mysqli_real_escape_string($this->sql_con, $this->parametros_abrev[$i]);
            if($i > 0){
                $consulta_productos = $consulta_productos . ", ";
            }
            $consulta_productos = $consulta_productos . $this->parametros[$i] . " as " . $this->parametros_abrev[$i];
        }
        $consulta_productos = $consulta_productos . " FROM producto p ";
        foreach($this->joins as $join){
            $consulta_productos = $consulta_productos . $join;
        }
        //print_r($this->link);
        //echo $consulta_productos;
        $result_productos = $this->sql_con->query($consulta_productos);
        if($result_productos === false){
            trigger_error("Ha ocurrido un error");
        }else{
            $i = 0;
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
    
    protected function push_extras(){
        foreach($this->extras as $indice=>$extra){
            $this->datos[$indice] = $extra;    
        }
    }
    
    public function __destruct(){
        echo json_encode($this->datos);
    }

}

?>