<?php

$carro_compra = new CarroCompra();

class CarroCompra{

    protected $parametros = array(), $carro, $accion;
    protected $respuesta = array();

    public function __construct(){
        session_start();
        $this->procesar();
    }

    protected function procesar(){
        $this->accion = $_POST['accion'];
        switch($this->accion){
            case '1':
                $this->agregar_oferta();
                break;
            case '2':
                $this->cargar_carro();
                break;
            case '3':
                $this->agregar_oferta();
                break;
            case '4':
                $this->eliminar_oferta();
                break;
        }
    }
    
    protected function agregar_oferta(){
        $this->crear_carro();
        $this->parametros = $_POST['parametros'];
        for($i = 0; $i < count($this->parametros); $i++){
            $this->agregar_oferta_carro($this->parametros[$i]);   
        }
        $this->respuesta['resultado'] = 1;
    }
    
    protected function crear_carro(){
        $this->carro = $_POST['carro'];
        if(!isset($_SESSION[$this->carro])){
            $_SESSION[$this->carro] = array();
        }
    }
    
    protected function cargar_carro(){
        $this->crear_carro();
        $this->respuesta['carro'] = $_SESSION[$this->carro];
    }
    
    protected function eliminar_oferta(){
        $this->crear_carro();
        $this->parametros = $_POST['parametros'];
        $param = $this->parametros[0];
        foreach($_SESSION[$this->carro] as $indice=>$carro){
            if($carro['oferta'] == $param['oferta']){
                array_splice($_SESSION[$this->carro], $indice -1, 1);
            }
        }
        $this->respuesta['resultado'] = 1;
    }
    
    //Ofertas
    
    protected function agregar_oferta_carro($param){
        $agregado = false;
        foreach($_SESSION[$this->carro] as $indice=>$carro){
            if($carro['oferta'] == $param['oferta']){
                if($this->accion == 1){
                    $carro['cantidad'] += $param['cantidad'];
                }else{
                    $carro['cantidad'] = $param['cantidad'];
                }
                $_SESSION[$this->carro][$indice] = $carro;
                $agregado = true;
            }
        }
        if(!$agregado){
            $arr = array();
            foreach($param as $indice=>$valor){
                $arr[$indice] = $valor;
            }
            array_push($_SESSION[$this->carro], $arr);
        }
    }
    
    protected function existe_oferta($oferta){
        foreach($_SESSION[$this->carro] as $carro){
            if($carro['oferta'] == $oferta){
                return true;
            }
        }
        return false;
    }
    
    //Fin ofertas

    public function __destruct(){
        //print_r($_SESSION[$this->carro]);
        echo json_encode($this->respuesta);
    }

}

?>