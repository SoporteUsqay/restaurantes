<?php
error_reporting(E_ALL);
$inicio = date("Y-m-d");
$fin = date("Y-m-d");

if(isset($_GET["i"])){
    $inicio = $_GET["i"];
}

if(isset($_GET["f"])){
    $fin = $_GET["f"];
}

$titulo_importante = "Ventas por trabajador del ".$inicio." al ".$fin;

include 'Application/Views/template/header.php';

include_once('reportes/recursos/componentes/MasterConexion.php');
$conn = new MasterConexion();

//Obtenemos moneda nacional
$query_moneda = "Select * from moneda where id = 1";
$r_moneda = $conn->consulta_arreglo($query_moneda);
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
    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showContent();
    ?>    
    <div class="container">
        <br>
        <br>
        <br>

        <div class="panel panel-success">
            <div class="panel-body panel-success row">

            <div class="form-group col-md-3">
                <label for="">Inicio</label>
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                    <input name="inicio" aria-describedby="basic-addon1" class="form-control" id="inicio" value="<?php echo $inicio; ?>">
                </div>
            </div>

            <div class="form-group col-md-3">
                <label for="">Fin</label>
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                    <input name="fin" aria-describedby="basic-addon1" class="form-control" id="fin" value="<?php echo $fin; ?>">
                </div>
            </div>

            <div class="form-group col-md-3">
                <center>
                    <label for=""><br/></label>
                    <button class="btn btn-primary form-control" onclick="filtrar()"><span class="glyphicon glyphicon-search"></span> Buscar</button>
                </center>
            </div>

            <div class="form-group col-md-3">
                <center>
                    <label for=""><br/></label>
                    <button class="btn btn-success form-control" onclick="grafica()"><span class="glyphicon glyphicon-print"></span> Generar Grafica</button>
                </center>
            </div>   

            </div>
        </div>   

        <!--FIN filtro-->
        <div class="row-border">
            <div class="col-lg-12">
                <?php
                $total_mesas = 0;
                $total_dinero = 0; 
                //$query = "select t.documento, t.nombres, t.apellidos,count(DISTINCT p.pkPediido) as total_pedidos , sum(dp.cantidad*dp.precio) as total from pedido p inner join detallepedido dp on p.pkpediido=dp.pkpediido inner join trabajador t on dp.pkMozo=t.pktrabajador where p.fechaCierre BETWEEN '$inicio' AND '$fin' AND p.estado <> 3 and dp.estado > 0 and dp.estado <3 group by t.documento order by 4 desc";
                $query = "Select t.documento, concat(t.nombres,' ',t.apellidos) as trabajador, count(DISTINCT p.pkPediido) as total_pedidos, sum(dp.cantidad*dp.precio) as total from pedido p, detallepedido dp, trabajador t where p.pkpediido = dp.pkpediido AND dp.pkMozo = t.pktrabajador AND p.fechaCierre BETWEEN '$inicio' AND '$fin' AND p.estado = 1 and dp.estado > 0 and dp.estado <3 group by t.documento order by 3 desc"
                ?>
                <div class="tab-content">
                    <div class="tab-pane active" id="pedidos">
                        <table class='tb display' cellspacing='0' width='100%' border="0">
                            <thead>
                                <tr>
                                    <th>DNI</th>
                                    <th>Nombres Y Apellidos</th>
                                    <th>Mesas Atendidas</th>
                                    <th>Total (<?php echo $r_moneda["simbolo"];?>)</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody >
                            <?php 
                            $resultado_top = $conn->consulta_matriz($query);
                            if(is_array($resultado_top)){
                                foreach($resultado_top as $t){
                                    echo "<tr>";
                                    echo "<td>".$t["documento"]."</td>";
                                    echo "<td>".$t["trabajador"]."</td>";
                                    echo "<td>".intval($t["total_pedidos"])."</td>";
                                    echo "<td>".round(floatval($t["total"]),2)."</td>";
                                    echo "<td><a onclick='detalle(".$t["documento"].",\"".$t["trabajador"]."\",\"".$inicio."\",\"".$fin."\")'><span class='glyphicon glyphicon-list' title='Detale de trabajador'></span></a></td>";
                                    echo "</tr>";

                                    $total_mesas = $total_mesas + intval($t["total_pedidos"]);
                                    $total_dinero = $total_dinero + round(floatval($t["total"]),2);
                                }

                                echo "<tr>";
                                echo "<td></td>";
                                echo "<td></td>";
                                echo "<td>".$total_mesas."</td>";
                                echo "<td>".$total_dinero."</td>";
                                echo "<td></td>";
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

    <script src="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/dataTables.buttons.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/jszip.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/pdfmake.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/vfs_fonts.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/buttons.html5.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/buttons.print.min.js"></script>
    <script>
        $("#inicio").datepicker({dateFormat: 'yy-mm-dd'});
        $("#fin").datepicker({dateFormat: 'yy-mm-dd'});

        function detalle($dni, $trabajador, $fechainicio, $fechafin)
        {
            window.open('<?php echo Class_config::get('urlApp') ?>/?controller=Report&action=VerDetalleTrabajador&dni=' + $dni + '&trabajador=' + $trabajador + '&inicio=' + $fechainicio + '&fin=' + $fechafin, '_blank');
        }

        function filtrar() {
            location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Report&action=TVentasMozo&i="+$("#inicio").val()+"&f="+$("#fin").val();
        }

        function grafica() {
            var url = "<?php echo Class_config::get('urlApp') ?>/ReportesGraficos/GraficaBarrasVentasMozo.php?DateInicio=" + $("#inicio").val() + "&DateFin=" + $("#fin").val();
            ;
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