/*global $, obtenerCierreCaja, mostrar_notificacion*/
/*jslint plusplus: true */

$(document).ready(function () {
    'use strict';
    var f = new Date(),
        month = parseInt(f.getMonth(), 0) + 1,
        fecha = f.getFullYear() + '-' + month + '-' + f.getDate() + 'T00:00:00Z',
        f_inicio,
        f_termino,
        n_fecha1,
        n_hora1,
        n_fecha2,
        n_hora2;
    $('.datetime').attr('data-date', fecha);
    $('#btn_reporte').on('click', function () {

        var f = new Date(),
            fechaActual,
            mes;
        mes = f.getMonth() + 1;
        fechaActual = f.getDate() + '-' + mes + '-' + f.getFullYear();

        function esMayor(ini, ter) {
            var arrIni = ini.split('-'),
                arrTer = ter.split('-'),
                i;
            arrIni.reverse();
            arrTer.reverse();
            for (i = 0; i < 3; i++) {
                if (parseInt(arrIni[i], 0) > parseInt(arrTer[i], 0)) {
                    return true;
                } else {
                    if (parseInt(arrIni[i], 0) < parseInt(arrTer[i], 0)) {
                        return false;
                    }
                }
            }
            return false;
        }

        f_inicio = $('#f_inicio').val();
        f_termino = $('#f_termino').val();
        if (esMayor(f_inicio, f_termino)) {
            mostrar_notificacion('Atención', 'Fecha de inicio no puede ser mayor a fecha de término', 'warning');
        } else {
            if (esMayor(f_inicio, fechaActual) || esMayor(f_termino, fechaActual)) {
                mostrar_notificacion('Atención', 'Fecha ingresada no puede ser mayor a fecha actual', 'warning');
            } else {
                n_fecha1 = f_inicio.substr(0, 10);
                n_hora1 = f_inicio.substr(11, 16);
                n_fecha1 = n_fecha1.split("-").reverse().join("-");
                n_fecha2 = f_termino.substr(0, 10);
                n_hora2 = f_termino.substr(11, 16);
                n_fecha2 = n_fecha2.split("-").reverse().join("-");
                obtenerCierreCaja(n_fecha1 + ' ' + n_hora1, n_fecha2 + ' ' + n_hora2);
            }
        }

    });
});

function actualizarValores(f_inicio, f_fin) {
    'use strict';
    if (f_fin === '') {
        f_fin = 0;
    } else {
        f_fin = f_fin.split("-").reverse().join("-");
    }
    if (f_inicio === '') {
        f_inicio = 0;
    } else {
        f_inicio = f_inicio.split("-").reverse().join("-");
    }
    $('#link_exportacion').attr('href', 'classes/obtener_informes.php?accion=1&f_inicio=' + f_inicio + '&f_fin=' + f_fin);
    $('#link_exportacion_excel').attr('href', 'classes/a_excel.php?op=1&f_inicio=' + f_inicio + '&f_termino=' + f_fin);
}

$('.fecha').on('change', function () {
    'use strict';
    var f_inicio = $('#f_inicio').val(),
        f_fin = $('#f_termino').val();
    actualizarValores(f_inicio, f_fin);
});
