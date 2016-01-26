/*global $, i, mostrar_notificacion*/
/*jslint plusplus: true */

var claseEvento;

$(document).ready(function () {
    'use strict';
    var f = new Date(),
        month = parseInt(f.getMonth(), 0) + 1,
        fecha = f.getFullYear() + '-' + month + '-' + f.getDate() + 'T00:00:00Z';
    $('.datetime').attr('data-date', fecha);
});

function crearEvento(fechaInicio, fechaFin, descripcion, tipos, evento) {
    'use strict';
    var url = 'classes/administrar_eventos.php',
        accion;
    if (evento > 0) {
        accion = 3;
    } else {
        accion = 1;
    }
    $.post(url,
        {
            accion: accion,
            evento: evento,
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin,
            descripcion: descripcion,
            tipos: tipos
        },
        function (data) {
            data = $.parseJSON(data);
            switch (data.resultado) {
            case 0:
                mostrar_notificacion('Error', 'No se ha podido procesar su solicitud.', 'danger');
                break;
            case 1:
                $('#form-evento').removeClass('md-show');
                mostrar_notificacion('Éxito', 'Evento agregado correctamente', 'success');
                break;
            default:
                mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte a un administrador', 'danger');
                break;
            }
        }).done(
        function () {
            i();
        }
    );
}

function validarEvento(color, evento) {
    'use strict';
    var datos = {}, valor, focusTaken = false;
    datos.descripcion = $('#descripcion').val();
    datos.f_inicio = $('#f_inicio').val();
    datos.f_termino = $('#f_termino').val();
    if (color === undefined) {
        color = 'bg-inverse';
    }
    datos.color = color;
    for (valor in datos) {
        if (datos.hasOwnProperty(valor)) {
            if (datos[valor] === null || datos[valor] === '') {
                if ($("#" + valor).hasClass('required')) {
                    $("#" + valor).parent().addClass('has-error');
                    if (!focusTaken) {
                        $("#" + valor).focus();
                        focusTaken = true;
                    }
                }
            } else {
                if ($('#' + valor).parent().hasClass('has-error')) {
                    $('#' + valor).parent().removeClass('has-error');
                }
            }
        }
    }
    if (datos.f_termino === '') {
        datos.f_termino = datos.f_inicio;
    }
    if (datos.f_termino < datos.f_inicio) {
        $('#f_inicio').parent().addClass('has-error');
        $('#f_termino').parent().addClass('has-error');
        mostrar_notificacion('Error', 'Fecha fin no puede ser menor a fecha inicio', 'warning');
        focusTaken = true;
    } else {
        $('#f_inicio').removeClass('has-error');
        $('#f_termino').removeClass('has-error');
    }
    if (!focusTaken) {
        crearEvento(datos.f_inicio, datos.f_termino, datos.descripcion, color, evento);
    }
}

function obtenerColor(clase) {
    'use strict';
    var retorno;
    switch (clase) {
    case 'bg-primary':
        retorno = '#5d9cec';
        break;
    case 'bg-danger':
        retorno = '#f05050';
        break;
    case 'bg-info':
        retorno = '#23b7e5';
        break;
    case 'bg-success':
        retorno = '#27c24c';
        break;
    case 'bg-warning':
        retorno = '#ff902b';
        break;
    case 'bg-green':
        retorno = '#37bc9b';
        break;
    case 'bg-pink':
        retorno = '#f532e5';
        break;
    case 'bg-purple':
        retorno = '#7266ba';
        break;
    case 'bg-inverse':
        retorno = '#131e26';
        break;
    default:
        retorno = '#131e26';
        break;
    }
    return retorno;
}

function cambiarClase(clase) {
    'use strict';
    var retorno;
    switch (clase) {
    case 'circle-pink':
        retorno = 'bg-pink';
        break;
    case 'circle-danger':
        retorno = 'bg-danger';
        break;
    case 'circle-primary':
        retorno = 'bg-primary';
        break;
    case 'circle-info':
        retorno = 'bg-info';
        break;
    case 'circle-success':
        retorno = 'bg-success';
        break;
    case 'circle-warning':
        retorno = 'bg-warning';
        break;
    case 'circle-green':
        retorno = 'bg-green';
        break;
    case 'circle-inverse':
        retorno = 'bg-inverse';
        break;
    case 'circle-purple':
        retorno = 'bg-purple';
        break;
    }
    return retorno;
}

function claseCirculos(clase) {
    'use strict';
    var retorno;
    switch (clase) {
    case 'bg-pink':
        retorno = 'circle-pink';
        break;
    case 'bg-danger':
        retorno = 'circle-danger';
        break;
    case 'bg-primary':
        retorno = 'circle-primary';
        break;
    case 'bg-info':
        retorno = 'circle-info';
        break;
    case 'bg-success':
        retorno = 'circle-success';
        break;
    case 'bg-warning':
        retorno = 'circle-warning';
        break;
    case 'bg-green':
        retorno = 'circle-green';
        break;
    case 'bg-inverse':
        retorno = 'circle-inverse';
        break;
    case 'bg-purple':
        retorno = 'circle-purple';
        break;
    }
    return retorno;
}

function eliminarEvento(evento) {
    'use strict';
    var url = 'classes/administrar_eventos.php';
    $.post(url,
        {
            accion: 4,
            eId: evento
        },
        function (data) {
            data = $.parseJSON(data);
            data = data.resultado;
            switch (data) {
            case 0:
                mostrar_notificacion('Error', 'No se ha podido procesar su solicitud.', 'danger');
                break;
            case 1:
                $('#form-evento').removeClass('md-show');
                mostrar_notificacion('Éxito', 'Evento eliminado correctamente', 'success');
                break;
            case 2:
                $('#form-evento').removeClass('md-show');
                mostrar_notificacion('Error', 'No tiene permisos para eliminar este evento', 'danger');
                break;
            default:
                mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte a un administrador', 'danger');
                break;
            }
        }).done(
        function () {
            i();
        }
    );
    
}

function obtenerDatosEvento(evento) {
    'use strict';
    var url = 'classes/administrar_eventos.php',
        tipo;
    $.post(url,
        {
            accion: 5,
            eId: evento
        },
        function (data) {
            data = $.parseJSON(data);
            data = data[0];
            $('#descripcion').val(data.descripcion);
            $('#f_inicio').val(data.fecha_ini);
            $('#f_termino').val(data.fecha_fin);
            tipo = data.color;
        }).done(
        function () {
            var clase = claseCirculos(tipo);
            $('.' + clase).addClass('bordeado');
            claseEvento = tipo;
        }
    );
}

function cargarModalEvento(eventoNuevo, evento) {
    'use strict';
    var clases = [],
        i = 0,
        clase;
    claseEvento = '';
    $('#descripcion').val('');
    $('#f_inicio').val('');
    $('#f_termino').val('');
    $('.circle').removeClass('bordeado');
    $('.circle').off('click');
    $('.circle').on('click', function (e) {
        e.preventDefault();
        clases = $(this).attr('class').split(' ');
        $('.circle').removeClass('bordeado');
        $(this).addClass('bordeado');
        for (i = 0; i < clases.length; i++) {
            var color = cambiarClase(clases[i]);
            if (color !== undefined) {
                claseEvento = color;
            }
        }
    });
    $('#btn_eliminar_evento').off('click');
    $('#btn_eliminar_evento').on('click', function () {
        eliminarEvento(evento.id);
    });
    if (!eventoNuevo) {
        obtenerDatosEvento(evento.id);
        
    }
    $('#btn_agregar_evento').off('click');
    $('#btn_agregar_evento').on('click', function () {
        validarEvento(claseEvento, evento.id);
    });
}

$('#btn_agregar').click(function () {
    'use strict';
    cargarModalEvento(true, 0);
});