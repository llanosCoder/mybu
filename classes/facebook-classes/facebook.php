<?php
/**
 * radius.php
 * Controller for Radius Server methods
 *
 * @author     Iván Valenzuela
 * @copyright  Gestsol - Gestión y Soluciones Tecnológicas
 * @version    1.0
 * @example    http://url/radius.php?op=1
 */

/* Para utilizar POST, quitar si utilizarás GET */
//$opcion = (isset($_POST['op'])) ? $_POST['op'] : 'none';

/* Para utilizar GET, quitar si utilizarás POST */

$opcion = (isset($_GET['op'])) ? $_GET['op'] : 'none';

if ($opcion!='none'){
    switch($opcion){
        case 'usuarios':
            /* Retorna en formato JSON la cantidad de conexiones por NasId */
            require('facebook.model.php');
            $facebook = new Facebook();
            $user =  $_GET['user'];
            if ($facebook->verificarUsuario($user))
                echo 'encontrado';
            else 
                echo 'not found';
            break;
        case 'estadisticas':
            /*Aquí puedes añadir otro método*/
            require('buses.model.php');
            $radius = new Buses();
            $radius->obtenerEstadisticas();
            break;
        case 'proyectos':
            /*Aquí puedes añadir otro método*/
            require('buses.model.php');
            $radius = new Buses();
            $radius->obtenerProyectos();
            break;
        default:
            echo 'not defined';
            break;
    }
}
else 
    echo 'not defined';


?>

