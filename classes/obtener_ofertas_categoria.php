<?php
    $cat_desc = $_POST['cId'];
    $producto = new Productos($cat_desc);
    
    class Productos{
        
        protected $categoria;
        protected $link, $sql_con;
        protected $datos = array();
        protected $tipo_cuenta, $sucursal;
                
        public function __construct($cat_desc){
            $this->categoria = $cat_desc;
            session_start();
            require('../hosts.php');
            require('conexion_new.php');
            $hosteo = new Host();
            $hosteo->obtener_conexion(0);
            $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
            $this->obtener_ofertas();
        }
        
        protected function set_conexion($host, $user, $pass, $bd){
            $conexion = new Conexion();
            $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
            $this->sql_con->set_charset('utf8');
        }
        
        protected function obtener_ofertas(){
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'],$this->link['pass'],$this->link['bd']);
            $this->sucursal = $_SESSION['id'];
            $this->tipo_cuenta = $_SESSION['tipo_cuenta'];
            $consulta = "SELECT oferta_id as pid FROM categoria_producto WHERE categoria_id = (SELECT categoria_id as cid FROM categoria WHERE categoria_descripcion = '$this->categoria' LIMIT 1)";
            $this->sql_con->set_charset("utf8");
            $rs = $this->sql_con->query($consulta);
            if($rs === false) {
                trigger_error('Ha ocurrido un error');
            } else {
                while($row = $rs->fetch_assoc()){
                }
            }
        }
        
        public function __destruct(){
            echo json_encode($this->datos);
        }
    }
?>