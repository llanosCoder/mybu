<?php

class LogAx{

    protected $link, $sql_con;
    
    protected function insertar_usuario_empresa($user, $empresa){
        $insercion = $this->sql_con->prepare("INSERT INTO usuario_empresa (usuario_id, empresa_id) VALUES (?, ?)");
        $insercion->bind_param('ii', $user, $empresa);
        $insercion->execute();
        $insercion->close();
    }
    
    public function log_registro($user, $empresa, $social){
        $this->insertar_usuario_empresa($user, $empresa);
        $this->log_login($user, $social);
    }
    
    public function log_login($user, $social){
        $stmt = $this->sql_con->prepare("INSERT INTO login (login_id, usuario_id, login_primer_fecha, login_ultima_fecha, login_count, login_social) VALUES(?, ?, NOW(), NOW(), 1, ?) ON DUPLICATE KEY UPDATE login_ultima_fecha=NOW(), login_count = login_count + 1, login_social = ?");
        $stmt->bind_param('iiii', $user, $user, $social, $social);
        $stmt->execute();
        $stmt->close();
    }
    
    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    
    function set_host_ax($host){
        $hosteo = new Host();
        $hosteo->obtener_conexion($host);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
    }
    
    public function __construct(){
        session_start();
        require_once('../hosts.php');
        require_once('conexion_new.php');
        $hosteo = new Host();
        $hosteo->obtener_conexion(0);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
    }

    public function __destruct(){
    }
}

?>