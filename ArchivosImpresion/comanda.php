<?php require_once '../Components/Config.inc.php'; 
$db = new SuperDataBase();
//Obtenemos data de detalle
$detalle = $db->executeQuery("Select * from detallepedido where pkDetallePedido = '".$_GET["id"]."'");

//Variables a usar
$nombre_plato = "";
$tipo_plato = "";
$cantidad_plato = "";
$salon = "";
$nsalon = "";
$mesa = "";
$nombre_moso = "";
$mensaje = "";
$id_pedido = "";
$id_plato = "";
//Procesamos mysql
while ($row0 = $db->fecth_array($detalle)) {
    $cantidad_plato = $row0["cantidad"];
    $nombre_plato = $row0["pkPlato"];
    $nombre_moso = $row0["pkMozo"];
    $mensaje = $row0["mensaje"];
    $id_pedido = $row0["pkPediido"];
    $id_plato = $row0["pkPlato"];
}

//Obtenemos data plato
$dplato = $db->executeQuery("Select * from plato where pkPlato = '".$nombre_plato."'");
//Procesamos mysql
while ($row0 = $db->fecth_array($dplato)) {
    $nombre_plato = $row0["descripcion"];
    $tipo_plato = $row0["pktipo"];
}

//Verificamos si hay detalles adicionales
$adicionales = false;
if(isset($_GET["agrupar"])){
    if(intval($_GET["agrupar"]) == 1){
        $query = "Select dp.* from detallepedido dp, plato pl where dp.pkDetallePedido <> '".$_GET["id"]."' AND dp.pkPediido = '".$id_pedido."' AND dp.pkPlato = pl.pkPlato AND pl.pktipo = '".$tipo_plato."' AND exists (select 1 from cola_impresion where CONVERT(codigo USING utf8) COLLATE utf8_spanish2_ci = CONVERT(dp.pkDetallePedido USING utf8) COLLATE utf8_spanish2_ci)";
        //echo $query;
        $adicionales = $db->executeQuery($query);
    }
}

//Para tao agrupamos por defecto
/*$query = "Select dp.* from detallepedido dp, plato pl where dp.pkDetallePedido <> '".$_GET["id"]."' AND dp.pkPediido = '".$id_pedido."' AND dp.pkPlato = pl.pkPlato AND pl.pktipo = '".$tipo_plato."' AND exists (select 1 from cola_impresion where CONVERT(codigo USING utf8) COLLATE utf8_spanish2_ci = CONVERT(dp.pkDetallePedido USING utf8) COLLATE utf8_spanish2_ci)";
//echo $query;
$adicionales = $db->executeQuery($query);*/

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

//Obtenemos data salon
$dsalon = $db->executeQuery("Select * from salon where pkSalon = '".$salon."'");
while($row0 = $db->fecth_array($dsalon)){
    $nsalon = $row0["nombre"];
}

//Obtenemos cliente
$nombre_cliente = "";
$query01 = "Select * from cliente_externo where id_pedido = '".$id_pedido."'";
$query02 = "Select * from pedido_cliente where pkPediido = '".$id_pedido."'";
$r1 = $db->executeQuery($query01);
$r2 = $db->executeQuery($query02);
$res = null;
if($row01 = $db->fecth_array($r1)){
    $nombre_cliente = $row01['nombres_y_apellidos'];
}else{
   if($row02 = $db->fecth_array($r2)){
       $nombre_cliente = $row02['nombre_cliente'];
    } 
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
            case 43: $msjSalon = "DELIVERY";
                break;
            case 44: $msjSalon = "LLEVAR";
                break;
            default: $msjSalon = "PEDIDO";
                break;
        }
        ?>
        <div class="txt-center micnf2"><?php echo utf8_encode($msjSalon); ?></div>
        <p></p>
        <div class="txt-center micnf2"><?php echo "N° " . $id_pedido; ?></div>
        <div class="txt-center micnf2"><?php echo $nsalon." - ".$mesa; ?></div>
        <div class="txt-center micnf2">MOZO: <?php echo $nombre_moso;?></div>
        <?php if($nombre_cliente <> ""):?>
        <div class="txt-center micnf2">CLIENTE: <?php echo strtoupper($nombre_cliente);?></div>
        <?php endif; ?>
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
        <?php if($mensaje != ""):?>       
        <div class="txt-center micnf2"><p>**<?php echo $mensaje;?>**</p></div>
        <?php endif;?>
        
            <?php
            //Limpiamos Actual
            $db->executeQuery("SET sql_safe_updates=0");
            $db->executeQuery("Delete from cola_impresion where codigo = '".$_GET["id"]."' AND tipo = 'PED'");
            $db->executeQuery("SET sql_safe_updates=1");
            
            //Procesamos adicionales
            if($adicionales){
                while ($row0 = $db->fecth_array($adicionales)) {
                   
                    
                    //Obtenemos data plato
                    $dplato1 = $db->executeQuery("Select * from plato where pkPlato = '".$row0["pkPlato"]."'");
                    $nplato = "";
                    //Procesamos mysql
                    while ($row1 = $db->fecth_array($dplato1)) {
                        $nplato = $row1["descripcion"];
                    }
            ?>
                    <div class="txt-center micnf" style="margin-top:10px;">
                        <div style="float:left; width: 15%;"><?php echo (integer) $row0["cantidad"]; ?></div>
                        <div style="float:right; width: 85%;"><?php echo $nplato; ?></div>
                    </div>
            <?php
                    $mensajeT = $row0["mensaje"];
                    if ($mensajeT != "") {
            ?>
                    <div class="txt-center micnf2"><p>**<?php echo $mensajeT;?>**</p></div>
            <?php
                    }
                    //Borramos la cola de este pedido
                    $db->executeQuery("SET sql_safe_updates=0");
                    $db->executeQuery("Delete from cola_impresion where codigo = '".$row0["pkDetallePedido"]."' AND tipo = 'PED'");
                    $db->executeQuery("SET sql_safe_updates=1");
                    
                }
            }
            ?>
    </body>
</html>
