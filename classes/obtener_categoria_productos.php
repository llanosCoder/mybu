<?php
$producto = new Productos();    
    
class Productos{
    
    //CONVERTIR EN POST
        protected $empresa_id = 1;
    //
    protected $link;
    protected $sql_con;
    protected $datos = array();
    protected $jerarquia = 0;
    
    public function __construct(){
        include("conexion.php");
        $this->link = Conectarse();
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'],$this->link['pass'],$this->link['bd']);
        $this->obtener_productos(0);
    }
        
    function obtener_productos($cat_padre, &$jerarquia = 1){
        $consulta = "SELECT categoria_id as cid, categoria_nombre as nombre, categoria_descripcion as descripcion, categoria_padre as padre FROM categoria WHERE categoria_padre = $cat_padre";
        $this->sql_con->set_charset("utf8");
        $rs = $this->sql_con->query($consulta);
        if($rs === false) {
            trigger_error('Ha ocurrido un error');
        } else {
            //$resultado = $rs->fetch_all(MYSQLI_ASSOC);
            while($row = $rs->fetch_assoc()){
                $dato = array();
                $dato['nombre'] = $row['nombre'];
                $dato['desc'] = $row['descripcion'];
                $dato['jerarquia'] = $jerarquia;
                array_push($this->datos, $dato);
                $subir_jerarquia = $jerarquia + 1;
                $this->obtener_productos($row['cid'], $subir_jerarquia);
            }
        }
        $rs->close();
    }
    
    function __destruct(){
        echo json_encode($this->datos);
    }
}
?>