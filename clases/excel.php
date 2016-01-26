<?php

$excel = new Excel();


class Excel{

	protected $link;
	protected $sql_con;
	protected $datos = array();
	protected $datos_usuario = array();
	
	
	public function __construct(){
		session_start();
        require('../hosts.php');
        require('conexion_new.php');
        $hosteo = new Host();
        $hosteo->obtener_conexion(7);
        $this->set_conexion($hosteo->datos_conexion['host'], $hosteo->datos_conexion['user'], $hosteo->datos_conexion['pass'], $hosteo->datos_conexion['bd']);
		$this->verificar_fichas();
		
	}
	
	
	protected function set_conexion($host, $user, $pass, $bd){
	
		$conexion = new Conexion();
        $this->link = $conexion->Conectarse($host, $user, $pass, $bd);
        $this->sql_con = new mysqli($this->link['host'], $this->link['user'], $this->link['pass'], $this->link['bd']);
        $this->sql_con->set_charset('utf8');
	}
    
    
    
    protected function verificar_fichas(){
        
        extract($_GET);
        
        $this->datos_usuario["usuario"]=mysqli_real_escape_string($this->sql_con, $u);
            
        
        $consulta = "select * from cliente c left join ingreso i on i.ing_rut_usu = c.cli_rut";
        $consulta.=" left join contacto co on co.con_cli_rut=c.cli_rut";
        $consulta.=" left join servicio s on s.serv_cli_rut=c.cli_rut";
        
        $revisar=$this->sql_con->query($consulta);
        $cantidad=$revisar->num_rows;
        
        if($cantidad > 0 && $this->datos_usuario["usuario"]!="" )
            $this->crear_excel();
        else
            $this->datos["respuesta"]=0;
        
        
    }

    protected function crear_excel(){
        
          
               

           include_once('../lib/PHPExcel/PHPExcel.php');
           $objPHPExcel = new PHPExcel();
           $objPHPExcel->getActiveSheet()->setTitle("Fichas Contrato");

           


           $objPHPExcel->
            getProperties()
                ->setCreator("")
                ->setLastModifiedBy("")
                ->setTitle("")
                ->setSubject("")
                ->setDescription("")
                ->setKeywords("")
                ->setCategory("");    


            $tituloReporte = "Fichas Contrato";
            $titulosColumnas = array('#', 'Rut','Nombre','Apellido Paterno','Apellido Materno','Mail','Actividad','Rsocial','Fantasía','Mail Empleador','Rut Empleador','Representante Legal','Rut Representante','Giro','Nombre Contacto','Mail Contacto','TelefonoMóvil Contacto','TelefonoFijo Contacto','Dirección','Tipo Calle','Entre Calles','Villa/Población/Condominio','Tipo','Piso','Block','Comuna','Ciudad','Plan','Años','Forma Pago','Nº Dcto','Nombre Proveedor','Vendedor','Observación','Fecha Contrato','Nº Folio','Ingreso','Usuario');

            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1',$tituloReporte) 
            ->setCellValue('A3',  $titulosColumnas[0])  
            ->setCellValue('B3',  $titulosColumnas[1])
            ->setCellValue('C3',  $titulosColumnas[2])
            ->setCellValue('D3',  $titulosColumnas[3])
            ->setCellValue('E3',  $titulosColumnas[4])
            ->setCellValue('F3',  $titulosColumnas[5])
            ->setCellValue('G3',  $titulosColumnas[6])
            ->setCellValue('H3',  $titulosColumnas[7])
            ->setCellValue('I3',  $titulosColumnas[8])
            ->setCellValue('J3',  $titulosColumnas[9])
            ->setCellValue('K3',  $titulosColumnas[10])
            ->setCellValue('L3',  $titulosColumnas[11])
            ->setCellValue('M3',  $titulosColumnas[12])
            ->setCellValue('N3',  $titulosColumnas[13])
            ->setCellValue('O3',  $titulosColumnas[14])
            ->setCellValue('P3',  $titulosColumnas[15])
            ->setCellValue('Q3',  $titulosColumnas[16])
            ->setCellValue('R3',  $titulosColumnas[17])
            ->setCellValue('S3',  $titulosColumnas[18])
            ->setCellValue('T3',  $titulosColumnas[19])
            ->setCellValue('U3',  $titulosColumnas[20])
            ->setCellValue('V3',  $titulosColumnas[21])
            ->setCellValue('W3',  $titulosColumnas[22])
            ->setCellValue('X3',  $titulosColumnas[23])
            ->setCellValue('Y3',  $titulosColumnas[24])
            ->setCellValue('Z3',  $titulosColumnas[25])
            ->setCellValue('AA3',  $titulosColumnas[26])
            ->setCellValue('AB3',  $titulosColumnas[27])
            ->setCellValue('AC3',  $titulosColumnas[28])
            ->setCellValue('AD3',  $titulosColumnas[29])
            ->setCellValue('AE3',  $titulosColumnas[30])
            ->setCellValue('AF3',  $titulosColumnas[31])
            ->setCellValue('AG3',  $titulosColumnas[32])
            ->setCellValue('AH3',  $titulosColumnas[33])
            ->setCellValue('AI3',  $titulosColumnas[34])
            ->setCellValue('AJ3',  $titulosColumnas[35])
            ->setCellValue('AK3',  $titulosColumnas[36])
            ->setCellValue('AL3',  $titulosColumnas[37]);
        
            $consulta = "select * from cliente c left join ingreso i on i.ing_rut_usu = c.cli_rut";
            $consulta.=" left join contacto co on co.con_cli_rut=c.cli_rut";
            $consulta.=" left join servicio s on s.serv_cli_rut=c.cli_rut";
            $consulta.=" left join direccion d on d.dir_cli_rut=c.cli_rut";
        
            $traer = $this->sql_con->query($consulta);
            $i = 4;
            while($arr = $traer->fetch_assoc()){
                
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $arr["cli_id"])
                    ->setCellValue('B'.$i, $this->rut($arr["cli_rut"]))
                    ->setCellValue('C'.$i, $arr["cli_nombre"])
                    ->setCellValue('D'.$i, $arr["cli_app"])
                    ->setCellValue('E'.$i, $arr["cli_apm"])
                    ->setCellValue('F'.$i, $arr["cli_mail"])
                    ->setCellValue('G'.$i, $arr["cli_actividad"])
                    ->setCellValue('H'.$i, $arr["cli_rsocial"])
                    ->setCellValue('I'.$i, $arr["cli_fantasia"])
                    ->setCellValue('J'.$i, $arr["cli_mail_emp"])
                    ->setCellValue('K'.$i, $this->rut($arr["cli_rut_emp"]))
                    ->setCellValue('L'.$i, $arr["cli_rep_legal"])
                    ->setCellValue('M'.$i, $this->rut($arr["cli_rut_rep"]))
                    ->setCellValue('N'.$i, $arr["cli_giro"])
                    ->setCellValue('O'.$i, $arr["con_nombre"])
                    ->setCellValue('P'.$i, $arr["con_mail"])
                    ->setCellValue('Q'.$i, $arr["con_tmovil"])
                    ->setCellValue('R'.$i, $arr["con_tfijo"])
                    ->setCellValue('S'.$i, $arr["dir_direccion"])
                    ->setCellValue('T'.$i, $this->tipo_calle($arr["dir_tipocalle"]))
                    ->setCellValue('U'.$i, $arr["dir_entrecalles"])
                    ->setCellValue('V'.$i, $arr["dir_valle"])
                    ->setCellValue('W'.$i, $this->tipo_calle_dp($arr["dir_tipodp"]))
                    ->setCellValue('X'.$i, $arr["dir_piso"])
                    ->setCellValue('Y'.$i, $arr["dir_block"])
                    ->setCellValue('Z'.$i, $arr["dir_comuna"])
                    ->setCellValue('AA'.$i, $arr["dir_ciudad"])
                    ->setCellValue('AB'.$i,$this->nombre_plan($arr["serv_tipoplan"]))
                    ->setCellValue('AC'.$i,$arr["serv_anos"])
                    ->setCellValue('AD'.$i,$arr["serv_formapago"])
                    ->setCellValue('AE'.$i,$arr["serv_ndoc"])
                    ->setCellValue('AF'.$i,$arr["serv_nombre_proveedor"])
                    ->setCellValue('AG'.$i,$arr["serv_vendedor"])
                    ->setCellValue('AH'.$i,utf8_encode($arr["serv_obs"]))
                    ->setCellValue('AI'.$i,$arr["ing_fecha_contrato"])
                    ->setCellValue('AJ'.$i,$arr["ing_nmr_folio"])
                    ->setCellValue('AK'.$i,$arr["ing_ingreso"])
                    ->setCellValue('AL'.$i,$arr["ing_usuario"]);
                    
                
               $i++;
                
            }

        /*$i = 4;// desde donde parte en la casilla A el registro en este caso del A4 en adelante, lo mismo para C y etc...   
          while ($registro = mysql_fetch_object ($resultado)) {

              $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $registro->registro)
                    ->setCellValue('C'.$i, $registro->rbd);
               $i++;

           }

         }*/


        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Fichas Clientes.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
        $objWriter->save('php://output');
        exit();
   
    }
    
     protected function rut( $rut ) {
        return number_format( substr ( $rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $rut, strlen($rut) -1 , 1 );
    }
    
    protected function nombre_plan($tipo){
        
        $plan = "";
        
        switch ($tipo){
            
            case 1:
                $plan = "INICIO";
            break;
            
            
        }
        return $plan;
        
    }
    
    
    protected function tipo_calle_dp($tipo){
        
        $calle = "";
        
        switch ($tipo){
            
            case 1:
                $calle = "DEPARTAMENTO";
            break;
            
            case 2:
                $calle = "OFICINA";
            break;
            
            
        }
        return $calle;
        
    }
    
    
    protected function tipo_calle($tipo){
        
        $calle = "";
        
        switch ($tipo){
            
            case 1:
                $calle = "PASAJE";
            break;
            
            case 2:
                $calle = "CALLE";
            break;
            
            case 3:
                $calle = "AVENIDA";
            break;
            
            
        }
        return $calle;
        
    }
        
        
}

?>