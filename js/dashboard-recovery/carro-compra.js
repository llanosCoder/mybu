/*global $*/

function subirCarro(carro, parametros, accion) {
    'use strict';
    var url = 'classes/administrar_carro_compra.php';
    $.post(url,
        {
            carro: carro,
            parametros: parametros,
            accion: accion
        },
        function (data) {
        }).done(
        function () {
        }
    );
}