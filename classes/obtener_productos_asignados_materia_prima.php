<?php

$materia = new Materias();

Class Materias{

    protected $link, $sql_con;
    protected $datos = array();
    
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
    
    protected function obtener_id($codigo){
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
    
    protected function obtener_productos_asignados($materia){
        $consulta = "SELECT p.producto_nombre as nombre, p.producto_codigo as codigo FROM producto p JOIN materia_prima_producto mpp ON p.producto_id = mpp.producto_id WHERE mpp.materia_prima_id = $materia";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            $this->datos['resultado'] = 0;
        }else{
            if(mysqli_num_rows($rs) > 0){
                $this->datos['productos'] = array();
                $dato = array();
                while($row = $rs->fetch_assoc()){
                    foreach($row as $indice=>$valor){
                        $dato[$indice] = $valor;
                    }
                    array_push($this->datos['productos'], $dato);
                }
                $this->datos['resultado'] = 1;
            }else{
                $this->datos['resultado'] = 0;
            }
        }
    }
    
    protected function procesar(){
        $materia_codigo = mysqli_real_escape_string($this->sql_con, $_POST['m_cod']);
        $m_id = $this->obtener_id($materia_codigo);
        if($m_id != 0){
            $this->obtener_productos_asignados($m_id);
        }else{
            $this->datos['resultado'] = 0;
        }
    }
    
    public function __destruct(){
        echo json_encode($this->datos);
    }
}

?>