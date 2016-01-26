<?php
/**
 * gastos.php
 * Controller for X Server methods
 *
 * @author     Jaime Llanos
 * @copyright  imasd
 * @version    1.0
 * @example    http://url/administrar_ventas.php?op=1
 */
    $accion = $_POST['accion'];
    switch ($accion) {
        case 1:
            session_start();
            require('administrar_ventas.model.php');
            $venta = new Ventas();
            $venta->set_host(0);
            $resultado = $venta->obtener_ventas_recientes();
            $resultado['edicion'] = $_SESSION['tipo_cuenta'];
            break;
        case 2:
            session_start();
            require('administrar_ventas.model.php');
            $codigo = $_POST['codigo'];
            $usuario = $_SESSION['id'];
            $venta = new Ventas();
            $venta->set_host(0);
            if($venta->verificar_permisos($_SESSION['id'], $_SESSION['empresa'], 0)){
                $resultado['resultado'] = $venta->anular_venta($codigo, $usuario);
            } else {
                $resultado['resultado'] = 0;
            }
            break;
    }
    echo json_encode($resultado);
?>