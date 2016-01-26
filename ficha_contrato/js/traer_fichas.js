    var url = '../../classes/obtener_datos_sesion.php';
    var parametros = ['id'];
    var usuario = "";
    $.post(url, {
            parametros: parametros
        },
        function (data) {
            data = $.parseJSON(data);
            $.each(data, function (i, datos) {
                usuario = datos.id;
            });

        }

    ).done(function (data) {
    
        if(usuario != 0){
            
            
            var url2 = "clases/traer_fichas.php";
            var enviar = usuario;
            
            $.post (url2, {
                     user: enviar
                },
                    
                 function(data) {
                    
                    mandar = $.parseJSON(data);
                
                        $.each(mandar.cliente, function (i, item) {
                            alert(item.cli_rut);
                            $('#tabla').append("<tr id='cliente_"+item.cli_rut+"'>"
                                               +"<td>"+item.cli_id+"</td>"
                                               +"<td>"+item.cli_nombre+" "+item.cli_app+" "+item.cli_apm+"</td>"
                                               +"<td>"+item.cli_fantasia+"</td>"
                                               +"<td>"+item.cli_rut_emp+"</td>"
                                               +"<tr>"

                             );


                        });
                
                
                        $.each(mandar.contacto, function (i, item) {
                            $('#cliente_'+item.con_rut).append("<td>"+item.con_nombre+"</td><td>"+item.con_mail+"</td>"
                                               +"<td>"+item.con_tmovil+"</td><td>"+item.con_tfijo+"</td>"
                                              

                             );


                        });
                
                
                        $.each(mandar.producto, function (i, item) {
                            
                            $('#cliente_'+item.serv_cli_rut).append("<td>"+item.serv_tipoplan+"</td><td>"+item.serv_nombre_proveedor+"</td><td>"+item.vendedor+"</td>"
                                               
                                              

                             );


                        });
                
                
                        
                      


                    
                 }
            
            )
                
        }else{
            
                window.location.href = 'http://www.nfnempresas.com';
        }
   
    

});