<?php

$materia = new Materias();

class Materias{

    protected $link, $sql_con;
    protected $sucursal, $tipo_cuenta;
    protected $parametros = array(), $parametros_abrev = array(), $joins = array(), $datos = array(), $extras = array();
    protected $obtener_productos = false;

    public function __construct(){
        session_start();
        require('../hosts.php');
        require('conexion_new.php');
        $hosteo = new Host();
        $hosteo->obtener_conexion(0);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $this->obtener_parametros();
        $this->obtener_materia_prima();
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
                $param = 'mp.materia_prima_id';
                $agregar = true;
                break;
            case 'codigo':
                $param = 'mp.materia_prima_codigo';
                $agregar = true;
                break;
            case 'nombre':
                $param = 'mp.materia_prima_nombre';
                $agregar = true;
                break;
            case 'unidad':
                $param = 'mp.materia_prima_unidad';
                $agregar = true;
                break;
            case 'descripcion':
                $param = 'mp.materia_prima_descripcion';
                $agregar = true;
                break;
            case 'stock_r':
                $param = 'ms.materia_prima_sucursal_stock_real';
                $join = " INNER JOIN materia_prima_sucursal ms ON ms.sucursal_id = $this->sucursal AND ms.materia_prima_id = mp.materia_prima_id";
                if(!$this->existe_join($join)){
                    array_push($this->joins, $join);
                    $agregar = true;
                }
                break;
            case 'stock_m':
                $param = 'ms.materia_prima_sucursal_stock_minimo';
                $join = " INNER JOIN materia_prima_sucursal ms ON ms.sucursal_id = $this->sucursal AND ms.materia_prima_id = mp.materia_prima_id";
                if(!$this->existe_join($join)){
                    array_push($this->joins, $join);
                    $agregar = true;
                }
                break;
            case 'sucursal':
                $param = 'ms.sucursal_id';
                $join = " INNER JOIN materia_prima_sucursal ms ON ms.sucursal_id = $this->sucursal AND ms.materia_prima_id = mp.materia_prima_id";
                if(!$this->existe_join($join)){
                    array_push($this->joins, $join);
                    $agregar = true;
                }
                break;
            case 'u_medida':
                $param = 'mp.materia_prima_unidad_medida';
                $agregar = true;
                break;
            case 'productos':
                $this->obtener_productos = true;
                $agregar = false;
                break;
            default:
                $agregar = false;
            }
            if($agregar){
                //$this->parametros_abrev[count($this->parametros_abrev)] = $param;
                array_push($this->parametros, $param);
            }else{
                array_splice($this->parametros_abrev, $indice, 1);
            }
        }
        //$this->parametros_abrev = $_POST['parametros_abrev'];
    }
    
    protected function obtener_materia_prima(){
        $consulta_productos = "SELECT ";
        for($i = 0; $i < count($this->parametros_abrev); $i++){
            $this->parametros_abrev[$i] = mysqli_real_escape_string($this->sql_con, $this->parametros_abrev[$i]);
            if($i > 0){
                $consulta_productos = $consulta_productos . ", ";
            }
            $consulta_productos = $consulta_productos . $this->parametros[$i] . " as " . $this->parametros_abrev[$i];
        }
        $consulta_productos = $consulta_productos . " FROM materia_prima mp ";
        foreach($this->joins as $join){
            $consulta_productos = $consulta_productos . $join;
        }
        $consulta_productos .= " GROUP BY materia_prima_codigo";
        //echo $consulta_productos;
        $result_productos = $this->sql_con->query($consulta_productos);
        if($result_productos === false){
            trigger_error("Ha ocurrido un error");
        }else{
            $i = 0;
            $this->datos['materia_prima'] = array();
            while($row_productos = $result_productos->fetch_assoc()){
                $dato = array();
                for($i = 0; $i < count($this->parametros_abrev); $i++){
                    $dato[$this->parametros_abrev[$i]] = $row_productos[$this->parametros_abrev[$i]];
                }
                $dato['productos'] = $this->obtener_productos_asignados($row_productos['codigo']);
                array_push($this->datos['materia_prima'], $dato);
            }
        }
    }
    
    protected function obtener_productos_asignados($codigo){
        $m_id = $this->obtener_materia_id($codigo);
        $consulta = "SELECT producto_id as p_id FROM materia_prima_producto WHERE materia_prima_id = $m_id";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return 0;
        }else{
            $datos = array();
            while($row = $rs->fetch_assoc()){
                $p_id = $row['p_id'];
                $consulta_producto = "SELECT producto_codigo as codigo, producto_nombre as nombre FROM producto WHERE producto_id = $p_id";
                $rs_producto = $this->sql_con->query($consulta_producto);
                if($rs_producto === false){
                    return 0;
                }else{
                    $row_producto = $rs_producto->fetch_assoc();
                    $dato = array();
                    $dato['codigo'] = $row_producto['codigo'];
                    $dato['nombre'] = $row_producto['nombre'];
                    array_push($datos, $dato);
                }
                $rs_producto->close();
            }
        }
        $rs->close();
        return $datos;
    }
    
    protected function obtener_materia_id($codigo){
        $consulta = "SELECT materia_prima_id as m_id FROM materia_prima WHERE materia_prima_codigo = '$codigo'";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return 0;
        }else{
            $row = $rs->fetch_assoc();
            $m_id = $row['m_id'];
            $rs->close();
            return $m_id;
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