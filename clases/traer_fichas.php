<?php


$traerfichas = new TraerFichas();


class TraerFichas{
    
    protected $link;
    protected $sql_con;
    protected $arreglo = array();
    
    
    
    public function __construct(){
		session_start();
        ini_set('display_errors', 'off');
        require('../hosts.php');
        require('../classes/conexion_new.php');
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
        
        $this->datos_usuario["tipo"]=mysqli_real_escape_string($this->sql_con, $tipo);
        $this->datos_usuario["nmr_folio"]=mysqli_real_escape_string($this->sql_con, $nmr_folio);
        
        switch($this->datos_usuario["tipo"]){
            
            case 1:
                $this->traer_todo();
            break;
            
        }
        
        if(isset($user)){
            $this->arreglo['cliente']=array();
            //$this->arreglo['contacto']=array();
            //$this->arreglo['producto']=array();
            //$this->arreglo['folio']=array();
            
           if($rol != 1){

                $consulta = "select * from cliente c left join ingreso i on i.ing_rut_usu = c.cli_rut";
                $consulta.=" left join contacto co on co.con_cli_rut=c.cli_rut";
                $consulta.=" left join servicio s on s.serv_cli_rut=c.cli_rut";
                $consulta.=" left join ingreso_estado ie on i.ing_id = ie.ingreso_id";
                
            if($rol==2)
                $consulta.=" where i.ing_id_usuario='".$user."'";
            
                
           }
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
                    "cli_rut_emp"=>$this->rut($arr["cli_rut_emp"]),
                    "nmr_folio"=>$arr["ing_nmr_folio"],
                    "fecha_ing"=>$arr["ing_fecha_contrato"],
                    "con_nombre"=>$arr["con_nombre"],
                    "con_mail"=>$arr["con_mail"],
                    "con_tmovil"=>$arr["con_tmovil"],
                    "con_tfijo"=>$arr["con_tfijo"],
                    "serv_tipoplan"=>$this->nombre_plan($arr["serv_tipoplan"]),
                    "serv_cli_rut"=>$arr["serv_cli_rut"],
                    "serv_nombre_proveedor"=>$arr["serv_nombre_proveedor"],
                    "vendedor"=>$arr["serv_vendedor"],
                    "estado"=>$arr["ingreso_estado"]
                );

                array_push($this->arreglo["cliente"], $clientes);
            }

        }
        
        
    }
    
    
    protected function traer_todo(){
        
         $this->arreglo["todo"] = array(); 
        
         $consulta = "select * from cliente c left join ingreso i on i.ing_rut_usu = c.cli_rut";
         $consulta.=" left join contacto co on co.con_cli_rut=c.cli_rut";
         $consulta.=" left join servicio s on s.serv_cli_rut=c.cli_rut";
         $consulta.=" left join direccion d on d.dir_cli_rut=c.cli_rut where i.ing_nmr_folio = '".$this->datos_usuario["nmr_folio"]."'";
        
         $traer = $this->sql_con->query($consulta);
        
         while($arr = $traer->fetch_assoc()){
                
             $todo = array();
             
             $todo = array(                   
                    "cli_rut"=>$arr["cli_rut"],
                    "cli_nombre"=>$arr["cli_nombre"],
                    "cli_app"=>$arr["cli_app"],
                    "cli_apm"=>$arr["cli_apm"],
                    "cli_id"=>$arr["cli_id"],
                    "cli_fantasia"=>$arr["cli_fantasia"],
                    "cli_rut_emp"=>$this->rut($arr["cli_rut_emp"]),
                    "cli_mail"=>$arr["cli_mail"],
                    "cli_actividad"=>$arr["cli_actividad"],
                    "cli_rsocial"=>$arr["cli_rsocial"],
                    "cli_fantasia"=>$arr["cli_fantasia"],
                    "cli_mail_emp"=>$arr["cli_mail_emp"],
                    "cli_rut_emp"=>$arr["cli_rut_emp"],
                    "cli_rep_legal"=>$arr["cli_rep_legal"],
                    "cli_rut_rep"=>$arr["cli_rut_rep"],
                    "cli_giro"=>$arr["cli_giro"],
                    "con_nombre"=>$arr["con_nombre"],
                    "con_mail"=>$arr["con_mail"],
                    "con_tmovil"=>$arr["con_tmovil"],
                    "con_tfijo"=>$arr["con_tfijo"],
                    "dir_direccion"=>$arr["dir_direccion"],
                    "dir_tipocalle"=>$arr["dir_tipocalle"],
                    "dir_entrecalles"=>$arr["dir_entrecalles"],
                    "dir_valle"=>$arr["dir_valle"],
                    "dir_tipodp"=>$this->tipo_calle($arr["dir_tipodp"]),
                    "dir_piso"=>$arr["dir_piso"],
                    "dir_block"=>$arr["dir_block"],
                    "dir_comuna"=>$arr["dir_comuna"],
                    "dir_ciudad"=>$arr["dir_ciudad"],
                    "serv_tipoplan"=>$this->nombre_plan($arr["serv_tipoplan"]),
                    "serv_anos"=>$arr["serv_anos"],
                    "serv_formapago"=>$arr["serv_formapago"],
                    "serv_ndoc"=>$arr["serv_ndoc"],
                    "serv_nombre_proveedor"=>$arr["serv_nombre_proveedor"],
                    "serv_vendedor"=>$arr["serv_vendedor"],
                    "serv_obs"=>utf8_encode($arr["serv_obs"]),
                    "ing_fecha_contrato"=>$arr["ing_fecha_contrato"],
                    "ing_nmr_folio"=>$arr["ing_nmr_folio"],
                    "ing_ingreso"=>$arr["ing_ingreso"],
                    "ing_usuario"=>$arr["ing_usuario"]
                 
             );
             
             array_push($this->arreglo["todo"], $todo);
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
    
    
    protected function tipo_calle($tipo){
        
        $calle = "";
        
        switch ($tipo){
            
            case 1:
                $calle = "DEPARTAMENTO";
            break;
            
            case 2:
                $calle = "OFICINA";
            break;
            
            
        }
        return $calle;
        
    }
    
    

    
    
    function __destruct(){
        
        echo json_encode($this->arreglo);   
        
    }
    
    
    
    
    
    
}



?>