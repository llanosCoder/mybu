<?php
    $cat_desc = $_POST['cId'];
    $producto = new Productos($cat_desc);
    
    class Productos{
        
        protected $categoria;
        protected $link, $sql_con;
        protected $datos = array();
        protected $tipo_cuenta, $sucursal;
        
        public function __construct($cat_desc){
            session_start();
            $this->categoria = $cat_desc;
            require('../hosts.php');
            require('conexion_new.php');
            $hosteo = new Host();
            $hosteo->obtener_conexion(0);
            $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'],$this->link['pass'],$this->link['bd']);
            $this->obtener_productos();
        }
        
        protected function set_conexion($host, $user, $pass, $bd){
            $conexion = new Conexion();
            $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
            $this->sql_con->set_charset('utf8');
        }
        
        protected function obtener_productos(){
            $this->tipo_cuenta = $_SESSION['tipo_cuenta'];
            $this->sucursal = $_SESSION['sucursal'];
            if($this->categoria == 'all')
                $consulta = "SELECT DISTINCT producto_id as pid FROM categoria_producto";
            else
                $consulta = "SELECT producto_id as pid FROM categoria_producto WHERE categoria_id = (SELECT categoria_id as cid FROM categoria WHERE categoria_descripcion = '$this->categoria' LIMIT 1)";
            $this->sql_con->set_charset("utf8");
            $rs = $this->sql_con->query($consulta);
            if($rs === false) {
                trigger_error('Ha ocurrido un error');
            } else {
                while($row = $rs->fetch_assoc()){
                    $p_id = $row['pid'];
                    $consulta_productos = "SELECT p.producto_id as p_id, p.producto_codigo as codigo, p.producto_nombre as p_nombre, p.marca_id as m_id, p.producto_modelo as p_modelo, p.producto_descripcion as p_desc, p.producto_imagen as p_img, t.producto_talla as p_talla, pe.producto_peso as p_peso, pe.producto_peso_unidad_medida as p_peso_unidad_medida, d.producto_alto as p_alto, d.producto_alto_unidad_medida as p_alto_unidad_medida, d.producto_ancho as p_ancho, d.producto_ancho_unidad_medida as p_ancho_unidad_medida, d.producto_largo as p_largo, d.producto_largo_unidad_medida as p_largo_unidad_medida, v.producto_volumen as p_volumen, v.producto_volumen_unidad_medida as p_volumen_unidad_medida FROM producto p LEFT JOIN producto_talla t ON p.producto_talla = t.producto_talla_id LEFT JOIN producto_peso pe ON pe.producto_peso_id = p.producto_peso LEFT JOIN producto_dimension d ON p.producto_dimension = d.producto_dimension_id LEFT JOIN producto_volumen v ON v.producto_volumen_id = p.producto_volumen WHERE producto_id = $p_id ORDER BY p_nombre DESC";
                     $this->sql_con->set_charset("utf8");
                    $rs2 = $this->sql_con->query($consulta_productos);
                    if($rs2 === false) {
                        trigger_error('Ha ocurrido un error');
                    } else {
                        $dato = array();
                        while($row_productos = $rs2->fetch_assoc()){
                            $dato['codigo'] = $row_productos['codigo'];
                            $dato['tipo_cuenta'] = $this->tipo_cuenta;
                            $dato['sucursal'] = $this->sucursal;
                            $dato['nombre'] = $row_productos['p_nombre'];
                            $dato['modelo'] = $row_productos['p_modelo'];
                            $dato['desc'] = $row_productos['p_desc'];
                            $dato['img'] = $row_productos['p_img'];
                            if($row_productos['p_talla'] != null){
                                $dato['talla'] = $row_productos['p_talla'];
                            }else{
                                $dato['talla'] = null;
                            }
                            if($row_productos['p_peso'] != null){
                                $dato['peso'] = $row_productos['p_peso'] . ' ' . $row_productos['p_peso_unidad_medida'];
                            }else{
                                $dato['peso'] = null;
                            }
                            if($row_productos['p_alto'] != null && $row_productos['p_ancho'] != null && $row_productos['p_largo'] != null){
                                $dato['dimensiones'] = $row_productos['p_alto'] . '' . $row_productos['p_alto_unidad_medida'] . ' x ' . $row_productos['p_ancho'] . '' . $row_productos['p_ancho_unidad_medida'] . ' x ' . $row_productos['p_largo'] . '' . $row_productos['p_largo_unidad_medida'];
                            }else{
                                $dato['dimensiones'] = null;
                            }
                            if($row_productos['p_volumen'] != null){
                                $dato['volumen'] = $row_productos['p_volumen'] . ' ' . $row_productos['p_volumen_unidad_medida'];
                            }else{
                                $dato['volumen'] = null;
                            }
                            $marca_id = $row_productos['m_id'];
                            $consulta_marca = "SELECT producto_marca_nombre as marca FROM producto_marca WHERE producto_marca_id = $marca_id";
                            $this->sql_con->set_charset("utf8");
                            $rs3 = $this->sql_con->query($consulta_marca);
                            if($rs3 === false) {
                                trigger_error('Ha ocurrido un error');
                            } else {
                                while($row_marca = $rs3->fetch_assoc()){
                                    $dato['marca'] = $row_marca['marca'];
                                }

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