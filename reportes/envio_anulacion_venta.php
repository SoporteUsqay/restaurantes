<?php
error_reporting(E_ALL);

include_once('recursos/componentes/MasterConexion.php');
$conn = new MasterConexion();

$pedido = $argv[1];
$comprobante = $argv[2];
$items = $argv[3];
$total = $argv[4];
$mesa = $argv[5];
$salon = $argv[6];
$usuario = $argv[7];
        
require_once 'mailer/Exception.php';
require_once 'mailer/PHPMailer.php';
require_once 'mailer/SMTP.php';

$the_subject = "Alerta de Venta Anulada";
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

//Obtenemos moneda nacional
$moneda = "";
$query_moneda = "Select * from moneda where estado = 2";
$res_m = $conn->consulta_arreglo($query_moneda);
if(is_array($res_m)){
    $moneda = $res_m["simbolo"];
}

$phpmailer->Subject = $the_subject;	
$phpmailer->Body .="<h1 style='color:#3498db;'>¡Se Anuló una Venta!</h1>";
$phpmailer->Body .= "<p>En <b>".$nombre_local."</b> se anuló la venta #".$pedido." realizada en la mesa <b>".$mesa."</b> del salon <b>".$salon."</b> por <b>".$usuario."</b>, los items incluidos en la venta son:</p>".$items."<p>El total de la venta es <b>".$moneda."".$total."</b></p>";
if($comprobante <> ""){
    $phpmailer->Body .= "<p>Además con la venta se anuló el comprobante <b>".$comprobante."</b></p>";
}
$phpmailer->Body .= "<p>Fecha y Hora: ".date("d-m-Y h:i:s A")."</p>";
$phpmailer->Body .= "<p>Dirección: ".$direccion."</p>";
$phpmailer->IsHTML(true);

if($se_envia === 1){
    $phpmailer->Send();
}