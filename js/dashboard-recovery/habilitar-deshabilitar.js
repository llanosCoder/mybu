function habilitarDeshabilitarBoton(id, habilitar, content){
    if(!content)
        content = 'Guardar';
    if(!habilitar){
        $("#"+id).addClass('disabled');
        $("#"+id).html('<i class="fa fa-spinner fa-spin fa-2x"></i>');
    }else{
        $("#"+id).removeClass('disabled');
        $("#"+id).html(content);
    }
}