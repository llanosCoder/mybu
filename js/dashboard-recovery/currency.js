function currencyFormat (num , extra) {
    if(isNumber(num)==true)
        return extra+num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")
    return "no definido";
}

function isNumber(input){
    return (input - 0) == input && (''+input).replace(/^\s+|\s+$/g, "").length > 0;
}