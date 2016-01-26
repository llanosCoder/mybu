var clasesTree = [],
    categoriasSeleccionadas = [],
    manejarCategorias = false,
    categoriaPadre = '';

$(document).ready(function () {
    obtenerDatosSesion();
    cargarCategorias();
});

function obtenerDatosSesion() {
    var url = 'classes/obtener_datos_sesion.php';
    $.post(url, {
            parametros: ['empresa']
        },
        function (data) {
            data = $.parseJSON(data);
            if (data[0].empresa === '1') {
                manejarCategorias = true;
            }
        });

}

function setPadre(id) {
    'use strict';
    categoriaPadre = id;
    setSolicitud(id);
}

function cargarCategorias() {
    $('#asignar_row').show();
    $('#contenido_materias_primas').html('<div id="loading"><i class="fa fa-spinner fa-spin fa-4x"></i></div>');
    var treeCategorias = '';
    treeCategorias += '<ul class="nav nav-list treeview collapse">',
        jerarquia = 1,
        ul = false,
        jerarquiaAnterior = 0;
    $.post("classes/obtener_categorias.php", {
            type: 0,

        },
        function (data) {
            data = $.parseJSON(data);
            $.each(data, function (i, datos) {
                if (datos.jerarquia == 1) {
                    clasesTree = [];
                    clasesTree.push(datos.desc);
                }
                if (datos.jerarquia > jerarquia) {
                    treeCategorias += '<ul class="nav nav-list tree">';
                    ul = true;
                    clasesTree.push(datos.desc);
                } else {
                    ul = false;
                    if (datos.jerarquia < jerarquia) {
                        var diferenciaCategorias = jerarquia - datos.jerarquia;
                        for (var i = 0; i < diferenciaCategorias; i++) {
                            treeCategorias += '</li></ul>';
                            clasesTree.pop();
                        }
                        clasesTree.pop();
                        clasesTree.push(datos.desc);
                    } else {
                        clasesTree.pop();
                        clasesTree.push(datos.desc);
                    }
                }
                if (i > 0 && !ul) {
                    treeCategorias += '</li>';
                }
                treeCategorias += '<li><div class="row"><div class="col-md-1"><input type="checkbox" class="';
                for (var indice = 0; indice < clasesTree.length; indice++) {
                    treeCategorias += clasesTree[indice] + ' ';
                }
                treeCategorias += 'da" id="' + datos.desc + '" name="' + datos.desc + '" value="' + datos.desc + '"/></div><div class="col-md-10"><label class="tree-toggler nav-header"><i class="fa fa-folder-o icon-tree"></i>' + datos.nombre + ' <i class="fa fa-plus-circle pull-right agregar_categoria" onClick="setPadre(\'' + datos.desc +'\')" data-modal="form-solicitar-categoria"></i></label></div></div>';
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
        treeCategorias += '</li></ul></div>';
        //console.log(treeCategorias);
        $('#btn_asignar_cat').show();
        $('#contenido_materias_primas').hide();
        $('#col-tree').html(treeCategorias);
        $('#wrapper').show();
        $('#btn_solicitar').modalEffects();
        $('.agregar_categoria').modalEffects();
        $('.da').iCheck({
            checkboxClass: 'icheckbox_flat-blue'
        });
        /*var url = 'classes/obtener_categorias_actuales.php';
        $.post(url, {
                parametros: ['descripcion']
            },
            function (data) {
                data = $.parseJSON(data);
                $.each(data, function (i, datos) {

                    $('#' + datos.descripcion).iCheck('check');
                });
            });
            */
        $('.da').on('ifChecked', function (event) {
            //$(this).parents('li').find(':checkbox').iCheck('check');
            var id = $(this).attr('id');
            marcar(id);
        });
        $('.da').on('ifUnchecked', function (event) {
            //$(this).parent().parent().parent().children('li').find(':checkbox').iCheck('uncheck');
            //var clases = $(this).attr('class').split(' ');
            var id = $(this).attr('id');
            desmarcar(id);
        });
        //$('label.tree-toggler').off('click');
        $('label.tree-toggler').on('click', function () {
            var icon = $(this).children(".icon-tree");
            if (icon.hasClass("fa-folder-o")) {
                icon.removeClass("fa-folder-o").addClass("fa-folder-open-o");
            } else {
                icon.removeClass("fa-folder-open-o").addClass("fa-folder-o");
            }
            $(this).parent().parent().parent().children('ul.tree').toggle(300, function () {
                $(this).parent().parent().parent().toggleClass("open");
                $(".tree .nscroller").nanoScroller({
                    preventPageScrolling: true
                });
            });
        });
        
    });
}

function marcar(id) {

    categoriasSeleccionadas.push($('#' + id).val());
    
    /*$(':checkbox').each(function () {
        var esPadre = true;
        var clasesElementos = $(this).attr('class').split(' ');
        for (var i = 0; i < clasesElementos.length; i++) {
            var indice = $.inArray(clasesElementos[i], clases);
            if (indice == -1) {
                esPadre = false;
                break;
            }
        }
        if (esPadre) {
            $(this).iCheck('check');
            if (existeValor(categoriasSeleccionadas, $(this).attr('id')) == 0) {
                categoriasSeleccionadas.push($(this).attr('id'));
            }
        }
    });*/
}

function desmarcar(id) {
    
    var index = $.inArray(id, categoriasSeleccionadas);
    categoriasSeleccionadas.splice(index, 1);
    
    /*$(':checkbox').each(function () {
        var esHijo = true;
        for (var i = 0; i < clases.length; i++) {
            if ($(this).attr('class').indexOf(clases[i]) == -1 && clases[i] != 'icheck') {
                esHijo = false;
                break;
            }
        }
        if (esHijo) {
            $(this).iCheck('uncheck');
            eliminarValor(categoriasSeleccionadas, $(this).attr('id'));
        }
    });*/
}

function asignarCategorias() {
    var url = 'classes/eliminar_categorias.php';
    $.post(url, 
    {
        categorias: categoriasSeleccionadas
    },
    function (data) {
        data = $.parseJSON(data);
        
        switch (data.resultado) {
        case 0:
            mostrar_notificacion('Error', 'No se eliminaron categorías ya que éstas tienen productos asignados', 'danger');
            categoriasSeleccionadas = [];
            cargarCategorias();
            break;
        case 1:
            mostrar_notificacion('Éxito', 'Categorias eliminadas con éxito', 'success');
            categoriasSeleccionadas = [];
            cargarCategorias();
            break;
        case 2:
            mostrar_notificacion('Atención', 'No se eliminaron categorías ya que éstas tienen productos asignados', 'warning');
            categoriasSeleccionadas = [];
            cargarCategorias();
            break;
        default:
            mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte un administrador', 'danger');
            break;
        }
    });
};

function setSolicitud(id) {
    limpiarFormulario();
    
    if(id != '0'){
        $('#select_padres').parent().hide();
    }else{
        $('#select_padres').parent().show();
        $('#select_padres').val('0').trigger("change");
        
    }
    
    categoriaPadre = id;
    
    var url = 'classes/obtener_categorias_actuales.php';
    var categorias = '<option value="0"></option>';
    $.post(url, {
            parametros: ['nombre', 'descripcion'],
            padre: categoriaPadre
        },
        function (data) {
            data = $.parseJSON(data);
            $.each(data, function (i, datos) {
                categorias += '<option value="' + datos.descripcion + '">' + datos.nombre + '</option>';
            });
        }).done(function () {
        $('#select_padres').html(categorias);
        $('#btn_aceptar_modal').off('click');
        $('#btn_aceptar_modal').on('click', function () {
            if(id == 0){
                categoriaPadre = $('#select_padres').val();
            }
            habilitarDeshabilitarBoton('btn_aceptar_modal', false);
            var nombre = $('#nombre').val();
            if (nombre != '' && nombre != null) {
                var url = 'classes/crear_categoria.php';
                var parametros = new Object();

                parametros['nombre'] = nombre;
                parametros['padre'] = categoriaPadre;
                $.post(url, {
                        parametros: parametros
                    },
                    function (data) {
                        data = $.parseJSON(data);
                        switch (data.resultado) {
                        case 0:
                            mostrar_notificacion('Error', 'No se pudo procesar su solicitud', 'danger');
                            break;
                        case 1:
                            mostrar_notificacion('Éxito', 'Su solicitud ha sido enviada con éxito', 'success');
                            $('#form-solicitar-categoria').removeClass('md-show');
                            cargarCategorias();
                            break;
                        default:
                            mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte un administrador', 'danger');
                            break;
                        }
                    }
                ).done(function () {
                    habilitarDeshabilitarBoton('btn_aceptar_modal', true);
                });

            } else {
                $('#nombre').parent().addClass('has-error');
                habilitarDeshabilitarBoton('btn_aceptar_modal', true);
                mostrar_notificacion('Atención', 'Debe dar un nombre a la categoría nueva', 'warning');
            }
        });
    });
}

function limpiarFormulario() {
    $(".formulario").each(function () {
        this.reset();
    });
}

function mostrarSolicitudes() {
    /*$('#asignar_row').hide();
    $('#contenido_materias_primas').show();
    $('#wrapper').hide();
    $('#contenido_materias_primas').html('<div id="loading"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');
    var url = 'classes/obtener_solicitudes_categorias.php';
    var tablaSolicitudes = '<div style="margin-top:3em;"><table id="tabla_solicitudes" class="table table-bordered"><thead><tr><th>Nombre</th><th>Cat. padre</th>';
    tablaSolicitudes += '<th>Fecha solicitud</th><th>Usuario</th>';
    $.post(url, {},
        function (data) {
            data = $.parseJSON(data);
            $.each(data.solicitudes, function (i, datos) {
                if (i == 0) {
                    if (data.tipo_cuenta == '3')
                        tablaSolicitudes += '<th>Empresa</th>';
                    tablaSolicitudes += '<th>Estado</th><th>Motivo</th>'
                    if (manejarCategorias) {
                        tablaSolicitudes += '<th>Responder</th>';
                    }
                    tablaSolicitudes += '</tr></thead><tbody>';
                }
                tablaSolicitudes += '<tr><td>' + datos.nombre + '</td>';
                var padre = datos.padre;
                if (padre == null)
                    padre = 'GLOBAL';
                tablaSolicitudes += '<td>' + padre + '</td><td>' + datos.fecha + '</td>';
                var estado = '';
                switch (datos.estado) {
                case '0':
                    estado = '<label>Pendiente</label>';
                    break;
                case '1':
                    estado = '<label style="color:green;">Aprobado</label>';
                    break;
                case '2':
                    estado = '<label style="color:red;">Rechazado</label>';
                    break;
                }
                var razon = datos.razon;
                if (razon == '')
                    razon = '-';
                tablaSolicitudes += '<td>' + datos.usuario + '</td>';
                if (data.tipo_cuenta == '3')
                    tablaSolicitudes += '<td>' + datos.empresa + '</td>';
                tablaSolicitudes += '<td>' + estado + '</td><td>' + razon + '</td>';
                if (manejarCategorias) {
                    tablaSolicitudes += '<td><a href="javascript:void(0)"><i class="fa fa-check-circle fa-2x aceptar_solicitud" id="as' + datos.id + '"></i></a><a href="javascript:void(0)" style="color:red;" data-modal="modal-rechazar-solicitud" class="pull-right rechazar_solicitud" id="rs' + datos.id + '"><i class="fa fa-times-circle fa-2x" id="rechazar_solicitud"></i></a></td>';
                }
                tablaSolicitudes += '</tr>';
            });
        }).done(function () {
        tablaSolicitudes += '</tbody></table></div>';
        $('#contenido_materias_primas').html(tablaSolicitudes);
        $('.rechazar_solicitud').modalEffects();
        $('#tabla_solicitudes').dataTable({
            "aaSorting": [[2, 'desc']],
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
        $('.aceptar_solicitud').off('click');
        $('.aceptar_solicitud').on('click', function () {
            procesarSolicitud($(this), 1);
        });
        //$('.rechazar_solicitud').off('click');
        $('.rechazar_solicitud').on('click', modalRechazarSolicitud);
    });
    */
}

$('#contenido_materias_primas').on('click', function () {
    
    $('.aceptar_solicitud').off('click');
    $('.aceptar_solicitud').on('click', function () {
            procesarSolicitud($(this), 1);
        });
    //$('.rechazar_solicitud').off('click');
    $('.rechazar_solicitud').on('click', modalRechazarSolicitud);
    $('.rechazar_solicitud').modalEffects();
});

function modalRechazarSolicitud() {
    $('#razon_rechazo').parent().removeClass('has-error');
    $('#razon_rechazo').val('');
    $('#razon_rechazo').focus();
    var id = $(this).attr('id');
    id = id.replace('rs', '');
    $('#btn_aceptar_rechazar_solicitud').off('click');
    $('#btn_aceptar_rechazar_solicitud').on('click', function () {
        rechazarSolicitud(id);
    });
}

function rechazarSolicitud(id) {

    if ($('#razon_rechazo').val() != '') {
        habilitarDeshabilitarBoton('btn_aceptar_rechazar_solicitud', false);
        var rechazo = $('#razon_rechazo').val();
        var url = 'classes/aceptar_rechazar_categoria.php';
        $.post(url, {
                accion: 2,
                id: id,
                razon: rechazo
            },
            function (data) {
                habilitarDeshabilitarBoton('btn_aceptar_rechazar_solicitud', true);
                switch (data) {
                case '1':
                    mostrar_notificacion('Éxito', 'Categoría rechazada', 'success')
                    $('#modal-rechazar-solicitud').removeClass('md-show');
                    mostrarSolicitudes();
                    break;
                case '3':
                    mostrar_notificacion('Error', 'No tiene los permisos para realizar tal acción', 'danger')
                    break;
                case '0':
                    mostrar_notificacion('Error', 'No se pudo procesar su solicitud', 'danger')
                    break;
                default:
                    mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte a un administrador', 'danger');
                    break;
                }
            }
        );

    } else {
        $('#razon_rechazo').parent().addClass('has-error');
        mostrar_notificacion('Atención', 'Ingrese una razón del rechazo', 'warning');
    }
};

function procesarSolicitud(element, accion) {
    var id = $(element).attr('id');
    id = id.replace('as', '');
    alertify.confirm('Atención', "¿Seguro quieres crear esta categoría? Esta acción no puede deshacerse.",
        function () {
            var url = "classes/aceptar_rechazar_categoria.php";
            if (accion === 1) {
                aceptarSolicitud(url, id, accion)
            }
        },
        function () {}
    ).set('labels', {
        ok: 'Sí',
        cancel: 'Cancelar'
    });
};

function aceptarSolicitud(url, id, accion) {
    $.post(url, {
            accion: accion,
            id: id
        },
        function (data) {
            switch (data) {
            case '1':
                mostrar_notificacion('Éxito', 'Categoría aprobada', 'success');
                break;
            case '2':
                mostrar_notificacion('Atención', 'Categoría creada, pero no se pudo cerrar solitcitud', 'warning');
                break;
            case '3':
                mostrar_notificacion('Error', 'No tiene los permisos para realizar tal acción', 'danger');
                break;
            case '0':
                mostrar_notificacion('Error', 'No se pudo procesar su solicitud', 'danger');
                break;
            default:
                mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte a un administrador', 'danger');
                break;
            }
        }
    ).done(function () {
        mostrarSolicitudes();
    });
}