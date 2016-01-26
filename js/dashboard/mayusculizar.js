function aMayusculas() {
    $(':input').keyup(function(){
        var id = $(this).attr('id');
        $("#"+id).val($("#"+id).val().toUpperCase());
    });
}

 $(':input').keyup(function(){
    var id = $(this).attr('id'),
        valorOriginal = $(this).val(),
        valorNuevo = $(this).val().toUpperCase();
    if ($('#'+id).attr('type') != 'password') {
        if (valorOriginal !== valorNuevo) {
            $("#"+id).val($("#"+id).val().toUpperCase());
        }
    }
});