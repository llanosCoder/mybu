<?php
class Gastos{
    protected $empresa_id = 1;
    protected $link;
    protected $sql_con;
    protected $datos = array();
    
    public function __construct(){
        session_start();
        require('../hosts.php');
        require('conexion_new.php');
        $hosteo = new Host();
        $hosteo->obtener_conexion(0);
        $hosteo->datos_conexion['bd']= 'mybu_gastos';
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
    }
    protected function set_conexion($host, $user, $pass, $bd){
        $conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
    }
    protected function set_host($host){
        $hosteo = new Host();
        $hosteo->obtener_conexion($host);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
    }
    
    function obtener_categorias(){
        $consulta = "SELECT cat_id AS c_id, cat_descripcion AS c_nombre,cat_padre as c_padre FROM categoria";
        $this->sql_con->set_charset("utf8");
        $rs = $this->sql_con->query($consulta);
        if($rs === false) {
            trigger_error('Ha ocurrido un error');
        } else {       
            while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
                $dato = array();
                $dato['id'] = $row['c_id'];
                $dato['nombre'] = $row['c_nombre'];
                $dato['padre'] = $row['c_padre'];
                array_push($this->datos, $dato);
            }
        }
        
    }
    
    function obtener_medios(){
        $consulta = "SELECT medio_id AS m_id, medio_descripcion AS m_nombre FROM medio";
        $this->sql_con->set_charset("utf8");
        $rs = $this->sql_con->query($consulta);
        if($rs === false) {
            trigger_error('Ha ocurrido un error');
        } else {       
            while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
                $dato = array();
                $dato['id'] = $row['m_id'];
                $dato['nombre'] = $row['m_nombre'];
                array_push($this->datos, $dato);
            }
        }
    }
    function obtener_estadistica(){
        $eid = $_SESSION['empresa'];
        $consulta = 'SELECT c.cat_descripcion as cat, SUM(g.gasto_total) as total FROM gasto g JOIN categoria c ON c.cat_id=g.cat_id WHERE empresa_id='.$eid.' AND month(gasto_fecha)=month(NOW()) AND year(gasto_fecha)=year(NOW()) GROUP BY g.cat_id ORDER BY total DESC LIMIT 1';
        $this->sql_con->set_charset("utf8");
        $rs = $this->sql_con->query($consulta);
        $dato = array();
        if($rs === false) {
            trigger_error('Ha ocurrido un error');
        } else {       
            while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
                $dato['cat'] = $row['cat'];
                $dato['total'] = $row['total'];
            }
        }
        
        $consulta = 'SELECT c.cat_descripcion as cat, SUM(g.gasto_total) as total FROM gasto g JOIN categoria c ON c.cat_id=g.cat_id WHERE empresa_id='.$eid.' AND month(gasto_fecha)=month(NOW())-1 AND year(gasto_fecha)=year(NOW()) GROUP BY g.cat_id ORDER BY total DESC LIMIT 1';
        $this->sql_con->set_charset("utf8");
        $rs = $this->sql_con->query($consulta);
        if($rs === false) {
            trigger_error('Ha ocurrido un error');
        } else {       
            while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
                $dato['cat_anterior'] = $row['cat'];
                $dato['total_anterior'] = $row['total'];
            }
        }
        
        $consulta = 'SELECT SUM(g.gasto_total) as total FROM gasto g WHERE empresa_id='.$eid.' AND month(gasto_fecha)=month(NOW()) AND year(gasto_fecha)=year(NOW()) LIMIT 1';
        $this->sql_con->set_charset("utf8");
        $rs = $this->sql_con->query($consulta);
        if($rs === false) {
            trigger_error('Ha ocurrido un error');
        } else {       
            while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
                $dato['total_mes'] = $row['total'];
            }
        }
        array_push($this->datos, $dato);
        
    }
    
    
    public function almacenar_gastos($f, $d, $c, $m, $i, $t){
        $insercion_gasto = $this->sql_con->prepare("INSERT INTO gasto( gasto_fecha, gasto_descripcion, cat_id, medio_id, gasto_documento_id,
          gasto_total, usuario_id, empresa_id) 
          VALUES (?,?,?,?,?,?,?,?)");
        $uid = $_SESSION['id'];
        $eid = $_SESSION['empresa'];
        
        $insercion_gasto->bind_param('ssiisiii',
          $f,
          $d,
          $c,
          $m,
          $i,
          $t,
          $uid,
          $eid);
        $insercion_gasto->execute();
        
        $dato = array();
        $dato['success']=0;
        if($this->sql_con->affected_rows>0)
            $dato['success'] = 1;
        array_push($this->datos, $dato);
        $insercion_gasto->close();
    }
    
    protected function obtener_nombre_usuario($u_id){
        $this->set_host(0);
        $consulta = "SELECT usuario_nombres as nombres, usuario_apellidos as apellidos FROM usuario WHERE usuario_id = $u_id";
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            return 'Indefinido';
        }else{
            $row = $rs->fetch_assoc();
            return $row['nombres'] . ' ' . $row['apellidos'];
        }
    }
    
    public function obtener_detalle_gastos($parametros){
        $empresa_id = $_SESSION['empresa'];
        $consulta = "SELECT DATE(g.gasto_fecha) as fecha, g.gasto_total AS total, g.gasto_descripcion as descripcion, c.cat_descripcion as categoria, m.medio_descripcion as medio, g.gasto_documento_id as documento, g.usuario_id as usuario FROM gasto g JOIN medio m ON g.medio_id = m.medio_id JOIN categoria c ON c.cat_id = g.cat_id WHERE g.empresa_id = $empresa_id";
        foreach($parametros as $indice=>$valor) {
            $valor = mysqli_real_escape_string($this->sql_con, $valor);
            switch ($indice) {
                case '':
                    break;
                case 'categorias':
                    if($valor != 0 && $valor != '' && $valor != ' ') {
                        $consulta .= ' AND g.cat_id = ' . $valor;
                    }
                    break;
                case 'monto_minimo':
                    if($valor != '' && $valor != ' ') {
                        $consulta .= ' AND g.gasto_total >= ' . $valor;
                    }
                    break;
                case 'monto_maximo':
                    if($valor != '' && $valor != ' ') {
                        $consulta .= ' AND g.gasto_total <= ' . $valor;
                    }
                    break;
                case 'f_inicio':
                    if($valor != '' && $valor != ' ') {
                        $consulta .= ' AND g.gasto_fecha >= "' . $valor . '"';
                    }
                    break;
                case 'f_termino':
                    if($valor != '' && $valor != ' ') {
                        $consulta .= ' AND g.gasto_fecha <= "' . $valor . '"';
                    }
                    break;
                    
            }
        }
        $consulta .= ' ORDER BY g.gasto_fecha DESC';
        $rs = $this->sql_con->query($consulta);
        if($rs === false){
            $this->datos['resultado'] = 0;
        }else{
            $this->datos['resultado'] = 1;
            $this->datos['gastos'] = array();
            while($row = $rs->fetch_assoc()){
                $datos = array();
                foreach($row as $indice=>$valor){
                    $datos[$indice] = $valor;
                }
                $datos['responsable'] = $this->obtener_nombre_usuario($row['usuario']);
                array_push($this->datos['gastos'], $datos);
            }
        }
    }
    
    function __destruct(){
        echo json_encode($this->datos);
    }
    
}
?>