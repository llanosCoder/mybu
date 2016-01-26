/*global $*/
var url = 'classes/obtener_datos_sesion.php';
var parametros = ['id', 'rol'];
var usuario = "";
var rol = "";

function activar_cuenta(id) {
    'use strict';
    var url = 'classes/administrar_cuentas.php';
    $.post(url,
        {
            cId: id
        },
        function (data) {
            
        }).done(
        function () {
        }
    );
}

$.post(url,
    {
        parametros: parametros
    },
    function (data) {
        'use strict';
        data = $.parseJSON(data);
        $.each(data, function (i, datos) {
            usuario = datos.id;
            rol = datos.rol;
        });
    }

    ).done(function (data) {
    'use strict';

    if (usuario !== '0' && rol !== '1') {


        var url2 = "clases/traer_fichas.php",
            enviar = usuario,
            tabla = '<table class="table table-bordered responsive" id="tabla_fichas"><thead><tr><th># Folio</th><th>Fecha Ing.</th><th>Nombre Cliente</th><th>Nombre Fantasía</th><th>Rut Empresa</th><th>Nombre Contacto</th><th>Mail Contacto</th><th>T-Móvil</th><th>T-Fijo</th><th>Plan</th><th>Nombre Proveedor</th><th>Vendedor</th><th>Estado</th><th>Activar</th></tr></thead><tbody>';

        $.post(url2,
            {
                user: enviar,
                rol: rol
            },
            function (data) {
                var mandar = $.parseJSON(data);

                $.each(mandar.cliente, function (i, item) {

                    tabla += "<tr>" + "<td>" + item.nmr_folio + "</td>" + "<td>" + item.fecha_ing + "</td>" + "<td>" + item.cli_nombre + " " + item.cli_app + " " + item.cli_apm + "</td>" + "<td>" + item.cli_fantasia + "</td>" + "<td>" + item.cli_rut_emp + "</td>" + "<td>" + item.con_nombre + "</td>" + "<td>" + item.con_mail + "</td>" + "<td>" + item.con_tmovil + "</td>" + "<td>" + item.con_tfijo + "</td>" + "<td>" + item.serv_tipoplan + "</td>" + "<td>" + item.serv_nombre_proveedor + "</td>" + "<td>" + item.vendedor + "</td>" + "<td align='center'><a href='javscript:void(0);'><i class='fa fa-circle fa-2x' id=" + item.nmr_folio + "></i></a></td>";
                    tabla += '<td><a href="javascript:void(0);"><i class="fa fa-user-plus fa-2x activar_cuenta" id="folio_' + item.nmr_folio + '"></i></a></td></tr>';

                });

                tabla += '</tbody></table>';
                $("#tabla_wrap").html(tabla);
                $("#tabla_fichas").DataTable({
                    "aaSorting": [[0, 'asc']],
                    "oLanguage": {
                        "sProcessing": "Procesando...",
                        "sLengthMenu": "Mostrar _MENU_ ",
                        "sZeroRecords": "No se han encontrado datos disponibles",
                        "sEmptyTable": "No se han encontrado datos disponibles",
                        "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                        "sInfoEmpty": "Mostrando  del 0 al 0 de un total de 0 ",
                        "sInfoFiltered": "(filtrado de un total de _MAX_ datos)",
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
                $('.activar_cuenta').on('click', function () {
                    activar_cuenta($(this).attr('id'));
                });
            }
            );
    } else {
        window.location.href = './';
    }

});