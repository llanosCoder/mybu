var usuario = "";
var web_estilo = "";
var nombre_empresa = "";
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
                nombre_empresa = datos.nombre_empresa;
                //$("#empresa_id").val(datos.empresa);
                //$("#nombre_empresa").val(datos.nombre_empresa);

            });

        }

    ).done(function () {



        var url = 'clases/sessiones.php';
        var parametros = ['estilo'];
        $.post(url, {
                parametros: parametros
            },
            function (data) {
                data = $.parseJSON(data);
                $.each(data, function (i, datos) {
                    web_estilo = datos.estilo;
                   
                });

            }

        ).done(function () {



            $.ajax({
                type: "POST",
                cache: false,
                async: false,
                url: 'clases/session_sitio.php',
                data: "usuario=" + usuario,
                dataType: 'JSON',
                success: function (data) {

                    $("#titulo").text(" Usted dispone del diseño Nº " + data.estilo);
                    nombre_empresa = nombre_empresa.replace(" ","");
                    $("#url").html("<center><strong>Tu url, para compartir </strong> <a href='http://www.nfnempresas.com/clases/verificar_profile.php?usuario="+nombre_empresa+"' target='_blank'>http://www.nfnempresas.com/"+$.trim(nombre_empresa)+"</a></center>");
                   
                    if (web_estilo == 0)
                        web_estilo = data.estilo;

                    if (web_estilo == undefined || data.estilo == undefined) {
                            
                        location.replace("web.html");

                    }


                    $("#sitio").append(
                        //col-md-offset-2	
                        '<div class="col-md-10 col-md-offset-1">' +
                        '<div class="panel panel-primary">' +

                        ' <div class="panel-heading">' +
                        '<div class="col-xs-2">' +
                        '<i class="fa fa-pencil fa-4x"></i>' +
                        '</div>' +
                        '<h3>Presione configurar para editar el contenido de su web.</h3>' +
                        '</div>' +

                        '<div class="panel-body">' +
                        '<div class="row">' +
                        '<div class="col-md-4 col-md-offset-4">' +
                        '<button type="button" class="btn btn-default enviar" id="configurar' + data.estilo + '"><span class="fa fa-pencil-square-o"></span>  Configurar Web</button>' +
                        ' <button type="button" class="btn btn-default ver" id="ver' + data.estilo + '"><span class="fa fa-eye"></span>  Ver Web</button>' +
                        '</div>' +
                        '</div>' +
                        '<div class="row">' +
                        '<div class="col-md-12">' +
                        '<img src="images/web/web' + data.estilo + '.jpg" width="100%">' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '</div>'

                    );




                    $(".enviar").on("click", function () {


                        var id_conf = $(this).attr("id");
                        var empresa_id = usuario;
                        var id = id_conf.replace("configurar", "");


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
                                    //window.open('http://www.nfn.cl/web-builder/index'+web_estilo+'.php','_newtab');
                                    //mostrar_notificacion("","<label style='color:white !important;font-size:13px'>Tiene web</label>","success","bottom-right");
                                    window.open('http://www.nfnempresas.com/web-builder/ir_web.php?usu_id=' + empresa_id + '&web_estilo=' + web_estilo, '_newtab')

                                    break;

                                case 2, 3:

                                    mostrar_notificacion("Error", "<label style='color:white !important;font-size:13px'>Usuario No existe</label>", "danger", "bottom-right");


                                    break;

                                default:

                                    mostrar_notificacion("Error", "<label style='color:white !important;font-size:13px'>Ocurrio un problema al intentar entrar a la web, intentelo más tarde.</label>", "danger", "bottom-right");

                                    break;



                                }


                            }

                        });



                    });


                    $(".ver").on("click", function () {

                        var id_ver = $(this).attr("id");
                        var solo_id = id_ver.replace("ver", "");
                        var estilo = web_estilo;
                        //var nombre_empresa = nombre_empresa;


                        if (estilo == solo_id)
                            window.open('http://www.nfnempresas.com/clases/verificar_profile.php?usuario=' + nombre_empresa, '_newtab');
                        else
                            mostrar_notificacion("Error", "<label style='color:white !important;font-size:13px'>No tiene acceso al sitio.</label>", "danger", "bottom-right");


                    });



                }




            });
        });
    });

});