<?php

$variable = new Clase();

    class Clase{

    protected $link, $sql_con;
    protected $tipo_cuenta, $datos = array();

    public function __construct(){
        session_start();
        require('../hosts.php');
        require('conexion_new.php');
        ini_set('display_errors', 'on');
        
        $hosteo = new Host();
        $hosteo->obtener_conexion(0);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $this->procesar();
    }
        
    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    
    protected function procesar(){
        $this->tipo_cuenta = $_SESSION['tipo_cuenta'];
        if($this->tipo_cuenta != 1){
            $this->datos['resultado'] = 2; //No posee los permisos para eliminar producto
            exit();
        }
        $producto_codigo = mysqli_real_escape_string($this->sql_con, $_POST['codigo']);
        $p_id = $this->obtener_producto_id($producto_codigo);
        if($this->producto_vendido($p_id)){
            $this->datos['resultado'] = 3; //Producto no se pudo eliminar ya que se vendio
            exit();
        }
        if($this->eliminar($p_id, 'producto', 'producto_id')){
            $this->eliminar($p_id, 'producto_sucursal', 'producto_id');
            $this->eliminar($p_id, 'producto_dimension', 'producto_dimension_id');
            $this->eliminar($p_id, 'producto_pesable', 'producto_id');
            $this->eliminar($p_id, 'producto_talla', 'producto_talla_id');
            $this->eliminar($p_id, 'producto_volumen', 'producto_volumen_id');
            $this->eliminar($p_id, 'categoria_producto', 'producto_id');
            $this->datos['resultado'] = 1;
        }else{
            $this->datos['resultado'] = 0;
        }
    }
        
    protected function eliminar($p_id, $tabla, $campo){
        $consulta = "DELETE FROM $tabla WHERE $campo = ?";
        $eliminar = $this->sql_con->prepare($consulta);
        $eliminar->bind_param('i', $p_id);
        $eliminar->execute();
        $afectadas = $this->sql_con->affected_rows;
        $eliminar->close();
        if($afectadas > 0){
            return true;
        }else{
            return false;
        }
    }
        
    protected function producto_vendido($p_id){
        $consulta = "SELECT count(*) as cont FROM venta_producto WHERE producto_id = $p_id";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            exit();
        }else{
            $row = $rs->fetch_assoc();
            if($row['cont'] > 0){
                return true;
            }else{
                return false;
            }
        }
    }
        
    protected function obtener_producto_id($codigo){
        $consulta = "SELECT producto_id as p_id FROM producto WHERE producto_codigo = '$codigo'";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            exit();
        } else {
            $row = $rs->fetch_assoc();
            return $row['p_id'];
        }
    }

    public function __destruct(){
        echo json_encode($this->datos);
    }

}

?>