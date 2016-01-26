var carrito = new Array();

$(document).ready(function () {


    cargando("product-body");
    $('.pay-button').modalEffects();
    $('.descuento-button').modalEffects();
    $('#medio-pago').multiselect();
    //regularizar tamaño
    $('.cuerpo').height($(window).height() - 80);
    $('.cuerpo2').height($(window).height() - 410);
    var productos_json = new Array();
    var categorias_json = new Array();
    var categoria_productos_json = new Array();


    var bruto = 0;
    var discount = 0;
    var total = 0;
    $(".nfn-overlay").show();
    $.post("classes/ptv_getbase_categorias.php", function (data) {
        categorias_json = jQuery.parseJSON(data);

    }).done(function () {
        $.post("classes/ptv_getbase_categoria_productos.php", function (data) {
            categoria_productos_json = jQuery.parseJSON(data);
        });


        $.post("classes/ptv_obtener_productos_total.php", function (data) {
            productos_json = jQuery.parseJSON(data);
        }).done(function () {
            cargar_categorias(0);
            $("#buscador").bind("input", function () {
                var buscado = this.value;
                if (buscado == "")
                    cargar_categorias(0);
                else {
                    $("#productos-content").html("");
                    //alert(buscado);

                    $.each(productos_json, function (i, v) {

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
                                //$("#productos-content").append('<button type="button" class="btn btn-default button-square nfn-text item-producto" tittle="description" data-modal="modal-agregar" value="' + codigo + '" style="color:red;" disabled><p style="font-size: 9px !important;line-height:0;margin:2px;color:#00a3e4;">SKU.' + codigo + '</p>' + nombre + '</button>');


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
            $(".nfn-overlay").hide();
        });
    });
    $('#buscador').keyup(function (e) {
        if (e.keyCode == 13) {
            var codigo = $('#buscador').val();
            if (document.getElementById("cantidadItems" + codigo)) {
                var cantidad = parseInt($('#cantidadItems' + codigo).val());
                if (verificarStock(codigo, cantidad)) {
                    carrito.push(codigo);
                    carrito = jQuery.unique(carrito);
                    //console.log("cantidad: " + cantidad);
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
                            stock = standAloneDB(productos_json, "codigo", this.id.replace('cantidadItems', ''), "stock_r");
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
        console.log("pressed: "+$(this).html());
        if ($(this).html()=='<i class="fa fa-arrow-left"></i>'){
            $("#buscador").val($("#buscador").val().substring(0,$("#buscador").val().length-1));
        }
        else if($(this).html()=='ENTER'){

            var codigo = $('#buscador').val();
            if (document.getElementById("cantidadItems" + codigo)) {
                var cantidad = parseInt($('#cantidadItems' + codigo).val());
                if (verificarStock(codigo, cantidad)) {
                    carrito.push(codigo);
                    carrito = jQuery.unique(carrito);
                    //console.log("cantidad: " + cantidad);
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
                            stock = standAloneDB(productos_json, "codigo", this.id.replace('cantidadItems', ''), "stock_r");
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
        }
        else{
            $("#buscador").val($("#buscador").val()+ $(this).html());
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
                    '                <li data-value="Efectivo"><a href="#">Efectivo</a>' +
                    '                </li>' +
                    '                <li data-value="Crédito"><a href="#">Crédito</a>' +
                    '                </li>' +
                    '                <li data-value="Débito"><a href="#">Débito</a>' +
                    '                </li>' +
                    '                <li data-value="Crédito Directo"><a href="#">Crédito Directo</a>' +
                    '                </li>' +
                    '                <!-- END Loop -->' +
                    '            </ul>' +
                    '        </div>' +
                    '    </div>' +
                    '</td>' +
                    '</tr>');
                bind_monto();
            });

        });
    }
    bind_monto();

    $("#btn-finalizar-compra").click(function () {
        // Medios de Pago completar - mediante ciclo
        var venta_medios = []
        $('.medio-pago-text').each(function (index) {
            var monto_medio = $("#monto-medio-" + index).val();
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
            }
            var medio = {
                venta_monto: monto_medio,
                venta_medio_pago_id: medio_id
            }
            venta_medios.push(medio);
        });
        //Productos dentro del Carro - completar mediante ciclo
        var venta_productos = [];
        $.each(carrito, function (i, codigo) {
            $.each(getProductObject(codigo), function (i, venta_producto) {
                venta_productos.push(venta_producto);
            });
        });
        $("#pre-venta").hide();
        $("#cargando-venta").show();
        var venta_JSON = {
            set_venta: 1,
            venta_bruto: bruto,
            venta_descuentos: discount,
            venta_neto: total
        };

        //almacenar medios
        $.post("classes/ptv_procesar_venta.php", venta_JSON, function (data) {
            var venta_id = data;
            //almacenar venta-productos
            var count = venta_productos.length;
            $.each(venta_productos, function (i, producto) {
                producto.venta_id = venta_id;
                producto.set_venta_producto = 1;
                $.post("classes/ptv_procesar_venta.php", producto);
                /*if (!--count) {
                    venta_divs();
                    recargar();

                }*/
            });
            var count = venta_medios.length;
            $.each(venta_medios, function (i, medio) {
                medio.venta_id = venta_id;
                medio.set_venta_medio = 1;
                $.post("classes/ptv_procesar_venta.php", medio);
                if (!--count) {
                    venta_divs();
                    recargar();

                }
            });
        });
    });

    function venta_divs() {
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
            .find('[data-bind="bs-drp-sel-label"]').text($target.attr('data-value')); /*$target.text()*/
        return false;
    });

    function cargar_categorias(padre) {
        $("#categorías-content").html("");
        var lastID = categorias_json.length - 1;

        $.each(categorias_json, function (index, categoria) {
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
                    //console.log(this.value)
                    cargar_categorias(this.value);
                    cargar_productos(this.value);

                });
                $(".item-categoria").tooltip();
            }
        });
        $("#categorías-content").append('<button class="btn btn-default  button-square-2 nfn-text"  id="btn-volver"><i class="fa fa-reply"></i><br>VOLVER</button>');
        activar_volver();
        /*.done(function () {
            
        })*/
        if (padre == 0) {
            $("#opacity-product-mask").fadeTo("fast", 1);
            $("#productos-content").html("");
            //console.log("opacidad a 1");
        }
    }

    function cargar_productos(categoria) {
        cargando("product-body");
        $("#productos-content").html("");
        var lastID = categoria_productos_json.length - 1;
        var total = 0;
        $.each(categoria_productos_json, function (index, categoria_producto) {
            if (parseInt(categoria_producto.cat_id) == parseInt(categoria)) {
                var id = categoria_producto.pro_id;
                var nombre = "";
                var codigo = "";
                $.each(productos_json, function (index2, producto) {

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
            //console.log("opacidad a 0.4");
        } else {
            $("#productos-content").html('<br><button type="button" class="btn btn-default button-square nfn-text item-producto" tittle="description" data-modal="modal-agregar"  style="color:red; width:100% !important; opacity:1 !important;" disabled>NO EXISTEN PRODUCTOS<br>EN ESTA CATEGORÍA</button>');
            $("#opacity-product-mask").fadeTo("fast", 1);

            //console.log("opacidad a 1");
        }

    }


    function contar_productos(categoria) {
        var total = 0;
        $.each(categoria_productos_json, function (index, categoria_producto) {
            if (parseInt(categoria_producto.cat_id) == parseInt(categoria)) {
                var id = categoria_producto.pro_id;
                $.each(productos_json, function (index2, producto) {

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
            //console.log("cantidad: " + cantidad);
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
                //console.log(producto);
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
                        stock = standAloneDB(productos_json, "codigo", this.id.replace('cantidadItems', ''), "stock_r");
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
        stock = standAloneDB(productos_json, "codigo", codigo, "stock_r");
        if (stock.length == 0 || parseInt(stock[0]) < cantidad) {
            //console.log("stock no disponible");
            return false;
        }
        //console.log("stock disponible");
        return true;
    }

    function calcularTotal() {
        total = 0;
        $.each(carrito, function (key, val) {
            codigo = carrito[key];
            precio = $("#cantidadItems" + codigo + "-total").text();
            //alert(precio);
            precio = precio.replace('$', '');
            precio = precio.replace('.', '');
            precio = precio.replace('.', '');
            //console.log(precio);
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
            //console.log(cantidad + " " + codigo);
            promociones = standAloneDB(productos_json, "codigo", codigo, "promociones");
            $.each(promociones[0], function (index, promo) {
                if (parseInt(promo.promo_tipo) == 1 && cantidad >= parseInt(promo.promo_cantidad)) {
                    var promo_qty = parseInt(cantidad / parseInt(promo.promo_cantidad));
                    descuento += parseInt(promo.promo_descontar) * promo_qty;

                    //console.log("descuento 1:" + cantidad);
                    cantidad -= promo_qty * parseInt(promo.promo_cantidad);
                    $("#descuentos-detalle").append('<tr><td>' + promo.promo_descripcion + '</td><td class="text-center">' + (promo_qty * parseInt(promo.promo_cantidad)) + '</td><td class="text-center">' + currencyFormat(((parseInt(promo.promo_descontar) + parseInt(promo.promo_precio)) * promo_qty), "$") + '</td><td class="text-center primary-emphasis"><b>' + currencyFormat((parseInt(promo.promo_descontar) * promo_qty), "$") + '</b></td><td class="text-center">' + currencyFormat(parseInt(promo.promo_precio) * promo_qty, "$") + '</td></tr>');
                }

            });
            $.each(promociones[0], function (index, promo) {
                if (parseInt(promo.promo_tipo) == 2 && cantidad > 0) {
                    //console.log("descuento 2:" + cantidad);
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
        promociones = standAloneDB(productos_json, "codigo", codigo, "promociones");
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
            //console.log("Buscando valor:" + buscado + " para la propiedad:" + propiedad + " verificando: " + val[propiedad]);
            if (val[propiedad] == buscado) {
                result.push(val[resultado]);
            }
        });
        return result;


    }

    function buscarProducto(buscado) {
        var producto = Array();
        $.each(productos_json, function (key, producto_buscado) {
            if (producto_buscado["codigo"] == buscado) {
                producto.push(producto_buscado);
            }
        });
        return producto;
    }

    function getProductObject(codigo, promocion) {
        var producto = [];
        cantidad = parseInt($("#cantidadItems" + codigo).val());
        $.each(productos_json, function (key, producto_buscado) {
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
        tid = setTimeout(mycode, 10000); // repeat myself
    }

    function change_index(div, index) {
        $(div).zIndex(index);
    }

    function recargar() {
        $(".nfn-overlay").show();
        $.post("classes/ptv_obtener_productos_total.php", function (data) {
            productos_json = jQuery.parseJSON(data);
        }).done(function () {
            $("#buscador").bind("input", function () {
                var buscado = this.value;
                if (buscado == "")
                    cargar_categorias(0);
                else {
                    $("#productos-content").html("");
                    //alert(buscado);

                    $.each(productos_json, function (i, v) {

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
            $(".nfn-overlay").hide();
        });
    }

    function activar_volver() {
        $("#btn-volver").click(function () {
            cargar_categorias(0);
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

        if (!$("#modal-pagar").css('visibility') == 'hidden')
            console.log("modal visible");
        if (!$('input:focus').length > 0 && event.which == 73) {
            event.preventDefault();
            $("#buscador").focus();
            console.log("(" + event.which + ") Activar busqueda");
        } else if (search_state == "" && event.which == 13 && $("#modal-pagar").css('visibility') == 'hidden') {
            event.preventDefault();
            $(".pay-button").click();
            $("#monto-medio-0").focus();
            console.log("(" + event.which + ") Activar pago");

        } else if ($("#total-modal").html().indexOf("$") > -1 && $("#modal-pagar").css('visibility') == 'visible' && event.which == 13 && resto > 0 && $("#btn-finalizar-compra-2").css('display') == 'none') {
            event.preventDefault();
            var aPagar = $("#total-modal").html();
            aPagar = aPagar.replace('$', '');
            aPagar = aPagar.replace('.', '');
            aPagar = aPagar.replace('.', '');
            $("#monto-medio-0").val(aPagar);
            $(".monto-pagar").trigger("input");
            console.log("(" + event.which + ") Activar monto");
        } else if ($("#total-modal").html().indexOf("$") > -1 && $("#modal-pagar").css('visibility') == 'visible' && event.which == 13 && resto <= 0 && $("#btn-finalizar-compra-2").css('display') == 'none') {
            event.preventDefault();
            $("#btn-finalizar-compra").click();
            console.log("(" + event.which + ") Autorizar pago");
        } else if ($("#total-modal").html().indexOf("$") > -1 && $("#modal-pagar").css('visibility') == 'visible' && event.which == 13 && resto <= 0 && $("#btn-finalizar-compra-2").css('display') == 'inline-block') {
            event.preventDefault();
            $("#btn-finalizar-compra-2").click();
            console.log("(" + event.which + ") Finalizar pago");
        }

    });
    /* Control keys END*/

    /*
    document.addEventListener("keydown", keyDownTextField, false);

    function keyDownTextField(e) {
        var keyCode = e.keyCode;
        var ctrl = window.event.ctrlKey;
        if (keyCode == 73) {
            ;
            console.log("Busqueda activa");
        }
        if (ctrl && keyCode == 13) {
            $(".pay-button").click();
            $("#monto-medio-0").focus();
            console.log("Pagar activado");
        }
        if (keyCode == 13  && $("#monto-medio-0").val()=="" && $("#total-modal").html().indexOf("$") > -1) {
            var aPagar = $("#total-modal").html();
            aPagar = aPagar.replace('$', '');
            aPagar = aPagar.replace('.', '');
            aPagar = aPagar.replace('.', '');
            $("#monto-medio-0").val(aPagar);
            $(".monto-pagar").trigger("input");
            console.log("Activar monto");
        }
        else if (keyCode == 13  && $("#monto-medio-0").val()!="" && $("#total-modal").html().indexOf("$") > -1) { 
            $("#btn-finalizar-compra").click();
            console.log("Activar monto 2");
        }
    }*/

    $(window).resize(function () {
        //regularizar tamaño
        $('.cuerpo').height($(window).height() - 80);
        $('.cuerpo2').height($(window).height() - 410);
    });

});

function vaciar() {
    $("#ptv-detalle").html("");

    $(".monto-pagar").each(function (i, item) {
        item.value = "";
    });
    $(".medios-extra").remove();
    $("#pay-message").html("");
    $("#pre-venta").html();
    $("#pre-venta").show();
    $("#cargando-venta").hide();
    $("#post-venta").hide();
    $("#modal-pagar").removeClass("md-show");
    $("#btn-finalizar-compra").show();
    $( "#btn-finalizar-compra" ).prop( "disabled", true );
    $("#btn-finalizar-compra-2").hide();
    $("#btn-finalizar-compra-close").show();
    $("#sub-total").html("");
    $("#descuentos").html("");
    $("#precio-total").html("");
    carrito = [];

}