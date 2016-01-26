<?php

$clase = new Clase();

class Clase{

    protected $link, $sql_con;
    protected $resultado = array();
    
    protected function existe_habilitado(){
        $consulta = "SELECT count(*) as cont FROM plan_pago WHERE plan_pago_codigo = 'habilitado'";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return true;
        }else{
            $row = $rs->fetch_assoc();
            if($row['cont'] > 0){
                return true;
            }else{
                return false;
            }
        }
    }
    
    protected function crear_plan_pago_habilitado(){
        if(!$this->existe_habilitado()){
            $insercion = $this->sql_con->prepare("INSERT INTO plan_pago (plan_pago_nombre, plan_pago_codigo, plan_pago_cuota, plan_pago_interes) VALUES ('HABILITADOS', 'habilitado', 1, 0)");
            $insercion->execute();
            $afectadas = $this->sql_con->affected_rows;
            $insercion->close();
            if($afectadas > 0){
                return true;
            }else{
                return false;
            }
        } else{
            return true;
        }
    }
    
    protected function eliminar_plan_pago_habilitado(){
        $eliminacion = $this->sql_con->prepare("DELETE FROM plan_pago WHERE plan_pago_codigo = 'habilitado'");
        $eliminacion->execute();
        $afectadas = $this->sql_con->affected_rows;
        $eliminacion->close();
        if($afectadas > 0){
            return true;
        }else{
            return false;
        }
    }
    
    protected function cambiar_estado($estado){
        if($estado == 'false'){
            if($this->eliminar_plan_pago_habilitado()){
                return true;
            }else{
                return false;
            }
        } else {
            if($this->crear_plan_pago_habilitado()){
                return true;
            }else{
                return false;
            }
        }
    }
    
    protected function obtener_estado_habilitados(){
        $consulta = "SELECT count(*) as cont FROM plan_pago WHERE plan_pago_codigo = 'habilitado'";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return 0;
        }else{
            $row = $rs->fetch_assoc();
            return $row['cont'];
        }
    }
    
    protected function procesar(){
        if($_POST['accion'] == 1){
            $estado = mysqli_real_escape_string($this->sql_con, $_POST['estado']);
            if($this->cambiar_estado($estado)){
                $this->resultado['resultado'] = 1; //Estado cambiado exitosamente
            }else{
                $this->resultado['resultado'] = 0; //Estado no se pudo cambiar
            }
        }else{
            $this->resultado['resultado'] = $this->obtener_estado_habilitados();
        }
    }
    
    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    
    public function __construct(){
        session_start();
        require('../hosts.php');
        require('conexion_new.php');
        $hosteo = new Host();
        $hosteo->obtener_conexion(0);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $this->procesar();
    }

    public function __destruct(){
        echo json_encode($this->resultado);
    }
}

?>