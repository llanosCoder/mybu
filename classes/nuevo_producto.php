<?php
    $producto = new Productos();

    class Productos{

        protected $link;
        protected $resultado = 1;
        protected $sql_con;
        protected $datos = array();
        protected $usuario;
        protected $empresa;
        protected $precio_u, $precio_m, $stock_r, $stock_m;

        public function __construct(){
            session_start();
            require('../hosts.php');
            require('conexion_new.php');
            $hosteo = new Host();
            $hosteo->obtener_conexion(0);
            $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
            $this->datos['nombre'] = mysqli_real_escape_string($this->sql_con, $_POST['nombre']);
            $this->datos['codigo'] = mysqli_real_escape_string($this->sql_con, $_POST['codigo']);
            include('sanear_string.php');
            $this->datos['codigo'] = sanear_string($this->datos['codigo']);
            $this->datos['select_marcas'] = mysqli_real_escape_string($this->sql_con, $_POST['select_marcas']);
            $this->datos['categoria'] = mysqli_real_escape_string($this->sql_con, $_POST['categoria']);
            $this->datos['modelo'] = mysqli_real_escape_string($this->sql_con, $_POST['modelo']);
            $this->datos['descripcion'] = mysqli_real_escape_string($this->sql_con, $_POST['descripcion']);
            $this->datos['talla'] = mysqli_real_escape_string($this->sql_con, $_POST['talla']);
            $this->datos['alto'] = mysqli_real_escape_string($this->sql_con, $_POST['alto']);
            $this->datos['alto_unidad_medida'] = mysqli_real_escape_string($this->sql_con, $_POST['alto_unidad_medida']);
            $this->datos['ancho'] = mysqli_real_escape_string($this->sql_con, $_POST['ancho']);
            $this->datos['ancho_unidad_medida'] = mysqli_real_escape_string($this->sql_con, $_POST['ancho_unidad_medida']);
            $this->datos['largo'] = mysqli_real_escape_string($this->sql_con, $_POST['largo']);
            $this->datos['largo_unidad_medida'] = mysqli_real_escape_string($this->sql_con, $_POST['largo_unidad_medida']);
            $this->datos['peso'] = mysqli_real_escape_string($this->sql_con, $_POST['peso']);
            $this->datos['peso_unidad_medida'] = mysqli_real_escape_string($this->sql_con, $_POST['peso_unidad_medida']);
            $this->datos['volumen'] = mysqli_real_escape_string($this->sql_con, $_POST['volumen']);
            $this->datos['volumen_unidad_medida'] = mysqli_real_escape_string($this->sql_con, $_POST['volumen_unidad_medida']);
            if(isset($_POST['check_pesable'])){
                $this->datos['pesable'] = mysqli_real_escape_string($this->sql_con, $_POST['check_pesable']);
            }else{
                $this->datos['pesable'] = 0;
            }
            if(isset($_POST['precio_u'])){
                $this->datos['precio_u'] = mysqli_real_escape_string($this->sql_con, $_POST['precio_u']);
            } else {
                $this->datos['precio_u'] = 0;
            }
            if(isset($_POST['precio_m'])){
                $this->datos['precio_m'] = mysqli_real_escape_string($this->sql_con, $_POST['precio_m']);
            } else {
                $this->datos['precio_m'] = 0;
            }
            if(isset($_POST['costo_compra'])){
                $this->datos['costo_compra'] = mysqli_real_escape_string($this->sql_con, $_POST['costo_compra']);
            } else {
                $this->datos['costo_compra'] = 0;
            }
            if(isset($_POST['stock_r'])){
                $this->datos['stock_r'] = mysqli_real_escape_string($this->sql_con, $_POST['stock_r']);
            } else {
                $this->datos['stock_r'] = 0;
            }
            if(isset($_POST['stock_m'])){
                $this->datos['stock_m'] = mysqli_real_escape_string($this->sql_con, $_POST['stock_m']);
            } else {
                $this->datos['stock_m'] = 0;
            }
            $this->usuario = $_SESSION['id'];
            $this->empresa = $_SESSION['empresa'];
            $this->agregar_producto();
        }

        protected function set_conexion($host, $user, $pass, $bd){
            $conexion = new Conexion();
            $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
            $this->sql_con->set_charset('utf8');
        }

        protected function existe_marca($m_id){
            $consulta = "SELECT count(*) as cont FROM producto_marca WHERE producto_marca_id = $m_id";
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

        protected function agregar_marca($m_nombre){
            $insercion = $this->sql_con->prepare("INSERT INTO producto_marca (producto_marca_nombre) VALUES (?)");
            $insercion->bind_param('s', $m_nombre);
            $insercion->execute();
            $afectadas = $this->sql_con->affected_rows;
            $insercion->close();
            if($afectadas > 0){
                return mysqli_insert_id($this->sql_con);
            }else{
                return 0;
            }
        }

        protected function agregar_producto(){

            $c_desc = $this->datos['categoria'];
            $consulta_marca = "SELECT categoria_id as c_id FROM categoria WHERE categoria_descripcion = '$c_desc'";
            $rs = $this->sql_con->query($consulta_marca);
            if($rs === false) {
                trigger_error('Ha ocurrido un error en ' . $this->sql_con->connect_error, E_USER_ERROR);
                $this->resultado = 0;
            } else {
                while($row = $rs->fetch_assoc()){
                    $c_id = $row['c_id'];
                }
                    $i_talla = $this->insertar_talla();
                    $i_peso = $this->insertar_peso();
                    $i_volumen = $this->insertar_volumen();
                    $i_dimensiones = $this->insertar_dimensiones();
                if($_FILES['imagen']['name'] != ''){
                    $this->datos['imagen'] = $this->subir_imagen();
                }else{
                    $this->datos['imagen'] = '';
                }
                $codigo = $this->datos['codigo'];
                $consulta_existe = "SELECT count(*) as cont FROM producto WHERE producto_codigo = '$codigo'";
                $rs_existe = $this->sql_con->query($consulta_existe);
                if($rs_existe === false){
                    trigger_error("Ha ocurrido un error");
                }else{
                    $row_existe = $rs_existe->fetch_assoc();
                    if($row_existe['cont'] < 1){
                        if(!$this->existe_marca($this->datos['select_marcas'])){
                            $this->datos['select_marcas'] = $this->agregar_marca($this->datos['select_marcas']);
                        }
                        $insercion_producto = $this->sql_con->prepare("INSERT INTO producto(producto_id, producto_codigo, producto_nombre,
                        marca_id, producto_modelo, producto_descripcion, producto_imagen,
                        producto_talla, producto_peso, producto_volumen, producto_dimension)
                        VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $insercion_producto->bind_param('ssisssiiii', $this->datos['codigo'],
                        $this->datos['nombre'],
                        $this->datos['select_marcas'],
                        $this->datos['modelo'],
                        $this->datos['descripcion'],
                        $this->datos['imagen'],
                        $i_talla,
                        $i_peso,
                        $i_volumen,
                        $i_dimensiones);
                        $insercion_producto->execute();
                        $insercion_producto->close();
                        $p_id = mysqli_insert_id($this->sql_con);
                        $this->insertar_categoria($c_id, $p_id);
                        do{
                            $consulta = "SELECT categoria_padre as padre FROM categoria WHERE categoria_id = $c_id";
                            $rs = $this->sql_con->query($consulta);
                            if($rs === false) {
                                trigger_error('Wrong SQL: ' . $consulta . ' Error: ' . $this->sql_con->error, E_USER_ERROR);
                            } else {
                                while($row = $rs->fetch_assoc()){
                                    $padre = $row['padre'];
                                    if($padre != 0 && $padre != '0'){
                                        $this->insertar_categoria($padre, $p_id);
                                    }
                                }
                            }
                            $c_id = $padre;
                        }while ($padre != 0 && $padre != '0');
                        $this->insertar_bodega($p_id);
                        if($this->datos['pesable'] == 1){
                            $this->insertar_pesable($p_id);
                        }
                    }else{
                        $this->resultado = 3;
                        exit();
                    }
                }
            }
        }

        protected function insertar_pesable($p_id){
            $insercion = $this->sql_con->prepare("INSERT INTO producto_pesable (producto_id) VALUES (?)");
            $insercion->bind_param('i', $p_id);
            $insercion->execute();
            $insercion->close();
        }

        protected function insertar_talla(){
            $insercion = $this->sql_con->prepare("INSERT INTO producto_talla(producto_talla) VALUES (?)");
            $insercion->bind_param('i', $this->datos['talla']);
            $insercion->execute();
            $insercion->close();
            return mysqli_insert_id($this->sql_con);
        }

        protected function insertar_peso(){
            $insercion = $this->sql_con->prepare("INSERT INTO producto_peso(producto_peso,
            producto_peso_unidad_medida) VALUES (?, ?)");
            $insercion->bind_param('is', $this->datos['peso'],
            $this->datos['peso_unidad_medida']);
            $insercion->execute();
            $insercion->close();
            return mysqli_insert_id($this->sql_con);
        }

        protected function insertar_volumen(){
            $insercion = $this->sql_con->prepare("INSERT INTO producto_volumen(producto_volumen,
            producto_volumen_unidad_medida) VALUES (?, ?)");
            $insercion->bind_param('is', $this->datos['volumen'],
            $this->datos['volumen_unidad_medida']);
            $insercion->execute();
            $insercion->close();
            return mysqli_insert_id($this->sql_con);
        }

        protected function insertar_dimensiones(){
            $insercion = $this->sql_con->prepare("INSERT INTO producto_dimension(producto_alto,
            producto_alto_unidad_medida,producto_ancho, producto_ancho_unidad_medida,
            producto_largo, producto_largo_unidad_medida) VALUES (?, ?, ?, ?, ?, ?)");
            $insercion->bind_param('isisis', $this->datos['alto'],
            $this->datos['alto_unidad_medida'],
            $this->datos['ancho'],
            $this->datos['ancho_unidad_medida'],
            $this->datos['largo'],
            $this->datos['largo_unidad_medida']);
            $insercion->execute();
            $insercion->close();
            return mysqli_insert_id($this->sql_con);
        }

        protected function insertar_categoria($padre, $p_id){
            $insercion = $this->sql_con->prepare("INSERT INTO categoria_producto(categoria_id,
            producto_id) VALUES (?, ?)");
            $insercion->bind_param('ii', $padre,
            $p_id);
            $insercion->execute();
            $insercion->close();
        }

        protected function subir_imagen(){
            $imagen = $_FILES['imagen']['name'];
            $directorio = "src/productos/";
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
            //
            $images = $_FILES["imagen"]["tmp_name"];
            copy($images,'../'.$directorio.$nombre_archivo);
            $width=150; //*** Fix Width & Heigh (Autu caculate) ***//
            $size=GetimageSize($images);
            $height=round($width*$size[1]/$size[0]);

            switch($extension){
                case "jpg": case "jpeg": case "JPG": case "JPEG":
                    $images_orig = ImageCreateFromJPEG($images);
                    break;
                case "gif": case "GIF":
                    $images_orig = imagecreatefromgif($images);
                    break;
                case "png": case "PNG":
                    $images_orig = imagecreatefrompng($images);
                    break;
                default:
                    $this->resultado = 2;
                    exit();
            }
            $photoX = ImagesX($images_orig);
            $photoY = ImagesY($images_orig);
            $images_fin = ImageCreateTrueColor($width, $height);
            ImageCopyResampled($images_fin, $images_orig, 0, 0, 0, 0, $width+1, $height+1, $photoX, $photoY);
            ImageJPEG($images_fin,'../'.$directorio.$new_images);
            ImageDestroy($images_orig);
            //ImageDestroy($images_fin);
            //
            if(!is_dir('../'.$directorio))
                mkdir('../'.$directorio, 0777);

            /*if ($imagen && move_uploaded_file($images_fin,'../'.$directorio.$nombre_archivo))
            {
               $this->resultado = 1;
            }else{
                $this->resultado = 0;
            }*/
            return mysqli_real_escape_string($this->sql_con, $directorio.$nombre_archivo);
        }

        protected function insertar_bodega($p_id){
            $consulta = "SELECT sucursal_id as sucursal FROM sucursal WHERE empresa_id = $this->empresa";
            $rs = $this->sql_con->query($consulta);
            if($rs === false) {
                trigger_error('Ha ocurrido un error ' . $this->sql_con->connect_error, E_USER_ERROR);
            } else {
                while($row = $rs->fetch_assoc()){
                    if(!$this->existe_producto_sucursal($p_id, $row['sucursal'])){
                        $insercion = $this->sql_con->prepare("INSERT INTO producto_sucursal(producto_sucursal_id, producto_id, sucursal_id, producto_sucursal_precio_unitario, producto_sucursal_precio_mayorista, producto_sucursal_costo, producto_sucursal_stock_real, producto_sucursal_stock_minimo) VALUES (null, ?, ?, ?, ?, ?, ?, ?)");
                        $insercion->bind_param('iiiiiii', $p_id,
                            $row['sucursal'],
                            $this->datos['precio_u'],
                            $this->datos['precio_m'],
                            $this->datos['costo_compra'],
                            $this->datos['stock_r'],
                            $this->datos['stock_m']);
                        $insercion->execute();
                        $insercion->close();
                    }
                }
            }

        }

        protected function existe_producto_sucursal($p_id, $sucursal){
            $consulta = "SELECT count(*) as cont FROM producto_sucursal WHERE producto_id = $p_id AND sucursal_id = $sucursal";
            $rs = $this->sql_con->query($consulta);
            if($rs === false){
                return true;
            }else{
                $row = $rs->fetch_assoc();
                if($row['cont'] > 0){
                    return true;
                }else{
                    return false;
                }
            }
        }

        function __destruct(){
            echo json_encode($this->resultado);
        }
    };
?>
