/*global $, FormData, mostrar_notificacion, alertify */

function obtenerLocalidades(accion) {
    'use strict';
    var url = 'classes/obtener_localidades.php',
        localidades,
        selectLocalidades = '<option value="0">Seleccione una opción</option>',
        opcion,
        selectDestino;
    switch (accion) {
    case 1:
        selectDestino = 'pais';
        break;
    case 2:
        opcion = $('#pais').val();
        selectDestino = 'region';
        break;
    case 3:
        opcion = $('#region').val();
        selectDestino = 'comuna';
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
        }
    );
}

function guardarUsuario() {
    'use strict';
    $('#formempresa').append('<input type="hidden" id="accion" name="accion" value="2">');
    var hayerror = false,
        formData = new FormData($("#formempresa")[0]),
        url = 'classes/administrar_cuentas.php';
    $("#formempresa").find(':input').each(function () {
        var elemento = this;

        if ($("#" + elemento.id).parent().hasClass('has-error')) {

            hayerror = true;
        }
    });

    if (!hayerror) {
        console.log("asd");
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                if (data !== '[]') {
                    data = $.parseJSON(data);
                    switch (data.resultado) {
                    case 1:
                        mostrar_notificacion('Éxito', 'Usuario creado exitosamente', 'success');
                        break;
                    case 2:
                        mostrar_notificacion('Error', 'No tiene permisos para realizar esta acción', 'danger');
                        break;
                    case 3:
                        
                        var userName = $('#usuario').val(),
                            nuevoUsername = data.sugerido;
                        alertify.confirm('Atención', "El nombre de usuario " + userName + " ya está en uso, te sugerimos el " + "nombre " + nuevoUsername + " ¿Creamos el usuario con este nombre?",
                            function () {
                                $('#usuario').val(nuevoUsername);
                                guardarUsuario();
                            },
                            function () {
                                $('#usuario').val(userName);
                            }
                            ).set('labels',
                            {
                                ok: 'Sí, crear usuario',
                                cancel: 'Elegiré otro nombre de usuario'
                            }
                            );
                        break;
                    default:
                        mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte a un administrador', 'danger');
                        break;
                    }
                }
            },
            error: function () {
                //Si no encuentra el archivo PHP
            }
        }).done(function () {
            $('#accion').remove();
        });
    }
}

$('#formempresa').on('submit', function (event) {
    'use strict';
    event.preventDefault();
    guardarUsuario();
});

$(document).on('ready', function () {
    'use strict';
    obtenerLocalidades(1);
    var url = 'classes/obtener_datos_sesion.php';
    $.post(url,
        {
            parametros: ['tipo_cuenta', 'rol']
        },
        function (data) {
            data = $.parseJSON(data);
            if (data[0].tipo_cuenta !== '1') {
                window.location.replace('../index.html');
            }
            if (data[0].rol === '3') {
                $('#rol-wrap').show();
            }
        }).done(
        function () {}
    );

    $('#formempresa').validate({
        rules: {
            rut: {
                required: true,
                rut: true
            },
            nombres: {
                required: true,
                lettersonly: true
            },
            apellido_paterno: {
                required: true,
                lettersonly: true
            },
            apellido_materno: {
                required: true,
                lettersonly: true
            },
            email: {
                required: true,
                email: true
            },
            usuario: {
                required: true,
                minlength: 6,
                numberletters: true
            },
            pass: {
                required: true,
                minlength: 6
            },
            pass2: {
                required: true,
                minlength: 6,
                equalTo: '#pass'
            },
            pregunta: {
                required: true
            },
            respuesta: {
                required: true,
                minlength: 3
            },
            direccion: {
                required: true
            },
            //examinar: {
            // required: true,
            //},
            comuna: {
                required: true
            },
            region: {
                required: true
            },
            pais: {
                required: true
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
});

$('#pais').on('change', function validar_pais() {
    'use strict';
    obtenerLocalidades(2);
    if (document.forms[0].pais.value === 0) {
        $('#alert2').html('Debe seleccionar un pais').slideDown(500);
        document.forms[0].pais.focus();
    } else {
        $('#alert2').html('').slideUp(300);
    }
});

$('#region').on('change', function validar_region() {
    'use strict';
    obtenerLocalidades(3);
    if (document.forms[0].region.value === 0) {
        $('#alert3').html('Debe seleccionar una región').slideDown(500);
        document.forms[0].region.focus();
    } else {
        $('#alert3').html('').slideUp(300);
    }
});

$('#comuna').on('change', function validar_comuna() {
    'use strict';
    if (document.forms[0].comuna.value === 0) {
        $('#alert4').html('Debe seleccionar una comuna').slideDown(500);
        document.forms[0].comuna.focus();
    } else {
        $('#alert4').html('').slideUp(300);
    }
});

$('#pregunta').on('change', function validar_pregunta() {
    'use strict';
    if (document.forms[0].pregunta.value === 0) {
        $('#alert8').html('Debe seleccionar una pregunta').slideDown(500);
        document.forms[0].pregunta.focus();
    } else {
        $('#alert8').html('').slideUp(300);
    }
});

$('#btn_limpiar').on('click', function () {
    'use strict';
    $('.select2').val('0').trigger("change");
});