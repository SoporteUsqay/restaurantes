<?php 
$anio = date("Y");
$mes = intval(date("m"));

if(isset($_GET["m"])){
    $mes = intval($_GET["m"]);
}

if(isset($_GET["a"])){
    $anio = intval($_GET["a"]);
}

$meses = array("MIAU","ENERO","FEBERERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");

$titulo_importante = "Ventas de ".$meses[$mes]." ".$anio;

include 'Application/Views/template/header.php';
?>
<body>
<link rel="stylesheet" href="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.css">
<style>
    .dt-buttons{
        margin-bottom: 10px !important;
    }
    .usqay-bdy{
        margin-left: 2% !important;
        margin-right: 2% !important;
    }
</style>
    <?php
    include_once('reportes/recursos/componentes/MasterConexion.php');
    $conn = new MasterConexion();

    //Obtenemos moneda nacional
    $query_moneda = "Select * from moneda where id = 1";
    $r_moneda = $conn->consulta_arreglo($query_moneda);

    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showContent();
    ?>    
    <div class="container">
        <input value="<?php echo UserLogin::get_pkSucursal(); ?>" id="lblsucursal" name="lblsucursal" style="display: none;">
        <br>
        <br>
        <br>
        <div class="panel panel-success">
            <div class="panel-body panel-success row">

                <div class="form-group col-md-4">
                    <label for="">Año</label>
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                        <input name="anio" aria-describedby="basic-addon1" class="form-control" id="anio" value="<?php echo $anio ?>">
                    </div>
                </div>

                <div class="form-group col-md-4">
                    <label for="">Mes</label>
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                        <select aria-describedby="basic-addon1" name="mes" class="form-control" id="mes">
                            <option value="1" <?php if($mes === 1){echo "selected";}?>>Enero</option>
                            <option value="2" <?php if($mes === 2){echo "selected";}?>>Febrero</option>
                            <option value="3" <?php if($mes === 3){echo "selected";}?>>Marzo</option>
                            <option value="4" <?php if($mes === 4){echo "selected";}?>>Abril</option>
                            <option value="5" <?php if($mes === 5){echo "selected";}?>>Mayo</option>
                            <option value="6" <?php if($mes === 6){echo "selected";}?>>Junio</option>
                            <option value="7" <?php if($mes === 7){echo "selected";}?>>Julio</option>
                            <option value="8" <?php if($mes === 8){echo "selected";}?>>Agosto</option>
                            <option value="9" <?php if($mes === 9){echo "selected";}?>>Septiembre</option>
                            <option value="10" <?php if($mes === 10){echo "selected";}?>>Octubre</option>
                            <option value="11" <?php if($mes === 11){echo "selected";}?>>Noviembre</option>
                            <option value="12" <?php if($mes === 12){echo "selected";}?>>Diciembre</option>
                        </select>
                    </div>
                </div>

                <div class="form-group col-md-4" style="text-align:center;">
                    <button class="btn btn-primary" onclick="loadTableSale()"><span class="glyphicon glyphicon-search"></span> Buscar</button> 

                    <button class="btn btn-success" onclick="generarGraficaBarras()"><span class="glyphicon glyphicon-export"></span> Generar Grafica</button>
                </div>

            </div>
        </div>

        <div class="row-border">
            <div class="col-lg-12">
                <?php
                    $total_pedidos = 0;
                    $total_descuentos = 0;
                    $total_ventas = 0; 
                    $query_mes = "Select fechaCierre, count(*) as pedidos, sum(total) as vendido, sum(descuento) as descuentos from pedido where estado <> 3 AND month(fechaCierre) = '".$mes."' AND year(fechaCierre) = '".$anio."' group by fechaCierre";
                    $resultado = $conn->consulta_matriz($query_mes);
                ?>
                <div class="tab-content">
                    <div class="tab-pane active" id="pedidos">
                        <table class='tb display' cellspacing='0' width='100%' border="0">
                            <thead>
                                <tr>
                                    <th>Día</th>
                                    <th class="text-right"># Pedidos</th>
                                    <th class="text-right">Descuentos (<?php echo $r_moneda["simbolo"];?>)</th>
                                    <th class="text-right">Total Vendido (<?php echo $r_moneda["simbolo"];?>)</th>
                                </tr>
                            </thead>
                            <tbody >
                            <?php 
                            if(is_array($resultado)){
                                foreach($resultado as $res){
                                    echo "<tr>";
                                    echo "<td>".$res["fechaCierre"]."</td>";
                                    echo "<td class='text-right'>".intval($res["pedidos"])."</td>";
                                    echo "<td class='text-right'>".number_format(floatval($res["descuentos"]),2)."</td>";
                                    echo "<td class='text-right'>".number_format(floatval($res["vendido"]),2)."</td>";
                                    echo "</tr>";

                                    $total_pedidos = $total_pedidos + intval($res["pedidos"]);
                                    $total_descuentos = $total_descuentos + (floatval($res["descuentos"]));
                                    $total_ventas = $total_ventas + (floatval($res["vendido"]));
                                }

                                echo "<tr>";
                                echo "<td></td>";
                                echo "<td class='text-right'>".$total_pedidos."</td>";
                                echo "<td class='text-right'>".number_format($total_descuentos, 2)."</td>";
                                echo "<td class='text-right'>".number_format($total_ventas, 2)."</td>";
                                echo "</tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>

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
<script>
    function loadTableSale() {
        location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Report&action=Tmes&a="+$("#anio").val()+"&m="+$("#mes").val();
    }

    function generarGraficaBarras() {
        var url = "<?php echo Class_config::get('urlApp') ?>/ReportesGraficos/GraficaVentasMes.php?&a="+$("#anio").val()+"&m="+$("#mes").val();
        window.open(url, '_blank');
    }

    $('.tb').DataTable( {
        dom: 'Blfrtip',
        "bSort": false,
        "bFilter": false,
        "bInfo": false,
        "ordering": false,
        "paging": false,
        buttons: [
        {
            extend: 'excelHtml5',
            title: '<?php echo $titulo_importante;?>'
        },
        {
            extend: 'pdfHtml5',
            orientation: 'portrait',
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
            title: '<?php echo $titulo_importante;?>'
        },
        {
            extend: 'print',
            orientation: 'portrait',
            pageSize: 'LEGAL',
            title: '<?php echo $titulo_importante;?>'
        }
        ]
    } );

</script>
