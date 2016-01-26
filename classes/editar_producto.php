<?php

 $producto = new Productos();

    class Productos{

        protected $link;
        protected $resultado = 1;
        protected $sql_con;
        protected $datos = array();
        protected $codigo;

        public function __construct(){
            session_start();
            require('../hosts.php');
            require('conexion_new.php');
            $hosteo = new Host();
            $hosteo->obtener_conexion(0);
            $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
            $this->editar_producto();
        }

        protected function set_conexion($host, $user, $pass, $bd){
            $conexion = new Conexion();
            $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
            $this->sql_con->set_charset('utf8');
        }

        protected function editar_producto(){
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'],$this->link['pass'],$this->link['bd']);
            $this->datos['p.producto_nombre'] = mysqli_real_escape_string($this->sql_con, $_POST['nombre']);
            $this->datos['p.marca_id'] = mysqli_real_escape_string($this->sql_con, $_POST['select_marcas']);
            $this->codigo = mysqli_real_escape_string($this->sql_con, $_POST['codigo']);
            $this->datos['p.producto_modelo'] = mysqli_real_escape_string($this->sql_con, $_POST['modelo']);
            $this->datos['p.producto_descripcion'] = mysqli_real_escape_string($this->sql_con, $_POST['descripcion']);
            $this->datos['t.producto_talla'] = mysqli_real_escape_string($this->sql_con, $_POST['talla']);
            $this->datos['d.producto_alto'] = mysqli_real_escape_string($this->sql_con, $_POST['alto']);
            $this->datos['d.producto_alto_unidad_medida'] = mysqli_real_escape_string($this->sql_con, $_POST['alto_unidad_medida']);
            $this->datos['d.producto_ancho'] = mysqli_real_escape_string($this->sql_con, $_POST['ancho']);
            $this->datos['d.producto_ancho_unidad_medida'] = mysqli_real_escape_string($this->sql_con, $_POST['ancho_unidad_medida']);
            $this->datos['d.producto_largo'] = mysqli_real_escape_string($this->sql_con, $_POST['largo']);
            $this->datos['d.producto_largo_unidad_medida'] = mysqli_real_escape_string($this->sql_con, $_POST['largo_unidad_medida']);
            $this->datos['pe.producto_peso'] = mysqli_real_escape_string($this->sql_con, $_POST['peso']);
            $this->datos['pe.producto_peso_unidad_medida'] = mysqli_real_escape_string($this->sql_con, $_POST['peso_unidad_medida']);
            $this->datos['v.producto_volumen'] = mysqli_real_escape_string($this->sql_con, $_POST['volumen']);
            $this->datos['v.producto_volumen_unidad_medida'] = mysqli_real_escape_string($this->sql_con, $_POST['volumen_unidad_medida']);
            if(isset($_POST['check_pesable'])){
                $this->datos['pesable'] = mysqli_real_escape_string($this->sql_con, $_POST['check_pesable']);
            }else{
                $this->datos['pesable'] = 0;
            }
            if(isset($_POST['precio_u'])){
                $bodega = true;
                $precio_u = $_POST['precio_u'];
                if ($precio_u == '' || $precio_u == ' ') {
                    $precio_u = 0;
                }
            }else{
                $precio_u = '0';
            }
            if(isset($_POST['precio_m'])){
                $precio_m = $_POST['precio_m'];
                $bodega = true;
                if ($precio_m == '' || $precio_m == ' ') {
                    $precio_m = 0;
                }
            }else{
                $precio_m = '0';
            }
            if(isset($_POST['stock_r'])){
                $bodega = true;
                $stock_r = $_POST['stock_r'];
                if ($stock_r == '' || $stock_r == ' ') {
                    $stock_r = 0;
                }
            }else{
                $stock_r = '0';
            }
            if(isset($_POST['stock_m'])){
                $bodega = true;
                $stock_m = $_POST['stock_m'];
                if ($stock_m == '' || $stock_m == ' ') {
                    $stock_m = 0;
                }
            }else{
                $bodega = false;
                $stock_r = '0';
            }
            if($_FILES['imagen']['name'] != ''){
                    $this->datos['p.producto_imagen'] = $this->subir_imagen();
            }
            //$this->eliminar_imagen();
            $consulta = "UPDATE producto p LEFT JOIN producto_talla t ON p.producto_talla = t.producto_talla_id
                                           LEFT JOIN producto_peso pe ON p.producto_peso = pe.producto_peso_id
                                           LEFT JOIN producto_volumen v ON p.producto_volumen = v.producto_volumen_id
                                           LEFT JOIN producto_dimension d ON p.producto_dimension = d.producto_dimension_id SET";
            $i = 0;
            foreach($this->datos as $indice=>$valor){
                if($valor != '' && $valor != null && $indice != 'pesable'){
                    if($i != 0){
                        $consulta = $consulta . ",";
                    }
                    $consulta = $consulta . " " . $indice . " = '" . $valor . "'";
                }
                $i++;
            }
            $consulta = $consulta . " WHERE p.producto_codigo = '" . $this->codigo . "'";
            if($this->sql_con->query($consulta) === false) {
              trigger_error('Wrong SQL: ' . $consulta . ' Error: ' . $this->sql_con->error, E_USER_ERROR);
            } else {
              $filas_afectadas = $this->sql_con->affected_rows;
                if($filas_afectadas == 0){
                    $this->resultado = 0;
                }else{
                    $this->resultado = 1;
                }

            }
            if($bodega){
                $consulta_bodega = "UPDATE producto_sucursal SET producto_sucursal_precio_unitario = $precio_u, producto_sucursal_precio_mayorista = $precio_m, producto_sucursal_stock_real = $stock_r, producto_sucursal_stock_minimo = $stock_m WHERE producto_id = (SELECT producto_id FROM producto WHERE producto_codigo = '$this->codigo')";
                $rs = $this->sql_con->query($consulta_bodega);
                if($rs === false) {
                  trigger_error('Wrong SQL: ' . $consulta_bodega . ' Error: ' . $this->sql_con->error, E_USER_ERROR);
                } else {
                  $filas_afectadas = $this->sql_con->affected_rows;
                    if($filas_afectadas > 0){
//                        $this->resultado = 1;
                    }else{
//                        $this->resultado = 0;
                    }
                }
            }
            $this->editar_pesable($this->codigo);
        }

        protected function obtener_producto_id($codigo){
            $consulta = "SELECT producto_id as p_id FROM producto WHERE producto_codigo = '$codigo'";
            $rs = $this->sql_con->query($consulta);
            if($rs === false){
                return 0;
            }else{
                $row = $rs->fetch_assoc();
                return $row['p_id'];
            }
        }

        protected function editar_pesable($codigo){
            $p_id = $this->obtener_producto_id($codigo);
            if($this->datos['pesable'] == 1){
                $this->insertar_pesable($p_id);
            }else{
                $consulta = $this->sql_con->prepare("DELETE FROM producto_pesable WHERE producto_id = ?");
                $consulta->bind_param('i', $p_id);
                $consulta->execute();
                $afectadas = $this->sql_con->affected_rows;
                $consulta->close();
                if($afectadas > 0){
                    $this->resultado = 1;
                }
            }
        }

        protected function insertar_pesable($p_id){
            $insercion = $this->sql_con->prepare("INSERT INTO producto_pesable (producto_id) VALUES (?)");
            $insercion->bind_param('i', $p_id);
            $insercion->execute();
            $afectadas = $this->sql_con->affected_rows;
            $insercion->close();
            if($afectadas > 0){
                $this->resultado = 1;
            }
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

        protected function eliminar_imagen(){
            $consulta_imagen = "SELECT producto_imagen as img FROM producto WHERE producto_codigo = '$this->codigo'";
            $result_imagen = $this->sql_con->query($consulta_imagen);
            if($result_imagen === false){
                trigger_error("Ha ocurrido un error");
            }else{
                $row_imagen = $result_imagen->fetch_assoc();
                if($row_imagen['img'] != '')
                    unlink("../" . $row_imagen['img']);
            }
        }

        public function __destruct(){
            echo $this->resultado;
        }

    }

?>
