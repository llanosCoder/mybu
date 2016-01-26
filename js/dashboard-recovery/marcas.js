var categoriaMarcaNueva = '';

$(document).ready(function(){
    $('#cl-wrapper').show();
    aMayusculas();
    $("#agregar_marca_sin_categoria").modalEffects();
    $('#contenidoCategorias').append('<div id="loading"><i class="fa fa-spinner fa-spin fa-4x"></i></div>');
    
    var treeCategorias = '<ul class="nav nav-list treeview collapse">';
    treeCategorias += '<li><label class="tree-toggler nav-header" onClick="cargarMarcas(\'all\');" id="all"><i class="fa fa-folder-o icon-tree"></i>Mostrar todo</label></li>';
    var jerarquia = 1;
    var ul = false;
    $.post("classes/obtener_categorias.php",
        {eId: "1"},
        function(data){
            data = $.parseJSON(data);
            $.each(data, function(i, datos) {
                if(datos.jerarquia > jerarquia){
                    treeCategorias += '<ul  class="nav nav-list tree">';
                    ul = true;
                }else{
                    ul = false;
                    if(datos.jerarquia < jerarquia){
                        var diferenciaCategorias = jerarquia - datos.jerarquia;
                        for(var i = 0; i < diferenciaCategorias; i++){
                            treeCategorias += '</li></ul>';
                        }
                    }
                }
                if(i > 0 && !ul){
                    treeCategorias += '</li>';
                }
                treeCategorias += '<li><label class="tree-toggler nav-header" onClick="cargarMarcas(\''+datos.desc+'\');" id="'+datos.desc+'"><i class="fa fa-folder-o icon-tree"></i>'+datos.nombre+'<i class="fa fa-plus-circle agregar_marca"  data-modal="form-agregar-marca" onClick="setMarca(\''+datos.desc+'\', true, 0);"></i></label>';
                jerarquia = datos.jerarquia;
                if(i == data.length-1){
                    var diferenciaCategorias = jerarquia - 1;
                    for(var i = 0; i < diferenciaCategorias; i++){
                        treeCategorias += '</li></ul>';
                    }
                }
            });
        }
    ).done(function(){
        treeCategorias += '</li></ul>';
        //console.log(treeCategorias);
        $('#loading').hide();
        $('.tree-body').html(treeCategorias);
        $('.fa-plus-circle').modalEffects();
        $('label.tree-toggler').click(function () {
            var icon = $(this).children(".icon-tree");
            if(icon.hasClass("fa-folder-o")){
                icon.removeClass("fa-folder-o").addClass("fa-folder-open-o");
            }else{
                icon.removeClass("fa-folder-open-o").addClass("fa-folder-o");
            }        
            $(this).parent().children('ul.tree').toggle(300,function(){
                $(this).parent().toggleClass("open");
                $(".tree .nscroller").nanoScroller({ preventPageScrolling: true });
            });
      });
    });
});

function cargarMarcas(cId){
    var parametros = ['value', 'nombre', 'logo'];
    
    $.post('classes/obtener_marcas.php', {cId: cId, parametros: parametros}, function(data) {
        
        var datosRecibidos = $.parseJSON(data);
        var marcas = '<table id="table_marcas" class="table table-bordered"><thead><tr><th>Nombre</th><th>Logo</th><th>Edición</th><th>Eliminar</th></tr></thead><tbody>';
        $.each(datosRecibidos, function(i, datos){
            marcas += '<tr><td>'+datos.nombre+'</td><td>';
            if (datos.logo == '' || datos.logo == 'null' || datos.logo == 'undefined') {
                marcas += '-';
            }else{
                marcas += '<a href="#" rel="shadowbox" onClick="abrirFoto(\''+datos.logo+'\');"><i class="fa fa-picture-o fa-2x"></i></a></td>';
            }
            marcas += '</td>';
            marcas += '<td><a href="#" onClick="cargarDatos('+datos.value+', \''+datos.nombre+'\', \''+cId+'\');"><i class="fa fa-pencil-square fa-2x editar_marca" data-modal="form-agregar-marca"></i></a></td>';
            marcas += '<td><a href="#" onClick="eliminarMarca('+datos.value+', \''+datos.nombre+'\', \''+cId+'\');" style="color:red;"><i class="fa fa-times-circle fa-2x"></i></a></td></tr>';
        });
        marcas += '</tbody></table>';
        $('#pcont').html(marcas);
        $('#table_marcas').dataTable({
            "aaSorting": [[0, 'asc']],
            "oLanguage": {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ marcas",
                "sZeroRecords": "No se han encontrado marcas disponibles",
                "sEmptyTable": "No se han encontrado marcas disponibles",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando marcas de la 0 a la 0 de un total de 0 marcas",
                "sInfoFiltered": "(filtrado de un total de _MAX_ marcas)",
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
        $('.editar_marca').modalEffects();
    });
}

$('#logo').change(function () {

    var file = $("#logo")[0].files[0];
    if ((file.size || f.fileSize) > 2097152) {
        $('#btn_aceptar_modal').prop('disabled', true);
        mostrar_notificacion('Error', 'Imagen muy pesada. Máximo 2mb', 'warning');
    } else {
        var fileName = file.name;
        //file.name = file.name.toLowerCase();
        fileExtension = fileName.substring(fileName.lastIndexOf('.') + 1);
        switch (fileExtension.toLowerCase()) {
        case 'jpg':
        case 'gif':
        case 'png':
        case 'jpeg':
            $('#btn_aceptar_modal').prop('disabled', false);
            break;
        default:
            $('#btn_aceptar_modal').prop('disabled', true);
            mostrar_notificacion('Error', 'Extensión de imagen no válida', 'warning');
            break;
        }
    }
});

$('#pcont').on('click', function () {
    $('.editar_marca').modalEffects();
});

function cargarDatos(id, nombre, cId){
    $('#marca').val(nombre);
    setMarca(cId, false, id);
}

function setMarca(marca, marcaNueva, id){
    $('#pag-0').hide();
    if(marcaNueva){
        $('#btn_aceptar_modal_editar').hide();
        $('#btn_aceptar_modal').show();
        $( "#btn_aceptar_modal" ).unbind( "click");
        $( "#btn_aceptar_modal" ).bind( "click", function() {
            guardarModal(true, 0);
        });
        $('.modal-header').html('<h3>Nueva Marca</h3>');
    }else{
        $('#btn_aceptar_modal').hide();
        $('#btn_aceptar_modal_editar').show();
        $( "#btn_aceptar_modal_editar" ).unbind( "click");
        $( "#btn_aceptar_modal_editar" ).bind( "click", function() {
            guardarModal(false, id);
        });
        $('.modal-header').html('<h3>Editar Marca</h3>');
        
    }
    $(".formulario").find(':input').each(function() {
        var elemento= this;
        if($("#"+elemento.id).parent().hasClass('has-error')){
            $("#"+elemento.id).parent().removeClass('has-error');
            
        }
        if(marcaNueva)
            $("#"+elemento.id).val('');
    });
    categoriaMarcaNueva = marca;
    $("#form-agregar-marca-pag-1").show();
}

function abrirFoto(content){
    Shadowbox.init();
    Shadowbox.open({
        content:content,
        player: 'img'
    });
}

function guardarModal(nuevaMarca, id){
    
    $("#btn_aceptar_modal").unbind("click");
    $("#btn_aceptar_modal_editar").unbind("click");
    if(nuevaMarca)
        habilitarDeshabilitarBoton('btn_aceptar_modal', false);
    else
        habilitarDeshabilitarBoton('btn_aceptar_modal_editar', false);
    var datos = new Object();
    datos['marca'] = $("#marca").val();
    datos['logo'] = $("#logo").val();
    var focusTaken = false;
    for(valor in datos){
        if(datos[valor] == null || datos[valor] == ''){
            if($("#"+valor).hasClass('required')){
                $("#"+valor).parent().addClass('has-error');
                if(!focusTaken){
                    $("#"+valor).focus();
                    focusTaken = true;
                    mostrar_notificacion('Atención', 'Rellene los campos marcados', 'warning');
                }
            }
        }
    }
    if(!focusTaken){
        $(".formulario").append('<input type="hidden" name="categoria" value="'+categoriaMarcaNueva+'"></input>');
        $(".formulario").append('<input type="hidden" name="id" value="'+id+'"></input>');
        var formData = new FormData($(".formulario")[0]);
        
        $.ajax({
            url: 'classes/nueva_marca.php',  
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data){
                habilitarDeshabilitarBoton('btn_aceptar_modal', true);
                habilitarDeshabilitarBoton('btn_aceptar_modal_editar', true);
                $('#form-agregar-marca').removeClass('md-show');
                $(".formulario").each(function(){
                    this.reset();
                });
                switch(data){
                    case '0':
                        mostrar_notificacion('Error', 'Ha ocurrido un error al registrar su marca', 'danger');
                        break;
                    case '1':
                        if(!nuevaMarca){
                            cargarMarcas(categoriaMarcaNueva);
                        }else{
                            cargarMarcas(categoriaMarcaNueva[0]);
                        }
                        mostrar_notificacion('Éxito', 'Su marca se ha registrado exitosamente', 'success');
                        break;
                    case '2':
                        mostrar_notificacion('Error', 'Archivo de imagen con formato no válido', 'danger');
                        break;
                    default:
                        mostrar_notificacion('Error', 'Ha ocurrido un error inesperado. Por favor, contacte un administrador', 'danger');
                        break;
                }
            },
            error: function(){
                mostrar_notificacion('Error', 'Ha ocurrido un error al registrar su marca', 'danger');
            }
        });
    }else{
        if(nuevaMarca)
            habilitarDeshabilitarBoton('btn_aceptar_modal', true);
        else
            habilitarDeshabilitarBoton('btn_aceptar_modal_editar', true, 'Guardar Cambios');
    }

}

$('#agregar_marca_sin_categoria').click(function() {
    var pag0 = '<div id="form-agregar-marca-pag-0">';
    pag0 += '<div class="form-group"><label>Categoria</label>';
    pag0 += '<select id="select_categorias" name="select_categorias" multiple="multiple" class="form-control pag-1 required"></select>';
    pag0 += '</div>';
    pag0 += '<button type="button" id="btn_pag0_sgte" class="btn btn-primary btn-flat" onClick="setCategoria()">Seguir</button></div>';
    $("#pag-0").html(pag0);
    $("#pag-0").show();
    $(".modal-header").html("<h3>Nueva Marca</h3>");
    if($("#select_categorias").parent().hasClass('has-error'))
            $("#select_categorias").parent().removeClass('has-error');
    var url = 'classes/obtener_categorias.php', select_categorias = '<option value=""></option>';
    $.post(url,
        function(data){
            data = $.parseJSON(data);
            $.each(data, function(i, datos) {
                select_categorias += '<option value="'+datos.desc+'">'+datos.nombre+'</option>';
            });
        }
    ).done(function() {
        $('#btn_aceptar_modal').hide();
        $('#btn_aceptar_modal_editar').hide();
        $("#select_categorias").html(select_categorias);
        $("#form-agregar-marca-pag-1").hide();
        $("#form-agregar-marca-pag-0").show();
        $('#select_categorias').select2({
            tags: true
        });
    });
    
});

function setCategoria() {
    var categoria = $("#select_categorias").val();
    if(categoria != '' && categoria != 'undefined' && categoria != null){
        $("#form-agregar-marca-pag-1").show();
        $("#form-agregar-marca-pag-0").hide();
        setMarca(categoria, true, 0);
        
    }else{
        if(!$("#select_categorias").parent().hasClass('has-error'))
            $("#select_categorias").parent().addClass('has-error');
        mostrar_notificacion('Error', 'Seleccione una categoria', 'danger');
    }
}

function eliminarMarca(id, nombre, cId){
        alertify.confirm('Atención<i class="fa fa-exclamation-triangle fa-2x" style="color:yellow;"></i>', "¿Seguro quieres eliminar la marca "+nombre+"? Esta acción no puede deshacerse.",
        function(){
            var url = "classes/eliminar_marca.php";
            $.post(url,
                {id: id},
                function(data){
                    if(data > 0){
                        mostrar_notificacion('Éxito', 'Solicitud procesada satisfactoriamente', 'success');
                    }else{
                        mostrar_notificacion('Error', 'No se ha podido procesar su solicitud', 'danger');
                    }
                }
            ).done(function(){
                cargarMarcas(cId);
            });
        },
        function(){
        }
    ).set('labels', {ok:'Sí', cancel: 'Cancelar'});
}