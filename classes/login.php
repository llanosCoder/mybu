<?php

$variable = new Clase();

class Clase{

    protected $link, $sql_con;
    protected $user, $pass, $remember;
    protected $datos = array();
    protected $sucursal, $tipo_cuenta;
    
    public function __construct(){
        session_start();
        require('../hosts.php');
        ini_set('display_errors', 'on');
        
        require('conexion_new.php');
        $hosteo = new Host();
        $hosteo->obtener_conexion(1);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $this->obtener_parametros();
        $this->login();
    }
    
    protected function obtener_parametros(){
        $this->user = mysqli_real_escape_string($this->sql_con, $_POST['user']);
        $this->pass = mysqli_real_escape_string($this->sql_con, $_POST['pass']);
        $this->remember = mysqli_real_escape_string($this->sql_con, $_POST['remember']);
        $this->pass = md5($this->pass);
    }
    
    protected function login(){
        $consulta = "SELECT u.usuario_id as id, u.usuario_login as usuario, u.usuario_nombres as nombres, u.usuario_apellidos as apellidos, u.usuario_avatar as avatar, ue.empresa_id as empresa, e.empresa_nombre as nombre_empresa, ur.rol_id as rol FROM usuario u INNER JOIN usuario_empresa ue ON u.usuario_id = ue.usuario_id INNER JOIN empresa e ON ue.empresa_id = e.empresa_id INNER JOIN usuario_pass up ON u.usuario_id = up.usuario_id INNER JOIN usuario_rol ur ON u.usuario_id = ur.usuario_id WHERE u.usuario_login = '$this->user' AND up.usuario_pass COLLATE utf8_spanish_ci LIKE  '$this->pass' GROUP BY u.usuario_id";
        $rs_consulta = $this->sql_con->query($consulta);
        $dato = array();
        if($rs_consulta === false){
            trigger_error("Ha ocurrido un error");
            $dato['estado'] = 2;
        }else{
            $datos_usuario = $rs_consulta->fetch_assoc();
            $filas = mysqli_num_rows($rs_consulta);
            if($filas > 0) 
                $dato['estado'] = 1;
            else
                $dato['estado'] = 0;
            $_SESSION['id'] = $datos_usuario['id'];
            $_SESSION['user'] = $datos_usuario['usuario'];
            $arr = explode(' ', $datos_usuario['nombres']);
            $nombre = $arr[0];
            $arr = explode(' ', $datos_usuario['apellidos']);
            $nombre = $nombre . " " . $arr[0];        
            $_SESSION['nombre'] = $nombre;
            $_SESSION['avatar'] = $datos_usuario['avatar'];
            $_SESSION['empresa'] = $datos_usuario['empresa'];
            $_SESSION['nombre_empresa'] = $datos_usuario['nombre_empresa'];
            $_SESSION['host'] = $this->set_host();
            $this->obtener_sucursal($datos_usuario['id']);
            $_SESSION['sucursal'] = $this->sucursal;
            $_SESSION['tipo_cuenta'] = $this->tipo_cuenta;
            $_SESSION['rol'] = $datos_usuario['rol'];
            $dato['bienvenida'] = $nombre;
            if($this->remember == 'true'){
                setcookie("datos_sesion", implode(',', $_SESSION), time()+60*60*24*6004);
            }
        }
        $rs_consulta->close();
        $dato['navegador'] = $this->obtener_navegador();
        array_push($this->datos, $dato);
        $this->registrar_login();
        require('logax.model.php');
        $log = new LogAx();
        $log->set_host_ax(27);
        $log->log_login($datos_usuario['id'], 1);
    }
    
    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    
    protected function set_host(){
        $empresa = $_SESSION['empresa'];
        $consulta = "SELECT host FROM empresa_conexion WHERE empresa_id = $empresa";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            exit();
        }else{
            $row = $rs->fetch_assoc();
            return $row['host'];
        }
    }
    
    protected function obtener_sucursal($id){
        $hosteo = new Host();
        $hosteo->obtener_conexion(0);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $consulta_sucursal = "SELECT us.sucursal_id as sucursal, us.usuario_sucursal_tipo_cuenta as tipo_cuenta FROM usuario_sucursal us WHERE us.usuario_id = $id LIMIT 1";
        $rs_sucursal = $this->sql_con->query($consulta_sucursal);
        if($rs_sucursal === false){
            exit();
        }else{
            $row_sucursal = $rs_sucursal->fetch_assoc();
            $this->sucursal = $row_sucursal['sucursal'];
            $this->tipo_cuenta = $row_sucursal['tipo_cuenta'];
        }
    }
    
    protected function registrar_login(){
        $insercion = $this->sql_con->prepare("INSERT INTO registro_usuario_login (usuario_id, registro_usuario_login_fecha) VALUES (?, NOW())");
        $insercion->bind_param('i', $_SESSION['id']);
        $insercion->execute();
        $insercion->close();
    }
    
    protected function obtener_navegador(){
        require('obtener_navegador.php');
        return detect();
    }
    
    public function __destruct(){
        echo json_encode($this->datos);
    }
}

?>