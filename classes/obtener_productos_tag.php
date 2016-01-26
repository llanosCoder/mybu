<?php

$tag = new Tags();

class Tags{

    protected $datos = array();
    protected $link, $sql_con, $codigo, $action;

    public function __construct(){
        session_start();
        require('../hosts.php');
        require('conexion_new.php');
        $hosteo = new Host();
        $hosteo->obtener_conexion(0);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $this->obtener_productos_tag();
    }
    
    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    
    protected function obtener_productos_tag(){
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'],$this->link['pass'],$this->link['bd']);
        $this->codigo = mysqli_real_escape_string($this->sql_con, $_POST['tCodigo']);
        @$this->action = mysqli_real_escape_string($this->sql_con, $_POST['action']);
        $consulta_producto_tag = "SELECT p.producto_codigo as codigo, p.producto_nombre as nombre, m.producto_marca_nombre as marca, p.producto_modelo as modelo, p.producto_descripcion as descripcion FROM producto p LEFT JOIN producto_marca m ON m.producto_marca_id = p.marca_id INNER JOIN tag_producto t ON p.producto_id = t.producto_id ";
        if($this->codigo != '0'){
            $consulta_producto_tag = $consulta_producto_tag . "WHERE t.tag_id = (SELECT tag_id FROM tag WHERE tag_codigo = '$this->codigo' LIMIT 1);";
        }else{
            $consulta_producto_tag = $consulta_producto_tag . "GROUP BY p.producto_codigo";
        }
        $this->sql_con->set_charset("utf8");
        $result_producto_tag = $this->sql_con->query($consulta_producto_tag);
        if($result_producto_tag === false) {
            trigger_error('Ha ocurrido un error');
        } else {
            while($row_producto_tag = $result_producto_tag->fetch_assoc()){
                $dato = array();
                $dato['codigo'] = $row_producto_tag['codigo'];
                $dato['nombre'] = $row_producto_tag['nombre'];
                $dato['marca'] = $row_producto_tag['marca'];
                $dato['modelo'] = $row_producto_tag['modelo'];
                $dato['descripcion'] = $row_producto_tag['descripcion'];
                switch($this->action){
                    case 1:
                        $dato['action'] = $this->obtener_1($row_producto_tag['codigo']);
                        break;
                    case 2:
                        //$dato['action'] = $this->obtener_2($row_producto_tag['codigo']);
                        $dato['action'] = $this->obtener_1($row_producto_tag['codigo']);
                        break;
                    case 3:
                        break;
                    default:
                        $dato['action'] = 0;
                        break;
                }
                array_push($this->datos, $dato);
            }
            $result_producto_tag->close();
        }
    }
    
    protected function obtener_1($codigo){
        $consulta_ventas = "SELECT count(venta_id) as action FROM venta_producto WHERE producto_id = (SELECT producto_id FROM producto WHERE producto_codigo = '$codigo')";
        $this->sql_con->set_charset("utf8");
        $result_consulta_ventas = $this->sql_con->query($consulta_ventas);
        if($result_consulta_ventas === false) {
            trigger_error('Ha ocurrido un error');
        } else {
            return $result_consulta_ventas->fetch_assoc();
        }
    }
    
    protected function obtener_2($codigo){
            $consulta_ventas = "SELECT count(producto_id) as action FROM tag_producto tp INNER JOIN tag t ON tp.tag_id = t.tag_id WHERE t.tag_id = (SELECT tag_id FROM tag WHERE tag_codigo='$this->codigo') AND tp.producto_id = (SELECT producto_id FROM producto WHERE producto_codigo = '$codigo')";
        $this->sql_con->set_charset("utf8");
        $result_consulta_ventas = $this->sql_con->query($consulta_ventas);
        if($result_consulta_ventas === false) {
            trigger_error('Ha ocurrido un error');
        } else {
            return $result_consulta_ventas->fetch_assoc();
        }
    }
    
    public function __destruct(){
        echo json_encode($this->datos);
    }

}