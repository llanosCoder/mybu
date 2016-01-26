<?php
include("../clases/conectar.php");

$con=Conectar::Conexion();	
 extract($_GET);
 $sql="select rut_inst,dv_inst,razon_social from Institucion where razon_social='$emp'";
 $resultado = sqlsrv_query ($con, $sql, array(), array("Scrollable"=>"buffered"));
 $registros = sqlsrv_num_rows ($resultado);
 
 
 $contando_fecha=count($ano_mes);
 $mes=substr($ano_mes, 4);
 
 if($contando_fecha==5){
 
 $ano=substr($ano_mes, -5,4);
 
 }else{

 $ano=substr($ano_mes, -6,4); 
	
 }
 
 
 $nombre_mes="";
 
 switch ($mes){
											
		case 1:
			$nombre_mes="Enero";
		break;
		
		case 2:
			$nombre_mes="Febrero";
		break;
		
		case 3:
			$nombre_mes="Marzo";
		break;
		
		case 4:
			$nombre_mes="Abril";
		break;
		
		case 5:
			$nombre_mes="Mayo";
		break;
		
		case 6:
			$nombre_mes="Junio";
		break;
		
		case 7:
			$nombre_mes="Julio";
		break;
		
		case 8:
			$nombre_mes="Agosto";
		break;
		
		case 9:
			$nombre_mes="Septiembre";
		break;
		
		case 10:
			$nombre_mes="Octubre";
		break;
		
		case 11:
			$nombre_mes="Noviembre";
		break;
		
		case 12:
			$nombre_mes="Diciembre";
		break;									
											
}
 
 
if ($registros > 0) {

require('/fpdf/fpdf.php');


$pdf=new FPDF('L');
$pdf->AddPage();

// Imagen

$pdf->Image('serbimaAA.jpg',245,8,30);

// titulo	
$pdf->SetFont('Arial','B',12);
$pdf->SetXY(90,10);
$pdf->Write(1,"PLANILLA DE DESCUENTO $emp");
$pdf->SetXY(125,15);
$pdf->Write(1,'Mes de '.$nombre_mes.' del '.$ano);


// Informacion del empleador	
$pdf->SetFont('Arial','U',10);
$pdf->SetXY(10,30);
$pdf->Write(1,'Información del empleador');

 if ($registro = sqlsrv_fetch_object ($resultado)) {
      
		$rut_inst=$registro->rut_inst;
		$dv_inst=$registro->dv_inst;
      
   }  
		function rut( $rut ) {
				return number_format( substr ( $rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $rut, strlen($rut) -1 , 1 );
		}   

$rut_inst_formateado=rut($rut_inst.$dv_inst);		

$pdf->SetFont('Arial','B',9);
$pdf->SetXY(20,40);
$pdf->Write(1,'R.U.T');
$pdf->SetFont('Arial','',9);
$pdf->SetXY(40,40);
$pdf->Write(1,$rut_inst_formateado);
$pdf->SetFont('Arial','B',9);
$pdf->SetXY(100,40);
$pdf->Write(1,'Nombre o Razón Social');
$pdf->SetFont('Arial','',9);
$pdf->SetXY(170,40);
$pdf->Write(1,$emp);

//====================================================================

// Informacion del socio	
$pdf->SetFont('Arial','U',10);
$pdf->SetXY(10,50);
$pdf->Write(1,'Información del Socio');
/*
$pdf->SetDrawColor (200,200,200);
$pdf->SetFont('Arial','',10);
$pdf->SetXY(10,60);
$pdf->Cell(20,10,'Rut',1,1,'L');
$pdf->SetXY(23,60);
$pdf->Cell(60,10,'Nombre',1,1,'L');

$pdf->SetFont('Arial','',8);
$pdf->SetXY(83,60);
$pdf->Cell(20,10,'Cuota Social',1,1,'C');

$pdf->SetFont('Arial','',8);
$pdf->SetXY(103,60);
$pdf->Cell(14,10,'N° Cuota',1,1,'C');


$pdf->SetFont('Arial','',8);
$pdf->SetXY(117,60);
$pdf->Cell(20,10,'Couta Social2',1,1,'C');

$pdf->SetFont('Arial','',8);
$pdf->SetXY(137,60);
$pdf->Cell(14,10,'N° Cuota',1,1,'C');
*/


$pdf->SetDrawColor (200,200,200);
$pdf->SetXY (10, 60);
$pdf->SetFont ("Arial","",8);


$pdf->Cell (18,5,"RUT" ,1,0,"L");
$pdf->Cell (75,5,"Nombre" ,1,0,"L");
$pdf->SetFont ("Arial","",8);
$pdf->Cell (20,5,"Cuota Social" ,1,"C");
$pdf->Cell (20,5,"Crédito Social",1,0,"C");
$pdf->Cell (10,5,"Cuota",1,0,"C");
$pdf->Cell (23,5,"Crédito Social2",1,0,"C");
$pdf->Cell (10,5,"Cuota",1,0,"C");
$pdf->Cell (22,5,"Adicional Benef.",1,0,"C");
$pdf->Cell (18,5,"A descontar",1,0,"C");
$pdf->Cell (16,5,"Dscto Efec.",1,0,"C");
$pdf->Cell (16,5,"Benef(Adic)",1,0,"C");
$pdf->Cell (18,5,"Observación",1,1,"C");


		$traer="select p.rut_pers,p.dv_pers,p.nmb_pers,p.ape_pater_pers,p.ape_mater_pers";
		$traer.=",ddp.nmr_ctasocial,ddp.nmr_credito,ddp.nmr_seguro,ddp.cdg_tipo_seguro,ddp.nmr_cargas,ddp.nmr_solicitud,ddp.nmr_cuota,ddp.cdg_est_socio";
		$traer.=" from DetalleDescuentoPlanilla ddp";
		$traer.=" join Persona p on p.rut_pers=ddp.rut_socio";
		$traer.=" where ddp.rut_empleador='$rut_inst' and ddp.ano_mes='$ano_mes' and ddp.nmr_corr_credito<2";
		$traer.=" order by ddp.rut_socio desc";
		$ver=sqlsrv_query($con, $traer);
		
	   $suma=0;
	   $suma1=0;
	   $suma2=0;
	   $suma3=0;
	   
	   $verificar=0;
	   $credito2="";
	   $cuotas="";
	   
	   $cuotas_cuota2="";
	   $obs="";
	   $morosidad="";
	   $rut_moroso="";
	   $i=60;
		while($m=sqlsrv_fetch_array($ver)){
				
				$rut_socio=$m["rut_pers"].$m["dv_pers"];
				$solo_rut=$m["rut_pers"];
				$nombre_socio=trim($m["ape_pater_pers"])." ".trim($m["ape_mater_pers"]).", ".trim($m["nmb_pers"]);
				
				$cuota_social=$m["nmr_ctasocial"];
				$cuota_social_f=number_format($cuota_social,0,",", ".");
				
				$credito_social=$m["nmr_credito"];
				$credito_social_f=number_format($credito_social,0,",", ".");
				
				$seguro=$m["nmr_seguro"];
				$seguro_f=number_format($seguro,0,",", ".");
				
				
				$tipo_seguro=$m["cdg_tipo_seguro"];
				$carga=$m["nmr_cargas"];
				
				if($carga==0)
					$carga="";
				else
					$carga=$carga;
				

				if($tipo_seguro==""){
				
					$tipo_seguro="";
				
				}else{
					
					if($tipo_seguro=="S"){
						$tipo_seguro="CS";
					}else{
						if($tipo_seguro=="A" or $tipo_seguro=="B" or $tipo_seguro=="C")
						$tipo_seguro="CHP";
					}
					
				}
				
				
				$nmr_solicitud=$m["nmr_solicitud"];
				$nmr_cuota=$m["nmr_cuota"];
				
				$cdg_est_socio=$m["cdg_est_socio"];
				$obs="";
				switch ($cdg_est_socio){
					case 4:
					$obs="Ex Socio - Renunciado";
					break;
					
					case 12:
					$obs="Cese";
					break;
				}
				
				
			
				$credito2="";
				$cuotas_cuota2="";
				$consultax="select nmr_credito,nmr_solicitud,nmr_cuota from DetalleDescuentoPlanilla ";
				$consultax.=" where rut_socio='$solo_rut' and rut_empleador='$rut_inst' and ano_mes='$ano_mes' and nmr_corr_credito=2";
				$r=sqlsrv_query($con, $consultax,array(), array("Scrollable"=>"buffered"));
				$contar=sqlsrv_num_rows($r);
				
				if($contar==0){
					
					$credito2=0;
				
				
				}else{
						if ($a=sqlsrv_fetch_array($r)){
								
								$nmr_credito2=$a["nmr_credito"];
								$nmr_sol_cred2=$a["nmr_solicitud"];
								$nmr_cuot_cred2=$a["nmr_cuota"];
								$credito2=$nmr_credito2;
								
								 
						}	
						
						$sol2="select nmr_cuotas from Atencion where nmr_solicitud='$nmr_sol_cred2'";
						$s2=sqlsrv_query($con, $sol2,array(), array("Scrollable"=>"buffered"));
						$cantidad_s2=sqlsrv_num_rows($s2);
						
						if($cantidad_s2==1){
					
									if($r=sqlsrv_fetch_array($s2)){
										
										$cuotas2=$r["nmr_cuotas"];
									
									}
				
						}
				
						if($nmr_cuot_cred2==0 and $cuotas2==0){
							
							$cuotas_cuota2="";
						
						}else{
							
							$cuotas_cuota2=$nmr_cuot_cred2."/".$cuotas2;
						
						}
						
				}
				
	
				
				
				
				$sol="select nmr_cuotas from Atencion where nmr_solicitud='$nmr_solicitud'";
				$s=sqlsrv_query($con, $sol,array(), array("Scrollable"=>"buffered"));
				$cantidad_s=sqlsrv_num_rows($s);
				
				if($cantidad_s==1){
					
					if($r=sqlsrv_fetch_array($s)){
						
						$cuotas=$r["nmr_cuotas"];
					
					}
				
				}
				
				if($nmr_cuota==0 and $cuotas==0){
					
					$cuotas_cuota="";
				
				}else{
					
					$cuotas_cuota=$nmr_cuota."/".$cuotas;
				
				}
				
				//=============== Ver si esta moroso
				// deje año 2014 para calcular mora cambiar cuando pase a 2015
				$fecha_hoy=date('Y-m-d');
				$nuevafecha = date('m',strtotime('-2 months', strtotime($fecha_hoy))); ;
			
				$morosidad="";
				$rut_moroso="";
				if($nmr_solicitud!=0){
					$cons="select cp.cdg_est_cuota,cp.fch_venc,a.rut_socio from CuotaPendiente cp";
					$cons.=" join Atencion a on a.nmr_solicitud=cp.nmr_solicitud";
					$cons.=" where cp.nmr_solicitud='$nmr_solicitud' and month(cp.fch_venc)<='$nuevafecha' and year(cp.fch_venc)<='2014'";
					$revisar=sqlsrv_query($con, $cons);

					while($v=sqlsrv_fetch_array($revisar)){
						
						$cdg_est_cuota=$v["cdg_est_cuota"];
						$fch_venc=$v["fch_venc"];
						
						$fecha_venc_nueva=date_format($fch_venc, 'Y-m-d');
						//$solo_mes=date("Y-m-d", strtotime($fch_venc));
						$rut_socio_=$v["rut_socio"];
						
						if($cdg_est_cuota!=4){
							
							
							$rut_moroso=$rut_socio_;
							
						
						}
					}
					
				}
				
				
				if($solo_rut==$rut_moroso){
					//$obs="Moroso";
					if($obs!="Ex Socio - Renunciado" and $obs!="Cese"){
					
						$obs="(C/Mora)";
					
					}else{
					
						if($obs=="Ex Socio - Renunciado"){
							
							$obs="Ex Socio - Renunciado , C/Mora"; 

						}else{
							
							$obs="Cese, C/Mora"; 
						
						}
					
					}
				}
				
				
				
				//==================================
				
				
				
				
				$tot_descontar=$cuota_social+$credito_social+$seguro+$credito2;
				$total_descontar=number_format($cuota_social+$credito_social+$seguro+$credito2,0,",", ".");
				
				$credito_2_f=number_format($credito2,0,",",".");
			
				
				$rut_formateado=rut($rut_socio);
				
				$suma=$suma+$cuota_social;
				$suma1=$suma1+$credito_social;
				$suma2=$suma2+$seguro;
				$suma3=$suma3+$tot_descontar;
				
				$pdf->SetX (10);
				$pdf->SetFont('Arial','',7);
				$pdf->Cell (18,5,"$rut_formateado" ,1,0,"L");
				$pdf->SetFont('Arial','',7);
				$pdf->Cell (75,5,"$nombre_socio" ,1,0,"L");
				$pdf->SetFont('Arial','',8);
				$pdf->Cell (20,5,"$cuota_social_f" ,1,0,"R");
				$pdf->Cell (20,5,"$credito_social_f",1,0,"R");
				$pdf->Cell (10,5,"$cuotas_cuota",1,0,"R");
				$pdf->Cell (23,5,"$credito_2_f",1,0,"R");
				$pdf->Cell (10,5,"$cuotas_cuota2",1,0,"R");
				$pdf->Cell (22,5,"$seguro_f",1,0,"R");
				$pdf->Cell (18,5,"$total_descontar",1,0,"R");
				$pdf->Cell (16,5,"",1,0,"R");
				$pdf->SetFont('Arial','',7);
				$pdf->Cell (16,5,"$tipo_seguro   $carga",1,0,"L");
				$pdf->Cell (18,5,"$obs",1,1,"R");
				
				$i++;
				
				
		}
		
		
		$pdf->AddPage();
		$pdf->SetDrawColor (7,7,7);
		$pdf->SetFont('Arial','B',8);
		$pdf->SetXY (93,8);
		$pdf->MultiCell(30,5,"Total Cuota \n Social",1,'C');  
		
		$pdf->SetFont('Arial','B',10);
		$pdf->SetXY (60,25);
		$pdf->Write (1,"Total Empleador");
		
		$pdf->SetFont('Arial','B',8);
		$pdf->SetXY (110,25);
		$pdf->Write (1,number_format($suma,0,",","."));
		
		$pdf->SetFont('Arial','B',8);
		$pdf->SetXY (125,8);
		$pdf->MultiCell(30,5,"Total Crédito \n Social",1,'C'); 
		
		$pdf->SetFont('Arial','B',8);
		$pdf->SetXY (142,25);
		$pdf->Write (1,number_format($suma1,0,",","."));
		
		$pdf->SetFont('Arial','B',8);
		$pdf->SetXY (157,8);
		$pdf->MultiCell(30,5,"Total Seguro \n Adicional",1,'C'); 
		
		$pdf->SetFont('Arial','B',8);
		$pdf->SetXY (176,25);
		$pdf->Write (1,number_format($suma2,0,",","."));
		
		$pdf->SetFont('Arial','B',8);
		$pdf->SetXY (189,8);
		$pdf->MultiCell(30,5,"Total a \n Descontar",1,'C'); 
		
		$pdf->SetFont('Arial','B',8);
		$pdf->SetXY (204,25);
		$pdf->Write (1,number_format($suma3,0,",","."));
		
		$pdf->SetFont('Arial','B',8);
		$pdf->SetXY (221,8);
		$pdf->MultiCell(30,5,"Total Dscto \n Efectivo",1,'C');
		
		$pdf->SetFont('Arial','B',8);
		$pdf->SetXY (226,25);
		$pdf->Write (1,"______________");
		
		
		//==========================================
		
		$pdf->SetFont('Arial','',10);
		$pdf->SetXY (10,50);
		$pdf->Write (1,"NOTAS : ");
		
		$pdf->SetFont('Arial','',9);
		$pdf->SetXY (40,60);
		$pdf->Write (1,"*  La columna 'Benef (Adic.)', muestra el tipo de seguro complementario de salud optado por el Socio y el número de cargas adicionales. ");
		
		$pdf->SetFont('Arial','',9);
		$pdf->SetXY (40,65);
		$pdf->Write (1,"** En la columna 'Dscto Efectivo', Indicar el valor efectivamente descontado, sólo cuando sea distinto al valor Total a descontar. ");
		
		$pdf->SetFont('Arial','',9);
		$pdf->SetDrawColor (0,0,0);
		$pdf->Line (10,80,270,80); // linea arriba
		$pdf->Line (10,95,101,95);	// linea siguiente
		$pdf->Line (101,80,101,130); // linea cierra cuadrado anterior
		
		$pdf->Line (225,80,225,130); // lineas cierre al medio
		$pdf->Line (270,80,270,130); // linea cierre final
		
		$pdf->Line (10,80,10,130); //linea izquierda
		$pdf->Line (10,130,270,130); //linea abajo
		
		$pdf->SetXY (10,87);
		$pdf->Write (1," 1.- Efectivo      Monto $         _______________________");
		
		$pdf->SetXY (10,100);
		$pdf->Write (1," 2.- Cheque      Monto $         _______________________");
		
		$pdf->SetXY (10,107);
		$pdf->Write (1,"                    Cheque N°         _______________________");
		
		$pdf->SetXY (10,114);
		$pdf->Write (1,"                            Banco         _______________________");
		
		$pdf->SetXY (10,121);
		$pdf->Write (1,"                             Plaza         _______________________");
		
		$pdf->SetFont('Arial','',7);
		$pdf->SetXY (120,87);
		$pdf->Write (1," Declaro bajo juramento que los datos consignados son expresión de la realidad.");
		$pdf->SetXY (120,114);
		$pdf->Write (1," _______________________________________________________________");
		$pdf->SetXY (130,121);
		$pdf->Write (1," Nombre, Firma y Timbre Empleador o Representante Legal.");
		
		$pdf->SetXY (230,87);
		$pdf->Write (1," Remuneraciones del mes.");
		
		$pdf->SetXY (230,95);
		$pdf->Cell (18,17,"",1,0,"C");
		$pdf->SetXY (235,98);
		$pdf->Write (1," MES ");
		$pdf->SetXY (250,95);
		$pdf->Cell (18,17,"",1,0,"C");
		$pdf->SetXY (255,98);
		$pdf->Write (1," AÑO ");
		
		$pdf->SetFont('Arial','B',10);
		$pdf->SetFont('Arial','U',10);
		$pdf->SetXY (10,140);
		$pdf->Write (1,"IMPORTANTE : ");
		
		$pdf->SetFont('Arial','B',10);
		$pdf->SetXY (10,148);
		$pdf->Write (1,"- El pago de la presente planilla lo puede efectuar en nuestras oficinas centrales Avda. Ejército 215 Santiago, por depósito o transferencia a nuestra ");
		
		$pdf->SetXY (10,155);
		$pdf->Write (1,"cuenta del BancoEstado Nr 28584-6, RUT 70.046.800-3, en los mismos plazos establecidos para el pago de las cotizaciones previsionales. La planilla con  ");
		
		$pdf->SetXY (10,162);
		$pdf->Write (1,"el detalle de los descuentos efectuados debe ser acompañada al documento de pago o debe enviar un correo electrónico a recaudacion@serbima.cl, ");
		
		$pdf->SetXY (10,169);
		$pdf->Write (1,"con la planilla (detalle) escaneada y el comprobante de transferencia o depósito, con el cual se completa el trámite, sin esta formalidad el pago queda nulo. ");
		
		}		
		
	
$pdf->Output("Planilla Descuento $nombre_mes del $ano $emp .pdf","I");
?>