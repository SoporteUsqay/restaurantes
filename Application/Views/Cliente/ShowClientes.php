<?php
error_reporting(E_ALL);
$titulo_importante = 'Clientes';
include 'Application/Views/template/header.php';
require_once 'reportes/recursos/componentes/MasterConexion.php';
$objcon = new MasterConexion();
$f = $objcon->consulta_arreglo("Select * from cierrediario where pkCierreDiario = 1");

$obj = new Application_Models_CajaModel();
$fechaCaja = $obj->fechaCierre();

$filter_inicio = date('Y-m-01');
if (isset($_REQUEST['inicio'])) {
    $filter_inicio = $_REQUEST['inicio'];
}

$filter_fin = date('Y-m-d');
if (isset($_REQUEST['fin'])) {
    $filter_fin = $_REQUEST['fin'];
}

$db = new SuperDataBase;

?>

<body>

    <?php
    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showContent();
    ?>

    <div class="container-fluid">

        <br>
        <br>
        <br>
        <br>


        <div class="panel panel-primary">

            <div class="panel-heading">
                <h4>
                    <i class="fa fa-clipboard"></i>
                    Reporte Clientes
                </h4>
            </div>



            <div class="panel-body">

                <div>

                    <form id="frmFiltro">

                        <div class="row">
                            <div class="col-xs-12 col-md-3">
                                <div class="form-group">
                                    <label for="">Fecha de Inicio</label>
                                    <input type="text" id="fecha_inicio" name="inicio" value="<?php echo $filter_inicio ?>" class="form-control">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-3">
                                <div class="form-group">
                                    <label for="">Fecha de Fin</label>
                                    <input type="text" id="fecha_fin" name="fin" value="<?php echo $filter_fin ?>" class="form-control">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-3">
                                <label for="" style="color: transparent">.</label>
                                <button type="button" class="btn btn-primary" style="display: block" onclick="Filtrar()">
                                    <i class="fa fa-filter"></i>
                                    Filtrar
                                </button>
                            </div>
                        </div>
                    </form>

                </div>

                <hr>

                <table id="empresas" class="table-bordered">
                    <thead>
                        <th>DOCUMENTO</th>
                        <th>Nombres / Razón Social</th>
                        <th class="text-right">Total <br> Consumido</th>
                        <th class="text-right">N° Visitas</th>
                        <th>Dirección</th>
                        <th>Email</th>
                    </thead>
                    <tbody>


                        <?php

                        $query = "SELECT
                                comprobante.ruc,
                                comprobante.documento,
                                person.nombres,
                                persona_juridica.razonSocial,
                                person.address,
                                person.email,
                                persona_juridica.address as address1,
                                persona_juridica.email as email1,
                                count(
                                    detallecomprobante.pkPediido
                                ) AS cantidad,
                                sum(detallecomprobante.total) AS total
                            FROM
                                detallecomprobante
                            LEFT JOIN pedido ON pedido.pkPediido = detallecomprobante.pkPediido
                            LEFT JOIN comprobante ON comprobante.pkComprobante = detallecomprobante.pkComprobante
                            LEFT JOIN person ON person.documento = comprobante.documento
                            LEFT JOIN persona_juridica ON persona_juridica.ruc = comprobante.ruc
                            WHERE
                                pedido.fechaCierre BETWEEN '$filter_inicio'
                            AND '$filter_fin'
                            AND pedido.estado IN (1, 4, 5)
                            AND comprobante.ruc is not null
                            AND comprobante.documento is not null
                            GROUP BY
                                ruc,
                                documento,
                                nombres,
                                razonSocial,
                                address,
                                email,
                                address1,
                                email1
                            ORDER BY
                                total desc,
                                cantidad desc
                            ";

                        $res = $db->executeQueryEx($query);

                        while ($row = $db->fecth_array($res)) :

                            if (!$row['documento'] && !$row['ruc']) continue;
                        ?>

                            <tr>
                                <td>
                                    <?php
                                    echo implode(' ', [
                                        $row['ruc'],
                                        $row['documento'],
                                    ])
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    echo implode(' ', [
                                        $row['nombres'],
                                        $row['razonSocial'],
                                    ])
                                    ?>
                                </td>
                                <td class="text-right">
                                    S/ <?php echo number_format($row['total'], 2) ?>
                                </td>
                                <td class="text-right">
                                    <?php echo $row['cantidad'] ?>
                                </td>
                                <td>
                                    <?php
                                    echo implode(' ', [
                                        $row['address'],
                                        $row['address1'],
                                    ])
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    echo implode(' ', [
                                        $row['email'],
                                        $row['email1'],
                                    ])
                                    ?>
                                </td>
                            </tr>

                        <?php endwhile ?>

                    </tbody>
                </table>

            </div>
        </div>

    </div>

    <link rel="stylesheet" type="text/css" href="Public/select2/css/select2.css" rel="stylesheet">
    <script type="text/javascript" src="Public/select2/js/select2.js"></script>
    <link rel="stylesheet" href="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.css">
    <script src="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/dataTables.buttons.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/jszip.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/pdfmake.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/vfs_fonts.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/buttons.html5.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/buttons.print.min.js"></script>
    <script>
        $(document).ready(() => {
            $("#empresas").DataTable({
                dom: 'Blfrtip',
                "bSort": false,
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
            $("#personas").DataTable({
                dom: 'Blfrtip',
                buttons: [

                ]
            });
            $("#externos").DataTable({
                dom: 'Blfrtip',
                buttons: [

                ]
            });

            $("#fecha_inicio").datepicker({
                dateFormat: 'yy-mm-dd'
            });
            $("#fecha_fin").datepicker({
                dateFormat: 'yy-mm-dd'
            });
        })


        function Filtrar() {

            console.log("<?php echo Class_config::get('urlApp') ?>/?controller=Sale&action=ReporteClientes&" + $('#frmFiltro').serialize())

            window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Sale&action=ReporteClientes&" + $('#frmFiltro').serialize();
        }
    </script>
</body>

</html>