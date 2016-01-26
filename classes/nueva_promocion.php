<?php

    $promocion = new Promociones();    
    
    class Promociones{
        
        protected $link;
        protected $resultado = 1;
        protected $sql_con;
        protected $datos = array();
        protected $alcance, $sucursal;
        
        public function __construct(){
            session_start();
            require('../hosts.php');
            require('conexion_new.php');
            $hosteo = new Host();
            $hosteo->obtener_conexion(0);
            $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
            $this->agregar_promocion();
        }
        
        protected function set_conexion($host, $user, $pass, $bd){
            $conexion = new Conexion();
            $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
            $this->sql_con->set_charset('utf8');
        }
        
        protected function agregar_promocion(){
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'],$this->link['pass'],$this->link['bd']);
            $this->datos['promocion_id'] = mysqli_real_escape_string($this->sql_con, $_POST['id']);
            $this->datos['promocion_descripcion'] = mysqli_real_escape_string($this->sql_con, $_POST['descripcion']);
            $this->datos['promocion_oferta_tipo'] = mysqli_real_escape_string($this->sql_con, $_POST['tipo_promocion']);
            $this->datos['promocion_descuento'] = mysqli_real_escape_string($this->sql_con, $_POST['descuento']);
            $this->datos['promocion_precio'] = mysqli_real_escape_string($this->sql_con, $_POST['precio']);
            $this->datos['promocion_precio'] = round($this->datos['promocion_precio']);
            $this->datos['promocion_cantidad'] = mysqli_real_escape_string($this->sql_con, $_POST['cantidad']);
            $this->datos['promocion_cantidad'] = round($this->datos['promocion_cantidad']);
            $this->datos['promocion_fecha_inicio'] = mysqli_real_escape_string($this->sql_con, $_POST['f_inicio']);
            if($_POST['f_inicio'] == 'null' || $_POST['f_inicio'] == '')
                $this->datos['promocion_fecha_inicio'] = date('Y-m-d H:i:s');
            $this->datos['promocion_fecha_termino'] = mysqli_real_escape_string($this->sql_con, $_POST['f_termino']);
            if($_POST['f_inicio'] == 'null' || $_POST['f_inicio'] == '')
                $this->datos['promocion_fecha_termino'] = date('Y-m-d H:i:s');
            $this->datos['promocion_tipo'] = mysqli_real_escape_string($this->sql_con, $_POST['tipo']);
            $this->datos['promocion_stock'] = mysqli_real_escape_string($this->sql_con, $_POST['stock']);
            $this->datos['promocion_stock'] = round($this->datos['promocion_stock']);
            $this->datos['promocion_estado'] = 1;
            $this->alcance = mysqli_real_escape_string($this->sql_con, $_POST['alcance']);
            $this->sucursal = $_SESSION['sucursal'];
            $this->sql_con->set_charset("utf8");
            if($this->datos['promocion_id'] != '0'){
                $consulta = "UPDATE promocion p SET";
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
                $id = $this->datos['promocion_id'];
                $consulta = $consulta . " WHERE p.promocion_id = " . $id;
                if($this->sql_con->query($consulta) === false) {
                    trigger_error('Wrong SQL: ' . $consulta . ' Error: ' . $this->sql_con->error, E_USER_ERROR);
                } else {
                    $edicion_exitosa = $this->sql_con->affected_rows;
                    if($edicion_exitosa > 0){
                        $this->resultado = 1;
                    }else{
                        $this->resultado = 3;
                    }
                }
            }else{
                $insercion_promocion = $this->sql_con->prepare("INSERT INTO promocion VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $insercion_promocion->bind_param('siiiissiii',
                $this->datos['promocion_descripcion'],
                $this->datos['promocion_oferta_tipo'],
                $this->datos['promocion_descuento'],
                $this->datos['promocion_precio'],
                $this->datos['promocion_cantidad'],
                $this->datos['promocion_fecha_inicio'],
                $this->datos['promocion_fecha_termino'],
                $this->datos['promocion_tipo'],
                $this->datos['promocion_stock'],
                $this->datos['promocion_estado']);
                $this->sql_con->set_charset("utf8");
                $insercion_promocion->execute();
                $insercion_promocion->close();
                $id = mysqli_insert_id($this->sql_con);
                if($id != 0){
                    $this->resultado = 1;
                }
            }
            $filas_afectadas = 0;
            if($this->alcance == 2){
                $consulta_sucursales = "SELECT sucursal_id as s_id FROM sucursal";
                $this->sql_con->set_charset("utf8");
                $rs_sucursales = $this->sql_con->query($consulta_sucursales);
                if($rs_sucursales === false) {
                    trigger_error('Ha ocurrido un error');
                } else {
                    while ($row_sucursales = $rs_sucursales->fetch_assoc()) {
                        $sucursal_actual = $row_sucursales['s_id'];
                        $consulta_sucursal_actual = "SELECT promocion_sucursal_id FROM promocion_sucursal WHERE 
                        sucursal_id = $sucursal_actual AND promocion_id = $id";
                        $rs_sucursal_actual = $this->sql_con->query($consulta_sucursal_actual);
                        if(mysqli_num_rows($rs_sucursal_actual) == 0){
                            $insercion_promocion_sucursal = $this->sql_con->prepare("INSERT INTO promocion_sucursal(promocion_sucursal_id,
                            promocion_id, sucursal_id) 
                            VALUES (null, ?, ?)");
                            $insercion_promocion_sucursal->bind_param('ii',
                            $id,
                            $row_sucursales['s_id']);
                            $insercion_promocion_sucursal->execute();
                            $insercion_promocion_sucursal->close();
                            $filas_afectadas++;
                        }
                    }
                }
            }else{
                $consulta_sucursal_actual = "SELECT promocion_sucursal_id as cont FROM promocion_sucursal WHERE 
                sucursal_id = $this->sucursal AND promocion_id = $id";
                $rs_sucursal_actual = $this->sql_con->query($consulta_sucursal_actual);
                if(mysqli_num_rows($rs_sucursal_actual) == 0){
                    $insercion_promocion_sucursal = $this->sql_con->prepare("INSERT INTO promocion_sucursal(promocion_sucursal_id, 
                    promocion_id, sucursal_id) VALUES (null, ?, ?)");
                    $insercion_promocion_sucursal->bind_param('ii',
                    $id,
                    $this->sucursal);
                    $insercion_promocion_sucursal->execute(); 
                    $insercion_promocion_sucursal->close();
                    $filas_afectadas++;
                }
                $eliminar_sucursales = $this->sql_con->prepare("DELETE FROM promocion_sucursal WHERE sucursal_id != ?");
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
        
        public function __destruct(){
            echo $this->resultado;
        }
    }
?>