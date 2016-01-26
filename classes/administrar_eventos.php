<?php

$variable = new Clase();

class Clase{

    protected $link, $sql_con;
    protected $accion, $usuario, $datos = array();

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
        $this->accion = $_REQUEST['accion'];
        $this->usuario = $_SESSION['id'];
        switch($this->accion){
            case 1:
                $fecha_ini = mysqli_real_escape_string($this->sql_con, $_POST['fecha_inicio']);
                $fecha_fin = mysqli_real_escape_string($this->sql_con, $_POST['fecha_fin']);
                $descripcion = mysqli_real_escape_string($this->sql_con, $_POST['descripcion']);
                $tipo = mysqli_real_escape_string($this->sql_con, $_POST['tipos']);
                $tipos = explode(" ", $tipo);
                $this->nuevo_evento($fecha_ini, $fecha_fin, $descripcion, $tipos);
            break;
            case 2:
                $this->obtener_eventos2();
            break;
            case 3:
                $evento = mysqli_real_escape_string($this->sql_con, $_POST['evento']);
                $fecha_ini = mysqli_real_escape_string($this->sql_con, $_POST['fecha_inicio']);
                $fecha_fin = mysqli_real_escape_string($this->sql_con, $_POST['fecha_fin']);
                $descripcion = mysqli_real_escape_string($this->sql_con, $_POST['descripcion']);
                $tipo = mysqli_real_escape_string($this->sql_con, $_POST['tipos']);
                $tipos = explode(" ", $tipo);
                $this->editar_evento($evento, $fecha_ini, $fecha_fin, $descripcion, $tipos);
            break;
            case 4:
                $evento = mysqli_real_escape_string($this->sql_con, $_POST['eId']);
                $this->eliminar_evento($evento);
            case 5:
                $evento = mysqli_real_escape_string($this->sql_con, $_POST['eId']);
                $this->obtener_eventos($evento);
        }
    }
    
    protected function obtener_color($tipos){
        for($i = 0; $i < count($tipos); $i++){
            $clas = substr($tipos[$i], 0, 3);
            if($clas == 'bg-'){
                $color = $tipos[$i];
                break;
            }else{
                $color = 'bg-inverse';
            }
        }
        return $color;
    }
    
    protected function nuevo_evento($fecha_ini, $fecha_fin, $descripcion, $tipos){
        $color = $this->obtener_color($tipos);
        $insercion = $this->sql_con->prepare("INSERT INTO evento (usuario_id, evento_descripcion, evento_fecha_inicio, evento_fecha_fin, evento_color) VALUES (?, ?, ?, ?, ?)");
        $insercion->bind_param('issss', $this->usuario,
           $descripcion,
           $fecha_ini,
           $fecha_fin,
           $color);
        $insercion->execute();
        $afectadas = $this->sql_con->affected_rows;
        if($afectadas > 0){
            $this->datos['resultado'] = 1;
        }else{
            $this->datos['resultado'] = 0;
        }
        $insercion->close();
    }
    
    protected function editar_evento($evento, $fecha_ini, $fecha_fin, $descripcion, $tipos){
        $color = $this->obtener_color($tipos);
        $editar = "UPDATE evento SET evento_fecha_inicio = '$fecha_ini', evento_fecha_fin = '$fecha_fin', evento_descripcion = '$descripcion', evento_color = '$color' WHERE usuario_evento_id = $evento";
        $rs = $this->sql_con->query($editar);
        if($rs === false){
            trigger_error("Ha ocurrido un error");
        }else{
            $afectadas = $this->sql_con->affected_rows;
            if($afectadas > 0){
                $this->datos['resultado'] = 1; //Evento editado correctamente
            }else{
                $this->datos['resultado'] = 0; //Evento no editado
            }
        }
    }
    
    protected function obtener_eventos2(){
        $consulta = "SELECT usuario_evento_id as id, evento_descripcion as descripcion, evento_fecha_inicio as fecha_ini, evento_fecha_fin as fecha_fin, evento_color as color FROM evento WHERE usuario_id = $this->usuario";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            trigger_error("Ha ocurrido un error");
        }else{
            $this->datos['datos'] = array();
            while($row = $rs->fetch_assoc()){
                $dato = array();
                foreach($row as $indice=>$valor){
                    $dato[$indice] = $valor;
                }
                array_push($this->datos['datos'], $dato);
            }
        }
    }
    
    protected function obtener_eventos($evento){
        $consulta = "SELECT evento_descripcion as descripcion, evento_fecha_inicio as fecha_ini, evento_fecha_fin as fecha_fin, evento_color as color FROM evento WHERE usuario_id = $this->usuario AND usuario_evento_id = $evento";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            trigger_error("Ha ocurrido un error");
        }else{
            while($row = $rs->fetch_assoc()){
                $dato = array();
                foreach($row as $indice=>$valor){
                    $dato[$indice] = $valor;
                }
                array_push($this->datos, $dato);
            }
        }
    }
    
    protected function eliminar_evento($evento){
        $datos = $this->obtener_dato_evento('usuario_id', 'usuario_evento_id', $evento, 1);
        $usuario = $datos[0]['usuario_id'];
        if($usuario == $this->usuario){
            $eliminar = $this->sql_con->prepare("DELETE FROM evento WHERE usuario_evento_id = ?");
            $eliminar->bind_param('i', $evento);
            $eliminar->execute();
            $afectadas = $this->sql_con->affected_rows;
            if($afectadas > 0){
                $this->datos['resultado'] = 1; //Evento se eliminó correctamente
            }else{
                $this->datos['resultado'] = 0; //No se ha eliminado evento
            }
            $eliminar->close();
        }else{
            $this->datos['resultado'] = 2; //Usuario no corresponde
        }
    }
    
    protected function obtener_dato_evento($dato_buscado, $donde_campo, $donde_valor, $limite){
        $consulta = "SELECT $dato_buscado FROM evento WHERE $donde_campo = $donde_valor";
        if($limite > 0){
            $consulta .= " LIMIT $limite";
        }
        $result = $this->sql_con->query($consulta);
        $datos = array();
        if($result === false){
            trigger_error("Ha ocurrido un error");
        }else{
            while($row = $result->fetch_assoc()){
                foreach($row as $indice=>$fila){
                    $dato[$indice] = $fila;
                }
                array_push($datos, $dato);
            }
        }
        return $datos;
    }
    
    public function __destruct(){
        echo json_encode($this->datos);
    }

}

?>