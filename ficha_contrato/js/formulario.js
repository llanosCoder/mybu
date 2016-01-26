
        $("#formulario").on("submit", function (event) {

                var formData = new FormData($("#formulario")[0]);


                $.ajax({
                    url: 'ficha_contrato/clases/verificar.php',
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
                            setTimeout(function () {
                                        window.location.reload(1);
                                    }, 2000);
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
                            mostrar_notificacion("Advertencia", "<label style='color:white !important;font-size:13px'>Mail inválidos</label>", "warning", "bottom-right");
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
                        
                        if (data == 6) {
                            mostrar_notificacion("Advertencia", "<label style='color:white !important;font-size:13px'>Nº de folio ya se encuentra registrado.</label>", "warning", "bottom-right");
                            /*setTimeout(function () {
                                            window.location.reload(1);
                                        }, 2000);*/
                            //return enviar_formulario("enviar");
                        }



                    }
                });
            
             event.preventDefault();
        });
