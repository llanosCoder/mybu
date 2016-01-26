var usuario = "";
$(document).ready(function () {

    var url = 'classes/obtener_datos_sesion.php';
    var parametros = ['empresa', 'nombre_empresa'];

    $.post(url, {
            parametros: parametros
        },
        function (data) {
            data = $.parseJSON(data);
            $.each(data, function (i, datos) {
                usuario = datos.empresa;
                $("#empresa_id").val(datos.empresa);
                $("#nombre_empresa").val(datos.nombre_empresa);

            });

        }

    ).done(function () {
        // Saber estilo y crear sesion
        $.ajax({
            type: "POST",
            cache: false,
            async: false,
            url: 'clases/session_sitio.php',
            data: "usuario=" + usuario,
            dataType: 'JSON',
            success: function (data) {

                $("#estilo").val(data.estilo);



                // ==================== 

                for (i = 1; i <= 6; i++) {


                    $("#sitio").append(

                        '<div class="col-md-4">' +
                        '<div class="panel panel-default" id="estilo' + i + '">' +

                        ' <div class="panel-heading">' +
                        '<div class="col-xs-3">' +
                        '<i class="fa fa-pencil fa-2x"></i>' +
                        '</div>' +
                        '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Web Nº ' + i + '&nbsp;&nbsp;&nbsp;&nbsp;<span id="web' + i + '"></span>' +
                        '</div>' +

                        '<div class="panel-body">' +
                        '<div class="row">' +
                        '<div class="col-md-12 text-center">'+
                        '<div class="row"'+
                        '<a href="" title="Click para visualizar" data-toggle="modal" data-target="#myModal' + i + '"><img src="images/web/info' + i + '.png" /></a></div><hr>' +
                        '<div class="row">'+
                        '<input type="hidden" name="web_estilo" id="web_estilo' + i + '" value="' + i + '">' +
                        
                        '<div class="col-md-12 text-center"><button type="button" class="btn btn-primary col-md-12 enviar text-center" id="configurar' + i + '">Cambiar Web</button></div>' +
                        '</div>' +
                        '</div>' +

                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '</div>'

                    );


                    $("#miModal").append(

                        '<div class="modal fade bs-example-modal-lg" id="myModal' + i + '" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3" aria-hidden="true">' +
                        '<div class="modal-dialog  modal-lg">' +
                        '<div class="modal-content">' +
                        '<div class="modal-header">' +
                        '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red !important;">&times;</span></button>' +
                        '<h4 class="modal-title text-center" id="myModalLabel">WEB Nº ' + i + '</h4>' +
                        '</div>' +
                        '<div class="modal-body" style="height:600px; overflow-y:scroll;">' +
                        '<center>' +
                        '<img src="images/web/web' + i + '.jpg" width="70%">' +
                        '</center>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '</div>');
                }

                $("#estilo" + data.estilo).removeClass("panel panel-default").addClass("panel panel-success");
                $("#web" + data.estilo).text("(Sitio Actual)");

                $(".enviar").on("click", function () {


                    var id_conf = $(this).attr("id");
                    var empresa_id = $("#empresa_id").val();
                    var id = id_conf.replace("configurar", "");

                    var web_estilo = $("#web_estilo" + id).val()


                    /*switch(id_conf) {
											
													case "configurar1":
															var web_estilo = $("#web_estilo1").val();
														break;
														
													case "configurar2":
															var web_estilo = $("#web_estilo2").val();
														break;
														
													case "configurar3":
															var web_estilo = $("#web_estilo3").val();
														break;
														
													case "configurar4":
															var web_estilo = $("#web_estilo4").val();
														break;
											}*/



                    $.ajax({
                        type: "POST",
                        cache: false,
                        async: false,
                        url: 'clases/verificar.php',
                        data: "web_estilo=" + web_estilo + "&empresa_id=" + empresa_id,
                        dataType: 'JSON',
                        success: function (data) {

                            switch (data.respuesta) {

                            case 1:
                                //window.open('../web-builder/index'+web_estilo+'.php','_newtab');
                                //mostrar_notificacion("","<label style='color:white !important;font-size:13px'>Tiene web</label>","success","bottom-right");
                                //$("#"+id_conf).css("display","none");
                                $("#" + id_conf).removeClass("btn btn-primary").addClass("btn btn-danger");
                                $("#" + id_conf).html("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Web ya asignada&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
                                break;

                            case 2:

                                mostrar_notificacion("Advertencia!", "<label style='color:white !important;font-size:13px'>Usted ya dispone de una web, si desea cambiar, presione nuevamente.</label>", "warning", "bottom-right");
                                $("#" + id_conf).html("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cambiar Web&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
                                $("#" + id_conf).removeClass("btn btn-default").addClass("btn btn-warning");
                                $("#" + id_conf).unbind("click");
                                $("#" + id_conf).bind("click", function () {

                                    var nombre_empresa = $("#nombre_empresa").val();

                                    $.ajax({
                                        type: "POST",
                                        cache: false,
                                        async: false,
                                        url: 'clases/actualizar.php',
                                        data: "web_estilo=" + web_estilo + "&empresa_id=" + empresa_id + "&nombre_empresa=" + nombre_empresa,
                                        dataType: 'JSON',
                                        success: function (data) {
                                            if (data.respuesta == 1) {
                                                mostrar_notificacion("Exito", "<label style='color:white !important;font-size:13px'>Web actualizada correctamente.</label>", "success", "bottom-right");
                                                $("#" + id_conf).unbind("click");
                                                $("#" + id_conf).removeClass("btn btn-warning").addClass("btn btn-primary");
                                                $("#" + id_conf).html("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cambiar Web&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");

                                                setTimeout(function () {
                                                    window.location.reload(1);
                                                }, 2000);
                                                //$("#"+id_conf).css("display","none");
                                                //$("#"+id_conf).bind("click",function(){enviar();});

                                            } else {
                                                mostrar_notificacion("Error", "<label style='color:white !important;font-size:13px'>Ocurrio un problema al intentar actualizar, intentelo más tarde.</label>", "danger", "bottom-right");
                                            }

                                        }
                                    });
                                });


                                break;

                            case 3:
                                mostrar_notificacion("No existe", "<label style='color:white !important;font-size:13px'>El usuario no ha creado una web.</label>", "danger", "bottom-right");
                                //$("#"+id_conf).attr("id","crear"+id);
                                $("#" + id_conf).removeClass("btn btn-primary").addClass("btn btn-danger");
                                $("#" + id_conf).html("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Crear Web&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
                                $("#" + id_conf).unbind("click");
                                $("#" + id_conf).bind("click", function () {

                                    var nombre_empresa = $("#nombre_empresa").val();

                                    $.ajax({
                                        type: "POST",
                                        cache: false,
                                        async: false,
                                        url: 'clases/crear.php',
                                        data: "web_estilo=" + web_estilo + "&empresa_id=" + empresa_id + "&nombre_empresa=" + nombre_empresa,
                                        dataType: 'JSON',
                                        success: function (data) {

                                            switch (data.respuesta) {

                                            case 1:

                                                mostrar_notificacion("Advertencia", "<label style='color:white !important;font-size:13px'>La web ya ha sido creada con éxtio, por favor recrgar la web.</label>", "warning", "bottom-right");

                                                break;

                                            case 2:

                                                mostrar_notificacion("Exito", "<label style='color:white !important;font-size:13px'>Web Creada Correctamente.</label>", "success", "bottom-right");
                                                //$("#"+id_conf).unbind("click");
                                                $("#" + id_conf).removeClass("btn btn-danger").addClass("btn btn-primary");
                                                $("#" + id_conf).html("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cambiar Web&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
                                                setTimeout(function () {
                                                    window.location.reload(1);
                                                }, 2000);
                                                    
                                                break;

                                            case 3:

                                                mostrar_notificacion("Error", "<label style='color:white !important;font-size:13px'>Ocurrio un problema al intentar crear la web, intentelo más tarde.</label>", "danger", "bottom-right");


                                                break;


                                            }




                                        }
                                    });
                                });

                                break;

                            }




                        }

                    });

                });


                $(".ver").on("click", function () {

                    var id_ver = $(this).attr("id");
                    var solo_id = id_ver.replace("ver", "");
                    var estilo = $("#estilo").val();
                    var nombre_empresa = $("#nombre_empresa").val();



                    if (estilo == solo_id)
                        window.open('clases/verificar_profile.php?usuario=' + nombre_empresa, '_newtab');
                    else
                        mostrar_notificacion("Error", "<label style='color:white !important;font-size:13px'>No tiene acceso al sitio.</label>", "danger", "bottom-right");



                });


            }
        });

    });

});