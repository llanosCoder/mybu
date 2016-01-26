<?php

$session_sitio = new SessionSitio();


class SessionSitio{

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
		$this->session_sitio();
		
	}
	
	
	protected function set_conexion($host, $user, $pass, $bd){
	
		$conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
	}
	
	
	
	protected function session_sitio(){
		
		extract($_POST);
		$this->sql_con = new mysqli($this->link['host'], $this->link['user'],$this->link['pass'],$this->link['bd']);
        $this->sql_con->set_charset('utf8');
		$this->datos_usuario["usuario"]=mysqli_real_escape_string($this->sql_con, $usuario);
		
		$consulta="select * from usuario where usu_id='".$this->datos_usuario["usuario"]."'";
		$this->sql_con->set_charset('utf8');
		$verificar = $this->sql_con->query($consulta);
		$cantidad=$verificar->num_rows;
		
		if($cantidad > 0){

				$cons="select w.usu_id,w.web_estilo from usuario u join web w on w.usu_id=u.usu_id where u.usu_id='".$this->datos_usuario["usuario"]."'";
				$this->sql_con->set_charset("utf8");
				$verif = $this->sql_con->query($cons);
				
					if($arr=mysqli_fetch_array($verif, MYSQLI_ASSOC)){
					
						$_SESSION["usuario"]=$arr["usu_id"];
						$_SESSION["estilo"]=$arr["web_estilo"];
						$this->datos["estilo"]=$arr["web_estilo"];
					}
			}
	
	}
	
	
	 function __destruct(){
        echo json_encode($this->datos);
    }


}


?>