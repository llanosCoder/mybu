$("#login").on('submit', iniciar_sesion);


function iniciar_sesion(event) {

    var usuario = $("#usuario").val();
    var pass = $("#pass").val();
    
    var formData = "usuario="+usuario+"&pass="+pass;

    $.ajax({
        url: 'clases/verificar_usuario.php',
        type: 'POST',
        data: formData,
        async: false,
        cache: false,
        //dataType:'JSON',
        success: function (data) {
            
                if(data != 0)
                    window.location.href = "ver_fichas.html";
                    

        }


    });

    event.preventDefault();
}