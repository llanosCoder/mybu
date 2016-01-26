/*global $, currencyFormat, obtenerVentasTiempoReal, obtenerVentasHora, obtenerVentasDia, obtenerVentasMes, obtenerRankingSemanal, obtenerComparativoMes, obtenerRankingVendedores, obtenerFluctuacionMes, obtenerPromedioCompra, obtenerTotales, obtenerTablaVentasDia, otrosDatos, verGrafico, obtenerProductosDia, tableObj, destruirTabla, obtenerProductosHora, obtenerVentasMesCategoria, obtenerVentasMesProducto, aRut, cargarDivGastos, obtenerTotalesInventario */
/*jslint vars: true, plusplus: true, devel: true, nomen: true, indent: 4, maxerr: 50 */
var arrayVentasTiempoReal = [], intervalo;

$(document).on('ready', function () {
    'use strict';
    var url = 'classes/obtener_datos_sesion.php', tipoCuenta,
        f = new Date(),
        month = parseInt(f.getMonth(), 0) + 1,
        fecha = f.getFullYear() + '-' + month + '-' + f.getDate() + 'T00:00:00Z';
    $('.datetime').attr('data-date', fecha);
    $.post(url,
        {
            parametros: ['tipo_cuenta']
        },
        function (data) {
            data = $.parseJSON(data);
            tipoCuenta = data[0].tipo_cuenta;

        }).done(
        function () {
            $('.nfn-overlay').hide();
            function obtenerProductos() {
                $('.nfn-overlay').show();
                var url = 'classes/obtener_productos.php',
                    listaProductos = '<option value="0">Seleccione un$a opción</option>';
                $.post(url,
                    {
                        parametros: ['codigo', 'nombre']
                    },
                    function (data) {
                        data = $.parseJSON(data);
                        $.each(data.productos, function (i, datos) {
                            listaProductos += '<option value="' + datos.codigo + '">' + datos.codigo + ' || ' + datos.nombre + '</option>';
                        });
                        $('#filtro_producto').html(listaProductos);
                    }).done(
                    function () {

                        $('.nfn-overlay').hide();
                    }
                );
            }

            function obtenerCategorias() {
                $('.nfn-overlay').show();
                var url = 'classes/obtener_categorias_actuales.php',
                    listaCategorias = '<option value="0">Seleccione una opción</option>';
                $.post(url,
                    {
                        parametros: ['descripcion', 'nombre']
                    },
                    function (data) {
                        data = $.parseJSON(data);
                        $.each(data, function (i, datos) {
                            listaCategorias += '<option value="' + datos.descripcion + '">' + datos.nombre + '</option>';
                        });
                    }).done(
                    function () {
                        $('#filtro_categoria').html(listaCategorias);
                        $('.nfn-overlay').hide();
                    }
                );

            }

            $('#ventas').on('click', function () {

                destruirTabla();
                $('.cl-mcont').show();
                $('.cl-mcont2').hide();
                $('.cl-mcont6').hide();
                $('.cl-mcont7').hide();
                $('.cl-mcont8').hide();
                $('.cl-mcont9').hide();
                $('#seleccione-opcion').hide();
                obtenerVentasTiempoReal(['ventas_tiempo_real']);
                intervalo = setInterval(function () {
                    obtenerVentasTiempoReal(['ventas_minuto']);
                }, 1000 * 60);
                obtenerVentasHora(['ventas_hora']);
                obtenerVentasDia(['ventas_dia']);
                obtenerVentasMes(['ventas_mes']);
                obtenerRankingSemanal(['ranking_semanal']);
                obtenerComparativoMes(['comparativo_mes']);
                if (tipoCuenta === '1') {
                    obtenerRankingVendedores(['ranking_vendedores']);
                }
                //obtenerFluctuacionMes(['fluctuacion_mes']);
                obtenerPromedioCompra(['promedio_compra']);
                obtenerTotales(['total_ventas_dia']);
                obtenerTotales(['total_ventas_mes']);
                obtenerTotales(['total_productos_dia']);
                obtenerTotales(['total_productos_mes']);
            });

            $('#productos').on('click', function () {

                clearInterval(intervalo);
                $('#seleccione-opcion').hide();
                $('.cl-mcont').show();
                $('.cl-mcont2').hide();
                $('.cl-mcont3').hide();
                $('.cl-mcont6').hide();
                $('.cl-mcont7').hide();
                $('.cl-mcont8').hide();
                $('.cl-mcont9').hide();
                $('#ventas_tiempo_real').hide();
                $('#ventas_hora').hide();
                $('#ventas_dia').hide();
                $('#ventas_mes').hide();
                $('#ranking_semanal').hide();
                $('#comparativo_mes').hide();
                $('#ranking_vendedores').hide();
                $('#fluctuacion_mes').hide();
                $('.stats_bar').hide();
                $('#fluctuacion_mes_wrap').hide();
                obtenerTotalesInventario();
                $('#stats_productos').show();
                destruirTabla();
                obtenerProductosDia(7);
                obtenerProductosHora(8);
            });

            function destruirTabla() {

                var i;
                for (i = 0; i < tableObj.length; i++) {
                    tableObj[i].destroy();
                }
                tableObj.splice(0, tableObj.length);
            }

            function mostrarInformes(opt) {

                $('#seleccione-opcion').hide();
                switch (opt) {
                case 'ventas_dia':
                    $('.cl-mcont').hide();
                    $('.cl-mcont2').show();
                    $('.cl-mcont3').hide();
                    $('.cl-mcont4').hide();
                    $('.cl-mcont5').hide();
                    $('.cl-mcont6').hide();
                    $('.cl-mcont7').hide();
                    $('.cl-mcont8').hide();
                    $('.cl-mcont9').hide();
                    obtenerTablaVentasDia([opt]);
                    break;
                case 'productos_mes':
                    $('.cl-mcont').hide();
                    $('.cl-mcont2').hide();
                    $('.cl-mcont3').show();
                    $('.cl-mcont4').hide();
                    $('.cl-mcont5').hide();
                    $('.cl-mcont6').hide();
                    $('.cl-mcont7').hide();
                    $('.cl-mcont8').hide();
                    $('.cl-mcont9').hide();
                    verGrafico(7, ['Fecha', 'Código', 'Nombre producto', 'Cantidad', 'Precio', 'Total vendido'], 'tabla_productos_mes');
                    verGrafico(8, ['Fecha', 'Código', 'Nombre producto', 'Cantidad', 'Precio', 'Total vendido'], 'tabla_productos_dia');
                    otrosDatos(9, ['Código', 'Nombre', 'Stock', 'Stock mínimo', 'Sucursal'], 'tabla_productos_bajo_stock');
                    otrosDatos(10, ['Código', 'Nombre', 'Stock', 'Sucursal'], 'tabla_productos_sin_stock');
                    break;
                case 'producto':
                    $('.cl-mcont').hide();
                    $('.cl-mcont2').hide();
                    $('.cl-mcont3').hide();
                    $('.cl-mcont4').show();
                    $('.cl-mcont5').hide();
                    $('.cl-mcont6').hide();
                    $('.cl-mcont7').hide();
                    $('.cl-mcont8').hide();
                    $('.cl-mcont9').hide();
                    $('#stats_productos').show();
                    break;
                case 'categoria':
                    $('.cl-mcont').hide();
                    $('.cl-mcont2').hide();
                    $('.cl-mcont3').hide();
                    $('.cl-mcont4').hide();
                    $('.cl-mcont5').show();
                    $('.cl-mcont6').hide();
                    $('.cl-mcont7').hide();
                    $('.cl-mcont8').hide();
                    $('.cl-mcont9').hide();
                    break;
                }
            }

            $('#informe_productos').on('click', function () {

                clearInterval(intervalo);
                mostrarInformes('productos_mes');
            });

            $('#informe_ventas').on('click', function () {

                clearInterval(intervalo);
                mostrarInformes('ventas_dia');
            });

            $('#reporte_filtro_productos').on('click', function () {

                obtenerProductos();
                mostrarInformes('producto');
            });

            $('#reporte_filtro_categorias').on('click', function () {

                obtenerCategorias();
                mostrarInformes('categoria');
            });

            $('#filtro_producto').on('change', function () {

                obtenerVentasMesProducto();
            });

            $('#filtro_categoria').on('change', function () {

                obtenerVentasMesCategoria();
            });

            function obtenerRegistros(accion) {
                $('#seleccione-opcion').hide();
                $('.cl-mcont6').show();
                $('.cl-mcont7').hide();
                $('.cl-mcont8').hide();
                $('.cl-mcont9').hide();
                $('.cl-mcont').hide();
                $('.cl-mcont2').hide();
                $('.cl-mcont3').hide();
                $('#ventas_tiempo_real').hide();
                $('#ventas_hora').hide();
                $('#ventas_dia').hide();
                $('#ventas_mes').hide();
                $('#ranking_semanal').hide();
                $('#comparativo_mes').hide();
                $('#ranking_vendedores').hide();
                $('#fluctuacion_mes').hide();
                $('.stats_bar').hide();
                $('#fluctuacion_mes_wrap').hide();
                destruirTabla();
                $('.nfn-overlay').show();
                var url = 'classes/obtener_registros.php',
                    tablaRegistros = '<table class="table table-bordered" id="tabla_registros"><thead><tr>';
                $.post(url,
                    {
                        accion: accion
                    },
                    function (data) {
                        var fechita,
                            horita;
                        data = $.parseJSON(data);
                        $.each(data.headers, function (i, datos) {
                            tablaRegistros += '<th>' + datos + '</th>';
                        });
                        tablaRegistros += '</tr><tbody>';
                        $.each(data.datos, function (i, datos) {
                            tablaRegistros += '<tr>';
                            $.each(datos, function (i, casilla) {
                                if (i === 'fecha') {
                                    fechita = casilla.substr(0, 10);
                                    horita = casilla.substr(11, 16);
                                    fechita = fechita.split("-").reverse().join("-");
                                    fechita += ' ' + horita;
                                    tablaRegistros += '<td><span style="display:none">' + casilla + '</span>' + fechita + '</td>';
                                } else {
                                    tablaRegistros += '<td>' + casilla + '</td>';
                                }
                            });
                            tablaRegistros += '</tr>';
                        });
                    }).done(
                    function () {
                        tablaRegistros += '</tbody></table>';
                        $('.cl-mcont6').html(tablaRegistros);
                        $('#tabla_registros').dataTable({
                            "aaSorting": [[0, 'asc']],
                            "oLanguage": {
                                "sProcessing": "Procesando...",
                                "sLengthMenu": "Mostrar _MENU_ registros",
                                "sZeroRecords": "No se han encontrado registros disponibles",
                                "sEmptyTable": "No se han encontrado registros disponibles",
                                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
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
                        $('.nfn-overlay').hide();
                        $('.cl-mcont6').show();
                    }
                );
            }

            $('#registro_stock').on('click', function () {
                obtenerRegistros(1);
            });

            $('#registro_materia_prima').on('click', function () {
                obtenerRegistros(2);
            });

            $('#registro_traspaso_stock').on('click', function () {
                obtenerRegistros(3);
            });

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

            function cargarClientes() {

                $('#seleccione-opcion').hide();
                $('.cl-mcont6').hide();
                $('.cl-mcont7').show();
                $('.cl-mcont8').hide();
                $('.cl-mcont9').hide();
                $('.cl-mcont').hide();
                $('.cl-mcont2').hide();
                $('.cl-mcont3').hide();
                $('#ventas_tiempo_real').hide();
                $('#ventas_hora').hide();
                $('#ventas_dia').hide();
                $('#ventas_mes').hide();
                $('#ranking_semanal').hide();
                $('#comparativo_mes').hide();
                $('#ranking_vendedores').hide();
                $('#fluctuacion_mes').hide();
                $('.stats_bar').hide();
                $('#fluctuacion_mes_wrap').hide();
                destruirTabla();
                $('.nfn-overlay').show();
                var url = 'classes/administrar_clientes.php',
                    tablaRegistros = '<table class="table table-bordered" id="tabla_clientes"><thead><tr><th>RUT</th><th>Nombre</th><th>Saldo total crédito</th><th>Saldo disponible</th><th>Fecha creación</th>';
                $.post(url,
                    {
                        accion: 2
                    },
                    function (data) {
                        var fechita,
                            tieneCredito = true;
                        data = $.parseJSON(data);
                        tablaRegistros += '</tr><tbody>';
                        $.each(data.resultado, function (i, datos) {
                            tablaRegistros += '<tr>';
                            tablaRegistros += '<td>' + aRut(datos.rut) + '</td>';
                            tablaRegistros += '<td>' + datos.nombre + ' ' + datos.apaterno + ' ' + datos.amaterno + '</td>';
                            if (datos.monto_autorizado === null) {
                                tablaRegistros += '<td>Sin línea de crédito</td>';
                                tieneCredito = false;
                            } else {
                                tablaRegistros += '<td>' + currencyFormat(datos.monto_autorizado, '$') + '</td>';
                                tieneCredito = true;
                            }
                            if (tieneCredito) {
                                tablaRegistros += '<td>' + currencyFormat(datos.cupo, '$') + '</td>';
                            } else {
                                tablaRegistros += '<td>Sin línea de crédito</td>';
                            }
                            fechita = datos.f_creacion.split("-").reverse().join("-");
                            tablaRegistros += '<td><span style="display:none;">' + fechita + '</span>' + datos.f_creacion + '</td>';
                            tablaRegistros += '</tr>';
                        });
                    }).done(
                    function () {
                        tablaRegistros += '</tbody></table>';
                        $('.cl-mcont7').html(tablaRegistros);
                        $('#tabla_clientes').dataTable({
                            "aaSorting": [[0, 'asc']],
                            "oLanguage": {
                                "sProcessing": "Procesando...",
                                "sLengthMenu": "Mostrar _MENU_ registros",
                                "sZeroRecords": "No se han encontrado registros disponibles",
                                "sEmptyTable": "No se han encontrado registros disponibles",
                                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
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
                        $('.nfn-overlay').hide();
                        $('.cl-mcont7').show();
                    }
                );
            }

            function cargarCategoriasGastos() {
                var url = 'classes/gastos.php',
                    categorias = '<option value="0">Seleccione una opción</option>';
                $.post(url,
                    {
                        op: 'categorias'
                    },
                    function (data) {
                        $.each(data, function (i, datos) {
                            categorias += '<option value="' + datos.id + '">' + datos.nombre + '</option>';
                        });

                    }, 'json').done(
                    function () {
                        $('#categorias_gastos').html(categorias);
                    }
                );
            }

            function verDetalleGastos() {

                $('#seleccione-opcion').hide();
                $('.cl-mcont8').show();
                $('.cl-mcont6').hide();
                $('.cl-mcont7').hide();
                $('.cl-mcont9').hide();
                $('.cl-mcont').hide();
                $('.cl-mcont2').hide();
                $('.cl-mcont3').hide();
                $('#ventas_tiempo_real').hide();
                $('#ventas_hora').hide();
                $('#ventas_dia').hide();
                $('#ventas_mes').hide();
                $('#ranking_semanal').hide();
                $('#comparativo_mes').hide();
                $('#ranking_vendedores').hide();
                $('#fluctuacion_mes').hide();
                $('.stats_bar').hide();
                $('#fluctuacion_mes_wrap').hide();
                $('#categorias_gastos').select2({
                    placeholder: 'Seleccione una categoría'
                });
                $('#monto_minimo').numeric();
                $('#monto_maximo').numeric();

                destruirTabla();
                cargarCategoriasGastos();
                cargarDivGastos();
            }

            $('#lista_clientes').on('click', cargarClientes);

            $('#ver_detalle_gastos').on('click', verDetalleGastos);

        }
    );
});
