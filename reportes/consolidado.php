<?php

$titulo_pagina = 'Consolidado de Ventas';

if (isset($_GET["tpf"])) {
    switch ($_GET["tpf"]) {
        case "d":
            if ($_GET["ff"] !== "") {
                $titulo_pagina .= " del " . $_GET["fi"] . " al " . $_GET["ff"];
            } else {
                $titulo_pagina .= " " . $_GET["fi"];
            }
            break;

        case "m":
            $titulo_pagina .= "de " . $_GET["mes"] . " del " . $_GET["ano"];
            break;

        case "a":
            $titulo_pagina .= " del " . $_GET["ano"];
            break;
    }
}

//Caja
$caja = "";
if (isset($_REQUEST['caja'])){
    $caja = $_REQUEST["caja"];
}

//Nivel de Usuario
$estilo_nivel = "style='display:none;'";
if(intval($_COOKIE["TYP"]) > 0 &&  intval($_COOKIE["TYP"]) < 3){
    $estilo_nivel = "";
}

$queryTotales = "";

$titulo_sistema = 'usqay2';
require_once('recursos/componentes/header.php'); 
include_once('recursos/componentes/MasterConexion.php');
include_once('../Components/Library/Framework/SuperDataBase.php');
$conn = new MasterConexion();
$sucursal = $_GET["s"];
$datos_sucursal = $conn->consulta_arreglo("Select * from sucursal where pkSucursal = '" . $sucursal . "'");
?>
<link rel="stylesheet" href="recursos/js/plugins/tablas/jquery.dataTables.min.css">
<h1>Consolidado de Ventas</h1>
<input type='hidden' id='idtitulo' name='idtitulo' value='<?php echo $titulo_pagina; ?>'/>
<input type='hidden' id='id_sucursal' name='id_sucursal' value='<?php echo $sucursal; ?>'/>
<input type='hidden' id='mensaje' name='mensaje' value='<?php
echo str_replace(
        array("<br>", "<br/>", "<p>", "</p>"), '', $datos_sucursal["nombreSucursal"]
);
?>'/>
<div class="panel panel-primary" id='pfecha'>
    <div class="panel-heading">
        <h3 class="panel-title">Filtros por fechas</h3>
    </div>
    <div class="panel-body">
        <div class='control-group'>
            <label>Tipo</label>
            <select class="form-control" id="tipo_busqueda_fecha">
                <option value="d">Día</option>
                <option value="m">Mes</option>
                <option value="a">Año</option>
            </select>
        </div>

        <div class='control-group' id="dinicio">
            <label>Fecha Inicio</label>
            <input class='form-control' placeholder='AAAA-MM-DD' id='fecha_inicio' name='fecha_inicio' value="<?php if (!isset($_GET["fi"])) { echo date("Y-m-d");}?>"/>
        </div>

        <div class='control-group' id="dfin" style="display: none;">
            <label>Fecha Fin</label>
            <input class='form-control' placeholder='AAAA-MM-DD' id='fecha_fin' name='fecha_fin' />
        </div>

        <div class='control-group' id="mes" style="display: none;">
            <label>Mes</label>
            <select class="form-control" id="mes_busqueda">
                <option value="1">Enero</option>
                <option value="2">Febrero</option>
                <option value="3">Marzo</option>
                <option value="4">Abril</option>
                <option value="5">Mayo</option>
                <option value="6">Junio</option>
                <option value="7">Julio</option>
                <option value="8">Agosto</option>
                <option value="9">Septiembre</option>
                <option value="10">Octubre</option>
                <option value="11">Noviembre</option>
                <option value="12">Diciembre</option>
            </select>
        </div>

        <div class='control-group' id="ano" style="display: none;">
            <label>Año</label>
            <input class='form-control' placeholder='AAAA' id='ano_busqueda' name='ano_busqueda' value="<?php echo date("Y");?>" type="number"/>
        </div>
        
        <div class='control-group' <?php echo $estilo_nivel;?>>
            <label>Caja</label>
            <select name="caja" class="form-control" id="caja">
            <?php if($caja === ""):?>
                <option value='' selected>Todas</option>
            <?php else:?>
                <option value=''>Todas</option>
            <?php endif;?>         
            <?php
            $rc = $conn->consulta_matriz("Select * from cajas");
            if(is_array($rc)){
                foreach($rc as $cajas){
                    if($cajas["caja"] === $caja){
                        echo "<option value='".$cajas["caja"]."' selected>".$cajas["caja"]."</option>";
                    }else{
                        echo "<option value='".$cajas["caja"]."'>".$cajas["caja"]."</option>";
                    }
                }
            }
            ?>
        </select>
        </div>

        <!-- <div class='control-group' id="corte">
            <label>Corte</label>
            <select class="form-control" id="corte_busqueda">
            </select>
        </div> -->
    </div>
</div>

<div class="panel panel-primary" id='pdatos'>
    <div class="panel-heading">
        <h3 class="panel-title">Filtros por Tipos</h3>
    </div>
    <div class="panel-body">
        <div class='control-group'>
            <label>Tipo</label>    
            <select class="form-control" id="tipo_plato" multiple="yes">
                <?php
                $tipos = $conn->consulta_matriz("Select * from tipos where estado = 0");
                foreach ($tipos as $pl):
                    ?>
                    <option value="<?php echo $pl["pkTipo"]; ?>"><?php echo $pl["descripcion"]; ?></option>
                    <?php
                endforeach;
                ?>
            </select>
        </div>
    </div>
</div>

<div class='control-group'>
    <p></p>
    <button type='button' class='btn btn-primary' onclick='filtrar()'>Buscar</button>
    <br/>
</div>
</form>

<hr/>
<?php
//filtro fecha
$hubo_actividad = true;
$ffecha = "";
if (isset($_GET["tpf"])) {
    switch ($_GET["tpf"]) {
        case "d":
            $dcI = $conn->consulta_arreglo("Select * from corte where fecha_cierre = '".$_GET["fi"]."'  order by inicio asc limit 1");
//echo"Select * from corte where fecha_cierre = '".$_GET["fi"]."'  order by inicio asc limit 1" ;
                if (!is_array($dcI)) {
                    
                    $hubo_actividad = false;
                    echo "<h4 class='text-center text-danger'>No se encontró actividad para el día ".$_GET["fi"]."</h4>";

                    $ffecha = " AND p.dateModify >= '" .$_GET["fi"]."'";
                    break;
                }

            // if ($_GET["fi"] == $_GET["ff"]) {
            //     if ($dcI['fin']) {
            //         $ffecha = " AND dp.horaPedido BETWEEN '" .$dcI['inicio'] . "' AND '" . $dcI['fin'] . "'";
            //     } else {
            //         $ffecha = " AND dp.horaPedido >= '" .$dcI['inicio']."'";
            //     }
            // } else {
                if (!$_GET['ff']) {
                    $_GET['ff'] = $_GET['fi'];
                }

                $dcF = $conn->consulta_arreglo("Select * from corte where fecha_cierre = '".$_GET["ff"]."' order by inicio desc limit 1");

                if ($dcF['fin']) {
                    $ffecha = " AND p.dateModify BETWEEN '" .$dcI['inicio'] . "' AND '" . $dcF['fin'] . "'";
                } else {
                    $ffecha = " AND p.dateModify >= '" .$dcI['inicio']."'";
                }
            // }

            // if (($_GET["fi"] != $_GET["ff"]) && ($_GET["ff"] != "")) {
            //     $dcF = $conn->consulta_arreglo("Select * from corte where fecha_cierre = '".$_GET["ff"]."'");

            //     $ffecha = " AND dp.horaPedido BETWEEN '" .$dcI['inicio'] . "' AND '" . $_GET["ff"] . "'";
            // } else {
            //     if($_GET["cc"] !== "ALL"){
            //         //Obtenemos data corte                  
            //         $dc = $conn->consulta_arreglo("Select * from corte where inicio = '".$_GET["cc"]."'");
            //         if($dc["fin"] !== ""){
            //             $ffecha = " AND p.fechaCierre = '" . $_GET["fi"] . "' AND p.dateModify BETWEEN '".$dc["inicio"]."' AND '".$dc["fin"]."'";
            //         }else{
            //             $ffecha = " AND p.fechaCierre = '" . $_GET["fi"] . "' AND p.dateModify >= '".$dc["inicio"]."'";
            //         }                    
            //     }else{
            //         $ffecha = " AND p.fechaCierre = '" . $_GET["fi"] . "' ";
            //     }                
            // }
            break;

        case "m":
            $ffecha = " AND MONTH(p.fechaCierre) = '" . $_GET["mes"] . "' AND YEAR(p.fechaCierre) = '" . $_GET["ano"] . "'";
            break;

        case "a":
            $ffecha = " AND YEAR(p.fechaCierre) = '" . $_GET["ano"] . "'";
            break;
    }
}
?>
<div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' border="0">
        <thead>
            <tr>
                <th style="text-align:center;">DESCRIPCION</th>
                <th style="text-align:center;">COSTO UNIDAD</th>
                <th style="text-align:center;">Cantidad Consumo</th>
                <th style="text-align:center;">Cantidad Credito</th>
                <th style="text-align:center;">Cantidad Venta</th>
                <th style="text-align:center;">Cantidad Venta Por Cobrar</th>
                <th style="text-align:center;">Soles Consumo</th>
                <th style="text-align:center;">Soles Credito</th>
                <th style="text-align:center;">Soles Venta</th>
                <th style="text-align:center;">Soles Venta Por Cobrar</th>
                <th style="text-align:center;">TOTAL CANTIDADES</th>
                <th style="text-align:center;">TOTAL SOLES</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $s1 = 0;
            $s2 = 0;
            $s3 = 0;
            $s4 = 0;
            $s5 = 0;
            $s6 = 0;
            $s7 = 0;
            $s8 = 0;
            $s9 = 0;
            $s10 = 0;

            if(isset($_GET["tp"])){
            $query_tipo = " ";
            
            if(intval($_GET["tp"])> 0){
                $query_tipo = " AND pl.pktipo in (";
                $query_tipo .= $_GET["tp"];
                $query_tipo .= ") ";
            }
            
            $query = "SELECT
                dp.pkPlato,
                pl.descripcion,
                pl.precio_venta,
                sum(IF(p.estado = 5, cantidad, 0)) AS consumo,
                sum(IF(p.estado = 5, cantidad * dp.precio, 0)) AS m_consumo,
                sum(IF(p.estado = 1, cantidad, 0)) AS venta,
                sum(IF(p.estado = 1, cantidad * dp.precio, 0)) AS m_venta,
                sum(IF(p.estado = 4, cantidad, 0)) AS credito,
                sum(IF(p.estado = 4, cantidad * dp.precio, 0)) AS m_credito,
                sum(IF(p.estado = 0, cantidad, 0)) AS por_cobrar,
                sum(IF(p.estado = 0, cantidad * dp.precio, 0)) AS m_por_cobrar
            FROM
                detallepedido dp,
                pedido p,
                plato pl ";

            if ($caja !== "") {
                $query .= ",  accion_caja ac";
            }

            $query .= " WHERE
                dp.pkPediido = p.pkPediido
                AND pl.pkPlato = dp.pkPlato";
            
            if ($caja !== "") {
                $query .= "  AND ac.pk_accion = dp.pkDetallePedido";
            }

            $query .= "
                AND dp.estado > 0
                AND dp.estado < 3
                $query_tipo
                $ffecha ";

            if ($caja !== "") {
                $query .= "  AND ac.tipo_accion = 'DET' AND ac.caja = '".$caja."'";
            }

            $query .= " GROUP BY
                dp.pkPlato";

            // echo $query;

            $res = $conn->consulta_matriz($query);

            if (is_array($res) && $hubo_actividad) {
                foreach ($res as $pl) {

                    $suma_cantidades = 0;
                    $suma_montos = 0;

                    $c_consumo = 0;
                    $c_credito = 0;
                    $c_venta = 0;
                    $c_venta_cobrar = 0;

                    $s_consumo = 0;
                    $s_credito = 0;
                    $s_venta_cobrar = 0;

                    $c_consumo = intval($pl["consumo"]);
                    $suma_cantidades += $c_consumo;
                    $s1 += intval($pl["consumo"]);

                    $c_credito = intval($pl["credito"]);
                    $suma_cantidades += $c_credito;
                    $s2 += intval($pl["credito"]);

                    $c_venta = intval($pl["venta"]);
                    $suma_cantidades += $c_venta;
                    $s3 += intval($pl["venta"]);

                    $c_venta_cobrar = intval($pl["por_cobrar"]);
                    $suma_cantidades += $c_venta_cobrar;
                    $s9 += intval($pl["por_cobrar"]);

                    $s_consumo = floatval($pl["m_consumo"]);
                    $suma_montos += $s_consumo;
                    $s4 += floatval($pl["m_consumo"]);

                    $s_credito = floatval($pl["m_credito"]);
                    $suma_montos += $s_credito;
                    $s5 += floatval($pl["m_credito"]);

                    $s_venta = floatval($pl["m_venta"]);
                    $suma_montos += $s_venta;
                    $s6 += floatval($pl["m_venta"]);

                    $s_venta_cobrar = floatval($pl["m_por_cobrar"]);
                    $suma_montos += $s_venta_cobrar;
                    $s10 += floatval($pl["m_por_cobrar"]);

                    if($suma_cantidades > 0){
                    ?>
                        <tr>
                            <td><?php echo utf8_encode($pl["descripcion"]); ?></td>
                            <td><center><?php echo $pl["precio_venta"]; ?></center></td>
                            <td style="text-align: center;"><?php echo $c_consumo; ?></td>
                            <td style="text-align: center;"><?php echo $c_credito; ?></td>
                            <td style="text-align: center;"><?php echo $c_venta; ?></td>
                            <td style="text-align: center;"><?php echo $c_venta_cobrar; ?></td>
                            <td style="text-align: right;"><?php echo number_format($s_consumo, 2, '.', ' '); ?></td>
                            <td style="text-align: right;"><?php echo number_format($s_credito, 2, '.', ' '); ?></td>
                            <td style="text-align: right;"><?php echo number_format($s_venta, 2, '.', ' '); ?></td>
                            <td style="text-align: right;"><?php echo number_format($s_venta_cobrar, 2, '.', ' '); ?></td>
                            <td style="text-align: center;"><?php echo intval($suma_cantidades);
                            $s7 += $suma_cantidades; ?></td>
                            <td style="text-align: right;"><?php echo number_format($suma_montos, 2, '.', ' ');
                            $s8 += $suma_montos; ?></td>
                        </tr>
                    <?php
                    }
                }
            }   

            if ($hubo_actividad) {
                $descuentoConsumo = 0.00;
                $descuentoCredito = 0.00;
                $descuentoVenta = 0.00;
                
                $queryConsumo = "";
                
                if($caja === ""){
                   $queryConsumo = $conn->consulta_arreglo("SELECT sum(descuento) as descuento FROM pedido p WHERE descuento>0 and p.estado=5 " . $ffecha ); 
                }else{
                    $queryConsumo = $conn->consulta_arreglo("SELECT sum(p.descuento) as descuento FROM pedido p, accion_caja ac WHERE p.descuento>0 and p.estado=5 ".$ffecha." AND ac.pk_accion = p.pkPediido AND ac.tipo_accion = 'PED' AND ac.caja = '".$caja."'");
                }
                
                $descuentoConsumo= number_format(floatval($queryConsumo["descuento"]), 2, '.', ' ');
                
                $queryCredito = "";
                
                if($caja === ""){
                   $queryCredito = $conn->consulta_arreglo("SELECT sum(descuento) as descuento FROM pedido p WHERE descuento>0 and p.estado=4 " . $ffecha ); 
                }else{
                    $queryCredito = $conn->consulta_arreglo("SELECT sum(p.descuento) as descuento FROM pedido p, accion_caja ac WHERE p.descuento>0 and p.estado=4 ".$ffecha." AND ac.pk_accion = p.pkPediido AND ac.tipo_accion = 'PED' AND ac.caja = '".$caja."'");
                }

                $descuentoCredito= number_format(floatval($queryCredito["descuento"]), 2, '.', ' ');
                
                $queryVenta = "";
                
                if($caja === ""){
                   $queryVenta = $conn->consulta_arreglo("SELECT sum(descuento) as descuento FROM pedido p WHERE descuento>0 and p.estado=1 " . $ffecha ); 
                }else{
                    $queryVenta = $conn->consulta_arreglo("SELECT sum(p.descuento) as descuento FROM pedido p, accion_caja ac WHERE p.descuento>0 and p.estado=1 ".$ffecha." AND ac.pk_accion = p.pkPediido AND ac.tipo_accion = 'PED' AND ac.caja = '".$caja."'");
                }

                $descuentoVenta= number_format(floatval($queryVenta["descuento"]), 2, '.', ' ');

                $queryDescuento = "";
                
                if($caja === ""){
                   $queryDescuento = $conn->consulta_arreglo("SELECT sum(descuento) as descuento FROM pedido p WHERE descuento>0 and p.estado=0 " . $ffecha ); 
                }else{
                    $queryDescuento = $conn->consulta_arreglo("SELECT sum(p.descuento) as descuento FROM pedido p, accion_caja ac WHERE p.descuento>0 and p.estado=0 ".$ffecha." AND ac.pk_accion = p.pkPediido AND ac.tipo_accion = 'PED' AND ac.caja = '".$caja."'");
                }
                
                $descuentoVentaCobrar = number_format(floatval(0), 2, '.', ' ');
                
                ?>
                <tr>
                <td style="text-align: center;"><b>SUBTOTALES<b></td>
                    <td style="text-align: center;"><b></b></td>
                    <td style="text-align: center;"><b></b></td>
                    <td style="text-align: center;"><b></b></td>
                    <td style="text-align: center;"><b></b></td>
                    <td style="text-align: center;"><b></b></td>
                    <td style="text-align: right;"><b><?php echo number_format($s4, 2, '.', ' '); ?></b></td><!--s4 s5 s6 s8 con S/.-->
                    <td style="text-align: right;"><b><?php echo number_format($s5, 2, '.', ' '); ?></b></td>
                    <td style="text-align: right;"><b><?php echo number_format($s6, 2, '.', ' '); ?></b></td>
                    <td style="text-align: right;"><b><?php echo number_format($s10, 2, '.', ' '); ?></b></td><!--s4 s5 s6 s8 con S/.-->
                    <td style="text-align: center;"><b></b></td>
                    <td style="text-align: right;"><b><?php echo number_format($s8-$s4, 2, '.', ' '); ?></b></td>
                    </tr>
                <tr>
                <td style="text-align: center;"><b>DESCUENTOS<b></td>
                    <td style="text-align: center;"><b></b></td>
                    <td style="text-align: center;"><b></b></td>
                    <td style="text-align: center;"><b></b></td>
                    <td style="text-align: center;"><b></b></td>
                    <td style="text-align: center;"><b></b></td>
                    <td style="text-align: right;"><b><?php echo number_format(floatval($descuentoConsumo), 2, '.', ' '); ?></b></td><!--s4 s5 s6 s8 con S/.-->
                    <td style="text-align: right;"><b><?php echo number_format(floatval($descuentoCredito), 2, '.', ' '); ?></b></td>
                    <td style="text-align: right;"><b><?php echo number_format(floatval($descuentoVenta), 2, '.', ' '); ?></b></td>
                    <td style="text-align: right;"><b><?php echo number_format(floatval($descuentoVentaCobrar), 2, '.', ' '); ?></b></td>
                    <td style="text-align: center;"><b></b></td>
                    <td style="text-align: right;"><b><?php echo number_format((floatval($descuentoConsumo)+floatval($descuentoCredito)+floatval($descuentoVenta)+floatval($descuentoVentaCobrar)), 2, '.', ' '); ?></b></td>
                </tr>
                <tr>
                <td style="text-align: center;"><b>TOTALES><b></td>
                    <td style="text-align: center;"><b></b></td>
                    <td style="text-align: center;"><b><?php echo $s1; ?></b></td>
                    <td style="text-align: center;"><b><?php echo $s2; ?></b></td>
                    <td style="text-align: center;"><b><?php echo $s3; ?></b></td>
                    <td style="text-align: center;"><b><?php echo $s9; ?></b></td>
                    <td style="text-align: right;"><b><?php echo number_format($s4-$descuentoConsumo, 2, '.', ' '); ?></b></td><!--s4 s5 s6 s8 con S/.-->
                    <td style="text-align: right;"><b><?php echo number_format($s5-$descuentoCredito, 2, '.', ' '); ?></b></td>
                    <td style="text-align: right;"><b><?php echo number_format($s6-$descuentoVenta, 2, '.', ' '); ?></b></td>
                    <td style="text-align: right;"><b><?php echo number_format($s10-$descuentoVentaCobrar, 2, '.', ' '); ?></b></td>
                    <td style="text-align: center;"><b><?php echo $s7; ?></b></td>
                    <td style="text-align: right;"><b><?php echo number_format($s8-(floatval($descuentoConsumo)+floatval($descuentoCredito)+floatval($descuentoVenta)+floatval($descuentoVentaCobrar))-floatval($s4), 2, '.', ' '); ?></b></td>
                </tr>
                <?php 
            }
                }
                $nombre_tabla = 'consolidado';
                require_once('recursos/componentes/footer.php');
                ?>
                <script src="recursos/js/plugins/tablas/jquery.dataTables.min.js"></script>
                <script src="recursos/js/plugins/tablas/dataTables.buttons.min.js"></script>
                <script src="recursos/js/plugins/tablas/jszip.min.js"></script>
                <script src="recursos/js/plugins/tablas/pdfmake.min.js"></script>
                <script src="recursos/js/plugins/tablas/vfs_fonts.js"></script>
                <script src="recursos/js/plugins/tablas/buttons.html5.min.js"></script>
                <script src="recursos/js/plugins/tablas/buttons.print.min.js"></script>
                <script>             
                    $(document).ready(function() {

                    $('#tb').DataTable( {
                          dom: 'Blfrtip',
                          "bSort": false,
                          "bFilter": false,
                          "bInfo": false,
                          "ordering": false,
                          "paging": false,
                          buttons: [
                              {
                                  extend: 'excelHtml5',
                                  title: '<?php echo $titulo_pagina;?>'
                              },
                              {
                                  extend: 'pdfHtml5',
                                  orientation: 'landscape',
                                  alignment: 'center',
                                  pageSize: 'LEGAL',
                                  customize: function(doc) {
                                      doc.content[1].margin = [ 100, 0, 100, 0 ] //left, top, right, bottom
                                  },
                                  title: '<?php echo $titulo_pagina;?>'
                              },
                              {
                                  extend: 'print',
                                  orientation: 'landscape',
                                  pageSize: 'LEGAL',
                                  title: '<?php echo $titulo_pagina;?>'
                              }
                              
                          ]
                      } );
                    

                    <?php
                    if (isset($_GET["tpf"])) {
                        echo '$("#tipo_busqueda_fecha").val(\'' . $_GET["tpf"] . '\');';
                        echo '$("#tipo_plato").val([' . $_GET["tp"] . ']);';
                        echo '$("#caja").val("'.$caja.'");';
                        switch ($_GET["tpf"]) {
                            case "d":
                                echo '$("#fecha_inicio").val(\'' . $_GET["fi"] . '\');
                                                                ';
                                echo '$("#fecha_fin").val(\'' . $_GET["ff"] . '\');
                                                                ';

                                if($_GET["fi"] === $_GET["ff"]){
                                    echo '$("#dinicio").show("fast");
                                        $("#dfin").hide("fast");
                                        $("#corte").show("fast");
                                        $("#mes").hide("fast");
                                        $("#ano").hide("fast");
                                        get_cortes($("#fecha_inicio").val(),"'.$_GET["cc"].'");';
                                }else{
                                    if($_GET["ff"] === ""){
                                        echo '$("#dinicio").show("fast");
                                        $("#dfin").hide("fast");
                                        $("#corte").show("fast");
                                        $("#mes").hide("fast");
                                        $("#ano").hide("fast");
                                        get_cortes($("#fecha_inicio").val(),"'.$_GET["cc"].'");';
                                    }else{
                                        echo '$("#dinicio").show("fast");
                                        $("#dfin").show("fast");
                                        $("#corte").hide("fast");
                                        $("#mes").hide("fast");
                                        $("#ano").hide("fast");';
                                    }                        
                                }
                                
                                break;

                            case "m":
                                echo '$("#mes_busqueda").val(\'' . $_GET["mes"] . '\');';
                                echo '$("#ano_busqueda").val(\'' . $_GET["ano"] . '\');';

                                echo '$("#dinicio").hide("fast");
                                        $("#dfin").hide("fast");
                                        $("#mes").show("fast");
                                        $("#corte").hide("fast");
                                        $("#ano").show("fast");';
                                break;

                            case "a":
                                echo '$("#ano_busqueda").val(\'' . $_GET["ano"] . '\');';
                                echo '$("#dinicio").hide("fast");
                                        $("#dfin").hide("fast");
                                        $("#mes").hide("fast");
                                        $("#corte").hide("fast");
                                        $("#ano").show("fast");';
                                break;
                        }

                    }else{
                        echo 'get_cortes("'.date("Y-m-d").'","INI")';
                    }
                    ?>
                    });
                </script>
                            
                            
                            
                            
                            
                            
                           