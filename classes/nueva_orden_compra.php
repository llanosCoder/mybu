<?php

    $orden = new Ordenes();
    
    class Ordenes{
    
        protected $link, $sql_con;
        protected $resultado;
        protected $empresa, $usuario, $f_vencimiento, $total_bruto, $descuentos, $total, $sucursal, $ahora, $id;
        protected $productos = array(), $ofertas = array(), $cantidad = array(), $proveedores;
        protected $esOferta = true;
        protected $host;
        
        public function __construct(){
            session_start();
            require('../hosts.php');
            require('conexion_new.php');
            $hosteo = new Host();
            $hosteo->obtener_conexion(1);
            $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
            $this->obtener_parametros();
            //$this->host = $this->obtener_origen_datos();
            $this->procesar();
        }
        
        protected function set_conexion($host, $user, $pass, $bd){
            $conexion = new Conexion();
            $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
            $this->sql_con->set_charset('utf8');
        }
        
        protected function procesar(){
            $proveedor = '';
            for($i = 0; $i < count($this->parametros); $i++){
                if($this->parametros[$i][5] != $proveedor){
                    $this->host = $this->obtener_origen_datos($this->parametros[$i][5]);    
                }
                $this->obtener_productos($this->parametros[$i][0], $this->parametros[$i][2]);
                $this->total_bruto += $this->parametros[$i][4] * $this->parametros[$i][2];
                $this->descuentos += ($this->parametros[$i][4] - $this->parametros[$i][3]) * $this->parametros[$i][2];
                $this->total += $this->parametros[$i][3] * $this->parametros[$i][2];
                if($i < count($this->parametros)- 1){
                    if($this->parametros[$i][5] != $this->parametros[$i+1][5]){
                        $this->proveedor = $this->parametros[$i][5];
                        $this->agregar_orden();
                    }
                }else{
                    $this->proveedor = $this->parametros[$i][5];
                    $this->agregar_orden();
                }
                $proveedor = $this->parametros[$i][5];
            }
        }
        
        protected function agregar_orden(){
            $hosteo = new Host();
            $hosteo->obtener_conexion(1);
            $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
            $this->ahora = date("Y-m-d H:i:s");
            $voucher = md5($this->ahora . $this->usuario . rand(0, 1000));
            $this->agregar_orden_global($voucher);
            if($this->id > 0){
                $this->agregar_orden_propia($voucher);
                $this->agregar_orden_proveedor($voucher);
                $i = 0;
                foreach($this->productos as $producto){
                    if($this->esOferta)
                        $insercion_relacion = $this->sql_con->prepare("INSERT INTO orden_producto VALUES (null, ?, ? , ?, ?, ?, 0)");
                    else
                        $insercion_relacion = $this->sql_con->prepare("INSERT INTO orden_producto VALUES (null, ?, ? , ?, ?, 0, ?)");
                    $insercion_relacion->bind_param('iiiii',
                    $this->id,
                    $producto['id'],
                    $producto['precio'],
                    $producto['cantidad'],
                    $producto['oferta']);
                    $insercion_relacion->execute();
                    $afected_rows = $this->sql_con->affected_rows;
                    if($afected_rows == -1)
                        $this->resultado = 0;
                    $insercion_relacion->close();
                    $i++;
                }
                $this->total_bruto += 0;
                $this->descuentos += 0;
                $this->total = 0;
                if($i > 0)
                    $this->resultado = 1;
                else
                    $this->resultado = 0;
            }
        }
        
        protected function agregar_orden_global($voucher){
            $hosteo = new Host();
            $hosteo->obtener_conexion(1);
            $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
            $insercion_orden = $this->sql_con->prepare("INSERT INTO orden_compra VALUES(null, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");
            $insercion_orden->bind_param('iiisssiii',
            $this->proveedor,
            $this->empresa,
            $this->usuario,
            $voucher,
            $this->ahora,
            $this->f_vencimiento,
            $this->total_bruto,
            $this->descuentos,
            $this->total);
            $insercion_orden->execute();
            $insercion = $this->sql_con->affected_rows;
            $this->id = mysqli_insert_id($this->sql_con);
            $insercion_orden->close();
        }
        
        protected function agregar_orden_proveedor($voucher){
            $hosteo = new Host();
            $hosteo->obtener_conexion($this->host);
            $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
            $insercion_orden = $this->sql_con->prepare("INSERT INTO orden_compra VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");
            $insercion_orden->bind_param('iiiisssiii',
            $this->id,
            $this->proveedor,
            $this->empresa,
            $this->usuario,
            $voucher,
            $this->ahora,
            $this->f_vencimiento,
            $this->total_bruto,
            $this->descuentos,
            $this->total);
            $insercion_orden->execute();
            $insercion = $this->sql_con->affected_rows;
            $insercion_orden->close();
        }
        
        protected function agregar_orden_propia($voucher){
            $hosteo = new Host();
            $hosteo->obtener_conexion(0);
            $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
            $insercion_orden = $this->sql_con->prepare("INSERT INTO orden_compra VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");
            $insercion_orden->bind_param('iiiisssiii',
            $this->id,
            $this->proveedor,
            $this->empresa,
            $this->usuario,
            $voucher,
            $this->ahora,
            $this->f_vencimiento,
            $this->total_bruto,
            $this->descuentos,
            $this->total);
            $insercion_orden->execute();
            $insercion_orden->close();
        }
        
        protected function obtener_parametros(){
            $this->empresa = $_SESSION['empresa'];
            $this->usuario = $_SESSION['id'];
            $this->sucursal = $_SESSION['sucursal'];
            /*$this->proveedor = mysqli_real_escape_string($this->sql_con, $_POST['proveedor']);
            $this->total_bruto = mysqli_real_escape_string($this->sql_con, $_POST['total_bruto']);
            $this->descuentos = mysqli_real_escape_string($this->sql_con, $_POST['descuentos']);
            $this->total = mysqli_real_escape_string($this->sql_con, $_POST['total']);*/
            $this->parametros = $_POST['parametros'];
            $this->f_vencimiento = mysqli_real_escape_string($this->sql_con, $_POST['f_vencimiento']);
            //$this->cantidad = $_POST['cantidad_ofertas'];
            if(isset($_POST['promociones'])){
                $this->ofertas = $_POST['promociones'];
                $this->esOferta = false;
            }
        }
        
        
        protected function obtener_origen_datos($proveedor){
            $hosteo = new Host();
            $hosteo->obtener_conexion(1);
            $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
            $consulta_origen = "SELECT host FROM empresa_conexion WHERE empresa_id = $proveedor";
            $rs_origen = $this->sql_con->query($consulta_origen);
            if($rs_origen === false){
                exit();
            }else{
                $row_origen = $rs_origen->fetch_assoc();
                return $row_origen['host'];
            }
        }
        
        protected function obtener_productos($oferta, $cantidad){
            $hosteo = new Host();
            $hosteo->obtener_conexion($this->host);
            $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
            if($this->esOferta){
                $consulta_productos = "SELECT ps.producto_id as id,  o.oferta_cantidad as cantidad, ps.producto_sucursal_precio_mayorista as precio, o.oferta_id as oferta FROM producto_sucursal ps INNER JOIN oferta o ON o.producto_id = ps.producto_id WHERE sucursal_id = 1 AND oferta_id = $oferta GROUP BY ps.producto_id";
                $result_productos = $this->sql_con->query($consulta_productos);
                if($result_productos === false){
                    trigger_error("Ha ocurrido un error");
                }else{
                    $j = 0;
                    while($row_productos = $result_productos->fetch_assoc()){
                        $producto = array();
                        $producto['id'] = $row_productos['id'];
                        $producto['precio'] = $row_productos['precio'];
                        $producto['oferta'] = $row_productos['oferta'];
                        $producto['cantidad'] = $row_productos['cantidad'] * $cantidad;
                        //echo $producto['id'] . " cantidad: " . $producto['cantidad'] . "\n";
                        array_push($this->productos, $producto);
                        $j++;
                    }
                }
                $result_productos->close();
            }
        }
        
        public function __destruct(){
            echo $this->resultado;
        }
    
    }
?>