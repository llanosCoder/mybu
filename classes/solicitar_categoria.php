<?php

$variable = new Clase();

class Clase{

    protected $link, $sql_con;
    protected $parametros = array();
    protected $usuario, $empresa;
    protected $resultado;

    public function __construct(){
        $this->conexion();
        $this->obtener_parametros();
        $this->procesar();
    }

    protected function conexion(){
        session_start();
        require('../hosts.php');
        require('conexion_new.php');
        $hosteo = new Host();
        $hosteo->obtener_conexion(1);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
    }
    
    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    
    protected function obtener_parametros(){
        $this->parametros = $_POST['parametros'];
        $this->usuario = $_SESSION['id'];
        $this->empresa = $_SESSION['empresa'];
    }
    
    protected function procesar(){
        $nombre = mysqli_real_escape_string($this->sql_con, $this->parametros['nombre']);
        $padre = mysqli_real_escape_string($this->sql_con, $this->parametros['padre']);
        $id_padre = $this->obtener_cat_id($padre);
        if($id_padre == '')
            $id_padre = 0;
        $insertar_categoria = $this->sql_con->prepare("INSERT INTO categoria_solicitud(usuario_id, empresa_id, categoria_solicitud_nombre, categoria_solicitud_padre, categoria_solicitud_fecha_creacion) VALUES (?, ?, ?, ?, NOW())");
        $insertar_categoria->bind_param('iiss',
        $this->usuario,
        $this->empresa,
        $nombre,
        $id_padre);
        $insertar_categoria->execute();
        $insercion = $this->sql_con->affected_rows;
        $insertar_categoria->close();
        if($insercion > 0)
            $this->resultado = 1;
        else
            $this->resultado = 0;
    }
                                        
    protected function obtener_cat_id($cat){
        $consulta_categoria = "SELECT categoria_id as id FROM categoria WHERE categoria_descripcion = '$cat'";
        $rs_categoria = $this->sql_con->query($consulta_categoria);
        if($rs_categoria === false){
            return 0;
        }else{
            $categoria = $rs_categoria->fetch_assoc();
            return $categoria['id'];
        }
    }
                                        
    public function __destruct(){
        echo $this->resultado;
    }

}

?>