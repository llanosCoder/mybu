<?php

$accion = $_POST['accion'];
$estadistica = new Estadisticas($accion);

class Estadisticas{

    protected $link, $sql_con;
    protected $datos = array(), $tipo_cuenta, $usuario;

    public function __construct($accion){
        session_start();
        require('../hosts.php');
        require('conexion_new.php');
        date_default_timezone_set('America/Buenos_Aires');
        $this->tipo_cuenta = $_SESSION['tipo_cuenta'];
        $this->usuario = $_SESSION['id'];
        $hosteo = new Host();
        $hosteo->obtener_conexion(0);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        for($i = 0; $i < count($accion); $i++){
            switch($accion[$i]){
                case 'ventas_tiempo_real':
                    $this->obtener_ventas_tiempo_real();
                    break;
                case 'ventas_minuto':
                    $this->obtener_ventas_minuto();
                    break;
                case 'ventas_hora':
                    $this->ventas_hora();
                    break;
                case 'ventas_dia':
                    $this->ventas_dia();
                    break;
                case 'ventas_mes':
                    $this->ventas_mes();
                    break;
                case 'ranking_semanal':
                    $this->ranking_semanal();
                    break;
                case 'comparativo_mes':
                    $this->comparativo_mes();
                    break;
                case 'ranking_vendedores':
                    $this->ranking_vendedores();
                    break;
                case 'fluctuacion_mes':
                    $this->fluctuacion_mes();
                    break;
                case 'promedio_compra':
                    $this->promedio_compra();
                    break;
                case 'total_ventas_dia':
                    $this->total_ventas_dia();
                    break;
                case 'total_ventas_mes':
                    $this->total_ventas_mes();
                    break;
                case 'total_productos_dia':
                    $this->total_productos_dia();
                    break;
                case 'total_productos_mes':
                    $this->total_productos_mes();
                    break;
            }
        }
    }
    
    protected function obtener_ventas_tiempo_real(){
        $fecha = date('Y-m-d H:i');
        $hora = date('H');
        $min = date('i');
        $this->datos['venta_tiempo_real'] = array();
        for($i = 0; $i < 60; $i++){
            if($i > 0)
                $nueva_fecha = strtotime('-1 minute', strtotime($fecha)) ;
            else
                $nueva_fecha = strtotime($fecha);
            $nueva_fecha = date ( 'Y-m-d H:i:s' , $nueva_fecha );
            if($this->tipo_cuenta == 1){
                $consulta = "SELECT SUM(venta_neto) as cont FROM venta WHERE venta_fecha > '$nueva_fecha' AND venta_fecha < '$fecha'";
            }else{
                $consulta = "SELECT SUM(venta_neto) as cont FROM venta WHERE venta_fecha > '$nueva_fecha' AND venta_fecha < '$fecha' AND usuario_venta_id = $this->usuario";
            }
            $rs = $this->sql_con->query($consulta);
            if($rs === false){
                $resultado = 0;
                exit();
            }else{
                $resultado = mysqli_num_rows($rs);
                $dato = array();
                while($row = $rs->fetch_assoc()){
                    $dato['fecha'] = $nueva_fecha;
                    if($row['cont'] == '')
                        $row['cont'] = 0;
                    $dato['cont'] = $row['cont'];
                }
            }
            $fecha = $nueva_fecha;
            $this->datos['resultado'] = $resultado;
            array_push($this->datos['venta_tiempo_real'], $dato);
        }
        $this->datos['tiempo'] = array();
        $arr_tiempo['hora'] = $hora;
        $arr_tiempo['min'] = $min;
        array_push($this->datos['tiempo'], $arr_tiempo);
    }
    
    protected function obtener_ventas_minuto(){
        $fecha = date('Y-m-d H:i');
        $hora = date('H');
        $min = date('i');
        $this->datos['venta_tiempo_real'] = array();
        $nueva_fecha = strtotime('-1 minute', strtotime($fecha)) ;
        $nueva_fecha = date ( 'Y-m-d H:i:s' , $nueva_fecha );
        if($this->tipo_cuenta == 1){
                $consulta = "SELECT SUM(venta_neto) as cont FROM venta WHERE venta_fecha > '$nueva_fecha' AND venta_fecha < '$fecha'";
            }else{
                $consulta = "SELECT SUM(venta_neto) as cont FROM venta WHERE venta_fecha > '$nueva_fecha' AND venta_fecha < '$fecha'  AND usuario_venta_id = $this->usuario";
            }
        
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            $resultado = 0;
            exit();
        }else{
            $resultado = mysqli_num_rows($rs);
            $dato = array();
            while($row = $rs->fetch_assoc()){
                if($row['cont'] == '')
                    $row['cont'] = 0;
                array_push($dato, $row['cont']);
            }
        }
        $fecha = $nueva_fecha;
        $this->datos['resultado'] = $resultado;
        array_push($this->datos['venta_tiempo_real'], $dato);
        $this->datos['tiempo'] = array();
        $arr_tiempo['hora'] = $hora;
        $arr_tiempo['min'] = $min;
        array_push($this->datos['tiempo'], $arr_tiempo);
    }
    
    protected function ventas_hora(){
        $fecha = date('Y-m-d H:i');
        $hora = date('H', strtotime($fecha));
        $mes = date('m', strtotime($fecha));
        $dia = strtotime('-1 day', strtotime($fecha));
        $dia = date('d', $dia);
        $this->datos['venta_hora'] = array();
        for($i = 0; $i < 24; $i++){
            $nueva_fecha = strtotime('-1 hour', strtotime($fecha));
            $nueva_fecha = date ( 'Y-m-d H:i' , $nueva_fecha );
            if($this->tipo_cuenta == 1){
                $consulta = "SELECT SUM(venta_neto) as cont, venta_fecha as fecha FROM venta WHERE venta_fecha > '$nueva_fecha' AND venta_fecha < '$fecha'";
            } else {
                $consulta = "SELECT SUM(venta_neto) as cont, venta_fecha as fecha FROM venta WHERE venta_fecha > '$nueva_fecha' AND venta_fecha < '$fecha' AND usuario_venta_id = $this->usuario";
            }
            $rs = $this->sql_con->query($consulta);
            if($rs === false){
                $resultado = 0;
                exit();
            }else{
                $resultado = mysqli_num_rows($rs);
                $dato = array();
                while($row = $rs->fetch_assoc()){
                    if($row['cont'] == '')
                        $row['cont'] = 0;
                    $dato['cont'] = $row['cont'];
                    $fecha_vta = $row['fecha'];
                    if($fecha_vta == null)
                        $fecha_vta = date ( 'Y-m-d H' , strtotime($fecha));
                    else
                        $fecha_vta = date ( 'Y-m-d H' , strtotime($row['fecha']));
                    $dato['fecha'] = $fecha_vta;
                }
            }
            $fecha = $nueva_fecha;
            $this->datos['resultado'] = $resultado;
            array_push($this->datos['venta_hora'], $dato);
        }
        $this->datos['tiempo'] = array();
        $hora = date('H', strtotime('+1 hour', strtotime($fecha)));
        
        $arr_tiempo['hora'] = $hora;
        $arr_tiempo['mes'] = $mes;
        $arr_tiempo['dia'] = $dia;
        array_push($this->datos['tiempo'], $arr_tiempo);
    }
    
    protected function ventas_dia(){
        $fecha = date('Y-m-d');
        $dia = date('Y-m-d');
        $ndia = strtotime($dia);
        $dia = date ( 'd' , $ndia );
        $mes_anterior = strtotime('-1 month', strtotime($fecha));
        $mes = date('m', $mes_anterior);
        $anio = date('Y', strtotime($fecha));
        $this->datos['venta_dia'] = array();
        $dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);
        $nueva_fecha = strtotime('-'.$dias_mes.' day', strtotime($fecha));
        $fecha = date ( 'Y-m-d' , $nueva_fecha );
        $nueva_fecha = date('Y-m-d', $nueva_fecha);
        for($i = 0; $i <= $dias_mes; $i++){
            if($this->tipo_cuenta == 1){
                $consulta = "SELECT SUM(venta_neto) as cont, count(*) as cant, venta_fecha as fecha FROM venta WHERE venta_fecha LIKE '$nueva_fecha%'";
            }else{
                $consulta = "SELECT SUM(venta_neto) as cont, count(*) as cant, venta_fecha as fecha FROM venta WHERE venta_fecha LIKE '$nueva_fecha%' AND usuario_venta_id = $this->usuario";
            }
            $rs = $this->sql_con->query($consulta);
            if($rs === false){
                $resultado = 0;
                exit();
            }else{
                $resultado = mysqli_num_rows($rs);
                $dato = array();
                while($row = $rs->fetch_assoc()){
                    if($row['cont'] == '')
                        $row['cont'] = 0;
                    $dato['cont'] = $row['cont'];
                    $dato['cant'] = $row['cant'];
                    $fecha_vta = $row['fecha'];
                    if($fecha_vta == null){
                        $fecha_vta = $nueva_fecha;
                    }else{
                        //$fecha_vta = $fecha;
                        $fecha_vta = date ( 'Y-m-d' , strtotime($row['fecha']));
                    }
                    $dato['fecha'] = $fecha_vta;
                }
            }
            $nueva_fecha = strtotime('+1 day', strtotime($fecha));
            $nueva_fecha = date ( 'Y-m-d' , $nueva_fecha );
            $fecha = $nueva_fecha;
            $this->datos['resultado'] = $resultado;
            if(strtotime($fecha_vta) <= strtotime(date("d-m-Y H:i:00"))){
                array_push($this->datos['venta_dia'], $dato);
            }
        }
        $this->datos['tiempo'] = array();
        $arr_tiempo['dia'] = $dia;
        array_push($this->datos['tiempo'], $arr_tiempo);
    }
    
    protected function ventas_mes(){
        $fecha = date('Y-m-d');
        //$mes = date('Y-m-d');
        $nmes = strtotime('-12 month', strtotime($fecha));
        $mes = date ( 'm' , $nmes );
        $anio = date ( 'Y' , $nmes );
        $this->datos['venta_mes'] = array();
        $nueva_fecha = strtotime('-12 month', strtotime($fecha));
        $fecha = date ( 'Y-m' , $nueva_fecha );
        for($i = 0; $i < 12; $i++){
            $nueva_fecha = strtotime('+1 month', strtotime($fecha));
            $nueva_fecha = date ( 'Y-m' , $nueva_fecha );
            if($this->tipo_cuenta == 1){
                $consulta = "SELECT SUM(venta_neto) as cont, venta_fecha as fecha FROM venta WHERE venta_fecha LIKE '$nueva_fecha%'";
            }else{
                $consulta = "SELECT SUM(venta_neto) as cont, venta_fecha as fecha FROM venta WHERE venta_fecha LIKE '$nueva_fecha%' AND usuario_venta_id = $this->usuario";
            }
            $rs = $this->sql_con->query($consulta);
            if($rs === false){
                $resultado = 0;
                exit();
            }else{
                $resultado = mysqli_num_rows($rs);
                $dato = array();
                while($row = $rs->fetch_assoc()){
                    if($row['cont'] == '')
                        $row['cont'] = 0;
                    $dato['cont'] = $row['cont'];
                    
                    $fecha_vta = $row['fecha'];
                    if($fecha_vta == null){
                        $fecha_vta = date ( 'Y' , strtotime($fecha));
                    }else{
                        $fecha_vta = date ( 'Y' , strtotime($row['fecha']));
                    }
                    $mes_palabra = date('F', strtotime($nueva_fecha));
                    $fecha_vta = $fecha_vta. " " . $this->obtener_mes($mes_palabra);
                    $dato['fecha'] = $fecha_vta;
                }
            }
            $fecha = $nueva_fecha;
            $this->datos['resultado'] = $resultado;
            array_push($this->datos['venta_mes'], $dato);
        }
        $this->datos['tiempo'] = array();
        $arr_tiempo['mes'] = $mes;
        $arr_tiempo['anio'] = $anio;
        array_push($this->datos['tiempo'], $arr_tiempo);
    }
    
    protected function ranking_semanal(){
        $fecha = date('Y-m-d');
        $this->datos['ranking_semanal'] = array();
        for($i = 0; $i < 7; $i++){
            if($this->tipo_cuenta == 1){
                $consulta = "SELECT SUM(venta_neto) as cont FROM venta WHERE venta_fecha LIKE '$fecha%'";
            }else{
                $consulta = "SELECT SUM(venta_neto) as cont FROM venta WHERE venta_fecha LIKE '$fecha%' AND usuario_venta_id = $this->usuario";
            }
            $rs = $this->sql_con->query($consulta);
            if($rs === false){
                $resultado = 0;
            }else{
                $resultado = mysqli_num_rows($rs);
                $dato = array();
                while($row = $rs->fetch_assoc()){
                    if($row['cont'] == '')
                        $row['cont'] = 0;
                    $dato['cont'] = $row['cont'];
                    $dia = date('l', strtotime($fecha));
                    $dato['fecha'] = $this->obtener_semana($dia);
                }
            }
            $nueva_fecha = strtotime('-1 day', strtotime($fecha));
            $fecha = date ( 'Y-m-d' , $nueva_fecha );
            $this->datos['resultado'] = $resultado;
            array_push($this->datos['ranking_semanal'], $dato);
        }
        $dia_semana = date('l');
        $dia = $this->obtener_semana($dia_semana);
    }
    
    protected function comparativo_mes(){
        $fecha = date('Y-m-d');
        $resulado = 1;
        $this->datos['comparativo'] = array();
        $this->datos['meses'] = array();
        for($i = 0; $i < 2; $i++){
            $nueva_fecha = strtotime('-1 month', strtotime($fecha));
            $fecha = date ( 'Y-m-d' , $nueva_fecha );
            $anio = date('Y', strtotime($fecha));
            $mes = date('m', strtotime($fecha));
            $cantidad_dias = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);
            $datoso = array();
            $mes_palabra = date('F', strtotime($fecha));
            array_push($this->datos['meses'], $this->obtener_mes($mes_palabra));
            for($j = 0; $j <= $cantidad_dias; $j++){
                if($j < 10)
                    $fecha_consulta = "$anio-$mes-0$j";
                else
                    $fecha_consulta = "$anio-$mes-$j";
                if($this->tipo_cuenta == 1){
                    $consulta = "SELECT SUM(venta_neto) as cont, venta_fecha as fecha FROM venta WHERE venta_fecha LIKE '$fecha_consulta%'";
                }else{
                    $consulta = "SELECT SUM(venta_neto) as cont, venta_fecha as fecha FROM venta WHERE venta_fecha LIKE '$fecha_consulta%' AND usuario_venta_id = $this->usuario";
                }
                $rs = $this->sql_con->query($consulta);
                if($rs === false || $resultado = 0){
                    $resultado = 0;
                }else{
                    $resultado = mysqli_num_rows($rs);
                    $dato = array();
                    while($row = $rs->fetch_assoc()){
                        if($row['cont'] == '')
                            $row['cont'] = 0;
                        $dato['cont'] = $row['cont'];
                        $fecha_vta = $row['fecha'];
                        if($fecha_vta == null){
                            $fecha_vta = date ( 'Y' , strtotime($fecha));
                        }else{
                            $fecha_vta = date ( 'Y' , strtotime($row['fecha']));
                        }
                        $dato['fecha'] = $row['fecha'];
                    }
                    array_push($datoso, $dato);
                }
            }
            array_push($this->datos['comparativo'], $datoso);
            $this->datos['tiempo'] = array();
            $arr_tiempo['anio'] = $anio;
            array_push($this->datos['tiempo'], $arr_tiempo);
        }
        $this->datos['resultado'] = $resultado;
    }
    
    protected function ranking_vendedores(){
        $fecha = date('Y-m');
        $this->datos['ranking_vendedores'] = array();
        $consulta = "SELECT SUM(venta_neto) as cont, u.usuario_nombres as nombres, u.usuario_apellidos as apellidos FROM venta v JOIN usuario u ON v.usuario_venta_id = u.usuario_id WHERE venta_fecha LIKE '$fecha%' GROUP BY v.usuario_venta_id LIMIT 5";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            $resultado = 0;
        }else{
            $resultado = mysqli_num_rows($rs);
            $dato = array();
            while($row = $rs->fetch_assoc()){
                if($row['cont'] == '')
                        $row['cont'] = 0;
                $dato['cont'] = $row['cont'];
                $nombres = explode(" ", $row['nombres']);
                $apellidos = explode(" ", $row['apellidos']);
                $dato['nombre'] = $nombres[0] . " " . $apellidos[0];
                array_push($this->datos['ranking_vendedores'], $dato);
            }
        }
        $this->datos['resultado'] = $resultado;
    }
    
    protected function fluctuacion_mes(){
        $fecha = date('Y-m-d');
        $this->datos['fluctuacion_mes'] = array();
        for($i = 0; $i < 2; $i++){
            
            $nueva_fecha = strtotime('-1 month', strtotime($fecha));
            $nueva_fecha = date('Y-m-d', $nueva_fecha);
            if($this->tipo_cuenta == 1){
                $consulta = "SELECT SUM(venta_neto) as cont FROM venta WHERE venta_fecha > '$nueva_fecha' AND venta_fecha < '$fecha'";
            }else{
                $consulta = "SELECT SUM(venta_neto) as cont FROM venta WHERE venta_fecha > '$nueva_fecha' AND venta_fecha < '$fecha' AND usuario_venta_id = $this->usuario";
            }
            $rs = $this->sql_con->query($consulta);
            if($rs === false){
                $resultado = 0;
            }else{
                $resultado = mysqli_num_rows($rs);
                $dato = array();
                while($row = $rs->fetch_assoc()){
                    $mes = date('F', strtotime($nueva_fecha));
                    $dato['fecha'] = $this->obtener_mes($mes);
                    if($row['cont'] == '')
                        $row['cont'] = 0;
                    $dato['cont'] = $row['cont'];
                    array_push($this->datos['fluctuacion_mes'], $dato);
                }
            }
            $fecha = $nueva_fecha;
        }
        $this->datos['resultado'] = $resultado;
    }
    
    protected function promedio_compra(){
        $fecha = date('Y-m-d');
        $this->datos['promedio_compra'] = array();
        for($i = 0; $i < 2; $i++){
            
            $nueva_fecha = strtotime('-30 day', strtotime($fecha));
            $nueva_fecha = date('Y-m-d', $nueva_fecha);
            if($this->tipo_cuenta == 1){
                $consulta = "SELECT venta_neto as total FROM venta WHERE venta_fecha > '$nueva_fecha' AND venta_fecha < '$fecha'";
            }else{
                $consulta = "SELECT venta_neto as total FROM venta WHERE venta_fecha > '$nueva_fecha' AND venta_fecha < '$fecha' AND usuario_venta_id = $this->usuario";
            }
            $rs = $this->sql_con->query($consulta);
            if($rs === false){
                $resultado = 0;
            }else{
                $resultado = mysqli_num_rows($rs);
                $dato = array();
                $total = 0;
                $total_ventas = 0;
                while($row = $rs->fetch_assoc()){
                    $total += $row['total'];
                    $total_ventas++;
                }
                $mes = date('F', strtotime($fecha));
                $dato['fecha'] = $this->obtener_mes($mes);
                if($total_ventas == 0){
                    $total_ventas++;
                }
                $dato['promedio'] = round($total / $total_ventas);
            }
            $fecha = $nueva_fecha;
            array_push($this->datos['promedio_compra'], $dato);
        }
        $this->datos['resultado'] = $resultado;
    }
    
    protected function obtener_semana($dia){
        $semana = ['Lunes', 'Martes', 'Miércoles','Jueves', 'Viernes', 'Sábado', 'Domingo'];
        $semana_ing = ['Monday', 'Tuesday', 'Wednesday','Thursday', 'Friday', 'Saturday', 'Sunday'];
        return $semana[array_search($dia, $semana_ing)];
    }
    
    protected function obtener_mes($mes){
        $array_mes = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $array_mes_ing = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        return $array_mes[array_search($mes, $array_mes_ing)];
    }
    
    protected function total_ventas_dia(){
        $fecha = date('Y-m-d');
        if($this->tipo_cuenta == 1){
            $consulta = "SELECT count(*) as cont FROM venta WHERE venta_fecha LIKE '$fecha%'";
        }else{
            $consulta = "SELECT count(*) as cont FROM venta WHERE venta_fecha LIKE '$fecha%' AND usuario_venta_id = $this->usuario";
        }
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            $resultado = 0;
        }else{
            $resultado = 1;
            $row = $rs->fetch_assoc();
            $this->datos['total'] = array();
            array_push($this->datos['total'], $row);
        }
        $this->datos['resultado'] = $resultado;
    }
    
    protected function total_ventas_mes(){
        $fecha = date('Y-m');
        if($this->tipo_cuenta == 1){
            $consulta = "SELECT count(*) as cont FROM venta WHERE venta_fecha LIKE '$fecha%'";
        }else{
            $consulta = "SELECT count(*) as cont FROM venta WHERE venta_fecha LIKE '$fecha%' AND usuario_venta_id = $this->usuario";
        }
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            $resultado = 0;
        }else{
            $resultado = 1;
            $row = $rs->fetch_assoc();
            $this->datos['total'] = array();
            array_push($this->datos['total'], $row);
        }
        $this->datos['resultado'] = $resultado;
    }
    
    protected function total_productos_dia(){
        $fecha = date('Y-m-d');
        if($this->tipo_cuenta == 1){
            $consulta = "SELECT count(vp.producto_id) * vp.producto_cantidad as cont FROM venta_producto vp JOIN venta v ON vp.venta_id = v.venta_id WHERE v.venta_fecha LIKE '$fecha%'";
        }else{
            $consulta = "SELECT count(vp.producto_id) * vp.producto_cantidad as cont FROM venta_producto vp JOIN venta v ON vp.venta_id = v.venta_id WHERE v.venta_fecha LIKE '$fecha%' AND v.usuario_venta_id = $this->usuario";
        }
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            $resultado = 0;
        }else{
            $resultado = 1;
            $row = $rs->fetch_assoc();
            $this->datos['total'] = array();
            array_push($this->datos['total'], $row);
        }
        $this->datos['resultado'] = $resultado;
    }
    
    protected function total_productos_mes(){
        $fecha = date('Y-m');
        if($this->tipo_cuenta == 1){
            $consulta = "SELECT count(vp.producto_id) * vp.producto_cantidad as cont FROM venta_producto vp JOIN venta v ON vp.venta_id = v.venta_id WHERE v.venta_fecha LIKE '$fecha%'";
        }else{
            $consulta = "SELECT count(vp.producto_id) * vp.producto_cantidad as cont FROM venta_producto vp JOIN venta v ON vp.venta_id = v.venta_id WHERE v.venta_fecha LIKE '$fecha%' AND v.usuario_venta_id = $this->usuario";
        }
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            $resultado = 0;
        }else{
            $resultado = 1;
            $row = $rs->fetch_assoc();
            $this->datos['total'] = array();
            array_push($this->datos['total'], $row);
        }
        $this->datos['resultado'] = $resultado;
    }
    
    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    
    public function __destruct(){
        echo json_encode($this->datos);
    }

}

?>