function guardarSucursal() {
    'use strict';
    var hayerror = false,
        formData = new FormData($("#formsucursal")[0]),
        url = 'classes/agregar_sucursal.php';
    $("#formsucursal").find(':input').each(function () {
        var elemento = this;
        if ($("#" + elemento.id).parent().hasClass('has-error')) {
            hayerror = true;
        }
    });

    if (!hayerror) {

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            async: false,
            cache: false,
            processData: false,
            contentType: false,
            dataType:'JSON',
            success: function (data) {
            
                
                if(data.respuesta!=undefined){
                    
                     
                    switch (data.respuesta){
                            
                        case 0:
                              mostrar_notificacion("Error", "<label style='color:white !important;font-size:13px'>Ocurrio un error al intentar crear la sucursal</label>", "danger", "bottom-right");  
                        break;
                        
                        case 1:
                            mostrar_notificacion("Exito", "<label style='color:white !important;font-size:13px'>Sucursal creada correctamente</label>", "success", "bottom-right");
                             setTimeout(function () {
                                window.location.reload(1);
                             }, 2000);
                        break;
                            
                        default:
                              mostrar_notificacion("Error", "<label style='color:white !important;font-size:13px'>Ocurrio un error al intentar crear la sucursal</label>", "danger", "bottom-right");  
                        break;
                            
                    }
                   
                    
                    
                }
           
            }
        });
    }
}





$('#formsucursal').on('submit', function (event) {
    
    /*formData = new FormData($("#formsucursal")[0]);
    
      $.ajax({
            url: 'classes/agregar_sucursal.php',
            type: 'POST',
            data: formData,
            async: false,
            cache: false,
            processData: false,
            contentType: false,
            dataType:'JSON',
            success: function (data) {
                
                alert(data.respuesta);
                
                
                
            }
        });
    */
    
    event.preventDefault();
    guardarSucursal();
    
});

$(document).on('ready', function () {
    'use strict';
    $('#formsucursal').validate({
        rules: {
            direccion: {
                required: true,
                maxlength: 60,
            },
    
            comuna: {
                required: true
            },
            region: {
                required: true
            },
            pais: {
                required: true
            },
            submitHandler: function () {
                //guardarUsuario();
            }
        },
        highlight: function (element) {
            $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
            //$('#paso1').removeClass('next');
            //$('#guardar').attr('disabled', true);
        },
        unhighlight: function (element) {
            $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
            //$('#pass').addClass('next');
        }
    });
});

$('#pais').on('change', function validar_pais() {
    'use strict';
    if (document.forms[0].pais.value === 0) {
        $('#alert2').html('Debe seleccionar un pais').slideDown(500);
        document.forms[0].pais.focus();
    } else {
        $('#alert2').html('').slideUp(300);
    }
});

$('#region').on('change', function validar_region() {
    'use strict';
    if (document.forms[0].region.value === 0) {
        $('#alert3').html('Debe seleccionar una región').slideDown(500);
        document.forms[0].region.focus();
    } else {
        $('#alert3').html('').slideUp(300);
    }
});

$('#comuna').on('change', function validar_comuna() {
    'use strict';
    if (document.forms[0].comuna.value === 0) {
        $('#alert4').html('Debe seleccionar una comuna').slideDown(500);
        document.forms[0].comuna.focus();
    } else {
        $('#alert4').html('').slideUp(300);
    }
});

