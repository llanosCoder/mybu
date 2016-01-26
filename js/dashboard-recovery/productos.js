var categoriaNueva = '';
var paginaActualForm = 1;
var codigoProducto = '';

$(document).ready(function () {
    "use strict";
    $("#agregar_producto_sin_categoria").modalEffects();
    $('.tree-body').html('<div id="loading"><i class="fa fa-spinner fa-spin fa-4x"></i></div>');
    var treeCategorias = '<ul class="nav nav-list treeview collapse">',
        jerarquia = 1,
        ul = false;
    treeCategorias += '<li><label class="tree-toggler nav-header" onClick="cargarProductos(\'all\');" id="all"><i class="fa fa-folder-o icon-tree"></i>Mostrar todo</label></li>';
    $.post("classes/obtener_categorias.php", {},
        function (data) {
            data = $.parseJSON(data);
            $.each(data, function (i, datos) {
                if (datos.jerarquia > jerarquia) {
                    treeCategorias += '<ul  class="nav nav-list tree">';
                    ul = true;
                } else {
                    ul = false;
                    if (datos.jerarquia < jerarquia) {
                        var diferenciaCategorias = jerarquia - datos.jerarquia;
                        for (var i = 0; i < diferenciaCategorias; i++) {
                            treeCategorias += '</li></ul>';
                        }
                    }
                }
                if (i > 0 && !ul) {
                    treeCategorias += '</li>';
                }
                treeCategorias += '<li><label class="tree-toggler nav-header" onClick="cargarProductos(\'' + datos.desc + '\');" id="' + datos.desc + '"><i class="fa fa-folder-o icon-tree"></i>' + datos.nombre + '<i class="fa fa-plus-circle agregar_producto agregar_marca" data-modal="form-agregar-producto" onClick="setMarca(\'' + datos.desc + '\', 0);"></i></label>';
                jerarquia = datos.jerarquia;
                if (i == data.length - 1) {
                    var diferenciaCategorias = jerarquia - 1;
                    for (var i = 0; i < diferenciaCategorias; i++) {
                        treeCategorias += '</li></ul>';
                    }
                }
            });
        }
    ).done(function () {

        treeCategorias += '</li></ul>';
        //console.log(treeCategorias);
        $('#loading').hide();
        $('.tree-body').html(treeCategorias);
        $('.fa-plus-circle').modalEffects();
        $('label.tree-toggler').click(function () {
            var icon = $(this).children(".icon-tree");
            if (icon.hasClass("fa-folder-o")) {
                icon.removeClass("fa-folder-o").addClass("fa-folder-open-o");
            } else {
                icon.removeClass("fa-folder-open-o").addClass("fa-folder-o");
            }
            $(this).parent().children('ul.tree').toggle(300, function () {
                $(this).parent().toggleClass("open");
                $(".tree .nscroller").nanoScroller({
                    preventPageScrolling: true
                });
            });
        });
    });
});

$('#codigo').keyup(function (e) {
    if (e.keyCode == 13) {
        $('#nombre').focus();
    }
});

function cargarProductos(cId) {

    categoriaNueva = cId;
    $("#pcont").html('<div id="loading"><i class="fa fa-spinner fa-spin fa-4x"></i></div>');
    var table = '<table id="tabla_productos" class="table table-bordered"><thead><tr><th>Código</th><th>Nombre</th><th>Modelo</th><th>Descripción</th><th>Atributos</th><th>Marca</th><th>Imagen</th>';
    $.post("classes/obtener_productos_categoria.php", {
            cId: cId
        },
        function (data) {

            data = $.parseJSON(data);
            $.each(data, function (i, datos) {

                if (i == 0) {
                    if (datos.tipo_cuenta == '1') {
                        table += '<th>Edición</th><th>Promoción</th><th>Eliminar</th>';
                    }
                    table += '</tr></thead><tbody>';
                }
                table += '<tr>';
                table += '<td>' + datos.codigo + '</td>';
                table += '<td>' + datos.nombre + '</td>';
                table += '<td>' + datos.modelo + '</td>';
                table += '<td>' + datos.desc + '</td>';
                table += '<td>';
                var primero = true;
                if (datos.peso != null && datos.peso != '0 mg') {
                    table += 'Peso: ' + datos.peso;
                    primero = false;
                }
                if (datos.talla != null && datos.talla != '0') {
                    if (!primero) {
                        table += '|';
                    }
                    primero = false;
                    table += 'Talla: ' + datos.talla;
                }
                if (datos.dimensiones != null && datos.dimensiones != '0mm x 0mm x 0mm') {
                    if (!primero) {
                        table += '|';
                    }
                    primero = false;
                    table += 'Dimensiones: ' + datos.dimensiones;
                }
                if (datos.volumen != null && datos.volumen != '0 c3') {
                    if (!primero) {
                        table += '|';
                    }
                    table += 'Volumen: ' + datos.volumen;
                }
                table += '</td>';
                table += '<td>' + datos.marca + '</td>';
                if (datos.img == '') {
                    table += '<td>-</td>';
                } else {
                    table += '<td><a href="#" rel="shadowbox" class="imagen-previa" onClick="abrirFoto(\'' + datos.img + '\');"><i class="fa fa-picture-o fa-2x"></i></a></td>';
                }
                if (datos.tipo_cuenta == 1) {
                    table += '<td><a href="#" onClick="setMarca(\'' + categoriaNueva + '\', \'' + datos.codigo + '\');"><i class="fa fa-pencil-square fa-2x editar_producto" data-modal="form-agregar-producto"></i></a></td><td><a href="#" class="asignar_promocion" onClick="asignarPromocion(\'' + datos.codigo + '\');" data-modal="form-asignar-promocion"><i class="fa fa-check-square fa-2x"></i></a></td>';
                    table += '<td><a href="#" onClick="eliminarProducto(\'' + datos.codigo + '\');" style="color:red;"><i class="fa fa-times-circle fa-2x eliminar_producto"></i></a></td>';
                }
                table += '</tr>';
            });
        }
    ).done(function () {

        table += '</tbody></table>';
        $("#pcont").html(table);
        $('#tabla_productos').DataTable({
            "aaSorting": [[0, 'asc']],
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
        $('.fa-pencil-square').modalEffects();
        $('.asignar_promocion').modalEffects();
    });
}

$("#pcont").click(function () {
    $('.fa-pencil-square').modalEffects();
    $('.asignar_promocion').modalEffects();
});

function eliminarProducto(codigo) {
    'use strict';
    alertify.confirm('Atención<i class="fa fa-exclamation-triangle fa-2x" style="color:yellow;"></i>', "¿Seguro quieres eliminar este producto? Esta acción no puede deshacerse.",
        function () {
            var url = 'classes/eliminar_producto.php';
            $.post(url, {
                    codigo: codigo
                },
                function (data) {
                    data = $.parseJSON(data);
                    switch (data.resultado) {
                    case 0:
                        mostrar_notificacion('Error', 'No se ha podido procesar su solicitud', 'danger');
                        break;
                    case 1:
                        mostrar_notificacion('Éxito', 'Producto eliminado exitosamente', 'success');
                        cargarProductos('all');
                        break;
                    case 2:
                        mostrar_notificacion('Error', 'No tiene permisos para eliminar un producto', 'danger');
                        break;
                    case 3:
                        mostrar_notificacion('Error', 'No puede eliminar un producto que posea ventas relacionadas', 'danger');
                        break;
                    default:
                        mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte a un administrador', 'danger');
                        break;
                    }
                }).done(
                function () {}
            );
        },
        function () {}
    ).set('labels', {
        ok: 'Sí',
        cancel: 'Cancelar'
    });


}

function abrirFoto(content) {

    Shadowbox.init();
    Shadowbox.open({
        content: content,
        player: 'img'
    });
}

function bloquearCtrlJ() { // Verificação das Teclas  
    var tecla = window.event.keyCode; //Para controle da tecla pressionada  
    var ctrl = window.event.ctrlKey; //Para controle da Tecla CTRL  

    if (ctrl && tecla == 74) { //Evita teclar ctrl + j  
        event.keyCode = 0;
        event.returnValue = false;
    }
}

function asignarPromocion(codigo) {
    $('.modal-title').html('<h3>Asignar Promoción</h3>');
    var url = "classes/obtener_promociones.php";
    var select_promociones = '<option value="0"></option>';
    $.post(url, {
            sucursal: '1'
        },
        function (data) {

            data = $.parseJSON(data);
            $.each(data, function (i, datos) {
                select_promociones += '<option value="' + datos.id + '">' + datos.descripcion + '</option>';
            });
        }
    ).done(function () {

        $("#select_promociones").html(select_promociones);
        $("#select_promociones").select2();
        $("#codigo_producto").val(codigo);
    });
}

function guardarModalAsignar() {

    habilitarDeshabilitarBoton('btn_aceptar_modal_asignar', false);
    var codigo = $("#codigo_producto").val();
    var select_promociones = $("#select_promociones").val();
    if (codigo != '' && select_promociones != '') {
        var url = "classes/asignar_promociones.php";
        $.post(url, {
                productos: [codigo],
                promociones: select_promociones
            },
            function (data) {
                //console.log(data);
                switch (data) {
                case '0':
                    mostrar_notificacion('Error', 'La promoción o el producto no existen', 'danger');
                    break;
                case '1':
                    mostrar_notificacion('Éxito', 'Producto asignado a la promoción', 'success');
                    $('#form-asignar-promocion').removeClass('md-show');
                    break;
                case '2':
                    mostrar_notificacion('Atención', 'Promoción no existe', 'warning');
                    break;
                default:
                    mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte a un administrador', 'danger');
                    break;
                }
            }
        ).done(function () {
            habilitarDeshabilitarBoton('btn_aceptar_modal_asignar', true);
        });
    }
}

$('#imagen').change(function () {

    var file = $("#imagen")[0].files[0];
    if ((file.size || f.fileSize) > 2097152) {
        $('#btn_aceptar_modal').prop('disabled', true);
        mostrar_notificacion('Error', 'Imagen muy pesada. Máximo 2mb', 'warning');
    } else {
        var fileName = file.name;
        fileExtension = fileName.substring(fileName.lastIndexOf('.') + 1);
        switch (fileExtension.toLowerCase()) {
        case 'jpg':
        case 'gif':
        case 'png':
        case 'jpeg':
            $('#btn_aceptar_modal').prop('disabled', false);
            break;
        default:
            $('#btn_aceptar_modal').prop('disabled', true);
            mostrar_notificacion('Error', 'Extensión de imagen no válida', 'warning');
            break;
        }
    }
});

function setMarca(categoria, codigo) {

    $(".formulario").find(':input').each(function () {

        var elemento = this;
        if ($("#" + elemento.id).parent().hasClass('has-error')) {
            $("#" + elemento.id).parent().removeClass('has-error');
        }
    });
    $(".formulario").each(function () {

        this.reset();
    });
    $('#select_categorias').val('0').trigger("change");
    $('#select_marcas').val('0').trigger("change");
    codigoProducto = codigo;
    categoriaNueva = categoria;
    $(".modal-title").html("<h3>Nuevo Producto</h3>");
    $("#btn_aceptar_modal_editar").hide();
    $("#form-agregar-producto-pag-1").show();
    $("#form-agregar-producto-pag-2").hide();
    $("#pag-0").hide();
    var parametros = ['value', 'marca'];
    //console.log(categoriaNueva);
    $.post("classes/obtener_marcas_categoria.php", {
            cId: categoriaNueva,
            parametros: parametros
        },
        function (data) {

            data = $.parseJSON(data);
            var marcas = '<option value=\'\'></option>';
            $.each(data, function (i, datos) {
                marcas += '<option  value="' + datos.value + '">' + datos.marca + '</option>';
            });
            $("#select_marcas").html(marcas);
        }
    ).done(function () {

        if (codigo != 0) {
            $.post("classes/obtener_producto_detalle.php", {
                    cId: categoriaNueva,
                    codigo: codigo
                },
                function (data) {
                    data = $.parseJSON(data);
                    $.each(data, function (i, datos) {
                        $("#nombre").val(datos.nombre);
                        $("#codigo").val(datos.codigo);
                        $("#select_marcas").val(datos.marca);
                        $('#select_marcas option[value=' + datos.marca_id + ']').attr("selected", true);
                        $("#modelo").val(datos.modelo);
                        $("#descripcion").val(datos.desc);
                        $("#talla").val(datos.talla);
                        $("#peso").val(datos.peso);
                        $('#peso_unidad_medida option[value=' + datos.peso_unidad_medida + ']').attr("selected", true);
                        $("#alto").val(datos.alto);
                        $('#alto_unidad_medida option[value=' + datos.alto_unidad_medida + ']').attr("selected", true);
                        $("#ancho").val(datos.ancho);
                        $('#ancho_unidad_medida option[value=' + datos.ancho_unidad_medida + ']').attr("selected", true);
                        $("#largo").val(datos.largo);
                        $('#largo_unidad_medida option[value=' + datos.largo_unidad_medida + ']').attr("selected", true);
                        $("#volumen").val(datos.peso);
                        $('#volumen_unidad_medida option[value=' + datos.peso_unidad_medida + ']').attr("selected", true);
                        console.log(datos.pesable);

                        if (datos.pesable == '1') {
                            $('#check_pesable').prop('checked', true);
                        }
                    });
                });
            $("#btn_aceptar_modal").hide();
            $("#btn_aceptar_modal_editar").show();
            $(".modal-title").html("<h3>Editar Producto</h3>");
            $('#codigo').prop('disabled', true);
        } else {
            $("#btn_aceptar_modal").show();
            $("#btn_aceptar_modal_editar").hide();
            $('#codigo').prop('disabled', false);
        }
    });
}

function avanzarRetrocederModal(pagina, seguir) {
    $("#form-agregar-producto-pag-" + pagina).toggle();
    if (seguir) {
        pagina++;
        paginaActualForm++;
    } else {
        pagina--;
        paginaActualForm--;
    }
    $("#form-agregar-producto-pag-" + pagina).toggle();
}

function guardarModal(productoNuevo) {
    habilitarDeshabilitarBoton('btn_aceptar_modal', false);
    habilitarDeshabilitarBoton('btn_aceptar_modal_editar', false);
    var datos = new Object();
    datos['codigo'] = $("#codigo").val();
    datos['nombre'] = $("#nombre").val();
    datos['select_marcas'] = $("#select_marcas").val();
    datos['categoria'] = categoriaNueva;
    datos['modelo'] = $("#modelo").val();
    datos['descripcion'] = $("#descripcion").val();
    datos['imagen'] = $("#imagen").val();
    datos['talla'] = $("#talla").val();
    datos['alto'] = $("#alto").val();
    datos['alto_unidad_medida'] = $("#alto_unidad_medida").val();
    datos['ancho'] = $("#ancho").val();
    datos['ancho_unidad_medida'] = $("#ancho_unidad_medida").val();
    datos['largo'] = $("#largo").val();
    datos['largo_unidad_medida'] = $("#largo_unidad_medida").val();
    datos['peso'] = $("#peso").val();
    datos['volumen'] = $("#volumen").val();
    if ($('#check_pesable').is(':checked')) {
        $('#check_pesable').val('1');
    } else {
        $('#check_pesable').val('0');
    }
    var focusTaken = false;
    for (valor in datos) {
        if (datos[valor] == null || datos[valor] == '') {
            if ($("#" + valor).hasClass('required')) {
                $("#" + valor).parent().addClass('has-error');
                if (!focusTaken) {
                    for (var indicePaginas = 0; indicePaginas < 3; indicePaginas++) {
                        if ($("#" + valor).hasClass('pag-' + indicePaginas)) {
                            if (indicePaginas != paginaActualForm) {
                                paginaActualForm = indicePaginas;
                            }
                        }
                    }
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
        var url = '';
        if (productoNuevo) {
            url = 'classes/nuevo_producto.php';
            $(".formulario").append('<input type="hidden" name="categoria" value="' + categoriaNueva + '"></input>');
        } else {
            url = 'classes/editar_producto.php';
            $(".formulario").append('<input type="hidden" name="codigo" value="' + codigoProducto + '"></input>');
        }
        var formData = new FormData($(".formulario")[0]);
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                console.log(data);
                switch (data) {
                case '0':
                    var mensaje = '';
                    if (productoNuevo) {
                        mensaje = 'Ha ocurrido un error al registrar su producto';
                    } else {
                        mensaje = 'No se han cambiado valores en su producto';
                    }
                    mostrar_notificacion('Error', mensaje, 'danger');
                    break;
                case '1':
                    var mensaje = '';
                    if (productoNuevo) {
                        mensaje = 'Su producto se ha registrado exitosamente';
                    } else {
                        mensaje = 'Su producto se ha editado exitosamente'
                    }
                    mostrar_notificacion('Éxito', mensaje, 'success');
                    cargarProductos(categoriaNueva);
                    $('#form-agregar-producto').removeClass('md-show');
                    break;
                case '2':
                    var mensaje = 'Archivo de imagen con formato no válido';
                    mostrar_notificacion('Error', mensaje, 'danger');
                    break;
                case '3':
                    var mensaje = 'El código del producto ya existe';
                    mostrar_notificacion('Error', mensaje, 'danger');
                    break;
                default:
                    var mensaje = 'Ha ocurrido un error inesperado. Por favor, contacte a un administrador';
                    mostrar_notificacion('Error', mensaje, 'danger');
                    break;
                }
            },
            error: function () {
                console.log(data);
                mostrar_notificacion('Error', 'Ha ocurrido un error al registrar su producto', 'danger');
            }
        }).done(function () {
            habilitarDeshabilitarBoton('btn_aceptar_modal', true);
            habilitarDeshabilitarBoton('btn_aceptar_modal_editar', true);
        });
    } else {
        mostrar_notificacion('Atención', 'Ingrese los datos correctamente', 'warning');
        habilitarDeshabilitarBoton('btn_aceptar_modal', true);
        habilitarDeshabilitarBoton('btn_aceptar_modal_editar', true);
    }
};

$('#agregar_producto_sin_categoria').click(function () {
    var pag0 = '<div id="form-agregar-producto-pag-0">';
    pag0 += '<div class="form-group"><label>Categoria</label>';
    pag0 += '<select id="select_categorias" name="select_categorias" class="form-control pag-0 required"></select>';
    pag0 += '</div>';
    pag0 += '<button type="button" id="btn_pag0_sgte" class="btn btn-primary btn-flat" onClick="setCategoria()">Seguir</button></div>';
    $("#pag-0").html(pag0);
    $("#pag-0").show();
    $('#btn_aceptar_modal').hide();
    $(".modal-title").html("<h3>Nuevo Producto</h3>");
    if ($("#select_categorias").parent().hasClass('has-error'))
        $("#select_categorias").parent().removeClass('has-error');
    var url = 'classes/obtener_categorias.php',
        select_categorias = '<option value=""></option>';
    $.post(url,
        function (data) {
            data = $.parseJSON(data);
            $.each(data, function (i, datos) {
                select_categorias += '<option value="' + datos.desc + '">';
                var jerarquia = parseInt(datos.jerarquia);
                for (var i = 0; i < jerarquia - 1; i++) {
                    select_categorias += '&nbsp;&nbsp;&nbsp;';
                }
                select_categorias += datos.nombre + '</option>';
            });
        }
    ).done(function () {
        $("#select_categorias").html(select_categorias);
        $("#form-agregar-producto-pag-1").hide();
        $("#form-agregar-producto-pag-2").hide();
        $("#btn_aceptar_modal_editar").hide();
        $("#form-agregar-producto-pag-0").show();
        $('.select2').select2();
    });

});

function setCategoria() {
    var categoria = $("#select_categorias").val();
    if (categoria != '' && categoria != 'undefined') {
        avanzarRetrocederModal(0, true);
        setMarca(categoria, 0);
    } else {
        if (!$("#select_categorias").parent().hasClass('has-error'))
            $("#select_categorias").parent().addClass('has-error');
        mostrar_notificacion('Error', 'Seleccione una categoria', 'danger');
    }
}