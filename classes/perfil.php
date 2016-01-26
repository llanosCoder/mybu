<?php

$perfil = new Perfil();

class Perfil{
        protected $link;
        protected $sql_con;
        protected $datos_usuario = array();
        protected $datos = array();
        protected $usuario;
        
    public function __construct(){
        session_start();
        ini_set('display_errors', 'off');
        require('../hosts.php');
        require('conexion_new.php');
        $this->set_host(0);
        $this->obtener_parametros();
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
    
    protected function obtener_parametros(){
        extract($_POST);
        $this->usuario = $_SESSION["id"];
        $this->datos_usuario['pass_actual'] = mysqli_real_escape_string($this->sql_con, $pass_actual);
        $this->datos_usuario['pass'] = mysqli_real_escape_string($this->sql_con, $pass_nuevo);
        $this->datos_usuario['pass2'] = mysqli_real_escape_string($this->sql_con, $pass_nuevo2);
        $this->datos_usuario['tipo'] = mysqli_real_escape_string($this->sql_con, $tipo);
        $this->datos_usuario['imagen'] = mysqli_real_escape_string($this->sql_con, $imagen);
        switch ($this->datos_usuario['tipo']){
            case 1:
                $this->password();
            break;
            case 2:
                $this->traer_avatar();    
            break;
            case 3:
                $this->guardar_avatar();
            break;
        }
    }
    
    protected function guardar_avatar(){
        if($_FILES["imagen"]["name"]==""){
            $this->datos["respuesta"] = 2 ;
        }else{
            $this->datos_usuario['imagen'] = $this->subir_imagen(); 
            if($this->datos["respuesta"] != 7)      
                $this->actualizar_avatar(0);
                $this->actualizar_avatar(1);
        }
    }

    protected function actualizar_avatar($host){
        $this->set_host($host);
        $remplazar_ruta = str_replace("../src/avatar_usuarios/","src/avatar_usuarios/",$this->datos_usuario["imagen"]);
        $agregar="update usuario set usuario_avatar='".$remplazar_ruta."' where usuario_id='".$this->usuario."' ";
        $enviar = $this->sql_con->query($agregar);
        if($enviar){
            $_SESSION["avatar"] = $remplazar_ruta ;
            $this->datos["respuesta"] = 1;
        }else{
            $this->datos["respuesta"] = 0;
        }
    }

    protected function get_comuna($comuna){
        $consulta = "SELECT comuna_nombre as comuna FROM comuna WHERE comuna_id = $comuna";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return '';
        }else{
            $row = $rs->fetch_assoc();
            return $row['comuna'];
        }
    }
    
    protected function get_region($region){
        $consulta = "SELECT region_nombre as region FROM region WHERE region_id = $region";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return '';
        }else{
            $row = $rs->fetch_assoc();
            return $row['region'];
        }
    }
    
    protected function get_pais($pais){
        $consulta = "SELECT pais_nombre as pais FROM pais WHERE pais_id = $pais";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return '';
        }else{
            $row = $rs->fetch_assoc();
            return $row['pais'];
        }
    }
    
    protected function get_pregunta_secreta($pregunta_secreta){
        $consulta = "SELECT pregunta_secreta_nombre as pregunta_secreta FROM pregunta_secreta WHERE pregunta_secreta_id = $pregunta_secreta";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return '';
        }else{
            $row = $rs->fetch_assoc();
            return $row['pregunta_secreta'];
        }
    }
    
    protected function traer_avatar(){
           $consulta="SELECT usuario_nombres as nombres, usuario_apellidos as apellidos, usuario_mail as mail, usuario_direccion as direccion, usuario_avatar as avatar, usuario_comuna as comuna, usuario_region as region, usuario_pais as pais, usuario_login as login, usuario_pregunta_secreta as pregunta_secreta FROM usuario WHERE usuario_id='$this->usuario'";
           $revisar = $this->sql_con->query($consulta);
           while($arr = $revisar->fetch_assoc()){
               foreach($arr as $indice=>$valor){
                   $this->datos[$indice] = $valor;
               }
               $avatar = str_replace('src/avatar_usuarios/','',$arr['avatar']);
        if($avatar == '' or empty($avatar) or $avatar==null)
            $this->datos['avatar'] = 'user.png';
        else
            $this->datos['avatar'] = $avatar;
           }
        $this->set_host(1);
        $this->datos['comuna'] = $this->get_comuna($this->datos['comuna']);
        $this->datos['region'] = $this->get_region($this->datos['region']);
        $this->datos['pais'] = $this->get_pais($this->datos['pais']);
        $this->datos['pregunta_secreta'] = $this->get_pregunta_secreta($this->datos['pregunta_secreta']);
    }

    protected function password(){
        if(empty($this->datos_usuario["pass_actual"]))
            $this->datos["respuesta"] = 2 ;
        else
            $this->revisarSiExiste();
    }
    
    protected function revisarSiExiste(){
        $consulta="select * from usuario_pass where usuario_pass='".md5($this->datos_usuario['pass_actual'])."' and usuario_id='".$this->usuario."'";
        $revisar=$this->sql_con->query($consulta);
        $siExiste = $revisar->num_rows;
        if($siExiste > 0)
            $this->revisar_passNuevas();
        else
            $this->datos["respuesta"]=4;
    }

    protected function revisar_passNuevas(){
        if(empty($this->datos_usuario["pass"]) or empty($this->datos_usuario["pass2"])){
            $this->datos["respuesta"] = 2 ;
        }else{
            if($this->datos_usuario["pass"] != $this->datos_usuario["pass2"]){
                $this->datos["respuesta"] = 3 ;
            }else{
                $this->actualizar_password(0);
                $this->actualizar_password(1);
            }
        }
    }

    protected function actualizar_password($host){
        $this->set_host($host);
        //$this->datos["respuesta"] = 1;
    $this->sql_con->set_charset('utf8');
    $nuevo_pass = md5($this->datos_usuario["pass"]);
    $ingresar="update usuario_pass set usuario_pass='".$nuevo_pass."' where usuario_id='".$this->usuario."'";
    $enviar = $this->sql_con->query($ingresar);   
        if($enviar)
            $this->datos["respuesta"] = 1;
        else
            $this->datos["respuesta"] = 0;
    }

    protected function subir_imagen(){
        $imagen = $_FILES['imagen']['name'];
        $directorio = "../src/avatar_usuarios/";
        $fecha = date('d.m.Y_H.i.s');
        $nombre_archivo = "avatar".$fecha;
        $i = strlen($imagen) - 1;
        $extension = '';
        while($imagen[$i] != '.'){
            $extension = $imagen[$i] . $extension;
            $i--;
        }
        $nombre_archivo = $nombre_archivo . '.' . $extension;
        $new_images = $nombre_archivo;
        //
        $images = $_FILES["imagen"]["tmp_name"];

        switch($extension){
            case "jpg": case "jpeg":
                $images_orig = ImageCreateFromJPEG($images);
                break;
            case "gif":
                $images_orig = imagecreatefromgif($images);
                break;
            case "png":
                $images_orig = imagecreatefrompng($images);
                break;
            default:
                $this->datos["respuesta"] = 7;
                exit();
                break;
        }
        copy($images,$directorio.$nombre_archivo);
        $width=300; //*** Fix Width & Heigh (Autu caculate) ***//
        $size=GetimageSize($images);
        $height=round($width*$size[1]/$size[0]);
        $photoX = ImagesX($images_orig);
        $photoY = ImagesY($images_orig);
        $images_fin = ImageCreateTrueColor($width, $height);
        ImageCopyResampled($images_fin, $images_orig, 0, 0, 0, 0, $width+1, $height+1, $photoX, $photoY);
        ImageJPEG($images_fin,$directorio.$new_images);
        ImageDestroy($images_orig);
        //ImageDestroy($images_fin);
        //
        if(!is_dir($directorio)) 
            mkdir($directorio, 0777);
        return mysqli_real_escape_string($this->sql_con, $directorio.$nombre_archivo);
    }

    public function __destruct(){
        echo json_encode($this->datos);
    }
}
?>