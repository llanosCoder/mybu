<?php
    session_start();
    setcookie("datos_sesion","",time()-3600);
    $parametros_cookies = session_get_cookie_params(); 
    setcookie(session_name(),0,1,$parametros_cookies["path"]);
    if(session_destroy())
        echo 1;
    else
        echo 0;
    
    
?>