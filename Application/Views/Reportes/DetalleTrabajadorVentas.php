<?php
$db = new SuperDataBase();
$dni = "";
$dni = $_REQUEST['dni'];
$trabajador = "";
$trabajador = $_REQUEST['trabajador'];
$inicio = "";
$inicio = $_REQUEST['inicio'];
$fin = "";
$fin = $_REQUEST['fin'];

$titulo_importante = "Detalle de ventas de ".$trabajador."  del ".$inicio." al ".$fin;

include 'Application/Views/template/header.php';
$objViewMenu = new Application_Views_IndexView();
$objViewMenu->showContent();
?>
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

<div class="container">

    <br><br><br><br>
    <div class="row">
        <div class="col-lg-12">

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4><i class="glyphicon glyphicon-list-alt"></i> <?php echo $titulo_importante; ?>
                    </h4>
                </div>
                <div class="panel-body">

                    <div class="tab-content">
                        <div class="tab-pane active"> 
                            <br>
                            <table class='tb display' cellspacing='0' width='100%' border="0">
                                <thead>
                                    <tr>                                 
                                        <th>Plato</th>
                                        <th>P.Unitario</th>
                                        <th>Cantidad</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $cantidad = 0;
                                    $totales = 0;
                                    $db = new SuperDataBase();
                                    $item = 0;
                                    /*$query = "SELECT sum(d.cantidad) as total, p.descripcion as plato, t.nombres  from detallepedido d inner join plato p on d.pkPlato=p.pkPlato inner join trabajador t on d.pkMozo=pkTrabajador inner join pedido pe on d.pkPediido=pe.pkPediido where fechaCierre between'$inicio' and '$fin' and t.documento LIKE '%$dni%' and d.estado > 0 and d.estado < 3 and pe.estado <> 3 group by p.descripcion";*/
                                    $query = "SELECT sum(d.cantidad) as cantidad, sum(d.cantidad*d.precio) as total, d.precio, p.descripcion as plato, t.nombres from detallepedido d, plato p, trabajador t, pedido pe where d.pkPlato=p.pkPlato AND d.pkMozo=pkTrabajador AND d.pkPediido=pe.pkPediido AND fechaCierre between'$inicio' and '$fin' and t.documento LIKE '%$dni%' AND d.estado > 0 and d.estado < 3 and pe.estado = 1 group by p.descripcion";
                                    $result = $db->executeQuery($query);
                                    while ($row = $db->fecth_array($result)) {
                                        $item = $item + 1;
                                        echo "<tr>";
                                        echo "<td>";
                                        echo $row['plato'];
                                        echo "</td>";
                                        echo "<td>";
                                        echo $row['precio'];
                                        echo "</td>";
                                        echo "<td>";
                                        echo $row['cantidad'];
                                        $cantidad = $cantidad + floatval($row['cantidad']);
                                        echo "</td>";
                                        echo "<td>";
                                        echo $row['total'];
                                        $total = $total + floatval($row['total']);
                                        echo "</td>";      
                                        echo "</tr>";
                                    }
                                    ?>
                                    <tr>
                                        <td>
                                        </td>
                                        <td>
                                        </td>
                                        <td>
                                        <?php 
                                        echo $cantidad;
                                        ?>
                                        </td>
                                        <td>
                                        <?php 
                                        echo $total;
                                        ?>
                                        </td>
                                    </tr>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
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