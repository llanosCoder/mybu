<?php


$sucursal = new Sucursal();


class Sucursal{
    

        protected $link;
        protected $sql_con;
        protected $datos_usuario = array();
        protected $datos = array();
        protected $empresa;
        
        public function __construct(){
            session_start();
            require('../hosts.php');
            require('conexion_new.php');
            ini_set('display_errors', 'on');
            $hosteo = new Host();
            $hosteo->obtener_conexion(6);
            $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
            $this->obtener_parametros_sucursal();
        }
        
        protected function set_conexion($host, $user, $pass, $bd){
            $conexion = new Conexion();
            $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
            $this->sql_con->set_charset('utf8');
        }
    
        protected function obtener_parametros_sucursal(){
        
            extract($_POST);
            $this->empresa = $_SESSION["empresa"];
            
            $this->datos_usuario['direccion'] = mysqli_real_escape_string($this->sql_con, $direccion);
            $this->datos_usuario['comuna'] = mysqli_real_escape_string($this->sql_con, $comuna);
            $this->datos_usuario['region'] = mysqli_real_escape_string($this->sql_con, $region);
            $this->datos_usuario['pais'] = mysqli_real_escape_string($this->sql_con, $pais);
            $this->datos_usuario['ciudad'] = mysqli_real_escape_string($this->sql_con, $ciudad);
            
            //print_r($this->datos_usuario["direccion"]." ".$this->datos_usuario["comuna"]." ".$this->datos_usuario["region"]." ".$this->datos_usuario["pais"]." ".$this->datos_usuario["ciudad"]);
            
            if($this->datos_usuario["direccion"]!="" or $this->datos_usuario['comuna']!=""
                or $this->datos_usuario["region"]!="" or $this->datos_usuario['pais']!=""){
                
                $this->insertar_surucursal();
                
            }

        }
    
    
        protected function insertar_surucursal(){

            //$this->datos["respuesta"] = 1;
            $this->sql_con->set_charset('utf8');

        $ingresar="insert into sucursal(empresa_id,sucursal_direccion,sucursal_comuna,sucursal_ciudad,sucursal_region,sucursal_pais)";
        $ingresar.="values(?,?,?,?,?,?)";

            $enviar = $this->sql_con->prepare($ingresar);   
            $enviar->bind_param('isiiii',
                 $this->empresa,                
                 $this->datos_usuario["direccion"],
                 $this->datos_usuario["comuna"],
                 $this->datos_usuario["ciudad"],
                 $this->datos_usuario["region"],
                 $this->datos_usuario["pais"]
             );

            $enviar->execute();
            $enviar->close();
            
            if($enviar)
                $this->datos["respuesta"] = 1;
            else
                $this->datos["respuesta"] = 0;



        }
    
        public function __destruct(){
            echo json_encode($this->datos);
        }
    
    
    
    
}




?>