<?php
session_start();
$ax = $_POST['ax'];
$user = $_SESSION['id'];
$empresa = $_SESSION['empresa'];
switch($ax){
    case 1:
        require('logax.model.php');
        $log = new LogAx();
        $log->set_host_ax(27);
        $log->log_registro($user, $empresa, 0);
        break;
    case 2:
        require('logax.model.php');
        $log = new LogAx();
        $log->set_host_ax(27);
        $log->log_login($user, 0);
        break;
    default:
        exit();
}

?>