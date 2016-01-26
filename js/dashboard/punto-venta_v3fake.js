var carrito = new Array();

$(document).ready(function () {
    cargando('product-body');
    $('.pay-button').modalEffects();
    $('.descuento-button').modalEffects();
    $('#medio-pago').multiselect();

    /* regularizar tamaño */
    $('.cuerpo').height($(window).height() - 80);
    $('.cuerpo2').height($(window).height() - 410);
    $('#categorías-content').height($(window).height() - 120);
    /* regularizar tamaño fin */

    var PRODUCTOS_JSON = new Array();
    var CATEGORIAS_JSON = new Array();
    var CATEGORIA_PRODUCTOS_JSON = new Array();
    var CLIENTES_EMPRESA = new Array();
    var PROMOCIONES_ESPECIALES = new Array();
    var PLANES_PAGO = new Array();
    var creditoDirecto = null;
    var bruto = 0;
    var discount = 0;
    var total = 0;

    $('.nfn-overlay').show();
    $.post('classes/ptv_getbase_categorias.php', function (data) {
        CATEGORIAS_JSON = jQuery.parseJSON(data);

    }).done(function () {
        $.post('classes/ptv_getbase_categoria_productos.php', function (data) {
            CATEGORIA_PRODUCTOS_JSON = jQuery.parseJSON(data);
        });

        $.post('classes/ptv_obtener_planes.php', function (data) {
            PLANES_PAGO = jQuery.parseJSON(data);
        }).done(function () {
            $.each(PLANES_PAGO, function (i, value) {
                $('#planes_pago').append('<option value="' + value.id + '">' + value.nombre + '</option>');
            });
        });

        $.post('classes/ptv_obtener_clientes.php', function (data) {
            CLIENTES_EMPRESA = jQuery.parseJSON(data);
        }).done(function () {
            $.each(CLIENTES_EMPRESA, function (i, value) {
                $('#selector_usuarios').append('<option value="' + value.linea + '">' + value.rut + '</option>');
            });

            console.log('Users OK');
        });

        $.post('classes/ptv_obtener_promociones_especiales.php', function (data) {
            PROMOCIONES_ESPECIALES = jQuery.parseJSON(data);
        }).done(function () {
            $.each(PROMOCIONES_ESPECIALES, function (i, value) {
                if (value.tipo == 1) {
                    $('#selector_promociones_especiales').append('<option value="' + value.id + '">' + value.nombre + ' (' + value.porcentaje + '% Dcto. sobre total)</option>');
                }

                if (value.tipo == 2) {
                    $('#selector_promociones_especiales').append('<option value="' + value.id + '">' + value.nombre + '</option>');
                }
            });

            console.log('Users OK');
        });

        $.post('classes/ptv_obtener_productos_total.php', function (data) {
            PRODUCTOS_JSON = jQuery.parseJSON(data);
        }).done(function () {
            cargarCategorias(0);
            $('#buscador').bind('input', function () {
                var buscado = this.value;
                buscado = buscado.replace('\'', '');
                buscado = buscado.replace('`', '');
                buscado = buscado.replace('ç', '');
                if (buscado == '')
                    cargarCategorias(0);
                else {
                    $('#productos-content').html('');
                    $.each(PRODUCTOS_JSON, function (i, v) {
                        if (v.nombre.search(new RegExp(buscado, 'gi')) != -1 && $('input[name="b-nombre"]').is(':checked')) {
                            var nombre = v.nombre;
                            nombre = nombre.replace(new RegExp(' ', 'g'), '<br>');
                            var codigo = v.codigo;
                            if (v.stock_r == 0) {
                                console.log(); //$("#productos-content").append('<button type="button" class="btn btn-default button-square nfn-text item-producto" tittle="description" data-modal="modal-agregar" value="' + codigo + '" style="color:red;" disabled><p style="font-size: 9px !important;line-height:0;margin:2px;color:#00a3e4;">SKU.' + codigo + '</p>' + nombre + '</button>');
                            } else if (parseInt(v.stock_r) <= parseInt(v.stock_m)) {
                                $('#productos-content').append('<button type="button" class="btn btn-default button-square nfn-text item-producto" tittle="description" data-modal="modal-agregar" value="' + codigo + '" style="color:red;" ><p style=" font-size: 9px !important;line-height:0;margin:2px;color:#00a3e4;">SKU.' + codigo + '</p>' + nombre + '</button>');
                            } else {
                                $('#productos-content').append('<button type="button" class="btn btn-default button-square nfn-text item-producto" tittle="description" data-modal="modal-agregar" value="' + codigo + '"><p style="font-size: 9px !important;line-height:0;margin:2px;color:#00a3e4;">SKU.' + codigo + '</p>' + nombre + '</button>');
                            }

                            $('#opacity-product-mask').fadeTo('fast', 1)
                            habilitarProducto();
                        } else if (v.desc.search(new RegExp(buscado, 'gi')) != -1 && $('input[name="b-descripcion"]').is(':checked')) {
                            var nombre = v.nombre;
                            nombre = nombre.replace(new RegExp(' ', 'g'), '<br>');
                            var codigo = v.codigo;
                            if (v.stock_r == 0) {
                                console.log(); //$("#productos-content").append('<button type="button" class="btn btn-default button-square nfn-text item-producto" tittle="description" data-modal="modal-agregar" value="' + codigo + '" style="color:red;" disabled><p style="font-size: 9px !important;line-height:0;margin:2px;color:#00a3e4;">SKU.' + codigo + '</p>' + nombre + '</button>');
                            } else if (parseInt(v.stock_r) <= parseInt(v.stock_m)) {
                                $('#productos-content').append('<button type="button" class="btn btn-default button-square nfn-text item-producto" tittle="description" data-modal="modal-agregar" value="' + codigo + '" style="color:red;" ><p style=" font-size: 9px !important;line-height:0;margin:2px;color:#00a3e4;">SKU.' + codigo + '</p>' + nombre + '</button>');
                            } else {
                                $('#productos-content').append('<button type="button" class="btn btn-default button-square nfn-text item-producto" tittle="description" data-modal="modal-agregar" value="' + codigo + '"><p style="font-size: 9px !important;line-height:0;margin:2px;color:#00a3e4;">SKU.' + codigo + '</p>' + nombre + '</button>');
                            }

                            $('#opacity-product-mask').fadeTo('fast', 1)
                            habilitarProducto();
                        } else if (v.codigo.search(new RegExp(buscado, 'gi')) != -1 && $('input[name="b-codigo"]').is(':checked')) {
                            var nombre = v.nombre;
                            nombre = nombre.replace(new RegExp(' ', 'g'), '<br>');
                            var codigo = v.codigo;

                            if (v.stock_r == 0) {
                                console.log(); //$("#productos-content").append('<button type="button" class="btn btn-default button-square nfn-text item-producto" tittle="description" data-modal="modal-agregar" value="' + codigo + '" style="color:red;" disabled><p style="font-size: 9px !important;line-height:0;margin:2px;color:#00a3e4;">SKU.' + codigo + '</p>' + nombre + '</button>');
                            } else if (parseInt(v.stock_r) <= parseInt(v.stock_m)) {
                                $('#productos-content').append('<button type="button" class="btn btn-default button-square nfn-text item-producto" tittle="description" data-modal="modal-agregar" value="' + codigo + '" style="color:red;" ><p style=" font-size: 9px !important;line-height:0;margin:2px;color:#00a3e4;">SKU.' + codigo + '</p>' + nombre + '</button>');
                            } else {
                                $('#productos-content').append('<button type="button" class="btn btn-default button-square nfn-text item-producto" tittle="description" data-modal="modal-agregar" value="' + codigo + '"><p style="font-size: 9px !important;line-height:0;margin:2px;color:#00a3e4;">SKU.' + codigo + '</p>' + nombre + '</button>');
                            }

                            $('#opacity-product-mask').fadeTo('fast', 1)
                            habilitarProducto();
                        }
                    });
                }
            });

            $('.nfn-overlay').hide();
        });
    });

    $('#buscador').keyup(function (e) {
        if (e.keyCode == 13) {
            var codigo = $('#buscador').val();
            codigo = codigo.replace('\'', '');
            codigo = codigo.replace('`', '');
            codigo = codigo.replace('ç', '');
            if (document.getElementById("cantidadItems" + codigo)) {
                var cantidad = parseInt($('#cantidadItems' + codigo).val());
                if (verificarStock(codigo, cantidad)) {
                    carrito.push(codigo);
                    carrito = jQuery.unique(carrito);
                    $('#cantidadItems' + codigo).val(cantidad + 1);
                    $('#cantidadItems' + codigo).change();
                    calcularTotal();
                    $('#buscador').val("");
                }
            } else {
                if (verificarStock(codigo, 1)) {
                    carrito.push(codigo);
                    carrito = jQuery.unique(carrito);

                    var producto = buscarProducto(codigo);
                    //console.log(producto);
                    $.each(producto, function (index, pro) {
                        $("#ptv-detalle").append('<tr><td class="td-left no-padding-nfn" width="30%">' + pro.nombre + '</td>' +
                            '<td class="td-center no-padding-nfn"  width="20%">' +
                            '<input id="cantidadItems' + codigo + '" type="text" value="1" size="2" name="cantidadItems"></td>' +
                            '<td id="cantidadItems' + codigo + '-precio" class="td-right no-padding-nfn" width="20%">' + currencyFormat(pro.precio_u, "$") + '</td>' +
                            '<td id="cantidadItems' + codigo + '-total" class="td-right no-padding-nfn" width="20%">' + currencyFormat(pro.precio_u, "$") + '</td></tr>');
                    });

                    $("input[name='cantidadItems']").TouchSpin();
                    $("input[name='cantidadItems']").change(function () {
                        if (verificarStock(codigo, $(this).val())) {
                            var precio = $("#" + this.id + "-precio").text();
                            precio = precio.replace('$', '');
                            precio = precio.replace('.', '');
                            precio = precio.replace('.', '');
                            var precioFinal = parseFloat(precio);
                            $("#" + this.id + "-total").html(currencyFormat(($(this).val() * precio), "$"));
                            calcularTotal();
                        } else {
                            stock = standAloneDB(PRODUCTOS_JSON, "codigo", this.id.replace('cantidadItems', ''), "stockReal");
                            $(this).val(stock[0]);
                            var precio = $("#" + this.id + "-precio").text();
                            precio = precio.replace('$', '');
                            precio = precio.replace('.', '');
                            precio = precio.replace('.', '');
                            var precioFinal = parseFloat(precio);
                            $("#" + this.id + "-total").html(currencyFormat(($(this).val() * precio), "$"));
                            calcularTotal();
                        }
                    });
                    calcularTotal();
                    $('#buscador').val("");
                }
            };
        }
    });




    $('.descuento-button').click(function () {
        change_index("#totales-container", 100);
    });

    $('.teclado').click(function () {
        console.log("pressed: " + $(this).html());
        if ($(this).html() == '<i class="fa fa-arrow-left"></i>') {
            $("#buscador").val($("#buscador").val().substring(0, $("#buscador").val().length - 1));
        } else if ($(this).html() == 'ENTER') {

            var codigo = $('#buscador').val();
            if (document.getElementById("cantidadItems" + codigo)) {
                var cantidad = parseInt($('#cantidadItems' + codigo).val());
                if (verificarStock(codigo, cantidad)) {
                    carrito.push(codigo);
                    carrito = jQuery.unique(carrito);
                    $('#cantidadItems' + codigo).val(cantidad + 1);
                    $('#cantidadItems' + codigo).change();
                    calcularTotal();
                    $('#buscador').val("");
                }
            } else {
                if (verificarStock(codigo, 1)) {
                    carrito.push(codigo);
                    carrito = jQuery.unique(carrito);

                    var producto = buscarProducto(codigo);
                    $.each(producto, function (index, pro) {
                        $("#ptv-detalle").append('<tr><td class="td-left no-padding-nfn" width="30%">' + pro.nombre + '</td>' +
                            '<td class="td-center no-padding-nfn"  width="20%">' +
                            '<input id="cantidadItems' + codigo + '" type="text" value="1" size="2" name="cantidadItems"></td>' +
                            '<td id="cantidadItems' + codigo + '-precio" class="td-right no-padding-nfn" width="20%">' + currencyFormat(pro.precio_u, "$") + '</td>' +
                            '<td id="cantidadItems' + codigo + '-total" class="td-right no-padding-nfn" width="20%">' + currencyFormat(pro.precio_u, "$") + '</td></tr>');
                    });

                    $("input[name='cantidadItems']").TouchSpin();
                    $("input[name='cantidadItems']").change(function () {
                        if (verificarStock(codigo, $(this).val())) {
                            var precio = $("#" + this.id + "-precio").text();
                            precio = precio.replace('$', '');
                            precio = precio.replace('.', '');
                            precio = precio.replace('.', '');
                            var precioFinal = parseFloat(precio);
                            $("#" + this.id + "-total").html(currencyFormat(($(this).val() * precio), "$"));
                            calcularTotal();
                        } else {
                            stock = standAloneDB(PRODUCTOS_JSON, "codigo", this.id.replace('cantidadItems', ''), "stockReal");
                            $(this).val(stock[0]);
                            var precio = $("#" + this.id + "-precio").text();
                            precio = precio.replace('$', '');
                            precio = precio.replace('.', '');
                            precio = precio.replace('.', '');
                            var precioFinal = parseFloat(precio);
                            $("#" + this.id + "-total").html(currencyFormat(($(this).val() * precio), "$"));
                            calcularTotal();
                        }
                    });
                    calcularTotal();
                    $('#buscador').val("");
                }
            }
        } else {
            $("#buscador").val($("#buscador").val() + $(this).html());
        }
    });
    //Pagar
    $(".pay-button").click(function () {

        change_index("#totales-container", 100);
        $("#total-modal").html(currencyFormat(getTotal(), "$"));
        $("#input-monto-pagar").focus();
    });

    function bind_monto() {
        $(".monto-pagar").bind("input", function () {
            var pagar = 0;
            $(".monto-pagar").each(function (i, item) {
                if (item.value != "")
                    pagar += parseInt(item.value);
                if (i == parseInt(creditoDirecto)) {
                    $('#planes_pago').change();
                }
            });
            //alert(pagar);
            var resto = total - parseInt(pagar);
            var mensaje = "";
            if (resto < 0) {
                mensaje = '<div class="alert alert-info alert-white rounded"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> <div class="icon"> <i class="fa fa-info-circle"> </i></div> <strong> Información: </strong>El vuelto a entregar es de:<h1>' + currencyFormat(resto * -1, "$") + '</h1> </div>';
                $("#btn-finalizar-compra").removeAttr("disabled");
            } else if (resto == 0) {
                mensaje = '<div class="alert alert-success alert-white rounded"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> <div class="icon"> <i class="fa fa-info-circle"> </i></div> <strong> ¡Listo!: </strong>Monto cubre el total.</div>';
                $("#btn-finalizar-compra").removeAttr("disabled");
            } else {
                mensaje = '<div class="alert alert-danger alert-white rounded"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> <div class="icon"> <i class="fa fa-info-circle"> </i></div> <strong> Información: </strong>Quedan por cancelar:<h1> ' + currencyFormat(resto, "$") + '</h1><br> <button class="btn btn-danger add-medio">Añadir otro medio de pago.</button> </div>';
                $("#btn-finalizar-compra").attr("disabled", "disabled");
            }

            $("#pay-message").html(mensaje);

            $(".add-medio").click(function () {
                $("#cuerpo-de-pago").append('<tr class="medios-extra">' +
                    '<td class="col-md-12">' +
                    '    <div class="input-group">' +
                    '        <span class="input-group-addon">$</span>' +
                    '        <input type="text" class="form-control monto-pagar" id="monto-medio-' + $('.medio-pago-text').length + '">' +
                    '        <div class="input-group-btn bs-dropdown-to-select-group">' +
                    '            <button type="button" class="btn btn-primary dropdown-toggle as-is bs-dropdown-to-select" data-toggle="dropdown" tabindex="-1">' +
                    '                <span class="medio-pago-text" data-bind="bs-drp-sel-label">Crédito</span>' +
                    '                <input type="hidden" name="country_path" data-bind="bs-drp-sel-value" value="Crédito">' +
                    '                <span class="caret"></span>' +
                    '                <span class="sr-only">Toggle Dropdown</span>' +
                    '            </button>' +
                    '            <ul class="dropdown-menu" role="menu" style=" max-height: 300px; overflow: scroll; overflow-y: scroll; overflow-x: hidden; ">' +
                    '                <!-- Loop -->' +
                    '                <li data-value="Efectivo" class="btn-medios-pago-' + $('.medio-pago-text').length + '"><a href="#">Efectivo</a></li>' +
                    '                <li data-value="Crédito" class="btn-medios-pago-' + $('.medio-pago-text').length + '"><a href="#">Crédito</a></li>' +
                    '                <li data-value="Débito" class="btn-medios-pago-' + $('.medio-pago-text').length + '"><a href="#">Débito</a></li>' +
                    '                <li data-value="Crédito Directo" class="btn-medios-pago-' + $('.medio-pago-text').length + '"><a href="#">Crédito Directo</a></li>' +
                    '                <!-- END Loop -->' +
                    '            </ul>' +
                    '        </div>' +
                    '    </div>' +
                    '</td>' +
                    '</tr>');
                bind_monto();
                inicializarCredito();
            });

        });
    }
    bind_monto();

    $("#btn-finalizar-compra").click(function () {
        // Medios de Pago completar - mediante ciclo
        var venta_medios = [];
        var ventaCredito = [];
        var cuotasCredito = [];
        var error_message = [];
        var error_bool = false;
        $('.medio-pago-text').each(function (index) {
            var monto_medio = $("#monto-medio-" + index).val();
            if (monto_medio == "") {
                error_bool = true;
                error_message.push('Debe ingresar todos los montos');
            }
            var medio_texto = $(this).text();
            var medio_id = 1;
            if (medio_texto == "Efectivo") {
                medio_id = 1;
            } else if (medio_texto == "Crédito") {
                medio_id = 2;
            } else if (medio_texto == "Débito") {
                medio_id = 3;
            } else if (medio_texto == "Crédito Directo") {
                medio_id = 4;
                lineaId = $('#selector_usuarios').val();
                planId = $('#planes_pago').val();

                if (parseInt(lineaId) != 0 && parseInt(planId) != 0) {
                    lineaCredito = getLinea(lineaId);
                    planPago = getPlan(planId);

                    var rutHabilitado = $('#habilitado_rut').val();
                    console.log('plan: ' + planPago.codigo + ' rutHabilitado: ' + rutHabilitado);
                    if (!VerificaRut(rutHabilitado) && planPago.codigo == 'habilitado') {
                        error_bool = true;
                        error_message.push('Debe ingresar un RUT de habilitado válido.');
                    }

                    var capital = parseInt($('#monto-medio-' + creditoDirecto).val());
                    var interes = (parseFloat(planPago.interes) / 100);
                    var periodos = parseInt(planPago.cuota);

                    var intereses = Math.ceil(capital * interes * periodos);
                    var total = Math.ceil(capital + intereses);
                    var cuota = Math.ceil(total / periodos);

                    if (lineaCredito.cupo < total) {
                        error_bool = true;
                        error_message.push('No dispone de cupo, para realizar esta compra');
                    }

                    ventaCredito = {
                        setVentaCredito: 1,
                        ventaCreditoId: 0,
                        lineaCreditoId: lineaId,
                        ventaCreditoEstado: 0,
                        ventaCreditoTotal: total,
                        ventaCreditoFechaOtorgada: getFecha(),
                        ventaCreditoTotalBruto: capital,
                        ventaCreditoTasaInteres: parseInt(planPago.interes),
                        ventaCreditoValorCuotaTotal: cuota
                    }

                    if (planPago.codigo == "habilitado") {
                        ventaCredito.ventaCreditoHabilitado = rutHabilitado;
                    }

                    cuotasCredito = {
                        setCuotasCredito: 1,
                        periodos: periodos,
                        ventaCreditoId: 0,
                        cuotaMonto: cuota,
                        cuotaFechaPago: lineaCredito.fechaPago,
                        cuotaFechaFacturacion: lineaCredito.facturacion,
                        cuotaEstado: 0,
                        cuotaFechaPagada: '0000-00-00'
                    }
                } else {
                    error_bool = true;
                    error_message.push('Debe Seleccionar un plan de pago y un Cliente');
                }
            }

            var medio = {
                venta_monto: monto_medio,
                venta_medio_pago_id: medio_id
            }
            venta_medios.push(medio);
        });
        if (!error_bool) {
            //Productos dentro del Carro - completar mediante ciclo
            var venta_productos = [];
            $.each(carrito, function (i, codigo) {
                $.each(getProductObject(codigo), function (i, venta_producto) {
                    venta_productos.push(venta_producto);
                });
            });
            //$("#pre-venta").hide();
            //$("#cargando-venta").show();
            var venta_JSON = {
                set_venta: 1,
                venta_bruto: bruto,
                venta_descuentos: discount,
                venta_neto: total
            };
            var test = 0;
            var tope = Math.floor((Math.random() * 10) + 3);
            for(test=0;test<tope;test++){
                //almacenar medios
                $.post("classes/ptv_procesar_venta.php", venta_JSON, function (data) {
                    var venta_id = data;

                    if (!$.isEmptyObject(ventaCredito)) {
                        ventaCredito.ventaCreditoId = parseInt(venta_id);

                        $.post("classes/ptv_procesar_credito.php", ventaCredito, function (data) {
                            console.log(data);
                        });

                    }
                    if (!$.isEmptyObject(cuotasCredito)) {
                        cuotasCredito.ventaCreditoId = parseInt(venta_id);

                        $.post("classes/ptv_procesar_credito.php", cuotasCredito, function (data) {
                            console.log(data);
                        });
                    }
                    //almacenar venta-productos
                    var count = venta_productos.length;
                    $.each(venta_productos, function (i, producto) {
                        producto.venta_id = venta_id;
                        producto.set_venta_producto = 1;
                        $.post("classes/ptv_procesar_venta.php", producto);
                    });
                    var count = venta_medios.length;
                    $.each(venta_medios, function (i, medio) {
                        medio.venta_id = venta_id;
                        medio.set_venta_medio = 1;
                        $.post("classes/ptv_procesar_venta.php", medio);
                        if (!--count) {
                            //venta_divs();
                            //recargar();

                        }
                    });
                });
            }
        } else {
            $.each(error_message, function (i, er) {
                mostrar_notificacion('Atención', er, 'danger');
            });
        }
    });

    function venta_divs() {
        $('#credito-datos').hide();
        $("#pre-venta").hide();
        $("#cargando-venta").hide();
        $("#post-venta").show();
        $("#btn-finalizar-compra").hide();
        $("#btn-finalizar-compra-2").show();
        $("#btn-finalizar-compra-close").hide();
    }

    $(document).on('click', '.bs-dropdown-to-select-group .dropdown-menu li', function (event) {
        var $target = $(event.currentTarget);
        $target.closest('.bs-dropdown-to-select-group')
            .find('[data-bind="bs-drp-sel-value"]').val($target.attr('data-value'))
            .end()
            .children('.dropdown-toggle').dropdown('toggle');
        $target.closest('.bs-dropdown-to-select-group')
            .find('[data-bind="bs-drp-sel-label"]').text($target.attr('data-value'));
        return false;
    });

    function cargarCategorias(padre) {
        $("#categorías-content").html("");
        var lastID = CATEGORIAS_JSON.length - 1;

        $.each(CATEGORIAS_JSON, function (index, categoria) {
            if (parseInt(categoria.cat_padre) == parseInt(padre)) {
                categoria_nombre = categoria.cat_nombre;
                categoria_nombre = categoria_nombre.replace(new RegExp(" ", "g"), "<br>");
                total_productos = contar_productos(categoria.cat_id);
                mensaje = "Categoría NO contiene productos";
                if (total_productos > 0)
                    mensaje = "Contiene " + total_productos + " productos";
                icono = "";
                if (padre == 0)
                    icono = '<i class="nfn nfn-' + categoria.cat_descripcion + '"></i><br>';
                $("#categorías-content").append('<button type="button" class="btn btn-primary button-square-2 nfn-text item-categoria"  data-toggle="tooltip" data-placement="right" title="' + mensaje + '" value="' + categoria.cat_id + '">' + icono + categoria_nombre + '</button><br>');
            }
            if (index == lastID) {
                $(".item-categoria").click(function () {
                    cargarCategorias(this.value);
                    cargar_productos(this.value);

                });
                $(".item-categoria").tooltip();
            }
        });
        $("#categorías-content").append('<button class="btn btn-default  button-square-2 nfn-text"  id="btn-volver"><i class="fa fa-reply"></i><br>VOLVER</button>');
        activar_volver();
        if (padre == 0) {
            $("#opacity-product-mask").fadeTo("fast", 1);
            $("#productos-content").html("");
        }
    }

    function cargar_productos(categoria) {
        cargando("product-body");
        $("#productos-content").html("");
        var lastID = CATEGORIA_PRODUCTOS_JSON.length - 1;
        var total = 0;
        $.each(CATEGORIA_PRODUCTOS_JSON, function (index, categoria_producto) {
            if (parseInt(categoria_producto.cat_id) == parseInt(categoria)) {
                var id = categoria_producto.pro_id;
                var nombre = "";
                var codigo = "";
                $.each(PRODUCTOS_JSON, function (index2, producto) {

                    if (parseInt(producto.id) == parseInt(id)) {
                        nombre = producto.nombre;
                        codigo = producto.codigo;
                        total++;
                        //console.log("total: " + total);
                        nombre = nombre.replace(new RegExp(" ", "g"), "<br>");
                        if (producto.stock_r == 0) {
                            //    $("#productos-content").append('<button type="button" class="btn btn-default button-square nfn-text item-producto" tittle="description" data-modal="modal-agregar" value="' + codigo + '" style="color:red;" disabled><p style="font-size: 9px !important;line-height:0;margin:2px;color:#00a3e4;">SKU.' + codigo + '</p>' + nombre + '</button>');


                        } else if (parseInt(producto.stock_r) <= parseInt(producto.stock_m)) {
                            $("#productos-content").append('<button type="button" class="btn btn-default button-square nfn-text item-producto" tittle="description" data-modal="modal-agregar" value="' + codigo + '" style="color:red;" ><p style=" font-size: 9px !important;line-height:0;margin:2px;color:#00a3e4;">SKU.' + codigo + '</p>' + nombre + '</button>');
                        } else {
                            $("#productos-content").append('<button type="button" class="btn btn-default button-square nfn-text item-producto" tittle="description" data-modal="modal-agregar" value="' + codigo + '"><p style="font-size: 9px !important;line-height:0;margin:2px;color:#00a3e4;">SKU.' + codigo + '</p>' + nombre + '</button>');
                        }
                    }
                });

            }

            if (index == lastID) {

                habilitarProducto();
            }
        });
        if (total > 0) {
            $("#opacity-product-mask").fadeTo("fast", 0);
        } else {
            $("#productos-content").html('<br><button type="button" class="btn btn-default button-square nfn-text item-producto" tittle="description" data-modal="modal-agregar"  style="color:red; width:100% !important; opacity:1 !important;" disabled>NO EXISTEN PRODUCTOS<br>EN ESTA CATEGORÍA</button>');
            $("#opacity-product-mask").fadeTo("fast", 1);
        }

    }


    function contar_productos(categoria) {
        var total = 0;
        $.each(CATEGORIA_PRODUCTOS_JSON, function (index, categoria_producto) {
            if (parseInt(categoria_producto.cat_id) == parseInt(categoria)) {
                var id = categoria_producto.pro_id;
                $.each(PRODUCTOS_JSON, function (index2, producto) {

                    if (parseInt(producto.id) == parseInt(id) && parseInt(producto.stock_r) > 0) {
                        total++;

                    }
                });

            }
        });
        return total;
    }

    function habilitarProducto() {

        $('.item-producto').modalEffects();
        $(".item-producto").click(function () {
            change_index("#totales-container", 9999);
            var codigo = this.value;
            var resultado = buscarProducto(codigo);
            $.each(resultado, function (index, pro) {
                $("#product-title").html("<span style='display: inline-block;'><h2>" + pro.nombre + "</h2><h5>" + pro.modelo + "&nbsp&nbsp&nbsp<b>COD. " + pro.codigo + "</b></h5></span>");
                $("#product-body").html("");
                var texto_descripcion = pro.desc;
                if (texto_descripcion.length > 150)
                    texto_descripcion = texto_descripcion.slice(0, 150);
                $("#product-body").append(texto_descripcion + "<br><br>");
                $("#product-body").append("<b><h3>Precio: " + currencyFormat(pro.precio_u, "$") + "</h3></b><br>");
                $("#product-body").append("<b>Stock: " + currencyFormat(pro.stock_r, "") + "</b><br>");
                $("#product-add").val(codigo);
            });
        });
    }

    function cargando(div) {
        $("#" + div).html('<i class="fa fa-spinner fa-spin fa-2x"></i><h4>Cargando...</h4>');

    }

    function currencyFormat(num, extra) {
        if (isNumber(num) == true)
            return extra + num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")
        return "no definido";
    }

    function isNumber(input) {
        return (input - 0) == input && ('' + input).replace(/^\s+|\s+$/g, "").length > 0;
    }

    $("#total-modal").click(function () {
        var aPagar = $("#total-modal").html();
        aPagar = aPagar.replace('$', '');
        aPagar = aPagar.replace('.', '');
        aPagar = aPagar.replace('.', '');
        $("#monto-medio-0").val(aPagar);
        $(".monto-pagar").trigger("input");
    });
    $("#product-add").click(function () {
        var codigo = this.value;

        if (document.getElementById("cantidadItems" + codigo)) {
            carrito.push(codigo);
            carrito = jQuery.unique(carrito);
            var cantidad = parseInt($('#cantidadItems' + codigo).val());
            if (verificarStock(codigo, cantidad)) {
                $('#cantidadItems' + codigo).val(cantidad + 1);
                $('#cantidadItems' + codigo).change();
                calcularTotal();
            }
        } else {
            if (verificarStock(codigo, 1)) {
                carrito.push(codigo);
                carrito = jQuery.unique(carrito);

                var producto = buscarProducto(codigo);
                $.each(producto, function (index, pro) {
                    $("#ptv-detalle").append('<tr><td class="td-left no-padding-nfn" width="30%">' + pro.nombre + '</td>' +
                        '<td class="td-center no-padding-nfn" width="20%">' +
                        '<input id="cantidadItems' + codigo + '" type="text" value="1" size="10" name="cantidadItems"></td>' +
                        '<td id="cantidadItems' + codigo + '-precio" class="td-right no-padding-nfn" width="20%">' + currencyFormat(pro.precio_u, "$") + '</td>' +
                        '<td id="cantidadItems' + codigo + '-total" class="td-right no-padding-nfn" width="20%">' + currencyFormat(pro.precio_u, "$") + '</td></tr>');
                });

                $("input[name='cantidadItems']").TouchSpin();
                $("input[name='cantidadItems']").change(function () {
                    if (verificarStock(codigo, $(this).val())) {
                        var precio = $("#" + this.id + "-precio").text();
                        precio = precio.replace('$', '');
                        precio = precio.replace('.', '');
                        precio = precio.replace('.', '');
                        var precioFinal = parseFloat(precio);
                        $("#" + this.id + "-total").html(currencyFormat(($(this).val() * precio), "$"));
                        calcularTotal();
                    } else {
                        stock = standAloneDB(PRODUCTOS_JSON, "codigo", this.id.replace('cantidadItems', ''), "stockReal");
                        $(this).val(stock[0]);
                        var precio = $("#" + this.id + "-precio").text();
                        precio = precio.replace('$', '');
                        precio = precio.replace('.', '');
                        precio = precio.replace('.', '');
                        var precioFinal = parseFloat(precio);
                        $("#" + this.id + "-total").html(currencyFormat(($(this).val() * precio), "$"));
                        calcularTotal();
                    }
                });

                calcularTotal();
            }
        };
    });

    function verificarStock(codigo, cantidad) {
        stock = standAloneDB(PRODUCTOS_JSON, "codigo", codigo, "stockReal");
        if (stock.length == 0 || parseInt(stock[0]) < cantidad) {
            return false;
        }
        return true;
    }

    function calcularTotal() {
        total = 0;
        $.each(carrito, function (key, val) {
            codigo = carrito[key];
            precio = $("#cantidadItems" + codigo + "-total").text();
            precio = precio.replace('$', '');
            precio = precio.replace('.', '');
            precio = precio.replace('.', '');
            total += parseInt(precio);
        });
        $("#sub-total").html(currencyFormat(total, "$"));
        bruto = total;
        calcularDescuento(total);
    }

    function calcularDescuento(total) {
        var descuento = 0;
        $("#descuentos-detalle").html("");
        $.each(carrito, function (key, val) {
            codigo = carrito[key];
            cantidad = parseInt($("#cantidadItems" + codigo).val());
            promociones = standAloneDB(PRODUCTOS_JSON, "codigo", codigo, "promociones");
            $.each(promociones[0], function (index, promo) {
                if (parseInt(promo.promo_tipo) == 1 && cantidad >= parseInt(promo.promo_cantidad)) {
                    var promo_qty = parseInt(cantidad / parseInt(promo.promo_cantidad));
                    descuento += parseInt(promo.promo_descontar) * promo_qty;
                    cantidad -= promo_qty * parseInt(promo.promo_cantidad);
                    $("#descuentos-detalle").append('<tr><td>' + promo.promo_descripcion + '</td><td class="text-center">' + (promo_qty * parseInt(promo.promo_cantidad)) + '</td><td class="text-center">' + currencyFormat(((parseInt(promo.promo_descontar) + parseInt(promo.promo_precio)) * promo_qty), "$") + '</td><td class="text-center primary-emphasis"><b>' + currencyFormat((parseInt(promo.promo_descontar) * promo_qty), "$") + '</b></td><td class="text-center">' + currencyFormat(parseInt(promo.promo_precio) * promo_qty, "$") + '</td></tr>');
                }

            });
            $.each(promociones[0], function (index, promo) {
                if (parseInt(promo.promo_tipo) == 2 && cantidad > 0) {
                    descuento += parseInt(promo.promo_descontar) * cantidad;
                    $("#descuentos-detalle").append('<tr><td>' + promo.promo_descripcion + '</td><td class="text-center">' + cantidad + '</td><td class="text-center">' + currencyFormat(((parseInt(promo.promo_descontar) / parseInt(promo.promo_descuento)) * 100 * cantidad), "$") + '</td><td class="text-center primary-emphasis"><b>' + currencyFormat((parseInt(promo.promo_descontar) * cantidad), "$") + '</b></td><td class="text-center">' + currencyFormat((((parseInt(promo.promo_descontar) / parseInt(promo.promo_descuento)) * 100 * cantidad) - (promo.promo_descontar * cantidad)), "$") + '</td></tr>');
                }

            });

        });
        discount = descuento;
        $("#descuentos").html(currencyFormat(descuento, "$"));
        $("#precio-total").html(currencyFormat(total - descuento, "$"));
    }

    function getPromo(codigo) {
        cantidad = parseInt($("#cantidadItems" + codigo).val());
        var promo_id = 0;
        var promo_qty = cantidad;
        promociones = standAloneDB(PRODUCTOS_JSON, "codigo", codigo, "promociones");
        $.each(promociones[0], function (index, promo) {
            promo_qty = parseInt(cantidad / parseInt(promo.promo_cantidad));
            promo_qty = promo_qty * promo.promo_cantidad;
            if (parseInt(promo.promo_tipo) == 1 && cantidad >= parseInt(promo.promo_cantidad)) {
                promo_id = parseInt(promo.promo_id);
            }
        });

        $.each(promociones[0], function (index, promo) {
            if (parseInt(promo.promo_tipo) == 2 && cantidad > 0) {
                promo_id = parseInt(promo.promo_id);
            }
        });
        var promo_final = {
            promo_id: promo_id,
            promo_cantidad: promo_qty
        }
        return promo_final;
    }

    function getTotal() {
        var parcial = $("#precio-total").html();
        parcial = parcial.replace('$', '');
        parcial = parcial.replace('.', '');
        parcial = parcial.replace('.', '');
        total = parseInt(parcial);
        return total;
    }

    function standAloneDB(objeto, propiedad, buscado, resultado) {
        var result = new Array();
        $.each(objeto, function (key, val) {
            if (val[propiedad] == buscado) {
                result.push(val[resultado]);
            }
        });
        return result;
    }

    function buscarProducto(buscado) {
        var producto = Array();
        $.each(PRODUCTOS_JSON, function (key, producto_buscado) {
            if (producto_buscado["codigo"] == buscado) {
                producto.push(producto_buscado);
            }
        });
        return producto;
    }

    function getProductObject(codigo, promocion) {
        var producto = [];
        cantidad = parseInt($("#cantidadItems" + codigo).val());
        $.each(PRODUCTOS_JSON, function (key, producto_buscado) {
            if (producto_buscado["codigo"] == codigo) {
                var promo_datos = getPromo(codigo);
                if (cantidad == promo_datos.promo_cantidad) {
                    casiproducto = {
                        producto_id: producto_buscado.id,
                        producto_precio: producto_buscado.precio_u,
                        producto_cantidad: promo_datos.promo_cantidad,
                        producto_promocion: promo_datos.promo_id
                    };
                    producto.push(casiproducto);
                } else if (cantidad > promo_datos.promo_cantidad && promo_datos.promo_cantidad != 0) {
                    var casiproducto = {
                        producto_id: producto_buscado.id,
                        producto_precio: producto_buscado.precio_u,
                        producto_cantidad: promo_datos.promo_cantidad,
                        producto_promocion: promo_datos.promo_id
                    }
                    producto.push(casiproducto);
                    casiproducto = {
                        producto_id: producto_buscado.id,
                        producto_precio: producto_buscado.precio_u,
                        producto_cantidad: cantidad - promo_datos.promo_cantidad,
                        producto_promocion: 0
                    };
                    producto.push(casiproducto);
                } else {
                    casiproducto = {
                        producto_id: producto_buscado.id,
                        producto_precio: producto_buscado.precio_u,
                        producto_cantidad: cantidad,
                        producto_promocion: 0
                    };
                    producto.push(casiproducto);
                }
            }
        });
        return producto;
    }




    //BANNER LOOP
    var imagen = 2;

    var tid = setTimeout(mycode, 10000);

    function mycode() {
        $("#banners").html('<br><img src="src/banner_' + imagen + '.jpg" class="img-responsive" />');
        if (imagen < 7)
            imagen++;
        else
            imagen = 1;
        tid = setTimeout(mycode, 10000);
    }

    function change_index(div, index) {
        $(div).zIndex(index);
    }

    function recargar() {
        $(".nfn-overlay").show();
        $('.medio-pago-text').each(function (index) {
            $(this).html('<span class="medio-pago-text" data-bind="bs-drp-sel-label">Efectivo</span>');
        });
        $('#t_a_pagar').html('<i class="fa fa-spinner fa-spin">');
        $('#c_v_cuota').html('<i class="fa fa-spinner fa-spin">');
        $('#habilitado_place').hide();
        $.post('classes/ptv_obtener_clientes.php', function (data) {
            CLIENTES_EMPRESA = jQuery.parseJSON(data);
        }).done(function () {
            $('#selector_usuarios').html('<option value="0">Buscar Rut</option>');
            $.each(CLIENTES_EMPRESA, function (i, value) {
                $('#selector_usuarios').append('<option value="' + value.linea + '">' + value.rut + '</option>');
            });
            $('#selector_usuarios').val("0").trigger("change");
            $('#planes_pago').val("0");
        });
        $.post("classes/ptv_obtener_productos_total.php", function (data) {
            PRODUCTOS_JSON = jQuery.parseJSON(data);
        }).done(function () {
            $("#buscador").bind("input", function () {
                var buscado = this.value;
                if (buscado == "")
                    cargarCategorias(0);
                else {
                    $("#productos-content").html("");

                    $.each(PRODUCTOS_JSON, function (i, v) {

                        if (v.nombre.search(new RegExp(buscado, "gi")) != -1 && $('input[name="b-nombre"]').is(':checked')) {

                            var nombre = v.nombre;
                            nombre = nombre.replace(new RegExp(" ", "g"), "<br>");
                            var codigo = v.codigo;


                            if (v.stock_r == 0) {
                                //$("#productos-content").append('<button type="button" class="btn btn-default button-square nfn-text item-producto" tittle="description" data-modal="modal-agregar" value="' + codigo + '" style="color:red;" disabled><p style="font-size: 9px !important;line-height:0;margin:2px;color:#00a3e4;">SKU.' + codigo + '</p>' + nombre + '</button>');


                            } else if (parseInt(v.stock_r) <= parseInt(v.stock_m)) {
                                $("#productos-content").append('<button type="button" class="btn btn-default button-square nfn-text item-producto" tittle="description" data-modal="modal-agregar" value="' + codigo + '" style="color:red;" ><p style=" font-size: 9px !important;line-height:0;margin:2px;color:#00a3e4;">SKU.' + codigo + '</p>' + nombre + '</button>');
                            } else {
                                $("#productos-content").append('<button type="button" class="btn btn-default button-square nfn-text item-producto" tittle="description" data-modal="modal-agregar" value="' + codigo + '"><p style="font-size: 9px !important;line-height:0;margin:2px;color:#00a3e4;">SKU.' + codigo + '</p>' + nombre + '</button>');
                            }

                            $("#opacity-product-mask").fadeTo("fast", 1)
                            habilitarProducto();
                        } else if (v.desc.search(new RegExp(buscado, "gi")) != -1 && $('input[name="b-descripcion"]').is(':checked')) {

                            var nombre = v.nombre;
                            nombre = nombre.replace(new RegExp(" ", "g"), "<br>");
                            var codigo = v.codigo;


                            if (v.stock_r == 0) {
                                // $("#productos-content").append('<button type="button" class="btn btn-default button-square nfn-text item-producto" tittle="description" data-modal="modal-agregar" value="' + codigo + '" style="color:red;" disabled><p style="font-size: 9px !important;line-height:0;margin:2px;color:#00a3e4;">SKU.' + codigo + '</p>' + nombre + '</button>');


                            } else if (parseInt(v.stock_r) <= parseInt(v.stock_m)) {
                                $("#productos-content").append('<button type="button" class="btn btn-default button-square nfn-text item-producto" tittle="description" data-modal="modal-agregar" value="' + codigo + '" style="color:red;" ><p style=" font-size: 9px !important;line-height:0;margin:2px;color:#00a3e4;">SKU.' + codigo + '</p>' + nombre + '</button>');
                            } else {
                                $("#productos-content").append('<button type="button" class="btn btn-default button-square nfn-text item-producto" tittle="description" data-modal="modal-agregar" value="' + codigo + '"><p style="font-size: 9px !important;line-height:0;margin:2px;color:#00a3e4;">SKU.' + codigo + '</p>' + nombre + '</button>');
                            }
                            $("#opacity-product-mask").fadeTo("fast", 1)
                            habilitarProducto();
                        } else if (v.codigo.search(new RegExp(buscado, "gi")) != -1 && $('input[name="b-codigo"]').is(':checked')) {

                            var nombre = v.nombre;
                            nombre = nombre.replace(new RegExp(" ", "g"), "<br>");
                            var codigo = v.codigo;


                            if (v.stock_r == 0) {
                                //$("#productos-content").append('<button type="button" class="btn btn-default button-square nfn-text item-producto" tittle="description" data-modal="modal-agregar" value="' + codigo + '" style="color:red;" disabled><p style="font-size: 9px !important;line-height:0;margin:2px;color:#00a3e4;">SKU.' + codigo + '</p>' + nombre + '</button>');


                            } else if (parseInt(v.stock_r) <= parseInt(v.stock_m)) {
                                $("#productos-content").append('<button type="button" class="btn btn-default button-square nfn-text item-producto" tittle="description" data-modal="modal-agregar" value="' + codigo + '" style="color:red;" ><p style=" font-size: 9px !important;line-height:0;margin:2px;color:#00a3e4;">SKU.' + codigo + '</p>' + nombre + '</button>');
                            } else {
                                $("#productos-content").append('<button type="button" class="btn btn-default button-square nfn-text item-producto" tittle="description" data-modal="modal-agregar" value="' + codigo + '"><p style="font-size: 9px !important;line-height:0;margin:2px;color:#00a3e4;">SKU.' + codigo + '</p>' + nombre + '</button>');
                            }
                            $("#opacity-product-mask").fadeTo("fast", 1)
                            habilitarProducto();
                        }
                    });
                    //$("#categorías-content").append('<button class="btn btn-default  button-square nfn-text"  id="btn-volver">VOLVER</button>');
                    //activar_volver();
                }

            });
            $('#credito-datos').hide();
            $(".nfn-overlay").hide();
        });
    }

    function activar_volver() {
        $("#btn-volver").click(function () {
            cargarCategorias(0);
        });
    }

    /* Control keys START */
    $(document).keydown(function (event) {
        var ctrl = window.event.ctrlKey;
        search_state = $("#buscador").val();
        var pagar = 0;
        $(".monto-pagar").each(function (i, item) {
            if (item.value != "")
                pagar += parseInt(item.value);
        });
        var resto = total - parseInt(pagar);

        //if (!$("#modal-pagar").css('visibility') == 'hidden')
        //  console.log("modal visible");
        if (!$('input:focus').length > 0 && event.which == 73) {
            event.preventDefault();
            $("#buscador").focus();
        } else if (search_state == "" && event.which == 13 && $("#modal-pagar").css('visibility') == 'hidden') {
            event.preventDefault();
            $(".pay-button").click();
            $("#monto-medio-0").focus();

        } else if ($("#total-modal").html().indexOf("$") > -1 && $("#modal-pagar").css('visibility') == 'visible' && event.which == 13 && resto > 0 && $("#btn-finalizar-compra-2").css('display') == 'none') {
            event.preventDefault();
            var aPagar = $("#total-modal").html();
            aPagar = aPagar.replace('$', '');
            aPagar = aPagar.replace('.', '');
            aPagar = aPagar.replace('.', '');
            $("#monto-medio-0").val(aPagar);
            $(".monto-pagar").trigger("input");
        } else if ($("#total-modal").html().indexOf("$") > -1 && $("#modal-pagar").css('visibility') == 'visible' && event.which == 13 && resto <= 0 && $("#btn-finalizar-compra-2").css('display') == 'none') {
            event.preventDefault();
            $("#btn-finalizar-compra").click();
        } else if ($("#total-modal").html().indexOf("$") > -1 && $("#modal-pagar").css('visibility') == 'visible' && event.which == 13 && resto <= 0 && $("#btn-finalizar-compra-2").css('display') == 'inline-block') {
            event.preventDefault();
            $("#btn-finalizar-compra-2").click();
        }

    });
    /* Control keys END*/

    $(window).resize(function () {
        //regularizar tamaño
        $('.cuerpo').height($(window).height() - 80);
        $('.cuerpo2').height($(window).height() - 410);
    });

    function inicializarCredito() {
        console.log("inicializando crédito");
        totalMedios = $('.medio-pago-text').length;
        for (i = 0; i < totalMedios; i++) {
            $('.btn-medios-pago-' + i).click(function () {
                elegido = $(this).html().replace('<a href="#">', '').replace('</a>', '');
                elegido = elegido.replace(/^\s+/, '').replace(/\s+$/, '');
                creditoDirecto = $(this).attr('class').replace('btn-medios-pago-', '');
                console.log(elegido);
                if (elegido == 'Crédito Directo') {
                    $('#credito-datos').show();
                } else {
                    $('#credito-datos').hide();
                }
            });
        }
    }

    inicializarCredito();

    $('#selector_usuarios').change(function () {
        linea = $(this).val();
        noEncontrado = true;
        $.each(CLIENTES_EMPRESA, function (i, cliente) {
            if (cliente.linea == linea) {
                noEncontrado = false;
                $('#c_nombre_completo').html(cliente.nombre + ' ' + cliente.paterno + ' ' + cliente.materno);
                $('#c_monto').html(currencyFormat(cliente.cupo, '$'));
            }
        });

        if (noEncontrado) {
            $('#c_nombre_completo').html('<i class="fa fa-spinner fa-spin"></i>');
            $('#c_monto').html('<i class="fa fa-spinner fa-spin"></i>');
        }
    });

    $('#planes_pago').change(function () {
        seleccionado = $(this).val();
        noEncontrado = true;
        $.each(PLANES_PAGO, function (i, plan) {
            if (plan.id == seleccionado) {
                noEncontrado = false;

                var capital = parseInt($('#monto-medio-' + creditoDirecto).val());
                var interes = (parseFloat(plan.interes) / 100);
                var periodos = parseInt(plan.cuota);
                var intereses = Math.ceil(capital * interes * periodos);
                var total = Math.ceil(capital + intereses);
                var cuota = Math.ceil(total / periodos);

                $('#t_a_pagar').html(currencyFormat(total, "$"));
                $('#c_v_cuota').html(currencyFormat(cuota, "$"));
                if (plan.codigo == 'habilitado') {
                    $('#habilitado_place').show();
                } else {
                    $('#habilitado_place').hide();
                }
            }

        });

        if (noEncontrado) {
            $('#c_v_cuota').html('<i class="fa fa-spinner fa-spin"></i>');
        }
    });

    function getLinea(id) {
        var clienteFound = [];
        $.each(CLIENTES_EMPRESA, function (key, cliente) {
            if (cliente.linea == id) {
                console.log(cliente);
                clienteFound = cliente;
            }
        });
        return clienteFound;
    }

    function getPlan(id) {
        planFound = [];
        $.each(PLANES_PAGO, function (key, plan) {
            if (plan.id == parseInt(id)) {
                planFound = plan;
            }
        });
        return planFound;
    }

    function getFecha() {
        var d = new Date();
        var month = d.getMonth() + 1;
        var day = d.getDate();
        var output = d.getFullYear() + '-' +
            (('' + month).length < 2 ? '0' : '') + month + '-' +
            (('' + day).length < 2 ? '0' : '') + day;
        return output;
    }

    function VerificaRut(rut) {
        if (rut.toString().trim() != '' && rut.toString().indexOf('-') > 0) {
            var caracteres = new Array();
            var serie = new Array(2, 3, 4, 5, 6, 7);
            var dig = rut.toString().substr(rut.toString().length - 1, 1);
            rut = rut.toString().substr(0, rut.toString().length - 2);

            for (var i = 0; i < rut.length; i++) {
                caracteres[i] = parseInt(rut.charAt((rut.length - (i + 1))));
            }

            var sumatoria = 0;
            var k = 0;
            var resto = 0;

            for (var j = 0; j < caracteres.length; j++) {
                if (k == 6) {
                    k = 0;
                }
                sumatoria += parseInt(caracteres[j]) * parseInt(serie[k]);
                k++;
            }

            resto = sumatoria % 11;
            dv = 11 - resto;

            if (dv == 10) {
                dv = "K";
            } else if (dv == 11) {
                dv = 0;
            }

            if (dv.toString().trim().toUpperCase() == dig.toString().trim().toUpperCase())
                return true;
            else
                return false;
        } else {
            return false;
        }
    }

});

function vaciar() {
    $('#ptv-detalle').html('');
    $('.monto-pagar').each(function (i, item) {
        item.value = '';
    });

    $('.medios-extra').remove();
    $('#pay-message').html('');
    $('#pre-venta').html();
    $('#pre-venta').show();
    $('#cargando-venta').hide();
    $('#post-venta').hide();
    $('#modal-pagar').removeClass('md-show');
    $('#btn-finalizar-compra').show();
    $('#btn-finalizar-compra').prop('disabled', true);
    $('#btn-finalizar-compra-2').hide();
    $('#btn-finalizar-compra-close').show();
    $('#sub-total').html('');
    $('#descuentos').html('');
    $('#precio-total').html('');
    carrito = [];

}