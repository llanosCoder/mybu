<?php

$clase = new Clase();

class Clase{

    protected $link, $sql_con;
    protected $resultado;
    protected $refresco;
    
    public function __construct(){
        session_start();
        $this->procesar();
    }
    
    protected function procesar(){
        $this->refresco = $_POST['refresco'];
        if ($this->refresco == 0) {
            
            $this->refresco = 1;
        } else {
            $this->refresco = 0;
        }
        $_SESSION['refresco'] = $this->refresco;
    }
    
    public function __destruct(){
        echo $this->refresco;
    }
}

?>