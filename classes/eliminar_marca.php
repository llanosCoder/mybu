<?php

$marca = new Marcas();

class Marcas{

    protected $link, $sql_con;
    protected $id;
    protected $resultado;

    public function __construct(){
        session_start();
        require('../hosts.php');
        require('conexion_new.php');
        $hosteo = new Host();
        $hosteo->obtener_conexion(0);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $this->obtener_parametros();
        $this->eliminar_marca();
    }
    
    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    
    protected function obtener_parametros(){
        $this->id = mysqli_real_escape_string($this->sql_con, $_POST['id']);
    }
    
    protected function eliminar_marca(){
        $eliminar_marca = $this->sql_con->prepare("DELETE FROM producto_marca WHERE producto_marca_id = ?");
        $eliminar_marca->bind_param('i', $this->id);
        $eliminar_marca->execute();
        $eliminacion_exitosa = $this->sql_con->affected_rows;
        $ins_id = mysqli_insert_id($this->sql_con);
        $eliminar_marca->close();
        if($eliminacion_exitosa > 0)
            $this->eliminar_relaciones($ins_id);
        else
            $this->resultado = 0;
    }
    
    protected function eliminar_relaciones($id){
        $eliminar_marca = $this->sql_con->prepare("DELETE FROM categoria_marca WHERE marca_id = ?");
        $eliminar_marca->bind_param('i', $id);
        $eliminar_marca->execute();
        $eliminar_marca->close();
        $this->resultado = 1;
    }
    
    public function __destruct(){
        echo $this->resultado;
    }

}

?>