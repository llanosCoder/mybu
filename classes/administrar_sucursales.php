<?php

$sucursal = new Sucursales();

class Sucursales{

    protected $link, $sql_con;
    protected $tipo_cuenta, $accion;

    public function __construct(){
        session_start();
        require('../hosts.php');
        require('conexion_new.php');
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
        $this->accion = $_POST['accion'];
        //$this->empresa = $_SESSION['empresa'];
        switch($this->accion){
            case 1:
                $this->obtener_sucursales();
                break;
            case 2:
                $this->tipo_cuenta = $_SESSION['tipo_cuenta'];
                if($this->tipo_cuenta == 1){
                    $sucursal_origen = mysqli_real_escape_string($this->sql_con, $_POST['sucursal_origen']);
                    $sucursal_destino = mysqli_real_escape_string($this->sql_con, $_POST['sucursal_destino']);
                    $producto = mysqli_real_escape_string($this->sql_con, $_POST['producto']);
                    $cantidad = mysqli_real_escape_string($this->sql_con, $_POST['cantidad']);
                    $this->traspasar_stock($sucursal_origen, $sucursal_destino, $producto, $cantidad);
                } else {
                    $this->datos['resultado'] = 0;
                }
                break;
        }
    }
    
    protected function obtener_sucursales(){
        $this->datos['sucursales'] = array();
        $consulta = $this->sql_con->prepare("SELECT sucursal_id as val, sucursal_direccion as direccion FROM sucursal");
        $consulta->execute();
        $result = $consulta->get_result();
        while($row = $result->fetch_assoc()){
            $dato = array();
            foreach($row as $indice=>$fila){
                $dato[$indice] = $fila;
            }
            array_push($this->datos['sucursales'], $dato);
        }
    }
    
    protected function traspasar_stock($origen, $destino, $producto, $cantidad){
        $p_id = $this->obtener_producto($producto);
        if($this->verificar_producto_sucursal_origen($p_id, $origen) >= $cantidad){
            $this->verificar_producto_sucursal_destino($p_id, $destino);
            if($this->aumentar_reducir_stock($origen, $p_id, $cantidad, '-')){
                if($this->aumentar_reducir_stock($destino, $p_id, $cantidad, '+')){
                    $this->registrar_traspaso($origen, $destino, $p_id, $cantidad);
                    $this->datos['resultado'] = 1;
                }else{
                    $this->datos['resultado'] = 2;
                }
            }else{
                $this->datos['resultado'] = 0;
            }
        }else{
            $this->datos['resultado'] = 3;
        }
    }
    
    protected function verificar_producto_sucursal_origen($p_id, $sucursal){
        $consulta = "SELECT producto_sucursal_stock_real AS stock FROM producto_sucursal WHERE producto_id = $p_id AND sucursal_id = $sucursal";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            trigger_error('Ha ocurrido un error');
        }else{
            $row = $rs->fetch_assoc();
            return $row['stock'];
        }
    }
    
    protected function verificar_producto_sucursal_destino($p_id, $sucursal){
        $consulta = "SELECT count(*) as cont FROM producto_sucursal WHERE sucursal_id = $sucursal AND producto_id = $p_id";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            trigger_error('Ha ocurrido un error');
        }else{
            $row = $rs->fetch_assoc();
            if($row['cont'] <= 0){
                $this->insertar_producto($p_id, $sucursal);
            }
        }
    }
    
    protected function insertar_producto($p_id, $sucursal){
        $insercion = $this->sql_con->prepare("INSERT INTO producto_sucursal (producto_id, sucursal_id, producto_sucursal_precio_unitario, producto_sucursal_precio_mayorista, producto_sucursal_stock_real, producto_sucursal_stock_minimo) VALUES (?, ?, 0, 0, 0, 0)");
        $insercion->bind_param('ii', $p_id, $sucursal);
        $insercion->execute();
        $afectadas = $this->sql_con->affected_rows;
        $insercion->close();
    }
     
    protected function obtener_producto($codigo){
        $consulta = "SELECT producto_id as p_id FROM producto WHERE producto_codigo = '$codigo'";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            trigger_error('Ha ocurrido un error');
        }else{
            while($row = $rs->fetch_assoc()){
                return $row['p_id'];
            }
        }
    }

    protected function aumentar_reducir_stock($s_id, $p_id, $cantidad, $operacion){
        //echo "$cantidad \n $operacion \n $s_id \n $p_id";
        $actualizar = "UPDATE producto_sucursal SET producto_sucursal_stock_real = producto_sucursal_stock_real $operacion $cantidad WHERE sucursal_id = $s_id AND producto_id = $p_id";
        if($this->sql_con->query($actualizar) === false) {
          trigger_error('Wrong SQL: ' . $actualizar . ' Error: ' . $this->sql_con->error, E_USER_ERROR);
        } else {
          $filas_afectadas = $this->sql_con->affected_rows;
            if($filas_afectadas == 0){
                return false;
            }else{
                return true;
            }
        }
    }
    
    protected function registrar_traspaso($origen, $destino, $p_id, $cantidad){
        $insercion = $this->sql_con->prepare("INSERT INTO traspaso_stock_registro(usuario_id, producto_id, sucursal_origen, sucursal_destino, traspaso_stock_registro_cantidad, traspaso_stock_registro_fecha) VALUES (?, ?, ?, ?, ?, NOW())");
        $insercion->bind_param('iiiii',
        $_SESSION['id'],
        $p_id,
        $origen,
        $destino,
        $cantidad);
        $insercion->execute();
        $insercion->close();
    }
    
    public function __destruct(){
        echo json_encode($this->datos);
    }

}

?>