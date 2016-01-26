<?php

$cuenta = new Cuentas();

class Cuentas{

    protected $link, $sql_con;
    protected $accion, $folio, $host_anterior = -1;
    protected $datos_cuenta = array(), $datos = array();
    protected $nueva_empresa = null, $nuevo_host, $usu_id = null, $login, $password, $empresa, $avatar;
    protected $rollback = array(), $operacionCancelada = false;
    protected $registro_nuevo = false;

    public function __construct(){
        session_start();
        set_time_limit(160);
        
        require('../hosts.php');
        require('conexion_new.php');
        require('sanear_string.php');
        /*if($_SESSION['tipo_cuenta'] != 1){
            $this->datos['resultado'] = 2;
            exit();
        }*/
        $this->set_host(1);
        $this->procesar();
    }
    
    protected function set_host($host){
        if($host != $this->host_anterior){
            $hosteo = new Host();
            $hosteo->obtener_conexion($host);
            $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        }
        $this->host_anterior = $host;
    }

    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    
    protected function registrar_folio(){
        $this->set_host(1);
        $insercion = $this->sql_con->prepare("INSERT INTO folio (folio_fecha) VALUES (?)");
        $fecha = date('Y-m-d H:i:s');
        $insercion->bind_param('s', $fecha);
        $insercion->execute();
        $insercion->close();
    }
    
    protected function procesar(){
        $this->accion = $_POST['accion'];
        switch($this->accion){
            case 1:
                $this->set_folio();
                if(!$this->verificar_cuenta()){
                    $this->activar();
                }
                break;
            case 2:
                $this->nuevo_usuario();
                break;
            case 3:
                $this->registro_nuevo = true;
                $email = mysqli_real_escape_string($this->sql_con, $_POST['email']);
                $pass = mysqli_real_escape_string($this->sql_con, $_POST['pass']);
                $this->procesar_datos($email, $pass);
                $this->registrar_folio();
                $this->activar();
                
                break;
        }
    }
    
    protected function nuevo_usuario(){
        $this->empresa = $_SESSION['empresa'];
        $this->sucursal = $_SESSION['sucursal'];
        $this->datos_cuenta['cli_nombre'] = mysqli_real_escape_string($this->sql_con, $_POST['nombres']);
        $this->datos_cuenta['cli_app'] = mysqli_real_escape_string($this->sql_con, $_POST['apellido_paterno']);
        $this->datos_cuenta['cli_apm'] = mysqli_real_escape_string($this->sql_con, $_POST['apellido_materno']);
        $this->datos_cuenta['cli_mail'] = mysqli_real_escape_string($this->sql_con, $_POST['email']);
        $this->datos_cuenta['cli_rut'] = mysqli_real_escape_string($this->sql_con, $_POST['pass']);

        $this->datos_cuenta['dir_direccion'] = mysqli_real_escape_string($this->sql_con, $_POST['direccion']);
        $this->datos_cuenta['comuna'] = mysqli_real_escape_string($this->sql_con, $_POST['comuna']);
        $this->datos_cuenta['region'] = mysqli_real_escape_string($this->sql_con, $_POST['region']);
        $this->datos_cuenta['pais'] = mysqli_real_escape_string($this->sql_con, $_POST['pais']);
        $this->datos_cuenta['pregunta'] = mysqli_real_escape_string($this->sql_con, $_POST['pregunta']);
        $this->nueva_empresa = $_SESSION['empresa'];
        $tipo_cuenta = mysqli_real_escape_string($this->sql_con, $_POST['tipo_cuenta']);
        $this->datos_cuenta['rol'] = mysqli_real_escape_string($this->sql_con, $_POST['rol']);
        $this->login = mysqli_real_escape_string($this->sql_con, $_POST['usuario']);
        
        
        if($this->login == ''){
            exit();
        }
        if(!$this->verificar_login(1, $this->login)){
            if($_FILES['avatar']['name'] != ''){
                $this->avatar = $this->subir_imagen();
            } else {
                $this->avatar = 'src/user.png';
            }
            if($this->crear_usuario(1)){
                if($this->crear_pass(1)){
                    if($this->relacionar_usuario_empresa(1)){
                        if($this->relacionar_usuario_rol(1, $this->datos_cuenta['rol'])){
                            if($this->relacionar_usuario_sucursal(1, $this->sucursal, $tipo_cuenta)){
                                if($this->crear_usuario(0)){
                                    if($this->crear_pass(0)){
                                        if($this->relacionar_usuario_empresa(0)){
                                            if($this->relacionar_usuario_sucursal(0, $this->sucursal, $tipo_cuenta)){
                                                
                                                $this->datos['resultado'] = 1;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $this->datos['resultado'] = 3; //Usuario ya existe
            $nuevo_login = $this->existe_login($this->login, 1);
            $this->datos['sugerido'] = $nuevo_login;
        }
    }
    
    protected function verificar_login($host, $login){
        $this->set_host($host);
        $consulta = "SELECT count(*) as cont FROM usuario WHERE usuario_login = '$login'";
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
    
    protected function verificar_cuenta(){
        $this->set_host(7);
        $consulta = "SELECT ingreso_estado as estado FROM ingreso_estado WHERE ingreso_id = (SELECT ing_id FROM ingreso WHERE ing_nmr_folio = '$this->folio')";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            trigger_error("Ha ocurrido un error");
        }else{
            $row = $rs->fetch_assoc();
            if($row['estado'] == 0){
                return false;
            }else{
                return true;
            }
        }
    }
    
    protected function verificar_bd(){
        $this->set_host(1);
        $consulta = "SELECT count(*) as cont FROM empresa_conexion WHERE bd = 'nfn_web_$this->folio'";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            $resultado = 0;
        }else{
            $row = $rs->fetch_assoc();
            if($row['cont'] > 0){
                $resultado = 0;
            }else{
                $resultado = 1;
            }
        }
        if($resultado == 0){
            $this->datos['resultado'] = array();
            $dato = array();
            $dato['resultado'] = 0;
            $dato['codigo'] = 15;
            array_push($this->datos['resultado'], $dato);
            exit();
        }
    }
    
    protected function activar(){
        if(!$this->registro_nuevo){
            $this->obtener_datos(7);    
        }
        $resultados_pasos = array();
        $this->verificar_bd();
        if($this->crear_bd()){
            $resultado = array();
            $resultado['resultado'] = 1;
            $resultado['codigo'] = 14;
            array_push($resultados_pasos, $resultado);
            if($this->insertar_empresa(1)){
                $resultado = array();
                $resultado['resultado'] = 1;
                $resultado['codigo'] = 13;
                array_push($resultados_pasos, $resultado);
                if($this->crear_conex_empresa(1)){
                    $resultado = array();
                    $resultado['resultado'] = 1;
                    $resultado['codigo'] = 12;
                    array_push($resultados_pasos, $resultado);
                    if($this->registro_nuevo){
                        $this->login = $this->datos_cuenta['cli_mail'];
                    }else{
                        $this->login = sanear_string(substr($this->datos_cuenta['cli_nombre'], 0, 1));
                        $this->login .= sanear_string($this->datos_cuenta['cli_app']);
                        $this->login = strtolower($this->existe_login($this->login, 1));
                    }
                    if($this->crear_usuario(1)){
                        $resultado = array();
                        $resultado['resultado'] = 1;
                        $resultado['codigo'] = 11;
                        array_push($resultados_pasos, $resultado);
                        if($this->crear_pass(1)){
                            $resultado = array();
                            $resultado['resultado'] = 1;
                            $resultado['codigo'] = 10;
                            array_push($resultados_pasos, $resultado);
                            if($this->relacionar_usuario_empresa(1)){
                                $resultado = array();
                                $resultado['resultado'] = 1;
                                $resultado['codigo'] = 9;
                                array_push($resultados_pasos, $resultado);
                                if($this->relacionar_usuario_rol(1, 1)){
                                    $resultado = array();
                                    $resultado['resultado'] = 1;
                                    $resultado['codigo'] = 8;
                                    array_push($resultados_pasos, $resultado);
                                    if($this->relacionar_usuario_sucursal(1, 1, 1)){
                                        $resultado = array();
                                        $resultado['resultado'] = 1;
                                        $resultado['codigo'] = 7;
                                        array_push($resultados_pasos, $resultado);
                                        if($this->insertar_empresa($this->nuevo_host)){
                                            $resultado = array();
                                            $resultado['resultado'] = 1;
                                            $resultado['codigo'] = 6;
                                            array_push($resultados_pasos, $resultado);
                                            if($this->crear_usuario($this->nuevo_host)){
                                                $resultado = array();
                                                $resultado['resultado'] = 1;
                                                $resultado['codigo'] = 5;
                                                array_push($resultados_pasos, $resultado);
                                                if($this->crear_pass($this->nuevo_host)){
                                                    $resultado = array();
                                                    $resultado['resultado'] = 1;
                                                    $resultado['codigo'] = 4;
                                                    array_push($resultados_pasos, $resultado);
                                                    if($this->relacionar_usuario_empresa($this->nuevo_host)){
                                                        $resultado = array();
                                                        $resultado['resultado'] = 1;
                                                        $resultado['codigo'] = 3;
                                                        array_push($resultados_pasos, $resultado);
                                                        if($this->relacionar_usuario_sucursal($this->nuevo_host, 1, 1)){
                                                            $resultado = array();
                                                            $resultado['resultado'] = 1;
                                                            $resultado['codigo'] = 2;
                                                            array_push($resultados_pasos, $resultado);
                                                            if($this->crear_sucursal($this->nuevo_host, $this->nueva_empresa, "SUCURSAL WEB", 1)){
                                                                if(!$this->registro_nuevo){
                                                                    $this->crear_datos_contacto(0);
                                                                    $this->crear_datos_contacto(1);
                                                                    $this->actualizar_cuenta(7, 1);
                                                                }
                                                                $resultado = array();
                                                                $resultado['resultado'] = 1;
                                                                $resultado['codigo'] = 1;
                                                                array_push($resultados_pasos, $resultado);
                                                                $this->obtener_datos_acceso();
                                                                //$this->enviar_correo();
                                                            }else{
                                                                $resultado = array();
                                                                $resultado['codigo'] = 1;
                                                                $resultado['resultado'] = $this->preparar_eliminacion($resultado['codigo']);
                                                                array_push($resultados_pasos, $resultado);
                                                            }
                                                        }else{
                                                            $resultado = array();
                                                            $resultado['codigo'] = 2;
                                                            $resultado['resultado'] = $this->preparar_eliminacion($resultado['codigo']);
                                                            array_push($resultados_pasos, $resultado);
                                                        }
                                                    }else{
                                                        $resultado = array();
                                                        $resultado['codigo'] = 3;
                                                        $resultado['resultado'] = $this->preparar_eliminacion($resultado['codigo']);
                                                        array_push($resultados_pasos, $resultado);
                                                    }
                                                }else{
                                                    $resultado = array();
                                                    $resultado['codigo'] = 4;
                                                    $resultado['resultado'] = $this->preparar_eliminacion($resultado['codigo']);
                                                    array_push($resultados_pasos, $resultado);
                                                }
                                            }else{
                                                $resultado = array();
                                                $resultado['codigo'] = 5;
                                                $resultado['resultado'] = $this->preparar_eliminacion($resultado['codigo']);
                                                array_push($resultados_pasos, $resultado);
                                            }
                                        }else{
                                            $resultado = array();
                                            $resultado['codigo'] = 6;
                                            $resultado['resultado'] = $this->preparar_eliminacion($resultado['codigo']);
                                            array_push($resultados_pasos, $resultado);
                                        }
                                    }else{
                                        $resultado = array();
                                        $resultado['codigo'] = 7;
                                        $resultado['resultado'] = $this->preparar_eliminacion($resultado['codigo']);
                                        array_push($resultados_pasos, $resultado);
                                    }
                                }else{
                                    $resultado = array();
                                    $resultado['codigo'] = 8;
                                    $resultado['resultado'] = $this->preparar_eliminacion($resultado['codigo']);
                                    array_push($resultados_pasos, $resultado);
                                }
                            }else{
                                $resultado = array();
                                $resultado['codigo'] = 9;
                                $resultado['resultado'] = $this->preparar_eliminacion($resultado['codigo']);
                                array_push($resultados_pasos, $resultado);
                            }
                        }else{
                            $resultado = array();
                            $resultado['codigo'] = 10;
                            $resultado['resultado'] = $this->preparar_eliminacion($resultado['codigo']);
                            array_push($resultados_pasos, $resultado);
                        }
                    }else{
                        $resultado = array();
                        $resultado['codigo'] = 11;
                        $resultado['resultado'] = $this->preparar_eliminacion($resultado['codigo']);
                        array_push($resultados_pasos, $resultado);
                    }
                }else{
                    $resultado = array();
                    $resultado['codigo'] = 12;
                    $resultado['resultado'] = $this->preparar_eliminacion($resultado['codigo']);
                    array_push($resultados_pasos, $resultado);
                }
            }else{
                $resultado = array();
                $resultado['codigo'] = 13;
                $resultado['resultado'] = $this->preparar_eliminacion($resultado['codigo']);
                array_push($resultados_pasos, $resultado);
            }
        }else{
            $resultado = array();
            $resultado['codigo'] = 14;
            $resultado['resultado'] = $this->preparar_eliminacion($resultado['codigo']);
            array_push($resultados_pasos, $resultado);
        }
        $this->comprobar_resultado($resultados_pasos);
    }
    
    protected function preparar_eliminacion($codigo){
        $resultado = 0;
        $array_consultas = [
            "DELETE FROM usuario_sucursal WHERE usuario_id = $this->usu_id",
            "DELETE FROM usuario_rol WHERE usuario_id = $this->usu_id",
            "DELETE FROM usuario_empresa WHERE usuario_id = $this->usu_id AND empresa_id = $this->nueva_empresa",
            "DELETE FROM usuario_pass WHERE usuario_id = $this->usu_id",
            "DELETE FROM usuario WHERE usuario_id = $this->usu_id",
            "DELETE FROM empresa_conexion WHERE empresa_id = $this->nueva_empresa",
            "DELETE FROM empresa WHERE empresa_id = $this->nueva_empresa",
            "DROP DATABASE nfn_web_$this->folio"
        ];
        switch($codigo){
            case 1:
                $consulta_eliminar = $array_consultas[0];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[1];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[2];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[3];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[4];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[5];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[6];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[7];
                $this->eliminar($consulta_eliminar, 1);
                $resultado = 0;
                break;
            case 2:
                $consulta_eliminar = $array_consultas[0];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[1];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[2];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[3];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[4];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[5];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[6];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[7];
                $this->eliminar($consulta_eliminar, 1);
                $resultado = 0;
                break;
            case 3:
                $consulta_eliminar = $array_consultas[0];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[1];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[2];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[3];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[4];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[5];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[6];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[7];
                $this->eliminar($consulta_eliminar, 1);
                $resultado = 0;
                break;
            case 4:
                $consulta_eliminar = $array_consultas[0];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[1];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[2];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[3];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[4];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[5];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[6];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[7];
                $this->eliminar($consulta_eliminar, 1);
                $resultado = 0;
                break;
            case 5:
                $consulta_eliminar = $array_consultas[0];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[1];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[2];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[3];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[4];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[5];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[6];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[7];
                $this->eliminar($consulta_eliminar, 1);
                $resultado = 0;
                break;
            case 6:
                $consulta_eliminar = $array_consultas[0];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[1];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[2];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[3];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[4];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[5];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[6];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[7];
                $this->eliminar($consulta_eliminar, 1);
                $resultado = 0;
                break;
            case 7:
                $consulta_eliminar = $array_consultas[1];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[2];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[3];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[4];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[5];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[6];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[7];
                $this->eliminar($consulta_eliminar, 1);
                $resultado = 0;
                break;
            case 8:
                $consulta_eliminar = $array_consultas[2];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[3];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[4];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[5];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[6];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[7];
                $this->eliminar($consulta_eliminar, 1);
                $resultado = 0;
                break;
            case 9:
                $consulta_eliminar = $array_consultas[3];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[4];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[5];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[6];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[7];
                $this->eliminar($consulta_eliminar, 1);
                $resultado = 0;
                break;
            case 10:
                $consulta_eliminar = $array_consultas[4];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[5];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[6];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[7];
                $this->eliminar($consulta_eliminar, 1);
                $resultado = 0;
                break;
            case 11:
                $consulta_eliminar = $array_consultas[5];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[6];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[7];
                $this->eliminar($consulta_eliminar, 1);
                $resultado = 0;
                break;
            case 12:
                $consulta_eliminar = $array_consultas[6];
                $this->eliminar($consulta_eliminar, 1);
                $consulta_eliminar = $array_consultas[7];
                $this->eliminar($consulta_eliminar, 1);
                $resultado = 0;
                break;
            case 13:
                $consulta_eliminar = $array_consultas[7];
                $this->eliminar($consulta_eliminar, 1);
                $resultado = 0;
                break;
            case 14:
                $consulta_eliminar = $array_consultas[7];
                $this->eliminar($consulta_eliminar, 1);
                $resultado = 0;
                break;
        }
        return $resultado;
    }
    
    protected function comprobar_resultado($resultados_pasos){
        $resultado = 1;
        $codigo = '';
        for($i = 0; $i < count($resultados_pasos); $i++){
            if($resultados_pasos[$i]['resultado'] == 0){
                $resultado = 0;
                $codigo = $resultados_pasos[$i]['codigo'];
                break;
            }
        }
        $this->datos['resultado'] = array();
        $dato = array();
        $dato['resultado'] = $resultado;
        $dato['codigo'] = $codigo;
        array_push($this->datos['resultado'], $dato);
        $this->set_host(27);
        if(isset($_POST['social'])) {
            $social = mysqli_real_escape_string($this->sql_con, $_POST['social']);
        }else{
            $social = 1;
        }
        $this->log_registro($this->usu_id, $this->nueva_empresa, $social);
    }
    
    protected function insertar_usuario_empresa($user, $empresa){
        $insercion = $this->sql_con->prepare("INSERT INTO usuario_empresa (usuario_id, empresa_id) VALUES (?, ?)");
        $insercion->bind_param('ii', $user, $empresa);
        $insercion->execute();
        $insercion->close();
    }
    
    public function log_registro($user, $empresa, $social){
        $this->insertar_usuario_empresa($user, $empresa);
        $this->log_login($user, $social);
    }
    
    public function log_login($user, $social){
        $stmt = $this->sql_con->prepare("INSERT INTO login (login_id, usuario_id, login_primer_fecha, login_ultima_fecha, login_count, login_social) VALUES(?, ?, NOW(), NOW(), 1, ?) ON DUPLICATE KEY UPDATE login_ultima_fecha=NOW(), login_count = login_count + 1, login_social = ?");
        $stmt->bind_param('iiii', $user, $user, $social, $social);
        $stmt->execute();
        $stmt->close();
    }
    
    protected function enviar_correo($user, $pass, $para){
        require('send_mail.php');
        $correo = new EnviarCorreo();
        $mensaje = "Estimado usuario NFN:<br>";
        $mensaje .= "Sus datos de acceso son:<br>";
        $datos_mensaje = "Username: $this->login:<br>";
        $datos_mensaje .= "Contraseña: $this->password:<br>";
        $mensaje .= $datos_mensaje;
        $mensaje .= "<br>Saludos.";
        $asunto = "Cuenta NFN.";
        $de = "soporte@imasdgroup.cl";
        $correo->enviar_correo($mensaje, $asunto, $this->datos_cuenta['cli_mail'], $de);
        $nombre = $this->datos_cuenta['cli_nombre'] . $this->datos_cuenta['cli_app'];
        $rut = $this->datos_cuenta['cli_rut'];
        $mensaje = "Los datos de acceso para el cliente $nombre ";
        $mensaje .= "de Rut $rut son:<br>";
        $mensaje .= $datos_mensaje;
        $mensaje .= "<br>Saludos.";
        $correo->enviar_correo($mensaje, $asunto, 'ivanvalenzuela@imasdgroup.cl', $de);
        //$correo->enviar_correo($mensaje, $asunto, 'alejandrogonzalez@nfn.cl', $de);
        $correo->enviar_correo($mensaje, $asunto, 'jaimellanos@imasdgroup.cl', $de);
    }
    
    protected function obtener_datos_acceso(){
        $this->datos['datos'] = array();
        $dato = array();
        $dato['user'] = $this->login;
        $dato['pass'] = $this->password;
        $dato['correo'] = $this->datos_cuenta['cli_mail'];
        $dato['nombre'] = $this->datos_cuenta['cli_nombre'];
        $dato['apellido'] = $this->datos_cuenta['cli_app'];
        array_push($this->datos['datos'], $dato);
    }
    
    protected function actualizar_cuenta($host, $nuevo_estado){
        $this->set_host($host);
        $ing_id = $this->datos_cuenta['ing_id'];
        $consulta = "UPDATE ingreso_estado SET ingreso_estado = $nuevo_estado WHERE ingreso_id = $ing_id";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return false;
        }else{
            $afectadas = $this->sql_con->affected_rows;
            if($afectadas > 0){
                $this->datos['estado_cuenta'] = array();
                $dato = array();
                $dato['estado_nuevo'] = $nuevo_estado;
                array_push($this->datos['estado_cuenta'], $dato);
                return true;
            }else{
                return false;
            }
        }
    }
    
    protected function crear_sucursal($host, $empresa, $direccion, $sucursal){
        $this->set_host($host);
        $insercion = $this->sql_con->prepare("INSERT INTO sucursal (sucursal_id, empresa_id, sucursal_direccion, sucursal_comuna, sucursal_ciudad, sucursal_region, sucursal_pais) VALUES (?, ?, ?, 1, 1, 1, 1)");
        $insercion->bind_param('iis',
                               $sucursal,
                               $empresa,
                               $direccion);
        $insercion->execute();
        $afectadas = $this->sql_con->affected_rows;
        $insercion->close();
        if($afectadas > 0){
            return true;
        }else{
            return false;
        }
    }
    
    protected function crear_usuario($host){
        $this->set_host($host);
        $insercion = $this->sql_con->prepare("INSERT INTO usuario (usuario_id, usuario_nombres, usuario_apellidos, usuario_mail, usuario_direccion, usuario_avatar, usuario_comuna, usuario_ciudad, usuario_region, usuario_pais, usuario_login, usuario_pregunta_secreta) VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?, ?, ?, ?)");
        $apellidos = $this->datos_cuenta['cli_app'] . ' ' . $this->datos_cuenta['cli_apm'];
        $insercion->bind_param('isssssiiisi',
            $this->usu_id,
            $this->datos_cuenta['cli_nombre'],
            $apellidos,
            $this->datos_cuenta['cli_mail'],
            $this->datos_cuenta['dir_direccion'],
            $this->avatar,
            $this->datos_cuenta['comuna'],
            $this->datos_cuenta['region'],
            $this->datos_cuenta['pais'],
            $this->login,
            $this->datos_cuenta['pregunta']     
        );
        $insercion->execute();
        $this->usu_id = mysqli_insert_id($this->sql_con);
        $afectadas = $this->sql_con->affected_rows;
        $insercion->close();
        if($afectadas > 0){
            return true;
        }else{
            return false;
        }
    }
    
    protected function crear_pass($host){
        $this->set_host($host);
        $insercion = $this->sql_con->prepare("INSERT INTO usuario_pass (usuario_id, usuario_pass) VALUES (?, ?)");
        if($this->registro_nuevo){
            $pass = md5($this->datos_cuenta['pass']);
        }else{
            $pass = $this->datos_cuenta['cli_rut'];
            while(strrpos($pass, ".") != ''){
                $pass = str_replace(".", "", $pass);
            }
            $pass = str_replace("-", "", $pass);
            $this->password = $pass;
            $pass = md5($pass);
        }
        $insercion->bind_param('is',
                               $this->usu_id,
                               $pass);
        $insercion->execute();
        $afectadas = $this->sql_con->affected_rows;
        $insercion->close();
        if($afectadas > 0){
            return true;
        }else{
            return false;
        }
    }
    
    protected function relacionar_usuario_empresa($host){
        $this->set_host($host);
        $insercion = $this->sql_con->prepare("INSERT INTO usuario_empresa (usuario_id, empresa_id) VALUES (?, ?)");
        $insercion->bind_param('ii',
                               $this->usu_id,
                               $this->nueva_empresa);
        $insercion->execute();
        $afectadas = $this->sql_con->affected_rows;
        $insercion->close();
        if($afectadas > 0){
            return true;
        }else{
            return false;
        }
    }
    
    protected function relacionar_usuario_rol($host, $rol){
        $this->set_host($host);
        $insercion = $this->sql_con->prepare("INSERT INTO usuario_rol (usuario_id, rol_id) VALUES (?, ?)");
        $insercion->bind_param('ii',
                               $this->usu_id,
                               $rol);
        $insercion->execute();
        $afectadas = $this->sql_con->affected_rows;
        $insercion->close();
        if($afectadas > 0){
            return true;
        }else{
            return false;
        }
    }
    
    protected function relacionar_usuario_sucursal($host, $sucursal, $tipo_cuenta){
        $this->set_host($host);
        $insercion = $this->sql_con->prepare("INSERT INTO usuario_sucursal (usuario_id, sucursal_id, usuario_sucursal_tipo_cuenta) VALUES (?, ?, ?)");
        $insercion->bind_param('iii',
                               $this->usu_id,
                               $sucursal,
                               $tipo_cuenta
                              );
        $insercion->execute();
        $afectadas = $this->sql_con->affected_rows;
        $insercion->close();
        if($afectadas > 0){
            return true;
        }else{
            return false;
        }
    }
    
    protected function existe_login($login, $host){
        $this->set_host($host);
        $consulta = "SELECT count(*) as cont FROM usuario WHERE usuario_login LIKE '$login%'";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return $login;
        }else{
            $row = $rs->fetch_assoc();
            if($row['cont'] > 0){
                return $login . $row['cont'];
            }else{
                return $login;
            }
        }
    }
    
    protected function crear_conex_empresa($host){
        $this->set_host($host);
        $insercion_conexion = $this->sql_con->prepare("INSERT INTO empresa_conexion (empresa_id, host, bd) VALUES (?, null, ?)");
        $bd = "nfn_web_" . $this->folio;
        $insercion_conexion->bind_param('is', $this->nueva_empresa, $bd);
        $insercion_conexion->execute();
        $this->nuevo_host = mysqli_insert_id($this->sql_con);
        $afectadas = $this->sql_con->affected_rows;
        $insercion_conexion->close();
        if($afectadas > 0){
            return true;
        }else{
            return false;
        }
        
    }
    
    protected function crear_datos_contacto($host){
        $this->set_host($host);
        $insercion = $this->sql_con->prepare("INSERT INTO empresa_contacto (empresa_telefono, empresa_correo) VALUES (?, ?)");
        $insercion->bind_param('ss', $this->datos_cuenta['con_tmovil'], $this->datos_cuenta['con_mail']);
        $insercion->execute();
        $afectadas = $this->sql_con->affected_rows;
        $insercion->close();
        if($afectadas > 0){
            return true;
        }else{
            return false;
        }
    }
    
    protected function crear_bd(){
        $consulta = "CREATE DATABASE nfn_web_$this->folio";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return false;
        }else{
            
            $this->set_conexion('localhost', 'root', '@DatabasePlatform2015', "nfn_web_$this->folio");
            require('nfn_script.php');
            $sql = explode(';', $consulta = get_script());
            foreach($sql as $consulta){
                if($consulta != ''){
                    $rs2 = $this->sql_con->query($consulta);
                    if($rs2 === false){
                        return false;
                    }
                }
            }
            $this->set_conexion('localhost', 'root', '@DatabasePlatform2015', "nfn_admin");
            return true;
        }
    }
    
    protected function obtener_folio(){
        $this->set_host(1);
        $consulta = "SELECT folio_id as f_id FROM folio ORDER BY folio_id DESC LIMIT 1";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            $this->folio = -1;
        }else{
            $row = $rs->fetch_assoc();
            $this->folio = $row['f_id'];
        }
    }
        
    protected function verificar_email($email){
        $this->set_host(1);
        $consulta = "SELECT count(*) as cont FROM usuario WHERE usuario_login = '$email'";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return false;
        }else{
            $row = $rs->fetch_assoc();
            if($row['cont'] > 0){
                return false;
            }else{
                return true;
            }
        }
    }
    
    protected function procesar_datos($email, $pass){
        if(!$this->verificar_email($email)){
            $this->datos['resultado'] = array();
            $dato = array();
            $dato['resultado'] = 16;
            $dato['codigo'] = 16;
            array_push($this->datos['resultado'], $dato);
            exit();
        }
        $this->datos_cuenta['cli_id'] = null;
        $this->datos_cuenta['cli_tipo_ciente'] = 1;
        $this->datos_cuenta['cli_app'] = 'Apellido paterno';
        $this->datos_cuenta['cli_apm'] = 'Apellido materno';
        $this->datos_cuenta['cli_nombre'] = 'Nombre';
        $this->datos_cuenta['cli_rut'] = 'Rut';
        $this->datos_cuenta['cli_mail'] = $email;
        $this->datos_cuenta['pass'] = $pass;
        $this->datos_cuenta['cli_actividad'] = 'Actividad de la empresa';
        $this->datos_cuenta['cli_rsocial'] = 'Razón social';
        $this->datos_cuenta['cli_fantasia'] = 'Nombre de fantasía';
        $this->datos_cuenta['cli_mail_emp'] = $email;
        $this->datos_cuenta['cli_rut_emp'] = 'Rut empresa';
        $this->datos_cuenta['cli_rep_legal'] = 'Representante legal';
        $this->datos_cuenta['cli_rut_rep'] = 'Rut representante legal';
        $this->datos_cuenta['cli_giro'] = 'Giro de la empresa';
        $this->obtener_folio();
        if($this->folio == -1){
            $this->folio = (md5($email . date('Y-m-d H:i:s')));
        }else{
            $this->folio++;
        }
        $this->datos_cuenta['ing_nmr_folio'] = $this->folio;
        $this->datos_cuenta['con_mail'] = $email;
        $this->datos_cuenta['con_tmovil'] = '1111111';
        $this->datos_cuenta['con_tfijo'] = '1111111';
        $this->datos_cuenta['dir_direccion'] = 'Dirección';
    }
    
    protected function obtener_datos($host){
        $this->set_host($host);
        $consulta = "SELECT * FROM cliente c LEFT JOIN ingreso i ON i.ing_rut_usu = c.cli_rut";
        $consulta.=" LEFT JOIN contacto co ON co.con_cli_rut=c.cli_rut";
        $consulta.=" LEFT JOIN servicio s ON s.serv_cli_rut=c.cli_rut";
        $consulta.=" LEFT JOIN direccion d ON d.dir_cli_rut=c.cli_rut WHERE i.ing_nmr_folio = '$this->folio'";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return false;
        }else{
            $row = $rs->fetch_assoc();
            foreach($row as $indice=>$dato){
                $this->datos_cuenta[$indice] = $dato;
            }
            return true;
        }
    }
    
    protected function insertar_empresa($host){
        $this->set_host($host);
        $consulta = "INSERT INTO empresa (empresa_id, empresa_nombre, empresa_rut, empresa_direccion, empresa_comuna, empresa_ciudad, empresa_region, empresa_pais, categoria_id) VALUES (?, ?, ?, ?, 1, 1, 1, 1, 1)";
        $insercion = $this->sql_con->prepare($consulta);
        $insercion->bind_param('isss', 
            $this->nueva_empresa,
            $this->datos_cuenta['cli_fantasia'],
            $this->datos_cuenta['cli_rut_emp'],
            $this->datos_cuenta['dir_direccion']);
        $insercion->execute();
        $afectadas = $this->sql_con->affected_rows;
        $this->nueva_empresa = mysqli_insert_id($this->sql_con);
        $insercion->close();
        if($afectadas > 0){
            return true;
        }else{
            return false;
        }
    }
    
            
    protected function subir_imagen(){
        if($this->registro_nuevo){
            return true;
        }
        $imagen = $_FILES['avatar']['name'];
        $directorio = "src/avatar_usuarios/";
        $fecha = date('d.m.Y_H.i.s');
        $nombre_archivo = "avatar_".$fecha;
        $i = strlen($imagen) - 1;
        $extension = '';
        while($imagen[$i] != '.'){
            $extension = $imagen[$i] . $extension;
            $i--;
        }
        $extension = strtolower($extension);
        $nombre_archivo = $nombre_archivo . '.' . $extension;
        $new_images = $nombre_archivo;
        $images = $_FILES['avatar']['tmp_name'];
        switch($extension){
            case "jpg": case "jpeg":
                $thumb = ImageCreateFromJPEG($images);
                break;
            case "gif":
                $thumb = imagecreatefromgif($images);
                break;
            case "png":
                try {
                    $thumb = imagecreatefrompng($images);
                }catch (Exception $e){
                    $this->resultado = -1;
                    exit();
                }

                break;
            default:
                $this->resultado = 2;
                exit();
                break;
        }
        copy($images,'../'.$directorio.$nombre_archivo);
        $width=300;
        $size=GetimageSize($images);
        if($size != 0){
            $height=round($width*$size[1]/$size[0]);
            //$height = 150;
        }
        $newwidth = 150;
        $newheight = $height;


        $srcWidth = imagesx($thumb);
        $srcHeight = imagesy($thumb);

        $newImg = imagecreatetruecolor($width, $height);
        imagealphablending($newImg, false);
        imagesavealpha($newImg,true);
        $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
        imagefilledrectangle($newImg, 0, 0, $width, $height, $transparent);
        imagecopyresampled($newImg, $thumb, 0, 0, 0, 0, $width, $height,
            $srcWidth, $srcHeight);

        imagepng($newImg,'../'.$directorio.$new_images);

        ImageDestroy($thumb);
        if(!is_dir('../'.$directorio)) 
            mkdir('../'.$directorio, 0777);

        return mysqli_real_escape_string($this->sql_con, $directorio.$nombre_archivo);
    }
    
    protected function eliminar($consulta, $host){
        $this->operacionCancelada = true;
        $this->set_host($host);
        $eliminar = $this->sql_con->prepare($consulta);
        $eliminar->execute();
        $eliminadas = $this->sql_con->affected_rows;
        $eliminar->close();
        if($eliminadas > 0){
            $resultado = 1; //Se pudo hacer rollback
        }else{
            $resultado = 0; //No se pudo hacer rollback
        }
        array_push($this->rollback, $resultado);
    }
    
    protected function set_folio(){
        $this->folio = mysqli_real_escape_string($this->sql_con, $_POST['cId']);
    }
    
    public function __destruct(){
        echo json_encode($this->datos);
    }

}

?>