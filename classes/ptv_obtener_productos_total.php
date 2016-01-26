<?php
    $producto = new Productos();
    
    class Productos{
        
        protected $categoria;
        protected $link, $sql_con;
        protected $datos = array();
        protected $sucursal;
        
        public function __construct(){
            session_start();
            $this->sucursal = $_SESSION["sucursal"];
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
            $consulta = "SELECT categoria_producto.producto_id as pid FROM categoria_producto JOIN producto ON producto.producto_id=categoria_producto.producto_id
                         JOIN producto_sucursal ON producto_sucursal.producto_id=producto.producto_id 
                         WHERE producto_sucursal.sucursal_id=$this->sucursal GROUP BY pid";
            $this->sql_con->set_charset("utf8");
            $rs1 = $this->sql_con->query($consulta);
            if($rs1 === false) {
                trigger_error('Ha ocurrido un error');
            }
           while ($row = mysqli_fetch_array($rs1, MYSQLI_ASSOC)) {
                $p_id = $row['pid'];
                $consulta_productos = "SELECT producto_id as p_id, producto_codigo as p_cod, producto_nombre as p_nombre, marca_id as m_id, producto_modelo as p_modelo, producto_descripcion as p_desc, producto_imagen as p_img FROM producto WHERE producto_id = $p_id";
                $this->sql_con->set_charset("utf8");
                $rs2 = $this->sql_con->query($consulta_productos);
                if($rs2 === false) {
                    trigger_error('Ha ocurrido un error');
                } else {
                    while ($row_productos = mysqli_fetch_array($rs2, MYSQLI_ASSOC)) {
                        $dato['id'] = $row_productos['p_id'];
                        $dato['codigo'] = $row_productos['p_cod'];
                        $dato['nombre'] = $row_productos['p_nombre'];
                        $dato['modelo'] = $row_productos['p_modelo'];
                        $dato['desc'] = $row_productos['p_desc'];
                        $dato['img'] = $row_productos['p_img'];
                        $marca_id = $row_productos['m_id'];
                        $consulta_marca = "SELECT producto_marca_descripcion as marca FROM producto_marca WHERE producto_marca_id = $marca_id";
                        $this->sql_con->set_charset("utf8");
                        $rs3 = $this->sql_con->query($consulta_marca);
                        if($rs3 === false) {
                            //trigger_error('Ha ocurrido un error');
                        }
                        else{
                            while ($row_marca = mysqli_fetch_array($rs3, MYSQLI_ASSOC)) {
                                $dato['marca'] = $row_marca['marca'];
                            }
                        }
                        $producto_id = $row_productos['p_id'];
                        $consulta_producto_sucursal = "SELECT producto_sucursal_precio_unitario as precio_u, producto_sucursal_precio_mayorista as precio_m, producto_sucursal_stock_real as stock_r, producto_sucursal_stock_minimo as stock_m FROM producto_sucursal WHERE producto_id = $producto_id AND producto_sucursal.sucursal_id=$this->sucursal";
                        $this->sql_con->set_charset("utf8");
                        $rs4 = $this->sql_con->query($consulta_producto_sucursal);
                        if($rs4 === false) {
                            //trigger_error('Ha ocurrido un error');
                        }
                        else{
                            while ($row_producto_sucursal = mysqli_fetch_array($rs4, MYSQLI_ASSOC)) {
                                $dato['precio_u'] = $row_producto_sucursal['precio_u'];
                                $dato['precio_m'] = $row_producto_sucursal['precio_m'];
                                $dato['stock_r'] = $row_producto_sucursal['stock_r'];
                                $dato['stock_m'] = $row_producto_sucursal['stock_m'];
                            }
                        }
                        $consulta_promocion_producto ="SELECT promocion_producto.promocion_id as promo_id, promocion.promocion_descripcion as promo_descripcion, promocion.promocion_oferta_tipo as promo_tipo, promocion.promocion_descuento as promo_descuento, promocion.promocion_precio as promo_precio , promocion.promocion_cantidad as promo_cantidad,tipo_oferta_promocion.tipo_oferta_promocion_nombre as promo_tipo_nombre FROM `promocion_producto` INNER JOIN promocion ON promocion.promocion_id=promocion_producto.promocion_id INNER JOIN tipo_oferta_promocion ON tipo_oferta_promocion.tipo_oferta_promocion_id=promocion.promocion_oferta_tipo WHERE promocion_producto.producto_id=$producto_id AND promocion.promocion_fecha_inicio<=NOW() AND promocion.promocion_fecha_termino>=NOW() AND promocion.promocion_estado=1";
                        
                        $this->sql_con->set_charset("utf8");
                        $rs5 = $this->sql_con->query($consulta_promocion_producto);
                        if($rs5 === false) {
                            //trigger_error('Ha ocurrido un error');
                        }
                        else{
                            $dato["promociones"] = array();
                            while ($row_promo_producto = mysqli_fetch_array($rs5, MYSQLI_ASSOC)) {
                                $promocion['promo_id'] = $row_promo_producto['promo_id'];
                                $promocion['promo_titulo'] = $row_promo_producto['promo_descripcion'];
                                $promocion['promo_tipo'] = $row_promo_producto['promo_tipo'];
                                $promocion['promo_descuento'] = $row_promo_producto['promo_descuento'];
                                $promocion['promo_precio'] = $row_promo_producto['promo_precio'];
                                $promocion['promo_cantidad'] = $row_promo_producto['promo_cantidad'];
                                $promocion['promo_tipo_nombre'] = $row_promo_producto['promo_tipo_nombre'];
                                if ($promocion['promo_tipo']==2){
                                    $promocion['promo_descontar'] = $dato['precio_u']*$promocion['promo_descuento']/100;
                                    $promocion['promo_descripcion'] = $promocion['promo_descuento']."% dcto. en ".$dato['nombre'];
                                }
                                else if ($promocion['promo_tipo']==1){
                                    $promocion['promo_descontar'] = ($dato['precio_u']*$promocion['promo_cantidad'])-$promocion['promo_precio'];
                                    $promocion['promo_descripcion'] = "Lleve ".$promocion['promo_cantidad']." pague $".$promocion['promo_precio']." en ".$dato['nombre'];
                                }
                                array_push($dato["promociones"], $promocion);
                                
                            }
                            
                            
                        }
                        array_push($this->datos, $dato);
                    }
                }
            }
            
        }
        
        public function __destruct(){
            echo json_encode($this->datos);
        }
    }
?>