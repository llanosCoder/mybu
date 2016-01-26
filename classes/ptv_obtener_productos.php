<?php

    if(isset($_POST["categoria"])){
        $categoria=$_POST["categoria"];
    }
    else if(isset($_GET["categoria"])){
        $categoria=$_GET["categoria"];
    }
    else{
        exit();
    }
    $producto = new Productos($categoria);
    
    class Productos{
        
        protected $categoria;
        protected $link, $sql_con;
        protected $datos = array();
        
        public function __construct($cat_desc){
            session_start();
            $this->sucursal = $_SESSION["sucursal"];
            $this->categoria = $cat_desc;
            include('conexion.php');
            $this->link = Conectarse();
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'],$this->link['pass'],$this->link['bd']);
            $this->obtener_productos();
        }
        
        protected function obtener_productos(){
            $consulta = "SELECT categoria_producto.producto_id as pid FROM categoria_producto JOIN producto ON producto.producto_id=categoria_producto.producto_id
                         JOIN producto_sucursal ON producto_sucursal.producto_id=producto.producto_id 
                         WHERE producto_sucursal.sucursal_id=$this->sucursal AND categoria_id = '$this->categoria'";
            
            $this->sql_con->set_charset("utf8");
            $rs1 = $this->sql_con->query($consulta);
            if($rs1 === false) {
                trigger_error('Ha ocurrido un error');
            }
           while ($row = mysqli_fetch_array($rs1, MYSQLI_ASSOC)) {
                $p_id = $row['pid'];
                $consulta_productos = "SELECT producto_id as p_id, producto_codigo as p_codigo,producto_nombre as p_nombre, marca_id as m_id, producto_modelo as p_modelo, producto_descripcion as p_desc, producto_imagen as p_img FROM producto WHERE producto_id = $p_id";
                $this->sql_con->set_charset("utf8");
                $rs2 = $this->sql_con->query($consulta_productos);
                if($rs2 === false) {
                    trigger_error('Ha ocurrido un error');
                } else {
                    while ($row_productos = mysqli_fetch_array($rs2, MYSQLI_ASSOC)) {
                        $dato['codigo'] = $row_productos['p_codigo'];
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
                        $consulta_producto_sucursal = "SELECT producto_sucursal_precio_unitario as precio_u, producto_sucursal_precio_mayorista as precio_m, producto_sucursal_stock_real as stock_r, producto_sucursal_stock_minimo as stock_m FROM producto_sucursal WHERE producto_id = $producto_id";
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