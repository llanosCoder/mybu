function sanear_numero(num) {
    'use strict';
    while(num.indexOf('.') != -1){
        num = num.replace('.', '');
    }
    num = num.replace('$', '');
    return num;
}