<?php
    $cat_desc = $_POST['cId'];
    $producto = new Productos($cat_desc);
    
    class Productos{
        
        protected $categoria;
        protected $link, $sql_con;
        protected $datos = array();
        //USAR SESIONES
        protected $tipo_cuenta = 1;
        protected $usuario = 1;
        //
        
        public function __construct($cat_desc){
            $this->categoria = $cat_desc;
            session_start();
            require('../hosts.php');
            require('conexion_new.php');
            $hosteo = new Host();
            $hosteo->obtener_conexion(0);
            $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
            $this->obtener_productos();
        }
        
        protected function set_conexion($host, $user, $pass, $bd){
            $conexion = new Conexion();
            $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
            $this->sql_con->set_charset('utf8');
        }
        
        protected function obtener_productos(){
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'],$this->link['pass'],$this->link['bd']);
            $consulta = "SELECT sucursal_id as sucursal FROM usuario_sucursal WHERE usuario_id = $this->usuario";
            $this->sql_con->set_charset("utf8");
            $rs = $this->sql_con->query($consulta);
            if($rs === false) {
                trigger_error('Ha ocurrido un error');
            } else {
                while($row = $rs->fetch_assoc()){
                    $sucursal = $row['sucursal'];
                    $consulta_productos = "SELECT p.producto_codigo as codigo, p.producto_nombre as nombre, p.producto_descripcion as descripcion, p.marca_id as marca, ps.producto_sucursal_precio_unitario as precio_u, ps.producto_sucursal_precio_mayorista as precio_m, ps.producto_sucursal_stock_real as stock_r, ps.producto_sucursal_stock_minimo as stock_m, ps.producto_sucursal_costo as costo FROM producto p INNER JOIN producto_sucursal ps ON p.producto_id = ps.producto_id INNER JOIN categoria_producto cp ON p.producto_id = cp.producto_id WHERE cp.categoria_id = (SELECT categoria_id as cid FROM categoria c WHERE c.categoria_descripcion = '$this->categoria' LIMIT 1) AND ps.sucursal_id = $sucursal";
                    $this->sql_con->set_charset("utf8");
                    $rs2 = $this->sql_con->query($consulta_productos);
                    if($rs2 === false) {
                        trigger_error('Ha ocurrido un error');
                    } else {
                        $dato = array();
                        while($row_productos = $rs2->fetch_assoc()){
                            $dato['codigo'] = $row_productos['codigo'];
                            $dato['sucursal'] = $sucursal;
                            $dato['nombre'] = $row_productos['nombre'];
                            $dato['descripcion'] = $row_productos['descripcion'];
                            $dato['precio_u'] = $row_productos['precio_u'];
                            $dato['precio_m'] = $row_productos['precio_m'];
                            $dato['stock_r'] = $row_productos['stock_r'];
                            $dato['stock_m'] = $row_productos['stock_m'];
                            $marca_id = $row_productos['marca'];
                            $consulta_marca = "SELECT producto_marca_nombre as marca FROM producto_marca WHERE producto_marca_id = $marca_id";
                            $this->sql_con->set_charset("utf8");
                            $rs3 = $this->sql_con->query($consulta_marca);
                            if($rs3 === false) {
                                trigger_error('Ha ocurrido un error');
                            } else {
                                while($row_marca = $rs3->fetch_assoc()){
                                    $dato['marca'] = $row_marca['marca'];
                                }
                                $dato['tipo_cuenta'] = $this->tipo_cuenta;
                                array_push($this->datos, $dato);
                            }
                            $rs3->close();
                        }
                    }
                    $rs2->close();
                }
            }
            $rs->close();
        }
        
        public function __destruct(){
            echo json_encode($this->datos);
        }
    }
?>