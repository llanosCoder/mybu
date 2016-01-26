<?php

class Ventas{

    protected $link, $sql_con;
    
    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    
    function set_host($host){
        $hosteo = new Host();
        $hosteo->obtener_conexion($host);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
    }
    
    public function obtener_ventas_recientes(){
        $fecha_limite = date('Y-m-d', strtotime('-2 week', date('Y-m-d')));
        $consulta = "SELECT v.venta_id as v_id, DATE(v.venta_fecha) as v_fecha, v.venta_bruto as bruto, v.venta_descuentos as descuentos, v.venta_neto as neto, CONCAT(u.usuario_apellidos, ' ', u.usuario_nombres) as u_nombres, c.cliente_rut as c_rut, CONCAT(c.cliente_apellido_paterno, ' ', c.cliente_apellido_materno, ' ', c.cliente_nombre) as c_nombre FROM venta v LEFT JOIN usuario u ON v.usuario_venta_id = u.usuario_id LEFT JOIN venta_credito vc ON v.venta_id = vc.venta_credito_id LEFT JOIN linea_credito lc ON vc.linea_credito_id = lc.linea_credito_id LEFT JOIN cliente c ON lc.cliente_id = c.cliente_id WHERE v.venta_fecha >= date_add(NOW(), INTERVAL -15 DAY) AND v.venta_bruto > 0 ORDER BY v.venta_fecha DESC";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            $datos['resultado'] = 0;
        } else {
            $datos['resultado'] = 1;
            $datos['ventas'] = array();
            while($row = $rs->fetch_assoc()){
                $dato = array();
                foreach($row as $indice=>$valor){
                    if($indice == 'v_id') {
                        $dato[$indice] = md5($valor);
                    } else {
                        $dato[$indice] = $valor;
                    }
                }
                array_push($datos['ventas'], $dato);
            }
        }
        return $datos;
    }
    
    
    
    protected function obtener_detalle_venta($codigo){
        $consulta = "SELECT venta_id as v_id, venta_bruto as bruto, venta_descuentos as descuentos, venta_neto as neto FROM venta WHERE MD5(venta_id) = '$codigo'";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return false;
        }else{
            while($row = $rs->fetch_assoc()){
                $dato = array();
                foreach($row as $indice=>$valor){
                    $dato[$indice] = $valor;
                }
            }
        }
        return $dato;
    }
    
    protected function actualizar_venta($detalle_venta){
        $actualizar = $this->sql_con->prepare("UPDATE venta SET venta_bruto = 0, venta_descuentos = 0, venta_neto = 0 WHERE venta_id = ?");
        $actualizar->bind_param('i', $detalle_venta['v_id']);
        $actualizar->execute();
        $afectadas = $this->sql_con->affected_rows;
        $actualizar->close();
        return $afectadas;
    }
    
    protected function registrar_anulacion($detalle_venta, $usuario_anulacion){
        $insercion = $this->sql_con->prepare("INSERT INTO venta_anulada (venta_id, venta_bruto, venta_descuento, venta_neto, venta_usuario_anulacion, venta_anulacion_fecha)VALUES (?, ?, ?, ?, ?, NOW())");
        $insercion->bind_param('iiiii',
                              $detalle_venta['v_id'],
                              $detalle_venta['bruto'],
                              $detalle_venta['descuentos'],
                              $detalle_venta['neto'],
                              $usuario_anulacion);
        $insercion->execute();
        $afectadas = $this->sql_con->affected_rows;
        $insercion->close();
        return $afectadas;
    }
    
    public function anular_venta($codigo, $usuario){
        $codigo = mysqli_real_escape_string($this->sql_con, $codigo);
        $detalle_venta = $this->obtener_detalle_venta($codigo);
        if($this->actualizar_venta($detalle_venta) > 0){
            if($this->registrar_anulacion($detalle_venta, $usuario) > 0){
                $resultado = 1;
            }
        } else {
            $resultado = 0;
        }
        return $resultado;
    }
    
    protected function corresponde_usuario($usuario){
        $consulta = "SELECT COUNT(*) AS cont FROM usuario WHERE usuario_id = $usuario";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return false;
        }else{
            $row = $rs->fetch_assoc();
            if($row['cont'] > 0){
                return true;
            }else{
                return false;
            }
        }
    }
    
    protected function corresponde_empresa($empresa){
        $consulta = "SELECT COUNT(*) AS cont FROM empresa WHERE empresa_id = $empresa";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return false;
        }else{
            $row = $rs->fetch_assoc();
            if($row['cont'] > 0){
                return true;
            }else{
                return false;
            }
        }
    }
    
    public function verificar_permisos($usuario, $empresa){
        if($this->corresponde_usuario($usuario)){
            if($this->corresponde_empresa($empresa)){
                return true;
            }
        }
        return false;
    }
    
    public function __construct(){
        session_start();
        require('../hosts.php');
        require('conexion_new.php');
    }

    public function __destruct(){
    }
}

?>