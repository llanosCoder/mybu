<?php

$resumen = new ResumenProductos();

class ResumenProductos{

    protected $link, $sql_con;
    protected $parametros = array(), $datos = array();
    protected $sucursal, $tipo_cuenta, $usuario;

    public function __construct(){
        session_start();
        //ini_set('display_errors', 'on');
        require('../hosts.php');
        require('conexion_new.php');
        $this->tipo_cuenta = $_SESSION['tipo_cuenta'];
        $this->usuario = $_SESSION['id'];
        $hosteo = new Host();
        $hosteo->obtener_conexion(0);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $this->obtener_parametros();
    }
    
    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    
    protected function obtener_parametros(){
        $this->parametros = $_POST['parametros'];
        $this->sucursal = $_SESSION['sucursal'];
        foreach($this->parametros as $indice=>$valor){
            switch($valor){
                case 1:
                    $consulta = "SELECT count(producto_id) as cont FROM producto";
                    $dato = 'productos';
                    $this->procesar($consulta, $dato, true);
                    break;
                case 2:
                    $consulta = "SELECT count(producto_id) as cont FROM promocion_producto";
                    $dato = 'productos_promocion';
                    $this->procesar($consulta, $dato, true);
                    break;
                case 3:
                    $consulta = "SELECT count(producto_id) as cont FROM producto_sucursal WHERE producto_sucursal_stock_real <= producto_sucursal_stock_minimo + producto_sucursal_stock_minimo / 4 AND sucursal_id = $this->sucursal";
                    $dato = 'productos_bajo_stock';
                    $this->procesar($consulta, $dato, true);
                    break;
                case 4:
                    $consulta = "SELECT count(producto_id) as cont FROM producto_sucursal WHERE producto_sucursal_stock_real <= 0 AND sucursal_id = $this->sucursal";
                    $dato = 'productos_sin_stock';
                    $this->procesar($consulta, $dato, true);
                    break;
                case 5:
                    $fecha = date('Y-m-d');
                    $consulta = "SELECT p.producto_nombre as nombre, count(vp.venta_id) as cont FROM venta_producto vp JOIN producto p ON vp.producto_id = p.producto_id JOIN venta v ON vp.venta_id = v.venta_id WHERE v.venta_fecha LIKE '$fecha%'";
                    $dato = 'producto_popular_dia';
                    $this->procesar($consulta, $dato, true);
                    break;
                case 6:
                    $fecha = date('Y-m');
                    $consulta = "SELECT p.producto_codigo as codigo, p.producto_nombre as nombre, count(vp.venta_id) as cont FROM venta_producto vp JOIN producto p ON vp.producto_id = p.producto_id JOIN venta v ON vp.venta_id = v.venta_id WHERE v.venta_fecha LIKE '$fecha%'";
                    $dato = 'producto_popular_mes';
                    $this->procesar($consulta, $dato, true);
                    break;
                case 7:
                    $fecha = strtotime('-1 month', strtotime(date('Y-m-d')));
                    $mes = date('m', $fecha);
                    $anio = date('Y', $fecha);
                    $dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);
                    $fecha = strtotime('-' . $dias_mes . ' day', strtotime(date('Y-m-d H:i:s')));
                    if($this->tipo_cuenta == 1){
                        $consulta = "SELECT p.producto_codigo as codigo, p.producto_nombre as nombre, ps.producto_sucursal_precio_unitario as precio, ROUND(count(v.venta_id) / 2, 0) * vp.producto_cantidad as cantidad, DATE(v.venta_fecha) as fecha FROM venta v JOIN venta_producto vp ON v.venta_id = vp.venta_id JOIN producto p ON vp.producto_id = p.producto_id JOIN producto_sucursal ps ON vp.producto_id = ps.producto_id WHERE venta_fecha LIKE ? GROUP BY v.venta_id ORDER BY cantidad DESC LIMIT 1";
                    }else{
                        $consulta = "SELECT p.producto_codigo as codigo, p.producto_nombre as nombre, ps.producto_sucursal_precio_unitario as precio, ROUND(count(v.venta_id) / 2, 0) * vp.producto_cantidad as cantidad, DATE(v.venta_fecha) as fecha FROM venta v JOIN venta_producto vp ON v.venta_id = vp.venta_id JOIN producto p ON vp.producto_id = p.producto_id JOIN producto_sucursal ps ON vp.producto_id = ps.producto_id WHERE venta_fecha LIKE ? AND v.usuario_venta_id = $this->usuario GROUP BY v.venta_id ORDER BY cantidad DESC LIMIT 1";
                    }
                    $this->obtener_estadistica_tiempo($fecha, $dias_mes, 'day', $consulta, 'Y-m-d');
                    break;
                case 8:
                    $fecha = strtotime('-24 hour', strtotime(date('Y-m-d H:i:s')));
                    if($this->tipo_cuenta == 1){
                        $consulta = "SELECT p.producto_codigo as codigo, p.producto_nombre as nombre, ps.producto_sucursal_precio_unitario as precio, ROUND(count(v.venta_id) / 2, 0) * vp.producto_cantidad as cantidad, v.venta_fecha as fecha FROM venta v JOIN venta_producto vp ON v.venta_id = vp.venta_id JOIN producto p ON vp.producto_id = p.producto_id JOIN producto_sucursal ps ON vp.producto_id = ps.producto_id WHERE venta_fecha LIKE ? GROUP BY v.venta_id ORDER BY cantidad DESC LIMIT 1";
                    }else{
                        $consulta = "SELECT p.producto_codigo as codigo, p.producto_nombre as nombre, ps.producto_sucursal_precio_unitario as precio, ROUND(count(v.venta_id) / 2, 0) * vp.producto_cantidad as cantidad, v.venta_fecha as fecha FROM venta v JOIN venta_producto vp ON v.venta_id = vp.venta_id JOIN producto p ON vp.producto_id = p.producto_id JOIN producto_sucursal ps ON vp.producto_id = ps.producto_id WHERE venta_fecha AND v.usuario_venta_id = $this->usuario LIKE ? GROUP BY v.venta_id ORDER BY cantidad DESC LIMIT 1";
                    }
                    $this->obtener_estadistica_tiempo2($fecha, 24, 'hour', $consulta, 'Y-m-d H');
                    break;
                case 9:
                    $sucursal = $_SESSION['sucursal'];
                    $consulta = "SELECT p.producto_codigo as codigo, p.producto_nombre as nombre, ps.producto_sucursal_stock_real as stock, ps.producto_sucursal_stock_minimo as stock_m, s.sucursal_direccion as sucursal FROM producto p JOIN producto_sucursal ps ON p.producto_id = ps.producto_id AND ps.sucursal_id = $sucursal JOIN sucursal s ON ps.sucursal_id = s.sucursal_id WHERE ps.producto_sucursal_stock_real < ps.producto_sucursal_stock_minimo";
                    $this->procesar($consulta, 'productos', false);
                    break;
                case 10:
                    $sucursal = $_SESSION['sucursal'];
                    $consulta = "SELECT p.producto_codigo as codigo, p.producto_nombre as nombre, ps.producto_sucursal_stock_real as stock, s.sucursal_direccion as sucursal FROM producto p JOIN producto_sucursal ps ON p.producto_id = ps.producto_id AND ps.sucursal_id = $sucursal JOIN sucursal s ON ps.sucursal_id = s.sucursal_id WHERE ps.producto_sucursal_stock_real <= 0";
                    $this->procesar($consulta, 'productos', false);
                    break;
                case 11:
                    $sucursal = $_SESSION['sucursal'];
                    $consulta = "SELECT p.producto_codigo as codigo, p.producto_nombre as nombre, ps.producto_sucursal_stock_real as stock, s.sucursal_direccion as sucursal FROM producto p JOIN producto_sucursal ps ON p.producto_id = ps.producto_id AND ps.sucursal_id = $sucursal JOIN sucursal s ON ps.sucursal_id = s.sucursal_id WHERE ps.producto_sucursal_stock_real > ps.producto_sucursal_stock_minimo";
                    $this->procesar($consulta, 'productos', false);
                    break;
                default:
                    $this->datos['resultado'] = 0;
                    exit();
                    break;
            }
            
        }
    }
    
    protected function procesar($consulta, $dato, $con_resultado){
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            $resultado = 0;
        }else{
            $resultado = 1;
            $this->datos[$dato] = array();
            while($row = $rs->fetch_assoc()){
                array_push($this->datos[$dato], $row);
            }
        }
        if($con_resultado){
            $arr = array();
            $arr['resultado'] = $resultado;
            array_push($this->datos[$dato], $arr);
        }
    }
    
    protected function obtener_estadistica_tiempo2($fecha, $intervalo, $tiempo, $consulta, $formato_fecha){
        $fecha_inicial = $fecha;
        //$fecha = strtotime('+1' . $tiempo, $fecha);
        $nfecha = date($formato_fecha, $fecha);
        $this->datos['productos'] = array();
        for($i = 0; $i <= $intervalo; $i++){
            $bindear = "$nfecha%";
            if($this->tipo_cuenta == 1){
                $consulta = "SELECT p.producto_codigo as codigo, p.producto_nombre as nombre, ps.producto_sucursal_precio_unitario as precio, ROUND(count(v.venta_id) / 2, 0) * vp.producto_cantidad as cantidad, v.venta_fecha as fecha FROM venta v JOIN venta_producto vp ON v.venta_id = vp.venta_id JOIN producto p ON vp.producto_id = p.producto_id JOIN producto_sucursal ps ON vp.producto_id = ps.producto_id WHERE venta_fecha LIKE '$bindear' GROUP BY v.venta_id ORDER BY cantidad DESC LIMIT 1";
            }else{
                $consulta = "SELECT p.producto_codigo as codigo, p.producto_nombre as nombre, ps.producto_sucursal_precio_unitario as precio, ROUND(count(v.venta_id) / 2, 0) * vp.producto_cantidad as cantidad, v.venta_fecha as fecha FROM venta v JOIN venta_producto vp ON v.venta_id = vp.venta_id JOIN producto p ON vp.producto_id = p.producto_id JOIN producto_sucursal ps ON vp.producto_id = ps.producto_id WHERE venta_fecha LIKE '$bindear' AND v.usuario_venta_id = $this->usuario GROUP BY v.venta_id ORDER BY cantidad DESC LIMIT 1";
            }
            //echo "\n $nfecha \n";
            $rs = $this->sql_con->query($consulta);
            if($rs->num_rows > 0){
                while($row = $rs->fetch_assoc()){
                    $dato = array();
                    foreach($row as $indice=>$fila){
                        if($indice == 'fecha' && $fila == null){
                            $fila = date($formato_fecha, strtotime($nfecha));
                        }else{
                            if($indice == 'fecha' && $fila != null){
                                $fila = date($formato_fecha, strtotime($fila));
                            }
                        }
                        if($indice == 'cantidad' && $fila == null)
                            $fila = 0;
                        $dato[$indice] = $fila;
                    }
                    $dato['tipo_fecha'] = $tiempo;
                    array_push($this->datos['productos'], $dato);
                }
            }else{
                $dato = array();
                $dato['resultado'] = 0;
                $dato['fecha'] = $nfecha;
                $dato['tipo_fecha'] = $tiempo;
                array_push($this->datos['productos'], $dato);
            }
            $fecha = strtotime('+1' . $tiempo, $fecha);
            $nfecha = date($formato_fecha, $fecha);
        }
        $anio = date('Y', $fecha_inicial);
        $mes = date('m', $fecha_inicial);
        $dia = date('d', $fecha_inicial);
        $hora = date('H', $fecha_inicial);
        $minuto = date('i', $fecha_inicial);
        $segundo = date('s', $fecha_inicial);
        $this->datos['tiempo'] = array();
        $arr_tiempo['anio'] = $anio;
        $arr_tiempo['mes'] = $mes;
        $arr_tiempo['dia'] = $dia;
        $arr_tiempo['hora'] = $hora;
        $arr_tiempo['minuto'] = $minuto;
        $arr_tiempo['segundo'] = $segundo;
        array_push($this->datos['tiempo'], $arr_tiempo);
    }
    
    protected function obtener_estadistica_tiempo($fecha, $intervalo, $tiempo, $consulta, $formato_fecha){
        $fecha_inicial = $fecha;
        //$fecha = strtotime('+1' . $tiempo, $fecha);
        $nfecha = date($formato_fecha, $fecha);
        $this->datos['productos'] = array();
        for($i = 0; $i <= $intervalo; $i++){
            $stmt = $this->sql_con->prepare($consulta);
            $bindear = "$nfecha%";
            if($this->tipo_cuenta == 1){
                $consulta = "SELECT p.producto_codigo as codigo, p.producto_nombre as nombre, ps.producto_sucursal_precio_unitario as precio, ROUND(count(v.venta_id) / 2, 0) * vp.producto_cantidad as cantidad, DATE(v.venta_fecha) as fecha FROM venta v JOIN venta_producto vp ON v.venta_id = vp.venta_id JOIN producto p ON vp.producto_id = p.producto_id JOIN producto_sucursal ps ON vp.producto_id = ps.producto_id WHERE venta_fecha LIKE '$bindear' GROUP BY v.venta_id ORDER BY cantidad DESC LIMIT 1";
            }else{
                $consulta = "SELECT p.producto_codigo as codigo, p.producto_nombre as nombre, ps.producto_sucursal_precio_unitario as precio, ROUND(count(v.venta_id) / 2, 0) * vp.producto_cantidad as cantidad, DATE(v.venta_fecha) as fecha FROM venta v JOIN venta_producto vp ON v.venta_id = vp.venta_id JOIN producto p ON vp.producto_id = p.producto_id JOIN producto_sucursal ps ON vp.producto_id = ps.producto_id WHERE venta_fecha LIKE '$bindear' AND v.usuario_venta_id = $this->usuario GROUP BY v.venta_id ORDER BY cantidad DESC LIMIT 1";
            }
            //echo "\n $nfecha \n";
            $rs = $this->sql_con->query($consulta);
            if($rs->num_rows > 0){
                while($row = $rs->fetch_assoc()){
                    $dato = array();
                    foreach($row as $indice=>$fila){
                        if($indice == 'fecha' && $fila == null){
                            $fila = date($formato_fecha, strtotime($nfecha));
                        }else{
                            if($indice == 'fecha' && $fila != null){
                                $fila = date($formato_fecha, strtotime($fila));
                            }
                        }
                        if($indice == 'cantidad' && $fila == null)
                            $fila = 0;
                        $dato[$indice] = $fila;
                    }
                    $dato['tipo_fecha'] = $tiempo;
                    array_push($this->datos['productos'], $dato);
                }
            }else{
                $dato = array();
                $dato['resultado'] = 0;
                $dato['fecha'] = $nfecha;
                $dato['tipo_fecha'] = $tiempo;
                array_push($this->datos['productos'], $dato);
            }
            $fecha = strtotime('+1' . $tiempo, $fecha);
            $nfecha = date($formato_fecha, $fecha);
        }
        $anio = date('Y', $fecha_inicial);
        $mes = date('m', $fecha_inicial);
        $dia = date('d', $fecha_inicial);
        $hora = date('H', $fecha_inicial);
        $minuto = date('i', $fecha_inicial);
        $segundo = date('s', $fecha_inicial);
        $this->datos['tiempo'] = array();
        $arr_tiempo['anio'] = $anio;
        $arr_tiempo['mes'] = $mes;
        $arr_tiempo['dia'] = $dia;
        $arr_tiempo['hora'] = $hora;
        $arr_tiempo['minuto'] = $minuto;
        $arr_tiempo['segundo'] = $segundo;
        array_push($this->datos['tiempo'], $arr_tiempo);
    }
    
    public function __destruct(){
        echo json_encode($this->datos);
    }

}

?>