/*global $, mostrar_notificacion, FormData, habilitarDeshabilitarBoton*/

$(document).on('ready', function () {
    'use strict';
    
});

function validarArchivo(archivo) {
    'use strict';
    var id = '#file_' + archivo,
        idBoton = '#btn_' + archivo,
        file,
        fileName,
        fileExtension;
    file = $(id)[0].files[0];
    fileName = file.name;
    if ((file.size || file.fileSize) === 0) {
        mostrar_notificacion('Error', 'No ha seleccionado ningún archivo', 'danger');
    } else {
        fileExtension = fileName.substring(fileName.lastIndexOf('.') + 1);
        switch (fileExtension.toLowerCase()) {
        case 'xls':
        case 'xlsx':
            return true;
        default:
            mostrar_notificacion('Error', 'Extensión de imagen no válida', 'warning');
            return false;
        }
    }
}

function importar(tipo) {
    'use strict';
    var url = 'classes/importar_excel.php',
        accion,
        formData;
    switch (tipo) {
    case 'categorias':
        accion = 1;
        break;
    case 'marcas':
        accion = 2;
        break;
    case 'productos':
        accion = 3;
        break;
    }
    $('#form_' + tipo).append('<input type="hidden" value="' + accion + '" name="accion">');
    formData = new FormData($("#form_" + tipo)[0]);
    if (validarArchivo(tipo)) {
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function () {
                habilitarDeshabilitarBoton('btn' + tipo, true);
            },
            success: function (data) {
                data = $.parseJSON(data);
                
                switch (data.resultado) {
                case 0:
                    mostrar_notificacion('Error', 'Ha ocurrido un error al importar archivo', 'danger');
                    break;
                case 1:
                    $("#file_" + tipo).val('');
                    mostrar_notificacion('Éxito', 'Archivo importado exitosamente', 'success');
                    break;
                case 2:
                    mostrar_notificacion('Error', 'No se ha encontrado archivo', 'danger');
                    break;
                default:
                    mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte un administrador', 'danger');
                    break;
                }
            },
            error: function () {
                mostrar_notificacion('Error', 'Ha ocurrido un error al conectar con servidor', 'danger');
            }
        });
        
    }
}

$('#btn_categorias').on('click', function () {
    'use strict';
    importar('categorias');
});

$('#btn_marcas').on('click', function () {
    'use strict';
    importar('marcas');
});

$('#btn_productos').on('click', function () {
    'use strict';
    importar('productos');
});