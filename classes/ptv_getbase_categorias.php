<?php
$categoria = new Categorias();    
    
class Categorias{
    protected $empresa_id = 1;
    protected $link;
    protected $sql_con;
    protected $datos = array();
    
    public function __construct(){
        session_start();
        require('../hosts.php');
        require('conexion_new.php');
        $hosteo = new Host();
        $hosteo->obtener_conexion(0);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $this->obtener_categorias();
    }
    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }     
    function obtener_categorias(){
        $consulta = "SELECT categoria_id AS c_id, categoria_nombre AS c_nombre, categoria_descripcion as c_descripcion,categoria_padre as c_padre FROM categoria";
        $this->sql_con->set_charset("utf8");
        $rs = $this->sql_con->query($consulta);
        if($rs === false) {
            trigger_error('Ha ocurrido un error');
        } else {       
            while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
                $dato = array();
                $dato['cat_id'] = $row['c_id'];
                $dato['cat_nombre'] = $row['c_nombre'];
                $dato['cat_descripcion'] = $row['c_descripcion'];
                $dato['cat_padre'] = $row['c_padre'];
                array_push($this->datos, $dato);
            }
        }
        

    }
    
    function __destruct(){
        echo json_encode($this->datos);
    }
    
}
?>