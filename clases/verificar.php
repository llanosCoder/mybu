<?php

$verificar = new Verificar();


class Verificar{

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
		$this->verificar_usuario();
		
	}
	
	
	protected function set_conexion($host, $user, $pass, $bd){
	
		$conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
	}


	function verificar_usuario(){
		extract($_POST);
		$this->sql_con = new mysqli($this->link['host'], $this->link['user'],$this->link['pass'],$this->link['bd']);
        $this->sql_con->set_charset('utf8');
		$this->datos_usuario["empresa_id"]=mysqli_real_escape_string($this->sql_con, $empresa_id);
		$this->datos_usuario["web_estilo"]=mysqli_real_escape_string($this->sql_con, $web_estilo);
		
		$verif="select * from usuario where usu_id='".$this->datos_usuario["empresa_id"]."'";
		$this->sql_con->set_charset('utf8');
		$res = $this->sql_con->query($verif);
		$cant_usu= $res->num_rows;
		
		
		if($cant_usu > 0){
			
				$consulta="select * from web where web_estilo='".$this->datos_usuario["web_estilo"]."' and usu_id='".$this->datos_usuario["empresa_id"]."'";
				$this->sql_con->set_charset("utf8");
				$verificar = $this->sql_con->query($consulta);
				$usu_id = $this->datos_usuario["empresa_id"];
				$nuevo_estilo=$this->datos_usuario["web_estilo"];
				$this->actualizar_session($nuevo_estilo, $usu_id);
				$cantidad= $verificar->num_rows;
				
				if($cantidad > 0)
					$this->datos["respuesta"] = 1;
				else
					$this->datos["respuesta"] = 2;
					
				
		}else{
			$this->datos["respuesta"] = 3;
		}
		
		
		
	}
	
	
    protected function actualizar_session($estilo, $usu){
		$_SESSION["usuario"] = $usu;
		$_SESSION["estilo"] = $estilo;
	}

	 function __destruct(){
        echo json_encode($this->datos);
    }

}



?>