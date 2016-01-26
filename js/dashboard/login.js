var esApp = false, social = 1;

$(document).ready(function () {
    $('.box').ready(function () {
        $('.box').css('margin-top', ($(window).height() / 2) - 215);
    });

    var url = 'classes/obtener_datos_sesion.php';
    var parametros = ['nombre', 'avatar', 'nombre_empresa', 'navegador'];
    $.post(url, {
            parametros: parametros
        },
        function (data) {
            data = $.parseJSON(data);
            $.each(data, function (i, datos) {

                if (datos.navegador.browser == 'SAFARI' && datos.navegador.os == 'LINUX' && datos.navegador.server.indexOf('Mobile Safari') != -1) {
                    esApp = true;
                }
                if (datos.nombre != 0 && !esApp)
                    window.location.replace("punto_venta.html");
                if (datos.nombre != 0 && esApp)
                    window.location.replace("../dashapp/index.html");
            });
            $("#menu_login").show();
        }
    );
    function validarEmail( email ) {
        expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if ( !expr.test(email) ) {
            return false;
        } else {
            return true;
        }
    }
    function registrarUsuario() {
        'use strict';
        var user = $('#newUser').val(), 
            pass = $('#newPass').val(),
            pass2 = $('#newPass2').val(),
            url = 'classes/administrar_cuentas.php',
            registrar = true;
        if (!validarEmail(user)) {
            registrar = false;
            mostrar_notificacion('Error', 'Ingrese un email válido', 'warning');
        }
        if (pass.length < 6) {
            registrar = false;
            mostrar_notificacion('Error', 'Su contraseña debe ser mínimo de 6 caracteres de longitud', 'warning');
        }
        if (pass2 !== pass) {
            registrar = false;
            mostrar_notificacion('Error', 'Las contraseñas ingresadas no coinciden', 'warning');
        }
        if (registrar) {
            $('.nfn-overlay').show();
            $.post(url,
                {
                    email: user,
                    pass: pass,
                    accion: 3,
                    social: social
                },
                function (data) {
                    data = $.parseJSON(data);
                    var datos = data.resultado[0];

                    switch(datos.resultado){
                        case 1:
                            mostrar_notificacion('Éxito', 'Usuario creado exitosamente', 'success');
                            var url = 'classes/login.php';
                            $.post(url,
                                {
                                    user: user,
                                    pass: pass,
                                    remember: true
                                },
                                function (data) {
                                    $('.nfn-overlay').hide();
                                    data = $.parseJSON(data);
                                    var cont = 0;
                                    $.each(data, function (i, datos) {
                                        switch (datos.estado) {
                                            case 1:
                                                var mensaje = '¡Bienvenido ' + datos.bienvenida + '!';
                                                window.location.replace("punto_venta.html");
                                                break;
                                            case 0:
                                                mostrar_notificacion('Login', 'Los datos ingresados son incorrectos', 'warning');
                                                break;
                                            default:
                                                mostrar_notificacion('Error', 'Ha ocurrido un error inesperado al iniciar su sesión. Por favor, contacte a un administrador', 'danger');
                                                break;
                                        }
                                    });
                                }).done(
                                function () {
                                }
                            );

                            break;
                        case 16:
                            mostrar_notificacion('Error', 'El correo ingresado ya se encuentra en uso', 'warning');
                            $('.nfn-overlay').hide();
                            break;
                        default:
                            mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte un administrador', 'danger');
                            break;
                    }

                }).done(
                function () {
                }
            );
        }
    }
    
    $('#quieroRegistrarme').on('click', function () {
        $('#signin-wrapper').hide('fast');
        $('#register-wrapper').show('fast');
        $('#newUser').prop('disabled', false);
        $('.form-control').off('keyup');
    });
    
    $('#btn_registro_normal').on('click', registrarUsuario);
    $('#register-wrapper').off('keyup');
    $('#register-wrapper').on('keyup', function (e) {
        //alert(e.keyCode);
        if (e.keyCode == 13) {
            registrarUsuario();
        }
    });
});

$('#btn_login').click(function () {
    login();
});

$('.form-control').on('keyup', function (e) {
    //alert(e.keyCode);
    if (e.keyCode == 13) {
        login();
    }
});

$('.btn-facebook').on('click', function () {
    social = 2;
    $('#btn_login').off('click');
    $('.form-control').off('keyup');
})

function login() {
    var user = $('#user').val();
    var pass = $('#pass').val();
    var remember = 'false';
    if ($("#remember-me").is(':checked'))
        remember = 'true';
    switch (verificarInputs()) {
    case 1:
        var url = 'classes/login.php';
        $.post(url, {
                user: user,
                pass: pass,
                remember: remember,
            },
            function (data) {
                $('.nfn-overlay').show();
                data = $.parseJSON(data);
                var cont = 0;
                $.each(data, function (i, datos) {
                    $('.nfn-overlay').hide();
                    cont++;
                    switch (datos.estado) {
                    case 1:
                        var mensaje = '¡Bienvenido ' + datos.bienvenida + '!';
                        if (datos.navegador.browser == 'SAFARI' && datos.navegador.os == 'LINUX' && datos.navegador.server.indexOf('Mobile Safari') != -1) {
                            //window.location.replace("http://162.248.54.58/dashapp/app/dashboard.html");
                            //window.location.replace('http://www.responsiweb.com/themes/preview/ace/1.3.3/');
                            esApp = true;
                            window.location.replace('../dashapp/index.html');
                        } else {

                            window.location.replace("punto_venta.html");
                        }
                        if (!esApp) {
                            mostrar_notificacion('Login', mensaje, 'success');
                        }
                        //window.location.href = 'punto_venta.html';
                        break;
                    case 0:
                        if (!esApp) {
                            mostrar_notificacion('Login', 'Los datos ingresados son incorrectos', 'warning');
                        }
                        break;
                    default:
                        if (!esApp) {
                            mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte a un administrador', 'danger');
                        }
                        break;
                    }
                });
                if (cont == 0) {
                    $('.nfn-overlay').hide();
                    if (!esApp) {
                        mostrar_notificacion('Login', 'Los datos ingresados son incorrectos', 'warning');
                    } else {


                        $('#message').html('Datos de ingreso incorrectos');
                        setTimeout(function () {
                            $('#message').html('');
                        }, 5000);
                    }
                }
            }
        );
        break;
    case 2:
        if (!esApp) {
            mostrar_notificacion('Atención', 'Rellene todos los campos', 'warning');
        }

        break;
    }
}

function verificarInputs() {
    var $inputs = $('.formulario :input');
    var formValido = 1;
    $inputs.each(function () {
        if ($(this).hasClass('required') && $(this).val() == '') {
            $(this).parent().addClass('has-error');
            /*var idCampo = $(this).parent().next('.message').attr('id');
            $('#'+idCampo).html('Campo requerido');
            setInterval(function(){
                    $('#'+idCampo).html('');
                    $('#'+idCampo).show();
            }, 3000);*/
            formValido = 2;
        } else {
            if ($(this).parent().hasClass('has-error'))
                $(this).parent().removeClass('has-error');
        }
    });
    return formValido;
}