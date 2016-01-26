$(document).ready(function () {
    cargarEstadisticas();
    
    var f = new Date(),
        month = parseInt(f.getMonth(), 0) + 1,
        fecha = f.getFullYear() + '-' + month + '-' + f.getDate() + 'T00:00:00Z';
    $('.datetime').attr('data-date', fecha);
    
    $('input#inputIdentificador').numeric();
    $('input#inputTotal').numeric();
    
    $.post('classes/gastos.php', {
        op: 'categorias'
    }, function (data) {
        var categorias = $.parseJSON(data);
        $.each(categorias, function (i, cat) {
            var categoria = "";
            if (parseInt(cat.padre) == 0) {
                categoria = '<optgroup label="' + cat.nombre + '">';
                $.each(categorias, function (i, hijo) {
                    if (parseInt(cat.id) == parseInt(hijo.padre))
                        categoria += '<option value="' + hijo.id + '">' + hijo.nombre + '</option>';
                });
                categoria += "</optgroup>";
            }
            $('#inputCategoria').append(categoria)
        });
        //console.log(categorias);
    });
    $.post('classes/gastos.php', {
        op: 'medios'
    }, function (data) {
        var medios = $.parseJSON(data);
        $.each(medios, function (i, med) {
            var medio = '<option value="' + med.id + '">' + med.nombre + '</option>';
            $('#inputMedio').append(medio)
        });
    });

    $('#inputSubmit').on('click', function () {
        var fechahora = $('#inputFecha').val();
        var descripcion = $('#inputDescripcion').val();
        var categoria = $('#inputCategoria').val();
        var medio = $('#inputMedio').val();
        var identificador = $('#inputIdentificador').val();
        var total = $('#inputTotal').val();
        var fecha = fechahora.substr(0, 10);
        var hora = fechahora.substr(11, 16);
        fecha = fecha.split("-").reverse().join("-");
        var isValido = validarFormulario();
        if (isValido) {
            $.post('classes/gastos.php', {
                op: 'ingresar',
                fecha: fecha + ' ' + hora,
                descripcion: descripcion,
                categoria: categoria,
                medio: medio,
                identificador: identificador,
                total: total
            }, function (data) {
                console.log(data[0]);
                if (data[0].success == 1)
                    mostrar_notificacion("Realizado con éxito", "El gasto ha sido ingresado con éxito", "success");
                else
                    mostrar_notificacion("Error", "No se pudo completar la operacion", "danger");

                cargarEstadisticas();
            }, 'json');
            $('#inputFecha').val('');
            $('#inputDescripcion').val('');
            $('#inputCategoria').val('');
            $('#inputMedio').val('');
            $('#inputIdentificador').val('');
            $('#inputTotal').val('');
        }
    });

    function validarFormulario() {
        var fechahora = $('#inputFecha').val();
        var descripcion = $('#inputDescripcion').val();
        var categoria = $('#inputCategoria').val();
        var medio = $('#inputMedio').val();
        var identificador = $('#inputIdentificador').val();
        var total = $('#inputTotal').val();
        
        var valido = true;
        var mensaje = "";
        if(fechahora==""){
            mensaje+="Debe ingresar una fecha<br>";
            valido = false;
        }
        /*if(descripcion==""){
            mensaje+="Debe ingresar una descripcion<br>";
            valido = false;
        }*/
        if(!isNumber(categoria)){
            mensaje+="Debe ingresar una categoría valida<br>";
            valido = false;
        }
        if(!isNumber(medio)){
            mensaje+="Debe ingresar un medio válido<br>";
            valido = false;
        }
        
        if(!isNumber(total)){
            mensaje+="Debe ingresar un total válido<br>";
            valido = false;
        }
        if(!valido){
            mostrar_notificacion("Error",mensaje,"danger");
        }
        return valido;
    }
    $('#inputTotal').keyup('change',function(){
        
        var total = $('#inputTotal').val();
        total = total.replace('-','');
        $('#inputTotal').val(total);
    });
    function cargarEstadisticas() {
        $.post('classes/gastos.php', {
            op: 'estadisticas'
        }, function (data) {
            $('#gastoTotal').html(currencyFormat(data[0].total_mes, '$'));
            $('#gastoMes').html(currencyFormat(data[0].total, '$'));
            $('#gastoMesCat').html('Gastos en ' + data[0].cat + '<br><span>(Destacado del mes)</span>');
            $('#gastoMesAnt').html(currencyFormat(data[0].total_anterior, '$'));
            $('#gastoMesAntCat').html('Gastos en ' + data[0].cat_anterior + '<br><span>(Destacado mes anterior)</span>');
        }, 'json');

    }

    function currencyFormat(num, extra) {
        if (isNumber(num) == true)
            return extra + num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")
        return "no definido";
    }

    function isNumber(input) {
        return (input - 0) == input && ('' + input).replace(/^\s+|\s+$/g, "").length > 0;
    }
});