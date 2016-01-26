<?php


$traerfichas = new TraerFichas();


class TraerFichas{
    
    protected $link;
    protected $sql_con;
    protected $arreglo = array();
    
    
    
    public function __construct(){
		session_start();
        require('../../hosts.php');
        require('conexion_new.php');
        $hosteo = new Host();
        $hosteo->obtener_conexion(7);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
		$this->traer_fichas();
		
	}
    
    protected function set_conexion($host, $user, $pass, $bd){
	
		$conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
	}
    
    protected function traer_fichas(){
        extract($_POST);
        
        if(isset($user)){
            $this->arreglo['cliente']=array();
            $this->arreglo['contacto']=array();
            $this->arreglo['producto']=array();

            $consulta = "select * from cliente";
            $con = $this->sql_con->query($consulta);

            while($arr = $con->fetch_assoc()){

                $clientes = array();
                $clientes = array(
                    "cli_rut"=>$arr["cli_rut"],
                    "cli_nombre"=>$arr["cli_nombre"],
                    "cli_app"=>$arr["cli_app"],
                    "cli_apm"=>$arr["cli_apm"],
                    "cli_id"=>$arr["cli_id"],
                    "cli_fantasia"=>$arr["cli_fantasia"],
                    "cli_rut_emp"=>$this->rut($arr["cli_rut_emp"])
                );

                array_push($this->arreglo["cliente"], $clientes);
            }


            $contacto = "select * from contacto";
            $con = $this->sql_con->query($contacto);

            while($row=$con->fetch_assoc()){

                $contactos = array();
                $contactos =array(
                        "con_rut"=>$row["con_cli_rut"],
                        "con_nombre"=>$row["con_nombre"],
                        "con_mail"=>$row["con_mail"],
                        "con_tmovil"=>$row["con_tmovil"],
                        "con_tfijo"=>$row["con_tfijo"]
                );

                array_push($this->arreglo["contacto"], $contactos);

            }
            
            $producto = "select * from servicio";
            $con = $this->sql_con->query($producto);
            
            while($row=$con->fetch_assoc()){
                
                $servicios = array();
                $servicios = array(
                    
                            "serv_tipoplan"=>$this->nombre_plan($row["serv_tipoplan"]),
                            "serv_cli_rut"=>$row["serv_cli_rut"],
                            "serv_nombre_proveedor"=>$row["serv_nombre_proveedor"],
                            "vendedor"=>$row["serv_vendedor"]
                    
                );
                
                array_push($this->arreglo["producto"], $servicios);
            }
        
        }
        
        
    }
    
    
    
    protected function rut( $rut ) {
        return number_format( substr ( $rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $rut, strlen($rut) -1 , 1 );
    }
    
    protected function nombre_plan($tipo){
        
        $plan = "";
        
        switch ($tipo){
            
            case 1:
                $plan = "INICIO";
            break;
            
            
        }
        return $plan;
        
    }
    
    
    function __destruct(){
        
        echo json_encode($this->arreglo);   
        
    }
    
    
    
    
    
    
}



?>