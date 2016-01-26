<?php
    $marca = new Marcas();    
    
    class Marcas{
        
        protected $link;
        protected $id;
        protected $datos = array();
        protected $sql_con;
        protected $parametros = array(), $parametros_abrev = array();
        
        public function __construct(){
            $this->id = $_POST['cId'];
            session_start();
            require('../hosts.php');
            require('conexion_new.php');
            $hosteo = new Host();
            $hosteo->obtener_conexion(0);
            $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
            $this->obtener_marcas();
        }
        
        protected function set_conexion($host, $user, $pass, $bd){
            $conexion = new Conexion();
            $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
            $this->sql_con->set_charset('utf8');
        }
        
        protected function obtener_marcas(){
            $this->obtener_parametros();
            $consulta_marca = "SELECT ";
            for($i = 0; $i < count($this->parametros_abrev); $i++){
                if($i > 0){
                    $consulta_marca = $consulta_marca . ", ";
                }
                $consulta_marca = $consulta_marca . $this->parametros[$i] . " as " . $this->parametros_abrev[$i];
            }
            if($this->id == "all"){
                $consulta_marca = $consulta_marca . "  FROM producto_marca INNER JOIN categoria_marca ON producto_marca_id = marca_id GROUP BY producto_marca_id ORDER BY producto_marca_nombre";
            }else{
                $consulta_marca = $consulta_marca . "  FROM producto_marca INNER JOIN categoria_marca ON marca_id = producto_marca_id GROUP BY producto_marca_id ORDER BY producto_marca_nombre";
            }
            $this->sql_con->set_charset("utf8");
            $rs2 = $this->sql_con->query($consulta_marca);
            if($rs2 === false) {
                trigger_error('Ha ocurrido un error');
            } else {
                while($row_marca = $rs2->fetch_assoc()){
                    $dato = array();
                    for($i = 0; $i < count($this->parametros_abrev); $i++){
                        $dato[$this->parametros_abrev[$i]] = $row_marca[$this->parametros_abrev[$i]];
                    }
                    array_push($this->datos, $dato);
                }
                $rs2->close();
            }
        }
        
        protected function obtener_parametros(){
            $this->parametros_abrev = $_POST['parametros'];
            foreach($this->parametros_abrev as $indice=>$valor){
                switch($valor){
                    case 'nombre':
                        $param = 'producto_marca_nombre';
                        $agregar = true;
                        break;
                    case 'logo':
                        $param = 'producto_marca_logo';
                        $agregar = true;
                        break;
                    case 'marca':
                        $param = 'producto_marca_nombre';
                        $agregar = true;
                        break;
                    case 'value':
                        $param = 'producto_marca_id';
                        $agregar = true;
                        break;
                    default:
                        $agregar = false;
                        break;
                }
                if($agregar){
                    array_push($this->parametros, $param); 
                }
            }
        }

        function __destruct(){
            echo json_encode($this->datos);
        }

        
    };
?>