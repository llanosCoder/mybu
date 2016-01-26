function mostrar_notificacion(type, text, class_name, position){
    position = (position) ? position : 'bottom-left';
    $.gritter.add({
        title: type,
        text: text,
        class_name: class_name,
        position: position
    });
};