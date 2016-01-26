<?php

$variable = new Clase();

class Clase{

    protected $link, $sql_con;
    protected $parametros = array();
    protected $datos = array();

    public function __construct(){
        session_start();
        $this->obtener_parametros();
        $this->obtener_datos();
    }
    
    protected function obtener_parametros(){
        if( isset( $_COOKIE['datos_sesion'] ) ) {
            $arr = explode(',', $_COOKIE['datos_sesion']);
            $_SESSION['id'] = $arr[0];
            $_SESSION['user'] = $arr[1];
            $_SESSION['nombre'] = $arr[2];
            $_SESSION['avatar'] = $arr[3];
            $_SESSION['empresa'] = $arr[4];
            $_SESSION['nombre_empresa'] = $arr[5];
            $_SESSION['host'] = $arr[6];
            $_SESSION['sucursal'] = $arr[7];
            $_SESSION['tipo_cuenta'] = $arr[8];
            $_SESSION['rol'] = $arr[9];
        }
        $this->parametros = $_POST['parametros'];
    }
    
    protected function obtener_datos(){
        $dato = array();
        foreach($this->parametros as $param){
            if($param == 'navegador'){
                require('obtener_navegador.php');
                $dato[$param] = detect();
            }else{
                if(isset($_SESSION[$param]))
                    $dato[$param] = $_SESSION[$param];
                else
                    $dato[$param] = 0;
            }
        }
        array_push($this->datos, $dato);
    }
    
    public function __destruct(){
        echo json_encode($this->datos);
    }

}

?>