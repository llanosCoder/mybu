<?php

    $promocion = new Promociones();
    
    class Promociones{
    
        protected $link, $sql_con, $resultado = 0, $pcod, $pid = array(), $prid, $datos_validos = false;
        
        public function __construct(){
            session_start();
            require('../hosts.php');
            require('conexion_new.php');
            $hosteo = new Host();
            $hosteo->obtener_conexion(0);
            $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
            $this->recibir_datos();
            $this->asignar_promociones();
        }
        
        protected function set_conexion($host, $user, $pass, $bd){
            $conexion = new Conexion();
            $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
            $this->sql_con->set_charset('utf8');
        }
        
        protected function recibir_datos(){
            $this->pcod = $_POST['productos'];
            $this->prid = $_POST['promociones'];
        }
        
        protected function verificar_datos_producto($pcod, $i){
            if($pcod != '0'){
                $consulta_existe_producto = "SELECT count(*) as cont, producto_id as pid FROM producto WHERE producto_codigo = '$pcod'";
                $result_existe_producto = $this->sql_con->query($consulta_existe_producto);
                if($result_existe_producto === false){
                    trigger_error('Ha ocurrido un error');
                }else{
                    $row_existe_producto = $result_existe_producto->fetch_assoc();
                    if($row_existe_producto['cont'] > 0){
                        $result_existe_producto->close();
                        $this->pid[$i] = $row_existe_producto['pid'];
                        return true;
                    }else{
                        $result_existe_producto->close();
                        return false;
                    }
                }
            }else{
                return false;
            }
        }
        
        protected function verificar_datos_promocion($prid){
            
                if($prid != 0){
                    $consulta_existe_promocion = "SELECT count(*) as cont FROM promocion WHERE promocion_id = $prid";
                    $result_existe_promocion = $this->sql_con->query($consulta_existe_promocion);
                    if($result_existe_promocion ===false){
                        trigger_error('Ha ocurrido un error');
                    }else{
                        $row_existe_promocion = $result_existe_promocion->fetch_assoc();
                        if($row_existe_promocion['cont'] > 0){
                            $result_existe_promocion->close();
                            return true;
                        }else{
                            $result_existe_promocion->close();
                            return false;
                        }
                    }
                }else{
                    return false;
                }
        }
        
        protected function verificar_datos_repetidos($pid){
            $consulta = "SELECT count(*) as cont FROM promocion_producto WHERE producto_id = $pid";
            $result = $this->sql_con->query($consulta);
            if($result === false){
                trigger_error('Ha ocurrido un error');
            }else{
                $row = $result->fetch_assoc();
                if($row['cont'] > 0){
                    $eliminar_promocion = $this->sql_con->prepare("DELETE FROM promocion_producto WHERE producto_id = ?");
                    $eliminar_promocion->bind_param('i',
                    $pid);
                    $eliminar_promocion->execute();
                    $eliminadas = $this->sql_con->affected_rows;
                    $eliminar_promocion->close();
                }
            }
        }
        
        protected function asignar_promociones(){
            for($i = 0; $i < count($this->pcod); $i++){
                $this->pcod[$i] = mysqli_real_escape_string($this->sql_con, $this->pcod[$i]);
                if(!$this->verificar_datos_producto($this->pcod[$i], $i)){
                    $this->resultado = 3;
                    exit();
                }
                $this->prid = mysqli_real_escape_string($this->sql_con, $this->prid);
                if(!$this->verificar_datos_promocion($this->prid)){
                    $this->resultado = 2;
                    exit();
                }
                $this->verificar_datos_repetidos($this->pid[$i]);
                $asignacion_promociones = mysqli_prepare($this->sql_con, "INSERT INTO promocion_producto VALUES(null, ?, ?)");
                $asignacion_promociones->bind_param('ii',
                $this->prid, $this->pid[$i]);
                $asignacion_promociones->execute();
                $filas_afectadas = $this->sql_con->affected_rows;
                if($filas_afectadas > 0){
                    $this->resultado = 1;
                }
                $asignacion_promociones->close();
            }
        }
        
        
        public function __destruct(){
            echo $this->resultado;
        }
    
    }

?>