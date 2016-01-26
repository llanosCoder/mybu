var esApp = false;

$(document).ready(function () {
    $('.box').ready(function() {
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
                
                if(datos.navegador.browser == 'SAFARI' && datos.navegador.os == 'LINUX' && datos.navegador.server.indexOf('Mobile Safari') != -1){
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
});

$('#btn_login').click(function () {
    login();
});

$('.form-control').keyup(function (e) {
    //alert(e.keyCode);
    if (e.keyCode == 13) {
        login();
    }
});

function login() {
    var user = $('#user').val();
    var pass = $('#pass').val();
    var remember = 'false';
    if($("#remember-me").is(':checked'))
        remember = 'true';
    switch (verificarInputs()) {
    case 1:
        var url = 'classes/login.php';
        $.post(url, {
                user: user,
                pass: pass,
                remember: remember
            },
            function (data) {
            $('.nfn-overlay').show();
                data = $.parseJSON(data);
                var cont = 0;
                $.each(data, function (i, datos) {
                    cont++;
                    switch (datos.estado) {
                    case 1:
                        var mensaje = '¡Bienvenido ' + datos.bienvenida + '!';
                        if(datos.navegador.browser == 'SAFARI' && datos.navegador.os == 'LINUX' && datos.navegador.server.indexOf('Mobile Safari') != -1){
                            //window.location.replace("http://162.248.54.58/dashapp/app/dashboard.html");
                            //window.location.replace('http://www.responsiweb.com/themes/preview/ace/1.3.3/');
                            esApp = true;
                            window.location.replace('../dashapp/index.html');
                        }else{
                            
                            window.location.replace("punto_venta.html");
                        }
                        if(!esApp){
                            mostrar_notificacion('Login', mensaje, 'success');
                        }
                        //window.location.href = 'punto_venta.html';
                        break;
                    case 0:
                            $('.nfn-overlay').hide();
                        if(!esApp){
                            mostrar_notificacion('Login', 'Los datos ingresados son incorrectos', 'warning');
                        }
                        break;
                    default:
                            $('.nfn-overlay').hide();
                        if(!esApp){
                            mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte a un administrador', 'danger');
                        }
                        break;
                    }
                });
                if (cont == 0) {
                    $('.nfn-overlay').hide();
                    if(!esApp){
                        mostrar_notificacion('Login', 'Los datos ingresados son incorrectos', 'warning');
                    }else{
                        
                        
                        $('#message').html('Datos de ingreso incorrectos');
                        setTimeout(function(){ 
                            $('#message').html('');
                        }, 5000);
                    }
                }
            }
        );
        break;
    case 2:
        if(!esApp){
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