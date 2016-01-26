<?php


$verificar_usuario = new VerificarUsuario();

class VerificarUsuario{
    
    
    protected $respuesta;
    
    
   public function __construct(){
        
       $this->revisar_usuario();
       
   }
    
    
    protected function revisar_usuario(){
        
        extract($_POST);
        
        if($usuario=="" or $pass=="")
                $this->resultado = 0;
        else
                $this->resultado = 1;
        
        
    }
    
    function __destruct(){
            echo $this->resultado;
     }
    
    
    
    
}




?>