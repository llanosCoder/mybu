<?php

$promocion_especial = new Promociones();

class Promociones{

    protected $link, $sql_con;
    protected $parametros = array(), $datos = array();

    public function __construct(){
        session_start();
        require('../hosts.php');
        require('conexion_new.php');
        $this->procesar();
    }
    
    protected function set_host($host){
        $hosteo = new Host();
        $hosteo->obtener_conexion($host);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
    }
    
    protected function set_conexion($host, $user, $pass, $bd){
        
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    
    protected function procesar(){
        $accion = $_POST['accion'];
        switch($accion){
            case 1:
                $this->set_host(0);
                $this->parametros_abrev = $_POST['parametros'];
                foreach($this->parametros_abrev as $indice=>$valor){
                    switch($valor){
                        case 'id':
                            $param = 'promocion_especial_id';
                            break;
                        case 'tipo':
                            $param = 'promocion_especial_tipo_id';
                            break;
                        case 'descripcion':
                            $param = 'promocion_especial_descripcion';
                            break;
                        case 'porcentaje':
                            $param = 'promocion_especial_porcentaje';
                            break;
                        case 'estado':
                            $param = 'promocion_especial_estado';
                            break;
                    }
                    array_push($this->parametros, $param);
                }
                $this->obtener_promociones();
                break;
            case 2:
                $this->set_host(1);
                $this->obtener_tipo_promociones();
                break;
            case 3:
                $this->set_host(0);
                $this->parametros = $_POST['parametros'];
                $this->agregar_promocion();
                break;
            case 4:
                $this->set_host(0);
                $this->parametros = $_POST['parametros'];
                $this->editar_promocion();
                break;
            case 5:
                $this->set_host(0);
                $this->parametros['id'] = mysqli_real_escape_string($this->sql_con, $_POST['pId']);
                if($this->parametros['id'] != 2){
                    $this->eliminar_promocion();
                }else{
                    $this->datos['promocion'][0]['resultado'] = 0;
                }
                break;
            case 6:
                $this->set_host(0);
                $id = mysqli_real_escape_string($this->sql_con, $_POST['pId']);
                $estado = mysqli_real_escape_string($this->sql_con, $_POST['estado']);
                $this->activar_desactivar_promocion($estado, $id);
                break;
        }
    }
    
    protected function obtener_promociones(){
        $consulta_promociones = "SELECT ";
        $i = 0;
        for($i = 0; $i < count($this->parametros_abrev); $i++){
            if($i > 0){
                $consulta_promociones = $consulta_promociones . ", ";
            }
            $consulta_promociones .= $this->parametros[$i] . ' as ' . $this->parametros_abrev[$i];
        }
        $consulta_promociones .= " FROM promocion_especial";
        $rs_promociones = $this->sql_con->query($consulta_promociones);
        if($rs_promociones === false){
            echo $consulta_promociones;
        }else{
            $this->datos['promociones'] = array();
            while($row = $rs_promociones->fetch_assoc()){
                $dato = array();
                foreach($row as $indice=>$valor){
                    $dato[$indice] = $valor;
                }
                array_push($this->datos['promociones'], $dato);
            }
        }
    }
    
    protected function obtener_tipo_promociones(){
        $consulta_tipo = "SELECT promocion_especial_tipo_id as value, promocion_especial_tipo_descripcion as nombre FROM promocion_especial_tipo";
        $rs_tipo = $this->sql_con->query($consulta_tipo);
        if($rs_tipo === false){
            echo $consulta_tipo;
        }else{
            $this->datos['tipos'] = array();
            while($row_tipo = $rs_tipo->fetch_assoc()){
                $dato = array();
                foreach($row_tipo as $indice=>$valor){
                    $dato[$indice] = $valor;
                }
                array_push($this->datos['tipos'], $dato); 
            }
        }
    }
    
    protected function agregar_promocion(){
        $this->parametros['select_tipo_promocion_especial'] = mysqli_real_escape_string($this->sql_con, $this->parametros['select_tipo_promocion_especial']);
        $this->parametros['descripcion_especial'] = mysqli_real_escape_string($this->sql_con, $this->parametros['descripcion_especial']);
        $this->parametros['porcentaje_especial'] = mysqli_real_escape_string($this->sql_con, $this->parametros['porcentaje_especial']);
        
        $insercion_promocion = $this->sql_con->prepare("INSERT INTO promocion_especial(promocion_especial_tipo_id, promocion_especial_descripcion, promocion_especial_porcentaje) VALUES (?, ?, ?)");
        $insercion_promocion->bind_param('isi',
        $this->parametros['select_tipo_promocion_especial'],
        $this->parametros['descripcion_especial'],
        $this->parametros['porcentaje_especial']);
        $insercion_promocion->execute();
        $insertadas = $this->sql_con->affected_rows;
        $insercion_promocion->close();
        $this->datos['promocion'] = array();
        $dato = array();
        if($insertadas > 0)
            $dato['resultado'] = 1;
        else
            $dato['resultado'] = 0;
        array_push($this->datos['promocion'], $dato);
    }
    
    protected function editar_promocion(){
        $id = mysqli_real_escape_string($this->sql_con, $this->parametros['id']);
        $tipo = mysqli_real_escape_string($this->sql_con, $this->parametros['select_tipo_promocion_especial']);
        $descripcion = mysqli_real_escape_string($this->sql_con, $this->parametros['descripcion_especial']);
        $porcentaje = mysqli_real_escape_string($this->sql_con, $this->parametros['porcentaje_especial']);
        
        $editar_promocion = "UPDATE promocion_especial SET promocion_especial_tipo_id = $tipo, promocion_especial_descripcion = '$descripcion', promocion_especial_porcentaje = $porcentaje WHERE promocion_especial_id = $id";
        $rs_editar = $this->sql_con->query($editar_promocion);
        if($rs_editar === false) {
            echo $editar_promocion;
            trigger_error('Ha ocurrido un error');
        } else {
            $this->datos['promocion'] = array();
            $dato = array();
            $dato['resultado'] = $this->sql_con->affected_rows;
            array_push($this->datos['promocion'], $dato);
        }
    }
    
    protected function eliminar_promocion(){
        $eliminar_promocion = $this->sql_con->prepare("DELETE FROM promocion_especial WHERE promocion_especial_id = ?");
        $eliminar_promocion->bind_param('i',
        $this->parametros['id']);
        $eliminar_promocion->execute();
        $eliminadas = $this->sql_con->affected_rows;
        $eliminar_promocion->close();
        $this->datos['promocion'] = array();
        $dato = array();
        if($eliminadas > 0)
            $dato['resultado'] = 1;
        else
            $dato['resultado'] = 0;
        array_push($this->datos['promocion'], $dato);
    }
    
    protected function activar_desactivar_promocion($estado, $id){
        if($estado == 1)
            $nuevo_estado = 0;
        else
            $nuevo_estado = 1;
        $consulta_activar_desactivar = "UPDATE promocion_especial SET promocion_especial_estado = $nuevo_estado WHERE promocion_especial_id = $id";
        $rs_activar_desactivar = $this->sql_con->query($consulta_activar_desactivar);
        if($rs_activar_desactivar === false) {
            trigger_error('Ha ocurrido un error');
        } else {
            $this->datos['promocion'] = array();
            $dato = array();
            $dato['resultado'] = $this->sql_con->affected_rows;
            array_push($this->datos['promocion'], $dato);
        }
    }
    
    public function __destruct(){
        echo json_encode($this->datos);
    }

}

?>