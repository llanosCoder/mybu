<?php

$variable = new Clase();

class Clase{

    protected $link, $sql_con;
    protected $parametros_abrev = array(), $parametros = array(), $padre = 0;
    protected $datos = array();

    public function __construct(){
        session_start();
        require('../hosts.php');
        require('conexion_new.php');
        $hosteo = new Host();
        $hosteo->obtener_conexion(0);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $this->obtener_parametros();
        $this->procesar();
    }

    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    
    protected function obtener_parametros(){
        $this->parametros_abrev = $_POST['parametros'];
        if(isset($_POST['padre'])){
            $this->padre = mysqli_real_escape_string($this->sql_con, $_POST['padre']);
        }
        $this->padre = $this->get_id($this->padre);
        foreach($this->parametros_abrev as $indice=>$valor){
            switch($valor){
                case 'id':
                    $param = 'categoria_id';
                    $agregar = true;
                    break;
                case 'nombre':
                    $param = 'categoria_nombre';
                    $agregar = true;
                    break;
                case 'descripcion':
                    $param = 'categoria_descripcion';
                    $agregar = true;
                    break;
                case 'padre':
                    $param = 'categoria_padre';
                    $agregar = true;
                    break;
                default:
                    $agregar = false;
            }
            if($agregar)
                array_push($this->parametros, $param);
            else
                array_splice($this->parametros_abrev, $indice, 1);
        }
    }
    
    protected function procesar(){
        $consulta_categoria = "SELECT ";
        for($i = 0; $i < count($this->parametros); $i++){
            if($i > 0)
                $consulta_categoria = $consulta_categoria . ", ";
            $consulta_categoria = $consulta_categoria . $this->parametros[$i] . " as " . $this->parametros_abrev[$i];
        }
        $consulta_categoria = $consulta_categoria . " FROM categoria";
        if($this->padre != 0){
            $consulta_categoria .= " WHERE categoria_id = $this->padre";
        }
        $rs_categoria = $this->sql_con->query($consulta_categoria);
        if($rs_categoria === false){
            trigger_error("Ha ocurrido un error");
            exit();
        }else{
            while($row_categoria = $rs_categoria->fetch_assoc()){
                $dato = array();
                for($i = 0; $i < count($this->parametros_abrev); $i++){
                    $dato[$this->parametros_abrev[$i]] = $row_categoria[$this->parametros_abrev[$i]];
                }
                array_push($this->datos, $dato);
            }
        }
    }
    
    protected function get_id($categoria){
        $consulta = "SELECT categoria_id as id FROM categoria WHERE categoria_descripcion = '$categoria'";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return 0;
        }else{
            $row = $rs->fetch_assoc();
            if(mysqli_num_rows($rs) > 0){
                return $row['id'];
            }else{
                return 0;
            }
        }
    }
    
    public function __destruct(){
        echo json_encode($this->datos);
    }

}

?>