<?php

    $oferta = new Ofertas();
    
    class Ofertas{
    
        protected $link, $sql_con, $datos = array();
        protected $conexion = "conexion_new.php";
        protected $filtro, $query, $alias;
        protected $usuario, $sucursal, $empresa;
        protected $propias;
        
        public function __construct(){
            session_start();
            require('../hosts.php');
            require($this->conexion);
            $hosteo = new Host();
            $hosteo->obtener_conexion(2);
            $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
            $this->obtener_ofertas();
        }
        
        protected function obtener_ofertas(){
            
            $this->usuario = $_SESSION['id'];
            $this->sucursal = $_SESSION['sucursal'];
            $this->empresa = $_SESSION['empresa'];
            $this->propias = $_POST['propias'];
            $this->filtro = mysqli_real_escape_string($this->sql_con, $_POST['filtro']);
            if($this->propias == 'true'){
                $hosteo = new Host();
                $hosteo->obtener_conexion(0);
                $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
            }
            $this->obtener_filtro();
            $result_ofertas = $this->sql_con->query($this->query);
            if($result_ofertas === false){
                trigger_error("Ha ocurrido un error");
            }else{
                while($row_ofertas = $result_ofertas->fetch_assoc()){
                    $dato = array();
                    foreach($row_ofertas as $indice=>$valor){
                        $dato[$indice] = $valor;
                    }
                    array_push($this->datos, $dato);
                }
            }
        }
        
        protected function obtener_filtro(){
            $consulta_filtro = "SELECT filtro_oferta_id as id, filtro_query as query, filtro_alias as alias FROM filtro_oferta WHERE filtro_codigo = '$this->filtro'";
            $result_filtro = $this->sql_con->query($consulta_filtro);
            if($result_filtro === false){
                trigger_error("Ha ocurrido un error");
            }else{
                while($row_filtro = $result_filtro->fetch_assoc()){
                    $this->query = $row_filtro['query'];
                    $this->alias = $row_filtro['alias'];
                }
                $this->query = str_replace("xxsucursalxx", $this->sucursal, $this->query);
                $this->query = str_replace("xxempresaxx", $this->empresa, $this->query);
            }
        }
        
        protected function set_conexion($host, $user, $pass, $bd){
            $conexion = new Conexion();
            $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
            $this->sql_con->set_charset('utf8');
        }
        
        public function __destruct(){
            echo json_encode($this->datos);
        }
    
    }

?>