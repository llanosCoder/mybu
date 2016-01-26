<?php
    session_start();
    $_SESSION["sucursal"]=1;
    if(isset($_GET["s"]))
        $_SESSION["sucursal"]=$_GET["s"];
    $_SESSION["perfil"]="mecanica";
    $_SESSION["logged"]=true;
    echo $_SESSION["sucursal"];
?>