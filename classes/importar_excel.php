<?php

$importador = new Importadores();

class Importadores{

    protected $sql_con, $link;
    protected $datos = array();
    
    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    
    protected function set_host($host){
        $hosteo = new Host();
        $hosteo->obtener_conexion($host);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
    }
    
    protected function subir_excel($tipo){
        $file = "file_$tipo";
        if(isset($_FILES[$file])){
            $archivo_temporal = $_FILES[$file]['tmp_name'];
            $extension = '';
            $nombre = $_FILES[$file]['name'];
            $i = strlen($_FILES[$file]['name']) - 1;
            while($nombre[$i] != '.'){
                $extension = $nombre[$i] . $extension;
                $i--;
            }
            if($extension != 'xls' and $extension != 'xlsx'){
                return false;
            }
            $archivo = '../files/excel_productos/' . date('Y_m_d_H_i_s') . '_' . $this->usuario . '.' . $extension;
            move_uploaded_file($archivo_temporal,$archivo);
            $result['archivo'] = $archivo;
            $result['extension'] = $extension;
            return $result;
        }else{
            return false;
        }
    }
    
    /***** CATEGORIAS ****/
    protected function es_global($categoria){
        $this->set_host(1);
        $consulta = "SELECT categoria_descripcion as descripcion FROM categoria WHERE categoria_nombre = '$categoria'";
        $rs = $this->sql_con->query($consulta);
        $nombre = sanear_string($categoria);
        if($rs === false){
            $descripcion = $nombre;
        }else{
            $row = $rs->fetch_assoc();
            if(mysqli_num_rows($rs) > 0){
                $descripcion = $row['descripcion'];
            }else{
                if($this->verificar_categoria($nombre) > 0){
                    $fecha = date('Y-m-d H:i:s');
                    $descripcion = md5($fecha . $nombre);
                }else{
                    $descripcion = $nombre;
                }
            }
        }
        return $descripcion;
    }
    
    protected function verificar_categoria($codigo){
        $this->set_host(0);
        $consulta_categoria = "SELECT count(*) as cont FROM categoria WHERE categoria_descripcion = '$codigo'";
        $rs_categoria = $this->sql_con->query($consulta_categoria);
        if($rs_categoria === false){
            return 0;
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
    
    protected function get_padre($categoria){
        $consulta = "SELECT categoria_padre as padre FROM categoria WHERE categoria_id = $categoria";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return 0;
        }else{
            $row = $rs->fetch_assoc();
            return $row['padre'];
        }
    }
    
    protected function insertar_categoria($registro, $descripciones){
        $descripcion = $this->es_global($registro[0]);
        if($registro[1] == 0){
            $padre = 0;
        }else{
            $padre = $this->get_id($descripciones[$registro[1] - 2], 0);
        }
        $this->set_host(0);
        $insercion = $this->sql_con->prepare("INSERT INTO categoria (categoria_nombre, categoria_descripcion, categoria_padre) VALUES (?, ?, ?)");
        $insercion->bind_param('ssi', 
                               $registro[0],
                               $descripcion,
                               $padre);
        $insercion->execute();
        $afectadas = $this->sql_con->affected_rows;
        $insercion->close();
        return $descripcion;
    }
    
    /***** FIN CATEGORIAS ****/
    
    /***** MARCAS *****/
    
    protected function verificar_asignacion_marca_categoria($categoria, $marca){
        $consulta = "SELECT count(*) as cont FROM categoria_marca WHERE categoria_id = $categoria AND marca_id = $marca";
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
    
    protected function insertar_asignacion_categoria_marca($categoria, $marca){
        if($this->verificar_asignacion_marca_categoria($categoria, $marca)){
            $insercion = $this->sql_con->prepare("INSERT INTO categoria_marca (categoria_id, marca_id) VALUES (?, ?)");
            $insercion->bind_param('ii', $categoria, $marca);
            $insercion->execute();
            $insercion->close();
            
        }
    }
    
    protected function asignar_marca_categoria($marca, $categoria){
        $consulta = "SELECT categoria_id as cid FROM categoria WHERE categoria_padre = $categoria OR categoria_id = $categoria";
        $rs = $this->sql_con->query($consulta);
        if($rs === false) {
            trigger_error('Ha ocurrido un error');
        } else {
            //$resultado = $rs->fetch_all(MYSQLI_ASSOC);
            while($row = $rs->fetch_assoc()){
                $id = $row['cid'];
                $this->insertar_asignacion_categoria_marca($categoria, $marca);
                if($categoria != $id){
                    $this->asignar_marca_categoria($marca, $id);
                }
            }
        }
        $rs->close();
    }
    
    protected function insertar_marca($registro){
        $insercion = $this->sql_con->prepare("INSERT INTO producto_marca (producto_marca_nombre, producto_marca_logo) VALUES (?, ?)");
        $insercion->bind_param('ss',
                              $registro[0],
                              $registro[1]);
        $insercion->execute();
        $afectadas = $this->sql_con->affected_rows;
        $id = mysqli_insert_id($this->sql_con);
        if($afectadas > 0){
            $this->asignar_marca_categoria($id, $registro[2]);
        }
        $insercion->close();
    }
    
    /***** FIN MARCAS ****/
    
    /**** PRODUCTOS ****/
    
    protected function asignar_producto_categoria($producto, $categoria){
        do{
            $insercion = $this->sql_con->prepare("INSERT INTO categoria_producto (categoria_id, producto_id) VALUES (?, ?)");
            $insercion->bind_param('ii',
                                  $categoria,
                                  $producto);
            $insercion->execute();
            $insercion->close();
            $categoria = $this->get_padre($categoria);
        }while($categoria != 0);
    }
    
    protected function insertar_talla($registro){
        $insercion = $this->sql_con->prepare("INSERT INTO producto_talla (producto_talla) VALUES (?)");
        $insercion->bind_param('i', $registro[12]);
        $insercion->execute();
        $id = mysqli_insert_id($this->sql_con);
        $insercion->close();
        return $id;
    }
    
    protected function insertar_peso($registro){
        $insercion = $this->sql_con->prepare("INSERT INTO producto_peso (producto_peso, producto_peso_unidad_medida) VALUES (?, ?)");
        $insercion->bind_param('is',
                               $registro[13], 
                               $registro[14]);
        $insercion->execute();
        $id = mysqli_insert_id($this->sql_con);
        $insercion->close();
        return $id;
    }
    
    protected function insertar_volumen($registro){
        $insercion = $this->sql_con->prepare("INSERT INTO producto_volumen (producto_volumen, producto_volumen_unidad_medida) VALUES (?, ?)");
        $insercion->bind_param('is',
                               $registro[15], 
                               $registro[16]);
        $insercion->execute();
        $id = mysqli_insert_id($this->sql_con);
        $insercion->close();
        return $id;
    }
    
    protected function insertar_dimension($registro){
        $insercion = $this->sql_con->prepare("INSERT INTO producto_dimension (producto_alto, producto_alto_unidad_medida, producto_largo, producto_largo_unidad_medida, producto_ancho, producto_ancho_unidad_medida) VALUES (?, ?, ?, ?, ?, ?)");
        $insercion->bind_param('isisis',
                               $registro[17], 
                               $registro[18],
                               $registro[19],
                               $registro[20],
                               $registro[21],
                               $registro[22]);
        $insercion->execute();
        $id = mysqli_insert_id($this->sql_con);
        $insercion->close();
        return $id;
    }
    
    protected function insertar_producto($registro, $talla, $peso, $volumen, $dimension){
        if($registro[5] == '-'){
            $registro[5] == '';
        }
        $insercion = $this->sql_con->prepare("INSERT INTO producto (producto_codigo, producto_nombre, marca_id, producto_modelo, producto_descripcion, producto_imagen, producto_talla, producto_peso, producto_volumen, producto_dimension) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insercion->bind_param('ssisssiiii',
                              $registro[0],
                              $registro[1],
                              $registro[2],
                              $registro[3],
                              $registro[4],
                              $registro[5],
                              $talla,
                              $peso,
                              $volumen,
                              $dimension);
        $insercion->execute();
        $id = mysqli_insert_id($this->sql_con);
        $insercion->close();
        return $id;
    }
    
    protected function insertar_producto_sucursal($p_id, $registro){
        $insercion = $this->sql_con->prepare("INSERT INTO producto_sucursal (producto_id, sucursal_id, producto_sucursal_precio_unitario, producto_sucursal_precio_mayorista, producto_sucursal_costo, producto_sucursal_stock_real, producto_sucursal_stock_minimo) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insercion->bind_param('iiiiiii', $p_id, $registro[23],
                              $registro[6],
                              $registro[7],
                              $registro[8],
                              $registro[9],
                              $registro[10]);
        $insercion->execute();
        $insercion->close();
    }
    
    protected function insertar_producto_pesable($p_id){
        $insercion = $this->sql_con->prepare("INSERT INTO producto_pesable (producto_id) VALUES (?)");
        $insercion->bind_param('i', $p_id);
        $insercion->execute();
        $insercion->close();
    }
    
    protected function existe_codigo($codigo){
        $consulta = "SELECT count(*) as cont FROM producto WHERE producto_codigo = '$codigo'";
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
    
    /**** FIN PRODUCTOS ****/
    
    /**** IMPORTACION *****/
    
    protected function importar($archivo, $extension, $accion){
        require_once('PHPExcel/IOFactory.php');
        $objPHPExcel = PHPExcel_IOFactory::load($archivo);
        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
            $descripciones = array();
            for($row = 2; $row <= $highestRow; $row++){
                $registro = array();
                $col = 0;
                do{
                    $cell = $worksheet->getCellByColumnAndRow($col, $row);
                    $val = $cell->getValue();
                    $registro[$col] = $val;
                    $col++;
                }while($val !== '' && $col <= $highestColumnIndex);
                switch ($accion) {
                    case 1:
                        array_push($descripciones, $this->insertar_categoria($registro, $descripciones));
                        $this->datos['resultado'] = 1;
                        break;
                    case 2:
                        $this->insertar_marca($registro);
                        $this->datos['resultado'] = 1;
                        break;
                    case 3:
                        if(!$this->existe_codigo($registro[0])){
                            $talla = $this->insertar_talla($registro);
                            $peso = $this->insertar_peso($registro);
                            $volumen = $this->insertar_volumen($registro);
                            $dimension = $this->insertar_dimension($registro);
                            $p_id = $this->insertar_producto($registro, $talla, $peso, $volumen, $dimension);
                            $this->insertar_producto_sucursal($p_id, $registro);
                            $this->asignar_producto_categoria($p_id, $registro[24]);
                            if($registro[11] == 1){
                                $this->insertar_producto_pesable($p_id);
                            }
                        }
                        $this->datos['resultado'] = 1;
                        break;
                }
                
            }
        }
    }

    /*** FIN IMPORTACION ****/
    
    protected function importar_productos($archivo){
        error_reporting(E_ALL ^ E_NOTICE);
        
        $data = new Spreadsheet_Excel_Reader($archivo);
        echo $data->sheets[0]['cells'][1][1];
    }
    
    protected function procesar(){
        $accion = $_POST['accion'];
        $this->usuario = $_SESSION['id'];
        switch ($accion) {
            case 1:
                $tipo = 'categorias';
                break;
            case 2:
                $tipo = 'marcas';
                break;
            case 3:
                $tipo = 'productos';
                break;
        }
        $result = $this->subir_excel($tipo);
        $archivo = $result['archivo'];
        $extension = $result['extension'];
        if ($archivo == false){
            $this->datos['resultado'] = 2; //Archivo no fue subido
        }
        $this->importar($archivo, $extension, $accion);
    }
    
    public function __construct(){
        session_start();
        require('../hosts.php');
        require('conexion_new.php');
        ini_set('display_errors', 'on');
        include('sanear_string.php');
        $hosteo = new Host();
        $hosteo->obtener_conexion(0);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $this->procesar();
    }
    
    public function __destruct(){
        echo json_encode($this->datos);
    }

}

?>