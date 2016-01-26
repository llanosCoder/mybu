<?php
class Facebook{

    protected $link, $sql_con;
    protected $sucursal, $tipo_cuenta;
    
    public function __construct(){
        require('../hosts.php');
        require('conexion_new.php');
    }
    public function verificarUsuario($user){
        $hosteo = new Host();
        $hosteo->obtener_conexion(1);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $consulta ='SELECT * FROM usuario WHERE usuario_login="'.$user.'"';
        //print_r($this->link);
        $result = $this->sql_con->query($consulta);

        if($result === false) {
            trigger_error('Ha ocurrido un error');
            return false;
        }
        else{
            if (mysqli_num_rows($result)>0)
                return true;
            else
                return false;
        }

    }
    
    public function login($user){
        $hosteo = new Host();
        $hosteo->obtener_conexion(1);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $consulta = "SELECT u.usuario_id as id, u.usuario_login as usuario, u.usuario_nombres as nombres, u.usuario_apellidos as apellidos, u.usuario_avatar as avatar, ue.empresa_id as empresa, e.empresa_nombre as nombre_empresa, ur.rol_id as rol FROM usuario u INNER JOIN usuario_empresa ue ON u.usuario_id = ue.usuario_id INNER JOIN empresa e ON ue.empresa_id = e.empresa_id INNER JOIN usuario_pass up ON u.usuario_id = up.usuario_id INNER JOIN usuario_rol ur ON u.usuario_id = ur.usuario_id WHERE u.usuario_login = '$user' GROUP BY u.usuario_id";
        $rs_consulta = $this->sql_con->query($consulta);
        $dato = array();
        if($rs_consulta === false){
            trigger_error("Ha ocurrido un error");
            //$dato['estado'] = 2;
        }else{
            $datos_usuario = $rs_consulta->fetch_assoc();
            $filas = mysqli_num_rows($rs_consulta);
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
        }
        $rs_consulta->close();
        require_once('logax.model.php');
        $log = new LogAx();
        $log->set_host_ax(27);
        $log->log_login($_SESSION['id'], 1);
        //$dato['navegador'] = $this->obtener_navegador();
        //array_push($this->datos, $dato);
        $this->registrar_login();
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
    
    public function __destruct(){
        //do 
    }
}

?>