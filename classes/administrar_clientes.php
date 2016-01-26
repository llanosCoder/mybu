<?php

$cliente = new Clientes();

class Clientes{

    protected $link, $sql_con;
    protected $accion, $datos = array(), $datos_cliente = array(), $empresa, $tipo_cuenta, $usuario;
    protected $pagadas = array();
    
    public function __construct(){
        session_start();
        ini_set('display_errors', 'on');
        require('../hosts.php');
        require('conexion_new.php');
        $this->set_host(0);
        $this->procesar();
    }
    
    protected function set_host($host){
        $hosteo = new Host();
        $hosteo->obtener_conexion($host);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
    }
    
    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    
    protected function agregar_linea_credito(){
        $monto_autorizado = $this->datos_cliente['monto_autorizado'];
        $fecha1 = date('Y-m-') . 20;
        $fecha2 = date('Y-m-') . 5;
        $cupo_ilimitado = mysqli_real_escape_string($this->sql_con, $_POST['cupo_ilimitado']);
        if ($cupo_ilimitado == 1){
            $plan_credito = 2;
            $monto_autorizado = 2147483647;
        }else{
            $plan_credito = 1;
        }
        $insercion = $this->sql_con->prepare("INSERT INTO linea_credito (empresa_id, cliente_id, linea_credito_monto_autorizado, linea_credito_fecha_facturacion, linea_credito_fecha_pago, linea_credito_saldo_favor, plan_credito_id) VALUES (?, ?, ?, ?, ?, 0, ?)");
        $insercion->bind_param('iiissi',
                               $this->empresa,
                               $this->usuario,
                               $monto_autorizado,
                               $fecha1,
                               $fecha2,
                               $plan_credito
                              );
        $insercion->execute();
        $insercion->close();
    }
    
    protected function procesar(){
        $this->empresa = $_SESSION['empresa']; 
        $this->accion = mysqli_real_escape_string($this->sql_con, $_POST['accion']);
        $this->tipo_cuenta = $_SESSION['tipo_cuenta'];
        switch($this->accion){
            case 1:
                $this->datos['resultado'] = array();
                $dato = array();
                if($this->verificar_permisos(1)){
                    $this->obtener_datos_cliente();
                    if($this->existe_rut($this->datos_cliente['rut'])){
                        $dato['resultado'] = 3; //Rut ya existe
                    }else{
                        if($this->agregar_cliente()){
                            $this->agregar_linea_credito();
                            $dato['resultado'] = 1; //Cliente agregado exitosamente
                        }else{
                            $dato['resultado'] = 0; //No se agrego cliente
                        }
                    }
                    
                }else{
                    $dato['resultado'] = 2; //No cuenta con los permisos
                }
                array_push($this->datos['resultado'], $dato);
            break;
            case 2:
                if($this->verificar_permisos(2)){
                    $this->obtener_clientes(0);
                }else{
                    $this->datos['resultado'] = array();
                    $dato = array();
                    $dato['resultado'] = 2;
                    array_push($this->datos['resultado'], $dato);
                }
            break;
            case 3:
                $this->datos['resultado'] = array();
                $dato = array();
                if($this->verificar_permisos(2)){
                    $this->obtener_datos_cliente();
                    if($this->existe_rut($this->datos_cliente['rut'])){
                        $this->datos_cliente['id'] = $this->obtener_id($this->datos_cliente['rut']);
                        if(!$this->existe_linea_credito($this->datos_cliente['id'])){
                            if($this->nuevo_credito()){
                                $dato['resultado'] = 1; //Linea de credito creada exitosamente
                            }else{
                                $dato['resultado'] = 0; //No se creo linea de credito
                            }
                        }else{
                            $dato['resultado'] = 4; //Cliente ya cuenta con linea de credito
                        }
                    }else{
                        $dato['resultado'] = 3; //Rut no existe
                    }
                }else{
                    $dato['resultado'] = 2; //No posee los permisos
                }
                array_push($this->datos['resultado'], $dato);
            break;
            case 4:
                if($this->verificar_permisos(2)){
                    $rut = mysqli_real_escape_string($this->sql_con, $_POST['rut']);
                    $this->obtener_clientes($rut);
                    
                }else{
                    $this->datos['resultado'] = array();
                    $dato = array();
                    $dato['resultado'] = 2;
                    array_push($this->datos['resultado'], $dato);
                }
            break;
            case 5:
                if($this->verificar_permisos(2)){
                    $rut = mysqli_real_escape_string($this->sql_con, $_POST['rut']);
                    $cuotas = $_POST['cuotas'];
                    $cuotas_pagar = $this->obtener_monto_cancelar($rut);
                    $num_errores = $this->pagar_cuotas($rut, $cuotas, $cuotas_pagar);
                    if($num_errores == 0){
                        $this->datos['resultado'] = 1; //Tudu bem
                    } else {
                        if($num_errores == count($cuotas)) {
                            $this->datos['resultado'] = 0; //No se proceso ningun pago
                        } else {
                            $this->datos['resultado'] = 2; //Algunos pagos no se realizaron
                        }
                    }
                }else{
                    $this->datos['resultado'] = array();
                    $dato = array();
                    $dato['resultado'] = 2;
                    array_push($this->datos['resultado'], $dato);
                }
            break;
            case 6:
                if($this->verificar_permisos(2)){
                    $rut = mysqli_real_escape_string($this->sql_con, $_POST['rut']);
                    $monto_nuevo = mysqli_real_escape_string($this->sql_con, $_POST['nuevo_monto']);
                    if($this->editar_monto_autorizado($rut, $monto_nuevo)){
                        $this->datos['resultado'] = 1; //Monto no se logro editar
                    }else{
                        $this->datos['resultado'] = 0; //Monto editado exitosamente
                    }
                }
            break;
            case 7:
                $plan = $_POST['plan_actual'];
                $rut = mysqli_real_escape_string($this->sql_con, $_POST['rut']);
                $this->actualizar_plan_cliente($plan, $rut);
                break;
        }
    }
    
    protected function actualizar_plan_cliente($plan, $rut){
        switch($plan) {
            case 'PLAN ILIMITADO':
                $nuevo_plan = 1;
                $monto = 250000;
                $nuevo_plan_nombre = 'PLAN NORMAL';
                break;
            case 'PLAN NORMAL': default:
                $nuevo_plan = 2;
                $monto = 2147483647;
                $nuevo_plan_nombre = 'PLAN ILIMITADO';
                break;
        }
        $consulta = "UPDATE linea_credito SET linea_credito_monto_autorizado = $monto, plan_credito_id = $nuevo_plan WHERE cliente_id = (SELECT cliente_id FROM cliente WHERE cliente_rut = '$rut')";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            $this->datos['resultado'] = 0;
        }else{
            $afectadas = $this->sql_con->affected_rows;
            if ($afectadas > 0){
                $this->datos['resultado'] = 1;
                $this->datos['plan_nuevo'] = $nuevo_plan_nombre;
            }else{
                $this->datos['resultado'] = 0;
            }
        }
    }
    
    protected function verificar_permisos($permiso_requerido){
        if($this->tipo_cuenta <= $permiso_requerido){
            return true;
        }else{
            return false;
        }
    }
    
    protected function editar_monto_autorizado($rut, $monto_nuevo){
        $edicion = $this->sql_con->prepare("UPDATE linea_credito SET linea_credito_monto_autorizado = ? WHERE cliente_id = (SELECT cliente_id FROM cliente WHERE cliente_rut = ?)");
        $edicion->bind_param('is', $monto_nuevo, $rut);
        $edicion->execute();
        $afectadas = $this->sql_con->affected_rows;
        $edicion->close();
        if($afectadas > 0){
            return true;
        }else{
            return false;
        }
    }
    
    protected function agregar_cliente(){
        $stmt = "INSERT INTO cliente (cliente_rut, cliente_nombre, cliente_apellido_paterno, cliente_apellido_materno, cliente_direccion, cliente_comuna_id, cliente_fecha_nacimiento, cliente_fecha_creacion, cliente_telefono, cliente_correo) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)";
        $insercion = $this->sql_con->prepare($stmt);
        $insercion->bind_param('sssssisss',
            $this->datos_cliente['rut'],
            $this->datos_cliente['nombre'],
            $this->datos_cliente['apaterno'],
            $this->datos_cliente['amaterno'],
            $this->datos_cliente['direccion'],
            $this->datos_cliente['comuna'],
            $this->datos_cliente['f_nacimiento'],
            $this->datos_cliente['telefono'],
            $this->datos_cliente['email']
        );
        $insercion->execute();
        //print_r($insercion);
        $this->usuario = mysqli_insert_id($this->sql_con);
        $afectadas = $this->sql_con->affected_rows;
        $insercion->close();
        if($afectadas > 0){
            return true;
        }else{
            return false;
        }
    }
    
    protected function obtener_cupo_credito($rut, $monto){
        $this->set_host(0);
        $consulta = "SELECT SUM(cu.cuota_monto) AS usado FROM cuota cu JOIN venta_credito vc ON cu.venta_credito_id = vc.venta_credito_id JOIN linea_credito lc ON vc.linea_credito_id = lc.linea_credito_id JOIN cliente c ON lc.cliente_id = c.cliente_id WHERE cu.cuota_estado = 0 AND c.cliente_rut = '$rut'";
        $rs = $this->sql_con->query($consulta);
        while($row = $rs->fetch_assoc()){
            $monto -= $row['usado'];
        }
        return $monto;
    }
    
    protected function obtener_clientes($rut){
        $this->datos['resultado'] = array();
        $consulta = "SELECT c.cliente_rut as rut, c.cliente_nombre as nombre, c.cliente_apellido_paterno as apaterno, c.cliente_apellido_materno as amaterno, c.cliente_direccion as direccion, c.cliente_comuna_id as comuna_id, DATE_FORMAT(c.cliente_fecha_nacimiento, '%d-%m-%Y') as f_nacimiento, DATE_FORMAT(c.cliente_fecha_creacion, '%d-%m-%Y') as f_creacion, c.cliente_telefono as telefono, c.cliente_correo as correo, lc.linea_credito_monto_autorizado as monto_autorizado, lc.linea_credito_fecha_facturacion as f_facturacion, lc.linea_credito_fecha_pago as f_pago, pc.plan_credito_nombre as pc_nombre, pc.plan_credito_costo_mantencion as costo_fijo, pc.plan_credito_costo_uso as uso FROM cliente c LEFT JOIN linea_credito lc ON c.cliente_id = lc.cliente_id LEFT JOIN plan_credito pc ON lc.plan_credito_id = pc.plan_credito_id";
        if($rut != 0){
            $consulta .= " WHERE c.cliente_rut = '$rut'";
        }
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            $dato = array();
            $dato['resultado'] = -1; //Error en consulta
            array_push($this->datos['resultado'], $dato);
        }else{
            while($row = $rs->fetch_assoc()){
                $dato = array();
                foreach($row as $indice=>$valor){
                    $dato[$indice] = $valor;
                }
                if($rut != 0){
                    $dato['monto_pagar'] = $this->obtener_monto_cancelar($rut);
                }
                $dato['cupo'] = $this->obtener_cupo_credito($row['rut'], $row['monto_autorizado']);
                $dato['comuna'] = $this->obtener_comuna($dato['comuna_id']);
                array_push($this->datos['resultado'], $dato);
            }
        }
    }
    
    protected function obtener_monto_cancelar($rut){
        $datos = array();
        $consulta = "SELECT (SUM(cu.cuota_monto) + pc.plan_credito_costo_mantencion + pc.plan_credito_costo_uso) as monto, cu.cuota_fecha_pago as fecha FROM cuota cu JOIN venta_credito vc ON cu.venta_credito_id = vc.venta_credito_id JOIN linea_credito lc ON vc.linea_credito_id = lc.linea_credito_id JOIN cliente c ON lc.cliente_id = c.cliente_id JOIN plan_credito pc ON lc.plan_credito_id = pc.plan_credito_id WHERE cu.cuota_estado = 0 AND c.cliente_rut = '$rut' GROUP BY MONTH(cu.cuota_fecha_pago), DAY(cu.cuota_fecha_pago) ORDER BY cu.cuota_fecha_pago";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return 0;
        }else{
            while($row = $rs->fetch_assoc()){
                $dato = array();
                if($row['fecha'] != null){
                    $fecha = $row['fecha'];
                }else{
                    $fecha = date('Y-m-d', strtotime('+1 month', strtotime($fecha)));
                }
                $dato['monto'] = $row['monto'];
                $dato['fecha'] = $fecha;
                array_push($datos, $dato);
            }
        }
        return $datos;
    }
    
    protected function pagar_cuotas($rut, $cuotas, $cuotas_pagar){
        $num_errores = 0;
        for($i = 0; $i < count($cuotas); $i++){
            for($j = 0; $j < count($cuotas_pagar); $j++){
                if ($cuotas[$i]['fecha'] == $cuotas_pagar[$j]['fecha']) {
                    if($cuotas[$i]['monto'] == $cuotas_pagar[$j]['monto']){
                        $consulta = "SELECT venta_credito_id as v_id FROM venta_credito WHERE linea_credito_id = (SELECT linea_credito_id FROM linea_credito WHERE cliente_id = (SELECT cliente_id FROM cliente WHERE cliente_rut = '$rut'))";
                        $rs = $this->sql_con->query($consulta);
                        if($rs === false){
                            continue;
                        }else{
                            while($row = $rs->fetch_assoc()){
                                $actualizar = $this->sql_con->prepare("UPDATE cuota SET cuota_estado = 1, cuota_fecha_pagada = DATE(NOW()) WHERE venta_credito_id = (?) AND cuota_fecha_pago = ?");
                                $actualizar->bind_param('is', $row['v_id'], $cuotas[$i]['fecha']);
                                $actualizar->execute();
                                $afectadas = $this->sql_con->affected_rows;
                                $actualizar->close();
                                if($afectadas > 0){
                                    $existe = false;
                                    for($jx = 0; $jx < count($this->pagadas); $jx++){
                                        for($kx = 0; $kx < count($cuotas_pagar); $kx++){
                                            if($this->pagadas[$jx]['fecha'] == $cuotas_pagar[$kx]['fecha']){
                                                $existe = true;
                                                break;
                                            }
                                        }
                                    }
                                    if(!$existe){
                                        array_push($this->pagadas, $cuotas[$i]);
                                    }
                                }
                            }
                        }
                    }else{
                        $num_errores++;
                    }
                }
            }
        }
        return $num_errores;
    }
    
    protected function obtener_plan_id($plan){
        $consulta = "SELECT plan_credito_id as id FROM plan_credito WHERE plan_credito_codigo = '$plan'";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return 0;
        }else{
            $row = $rs->fetch_assoc();
            return $row['id'];
        }
    }
    
    protected function nuevo_credito(){
        $fecha = date('Y-m');
        $this->datos_cliente['f_pago'] = $fecha . '-' . $this->datos_cliente['f_pago'];
        $this->datos_cliente['f_facturacion'] = $fecha . '-' . $this->datos_cliente['f_facturacion'];
        $plan = $this->obtener_plan_id($this->datos_cliente['plan']);
        $insercion = $this->sql_con->prepare("INSERT INTO linea_credito (empresa_id, cliente_id, linea_credito_monto_autorizado, linea_credito_fecha_facturacion, linea_credito_fecha_pago, linea_credito_saldo_favor, plan_credito_id) VALUES (?, ?, ?, ?, ?, 0, 1)");
        $insercion->bind_param('iiiss',
           $this->empresa,
           $this->datos_cliente['id'],
           $this->datos_cliente['monto_autorizado'],
           $this->datos_cliente['f_facturacion'],
           $this->datos_cliente['f_pago']
        );
        $insercion->execute();
        $afectadas = $this->sql_con->affected_rows;
        $insercion->close();
        if($afectadas > 0){
            return true;
        }else{
            return false;
        }
    }
    
    protected function existe_rut($rut){
        $consulta = "SELECT count(*) AS cont FROM cliente WHERE cliente_rut = '$rut'";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return true;
        }else{
            $row = $rs->fetch_assoc();
            if($row['cont'] > 0){
                return true;
            }else{
                return false;
            }
        }
    }
    
    protected function existe_linea_credito($c_id){
        $consulta = "SELECT count(*) AS cont FROM linea_credito WHERE cliente_id = $c_id";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return true;
        }else{
            $row = $rs->fetch_assoc();
            if($row['cont'] > 0){
                return true;
            }else{
                return false;
            }
        }
    }
    
    protected function obtener_datos_cliente(){
        foreach($_POST as $indice=>$valor){
            $this->datos_cliente[$indice] = mysqli_real_escape_string($this->sql_con, $valor);
        }
        $this->datos_cliente['rut'] = str_replace(".","",$this->datos_cliente['rut']);
        $this->datos_cliente['rut'] = str_replace("-","",$this->datos_cliente['rut']);
    }
    
    protected function obtener_id($rut){
        $consulta = "SELECT cliente_id as c_id FROM cliente WHERE cliente_rut = '$rut'";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return false;
        }else{
            $row = $rs->fetch_assoc();
            return $row['c_id'];
        }
    }
    
    protected function obtener_comuna($id){
        $this->set_host(1);
        $consulta = "SELECT comuna_nombre as nombre FROM comuna WHERE comuna_id = $id";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return '';
        }else{
            $row = $rs->fetch_assoc();
            return $row['nombre'];
        }
    }

    public function __destruct(){
        echo json_encode($this->datos);
    }

}

?>