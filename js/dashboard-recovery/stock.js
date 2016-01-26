/*global $, mostrar_notificacion, isNumber*/

function limpiarFormulario() {
    'use strict';
    $('#cantidad').val('');
    //$("#select_sucursales_origen").select2("val", "0");
    $(".select2").val("0").trigger("change"); 
}

function obtenerSucursales() {
    'use strict';
    var url = 'classes/administrar_sucursales.php',
        selectSucursales = '<option value="0"></option>';
    $.post(url,
        {
            accion: 1
        },
        function (data) {
            data = $.parseJSON(data);
            $.each(data.sucursales, function (i, datos) {
                selectSucursales += '<option value="' + datos.val + '">' + datos.direccion + '</option>';
            });
        }).done(
        function () {
            $('#select_sucursales_origen').html(selectSucursales);
            $('#select_sucursales_origen').select2();
            $('#select_sucursales_destino').html(selectSucursales);
            $('#select_sucursales_destino').select2();
        }
    );
}

function obtenerProductos() {
    'use strict';
    var url = 'classes/obtener_productos.php',
        arrayParametros = ['codigo', 'nombre'],
        selectProductos = '<option value="0"></option>';
    $.post(url,
        {
            parametros: arrayParametros
        },
        function (data) {
            data = $.parseJSON(data);
            $.each(data.productos, function (i, datos) {
                selectProductos += '<option value="' + datos.codigo + '">' + datos.codigo + ' | ' + datos.nombre + '</option>';
            });
        }
        ).done(
        function () {
            $("#select_productos").html(selectProductos);
            $('#select_productos').select2();
        }
    );
}

$(document).on('ready', function () {
    'use strict';
    obtenerSucursales();
    obtenerProductos();
});

function traspasarStock() {
    'use strict';
    var url = 'classes/administrar_sucursales.php',
        accion = 2,
        sucursalOrigen = $('#select_sucursales_origen').val(),
        sucursalDestino = $('#select_sucursales_destino').val(),
        producto = $('#select_productos').val(),
        cantidad = $('#cantidad').val();
    if (sucursalOrigen !== '' && sucursalDestino !== '' && producto !== '' && isNumber(cantidad)) {
        $.post(url,
            {
                accion: accion,
                sucursal_origen: sucursalOrigen,
                sucursal_destino: sucursalDestino,
                producto: producto,
                cantidad: cantidad
            },
            function (data) {
                data = $.parseJSON(data);
                switch (data.resultado) {
                case 0:
                    mostrar_notificacion('Error', 'No se pudo procesar su solicitud', 'danger');
                    break;
                case 1:
                    mostrar_notificacion('Éxito', 'Stock enviado con éxito', 'success');
                    limpiarFormulario();
                    break;
                case 2:
                    mostrar_notificacion('Atención', 'Stock no se ha agregado a la sucursal destino. Sí se descontó de sucursal origen', 'warning');
                    limpiarFormulario();
                    break;
                case 3:
                    mostrar_notificacion('Atención', 'No cuenta con suficiente stock en sucursal origen', 'warning');
                    break;
                default:
                    mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte a un administrador', 'danger');
                    break;
                }
            }).done(
            function () {
            }
        );
    } else {
        mostrar_notificacion('Atención', 'Complete todos los campos', 'warning');
    }
}

$('#btn_aceptar').on('click', traspasarStock);