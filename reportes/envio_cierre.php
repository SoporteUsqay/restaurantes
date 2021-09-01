<?php
error_reporting(0);

include_once('recursos/componentes/MasterConexion.php');
$ruta = dirname(__FILE__);
$nruta = explode("\\reportes",$ruta);

include_once($nruta[0].'\Application\Models\CajaModel.php');
include_once($nruta[0].'\Components\Config.inc.php');
include_once($nruta[0].'\Components\Library\Framework\config.php');
include_once($nruta[0].'\Components\Library\Framework\Factory.php');
include_once($nruta[0].'\Components\Library\Framework\SuperDataBase.php');

$objModelCaja = new Application_Models_CajaModel();
$conn = new MasterConexion();

$fecha = $argv[1];
$cajero = $argv[2];
$corte = $argv[3];
$inicial = $argv[4];
$caja = $argv[5];
$tipo = $argv[6];
        
require_once 'mailer/Exception.php';
require_once 'mailer/PHPMailer.php';
require_once 'mailer/SMTP.php';

$the_subject = "";
if(intval($tipo) === 1){
    $the_subject = "Cierre de Caja";    
}else{
    $the_subject = "Corte de Caja"; 
}

$from_name = "Usqay Cloud";

$phpmailer = new PHPMailer\PHPMailer\PHPMailer();

// ---------- datos de la cuenta de Gmail -------------------------------
$phpmailer->Username = $email_user;
$phpmailer->Password = $email_password; 
//-----------------------------------------------------------------------
//$phpmailer->SMTPDebug = 2;
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

//Obtenemos los medios a buscar
$medios = array(); 
$resultado_medios = $conn->consulta_matriz("Select mp.id, mp.moneda, mp.nombre, m.simbolo from medio_pago mp, moneda m where mp.estado > 0 AND mp.moneda = m.id");
if(is_array($resultado_medios)){
    foreach($resultado_medios as $medio){
        if(intval($medio["id"]) === 1){
            $resultado_monedas = $conn->consulta_matriz("Select * from moneda where estado > 0");
            if(is_array($resultado_monedas)){
                foreach($resultado_monedas as $moneda){
                    $tmp = array();
                    $tmp["nombre"] = $medio["nombre"]." ".$moneda["simbolo"];
                    $tmp["id_medio"] = $medio["id"];
                    $tmp["id_moneda"] = $moneda["id"];
                    $medios[] = $tmp;
                }
            }
        }else{
            $tmp = array();
            $tmp["nombre"] = $medio["nombre"]." ".$medio["simbolo"];
            $tmp["id_medio"] = $medio["id"];
            $tmp["id_moneda"] = $medio["moneda"];
            $medios[] = $tmp;
        }
    }
}

//Obtenemos todos los tipos de pagos
$tipos_gastos = array();
$resultado_gastos = $conn->consulta_matriz("Select * from tipo_gasto where estado = 1");
if(is_array($resultado_gastos)){
    foreach ($resultado_gastos as $gasto) {
        $tmp = array();
        $tmp["id"] = $gasto["id"];
        $tmp["nombre"] = $gasto["nombre"];
        $tmp["direccion"] = $gasto["direccion"];
        $tipos_gastos[] = $tmp;
    }
}

//Obtenemos todas las monedas
$monedas = array();
$resultado_monedas = $conn->consulta_matriz("Select * from moneda where estado > 0");
if(is_array($resultado_monedas)){
    foreach($resultado_monedas as $moneda){
        $tmp = array();
        $tmp["id"] = $moneda["id"];
        $tmp["nombre"] = $moneda["nombre"];
        $tmp["simbolo"] = $moneda["simbolo"];
        $monedas[] = $tmp;
    }
}

//Obtenemos data corte segun tipo impresion
$data_corte = null;
if(intval($inicial) == 1){
    $data_corte = $objModelCaja->_TotalDiaCorte($fecha,$corte,$caja);
}else{
    $data_corte = $objModelCaja->_TotalDia($fecha);
}

$phpmailer->Subject = $the_subject;
if(intval($tipo) === 1){	
    $phpmailer->Body .="<h1 style='color:#3498db;'>Cierre de Caja</h1>";
}else{
    $phpmailer->Body .="<h1 style='color:#3498db;'>Corte de Caja</h1>";
}
$phpmailer->Body .= "<p><b>".$nombre_local."</b></p>";
$phpmailer->Body .= "<p><b>".$direccion."</b></p>";
$phpmailer->Body .= "<p><b>Fecha y Hora:</b> ".date("d-m-Y h:i:s A")."</p>";
$phpmailer->Body .= "<p><b>Cajero:</b> ".$cajero."</p>";
$phpmailer->Body .= "<p><b>Caja:</b> ".$caja."</p>";
if(intval($inicial) == 1){
    $phpmailer->Body .= "<p><b>Corte:</b> ".$corte."</p>";
}
$phpmailer->Body .= "<hr/>";

$phpmailer->Body .= "<p><b>TOTALES</b></p>";
foreach($medios as $med){
    $phpmailer->Body .= "<p><b>".$med["nombre"].":</b> ".round($data_corte["tot_".$med["id_medio"]."_".$med["id_moneda"]],2)."</p>";
}

$phpmailer->Body .= "<hr/>";
$phpmailer->Body .= "<p><b>INGRESO VENTAS</b></p>";
foreach($medios as $med){
    $phpmailer->Body .= "<p><b>".$med["nombre"].":</b> ".round($data_corte["ven_".$med["id_medio"]."_".$med["id_moneda"]],2)."</p>";
}

$phpmailer->Body .= "<hr/>";
$phpmailer->Body .= "<p><b>PROPINAS</b></p>";
foreach($medios as $med){
    $phpmailer->Body .= "<p><b>".$med["nombre"].":</b> ".round($data_corte["prop_".$med["id_medio"]."_".$med["id_moneda"]],2)."</p>";
}

$phpmailer->Body .= "<hr/>";
$phpmailer->Body .= "<p><b>MONTOS INICIALES</b></p>";
foreach($monedas as $mo){
    $phpmailer->Body .= "<p><b>".$mo["nombre"]." ".$mo["simbolo"].":</b> ".round($data_corte["ini_".$mo["id"]],2)."</p>";
}

$phpmailer->Body .= "<hr/>";
$phpmailer->Body .= "<p><b>INGRESOS ADICIONALES</b></p>";
foreach($medios as $med){
    $phpmailer->Body .= "<p><b>".$med["nombre"].":</b> ".round($data_corte["ing_".$med["id_medio"]."_".$med["id_moneda"]],2)."</p>";
}

$phpmailer->Body .= "<hr/>";
$phpmailer->Body .= "<p><b>SALIDAS</b></p>";
foreach($medios as $med){
    $phpmailer->Body .= "<p><b>".$med["nombre"].":</b> ".round($data_corte["sal_".$med["id_medio"]."_".$med["id_moneda"]],2)."</p>";
}

$phpmailer->Body .= "<hr/>";
$phpmailer->Body .= "<p><b>RESUMEN VENTAS</b></p>";
$phpmailer->Body .= "<p><b>Total Vendido:</b> ".round($data_corte["vendido"],2)."</p>";
$phpmailer->Body .= "<p><b>Total Creditos:</b> ".round($data_corte["credito"],2)."</p>";
$phpmailer->Body .= "<p><b>Total Consumos:</b> ".round($data_corte["consumo"],2)."</p>";
$phpmailer->Body .= "<p><b>Total Descuentos:</b> ".round($data_corte["descuento"],2)."</p>";

foreach($tipos_gastos as $tip){
    $phpmailer->Body .= "<hr/>";
    $phpmailer->Body .= "<p><b>RESUMEN ".strtoupper($tip["nombre"])."</b></p>";
    foreach($medios as $med){
        $phpmailer->Body .= "<p><b>".$med["nombre"].":</b> ".round($data_corte["tip_".$tip["id"]."_".$med["id_medio"]."_".$med["id_moneda"]],2)."</p>";
    }
}

$phpmailer->Body .= "<hr/>";
$phpmailer->Body .= "<p><b>SALIDA DE PLATOS Y/O PRODUCTOS</b></p>";
$phpmailer->Body .= "<table><thead><tr><th><center>Item</center></th><th><center>Cantidad</center></th></tr></thead>";
$phpmailer->Body .= "<tbody style='text-align:center;'>";
foreach($data_corte["platos"] as $pl){
    $phpmailer->Body .= "<tr><td>".$pl["descripcion"]."</td><td>".$pl["salidas"]."</td></tr>";
}
$phpmailer->Body .= "</tbody></table>";

$phpmailer->Body .= "<hr/>";
$phpmailer->Body .= "<p><b>MOVIMIENTOS CON MEDIOS ELECTRONICOS</b></p>";
$phpmailer->Body .= "<table><thead><tr><th><center>ID</center></th><th><center>OP</center></th><th><center>MEDIO</center></th><th><center>MONTO</center></th></tr></thead>";
$phpmailer->Body .= "<tbody style='text-align:center;'>";
foreach($data_corte["movimientos"] as $pl){
    $phpmailer->Body .= "<tr><td>".$pl["id"]."</td><td>".$pl["op"]."</td><td>".$pl["medio"]."</td><td>".$pl["moneda"]." ".$pl["total"]."</td></tr>";
}
$phpmailer->Body .= "</tbody></table>";

$phpmailer->IsHTML(true);

if($se_envia === 1){
    $phpmailer->Send();
}
