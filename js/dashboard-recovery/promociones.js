var arrayEstados = [];
var ultimoRadioCliqueado = 1,
    alcanceSucursal = 1;
var idActual = 0,
    paginaActualForm = 1,
    listaProductos = [];
var promocionActiva = false,
    promocionesActuales = 'normales';

$(document).ready(function () {
    var f = new Date();
    var month = parseInt(f.getMonth()) + 1;
    var fecha = f.getFullYear() + '-' + month + '-' + f.getDate() + 'T00:00:00Z';
    $('.datetime').attr('data-date', fecha);
    cargarPromociones();
});

function cargarPromociones() {
    promocionesActuales = 'normales';
    $("#contenido_promociones").html('<div id="loading"><i class="fa fa-spinner fa-2x fa-spin"></i></div>');
    var promociones = '',
        cantidadPromociones = 0;
    $.post("classes/obtener_promociones.php", {
            sId: 1
        },
        function (data) {
            data = $.parseJSON(data);
            promociones += '<div id="tabla_promociones_cont"><table id="tabla_promociones" class="table table-bordered"><thead><tr><th>Descripción</th><th>Descuento</th><th>Fecha inicio</th><th>Fecha término</th><th>Stock</th><th>Editar</th><th>Control</th><th>Eliminar</th></tr></thead><tbody>';
            $.each(data, function (i, datos) {
                cantidadPromociones++;
                if (i == 0) {
                    promociones += '';
                }
                promociones += '<tr><td>' + datos.descripcion + '</td><td>' + datos.descuento + '</td>';
                if (datos.f_inicio == '0000-00-00 00:00:00') {
                    promociones += '<td>-</td>';
                } else {
                    promociones += '<td>' + datos.f_inicio + '</td>';
                }
                if (datos.f_termino == '0000-00-00 00:00:00') {
                    promociones += '<td>-</td>';
                } else {
                    promociones += '<td>' + datos.f_termino + '</td>';
                }
                promociones += '<td>' + datos.stock + '</td>';
                promociones += '<td class="text-center"><a href="#"><i class="fa fa-pencil-square fa-2x agregar_promocion" data-modal="form-agregar-promocion" onClick="editarPromocion(' + datos.id + ', \'' + datos.cantidad + '\', \'' + datos.descripcion + '\',\'' + datos.tipo_promocion + '\', \'' + datos.precio + '\', \'' + datos.descuento + '\', \'' + datos.f_inicio + '\', \'' + datos.f_termino + '\', ' + datos.stock + ');"></i></a></td>';
                arrayEstados[datos.id] = datos.estado;
                promociones += '<td><div data-modal="form-procesar-control" id="' + datos.id + 'div" onClick="activarDesactivarPromocion(' + datos.id + ', ' + arrayEstados[i] + ');" class="activar_desactivar_promocion disabled"><input type="checkbox" id="' + datos.id + 'checkbox" class="control-promocion"';
                if (arrayEstados[datos.id] == '1')
                    promociones += ' checked ';
                promociones += 'data-toggle="toggle"></div></td>';
                promociones += '<td class="text-center"><a href="#" style="color:red;"><i class="fa fa-times-circle fa-2x" onClick="eliminarPromocion(' + datos.id + ')"></i></a></td></tr>';
            });
            promociones += '</tbody></table></div>';
        }
    ).done(function () {
        $("#contenido_promociones").html(promociones);
        $("#tabla_promociones").DataTable({
            "aaSorting": [[7, 'asc']],
            "oLanguage": {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ promociones",
                "sZeroRecords": "No se encontraron promociones",
                "sEmptyTable": "No hay promociones disponibles",
                "sInfo": "Mostrando promociones de la _START_ a la _END_ de un total de _TOTAL_ promociones",
                "sInfoEmpty": "Mostrando promociones de la 0 a la 0 de un total de 0 promociones",
                "sInfoFiltered": "(filtrado de un total de _MAX_ promociones)",
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
        $('.control-promocion').bootstrapToggle();
        $(".activar_desactivar_promocion").modalEffects();
        $(".stock").prop('disabled', true);


        $(".agregar_promocion").modalEffects();
    });
}

function cargarPromocionesEspeciales() {
    promocionesActuales = 'especiales';
    $("#contenido_promociones").html('<div id="loading"><i class="fa fa-spinner fa-2x fa-spin"></i></div>');
    var promociones = '',
        parametros = ['id', 'tipo', 'descripcion', 'porcentaje', 'estado'];

    $.post("classes/administrar_promociones_especiales.php", {
            accion: 1,
            parametros: parametros
        },
        function (data) {
            data = $.parseJSON(data);
            promociones += '<div id="tabla_promociones_especiales_cont"><table id="tabla_promociones_especiales" class="table table-bordered"><thead><tr><th>Tipo</th><th>Descripción</th><th>Porcentaje</th><th>Editar</th><th>Control</th><th>Eliminar</th></tr></thead><tbody>';
            $.each(data.promociones, function (i, datos) {
                promociones += '<tr><td>' + datos.tipo + '</td><td>' + datos.descripcion + '</td>';
                promociones += '<td>' + datos.porcentaje + '%</td>';
                promociones += '<td class="text-center"><a href="#"><i class="fa fa-pencil-square fa-2x agregar_promocion" data-modal="form-agregar-promocion_especial" onClick="editarPromocionEspecial(' + datos.id + ', \'' + datos.tipo + '\', \'' + datos.descripcion + '\',\'' + datos.porcentaje + '\');"></i></a></td>';
                arrayEstados[datos.id] = datos.estado;
                promociones += '<td><div data-modal="form-procesar-control" id="' + datos.id + 'div" onClick="activarDesactivarPromocion(' + datos.id + ', ' + arrayEstados[datos.id] + ');" class="activar_desactivar_promocion disabled"><input type="checkbox" id="' + datos.id + 'checkbox" class="control-promocion"';
                if (arrayEstados[datos.id] == '1')
                    promociones += ' checked ';
                promociones += 'data-toggle="toggle"></div></td>';
                promociones += '<td class="text-center"><a href="#" style="color:red;"><i class="fa fa-times-circle fa-2x" onClick="eliminarPromocionEspecial(' + datos.id + ')"></i></a></td></tr>';
            });
            promociones += '</tbody></table></div>';
        }
    ).done(function () {
        $("#contenido_promociones").html(promociones);
        $("#tabla_promociones_especiales").dataTable({
            "aaSorting": [[1, 'asc']],
            "oLanguage": {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ promociones",
                "sZeroRecords": "No se encontraron promociones",
                "sEmptyTable": "No hay promociones disponibles",
                "sInfo": "Mostrando promociones de la _START_ a la _END_ de un total de _TOTAL_ promociones",
                "sInfoEmpty": "Mostrando promociones de la 0 a la 0 de un total de 0 promociones",
                "sInfoFiltered": "(filtrado de un total de _MAX_ promociones)",
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
        $('.control-promocion').bootstrapToggle();
        $(".activar_desactivar_promocion").modalEffects();
        $(".stock").prop('disabled', true);


        $(".agregar_promocion").modalEffects();
    });
}

function cargarProductos() {
    $("#contenido_promociones").html('<div id="loading"><i class="fa fa-spinner fa-2x fa-spin"></i></div>');
    var productos = '';
    productos += '<table id="tabla_productos" class="table table-bordered"><thead><tr><th>Seleccionar</th><th>Código</th><th>Nombre</th><th>Marca</th><th>Descripción</th><th>Promoción</th></tr></thead><tbody>';
    var url = 'classes/obtener_productos.php',
        parametros = ['codigo', 'nombre', 'marca_nombre', 'descripcion', 'promocion'];
    $.post(url, {
            parametros: parametros
        },
        function (data) {
            data = $.parseJSON(data);
            $.each(data.productos, function (i, datos) {
                var tienePromocion = true;
                var promocion = datos.promocion
                if (promocion == null || promocion == 'undefined' || promocion == '') {
                    promocion = '-';
                    tienePromocion = false;
                }
                productos += '<tr><td><input type="checkbox" name="option1" value="' + datos.codigo + '" id="' + datos.codigo + '" onChange="listarProductos(\'' + datos.codigo + '\', ' + tienePromocion + ');"></td>';
                productos += '<td>' + datos.codigo + '</td><td>' + datos.nombre + '</td><td>' + datos.marca_nombre + '</td><td>' + datos.descripcion + '</td>';
                productos += '<td>' + promocion + '</td></tr>';
            });
        }).done(function obtenerProductosTerminada() {
        productos += '</tbody></table>';
        $("#contenido_promociones").html(productos);
        $("#tabla_productos").dataTable({
            "order": [0, 'asc'],
            "oLanguage": {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ productos",
                "sZeroRecords": "No se han encontrado productos disponibles",
                "sEmptyTable": "No se han encontrado productos disponibles",
                "sInfo": '<button class="btn btn-default pull-right" id="btn_asignar_promo" onClick="prepararLista();" data-modal="form-asignar-promocion"><i class="fa fa-caret-up"></i> Asignar a una Promoción</button>',
                "sInfoEmpty": "Mostrando productos del 0 al 0 de un total de 0 productos",
                "sInfoFiltered": "",
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
        $('#btn_agregar_promocion').modalEffects();
        $("#btn_asignar_promo").modalEffects();
    });
};

function listarProductos(codigo, tienePromocion) {

    if (tienePromocion)
        promocionActiva = true;
    else
        promocionActiva = false;
    if ($("#" + codigo).is(":checked")) {
        listaProductos[listaProductos.length] = codigo;
    } else {
        for (var i = 0; i < listaProductos.length; i++) {
            if (listaProductos[i] == codigo) {
                listaProductos.splice(i, 1);
            }
        }
    }
};

function prepararLista() {
    if (listaProductos.length == 0) {
        $('#form-asignar-promocion').hide();
        $('.md-overlay').hide();
        mostrar_notificacion('Atención', 'Debe seleccionar al menos un producto', 'warning');
        $(':checkbox').addClass('shake');
        setTimeout(function () {
            $(':checkbox').removeClass('shake');
            $('#form-asignar-promocion').removeClass('md-show');
            $('#form-asignar-promocion').show();
            $('.md-overlay').show();
        }, 500);
    } else {
        //$('#form-asignar-promocion').addClass('md-show');
        var url = 'classes/obtener_promociones.php';
        $('#modal-title').html('<h3>Asignar Promoción</h3>');
        var promociones = '<option value=""></option>';
        $.post(url, {},
            function (data) {
                data = $.parseJSON(data);
                $.each(data, function (i, datos) {
                    promociones += '<option value="' + datos.id + '">' + datos.descripcion + '</option>';
                });
            }).done(function () {
            $('#select_promociones').html(promociones);
            $('#select_promociones').select2();
            //$('#t').val('0').trigger("change");
            var productosAsignar = '<ul>';
            for (var i = 0; i < listaProductos.length; i++) {
                if (i > 4) {
                    break;
                }
                productosAsignar += '<li>' + listaProductos[i] + '</li>';
            }
            productosAsignar += '</ul>';
            productosAsignar += '<a href="#" id="listado_completo" onClick="listarCompleto()">Ver listado completo...</a>';
            $("#lista-body").html(productosAsignar);
        });
    }
};

function listarCompleto() {
    var productosAsignar = '<ul>';
    for (var i = 0; i < listaProductos.length; i++) {
        productosAsignar += '<li>' + listaProductos[i] + '</li>';
    }
    productosAsignar += '</ul>';
    productosAsignar += '<a href="#" id="listado_resumido" onClick="prepararLista()">Ver listado resumido...</a>';
    console.log(productosAsignar);
    $("#lista-body").html(productosAsignar);
}

function guardarModalAsignar() {
    if (promocionActiva) {
        $("#form-asignar-promocion").removeClass('md-show');
        alertify.confirm('Atención', "Este producto ya tiene una promoción asignada ¿Desea reemplazarla?",
            function () {
                enviarDatos();
            },
            function () {}
        ).set('labels', {
            ok: 'Sí',
            cancel: 'Cancelar'
        });
    } else {
        enviarDatos();
    }

}

function enviarDatos() {
    var url = 'classes/asignar_promociones.php';
    var promociones = $("#select_promociones").val()
    console.log(promociones);

    var productoSeleccionado = false;
    for (var i = 0; i < listaProductos.length; i++) {
        if ($("#" + listaProductos[i]).is(':checked')) {
            productoSeleccionado = true;
            break;
        }
    }
    if (productoSeleccionado) {
        $.post(url, {
                productos: listaProductos,
                promociones: promociones
            },
            function (data) {
                switch (data) {
                case '1':
                    mostrar_notificacion('Éxito', 'Productos asignados satisfactoriamente', 'success');
                    $("#form-asignar-promocion").removeClass('md-show');
                    listaProductos = [];
                    cargarProductos();
                    break;
                case '2':
                    mostrar_notificacion('Atención', 'Productos ya asignados a estos tags', 'warning');
                    break;
                default:
                    mostrar_notificacion('Error', 'Ha ocurrido un error. Por favor, contacte a un administrador', 'danger');
                    break;
                }
            }
        ).done(function () {
            for (var i = 0; i < listaProductos.length; i++) {
                $("#" + listaProductos[i]).prop('checked', false);
            }
        });
    } else {
        mostrar_notificacion('Atención', 'Primero debe seleccionar los productos a los que desea asignar tags', 'warning');
    }
}

function agregarPromocion() {
    var url = 'classes/obtener_tipo_promociones.php'
    var tipos = '<option value="0"></option>';
    $.post(url,
        function (data) {
            data = $.parseJSON(data);
            $.each(data, function (i, datos) {
                tipos += '<option value="' + datos.id + '">' + datos.nombre + '</option>';
            });
        }
    ).done(function () {
        $('#tipo_promocion').html(tipos);
    });
    $(".modal-header").html("<h3>Nueva Promoción</h3>");
    $("#form-agregar-promocion-pag-1").show();
    $("#form-agregar-promocion-pag-2").hide();
    $(".formulario").each(function () {
        this.reset();
    });
    $("#descuento").parent().parent().hide();
    $("#precio").parent().parent().hide();
};

function mostrarModalAgregarPromocionEspecial() {
    $('#wrap_porcentaje').hide();
    $(".formulario").find(':input').each(function () {
        var elemento = this;
        if ($("#" + elemento.id).parent().hasClass('has-error')) {
            $("#" + elemento.id).parent().removeClass('has-error');
        }
    });
    $('#select_tipo_promocion_especial').select2();
    var url = 'classes/administrar_promociones_especiales.php',
        tipos = '<option value="0"></option>';
    $.post(url, {
            accion: 2
        },
        function (data) {
            data = $.parseJSON(data);
            $.each(data.tipos, function (i, datos) {
                tipos += '<option value="' + datos.value + '">' + datos.nombre + '</option>';
            });
        }
    ).done(function () {
        $('#select_tipo_promocion_especial').html(tipos);
    });

};

$("#tipo_promocion").change(function () {
    if ($("#tipo_promocion").val() == '1') {
        $("#descuento").parent().parent().hide();
        $("#precio").addClass('required');
        $("#precio").addClass('numeric');
        $("#descuento").removeClass('required');
        $("#descuento").removeClass('numeric');
        $("#precio").parent().parent().show();
    } else {
        $("#precio").parent().parent().hide();
        $("#descuento").addClass('required');
        $("#descuento").addClass('numeric');
        $("#precio").removeClass('required');
        $("#precio").removeClass('numeric');
        $("#descuento").parent().parent().show();
    }
});

function editarPromocion(id, cantidad, descripcion, tipo_promocion, precio, descuento, f_inicio, f_termino, stock) {
    $("#form-agregar-promocion-pag-1").show();
    $("#form-agregar-promocion-pag-2").hide();
    $(".modal-header").html("<h3>Editar Promoción</h3>");
    $(".formulario").find(':input').each(function () {
        var elemento = this;
        if ($("#" + elemento.id).parent().hasClass('has-error')) {
            $("#" + elemento.id).parent().removeClass('has-error');
        }
    });
    idActual = id;
    $("#descripcion").val(descripcion);
    $("#cantidad").val(cantidad);
    $("#tipo_promocion option[value=" + tipo_promocion + "]").attr("selected", true);
    $("#tipo_promocion").val(tipo_promocion);
    $("#precio").val(currencyFormat(precio, ''));
    $("#descuento").val(descuento);
    $("#f_inicio").val(f_inicio);
    $("#f_termino").val(f_termino);
    $(".stock").val(stock);
}

function editarPromocionEspecial(id, tipo, descripcion, porcentaje) {
    var url = "classes/administrar_promociones_especiales.php",
        content = '<option value="0"></option>';
    $.post(url, {
            accion: 2
        },
        function (data) {
            data = $.parseJSON(data);
            $.each(data.tipos, function (i, datos) {
                content += '<option value="' + datos.value + '">' + datos.nombre + '</option>';
            });
        }
    ).done(function () {
        $('#select_tipo_promocion_especial').html(content);
        $("#descripcion_especial").val(descripcion);
        $("#select_tipo_promocion_especial option[value=" + tipo + "]").attr("selected", true);
        if (tipo === '1') {
            $('#wrap_porcentaje').show();
        } else {
            $('#wrap_porcentaje').hide();
        }
        $("#porcentaje_especial").val(porcentaje);
    });
    $(".modal-header").html("<h3>Editar Promoción</h3>");
    $(".formulario").find(':input').each(function () {
        var elemento = this;
        if ($("#" + elemento.id).parent().hasClass('has-error')) {
            $("#" + elemento.id).parent().removeClass('has-error');
        }
    });
    idActual = id;
}

function activarDesactivarPromocion(indice) {
    var url = '';
    if (promocionesActuales === 'normales') {
        url = 'classes/activar_desactivar_promocion.php';
    } else {
        url = 'classes/administrar_promociones_especiales.php';
    }
    var check = '<input type="checkbox" id="' + indice + 'checkbox" class="control-promocion"';
    if (arrayEstados[indice] == 0)
        check += ' checked ';
    check += 'disabled data-toggle="toggle">';
    $("#" + indice + 'div').html(check);
    $('.control-promocion').bootstrapToggle();
    $.post(url, {
            accion: 6,
            pId: indice,
            estado: arrayEstados[indice]
        },
        function (data) {
            if (promocionesActuales === 'especiales') {
                data = $.parseJSON(data);
                data = data.promocion[0];
                data = data.resultado;
            }
            if (data == 1) {
                if (arrayEstados[indice] == 0) {
                    arrayEstados[indice] = 1;
                } else {
                    arrayEstados[indice] = 0;
                }
            }
        }
    ).done(function () {
        $('#form-procesar-control').removeClass('md-show');
    });
    check = '<input type="checkbox" id="' + indice + ' checkbox" class="control-promocion"';
    if (arrayEstados[indice] == 0)
        check += ' checked ';
    check += 'data-toggle="toggle">';
    $("#" + indice + 'div').html(check);
    $('.control-promocion').bootstrapToggle();
}

$("#radio_fecha").click(function () {
    if (ultimoRadioCliqueado != 1) {
        if ($("#btn_f_inicio").hasClass('disabled')) {
            $("#btn_f_inicio").removeClass('disabled');
            $("#btn_f_termino").removeClass('disabled');
        }
        if (!$(".stock").prop('disabled')) {
            $(".stock").prop('disabled', true);
        }
        if (!$("#btn_f_inicio").hasClass('required')) {
            $("#btn_f_inicio").addClass('required');
            $("#btn_f_termino").addClass('required');
            $("#f_inicio").prop('disabled', false);
            $("#f_termino").prop('disabled', false);
        }
        if ($(".stock").hasClass('required'))
            $(".stock").removeClass('required');
    }
    ultimoRadioCliqueado = 1;
});

$("#radio_stock").click(function () {
    if (ultimoRadioCliqueado != 2) {
        if ($(".stock").prop('disabled')) {
            $(".stock").prop('disabled', false);
        }
        if (!$("#btn_f_inicio").hasClass('disabled')) {
            $("#btn_f_inicio").addClass('disabled');
            $("#btn_f_termino").addClass('disabled');
            $("#f_inicio").prop('disabled', true);
            $("#f_termino").prop('disabled', true);
        }
        if (!$(".stock").hasClass('required'))
            $(".stock").addClass('required');
        if ($("#f_inicio").hasClass('required')) {
            $("#f_inicio").removeClass('required');
            $("#f_termino").removeClass('required');
        }
    }
    ultimoRadioCliqueado = 2;
});

$("#radio_mixta").click(function () {
    if (ultimoRadioCliqueado != 3) {
        if ($(".stock").prop('disabled')) {
            $(".stock").prop('disabled', false);
        }
        if ($("#f_inicio").prop('disabled')) {
            $("#f_inicio").prop('disabled', false);
            $("#f_termino").prop('disabled', false);
        }
        if ($("#btn_f_inicio").hasClass('disabled')) {
            $("#btn_f_inicio").removeClass('disabled');
            $("#btn_f_termino").removeClass('disabled');
        }
        if (!$(".stock").hasClass('required'))
            $(".stock").addClass('required');
        if (!$("#btn_f_inicio").hasClass('required')) {
            $("#btn_f_inicio").addClass('required');
            $("#btn_f_termino").addClass('required');
        }
    }
    ultimoRadioCliqueado = 3;
});

$("#radio_sucursal").click(function () {
    alcanceSucursal = 1;
});

$("#radio_global").click(function () {
    alcanceSucursal = 2;
});

function avanzarRetrocederModal(pagina, seguir) {
    $("#form-agregar-promocion-pag-" + pagina).toggle();
    if (seguir) {
        pagina++;
        paginaActualForm++;
    } else {
        pagina--;
        paginaActualForm--;
    }
    $("#form-agregar-promocion-pag-" + pagina).toggle();
}

$("#btn_aceptar_modal").click(function () {
    habilitarDeshabilitarBoton('btn_aceptar_modal', false);
    $(".formulario").find(':input').each(function () {
        var elemento = this;
        if ($("#" + elemento.id).parent().hasClass('has-error')) {
            $("#" + elemento.id).parent().removeClass('has-error');
        }
    });
    var datos = new Object();
    datos['id'] = idActual;
    datos['descripcion'] = $("#descripcion").val();
    datos['cantidad'] = $("#cantidad").val();
    datos['tipo_promocion'] = $("#tipo_promocion").val();
    datos['precio'] = sanear_numero($("#precio").val());
    datos['descuento'] = $("#descuento").val();
    datos['f_inicio'] = $("#f_inicio").val();
    datos['f_termino'] = $("#f_termino").val();
    datos['stock'] = $("#stock").val();
    datos['tipo'] = ultimoRadioCliqueado;
    datos['alcance'] = alcanceSucursal;
    var focusTaken = false;
    if (datos['f_termino'] < datos['f_inicio']) {
        $('#f_inicio').parent().addClass('has-error');
        $('#f_termino').parent().addClass('has-error');
        focusTaken = true;
        $('#f_inicio').focus();
        mostrar_notificacion('Error', 'La fecha de término no puede ser menor a la fecha de inicio', 'danger');
    }
    for (valor in datos) {
        if (datos[valor] == null || datos[valor] == '') {
            if ($("#" + valor).hasClass('required')) {
                $("#" + valor).parent().addClass('has-error');
                if (!focusTaken) {
                    $("#" + valor).focus();
                    focusTaken = true;
                    mostrar_notificacion('Atención', 'Rellene todos los campos marcados', 'warning');
                }
            }
        } else {
            if ($("#" + valor).hasClass('numeric') && $.isNumeric(datos[valor]) == false) {
                $("#" + valor).parent().addClass('has-error');
                focusTaken = true;
                mostrar_notificacion('Atención', 'El campo debe contener un valor numérico', 'warning');
            } else {
                if ($("#" + valor).parent().hasClass('has-error')) {
                    $("#" + valor).parent().removeClass('has-error');
                }
                if ($("#" + valor).hasClass('numeric') && $.isNumeric(datos[valor])) {
                    $("#" + valor).parent().removeClass('has-error');
                }
            }
        }
        if ($("#" + valor).parent().hasClass('has-error') && !$("#" + valor).hasClass('required')) {
            $("#" + valor).parent().removeClass('has-error');
        }
    }
    if (!focusTaken) {
        var formData = new FormData($(".formulario")[0]);
        var url = "classes/nueva_promocion.php";
        $.post(url, {
                id: datos['id'],
                descripcion: datos['descripcion'],
                cantidad: datos['cantidad'],
                tipo_promocion: datos['tipo_promocion'],
                descuento: datos['descuento'],
                precio: datos['precio'],
                f_inicio: datos['f_inicio'],
                f_termino: datos['f_termino'],
                stock: datos['stock'],
                tipo: datos['tipo'],
                alcance: datos['alcance']
            },
            function (data) {
                console.log(data);
                cargarPromociones();
                habilitarDeshabilitarBoton('btn_aceptar_modal', true);
                $("form-agregar-promocion-pag-1").show();
                $("form-agregar-promocion-pag-2").hide();
                $('#form-agregar-promocion').removeClass('md-show');
                $(".formulario").each(function () {
                    this.reset();
                });
                switch (data) {
                    default: mostrar_notificacion('Error', 'No se ha podido procesar su solicitud', 'danger');
                    break;
                case '1':
                        mostrar_notificacion('Éxito', 'Solicitud procesada satisfactoriamente', 'success');
                    break;
                case '3':
                        mostrar_notificacion('Atención', 'No se ha modificado ningún campo', 'warning');
                    break;
                }
            }
        ).done(function () {

        });
    } else {
        habilitarDeshabilitarBoton('btn_aceptar_modal', true);
    }
});

$('#btn_aceptar_modal_promocion_especial').on('click', function () {

    habilitarDeshabilitarBoton('btn_aceptar_modal_promocion_especial', false);
    var datos = new Object();
    datos['id'] = idActual;
    datos['select_tipo_promocion_especial'] = $("#select_tipo_promocion_especial").val();
    datos['descripcion_especial'] = $("#descripcion_especial").val();
    datos['porcentaje_especial'] = $("#porcentaje_especial").val();

    var focusTaken = false;
    for (valor in datos) {
        if (datos[valor] == null || datos[valor] == '') {
            if ($("#" + valor).hasClass('required')) {
                $("#" + valor).parent().addClass('has-error');
                if (!focusTaken) {
                    $("#" + valor).focus();
                    focusTaken = true;
                    mostrar_notificacion('Atención', 'Rellene todos los campos marcados', 'warning');
                }
            }
        } else {
            if ($("#" + valor).hasClass('numeric') && $.isNumeric(datos[valor]) == false) {

                $("#" + valor).parent().addClass('has-error');
                focusTaken = true;
                mostrar_notificacion('Atención', 'El campo debe contener un valor numérico', 'warning');
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
        var accion = 3;
        if (idActual !== 0) {
            accion = 4;
        }
        var url = "classes/administrar_promociones_especiales.php";
        $.post(url, {
                accion: accion,
                parametros: datos
            },
            function (data) {
                habilitarDeshabilitarBoton('btn_aceptar_modal_promocion_especial', true);
                data = $.parseJSON(data);
                data = data.promocion[0];
                switch (data.resultado) {
                    case 0:
                        mostrar_notificacion('Error', 'No se ha podido procesar su solicitud', 'danger');
                        break;
                    case 1:
                        mostrar_notificacion('Éxito', 'Solicitud procesada satisfactoriamente', 'success');
                        cargarPromocionesEspeciales();
                        $('#form-agregar-promocion_especial').removeClass('md-show');
                        $(".formulario").each(function () {
                            this.reset();
                        });
                        break;
                    case 3:
                        mostrar_notificacion('Atención', 'No se ha modificado ningún campo', 'warning');
                        break;
                    default:
                        mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte a un administrador', 'danger');
                        break;

                }
            }
        ).done(function () {

        });
    } else {
        habilitarDeshabilitarBoton('btn_aceptar_modal_promocion_especial', true);
    }
});

function eliminarPromocion(pId) {
    alertify.confirm('Atención', "¿Seguro quieres eliminar esta promoción? Esta acción no puede deshacerse.",
        function () {
            var url = "classes/eliminar_promocion.php";
            $.post(url, {
                    pId: pId
                },
                function (data) {
                    if (data > 0) {
                        mostrar_notificacion('Éxito', 'Solicitud procesada satisfactoriamente', 'success');
                    } else {
                        mostrar_notificacion('Error', 'No se ha podido procesar su solicitud', 'danger');
                    }
                }
            ).done(function () {
                cargarPromociones();
            });
        },
        function () {}
    ).set('labels', {
        ok: 'Sí',
        cancel: 'Cancelar'
    });
}

function eliminarPromocionEspecial(pId) {
    alertify.confirm('Atención', "¿Seguro quieres eliminar esta promoción? Esta acción no puede deshacerse.",
        function () {
            var url = "classes/administrar_promociones_especiales.php";
            $.post(url, {
                    accion: 5,
                    pId: pId
                },
                function (data) {
                    data = $.parseJSON(data);
                    data = data.promocion[0];
                    if (data.resultado > 0) {
                        mostrar_notificacion('Éxito', 'Solicitud procesada satisfactoriamente', 'success');
                    } else {
                        mostrar_notificacion('Error', 'No se ha podido procesar su solicitud', 'danger');
                    }
                }
            ).done(function () {
                cargarPromocionesEspeciales();
            });
        },
        function () {}
    ).set('labels', {
        ok: 'Sí',
        cancel: 'Cancelar'
    });
}

$("#contenido_promociones").click(function () {
    $('.control-promocion').bootstrapToggle();
    $('.fa-pencil-square').modalEffects();
    $('#btn_asignar_promo').modalEffects();
});

$('#select_tipo_promocion_especial').on('change', function () {
    if ($(this).val() === '1') {
        $('#wrap_porcentaje').show();
        $('#porcentaje_especial').addClass('required');
    } else {
        $('#wrap_porcentaje').hide();
        $('#porcentaje_especial').removeClass('required');
    }
});