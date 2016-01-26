$(document).on("ready", function(){

    $("#cambiar-pass").modalEffects();
    $("#cambiar-avatar").modalEffects();
    
    
                        
                    url = "classes/perfil.php";
                    $.post(url, {
                        tipo: 2
                    },
                    function (data) {
                        data = $.parseJSON(data);
                        $("#imagen_actual").attr("src","src/avatar_usuarios/"+data.avatar);
                     }
                  )
               
                    
                    
        $('#imagen').change(function(){
			var f=this.files[0];
            var fileExtension = this.value.split('.').pop();
						
			if ((f.size||f.fileSize)>2097152){
						
			mostrar_notificacion("Advertencia", "<label style='color:white !important;font-size:13px'>Avatar no puede superar los 2MB., intente nuevamente.</label>", "warning", "bottom-right");
			$("#imagen").val("");
                            
            }
            
            if((fileExtension != "png") && (fileExtension != "jpg") && (fileExtension != "jpeg") && (fileExtension != "gif")){
                
               mostrar_notificacion("Advertencia", "<label style='color:white !important;font-size:13px'>Extenciones válidas : GIF PNG JPG-JPEG.</label>", "warning", "bottom-right");
			$("#imagen").val(""); 
                
            }
                
                
                
            
        });
    
    
});


$('#btn-cerrar-pass').click(function(){
    $('#form-cambiar-pass').removeClass('md-show');
    //reducirModal();
});

$('#btn-cerrar-avatar').click(function(){
    $('#form-cambiar-avatar').removeClass('md-show');
    //reducirModal();
});




$("#cambiar_contrasena").on("submit", function(event){
    
       var datos = new FormData($("#cambiar_contrasena")[0]);
       
       $("#confirmar").html("Cambiando contraseña.. <i class='fa fa-spinner fa-spin'></i>");
       $("#confirmar").addClass("disabled");
    
    
        $.ajax({
        url: 'classes/perfil.php',
        type: 'POST',
        data: datos,
        async: false,
        cache: false,
        processData: false,
        contentType: false,
        dataType:'JSON',
        success: function (data) {

            if (data.respuesta == 0) {
                mostrar_notificacion("Error", "<label style='color:white !important;font-size:13px'>Ocurrio un problema al intentar guardar los datos.</label>", "danger", "bottom-right");
                $("#confirmar").html("Confirmar cambio");
                $("#confirmar").removeClass("disabled");
            }

            if (data.respuesta == 1) {
                mostrar_notificacion("Exito", "<label style='color:white !important;font-size:13px'>Contraseña cambiada correctamente.</label>", "success", "bottom-right");
                
                
                setTimeout(function () {
                    window.location.reload(1);
                }, 2000);
            }

            if (data.respuesta == 2) {
                mostrar_notificacion("Error", "<label style='color:white !important;font-size:13px'>No dejar campos contraseña vacios.</label>", "danger", "bottom-right");
                $("#confirmar").html("Confirmar cambio");
                $("#confirmar").removeClass("disabled");
            }
            
             if (data.respuesta == 3) {
                mostrar_notificacion("Advertencia", "<label style='color:white !important;font-size:13px'>Las contraseñas no coinciden.</label>", "warning", "bottom-right");
                $("#confirmar").html("Confirmar cambio");
                $("#confirmar").removeClass("disabled");
            }
            
            if (data.respuesta == 4) {
                mostrar_notificacion("Advertencia", "<label style='color:white !important;font-size:13px'>Contraseña actual erronea, intente nuevamente.</label>", "warning", "bottom-right");
                $("#confirmar").html("Confirmar cambio");
                $("#confirmar").removeClass("disabled");
            }




        }
    });

    
        
        event.preventDefault();
    
});

$("#confirmar_avatar").on("click", function(){
    
    
         
       if ($("#imagen").val() != ""){
        
           //$("#confirmar_avatar").html("Cambiando avatar.. <i class='fa fa-spinner fa-spin'></i>");
           //$("#confirmar_avatar").addClass("disabled");
           //alert("casasas")
           
           $(".nfn-overlay").show();
       }
    
});


$("#cambiar_avatar").on("submit", function(event){
    
       var datos = new FormData($("#cambiar_avatar")[0]);
    
    
        $.ajax({
        url: 'classes/perfil.php',
        type: 'POST',
        data: datos,
        async: false,
        cache: false,
        mimeType:"multipart/form-data",
        processData: false,
        contentType: false,
        dataType:'JSON',
        success: function (data) {

            if (data.respuesta == 0) {
                mostrar_notificacion("Error", "<label style='color:white !important;font-size:13px'>Ocurrio un problema al intentar guardar los datos.</label>", "danger", "bottom-right");
                //$("#confirmar_avatar").html("Cambiar avatar");
                //$("#confirmar_avatar").removeClass("disabled");
                $(".nfn-overlay").hide();
            }

            if (data.respuesta == 1) {
                mostrar_notificacion("Exito", "<label style='color:white !important;font-size:13px'>Avatar cambiado correctamente.</label>", "success", "bottom-right");
                
                
                setTimeout(function () {
                    window.location.reload(1);
                }, 2000);
            }

            if (data.respuesta == 2) {
                mostrar_notificacion("Error", "<label style='color:white !important;font-size:13px'>No ha cargado ninguna imagen.</label>", "danger", "bottom-right");
                //$("#confirmar_avatar").html("Cambiar avatar");
                //$("#confirmar_avatar").removeClass("disabled");
                $(".nfn-overlay").hide();
            }
            
            
            if (data.respuesta == 7) {
                mostrar_notificacion("Advertencia", "<label style='color:white !important;font-size:13px'>Extenciones válidas : GIF PNG JPG-JPEG.</label>", "warning", "bottom-right");
                //$("#confirmar_avatar").html("Cambiar avatar");
                //$("#confirmar_avatar").removeClass("disabled");
                $(".nfn-overlay").hide();
            }
            
           



        }
    });

    
        
        event.preventDefault();
    
});


    
