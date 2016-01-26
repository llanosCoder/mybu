<?php

$compra = new Compras();

class Compras{

    protected $link, $sql_con;
    protected $vouchers = array(), $esOferta, $medio_pago, $total_bruto, $total_descuentos, $total_neto, $usuario_compra, $rechazar_orden, $razon;
    protected $resultado, $productos_descontados = array();
    protected $sucursal, $empresa, $usuario;
    protected $registro_tag, $host;
    
    public function __construct(){
        session_start();
        require('../hosts.php');
        require('conexion_new.php');
        $hosteo = new Host();
        $hosteo->obtener_conexion(0);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $this->obtener_parametros();
        $this->procesar_compra();
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
        $this->usuario = $_SESSION['id'];
        $this->vouchers = $_POST['voucher'];
        $this->solicitante = mysqli_real_escape_string($this->sql_con, $_POST['solicitante']);
        foreach($this->vouchers as $indice=>$voucher){
            /*foreach($voucher['pagos'] as $pago){
                print_r($pago);
            }*/
            //print_r($voucher['pagos']['monto']);
        }
        if(isset($_POST['oferta']))
           $this->esOferta = $_POST['oferta'];
        if(isset($_POST['medioPago']))
            $this->medio_pago = mysqli_real_escape_string($this->sql_con, $_POST['medioPago']);
        else
            $this->medio_pago = 2;
        if(isset($_POST['rechazar']))
            $this->rechazar_orden = $_POST['rechazar'];
        else
            $this->rechazar_orden = false;
        if(isset($_POST['razon']))
            $this->razon = mysqli_real_escape_string($this->sql_con, $_POST['razon']);
        else
            $this->razon = '';
    }
    
    protected function obtener_origen_datos(){
            $consulta_origen = "SELECT host FROM empresa_conexion WHERE empresa_id = $this->solicitante";
            $rs_origen = $this->sql_con->query($consulta_origen);
            if($rs_origen === false){
                exit();
            }else{
                $row_origen = $rs_origen->fetch_assoc();
                return $row_origen['host'];
            }
        }
    
    protected function procesar_compra(){
        foreach($this->vouchers as $indice=>$vouchers){
            $voucher = $vouchers['voucher'];
            if($this->rechazar_orden){
                $hosteo = new Host();
                $hosteo->obtener_conexion(1);
                $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
                $this->cerrar_orden($voucher, 3, false);
                $this->host = $this->obtener_origen_datos();
                $hosteo = new Host();
                $hosteo->obtener_conexion($this->host);
                $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
                $this->cerrar_orden($voucher, 3, true);
                $hosteo = new Host();
                $hosteo->obtener_conexion(0);
                $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
                $this->cerrar_orden($voucher, 3, false);
                continue;
            }
            $voucher = mysqli_real_escape_string($this->sql_con, $voucher);
            if($this->esOferta){
                /*
                *    cambiar this->sucursal, esta mal
                */
                $consulta_producto = "SELECT ps.producto_id as id, ps.sucursal_id as sucursal, ps.producto_sucursal_stock_real as stock_r, op.producto_valor as precio, op.producto_cantidad as cantidad, op.orden_compra_oferta_id as oferta FROM orden_compra oc INNER JOIN orden_producto op ON oc.orden_id = op.orden_id INNER JOIN producto_sucursal ps ON op.producto_id = ps.producto_id AND ps.sucursal_id = $this->sucursal WHERE oc.orden_voucher = '$voucher'";
                $result_producto = $this->sql_con->query($consulta_producto);
                if($result_producto === false){
                    trigger_error("Ha ocurrido un error");
                    $this->resultado = 0;  //No se ha podido procesar su compra
                    exit();
                }else{
                    while($row_producto = $result_producto->fetch_assoc()){
                        $id = $row_producto['id'];
                        $sucursal = $row_producto['sucursal'];
                        $cantidad = $row_producto['cantidad'];
                        $stock = $row_producto['stock_r'];
                        $precio = $row_producto['precio'];
                        $oferta = $row_producto['oferta'];
                        $hosteo = new Host();
                        $hosteo->obtener_conexion($this->host);
                        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
                        $update_producto = "UPDATE producto_sucursal SET producto_sucursal_stock_real = producto_sucursal_stock_real - $cantidad WHERE producto_id = $id AND sucursal_id = $sucursal";
                        if($this->sql_con->query($update_producto) === false) {
                            trigger_error('Wrong SQL: ' . $consulta . ' Error: ' . $this->sql_con->error, E_USER_ERROR);
                            $this->resultado = 0;  //No se ha podido procesar su compra
                            exit();
                        } else {
                            $edicion_exitosa = $this->sql_con->affected_rows;
                            if($edicion_exitosa > 0){
                                $this->reducir_stock_oferta($oferta, $cantidad);
                                $hosteo = new Host();
                                $hosteo->obtener_conexion(2);
                                $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
                                $this->reducir_stock_oferta($oferta, $cantidad);
                                $hosteo = new Host();
                                $hosteo->obtener_conexion($this->host);
                                $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
                                $this->registro_tag = $this->registrar_tags($id, $cantidad);
                                $producto_descontado = array();
                                $producto_descontado['id'] = $id;
                                $producto_descontado['precio'] = $precio;
                                $producto_descontado['cantidad'] = $cantidad;
                                $producto_descontado['oferta'] = $oferta;
                                $producto_descontado['promocion'] = 0;
                                array_push($this->productos_descontados,$producto_descontado);
                            }
                        }
                    }
                    $this->obtener_totales($voucher);
                    $insercion_compra = $this->sql_con->prepare("INSERT INTO venta VALUES(null, NOW(), ?, ?, ?, ?, ?, ?)");
                    $insercion_compra->bind_param('iiiiii',
                    $this->total_bruto,
                    $this->total_descuentos,
                    $this->total_neto,
                    $this->empresa,
                    $this->usuario,
                    $this->usuario_compra);
                    $insercion_compra->execute();
                    $insercion_compra->close();
                    $venta_id = mysqli_insert_id($this->sql_con);
                    if($venta_id != 0){
                        $pagos_registrados = $this->registrar_pagos($venta_id, $voucher);
                        if($pagos_registrados == 0){
                            $this->resultado = 5;
                            exit();
                        }else{
                            if($this->registro_tag == 0)
                                $this->resultado = 6;
                        }
                        $this->relacionar_venta_producto($venta_id);
                        $hosteo = new Host();
                        $hosteo->obtener_conexion(1);
                        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
                        $this->cerrar_orden($voucher, 1, false);
                        $this->host = $this->obtener_origen_datos();
                        $hosteo = new Host();
                        $hosteo->obtener_conexion($this->host);
                        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
                        $this->cerrar_orden($voucher, 1, false);
                        $hosteo = new Host();
                        $hosteo->obtener_conexion(0);
                        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
                        $this->cerrar_orden($voucher, 1, true);   
                    }else{
                        $this->resultado = 4; //Venta no registrada
                    }
                }
            }
            $result_producto->close();
        }
    }
    
    protected function obtener_totales($voucher){
        $consulta_ofertas = "SELECT orden_total_bruto as bruto, orden_total_descuentos as descuentos, orden_total as neto, usuario_id as comprador FROM orden_compra WHERE orden_voucher = '$voucher'";
        $rs_ofertas = $this->sql_con->query($consulta_ofertas);
        if($rs_ofertas === false){
            trigger_error("Ha ocurrido un error");
            $this->resultado = 0;  //No se ha podido procesar su compra
            exit();
        }else{
            $row_ofertas = $rs_ofertas->fetch_assoc();
            $this->total_bruto = $row_ofertas['bruto'];
            $this->total_descuentos = $row_ofertas['descuentos'];
            $this->total_neto = $row_ofertas['neto'];
            $this->usuario_compra = $row_ofertas['comprador'];
        }
        $rs_ofertas->close();
    }

    protected function relacionar_venta_producto($venta_id){
        foreach($this->productos_descontados as $producto){
            $id = $producto['id'];
            $precio = $producto['precio'];
            $oferta = $producto['oferta'];
            $promocion = $producto['promocion'];
            $cantidad = $producto['cantidad'];
            $insercion_relacion = $this->sql_con->prepare("INSERT INTO venta_producto VALUES (null, ?, ?, ?, ?, ?, ?)");
            $insercion_relacion->bind_param('iiiiii',
            $venta_id,
            $id,
            $precio,
            $cantidad,
            $promocion,
            $oferta);
            $insercion_relacion->execute();
            $insercion_relacion->close();
        }
    }
    
    protected function registrar_pagos($venta_id, $voucher){
        $inserciones = 0;
        foreach($this->vouchers as $vouchers){
            if($vouchers['voucher'] == $voucher){
                foreach($vouchers['pagos'] as $pago){
                    $insercion_pagos = $this->sql_con->prepare('INSERT INTO venta_pago VALUES(null, ?, ?, ?);');
                    $insercion_pagos->bind_param('iii',
                    $venta_id,
                    $pago['monto'],
                    $pago['modo_pago']);
                    $insercion_pagos->execute();
                    $inserciones += $this->sql_con->affected_rows;
                    $insercion_pagos->close();
                }
            }
        }
        return $inserciones;
    }
    
    protected function reducir_stock_oferta($id_oferta, $cantidad){
        $consulta_stock = "UPDATE oferta SET oferta_stock = oferta_stock - $cantidad WHERE oferta_id = $id_oferta AND oferta_stock > 0;";
        if($this->sql_con->query($consulta_stock) === false) {
            trigger_error('Wrong SQL: ' . $consulta_stock . ' Error: ' . $this->sql_con->error, E_USER_ERROR);
            $this->resultado = 0; //No se ha podido procesar su compra
            exit();
        }else{
            $insercion = $this->sql_con->affected_rows;
        }
        
    }
    
    protected function registrar_tags($p_id, $cantidad){
        $consulta_tags = "SELECT t.tag_id as t_id FROM tag t INNER JOIN tag_producto tp ON t.tag_id = tp.tag_id AND tp.producto_id = $p_id";
        $rs_tags = $this->sql_con->query($consulta_tags);
        if($rs_tags === false){
            trigger_error("Ha ocurrido un error");
        }else{
            while($row_tags = $rs_tags->fetch_assoc()){
                $t_id = $row_tags['t_id'];
                $consulta_venta_acumulada = "SELECT venta_acumulada_id as v_id, count(*) as cont FROM venta_acumulada WHERE tag_id = $t_id";
                $rs_venta_acumulada = $this->sql_con->query($consulta_venta_acumulada);
                if($rs_venta_acumulada === false){
                    trigger_error("Ha ocurrido un error");
                }else{
                    $row_venta_acumulada = $rs_venta_acumulada->fetch_assoc();
                    if($row_venta_acumulada['cont'] > 0){
                        $v_id = $row_venta_acumulada['v_id'];
                        $update_venta = "UPDATE venta_acumulada SET venta_cantidad = venta_cantidad + $cantidad, venta_ultima_actualizacion = NOW() WHERE venta_acumulada_id = $v_id";
                        if($this->sql_con->query($update_venta) === false) {
                            trigger_error('Wrong SQL: ' . $update_venta . ' Error: ' . $this->sql_con->error, E_USER_ERROR);
                        } else {
                            $edicion_exitosa = $this->sql_con->affected_rows;
                            if($edicion_exitosa){
                                return 1;
                            }else{
                                return 0;
                            }
                        }
                    }else{
                        $insert_venta = $this->sql_con->prepare("INSERT INTO venta_acumulada(tag_id, venta_cantidad, venta_ultima_actualizacion) VALUES (?, ?, NOW())");
                        $insert_venta->bind_param('ii', $row_tags['t_id'],
                        $cantidad);
                        $insert_venta->execute();
                        $insert_venta->close();
                        $id = mysqli_insert_id($this->sql_con);
                        if($id != 0){
                            return 1;
                        }else{
                            return 0;
                        }
                    }
                }
            }
        }
    }
    
    protected function cerrar_orden($voucher, $estado_nuevo, $bd_local){
        $update_producto = "UPDATE orden_compra SET orden_estado = $estado_nuevo WHERE orden_voucher = '$voucher'";
        if($this->sql_con->query($update_producto) === false) {
            trigger_error('Wrong SQL: ' . $update_producto . ' Error: ' . $this->sql_con->error, E_USER_ERROR);
            $this->resultado = 0; //No se ha podido procesar su compra
            exit();
        } else {
            $edicion_exitosa = $this->sql_con->affected_rows;
            if($estado_nuevo == 3 && $bd_local){
                $rechazo = $this->registrar_rechazo($voucher);
                if($rechazo > 0)
                    $this->resultado = 1;
                else
                    $this->resultado = 7;
            }
            if($edicion_exitosa > 0){
                $this->resultado = 1; //Venta realizada con éxito
            }else{
                $this->resultado = 3; //No se pudo cerrar orden
            }
        }
    }
    
    protected function registrar_rechazo($voucher){
        $o_id = $this->obtener_id_voucher($voucher);
        $insercion_rechazo = $this->sql_con->prepare("INSERT INTO orden_rechazo VALUES(?, ?)");
        $insercion_rechazo->bind_param('is',
        $o_id,
        $this->razon);
        $insercion_rechazo->execute();
        $insercion = $this->sql_con->affected_rows;
        $insercion_rechazo->close();
        return $insercion;
    }
    
    protected function obtener_id_voucher($voucher){
        $consulta_id = "SELECT orden_id as id FROM orden_compra WHERE orden_voucher = '$voucher'";
        $rs_id = $this->sql_con->query($consulta_id);
        if($rs_id === false){
            return 0;
        }else{
            $row_id = $rs_id->fetch_assoc();
            return $row_id['id'];
        }
    }
    
    public function __destruct(){
        echo $this->resultado;
    }
    
}
?>