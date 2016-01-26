<?php

$clase = new Clase();

Class Clase{

    protected $link, $sql_con;
    protected $datos = array();
    
    protected function obtener_planes_pagos(){
        $consulta = "SELECT plan_pago_nombre as nombre, plan_pago_codigo as codigo, plan_pago_cuota as cuotas, plan_pago_interes as interes, plan_pago_habilitado AS habilitado FROM plan_pago";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            $this->datos['resultado'] = 0; //Hubo un error en la consulta
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
    
    protected function crear_plan_pago($nombre, $cuotas, $interes, $es_habilitado){
        $codigo = md5(date('Y-m-d H:i:s') . $nombre . $es_habilitado);
        $insercion = $this->sql_con->prepare("INSERT INTO plan_pago (plan_pago_nombre, plan_pago_codigo, plan_pago_cuota, plan_pago_interes, plan_pago_habilitado) VALUES (?, ?, ?, ?, ?)");
        $insercion->bind_param('ssiii',
                              $nombre,
                              $codigo,
                              $cuotas,
                              $interes,
                              $es_habilitado);
        $insercion->execute();
        $insertadas = $this->sql_con->affected_rows;
        $insercion->close();
        if($insertadas > 0){
            $this->datos['resultado'] = 1; //Plan creado exitosamente
        }else{
            $this->datos['resultado'] = 0; //Plan no se creo
        }
    }
    
    protected function editar_plan_pago($nombre, $codigo, $cuotas, $interes){
        $edicion = $this->sql_con->prepare("UPDATE plan_pago SET plan_pago_nombre = ?, plan_pago_cuota = ?, plan_pago_interes = ? WHERE plan_pago_codigo = ?");
        $edicion->bind_param('siis',
                            $nombre,
                            $cuotas,
                            $interes,
                            $codigo);
        $edicion->execute();
        $insertadas = $this->sql_con->affected_rows;
        $edicion->close();
        if($insertadas > 0){
            $this->datos['resultado'] = 1; //Registro editado exitosamente
        }else{
            $this->datos['resultado'] = 0; //No se editó registro
        }
    }
    
    protected function eliminar_plan_pago($codigo){
        $eliminacion = $this->sql_con->prepare("DELETE FROM plan_pago WHERE plan_pago_codigo = ?");
        $eliminacion->bind_param('s', $codigo);
        $eliminacion->execute();
        $insertadas = $this->sql_con->affected_rows;
        $eliminacion->close();
        if($insertadas > 0){
            $this->datos['resultado'] = 1; //Plan eliminado exitosamente
        }else{
            $this->datos['resultado'] = 0; //Plan no se elimino
        }
    }
    
    protected function procesar(){
        $accion = mysqli_real_escape_string($this->sql_con, $_POST['accion']);
        switch ($accion) {
            case 1:
                $this->obtener_planes_pagos();
                break;
            case 2:
                $nombre = mysqli_real_escape_string($this->sql_con, $_POST['nombre']);
                $cuotas = mysqli_real_escape_string($this->sql_con, $_POST['cuotas']);
                $interes = mysqli_real_escape_string($this->sql_con, $_POST['interes']);
                $habilitados = mysqli_real_escape_string($this->sql_con, $_POST['habilitados']);
                
                $this->crear_plan_pago($nombre, $cuotas, $interes, 0);
                if($habilitados == 1){
                    $this->crear_plan_pago($nombre, $cuotas, $interes, 1);
                }
                break;
            case 3:
                $nombre = mysqli_real_escape_string($this->sql_con, $_POST['nombre']);
                $codigo = mysqli_real_escape_string($this->sql_con, $_POST['codigo']);
                $cuotas = mysqli_real_escape_string($this->sql_con, $_POST['cuotas']);
                $interes = mysqli_real_escape_string($this->sql_con, $_POST['interes']);
                $this->editar_plan_pago($nombre, $codigo, $cuotas, $interes);
                break;
            case 4:
                $codigo = mysqli_real_escape_string($this->sql_con, $_POST['codigo']);
                $this->eliminar_plan_pago($codigo);
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