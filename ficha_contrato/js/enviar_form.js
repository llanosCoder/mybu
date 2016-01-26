$(document).ready(function () {
    
    $('#formulario input, select').keypress(function () {

        var m = $(this).attr("id");


        if (m == 'rut' || m == 'rut_empresa' || m == 'rut_repres') {
            m = 'paso1';
            $("#rut").attr("id", 'pa1');
            $("#rut_empresa").attr("id", 'pa1');
            $("#rut_repres").attr("id", 'pa1');
        }

        var solo_m = m.replace("pa", "");

        var a = $("#paso" + solo_m).attr('id', "cambiar" + solo_m);


        if ($('#formulario').valid()) {

            $("#cambiar" + solo_m).prop('disabled', false);
            pasos("cambiar" + solo_m);
            enviar_formulario("enviar");



        } else {
            $("#cambiar" + solo_m).prop('disabled', true);
            $("#cambiar4").removeClass("submit").addClass("enviar");

        }

    });


    $('#formulario input, select').keyup(function () {
        var m = $(this).attr("id");


        if (m == 'rut' || m == 'rut_empresa' || m == 'rut_repres') {
            m = 'paso1';
            $("#rut").attr("id", 'pa1');
            $("#rut_empresa").attr("id", 'pa1');
            $("#rut_repres").attr("id", 'pa1');
        }
        var solo_m = m.replace("pa", "");

        var a = $("#paso" + solo_m).attr('id', "cambiar" + solo_m);


        if ($('#formulario').valid()) {

            $("#cambiar" + solo_m).prop('disabled', false);
            pasos("cambiar" + solo_m);
            enviar_formulario("enviar");



        } else {
            $("#cambiar" + solo_m).prop('disabled', true);
            $("#cambiar4").removeClass("submit").addClass("enviar");

        }

    });


    /*$('select').blur(function (){
					var m = $(this).attr("id");
					

					if (m == 'rut' || m == 'rut_empresa' || m == 'rut_repres'){
							m = 'paso1';
							$("#rut").attr("id",'pa1');
							$("#rut_empresa").attr("id",'pa1');
							$("#rut_repres").attr("id",'pa1');
					}
					
					var solo_m = m.replace("pa","");
					
					var a  = $("#paso"+solo_m).attr('id',"cambiar"+solo_m);
					
					
						if ($('#formulario').valid()){
						
						     $("#cambiar"+solo_m).prop('disabled',false);
								pasos("cambiar"+solo_m);
								enviar_formulario("enviar");
								
								
							
						}else{
								$("#cambiar"+solo_m).prop('disabled',true);
								$("#cambiar4").removeClass("submit").addClass("enviar");
								
						}
		
			});*/



    var cont = 0;

    function enviar_formulario(algo) {
        $("." + algo).on("click", function () {
            cont += 1;
            if (cont >= 17)
                cont = 0;

            if (cont == 1) {

                var formData = new FormData($("#formulario")[0]);


                $.ajax({
                    url: 'clases/verificar.php',
                    type: 'POST',
                    data: formData,
                    async: false,
                    cache: false,
                    processData: false,
                    contentType: false,
                    //dataType:'JSON',
                    success: function (data) {

                        if (data == 0) {
                            mostrar_notificacion("Error", "<label style='color:white !important;font-size:13px'>Ocurrio un problema al intentar guardar los datos.</label>", "danger", "bottom-right");
                            /*setTimeout(function () {
                                            window.location.reload(1);
                                        }, 2000);*/
                            //return enviar_formulario("enviar");
                        }
                        if (data == 1) {
                            mostrar_notificacion("Exito", "<label style='color:white !important;font-size:13px'>Datos Agregados Correctamente.</label>", "success", "bottom-right");
                            /*setTimeout(function () {
                                        window.location.reload(1);
                                    }, 2000);*/
                            //return enviar_formulario("enviar");
                        }
                        if (data == 2) {
                            mostrar_notificacion("Existe", "<label style='color:white !important;font-size:13px'>El cliente que quiere registrar, ya se encuentra en nuestro sistema.</label>", "warning", "bottom-right");
                            /*setTimeout(function () {
                                                    window.location.reload(1);
                                                }, 2000);*/
									//return enviar_formulario("enviar");
								}
								if(data == 3){
									mostrar_notificacion("Error","<label style='color:white !important;font-size:13px'>Faltaron campos por llenar.</label>","danger","bottom-right");
                                    /*setTimeout(function () {
                                                    window.location.reload(1);
                                                }, 2000);*/
                            //cont = -1;
                            //return enviar_formulario("enviar");
                        }
                        if (data == 4) {
                            mostrar_notificacion("Advertencia", "<label style='color:white !important;font-size:13px'>Mail inválido</label>", "warning", "bottom-right");
                            /*setTimeout(function () {
                                            window.location.reload(1);
                                        }, 2000);*/
                            //return enviar_formulario("enviar");
                        }
                        if (data == 5) {
                            mostrar_notificacion("Advertencia", "<label style='color:white !important;font-size:13px'>Rut Incorrecto</label>", "warning", "bottom-right");
                            /*setTimeout(function () {
                                            window.location.reload(1);
                                        }, 2000);*/
                            //return enviar_formulario("enviar");
                        }


                    }
                });
            }
        });


    }

});