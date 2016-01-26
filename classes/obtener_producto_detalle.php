<?php
    $cat_desc = $_POST['cId'];
    $codigo = $_POST['codigo'];

    $producto = new Productos($cat_desc, $codigo);
    
    class Productos{
        
        protected $categoria;
        protected $codigo;
        protected $link, $sql_con;
        protected $datos = array();
        protected $tipo_cuenta, $sucursal = 1;
        
        public function __construct($cat_desc, $codigo){
            session_start();
            $this->categoria = $cat_desc;
            $this->codigo = $codigo;
            require('../hosts.php');
            require('conexion_new.php');
            $hosteo = new Host();
            $hosteo->obtener_conexion(0);
            $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'],$this->link['pass'],$this->link['bd']);
            $this->tipo_cuenta = $_SESSION['tipo_cuenta'];
            $this->sucursal = $_SESSION['sucursal'];
            $this->obtener_detalle();
        }
        
        protected function set_conexion($host, $user, $pass, $bd){
            $conexion = new Conexion();
            $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
            $this->sql_con->set_charset('utf8');
        }
        
        protected function obtener_detalle(){
            $consulta = "SELECT p.producto_id as p_id, p.producto_codigo as codigo, p.producto_nombre as p_nombre, p.marca_id as m_id, p.producto_modelo as p_modelo, p.producto_descripcion as p_desc, p.producto_imagen as p_img, t.producto_talla as p_talla, pe.producto_peso as p_peso, pe.producto_peso_unidad_medida as p_peso_unidad_medida, d.producto_alto as p_alto, d.producto_alto_unidad_medida as p_alto_unidad_medida, d.producto_ancho as p_ancho, d.producto_ancho_unidad_medida as p_ancho_unidad_medida, d.producto_largo as p_largo, d.producto_largo_unidad_medida as p_largo_unidad_medida, v.producto_volumen as p_volumen, v.producto_volumen_unidad_medida as p_volumen_unidad_medida, count(ppes.producto_pesable_id) as pesable FROM producto p LEFT JOIN producto_talla t ON p.producto_talla = t.producto_talla_id LEFT JOIN producto_peso pe ON pe.producto_peso_id = p.producto_peso LEFT JOIN producto_dimension d ON p.producto_dimension = d.producto_dimension_id LEFT JOIN producto_volumen v ON v.producto_volumen_id = p.producto_volumen LEFT JOIN producto_pesable ppes ON p.producto_id = ppes.producto_id WHERE producto_codigo = '$this->codigo' ORDER BY p_nombre DESC LIMIT 1";
            $this->sql_con->set_charset("utf8");
            $rs = $this->sql_con->query($consulta);
            if($rs === false) {
                trigger_error('Ha ocurrido un error');
            } else {
                $dato = array();
                while($row_productos = $rs->fetch_assoc()){
                    $dato['codigo'] = $row_productos['codigo'];
                    $dato['tipo_cuenta'] = $this->tipo_cuenta;
                    $dato['sucursal'] = $this->sucursal;
                    $dato['nombre'] = $row_productos['p_nombre'];
                    $dato['modelo'] = $row_productos['p_modelo'];
                    $dato['desc'] = $row_productos['p_desc'];
                    $dato['img'] = $row_productos['p_img'];
                    $dato['pesable'] = $row_productos['pesable'];
                    $dato['talla'] = $row_productos['p_talla'];

                    $dato['peso'] = $row_productos['p_peso'];
                    $dato['peso_unidad_medida'] = $row_productos['p_peso_unidad_medida'];

                    $dato['alto'] = $row_productos['p_alto'];
                    $dato['alto_unidad_medida'] = $row_productos['p_alto_unidad_medida'];
                    $dato['ancho'] = $row_productos['p_ancho'];
                    $dato['ancho_unidad_medida'] = $row_productos['p_ancho_unidad_medida'];
                    $dato['largo'] = $row_productos['p_largo'];
                    $dato['largo_unidad_medida'] = $row_productos['p_largo_unidad_medida'];
                    
                    $dato['volumen'] = $row_productos['p_volumen'];
                    $dato['volumen_unidad_medida'] = $row_productos['p_volumen_unidad_medida'];
                    
                    $marca_id = $row_productos['m_id'];
                    $dato['marca_id'] = $marca_id;
                    $consulta_marca = "SELECT producto_marca_nombre as marca FROM producto_marca WHERE producto_marca_id = $marca_id";
                    $this->sql_con->set_charset("utf8");
                    $rs2 = $this->sql_con->query($consulta_marca);
                    if($rs === false) {
                        trigger_error('Ha ocurrido un error');
                    } else {
                        while($row_marca = $rs2->fetch_assoc()){
                            $dato['marca'] = $row_marca['marca'];
                        }
                        $producto_id = $row_productos['p_id'];
                        $consulta_producto_sucursal = "SELECT producto_sucursal_precio_unitario as precio_u, producto_sucursal_precio_mayorista as precio_m, producto_sucursal_stock_real as stock_r, producto_sucursal_stock_minimo as stock_m FROM producto_sucursal WHERE producto_id = $producto_id";
                        $this->sql_con->set_charset("utf8");
                        $rs3 = $this->sql_con->query($consulta_producto_sucursal);
                        if($rs3 === false) {
                            trigger_error('Ha ocurrido un error');
                        } else {
                            while($row_producto_sucursal = $rs3->fetch_assoc()){
                                $dato['precio_u'] = $row_producto_sucursal['precio_u'];
                                $dato['precio_m'] = $row_producto_sucursal['precio_m'];
                                $dato['stock_r'] = $row_producto_sucursal['stock_r'];
                                $dato['stock_m'] = $row_producto_sucursal['stock_m'];
                            }
                            array_push($this->datos, $dato);
                        }
                        $rs3->close();
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