function eliminarValor(arr, val){
    for(var i = 0; i < arr.length; i++){
        if(arr[i] == val){
            arr.splice(i, 1);
        }
    }
}

function existeValor(arr, val){
    var cont = 0;
    for(var i = 0; i < arr.length; i++){
        if(arr[i] == val){
            cont++;
        }
    }
    return cont;
}