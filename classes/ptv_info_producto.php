<?php
    if(isset($_POST["codigo"])){
        $codigo=$_POST["codigo"];
    }
    else{
        exit();
    }
    $producto = new Producto($codigo);
    
    class Producto{
        
        protected $codigo;
        protected $link, $sql_con;
        protected $datos = array();
        protected $sucursal;
        public function __construct($codigo){
            session_start();
            $this->sucursal = $_SESSION["sucursal"];
            $this->codigo = $codigo;
            include('conexion.php');
            $this->link = Conectarse();
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'],$this->link['pass'],$this->link['bd']);
            $this->obtener_producto();
        }
        
        protected function obtener_producto(){
            $p_id = $this->codigo;
            $consulta_productos = "SELECT producto_id as p_id, producto_nombre as p_nombre, marca_id as m_id, producto_modelo as p_modelo, producto_descripcion as p_desc, producto_imagen as p_img FROM producto WHERE producto_codigo = '$p_id'";
            $this->sql_con->set_charset("utf8");
            $rs2 = $this->sql_con->query($consulta_productos);
            if($rs2 === false) {
                trigger_error('Ha ocurrido un error');
            } else {
                $row_productos = $rs2->fetch_assoc();
                $dato = array();
                $dato['nombre'] = $row_productos['p_nombre'];
                $dato['modelo'] = $row_productos['p_modelo'];
                $dato['desc'] = $row_productos['p_desc'];
                $dato['img'] = $row_productos['p_img'];
                $marca_id = $row_productos['m_id'];
                $consulta_marca = "SELECT producto_marca_nombre as marca FROM producto_marca WHERE producto_marca_id = $marca_id";
                $this->sql_con->set_charset("utf8");
                $rs3 = $this->sql_con->query($consulta_marca);
                if (!$rs3) {
                    printf("Error: %s\n", mysqli_error($this->sql_con));
                    exit();
                }
                while ($row_marca = mysqli_fetch_array($rs3, MYSQLI_ASSOC)) {
                    $dato['marca'] = $row_marca['marca'];
                }
                
                $producto_id = $row_productos['p_id'];
                $consulta_producto_empresa = "SELECT producto_sucursal_precio_unitario as precio_u, producto_sucursal_precio_mayorista as precio_m, producto_sucursal_stock_real as stock_r, producto_sucursal_stock_minimo as stock_m FROM producto_sucursal WHERE producto_id = $producto_id AND sucursal_id=$this->sucursal";
                $this->sql_con->set_charset("utf8");
                $rs4 = $this->sql_con->query($consulta_producto_empresa);
                if (!$rs4) {
                    printf("Error: %s\n", mysqli_error($this->sql_con));
                    exit();
                }                
                while ($row_producto_empresa = mysqli_fetch_array($rs4, MYSQLI_ASSOC)) {
                    $dato['marca'] = $row_marca['marca'];
                    $dato['precio_u'] = $row_producto_empresa['precio_u'];
                    $dato['precio_m'] = $row_producto_empresa['precio_m'];
                    $dato['stock_r'] = $row_producto_empresa['stock_r'];
                    $dato['stock_m'] = $row_producto_empresa['stock_m'];
                }
                array_push($this->datos, $dato);
            }
        }
        
        public function __destruct(){
            echo json_encode($this->datos);
        }
    }
?>