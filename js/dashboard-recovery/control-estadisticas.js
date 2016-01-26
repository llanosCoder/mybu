/*global $, currencyFormat, obtenerVentasTiempoReal, obtenerVentasHora, obtenerVentasDia, obtenerVentasMes, obtenerRankingSemanal, obtenerComparativoMes, obtenerRankingVendedores, obtenerFluctuacionMes, obtenerPromedioCompra, obtenerTotales, obtenerTablaVentasDia, otrosDatos, verGrafico, obtenerProductosDia, tableObj, destruirTabla */
/*jslint vars: true, plusplus: true, devel: true, nomen: true, indent: 4, maxerr: 50 */
var arrayVentasTiempoReal = [], intervalo;

$('#ventas').on('click', function () {
    'use strict';
    destruirTabla();
    $('.cl-mcont').show();
    $('.cl-mcont2').hide();
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
    obtenerRankingVendedores(['ranking_vendedores']);
    obtenerFluctuacionMes(['fluctuacion_mes']);
    obtenerPromedioCompra(['promedio_compra']);
    obtenerTotales(['total_ventas_dia']);
    obtenerTotales(['total_ventas_mes']);
    obtenerTotales(['total_productos_dia']);
    obtenerTotales(['total_productos_mes']);
});

$('#productos').on('click', function () {
    'use strict';
    clearInterval(intervalo);
    $('#seleccione-opcion').hide();
    $('.cl-mcont').show();
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
    obtenerProductosDia(7);
    obtenerProductosHora(8);
});

function destruirTabla() {
    'use strict';
    var i;
    for (i = 0; i < tableObj.length; i++) {
        tableObj[i].destroy();
    }
    tableObj.splice(0, tableObj.length);
}

function mostrarInformes(opt) {
    'use strict';
    $('#seleccione-opcion').hide();
    switch (opt) {
    case 'ventas_dia':
        $('.cl-mcont').hide();
        $('.cl-mcont2').show();
        $('.cl-mcont3').hide();
        obtenerTablaVentasDia([opt]);
        break;
    case 'productos_mes':
        $('.cl-mcont').hide();
        $('.cl-mcont2').hide();
        $('.cl-mcont3').show();
        verGrafico(7, ['Fecha', 'Código', 'Nombre producto', 'Cantidad', 'Precio', 'Total vendido'], 'tabla_productos_mes');
        verGrafico(8, ['Fecha', 'Código', 'Nombre producto', 'Cantidad', 'Precio', 'Total vendido'], 'tabla_productos_dia');
        otrosDatos(9, ['Código', 'Nombre', 'Stock', 'Stock mínimo', 'Sucursal'], 'tabla_productos_bajo_stock');
        otrosDatos(10, ['Código', 'Nombre', 'Stock', 'Sucursal'], 'tabla_productos_sin_stock');
        break;
    }
}

$('#informe_productos').on('click', function () {
    'use strict';
    clearInterval(intervalo);
    mostrarInformes('productos_mes');
});

$('#informe_ventas').on('click', function () {
    'use strict';
    clearInterval(intervalo);
    mostrarInformes('ventas_dia');
});