<?php

$credito = new Creditos();

Class Creditos{

    protected $link, $sql_con;
    protected $datos = array();
    protected $accion;
    
    protected function cargar_planes($filtrar_estado, $filtro){
        $consulta = "SELECT plan_credito_nombre as nombre, plan_credito_codigo as codigo, plan_credito_costo_mantencion as c_mantencion, plan_credito_costo_uso as c_uso, plan_credito_estado as estado FROM plan_credito";
        if($filtrar_estado == true){
            $consulta .= " WHERE $filtro";
        }
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            $this->datos['resultado'] = 0;
        }else{
            $this->datos['resultado'] = 1;
            $this->datos['planes'] = array();
            while($row = $rs->fetch_assoc()){
                $dato = array();
                foreach($row as $indice=>$valor){
                    $dato[$indice] = $valor;
                }
                array_push($this->datos['planes'], $dato);
            }
        }
    }
    
    protected function crear_plan($nombre, $c_mantencion, $c_uso){
        $insercion = $this->sql_con->prepare("INSERT INTO plan_credito (plan_credito_nombre, plan_credito_codigo, plan_credito_costo_mantencion, plan_credito_costo_uso, plan_credito_estado) VALUES (?, ?, ?, ?, 1)");
        $insercion->bind_param('ssii', $nombre, md5(date('Y-m-d H:i:s') . $nombre), $c_mantencion, $c_uso);
        $insercion->execute();
        $insertadas = $this->sql_con->affected_rows;
        $insercion->close();
        if($insertadas > 0){
            $this->datos['resultado'] = 1;    
        }else{
            $this->datos['resultado'] = 0;
        }
    }
    
    protected function editar_plan($nombre, $codigo, $c_mantencion, $c_uso){
        $editar = $this->sql_con->prepare("UPDATE plan_credito SET plan_credito_nombre = ?, plan_credito_costo_mantencion = ?, plan_credito_costo_uso = ? WHERE plan_credito_codigo = ?");
        $editar->bind_param('siis',
                           $nombre,
                           $c_mantencion,
                           $c_uso,
                           $codigo);
        $editar->execute();
        $insertadas = $this->sql_con->affected_rows;
        $editar->close();
        if($insertadas > 0){
            $this->datos['resultado'] = 1;    
        }else{
            $this->datos['resultado'] = 0;
        }
    }
    
    protected function actualizar_estado($codigo, $estado){
        if($estado == 0){
            $estado = 1;
        }else{
            $estado = 0;
        }
        $edicion = $this->sql_con->prepare("UPDATE plan_credito SET plan_credito_estado = ? WHERE plan_credito_codigo = ?");
        $edicion->bind_param('is', $estado, $codigo);
        $edicion->execute();
        $insertadas = $this->sql_con->affected_rows;
        $edicion->close();
        if($insertadas > 0){
            $this->datos['resultado'] = 1;    
        }else{
            $this->datos['resultado'] = 0;
        }
    }
    
    protected function procesar(){
        $this->accion = $_POST['accion'];
        switch($this->accion){
            case 1:
                if($_POST['filtro'] == 0){
                    $this->cargar_planes(false, '');
                }else{
                    $this->cargar_planes(true, 'plan_credito_estado = 1');
                }
            break;
            case 2:
                $nombre = mysqli_real_escape_string($this->sql_con, $_POST['nombre']);
                $c_mantencion = mysqli_real_escape_string($this->sql_con, $_POST['c_mantencion']);
                $c_uso = mysqli_real_escape_string($this->sql_con, $_POST['c_uso']);
                $this->crear_plan($nombre, $c_mantencion, $c_uso);
            break;
            case 3:
                $nombre = mysqli_real_escape_string($this->sql_con, $_POST['nombre']);
                $codigo = mysqli_real_escape_string($this->sql_con, $_POST['codigo']);
                $c_mantencion = mysqli_real_escape_string($this->sql_con, $_POST['c_mantencion']);
                $c_uso = mysqli_real_escape_string($this->sql_con, $_POST['c_uso']);
                $this->editar_plan($nombre, $codigo, $c_mantencion, $c_uso);
            break;
            case 4:
                $codigo = mysqli_real_escape_string($this->sql_con, $_POST['codigo']);
                $estado = mysqli_real_escape_string($this->sql_con, $_POST['estado']);
                $this->actualizar_estado($codigo, $estado);
            break;
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
        echo json_encode($this->datos);
    }
}

?>