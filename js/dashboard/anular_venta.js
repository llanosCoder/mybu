/*global $, currencyFormat, aRut, alertify, mostrar_notificacion*/
/*jslint vars: true, plusplus: true, devel: true, nomen: true, indent: 4, maxerr: 50 */

$(document).on('ready', function () {
    'use strict';
    function cargarVentasRecientes() {
        var url = 'classes/administrar_ventas.php',
            tablaVentas = '<table class="table table-bordered responsive" id="tabla_ventas"><thead><tr><th>Código de Venta</th><th>Fecha</th><th>Total Bruto</th><th>Total Descuentos</th><th>Total Neto</th><th>Vendedor</th><th>Rut Cliente</th><th>Nombre Cliente</th><th>Anular Venta</th></tr></thead><tbody>"';
        $.post(url,
            {
                accion: 1
            },
            function (data) {
                var fecha,
                    edicion = data.edicion;
                $.each(data.ventas, function (i, venta) {
                    tablaVentas += '<tr>';
                    tablaVentas += '<td>' + venta.v_id + '</td>';
                    fecha = venta.v_fecha.split("-").reverse().join("-");
                    tablaVentas += '<td><span style="display:none;">' + venta.v_fecha + '</span>' + fecha + '</td>';
                    tablaVentas += '<td>' + currencyFormat(venta.bruto, '$') + '</td>';
                    tablaVentas += '<td>' + currencyFormat(venta.descuentos, '$') + '</td>';
                    tablaVentas += '<td>' + currencyFormat(venta.neto, '$') + '</td>';
                    if (venta.u_nombres === null) {
                        tablaVentas += '<td>-</td>';
                    } else {
                        tablaVentas += '<td>' + venta.u_nombres + '</td>';
                    }
                    if (venta.c_rut !== null) {
                        tablaVentas += '<td>' + aRut(venta.c_rut) + '</td>';
                    } else {
                        tablaVentas += '<td>-</td>';
                    }
                    if (venta.c_nombre !== null) {
                        tablaVentas += '<td>' + venta.c_nombre + '</td>';
                    } else {
                        tablaVentas += '<td>-</td>';
                    }
                    if (edicion === '1') {
                        tablaVentas += '<td class="text-center"><a href="javascript:void(0);" class="red anular_venta" codigo="' + venta.v_id + '"><i class="fa fa-times-circle fa-2x"></a></td>';
                    } else {
                        tablaVentas += '<td>-</td>';
                    }
                    tablaVentas += '</tr>';
                });
                tablaVentas += '</tbody></table>';
            }, 'json').done(
            function () {
                $('.cl-mcont').html(tablaVentas);
                
                function anularVenta(codigo) {
                    var url = 'classes/administrar_ventas.php';
                    $.post(url,
                        {
                            accion: 2,
                            codigo: codigo
                        },
                        function (data) {
                            switch (data.resultado) {
                            case 1:
                                mostrar_notificacion('Éxito', 'Venta anulada exitosamente', 'success');
                                cargarVentasRecientes();
                                break;
                            case 0:
                                mostrar_notificacion('Error', 'No se ha podido procesar su solicitud', 'danger');
                                break;
                            default:
                                mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte un administrador', 'danger');
                                break;
                                    
                            }
                        }, 'json').done(
                        function () {
                        }
                    );
                    
                }
                
                $('.anular_venta').on('click', function () {
                    var codigo = $(this).attr('codigo');
                    
                    alertify.confirm('Atención', "¿Está seguro que desea anular esta venta?",
                        function () {
                            anularVenta(codigo);
                        },
                        function () {}
                        ).set('labels',
                        {
                            ok: 'Sí',
                            cancel: 'Cancelar'
                        }
                        );
                });
                
                $('#tabla_ventas').DataTable({
                    "aaSorting": [[1, 'desc']],
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
    
    cargarVentasRecientes();
});