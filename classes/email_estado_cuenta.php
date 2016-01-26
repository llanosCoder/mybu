<?php
ini_set('display_errors', 'on');

$estado_cuenta = $_POST['estado_cuenta'];
$mail_destinatario = $_POST['mail'];

require_once('PHPMailer-master/class.smtp.php');
require_once('PHPMailer-master/class.phpmailer.php');
$mail = new PHPMailer();
$mail->Mailer = "smtp";
$mail->Host = "mail.nfn.cl";
$mail->SMTPAuth = true;
$mail->Username = "nfn"; 
$mail->Password = "ivan1989";
$mail->Port = 25;
$mail->SetFrom('soporte@nfn.cl', 'Soporte NFN');
$mail->FromName = "Soporte NFN";
$mail->AddAttachment("../files/estado_cuentas/$estado_cuenta.pdf");
$mail->AddAddress($mail_destinatario);
$mail->Subject = "Estado de cuenta.";
$mail->Body = "Estimado:\n Adjunto estado de cuenta de su línea de crédito.\n Saludos.";
$exito = $mail->Send();
if(!$exito){
    echo 0;
}else{
    echo 1;
}

?>