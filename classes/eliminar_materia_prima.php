<?php

$clase = new Clase();

Class Clase{

    protected $link, $sql_con;
    protected $resultado = array();
    
    protected function obtener_materia_prima_id($codigo){
        $consulta = "SELECT materia_prima_id as id FROM materia_prima WHERE materia_prima_codigo = '$codigo'";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return 0;
        }else{
            $row = $rs->fetch_assoc();
            return $row['id'];
        }
    }
    
    protected function eliminar_materia_prima($id){
        $eliminacion = $this->sql_con->prepare("DELETE FROM materia_prima WHERE materia_prima_id = ?");
        $eliminacion->bind_param('s', $id);
        $eliminacion->execute();
        $insertadas = $this->sql_con->affected_rows;
        $eliminacion->close();
        if($insertadas > 0){
            return true;
        }else{
            return false;
        }
    }
    
    protected function eliminar_asignaciones_materia_prima($id){
        $eliminacion = $this->sql_con->prepare("DELETE FROM materia_prima_producto WHERE materia_prima_id = ?");
        $eliminacion->bind_param('s', $id);
        $eliminacion->execute();
        $insertadas = $this->sql_con->affected_rows;
        $eliminacion->close();
        if($insertadas > 0){
            return true;
        }else{
            return false;
        }
    }
    
    protected function procesar(){
        if($_SESSION['tipo_cuenta'] == 1){
            $codigo = mysqli_real_escape_string($this->sql_con, $_POST['codigo']);
            $id = $this->obtener_materia_prima_id($codigo);
            if($this->eliminar_materia_prima($id)){
                $this->eliminar_asignaciones_materia_prima($id);
                $this->resultado['resultado'] = 1;
            }else{
                $this->resultado['resultado'] = 0; //No se pudo eliminar materia prima
            }
        }else{
            $this->resultado['resultado'] = 3; //No tiene permisos suficientes
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