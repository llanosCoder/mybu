<?php

$categoria = new Categoria();

Class Categoria{

    protected $link, $sql_con;
    protected $categoria = array();
    protected $resultado = array();
    protected $usuario, $cat_id = 0;

    public function __construct(){
        session_start();
        require('../hosts.php');
        ini_set('display_errors', 'on');
        require('conexion_new.php');
        include('sanear_string.php');
        $this->set_host(1);
        $this->procesar();
    }
    
    protected function set_host($host){
        $hosteo = new Host();
        $hosteo->obtener_conexion($host);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
    }
    
    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    
    protected function procesar(){
        $this->usuario = $_SESSION['id'];
        $parametros = $_POST['parametros'];
        $this->categoria['nombre'] = mysqli_real_escape_string($this->sql_con, $parametros['nombre']);
        $this->categoria['padre'] = mysqli_real_escape_string($this->sql_con, $parametros['padre']);
        $this->es_global($this->categoria['nombre']);
        $this->categoria['padre'] = $this->get_id($this->categoria['padre'], 0);
        $dato = array();
        if($this->insertar_categoria(0)){
            //$this->asignar_marcas();
            $dato['resultado'] = 1;
        }else{
            $dato['resultado'] = 0;
        }
        $this->resultado = $dato;
    }
    
    protected function insertar_categoria($host){
        $this->set_host($host);
        $insercion = $this->sql_con->prepare("INSERT INTO categoria (categoria_nombre, categoria_descripcion, categoria_padre) VALUES (?, ?, ?)");
        $insercion->bind_param('ssi',
                              $this->categoria['nombre'],
                              $this->categoria['descripcion'],
                              $this->categoria['padre']);
        $insercion->execute();
        $insertadas = $this->sql_con->affected_rows;
        $insercion->close();
        $this->cat_id = mysqli_insert_id($this->sql_con);
        if($insertadas > 0){
            return true;
        }else{
            return false;
        }
    }
    
    protected function insertar_marca($categoria, $marca){
        $insercion = $this->sql_con->prepare("INSERT INTO categoria_marca (categoria_id, marca_id) VALUES (?, ?)");
        $insercion->bind_param('ii', $categoria, $marca);
        $insercion->execute();
        $afectadas = $this->sql_con->affected_rows;
        $insercion->close();
        
        if($afectadas > 0){
            return true;
        }else{
            return false;
        }
    }
    
    protected function asignar_marcas(){
        if($this->cat_id == 0){
            return false;
        }else{
            $padre_id = $this->categoria['padre'];
            $categoria = $this->cat_id;
            while($padre_id != 0){
                $consulta = "SELECT marca_id as marca, categoria_padre as padre FROM categoria_marca cm JOIN categoria c ON c.categoria_id = cm.categoria_id WHERE cm.categoria_id = $padre_id";
                $rs = $this->sql_con->query($consulta);
                if($rs === false){
                    break;
                }else{
                    while($row = $rs->fetch_assoc()){
                        $marca = $row['marca'];
                        $this->insertar_marca($categoria, $marca);
                        $categoria = $padre_id;
                        $padre_id = $row['padre'];
                    }
                }
            }
        }
    }
    
    protected function es_global($categoria){
        $this->set_host(1);
        $consulta = "SELECT categoria_descripcion as descripcion FROM categoria WHERE categoria_nombre = '$categoria'";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return false;
        }else{
            $row = $rs->fetch_assoc();
            if(mysqli_num_rows($rs) > 0){
                $this->categoria['descripcion'] = $row['descripcion'];
            }else{
                $nombre = sanear_string($categoria);
                if($this->verificar_categoria($nombre) > 0){
                    $fecha = date('Y-m-d H:i:s');
                    $this->categoria['descripcion'] = md5($fecha . $nombre);
                }else{
                    $this->categoria['descripcion'] = $nombre;
                }
            }
        }
    }
    
    protected function verificar_categoria($codigo){
        $this->set_host(0);
        $consulta_categoria = "SELECT count(*) as cont FROM categoria WHERE categoria_descripcion = '$codigo'";
        $rs_categoria = $this->sql_con->query($consulta_categoria);
        if($rs_categoria === false){
            echo $consulta_categoria;
        }else{
            $row_categoria = $rs_categoria->fetch_assoc();
            return $row_categoria['cont'];
        }
    }
    
    protected function get_id($categoria, $host){
        $this->set_host($host);
        $consulta = "SELECT categoria_id as id FROM categoria WHERE categoria_descripcion = '$categoria'";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return 0;
        }else{
            $row = $rs->fetch_assoc();
            if(mysqli_num_rows($rs) > 0){
                return $row['id'];    
            }else{
                return 0;
            }
            
        }
    }
    
    public function __destruct(){
        echo json_encode($this->resultado);
    }
}

?>