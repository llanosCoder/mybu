var productosCodigo = [],
    productosCantidad = [],
    productosStock = [],
    productosMinimo = [];
var indiceOrden = 0;

$(document).ready(function documentoListo() {
    obtenerOrdenesPendientes(indiceOrden);
});

$("#radio_mias").click(function clickRadioMias() {
    if ($("#radio_mias").hasClass("active")) {
        indiceOrden = 3;
    } else {
        indiceOrden = 0;
    }
    obtenerOrdenesPendientes(indiceOrden);
});

$("#radio_otros").click(function clickRadioOtros() {
    if ($("#radio_otros").hasClass("active")) {
        indiceOrden = 0;
    } else {
        indiceOrden = 3;
    }
    obtenerOrdenesPendientes(indiceOrden);
});

function obtenerOrdenesPendientes(orden) {
    var url = 'classes/obtener_ordenes.php';
    var table = '<table class="table table-bordered" id="tabla_ordenes" class="table table-bordered"><thead><tr><th>Empresa</th><th>Voucher<i></th><th>Fecha creación</th><th>Fecha Vencimiento </th><th>Total Bruto</th><th>Descuentos</th><th>Total Final</th><th>Detalle</th></tr></thead><tbody>';
    $.post(url, {
            orden: orden
        },
        function datosOrdenes(data) {
            data = $.parseJSON(data);
            $.each(data, function (i, datos) {
                table += '<tr><td>' + datos.solicitante + '</td>';
                table += '<td>' + datos.voucher + '</td><td>' + datos.f_creacion + '</td><td>' + datos.f_vencimiento + '</td>';
                var totalBruto = currencyFormat(datos.total_bruto, '$ ');
                var totalDescuentos = currencyFormat(datos.descuentos, '$ ');
                var total = currencyFormat(datos.total, '$ ');
                console.log(datos.solicitada);
                
                table += '<td>' + totalBruto + '</td><td>' + totalDescuentos + '</td><td>' + total + '</td>';
                table += '<td><a href="#"><i class="fa fa-list-alt fa-2x detalle_orden" data-modal="form-detalle-orden" onClick="obtenerProductosOrden('+datos.solicitante_id+', '+datos.solicitada+', \'' + datos.voucher + '\', ' + datos.total + ');"></i></a></td></tr>';
            });
        }
    ).done(function cargaOrdenesLista() {
        table += '</tbody></table>';
        $("#contenido_ordenes").html(table);
        $("#tabla_ordenes").dataTable({
            "lengthMenu": [5, 10, 25, 50, 100],
            "aaSorting": [[2, 'asc']],
            "pageLength": 10,
            "oLanguage": {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ pendientes",
                "sZeroRecords": "No se encontraron ordenes de compra pendientes",
                "sEmptyTable": "No hay ordenes de compra pendientes",
                "sInfo": "Mostrando ordenes de compra de la _START_ a la _END_ de un total de _TOTAL_ ordenes de compra",
                "sInfoEmpty": "Mostrando ordenes de compra de la 0 a la 0 de un total de 0 ordenes de compra",
                "sInfoFiltered": "(filtrado de un total de _MAX_ ordenes de compra)",
                "sInfoPostFix": "",
                "sSearch": "Buscar:",
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
        $('.detalle_orden').modalEffects();
    });
}

$("#contenido_ordenes").click(function clickContenidoOrdenes() {
    $('.detalle_orden').modalEffects();
    $('.asignar_promocion').modalEffects();
});

function obtenerProductosOrden(solicitante, solicitada, voucher, total) {
    if (indiceOrden == 0) {
        $("#btn_autorizar_modal").hide();
        $("#btn_rechazar_modal").hide();
        $("#btn_cancelar_modal").show();
    } else {
        $("#btn_cancelar_modal").hide();
        $("#btn_autorizar_modal").show();
        $("#btn_rechazar_modal").show();
    }
    $("#detalle_body").html('<div id="loading"><i class=fa fa-spinner fa-spin fa-2x"></i></div>');
    var url = 'classes/obtener_detalle_orden.php';
    var parametros = ['precio_m', 'oferta', 'codigo', 'nombre', 'stock_r', 'stock_m', 'cantidad', 'voucher'];
    var body = '<table id="tabla_detalle_orden" class="table table-bordered"><thead><tr><th>Código</th><th>Nombre</th><th>Cantidad</th><th>Stock</th><th>Precio</th><th>Oferta</th></tr></thead><tbody>';
    $.post(url, {
            parametros: parametros,
            voucher: voucher
        },
        function datosProductos(data) {
            data = $.parseJSON(data);
            body += '<div class="block-flat"><h4>Datos Empresa.</h4><br/>';
            $.each(data.empresa, function (i, datos) {
                body += '<span>Nombre empresa: ' + datos.e_nombre + '</span><br/><span>RUT: ' + datos.e_rut + '</span><br/>';
                body += '<span>Dirección: ' + datos.e_direccion + ", " + datos.e_ciudad + '</span>';
                if (datos.e_telefono !== undefined) {
                    body += '<br><span>Teléfono: ' + datos.e_telefono + '</span>';
                }
                if (datos.e_correo !== undefined) {
                    body += '<br><span>Correo: ' + datos.e_correo + '</span>';
                }
            });
            body += '</div>';
            var total = 0;
            $.each(data.productos, function (i, datos) {
                productosCodigo.push(datos.codigo);
                productosCantidad.push(parseInt(datos.cantidad));
                total += datos.cantidad * datos.precio_m;
                productosStock.push(parseInt(datos.stock_r));
                productosMinimo.push(parseInt(datos.stock_m));
                var cantidad = currencyFormat(datos.cantidad, '');
                var stock_r = currencyFormat(datos.stock_r, '');
                var precio_m = currencyFormat(datos.precio_m, '$ ');
                body += '<tr><td>' + datos.codigo + '</td><td>' + datos.nombre + '</td><td>' + cantidad + '</td><td>' + stock_r + '</td><td>' + precio_m + '</td>';
                body += '<td>' + datos.oferta + '</td></tr>';
            });
            body += '</tbody></table>';
            body += '<br/><span class="pull-right"><a href="#"><i class="fa fa-question-circle"></i></a>Total: ' + currencyFormat(total, '$ ') + '</span><br/>';
        }
    ).done(function cargaProductosLista() {
        $("#detalle_body").html(body);
        $("#tabla_detalle_orden").dataTable({
            "lengthMenu": [3, 5, 10],
            "oLanguage": {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ productos",
                "sZeroRecords": "No se encontraron productos",
                "sEmptyTable": "No hay productos en esta orden de compra",
                "sInfo": "Mostrando productos del _START_ al _END_ de un total de _TOTAL_ productos",
                "sInfoEmpty": "Mostrando productos del 0 al 0 de un total de 0 productos",
                "sInfoFiltered": "(filtrado de un total de _MAX_ productos)",
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
            },
        });
        $("#btn_autorizar_modal").unbind("click");
        $("#btn_rechazar_modal").unbind("click");
        $("#btn_cancelar_modal").unbind("click");
        $("#btn_autorizar_modal").bind("click", function clickAutorizarModal() {
            autorizarCompra(solicitante, voucher, total);
        });
        $("#btn_rechazar_modal").bind("click", function () {
            razonRechazo(solicitante, voucher);
        });
        $("#btn_cancelar_modal").bind("click", function () {
            cancelarOrden(solicitada, voucher);
        });
    });
}

function razonRechazo(solicitante, voucher) {
    $('.md-overlay').click(function () {
        $('#div_rechazo').hide();
    });
    $('#btn_cerrar_modal').click(function () {
        $('#div_rechazo').hide();
    });
    var div = '<div class="row form-group" id="div_rechazo" style="width:90%;margin-left:5%;"><label>Razon del rechazo (opcional)</label><textarea class="form-control" id="text-razon"></textarea></div>';
    $("#razon_rechazo").html(div);
    $("#btn_rechazar_modal").unbind("click");
    $("#btn_rechazar_modal").bind("click", function clickRechazarModal() {
        var razon = $('#text-razon').val();
        if (razon != 'undefined' && razon != '') {
            $("#razon_rechazo").html('');
            $("#form-detalle-orden").removeClass('md-show');
            alertify.confirm('<i class="fa fa-exclamation-triangle warning"></i> Atención', '¿Está seguro que desea rechazar la compra?',
                function () {
                    rechazarCompra(solicitante, voucher, razon);
                },
                function () {}
            ).set('labels', {
                ok: 'Sí',
                cancel: 'Cancelar'
            });
        } else {
            $('#text-razon').parent().addClass('has-error');
        }
    });
}

function autorizarCompra(solicitante, voucher, total) {
    for (var i = 0; i < productosCodigo.length; i++) {
        if (productosStock[i] < productosCantidad[i]) {
            $("#form-detalle-orden").removeClass('md-show');
            alertify.confirm('<i class="fa fa-exclamation-triangle warning"></i> Atención', 'El stock existente del producto ' + productosCodigo[i] + ' es menor a la cantidad solicitada, ¿desea continuar?<a href="inventario_bodega.html"><br/><br/><button class="btn btn-flat btn-primary">Ir a bodega</button></a>',
                function () {
                    procesarCompra(solicitante, voucher, total);
                },
                function () {}
            ).set('labels', {
                ok: 'Sí',
                cancel: 'Cancelar'
            });
        } else {
            if (productosStock[i] - productosMinimo[i] < productosCantidad[i]) {
                $("#form-detalle-orden").removeClass('md-show');
                alertify.confirm('<i class="fa fa-exclamation-triangle warning" ></i> Atención', 'Si autorizas la compra, el stock del producto ' + productosCodigo[i] + ' bajará del mínimo, ¿desea continuar?<br/><br/><button class="btn btn-flat btn-primary ">Ir a bodega</button></a>',
                    function () {
                        procesarCompra(solicitante, voucher, total);
                    },
                    function () {}
                ).set('labels', {
                    ok: 'Sí',
                    cancel: 'Cancelar'
                });
            } else {
                $("#form-detalle-orden").removeClass('md-show');
                procesarCompra(solicitante, voucher, total);
            }
        }
    }
};

function rechazarCompra(solicitante, voucher, razon) {
    var vouchers = new Object();
    vouchers.voucher = new Object();
    vouchers.voucher.voucher = voucher;
    if (razon == '' || razon == 'undefined')
        return false;
    var url = 'classes/procesar_compra.php';
    $.post(url, {
            solicitante: solicitante,
            voucher: vouchers,
            rechazar: true,
            razon: razon
        },
        function (data) {
            switch (data) {
            case '0':
                mostrar_notificacion('Error', 'No se pudo cerrar orden de compra.', 'danger');
                break;
            case '1':
                mostrar_notificacion('Éxito', 'Orden de compra rechazada.', 'success');
                break;
            default:
                mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte a un administrador.', 'danger');
                break;
            }
        }
    ).done(function () {
        obtenerOrdenesPendientes(indiceOrden);
        //$("#form-detalle-orden").removeClass('md-show');
    });
}

function cancelarOrden(solicitada, voucher){
    
    $("#form-detalle-orden").removeClass('md-show');
    alertify.confirm('<i class="fa fa-exclamation-triangle warning" ></i> Atención', '¿Está seguro que desea cancelar esta orden?',
        function () {
            var url = 'classes/cancelar_orden.php';
            $.post(url,
            {solicitado: solicitada,
             voucher: voucher},
            function(data){
                switch(data){
                    case '0':
                        mostrar_notificacion('Error', 'No se pudo cancelar orden de compra.', 'danger');
                        break;
                    case '1':
                        mostrar_notificacion('Éxito', 'Orden de compra cancelada.', 'success');
                        obtenerOrdenesPendientes(indiceOrden);
                        break;
                    case '2':
                        mostrar_notificacion('Error', 'No tiene permisos para cancelar esta orden.', 'danger');
                        break;
                    default:
                        mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte a un administrador.', 'danger');
                        break;
                }
            });
        },
        function () {}
    ).set('labels', {
        ok: 'Sí',
        cancel: 'Cancelar'
    });
}

function procesarCompra(solicitante, voucher, total) {
    var url = "classes/procesar_compra.php";

    /*var vouchers = {};
    vouchers['voucher1'] = {"medio_pago": 2, "monto": total};
    vouchers['voucher1'] += {"medio_pago": 3, "monto": total};*/
    var vouchers = new Object();
    vouchers.voucher = new Object();
    vouchers.voucher.voucher = voucher;
    vouchers.voucher.pagos = [];
    var pago = {
        'modo_pago': 2,
        'monto': total
    };
    vouchers.voucher.pagos.push(pago);
    console.log(vouchers);
    $.post(url, {
            solicitante: solicitante,
            voucher: vouchers,
            oferta: true
        },
        function (data) {
            switch (data) {
            case '0':
                mostrar_notificacion('Error', 'No se ha podido procesar su venta', 'danger');
                break;
            case '1':
                mostrar_notificacion('Éxito', 'Venta realizada satisfactoriamente', 'success');
                obtenerOrdenesPendientes(indiceOrden);
                break;
            case '2':
                mostrar_notificacion('Error', 'Stock de producto insuficiente.', 'danger');
                break;
            case '3':
                mostrar_notificacion('Atención', 'No se pudo cerrar orden de compra. Venta realizada.', 'warning');
                break;
            case '4':
                mostrar_notificacion('Atención', 'Venta no registrada. Stock descontado. Orden no cerrada.', 'warning');
                break;
            case '5':
                mostrar_notificacion('Error', 'No se pudieron registrar los pagos.', 'danger');
                break;
            case '6':
                mostrar_notificacion('Atención', 'No se registró la venta en los tags.', 'warning');
                break;
            case '7':
                mostrar_notificacion('Atención', 'No se registró la razón del rechazo.', 'warning');
                break;
            default:
                mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte a un administrador.', 'danger');
                break;
            }
        }
    );
}