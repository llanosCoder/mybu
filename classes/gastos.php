<?php
/**
 * gastos.php
 * Controller for X Server methods
 *
 * @author     Iván Valenzuela
 * @copyright  imasd
 * @version    1.0
 * @example    http://url/gastos.php?op=1
 */

/* Para utilizar POST, quitar si utilizarás GET */
$opcion = (isset($_POST['op'])) ? $_POST['op'] : 'none';

/* Para utilizar GET, quitar si utilizarás POST */
//$opcion = (isset($_GET['op'])) ? $_GET['op'] : 'none';

if ($opcion!='none'){
    switch($opcion){
        case 'categorias':
            require('gastos.model.php');
            $gastos = new Gastos();
            $gastos->obtener_categorias();
            break;
        case 'medios':
            require('gastos.model.php');
            $gastos = new Gastos();
            $gastos->obtener_medios();
            break;
        case 'estadisticas':
            require('gastos.model.php');
            $gastos = new Gastos();
            $gastos->obtener_estadistica();
            break;
        case 'ingresar':
            /*Aquí puedes añadir otro método*/
            $f = $_POST['fecha'];
            $d = $_POST['descripcion'];
            $c = $_POST['categoria'];
            $m = $_POST['medio'];
            $i = $_POST['identificador'];
            $t = $_POST['total'];
            require('gastos.model.php');
            $gastos = new Gastos();
            $gastos->almacenar_gastos($f, $d, $c, $m, $i, $t);
            break;
        case 'detalle_dia':
            $parametros = $_POST['parametros'];
            require('gastos.model.php');
            $gastos = new Gastos();
            $gastos->obtener_detalle_gastos($parametros);
            break;
        default:
            echo 'not defined';
            break;
    }
}
else 
    echo 'not defined';
?>