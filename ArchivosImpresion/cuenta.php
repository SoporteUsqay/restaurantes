<?php require_once '../Components/Config.inc.php'; 
$db = new SuperDataBase();
$mesa = "";
$salon = "";

//Obtenemos la data del cajero
$nombre_cajero = "";
$dcajero = $db->executeQuery("Select * from trabajador where pkTrabajador = '".$_GET["cajero"]."'");
//Procesamos mysql
while ($row0 = $db->fecth_array($dcajero)) {
    $nombre_cajero = $row0["nombres"];
}

//MOZO 
$query_nomMozo ="Select t.nombres, p.pkMesa from pedido p inner join detallepedido dp on p.pkPediido=dp.pkPediido inner join trabajador t on dp.pkMozo=t.pkTrabajador where p.pkPediido='".$_GET["id"]."' limit 1";
        
$result_nomMozo = $db->executeQuery($query_nomMozo);
$nomMozo = "";
while ($row1 = $db->fecth_array($result_nomMozo)) {
    $nomMozo = $row1['nombres'];
}

//MESA
$query_mesa = "Select pkMesa from pedido where pkPediido = '".$_GET["id"]."'";
$result_nMesa = $db->executeQuery($query_mesa);
while ($rowm = $db->fecth_array($result_nMesa)) {
    $mesa = $rowm['pkMesa'];
}

//Obtenemos data mesa
$dmesa = $db->executeQuery("Select * from mesas where pkMesa = '".$mesa."'");
//Procesamos mysql
while ($row0 = $db->fecth_array($dmesa)) {
    $numMesa = $row0["nmesa"];
    $salon = $row0["pkSalon"];
}

//SALON
$dsalon = $db->executeQuery("Select * from salon where pkSalon = '".$salon."'");
while($row0 = $db->fecth_array($dsalon)){
    $salon = $row0["nombre"];
}


//obtenemos los datos de la sucursal a mostrar en el comprobante de pago
$query_sucursal = "SELECT nombreSucursal, razon, ruc, direccion, ciudad, telefono, pagweb FROM sucursal WHERE pkSucursal = 'SU009'";
$result_sucursal = $db->executeQuery($query_sucursal);
while ($row_sucursal = $db->fecth_array($result_sucursal)) {
    $nomSucursal = $row_sucursal['nombreSucursal'];
    $razonSucursal = $row_sucursal['razon'];
    $rucSucursal = $row_sucursal['ruc'];
    $dirSucursal = $row_sucursal['direccion'];
    $ciudadSucursal = $row_sucursal['ciudad'];
    $telSucursal = $row_sucursal['telefono'];
    $webSucursal = $row_sucursal['pagweb'];
}
?>

<?php
        
$subTotal = 0;
$total = 0;
$descuento = 0;
$igv = 0.18;
$stigv=1.18; // calculo para generar el subtotal

/* Procedimiento para listar los items de un pedido */
$query_numPedido = 
"SELECT (SELECT descuento FROM pedido WHERE pkPediido='".$_GET["id"]."')AS descuento, pkDetallePedido, cantidad, " .
"precio,  ROUND((cantidad*precio),2) " .
"AS importe, mensaje, d.estado, pkPediido , fechaPedido, horaPedido, horaTermino,  pkCocinero, pkMozo, " .
"estadoImpresion, t.nombres, t.apellidos,(SELECT UPPER(descripcion) " .
"FROM plato pl WHERE pl.pkPlato=d.pkPlato) AS pedido FROM detallepedido d  INNER JOIN trabajador t " .
" ON t.pkTrabajador=pkMozo WHERE d.pkPediido='".$_GET["id"]."' and " .
"d.estado<>3 and d.estado<>0;";
$result_numPedido = $db->executeQuery($query_numPedido);

$query_tipoPago = "SELECT tipo_pago,estado, case when (length(documento)<9 and length(documento) >1)  then (select concat(nombres, ' ' ,lastName) from person pe where pe.documento=p.documento) when length(documento)>11 then (select razonSocial from persona_juridica where ruc=documento)  end as documento  FROM pedido p WHERE pkPediido = '".$_GET["id"]."';";
$result_tipoPago = $db->executeQuery($query_tipoPago);
$estadio = "";
$tipoPago = "";
$cliente = "";
while ($row_tipoPago = $db->fecth_array($result_tipoPago)) {
    $tipoPago = $row_tipoPago['tipo_pago'];
    $estadio = $row_tipoPago['estado'];
    $cliente = $row_tipoPago['documento'];
}

$impresion = "";

switch ($estadio) {
    case "0": $msjPago = "PRE CUENTA";
        break;

    case "1": $msjPago = "TICKET";
        break;

    case "3" : $msjPago = "ANULACIÓN";
    break;
        
    case "4" : $msjPago = "CREDITO";
        break;
    
    case "5" : $msjPago = "CONSUMO";
        break;
}
?>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="../Public/css/impresion_style.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body,
        table{
            font-family: "Lucida Console", Monaco, monospace;
            line-height: 0.8 !important;             
        }
        
        body{
            font-size: 12px;
            font-weight: 200;
        }
        table{
            font-size: 12px;
            font-weight: 300;
        }
        body{
            zoom: 200%;
        }
    </style>
    <title>
    <?php 
        switch(intval($estadio)){
            case 4:
                echo "cr";
            break;

            case 5:
                echo "co";
            break;

            default:
                echo "c";
            break;
        }
    ?>
    </title>
</head>
<body style="margin: 0px !important;">    
<?php        
        //Si el mensaje de pago es precuenta, mostramos datos del cliente
        $nombre_cliente = "";
        $direccion_cliente = "";
        $telefono_cliente = "";
        if($msjPago == "PRE CUENTA" || $msjPago == "TICKET"){
            $query01 = "Select * from cliente_externo where id_pedido = '".$_GET["id"]."'";
            $query02 = "Select * from pedido_cliente where pkPediido = '".$_GET["id"]."'";
            $r1 = $db->executeQuery($query01);
            $r2 = $db->executeQuery($query02);
            if($row01 = $db->fecth_array($r1)){
                $nombre_cliente = $row01["nombres_y_apellidos"];
                $direccion_cliente = $row01["direccion"];
                $telefono_cliente = $row01["telefono"];
            }else{
               if($row02 = $db->fecth_array($r2)){
                    $nombre_cliente = $row02["nombre_cliente"];
                } 
            }
        }
        ?>
        <table border="0" width="100%">
            <tr>
                <!-- Cabecera, contiene datos de la empresa -->
                <td colspan="4" class="txt-center">
                    <?php
                    if (file_exists("../Public/images/logo_empresa.png")) {
                        echo "<center><img src='../Public/images/logo_empresa.png' style='width:200px;'/></center>";
                        echo "<p></p>";
                    }
                    ?>

                    <?php
                    if (!file_exists("../Public/images/logo_empresa.png")) {?>
                        <div style="font-size: 24px !important; font-weight: bold !important;">
                            <?php echo $nomSucursal; ?>
                        </div>
                    <?php } ?>
                    <?php echo $razonSucursal; ?><br>
                    RUC <?php echo $rucSucursal; ?><br/>
                    <?php echo utf8_encode($dirSucursal); ?><br/>
                    <?php echo utf8_encode($ciudadSucursal); ?><br/>
                    <?php echo utf8_encode($telSucursal); ?><br/>
                    <hr class="hr-header"/>
                </td>
                <!-- Fin cabecera -->
            </tr>
            <tr>
                <td colspan="4" class="txt-center bold" style="">
                    <?php echo $msjPago; ?><br/>
                    N° <?php echo $_GET["id"]; ?>
                    <hr class="hr-header"/>
                </td>
            </tr>
            <?php if($nombre_cliente !== ""):?>
            <tr>
                <td colspan="4" class="txt-center bold" style="">
                    CLIENTE: <?php echo $nombre_cliente; ?><br/>
                    <?php if($direccion_cliente !== ""):?>
                    DIRECCIÓN: <?php echo $direccion_cliente; ?><br/>
                    <?php endif;?>
                    <?php if($telefono_cliente !== ""):?>
                    TELÉFONO: <?php echo $telefono_cliente; ?><br/>
                    <?php endif;?>
                    <hr class="hr-header"/>
                </td>
            </tr>
            <?php endif;?>
            <tr>
                <td><b><?php echo date('d/m/Y'); ?></b></td>
                <td></td>
                <td></td>
                <td class="txt-right"><b><?php echo date('h:i:s'); ?></b></td>
            </tr>
            <tr>
                <td><b><?php echo $numMesa; ?> - <?php echo $salon;?></b></td>
                <td></td>
                <td></td>
                <td class="txt-right"><b><?php echo $nombre_cajero; ?></b></td>
            </tr>
            <tr>
                <td colspan="4">
                    <hr class="hr-detalle"/>
                </td>
            </tr>
        </table>
        <table border="0" width="100%">
            <tr class="txt-center">
                <td border="1">Cant</td>
                <td colspan="2">Descripción</td>
                <td>Total</td>
            </tr>
            <tr>
                <td colspan="4">
                    <hr class="hr-detalle"/>
                </td>
            </tr>
            
            <?php
            $subTotal=0;
            $importe=0;
            $array_impresion = array();
            $cnt = 0;
            
            //Condiciones para busqueda de huerfanos
            $condiciones = "";
            
            while ($row = $db->fecth_array($result_numPedido)) {
                //Verificamos si elemento fue sometido al cambiazo
                $query_ver = "Select cf.id, cf.cantidad_cambio, cf.precio_cambio, cf.pk_plato_cambio, UPPER(p.descripcion) as descripcion from cambio_facturacion cf, plato p where cf.pk_plato_cambio = p.pkPlato AND cf.pk_detalle =  '".$row['pkDetallePedido']."' AND cf.pk_pedido_destino = '".$_GET["id"]."'";
                
                $hay = 0;
                
                $r_ver = $db->executeQuery($query_ver);
                
                while($row_v = $db->fecth_array($r_ver)){
                    $condiciones = $condiciones." AND cf.id <> '".$row_v['id']."'";
                    $hay = 1;
                    
                    $importe = floatval($row_v['cantidad_cambio'])*floatval($row_v['precio_cambio']);                
                    $recurrente = 0;
                    foreach ($array_impresion as &$pi){
                        if ($row_v['descripcion'] == $pi['pedido']) {
                            $recurrente = 1;
                            $pi["cantidad"] = intval($pi["cantidad"])+intval($row_v['cantidad_cambio']);
                            $pi["importe"] = floatval($pi["importe"])+floatval($importe);
                        }
                    }

                    if($recurrente === 0){
                        $fi = array();
                        $fi["cantidad"] = $row_v['cantidad_cambio'];
                        $fi["pedido"] = $row_v['descripcion'];
                        $fi["importe"] = $importe;

                        $array_impresion[$cnt] = $fi;
                        $cnt = $cnt + 1;
                    }

                    $subTotal = $subTotal + floatval($importe);
                }
                
                
                if($hay === 0){
                    $importe = $row['importe'];                
                    $descuento = $row['descuento'];

                    $recurrente = 0;
                    foreach ($array_impresion as &$pi){
                        if ($row['pedido'] == $pi['pedido']) {
                            $recurrente = 1;
                            $pi["cantidad"] = intval($pi["cantidad"])+intval($row['cantidad']);
                            $pi["importe"] = floatval($pi["importe"])+floatval($importe);
                        }
                    }

                    if($recurrente === 0){
                        $fi = array();
                        $fi["cantidad"] = $row['cantidad'];
                        $fi["pedido"] = $row['pedido'];
                        $fi["importe"] = $importe;

                        $array_impresion[$cnt] = $fi;
                        $cnt = $cnt + 1;
                    }

                    $subTotal = $subTotal + floatval($importe);
                }
            }
            
            //Buscamos detalles truchos huerfanos
            $query_ex = "Select cf.id, cf.cantidad_cambio, cf.precio_cambio, cf.pk_plato_cambio, upper(p.descripcion) as descripcion from cambio_facturacion cf, plato p where cf.pk_plato_cambio = p.pkPlato AND cf.pk_pedido_destino = '".$_GET["id"]."'".$condiciones;
            $result_ex = $db->executeQuery($query_ex);
            while($row_ex = $db->fecth_array($result_ex)){
                $importe = floatval($row_ex['cantidad_cambio'])*floatval($row_ex['precio_cambio']);                
                $recurrente = 0;
                foreach ($array_impresion as &$pi){
                    if ($row_ex['descripcion'] == $pi['pedido']) {
                        $recurrente = 1;
                        $pi["cantidad"] = intval($pi["cantidad"])+intval($row_ex['cantidad_cambio']);
                        $pi["importe"] = floatval($pi["importe"])+floatval($importe);
                    }
                }

                if($recurrente === 0){
                    $fi = array();
                    $fi["cantidad"] = $row_ex['cantidad_cambio'];
                    $fi["pedido"] = $row_ex['descripcion'];
                    $fi["importe"] = $importe;

                    $array_impresion[$cnt] = $fi;
                    $cnt = $cnt + 1;
                }

                $subTotal = $subTotal + floatval($importe);
            }
            
            foreach($array_impresion as $imp){
                echo "<tr>";
                echo "<td class='txt-center'>" . $imp['cantidad'] . "</td>";
                echo "<td colspan='2'>" . utf8_encode($imp['pedido']) . "</td>";
                echo "<td class='txt-right'>" . number_format($imp['importe'],2) . "</td>";
                echo "</tr>";
            }

            //Verificamos si hay que facturar la propina
            $mostrar_propina = 0;
            $query_pro = "Select subTotal as dpropina from pedido where pkPediido = '".$_REQUEST["id"]."'";

            $result_pro = $db->executeQuery($query_pro);
            if($row_pro = $db->fecth_array($result_pro)){
                $mostrar_propina = intval($row_pro["dpropina"]);
                if($mostrar_propina === 1){
                    $monto_propina = 0;
                    $query_tp = "Select sum(monto) as propina from pedido_propina where pkPediido = '".$_REQUEST["id"]."'";
                    $tp = $db->executeQuery($query_tp);
                    if ($rowp = $db->fecth_array($tp)){
                        $monto_propina = floatval($rowp["propina"]);
                    }

                    if($monto_propina > 0){
                        echo "<tr>";
                        echo "<td class='txt-center'>1</td>";
                        echo "<td colspan='2'>CARGO POR SERVICIO</td>";
                        echo "<td class='txt-right'>" .number_format($monto_propina,2) . "</td>";
                        echo "</tr>";

                        $subTotal = $subTotal + number_format($monto_propina,2);
                    }
                }
            }

            $totalPagar = ($subTotal - $descuento);
            
            $subtototalprint =  number_format(floatval($totalPagar/$stigv),2);
           
            $igvprint = $igv * ($totalPagar / $stigv);
         
                        
            ?>  
            <tr>
                <td colspan="4">
                    <hr class="hr-detalle"/>
                </td>
            </tr>
        </table>        
        <table border="0" width="100%">  
            
            <?php
            if ($descuento > 0) {
                echo "<tr class='txt-center bold'>";
                echo "<td></td>";
                echo "<td class='txt-right'>Descuento:</td>";
                echo "<td colspan='2' class='txt-right'>  S/ " . number_format(floatval($descuento),2) . "</td>";
                echo "</tr>";
            }
            ?>

            <tr class="txt-center bold">
                <td></td>
                <td class="txt-right">Total:</td>
                <td colspan="2" class="txt-right">S/ <?php echo number_format(floatval($totalPagar),2); ?></td>
            </tr>

            <tr>
                <td colspan="4">
                    <hr class="hr-detalle"/>
                </td>
            </tr>

            <!-- Mostramos medios de pago -->
            <?php
                $query_medios = "Select md.monto, md.comentario, mo.simbolo, mp.nombre, md.id_medio  from movimiento_dinero md, medio_pago mp, moneda mo where md.tipo_origen = 'PED' AND md.id_origen = '".$_GET["id"]."' AND md.id_medio = mp.id AND md.moneda = mo.id";
                $res_pagos = $db->executeQuery($query_medios);
                $total_efectivo = 0;
                while($rp = $db->fecth_array($res_pagos)){
                    if(intval($rp["id_medio"]) === 1){
                        $total_efectivo = $total_efectivo + floatval($rp["monto"]);
                    }else{ ?>
                        <tr class="txt-center bold">
                            <td></td>
                            <?php if($rp["comentario"] == ""){?>
                                <td class="txt-right"><?php echo $rp["nombre"]."";?></td>
                            <?php }else{?>
                                <td class="txt-right"><?php echo $rp["nombre"]." (".$rp["comentario"].")";?></td>  
                            <?php }?>       
                            <td colspan="2" class="txt-right"><?php echo $rp["simbolo"]." ".number_format(floatval($rp["monto"]),2); ?></td>
                        </tr>
            <?php            
                    }
                }
            ?>
            <!--Buscamos los medios de pago para propinas (si es que aplica)-->
            <?php
                if($mostrar_propina === 1){
                    $query_propinas = "Select pp.monto, mo.simbolo, mp.nombre, pp.id_medio from pedido_propina pp, medio_pago mp, moneda mo where pp.pkPediido = '".$_REQUEST["id"]."' AND pp.id_medio = mp.id AND pp.moneda = mo.id";
                    //echo $query_medios;
                    $res_propinas = $db->executeQuery($query_propinas);
                    while($rpr = $db->fecth_array($res_propinas)){
                        if(intval($rpr["id_medio"]) === 1){
                            $total_efectivo = $total_efectivo + floatval($rpr["monto"]);
                        }else{ ?>
                            <tr class="txt-center bold">
                                <td></td>
                                <td class="txt-right"><?php echo $rpr["nombre"]."";?></td>     
                                <td colspan="2" class="txt-right"><?php echo $rpr["simbolo"]." ".number_format(floatval($rpr["monto"]),2); ?></td>
                            </tr>
            <?php            
                        }
                    }
                }
            ?>
            <!-- si pago con efectivo mostramos la moneda-->
            <?php
                $chazchaz = 0;
                $vuelto = 0; 
                $res_efectivo = $db->executeQuery("Select * from pedido_efectivo where id_pedido = '".$_GET["id"]."'");
                if($row_efectivo = $db->fecth_array($res_efectivo)){
                    $chazchaz = floatval($row_efectivo["monto"]);
                    $vuelto = $chazchaz - $total_efectivo;
                }    
            ?>
            <?php if($chazchaz>0):?>         
            <tr class="txt-center bold">
                <td></td>
                <td class="txt-right">EFECTIVO</td>
                <td colspan="2" class="txt-right">S/ <?php echo number_format($chazchaz,2); ?></td>
            </tr>
            <?php endif;?>
            <?php if($vuelto>0):?>         
            <tr class="txt-center bold">
                <td></td>
                <td class="txt-right">VUELTO EFECTIVO</td>
                <td colspan="2" class="txt-right">S/ <?php echo number_format($vuelto,2); ?></td>
            </tr>
            <?php endif;?>
        </table>

        <br />
        <br />

        <?php
        if ($estadio == 4 || $estadio == 5) {
            echo "<table border=" . "1" . " width='100%'" . " class=" . "table-border" . ">";
            echo "<tr>";
            echo "<td>";
            echo "<br/><br/><hr class=" . "hr-detalle-60p" . "/>";
            echo "<div class=" . "txt-center" . ">" . "" . "</div>";
            echo "<div class=" . "txt-center" . ">" . $cliente . "</div>";
            echo "<div class=" . "txt-left margin-left" . ">" . "Doc: " . "</div><br/>";
            echo "</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td>";
            echo "<br/><br/><hr class=" . "hr-detalle-60p" . "/>";
            echo "<div class=" . "txt-center" . ">" . "V° B°" . "</div>";
            echo "<div class=" . "txt-left margin-left" . ">" . "Doc:" . "</div><br/>";
            echo "</td>";
            echo "</tr>";
            echo "</table>";
        }

        echo '<p></p>';
        if($nomMozo == ""){
            $nomMozo = $nombre_cajero;
        }      
        echo "<div class=" . "txt-center" . ">" . "Usted ha sido atendido por ".$nomMozo."</div>";
        echo '<p></p>';
        echo "<div class=" . "txt-center" . ">" . "Este no es un comprobante de pago solicite su boleta o factura." . "</div>";

        echo '<p></p>
        <div class="txt-center"><b>USQAY</b> es facturacion electronica</div>
        <div class="txt-center">www.sistemausqay.com</div>
        <div class="txt-center">www.facebook.com/UsqayPeru</div>';

        ?>
    </body>
</html>

<?php
echo $impresion;
?>
</li>