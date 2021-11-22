<?php
//importando los archivos de configuración
require_once '../Components/Config.inc.php';
require_once '../Components/CifrasEnLetras.php';
?>
<html lang="en">
    <head>
        <title>f</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="../Public/css/impresion_style.css"/>
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
    </head>
    <body style="margin: 0px !important;">
        <?php
        $nombre_cajero = "";
        //establecemos la conexión a la base de datos
        $db = new SuperDataBase();
        //Obtenemos SERIE
        $serie = NULL;
        $c0 = "Select * from cloud_config where parametro = 'sfactura'";
        $s0 = $db->executeQuery($c0);
        if ($row = $db->fecth_array($s0)){
            $serie = $row["valor"];
        }
        //Obtenemos URL consulta
        $url_consulta = NULL;
        $c1 = "Select * from cloud_config where parametro = 'urlpce'";
        $s1 = $db->executeQuery($c1);
        if ($row = $db->fecth_array($s1)){
            $url_consulta = $row["valor"];
        }
        //Obtenemos la data del cajero
        $dcajero = $db->executeQuery("Select * from trabajador where pkTrabajador = '".$_GET["cajero"]."'");
        //Procesamos mysql
        while ($row0 = $db->fecth_array($dcajero)) {
            $nombre_cajero = $row0["nombres"];
        }
        
        //obtenemos los datos de la sucursal a mostrar en el comprobante de pago
        $query_sucursal = "SELECT nombreSucursal, razon, ruc, direccion, ciudad, telefono, pagweb, serieImpresora " .
                "FROM sucursal " .
                "WHERE pkSucursal = 'SU009'";

        $result_sucursal = $db->executeQuery($query_sucursal);
        while ($row_sucursal = $db->fecth_array($result_sucursal)) {
            $nomSucursal = $row_sucursal['nombreSucursal'];
            $razonSucursal = $row_sucursal['razon'];
            $rucSucursal = $row_sucursal['ruc'];
            $dirSucursal = $row_sucursal['direccion'];
            $ciudadSucursal = $row_sucursal['ciudad'];
            $telSucursal = $row_sucursal['telefono'];
            $webSucursal = $row_sucursal['pagweb'];
            $serieImpresora = $row_sucursal['serieImpresora'];
        }

        //obtenemos el código del comprobante
        $codComprobante = $_GET["id"];
        
        //Numeros
        $descuento_ = NULL;

        $gravada_ = NULL;
        $inafecta_ = NULL;
        $exonerada_ = NULL;
        $gratuita_ = NULL;

        $impuesto_ = NULL;
        $bolsa_ = NULL;

        $total_ = NULL;

        //obtenemos los datos del comprobante
        $query_datosComprobante = "SELECT ncomprobante, fechaImpresion, " .
        "CASE WHEN (character_length(c.ruc) = 11) THEN " .
        "(SELECT c.ruc) " .
        "WHEN (character_length(c.documento) > 0 AND character_length(c.documento) < 8) THEN".
        "(Select '-')".
        "WHEN (character_length(c.documento) = 8) THEN " .
        "(SELECT c.documento) " .
        "END AS documento, " .
        "CASE WHEN (character_length(c.ruc) = 11) THEN " .
        "(SELECT RazonSocial FROM persona_juridica pj WHERE pj.ruc = c.ruc) " .
        "WHEN (character_length(c.documento) > 0 AND character_length(c.documento) < 8) THEN".
        "(select nombre from cliente_generico where id=c.documento)".
        "WHEN (character_length(c.documento) = 8) THEN " .
        "(SELECT Nombres FROM person p WHERE p.documento = c.documento) " .
        "END AS cliente, " .
        "CASE WHEN (character_length(c.ruc) = 11) THEN " .
        "(SELECT address FROM persona_juridica pj WHERE pj.ruc = c.ruc) " .
        "WHEN (character_length(c.documento) > 0 AND character_length(c.documento) < 8) THEN".
        "(select direccion from cliente_generico where id=c.documento)".
        "WHEN (character_length(c.documento) = 8) THEN " .
        "(SELECT address FROM person p WHERE p.documento = c.documento) " .
        "END AS direccion " .
        "FROM comprobante c " .
        "WHERE c.pkComprobante = '$codComprobante'";
        
        $result_datosComprobante = $db->executeQuery($query_datosComprobante);
        while ($row = $db->fecth_array($result_datosComprobante)) {
                $numComprobante = $row['ncomprobante'];            
                $fechaImpresion = date_create($row['fechaImpresion']);
                $documentoCliente = $row['documento'];
                $cliente = $row['cliente'];
                $direccionCliente = $row['direccion'];
        }

        //Obtenemos totales
        $query_totales = "Select * from comprobante_impuestos where pkComprobante = '".$codComprobante."'";
        $result_totales = $db->executeQuery($query_totales);
        if($row0 = $db->fecth_array($result_totales)){
            $gravada_ = $row0["total_gravada"];
            $impuesto_ = $row0["total_igv"];
            $inafecta_ = $row0["total_inafecta"];
            $exonerada_ = $row0["total_exonerada"];
            $descuento_ = $row0["total_descuento"];
            $gratuita_ = $row0["total_gratuita"];
            $bolsa_ = $row0["total_icbper"];
            $total_ = $row0["total_final"];
        }
        
        //obtenemos los detalles de pedido del comprobante
        $query_detallePedido = "SELECT dc.pkDetalleComprobante, dp.pkDetallePedido, dp.pkPediido, dp.pkPlato, tr.nombres AS mozo, dp.descripcionPedido, " .
        "(SELECT UPPER(descripcion) FROM plato pl WHERE pl.pkPlato=dp.pkPlato) " .
        "AS pedido, cantidad, precio, " .
        "CASE WHEN MOD(cantidad,1) = 0 THEN " .
        "ROUND((cantidad*precio),2) " .
        "ELSE " .
        "ROUND((cantidad*precio),0) " .
        "END AS importe, p.descuento " .
        "FROM detalle_comprobante2 dc " .
        "INNER JOIN detallepedido dp ON dc.pkDetallePedido = dp.pkDetallePedido " .
        "INNER JOIN pedido p ON p.pkPediido = dp.pkPediido " .
        "INNER JOIN trabajador tr ON tr.pkTrabajador = p.idUser " .
        "WHERE dc.pkDetalleComprobante = '$codComprobante'";

        $result_detallePedido = $db->executeQuery($query_detallePedido);  
        //Obtenemos id pedido original
        $pkPedido_c = "";
        $query_id_pedido = "Select * from detallecomprobante where pkComprobante = '".$codComprobante."'";
        $result_id_pedido = $db->executeQuery($query_id_pedido);
        while ($row = $db->fecth_array($result_id_pedido)) {
            $pkPedido_c = $row["pkPediido"];
        }

        //Obtenemos monto de impuesto de bolsa plastica
        $monto_icbper = 0;
        $query_icbper = "Select * from cloud_config where parametro = 'icbper'";
        $result_i = $db->executeQuery($query_icbper);

        if($row = $db->fecth_array($result_i)){
            $monto_icbper = floatval($row["valor"]);
        }

        //Variable para mostrar propina
        $mostrar_propina = 0;
        ?>

        <!-- definimos el formato de la boleta mediante una tabla -->        
        <table border="0" width="100%">
            <tr>
                <!-- Cabecera, contiene datos de la empresa -->
                <td colspan="5" class="txt-center">
                    <?php
                    if (file_exists("../Public/images/logo_empresa.png")) {
                        echo "<center><img src='../Public/images/logo_empresa.png' style='width:200px;'/></center>";
                        echo "<p></p>";
                    }
                    ?>
                    <?php
                    if (!file_exists("../Public/images/logo_empresa.png")) {?>
                        <div style="font-size: 24px; font-weight: bold;">
                            <?php echo $nomSucursal; ?>
                        </div>
                    <?php } ?>
                    <?php echo $razonSucursal; ?><br>
                    RUC <?php echo $rucSucursal; ?><br/>
                    <?php echo utf8_encode($dirSucursal); ?><br/>
                    <?php echo utf8_encode($ciudadSucursal); ?><br/>
                    <?php echo utf8_encode($telSucursal); ?><br/>
                    <!--                    <br>-->
                    <hr class="hr-header"/>
                </td>
                <!-- Fin cabecera -->
            </tr>
            <tr>
                <td colspan="5" class="txt-center bold">
                    FACTURA ELECTRONICA<br/>
                    <?php echo $serie?> - 
                    <?php echo str_pad($numComprobante, 6, "0", STR_PAD_LEFT);  ?><br/>
                    <hr class="hr-header"/>
                    <!--                    <br>-->
                </td>
            </tr>
            <tr>                
                <td colspan="2">Emisión</td>
                <td colspan="1">:</td>
                <td colspan="2"><?php echo date_format($fechaImpresion, 'd/m/Y  H:i:s') ?></td>
            </tr>
            <tr>
                <td colspan="2">Moneda</td>
                <td colspan="1">:</td>
                <td colspan="2">Soles</td>
            </tr>
            <tr>
                <td colspan="2">Cajero</td>
                <td colspan="1">:</td>
                <td colspan="2"><?php echo $nombre_cajero; ?></td>
            </tr>
            <tr><td colspan="5"><hr class="hr-detalle"/></td></tr>
            <?php
            if (isset($documentoCliente)) {
                echo "<tr>";
                echo "<td colspan='2'>Documento</td>";
                echo "<td colspan='1'>:</td>";
                echo "<td colspan='2'>" . urldecode($documentoCliente) . "</td>";
                echo "</tr>";
                echo "<tr>";
                echo "<td colspan='2'>Cliente</td>";
                echo "<td colspan='1'>:</td>";
                echo "<td colspan='2'>" . urldecode($cliente) . "</td>";
                echo "</tr>";
                echo "<tr>";
                echo "<td colspan='2'>Dirección</td>";
                echo "<td colspan='1'>:</td>";
                echo "<td colspan='2'>" . urldecode($direccionCliente) . "</td>";
                echo "</tr>";
                echo '<tr><td colspan="5"><hr class="hr-detalle"/></td></tr>';         
            }
            ?>
        </table>     

        <table border="0" width="100%">
            <tr class="txt-center">
                <?php 
                    echo "<td>Cant</td>";
                    echo "<td colspan='2'>Descripción</td>";
                    echo "<td>Total</td>";                                   
                ?>
            </tr>
            <tr>
                <td colspan="4">
                    <hr class="hr-detalle"/>
                </td>
            </tr>
            <?php
            //Condiciones para busqueda de huerfanos
            $condiciones = "";

            //Buscamos si es por consumo
            $consumo = 1;
            if(isset($_REQUEST["consumo"])){
                $consumo = intval($_REQUEST["consumo"]);
            }
            
            if($consumo === 1){
                //mostramos los detalles del pedido
                while ($row = $db->fecth_array($result_detallePedido)) {
                    //Verificamos si elemento fue sometido al cambiazo
                    $query_ver = "Select cf.id, cf.cantidad_cambio, cf.precio_cambio, cf.pk_plato_cambio, p.descripcion, cf.pk_pedido_destino from cambio_facturacion cf, plato p where cf.pk_plato_cambio = p.pkPlato AND cf.pk_detalle =  '".$row['pkDetallePedido']."' AND cf.pk_detalle = '".$row["pkDetallePedido"]."'";
                    
                    $hay = 0;
                    
                    $r_ver = $db->executeQuery($query_ver);
                    
                    while($row_v = $db->fecth_array($r_ver)){
                        $condiciones = $condiciones." AND cf.id <> '".$row_v['id']."'";
                        $hay = 1;

                        $query_bolsa = "Select * from plato_codigo_sunat where id_plato = '".$row_v['pk_plato_cambio']."' AND id_tipo_impuesto = 5";

                        $r_bolsa = $db->executeQuery($query_bolsa);

                        if($row_b = $db->fecth_array($r_bolsa)){
                            $precio_bolsa = floatval($row_v['precio_cambio']) - $monto_icbper;
                            if($precio_bolsa>0){
                                echo "<tr>";
                                echo "<td class='txt-center'>" . $row_v['cantidad_cambio'] . "</td>";
                                echo "<td colspan='2'>" . strtoupper(utf8_encode($row_v['descripcion'])) . "</td>";
                                echo "<td class='txt-right'>" .number_format((floatval($row_v['cantidad_cambio'])*$precio_bolsa),2) . "</td>";
                                echo "</tr>";
                            }
                            echo "<tr>";
                            echo "<td class='txt-center'>" . $row_v['cantidad_cambio'] . "</td>";
                            echo "<td colspan='2'>IMPUESTO BOLSA PLASTICA</td>";
                            echo "<td class='txt-right'>" .number_format((floatval($row_v['cantidad_cambio'])*$monto_icbper),2) . "</td>";
                            echo "</tr>";
                        }else{
                            echo "<tr>";
                            echo "<td class='txt-center'>" . $row_v['cantidad_cambio'] . "</td>";
                            echo "<td colspan='2'>" . strtoupper(utf8_encode($row_v['descripcion'])) . "</td>";
                            echo "<td class='txt-right'>" .number_format((floatval($row_v['cantidad_cambio'])*floatval($row_v['precio_cambio'])),2) . "</td>";
                            echo "</tr>";
                        }
                    }
                    
                    
                    if($hay === 0){
                        $query_bolsa = "Select * from plato_codigo_sunat where id_plato = '".$row['pkPlato']."' AND id_tipo_impuesto = 5";

                        $r_bolsa = $db->executeQuery($query_bolsa);

                        if($row_b = $db->fecth_array($r_bolsa)){
                            $precio_bolsa = floatval($row['importe']) - $monto_icbper;
                            if($precio_bolsa>0){
                                echo "<tr>";
                                echo "<td class='txt-center'>" . $row['cantidad'] . "</td>";
                                echo "<td colspan='2'>" . strtoupper(utf8_encode($row['pedido'])) . "</td>";
                                echo "<td class='txt-right'>" .number_format((floatval($row['cantidad'])*$precio_bolsa),2) . "</td>";
                                echo "</tr>";
                            }
                            echo "<tr>";
                            echo "<td class='txt-center'>" . $row['cantidad'] . "</td>";
                            echo "<td colspan='2'>IMPUESTO BOLSA PLASTICA</td>";
                            echo "<td class='txt-right'>" .number_format((floatval($row['cantidad'])*$monto_icbper),2) . "</td>";
                            echo "</tr>";
                        }else{
                            echo "<tr>";
                            echo "<td class='txt-center'>" . $row['cantidad'] . "</td>";
                            echo "<td colspan='2'>" . strtoupper(utf8_encode($row['pedido'])) . "</td>";
                            echo "<td class='txt-right'>" . $row['importe'] . "</td>";
                            echo "</tr>";
                        }
                    }
                }

                //Buscamos detalles truchos huerfanos
                $query_ex = "Select cf.id, cf.cantidad_cambio, cf.precio_cambio, cf.pk_plato_cambio, p.descripcion from cambio_facturacion cf, plato p where cf.pk_plato_cambio = p.pkPlato AND cf.pk_pedido_destino = '".$pkPedido_c."'".$condiciones;
                $result_ex = $db->executeQuery($query_ex);
                while($row_ex = $db->fecth_array($result_ex)){
                    $query_bolsa = "Select * from plato_codigo_sunat where id_plato = '".$row_ex['pk_plato_cambio']."' AND id_tipo_impuesto = 5";

                    $r_bolsa = $db->executeQuery($query_bolsa);

                    if($row_b = $db->fecth_array($r_bolsa)){
                        $precio_bolsa = floatval($row_ex['precio_cambio']) - $monto_icbper;
                        if($precio_bolsa>0){
                            echo "<tr>";
                            echo "<td class='txt-center'>" . $row_ex['cantidad_cambio'] . "</td>";
                            echo "<td colspan='2'>" . strtoupper(utf8_encode($row_ex['descripcion'])) . "</td>";
                            echo "<td class='txt-right'>" .number_format((floatval($row_ex['cantidad_cambio'])*$precio_bolsa),2) . "</td>";
                            echo "</tr>";
                        }
                        echo "<tr>";
                        echo "<td class='txt-center'>" . $row_ex['cantidad_cambio'] . "</td>";
                        echo "<td colspan='2'>IMPUESTO BOLSA PLASTICA</td>";
                        echo "<td class='txt-right'>" .number_format((floatval($row_ex['cantidad_cambio'])*$monto_icbper),2) . "</td>";
                        echo "</tr>";
                    }else{
                        echo "<tr>";
                        echo "<td class='txt-center'>" . $row_ex['cantidad_cambio'] . "</td>";
                        echo "<td colspan='2'>" . strtoupper(utf8_encode($row_ex['descripcion'])) . "</td>";
                        echo "<td class='txt-right'>" .number_format((floatval($row_ex['cantidad_cambio'])*floatval($row_ex['precio_cambio'])),2) . "</td>";
                        echo "</tr>";
                    }
                }

                //Buscamos detalles si es personalizada
                $query_ex = "Select d.id_plato, d.precio_unitario, d.cantidad, d.total, p.descripcion from detalle_comprobante_directo d, plato p where d.id_plato = p.pkPlato AND d.id_comprobante = '".$codComprobante."'";

                $result_ex = $db->executeQuery($query_ex);
                while($row_ex = $db->fecth_array($result_ex)){
                    $query_bolsa = "Select * from plato_codigo_sunat where id_plato = '".$row_ex['id_plato']."' AND id_tipo_impuesto = 5";

                    $r_bolsa = $db->executeQuery($query_bolsa);

                    if($row_b = $db->fecth_array($r_bolsa)){
                        $precio_bolsa = floatval($row_ex['precio_unitario']) - $monto_icbper;
                        if($precio_bolsa>0){
                            echo "<tr>";
                            echo "<td class='txt-center'>" . $row_ex['cantidad'] . "</td>";
                            echo "<td colspan='2'>" . strtoupper(utf8_encode($row_ex['descripcion'])) . "</td>";
                            echo "<td class='txt-right'>" .number_format((floatval($row_ex['cantidad'])*$precio_bolsa),2) . "</td>";
                            echo "</tr>";
                        }
                        echo "<tr>";
                        echo "<td class='txt-center'>" . $row_ex['cantidad'] . "</td>";
                        echo "<td colspan='2'>IMPUESTO BOLSA PLASTICA</td>";
                        echo "<td class='txt-right'>" .number_format((floatval($row_ex['cantidad'])*$monto_icbper),2) . "</td>";
                        echo "</tr>";
                    }else{
                        echo "<tr>";
                        echo "<td class='txt-center'>" . $row_ex['cantidad'] . "</td>";
                        echo "<td colspan='2'>" . strtoupper(utf8_encode($row_ex['descripcion'])) . "</td>";
                        echo "<td class='txt-right'>" .number_format((floatval($row_ex['cantidad'])*floatval($row_ex['precio_unitario'])),2) . "</td>";
                        echo "</tr>";
                    }
                }

                //Verificamos si hay que facturar la propina
                $mostrar_propina = 0;
                $query_pro = "Select subTotal as dpropina from pedido where pkPediido = '".$pkPedido_c."'";

                $result_pro = $db->executeQuery($query_pro);
                if($row_pro = $db->fecth_array($result_pro)){
                    $mostrar_propina = intval($row_pro["dpropina"]);
                    if($mostrar_propina === 1){
                        $monto_propina = 0;
                        $query_tp = "Select sum(monto) as propina from pedido_propina where pkPediido = '".$pkPedido_c."'";
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
                        }
                    }
                }

            }else{
                echo "<tr>";
                echo "<td class='txt-center'>1</td>";
                echo "<td colspan='2'>POR CONSUMO</td>";
                echo "<td class='txt-right'>" .number_format($gravada_,2). "</td>";
                echo "</tr>";
            }

            ?>
            <tr>
                <td colspan="4">
                    <hr class="hr-detalle"/>
                </td>
            </tr>
        </table>
        <table border="0" width="100%" style="">
            <?php
            if (floatval($descuento_) > 0) {
                echo "<tr class='txt-center bold'>";
                echo "<td></td>";
                echo "<td class='txt-right'>Descuento Total:</td>";
                echo "<td colspan='2' class='txt-right'>  S/ ";
                echo number_format($descuento_,2);
                echo "</td>";
                echo "</tr>";
            }
            ?>

            <?php
            if (floatval($gravada_) > 0) {
                echo "<tr class='txt-center bold'>";
                echo "<td></td>";
                echo "<td class='txt-right'>Op. Gravadas:</td>";
                echo "<td colspan='2' class='txt-right'>  S/ ";
                echo number_format($gravada_,2);
                echo "</td>";
                echo "</tr>";
            }
            ?>

            <?php
            if (floatval($inafecta_) > 0) {
                echo "<tr class='txt-center bold'>";
                echo "<td></td>";
                echo "<td class='txt-right'>Op. Inafectas:</td>";
                echo "<td colspan='2' class='txt-right'>  S/ ";
                echo number_format($inafecta_,2);
                echo "</td>";
                echo "</tr>";
            }
            ?>

            <?php
            if (floatval($exonerada_) > 0) {
                echo "<tr class='txt-center bold'>";
                echo "<td></td>";
                echo "<td class='txt-right'>Op. Exoneradas:</td>";
                echo "<td colspan='2' class='txt-right'>  S/ ";
                echo number_format($exonerada_,2);
                echo "</td>";
                echo "</tr>";
            }
            ?>

            <?php
            if (floatval($gratuita_) > 0) {
                echo "<tr class='txt-center bold'>";
                echo "<td></td>";
                echo "<td class='txt-right'>Op. Exoneradas:</td>";
                echo "<td colspan='2' class='txt-right'>  S/ ";
                echo number_format($gratuita_,2);
                echo "</td>";
                echo "</tr>";
            }
            ?>

            <?php
            if (floatval($bolsa_) > 0) {
                echo "<tr class='txt-center bold'>";
                echo "<td></td>";
                echo "<td class='txt-right'>ICBPER:</td>";
                echo "<td colspan='2' class='txt-right'>  S/ ";
                echo number_format($bolsa_,2);
                echo "</td>";
                echo "</tr>";
            }
            ?>
           <tr class="txt-center bold">
                <td></td>
                <td class="txt-right">I.G.V:</td>
                <td colspan="2" class="txt-right">S/ <?php echo number_format($impuesto_, 2); ?></td>
            </tr>                   
            <tr class="txt-center bold">
                <td></td>
                <td class="txt-right">Total:</td>
                <td colspan="2" class="txt-right">S/ <?php echo number_format($total_, 2); ?></td>
            </tr>
            
            <tr>
                <td colspan="4">
                    <hr class="hr-detalle"/>
                </td>
            </tr>
            <!-- Mostramos medios de pago -->
            <?php
                $query_medios = "Select md.monto, md.comentario, mo.simbolo, mp.nombre, md.id_medio  from movimiento_dinero md, medio_pago mp, moneda mo where md.tipo_origen = 'PED' AND md.id_origen = '".$pkPedido_c."' AND md.id_medio = mp.id AND md.moneda = mo.id";
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
            <tr class="txt-center bold">
                <td></td>
                <td class="txt-right">Forma de Pago: </td>
                <td class="txt-right">Al CONTADO</td>   
                <td colspan="2" class="txt-right"></td>
            </tr>
            <!--Buscamos los medios de pago para propinas (si es que aplica)-->
            <?php
                if($mostrar_propina === 1){
                    $query_propinas = "Select pp.monto, mo.simbolo, mp.nombre, pp.id_medio from pedido_propina pp, medio_pago mp, moneda mo where pp.pkPediido = '".$pkPedido_c."' AND pp.id_medio = mp.id AND pp.moneda = mo.id";
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
                $res_efectivo = $db->executeQuery("Select * from pedido_efectivo where id_pedido = '".$pkPedido_c."'");
                $chazchaz = 0;
                if($row_efectivo = $db->fecth_array($res_efectivo)){
                    $chazchaz = floatval($row_efectivo["monto"]);
                }   
                
                $total_detraccion = 0;
                if ($pkPedido_c) {
                    $res_detra = $db->executeQuery("select * from pedido_detraccion where pedido_id = '$pkPedido_c'");
                    if ($row_detra = $db->fecth_array($res_detra)) {
                        $total_detraccion = floatval($row_detra['total']);
                    }
                } else {
                    $res_detra = $db->executeQuery("select * from pedido_detraccion where comprobante_id = '$codComprobante'");
                    if ($row_detra = $db->fecth_array($res_detra)) {
                        $total_detraccion = floatval($row_detra['total']);
                    }
                }

                $vuelto = $chazchaz - $total_efectivo;
            ?>
            <?php if($chazchaz>0):?>         
            <tr class="txt-center bold">
                <td></td>
                <td class="txt-right">EFECTIVO</td>
                <td colspan="2" class="txt-right">S/ <?php echo number_format($chazchaz,2); ?></td>
            </tr>
            <?php endif;?>
            <?php if ($total_detraccion > 0) :?>
            <tr class="txt-center bold">
                <td></td>
                <td class="txt-right">DETRACCIÓN</td>
                <td colspan="2" class="txt-right">S/ <?php echo number_format($total_detraccion,2); ?></td>
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
        <h4 class="msg" style="text-transform: uppercase;"><?php  echo CifrasEnLetras::convertirNumeroEnLetras(number_format($total_, 2, ',', '.'),1, "nuevo sol","nuevos soles",true, "céntimo","",false)
                                                        ?></h4>
        <?php if ($total_detraccion > 0): ?>
        <p></p>
        <div class="txt-center" style=''>Operación sujeta al Sistema de Pago de Obligaciones
        Tributarias - N° de Cuenta BANCO DE LA NACIÓN: 
        <?php 
            $res_cu = $db->executeQuery("Select * from cloud_config where parametro = 'cuenta_detraccion'");
            if ($row_cu = $db->fecth_array($res_cu)) {
                echo $row_cu['valor'];
            }
        ?>
        </b></div>
        <?php endif ?>
        <p></p>
        <div class="txt-center" style=''>Representacion impresa de la FACTURA ELECTRONICA. Puedes descargar el documento en <b><?php echo $url_consulta;?></b></div>
        <p></p>
        <div class="txt-center" style=''>Autorizado mediante Resolucion <b>0340050005315/SUNAT</b></div>
        
        <?php 
        //Obtenemos nuevos datos PSE
        $query_extra = "Select * from comprobante_hash where pkComprobante = '".$codComprobante."'";
        $res_extra = $db->executeQuery($query_extra);
        $qr = "";
        $hash = "";
        
        if($rowR = $db->fecth_array($res_extra)){
            $qr = $rowR["hash_qr"];
            $hash = $rowR["hash"];
        }
        
        //Si la cadena del qr esta vacia, la armamos nosotros
        if($qr == ""){
            $qr .= "".$rucSucursal." | 03 | ".$serie." | ".str_pad($numComprobante, 6, "0", STR_PAD_LEFT)." | ".$impuesto_." | ".$total_." | ".date("d/m/Y")." | 1 | ".$documentoCliente." |";
        }
        
        ?>
               
        <center><img src="qrgen.php?data=<?php echo urlencode($qr); ?>" style="width:110px !important;"/></center>
        <div class="txt-center" style=''><?php echo $hash;?></div>
        <p></p>
        <div class="txt-center" style=''><b>USQAY</b> es facturacion electronica</div>
        <div class="txt-center">www.sistemausqay.com</div>
        <div class="txt-center">www.facebook.com/UsqayPeru</div>

    </body>
</html>