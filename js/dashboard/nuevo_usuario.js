/*global $, mostrar_notificacion, isNumber*/
/*jslint vars: true, plusplus: true, devel: true, nomen: true, indent: 4, maxerr: 50 */

function volverPag(pagActual) {
    'use strict';
    
    $('#registro_pag_1').hide();
    $('#registro_pag_2').hide();
    $('#registro_pag_3').hide();
    $('#registro_pag_' + pagActual).show();
    pagActual--;
    console.log(pagActual);
    
    if (pagActual <= 1) {
        $('.btn_back_wrapper').hide();
    }
}

function registroNormal() {
    'use strict';
    $('#registro_pag_1').hide();
    $('#registro_pag_2').hide();
    $('#registro_pag_3').show();
    $('.btn_back_wrapper').off('click');
    $('.btn_back_wrapper').on('click', function () {
        volverPag(2);
    });
}

function registroFacebook() {
    'use strict';
    $('#registro_pag_1').hide();
    $('#registro_pag_2').hide();
    $('#registro_pag_3').show();
    $('.btn_back_wrapper').off('click');
    $('.btn_back_wrapper').on('click', function () {
        volverPag(2);
    });
}

function nuevaEmpresa() {
    'use strict';
    $('#registro_pag_1').hide();
    $('#registro_pag_3').hide();
    $('#registro_pag_2').show();
    $('.btn_back_wrapper').show();
    $('.btn_back_wrapper').off('click');
    $('.btn_back_wrapper').on('click', function () {
        volverPag(1);
    });
    $('#btn_normal').off('click');
    $('#btn_normal').on('click', registroNormal);
    $('#btn_facebook').off('click');
    $('#btn_facebook').on('click', registroFacebook);
}

function nuevoUsuario() {
    'use strict';
    $('#registro_pag_1').hide();
    $('#registro_pag_3').hide();
    $('#registro_pag_2').show();
    $('.btn_back_wrapper').show();
    $('#btn_back_wrapper').off('click');
    $('.btn_back_wrapper').on('click', function () {
        volverPag(1);
    });
    $('#btn_facebook').off('click');
    $('#btn_facebook').on('click', registroFacebook);
}

$(document).ready(function () {
    'use strict';
    $('#btn_empresa').on('click', nuevaEmpresa);
    $('#btn_usuario').on('click', nuevoUsuario);
});
