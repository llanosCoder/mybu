<?php

$categoria = new Categorias();

class Categorias{

    protected $link, $sql_con;
    protected $accion, $resultado, $motivo_rechazo, $id;

    public function __construct(){
        session_start();
        require('../hosts.php');
        require('conexion_new.php');
        $hosteo = new Host();
        $hosteo->obtener_conexion(1);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $this->procesar();
    }

    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }

    protected function procesar(){
        if($_SESSION['empresa'] == 1 && $_SESSION['tipo_cuenta'] == 1){
            $this->accion = $_POST['accion'];
            switch($this->accion){
                case 1:
                    if ($this->agregar_categoria()){
                        if($this->actualizar_categoria(1, '-')){
                            $this->resultado = 1;
                        }else{
                            $this->resultado = 2;
                        }
                    }else{
                        $this->resultado = 0;
                    }
                    break;
                case 2:
                    $this->id = mysqli_real_escape_string($this->sql_con, $_POST['id']);
                    $razon_rechazo = mysqli_real_escape_string($this->sql_con, $_POST['razon']);
                    if($this->actualizar_categoria(2,$razon_rechazo)){
                        $this->resultado = 1;
                    }else{
                        $this->resultado = 0;
                    }
                    break;
                default:
                    $this->resultado = 0;
                    break;
            }
        }else{
            $this->resultado = 3;
        }
    }

    protected function agregar_categoria(){
        $dato = array();
        $dato = $this->obtener_categoria();
        include('sanear_string.php');
        $codigo = sanear_string($dato['nombre']);
        if($this->verificar_categoria($codigo) > 0) {
            $fecha = date('Y-m-d H:i:s');
            $codigo = md5($fecha . $codigo);
        }
        $insercion = $this->sql_con->prepare("INSERT INTO categoria VALUES(null, ?, ?, ?)");
        $insercion->bind_param('ssi',
        $dato['nombre'],
        $codigo,
        $dato['padre']);
        $insercion->execute();
        $insertadas = $this->sql_con->affected_rows;
        if($insertadas > 0){
            return true;
        }else{
            return false;
        }
    }

    protected function actualizar_categoria($nuevo_estado, $razon){
        if(!isset($razon))
            $razon = '-';
        $actualizar = "UPDATE categoria_solicitud SET categoria_solicitud_estado = $nuevo_estado, categoria_solicitud_rechazo_razon = '$razon' WHERE categoria_solicitud_id = $this->id";
        if($this->sql_con->query($actualizar) === false) {
            trigger_error('Wrong SQL: ' . $actualizar . ' Error: ' . $this->sql_con->error, E_USER_ERROR);
        } else {
            $editadas = $this->sql_con->affected_rows;
            if($editadas > 0){
                return true;
            }else{
                return false;
            }
        }
    }

    protected function verificar_categoria($codigo){
        $consulta_categoria = "SELECT count(*) as cont FROM categoria WHERE categoria_descripcion = '$codigo'";
        $rs_categoria = $this->sql_con->query($consulta_categoria);
        if($rs_categoria === false){
            echo $consulta_categoria;
        }else{
            $row_categoria = $rs_categoria->fetch_assoc();
            return $row_categoria['cont'];
        }
    }

    protected function obtener_categoria(){
        $this->id = mysqli_real_escape_string($this->sql_con, $_POST['id']);
        $consulta_categoria = "SELECT categoria_solicitud_nombre as nombre, categoria_solicitud_padre as padre FROM categoria_solicitud WHERE categoria_solicitud_id = $this->id";
        $rs_categoria = $this->sql_con->query($consulta_categoria);
        if($rs_categoria === false){
            echo $consulta;
        }else{
            $row_categoria = $rs_categoria->fetch_assoc();
            return $row_categoria;
        }
    }

    public function __destruct(){
        echo $this->resultado;
    }

}

?>
