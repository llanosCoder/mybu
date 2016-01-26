    var url = '../classes/obtener_datos_sesion.php';
    var parametros = ['id','rol'];
    var usuario = "";
    var rol = "";
    $.post(url, {
            parametros: parametros
        },
        function (data) {
            data = $.parseJSON(data);
            $.each(data, function (i, datos) {
                usuario = datos.id;
                rol = datos.rol;
            });

        }

    ).done(function (data) {

        if (usuario != 0 && rol != 1) {
            
            
            $("#excel").on("click", function(){
                
                window.location.href = 'http://www.nfnempresas.com/clases/excel.php?u='+usuario+'';
                
            });


            var url2 = "../clases/traer_fichas.php";
            var enviar = usuario;
            var tabla ='<table class="table table-bordered responsive" id="tabla_fichas"><thead><tr><th># Folio</th><th>Fecha Ing.</th><th>Nombre Cliente</th><th>Nombre Fantasía</th><th>Rut Empresa</th><th>Nombre Contacto</th><th>Mail Contacto</th><th>T-Móvil</th><th>T-Fijo</th><th>Plan</th><th>Nombre Proveedor</th><th>Vendedor</th><th>Detalle</th></tr></thead><tbody>';

            $.post(url2, {
                    user: enviar,
                    rol : rol
                },
                function (data) {
                    mandar = $.parseJSON(data);
                    
                    $.each(mandar.cliente, function (i, item){
                       
                        tabla += "<tr>"
                                +"<td>"+item.nmr_folio+"</td>"
                                +"<td>"+item.fecha_ing+"</td>"
                                +"<td>"+ item.cli_nombre + " " + item.cli_app + " " + item.cli_apm +"</td>"
                                +"<td>"+ item.cli_fantasia +"</td>"
                                +"<td>"+ item.cli_rut_emp +"</td>"
                                +"<td>"+ item.con_nombre +"</td>"
                                +"<td>"+ item.con_mail +"</td>"
                                +"<td>"+ item.con_tmovil +"</td>"
                                +"<td>"+ item.con_tfijo +"</td>"
                                +"<td>"+ item.serv_tipoplan +"</td>"
                                +"<td>"+ item.serv_nombre_proveedor +"</td>"
                                +"<td>"+ item.vendedor +"</td>"
                                +"<td align='center'><i class='fa fa-edit fa-2x ver-detalle' id="+item.nmr_folio+" data-modal='ver-todo'></i></td>"
                                +"</tr>"

                    });
                
                    
                    
                    tabla += '</tbody></table>';
                    $("#datatable1").html(tabla);
                    $(".ver-detalle").modalEffects();
                    $("#tabla_fichas").DataTable({
                            "aaSorting": [[0, 'asc']],
                            "oLanguage": {
                                "sProcessing": "Procesando...",
                                "sLengthMenu": "Mostrar _MENU_ ",
                                "sZeroRecords": "No se han encontrado datos disponibles",
                                "sEmptyTable": "No se han encontrado datos disponibles",
                                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                                "sInfoEmpty": "Mostrando  del 0 al 0 de un total de 0 ",
                                "sInfoFiltered": "(filtrado de un total de _MAX_ datos)",
                                "sInfoPostFix": "",
                                "sSearch": "Buscar: ",
                                "sUrl": "",
                                "sInfoThousands": ",",
                                "sLoadingRecords": "Cargando...",
                                "oPaginate": {
                                    "sFirst": "Primero",
                                    "sLast": "Último",
                                    "sNext": "Siguiente",
                                    "sPrevious": "Anterior"
                                },
                                "oAria": {
                                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                                }
                            }
                    });
                
                    $(".ver-detalle").modalEffects();
                
                    $(".ver-detalle").on("click", function(){
                        
                        var nmr_folio = $(this).attr("id");
                        
                        
                        $.post("../clases/traer_fichas.php", {
                                nmr_folio: nmr_folio,
                                tipo : "1"
                            },
                            function (data) {
                                todo = $.parseJSON(data);
                                    
                                $.each(todo.todo, function(i, item){
                                    
                                    
                                       $("#todos").html(
               '<h4>Datos Cliente</h4><hr>'
               +'<b><i class="fa fa-angle-double-right"></i> Nombre: '+item.cli_nombre+" "+item.cli_app+" "+item.cli_apm+' <br>'                      +'<i class="fa fa-angle-double-right"></i> Rut: '+item.cli_rut+' <br>'
               +'<i class="fa fa-angle-double-right"></i> Mail: '+item.cli_mail+' <br>' 
               +'<i class="fa fa-angle-double-right"></i> Actividad: '+item.cli_actividad+' <br>'
               +'<i class="fa fa-angle-double-right"></i> Razón Social: '+item.cli_rsocial+' <br>'
               +'<i class="fa fa-angle-double-right"></i> Nombre Fantasía: '+item.cli_fantasia+' <br>'
               +'<i class="fa fa-angle-double-right"></i> Mail Empleador: '+item.cli_mail_emp+' <br>'
               +'<i class="fa fa-angle-double-right"></i> Rut Empleador: '+item.cli_rut_emp+' <br>'
               +'<i class="fa fa-angle-double-right"></i> Representante Legal: '+item.cli_rep_legal+' <br>'
               +'<i class="fa fa-angle-double-right"></i> Rut Representante: '+item.cli_rut_rep+' <br>'
               +'<i class="fa fa-angle-double-right"></i> Giro: '+item.cli_giro+' <br><br>'
                                           
                                           
               +'<hr><h4>Datos Contacto</h4>'
               +'<i class="fa fa-angle-double-right"></i> Nombre: '+item.con_nombre+' <br>'
               +'<i class="fa fa-angle-double-right"></i> Mail: '+item.con_mail+' <br>' 
               +'<i class="fa fa-angle-double-right"></i> Teléfono Móvil: '+item.con_tmovil+' <br>'
               +'<i class="fa fa-angle-double-right"></i> Teléfono Fijo: '+item.con_tfijo+' <br>'
                                           
               +'<hr><h4>Datos Dirección</h4>'
               +'<i class="fa fa-angle-double-right"></i> Dirección: '+item.dir_direccion+' <br>'
               +'<i class="fa fa-angle-double-right"></i> Tipo de calle: '+item.dir_tipocalle+' <br>' 
               +'<i class="fa fa-angle-double-right"></i> Entre las calles: '+item.dir_entrecalles+' <br>'
               +'<i class="fa fa-angle-double-right"></i> Villa/Población/Condominio: '+item.dir_valle+' <br>'
               +'<i class="fa fa-angle-double-right"></i> Tipo: '+item.dir_tipodp+' <br>' 
               +'<i class="fa fa-angle-double-right"></i> Piso: '+item.dir_piso+' <br>'
               +'<i class="fa fa-angle-double-right"></i> Block: '+item.dir_block+' <br>'
               +'<i class="fa fa-angle-double-right"></i> Comuna: '+item.dir_comuna+' <br>' 
               +'<i class="fa fa-angle-double-right"></i> Ciudad: '+item.dir_ciudad+' <br>'
                                           
                                           
                +'<hr><h4>Datos Servicio</h4>'
               +'<i class="fa fa-angle-double-right"></i> Plan: '+item.serv_tipoplan+' <br>'
               +'<i class="fa fa-angle-double-right"></i> Años: '+item.serv_anos+' <br>' 
               +'<i class="fa fa-angle-double-right"></i> Forma de pago: '+item.serv_formapago+' <br>'
               +'<i class="fa fa-angle-double-right"></i> Nº Documento: '+item.serv_ndoc+' <br>'
               +'<i class="fa fa-angle-double-right"></i> Nombre Proveedor: '+item.serv_nombre_proveedor+' <br>' 
               +'<i class="fa fa-angle-double-right"></i> Vendedor: '+item.serv_vendedor+' <br>'
               +'<i class="fa fa-angle-double-right"></i> Observación: '+item.serv_obs+' <br>'
                                           
                                           
              +'<hr><h4>Ingreso</h4>'
               +'<i class="fa fa-angle-double-right"></i> Fecha del contrato: '+item.ing_fecha_contrato+' <br>'
               +'<i class="fa fa-angle-double-right"></i> Nmr Folio: '+item.ing_nmr_folio+' <br>' 
               +'<i class="fa fa-angle-double-right"></i> Ingreso: '+item.ing_ingreso+' <br>'
               +'<i class="fa fa-angle-double-right"></i> Usuario: '+item.ing_usuario+' <br>'
        
                                   );
                                       
                                });
                            }
                        );
                        
                    });
                
                }

          )

        } else {

            window.location.href = 'http://www.nfnempresas.com';
        }
        
        
        /*$("#descargar_excel").on("click",function(){
                
              window.location.href= "http://www.nfnempresas.com/clases/excel.php";
                
                    
            
        });*/
        
        
    });