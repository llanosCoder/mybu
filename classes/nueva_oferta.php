<?php

    $oferta = new ofertaes();    
    
    class ofertaes{
        
        protected $link;
        protected $resultado = 0;
        protected $sql_con;
        protected $datos = array();
        protected $alcance, $sucursal, $empresa;
        protected $id;
        
        public function __construct(){
            session_start();
            require('../hosts.php');
            require_once('conexion_new.php');
            $hosteo = new Host();
            $hosteo->obtener_conexion(0);
            $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
            $this->agregar_oferta();
        }
        
        protected function set_conexion($host, $user, $pass, $bd){
            $conexion = new Conexion();
            $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
            $this->sql_con->set_charset('utf8');
        }
        
        protected function agregar_oferta(){
            $this->datos['oferta_id'] = mysqli_real_escape_string($this->sql_con, $_POST['id']);
            $this->datos['oferta_descripcion'] = mysqli_real_escape_string($this->sql_con, $_POST['descripcion']);
            $this->datos['oferta_oferta_tipo'] = mysqli_real_escape_string($this->sql_con, $_POST['tipo_oferta']);
            $this->datos['oferta_descuento'] = mysqli_real_escape_string($this->sql_con, $_POST['descuento']);
            $this->datos['oferta_descuento'] = round($this->datos['oferta_descuento']);
            $this->datos['oferta_precio'] = mysqli_real_escape_string($this->sql_con, $_POST['precio']);
            $this->datos['oferta_precio'] = round($this->datos['oferta_precio']);
            $this->datos['oferta_cantidad'] = mysqli_real_escape_string($this->sql_con, $_POST['cantidad']);
            $this->datos['oferta_cantidad'] = round($this->datos['oferta_cantidad']);
            if($_POST['f_inicio'] == 'null' || $_POST['f_inicio'] == '')
                $this->datos['oferta_fecha_inicio'] = date('Y-m-d');
            else
                $this->datos['oferta_fecha_inicio'] = mysqli_real_escape_string($this->sql_con, $_POST['f_inicio']);
            if($_POST['f_termino'] == 'null' || $_POST['f_inicio'] == '')
                $this->datos['oferta_fecha_termino'] = date('Y-m-d');
            else
                $this->datos['oferta_fecha_termino'] = mysqli_real_escape_string($this->sql_con, $_POST['f_termino']);
            $this->datos['oferta_tipo'] = mysqli_real_escape_string($this->sql_con, $_POST['tipo']);
            $this->datos['producto_id'] = $this->obtener_producto_id(mysqli_real_escape_string($this->sql_con, $_POST['producto']));
            $this->datos['oferta_stock'] = mysqli_real_escape_string($this->sql_con, $_POST['stock']);
            $this->datos['oferta_stock'] = round($this->datos['oferta_stock']);
            $this->datos['oferta_estado'] = 1;
            $this->alcance = mysqli_real_escape_string($this->sql_con, $_POST['alcance']);
            $this->sucursal = $_SESSION['sucursal'];
            $this->empresa = $_SESSION['empresa'];
            if($this->datos['oferta_id'] != '0'){
                $estado1 = $this->editar_oferta();
                $hosteo = new Host();
                $hosteo->obtener_conexion(2);
                $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
                $estado2 = $this->editar_oferta();
                if($estado1 == 1 && $estado2 == 1){
                    $this->resultado = 1;
                }else{
                    if($estado1 == 1 && $estado2 != 1){
                        $this->resultado = 3;
                    }else{
                        $this->resultado = 0;
                    }
                }
                $hosteo = new Host();
                $hosteo->obtener_conexion(0);
                $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
            }else{
                $this->datos['producto_precio'] = $this->obtener_precio();
                $cont = 0;
                $hosteo = new Host();
                $hosteo->obtener_conexion(2);
                $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
                $estado_2 = $this->insertar_oferta_global();
                $hosteo = new Host();
                $hosteo->obtener_conexion(0);
                $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
                $estado_1 = $this->insertar_oferta();
                
                if($estado_1 > 0)
                    $cont++;
                if($estado_2 > 0)
                    $cont++;
                if($cont == 2){
                    $this->resultado = 1;
                }else{
                    if($cont == 1){
                        $this->resultado = 2;
                    }else{
                        $this->resultado = 0;
                    }
                }
            }
            $filas_afectadas = 0;
            if($this->alcance == 2){
                $consulta_sucursales = "SELECT sucursal_id as s_id FROM sucursal";
                $rs_sucursales = $this->sql_con->query($consulta_sucursales);
                if($rs_sucursales === false) {
                    trigger_error('Ha ocurrido un error');
                } else {
                    while ($row_sucursales = $rs_sucursales->fetch_assoc()) {
                        $sucursal_actual = $row_sucursales['s_id'];
                        $consulta_sucursal_actual = "SELECT oferta_sucursal_id FROM oferta_sucursal WHERE 
                        sucursal_id = $sucursal_actual AND oferta_id = $this->id";
                        $rs_sucursal_actual = $this->sql_con->query($consulta_sucursal_actual);
                        if(mysqli_num_rows($rs_sucursal_actual) == 0 && $this-resultado == 1){
                            $insercion_oferta_sucursal = $this->sql_con->prepare("INSERT INTO oferta_sucursal(oferta_sucursal_id,
                            oferta_id, sucursal_id) 
                            VALUES (null, ?, ?)");
                            $insercion_oferta_sucursal->bind_param('ii',
                            $this->id,
                            $row_sucursales['s_id']);
                            $insercion_oferta_sucursal->execute(); 
                            $insercion_oferta_sucursal->close();
                            $filas_afectadas++;
                        }
                    }
                }
            }else{
                $consulta_sucursal_actual = "SELECT oferta_sucursal_id as cont FROM oferta_sucursal WHERE sucursal_id = $this->sucursal AND oferta_id = $this->id";
                $rs_sucursal_actual = $this->sql_con->query($consulta_sucursal_actual);
                if(mysqli_num_rows($rs_sucursal_actual) >= 0){
                    $insercion_oferta_sucursal = $this->sql_con->prepare("INSERT INTO oferta_sucursal(oferta_sucursal_id, 
                    oferta_id, sucursal_id) VALUES (null, ?, ?)");
                    $insercion_oferta_sucursal->bind_param('ii',
                    $this->id,
                    $this->sucursal);
                    $insercion_oferta_sucursal->execute(); 
                    $insercion_oferta_sucursal->close();
                    $filas_afectadas++;
                }
                $eliminar_sucursales = $this->sql_con->prepare("DELETE FROM oferta_sucursal WHERE sucursal_id != ?");
                $eliminar_sucursales->bind_param('i', $this->sucursal);
                $eliminar_sucursales->execute(); 
                $eliminacion_exitosa = $this->sql_con->affected_rows;
                if($eliminacion_exitosa > 0){
                        $filas_afectadas++;
                }
                $eliminar_sucursales->close(); 
            }
            if($filas_afectadas > 0){
                $this->resultado = 1;
            }
        }
        
        protected function obtener_precio(){
            $p_id = $this->datos['producto_id'];
            $consulta_precio = "SELECT producto_sucursal_precio_mayorista as precio_producto FROM producto_sucursal WHERE producto_id = $p_id AND sucursal_id = 1";
            $rs_precio = $this->sql_con->query($consulta_precio);
            if($rs_precio === false){
                $this->datos['producto_precio'] = NULL;
            }else{
                $row_precio = $rs_precio->fetch_assoc();
                return $row_precio['precio_producto'];
            }
        }
        
        protected function editar_oferta(){
            $consulta = "UPDATE oferta p SET";
            $i = 0;
            foreach($this->datos as $indice=>$valor){
                if($valor != '' && $valor != null){
                    if($i != 0){
                        $consulta = $consulta . ",";
                    }
                    $consulta = $consulta . " " . $indice . " = '" . $valor . "'";
                }
                $i++;
            }
            $this->id = $this->datos['oferta_id'];
            $consulta = $consulta . " WHERE p.oferta_id = " . $this->id;
            if($this->sql_con->query($consulta) === false) {
              trigger_error('Wrong SQL: ' . $consulta . ' Error: ' . $this->sql_con->error, E_USER_ERROR);
            } else {
                $edicion_exitosa = $this->sql_con->affected_rows;
                if($edicion_exitosa > 0){
                    return 1;
                }else{
                    return 0;
                }
            }
        }
        
        protected function insertar_oferta_global(){
            $insercion_oferta = $this->sql_con->prepare("INSERT INTO oferta VALUES (null, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insercion_oferta->bind_param('isiiiissiiiii',
            $this->empresa,
            $this->datos['oferta_descripcion'],
            $this->datos['oferta_oferta_tipo'],
            $this->datos['oferta_descuento'],
            $this->datos['oferta_precio'],
            $this->datos['oferta_cantidad'],
            $this->datos['oferta_fecha_inicio'],
            $this->datos['oferta_fecha_termino'],
            $this->datos['oferta_stock'],
            $this->datos['oferta_tipo'],
            $this->datos['oferta_estado'],
            $this->datos['producto_id'],
            $this->datos['producto_precio']);
            $insercion_oferta->execute(); 
            $this->id = mysqli_insert_id($this->sql_con);
            $aff = $this->sql_con->affected_rows;
            $insercion_oferta->close();
            return $aff;
        }
        
        protected function insertar_oferta(){
            $insercion_oferta = $this->sql_con->prepare("INSERT INTO oferta VALUES (?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insercion_oferta->bind_param('iisiiiissiiiii',
            $this->id,
            $this->empresa,
            $this->datos['oferta_descripcion'],
            $this->datos['oferta_oferta_tipo'],
            $this->datos['oferta_descuento'],
            $this->datos['oferta_precio'],
            $this->datos['oferta_cantidad'],
            $this->datos['oferta_fecha_inicio'],
            $this->datos['oferta_fecha_termino'],
            $this->datos['oferta_stock'],
            $this->datos['oferta_tipo'],
            $this->datos['oferta_estado'],
            $this->datos['producto_id'],
            $this->datos['producto_precio']);
            $insercion_oferta->execute(); 
            $insercion_oferta->close();
            return $this->sql_con->affected_rows;
        }
        
        protected function obtener_producto_id($codigo){
            $consulta_producto = "SELECT producto_id FROM producto WHERE producto_codigo = '$codigo' LIMIT 1";
            $result_producto = $this->sql_con->query($consulta_producto);
            if($result_producto === false){
                trigger_error("Ha ocurrido un error");
            }else{
                $row_producto = $result_producto->fetch_assoc();
                return $row_producto['producto_id'];
            }
        }
        
        public function __destruct(){
            echo $this->resultado;
        }
    }
?>