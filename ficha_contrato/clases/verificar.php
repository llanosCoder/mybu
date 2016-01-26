<?php

$verificar = new Verificar();


class Verificar{
	
	protected $link;
	protected $sql_con;
	protected $respuesta;
	protected $datos_usuario = array();
	
	
	public function __construct(){
		session_start();
        require('../../hosts.php');
        require('conexion_new.php');
        $hosteo = new Host();
        $hosteo->obtener_conexion(7);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
        $this->obtener_parametros();
		// $this->verificar_cliente();
	
	}
	
	
	protected function set_conexion($host, $user, $pass, $bd){
	
		$conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
	}
	
	protected function obtener_parametros(){
	
		extract($_POST);
		$this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
		$this->sql_con->set_charset('utf-8');
        
        
        // FOLIO
        $this->datos_usuario["fecha_contrato"] = mysqli_real_escape_string($this->sql_con, $fecha_contrato);
        $this->datos_usuario["nmr_folio"]      = mysqli_real_escape_string($this->sql_con, $nmr_folio);
        $this->datos_usuario["id_usuario"]     = mysqli_real_escape_string($this->sql_con, $id_usuario);
        $this->datos_usuario["name_user"]      = mysqli_real_escape_string($this->sql_con, $name_user);
        
		
		// parte 1
		$this->datos_usuario["tipo_cliente"] = 	mysqli_real_escape_string($this->sql_con, $tipo_cliente);
		$this->datos_usuario["apm"] =          	mysqli_real_escape_string($this->sql_con, $apm);
		$this->datos_usuario["app"] =          	mysqli_real_escape_string($this->sql_con, $app);
		$this->datos_usuario["nombre"] = 		mysqli_real_escape_string($this->sql_con, $nombre);
		$this->datos_usuario["rut"] =		    mysqli_real_escape_string($this->sql_con, $rut);
		$this->datos_usuario["email"] =			mysqli_real_escape_string($this->sql_con, $email);
		$this->datos_usuario["actividad"]=      mysqli_real_escape_string($this->sql_con, $actividad);
		$this->datos_usuario["rs"] = 			mysqli_real_escape_string($this->sql_con, $rs);
		$this->datos_usuario["nmb_fant"] = 		mysqli_real_escape_string($this->sql_con, $nmb_fant);
		$this->datos_usuario["email_empresa"] = mysqli_real_escape_string($this->sql_con, $email_empresa);
		$this->datos_usuario["rut_empresa"] = 	mysqli_real_escape_string($this->sql_con, $rut_empresa);
		$this->datos_usuario["representante"] = mysqli_real_escape_string($this->sql_con, $representante);
		$this->datos_usuario["rut_repres"] = 	mysqli_real_escape_string($this->sql_con, $rut_repres);
		$this->datos_usuario["giro"] = 			mysqli_real_escape_string($this->sql_con, $giro);
		
		// parte 2
		$this->datos_usuario["direccion"]=      mysqli_real_escape_string($this->sql_con, $direccion);
		$this->datos_usuario["tipo_calle"] = 	mysqli_real_escape_string($this->sql_con, $tipo_calle);
		$this->datos_usuario["entre_calles"] = 	mysqli_real_escape_string($this->sql_con, $entre_calles);
		$this->datos_usuario["villa"] = 		mysqli_real_escape_string($this->sql_con, $villa);
		$this->datos_usuario["tipo_dp"] = 		mysqli_real_escape_string($this->sql_con, $tipo_dp);
		$this->datos_usuario["piso"] = 			mysqli_real_escape_string($this->sql_con, $piso);
		$this->datos_usuario["block"] = 		mysqli_real_escape_string($this->sql_con, $block);
		$this->datos_usuario["comuna"] = 		mysqli_real_escape_string($this->sql_con, $comuna);
		$this->datos_usuario["ciudad"] = 		mysqli_real_escape_string($this->sql_con, $comuna);
		
		// parte 3
		$this->datos_usuario["nombre_contacto"] = 		mysqli_real_escape_string($this->sql_con, $nombre_contacto);
		$this->datos_usuario["mail_contacto"] = 		mysqli_real_escape_string($this->sql_con, $mail_contacto);
		$this->datos_usuario["tel_movil"] = 			mysqli_real_escape_string($this->sql_con, $tel_movil);
		$this->datos_usuario["tel_fijo"] = 				mysqli_real_escape_string($this->sql_con, $tel_fijo);
		
		// parte 4
		$this->datos_usuario["tipo_plan"] = 			mysqli_real_escape_string($this->sql_con, $tipo_plan);
		$this->datos_usuario["anos_servicio"] = 		mysqli_real_escape_string($this->sql_con, $anos_servicio);
		$this->datos_usuario["forma_pago"] = 			mysqli_real_escape_string($this->sql_con, $forma_pago);
        $this->datos_usuario["n_documento"]=            mysqli_real_escape_string($this->sql_con, $n_documento);
		$this->datos_usuario["nombre_proveedor"] = 		mysqli_real_escape_string($this->sql_con, $nombre_proveedor);
        $this->datos_usuario["vendedor"] = 			    mysqli_real_escape_string($this->sql_con, $vendedor);
        
        $this->datos_usuario["observacion"] =           mysqli_real_escape_string($this->sql_con, $observacion);
        
        
        $this->verificar_nmr_folio();
        
    
    }
    
    
    
    protected function verificar_nmr_folio(){
        
        $consulta="select * from ingreso where ing_nmr_folio='".$this->datos_usuario["nmr_folio"]."'";
        $verificar = $this->sql_con->query($consulta);
        $contar = $verificar->num_rows;
        
        
        if($contar > 0)
            $this->resultado = 6;
        else
            $this->verificar_cliente();
        
        
        
    }
    
    
    
    protected function verificar_cliente(){
		
		
		
		if(!(empty($this->datos_usuario["rut"]) 
            or empty($this->datos_usuario["fecha_contrato"])
            or empty($this->datos_usuario["nmr_folio"])
            or empty($this->datos_usuario["rut_empresa"]) 
            or empty($this->datos_usuario["rut_repres"])
            or empty($this->datos_usuario["tipo_cliente"]) 
            or empty($this->datos_usuario["apm"])
			or empty($this->datos_usuario["app"])  
            or empty($this->datos_usuario["nombre"])	
			or empty($this->datos_usuario["email"])  
            or empty($this->datos_usuario["rs"])
			or empty($this->datos_usuario["nmb_fant"]) 
            or empty($this->datos_usuario["email_empresa"])
			or empty($this->datos_usuario["representante"]) 
            or empty($this->datos_usuario["giro"])
			or empty($this->datos_usuario["actividad"]) 
            or empty($this->datos_usuario["direccion"]) 
            or empty($this->datos_usuario["tipo_calle"])
			or empty($this->datos_usuario["entre_calles"])  
            or empty($this->datos_usuario["villa"])	
			or empty($this->datos_usuario["comuna"])  
            or empty($this->datos_usuario["ciudad"])
            or empty($this->datos_usuario["nombre_contacto"]) 
            or empty($this->datos_usuario["mail_contacto"])
			or empty($this->datos_usuario["tel_movil"])  
            or empty($this->datos_usuario["tel_fijo"])
            or empty($this->datos_usuario["tipo_plan"]) 
            or empty($this->datos_usuario["anos_servicio"])
			or empty($this->datos_usuario["forma_pago"])  
            or empty($this->datos_usuario["n_documento"])
            or empty($this->datos_usuario["nombre_proveedor"])  
            or empty($this->datos_usuario["vendedor"])
            
            )){
		
			//or !($this->verificaremail($this->datos_usuario["email_empresa"]))
			if(!($this->verificaremail($this->datos_usuario["email"])) or !($this->verificaremail($this->datos_usuario["email_empresa"]))){
					
					$this->resultado = 4;
			
			}else{
			
					if( !($this->revisaRut($this->datos_usuario["rut_empresa"])) or !($this->validaRut($this->datos_usuario["rut_empresa"]))
                        or
                        !($this->revisaRut($this->datos_usuario["rut"])) or !($this->validaRut($this->datos_usuario["rut"]))
                        or
                        !($this->revisaRut($this->datos_usuario["rut_repres"])) or !($this->validaRut($this->datos_usuario["rut_repres"]))
                      ){
                        
                        $this->resultado = 5;
					
					}else{
		
						$consultar = "select * from cliente where cli_rut='".$this->datos_usuario['rut']."' ";
						$verificar = $this->sql_con->query($consultar);
						$si_existe = $verificar->num_rows;
						
						if($si_existe > 0 )
								
								$this->resultado = 2;
							
						else{
								$this->ingresar_parte1();
								$this->ingresar_parte2();
								$this->ingresar_parte3();
								$this->ingresar_parte4();
                                $this->ingresar_datosfolio();
						}
					}	
			}
		
		}else{
		       
					$this->resultado = 3;
		}
	}
	

	protected function ingresar_parte1(){
	
			
				    $this->sql_con->set_charset('utf-8');
					$ingresar="insert into cliente(cli_tipo_cliente,cli_app, cli_apm, cli_nombre, cli_rut, cli_mail, cli_actividad, cli_rsocial, cli_fantasia, cli_mail_emp, cli_rut_emp, cli_rep_legal, cli_rut_rep, cli_giro)";
					$ingresar.=" values(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
					$enviar = $this->sql_con->prepare($ingresar);
					$enviar->bind_param('ssssssssssssss',
					$this->datos_usuario["tipo_cliente"], 
					$this->datos_usuario["apm"],
					$this->datos_usuario["app"],
					$this->datos_usuario["nombre"] ,
					$this->datos_usuario["rut"],
					$this->datos_usuario["email"],
					$this->datos_usuario["actividad"],
					$this->datos_usuario["rs"],
					$this->datos_usuario["nmb_fant"],
					$this->datos_usuario["email_empresa"],
					$this->datos_usuario["rut_empresa"],
					$this->datos_usuario["representante"] ,
					$this->datos_usuario["rut_repres"],
					$this->datos_usuario["giro"]
					);
					$enviar->execute();
					$enviar->close();
					
						if($enviar)
							$this->resultado = 1;
						else
							$this->resultado = 0;
				
			
	
	
	}
	
	
	protected function ingresar_parte2(){
		
				    $this->sql_con->set_charset('utf-8');
					$ingresar="insert into direccion(dir_direccion,dir_tipocalle, dir_entrecalles, dir_valle, dir_tipodp, dir_piso, dir_block, dir_comuna, dir_ciudad, dir_cli_rut)";
					$ingresar.=" values(?,?,?,?,?,?,?,?,?,?)";
					$enviar = $this->sql_con->prepare($ingresar);
					$enviar->bind_param('ssssssssss',
					$this->datos_usuario["direccion"], 
					$this->datos_usuario["tipo_calle"],
					$this->datos_usuario["entre_calles"],
					$this->datos_usuario["villa"] ,
					$this->datos_usuario["tipo_dp"],
					$this->datos_usuario["piso"],
					$this->datos_usuario["block"],
					$this->datos_usuario["comuna"],
					$this->datos_usuario["ciudad"],
					$this->datos_usuario["rut"]
					);
					$enviar->execute();
					$enviar->close();
					
						if($enviar)
							$this->resultado = 1;
						else
							$this->resultado = 0;
				
			
	
	
	}
	
	
	protected function ingresar_parte3(){
		
				
				if(!($this->verificaremail($this->datos_usuario["mail_contacto"]))){
					
					$this->resultado = 4;
				
				}else{
				
					$ingresar="insert into contacto(con_nombre,con_mail, con_tmovil, con_tfijo, con_cli_rut)";
					$ingresar.=" values(?,?,?,?,?)";
					$enviar = $this->sql_con->prepare($ingresar);
					$enviar->bind_param('sssss',
					$this->datos_usuario["nombre_contacto"], 
					$this->datos_usuario["mail_contacto"],
					$this->datos_usuario["tel_movil"],
					$this->datos_usuario["tel_fijo"],
					$this->datos_usuario["rut"]
					);
					$enviar->execute();
					$enviar->close();
					
						if($enviar)
							$this->resultado = 1;
						else
							$this->resultado = 0;
				}
			
	
	
	}
	
	
	protected function ingresar_parte4(){
                    $this->sql_con->set_charset('utf-8');
					$ingresar="insert into servicio(serv_tipoplan,serv_anos, serv_formapago, serv_ndoc,serv_nombre_proveedor,serv_vendedor,serv_obs, serv_cli_rut)";
					$ingresar.=" values(?,?,?,?,?,?,?,?)";
					$enviar = $this->sql_con->prepare($ingresar);
					$enviar->bind_param('iisissss',
					$this->datos_usuario["tipo_plan"], 
					$this->datos_usuario["anos_servicio"],
					$this->datos_usuario["forma_pago"],
					$this->datos_usuario["n_documento"],
                    $this->datos_usuario["nombre_proveedor"],
                    $this->datos_usuario["vendedor"],
                    $this->datos_usuario["observacion"],
					$this->datos_usuario["rut"]
					);
					$enviar->execute();
					$enviar->close();
					
						if($enviar)
							$this->resultado = 1;
						else
							$this->resultado = 0;
				
			 
	
	
	}
    
    
    protected function ingresar_datosfolio(){
        
        $this->sql_con->set_charset('utf-8');
        $ingresar = "insert into ingreso(ing_fecha_contrato, ing_nmr_folio, ing_ingreso, ing_id_usuario, ing_usuario, ing_rut_usu)";
        $ingresar.= " values(?, ?, ?, ?, ?, ?)";
        $enviar = $this->sql_con->prepare($ingresar);
        date_default_timezone_set("America/Santiago");
        $fecha = date('Y-m-d H:i:s');
        $enviar->bind_param('sisiss',
            $this->datos_usuario["fecha_contrato"],
            $this->datos_usuario["nmr_folio"],
            $fecha,
            $this->datos_usuario["id_usuario"],
            $this->datos_usuario["name_user"],
            $this->datos_usuario["rut"]                
        );
        
        $enviar->execute();
        $ins_id = mysqli_insert_id($this->sql_con);
        $this->ingresar_estado($ins_id);
        $enviar->close();
					
                        if($enviar)
							$this->resultado = 1;
						else
							$this->resultado = 0;
        
        
    }
    
    protected function ingresar_estado($id){
        $insercion = $this->sql_con->prepare("INSERT INTO ingreso_estado (ingreso_id, ingreso_estado) VALUES (?, 0)");
        $insercion->bind_param('i', $id);
        $insercion->execute();
        $insercion->close();
    }
    
    
    protected function verificaremail($email){ 
          if (!ereg("^([a-zA-Z0-9._]+)@([a-zA-Z0-9.-]+).([a-zA-Z]{2,4})$",$email)){ 
              return FALSE; 
          } else { 
               return TRUE; 
          } 
    }
    
    
   function y($rut){
       
            $RegExp = '/^([0-9])+\-([kK0-9])+$/';
                if (!preg_match($RegExp, $rut)){
                   return false;
            }else{
                if(strpos($rut,"-")==false){
                    $RUT[0] = substr($rut, 0, -1);
                    $RUT[1] = substr($rut, -1);
                }else{
                    $RUT = explode("-", trim($rut));
                }
                $elRut = str_replace(".", "", trim($RUT[0]));
                $factor = 2;
                for($i = strlen($elRut)-1; $i >= 0; $i--):
                    $factor = $factor > 7 ? 2 : $factor;
                    $suma += $elRut{$i}*$factor++;
                endfor;
                $resto = $suma % 11;
                $dv = 11 - $resto;
                if($dv == 11){
                    $dv=0;
                }else if($dv == 10){
                    $dv="k";
                }else{
                    $dv=$dv;
                }
               if($dv == trim(strtolower($RUT[1]))){
                   return true;
               }else{
                   return false;
               }
                    
                    
            }
       
       
    }
    
    
    
    
    function m($rut) {
    $rut=str_replace('.', '', $rut);
        if (preg_match('/^(\d{1,9})-((\d|k|K){1})$/',$rut,$d)) {
            $s=1;$r=$d[1];for($m=0;$r!=0;$r/=10)$s=($s+$r%10*(9-$m++%6))%11;
            return chr($s?$s+47:75)==strtoupper($d[2]);
        }
    } 
    
    
    function revisaRut($rut){
        $permitidos = "kK0123456789-";
        $res=true;
        for ($i=0; $i<strlen($rut); $i++){
              if (strpos($permitidos, substr($rut,$i,1))===false){
                 $res = false;
              }
        }
        return $res;
    }

    function validaRut($rut){ 
        $suma=0;
        if(strpos($rut,"-")==false){
            $RUT[0] = substr($rut, 0, -1);
            $RUT[1] = substr($rut, -1);
        }else{
            $RUT = explode("-", trim($rut));
        }
        $elRut = str_replace(".", "", trim($RUT[0]));
        $factor = 2;
        for($i = strlen($elRut)-1; $i >= 0; $i--):
            $factor = $factor > 7 ? 2 : $factor;
            $suma += $elRut{$i}*$factor++;
        endfor;
        $resto = $suma % 11;
        $dv = 11 - $resto;
        if($dv == 11){
            $dv=0;
        }else if($dv == 10){
            $dv="k";
        }else{
            $dv=$dv;
        }
       if($dv == trim(strtolower($RUT[1]))){
           return true;
       }else{
           return false;
       }
    } 
    
	
	
	
	 public function __destruct(){
            echo $this->resultado;
     }
	 
	
   


}


?>