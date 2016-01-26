<?php

class Perfil{

    protected $link, $sql_con;

    public function __construct(){
        require_once('../hosts.php');
        require('conexion_new.php');
    }

    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }

    public function set_host($host){
        $hosteo = new Host();
        $hosteo->obtener_conexion($host);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
    }

    public function comprobar_contrasena($usuario, $contrasena){
        $this->set_host(1);
        $contrasena = md5(mysqli_real_escape_string($this->sql_con, $contrasena));
        $consulta = "SELECT count(*) as cont FROM usuario_pass WHERE usuario_id = $usuario AND usuario_pass COLLATE utf8_spanish_ci LIKE '$contrasena'";
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

    protected function limpiar_datos($datos){
        $i = 0;
        foreach($datos as $indice=>$valor){
            switch($indice){
                case 'nombre':
                    $n_indice = 'empresa_nombre';
                    break;
                case 'rut':
                    $n_indice = 'empresa_rut';
                    break;
                case 'direccion':
                    $n_indice = 'empresa_direccion';
                    break;
                case 'comuna':
                    $n_indice = 'empresa_comuna';
                    break;
                case 'region':
                    $n_indice = 'empresa_region';
                    break;
                case 'pais':
                    $n_indice = 'empresa_pais';
                    break;
                case 'nombre':
                    $n_indice = 'empresa_nombre';
                    break;
                case 'telefono':
                    $n_indice = 'empresa_telefono';
                    break;
                case 'email':
                    $n_indice = 'empresa_correo';
                    break;

                case 'u_nombres':
                    $n_indice = 'usuario_nombres';
                    break;
                case 'u_apellidos':
                    $n_indice = 'usuario_apellidos';
                    break;
                case 'u_direccion':
                    $n_indice = 'usuario_direccion';
                    break;
                case 'u_comuna':
                    $n_indice = 'usuario_comuna';
                    break;
                case 'u_region':
                    $n_indice = 'usuario_region';
                    break;
                case 'u_pais':
                    $n_indice = 'usuario_pais';
                    break;
                case 'u_pregunta_secreta':
                    $n_indice = 'usuario_pregunta_secreta';
                    break;
                case 'u_email':
                    $n_indice = 'usuario_mail';
                    break;
                case 'f_nacimiento':
                    $n_indice = 'fecha_nacimiento';
                    break;
                case 'sexo':
                    $n_indice = 'u_sexo';
                    break;
                case 'respuesta_secreta':
                    $n_indice = 'respuesta_secreta';
                    break;
            }
            $datos[$n_indice] = mysqli_real_escape_string($this->sql_con, $valor);
            $datos[$indice] = '';
            $i++;
        }
        return $datos;
    }

    public function editar_empresa($empresa, $datos){
        $datos = $this->limpiar_datos($datos);
        $consulta = "UPDATE empresa SET ";
        $i = 0;
        foreach($datos as $indice=>$valor){
            if($valor != '' && $valor != '0'){
                if ($i > 0) {
                    $consulta .= ', ';
                }
                $consulta .= $indice . ' = "' . $valor . '"';
                $i++;
            }
        }
        $consulta .= " WHERE empresa_id = $empresa";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return false;
        }else{
            $afectadas = $this->sql_con->affected_rows;
            if($afectadas > 0){
                return true;
            }else{
                return false;
            }
        }
    }

    public function empresa_contacto_existe($empresa){
        $consulta = "SELECT count(*) as cont FROM empresa_contacto WHERE empresa_id = $empresa";
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

    public function editar_empresa_contacto($empresa, $datos){
        $datos = $this->limpiar_datos($datos);
        if($this->empresa_contacto_existe($empresa)){
            $consulta = "UPDATE empresa_contacto SET ";
            $i = 0;
            foreach($datos as $indice=>$valor){
                if($valor != '' && $valor != '0'){
                    if ($i > 0) {
                        $consulta .= ', ';
                    }
                    $consulta .= $indice . ' = "' . $valor . '"';
                    $i++;
                }

            }
            $consulta .= " WHERE empresa_id = $empresa";
        }else{
            $consulta = "INSERT INTO empresa_contacto (empresa_id";
            if($datos['empresa_telefono'] != '' && $datos['empresa_telefono'] != null){
                $consulta .= ", ";
                $consulta .= "empresa_telefono";
            }
            if($datos['empresa_correo'] != '' && $datos['empresa_telefono'] != null){
                $consulta .= ", ";
                $consulta .= "empresa_correo";
            }
            $consulta .= ") VALUES ($empresa";
            $i = 0;
            foreach($datos as $indice=>$valor){
                if($valor != '' && $valor != '0'){
                    if ($i > 0){
                        $consulta .= ', ';
                    }
                    $consulta .= ', ';
                    $consulta .= "'$valor'";
                    $i++;
                }
            }
            $consulta .= ")";
        }
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            trigger_error('Wrong SQL: ' . $consulta . ' Error: ' . $this->sql_con->error, E_USER_ERROR);
            return false;
        }else{
            $afectadas = $this->sql_con->affected_rows;
            if($afectadas > 0){
                return true;
            }else{
                return false;
            }
        }
    }

    protected function existe_respuesta_secreta($usuario){
        $consulta = "SELECT count(*) as cont FROM usuario_respuesta_secreta WHERE usuario_id = $usuario";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return false;
        }else{
            $row = $rs->fetch_assoc();
            if($row['cont'] > 0){
                return true;
            } else{
                return false;
            }
        }
    }

    protected function insertar_respuesta_secreta($respuesta_secreta, $usuario){
        $insercion = $this->sql_con->prepare("INSERT INTO usuario_respuesta_secreta (usuario_respuesta_secreta, usuario_id) VALUES (?, ?)");
        $insercion->bind_param('si',
                              $respuesta_secreta,
                              $usuario);
        $insercion->execute();
        $afectadas = $this->sql_con->affected_rows;
        $insercion->close();
        if ($afectadas > 0) {
            return true;
        }else{
            return false;
        }
    }

    public function editar_respuesta_secreta($respuesta_secreta, $usuario){
        if(!$this->existe_respuesta_secreta($usuario)) {
            $this->insertar_respuesta_secreta($respuesta_secreta, $usuario);
        } else {
            $stmt = $this->sql_con->prepare("UPDATE usuario_respuesta_secreta SET usuario_respuesta_secreta = ? WHERE usuario_id = ?");
            $stmt->bind_param('si',
                             $respuesta_secreta,
                             $usuario);
            $stmt->execute();
            $afectadas = $this->sql_con->affected_rows;
            $stmt->close();
        }
        return $afectadas;
    }

    protected function editar_usuario_sexo($sexo, $usuario){
        $stmt = $this->sql_con->prepare("INSERT INTO usuario_sexo (usuario_sexo_id, sexo_id, usuario_id) VALUES (?, ?, ?)  ON DUPLICATE KEY UPDATE sexo_id = ?");
        $stmt->bind_param('iiii', $usuario, $sexo, $usuario, $sexo);
        $stmt->execute();
        $afectadas = $this->sql_con->affected_rows;
        $stmt->close();
        return $afectadas;
    }

    protected function editar_usuario_fecha_nacimiento($fecha, $usuario){
        $fecha = date('Y-m-d', strtotime($fecha));
        $stmt = $this->sql_con->prepare("INSERT INTO usuario_edad (usuario_edad_id, usuario_id, usuario_fecha_nacimiento) VALUES (?, ?, ?)  ON DUPLICATE KEY UPDATE usuario_fecha_nacimiento = ?");
        $stmt->bind_param('iiss', $usuario, $usuario, $fecha, $fecha);
        $stmt->execute();
        $afectadas = $this->sql_con->affected_rows;
        $stmt->close();
        return $afectadas;
    }

    public function editar_usuario($usuario, $datos){
        $cont = 0;
        $datos = $this->limpiar_datos($datos);
        if($datos['respuesta_secreta'] != '') {
            if($this->editar_respuesta_secreta($datos['respuesta_secreta'], $usuario) > 0){
                $cont++;
            }
        }
        if ($datos['u_sexo'] != 0) {
            if($this->editar_usuario_sexo($datos['u_sexo'], $usuario) > 0){
                $cont++;
            }
        }
        if ($datos['fecha_nacimiento'] != '') {
            if($this->editar_usuario_fecha_nacimiento($datos['fecha_nacimiento'], $usuario) > 0){
                $cont++;
            }
        }
        $consulta = "UPDATE usuario SET ";
        $i = 0;
        foreach($datos as $indice=>$valor){
            if($valor != '' && $valor != '0' && $indice != 'respuesta_secreta' && $indice != 'u_sexo' && $indice != 'fecha_nacimiento'){
                if ($i > 0) {
                    $consulta .= ', ';
                }
                $consulta .= $indice . ' = "' . $valor . '"';
                $i++;
            }

        }
        $consulta .= " WHERE usuario_id = $usuario";
        $rs = $this->sql_con->query($consulta);
        if($rs != false) {
            $afectadas = $this->sql_con->affected_rows;
            if($afectadas > 0){
                $cont++;
            }
        } else {
            trigger_error('Wrong SQL: ' . $consulta . ' Error: ' . $this->sql_con->error, E_USER_ERROR);
        }
        if ($cont > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function obtener_datos_usuario($usuario){
        $consulta = "SELECT usuario_nombres as u_nombres, usuario_apellidos as u_apellidos, usuario_direccion as u_direccion, usuario_mail as u_email FROM usuario WHERE usuario_id = $usuario";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return false;
        }else{
            $row = $rs->fetch_assoc();
            return $row;
        }
    }

    public function obtener_datos_empresa($empresa){
        $consulta = "SELECT e.empresa_nombre as nombre, e.empresa_rut as rut, e.empresa_direccion as direccion, ec.empresa_telefono as telefono, ec.empresa_correo as mail FROM empresa e LEFT JOIN empresa_contacto ec ON e.empresa_id = ec.empresa_id WHERE e.empresa_id = $empresa";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return false;
        }else{
            $row = $rs->fetch_assoc();
            return $row;
        }
    }

    public function __destruct(){
    }
}

?>
