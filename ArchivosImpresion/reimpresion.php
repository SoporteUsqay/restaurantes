<?php require_once '../Components/Config.inc.php'; 
$db = new SuperDataBase();
$mesa = "";
?>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="../Public/css/impresion_style.css" />
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
        <title>c</title>
    </head>
    <body style="font-family: sans-serif; margin: 0px !important;">
        <?php

        //Moso
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
        $query_numMesa = "SELECT nmesa FROM mesas m WHERE pkMesa=".$mesa;
        $result_numMesa = $db->executeQuery($query_numMesa);
        $numMesa = "";
        while ($row1 = $db->fecth_array($result_numMesa)) {
            $numMesa = $row1['nmesa'];
        }
        
        
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
        "estadoImpresion, t.nombres, t.apellidos, CASE WHEN character_length(pkProducto) <6 THEN (SELECT UPPER(descripcion) " .
        "FROM plato pl WHERE pl.pkPlato=d.pkPlato) WHEN character_length(pkPlato)<6 THEN (SELECT UPPER(descripcion) " .
        "FROM productos pr WHERE pr.pkProducto=d.pkProducto) END AS pedido FROM detallepedido d  INNER JOIN trabajador t " .
        " ON t.pkTrabajador=pkMozo WHERE d.pkPediido='".$_GET["id"]."' and " .
        "d.estado=1;";
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
        
        $msjPago = "REIMPRESION";
        ?>
        <table border="0" width="100%">
            <tr>
                <!-- Cabecera, contiene datos de la empresa -->
                <td colspan="4" class="txt-center">
                    <div style="font-size: 19px; font-weight: bold;">
                        <?php echo $nomSucursal; ?>
                    </div>
                    <?php echo utf8_encode($razonSucursal); ?><br>
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
            <tr>
                <td><?php echo date('d/m/Y'); ?></td>
                <td></td>
                <td></td>
                <td class="txt-right"><?php echo date('h:i:s'); ?></td>
            </tr>
            <tr>
                <td><?php echo $numMesa; ?></td>
                <td></td>
                <td></td>
                <td class="txt-right"></td>
            </tr>
            <tr>
                <td colspan="4">
                    <hr class="hr-detalle"/>
                </td>
            </tr>
        </table>
        <table border="0" width="100%">
            <tr class="txt-center arial">
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
                echo "<tr class='arial'>";
                echo "<td class='txt-center'>" . $imp['cantidad'] . "</td>";
                echo "<td colspan='2'>" . utf8_encode($imp['pedido']) . "</td>";
                echo "<td class='txt-right'>" . number_format($imp['importe'],2) . "</td>";
                echo "</tr>";
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
            
            <tr class="txt-center">
                <td></td>
                <td class="txt-right">Subtotal:</td>
                <td colspan="2" class="txt-right">S/. <?php echo $subtototalprint; ?></td>
            </tr>
           
            <?php
            if ($descuento > 0) {
                echo "<tr class='txt-center'>";
                echo "<td></td>";
                echo "<td class='txt-right'>Desuento:</td>";
                echo "<td colspan='2' class='txt-right'>  S/. " . number_format(floatval($descuento),2) . "</td>";
                echo "</tr>";
            }
            ?>
            <tr class="txt-center">
                <td></td>
                <td class="txt-right">I.G.V:</td>
                <td colspan="2" class="txt-right">S/. <?php echo number_format($igvprint,2); ?></td>
            </tr>
            
            <tr class="txt-center bold">
                <td></td>
                <td class="txt-right">Total:</td>
                <td colspan="2" class="txt-right">S/. <?php echo number_format(floatval($totalPagar),2); ?></td>
            </tr>
        </table>

        <br />
        <br />

    </body>
</html>
</li>