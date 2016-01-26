var paginaActualForm = 1,
    listaMateriaPrima = [],
    listaProductosAsignados = {},
    cantidadProductos = 1,
    selectProductos = '';

$(document).ready(function () {
    cargarMateriaPrima();
});

$('#pcon').click(function () {
    $('.info_asignados').modalEffects();
    $('#btn_asignar_materia').modalEffects();
    $('.editar_marca').modalEffects();
    $('#btn_agregar').modalEffects();
    $('.editar_stock').modalEffects();
    $('.agregar_stock').modalEffects();
    
});

$('#codigo').keyup(function (e) {
    if (e.keyCode == 13) {
        $('#nombre').focus();
    }
});

$(document).on('click','#btn_asignar_materia', mostrarModalAsignar);

function cargarMateriaPrima() {
    $("#pcont").html('<div id="loading"><i class="fa fa-spinner fa-2x fa-spin"></i></div>');
    var url = 'classes/obtener_materia_prima.php';
    var botones = '<ul class="nav nav-list treeview collapse"><li><label class="tree-toggler nav-header" id="btn_agregar" onClick="setMarca();" data-modal="form-agregar-materia_prima"><i class="fa fa-plus-circle"></i> Agregar Materia Prima</label></li>';
    botones += '</ul>';
    $('.tree-body').html(botones);
    var tablaMateria = '<div class="table-responsive"><table id="tabla_materia" class="table table-bordered responsive"><thead><tr><th>Asignar</th><th>Código</th><th>Nombre</th><th>Descripción</th><th>Unidad</th><th>Unidad Medida</th><th>Productos Asig</th><th>Stock</th><th>Stock Mínimo</th><th>Edición</th><th>Stock Mín.</th><th>Agregar Stock</th></tr></thead><tbody>'
    var parametros = ['codigo', 'nombre', 'descripcion', 'unidad', 'u_medida', 'stock_r', 'stock_m', 'productos'];
    $.post(url, {
            parametros: parametros
        },
        function (data) {

            data = $.parseJSON(data);
            listaProductosAsignados.materia = [];
            $.each(data.materia_prima, function (i, datos) {
                var obj = {};
                obj.codigo = datos.codigo;
                obj.nombre = datos.nombre;
                obj.productos = [];
                $.each(datos.productos, function (i, producto) {
                    var objP = {};
                    objP.codigo = producto.codigo;
                    objP.nombre = producto.nombre;
                    obj.productos.push(objP);
                });
                listaProductosAsignados.materia.push(obj);
                tablaMateria += '<td><input type="checkbox" name="option1" value="' + datos.codigo + '" id="' + datos.codigo + '" onChange="listarMateriaPrima(\'' + datos.codigo + '\');">';
                tablaMateria += '<td>' + datos.codigo + '</td><td>' + datos.nombre + '</td><td>' + datos.descripcion + '</td><td>' + datos.unidad + '</td><td>' + datos.u_medida;
                tablaMateria += '<td><a href="#" data-modal="form-lista-asignados" class="info_asignados" onClick="obtenerProductosAsignados(\'' + datos.codigo + '\');"><i class="fa fa-info-circle fa-2x"></i></a></td>';
                var stock_r = parseInt(datos.stock_r);
                var stock_m = parseInt(datos.stock_m);
                if (stock_r <= stock_m) {
                    tablaMateria += '</td><td style="color:red;">';
                } else if (stock_r <= (stock_m + stock_m / 4)) {
                    tablaMateria += '</td><td style="color:yellow;">';
                } else {
                    tablaMateria += '</td><td>';
                }
                tablaMateria += datos.stock_r + '</td><td>' + datos.stock_m + '</td>';
                tablaMateria += '<td><a href="#" onClick="setMarca(\'' + datos.codigo + '\', \'' + datos.nombre + '\', \'' + datos.descripcion + '\', \'' + datos.u_medida + '\');"><i class="fa fa-pencil-square fa-2x editar_marca" data-modal="form-agregar-materia_prima"></i></a></td>';
                tablaMateria += '<td><a href="#" onClick="setStock(\'' + datos.codigo + '\', \'' + datos.stock_m + '\');"><i class="fa fa-pencil fa-2x editar_stock" data-modal="form-editar-stock_minimo"></i></a></td>';
                tablaMateria += '<td><a href="#" onClick="setStockAgregar(\'' + datos.codigo + '\');"><i class="fa fa-plus-square fa-2x agregar_stock" data-modal="form-agregar-stock"></i></a></td></tr>';
            });
        }).done(function () {
        tablaMateria += '</tbody></table></div>';
        $('#pcont').html(tablaMateria);
        $('#tabla_materia').dataTable({
            "aaSorting": [[0, 'asc']],
            "oLanguage": {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ productos",
                "sZeroRecords": "No se han encontrado productos disponibles",
                "sEmptyTable": "No se han encontrado productos disponibles",
                "sInfo": '<button class="btn btn-default" data-modal="form-asignar-materia" id="btn_asignar_materia"><i class="fa fa-caret-up"></i> Asignar a Productos</button></li>',
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
        $('.info_asignados').modalEffects();
        $('#btn_asignar_materia').modalEffects();
        $('.editar_marca').modalEffects();
        $('#btn_agregar').modalEffects();
        $('.editar_stock').modalEffects();
        $('.agregar_stock').modalEffects();
    });
}

function obtenerProductosAsignados(codigo) {

    var listaAsignados = '';
    $.each(listaProductosAsignados.materia, function (i, datos) {

        if (datos.codigo == codigo) {
            listaAsignados += 'Materia Prima<li> Código: ' + datos.codigo + ' | Nombre: ' + datos.nombre + '</li><ul> Productos';
            $.each(datos.productos, function (i, producto) {
                listaAsignados += '<li> Código: ' + producto.codigo + ' | Nombre: ' + producto.nombre + '</li>';
            });
        }
        listaAsignados += '</ul>';
    });
    $('#listado_asignados').html(listaAsignados);
}

function setMarca(codigo, nombre, descripcion, u_medida) {
    if (!codigo) {
        limpiarFormulario();
        $('#select_u_medidas option[value=0]').attr("selected", true);
        $('#btn_aceptar_modal').off('click');
        $('#btn_aceptar_modal').on('click', function () {
            guardarModal(true);
        });
    } else {
        $('#codigo').val(codigo);
        $('#nombre').val(nombre);
        $('#descripcion').val(descripcion);
        $('#select_u_medidas option[value=' + u_medida + ']').attr("selected", true);
        $('#btn_aceptar_modal').off('click');
        $('#btn_aceptar_modal').on('click', function () {
            guardarModal(false);
        });
    }
    $('#form-agregar-materia_prima-pag-1').show();
    $('#form-agregar-materia_prima-pag-2').hide();

    $('#select_unidades').change(function () {
        changeUnidades($(this).attr('id'));
    });
}

function setStock(codigo, stock_m) {
    if (stock_m != '0')
        $('#stock_m').val(stock_m);
    $('#btn_aceptar_modal_editar_stock').off('click');
    $('#btn_aceptar_modal_editar_stock').on('click', function () {
        guardarModalEditarStock(codigo);
    });
}

$('#btn_cerrar_asignar').on('click', cancelarCerrarAsignarMateriaPrima);

function setStockAgregar(codigo) {
    limpiarFormulario();
    $('#btn_aceptar_agregar_stock').off('click');
    $('#btn_aceptar_agregar_stock').on('click', function () {
        guardarModalAgregarStock(codigo);
    });
}

function guardarModal(materiaPrimaNueva) {
    
    var datos = {},
        focusTaken = false;
    datos['codigo'] = $('#codigo').val();
    datos['nombre'] = $('#nombre').val();
    datos['descripcion'] = $('#descripcion').val();
    datos['select_unidades'] = $('#select_unidades').val();
    datos['unidad'] = $('#unidad').val();
    datos['u_medida'] = $('#select_u_medidas').val();
    for (valor in datos) {
        if (datos[valor] == null || datos[valor] == '' || (valor == 'select_unidades' && datos[valor] == '0')) {

            if ($("#" + valor).hasClass('required')) {

                $("#" + valor).parent().addClass('has-error');

                if (!focusTaken) {
                    $("#" + valor).focus();
                    focusTaken = true;
                }
            }
        } else {
            if ($("#" + valor).hasClass('numeric') && $.isNumeric(datos[valor]) == false) {
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
        var url = 'classes/nueva_materia_prima.php';
        if (materiaPrimaNueva)
            var accion = 1;
        else
            var accion = 2;
        $.post(url, {
                datos: datos,
                accion: accion
            },
            function (data) {
                switch (data) {
                case '1':
                    mostrar_notificacion('Éxito', 'Producto agregado exitosamente', 'success');
                    cargarMateriaPrima();
                    $('#form-agregar-materia_prima').removeClass('md-show');
                    break;
                case '2':
                    mostrar_notificacion('Éxito', 'Producto editado exitosamente', 'success');
                    cargarMateriaPrima();
                    $('#form-agregar-materia_prima').removeClass('md-show');
                    break;
                case '7':
                    mostrar_notificacion('Atención', 'No se pudo registrar su producto en algunas sucursales', 'warning');
                    cargarMateriaPrima();
                    $('#form-agregar-materia_prima').removeClass('md-show');
                    break;
                case '5':
                    mostrar_notificacion('Error', 'Código existente', 'danger');
                    break;
                case '6':
                    mostrar_notificacion('Error', 'No cuenta con los permisos suficientes para realizar esta acción', 'danger');
                    $('#form-agregar-materia_prima').removeClass('md-show');
                    break;
                case '0':
                    mostrar_notificacion('Error', 'No se pudo procesar su solicitud', 'danger');
                    break;
                default:
                    mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte a un administrador', 'danger');
                    break;
                }
            });
    } else {
        mostrar_notificacion('Atención', 'Ingrese los datos correctamente', 'warning');
        habilitarDeshabilitarBoton('btn_aceptar_modal', true);
        habilitarDeshabilitarBoton('btn_aceptar_modal_editar', true);
    }
};

function guardarModalEditarStock(codigo) {
    var stock_m = $('#stock_m').val();
    if (stock_m == '' || stock_m == null || !$.isNumeric(stock_m)) {
        mostrar_notificacion('Atención', 'Ingrese los datos correctamente', 'warning');
    } else {
        var datos = {};
        datos['codigo'] = codigo;
        datos['stock_m'] = stock_m;
        var url = 'classes/nueva_materia_prima.php';
        $.post(url, {
                datos: datos,
                accion: 4
            },
            function (data) {
                switch (data) {
                case '4':
                    mostrar_notificacion('Éxito', 'Stock mínimo editado exitosamente', 'success');
                    cargarMateriaPrima();
                    $('#form-editar-stock_minimo').removeClass('md-show');
                    break;
                case '6':
                    mostrar_notificacion('Error', 'No cuenta con los permisos suficientes para realizar esta acción', 'danger');
                    $('#form-editar-stock_minimo').removeClass('md-show');
                    break;
                case '0':
                    mostrar_notificacion('Error', 'No se pudo procesar su solicitud', 'danger');
                    break;
                default:
                    mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte a un administrador', 'danger');
                    break;
                }
            });

    }
};

function guardarModalAgregarStock(codigo) {
    
    var stock_r = $('#stock_r').val();
    if (stock_r == '' || stock_r == null || !$.isNumeric(stock_r)) {
        mostrar_notificacion('Atención', 'Ingrese los datos correctamente', 'warning');
    } else {
        var datos = {};
        datos['codigo'] = codigo;
        datos['stock_r'] = stock_r;
        var url = 'classes/nueva_materia_prima.php';
        $.post(url, {
                datos: datos,
                accion: 3
            },
            function (data) {
                switch (data) {
                case '3':
                    mostrar_notificacion('Éxito', 'Stock mínimo editado exitosamente', 'success');
                    cargarMateriaPrima();
                    $('#form-agregar-stock').removeClass('md-show');
                    break;
                case '1':
                    mostrar_notificacion('Atención', 'No se pudo registrar el flujo de stock, pero el stock se cambió', 'warning');
                    cargarMateriaPrima();
                    $('#form-agregar-stock').removeClass('md-show');
                    break;
                case '6':
                    mostrar_notificacion('Error', 'No cuenta con los permisos suficientes para realizar esta acción', 'danger');
                    $('#form-agregar-stock').removeClass('md-show');
                    break;
                case '0':
                    mostrar_notificacion('Error', 'No se pudo procesar su solicitud', 'danger');
                    break;
                default:
                    mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte a un administrador', 'danger');
                    break;
                }
            });

    }
};


function listarMateriaPrima(codigo) {
    if ($("#" + codigo).is(":checked")) {
        listaMateriaPrima[listaMateriaPrima.length] = codigo;
    } else {
        for (var i = 0; i < listaMateriaPrima.length; i++) {
            if (listaMateriaPrima[i] == codigo) {
                listaMateriaPrima.splice(i, 1);
            }
        }
    }
};

function limpiarLista() {
    $(':checkbox').each(function () {
        $(this.id).prop("checked", false);
    });
}

function limpiarFormulario() {
    $(".formulario").each(function () {
        this.reset();
    });
}

function changeUnidades(id) {
    if ($('#' + id).val() == 'otro') {
        $('#unidad').addClass('required');
        $('#group-unidad').show('slow');
    } else {
        $('#unidad').removeClass('required');
        $('#group-unidad').hide('slow');
    }
};

$('#agregar_producto').on('click', function () {
    agregarProducto();
});

function agregarProducto() {
    cantidadProductos++;
    var item = '<div class="form-group" id="item' + cantidadProductos + '"><label>Producto ' + cantidadProductos + '</label> <div class="row"> <div class="col-md-6"> <select id="select_producto' + cantidadProductos + '" name="select_producto' + cantidadProductos + '" class="form-control pag-1 required"> <option value="0"></option> </select> </div> <div class="col-md-6"> <input type="text" id="unidad_producto' + cantidadProductos + '" style="height:28px;" class="form-control" placeholder="Unidades requeridas"> </div> </div></div>';
    $('#lista_productos').append(item);
    $("#select_producto" + cantidadProductos).html(selectProductos);
    $("#select_producto" + cantidadProductos).select2();
    $('#eliminar_producto').show();
}

$('#eliminar_producto').on('click', function () {
    eliminarProducto();
});

function eliminarProducto() {
    if (cantidadProductos > 1) {
        $('#item' + cantidadProductos).remove();
        cantidadProductos--;
    }
    if (cantidadProductos <= 2) {
        $('#eliminar_producto').hide();
    }
}

function mostrarModalAsignar() {
    
    if (listaMateriaPrima.length == 0) {
        $('#form-asignar-materia').hide();
        $('.md-overlay').hide();
        mostrar_notificacion('Atención', 'Debe seleccionar al menos una materia prima', 'warning');
        $(':checkbox').addClass('shake');
        setTimeout(function(){ 
            $(':checkbox').removeClass('shake');
            $('.md-overlay').show();
            $('#form-asignar-materia').removeClass('md-show');
            $('#form-asignar-materia').show();
            $('.md-overlay').show();
        }, 500);
    }else{
        $('#form-asignar-materia').addClass('md-show');
        $('#eliminar_producto').hide();
        var url = 'classes/obtener_productos.php';
        var arrayParametros = ['codigo', 'nombre'];
        selectProductos = '<option value="0"></option>';
        $.post(url, {
                parametros: arrayParametros
            },
            function (data) {
                data = $.parseJSON(data);
                $.each(data.productos, function (i, datos) {
                    selectProductos += '<option value="' + datos.codigo + '">' + datos.codigo + ' | ' + datos.nombre + '</option>';
                });
            }
        ).done(function () {
            for (var i = 1; i <= cantidadProductos; i++) {
                $("#select_producto" + i).html(selectProductos);
            }
        });
        var listadoMateria = '';
        for (var i = 0; i < listaMateriaPrima.length; i++) {
            listadoMateria += '<li>' + listaMateriaPrima[i] + '</i>';
        }
        $('#select_producto1').select2();
        $('#lista_materia_prima').html(listadoMateria);
    }
}

$(document).on('click', '#btn_aceptar_asignar', asignarMateriaaProducto);

$(document).on('click', '.md-overlay', cancelarCerrarAsignarMateriaPrima);

function cancelarCerrarAsignarMateriaPrima() {
    
    listaMateriaPrima = [];
    cantidadProductos = 1;
    $(':checkbox').each(function (index) {
        $(this).prop('checked', false);
    });
    $('#form-asignar-materia').removeClass('md-show');
    $('#lista_productos').html('<div class="form-group" id="item1"><label>Producto 1</label><div class="row"><div class="col-md-6"><select id="select_producto1" name="select_producto1" class="form-control pag-1 required"><option value="0"></option></select></div><div class="col-md-6"><input type="text" id="unidad_producto1" style="height:28px;" class="form-control" placeholder="Unidades requeridas"> </div> </div> </div>');
}

function asignarMateriaaProducto() {
    var url = 'classes/nueva_materia_prima.php',
        listaProductos = [],
        listaUnidades = [];
    $('#lista_productos').children().find('.required').each(function (index) {
        var indice = index + 1;
        if ($(this).val() !== '0' && $('#unidad_producto' + indice).val() !== '' && isNumber($('#unidad_producto' + indice).val())) {
            listaProductos.push($(this).val());
            listaUnidades.push($('#unidad_producto' + indice).val());
            $(this).parent().parent().removeClass('has-error');
            $(this).parent().children().find('.select2-selection--single').css("border-color", "#adadad");
        } else {
            $(this).parent().parent().addClass('has-error');
            $(this).parent().children().find('.select2-selection--single').css("border-color", "#a94442");
            mostrar_notificacion('Atención', 'Rellene todos los campos', 'warning');
        }
    });
    listaProductos = $.unique(listaProductos);
    $.post(url, {
            accion: 5,
            materias: listaMateriaPrima,
            productos: listaProductos,
            unidades: listaUnidades
        },
        function (data) {
            switch (data) {
            case '1':
                cancelarCerrarAsignarMateriaPrima();
                cargarMateriaPrima();
                mostrar_notificacion('Éxito', 'Materia Prima asignada con éxito', 'success');
                break;
            case '2':
                cancelarCerrarAsignarMateriaPrima();
                mostrar_notificacion('Atención', 'Algunos datos pueden no haberse actualizado', 'warning');
                break;
            case '0':
                mostrar_notificacion('Error', 'No se pudo procesar su solicitud', 'danger');
                break;
            default:
                mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor contacte a un administrador', 'danger');
                break;
            }
        });

}