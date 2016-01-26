<?php
class Conexion{
    function Conectarse($host, $user, $pass, $bd) 
    { 
        $datos['host'] = $host;
        $datos['user'] = $user;
        $datos['pass'] = $pass;
        $datos['bd'] = $bd;
        return $datos;
    }
}
?>