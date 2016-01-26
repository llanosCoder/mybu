function setImagenDefecto() {
    'use strict';
    $('#avatar').attr('src', 'src/user.png');
    $('#imagen_actual').attr('src', 'src/user.png');
}

$('#user_pic_wrapper').hover(function () {
    $('#curtain').addClass('avatar-curtain');
    $('#pencil').html('<i class="fa fa-edit fa-5x"></i>');
}, function () {
    $('#curtain').removeClass('avatar-curtain');
    $('#pencil').html('');
});

$(document).on("ready", function () {
    
    var f = new Date(),
        month = parseInt(f.getMonth(), 0) + 1,
        fecha = f.getFullYear() + '-' + month + '-' + f.getDate() + 'T00:00:00Z';
    $('.datetime').attr('data-date', fecha);
    
    function obtenerLocalidades(accion, selectDestino, selectOrigen) {
        'use strict';
        if ($('#' + selectOrigen).val() === null) {
            return false;
        }
        var url = 'classes/obtener_localidades.php',
            localidades,
            selectLocalidades = '<option value="0">Seleccione una opción</option>',
            opcion;
        switch (accion) {
        case 2:
            opcion = $('#' + selectOrigen).val();
            break;
        case 3:
            opcion = $('#' + selectOrigen).val();
            break;
        }

        $.post(url,
            {
                accion: accion,
                opcion: opcion
            },
            function (data) {
                data = $.parseJSON(data);
                localidades = data.resultado;
            }).done(
            function () {
                $.each(localidades, function (i, localidad) {
                    selectLocalidades += '<option value="' + localidad.id + '">' + localidad.nombre + '</option>';
                });
                $('#' + selectDestino).html(selectLocalidades);
                $('#' + selectDestino).select2();
            }
        );
    }
    
    function obtenerPreguntasSecretas(destino) {
        'use strict';
        var url = 'classes/preguntas_secretas.php',
            preguntas = '<option value="0">Seleccione una pregunta secreta</option>';
        $.post(url,
            {
                accion: 1
            },
            function (data) {
                if (data.resultado === 1) {
                    $.each(data.preguntas, function (i, datos) {
                        preguntas += '<option value="' + datos.value + '">' + datos.pregunta + '</option>';
                    });
                    $('#' + destino).append(preguntas);
                } else {
                    mostrar_notificacion('Atención', 'No se pudieron cargar las preguntas secretas', 'warning');
                }
            }, 'json').done(
            function () {
            }
        );
        
    }
    
    function cargarValidacionesEmpresa() {
        'use strict';
        $('#form_empresa').validate({
            rules: {
                rut: {
                    rut: true
                },
                submitHandler: function () {
                    //guardarUsuario();
                }
            },
            highlight: function (element) {
                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
                //$('#paso1').removeClass('next');
                //$('#guardar').attr('disabled', true);
            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
                //$('#pass').addClass('next');
            }
        });
        $('#form_empresa_contacto').validate({
            rules: {
                email: {
                    email: true
                },
                telefono: {
                    number: true  
                },
                submitHandler: function () {
                    //guardarUsuario();
                }
            },
            highlight: function (element) {
                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
                //$('#paso1').removeClass('next');
                //$('#guardar').attr('disabled', true);
            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
                //$('#pass').addClass('next');
            }
        });
        
    }
    
    function cargarValidacionesUsuario() {
        'use strict';
        $('#form_usuario').validate({
             rules: {
                u_nombres: {
                    lettersonly: true
                },
                u_apellidos: {
                    lettersonly: true
                },
                u_email: {
                    email: true
                },
                submitHandler: function () {
                    //guardarUsuario();
                }
            },
            highlight: function (element) {
                $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
                //$('#paso1').removeClass('next');
                //$('#guardar').attr('disabled', true);
            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
                //$('#pass').addClass('next');
            }
        });
    }

    function cargarInfoPerfil() {
        'use strict';
        $('#edicion_empresa_wrapper').fadeOut('slow');
        $('#edicion_usuario_wrapper').fadeOut('slow');
        $('.nfn-overlay').show();
        $("#cambiar-pass").modalEffects();
        $("#avatar").modalEffects();
        var url = "classes/perfil.php";
        $.post(url,
            {
                tipo: 2
            },
            function (data) {
                data = $.parseJSON(data);
                $('#avatar').attr('src', 'src/avatar_usuarios/' + data.avatar);
                $('#imagen_actual').attr('src', 'src/avatar_usuarios/' + data.avatar);
                $.each(data, function (i, item) {
                    $('#' + i + '_wrapper').html(item);
                });
                $('#titulo_nombres').html(data.nombres);
            }
        ).done(function () {
            $('.nfn-overlay').hide();
        });
        $('#imagen').change(function () {
            var f = this.files[0];
            var fileExtension = this.value.split('.').pop();
            if ((f.size || f.fileSize) > 2097152) {

                mostrar_notificacion("Advertencia", "<label style='color:white !important;font-size:13px'>Avatar no puede superar los 2MB., intente nuevamente.</label>", "warning", "bottom-right");
                $("#imagen").val("");
            }
            if ((fileExtension != "png") && (fileExtension != "jpg") && (fileExtension != "jpeg") && (fileExtension != "gif")) {
                mostrar_notificacion("Advertencia", "<label style='color:white !important;font-size:13px'>Extenciones válidas : GIF PNG JPG-JPEG.</label>", "warning", "bottom-right");
                $("#imagen").val("");
            }
        });
    }
    
    function comprobarDatos() {
        'use strict';
        
        var hayerror = false, 
            formulario = $('#btn_confirmar_password').attr('data'),
            formData = {},
            url = 'classes/edicion_perfil.php',
            accion;
        switch (formulario) {
        case 'form_empresa':
            accion = 1;
            break;
        case 'form_empresa_contacto':
            accion = 2;
            break;
        case 'form_usuario':
            accion = 3;
            break;
        }
        
        $("#" + formulario).find(':input').each(function () {
            var elemento = this;
            if ($("#" + elemento.id).parent().hasClass('has-error')) {
                hayerror = true;
            }
            formData[elemento.id] = $('#' + elemento.id).val();
        });
        if (!hayerror) {
            return true;
        }else{
            return false;
        }
    }
    
    function guardarCambios() {
        'use strict';
        //$('#' + formulario).append('<input type="hidden" id="accion" name="accion" value="2">');
        var hayerror = false, 
            formulario = $('#btn_confirmar_password').attr('data'),
            formData = {},
            url = 'classes/edicion_perfil.php',
            accion;
        switch (formulario) {
        case 'form_empresa':
            accion = 1;
            break;
        case 'form_empresa_contacto':
            accion = 2;
            break;
        case 'form_usuario':
            accion = 3;
            break;
        }
        $("#" + formulario).find(':input').each(function () {
            var elemento = this;
            if ($("#" + elemento.id).parent().hasClass('has-error')) {
                hayerror = true;
            }
            formData[elemento.id] = $('#' + elemento.id).val();
        });
        if (!hayerror) {
            $.post(url,
                {
                    datos: formData,
                    pass: $('#contrasena_confirmacion').val(),
                    accion: accion
                },
                function (data) {
                    switch (data.resultado) {
                    case 1:
                        mostrar_notificacion('Éxito', 'Datos guardados exitosamente', 'success');
                        $('#form-contrasena').hide();
                        $('.md-overlay').hide();
                        $('.form-control').val('');
                        $('.select2').val(0).trigger('change');
                        break;
                    case 2:
                        mostrar_notificacion('Error', 'Contraseña ingresada incorrecta', 'danger');
                        break;
                    case 3:
                        mostrar_notificacion('Error', 'Ingrese su contraseña', 'danger');
                        break;
                    default:
                        mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte un administrador', 'danger');
                        break;
                    case 0:
                        mostrar_notificacion('Error', 'No se ha podido procesar su solicitud', 'danger');
                        break;
                    }
                }, 'json').done(
                function () {
                }
                );
        } else {
            $('#form-contrasena').hide();
            $('.md-overlay').hide();
            mostrar_notificacion('Error', 'Ingrese los datos correctamente', 'danger');
        }
    }
    
    function cargarDatos(accion) {
        'use strict';
        var url = 'classes/edicion_perfil.php', valor;
        $.post(url,
            {
                accion: accion
            },
            function (data) {
                for(valor in data){
                    if (data[valor] != null) {
                        $('#' + valor).attr('placeholder', data[valor]);
                        
                    }
                }
            }, 'json').done(
            function () {
            }
        );
    }
    
    function cargarFormularioEmpresa() {
        'use strict';
        
        $('.nfn-overlay').show();
        $('#perfil_wrapper').fadeOut('slow');
        $('#edicion_usuario_wrapper').fadeOut('slow');
        $('#edicion_empresa_wrapper').fadeIn('slow');
        $('#comuna').select2({
           placeholder: "Seleccione una comuna"
        });
        $('#region').select2({
           placeholder: "Seleccione una región"
        });
        $('#pais').select2({
           placeholder: "Seleccione un país"
        });
        obtenerLocalidades(1, 'pais', '');
        $('#pais').off('change');
        $('#pais').on('change', function () {
            obtenerLocalidades(2, 'region', 'pais');
        });
        $('#region').off('change');
        $('#region').on('change', function () {
            obtenerLocalidades(3, 'comuna', 'region');
        });
        cargarValidacionesEmpresa();
        cargarDatos(4);
        $('.guardar_cambios').off('click');
        $('.guardar_cambios').on('click', function () {
            $('#btn_confirmar_password').attr('data', 'form_empresa');
            if (comprobarDatos()) {
                $('#form-contrasena').show();
                $('.md-overlay').show();
                $('#btn_confirmar_password').off('click');
                $('#btn_confirmar_password').on('click', guardarCambios);
            } else {
                $('#form-contrasena').hide();
                $('.md-overlay').hide();
                mostrar_notificacion('Error', 'Ingrese los datos correctamente', 'danger');
            }
        });
        $('.guardar_cambios_contacto').off('click');
        $('.guardar_cambios_contacto').on('click', function () {
            $('#btn_confirmar_password').attr('data', 'form_empresa_contacto');
            if (comprobarDatos()) {
                $('#form-contrasena').show();
                $('.md-overlay').show();
                $('#btn_confirmar_password').off('click');
                $('#btn_confirmar_password').on('click', guardarCambios);
            } else {
                $('#form-contrasena').hide();
                $('.md-overlay').hide();
                mostrar_notificacion('Error', 'Ingrese los datos correctamente', 'danger');
            }
        });
        $('.guardar_cambios').modalEffects();
        $('.guardar_cambios_contacto').modalEffects();
        $('.nfn-overlay').hide();
        
    }
    
    function cargarFormularioUsuario() {
        'use strict';
        
        $('.nfn-overlay').show();
        $('#perfil_wrapper').fadeOut('slow');
        $('#edicion_empresa_wrapper').fadeOut('slow');
        $('#edicion_usuario_wrapper').fadeIn('slow');
        $('#u_comuna').select2({
           placeholder: "Seleccione una comuna"
        });
        $('#u_region').select2({
           placeholder: "Seleccione una región"
        });
        $('#u_pais').select2({
           placeholder: "Seleccione un país"
        });
        $('#u_pregunta_secreta').select2({
           placeholder: "Seleccione una pregunta secreta"
        });
        obtenerLocalidades(1, 'u_pais');
        obtenerPreguntasSecretas('u_pregunta_secreta');
        cargarDatos(5);
        $('#u_pais').off('change');
        $('#u_pais').on('change', function () {
            obtenerLocalidades(2, 'u_region', 'u_pais');
        });
        $('#u_region').off('change');
        $('#u_region').on('change', function () {
            obtenerLocalidades(3, 'u_comuna', 'u_region');
        });
        cargarValidacionesUsuario();
        $('.guardar_cambios_usuario').off('click');
        $('.guardar_cambios_usuario').on('click', function () {
            $('#btn_confirmar_password').attr('data', 'form_usuario');
            if (comprobarDatos()) {
                $('#form-contrasena').show();
                $('.md-overlay').show();
                $('#btn_confirmar_password').off('click');
                $('#btn_confirmar_password').on('click', guardarCambios);
            } else {
                $('#form-contrasena').hide();
                $('.md-overlay').hide();
                mostrar_notificacion('Error', 'Ingrese los datos correctamente', 'danger');
            }
        });
        $('.guardar_cambios_usuario').modalEffects();
        $('.nfn-overlay').hide();
    }
    
    function cargarPerfil() {
        'use strict';
        $('.nfn-overlay').show();
        $('#edicion_empresa_wrapper').fadeOut('slow');
        $('#perfil_wrapper').fadeIn('slow');
        $('#sexo').select2({
           placeholder: "Seleccione un sexo"
        });
        cargarInfoPerfil();
    }
    
    $('#btn_edicion_empresa').on('click', cargarFormularioEmpresa);
    $('#btn_edicion_usuario').on('click', cargarFormularioUsuario);
    $('#btn_ver_perfil').on('click', cargarPerfil);
    cargarInfoPerfil();
    
});

$('#btn-cerrar-pass').click(function () {
    $('#form-cambiar-pass').removeClass('md-show');
    //reducirModal();
});

$('#btn-cerrar-avatar').click(function () {
    $('#form-cambiar-avatar').removeClass('md-show');
    //reducirModal();
});

$("#cambiar_contrasena").on("submit", function (event) {
    var datos = new FormData($("#cambiar_contrasena")[0]);
    $("#confirmar").html("Cambiando contraseña.. <i class='fa fa-spinner fa-spin'></i>");
    $("#confirmar").addClass("disabled");
    $.ajax({
        url: 'classes/perfil.php',
        type: 'POST',
        data: datos,
        async: false,
        cache: false,
        processData: false,
        contentType: false,
        dataType: 'JSON',
        success: function (data) {

            if (data.respuesta == 0) {
                mostrar_notificacion("Error", "<label style='color:white !important;font-size:13px'>Ocurrio un problema al intentar guardar los datos.</label>", "danger", "bottom-right");
                $("#confirmar").html("Confirmar cambio");
                $("#confirmar").removeClass("disabled");
            }

            if (data.respuesta == 1) {
                mostrar_notificacion("Exito", "<label style='color:white !important;font-size:13px'>Contraseña cambiada correctamente.</label>", "success", "bottom-right");


                setTimeout(function () {
                    window.location.reload(1);
                }, 2000);
            }

            if (data.respuesta == 2) {
                mostrar_notificacion("Error", "<label style='color:white !important;font-size:13px'>No dejar campos contraseña vacios.</label>", "danger", "bottom-right");
                $("#confirmar").html("Confirmar cambio");
                $("#confirmar").removeClass("disabled");
            }

            if (data.respuesta == 3) {
                mostrar_notificacion("Advertencia", "<label style='color:white !important;font-size:13px'>Las contraseñas no coinciden.</label>", "warning", "bottom-right");
                $("#confirmar").html("Confirmar cambio");
                $("#confirmar").removeClass("disabled");
            }

            if (data.respuesta == 4) {
                mostrar_notificacion("Advertencia", "<label style='color:white !important;font-size:13px'>Contraseña actual erronea, intente nuevamente.</label>", "warning", "bottom-right");
                $("#confirmar").html("Confirmar cambio");
                $("#confirmar").removeClass("disabled");
            }
        }
    });
    event.preventDefault();
});

$("#confirmar_avatar").on("click", function () {
    if ($("#imagen").val() != "") {
        //$("#confirmar_avatar").html("Cambiando avatar.. <i class='fa fa-spinner fa-spin'></i>");
        //$("#confirmar_avatar").addClass("disabled");
        //alert("casasas")
        
    }
});

$("#cambiar_avatar").on("submit", function (event) {
    var datos = new FormData($("#cambiar_avatar")[0]);
    $(".nfn-overlay").show();
    $.ajax({
        url: 'classes/perfil.php',
        type: 'POST',
        data: datos,
        async: false,
        cache: false,
        mimeType: "multipart/form-data",
        processData: false,
        contentType: false,
        dataType: 'JSON',
        success: function (data) {

            if (data.respuesta == 0) {
                mostrar_notificacion("Error", "<label style='color:white !important;font-size:13px'>Ocurrio un problema al intentar guardar los datos.</label>", "danger", "bottom-right");
                //$("#confirmar_avatar").html("Cambiar avatar");
                //$("#confirmar_avatar").removeClass("disabled");
                $(".nfn-overlay").hide();
            }

            if (data.respuesta == 1) {
                mostrar_notificacion("Exito", "<label style='color:white !important;font-size:13px'>Avatar cambiado correctamente.</label>", "success", "bottom-right");


                setTimeout(function () {
                    window.location.reload(1);
                }, 2000);
            }

            if (data.respuesta == 2) {
                mostrar_notificacion("Error", "<label style='color:white !important;font-size:13px'>No ha cargado ninguna imagen.</label>", "danger", "bottom-right");
                //$("#confirmar_avatar").html("Cambiar avatar");
                //$("#confirmar_avatar").removeClass("disabled");
                $(".nfn-overlay").hide();
            }


            if (data.respuesta == 7) {
                mostrar_notificacion("Advertencia", "<label style='color:white !important;font-size:13px'>Extenciones válidas : GIF PNG JPG-JPEG.</label>", "warning", "bottom-right");
                //$("#confirmar_avatar").html("Cambiar avatar");
                //$("#confirmar_avatar").removeClass("disabled");
                $(".nfn-overlay").hide();
            }
        }
    });
    event.preventDefault();
});