var tId = 0;
var listaProductos = [];
var tableProductos = '';

$(document).ready(function () {
    $('.agregar_tag').modalEffects();
    cargarListaTags();
});

function cargarListaTags() {

    $('#listaDeTags').html('<div id="loading"><i class="fa fa-spinner fa-spin fa-4x"></i></div>');
    var treeTags = '<ul class="nav nav-list treeview collapse">';
    var ul = false;
    $.post("classes/obtener_tags.php", {
            eId: "1"
        },
        function (data) {
            data = $.parseJSON(data);
            $.each(data, function (i, datos) {
                treeTags += '<li><label class="tree-toggler nav-header" onClick="cargarProductos(\'' + datos.codigo + '\', 0);" id="' + datos.desc + '"><i class="fa fa-tags"></i>' + datos.nombre + '</label></li>';
            });
        }
    ).done(function () {
        treeTags += '</ul>';
        $('#loading').hide();
        $('#listaDeTags').html(treeTags);
        /*if ($("#listadoTags").prop('scrollHeight') > $("#listadoTags").height() ) {
            $('#flecha_abajo').show();
            
        }*/
    });
}

function cargarTags() {
    $("#pcont").html('<div id="loading"><i class="fa fa-spinner fa-spin fa-3x"></i></div>');
    var url = 'classes/obtener_tags.php';
    var table = '<table id="tabla_tags" class="table table-bordered"><thead><tr><th>#</th><th>Nombre</th><th>Código</th><th>Edición</th><th>Eliminar</th></tr></thead><tbody>';
    $.post(url, {},
        function (data) {
            data = $.parseJSON(data);
            var cont = 1;
            $.each(data, function (i, datos) {
                table += '<tr><td>' + cont + '</td><td>' + datos.nombre + '</td><td>' + datos.codigo + '</td>';
                table += '<td><a href="#"><i class="fa fa-pencil-square fa-2x editar_tag" data-modal="form-agregar-tag" onClick="editarProducto(' + datos.id + ', \'' + datos.nombre + '\', \'' + datos.codigo + '\');"></i></a></td><td><a href="#" style="color:red;"><i class="fa fa-times-circle fa-2x" onClick="eliminarTag(' + datos.id + ');"></i></a></td></tr>';
                cont++;
            });
        }
    ).done(function () {
        table += '</tbody></table>';
        $("#pcont").html(table);
        $("#tabla_tags").dataTable({
            "order": [0, 'asc'],
            "oLanguage": {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ productos",
                "sZeroRecords": "No se han encontrado productos disponibles",
                "sEmptyTable": "No se han encontrado productos disponibles",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
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
        $('.editar_tag').modalEffects();
    });
}

function cargarProductos(option, action) {
    //$("#input_tags").val(option + ', ');
    var parametros = [];
    if (action == 4) {
        var url = 'classes/obtener_productos.php';
        parametros = ['id', 'codigo', 'nombre', 'marca_nombre', 'modelo', 'descripcion'];
    } else {
        var url = 'classes/obtener_productos_tag.php';
    }
    $.post(url, {
            tCodigo: option,
            parametros: parametros,
            action: action
        },
        function (data) {
            if (action == 4)
                cargarTodos(data);
            else
                cargarProductosTags(action, data);
        }
    ).done(function () {
        tableProductos += '</tbody></table>';

        $("#pcont").html(tableProductos);
        if (action != 0 && action != 4) {
            $("#tabla_productos").dataTable({
                "order": [5, 'desc'],
                "oLanguage": {
                    "sProcessing": "Procesando...",
                    "sLengthMenu": "Mostrar _MENU_ productos",
                    "sZeroRecords": "No se han encontrado productos disponibles",
                    "sEmptyTable": "No se han encontrado productos disponibles",
                    "sInfo": '<button class="btn btn-default pull-right" id="btn_asignar" onClick="prepararLista();" data-modal="form-asignar-tag"><i class="fa fa-caret-up"></i> Asignar a un Tag</button>',
                    "sInfoEmpty": "Mostrando productos del 0 al 0 de un total de 0 productos",
                    "sInfoFiltered": "",
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
        } else {
            $("#tabla_productos").dataTable({
                "oLanguage": {
                    "sProcessing": "Procesando...",
                    "sLengthMenu": "Mostrar _MENU_ productos",
                    "sZeroRecords": "No se han encontrado productos disponibles",
                    "sEmptyTable": "No se han encontrado productos disponibles",
                    "sInfo": '<button class="btn btn-default pull-right" id="btn_asignar" onClick="prepararLista();" data-modal="form-asignar-tag"><i class="fa fa-caret-up"></i> Asignar a un Tag</button>',
                    "sInfoEmpty": "Mostrando productos del 0 al 0 de un total de 0 productos",
                    "sInfoFiltered": "",
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
        $("#btn_asignar").modalEffects();
    });
}

function cargarProductosTags(action, data) {
    tableProductos = '<table id="tabla_productos" class="table table-bordered"><thead><tr><th>Seleccionar</th><th>Código</th><th>Nombre</th><th>Marca</th><th>Descripción </th>';
    switch (action) {
    case 1:
    case 2:
    case 3:
        tableProductos += '<th>U. vendidas</th>';
        break;
    }
    tableProductos += '</tr></thead><tbody>';
    data = $.parseJSON(data);
    $.each(data, function (i, datos) {
        tableProductos += '<tr><td><input type="checkbox" name="option1" value="' + datos.codigo + '" id="' + datos.codigo + '" onChange="listarProductos(\'' + datos.codigo + '\');"></td><td>' + datos.codigo + '</td>';
        tableProductos += '<td>' + datos.nombre + '</td><td>' + datos.marca + '</td><td>' + datos.descripcion + '</td>';
        var action_val = datos.action;
        if (action_val != 0) {
            tableProductos += '<td>' + currencyFormat(action_val['action'], '') + '</td>';
        }
        tableProductos += '</tr>';
    });
}

function cargarTodos(data) {

    tableProductos = '<table id="tabla_productos" class="table table-bordered"><thead><tr><th>Seleccionar</th><th>Código</th><th>Nombre</th><th>Marca</th><th>Descripción </th>';
    tableProductos += '</tr></thead><tbody>';
    data = $.parseJSON(data);
    $.each(data.productos, function (i, datos) {
        tableProductos += '<tr><td><input type="checkbox" name="option1" value="' + datos.codigo + '" id="' + datos.codigo + '" onChange="listarProductos(\'' + datos.codigo + '\');"></td><td>' + datos.codigo + '</td>';
        tableProductos += '<td>' + datos.nombre + '</td><td>' + datos.marca_nombre + '</td><td>' + datos.descripcion + '</td>';
        tableProductos += '</tr>';
    });
}


$(".agregar_tag").click(function () {
    $("#nombre").val('');
    $("#codigo").val('');
    if ($("#nombre").parent().hasClass('has-error'))
        $("#nombre").parent().removeClass('has-error');
    if ($("#codigo").parent().hasClass('has-error'))
        $("#codigo").parent().removeClass('has-error');
    $("#nombre").keyup(function () {
        completarCodigo();
    });
});

function completarCodigo() {
    console.log('asd');

    $("#codigo").val('');
    //var codigo_val = $("#nombre").val().charAt($("#nombre").val().length -1 );
    var codigo_val = $("#nombre").val();
    codigo_val = codigo_val.replace(' ', '_');
    $("#codigo").val($("#codigo").val() + codigo_val);
    $("#codigo").val($("#codigo").val().toUpperCase());
};

function listarProductos(codigo) {
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

function editarProducto(id, nombre, codigo) {
    if ($("#nombre").parent().hasClass('has-error'))
        $("#nombre").parent().removeClass('has-error');
    if ($("#codigo").parent().hasClass('has-error'))
        $("#codigo").parent().removeClass('has-error');
    $(".modal-title").html("<h3>Editar Tag</h3>");
    tId = id;
    $("#nombre").val(nombre);
    $("#codigo").val(codigo);
}

$(".agregar_tag").click(function () {
    $(".modal-title").html("<h3>Agregar nuevo Tag</h3>");
});

function guardarModal() {
    habilitarDeshabilitarBoton('btn_aceptar_modal', false);
    var nombre = $("#nombre").val();
    var codigo = $("#codigo").val();
    if (nombre != '' && codigo != '') {
        if ($("#nombre").parent().hasClass('has-error'))
            $("#nombre").parent().removeClass('has-error');
        if ($("#codigo").parent().hasClass('has-error'))
            $("#codigo").parent().removeClass('has-error');
        var url = "classes/administrar_tags.php";
        $.post(url, {
                tId: tId,
                nombre: nombre,
                codigo: codigo,
            },
            function (data) {
                habilitarDeshabilitarBoton('btn_aceptar_modal', true);
                switch (data) {
                case '0':
                    mostrar_notificacion('Error', 'No se ha podido procesar su solicitud', 'danger');
                    break;
                case '1':
                    mostrar_notificacion('Éxito', 'Su solicitud se ha procesado satisfactoriamente', 'success');
                    break;
                case '2':
                    mostrar_notificacion('Atención', 'No se ha editado ningún dato', 'warning');
                    break;
                default:
                    mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte a un administrador', 'danger');
                    break;
                }
            }
        ).done(function () {
            $("#nombre").val('');
            $("#codigo").val('');
            $("#form-agregar-tag").removeClass('md-show');
            cargarTags();
            cargarListaTags();
        });
    } else {
        habilitarDeshabilitarBoton('btn_aceptar_modal', true);
        habilitarDeshabilitarBoton('btn_aceptar_modal_editar', true);
        if (nombre == '') {
            $("#nombre").parent().addClass('has-error');
        }
        if (codigo == '') {
            $("#codigo").parent().addClass('has-error');
        }
    }
}

function prepararLista() {
    
    if (listaProductos.length == 0) {
        $('#form-asignar-tag').hide();
        $('.md-overlay').hide();
        mostrar_notificacion('Atención', 'Debe seleccionar al menos un producto', 'warning');
        $(':checkbox').addClass('shake');
        setTimeout(function(){ 
            $(':checkbox').removeClass('shake');
            $('#form-asignar-tag').removeClass('md-show');
            $('#form-asignar-tag').show();
            $('.md-overlay').show();
        }, 500);
        
    } else {
        //$('#form-asignar-tag').addClass('md-show');
        var url = 'classes/obtener_tags.php';
        var tags = '<option value=""></option>';
        $.post(url, {},
            function (data) {
                data = $.parseJSON(data);
                $.each(data, function (i, datos) {
                    tags += '<option value="' + datos.codigo + '">' + datos.nombre + '</option>';
                });
            }).done(function () {
            $('#select_tags').html(tags);
            $('#select_tags').select2({
                tags: true,
                tokenSeparators: [',', ' ']
            });
            //$('#select_tags').val('0').trigger("change");
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
    $("#lista-body").html(productosAsignar);
}

function guardarModalAsignar() {
    var url = 'classes/asignar_tags.php';
    var tags = $("#select_tags").val();
    if (tags === null) {
        console.log("asd");
        mostrar_notificacion('Atención', 'Debe elegir al menos un tag', 'warning');
        return false;
    }
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
                tags: tags
            },
            function (data) {
                switch (data) {
                case '1':
                    mostrar_notificacion('Éxito', 'Productos asignados satisfactoriamente', 'success');
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
            listaProductos = [];
            $("#form-asignar-tag").removeClass('md-show');
            cargarListaTags();
        });
    } else {
        mostrar_notificacion('Atención', 'Primero debe seleccionar los productos a los que desea asignar tags', 'warning');
    }
}

function eliminarTag(tId) {
    alertify.confirm('Atención', "¿Seguro quieres eliminar esta promoción? Esta acción no puede deshacerse.",
        function () {
            var url = "classes/eliminar_tag.php";
            $.post(url, {
                    tId: tId
                },
                function (data) {
                    if (data > 0) {
                        mostrar_notificacion('Éxito', 'Solicitud procesada satisfactoriamente', 'success');
                    } else {
                        mostrar_notificacion('Error', 'No se ha podido procesar su solicitud', 'danger');
                    }
                }
            ).done(function () {
                cargarTags();
                cargarListaTags();
            });
        },
        function () {}
    ).set('labels', {
        ok: 'Sí',
        cancel: 'Cancelar'
    });
}

function trendingTags(option) {
    var id = '';
    switch (option) {
    case 1:
        id = '#mas_vendidos';
        break;
    case 2:
        id = '#mas_usados';
        break;
    case 3:
        id = '#populares';
        break;
    }
    $(id + "_list").html('<div id="loading"><i class="fa fa-spinner fa-spin"></i></div>');

    var url = 'classes/obtener_trending_tags.php';
    var list = '';
    $.post(url, {
            option: option
        },
        function (data) {
            if (data != '[]') {
                data = $.parseJSON(data);
                $.each(data, function (i, datos) {
                    var action = currencyFormat(datos.action, '');
                    list += '<li><label class="tree-toggler nav-header" onClick="cargarProductos(\'' + datos.codigo + '\', ' + option + ')";><i class="fa fa-tags"></i>' + datos.nombre + '<div class="badge badge-primary pull-right" style="border-radius: 50px">' + action + '</div></label></li>';
                });
            }
        }
    ).done(function () {
        $(id + '_list').html(list);
        $(id).attr("onClick", "esconderTrendingTags('" + id + "', " + option + ")");
    });
}

function esconderTrendingTags(id, option) {
    $(id + '_list').html('');
    $(id).attr("onClick", "trendingTags(" + option + ")");
}

$("#pcont").click(function () {
    $('.fa-pencil-square').modalEffects();
    $('.asignar_promocion').modalEffects();
    $('#btn_asignar').modalEffects();
});