<?php
error_reporting(E_ALL);

include_once('recursos/componentes/MasterConexion.php');
$conn = new MasterConexion();


$cantidad = $argv[2];
$plato = $argv[3];
$id_pedido = $argv[1];
$mesa = $argv[4];
$salon = $argv[5];
$usuario = $argv[6];
$motivo = $argv[7];
        
require_once 'mailer/Exception.php';
require_once 'mailer/PHPMailer.php';
require_once 'mailer/SMTP.php';

$the_subject = "Alerta de Anulacion";
$from_name = "Usqay Cloud";

$phpmailer = new PHPMailer\PHPMailer\PHPMailer();

// ---------- datos de la cuenta de Gmail -------------------------------
$phpmailer->Username = $email_user;
$phpmailer->Password = $email_password; 
//-----------------------------------------------------------------------
// $phpmailer->SMTPDebug = 1;
$phpmailer->SMTPSecure = 'ssl';
$phpmailer->Host = "smtp.gmail.com"; // GMail
$phpmailer->Port = 465;
$phpmailer->IsSMTP(); // use SMTP
$phpmailer->SMTPAuth = true;

$phpmailer->setFrom($phpmailer->Username,$from_name);

//Obtenemos todos los correos afiliados
$se_envia = 0;
$query_correos = "SELECT * from cloud_config where parametro = 'correos_notificaciones'";
$res = $conn->consulta_arreglo($query_correos);
if(is_array($res)){
    $se_envia = 1;
    $direcciones = explode(",",$res["valor"]);
    foreach($direcciones as $dir){
        $phpmailer->AddAddress($dir);
    }
}

//Obtenemos nombre Local
$nombre_local = "";
$direccion = "";
$query_local = "SELECT * from sucursal LIMIT 1";
$res_l = $conn->consulta_arreglo($query_local);
if(is_array($res_l)){
    $nombre_local = $res_l["nombreSucursal"];
    $direccion = $res_l["direccion"];
}

$phpmailer->Subject = $the_subject;	
$phpmailer->Body .="<h1 style='color:#3498db;'>¡Se Anuló un Pedido!</h1>";
$phpmailer->Body .= "<p>En <b>".$nombre_local."</b> se anularon <b>".$cantidad."</b> unidad(es) del item <b>'".$plato."'</b> en el pedido <b>#".$id_pedido."</b> en la mesa <b>".$mesa."</b> del salon <b>".$salon."</b> por <b>".$usuario."</b></p>";
$phpmailer->Body .= "<p>El motivo de la anulacion es: <b>".$motivo."</b></p>";
$phpmailer->Body .= "<p>Fecha y Hora: ".date("d-m-Y h:i:s A")."</p>";
$phpmailer->Body .= "<p>Dirección: ".$direccion."</p>";
$phpmailer->IsHTML(true);

if($se_envia === 1){
    $phpmailer->Send();
}