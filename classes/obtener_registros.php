<?php

$registro = new Registros();

class Registros{

    protected $link, $sql_con;
    protected $accion, $datos = array();

    public function __construct(){
        session_start();
        require('../hosts.php');
        require('conexion_new.php');
        $hosteo = new Host();
        $hosteo->obtener_conexion(0);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $this->procesar();
    }
    
    protected function procesar(){
        $this->accion = mysqli_real_escape_string($this->sql_con, $_POST['accion']);
        switch($this->accion){
            case 1:
                $consulta = "SELECT p.producto_codigo as codigo, p.producto_nombre as nombre, s.sucursal_direccion as sucursal, rs.producto_stock_agregado as cantidad, CONCAT(u.usuario_nombres, ' ', u.usuario_apellidos) as usuario, rs.fecha_stock_agregado as fecha FROM registro_stock rs JOIN producto p ON rs.producto_id = p.producto_id JOIN usuario u ON rs.usuario_id = u.usuario_id JOIN sucursal s ON rs.sucursal_id = s.sucursal_id";
                $this->datos['headers'] = ['Código', 'Nombre de producto', 'Sucursal', 'Cantidad agregada', 'Responsable', 'Fecha'];
                $this->obtener_registros($consulta);
                break;
            case 2:
                $consulta = "SELECT mp.materia_prima_codigo as codigo, mp.materia_prima_nombre as nombre, s.sucursal_direccion as sucursal, registro_stock_materia_prima_cantidad_agregada as cantidad, CONCAT(u.usuario_nombres, ' ', u.usuario_apellidos) as usuario, rs.registro_stock_materia_prima_fecha as fecha FROM registro_stock_materia_prima rs JOIN materia_prima mp ON rs.materia_prima_id = mp.materia_prima_id JOIN usuario u ON rs.usuario_id = u.usuario_id JOIN sucursal s ON rs.sucursal_id = s.sucursal_id";
                $this->datos['headers'] = ['Código', 'Nombre de producto', 'Sucursal', 'Cantidad', 'Responsable', 'Fecha'];
                $this->obtener_registros($consulta);
                break;
            case 3:
                $consulta = "SELECT p.producto_codigo as codigo, p.producto_nombre as nombre, s1.sucursal_direccion as sucursal_origen, s2.sucursal_direccion as sucursal_destino, rs.traspaso_stock_registro_cantidad as cantidad, CONCAT(u.usuario_nombres, ' ', u.usuario_apellidos) as usuario, rs.traspaso_stock_registro_fecha as fecha FROM traspaso_stock_registro rs JOIN producto p ON rs.producto_id = p.producto_id JOIN usuario u ON rs.usuario_id = u.usuario_id JOIN sucursal s1 ON rs.sucursal_origen = s1.sucursal_id  JOIN sucursal s2 ON rs.sucursal_destino = s2.sucursal_id";
                $this->datos['headers'] = ['Código', 'Nombre', 'Sucursal origen', 'Sucursal destino', 'Cantidad', 'Responsable', 'Fecha'];
                $this->obtener_registros($consulta);
                break;
        }
    }

    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    
    protected function obtener_registros($consulta){
        $stmt = $this->sql_con->query($consulta);
        $this->datos['datos'] = array();
        while($row = $stmt->fetch_assoc()){
            $dato = array();
            foreach($row as $indice=>$fila){
                $dato[$indice] = $fila;
            }
            array_push($this->datos['datos'], $dato);
        }
    }
    
    public function __destruct(){
        echo json_encode($this->datos);
    }

    }

?>