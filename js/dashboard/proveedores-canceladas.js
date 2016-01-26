var productosCodigo = [],
    productosCantidad = [],
    productosStock = [],
    productosMinimo = [];

$(document).ready(function documentoListo() {
    obtenerOrdenesCanceladas(8);
});

function obtenerOrdenesCanceladas(orden) {
    var url = 'classes/obtener_ordenes.php';
    var table = '<table class="table table-bordered" id="tabla_ordenes" class="table table-bordered"><thead><tr><th>Voucher<i></th><th>Fecha creación</th><th>Fecha Vencimiento </th><th>Total Bruto</th><th>Descuentos</th><th>Total Final</th><th>Detalle</th></tr></thead><tbody>';
    $.post(url, {
            orden: orden
        },
        function datosOrdenes(data) {
            data = $.parseJSON(data);
            $.each(data, function (i, datos) {
                table += '<tr><td>' + datos.voucher + '</td><td>' + datos.f_creacion + '</td><td>' + datos.f_vencimiento + '</td>';
                var totalBruto = currencyFormat(datos.total_bruto, '$ ');
                var totalDescuentos = currencyFormat(datos.descuentos, '$ ');
                var total = currencyFormat(datos.total, '$ ');
                table += '<td>' + totalBruto + '</td><td>' + totalDescuentos + '</td><td>' + total + '</td>';
                table += '<td><a href="#"><i class="fa fa-list-alt fa-2x detalle_orden" data-modal="form-detalle-orden" onClick="obtenerProductosOrden(\'' + datos.voucher + '\', ' + datos.total + ');"></i></a></td></tr>';
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
                "sZeroRecords": "No se encontraron ordenes de compra canceladas",
                "sEmptyTable": "No hay ordenes de compra canceladas",
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

function obtenerProductosOrden(voucher, total) {
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
    });
}