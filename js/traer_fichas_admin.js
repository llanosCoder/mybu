/*global $*/
var url = 'classes/obtener_datos_sesion.php';
var parametros = ['id', 'rol'];
var usuario = "";
var rol = "";

$(document).ready(function () {
    'use strict';
    cargarFichas();
    $('.nfn-overlay').hide();
});

function enviar_correo(user, pass, nombre, apellido, correo) {
    'use strict';
    var url = 'http://www.nfn.cl/enviar_correo.php';
    $.post(url,
        {
            user: user,
            pass: pass,
            correo: correo,
            nombre: nombre,
            apellido: apellido,
        },
        function (data) {
            
        }).done(
        function () {
        }
    );
    
}

function feedback(codigo) {
    'use strict';
    
    var mensaje = '';
    switch (codigo) {
        case 1:
            mensaje = 'Error al crear la sucursal (BD cliente)';
            break;
        case 2:
            mensaje = 'Error al ingresar usuario a la sucursal (BD cliente)';
            break;
        case 3:
            mensaje = 'Error al ingresar usuario a la empresa (BD cliente)';
            break;
        case 4:
            mensaje = 'Error al crear contraseña para usuario (BD cliente)';
            break;
        case 5:
            mensaje = 'Error al crear usuario (BD cliente)';
            break;
        case 6:
            mensaje = 'Error al crear empresa (BD cliente)';
            break;
        case 7:
            mensaje = 'Error al ingresar usuario a la sucursal (BD Administración)';
            break;
        case 8:
            mensaje = 'Error al asignarle un rol al usuario (BD Administración)';
            break;
        case 9:
            mensaje = 'Error al ingresar usuario a la empresa (BD Administración)';
            break;
        case 10:
            mensaje = 'Error al crear contraseña para usuario (BD Administración)';
            break;
        case 11:
            mensaje = 'Error al crear usuario (BD Administración)';
            break;
        case 12:
            mensaje = 'Error al crear host para empresa (BD Administración)';
            break;
        case 13:
            mensaje = 'Error al crear empresa (BD Administración)';
            break;
        case 14:
            mensaje = 'Error al crear BD para cliente';
            break;
        case 15:
            mensaje = 'Nro de folio ya en uso';
            break;
        default:
            mensaje = 'Error inesperado';
            break;
    }
    mostrar_notificacion('Error', mensaje, 'danger');
}

function activar_cuenta(id) {
    'use strict';
    var url = 'classes/administrar_cuentas.php';
    id = id.replace('folio_', '');
    $('.nfn-overlay').show();
    $.post(url,
        {
            accion: 1,
            cId: id
        },
        function (data) {
            data = $.parseJSON(data);
            var datos = data.resultado[0];
            $('.nfn-overlay').hide();
            switch (datos.resultado) {
            case 1:
                enviar_correo(data.datos[0].user, data.datos[0].pass, data.datos[0].nombre, data.datos[0].apellido, data.datos[0].correo);
            mostrar_notificacion('Éxito', 'Usuario creado satisfactoriamente', 'success');
                break;
            case 0:
                feedback(data.resultado[0].codigo);
                mostrar_notificacion('Error', 'No se ha podido crear el usuario', 'danger');
                break;
            case 3:
                feedback(data.resultado[0].codigo);
                mostrar_notificacion('Error', 'No se ha podido crear el usuario, algunas acciones no se pudieron deshacer', 'danger');
                break;
            }
        }).done(
        function () {
            cargarFichas();
            
        }
    );
}

function cargarFichas() {
    'use strict';
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
                    if(item.cli_app.indexOf(" ") == -1 && item.cli_apm.indexOf(" ") == -1){
                        tabla += "<tr>";
                    }else{
                        tabla += "<tr class='warn'>";
                    }
                    tabla += "<td>" + item.nmr_folio + "</td>" + "<td>" + item.fecha_ing + "</td>" + "<td>" + item.cli_nombre + " " + item.cli_app + " " + item.cli_apm + "</td>" + "<td>" + item.cli_fantasia + "</td>" + "<td>" + item.cli_rut_emp + "</td>" + "<td>" + item.con_nombre + "</td>" + "<td>" + item.con_mail + "</td>" + "<td>" + item.con_tmovil + "</td>" + "<td>" + item.con_tfijo + "</td>" + "<td>" + item.serv_tipoplan + "</td>" + "<td>" + item.serv_nombre_proveedor + "</td>" + "<td>" + item.vendedor + "</td>";
                    
                    if (item.estado === '0') {
                        tabla += "<td align='center' style='color:yellow;'><i class='fa fa-circle fa-2x' id=" + item.nmr_folio + "></i></td>";
                        tabla += '<td><a href="javascript:void(0);"><i class="fa fa-user-plus fa-2x activar_cuenta" id="folio_' + item.nmr_folio + '"></i></a></td></tr>';
                    }else{
                        if(item.estado === '1'){
                            tabla += "<td align='center' style='color:green;'><i class='fa fa-circle fa-2x' id=" + item.nmr_folio + "></i></td>";
                        }else{
                            tabla += "<td align='center'><i class='fa fa-circle fa-2x' id=" + item.nmr_folio + "></i></td>";
                        }
                        tabla += '<td>-</td></tr>';
                    }

                });

                tabla += '</tbody></table>';
                $("#cl-mcont").html(tabla);
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
}

$('#cl-mcont').on('click', function () {
    $('.activar_cuenta').off('click');
    $('.activar_cuenta').on('click', function () {
        activar_cuenta($(this).attr('id'));
    });
});