/*global $ */

function regAx(ax) {
    'use strict';
    var url = 'logax.php';
    $.post(url,
        {
            ax: ax
        },
        function (data) {
        }).done(
        function () {
        }
    );
}