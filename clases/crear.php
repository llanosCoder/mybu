<?php

$crear = new Crear();


class Crear{

	protected $link;
	protected $sql_con;
	protected $datos = array();
	protected $datos_usuario = array();

	
	
	public function __construct(){
		session_start();
        require('../hosts.php');
        require('conexion_new.php');
        $hosteo = new Host();
        $hosteo->obtener_conexion(5);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
		$this->crear_sitio();
		
	}
	
	
	protected function set_conexion($host, $user, $pass, $bd){
	
		$conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
	}
	
	
	
	protected function crear_sitio(){
		
		extract($_POST);
		$this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
		
		$this->datos_usuario["empresa_id"]=mysqli_real_escape_string($this->sql_con, $empresa_id);
		$this->datos_usuario["web_estilo"]=mysqli_real_escape_string($this->sql_con, $web_estilo);
		$this->datos_usuario["nombre_empresa"]=mysqli_real_escape_string($this->sql_con, $nombre_empresa);
		
		$c = "select * from usuario where usu_id='".$this->datos_usuario["empresa_id"]."'";
		$ver = $this->sql_con->query($c);
		$existe = $ver->num_rows;
		
		if($existe > 0){
			
			$this->datos["respuesta"] = 1;
			
		}else{
			$empresa_id = $this->datos_usuario["empresa_id"];
			$web_estilo = $this->datos_usuario["web_estilo"];
			$nombre_empresa = $this->datos_usuario["nombre_empresa"];
			$this->datos_web($empresa_id);
			$this->crear_estilo($web_estilo, $empresa_id);
			$this->crear_usuario($nombre_empresa, $empresa_id);
		
		}
	
	
	}
	
	
	protected function datos_web($empresa_id){
		
		$telefono = "123456";
		$mail = "mail@dominio.cl";
		$titulo1 ="Titulo 1";
		$subtitulo1 = "Subitulo 1";
		$texto1 = "Texto";
		
		$boxes1tittle = "Titulo 1";
		$boxes2tittle = "Titulo 2";
		$boxes3tittle = "Titulo 3";
		$boxes4tittle = "Titulo 4";
		
		$boxes1description = "Subtitulo 1";
		$boxes2description = "Subtitulo 2";
		$boxes3description = "Subtitulo 3";
		$boxes4description = "Subtitulo 4";
		
		$company = "NFN";
		$contact_descripcion = "Contactanos";
		$contact_dire1 = "Dir N-";
		$contact_dire2 = "Santiago";
		$contact_dire3 = "Chile";
		$contact_telefono = "Telefono -123456";
		
		$web="insert into dato(telefono,mail,titulo1,subtitulo1,texto1,boxes1tittle,boxes2tittle,boxes3tittle,boxes4tittle,";
		$web.="boxes1description,boxes2description,boxes3description,boxes4description,company,contact_descripcion,contact_dire1,contact_dire2,contact_dire3,contact_telefono,usu_id)";
		$web.=" values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		
		$insertar = $this->sql_con->prepare($web);
		$insertar->bind_param('issssssssssssssssssi',$telefono,$mail,$titulo1,$subtitulo1,$texto1,$boxes1tittle,$boxes2tittle,$boxes3tittle,$boxes4tittle,$boxes1description,$boxes2description,$boxes3description,$boxes4description,$company,$contact_descripcion,$contact_dire1,$contact_dire2,$contact_dire3,$contact_telefono,$empresa_id);
		$insertar -> execute();
		$insertar -> close();
		
				if($insertar){
					$this->datos["respuesta"] = 2;
					$this->crear_icono($empresa_id);
				}else{
					$this->datos["respuesta"] = 3;
				}
		
	
	}
	
	
	protected function crear_icono($empresa_id){
		
		$iconos = "change-icon fa  fa-ils";
		$variable = "";
		
			for($i=1;$i<=4;$i++){
				$insertar1 = $this->sql_con->prepare("insert into icono(icono, usu_id) values(?, ?)");
				$insertar1->bind_param('si',$iconos,$empresa_id);
				$insertar1->execute();
				$insertar1->close();
				
				if($insertar1){
					$this->datos["respuesta"] = 2;
					$variable = 1;
				}else{
					$this->datos["respuesta"] = 3;
					$variable = 0;
				}
			}
			
		if($variable == 1){
		
			$this->crear_imagen($empresa_id);
		
		}
	
	
	}
	
	
	protected function crear_imagen($empresa_id){
		
		$ruta_imagen = "imagenes/ejemplo.jpg";
		$variable = "";

		for($j=1;$j<=4;$j++){
			
			$insertar = $this->sql_con->prepare("insert into imagen(img_ruta, usu_id) values(?, ?)");
			$insertar->bind_param('si',$ruta_imagen,$empresa_id);
			$insertar->execute();
			$insertar->close();
			
				if($insertar){
					$this->datos["respuesta"] = 2;
					$variable = 1;
				}else{
					$this->datos["respuesta"] = 3;
					$variable = 0;
				}
			
		}
		
		if($variable == 1){
			
			$ds_titulo = "Titulo";
			$ds_subtitulo = "Edicion";
			$ds_mensaje = "Desarrollado";
			
			$consulta = "select * from imagen where usu_id='".$empresa_id."'";
			$cons = $this->sql_con->query($consulta);
			$this->sql_con->set_charset('utf8');
			$m = 0;
			// datos slider
			while($arr = $cons->fetch_assoc()){
				$m++;
				$insertar2 = $this->sql_con->prepare("insert into datoslider (ds_titulo,ds_subtitulo,ds_mensaje,img_id,usu_id) values (?, ?, ?, ?, ?)");
				$insertar2->bind_param('sssii',
				$ds_titulo,
				$ds_subtitulo,
				$ds_mensaje,
				$img_id = $arr['img_id'],
				$empresa_id);
				$insertar2->execute();
				$insertar2->close();
				
					if($insertar2){
						$this->datos["respuesta"] = 2;
					}else{
						$this->datos["respuesta"] = 3;
					}
			}
			
			
			$this->crear_logo($empresa_id);
			
		}
	
	
	}
	
	
	protected function crear_logo($empresa_id){
		
		$logo_ruta = "imagenes/logo.png";
		$variable = "";
		
		$insertar = $this->sql_con->prepare("insert into logo(log_ruta,usu_id) values(?, ?)");
		$insertar->bind_param('si',
		$logo_ruta,
		$empresa_id);
		$insertar->execute();
		$insertar->close();
	
		
			if($insertar){
				$this->datos["respuesta"] = 2;
				$variable=1;
			}else{
				$this->datos["respuesta"] = 3;
				$variable=0;
			}
		
		if($variable == 1){
			
			$this->crear_menu($empresa_id);
		
		}
	
	}
	
	
	protected function crear_menu($empresa_id){
	
		$menu1 = "Inicio";
		$menu2 = "Quienes Somos";
		$menu3 = "Que Hacemos";
		$menu4 = "Contactanos";
		$variable = "";
		
		$insertar = $this->sql_con->prepare("insert into menu(menu1,menu2,menu3,menu4,usu_id) values (?, ?, ?, ?, ?)");
		$insertar->bind_param('ssssi',
			$menu1,
			$menu2,
			$menu3,
			$menu4,
			$empresa_id);
		$insertar->execute();
		$insertar->close();
		
			if($insertar){
				$this->datos["respuesta"] = 2;
				$variable=1;
			}else{
				$this->datos["respuesta"] = 3;
				$variable=0;
			}
		
		if($variable == 1){
			
			$this->crear_noticia($empresa_id);
		
		}

	
	}
	
	protected function crear_noticia($empresa_id){
	
	
		$fecha=date("Y-m-d H:i:s");
		$noticia="Noticia";
		$variable = "";
		
		

		for($k=1;$k<=4;$k++){
			
			$insertar = $this->sql_con->prepare("insert into noticia(noticia,not_fech,usu_id) values (?, ?, ?)");
			$insertar->bind_param('ssi',
				$noticia,
				$fecha,
				$empresa_id);
	
			$insertar->execute();
			$insertar->close();
			
			if($insertar){
				$this->datos["respuesta"] = 2;
				$variable=1;
			}else{
				$this->datos["respuesta"] = 3;
				$variable=0;
			}	
			
		}
		
		if($variable == 1){
			
			//$this->crear_estilo($empresa_id);
		
		}
	
	
	}
	
	protected function crear_estilo($web_estilo, $empresa_id){
		
		$insertar = $this->sql_con->prepare("insert into web(web_estilo,usu_id) values (?, ?)");
		$insertar->bind_param('ii',
			$web_estilo,
			$empresa_id);
		$insertar->execute();
		$insertar->close();
		
			if($insertar)
				$this->datos["respuesta"] = 2;
			else
				$this->datos["respuesta"] = 3;

	
	}
	
	protected function crear_usuario($nombre_empresa, $empresa_id){
	
		$insertar = $this->sql_con->prepare("insert into usuario(usu_id,usu_nombre) values(?, ?)");
		$insertar->bind_param('is',
			$empresa_id,
			$this->limpia_espacios($nombre_empresa));
		$insertar->execute();
		$insertar->close();

		if($insertar)
			$this->datos["respuesta"] = 2;
		else
			$this->datos["respuesta"] = 3;
	
	
	}
	
    
    protected function limpia_espacios($cadena){
	   $cadena = str_replace(' ', '', $cadena);
	   return $cadena;
    }
	
	

	function __destruct(){
        echo json_encode($this->datos);
    }

}


?>