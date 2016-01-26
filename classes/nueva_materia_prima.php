<?php

$materia = new Materias();

class Materias{

    protected $link, $sql_con;
    protected $sucursal, $tipo_cuenta, $empresa, $usuario;
    protected $datos = array(),  $accion = 0;
    protected $resultado = 0;

    public function __construct(){
        session_start();
        require('../hosts.php');
        require('conexion_new.php');
        $hosteo = new Host();
        $hosteo->obtener_conexion(0);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $this->procesar();
    }
    
    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    
    protected function obtener_parametros(){
        $this->datos = $_POST['datos'];
        $this->sucursal = $_SESSION['sucursal'];
        $this->usuario = $_SESSION['id'];   
        $this->empresa = $_SESSION['empresa'];
    }
           
    protected function procesar(){
        $this->tipo_cuenta = $_SESSION['tipo_cuenta'];
        if($this->tipo_cuenta == 1){
            $this->accion = $_POST['accion'];
            switch($this->accion){
                case 1:
                    $this->obtener_parametros();
                    $codigo = mysqli_real_escape_string($this->sql_con, $this->datos['codigo']);
                    include('sanear_string.php');
                    $codigo = sanear_string($codigo);
                    $nombre = mysqli_real_escape_string($this->sql_con, $this->datos['nombre']);
                    $descripcion = mysqli_real_escape_string($this->sql_con, $this->datos['descripcion']);
                    $unidad = mysqli_real_escape_string($this->sql_con, $this->datos['select_unidades']);
                    if($unidad == 'otro')
                        $unidad = mysqli_real_escape_string($this->sql_con, $this->datos['unidad']);
                    $u_medida = mysqli_real_escape_string($this->sql_con, $this->datos['u_medida']);
                    $this->insertar_materia($codigo, $nombre, $descripcion, $unidad, $u_medida);
                    break;
                case 2:
                    $this->obtener_parametros();
                    $codigo = mysqli_real_escape_string($this->sql_con, $this->datos['codigo']);
                    $nombre = mysqli_real_escape_string($this->sql_con, $this->datos['nombre']);
                    $descripcion = mysqli_real_escape_string($this->sql_con, $this->datos['descripcion']);
                    $unidad = mysqli_real_escape_string($this->sql_con, $this->datos['select_unidades']);
                    if($unidad == 'otro')
                        $unidad = mysqli_real_escape_string($this->sql_con, $this->datos['unidad']);
                    $u_medida = mysqli_real_escape_string($this->sql_con, $this->datos['u_medida']);
                    $this->actualizar_materia($codigo, $nombre, $descripcion, $unidad, $u_medida);
                    break;
                case 3:
                    $this->obtener_parametros();
                    $codigo = mysqli_real_escape_string($this->sql_con, $this->datos['codigo']);
                    $stock_r = mysqli_real_escape_string($this->sql_con, $this->datos['stock_r']);
                    $this->agregar_stock($codigo, $stock_r);
                    break;
                case 4:
                    $this->obtener_parametros();
                    $codigo = mysqli_real_escape_string($this->sql_con, $this->datos['codigo']);
                    $stock_m = mysqli_real_escape_string($this->sql_con, $this->datos['stock_m']);
                    $this->editar_stock_minimo($codigo, $stock_m);
                    break;
                case 5:
                    $productos = $_POST['productos'];
                    $materias = $_POST['materias'];
                    $unidades = $_POST['unidades'];
                    $this->asignar_productos($productos, $materias, $unidades);
                    break;
            }
        }else{
            $this->resultado = 6; //Sin permisos suficientes
        }
    }
    
    protected function insertar_materia($codigo, $nombre, $descripcion, $unidad, $u_medida){
        if(!$this->verificar_codigo($codigo)){
            $insercion_materia = $this->sql_con->prepare("INSERT INTO materia_prima(materia_prima_codigo, materia_prima_nombre, materia_prima_descripcion, materia_prima_unidad, materia_prima_unidad_medida) VALUES (?, ?, ?, ?, ?)");
            $insercion_materia->bind_param('sssis',
            $codigo,
            $nombre,
            $descripcion,
            $unidad,
            $u_medida);
            $insercion_materia->execute();
            $insercion_materia->close();
            $mp_id = mysqli_insert_id($this->sql_con);
            if($mp_id > 0){
                $this->insercion_sucursal($mp_id);
                //$this->resultado = 1; //Materia insertada correctamente
            }else{
                $this->resultado = 0; //Materia no ingresada
            }
        }else{
            $this->resultado = 5; //Codigo existente
        }
    }
    
    protected function actualizar_materia($codigo, $nombre, $descripcion, $unidad, $u_medida){
        $actualizar_materia = "UPDATE materia_prima SET materia_prima_nombre = '$nombre', materia_prima_descripcion = '$descripcion', materia_prima_unidad = $unidad, materia_prima_unidad_medida = '$u_medida' WHERE materia_prima_codigo = '$codigo'";
        if($this->sql_con->query($actualizar_materia) === false) {
            trigger_error('Wrong SQL: ' . $actualizar_materia . ' Error: ' . $this->sql_con->error, E_USER_ERROR);
        } else {
            $edicion_exitosa = $this->sql_con->affected_rows;
            if($edicion_exitosa > 0){
                $this->resultado = 2; //Actualizacion correcta
            }else{
                $this->resultado = 0; //Actualizacion incorrecta
            }
        }
    }
    
    protected function agregar_stock($codigo, $stock_r){
        $agregar_stock = "UPDATE materia_prima_sucursal SET materia_prima_sucursal_stock_real = materia_prima_sucursal_stock_real + $stock_r WHERE materia_prima_id = (SELECT materia_prima_id FROM materia_prima WHERE materia_prima_codigo = '$codigo') AND sucursal_id = $this->sucursal";
        if($this->sql_con->query($agregar_stock) === false) {
            trigger_error('Wrong SQL: ' . $agregar_stock . ' Error: ' . $this->sql_con->error, E_USER_ERROR);
        } else {
            $edicion_exitosa = $this->sql_con->affected_rows;
            if($edicion_exitosa > 0){
                if($this->registro_stock($codigo, $stock_r))
                    $this->resultado = 3; //Stock agregado correctamente
                else
                    $this->resultado = 8; //No se pudo registrar cambio en el stock
            }else{
                $this->resultado = 0; //No se pudo agregar stock
            }
        }
    }
    
    protected function editar_stock_minimo($codigo, $stock_m){
        $actualizar_stock = "UPDATE materia_prima_sucursal SET materia_prima_sucursal_stock_minimo = $stock_m WHERE materia_prima_id = (SELECT materia_prima_id FROM materia_prima WHERE materia_prima_codigo = '$codigo') AND sucursal_id = $this->sucursal";
        if($this->sql_con->query($actualizar_stock) === false) {
            trigger_error('Wrong SQL: ' . $actualizar_stock . ' Error: ' . $this->sql_con->error, E_USER_ERROR);
        } else {
            $edicion_exitosa = $this->sql_con->affected_rows;
            if($edicion_exitosa > 0){
                $this->resultado = 4; //Stock agregado correctamente
            }else{
                $this->resultado = 0; //No se pudo agregar stock
            }
        }
    }
    
    protected function verificar_codigo($codigo){
        $consulta_codigo = "SELECT count(*) as cont FROM materia_prima WHERE materia_prima_codigo = '$codigo'";
        $rs_codigo = $this->sql_con->query($consulta_codigo);
        if($rs_codigo === false){
            trigger_error("Ha ocurrido un error");
            exit();
        }else{
            $row_codigo = $rs_codigo->fetch_assoc();
            if($row_codigo['cont'] > 0)
                return true;
            else
                return false;
        }
    }
    
    protected function insercion_sucursal($mp_id){
        $consulta_sucursales = "SELECT sucursal_id as s_id FROM sucursal WHERE empresa_id = $this->empresa";
        $rs_sucursales = $this->sql_con->query($consulta_sucursales);
        $inserciones_estado = true;
        if($rs_sucursales === false){
            trigger_error("Ha ocurrido un error");
            exit();
        }else{
            while($row_sucursales = $rs_sucursales->fetch_assoc()){
                $insercion_sucursal = $this->sql_con->prepare("INSERT INTO materia_prima_sucursal(materia_prima_id, sucursal_id) VALUES (?, ?)");
                $insercion_sucursal->bind_param('ii',
                $mp_id,
                $row_sucursales['s_id']);
                $insercion_sucursal->execute();
                //print_r($insercion_sucursal);
                $filas_afectadas = $this->sql_con->affected_rows;
                    if($filas_afectadas == 0)
                        $inserciones_estado = 0;
                $insercion_sucursal->close();
            }
            if($inserciones_estado){
                $this->resultado = 1;
            }else{
                $this->resultado = 7; //Insercion en sucursal con problemas
            }
        }
    }
    
    protected function registro_stock($codigo, $cantidad){
        $consulta_id = "SELECT materia_prima_id as id FROM materia_prima WHERE materia_prima_codigo = '$codigo'";
        $rs_id = $this->sql_con->query($consulta_id);
        if($rs_id === false){
            trigger_error("Ha ocurrido un error");
        }else{
            $row_id = $rs_id->fetch_assoc();
            $insercion_registro = $this->sql_con->prepare("INSERT INTO registro_stock_materia_prima VALUES(null, ?, ?, ?, ?, NOW())");
            $insercion_registro->bind_param('iiii',
            $row_id['id'],
            $this->sucursal,
            $this->usuario,
            $cantidad);
            $insercion_registro->execute();
            $insercion_exitosa = $this->sql_con->affected_rows;
            $insercion_registro->close();
            if($insercion_exitosa > 0){
                return true;
            }else{
                return false;
            }
        }
    }
    
    protected function asignar_productos($productos, $materias, $unidades){
        $registros_insertados = 0;
        $problemas = 0;
        for($i = 0; $i < count($materias); $i++){
            $materia = mysqli_real_escape_string($this->sql_con, $materias[$i]);
            $consulta_materia = "SELECT materia_prima_id as m_id FROM materia_prima WHERE materia_prima_codigo = '$materia' LIMIT 1";
            $rs_materia = $this->sql_con->query($consulta_materia);
            if($rs_materia === false){
                continue;
            }else{
                $row_materia = $rs_materia->fetch_assoc();
                $m_id = $row_materia['m_id'];
                for($j = 0; $j < count($productos); $j++){
                    $producto = mysqli_real_escape_string($this->sql_con, $productos[$j]);
                    $unidad = mysqli_real_escape_string($this->sql_con, $unidades[$j]);
                    $consulta_producto = "SELECT producto_id as p_id FROM producto WHERE producto_codigo = '$producto'";
                    $rs_producto = $this->sql_con->query($consulta_producto);
                    if($rs_producto === false){
                        continue;
                    }else{
                        $row_producto = $rs_producto->fetch_assoc();
                        $p_id = $row_producto['p_id'];
                        if($unidad > 0){
                            if($this->asignacion_nueva($m_id, $p_id) <= 0)
                                $aff = $this->insertar_asignacion($m_id, $p_id, $unidad);
                            else
                                $aff = $this->actualizar_asignacion($m_id, $p_id, $unidad);
                        }else{
                            $aff = $this->eliminar_asignacion($m_id, $p_id);
                        }
                        if($aff <= 0)
                            $problemas++;
                        else
                            $registros_insertados++;
                    }
                }
                $rs_producto->close();
            }
            $rs_materia->close();
        }
        $this->resultado = 1;
    }
    
    protected function asignacion_nueva($m_id, $p_id){
        $consulta = "SELECT count(*) as cont FROM materia_prima_producto WHERE materia_prima_id = $m_id AND producto_id = $p_id";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            $resultado = 0;
            exit();
        }else{
            $row = $rs->fetch_assoc();
            return $row['cont'];
        }
    }
    
    protected function insertar_asignacion($m_id, $p_id, $unidad){
        $insercion = $this->sql_con->prepare("INSERT INTO materia_prima_producto (materia_prima_id, producto_id, materia_prima_unidad) VALUES (?, ?, ?)");
        $insercion->bind_param('iii',
        $m_id,
        $p_id,
        $unidad);
        $insercion->execute();
        $aff = $this->sql_con->affected_rows;
        $insercion->close();
    }
    
    protected function actualizar_asignacion($m_id, $p_id, $unidad){
        $actualizacion = "UPDATE materia_prima_producto SET materia_prima_unidad = $unidad WHERE materia_prima_id = $m_id AND producto_id = $p_id";
        if($this->sql_con->query($actualizacion) === false) {
            trigger_error('Wrong SQL: ' . $actualizacion . ' Error: ' . $this->sql_con->error, E_USER_ERROR);
        } else {
            $filas_afectadas = $this->sql_con->affected_rows;
            return $filas_afectadas;
        }
        return 0;
    }
    
    protected function eliminar_asignacion($m_id, $p_id){
        $eliminacion = $this->sql_con->prepare("DELETE FROM materia_prima_producto WHERE materia_prima_id = ? AND producto_id = ?");
        $eliminacion->bind_param('ii',
        $m_id,
        $p_id
        );
        $eliminacion->execute();
        $aff = $this->sql_con->affected_rows;
        $eliminacion->close();
        return $aff;
    }
    
    public function __destruct(){
        echo $this->resultado;
    }

}

?>