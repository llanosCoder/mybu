var categoriaNueva = '';
var paginaActualForm = 1;
var codigoProducto = 0;

$(document).ready(function () {
    cargarProductos();
});

function cargarProductos() {
    $("#pcont").html('<div id="loading"><i class="fa fa-spinner fa-spin fa-4x"></i></div>');
    var table = '<div class="sin-resultados"><h3><i>No se encontraron resultados</i></h3></div>';
    var arrayParametros = ['id', 'codigo', 'sucursal', 'nombre', 'marca_nombre', 'descripcion', 'precio_u', 'precio_m', 'stock_r', 'stock_m', 'tipo_cuenta']
    var puedeEditar = false;
    $.post("classes/obtener_productos.php", {
            parametros: arrayParametros
        },
        function (data) {
            data = $.parseJSON(data);
            /*if(data != '[]'){
                table = '';
            }else{*/
            table = '<table id="tabla_bodega" class="table table-bordered"><thead><tr><th>Código</th><th>Sucursal</th><th>Nombre</th><th>Marca</th>';
            table += '<th>Descripción</th><th>Precio Unit.</th><th>Precio Mayor.</th><th>Stock real</th><th>Stock mín.</th>';
            if (data['tipo_cuenta'] == '1' || data['tipo_cuenta'] == '2') {
                table += '<th>Edición</th><th>Agregar Stock</th>';
                puedeEditar = true;
            }
            table += '</tr></thead><tbody>';
            //}
            $.each(data.productos, function (i, datos) {

                table += '<tr><td>' + datos.codigo + '</td><td>' + datos.sucursal + '</td>';
                table += '<td>' + datos.nombre + '</td>';
                if (datos.marca_nombre == null) {
                    table += '<td>-</td>';
                } else {
                    table += '<td>' + datos.marca_nombre + '</td>';
                }
                table += '<td>' + datos.descripcion + '</td>';
                var precio_u = currencyFormat(datos.precio_u, '$');
                table += '<td>' + precio_u + '</td>';
                var precio_m = currencyFormat(datos.precio_m, '$');
                table += '<td>' + precio_m + '</td>';
                var stockReal = parseInt(datos.stock_r);
                var stockMin = parseInt(datos.stock_m);
                if (stockReal <= stockMin) {
                    table += '<td class="caution">' + currencyFormat(datos.stock_r, '') + '</td>';
                } else {
                    stockMinRango = Math.floor(stockMin / 4);
                    if ((stockMin + stockMinRango) >= stockReal) {
                        table += '<td class="warning">' + currencyFormat(datos.stock_r, '') + '</td>';
                    } else {
                        table += '<td>' + currencyFormat(datos.stock_r, '') + '</td>';
                    }
                }
                table += '<td>' + currencyFormat(datos.stock_m, '') + '</td>';
                if (puedeEditar) {
                    table += '<td><a href="#" onClick="setDatos(\'' + datos.codigo + '\', ' + datos.precio_u + ', ' + datos.precio_m + ', ' + datos.stock_m + ');"><i class="fa fa-pencil-square fa-2x editar_producto" data-modal="form-agregar-producto"></i></a></td>';
                    table += '<td><a href="#" onClick="setStock(\'' + datos.codigo + '\');"><i class="fa fa-plus-square fa-2x agregar_stock" data-modal="form-agregar-stock"></i></a></td>';
                }
                table += '</tr>';
            });
        }
    ).done(function () {
        table += '</tbody></table>';
        $("#pcont").html(table);
        $('#tabla_bodega').DataTable({
            "aaSorting": [[0, 'asc']],
            "oLanguage": {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ productos",
                "sZeroRecords": "No se han encontrado productos disponibles",
                "sEmptyTable": "No se han encontrado productos disponibles",
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
            }
        });
        $('.fa-pencil-square').modalEffects();
        $('.fa-plus-square').modalEffects();
    });
}

$("#pcont").click(function () {
    
    $('.fa-pencil-square').modalEffects();
    $('.asignar_promocion').modalEffects();
});

function setDatos(codigo, precio_u, precio_m, stock_m) {
    $(".formulario").each(function () {
        this.reset();
    });
    $("#codigo").val(codigo);
    $("#precio_u").val(currencyFormat(precio_u, ''));
    $("#precio_m").val(currencyFormat(precio_m, ''));
    $("#stock_m").val(currencyFormat(stock_m, ''));
    $(".formulario").find(':input').each(function () {
        var elemento = this;
        if ($("#" + elemento.id).parent().hasClass('has-error')) {
            $("#" + elemento.id).parent().removeClass('has-error');
        }
    });
    $(".modal-title").html("<h3>Producto</h3>");
    $("#form-agregar-producto-pag-1").show();
    $('#codigo').prop('disabled', true);
};

function setStock(codigo) {
    codigoProducto = codigo;
    $("#form-agregar-stock").show();
    $("#form2").each(function () {
        this.reset();
    });
    $("#form2").find(':input').each(function () {
        var elemento = this;
        if ($("#" + elemento.id).parent().hasClass('has-error')) {
            $("#" + elemento.id).parent().removeClass('has-error');
        }
    });
    $('#form2').append('<input type="hidden" id="codigo2" name="codigo2" value="'+codigo+'">');
    $("#form-agregar-producto-pag-1").show();
}


function guardarModal() {
    habilitarDeshabilitarBoton('btn_aceptar_modal', false);
    var datos = new Object();
    datos['codigo'] = $('#codigo').val();
    datos['precio_u'] = sanear_numero($("#precio_u").val());
    datos['precio_m'] = sanear_numero($("#precio_m").val());
    datos['stock_m'] = sanear_numero($("#stock_m").val());
    var focusTaken = false;
    for (valor in datos) {
        if (datos[valor] == null || datos[valor] == '') {
            if ($("#" + valor).hasClass('required')) {
                $("#" + valor).parent().addClass('has-error');
                if (!focusTaken) {
                    $("#" + valor).focus();
                    focusTaken = true;
                }
            }
        } else {
            if ($("#" + valor).hasClass('numeric') && ($.isNumeric(datos[valor]) == false || $("#" + valor).val() < 0)) {
                $("#" + valor).parent().addClass('has-error');
                focusTaken = true;
            } else {
                if ($("#" + valor).parent().hasClass('has-error')) {
                    $("#" + valor).parent().removeClass('has-error');
                }
                if ($("#" + valor).hasClass('numeric') && $.isNumeric(datos[valor])) {
                    $("#" + valor).parent().removeClass('has-error');
                }
            }
        }
    }
    if (!focusTaken) {
        var url = 'classes/editar_bodega.php';
        $.post(
            url, {
                codigo: datos['codigo'],
                precio_u: datos['precio_u'],
                precio_m: datos['precio_m'],
                stock_m: datos['stock_m']
            },
            function (data) {
                switch (data) {
                case '0':
                    var mensaje = '',
                        type = '',
                        title = '';
                    mensaje = 'No se han cambiado valores en su producto';
                    type = 'warning';
                    title = 'Atención';
                    mostrar_notificacion(title, mensaje, type, 'bottom-left');
                    break;
                case '1':
                    var mensaje = '',
                        type = '',
                        title = '';
                    mensaje = 'Su producto se ha editado exitosamente'
                    type = 'success';
                    title = 'Éxito';
                    mostrar_notificacion(title, mensaje, type, 'bottom-left');
                    $('#form-agregar-producto').removeClass('md-show');
                    cargarProductos();
                    break;
                default:
                    var mensaje = 'Ha ocurrido un error inesperado. Por favor, contacte a un administrador';
                    mostrar_notificacion('Error', mensaje, 'danger', 'bottom-left');
                    break;
                }
            }
        ).done(function () {
            habilitarDeshabilitarBoton('btn_aceptar_modal', true);
            
        });
    } else {
        mostrar_notificacion('Error', 'Ingrese los datos correctamente', 'danger');
        habilitarDeshabilitarBoton('btn_aceptar_modal', true);
    }
};

function guardarModalStock() {
    habilitarDeshabilitarBoton('btn_aceptar_modal_stock', false);
    var stock_r = sanear_numero($('#stock_r').val());
    var codigo = 0;
    if(codigoProducto !== 0){
        codigo = codigoProducto;
    }else{
        codigo = $('#codigo').val();
    }
    if (stock_r != '' && ($.isNumeric(stock_r)) && codigo != '' && stock_r.charAt(0) != '-') {
        var url = 'classes/editar_bodega.php';
        $.post(
            url, {
                codigo: codigo,
                stock_r: stock_r
            },
            function (data) {
                switch (data) {
                case '0':
                    var mensaje = '',
                        type = '',
                        title = '';
                    mensaje = 'No se han cambiado valores en su producto';
                    type = 'warning';
                    title = 'Atención';
                    mostrar_notificacion(title, mensaje, type, 'bottom-left');
                    break;
                case '1':
                    var mensaje = '',
                        type = '',
                        title = '';
                    mensaje = 'Su producto se ha editado exitosamente'
                    type = 'success';
                    title = 'Éxito';
                    mostrar_notificacion(title, mensaje, type, 'bottom-left');
                    $('#form-agregar-stock').removeClass('md-show');
                    cargarProductos();
                    break;
                default:
                    var mensaje = 'Ha ocurrido un error inesperado. Por favor, contacte a un administrador';
                    mostrar_notificacion('Error', mensaje, 'danger', 'bottom-left');
                    break;
                }
            }
        ).done(function () {
            habilitarDeshabilitarBoton('btn_aceptar_modal_stock', true);
        });

    } else {
        mostrar_notificacion('Error', 'El stock ingresado no es válido. Sólo ingrese números.', 'danger', 'bottom-left');
        $("#stock_r").parent().addClass('has-error');
        habilitarDeshabilitarBoton('btn_aceptar_modal_stock', true);
    }
}

$("#pcont").click(function () {
    $('.fa-pencil-square').modalEffects();
    $('.fa-plus-square').modalEffects();
    $('.asignar_promocion').modalEffects();
});