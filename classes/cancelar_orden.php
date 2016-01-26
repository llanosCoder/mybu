<?php

$variable = new Clase();

class Clase{

    protected $link, $sql_con;
    protected $voucher;
    protected $usuario, $empresa;
    protected $resultado;
    protected $solicitado;

    public function __construct(){
        session_start();
        require('../hosts.php');
        require('conexion_new.php');
        $hosteo = new Host();
        $hosteo->obtener_conexion(0);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $this->obtener_parametros();
        $this->cancelar_orden();
    }
    
    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    
    protected function obtener_parametros(){
        $this->usuario = $_SESSION['id'];
        $this->empresa = $_SESSION['empresa'];
        $this->solicitado = mysqli_real_escape_string($this->sql_con, $_POST['solicitado']);
        $this->voucher = mysqli_real_escape_string($this->sql_con, $_POST['voucher']);
    }
    
    protected function cancelar_orden(){
        if($this->verificar_orden()){
            if($this->registrar_cancelacion()){
                //Orden bd local
                $this->cerrar_orden($this->voucher, 4);
                //Orden bd administración
                $hosteo = new Host();
                $hosteo->obtener_conexion(1);
                $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
                $this->cerrar_orden($this->voucher, 4);
                $this->host = $this->obtener_origen_datos();
                //Orden bd externa
                $hosteo = new Host();
                $hosteo->obtener_conexion($this->host);
                $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
                $this->cerrar_orden($this->voucher, 4);
            }
        }else{
            $this->resultado = 2;
        }
    }
    
    protected function obtener_origen_datos(){
        $consulta_origen = "SELECT host FROM empresa_conexion WHERE empresa_id = $this->solicitado";
        $rs_origen = $this->sql_con->query($consulta_origen);
        if($rs_origen === false){
            exit();
        }else{
            $row_origen = $rs_origen->fetch_assoc();
            return $row_origen['host'];
        }
    }
    
    protected function verificar_orden(){
        $consulta_usuario = "SELECT usuario_id as id FROM usuario_empresa WHERE empresa_id = (SELECT empresa_id FROM usuario_empresa WHERE usuario_id = $this->usuario)";
        $rs_usuario = $this->sql_con->query($consulta_usuario);
        if($rs_usuario === false){
            exit();
        }else{
            while($row_usuario = $rs_usuario->fetch_assoc()){
                if($this->usuario == $row_usuario['id']){
                    return true;
                }
            }
            return false;
        }
    }
    
    protected function registrar_cancelacion(){
        $o_id = $this->obtener_id_voucher($this->voucher);
        $insercion_cancelacion = $this->sql_con->prepare("INSERT INTO orden_cancelada VALUES(?, ?, NOW())");
        $insercion_cancelacion->bind_param('ii',
        $o_id,
        $this->usuario);
        $insercion_cancelacion->execute();
        $insercion = $this->sql_con->affected_rows;
        $insercion_cancelacion->close();
        if($insercion > 0)
            return true;
        else
            return false;
    }
    
    protected function obtener_id_voucher($voucher){
        $consulta_id = "SELECT orden_id as id FROM orden_compra WHERE orden_voucher = '$voucher'";
        $rs_id = $this->sql_con->query($consulta_id);
        if($rs_id === false){
            return 0;
        }else{
            $row_id = $rs_id->fetch_assoc();
            return $row_id['id'];
        }
    }
    
        protected function cerrar_orden($voucher, $estado_nuevo){
        $update_producto = "UPDATE orden_compra SET orden_estado = $estado_nuevo WHERE orden_voucher = '$voucher'";
        if($this->sql_con->query($update_producto) === false) {
            trigger_error('Wrong SQL: ' . $update_producto . ' Error: ' . $this->sql_con->error, E_USER_ERROR);
            $this->resultado = 0; //No se ha podido procesar su compra
            exit();
        } else {
            $edicion_exitosa = $this->sql_con->affected_rows;
            if($edicion_exitosa > 0){
                $this->resultado = 1; //Venta realizada con éxito
            }else{
                $this->resultado = 0; //No se pudo cerrar orden
            }
        }
    }
    
    public function __destruct(){
        echo $this->resultado;
    }

}

?>