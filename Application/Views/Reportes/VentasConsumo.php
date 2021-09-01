<?php 
error_reporting(E_ALL);
$db = new SuperDataBase();

$ModeloFechaCierre = new Application_Models_CajaModel();
$fechaHoy = $ModeloFechaCierre->fechaCierre();

$fechaInicio = date('Y-m-d');
if (isset($_REQUEST['txtfechainicio'])){
    $fechaInicio = $_REQUEST['txtfechainicio'];
}

$fechaFin = date('Y-m-d');
if (isset($_REQUEST['txtfechafin'])){
    $fechaFin = $_REQUEST['txtfechafin'];
}

$estadoVentas = 1;
if (isset($_REQUEST['cmbventas'])){
    $estadoVentas = $_REQUEST['cmbventas'];
}

$tipo_comprobante = 3;
if (isset($_REQUEST['cmbcomprobantes'])){
    $tipo_comprobante = $_REQUEST['cmbcomprobantes'];
}

//Caja
$caja = $_COOKIE["c"];
if (isset($_REQUEST['caja'])){
    $caja = $_REQUEST["caja"];
}

//Nivel de Usuario
$estilo_nivel = "style='display:none;'";
if(intval($_COOKIE["TYP"]) > 0 &&  intval($_COOKIE["TYP"]) < 3){
    $estilo_nivel = "";
}

$tipo_reporte = "Ventas";

switch(intval($estadoVentas)){
    case 0:
    case 1:
        $tipo_reporte = "Ventas";
    break;

    case 4:
        $tipo_reporte = "Ventas al Credito";
    break;

    case 3:
        $tipo_reporte = "Ventas Anuladas";
    break;

    case 5:
        $tipo_reporte = "Ventas por Consumo";
    break;
}

$titulo_importante = "Reporte de ".$tipo_reporte." de ";

if($fechaInicio === $fechaFin){
    $titulo_importante = $titulo_importante.$fechaInicio;
}else{
    $titulo_importante = $titulo_importante.$fechaInicio." al ".$fechaFin;
}


include 'Application/Views/template/header.php'; 
$objViewMenu = new Application_Views_IndexView();
$objViewMenu->showContent();

?>
<link rel="stylesheet" href="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.css">
<style>
    .dataTables_filter{
        margin-right:15px !important;
    }
    .lbl-total {
        font-size: 20px
    }
</style>
<body>
    <?php
    //Delimitamos consulta por caja
    $query_caja = "";
    if($caja !== ""){
        $query_caja = " AND md.caja = '".$caja."'";
    }

    $query_por_comprobante = "";

    switch ($tipo_comprobante) {

        case 0:
            $query_por_comprobante = "  AND p.pkPediido NOT IN (
                SELECT
                    dc.pkPediido
                FROM
                    detallecomprobante dc,
                    comprobante c
                WHERE
                    dc.pkComprobante = c.pkComprobante
                and c.estado = 0
            )";
        break;

        case 1:
        case 2:
            $query_por_comprobante = "  AND p.pkPediido IN (
                SELECT
                    dc.pkPediido
                FROM
                    detallecomprobante dc,
                    comprobante c
                WHERE
                    dc.pkComprobante = c.pkComprobante
                and c.pkTipoComprobante = $tipo_comprobante
                and c.estado = 0
            )";
            break;
    }

    $totales = array();
    $r_medios = $db->executeQuery("Select mp.id, mp.moneda, mp.nombre, m.simbolo from medio_pago mp, moneda m where mp.estado > 0 AND mp.moneda = m.id");
    while ($row_m = $db->fecth_array($r_medios)) {    
        if(intval($row_m["id"]) === 1){
            //Si es efectivo buscamos todas las monedas
            $query_monedas = "Select * from moneda where estado > 0";
            $r_monedas = $db->executeQuery($query_monedas);
            while($row_mo = $db->fecth_array($r_monedas)){
                $tmp = array();
                $tmp["nombre"] = $row_m["nombre"]." ".$row_mo["simbolo"];
                $query_total = "Select sum(md.monto) as total from movimiento_dinero md, pedido p where date(md.fecha_cierre) between '$fechaInicio' and '$fechaFin' 
                AND md.id_medio = '".$row_m["id"]."' AND md.moneda = '".$row_mo["id"]."' AND md.id_origen = p.pkPediido AND md.estado = 1 AND md.tipo_origen = 'PED' AND p.estado = '".$estadoVentas."'".$query_caja.$query_por_comprobante;
                // echo $query_total;
                $r_total = $db->executeQuery($query_total);
                if($row_t = $db->fecth_array($r_total)){
                    $tmp["total"] = round(floatval($row_t["total"]),2);
                }
                $tmp["id_medio"] = $row_m["id"];
                $tmp["id_moneda"] = $row_mo["id"];
                $totales[] = $tmp;
            }
        }else{
            $tmp = array();
            $tmp["nombre"] = $row_m["nombre"]." ".$row_m["simbolo"];
            $query_total = "Select sum(md.monto) as total from movimiento_dinero md, pedido p where date(md.fecha_cierre) between '$fechaInicio' and '$fechaFin'
             AND md.id_medio = '".$row_m["id"]."' AND md.id_origen = p.pkPediido AND md.tipo_origen = 'PED' AND md.estado = 1 AND p.estado = '".$estadoVentas."'".$query_caja.$query_por_comprobante;
            // echo $query_total;
            $r_total = $db->executeQuery($query_total);
            if($row_t = $db->fecth_array($r_total)){
                $tmp["total"] = round(floatval($row_t["total"]),2);
            }
            $tmp["id_medio"] = $row_m["id"];
            $tmp["id_moneda"] = $row_m["moneda"];
            $totales[] = $tmp;
        }
    }

    $cantidad = 0;
    $descuento = 0.00;

    $queryTotales = "";
    if($caja === ""){
        $queryTotales = "select count(*) as cantidad, IFNULL(sum(p.descuento),0.00) as descuento from pedido p where p.estado='".$estadoVentas."' and date(p.fechaCierre) between '$fechaInicio' and '$fechaFin'" . $query_por_comprobante;
    }else{
        $queryTotales = "select count(*) as cantidad, IFNULL(sum(p.descuento),0.00) as descuento from pedido p, accion_caja ac where p.estado='".$estadoVentas."' and date(p.fechaCierre) between '$fechaInicio' and '$fechaFin' AND ac.pk_accion = p.pkPediido AND ac.tipo_accion = 'PED' AND ac.caja = '".$caja."'".$query_por_comprobante;
    }

    //echo $queryTotales;
    
    $resultTotales = $db->executeQuery($queryTotales);
    while ($rowTotales = $db->fecth_array($resultTotales)) {
        $cantidad = $rowTotales['cantidad'];
        $descuento = round($rowTotales['descuento'],2);
    }
    ?>
    <style>
        .dt-buttons{
            margin-bottom: 10px !important;
        }
    </style>
    <div class="container">
        <!--<div class="jumbotron">-->
        <br>
        <br>
        <br>  <br>

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <i class="glyphicon glyphicon-list-alt"></i> Reporte de Ventas
                            <?php
                            if ($estadoVentas == 1 or $estadoVentas == 0) {
                                echo "";
                            }
                            if ($estadoVentas == 3) {
                                echo " Anuladas";
                            }
                            if ($estadoVentas == 4) {
                                echo " por Credito";
                            }
                            if ($estadoVentas == 5) {
                                echo " por Consumo";
                            }
                            ?>
                    </div>
                    <form id="frmBus" style="padding:1%;">
                        <div class='control-group row'>                               
                            <div class="col-lg-2 col-xs-12">
                                <label>Fecha Inicio</label>
                                <input class="form-control date" id="txtfechainicio" name="txtfechainicio" value="<?php echo $fechaInicio ?>">
                            </div>
                            <div class="col-lg-2 col-xs-12">
                                <label>Fecha Fin</label>
                                <input class="form-control date" id="txtfechafin" name="txtfechafin" value="<?php echo $fechaFin ?>">
                            </div>
                            <div class="col-lg-2 col-xs-12" <?php echo $estilo_nivel;?>>
                                <label>Caja</label>
                                <select name="caja" class="form-control" id="caja">
                                <option value=''>Todas</option>
                                <?php
                                $rc = $db->executeQuery("Select * from cajas");
                                while($cajas = $db->fecth_array($rc)){
                                    if($cajas["caja"] === $caja){
                                            echo "<option value='".$cajas["caja"]."' selected>".$cajas["caja"]."</option>";
                                        }else{
                                            echo "<option value='".$cajas["caja"]."'>".$cajas["caja"]."</option>";
                                        }
                                }
                                ?>
                            </select>
                            </div>
                            <div class="col-lg-3 col-xs-12">
                                <label>Buscar ventas por: </label>
                                <select type="text" class="form-control" id="cmbventas" name="cmbventas" value="<?php echo $estadoVentas ?>">
                                    <option value="0" <?php if($estadoVentas == 0){echo "selected";}?>>Seleccione una opcion</option>
                                    <option value="1" <?php if($estadoVentas == 1){echo "selected";}?>>Venta (Efectivo y Tarjeta)</option>
                                    <option value="4" <?php if($estadoVentas == 4){echo "selected";}?>>Credito</option>
                                    <option value="3" <?php if($estadoVentas == 3){echo "selected";}?>>Anuladas</option>
                                    <option value="5" <?php if($estadoVentas == 5){echo "selected";}?>>Consumo</option>
                                </select>
                            </div>
                            <div class="col-lg-3 col-xs-12">
                                <label>Tipo de Comprobante: </label>
                                <select type="text" class="form-control" id="cmbcomprobantes" name="cmbcomprobantes" value="<?php echo $tipo_comprobante ?>">
                                    <option value="3" <?php if(isset($_GET["cmbcomprobantes"])){if($_GET["cmbcomprobantes"] == 3){echo "selected";}}?>>---Todos---</option>
                                    <option value="0" <?php if(isset($_GET["cmbcomprobantes"])){if($_GET["cmbcomprobantes"] == 0){echo "selected";}}?>>Ticket</option>
                                    <option value="1" <?php if(isset($_GET["cmbcomprobantes"])){if($_GET["cmbcomprobantes"] == 1){echo "selected";}}?>>Boleta</option>
                                    <option value="2" <?php if(isset($_GET["cmbcomprobantes"])){if($_GET["cmbcomprobantes"] == 2){echo "selected";}}?>>Factura</option>
                                </select>
                            </div>
                        </div>
                        <div class='control-group row'>
                            <div class="col-lg-8 col-xs-12">
                            </div>
                            <div class="col-lg-4 col-xs-12">                                
                                <button style="margin-top:20px;float: right;margin-right: 10px;" type="button" class="btn btn-primary" onclick="buscarVentasConsumo()">Buscar</button>
                            </div>
                        </div>
                    </div>                  
                </form>

                <div class="panel panel-primary" id="totales">
                    <div class="panel-heading">TOTALES</div>
                    <div class="panel-body">
                                
                        <div class="row">
                            <div class="col-xs-12 col-md-3 text-center" style="margin-bottom: 1em">
                                <div class="lbl-total text"><?php echo $cantidad ?></div>
                                <div class="">Ventas Realizadas</div>
                            </div>

                            <div class="col-xs-12 col-md-3 text-center" style="margin-bottom: 1em">
                                <div class="lbl-total text"><?php echo number_format($descuento, 2) ?></div>
                                <div class="">Descuentos S/</div>
                            </div>

                            <?php foreach($totales as $tot):?>
                                <div class="col-xs-12 col-md-3 text-center" style="margin-bottom: 1em">
                                    <div class="lbl-total text-<?php echo $tot['total'] >= 0 ? 'success' : 'danger' ?>"><?php echo number_format($tot['total'], 2) ?></div>
                                    <div class=""><?php echo mb_strtoupper($tot['nombre']) ?></div>
                                </div>
                            <?php endforeach;?>
                        </div>
                    </div>
                </div>

                
            </div>
        </div>
        <br>
        <div class="tab-content">
            <div class='contenedor-tabla' style="overflow-x: scroll !important;"> 
                <table id="tblVentasCuenta" class="table table-hover">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <?php if (!isset($es_caja)):?>
                                <?php if ($estadoVentas != 3) { ?>
                                    <th></th>
                                <?php } ?>
                            <?php endif;?>
                            <th>#</th>
                            <th>Tipo</th>
                            <th>Serie</th>
                            <th>Cajero</th>
                            <th>Salon</th>
                            <th>Mesa</th>
                            <th>Caja</th>
                            <th>Fecha</th>
                            <th style='text-align: center;'>Inicio</th>
                            <th style='text-align: center;'>Fin</th>
                            <th style='text-align: center;'>Tiempo</th>
                            <th style='text-align: center;'>Medios</th>
                            <th style='text-align: center;'>SUB-T</th>
                            <th style='text-align: center;'>DESC</th>
                            <th style='text-align: center;'>TOTAL</th>
                            <?php foreach($totales as $tot):?>
                            <th style='text-align: center;'><?php echo $tot["nombre"];?></th>
                            <?php endforeach;?>
                            <th style='text-align: center;'>DETRA<br>CCIÓN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $subTotal_f = 0;
                        $descuento_f = 0;
                        $total_f = 0;
                        $efectivo_f = 0;
                        $tarjeta_f = 0;
                        $total_detraccion = 0;
                        
                        
                        $sucursal = UserLogin::get_pkSucursal();
                        $nombresucursal = UserLogin::get_nombreSucursal();
                        $query = "";
                        
                        if($caja === ""){
                            $query = "select p.pkpediido,date(p.fechaCierre) as fecha,p.total, p.descuento, p.pkmesa, p.total_tarjeta, p.total_efectivo, p.nombreTarjeta, t.nombres, t.apellidos, ac.caja, m.nmesa, s.nombre, p.fechaApertura, p.fechaFin from pedido p, accion_caja ac, trabajador t, mesas m, salon s where p.idUser = t.pkTrabajador AND ac.pk_accion = p.pkPediido AND ac.tipo_accion = 'PED' AND p.estado=$estadoVentas AND p.pkMesa = m.pkMesa AND m.pkSalon = s.pkSalon and date(p.fechaCierre) between '$fechaInicio' and '$fechaFin' $query_por_comprobante order by p.pkpediido ASC";
                        }else{
                            $query = "select p.pkpediido,date(p.fechaCierre) as fecha,p.total, p.descuento, p.pkmesa, p.total_tarjeta, p.total_efectivo, p.nombreTarjeta, t.nombres, t.apellidos, ac.caja, m.nmesa, s.nombre, p.fechaApertura, p.fechaFin from pedido p, accion_caja ac, trabajador t, mesas m, salon s where p.idUser = t.pkTrabajador AND p.estado=$estadoVentas and date(p.fechaCierre) between '$fechaInicio' and '$fechaFin' AND ac.pk_accion = p.pkPediido AND ac.tipo_accion = 'PED' AND ac.caja = '".$caja."' AND p.pkMesa = m.pkMesa AND m.pkSalon = s.pkSalon $query_por_comprobante  order by p.pkpediido ASC"; 
                        }

                        $result = $db->executeQuery($query);
                        while ($row = $db->fecth_array($result)) {         
                            //Mostramos la tabla
                            echo "<tr>";
                            //Opciones para eliminar o reimprimir
                            echo "<td style='text-align: center'>";
                            echo "<a onclick='verDetallesVentas(\"" . $row['pkpediido'] . "\"," . $estadoVentas . ",\"" . $fechaInicio . "\",\"" . $fechaFin . "\",\"" . $sucursal . "\")' title='Ver Detalle'><span class='glyphicon glyphicon-log-out'></span></a>";

                            echo "</td>";

                            //Obtenemos data Comprobante
                            $query_comprobante = "Select c.* from detallecomprobante dc, comprobante c where dc.pkComprobante = c.pkComprobante AND dc.pkPediido = '".$row['pkpediido']."' AND c.estado = 0";
                            $result_c = $db->executeQuery($query_comprobante);
                            $existe_comprobante = 0;
                            while ($row_c = $db->fecth_array($result_c)) {
                                $tipo_impresion = "";
                                $existe_comprobante = 1;
                                $serie = "";

                                if(intval($row_c["pkTipoComprobante"]) === 1){
                                    $query_serie = "Select * from cloud_config where parametro = 'sboleta'";
                                    $result_serie = $db->executeQuery($query_serie);
                                    while ($row_s = $db->fecth_array($result_serie)) {
                                        $serie = $row_s["valor"];
                                    }
                                    $tipo_impresion = "BOLETA";
                                }else{
                                    $tipo_impresion = "FACTURA";
                                    $query_serie = "Select * from cloud_config where parametro = 'sfactura'";
                                    $result_serie = $db->executeQuery($query_serie);
                                    while ($row_s = $db->fecth_array($result_serie)) {
                                        $serie = $row_s["valor"];
                                    }
                                }
                                
                                echo "<td style='text-align: center'>";
                                echo "<a href='#' onclick='openImprimirCuenta(\"" . $row_c["pkComprobante"]. "\",\"" . $tipo_impresion. "\")' title='Re Imprimir'><span class='glyphicon glyphicon-print'></span></a>";
                                echo "</td>";

                                /*$query_pr = "Insert into cola_impresion values(NULL,'".$row_c["pkComprobante"]."','CUE-".$tipo_impresion."','01','1,1',0)";
                                $db->executeQuery($query_pr);*/
                                
                                if (!isset($es_caja)){
                                    if ($estadoVentas != 3) {
                                        echo "<td style='text-align: center'>";
                                        if ($fechaHoy == $row['fecha']) {
                                            echo "<a onclick='EliminaVentaDia(\"" . $row['pkpediido'] . "\")' title='Anular Venta'><span class='glyphicon glyphicon-remove'></span></a>";
                                        } else {
                                            echo "<a style='color: #8699a4' title='Las ventas de un dia cerrado ya no se pueden anular'><span class='glyphicon glyphicon-remove'></span></a>";
                                        }
                                        echo "</td>";
                                    }
                                }

                                echo "<td style='text-align: center'>";
                                echo $row['pkpediido'];
                                echo "</td>";

                                echo "<td style='text-align: center; color: green; font-weight: bold;'>";
                                echo $tipo_impresion;
                                $query_credito = "Select * from creditos where id_pedido = '".$row['pkpediido']."'";
                                $result_cre = $db->executeQuery($query_credito);
                                if($row_cre = $db->fecth_array($result_cre)) {
                                    echo "<br/>(DESDE CREDITO ".$row_cre["fecha_credito"].")";
                                }

                                echo "</td>";

                                echo "<td style='text-align: center'>";
                                echo $serie."-".str_pad($row_c["ncomprobante"], 6, "0", STR_PAD_LEFT);
                                echo "</td>";
                            }

                            if($existe_comprobante === 0){

                                echo "<td style='text-align: center'>";
                                echo "<a href='#' onclick='openImprimirCuenta(\"" . $row['pkpediido']. "\",\"CUENTA\")' title='Re Imprimir'><span class='glyphicon glyphicon-print'></span></a>";
                                echo "</td>";

                                /*$query_pr = "Insert into cola_impresion values(NULL,'".$row['pkpediido']."','CUE-CUENTA','01','1,1',0)";
                                $db->executeQuery($query_pr);*/
                                
                                if (!isset($es_caja)){
                                    if ($estadoVentas != 3) {
                                        echo "<td style='text-align: center'>";
                                        if ($fechaHoy == $row['fecha']) {
                                            echo "<a onclick='EliminaVentaDia(\"" . $row['pkpediido'] . "\")' title='Anular Venta'><span class='glyphicon glyphicon-remove'></span></a>";
                                        } else {
                                            echo "<a style='color: #8699a4' title='Las ventas de un dia cerrado ya no se pueden anular'><span class='glyphicon glyphicon-remove'></span></a>";
                                        }
                                        echo "</td>";
                                    }
                                }

                                echo "<td style='text-align: center'>";
                                echo $row['pkpediido'];
                                echo "</td>";
                                echo "<td style='text-align: center'>";
                                echo "<a href='#' onclick='menu_canjear(".$row['pkpediido'].",".$row['total'].")'>TICKET</a>";
                                $query_credito = "Select * from creditos where id_pedido = '".$row['pkpediido']."'";
                                $result_cre = $db->executeQuery($query_credito);
                                if($row_cre = $db->fecth_array($result_cre)) {
                                    echo "<br/>(DESDE CREDITO ".$row_cre["fecha_credito"].")";
                                }
                                echo "</td>";
                                echo "<td style='text-align: center'>";
                                echo $row['pkpediido'];
                                echo "</td>";
                            }

                            echo "<td>";
                            echo $row['nombres']." ".$row["apellidos"];
                            echo "</td>";
                            echo "<td style='text-align: center'>";
                            echo $row['nombre'];
                            echo "</td>";
                            echo "<td style='text-align: center'>";
                            echo $row['nmesa'];
                            echo "</td>";
                            echo "<td style='text-align: center'>";
                            echo $row['caja'];
                            echo "</td>";
                            echo "<td>";
                            echo $row['fecha'];
                            echo "</td>";
                            echo "<td>";
                            echo date("h:i A", strtotime($row['fechaApertura']));
                            echo "</td>";
                            echo "<td>";
                            echo date("h:i A", strtotime($row['fechaFin']));
                            echo "</td>";
                            echo "<td>";
                            $apertura = new DateTime($row['fechaApertura']);
                            $cierre = new DateTime($row['fechaFin']);

                            $tiempo = $apertura->diff($cierre);

                            echo $tiempo->format('%H:%I:%S');
                            //echo $row['fecha'];
                            echo "</td>";
                            echo "<td style='text-align: center'>";
                            echo $row['nombreTarjeta'];
                            echo "</td>";
                            echo "<td style='text-align: center'>";
                            $subTotal_f = $subTotal_f + $row['total'] + $row['descuento'];
                            echo number_format(floatval($row['total'] + $row['descuento']), 2, '.', ' ');
                            echo "</td>";

                            echo "<td style='text-align: center'>";
                            $descuento_f = $descuento_f + $row['descuento'];
                            echo number_format(floatval($row['descuento']), 2, '.', ' ');
                            echo "</td>";
                            echo "<td style='text-align: center'>";
                            $total_f = $total_f + $row['total'];
                            echo number_format(floatval($row['total']), 2, '.', ' ');
                            echo "</td>";

                            foreach($totales as $tot){
                                $query_parcial = "Select sum(monto) as parcial from movimiento_dinero where id_origen = '".$row['pkpediido']."' AND tipo_origen = 'PED' AND estado = 1 AND id_medio = '".$tot["id_medio"]."' AND moneda = '".$tot["id_moneda"]."'";
                                $r_parcial = $db->executeQuery($query_parcial);
                                if($row_p = $db->fecth_array($r_parcial)){
                                    echo "<td style='text-align: center'>";
                                    echo number_format(floatval($row_p['parcial']), 2, '.', ' ');
                                    echo "</td>";
                                }
                            }

                            $query_detra = "select * from pedido_detraccion where pedido_id = ${row['pkpediido']}";
                            $res_detra = $db->executeQuery($query_detra);
                            if($row_detra = $db->fecth_array($res_detra)){
                                echo "<td style='text-align: center'>";
                                echo number_format(floatval($row_detra['total']), 2, '.', ' ');
                                echo "</td>";
                                $total_detraccion += floatval($row_detra['total']);
                            } else {
                                echo "<td></td>";
                            }

                            echo "</tr>";
                        }
                        ?>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style='text-align: center'><?php
                            echo number_format($subTotal_f, 2, '.', '');
                            ?></td>
                            <td style='text-align: center'><?php
                            echo number_format($descuento_f, 2, '.', '');
                            ?></td>
                            <td style='text-align: center'><?php
                            echo number_format($total_f, 2, '.', '');
                            ?></td>
                            <?php foreach($totales as $tot):?>
                                <td style='text-align: center'><?php
                                echo number_format($tot["total"], 2, '.', '');
                                ?></td>
                            <?php endforeach;?>
                            <td style='text-align: center'><?php
                            echo number_format($total_detraccion, 2, '.', '');
                            ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<!--modal para canjear ticket por comprobante-->
<div id="modal_comprobante" class="modal fade bs-example-modal-lg in" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">Generado Comprobante</h4>
      </div>
      <div class="modal-body labelTotal">
        <form id="frm_comprobante">
          <table class="table table-bordered labelTotal">
            <tbody><tr>
              <td>Total</td>
              <td><label class="control-label reset" id="lbl_total_comprobante">0.00</label></td>
            </tr>
            <tr>
              <td>Tipo Comprobante</td>
              <td>
                <input type="radio" name="tipo_comprobante" value="1" checked="true">Boleta<br/>
                <input type="radio" name="tipo_comprobante" value="2">Factura
              </td>
            </tr>
            <tr>
              <td>Cliente</td>
              <td>
                <div class="input-group">
                <input id="documento" maxlength="11" name="documento" class="form-control" placeholder="Ingrese Su DNI/RUC - Presione 'Enter para iniciar la busqueda'">
                <span class="input-group-addon" onclick='window.open("http://www.sunat.gob.pe/cl-ti-itmrconsruc/FrameCriterioBusquedaMovil.jsp","Consulta Sunat","width=600,height=600,top=10,left=20,resizable=no,scrollbars=yes,menubar=no,toolbar=no,status=no,location=no")'><img src="<?php echo '//' . $_SERVER['HTTP_HOST'] . '/usqay' ?>/Public/images/sunat.png" style="height: 20px; width: 20px;"></span>
                </div>
              </td>
            </tr>
            <tr>
              <td></td>
              <td><input class="form-control" name="cliente" id="cliente" placeholder="Ingrese su Nombre/Razon Social"></td>
            </tr>
            <tr>
              <td></td>
              <td><input class="form-control" name="direccion" id="direccion" placeholder="Ingrese su Dirección"></td>
            </tr>
            <tr>
              <td></td>
                <td><input id="correo" name="correo" class="form-control" placeholder="Ingrese Su Correo Electronico"></td>
            </tr>
          </tbody></table>
        </form>
        <div class="alert alert-danger alert-dismissable" style="display:none;" id="error_comprobante">
          Hubo un error, reintenta
        </div>
        <div class="alert alert-success alert-dismissable" style="display:none;" id="exito_comprobante">
          Conectando a SUNAT, espere porfavor ...
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-default btn-lg" onclick="$('#modal_comprobante').modal('hide');$('#cliente').val('');
        $('#direccion').val('');$('#correo').val('');$('#documento').val('')">Cancelar</button>
        <button class="btn btn-danger btn-lg" onclick="genera_comprobante_diferido()">Generar</button>
      </div>
    </div>
  </div>
</div>
<!--Modal para bloquear input-->
<div class='modal fade' id='modal_envio_anim' tabindex='-1' role='dialog' data-keyboard="false" data-backdrop="static" style="z-index: 999999 !important;">
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title' id='myModalLabel'>Procesando</h4>
            </div>
            <div class='modal-body'>
                <center>
                    <img src="Public/images/pacman.gif">
                </center>
            </div>
        </div>
    </div>
</div>
</body>
<script src="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.js"></script>
<script src="reportes/recursos/js/plugins/tablas/dataTables.buttons.min.js"></script>
<script src="reportes/recursos/js/plugins/tablas/jszip.min.js"></script>
<script src="reportes/recursos/js/plugins/tablas/pdfmake.min.js"></script>
<script src="reportes/recursos/js/plugins/tablas/vfs_fonts.js"></script>
<script src="reportes/recursos/js/plugins/tablas/buttons.html5.min.js"></script>
<script src="reportes/recursos/js/plugins/tablas/buttons.print.min.js"></script>
<script type="text/javascript" src="Application/Views/Reportes/js/VentasConsumo.js.php"></script>
<script>
    var pk_comprobante_diferido = 0;
    $('#documento').keypress(function(event) {
      var keycode = (event.keyCode ? event.keyCode : event.which);
      if (keycode == '13') {
        $('#modal_envio_anim').modal('show');
        $("#cliente").val("");
        $("#direccion").val("");
        $("#correo").val("");

        var param = {'document': $('#documento').val()};

        if ($('#documento').val().length == 8) {
            $.ajax({
                url: "<?php echo '//' . $_SERVER['HTTP_HOST'] . '/usqay' ?>/?controller=Cliente&&action=ClienteDni",
                type: 'POST',
                data: param,
                cache: false,
                dataType: 'json',
                success: function(data1) {
                    var hay = 0;
                    $.each( data1, function( key1, value1 ) {
                        $("#cliente").val(value1.nombres);
                        $("#direccion").val(value1.direccion);
                        $("#correo").val(value1.email);
                        hay = 1;
                        $('#modal_envio_anim').modal('hide');
                        return false;
                    });

                }
            });
        } else {
            $.ajax({
                url: "<?php echo '//' . $_SERVER['HTTP_HOST'] . '/usqay' ?>/?controller=Cliente&&action=ClienteRuc",
                type: 'POST',
                data: param,
                cache: false,
                dataType: 'json',
                success: function(data0) {
                    $('#modal_envio_anim').modal('hide');
                    $.each( data0, function( key0, value0 ) {
                        $("#cliente").val(value0.companyName);
                        $("#direccion").val(value0.address);
                        $("#correo").val(value0.email);
                        return false;
                    });
                }
            });
        }
      }
    });

    $('#tblVentasCuenta').DataTable( {
        dom: 'Blfrtip',
        "bSort": false,
        "bFilter": true,
        "bInfo": true,
        "ordering": false,
        "paging": true,
        buttons: [
            {
                extend: 'excelHtml5',
                exportOptions: {
                    columns: [3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17
                    <?php 
                    $i = 1;
                    foreach($totales as $tot):?>
                            <?php echo ",".(17+$i);
                            $i = $i+1;
                            ?>
                    <?php endforeach;?>
                    ]
                },
                title: '<?php echo $titulo_importante;?>'
            },
            {
                extend: 'pdfHtml5',
                orientation: 'landscape',
                alignment: 'center',
                pageSize: 'LEGAL',
                customize: function(doc) {
                    doc.defaultStyle.alignment = 'center';
                    var objLayout = {};
                    objLayout['hLineWidth'] = function(i) { return .5; };
                    objLayout['vLineWidth'] = function(i) { return .5; };
                    objLayout['hLineColor'] = function(i) { return '#aaa'; };
                    objLayout['vLineColor'] = function(i) { return '#aaa'; };
                    objLayout['paddingLeft'] = function(i) { return 4; };
                    objLayout['paddingRight'] = function(i) { return 4; };
                    doc.content[1].layout = objLayout;
                },
                exportOptions: {
                    columns: [3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17
                    <?php 
                    $i = 1;
                    foreach($totales as $tot):?>
                            <?php echo ",".(17+$i);
                            $i = $i+1;
                            ?>
                    <?php endforeach;?>
                    ]
                },
                title: '<?php echo $titulo_importante;?>'
            },
            {
                extend: 'print',
                orientation: 'landscape',
                pageSize: 'LEGAL',
                exportOptions: {
                    columns: [3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17
                    <?php 
                    $i = 1;
                    foreach($totales as $tot):?>
                            <?php echo ",".(17+$i);
                            $i = $i+1;
                            ?>
                    <?php endforeach;?>
                    ]
                },
                title: '<?php echo $titulo_importante;?>'
            }
            
        ]
    } );

    date('date');

    function menu_canjear(pk_comprobante,precio){
        pk_comprobante_diferido = pk_comprobante;
        $("#lbl_total_comprobante").html(parseFloat(precio).toFixed(2));
        $('#modal_comprobante').modal('show');
    }

    function genera_comprobante_diferido(){
        
        //Tipo comprobante
        var tipo_comprobante = $("input[name='tipo_comprobante']:checked").val();
        //Primero verificamos para no cagarla
        //Datos Cliente
        var documento = $("#documento").val();
        var cliente = $("#cliente").val();
        var direccion = $("#direccion").val();
        var correo = $("#correo").val();
        //Variable centinella
        var pasa = 0;
        var mensaje = "";
        //Verificamos
        if(parseInt(tipo_comprobante) === 1){
            if(parseFloat($("#lbl_total_comprobante").html()) > 700){
                if(documento !== "" && cliente !== "" && direccion !== ""){
                    if(documento.length === 8){
                        pasa = 1;
                    }else{
                        mensaje = "El DNI debe ser de 8 digitos";
                    }
                }else{
                    mensaje = "Para montos mayores a 700 soles la boleta debe llevar datos";
                }
            }else{
                if(documento.length > 1){
                    if(documento.length === 8){
                        pasa = 1;
                    }else{
                        mensaje = "El DNI debe ser de 8 digitos";
                        pasa = 0;
                    }
                }else{
                    pasa = 1;
                }
            }
        }
        
        if(parseInt(tipo_comprobante) === 2){
            if(documento !== "" && cliente !== "" && direccion !== ""){
                if(documento.length === 11){
                    pasa = 1;
                }else{
                    mensaje = "El RUC debe ser de 11 digitos";
                }
            }else{
                mensaje = "Es obligatorio poner los datos del cliente en una factura";
            }
        }

        if(pasa === 1){
            $('#modal_envio_anim').modal('show');
            var param = {'pkPediido': pk_comprobante_diferido, tipo_comprobante: tipo_comprobante, documento: documento, cliente: cliente, direccion: direccion, correo: correo};
            $.ajax({
              url: "<?php echo Class_config::get('urlApp') ?>/?controller=Comprobante&&action=Diferido",
              type: 'POST',
              data: param,
              success: function(data1) {
                var json = JSON.parse(data1);
                if(json.exito == 1){
                    var tipo_impresion = "BOLETA";
                    if(parseInt(tipo_comprobante) === 2){
                        tipo_impresion = "FACTURA";
                    }
                    //Finalmente ponemos en cola la impresion
                    var param2 = {'pkPedido': json.id_comprobante, 'terminal': '<?php echo $_COOKIE['t'] ?>', 'tipo': tipo_impresion, 'aux': '<?php echo UserLogin::get_id();?>,1'};
                    $.ajax({
                        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ImprimeCuenta",
                            type: 'POST',
                            data: param2,
                            success: function() {
                               window.location.reload();
                            }
                    });
                }else{
                    alert(json.mensaje);
                    $('#modal_envio_anim').modal('hide');
                }
              }
            });
        }else{
            alert("Error: "+mensaje);
        }
    }

    
    function openImprimirCuenta(idp,tipo) {
        var param = {'pkPedido': idp, 'terminal': '<?php echo $_COOKIE["t"]?>','tipo': tipo, 'aux': '<?php echo $_SESSION['id'];?>,1'};
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ImprimeCuenta",
                type: 'POST',
                data: param,
                cache: false,
                dataType: 'json',
                success: function() {
                    ///Nada
                }
        });
    }
</script>