<?php
$bodega = new Bodegas();

class Bodegas{

    protected $link, $sql_con;
    protected $codigo, $parametros = array(), $solo_stock = false;
    protected $resultado;
    
    public function __construct(){
        session_start();
        
        $this->sucursal = $_SESSION['sucursal'];
        $this->empresa = $_SESSION['empresa'];
        
        require('../hosts.php');
        require('conexion_new.php');
        $hosteo = new Host();
        $hosteo->obtener_conexion(0);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $this->procesar();
        $this->editar_bodega();
    }
    
    protected function procesar(){
        foreach($_POST as $indice=>$value){
            switch($indice){
                case 'precio_u':
                    $this->parametros['producto_sucursal_precio_unitario'] = $value;
                    break;
                case 'precio_m':
                    $this->parametros['producto_sucursal_precio_mayorista'] = $value;
                    break;
                case 'stock_m':
                    $this->parametros['producto_sucursal_stock_minimo'] = $value;
                    break;
                case 'stock_r':
                    $this->parametros['producto_sucursal_stock_real'] = $value;
                    break;
                case 'costo':
                    $this->parametros['producto_sucursal_costo'] = $value;
                    break;
            }
        }
    }
    
    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    
    protected function editar_bodega(){
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->usuario = $_SESSION['id'];
        $this->obtener_parametros();
        $p_id = $this->obtener_id();
        if($this->solo_stock){
            $this->stock_r = $this->parametros['producto_sucursal_stock_real'];
            $consulta_producto = "UPDATE producto_sucursal SET producto_sucursal_stock_real = producto_sucursal_stock_real + $this->stock_r WHERE producto_id = $p_id AND sucursal_id = $this->sucursal;";
        }else{
            //$consulta_producto = "UPDATE producto_sucursal SET producto_sucursal_precio_unitario = $this->precio_u, producto_sucursal_precio_mayorista = $this->precio_m, producto_sucursal_stock_minimo = $this->stock_m, producto_sucursal_stock_real = $this->stock_r, producto_sucursal_costo = $this->costo WHERE producto_id = $p_id AND sucursal_id = $this->sucursal;";
            $consulta_producto = "UPDATE producto_sucursal SET ";
            $i = 0;
            foreach($this->parametros as $indice=>$value){
                if($i != 0){
                    $consulta_producto .= ", ";
                }
                if($value == '' || $value == ' ') {
                    $value = 0;
                }
                $consulta_producto .= $indice . " = " . $value;
                $i++;
            }
            $consulta_producto .= " WHERE producto_id = $p_id AND sucursal_id = $this->sucursal;";
        }
        if($this->sql_con->query($consulta_producto) === false) {
                  trigger_error('Wrong SQL: ' . $consulta_producto . ' Error: ' . $this->sql_con->error, E_USER_ERROR);
        } else {
            $edicion_exitosa = $this->sql_con->affected_rows;
            if($edicion_exitosa > 0 && $this->solo_stock){
                $hosteo = new Host();
                $hosteo->obtener_conexion(0);
                $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
                $consulta_stock = $this->sql_con->prepare("INSERT INTO registro_stock VALUES(null, ?, ?, ?, ?, NOW())");
                $consulta_stock->bind_param('iiii',
                $p_id,
                $this->sucursal,
                $this->usuario,
                $this->stock_r);
                $consulta_stock->execute(); 
                $consulta_stock->close();
                $id = mysqli_insert_id($this->sql_con);
                if($id == 0){
                    $this->resultado = 0;
                }else{
                    $this->resultado = 1;
                }
            }else{
                if(!$this->solo_stock && $edicion_exitosa){
                    if($this->sucursal == 1){
                        $this->actualizar_oferta($p_id);
                        $hosteo = new Host();
                        $hosteo->obtener_conexion(2);
                        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
                        $this->actualizar_oferta($p_id);
                    }
                    $this->resultado = 1;
                }else{
                    $this->resultado = 0;
                }
            }
        }
    }
                                    
    protected function obtener_id(){
        $consulta_id = "SELECT producto_id FROM producto WHERE producto_codigo = '$this->codigo'";
        $result_id = $this->sql_con->query($consulta_id);
        $id = '';
        if($result_id === false){
            trigger_error("Ha ocurrido un error");
            $this->resultado = 2;
            exit();
        }else{
            $row = $result_id->fetch_assoc();
        }
        return $row['producto_id'];
    }
    
    protected function actualizar_oferta($p_id){
        
        $consulta_oferta = "SELECT oferta_id as o_id FROM oferta WHERE producto_id = $p_id";
        $rs_oferta = $this->sql_con->query($consulta_oferta);
        if($rs_oferta === false){
            exit();
        }else{
            while($row_oferta = $rs_oferta->fetch_assoc()){
                $update_oferta = $this->sql_con->prepare("UPDATE oferta SET producto_precio_original = ? WHERE oferta_id = ? AND oferta_proveedor = ?");
                $update_oferta->bind_param('iii',
                $this->precio_m,
                $row_oferta['o_id'],
                $this->empresa);
                $update_oferta->execute();
                $insercion = $this->sql_con->affected_rows;
                $update_oferta->close();
            }
        }
    }
    
    protected function obtener_parametros(){
        $this->codigo =  mysqli_real_escape_string($this->sql_con, $_POST['codigo']);
        $this->solo_stock = $_POST['solo_stock'];
    }
    
    public function __destruct(){
        echo $this->resultado;
    }
}

?>