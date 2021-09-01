<?php require_once '../Components/Config.inc.php'; 
$db = new SuperDataBase();
//Obtenemos data de detalle
$detalle = $db->executeQuery("Select * from detallepedido where pkDetallePedido = '".$_GET["id"]."'");
//Variables a usar
$nombre_plato = "";
$tipo_plato = "";
$cantidad_plato = "";
$salon = "";
$mesa = "";
$nombre_moso = "";
$id_pedido = "";
//Procesamos mysql
while ($row0 = $db->fecth_array($detalle)) {
    $cantidad_plato = $row0["cantidad"];
    $nombre_plato = $row0["pkPlato"];
    $nombre_moso = $row0["pkMozo"];
    $id_pedido = $row0["pkPediido"];
}


//Obtenemos data moso
$dmoso = $db->executeQuery("Select * from trabajador where pkTrabajador = '".$nombre_moso."'");
//Procesamos mysql
while ($row0 = $db->fecth_array($dmoso)) {
    $nombre_moso = $row0["nombres"];
}


//Obtenemos data adicional 
$dpedido = $db->executeQuery("Select * from pedido where pkPediido = '".$id_pedido."'");
//Procesamos mysql
while ($row0 = $db->fecth_array($dpedido)) {
    $mesa = $row0["pkMesa"];
}


//Obtenemos data mesa
$dmesa = $db->executeQuery("Select * from mesas where pkMesa = '".$mesa."'");
//Procesamos mysql
while ($row0 = $db->fecth_array($dmesa)) {
    $mesa = $row0["nmesa"];
    $salon = $row0["pkSalon"];
}


//Obtenemos data plato
$dplato = $db->executeQuery("Select * from plato where pkPlato = '".$nombre_plato."'");
//Procesamos mysql
while ($row0 = $db->fecth_array($dplato)) {
    $nombre_plato = $row0["descripcion"];
    $tipo_plato = $row0["pktipo"];
}

//Obtenemos data salon
$dsalon = $db->executeQuery("Select * from salon where pkSalon = '".$salon."'");
while($row0 = $db->fecth_array($dsalon)){
    $nsalon = $row0["nombre"];
}


?>
<html lang="en">
    <head>
        <title><?php echo intval($tipo_plato);?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="../Public/css/impresion_style.css" />
        <style>
            body{
                zoom: 200%;
            }
            .micnf {
                font-family: Arial;
                font-size: 160%;
                font-weight: bold;     
                word-wrap: break-word !important;
                width: 100%;
                display:flex;
            }
            
            .micnf1 {
                font-family: Arial;
                font-size: 14px;
                font-weight: bold;
                word-break: break-all !important;
                width: 100%;
            }
            
            .micnf2 {
                font-family: Arial;
                font-size: 140%;
                font-weight: bold;
                word-wrap: break-word !important;
                width: 100%;
            }
        </style>
    </head>
    <body style="font-family: sans-serif; margin: 0px !important;">
        <?php
        $msjSalon = "";
        switch ($salon) {
            case 43: $msjSalon = "ANULACION DE DELIVERY";
                break;
            case 44: $msjSalon = "ANULACION PARA LLEVAR";
                break;
            default: $msjSalon = "ANULACION DE PEDIDO";
                break;
        }
        ?>
        <div class="txt-center micnf2"><?php echo utf8_encode($msjSalon); ?></div>
        <p></p>
        <div class="txt-center micnf1"><?php echo "N° " . $id_pedido; ?></div>
        <div class="txt-center micnf1"><?php echo $nsalon." - ".$mesa; ?></div>
        <div class="txt-center micnf1"><?php echo $nombre_moso;?></div>
        <p></p>
        <div class="txt-center">
            <div style="float:right;width:50%;"><?php echo date('d/m/Y'); ?></div>
            <div style="float:left;width:50%;"><?php echo date('h:i:s'); ?></div>
        </div>
        <p></p>
        <div class="txt-center">
            <div style="float:left;width:15%;">Cant</div>
            <div style="float:right;width:85%;">Producto</div>
            <hr class="hr-detalle"/>
        </div>
        <div class="txt-center micnf">
            <div style="float:left; width: 15%;"><?php echo (integer) $cantidad_plato; ?></div>
            <div style="float:right; width: 85%;"><?php echo $nombre_plato; ?></div>
        </div>
    </body>
</html>
