var arrayEstados = [],
    paginaActualForm = 1,
    ultimoRadioCliqueado = 0,
    alcanceSucursal = 1,
    idActual = 0;
var arrayOfertasComprar = [],
    proveedores = [];
var contadorLista = 0,
    contadorItems = 0;
var admin = 0;

$(document).ready(function () {
    var f = new Date();
    var month = parseInt(f.getMonth()) + 1;
    var fecha = f.getFullYear()+'-'+month+'-'+f.getDate()+'T00:00:00Z';
    $('.datetime').attr('data-date',fecha);
    cargarFiltros(2);
    cargarOfertas('nuevos', true);
    cargarCarroCompra('carro_compra_ofertas');
    $("#contenido_admin").addClass('div-hide');
    $("#btn_comprar").addClass('div-hide');
    $("#btn_comprar").modalEffects();
    $(".agregar_oferta").modalEffects();
});

function cargarCarroCompra(nombreCarro) {
    'use strict';
    var url = 'classes/administrar_carro_compra.php',
        arr = [];
    $.post(url,
        {
            carro: nombreCarro,
            accion: 2
        },
        function (data) {
            data = $.parseJSON(data);
            $.each(data.carro, function (i, datos) {
                arr[0] = datos.oferta;
                arr[1] = datos.oferta_nombre;
                arr[2] = datos.cantidad;
                arr[3] = datos.precio_oferta;
                arr[4] = datos.precio_normal;
                arr[5] = datos.proveedor;
                agregarCarro(arr[5], arr[0], arr[1], arr[3], arr[4], arr[2], false);
                contadorItems += parseInt(datos.cantidad) - 1;
            });
        }).done(
        function () {
            actualizarCarro();
        }
    );
    
}

function cargarFiltros(fuente) {
    var url = 'classes/obtener_filtros.php';
    var filtros = '';
    $.post(url, {
            fuente: fuente
        },
        function (data) {
            data = $.parseJSON(data);
            $.each(data, function (i, datos) {
                filtros += '<option value="' + datos.codigo + '">' + datos.nombre + '</option>';
            });
        }
    ).done(function () {
        $('#select_filtros').html(filtros);
    });
}

$("#select_filtros").change(function () {
    if (admin == 1)
        cargarOfertasPropias($("#select_filtros").val(), true);
    else
        cargarOfertas($("#select_filtros").val(), true);
});

function cargarOfertas(filtro, filtrado) {
    if (!filtrado)
        cargarFiltros(2);
    var url = "classes/obtener_ofertas.php";
    var tablaOfertas = '<table id="tabla_ofertas" class="table table-bordered"><thead><tr><th>Proveedor</th><th>Descripción</th><th>Oferta</th><th>Disponibilidad</th><th>Comprar</th></tr></thead><tbody>';
    $.post(url, {
            filtro: filtro,
            propias: false
        },
        function (data) {
            data = $.parseJSON(data);
            $.each(data, function (i, datos) {
                tablaOfertas += '<tr><td>' + datos.proveedor + '</td><td>' + datos.descripcion + '</td>';
                switch (datos.oferta_tipo) {
                case '1':
                    var precio = currencyFormat(datos.precio, '$');
                    var cantidad = currencyFormat(datos.cantidad, '');
                    tablaOfertas += '<td>' + precio + ' pesos por ' + cantidad + ' productos.</td>';
                    break;
                case '2':
                    var cantidad = currencyFormat(datos.cantidad, '');
                    tablaOfertas += '<td>' + datos.descuento + '% de descuento al llevar ' + cantidad + '.</td>';
                    break;
                default:
                    tablaOfertas += '<td></td>';
                }
                switch (datos.tipo) {
                case '1':
                    tablaOfertas += '<td>Desde el ' + datos.f_inicio + ' hasta el ' + datos.f_termino + '</td>';
                    break;
                case '2':
                    var stock = currencyFormat(datos.stock, '');
                    tablaOfertas += '<td>Quedan ' + stock + ' unidades</td>';
                    break;
                case '3':
                    var stock = currencyFormat(datos.stock, '');
                    tablaOfertas += '<td>Desde el ' + datos.f_inicio + ' hasta el ' + datos.f_termino + ' y quedan ' + stock + ' unidades</td>';
                    break;
                default:
                    tablaOfertas += '<td></td>';
                    break;
                }
                if (datos.oferta_tipo == '1') {
                    var precioOferta = datos.precio;
                } else {
                    var precioOferta = (datos.precio_producto * datos.cantidad) - ((datos.precio_producto * datos.cantidad) * datos.descuento / 100);
                }
                var precioNormal = datos.precio_producto * datos.cantidad;
                tablaOfertas += '<td class="text-center"><a href="#" onClick="agregarCarro(' + datos.proveedor_id + ', ' + datos.id + ', \'' + datos.descripcion + '\', ' + precioOferta + ', ' + precioNormal + ', 1, true);"><i class="fa fa-cart-plus fa-2x"></i></a></td></tr>';
            });
        }
    ).done(function () {
        tablaOfertas += '</tbody></table>';
        $("#tabla_ofertas_cont").html(tablaOfertas);
        $("#tabla_ofertas").dataTable({
            "order": [1, 'asc'],
            "oLanguage": {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ ofertas",
                "sZeroRecords": "No se encontraron ofertas",
                "sEmptyTable": "No hay ofertas disponibles",
                "sInfo": "Mostrando ofertas de la _START_ a la _END_ de un total de _TOTAL_ ofertas",
                "sInfoEmpty": "Mostrando ofertas de la 0 a la 0 de un total de 0 ofertas",
                "sInfoFiltered": "(filtrado de un total de _MAX_ ofertas)",
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
    });
}

$('#btn-cerrar-comprar').click(function(){
    $('#form-comprar').removeClass('md-show');
    reducirModal();
});

function reducirModal(){
    $('#form-comprar').addClass('no-scroll');
}

function agregarCarro(proveedor, oId, oNombre, precioOferta, precioNormal, cantidad, subir) {
    if(subir == true){
        var objSubir = {'oferta':oId,'oferta_nombre': oNombre, 'cantidad':cantidad, 'precio_oferta': precioOferta, 'precio_normal': precioNormal, 'proveedor': proveedor};
        subirCarro('carro_compra_ofertas', [objSubir], 1);
        mostrar_notificacion('', 'Producto añadido al carro.', 'success', 'bottom-left');
    }
    $("#btn_comprar").show();
    var nuevoProducto = true;
    proveedores.push(proveedor);
    for (var i = 0; i < arrayOfertasComprar.length; i++) {
        if (arrayOfertasComprar[i][0] == oId) {
            arrayOfertasComprar[i][2] += cantidad;
            nuevoProducto = false;
            continue;
        }
    }
    if (nuevoProducto) {
        var lista = [oId, oNombre, cantidad, precioOferta, precioNormal, proveedor];
        arrayOfertasComprar.push(lista);
        var menor = 0,
            posicion = 0;
        var arrayTemporal = arrayOfertasComprar;
        //arrayOfertasComprar = [];

        for (var j = 0; j < arrayTemporal.length; j++) {
            menor = 0;
            posicion = 0;
            $.each(arrayTemporal, function (i, item) {
                if (menor < item[5]) {
                    posicion = i;
                    menor = item[5];
                }
            });
            arrayOfertasComprar.push(arrayTemporal[posicion]);
            arrayTemporal.splice(posicion, 1);

        }
        contadorLista++;
    }
    contadorItems++;
    actualizarCarro();
};

function actualizarCarro() {
    $("#btn_comprar_nro_items").html(contadorItems);
}

$("#btn_comprar").click(function () {
    $("#f_vencimiento").parent().removeClass('has-error');
    cargarListaCompras();
});

function cargarListaCompras() {
    $('#form-comprar').removeClass('no-scroll');
    $('.md-overlay').off('click',  reducirModal);
    $('.md-overlay').on('click',  reducirModal);
    var tablaCompras = '<table id="tabla_lista_compras" class="table table-bordered"><thead><tr><th>Nombre</th><th>Cantidad</th><th>Precio normal </th><th>Precio oferta</th><th>Total ítem</th><th>Quitar</th></tr></thead><tbody>';
    
    for (var i = 0; i < arrayOfertasComprar.length; i++) {
        tablaCompras += '<tr><td>' + arrayOfertasComprar[i][1] + '</td>';
        console.log(arrayOfertasComprar[i][2]);
        
        tablaCompras += '<td><input id="cantidad' + arrayOfertasComprar[i][0] + '" value="' + arrayOfertasComprar[i][2] + '" type="text" onChange="cambiarCantidadItem(' + arrayOfertasComprar[i][0] + ')" name="cantidad"></td>';
        tablaCompras += '<td>' + currencyFormat(arrayOfertasComprar[i][4], '$ ') + '</td>';
        tablaCompras += '<td>' + currencyFormat(arrayOfertasComprar[i][3], '$ ') + '</td>';
        var cantidad = arrayOfertasComprar[i][2];
        var precioOferta = (arrayOfertasComprar[i][3]) * cantidad;
        tablaCompras += '<td id="precio_oferta' + i + '">' + currencyFormat(precioOferta, '$ ') + '</td>';
        tablaCompras += '<td><a href="#" style="color:red;" onClick="eliminarOfertaCompra(' + arrayOfertasComprar[i][0] + ')">';
        tablaCompras += '<i class="fa fa-times-circle fa-2x"></i></a></td></tr>';
    }
    tablaCompras += '</tbody></table>';

    var totalOfertas = 0,
        totalNormal = 0;
    for (var i = 0; i < arrayOfertasComprar.length; i++) {
        totalOfertas += arrayOfertasComprar[i][3] * arrayOfertasComprar[i][2];
        totalNormal += arrayOfertasComprar[i][4] * arrayOfertasComprar[i][2];
    }

    $("#sub-total").html('Sub-Total: ' + currencyFormat(totalNormal, '$ '));
    var descuentos = totalNormal - totalOfertas;
    $("#descuentos").html('Descuentos: ' + currencyFormat(descuentos, '$ '));
    $("#total").html('Total: ' + currencyFormat(totalOfertas, '$ '));
    $("#lista_tabla_compras").html(tablaCompras);
    $("#tabla_lista_compras").dataTable({
        "order": [[0, 'asc']],
        "oLanguage": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ ofertas",
            "sZeroRecords": "No se encontraron ofertas",
            "sEmptyTable": "No hay ofertas disponibles",
            "sInfo": "Mostrando ofertas de la _START_ a la _END_ de un total de _TOTAL_ ofertas",
            "sInfoEmpty": "Mostrando ofertas de la 0 a la 0 de un total de 0 ofertas",
            "sInfoFiltered": "(filtrado de un total de _MAX_ ofertas)",
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
    $("input[name='cantidad']").TouchSpin();
}

function calcularOferta(i) {
    var cantidad = arrayOfertasComprar[i][2];
    var precioOferta = (arrayOfertasComprar[i][3]) * cantidad;
    $("#precio_oferta" + i).html(currencyFormat(precioOferta, '$ '));
}

function calcularTotales() {
    var totalOfertas = 0,
        totalNormal = 0;
    for (var i = 0; i < arrayOfertasComprar.length; i++) {
        totalOfertas += arrayOfertasComprar[i][3] * arrayOfertasComprar[i][2];
        totalNormal += arrayOfertasComprar[i][4] * arrayOfertasComprar[i][2];
    }
    $("#sub-total").html('Sub-Total:' + currencyFormat(totalNormal, '$ '));
    $("#descuentos").html('Descuentos: ' + currencyFormat((totalNormal - totalOfertas), '$ '));
    $("#total").html('Total:' + currencyFormat(totalOfertas, '$ '));
}

function cambiarCantidadItem(oId) {
    var cantidadInicial = 0;
    
    for (var i = 0; i < arrayOfertasComprar.length; i++) {
        if (arrayOfertasComprar[i][0] == oId) {
            cantidadInicial = arrayOfertasComprar[i][2];
            arrayOfertasComprar[i][2] = parseInt($("#cantidad" + oId).val());
            calcularOferta(i);
            calcularTotales();
            var arr = [];
            var objSubir = {};
            objSubir['oferta'] = arrayOfertasComprar[i][0];
            objSubir['oferta_nombre'] = arrayOfertasComprar[i][1];
            objSubir['cantidad'] = arrayOfertasComprar[i][2];
            objSubir['precio_oferta'] = arrayOfertasComprar[i][3];
            objSubir['precio_normal'] = arrayOfertasComprar[i][4];
            objSubir['proveedor'] = arrayOfertasComprar[i][5];
            arr.push(objSubir);
            /*var arr2 = [objSubir['oferta'],  objSubir['oferta_nombre'], objSubir['cantidad'], objSubir['precio_oferta'], objSubir['precio_normal'], objSubir['proveedor']];
            arrayOfertasComprar.push(arr2);*/
            
            subirCarro('carro_compra_ofertas', arr, 3);
            break;
        }
    }
    contadorItems += $("#cantidad" + oId).val() - cantidadInicial;
    actualizarCarro();
};

function eliminarOfertaCompra(oId) {
    for (var i = 0; i < arrayOfertasComprar.length; i++) {
        if (arrayOfertasComprar[i][0] == oId) {
            contadorItems = contadorItems - arrayOfertasComprar[i][2];
            arrayOfertasComprar.splice(i, 1);
            continue;
        }
    }
    cargarListaCompras();
    actualizarCarro();
    eliminarOfertaCarro(oId);
}

function eliminarOfertaCarro(oId) {
    'use strict';
    var url = 'classes/administrar_carro_compra.php';
    $.post(url,
        {
            accion: 4,
            carro: 'carro_compra_ofertas',
            parametros: [{'oferta': oId}]
        },
        function (data) {
            
        }).done(
        function () {
        }
    );
}

$("#btn_comprar_modal").click(function () {
    var url = 'classes/nueva_orden_compra.php';
    var fechaVencimiento = $("#f_vencimiento").val();
    var f = new Date();
    var mes = f.getMonth() + 1;
    if (mes < 10)
        mes = '0' + mes;
    var dia = f.getDate();
    if (dia < 10)
        dia = '0' + dia;
    var fechaActual = f.getFullYear() + "-" + mes + "-" + dia + " - " + f.getHours() + ":" + f.getMinutes();
    if (fechaVencimiento < fechaActual || fechaVencimiento == '') {
        $("#f_vencimiento").parent().addClass('has-error');
        mostrar_notificacion('Error', 'La fecha de vencimiento no puede ser menor a la fecha actual', 'danger');
    } else {
        $("#f_vencimiento").parent().removeClass('has-error');
        habilitarDeshabilitarBoton('btn_comprar_modal', false);
        var totalOfertas = 0,
            totalNormal = 0,
            ofertas = [],
            ofertasCantidad = [];
        var proveedorAnterior = arrayOfertasComprar[0][5];
        for (var i = 0; i < ofertas.length; i++) {
            ofertasCantidad.push($("#cantidad" + ofertas[i]).val());
        }
        var descuentos = totalNormal - totalOfertas;
        $.post(url, {
                cantidad_ofertas: ofertasCantidad,
                f_vencimiento: fechaVencimiento,
                parametros: arrayOfertasComprar
            },
            function (data) {
                switch (data) {
                case '1':
                    mostrar_notificacion('Éxito', 'Se ha procesado su solicitud satisfactoriamente', 'success');
                    arrayOfertasComprar = [];
                    contadorItems = 0;
                    actualizarCarro();
                    break;
                case '0':
                    mostrar_notificacion('Error', 'No se ha podido procesar su solicitud', 'danger');
                    break;
                default:
                    mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte a un administrador', 'danger');
                    break;
                }
            }
        ).done(function () {
            habilitarDeshabilitarBoton('btn_comprar_modal', true, 'Comprar');
            $("#form-comprar").removeClass('md-show');
            reducirModal();
        });
    }
});

function cargarOfertasPropias(filtro, filtrado) {
    if (!filtrado)
        cargarFiltros(1);
    $("#btn_comprar").hide();
    var url = "classes/obtener_ofertas.php";
    var tablaOfertas = '<table id="tabla_ofertas_propias" class="table table-bordered"><thead><tr><th>Descripción</th><th>Descuento</th><th>Precio</th><th>Cantidad</th><th>Fecha Inicio</th><th>Fecha término</th><th>Stock</th><th>Estado</th><th>Edición</th><th>Eliminar</th></tr></thead><tbody>';
    if (!filtro) {
        filtro = 'todos';
    }
    $.post(url, {
            filtro: filtro,
            propias: true
        },
        function (data) {
            data = $.parseJSON(data);
            $.each(data, function (i, datos) {
                tablaOfertas += '<tr><td>' + datos.descripcion + '</td><td>' + datos.descuento + '</td><td>' + currencyFormat(datos.precio, '$') + '</td><td>' + currencyFormat(datos.cantidad, '');
                tablaOfertas += '</td><td>' + datos.f_inicio + '</td><td>' + datos.f_termino + '</td><td>' + currencyFormat(datos.stock, '') + '</td>';
                arrayEstados[datos.id] = datos.estado;
                tablaOfertas += '<td><div data-modal="form-procesar-control" id="' + datos.id + 'div" onClick="activarDesactivarOferta(' + datos.id + ', ' + arrayEstados[i] + ');" class="activar_desactivar_oferta disabled"><input type="checkbox" id="' + datos.id + 'checkbox" class="control-oferta"';
                if (arrayEstados[datos.id] == 1)
                    tablaOfertas += ' checked ';
                tablaOfertas += 'data-toggle="toggle"></div></td>';
                tablaOfertas += '<td class="text-center"><a href="#" onClick="editarOferta(' + datos.id + ', \'' + datos.descripcion + '\', ' + datos.oferta_tipo + ', ' + datos.descuento + ', ' + datos.precio + ', ' + datos.cantidad + ', \'' + datos.f_inicio + '\', \'' + datos.f_termino + '\', ' + datos.stock + ', \'' + datos.producto + '\')"><i class="fa fa-pencil-square fa-2x agregar_oferta" data-modal="form-agregar-oferta"></i></a></td>';
                tablaOfertas += '<td class="text-center"><a href="#" style="color:red;"><i class="fa fa-times-circle fa-2x" onClick="eliminarOferta(' + datos.id + ')"></i></a></td></tr>';
            });
        }
    ).done(function () {
        tablaOfertas += '</tbody></table>';
        $("#tabla_ofertas_cont2").html(tablaOfertas);
        $('.control-oferta').bootstrapToggle();
        $("#tabla_ofertas_propias").DataTable({
            "aaSorting": [[0, 'asc']],
            "oLanguage": {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ ofertas",
                "sZeroRecords": "No se encontraron ofertas",
                "sEmptyTable": "No hay ofertas disponibles",
                "sInfo": "Mostrando ofertas de la _START_ a la _END_ de un total de _TOTAL_ ofertas",
                "sInfoEmpty": "Mostrando ofertas de la 0 a la 0 de un total de 0 ofertas",
                "sInfoFiltered": "(filtrado de un total de _MAX_ ofertas)",
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
        $(".activar_desactivar_").modalEffects();
    });
}

function activarDesactivarOferta(indice) {
    var check = '<input type="checkbox" id="' + indice + 'checkbox" class="control-oferta"';
    if (arrayEstados[indice] == 0)
        check += ' checked ';
    check += 'disabled data-toggle="toggle">';
    $("#" + indice + 'div').html(check);
    $('.control-oferta').bootstrapToggle();
    $.post("classes/activar_desactivar_oferta.php", {
            pId: indice,
            estado: arrayEstados[indice]
        },
        function (data) {
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
    check = '<input type="checkbox" id="' + indice + ' checkbox" class="control-oferta"';
    if (arrayEstados[indice] == 0)
        check += ' checked ';
    check += 'data-toggle="toggle">';
    $("#" + indice + 'div').html(check);
    $('.control-oferta').bootstrapToggle();
}

function eliminarOferta(pId) {
    alertify.confirm('Atención', "¿Seguro quieres eliminar esta oferta? Esta acción no puede deshacerse.",
        function () {
            var url = "classes/eliminar_oferta.php";
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
                cargarOfertasPropias('todos');
            });
        },
        function () {}
    ).set('labels', {
        ok: 'Sí',
        cancel: 'Cancelar'
    });

}

$("#btn_administrar").click(function () {
    admin = 1;
    $("#contenido_ofertas").fadeOut('slow');
    $("#contenido_admin").fadeIn('slow');
    cargarOfertasPropias('todos');
    //$('#btn_administrar').fadeOut('slow');
});

$("#btn_mostrar").click(function () {
    admin = 0;
    $("#contenido_admin").fadeOut('slow');
    $("#contenido_ofertas").fadeIn('slow');
    cargarOfertas('nuevos');
    //$('#btn_administrar').fadeIn('slow');
});

function agregarOferta() {
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
        $('#tipo_oferta').html(tipos);
    });
    $(".modal-title").html("<h3>Nueva Oferta</h3>");
    $("#form-agregar-oferta-pag-1").show();
    $("#form-agregar-oferta-pag-2").hide();
    $("#form-agregar-oferta-pag-3").hide();
    $(".formulario").each(function () {
        this.reset();
    });
    $("#descuento").parent().hide();
    $("#precio").parent().hide();
    $('#select_productos').select2();
    cargarProductos();
};

$("#tipo_oferta").change(function () {
    if ($("#tipo_oferta").val() == '1') {
        $("#descuento").parent().hide();
        $("#precio").addClass('required');
        $("#precio").addClass('numeric');
        $("#descuento").removeClass('required');
        $("#descuento").removeClass('numeric');
        $("#precio").parent().show();
    } else {
        $("#precio").parent().hide();
        $("#descuento").addClass('required');
        $("#descuento").addClass('numeric');
        $("#precio").removeClass('required');
        $("#precio").removeClass('numeric');
        $("#descuento").parent().show();
    }
});

function editarOferta(id, descripcion, tipo_oferta, descuento, precio, cantidad, f_inicio, f_termino, stock, producto) {
    $("#form-agregar-oferta-pag-1").show();
    $("#form-agregar-oferta-pag-2").hide();
    $("#form-agregar-oferta-pag-3").hide();
    $(".modal-title").html("<h3>Editar Oferta</h3>");
    $(".formulario").find(':input').each(function () {
        var elemento = this;
        if ($("#" + elemento.id).parent().hasClass('has-error')) {
            $("#" + elemento.id).parent().removeClass('has-error');
        }
    });
    idActual = id;
    $("#descripcion").val(descripcion);
    $("#cantidad").val(cantidad);
    $("#tipo_oferta option[value=" + tipo_oferta + "]").attr("selected", true);
    $("#tipo_oferta").val(tipo_oferta);
    if (tipo_oferta == 0) {
        $("#precio").parent().hide();
    } else {
        $("#descuento").parent().hide();
    }
    $("#precio").val(currencyFormat(precio, ''));
    $("#descuento").val(descuento);
    $("#f_inicio").val(f_inicio);
    $("#f_termino").val(f_termino);
    $(".stock").val(stock);
    cargarProductos(producto);
}

function cargarProductos(pId) {
    var url = 'classes/obtener_productos.php';
    var arrayParametros = ['codigo', 'nombre'];
    var selectProductos = '<option value="0"></option>';
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
        $("#select_productos").html(selectProductos);
        if (pId)
            $("#select_productos option[value=" + pId + "]").attr("selected", true);
    });
}

function avanzarRetrocederModal(pagina, seguir) {
    $("#form-agregar-oferta-pag-" + pagina).toggle();
    if (seguir) {
        pagina++;
        paginaActualForm++;
    } else {
        pagina--;
        paginaActualForm--;
    }
    $("#form-agregar-oferta-pag-" + pagina).toggle();
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
    idActual = 0;
    datos['descripcion'] = $("#descripcion").val();
    datos['cantidad'] = $("#cantidad").val();
    datos['tipo_oferta'] = $("#tipo_oferta").val();
    datos['precio'] = sanear_numero($("#precio").val());
    datos['descuento'] = $("#descuento").val();
    datos['f_inicio'] = $("#f_inicio").val();
    datos['f_termino'] = $("#f_termino").val();
    datos['stock'] = $("#stock").val();
    datos['producto'] = $("#select_productos").val();
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
                    habilitarDeshabilitarBoton('btn_aceptar_modal', true);
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
        var url = "classes/nueva_oferta.php";
        $.post(url, {
                id: datos['id'],
                descripcion: datos['descripcion'],
                cantidad: datos['cantidad'],
                tipo_oferta: datos['tipo_oferta'],
                descuento: datos['descuento'],
                precio: datos['precio'],
                f_inicio: datos['f_inicio'],
                f_termino: datos['f_termino'],
                stock: datos['stock'],
                producto: datos['producto'],
                tipo: datos['tipo'],
                alcance: datos['alcance']
            },
            function (data) {
                cargarOfertasPropias('todos');
                $("form-agregar-oferta-pag-1").show();
                $("form-agregar-oferta-pag-2").hide();
                $('#form-agregar-oferta').removeClass('md-show');
                $(".formulario").each(function () {
                    this.reset();
                });
                switch (data) {
                    case '0': 
                        mostrar_notificacion('Error', 'No se ha podido procesar su solicitud', 'danger');
                        break;
                    case '1':
                        mostrar_notificacion('Éxito', 'Solicitud procesada satisfactoriamente', 'success');
                        break;
                    case '3':
                        mostrar_notificacion('Atención', 'Se ha modificado la oferta sólo localmente', 'warning');
                        break;
                    default:
                        mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte a un administrador', 'danger');
                        break;
                }
            }
        ).done(function () {
            habilitarDeshabilitarBoton('btn_aceptar_modal', true);
        });
    }else{
        habilitarDeshabilitarBoton('btn_aceptar_modal', true);
    }
});

$("#contenido_admin").click(function () {
    $('.fa-pencil-square').modalEffects();
    $('.asignar_promocion').modalEffects();
});