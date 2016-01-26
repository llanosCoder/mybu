<?php

$variable = new Clase();

class Clase{

    protected $link, $link_admin, $sql_con;
    protected $datos = array();
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
        $hosteo->obtener_conexion(0);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $hosteo = new Host();
        $hosteo->obtener_conexion(1);
        $this->sql_con_admin = new mysqli($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $this->sql_con_admin->set_charset('utf8');
    }
    
    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    
    protected function obtener_parametros(){
        $this->datos = $_POST['categorias'];
    }
    
    protected function procesar(){
        $consulta_categorias = "SELECT categoria_id as id, categoria_nombre as nombre, categoria_descripcion as descripcion, categoria_padre as padre FROM categoria";
        $rs_categorias = $this->sql_con_admin->query($consulta_categorias);
        if($rs_categorias === false){
            trigger_error("Ha ocurrido un error");
        }else{
            $num_errores = 0;
            while($row_categoria = $rs_categorias->fetch_assoc()){
                if(in_array($row_categoria['descripcion'], $this->datos)){
                    if(!$this->existe_categoria($row_categoria['descripcion'])){
                        $insercion = $this->insertar_categoria($row_categoria);
                        if($insercion <= 0){
                            $num_errores++;
                        }
                    }
                }else{
                    $eliminacion = $this->eliminar_categoria($row_categoria['descripcion']);
                    if(!$eliminacion){
                        $num_errores++;
                    }
                }
            }
            if($num_errores == 0){
                $this->resultado = 1;
            }else{
                if($num_errores == count($this->datos))
                    $this->resultado = 0;
                else
                    $this->resultado = 2;
            }
        }
    }
    
    protected function insertar_categoria($categoria){
        $insercion_categoria = $this->sql_con->prepare("INSERT INTO categoria VALUES(?, ?, ?, ?)");
        $insercion_categoria->bind_param('issi',
        $categoria['id'],
        $categoria['nombre'],
        $categoria['descripcion'],
        $categoria['padre']);
        $insercion_categoria->execute();
        $insercion = $this->sql_con->affected_rows;
        $insercion_categoria->close();
        return $insercion;
    }
    
    protected function eliminar_categoria($cat){
        $eliminar_categoria = $this->sql_con->prepare("DELETE FROM categoria WHERE categoria_descripcion = ?");
        $eliminar_categoria->bind_param('s',
        $cat);
        $eliminar_categoria->execute();
        $eliminadas = $this->sql_con->affected_rows;
        $eliminar_categoria->close();
        if($eliminadas > 0){
            return true;
        }else{
            if(!$this->existe_categoria($cat)){
                return true;
            }else{
                return false;
            }
        }
    }
    
    protected function existe_categoria($cat){
        $consulta_categoria = "SELECT count(*) as cont FROM categoria WHERE categoria_descripcion = '$cat'";
        $rs_categoria = $this->sql_con->query($consulta_categoria);
        if($rs_categoria === false){
            return false;
        }else{
            $categoria = $rs_categoria->fetch_assoc();
            if($categoria['cont'] == '0')
                return false;
            else
                return true;
        }
    }
    
    public function __destruct(){
        echo $this->resultado;
    }

}

?>