$(document).ready(function(){
    
    //var nombre =   $('.next').parent('#paso1').hide();

    
    
        $("#formulario").validate({
						rules: {
                            
                            fecha_contrato:{
                                required: true
                            },
                            nmr_folio:{
                                required:true,
                                digits: true
                            },
                            //parte1
							tipo_cliente:{
							
								required: true,
							},
							rut:{
							
								required: true,
								rut : true,
							},
							apm:{
								required: true,
								solo_letras: true,
							},
							app:{
								required: true,
								solo_letras: true,
							},
							nombre:{
								required:true,
								solo_letras: true,
							},
							email:{
								required:true,
								email: true,
							},
							actividad:{
								required:true,
							},
							rs:{
								required:true,
							},
							nmb_fant:{
							
								required:true,
							},
							email_empresa:{
								required:true,
								email:true,
							},
							rut_empresa:{
							
								required: true,
								rut : true,
							},
							representante:{
								required:true,
								solo_letras: true,
							},
							rut_repres:{
								required:true,
								rut: true,
							},
							giro:{
								required: true,
							},
							// Parte 2
							direccion:{
								
								required: true,
							
							},
							tipo_calle:{
								required: true,
							},
							entre_calles:{
								required: true,
							},
							villa:{
								required: true,
							},
							comuna:{
								required:true,
								solo_letras: true,
							},
							ciudad:{
								required:true,
								solo_letras: true,
							},
							// Parte 3
							nombre_contacto:{
								
								required: true,
								solo_letras: true,
							
							},
							mail_contacto:{
								required:true,
								email: true,
							},
							
							tel_movil:{
								required: true,
								digits: true,
							},
							tel_fijo:{
								required: true,
								digits: true,
							},
							// Parte 4
							tipo_plan:{
								required:true,
							},
							anos_servicio:{
								required:true,
							},
							forma_pago:{
								required: true,
							},
							n_documento:{
								required: true,
								digits: true,
							},
                            nombre_proveedor:{
								required: true,
								solo_letras: true,
							},
                            vendedor:{
								required: true,
								solo_letras: true,
							}
							
						},
						highlight: function (element) {
							$(element).closest('.form-group').removeClass('has-success').addClass('has-error');
							//$("#paso1").removeClass('next');
							
							
						},
						unhighlight: function (element) {
							$(element).closest('.form-group').removeClass('has-error').addClass('has-success');
							//$("#paso1").addClass('next');
							
						},
						
						
					});
				
		
			$("#Verificacion").hide();
			$("#Verificacion_emp").hide();
			$("#Verificacion_repr").hide();
			
    
    
    
            $("#paso1").on("click",function(){
			
				$("#formulario").valid();
			
			});
			
			
			$("#paso2").on("click",function(){
			
				$("#formulario").valid();
			
			});
			
			$("#paso3").on("click",function(){
			
				$("#formulario").valid();
			
			});
            
            $("#paso4").on("click",function(){
			
				$("#formulario").valid();
			
			});
			
			
    
    
});