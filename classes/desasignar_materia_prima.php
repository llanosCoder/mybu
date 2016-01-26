<?php

$materia = new Materias();

Class Materias{

    protected $link, $sql_con;
    protected $resultado = array();

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
    
    protected function obtener_id_materia_prima($codigo){
        $consulta = "SELECT materia_prima_id as m_id FROM materia_prima WHERE materia_prima_codigo = '$codigo'";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return 0;
        }else{
            $row = $rs->fetch_assoc();
            if(mysqli_num_rows($rs) > 0){
                return $row['m_id'];
            }else{
                return 0;
            }
        }
    }
    
    protected function obtener_id_producto($codigo){
        $consulta = "SELECT producto_id as p_id FROM producto WHERE producto_codigo = '$codigo'";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return 0;
        }else{
            $row = $rs->fetch_assoc();
            if(mysqli_num_rows($rs) > 0){
                return $row['p_id'];
            }else{
                return 0;
            }
        }
    }
    
    protected function eliminar_asignacion($m_id, $p_id){
        $eliminacion = $this->sql_con->prepare("DELETE FROM materia_prima_producto WHERE producto_id = ? AND materia_prima_id = ?");
        $eliminacion->bind_param('ii', $p_id, $m_id);
        $eliminacion->execute();
        $eliminadas = $this->sql_con->affected_rows;
        $eliminacion->close();
        if($eliminadas > 0){
            return true;
        }else{
            return false;
        }
    }
    
    protected function procesar(){
        $materia_codigo = mysqli_real_escape_string($this->sql_con, $_POST['materia']);
        $m_id = $this->obtener_id_materia_prima($materia_codigo);
        $producto_codigo = mysqli_real_escape_string($this->sql_con, $_POST['producto']);
        $p_id = $this->obtener_id_producto($producto_codigo);
        if($this->eliminar_asignacion($m_id, $p_id)){
            $this->resultado['resultado'] = 1; //Productos desasignados
        }else{
            $this->resultado['resultado'] = 0; //No se pudo desasignar
        }
    }
    
    public function __destruct(){
        echo json_encode($this->resultado);
    }
}

?>