<?php
    $marca = new Marcas();

    class Marcas{
        
        protected $link;
        protected $id;
        protected $datos = array(), $categorias = array();
        protected $sql_con;
        
        public function __construct(){
            session_start();
            require('../hosts.php');
            require('conexion_new.php');
            ini_set('display_errors', 'on');
            
            $hosteo = new Host();
            $hosteo->obtener_conexion(0);
            $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
            $this->id = mysqli_real_escape_string($this->sql_con, $_POST['cId']);
            if($this->id != 'all')
                $this->obtener_marcas();
            else
                $this->obtener_todas();
        }
        
        protected function set_conexion($host, $user, $pass, $bd){
            $conexion = new Conexion();
            $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
            $this->sql_con->set_charset('utf8');
        }
        
        protected function obtener_marcas(){
            $this->sql_con = new mysqli($this->link['host'], $this->link['user'],$this->link['pass'],$this->link['bd']);
            $this->sql_con->set_charset('utf8');
            $consulta_cat = "SELECT categoria_id as cid FROM categoria WHERE categoria_descripcion = '$this->id'";
            $rs = $this->sql_con->query($consulta_cat);
            $row = $rs->fetch_assoc();
            $this->id = $row['cid'];
            $rs->close();
            array_push($this->categorias, $this->id);
            
            $this->obtener_categorias($this->id);
            for($i = 0; $i < count($this->categorias); $i++){
                $c_id = $this->categorias[$i];
                $consulta_marca_cat = "SELECT marca_id as mid FROM categoria_marca WHERE categoria_id = $c_id";
                $rs_marca_cat = $this->sql_con->query($consulta_marca_cat);
                while($row_marca_cat = $rs_marca_cat->fetch_assoc()){
                    $marca_id = $row_marca_cat['mid'];
                    $consulta_marcas = "SELECT producto_marca_id as value, producto_marca_nombre as nombre, producto_marca_logo as logo FROM producto_marca WHERE producto_marca_id = $marca_id";
                    $rs2 = $this->sql_con->query($consulta_marcas);
                    if($rs2 === false) {
                        trigger_error('Ha ocurrido un error');
                    } else {
                        while ($row = $rs2->fetch_assoc()) {
                            $dato = array();
                            $dato['value'] = $row['value'];
                            $dato['nombre'] = $row['nombre'];
                            $dato['logo'] = $row['logo'];
                            array_push($this->datos, $dato);
                        }
                    }
                    $rs2->close();
                }
                $rs_marca_cat->close();
            }
        }

        protected function obtener_todas(){
            $consulta_marcas = "SELECT producto_marca_id as value, producto_marca_nombre as nombre, producto_marca_logo as logo FROM producto_marca";
            $rs2 = $this->sql_con->query($consulta_marcas);
            if($rs2 === false) {
                trigger_error('Ha ocurrido un error');
            } else {
                while ($row = $rs2->fetch_assoc()) {
                    $dato = array();
                    $dato['value'] = $row['value'];
                    $dato['nombre'] = $row['nombre'];
                    $dato['logo'] = $row['logo'];
                    array_push($this->datos, $dato);
                }
            }
            $rs2->close();
        }

    function obtener_categorias($cat_padre, &$jerarquia = 1){
        $consulta = "SELECT categoria_id as cid FROM categoria WHERE categoria_padre = '$cat_padre'";
        $rs = $this->sql_con->query($consulta);
        if($rs === false) {
            trigger_error('Ha ocurrido un error');
        } else {
            //$resultado = $rs->fetch_all(MYSQLI_ASSOC);
            while($row = $rs->fetch_assoc()){
                array_push($this->categorias, $row['cid']);
                $subir_jerarquia = $jerarquia + 1;
                $this->obtener_categorias($row['cid'], $subir_jerarquia);
            }
        }
        $rs->close();
    }
        
        public function __destruct(){
            $this->datos = array_map("unserialize", array_unique(array_map("serialize", $this->datos)));
            echo json_encode($this->datos);
        }
        
    }
?>