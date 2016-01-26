<?php

class EnviarCorreo{

        function enviar_correo($mensaje, $asunto, $para, $de){  
            
        $mensaje_contacto = "$mensaje";

        $asunto  = "$asunto";

        $casilla = "$para";

        $cabeceras = "From: $de\r\n";


        $cabeceras .= "MIME-Version: 1.0\r\n";

        $cabeceras .= "Content-Type: text/html; charset=UTF-8\r\n";



        $mensaje = '<!doctype html>';
        $mensaje .='<html>';
        $mensaje .='<head>';
        $mensaje .='<meta charset="UTF-8">';
        $mensaje .='<title>Contacto WEB</title>';
        $mensaje .='<meta name="viewport" content="width=device-width, initial-scale=1">';
        $mensaje .='<link rel="stylesheet" href="http://nfn.cl/src/bootstrap.css" media="screen">';
        $mensaje .='<link rel="stylesheet" href="http://nfn.cl/src/app.css" media="screen">';
        $mensaje .='</head>';

        $mensaje .='<body>';

        $mensaje .='<div class="container">';

        $mensaje .='<div class="row margin-bottom-30"></div>';

        $mensaje .='	<div class="row">';
        $mensaje .='    	<div class="col-lg-2 hidden-sm"></div>';
        $mensaje .='    	<div class="col-lg-6 col-sm-12 col-md-6">  ';
        $mensaje .="			<p class=\"text-left\">$mensaje_contacto</p>";
        $mensaje .='        </div>';            
        $mensaje .='	</div>';
        $mensaje .='</div>';
        $mensaje .='</body>';
        $mensaje .='</html>';
        mail($casilla,$asunto,$mensaje,$cabeceras);
  
        }
}
?>