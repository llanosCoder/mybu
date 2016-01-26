<?php

$actualizar = new Actualizar();


class Actualizar{

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
		$this->actualizar_sitio();
		
	}
	
	
	protected function set_conexion($host, $user, $pass, $bd){
	
		$conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
	}
	
	
	protected function actualizar_sitio(){
	
		extract($_POST);
		$this->sql_con = new mysqli($this->link['host'], $this->link['user'],$this->link['pass'],$this->link['bd']);
        $this->sql_con->set_charset('utf8');
		$this->datos_usuario["web_estilo"] = mysqli_real_escape_string($this->sql_con, $web_estilo);
		$this->datos_usuario["empresa_id"] = mysqli_real_escape_string($this->sql_con, $empresa_id);
		$nuevo_estilo = $this->datos_usuario["web_estilo"];
		$this->actualizar_session($nuevo_estilo);
		
			$sql="update web set web_estilo='".$this->datos_usuario["web_estilo"]."' where usu_id='".$this->datos_usuario["empresa_id"]."'";
			$this->sql_con->set_charset('utf8');
			$reg= $this->sql_con->query($sql);
		
			if($reg)
				$this->datos["respuesta"] = 1;
			else
				$this->datos["respuesta"] = 2;
			
	}
	
	
	protected function actualizar_session($estilo){
		
		$_SESSION["estilo"] = $estilo;
	
	}
	
	 function __destruct(){
        echo json_encode($this->datos);
    }
	


}









?>