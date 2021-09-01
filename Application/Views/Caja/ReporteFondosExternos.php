<?php 
include 'Application/Views/template/header.php';
require_once 'reportes/recursos/componentes/MasterConexion.php'; 
$objcon = new MasterConexion();
$f = $objcon->consulta_arreglo("Select * from cierrediario where pkCierreDiario = 1");

$obj = new Application_Models_CajaModel();
$fechaCaja = $obj->fechaCierre();

$filter_inicio = date('Y-m-01');
if (isset($_REQUEST['inicio'])){
    $filter_inicio = $_REQUEST['inicio'];
}

$filter_fin = date('Y-m-d');
if (isset($_REQUEST['fin'])){
    $filter_fin = $_REQUEST['fin'];
}

?>

<style>
    .lbl-total {
        font-size: 20px
    }
    .text-danger {
        color: #fd2b27;
    }
</style>

<body>

    <?php
        $objViewMenu = new Application_Views_IndexView();
        $objViewMenu->showContent();

        $db = new SuperDataBase();

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
                Reporte Fondos Externos</h4>
            </div>

            <div class="panel-body">

                <div>

                    <form id="frmFiltro">

                        <div class="row">
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <label for="">Fecha de Inicio</label>
                                    <input type="text" id="fecha_inicio" name="inicio" value="<?php echo $filter_inicio ?>" class="form-control">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <label for="">Fecha de Fin</label>
                                    <input type="text" id="fecha_fin" name="fin" value="<?php echo $filter_fin ?>" class="form-control">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <label for="" style="color: transparent">.</label>
                                <button type="button" class="btn btn-primary" style="display: block" onclick="Filtrar()">
                                    <i class="fa fa-filter"></i>
                                    Filtrar
                                </button>
                            </div>
                        </div>
                    </form>
                    
                </div>

                <br>

                <div class="row">

                    <?php 

                        $query = "SELECT
                            moneda.simbolo AS moneda_simbolo,
                            medio_pago.nombre AS medio_pago_nombre,
                            SUM(monto) AS total
                        FROM
                            movimiento_dinero_fe
                        LEFT JOIN moneda ON movimiento_dinero_fe.moneda = moneda.id
                        LEFT JOIN medio_pago ON movimiento_dinero_fe.id_medio = medio_pago.id
                        WHERE
                            movimiento_dinero_fe.fecha_cierre BETWEEN '$filter_inicio' AND '$filter_fin'
                        AND movimiento_dinero_fe.estado = 1
                        GROUP BY
                            movimiento_dinero_fe.id_medio,
                            movimiento_dinero_fe.moneda";

                        $res = $db->executeQueryEx($query);

                        while ($row = $db->fecth_array($res)):

                    ?>

                    <div class="col-xs-12 col-md-3 text-center">
                        <div class="lbl-total text-<?php echo $row['total'] >= 0 ? 'success' : 'danger' ?>"><?php echo number_format($row['total'], 2) ?></div>
                        <div class=""><?php echo mb_strtoupper($row['medio_pago_nombre']) ?> (<?php echo $row['moneda_simbolo'] ?>)</div>
                    </div>



                    <?php endwhile; ?>
                </div>

                <hr>

                <table id="tblCompras" class="table">

                    <thead>
                        <th>#</th>
                        <th>Fecha y Hora</th>
                        <!-- <th>Fecha Caja</th> -->
                        <th>Origen</th>
                        <th class="text-center">Medio Pago</th>
                        <th class="text-right"></th>
                        <th class="text-right">Total</th>
                        <th>Comentario</th>
                        <th>Usuario</th>
                    </thead>
                
                    <tbody>

                        <?php 

                            $query = "SELECT
                                movimiento_dinero_fe.*,
                                moneda.simbolo as moneda_simbolo,
                                medio_pago.nombre as medio_pago_nombre,
                                CONCAT(trabajador.nombres, ' ', trabajador.apellidos) as trabajador_nombre,
                                tipo_gasto.nombre as tipo_gasto_nombre
                            FROM
                                movimiento_dinero_fe
                            LEFT JOIN moneda ON movimiento_dinero_fe.moneda = moneda.id
                            LEFT JOIN medio_pago ON movimiento_dinero_fe.id_medio = medio_pago.id
                            LEFT JOIN trabajador ON movimiento_dinero_fe.id_usuario = trabajador.pkTrabajador
                            LEFT JOIN tipo_gasto ON movimiento_dinero_fe.id_origen = tipo_gasto.id
                            WHERE 
                                movimiento_dinero_fe.fecha_cierre BETWEEN '$filter_inicio' AND '$filter_fin'
                            AND movimiento_dinero_fe.estado = 1
                            ";

                            $res = $db->executeQueryEx($query);
                            
                            $index = 1;
                            while ($row = $db->fecth_array($res)):

                        ?>
                        <tr>
                            <td><?php echo $index++ ?></td>
                            <td><?php echo $row['fecha_hora'] ?></td>
                            <!-- <td class="text-info"><?php echo $row['fecha_cierre'] ?></td> -->
                            <td><?php 
                                switch ($row['tipo_origen']) {
                                    case 'COM':
                                        echo 'COMPRAS';
                                        break;
                                    case 'GAS':
                                        echo $row['tipo_gasto_nombre'];
                                        break;
                                    case 'CC':
                                        echo 'CIERRE CAJA';
                                        break;
                                }
                            ?></td>
                            <td class="text-center"><?php echo $row['medio_pago_nombre'] ?></td>
                            <td class="text-right <?php echo $row['monto'] < 0 ? 'alert-danger' : 'alert-success' ?>"><?php echo $row['moneda_simbolo'] ?></td>
                            <td class="text-right <?php echo $row['monto'] < 0 ? 'alert-danger' : 'alert-success' ?>">
                                <?php echo number_format($row['monto'], 2) ?>
                            </td>
                            <td><?php echo $row['comentario'] ?></td>
                            <td><?php echo $row['trabajador_nombre'] ?></td>
                        </tr>

                        <?php endwhile ?> 

                    </tbody>
                </table>

            </div>
        </div>
    
    </div>


    <link rel="stylesheet" type="text/css" href="Public/select2/css/select2.css" rel="stylesheet">
    <script  type="text/javascript" src="Public/select2/js/select2.js"></script>
    <!-- <script src="Application/Views/Compras/js/Compras.js.php"></script> -->
    
    <script>

        $(document).ready(function () {
            $('#tblCompras').DataTable({
                // "ordering": false,
                // "bSort": false,
            });

            $("#fecha_inicio").datepicker({dateFormat: 'yy-mm-dd'});
            $("#fecha_fin").datepicker({dateFormat: 'yy-mm-dd'});

        });

        function Filtrar() {
            window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Report&action=showFondosExternos&" + $('#frmFiltro').serialize();
        }
    </script>
</body>

</html>