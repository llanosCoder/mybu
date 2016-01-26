<?php
session_start();

if(isset($_GET["username"])){


	$usuario=$_GET["username"];
	$username_session = $_SESSION["username"];
	$estilo = $_SESSION["estilo"];
    
    //Validacion doble slash
    if (strpos($_GET["username"],'/') !== false) {
        $lugar = ereg_replace('[^A-Za-z0-9]', "",$_GET["username"] );
		$usuario = $lugar;
        header('Location: http://www.nfnempresas.com/'.$lugar);
    }
	
	
	//echo dirname(__FILE__);
	
	if($usuario==$username_session){
        header('Location: web-builder/sitio'.$estilo.'.php');
		//include("web-builder/sitio".$estilo.".php");
		//echo readfile("http://www.nfn.cl");

		
	}else{
	
		header('Location: clases/verificar_profile.php?usuario='.$usuario);
	
	}
	
	

}
?>
