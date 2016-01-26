var primerTabla = true, tableObj = [];

function obtenerVentasTiempoReal(accion) {
    var url = 'classes/obtener_estadisticas.php', hora = 0, min = 0, arr = [];
    $.post(url,
    {accion: accion},
    function(data){
        data = $.parseJSON(data);
        resultado = parseInt(data.resultado);
        if(data.resultado > 0){
            if(accion == 'ventas_tiempo_real'){
                arrayVentasTiempoReal = [];
                $.each(data.venta_tiempo_real, function(i, datos){
                    arr = [];
                    arr.push(datos['fecha'], parseInt(datos['cont']));
                    arrayVentasTiempoReal.push(arr);
                });
                arrayVentasTiempoReal.reverse();
                hora = data.tiempo[0].hora - 1;
                min = data.tiempo[0].min;
            }else{
                var subIndex = data.venta_tiempo_real[0];
                arrayVentasTiempoReal.push(parseInt(subIndex[0]));
                arrayVentasTiempoReal.shift();
                hora = data.tiempo[0].hora - 1;
                min = data.tiempo[0].min;
            }
        }
    }).done(function() {
        actualizarVentasTiempoReal(hora, min);
    });
}

function obtenerVentasHora(accion){
    var url = 'classes/obtener_estadisticas.php', hora = 0, mes = 0, dia = 0, arrayVentasHora = [];
    $.post(url,
    {accion: accion},
    function(data){
        data = $.parseJSON(data);
        resultado = parseInt(data.resultado);
        if(data.resultado > 0){
            $.each(data.venta_hora, function(i, datos){
                var subArray = [];
                subArray[0] = datos.fecha;
                subArray[1] = parseInt(datos.cont);
                arrayVentasHora.push(subArray);
            });
            hora = data.tiempo[0].hora;
            mes = data.tiempo[0].mes;
            dia = data.tiempo[0].dia;
        }
    }).done(function() {
        arrayVentasHora.reverse();
        actualizarVentasHora(arrayVentasHora, hora, mes, dia);
    });
}

function obtenerVentasDia(accion){
    var url = 'classes/obtener_estadisticas.php', dia = 0, arrayVentasDia = [];
    $.post(url,
    {accion: accion},
    function(data){
        data = $.parseJSON(data);
        resultado = parseInt(data.resultado);
        if(data.resultado > 0){
            $.each(data.venta_dia, function(i, datos){
                subArray = [];
                subArray[0] = datos.fecha;
                subArray[1] = parseInt(datos.cont);
                subArray[2] = parseInt(datos.cant);
                arrayVentasDia.push(subArray);
            });
            dia = data.tiempo[0].dia;
        }
    }).done(function() {
        actualizarVentasDia(arrayVentasDia, dia);
    });
}


function obtenerTablaVentasDia(accion){
    var url = 'classes/obtener_estadisticas.php', dia = 0, arrayVentasDia = [];
    $.post(url,
    {accion: accion},
    function(data){
        data = $.parseJSON(data);
        resultado = parseInt(data.resultado);
        if(data.resultado > 0){
            $.each(data.venta_dia, function(i, datos){
                subArray = [];
                subArray[0] = datos.fecha;
                subArray[1] = parseInt(datos.cont);
                subArray[2] = parseInt(datos.cant);
                arrayVentasDia.push(subArray);
            });
            dia = data.tiempo[0].dia;
        }
    }).done(function() {
        var arrayHeaders = ['Fecha', 'Monto vendido', 'Cantidad ventas'];
        crearTabla(arrayVentasDia, arrayHeaders, 'tabla_ventas_mes');
    });
}


function obtenerVentasMes(accion){
    var url = 'classes/obtener_estadisticas.php', mes = 0, anio = 0, arrayVentasMes = [];
    $.post(url,
    {accion: accion},
    function(data){
        data = $.parseJSON(data);
        resultado = parseInt(data.resultado);
        if(data.resultado > 0){
            $.each(data.venta_mes, function(i, datos){
                subArray = [];
                subArray[0] = datos.fecha;
                subArray[1] = parseInt(datos.cont);
                arrayVentasMes.push(subArray);
            });
            mes = data.tiempo[0].mes;
            anio = data.tiempo[0].anio;
        }
    }).done(function() {
        actualizarVentasMes(arrayVentasMes, mes, anio);
    });
}

function obtenerRankingSemanal(accion){
    var url = 'classes/obtener_estadisticas.php', arrayRankingSemanal = [];
    $.post(url,
    {accion: accion},
    function(data){
        data = $.parseJSON(data);
        resultado = parseInt(data.resultado);
        if(data.resultado > 0){
            $.each(data.ranking_semanal, function(i, datos){
                subArray = [];
                subArray[0] = datos.fecha
                subArray[1] = parseInt(datos.cont);
                arrayRankingSemanal.push(subArray);
            });
        }
    }).done(function() {
        rankingVentaSemanal(arrayRankingSemanal);
    });
}

function obtenerComparativoMes(accion){
    var url = 'classes/obtener_estadisticas.php', arrayMes0 = [], arrayMes1 = [], arrayComparativo = [], anio;
    var mes0 = new Object(), mes1 = new Object();
    $.post(url,
    {accion: accion},
    function(data){
        data = $.parseJSON(data);
        resultado = parseInt(data.resultado);
        if(data.resultado > 0){
            $.each(data.comparativo[0], function(i, datos){
                arrayMes0.push(parseInt(datos['cont']));
            });
            $.each(data.comparativo[1], function(i, datos){
                arrayMes1.push(parseInt(datos['cont']));
            });
            mes0.name = data.meses[0];
            mes0.data = arrayMes0;
            mes1.name = data.meses[1];
            mes1.data = arrayMes1;
            anio = parseInt(data.tiempo[0].anio);
        }
    }).done(function() {
        arrayComparativo.push(mes0, mes1);

        actualizarVentaComparativoMes(arrayComparativo, anio);
    });
}

function obtenerRankingVendedores(accion){
    var url = 'classes/obtener_estadisticas.php', arrayRankingVendedores = [];
    $.post(url,
    {accion: accion},
    function(data){
        data = $.parseJSON(data);
        resultado = parseInt(data.resultado);
        if(data.resultado > 0){
            $.each(data.ranking_vendedores, function(i, datos){
                subArray = [];
                subArray[0] = datos.nombre;
                subArray[1] = parseInt(datos.cont);
                arrayRankingVendedores.push(subArray);
            });
        }
    }).done(function() {
        rankingVendedores(arrayRankingVendedores);
    });
}

function obtenerFluctuacionMes(accion){
    var url = 'classes/obtener_estadisticas.php', arrayFluctuacionMes = [];
    $.post(url,
    {accion: accion},
    function(data){
        data = $.parseJSON(data);
        resultado = parseInt(data.resultado);
        if(data.resultado > 0){
            $.each(data.fluctuacion_mes, function(i, datos){
                subArray = [];
                subArray[0] = datos.fecha;
                subArray[1] = parseInt(datos.cont);
                arrayFluctuacionMes.push(subArray);
            });
        }
    }).done(function() {
        $('#fluctuacion_mes_wrap').show();
        var porcentaje = arrayFluctuacionMes[1][1] * 100;
        porcentaje = porcentaje / arrayFluctuacionMes[0][1];
        porcentaje = Math.round(porcentaje);
        var fluctuacion = 100 - porcentaje;
        var content = '';
        if(fluctuacion > 0)
            content += '<span style="color: green;"><i class="fa fa-caret-up fa-2x"></i></span> ';
        else
            content += '<span style="color: red;"><i class="fa fa-caret-down fa-2x"></i></span> ';
        var montoPromedio = currencyFormat(arrayFluctuacionMes[0][1], '$');
        content += fluctuacion + '%  <b>Ventas totales del mes: ' + montoPromedio + '</b>';
        $('#detalle_fluctuacion_mes').html(content);
        fluctuacionMes(arrayFluctuacionMes);
    });
}

function obtenerPromedioCompra(accion){
    var url = 'classes/obtener_estadisticas.php', arrayPromedioCompra = [];
    $.post(url,
    {accion: accion},
    function(data){
        data = $.parseJSON(data);
        resultado = parseInt(data.resultado);
        if(data.resultado > 0){
            $.each(data.promedio_compra, function(i, datos){
                subArray = [];
                subArray[0] = datos.fecha;
                subArray[1] = parseInt(datos.promedio);
                arrayPromedioCompra.push(subArray);
            });
        }
    }).done(function() {
        var porcentaje = arrayPromedioCompra[1][1] * 100;
        porcentaje = porcentaje / arrayPromedioCompra[0][1];
        porcentaje = Math.round(porcentaje);
        var fluctuacion = 100 - porcentaje;
        var content = '';
        if(fluctuacion > 0)
            content += '<span style="color: green;"><i class="fa fa-caret-up fa-2x"></i></span> ';
        else
            content += '<span style="color: red;"><i class="fa fa-caret-down fa-2x"></i></span> ';
        var montoPromedio = currencyFormat(arrayPromedioCompra[0][1], '$');
        content += fluctuacion + '%  <b>Monto de venta promedio: ' + montoPromedio + '</b>';
        $('#promedio_compra_det').html(content);
        //promedioCompra(arrayPromedioCompra);
    });
}

function obtenerTotales(accion){
    var url = 'classes/obtener_estadisticas.php';
    $.post(url,
    {accion: accion},
    function(data){
        data = $.parseJSON(data);
        if (data.total[0].cont === '' || data.total[0].cont == null || data.total[0].cont === 'undefined') {
            data.total[0].cont = 0;
        }
        $('#'+accion[0]).html(data.total[0].cont);
    });
    $('.stats_bar').show();
}

function actualizarVentasTiempoReal(hora, min){

    var ventasCont = '<div class="block-flat"><div class="header"><h3>Line Chart</h3></div><div class="content"><div id="site_statistics" style="height: 180px; padding: 0px; position: relative;"></div></div></div>';
    var f = new Date();
    var anio = f.getFullYear();
    var mes = f.getMonth();
    mes += 1;
    var dia = f.getDate();
    $('#ventas_tiempo_real').show();
    $('#ventas_tiempo_real').html(ventasCont);
    $('#ventas_tiempo_real').highcharts({
        chart: {
            zoomType: 'x'
        },
        title: {
            text: 'Ventas en Tiempo Real'
        },
        subtitle: {
            text: document.ontouchstart === undefined ?
                    'Haz clic y arrastra el puntero para hacer zoom' :
                    'Pincha el gráfico para hacer zoom'
        },
        xAxis: {
            type: 'datetime',
            tickPixelInterval: 150,
            maxZoom: 20 * 1000
        },
        yAxis: {
            title: {
                text: 'Cantidad ventas'
            },
            min: 0,
        },
        legend: {
            enabled: false
        },
        tooltip: {
            pointFormat: '{series.name}: <b>${point.y}</b>'
        },
        plotOptions: {
            area: {
                fillColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1},
                    stops: [
                        [0, Highcharts.getOptions().colors[0]],
                        [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                    ]
                },
                marker: {
                    radius: 2
                },
                lineWidth: 1,
                states: {
                    hover: {
                        lineWidth: 1
                    }
                },
                threshold: null
            }
        },
        series: [{
            type: 'area',
            name: 'Vta por min',
            pointInterval: 60 * 1000,
            pointStart: Date.UTC(anio, mes, dia, hora, min),
            data: arrayVentasTiempoReal
        }]
    });
}

function actualizarVentasHora(data, hora, mes, dia){

    var f = new Date();
    var anio = f.getFullYear();
    mes -= 1;
    $('#ventas_hora').show();
    $('#ventas_hora').highcharts({
        chart: {
            zoomType: 'x'
        },
        title: {
            text: 'Ventas de las últimas 24 horas'
        },
        subtitle: {
            text: document.ontouchstart === undefined ?
                    'Haz clic y arrastra el puntero para hacer zoom' :
                    'Pincha el gráfico para hacer zoom'
        },
        xAxis: {
            type: 'datetime',
            tickPixelInterval: 150,
            maxZoom: 20 * 1000,
            units: [['hour', [1, 3]]],
        },
        yAxis: {
            title: {
                text: 'Cantidad ventas'
            }
        },
        legend: {
            enabled: false
        },
        plotOptions: {
            area: {
                fillColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1},
                    stops: [
                        [0, Highcharts.getOptions().colors[0]],
                        [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                    ]
                },
                marker: {
                    radius: 2
                },
                lineWidth: 1,
                states: {
                    hover: {
                        lineWidth: 1
                    }
                },
                threshold: null
            }
        },
        tooltip: {
            pointFormat: '{series.name}: <b>${point.y}</b>'
        },
        series: [{
            type: 'column',
            name: 'Vta por hora',
            pointInterval: 3600 * 1000,
            pointStart: Date.UTC(anio, mes, dia, hora),
            data: data,
            pointWidth: 20
        }]
    });
}

function actualizarVentasDia(data, dia){
    var f = new Date();
    var anio = f.getFullYear();
    var mes = f.getMonth();
    mes -= 1;
    if (mes == 1) {
        dia = 28;
    }
    $('#ventas_dia').show();
    $('#ventas_dia').highcharts({
        chart: {
            zoomType: 'x'
        },
        title: {
            text: 'Ventas del último mes'
        },
        subtitle: {
            text: document.ontouchstart === undefined ?
                    'Haz clic y arrastra el puntero para hacer zoom' :
                    'Pincha el gráfico para hacer zoom'
        },
        xAxis: {
            type: 'datetime',
            units: [['day', [2, 6, 12]]],
            maxZoom: 20 * 1000,
        },
        yAxis: {
            title: {
                text: 'Cantidad ventas'
            }
        },
        legend: {
            enabled: false
        },
        plotOptions: {
            series: {
                borderWidth: 1,
            }
        },
        tooltip: {
            pointFormat: '{series.name}: <b>${point.y}</b>'
        },
        series: [{
            type: 'column',
            name: 'Vta por día',
            pointInterval: 24 * 3600 * 1000,
            pointStart: Date.UTC(anio, mes, dia),
            data: data,
            pointWidth: 20
        }]
    });
}



function actualizarVentasMes(data, mes, anio){
    //mes -= 1;
    var f = new Date();
    $('#ventas_mes').show();
    $('#ventas_mes').highcharts({
        chart: {
            zoomType: 'x'
        },
        title: {
            text: 'Ventas mensuales'
        },
        subtitle: {
            text: document.ontouchstart === undefined ?
                    'Haz clic y arrastra el puntero para hacer zoom' :
                    'Pincha el gráfico para hacer zoom'
        },
        xAxis: {
            type: 'datetime',
            units: [['month', [2, 6, 12]]]
        },
        yAxis: {
            title: {
                text: 'Cantidad ventas'
            }
        },
        legend: {
            enabled: false
        },
        plotOptions: {
            series: {
                borderWidth: 1,
            }
        },
        tooltip: {
            pointFormat: '{series.name}: <b>${point.y}</b>'
        },
        series: [{
            type: 'column',
            name: 'Vta por mes',
            pointInterval: 24 * 3600 * 1000 * 31,
            pointStart: Date.UTC(anio, mes),
            data: data,
            pointWidth: 28
        }]
    });
}

function rankingVentaSemanal(data){
    $('#ranking_semanal').show();
    $('#ranking_semanal').highcharts({
       chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text: 'Ranking Semanal de ventas'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Porcentaje ventas',
            data: data
        }]
    });
}

function actualizarVentaComparativoMes(data, anio){

    $('#comparativo_mes').show();
    $('#comparativo_mes').highcharts({
        chart: {
            type: 'area',
            zoomType: 'x'
        },
        title: {
            text: 'Comparativo de meses anteriores'
        },
        xAxis: {
            allowDecimals: false,

        },
        yAxis: {
            title: {
                text: 'Ventas diarias por mes'
            },

        },
        tooltip: {
            pointFormat: '<b>{series.name} {point.x}</b><br>${point.y:,.0f} ventas'
        },
        plotOptions: {
            area: {
                pointStart: 0,
                marker: {
                    enabled: false,
                    symbol: 'circle',
                    radius: 2,
                    states: {
                        hover: {
                            enabled: true
                        }
                    }
                }
            }
        },
        series: data
    });
}

function rankingVendedores(data){
    $('#ranking_vendedores').show();
    $('#ranking_vendedores').highcharts({
       chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text: 'Ranking Vendedores'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Porcentaje de ventas',
            data: data
        }]
    });
}

function fluctuacionMes(data){
    $('#fluctuacion_mes').show();
    $('#fluctuacion_mes').highcharts({
       chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text: 'Fluctuación mensual de ventas'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Porcentaje de ventas',
            data: data
        }]
    });
}

function promedioCompra(data){
    $('#promedio_compra').show();
    $('#promedio_compra').highcharts({
       chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text: 'Promedio de monto de compra mensual'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Porcentaje de ventas',
            data: data
        }]
    });
}

function crearTabla(array, arrayHeaders, clase){

    var table = '<table class="table table-bordered" id="tabla_ventas"><thead><tr class="tr-head"></tr></thead><tbody class="table-body">', headerContent = '';
    for(var i = 0; i < array.length; i++){
        table += '<tr>';
        var fechita = array[i][0].split("-").reverse().join("-");
        table += '<td><span style="display:none">' + array[i][0] + '</span>' + fechita + '</td>';
        table += '<td>' + currencyFormat(array[i][1], '$') + '</td>';
        table += '<td>' + currencyFormat(array[i][2], '') + '</td>';
        table += '</tr>';
        if(i <= 2)
            headerContent += '<th>' + arrayHeaders[i] + '</th>';
    }
    table += '</tbody></table>';
    $('#' + clase).html(table);
    $('.tr-head').html(headerContent);
    $('#tabla_ventas').addClass(clase);
    $('#panel-detalle-ventas-mensual').show();
    $('#' + clase).show();
    if (!primerTabla) {
        //destruirTabla();
    }
    primerTabla = false;
    tableObj.push( $('.'+clase).DataTable({
        "aaSorting": [[0, 'desc']],
        "oLanguage": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se han encontrado registros",
            "sEmptyTable": "No se han encontrado registros",
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
    }));
}

//PRODUCTOS

function obtenerProductosDia(accion){
    var url = 'classes/obtener_resumen_productos.php', dia = 0, arrayVentasDia = [];
    $.post(url,
    {parametros: [accion]},
    function(data){
        data = $.parseJSON(data);
        $.each(data.productos, function(i, datos){
            subArray = [];
            subArray[0] = datos.fecha;
            //subArray[1] = parseInt(datos.cont);
            if(datos.cantidad == null)
                datos.cantidad = 0;
            subArray[1] = parseInt(datos.cantidad);
            arrayVentasDia.push(subArray);
        });
        dia = data.tiempo[0].dia;
    }).done(function() {
        actualizarProductosDia(arrayVentasDia, dia);
    });
}

function obtenerProductosHora(accion){
    var url = 'classes/obtener_resumen_productos.php', hora = 0, mes = 0, dia = 0, arrayVentasHora = [];
    $.post(url,
    {parametros: [accion]},
    function(data){
        data = $.parseJSON(data);
        resultado = parseInt(data.resultado);
        $.each(data.productos, function(i, datos){
            var subArray = [];
            subArray[0] = datos.fecha;
            if(datos.resultado == 0){
                subArray[1] = 0;
            }else{
                subArray[1] = parseInt(datos.cantidad);
            }
            arrayVentasHora.push(subArray);
        });
        hora = data.tiempo[0].hora;
        mes = data.tiempo[0].mes;
        dia = data.tiempo[0].dia;
    }).done(function() {
        actualizarProductosHora(arrayVentasHora, hora, mes, dia);
    });
}

function crearTablaProducto(datos, cabeceras, idTabla) {
    'use strict';

    var i,
        tablaDatos = '<thead><tr>',
        val;
    for (i = 0; i < cabeceras.length; i++) {
        tablaDatos += '<th>' + cabeceras[i] + '</th>';
    }
    i = 0;
    tablaDatos += '</tr></thead><tbody>';
    for (i = 0; i < datos.length; i++) {
        tablaDatos += '<tr>';
        for (val in datos[i]) {
            if (datos[i].hasOwnProperty(val)) {
                if (val === 'fecha') {

                    var fechita = datos[i][val].split("-").reverse().join("-");
                    tablaDatos += '<td><span style="display:none">' + datos[i][val] + '</span>' + fechita + '</td>';
                } else {
                    tablaDatos += '<td>' + datos[i][val] + '</td>';
                }
            }
        }
        tablaDatos += '</tr>';
    }
    primerTabla = false;
    tablaDatos += '</tbody>';
    $('#' + idTabla).html(tablaDatos);
    tableObj.push($('#' + idTabla).DataTable({
        "aaSorting": [[0, 'desc']],
        "oLanguage": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se han encontrado registros",
            "sEmptyTable": "No se han encontrado registros",
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
    }));
}

function verGrafico(param, cabeceras, idTabla) {
    'use strict';
    var url = 'classes/obtener_resumen_productos.php',
        arrayDatos = [],
        hora,
        mes,
        dia;
    $.post(url,
        {
            parametros: [param]
        },
        function (data) {
            data = $.parseJSON(data);
            $.each(data.productos, function (i, datos) {
                var subArray = {};
                if (datos.resultado === 0) {
                    if (datos.tipo_fecha === 'day') {
                        subArray.fecha = datos.fecha;
                    } else {
                        subArray.fecha = datos.fecha.substr(datos.fecha.length - 2, datos.fecha.length) + 'hrs';
                    }
                    subArray.codigo = '<p style="text-align:center">-</p>';
                    subArray.nombre = '<p style="text-align:center">-</p>';
                    subArray.cantidad = '<p style="text-align:center">-</p>';
                    subArray.precio = '<p style="text-align:center">-</p>';
                    subArray.total = '<p style="text-align:center">-</p>';
                    arrayDatos.push(subArray);
                } else {
                    if (datos.tipo_fecha === 'day') {
                        subArray.fecha = datos.fecha;
                    } else {
                        subArray.fecha = datos.fecha;
                    }
                    subArray.codigo = '<p style="text-align:center">' + datos.codigo + '</p>';
                    subArray.nombre = '<p style="text-align:center">' + datos.nombre + '</p>';
                    subArray.cantidad = '<p style="text-align:center">' + currencyFormat(parseInt(datos.cantidad, 10), '') + '</p>';
                    subArray.precio = '<p style="text-align:center">' + currencyFormat(datos.precio, '$') + '</p>';
                    subArray.total = '<p style="text-align:center">' + currencyFormat(datos.cantidad * datos.precio, '$') + '</p>';
                    arrayDatos.push(subArray);
                }
            });
            hora = data.tiempo[0].hora;
            mes = data.tiempo[0].mes;
            dia = data.tiempo[0].dia;
        }).done(
        function () {
            //arrayDatos.reverse();
            crearTablaProducto(arrayDatos, cabeceras, idTabla);
        }
    );
}

function otrosDatos(param, cabeceras, idTabla) {
    'use strict';
    var url = 'classes/obtener_resumen_productos.php';
    $.post(url,
        {
            parametros: [param]
        },
        function (data) {
            data = $.parseJSON(data);
            crearTablaProducto(data.productos, cabeceras, idTabla);
        }).done(
        function () {
        }
    );
}

function actualizarProductosDia(data, dia){
    var f = new Date();
    var anio = f.getFullYear();
    var mes = f.getMonth();
    mes -= 1;
    if (mes == 1) {
        dia = 28;
    }
    $('#ventas_dia').show();
    $('#ventas_dia').highcharts({
        chart: {
            zoomType: 'x'
        },
        title: {
            text: 'Productos más vendido del último mes'
        },
        subtitle: {
            text: document.ontouchstart === undefined ?
                    'Haz clic y arrastra el puntero para hacer zoom' :
                    'Pincha el gráfico para hacer zoom'
        },
        xAxis: {
            type: 'datetime',
            units: [['day', [2, 6, 12]]],
            maxZoom: 20 * 1000,
        },
        yAxis: {
            title: {
                text: 'Cantidad ventas'
            }
        },
        legend: {
            enabled: false
        },
        plotOptions: {
            series: {
                borderWidth: 1,
            }
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.y}</b>'
        },
        series: [{
            type: 'column',
            name: 'Vta por día',
            pointInterval: 24 * 3600 * 1000,
            pointStart: Date.UTC(anio, mes, dia),
            data: data,
            pointWidth: 20
        }]
    });
}

function actualizarProductosHora(data, hora, mes, dia){

    var f = new Date();
    var anio = f.getFullYear();
    mes -= 1;
    $('#ventas_hora').show();
    $('#ventas_hora').highcharts({
        chart: {
            zoomType: 'x'
        },
        title: {
            text: 'Producto más vendido del último día'
        },
        subtitle: {
            text: document.ontouchstart === undefined ?
                    'Haz clic y arrastra el puntero para hacer zoom' :
                    'Pincha el gráfico para hacer zoom'
        },
        xAxis: {
            type: 'datetime',
            tickPixelInterval: 150,
            maxZoom: 20 * 1000,
            units: [['hour', [1, 3]]],
        },
        yAxis: {
            title: {
                text: 'Cantidad ventas'
            }
        },
        legend: {
            enabled: false
        },
        plotOptions: {
            area: {
                fillColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1},
                    stops: [
                        [0, Highcharts.getOptions().colors[0]],
                        [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                    ]
                },
                marker: {
                    radius: 2
                },
                lineWidth: 1,
                states: {
                    hover: {
                        lineWidth: 1
                    }
                },
                threshold: null
            }
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.y}</b>'
        },
        series: [{
            type: 'column',
            name: 'Vta por hora',
            pointInterval: 3600 * 1000,
            pointStart: Date.UTC(anio, mes, dia, hora),
            data: data,
            pointWidth: 20
        }]
    });
}

//CAJA

function obtenerCierreCaja(f_inicio, f_termino) {
    'use strict';

    function obtenerMedioPago(medioPago) {
        'use strict';
        switch (medioPago) {
            case '1':
                return 'Efectivo';
            case '2':
                return 'Crédito';
            case '3':
                return 'Débito';
            case '4':
                return 'Crédito local';
        }
    }

    $('.nfn-overlay').show();
    var url = 'classes/obtener_resumen.php',
        tablaCierre = '<table id="tabla_cierre" class="table table-bordered table-responsive"><thead><tr><th>#</th><th>Hora</th><th>Bruto</th><th>Neto</th><th>Descuentos</th><th>Costo</th><th>Medio Pago</th></tr></thead><tbody>',
        divTotales = '';
    $.post(url,
        {
            parametros: ['cierre_caja'],
            f_inicio: f_inicio,
            f_termino: f_termino
        },
        function (data) {
            data = $.parseJSON(data);
            $.each(data.ventas, function (i, datos) {
                tablaCierre += '<tr>';
                tablaCierre += '<td>' + currencyFormat(datos.v_id, '') + '</td>';
                tablaCierre += '<td>' + datos.fecha + '</td>';
                tablaCierre += '<td>' + currencyFormat(datos.bruto, '$') + '</td>';
                tablaCierre += '<td>' + currencyFormat(datos.neto, '$') + '</td>';
                tablaCierre += '<td>' + currencyFormat(datos.descuentos, '$') + '</td>';
                var costo = datos.costo;
                if (costo === null) {
                    costo = 0;
                }
                tablaCierre += '<td>' + currencyFormat(costo, '$') + '</td>';
                tablaCierre += '<td>' + obtenerMedioPago(datos.medio_pago) + '</td>';
                //tablaCierre += '<td>' + currencyFormat(datos.productos_unicos, '') + '</td>';
                //tablaCierre += '<td>' + currencyFormat(datos.total_productos, '') + '</td>';
                tablaCierre += '</tr>';
            });
            $.each(data.totales, function (i, datos) {
                if (datos.bruto != null) {
                    divTotales += '<div class="butpro butstyle flat" style="min-height:100px"><div class="sub"><h2>Total Bruto</h2><br><span id="total_ventas_dia">' + currencyFormat(datos.bruto, '$') + '</span></div></div>';
                } else {
                    divTotales += '<div class="butpro butstyle flat" style="min-height:100px"><div class="sub"><h2>Total Bruto</h2><br><span id="total_ventas_dia">$0</span></div></div>';
                }
                if (datos.descuentos != null) {
                    divTotales += '<div class="butpro butstyle flat" style="min-height:100px"><div class="sub"><h2>Total Descuentos</h2><br><span id="total_ventas_dia">' + currencyFormat(datos.descuentos, '$') + '</span></div></div>';
                } else {
                    divTotales += '<div class="butpro butstyle flat" style="min-height:100px"><div class="sub"><h2>Total Descuentos</h2><br><span id="total_ventas_dia">$0</span></div></div>';
                }
                //
                if (datos.neto != null) {
                    divTotales += '<div class="butpro butstyle flat" style="min-height:100px"><div class="sub"><h2>Total Neto</h2><br><span id="total_ventas_dia">' + currencyFormat(datos.neto, '$') + '</span></div></div>';
                } else {
                    divTotales += '<div class="butpro butstyle flat" style="min-height:100px"><div class="sub"><h2>Total Neto</h2><br><span id="total_ventas_dia">$0</span></div></div>';
                }
                if (datos.costo != null) {
                    divTotales += '<div class="butpro butstyle flat" style="min-height:100px"><div class="sub"><h2>Total Costo</h2><br><span id="total_ventas_dia">' + currencyFormat(datos.costo, '$') + '</span></div></div>';
                } else {
                    divTotales += '<div class="butpro butstyle flat" style="min-height:100px"><div class="sub"><h2>Total Costo</h2><br><span id="total_ventas_dia">$0</span></div></div>';
                }
                if (datos.ganancia != null) {
                    divTotales += '<div class="butpro butstyle flat" style="min-height:100px"><div class="sub"><h2>Total Ganancias</h2><br><span id="total_ventas_dia">' + currencyFormat(datos.ganancia, '$') + '</span></div></div>';
                } else {
                    divTotales += '<div class="butpro butstyle flat" style="min-height:100px"><div class="sub"><h2>Total Ganancias</h2><br><span id="total_ventas_dia">$0</span></div></div>';
                }
                var datos_pagos = datos.medios;
                if (datos_pagos.length == 0) {
                    divTotales += '<div class="butpro butstyle flat" style="min-height:100px"><div class="sub"><h2>Total Efectivo</h2><br><span id="total_ventas_dia">$0</span></div></div>';
                    divTotales += '<div class="butpro butstyle flat" style="min-height:100px"><div class="sub"><h2>Total Crédito</h2><br><span id="total_ventas_dia">$0</span></div></div>';
                    divTotales += '<div class="butpro butstyle flat" style="min-height:100px"><div class="sub"><h2>Total Débito</h2><br><span id="total_ventas_dia">$0</span></div></div>';
                    divTotales += '<div class="butpro butstyle flat" style="min-height:100px"><div class="sub"><h2>Total Crédito local</h2><br><span id="total_ventas_dia">$0</span></div></div>';
                } else {
                    if(datos_pagos[0].efectivo != undefined){
                        divTotales += '<div class="butpro butstyle flat" style="min-height:100px"><div class="sub"><h2>Total Efectivo</h2><br><span id="total_ventas_dia">' + currencyFormat(datos_pagos[0].efectivo, '$') + '</span></div></div>';
                    } else {
                        divTotales += '<div class="butpro butstyle flat" style="min-height:100px"><div class="sub"><h2>Total Efectivo</h2><br><span id="total_ventas_dia">$0</span></div></div>';
                    }
                    if(datos_pagos[0].credito != undefined){
                        divTotales += '<div class="butpro butstyle flat" style="min-height:100px"><div class="sub"><h2>Total Crédito</h2><br><span id="total_ventas_dia">' + currencyFormat(datos_pagos[0].credito, '$') + '</span></div></div>';
                    } else {
                        divTotales += '<div class="butpro butstyle flat" style="min-height:100px"><div class="sub"><h2>Total Crédito</h2><br><span id="total_ventas_dia">$0</span></div></div>';
                    }
                    if(datos_pagos[0].debito != undefined){
                        divTotales += '<div class="butpro butstyle flat" style="min-height:100px"><div class="sub"><h2>Total Débito</h2><br><span id="total_ventas_dia">' + currencyFormat(datos_pagos[0].debito, '$') + '</span></div></div>';
                    } else {
                        divTotales += '<div class="butpro butstyle flat" style="min-height:100px"><div class="sub"><h2>Total Débito</h2><br><span id="total_ventas_dia">$0</span></div></div>';
                    }
                    if(datos_pagos[0].credito_local != undefined){
                        divTotales += '<div class="butpro butstyle flat" style="min-height:100px"><div class="sub"><h2>Total Crédito local</h2><br><span id="total_ventas_dia">' + currencyFormat(datos_pagos[0].credito_local, '$') + '</span></div></div>';
                    } else {
                        divTotales += '<div class="butpro butstyle flat" style="min-height:100px"><div class="sub"><h2>Total Crédito local</h2><br><span id="total_ventas_dia">$0</span></div></div>';
                    }
                }
            });

        }).done(
        function () {
            $('#tabla_wrap').html(tablaCierre);
            $('#totales').html(divTotales);
            $('#tabla_cierre').dataTable({
                "aaSorting": [[0, 'asc']],
                "oLanguage": {
                    "sProcessing": "Procesando...",
                    "sLengthMenu": "Mostrar _MENU_ registros",
                    "sZeroRecords": "No se han encontrado registros",
                    "sEmptyTable": "No se han encontrado registros",
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
        }
    );
}

//REPORTES CON FILTRO

function validar(cadena, tipo) {
    'use strict';
    switch (tipo) {
    case 'categoria':
        if (cadena === undefined) {
            var val = $('#filtro_categoria').val();
            cadena = $('#filtro_categoria option[value=' + val + ']').text();
        }
        break;
    case 'producto_codigo':
        if (cadena === undefined) {
            cadena = $('#filtro_producto').val();
        }
        break;
    case 'producto_nombre':
        if (cadena === undefined) {
            var val = $('#filtro_producto').val(),
                n = 0;
            cadena = $('#filtro_producto option[value=' + val + ']').text();
            n = 3 + cadena.lastIndexOf('||');
            cadena = cadena.substr(n, cadena.length - n);
        }
        break;
    case 'date':
        if (cadena === undefined) {
            cadena = '-';
        }
        break;
    case 'int':
        if (cadena === undefined) {
            cadena = 0;
        }
        break;
    }
    return cadena;
}

function obtenerVentasMesCategoria() {
    'use strict';
    var url = 'classes/obtener_resumen.php',
        categoria = $('#filtro_categoria').val(),
        html = '<table class="table table-bordered responsive" id="tabla_filtro"><thead><tr><th>Categoría</th><th>Fecha</th><th>Monto ventas</th><th>Cantidad vendida</th></tr></thead><tbody>';
    $.post(url,
    {
        parametros: ['filtro_categoria'],
        categoria: categoria
    },
    function (data) {
        data = $.parseJSON(data);
        $.each(data.venta_dia, function (i, datos) {
            html += '<tr>';
            html += '<td>' + validar(datos.c_nombre, 'categoria') + '</td>';
            var fechita = datos.v_fecha.split("-").reverse().join("-");
            html += '<td><span style="display:none">' + datos.v_fecha + '</span>' + validar(fechita, 'date') + '</td>';
            html += '<td>' + currencyFormat(validar(datos.v_neto, 'int'), '$') + '</td>';
            html += '<td>' + currencyFormat(validar(datos.vp_cantidad, 'int'), '') + '</td>';
            html += '</tr>';
        });
        html += '</tbody></table>';
    }).done(
    function ()
        {
            $('#wrap_tabla').html('');
            $('.cl-mcont5').find('#wrap_tabla').html(html);
            $('#tabla_filtro').DataTable({
                "aaSorting": [[1, 'desc']],
                "oLanguage": {
                    "sProcessing": "Procesando...",
                    "sLengthMenu": "Mostrar _MENU_ registros",
                    "sZeroRecords": "No se han encontrado registros",
                    "sEmptyTable": "No se han encontrado registros",
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
    }
    );
}

function obtenerVentasMesProducto() {
    'use strict';
    $('.nfn-overlay').show();
    var url = 'classes/obtener_resumen.php',
        producto = $('#filtro_producto').val(),
        html = '<table class="table table-bordered responsive" id="tabla_filtro"><thead><tr><th>Código</th><th>Nombre</th><th>Fecha</th><th>Monto ventas</th><th>Cantidad vendida</th></tr></thead><tbody>';
    $.post(url,
    {
        parametros: ['filtro_producto'],
        producto: producto
    },
    function (data) {
        data = $.parseJSON(data);
        $.each(data.venta_dia, function (i, datos) {
            html += '<tr>';
            html += '<td>' + validar(datos.p_codigo, 'producto_codigo') + '</td>';
            html += '<td>' + validar(datos.p_nombre, 'producto_nombre') + '</td>';
            var fechita = datos.v_fecha.split("-").reverse().join("-");
            html += '<td><span style="display:none">' + datos.v_fecha + '</span>' + validar(fechita, 'date') + '</td>';
            html += '<td>' + currencyFormat(validar(datos.v_neto, 'int'), '$') + '</td>';
            html += '<td>' + currencyFormat(validar(datos.vp_cantidad, 'int'), '') + '</td>';
            html += '</tr>';
        });
        html += '</tbody></table>';
    }).done(
    function ()
        {
            $('#wrap_tabla').html('');
            $('.cl-mcont4').find('#wrap_tabla').html(html);
            $('#tabla_filtro').DataTable({
                "aaSorting": [[2, 'desc']],
                "oLanguage": {
                    "sProcessing": "Procesando...",
                    "sLengthMenu": "Mostrar _MENU_ registros",
                    "sZeroRecords": "No se han encontrado registros",
                    "sEmptyTable": "No se han encontrado registros",
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
    }
    );
}

//GASTOS

function cargarTablaGastos(parametros) {
    'use strict';
    var url = 'classes/gastos.php',
        tabla = '<table class="table table-bordered responsive" id="tabla_gastos"><thead><tr><th>#</th><th>Fecha</th><th>Total Gastado</th><th>Descripción</th><th>Categoría</th><th>Medio de Pago</th><th>Nro Documento</th><th>Responsable</th></tr></thead><tbody>';
    $.post(url,
        {
            op: 'detalle_dia',
            parametros: parametros
        },
        function (data) {
            var fecha;
            $.each(data.gastos, function (i, datos) {
                tabla += '<tr>';
                tabla += '<td>' + i + '</td>';
                fecha = datos.fecha.split("-").reverse().join("-");
                tabla += '<td><span style="display:none;">' + datos.fecha + '</span>' + fecha + '</td>';
                tabla += '<td>' + datos.total + '</td>';
                tabla += '<td>' + datos.descripcion + '</td>';
                tabla += '<td>' + datos.categoria + '</td>';
                tabla += '<td>' + datos.medio + '</td>';
                tabla += '<td>' + datos.documento + '</td>';
                tabla += '<td>' + datos.responsable + '</td>';
                tabla += '</tr>';
            });
            tabla += '</tbody><tfoot><tr>';
            tabla += '<th>#</th>';
            tabla += '<th>Fecha</th>';
            tabla += '<th>Total Gastado</th>';
            tabla += '<th>Descripción</th>';
            tabla += '<th>Categoría</th>';
            tabla += '<th>Medio de pago</th>';
            tabla += '<th>Nro de documento</th>';
            tabla += '<th>Responsable</th>';
            tabla += '</tr></tfoot>';
            tabla += '</table>';
        }, 'json').done(
        function () {
            $('.cl-mcont8 #tabla_gastos_wrapper').html(tabla);
            $('#tabla_gastos').dataTable({
                "oLanguage": {
                    "aaSorting": [[1, 'desc']],
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
            }).columnFilter();
            $('#tabla_gastos tfoot tr').find(':input').each(function(index){
                //var colWidth = $('#tabla_gastos thead tr th:eq(' + index + ')').css('width');
                var colWidth = 90;
                $(this).css("width", colWidth );
            });
        }
    );
}

function cargarDivGastos() {
    'use strict';

    $('#monto_minimo_gastos').numeric();
    $('#monto_maximo_gastos').numeric();

    function mostrar(e) {
        $('#opciones_gastos_wrapper').show('slow');
        $(e).removeClass('shower');
        $(e).addClass('hider');
        $(e).find('i').addClass('fa-caret-up');
        $(e).find('i').removeClass('fa-caret-down');
        $('#explicacion_filtro_gastos').show('slow');
        $(e).find('.texto').html('Ocultar filtros');
    }

    function esconder(e) {
        $('#opciones_gastos_wrapper').hide('slow');
        $(e).removeClass('hider');
        $(e).addClass('shower');
        $(e).find('i').addClass('fa-caret-down');
        $(e).find('i').removeClass('fa-caret-up');
        $('#explicacion_filtro_gastos').hide('slow');
        $(e).find('.texto').html('Mostrar filtros');
    }

    $('#btn_mostrar_opciones_gastos').on('click', function () {
        if ($(this).hasClass('hider')) {
            esconder(this);
        } else {
            mostrar(this);
        }
    });

    $('#btn_cargar_gastos').on('click', function () {
        var parametros = {},
            fechaInvertida,
            hora;
        esconder($('#btn_mostrar_opciones_gastos'));
        parametros.categorias = $('#categorias_gastos').val();
        parametros.monto_minimo = $('#monto_minimo_gastos').val();
        parametros.monto_maximo = $('#monto_maximo_gastos').val();
        fechaInvertida = $('#f_inicio_gastos').val().substr(0, 10);
        hora = $('#f_inicio_gastos').val().substr(11, 16);
        fechaInvertida = fechaInvertida.split("-").reverse().join("-");
        parametros.f_inicio = fechaInvertida + ' ' + hora.substr(2, hora.length);
        fechaInvertida = $('#f_termino_gastos').val().substr(0, 10);
        hora = $('#f_termino_gastos').val().substr(11, 16);
        fechaInvertida = fechaInvertida.split("-").reverse().join("-");
        parametros.f_termino = fechaInvertida + ' ' + hora.substr(2, hora.length);
        cargarTablaGastos(parametros);
    });

    $('#btn_vaciar_gastos').on('click', function () {
        $('input[type=text]').each(function(){
            $(this).val('');
        });
        $('#categorias_gastos').val('').trigger('change');
    });
}

function obtenerTotalesInventario() {
    'use strict';
    var url = 'classes/obtener_resumen.php',
        totales;
    $.post(
        url,
        {
            parametros: ['totales_inventario']
        },
        function (data) {
            totales = data.totales;
            $('#valor_inventario').html(currencyFormat(totales.valor, '$'));
            $('#costo_inventario').html(currencyFormat(totales.costo, '$'));
            $('#margen_ganancia').html(currencyFormat(totales.ganancia, '$'));
        },
        'json'
    ).done(function () {
        console.log("");
    });;
}
