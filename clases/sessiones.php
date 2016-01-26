<?php

$sessiones = new Sessiones();


class Sessiones{

	protected $parametros = array();
    protected $datos = array();
	
	
	public function __construct(){
		session_start();
		$this->buscar_sesion();
		$this->obtener_sesion();
	
	}
	
	
	protected function buscar_sesion(){
		extract($_POST);
		$this->parametros = $parametros;
	
	}
	
	protected function obtener_sesion(){
		$dato = array();
		foreach($this->parametros as $valor){
			
			if(isset($_SESSION[$valor]))
				$dato[$valor] = $_SESSION[$valor];
			else
				$dato[$valor] = 0;
		
		}
		array_push($this->datos, $dato);
	
	}
	
	 public function __destruct(){
        echo json_encode($this->datos);
    }


}


?>