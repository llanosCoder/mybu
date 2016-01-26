function aMayusculas() {
    $(':input').keyup(function(){
        var id = $(this).attr('id');
        $("#"+id).val($("#"+id).val().toUpperCase());
    });
}

 $(':input').keyup(function(){
    var id = $(this).attr('id');
    $("#"+id).val($("#"+id).val().toUpperCase());
});