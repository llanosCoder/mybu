var current_fs, next_fs, opacity;

$(document).on('ready', comenzar);

function comenzar() {
    $("#progreso li a").removeAttr("href").css("cursor", "pointer");
    
}

function pasos(id) {
    $(".next").click(function () {
        current_fs = $("#" + id).parent();
        next_fs = $("#" + id).parent().next();

        //activar el siguiente paso en progreso usando el índice de next_fs
        $("#progreso li").eq($("fieldset").index(next_fs)).addClass("active");
        $("#progreso li a").eq($("fieldset").index(next_fs)).addClass("atras").attr("id", (id + 1));
        

        //mostrar el siguiente fieldset
        next_fs.show();
        current_fs.hide();
        opacity: 1;
        next_fs.css({
            'opacity': opacity
        });
        //ocultar el fieldset actual con estilo
        /*current_fs.animate({opacity: 0}, {
			step: function(now, mx) {
				//como la opacidad de current_fs está reducida a 0 - almacenado en "now"
				//1. escala current_fs hasta 80%
				//scale = 1 - (1 - now) * 0.2;
				//2. traer next_fs desde la derecha (50%)
				//left = (now * 50)+"%";
				//3. aumentar la opacidad de next_fs a 1 a medida que avanza
				//opacity = 1 - now;
				opacity = 1;
				//current_fs.css({'transform': 'scale('+scale+')'});
				next_fs.css({'opacity': opacity});
			}, 
			duration: 1, 
			complete: function(){
				current_fs.hide();
			}, 
			//Esto viene del plugin easing
			easing: 'easeInOutBack'
		});*/
    });


    $(".previous").click(function(){
		current_fs = $(this).parent();
		previous_fs = $(this).parent().prev();

		
		//des-activar paso actual en progreso
		$("#progreso li").eq($("fieldset").index(current_fs)).removeClass("active");
		
		//mostrar el fieldset anterior
		previous_fs.show(); 
		current_fs.hide();
		opacity : 1;
		previous_fs.css({'opacity': opacity});
		//ocultar el fieldset actual con estilo
		/*current_fs.animate({opacity: 0}, {
			step: function(now, mx) {
				//as the opacity of current_fs reduces to 0 - stored in "now"
				//1. escalar previous_fs de 80% a 100%
				//scale = 0.8 + (1 - now) * 0.2;
				//2. take current_fs to the right(50%) - from 0%
				//left = ((1-now) * 50)+"%";
				//3. increase opacity of previous_fs to 1 as it moves in
				opacity = 1 - now;
				//current_fs.css({'left': left});
				previous_fs.css({'opacity': opacity});
			}, 
			duration: 1, 
			complete: function(){
				current_fs.hide();
			}, 
			//Esto viene del plugin easing
			easing: 'easeInOutBack'
			});*/
    });


}









/*
$(".submit").click(function(){
	return false;
});*/