function mostrar_mensaje(msj, color){
	$("#mensaje").hide();
	$("#mensaje").html('<span style=color:'+color+'>'+msj+'</span>');
	$("#mensaje").fadeIn("slow").delay(1000).fadeOut("slow");
}