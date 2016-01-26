<?php
/**
 * database.conf.php
 * Universal Secure DB configuration
 *
 * @author     Iván Valenzuela
 * @copyright  Gestsol - Gestión y Soluciones Tecnológicas
 * @version    1.0
 */

class Host{
    public $hosting;
    public $datos = array();
    
    public function __construct($server){
        $this->hosting = $server;
        $this->obtenerConexion();
    }
    
    public function obtenerConexion(){
        switch($this->hosting){
            case '1':
                $this->datos['host'] = 'imasdgroup.cl';
                $this->datos['user'] = 'root';
                $this->datos['pass'] = 'gestsol2015*_';
                $this->datos['bd'] = 'nfn_admin';
                break;
            case '2':
                $this->datos['host'] = '192.168.10.100';
                $this->datos['user'] = 'root';
                $this->datos['pass'] = 'gestsol2146';
                $this->datos['bd'] = 'mediacenter';
                break;
            case '3':
                $this->datos['host'] = 'localhost';
                $this->datos['user'] = 'root';
                $this->datos['pass'] = 'gestsol2146';
                $this->datos['bd'] = 'mediacenter';
                break;
			case '4':
                $this->datos['host'] = 'gestsol.cl';
                $this->datos['user'] = 'gestsol';
                $this->datos['pass'] = 'gest200';
                $this->datos['bd'] = 'gestsol_pullmanadmin';
                break;
            default:
                $this->datos['host'] = 'gestsol.cl';
                $this->datos['user'] = 'gestsol';
                $this->datos['pass'] = 'gest200';
                $this->datos['bd'] = 'gestsol_media';
                break;
        }
    }   
}
?>