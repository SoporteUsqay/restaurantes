<?php 
$titulo_importante = "Reporte Pedidos Anulados";
include 'Application/Views/template/header.php'; ?>

<body>

    <?php
    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showContent();
    $obj = new Application_Models_CajaModel();
    $fechaInicio = date('Y-m-d', strtotime('-1 day'));
    if (isset($_REQUEST['fecha_inicio']))
        $fechaInicio = $_REQUEST['fecha_inicio'];

    $fechaFin = date('Y-m-d');
    if (isset($_REQUEST['fecha_fin']))
        $fechaFin = $_REQUEST['fecha_fin'];
    ?>
    <link rel="stylesheet" href="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.css">
    <style>
        .dataTables_filter{
            margin-right:15px !important;
        }
        .dt-buttons{
            margin-bottom: 10px !important;
        }
    </style>   
    <div class="container">

        <br /><br/><br/>
        <h2>Reporte de Pedidos Anulados</h2>
        <form id="frmFiltroProductosAnulados">
            <div class="panel panel-primary" id='pfecha'>
                <div class="panel-heading">
                    Filtros por fechas
                </div>
                <div class="panel-body">
                    <div class='control-group col-lg-4' id="dinicio">
                        <label>Fecha Inicio</label>
                        <input class='form-control date' placeholder='AAAA-MM-DD' id='fecha_inicio' name='fecha_inicio' value="<?php echo $fechaInicio ?>"/>
                    </div>
                    <div class='control-group  col-lg-4' id="ffin">
                        <label>Fecha Fin</label>
                        <input class='form-control date' placeholder='AAAA-MM-DD' id='fecha_fin' name='fecha_fin' value="<?php echo $fechaFin ?>"/>
                    </div>
                    <div class='control-group col-lg-4'>
                        <br/>
                        <button type='button' class='btn btn-primary' onclick='buscarProductosAnulados()'>Buscar</button>
                        <br/>
                    </div>
                </div>
            </div>
        </form>
        <table class="table" id="tabla_anulaciones">
            <thead>
                <tr>
                    <th>COD</th>
                    <th>ID Venta</th>
                    <th>Cant</th>
                    <th>Pedido</th>
                    <th>Fecha Pedido</th>
                    <th>Fecha Anulación</th>
                    <th>Total</th>
                    <th>Motivo</th>
                    <th>Pedido por</th>
                    <th>Anulado por</th>
                </tr>

            </thead>
            <tbody>
                <?php
                $db = new SuperDataBase();
                $query = "SELECT *,(select upper(descripcion) from plato pl where pl.pkPlato=d.pkPlato) as pedido, case  when length(pkCocinero)>0 then (select concat(nombres,' ', apellidos) from  trabajador t where t.pkTrabajador=pkCocinero) end cocinero, case when length(pkMozo)>0 then (select concat(nombres,' ', apellidos) from  trabajador t where t.pkTrabajador=pkMozo) end mozo FROM detallepedido d where estado =3 and date_format(fechaPedido,'%Y-%m-%d') between '$fechaInicio' and '$fechaFin';";
                $result = $db->executeQuery($query);
                while ($row = $db->fecth_array($result)) {
                    ?>
                    <tr>
                        <td><?php echo $row['pkDetallePedido'] ?></td> 
                        <td><?php echo $row['pkPediido'] ?></td> 
                        <td><?php echo $row['cantidad'] ?></td> 
                        <td><?php echo $row['pedido'] ?></td>
                        <td><?php echo $row['horaPedido'] ?></td>
                        <td><?php echo $row['fechaPedido'] ?></td>
                        <td><?php echo $row['cantidad'] * $row['precio'] ?></td>
                        <td><?php echo $row['mensaje'];?></td>
                        <td><?php echo $row['mozo'] ?></td>
                        <td><?php echo $row['cocinero'] ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>

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
    $(".date").datepicker({dateFormat: 'yy-mm-dd'});
    $('#tabla_anulaciones').DataTable( {
        dom: 'Blfrtip',
        "bSort": false,
        "bFilter": true,
        "bInfo": true,
        "ordering": false,
        "paging": true,
        buttons: [
            {
                extend: 'excelHtml5',
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
                title: '<?php echo $titulo_importante;?>'
            },
            {
                extend: 'print',
                orientation: 'landscape',
                pageSize: 'LEGAL',
                title: '<?php echo $titulo_importante;?>'
            }
            
        ]
    } );
    function buscarProductosAnulados() {
        window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Report&action=showPedidosAnulados&" + $('#frmFiltroProductosAnulados').serialize();
    }

</script>