/*global $*/

$(document).ready(function () {
    'use strict';
});

function obtenerRegistros(accion) {
    'use strict';
    var url = 'classes/obtener_registros.php',
        tablaRegistros = '<table class="table table-bordered" id="tabla_registros"><thead><tr>';
    $.post(url,
        {
            accion: accion
        },
        function (data) {
            data = $.parseJSON(data);
            $.each(data.headers, function (i, datos) {
                tablaRegistros += '<th>' + datos + '</th>';
            });
            tablaRegistros += '</tr><tbody>';
            $.each(data.datos, function (i, datos) {
                tablaRegistros += '<tr>';
                $.each(datos, function (i, casilla) {
                    tablaRegistros += '<td>' + casilla + '</td>';
                });
                tablaRegistros += '</tr>';
            });
        }).done(
        function () {
            tablaRegistros += '</tbody></table>';
            $('.cl-mcont').html(tablaRegistros);
            $('#tabla_registros').dataTable({
                "aaSorting": [[0, 'asc']],
                "oLanguage": {
                    "sProcessing": "Procesando...",
                    "sLengthMenu": "Mostrar _MENU_ registros",
                    "sZeroRecords": "No se han encontrado registros disponibles",
                    "sEmptyTable": "No se han encontrado registros disponibles",
                    "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "sInfoPostFix": "",
                    "sSearch": "Buscar: ",
                    "sUrl": "",
                    "sInfoThousands": ",",
                    "sLoadingRecords": "Cargando...",
                    "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "Último",
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                    },
                    "oAria": {
                        "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                    }
                }
            });
        }
    );
}

$('#registro_stock').on('click', function () {
    'use strict';
    obtenerRegistros(1);
});

$('#registro_materia_prima').on('click', function () {
    'use strict';
    obtenerRegistros(2);
});

$('#registro_traspaso_stock').on('click', function () {
    'use strict';
    obtenerRegistros(3);
});