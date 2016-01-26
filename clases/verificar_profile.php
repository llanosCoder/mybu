<?php

$verificar_profile = new VerificarProfile();

class VerificarProfile{

	protected $link;
	protected $sql_con;
	protected $datos = array();
	
	
	public function __construct(){
		session_start();
        require('../hosts.php');
        require('conexion_new.php');
        $hosteo = new Host();
        $hosteo->obtener_conexion(5);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
		$this->verificar_profile();
		
	}
	
	
	protected function set_conexion($host, $user, $pass, $bd){
	
		$conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
	}
	
	protected function verificar_profile(){
	
		extract($_GET);
		$_SESSION["username"]=ereg_replace('[^A-Za-z0-9 %20]', "",$usuario);;
		$this->sql_con = new mysqli($this->link['host'], $this->link['user'],$this->link['pass'],$this->link['bd']);
        $this->sql_con->set_charset('utf8');
		
		$this->datos["usuario"]=mysqli_real_escape_string($this->sql_con, $usuario);
		$usuario = $this->datos["usuario"];
		
		$consulta="select * from usuario where usu_nombre='".$this->datos["usuario"]."'";
		$this->sql_con->set_charset("utf8");
		$verificar = $this->sql_con->query($consulta);
		$cantidad = $verificar->num_rows;
		

		
			if($cantidad > 0){
				
				$cons="select w.usu_id,w.web_estilo from usuario u join web w on w.usu_id=u.usu_id where u.usu_nombre='".$this->datos["usuario"]."'";
				$this->sql_con->set_charset("utf8");
				$verif = $this->sql_con->query($cons);
				
					if($arr=mysqli_fetch_array($verif, MYSQLI_ASSOC)){
					
						$_SESSION["usuario"] = $arr["usu_id"];
						$_SESSION["estilo"]  = $arr["web_estilo"];
						$usu_id = $arr["usu_id"];
					}
					
				$visitas = "select * from visita where usu_id='".$usu_id."'";
				$this->sql_con->set_charset("utf8");
				$v = $this->sql_con->query($visitas);
				$cantidad_visita = $v->num_rows;
				
				if($cantidad_visita > 0){
					
					$actualizar = "update visita set cant_visita=((cant_visita)+1) where usu_id = '".$usu_id."'";
					$revisar = $this->sql_con->query($actualizar);
					$filas_afectadas = $this->sql_con->affected_rows;
					
						if($filas_afectadas != 0){
							
							header('Location: http://www.nfnempresas.com/web-builder/profile.php?username='.$usuario);	
							
						}
					
				
				}else{
					$cant_visita = 1;
					$insertar = $this->sql_con->prepare("insert into visita (usu_id,cant_visita) values (?, ?)");
					$insertar->bind_param('ii',
						$_SESSION["usuario"],
						$cant_visita);
					$insertar->execute();
					$insertar->close();
					
					$filas_afectadas = $this->sql_con->affected_rows;
				
						if($filas_afectadas != 0){
							
							header('Location: http://www.nfnempresas.com/web-builder/profile.php?username='.$usuario);
						}
				}
				
					
				
					
				
			}else{
				unset($_SESSION['username']);
				header('Location: http://www.nfn.cl/');

			}
		
	}
	


}
	

	
?>