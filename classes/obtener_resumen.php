<?php

$producto = new Productos();

class Productos{

    protected $link, $sql_con;
    protected $sucursal, $empresa, $usuario, $tipo_cuenta;
    protected $datos = array();

    public function __construct(){
        session_start();
        require('../hosts.php');
        require('conexion_new.php');
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
        $this->sucursal = $_SESSION['sucursal'];
        $this->empresa = $_SESSION['empresa'];
        $this->tipo_cuenta = $_SESSION['tipo_cuenta'];
        $this->usuario = $_SESSION['id'];
        $this->parametros = $_POST['parametros'];
        $fecha = date('Y-m-d');
        foreach($this->parametros as $valor){
            switch($valor){
                case 'stock':
                    $consulta = "SELECT count(*) as cont FROM producto_sucursal WHERE producto_sucursal_stock_real <= producto_sucursal_stock_minimo AND sucursal_id = $this->sucursal";
                    $this->procesar($consulta, $valor);
                    break;
                case 'ordenes':
                    $consulta = "SELECT count(*) as cont FROM orden_compra WHERE orden_estado = 0 AND empresa_solicitante <> $this->empresa AND empresa_solicitada = $this->empresa";
                    $this->procesar($consulta, $valor);
                    break;
                case 'ventas':
                    $consulta = "SELECT count(*) as cont FROM venta WHERE venta_fecha LIKE '$fecha%'";
                    $this->procesar($consulta, $valor);
                    break;
                case 'monto_ventas_hoy':
                    $this->obtener_ventas($fecha);
                    break;
                case 'mejor_vendedor':
                    $consulta = "SELECT count(venta_id) as cont, usuario_nombres as vendedor_nombre, usuario_apellidos as vendedor_apellido FROM venta JOIN usuario ON usuario_venta_id = usuario_id WHERE venta_fecha LIKE '$fecha%' GROUP BY usuario_venta_id ORDER BY count(venta_id) LIMIT 1";
                    $this->procesar($consulta, $valor);
                    break;
                case 'productos':
                    $consulta = "SELECT (SELECT count(producto_id) FROM producto_sucursal WHERE sucursal_id = $this->sucursal AND producto_sucursal_stock_real > 0) * 100 / count(producto_id) as productos_disponibles FROM producto_sucursal WHERE sucursal_id = $this->sucursal";
                    $this->procesar($consulta, $valor);
                    break;
                case 'cierre_caja':
                    $fecha_inicio = mysqli_real_escape_string($this->sql_con, $_POST['f_inicio']);
                    $fecha_termino = mysqli_real_escape_string($this->sql_con, $_POST['f_termino']);
                    if ($fecha_inicio != '' && $fecha_inicio != ' ') {
                        $fecha_inicio .= '00:00:00';
                    }
                    if ($fecha_termino != '' && $fecha_termino != ' ') {
                        $fecha_termino .= '23:59:59';
                    }
                    $this->cerrar_caja($fecha_inicio, $fecha_termino);
                    break;
                case 'filtro_categoria':
                    $categoria = mysqli_real_escape_string($this->sql_con, $_POST['categoria']);
                    $this->obtener_ventas_dia_categoria($categoria);
                    break;
                case 'filtro_producto':
                    $producto = mysqli_real_escape_string($this->sql_con, $_POST['producto']);
                    $this->obtener_ventas_dia_producto($producto);
                    break;
                case 'totales_inventario':
                    $this->obtener_totales_inventario();
                    break;
            }
        }
    }

    protected function obtener_totales_inventario() {
        $consulta = "SELECT SUM(ps.producto_sucursal_precio_unitario) AS valor, SUM(ps.producto_sucursal_costo) AS costo, (SUM(ps.producto_sucursal_precio_unitario) - SUM(ps.producto_sucursal_costo)) AS ganancia FROM producto_sucursal ps;";
        $rs = $this->sql_con->query($consulta);
        if ($rs === false) {
            trigger_error('Wrong SQL: ' . $consulta . ' Error: ' . $this->sql_con->error, E_USER_ERROR);
        } else {
            $row = $rs->fetch_assoc();
            $dato = array();
            foreach ($row as $indice => $valor) {
                $dato[$indice] = $valor;
            }
            $this->datos['totales'] = $dato;
        }
    }

    protected function obtener_ventas_dia_categoria($categoria){
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
                $consulta = "SELECT c.categoria_nombre as c_nombre, DATE(v.venta_fecha) as v_fecha, v.venta_neto as v_neto, vp.producto_cantidad as vp_cantidad FROM categoria c LEFT JOIN categoria_producto cp ON c.categoria_id = cp.categoria_id LEFT JOIN producto p ON cp.producto_id = p.producto_id LEFT JOIN venta_producto vp ON p.producto_id = vp.producto_id LEFT JOIN venta v ON vp.venta_id = v.venta_id WHERE c.categoria_descripcion = '$categoria' AND DATE(v.venta_fecha) = '$nueva_fecha' GROUP BY c.categoria_id";
            }else{
                $consulta = "SELECT c.categoria_nombre as c_nombre, DATE(v.venta_fecha) as v_fecha, v.venta_neto as v_neto, vp.producto_cantidad as vp_cantidad FROM categoria c LEFT JOIN categoria_producto cp ON c.categoria_id = cp.categoria_id LEFT JOIN producto p ON cp.producto_id = p.producto_id LEFT JOIN venta_producto vp ON p.producto_id = vp.producto_id LEFT JOIN venta v ON vp.venta_id = v.venta_id WHERE c.categoria_descripcion = '$categoria' AND DATE(v.venta_fecha) = '$nueva_fecha' AND v.usuario_venta_id = $this->usuario GROUP BY c.categoria_id";
            }
            $rs = $this->sql_con->query($consulta);
            if($rs === false){
                $resultado = 0;
                exit();
            }else{
                $resultado = mysqli_num_rows($rs);
                $dato = array();
                while($row = $rs->fetch_assoc()){
                    foreach($row as $indice=>$valor){
                        $dato[$indice] = $valor;
                    }
                }
                if($resultado == 0){
                    $dato['v_fecha'] = $nueva_fecha;
                }
            }
            $nueva_fecha = strtotime('+1 day', strtotime($fecha));
            $nueva_fecha = date ( 'Y-m-d' , $nueva_fecha );
            $fecha = $nueva_fecha;
            $this->datos['resultado'] = $resultado;
            if(strtotime($dato['v_fecha']) <= strtotime(date("d-m-Y H:i:00"))){
                array_push($this->datos['venta_dia'], $dato);
            }
        }
        $this->datos['tiempo'] = array();
        $arr_tiempo['dia'] = $dia;
        array_push($this->datos['tiempo'], $arr_tiempo);
    }

    protected function obtener_ventas_dia_producto($producto){
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
                $consulta = "SELECT p.producto_codigo as p_codigo, p.producto_nombre as p_nombre, DATE(v.venta_fecha) as v_fecha, v.venta_neto as v_neto, vp.producto_cantidad as vp_cantidad FROM producto p LEFT JOIN venta_producto vp ON p.producto_id = vp.producto_id LEFT JOIN venta v ON vp.venta_id = v.venta_id WHERE p.producto_codigo = '$producto' AND DATE(v.venta_fecha) = '$nueva_fecha'";
            }else{
                $consulta = "SELECT p.producto_codigo as p_codigo, p.producto_nombre as p_nombre, DATE(v.venta_fecha) as v_fecha, v.venta_neto as v_neto, vp.producto_cantidad as vp_cantidad FROM producto p LEFT JOIN venta_producto vp ON p.producto_id = vp.producto_id LEFT JOIN venta v ON vp.venta_id = v.venta_id WHERE p.producto_codigo = '$producto' AND DATE(v.venta_fecha) = '$nueva_fecha' AND v.usuario_venta_id = $this->usuario";
            }
            $rs = $this->sql_con->query($consulta);
            if($rs === false){
                $resultado = 0;
                exit();
            }else{
                $resultado = mysqli_num_rows($rs);
                $dato = array();
                while($row = $rs->fetch_assoc()){
                    foreach($row as $indice=>$valor){
                        $dato[$indice] = $valor;
                    }
                }
                if($resultado == 0){
                    $dato['v_fecha'] = $nueva_fecha;
                }
            }
            $nueva_fecha = strtotime('+1 day', strtotime($fecha));
            $nueva_fecha = date ( 'Y-m-d' , $nueva_fecha );
            $fecha = $nueva_fecha;
            $this->datos['resultado'] = $resultado;
            if(strtotime($dato['v_fecha']) <= strtotime(date("d-m-Y H:i:00"))){
                array_push($this->datos['venta_dia'], $dato);
            }
        }
        $this->datos['tiempo'] = array();
        $arr_tiempo['dia'] = $dia;
        array_push($this->datos['tiempo'], $arr_tiempo);
    }

    protected function obtener_totales_cierre_caja($user, $f_inicio, $f_termino){
        $this->datos['totales'] = array();
        if($f_inicio != '' && $f_termino != '' && $f_inicio != ' ' && $f_termino != ' '){
            $consulta = "SELECT SUM(v.venta_neto) as neto, SUM(v.venta_bruto) as bruto, SUM(v.venta_descuentos) as descuentos, SUM(ps.producto_sucursal_costo) as costo, (SUM(v.venta_neto) - SUM(ps.producto_sucursal_costo)) as ganancia FROM venta v JOIN venta_producto vp ON v.venta_id = vp.venta_id JOIN producto_sucursal ps ON vp.producto_id = ps.producto_id WHERE v.venta_fecha >= '$f_inicio' AND v.venta_fecha <= '$f_termino' AND v.usuario_venta_id = $user AND ps.sucursal_id = $this->sucursal";
        }else{
            $fecha = date('Y-m-d');
            $consulta = "SELECT SUM(v.venta_neto) as neto, SUM(ps.producto_sucursal_costo) as costo, (SUM(v.venta_neto) - SUM(ps.producto_sucursal_costo)) as ganancia FROM venta v JOIN venta_producto vp ON v.venta_id = vp.venta_id JOIN producto_sucursal ON vp.producto_id = ps.producto_id WHERE v.venta_fecha LIKE '$fecha%' AND v.usuario_venta_id = $user AND ps.sucursal_id = $this->sucursal";
        }
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            trigger_error('Wrong SQL: ' . $consulta . ' Error: ' . $this->sql_con->error, E_USER_ERROR);
            $totales['resultado'] = 0;
            array_push($this->datos['totales'], $totales);
        }else{
            $totales['resultado'] = 1;
            $row = $rs->fetch_assoc();
            $datos = array();
            //INGRESAR AL ARRAY
            foreach($row as $indice=>$valor){
                $datos[$indice] = $valor;
            }
            if($f_inicio != '' && $f_termino != '' && $f_inicio != ' ' && $f_termino != ' '){
                $consulta = "SELECT SUM(v.venta_bruto) as venta_medio, vp.venta_medio_pago_id as medio_pago FROM venta v JOIN venta_pago vp ON v.venta_id = vp.venta_id WHERE v.venta_fecha >= '$f_inicio' AND v.venta_fecha <= '$f_termino' AND v.usuario_venta_id = $user GROUP BY vp.venta_medio_pago_id";
            } else{
                $fecha = date('Y-m-d');
                $consulta = "SELECT SUM(v.venta_bruto) as venta_medio, vp.venta_medio_pago_id as medio_pago FROM venta v JOIN venta_pago vp ON v.venta_id = vp.venta_id WHERE v.venta_fecha LIKE '$fecha%' AND v.usuario_venta_id = $user GROUP BY vp.venta_medio_pago_id";
            }
            $rs2 = $this->sql_con->query($consulta);
            if ($rs2 != false) {
                $datos['medios'] = array();
                while($row2 = $rs2->fetch_assoc()){
                    $dato = array();
                    switch ($row2['medio_pago']){
                        case 1:
                            $dato['efectivo'] = $row2['venta_medio'];
                            break;
                        case 2:
                            $dato['credito'] = $row2['venta_medio'];
                            break;
                        case 3:
                            $dato['debito'] = $row2['venta_medio'];
                            break;
                        case 4:
                            $dato['credito_local'] = $row2['venta_medio'];
                            break;
                    }
                    if ($row2['medio_pago'] == 1){

                    }
                    /*foreach($row2 as $indice=>$valor){
                        $dato[$indice] = $valor;
                    }*/
                    array_push($datos['medios'], $dato);
                }

            }
        }
        array_push($this->datos['totales'], $datos);
    }

    protected function cerrar_caja($fecha_inicio, $fecha_termino){
        $user = $_SESSION['id'];
        if($fecha_inicio == '' || $fecha_inicio == ' '){
            $fecha_inicio = date('Y-m-d') . ' 00:00:00';

        }
        if($fecha_termino == '' || $fecha_termino == ' '){
            $fecha_termino = date('Y-m-d') . ' 23:59:59';

        }
            //$fecha = date('Y-m-d');
            //$consulta = "SELECT v.venta_id as v_id, v.venta_bruto as bruto, v.venta_descuentos as descuentos, v.venta_neto as neto, TIME(venta_fecha) as fecha, vp.venta_medio_pago_id as medio_pago FROM venta v JOIN venta_pago vp ON v.venta_id = vp.venta_id WHERE v.venta_fecha LIKE '$fecha%' AND v.usuario_venta_id = $user ORDER BY fecha ASC";
        $consulta = "SELECT v.venta_id as v_id, v.venta_bruto as bruto, v.venta_descuentos as descuentos, v.venta_neto as neto, TIME(venta_fecha) as fecha, vp.venta_medio_pago_id as medio_pago, ps.producto_sucursal_costo as costo FROM venta v JOIN venta_pago vp ON v.venta_id = vp.venta_id JOIN venta_producto vpr ON v.venta_id = vpr.venta_id JOIN producto_sucursal ps ON vpr.producto_id = ps.producto_id WHERE venta_fecha >= '$fecha_inicio' AND v.venta_fecha <= '$fecha_termino' AND v.usuario_venta_id = $user AND ps.sucursal_id = $this->sucursal ORDER BY fecha ASC";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return false;
        }else{
            $this->datos['ventas'] = array();
            while($row = $rs->fetch_assoc()){
                $dato = array();
                foreach($row as $indice=>$valor){
                    $dato[$indice] = $valor;
                }
                $dato['productos'] = array();
                $dato['productos'] = $this->obtener_productos($row['v_id']);
                $dato['productos_unicos'] = $this->obtener_productos_unicos_venta($dato['productos']);
                $dato['total_productos'] = $this->obtener_total_productos_venta($dato['productos']);
                array_push($this->datos['ventas'], $dato);
            }
            $this->obtener_totales_cierre_caja($user, $fecha_inicio, $fecha_termino);
        }
    }

    protected function obtener_productos_unicos_venta($venta){
        $productos = array();
        foreach($venta as $indice=>$valor){
            if(!in_array($valor['codigo'], $productos)){
                array_push($productos, $valor['codigo']);
            }
        }
        return count($productos);
    }

    protected function obtener_total_productos_venta($venta){
        $total_productos = 0;
        foreach($venta as $indice=>$valor){
            $total_productos += $valor['cantidad'];
        }
        return $total_productos;
    }

    protected function obtener_productos($v_id){
        $consulta_productos = "SELECT p.producto_codigo as codigo, p.producto_nombre as nombre, vp.producto_cantidad as cantidad FROM producto p JOIN venta_producto vp ON p.producto_id = vp.producto_id WHERE vp.venta_id = $v_id";
        $rs_productos = $this->sql_con->query($consulta_productos);
        if($rs_productos === false){
            return false;
        }else{
            $datos = array();
            $dato = array();
            while($row_productos = $rs_productos->fetch_assoc()){
                foreach($row_productos as $indice=>$valor){
                    $dato[$indice] = $valor;
                }
                array_push($datos, $dato);
            }
            return $datos;
        }
    }

    protected function procesar($consulta, $tipo_dato){
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            $this->datos['resultado'] = 0;
        }else{
            $dato = array();
            $this->datos['resultado'] = 1;
            $this->datos[$tipo_dato] = array();
            while($row = $rs->fetch_assoc()){
                $dato = array();
                foreach($row as $indice=>$valor){
                    $dato[$indice] = $valor;
                }
                array_push($this->datos[$tipo_dato], $dato);
            }
        }
    }

    protected function obtener_ventas($fecha){
        $consulta = "SELECT SUM(venta_neto) as suma_ventas FROM venta WHERE venta_fecha LIKE '$fecha%' AND usuario_venta_id = ";
        $this->procesar($consulta, 'monto_ventas_hoy');
        for($i = 0; $i < 8; $i++){
            $nueva_fecha = strtotime('-1 day', strtotime($fecha));
            $fecha = date('Y-m-d', $nueva_fecha);
            $consulta = "SELECT SUM(venta_neto) as suma_ventas FROM venta WHERE venta_fecha LIKE '$fecha%'";
            $rs = $this->sql_con->query($consulta);
            if($rs === false){
                $this->datos['resultado'] = 0;
            }else{
                $dato = array();
                $this->datos['resultado'] = 1;
                while($row = $rs->fetch_assoc()){
                    $dato = array();
                    foreach($row as $indice=>$valor){
                        if($valor == null)
                            $dato[$indice] = 0;
                        else
                            $dato[$indice] = $valor;
                    }
                    array_push($this->datos['monto_ventas_hoy'], $dato);
                }
            }
        }
    }

    public function __destruct(){
        echo json_encode($this->datos);
    }

    }

?>
