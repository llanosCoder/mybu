<?php
$categoria = new Categorias();    
    
class Categorias{
    
    protected $link;
    protected $sql_con;
    protected $datos = array();
    protected $jerarquia = 0;
    protected $tipo = 0;
    
    public function __construct(){
        session_start();
        if(isset($_POST['type']))
            $this->tipo = $_POST['type'];
        require('../hosts.php');
        require('conexion_new.php');
        $hosteo = new Host();
        switch($this->tipo){
            case 0:
                $hosteo->obtener_conexion(0);
            break;
            case 1:
                $hosteo->obtener_conexion(1);
            break;
        }
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $this->obtener_categorias(0);
    }
    
    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    
    function obtener_categorias($cat_padre, &$jerarquia = 1){
        $consulta = "SELECT categoria_id as cid, categoria_nombre as nombre, categoria_descripcion as descripcion, categoria_padre as padre FROM categoria WHERE categoria_padre = $cat_padre";
        $rs = $this->sql_con->query($consulta);
        if($rs === false) {
            trigger_error('Ha ocurrido un error');
        } else {
            //$resultado = $rs->fetch_all(MYSQLI_ASSOC);
            $i = 0;
            while($row = $rs->fetch_assoc()){
                $dato = array();
                $dato['nombre'] = $row['nombre'];
                $dato['desc'] = $row['descripcion'];
                $dato['jerarquia'] = $jerarquia;
                array_push($this->datos, $dato);
                $subir_jerarquia = $jerarquia + 1;
                $this->obtener_categorias($row['cid'], $subir_jerarquia);
            }
        }
        $rs->close();
    }
    
    function __destruct(){
        echo json_encode($this->datos);
    }
}
?>