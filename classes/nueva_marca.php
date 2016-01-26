<?php
    $marca = new Marcas();    
    
    class Marcas{
        
        protected $link;
        protected $resultado = 1;
        protected $sql_con;
        protected $datos = array();
        protected $id_marca = 0;
        protected $categorias = array();
        
        public function __construct(){
            session_start();
            require('../hosts.php');
            require('conexion_new.php');
            $hosteo = new Host();
            $hosteo->obtener_conexion(0);
            $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
            $this->obtener_parametros();
            if(isset($_POST['id']))
               $this->id_marca = $_POST['id'];
            if($this->id_marca != 0)
                $this->editar_marca();
            else
                $this->agregar_marca();
        }
        
        protected function set_conexion($host, $user, $pass, $bd){
            $conexion = new Conexion();
            $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
            $this->sql_con->set_charset('utf8');
        }
        
        protected function obtener_parametros(){
            $this->datos['marca'] = mysqli_real_escape_string($this->sql_con, $_POST['marca']);
            $this->datos['categoria'] = $_POST['categoria'];
            if($_FILES['logo']['name'] != ''){
                $this->datos['logo'] = $this->subir_imagen();
            }else{
                $this->datos['logo'] = '';
            }
        }
        
        protected function agregar_marca(){
            $insercion_marca = $this->sql_con->prepare("INSERT INTO producto_marca(producto_marca_id, producto_marca_nombre, producto_marca_logo) VALUES (null, ?, ?)");
            $insercion_marca->bind_param('ss', $this->datos['marca'],
            $this->datos['logo']);
            $insercion_marca->execute(); 
            $insercion_marca->close();
            $m_id = mysqli_insert_id($this->sql_con);
            $this->datos['categoria'] = explode(',', $this->datos['categoria']);
            foreach($this->datos['categoria'] as $c_desc){
                $c_desc = mysqli_real_escape_string($this->sql_con, $c_desc);
                $consulta_marca = "SELECT categoria_id as c_id FROM categoria WHERE categoria_descripcion = '$c_desc'";
                //$this->sql_con->set_charset("utf8");
                $rs = $this->sql_con->query($consulta_marca);
                if($rs === false) {
                    trigger_error('Ha ocurrido un error en ' . $this->sql_con->connect_error, E_USER_ERROR);
                    $this->resultado = 0;
                } else {
                    while($row = $rs->fetch_assoc()){
                        $c_id = $row['c_id'];
                    }
                    $this->insertar_categoria($c_id, $m_id);
                    $this->obtener_categorias($c_id);
                    for($i = 0; $i < count($this->categorias); $i++){
                        $this->insertar_categoria($this->categorias[$i], $m_id);
                    }
                }
                
            }
        }
        
        function obtener_categorias($cat_padre, &$jerarquia = 1){
            $consulta = "SELECT categoria_id as cid FROM categoria WHERE categoria_padre = $cat_padre";
            $rs = $this->sql_con->query($consulta);
            if($rs === false) {
                trigger_error('Ha ocurrido un error');
            } else {
                //$resultado = $rs->fetch_all(MYSQLI_ASSOC);
                while($row = $rs->fetch_assoc()){
                    array_push($this->categorias, $row['cid']);
                    $subir_jerarquia = $jerarquia + 1;
                    $this->obtener_categorias($row['cid'], $subir_jerarquia);
                }
            }
            $rs->close();
        }
               
        protected function editar_marca(){
            $edicion_marca = "UPDATE producto_marca SET ";
            $edicion_marca = $edicion_marca . "producto_marca_nombre = '" . $this->datos['marca'] . "'";
            if($this->datos['logo'] != '')
                $edicion_marca = $edicion_marca . ", producto_marca_logo = '" . $this->datos['logo'] . "'";
            $edicion_marca = $edicion_marca . " WHERE producto_marca_id = " . $this->id_marca;
            if($this->sql_con->query($edicion_marca) === false) {
              trigger_error('Wrong SQL: ' . $consulta . ' Error: ' . $this->sql_con->error, E_USER_ERROR);
            } else {
              $filas_afectadas = $this->sql_con->affected_rows;
                if($filas_afectadas == 0){
                    $this->resultado = 0;
                }else{
                    $this->resultado = 1;
                }
            }
        }
        
        protected function insertar_categoria($padre, $m_id){
            $insercion = $this->sql_con->prepare("INSERT INTO categoria_marca(categoria_id, 
            marca_id) VALUES (?, ?)");
            $insercion->bind_param('ii', $padre,
            $m_id);
            $insercion->execute(); 
            $insercion->close();
        }
        
        protected function subir_imagen(){
            $imagen = $_FILES['logo']['name'];
            $directorio = "src/marcas_logo/";
            $fecha = date('d.m.Y_H.i.s');
            $nombre_archivo = "logo_".$fecha;
            $i = strlen($imagen) - 1;
            $extension = '';
            while($imagen[$i] != '.'){
                $extension = $imagen[$i] . $extension;
                $i--;
            }
            $nombre_archivo = $nombre_archivo . '.' . $extension;
            $new_images = $nombre_archivo;
            $images = $_FILES['logo']['tmp_name'];
            switch($extension){
                case "jpg": case "jpeg": case "JPG": case "JPEG":
                    $thumb = ImageCreateFromJPEG($images);
                    break;
                case "gif": case "GIF":
                    $thumb = imagecreatefromgif($images);
                    break;
                case "png": case "PNG":
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
            /*
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);  
            imagealphablending($thumb, true);
            /*$photoX = ImagesX($thumb);
            $photoY = ImagesY($thumb);
            $new_image = imagecreatetruecolor ( $width, $height );
            imagecopyresampled($new_image, $thumb, 0, 0, 0, 0, $width+1, $height+1, $photoX, $photoY);*/
            /*switch($extension){
                case "jpg": case "jpeg":
                    ImageJPEG($new_image,'../'.$directorio.$new_images);
                    break;
                case "gif":
                    imagegif($new_image,'../'.$directorio.$new_images);
                    break;
                case "png":
                    echo "helo";
                    imagepng ($new_image,'../'.$directorio.$new_images, 9);
                    break;
                default:
                    $this->resultado = 2;
                    exit();
            }*/
            ImageDestroy($thumb);
            if(!is_dir('../'.$directorio)) 
                mkdir('../'.$directorio, 0777);

            return mysqli_real_escape_string($this->sql_con, $directorio.$nombre_archivo);
        }
        
        public function __destruct(){
            echo $this->resultado;
        }
    }
?>