<?php

class Host{

    public $hosting, $datos_conexion = array();

    public function __construct(){
        if(isset($_SESSION['host']))
            $this->hosting = $_SESSION['host'];
    }

    public function obtener_conexion($host){
        if($host != 0)
            $this->hosting = $host;
        switch($this->hosting){
            case '1':
                $this->datos_conexion['host'] = 'localhost';
                $this->datos_conexion['user'] = 'root';
                $this->datos_conexion['pass'] = '@DatabasePlatform2015';
                $this->datos_conexion['bd'] = 'nfn_admin';
                break;
            case '2':
                $this->datos_conexion['host'] = 'localhost';
                $this->datos_conexion['user'] = 'root';
                $this->datos_conexion['pass'] = '@DatabasePlatform2015';
                $this->datos_conexion['bd'] = 'nfn_ofertas';
                break;
            default:
                $this->obtener_host('localhost', 'root', '@DatabasePlatform2015', 'nfn_admin');
                break;
            case '5':
                $this->datos_conexion['host'] = 'localhost';
                $this->datos_conexion['user'] = 'root';
                $this->datos_conexion['pass'] = '@DatabasePlatform2015';
                $this->datos_conexion['bd'] = 'nfn_site';
                break;
            case '7':
                $this->datos_conexion['host'] = 'localhost';
                $this->datos_conexion['user'] = 'root';
                $this->datos_conexion['pass'] = '@DatabasePlatform2015';
                $this->datos_conexion['bd'] = 'nfn_contrato';
                break;

            case '6':
                $this->datos_conexion['host'] = 'localhost';
                $this->datos_conexion['user'] = 'root';
                $this->datos_conexion['pass'] = '@DatabasePlatform2015';
                $this->datos_conexion['bd'] = 'nfn_dashboard3';
            break;
            case '27':
                $this->datos_conexion['host'] = 'localhost';
                $this->datos_conexion['user'] = 'root';
                $this->datos_conexion['pass'] = '@DatabasePlatform2015';
                $this->datos_conexion['bd'] = 'mybu_log';
            break;
            case '28':
                $this->datos_conexion['host'] = 'localhost';
                $this->datos_conexion['user'] = 'root';
                $this->datos_conexion['pass'] = '@DatabasePlatform2015';
                $this->datos_conexion['bd'] = 'mybu_ads';
            break;
            case '43':
                $this->datos_conexion['host'] = 'localhost';
                $this->datos_conexion['user'] = 'root';
                $this->datos_conexion['pass'] = '@DatabasePlatform2015';
                $this->datos_conexion['bd'] = 'nfn_credito';
            break;
        }
    }

    protected function obtener_host($host, $user, $pass, $bd){
        $this->set_conexion($host, $user, $pass, $bd);
        $consulta = "SELECT bd FROM empresa_conexion WHERE host = $this->hosting";
        $rs = $this->sql_con->query($consulta)or die(mysql_error());
        $row = $rs->fetch_assoc();
        $this->datos_conexion['host'] = $host;
        $this->datos_conexion['user'] = $user;
        $this->datos_conexion['pass'] = $pass;
        $this->datos_conexion['bd'] = $row['bd'];
    }

    protected function set_conexion($host, $user, $pass, $bd){
            $conexion = new Conexion();
            $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
            $this->sql_con->set_charset('utf8');
        }

}

?>
