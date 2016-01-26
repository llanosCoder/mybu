$(document).ready(function(){

    $("#tabla_bodega").DataTable();
    $('#contenidoCategorias').append('<div id="loading"><i class="fa fa-spinner fa-spin fa-4x"></i></div>');
    var treeCategorias = '<ul class="nav nav-list treeview collapse">';
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
                treeCategorias += '<li><label class="tree-toggler nav-header" onClick="cargarProductos(\''+datos.desc+'\');" id="'+datos.desc+'"><i class="fa fa-folder-o icon-tree"></i>'+datos.nombre+'<i class="fa fa-plus-circle agregar_producto" data-modal="form-agregar-producto" onClick="setMarca(\''+datos.desc+'\', 0);"></i></label>';
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
        $('#contenidoCategorias').append(treeCategorias);
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