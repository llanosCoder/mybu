<?php
$padre=0;
if(isset($_POST["padre"])){
    $padre=$_POST["padre"];
}
else{
    exit();
}
$producto = new Productos($padre);    
    
class Productos{
    protected $empresa_id = 1;
    protected $link;
    protected $sql_con;
    protected $datos = array();
    protected $jerarquia = 0;
    
    public function __construct($padre){
        include("conexion.php");
        $this->link = Conectarse();
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'],$this->link['pass'],$this->link['bd']);
        $this->obtener_productos($padre);
    }
        
    function obtener_productos($cat_padre, &$jerarquia = 1){
        $consulta = "SELECT categoria_id as cid, categoria_nombre as nombre, categoria_descripcion as descripcion, categoria_padre as padre FROM categoria WHERE categoria_padre = $cat_padre";
        $this->sql_con->set_charset("utf8");
        $rs = $this->sql_con->query($consulta);
        if($rs === false) {
            trigger_error('Ha ocurrido un error');
        } else {       
            while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
                $dato = array();
                $dato['nombre'] = $row['nombre'];
                $dato['desc'] = $row['descripcion'];
                $dato['id_padre'] = $row['cid'];
                array_push($this->datos, $dato);
            }
        }
        

    }
    
    function __destruct(){
        echo json_encode($this->datos);
    }
    
}
?>