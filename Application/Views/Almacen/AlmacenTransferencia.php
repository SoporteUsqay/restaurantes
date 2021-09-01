<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <title><?php echo Class_config::get('nameApplication') ?></title>
        <!--<link rel="stylesheet" type="text/css" href="Public/jquery-easyui/easyui/themes/default/easyui.css">-->
        <!--<link rel="stylesheet" type="text/css" href="Public/jquery-easyui/easyui/themes/icon.css">-->
        <link rel="stylesheet" href="Public/jquery-ui-1.10.4.custom/css/jquery-ui.min.css">

        <link href="Public/Bootstrap/css/bootstrap.css" rel="stylesheet">
        <link href="Public/Bootstrap/media/css/jquery.dataTables.min.css" rel="stylesheet">

        <link rel="stylesheet" type="text/css" href="Public/css/style.css">
        <!--<link rel="stylesheet" type="text/css" href="Public/css/style2.css">-->
        <!--<link rel="stylesheet" type="text/css" href="Public/jquery-easyui/easyui/demo.css">-->

        <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery-1.10.2.js"></script>
        <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery-ui-1.10.4.custom.js"></script>
        <!--<script src="//code.jquery.com/jquery-1.10.2.js"></script>-->
        <!--<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>-->
        <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery.numeric.js"></script>
        <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery.maskedinput.js"></script>
        <script type="text/javascript" src="Public/scripts/body.js.php"></script>
        <script type="text/javascript" src="Public/scripts/listGeneral.js.php"></script>
        <script type="text/javascript" src="Public/scripts/Validation.js.php"></script>
        <script  type="text/javascript" src="Public/Bootstrap/js/bootstrap.min.js"></script>
        <script  type="text/javascript" src="Public/Bootstrap/media/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/Concurrent.Thread.js"></script>
        <link rel="stylesheet" href="Public/Usqay/css/usqay.css">
        <link rel="stylesheet" href="Public/Usqay/css/clc.css">
        
        <link rel="icon" href="logo.ico"/>

        <style>
            .select2-container--default .select2-selection--single {
                height: 46px !important;
            }
        </style>
    </head>
    <body>
    <?php
        // error_reporting(E_ALL);
        require_once('KardexHelper.php');

        $objViewMenu = new Application_Views_IndexView();
        $objViewMenu->showContent();

        $obj = new Application_Models_CajaModel();
        $fecha = $obj->fechaCierre();
        
        $db = new SuperDataBase();
    ?>
        <div class="container-fluid">
            <br>
            <br>
            <br>

            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4>Transferencia entre Almacenes</h4>
                </div>

                <div class="panel-body">

                    <div class="col-xs-12 col-md-6 col-md-offset-3">
                        <div class="form-group">
                            <label for="">Almacén</label>
                            <select class="form-control" name="" id="cmbAlmacen">
                                <!-- <option value="">Seleccione</option> -->
                                <?php 
                                    $query = "select * from n_almacen";

                                    $res = $db->executeQueryEx($query);

                                    while($row = $db->fecth_array($res)):
                                ?>
                                    <option value="<?php echo $row['id'] ?>" 
                                        <?php echo ($_GET['almacen_id'] == $row['id']) ? 'selected' : '' ?> >
                                        <?php echo $row['nombre'] ?>
                                    </option>    
                                <?php endwhile ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-xs-12">
                                        
                        <table id="tblDetalles" class="table">
                            <thead>
                                <th>Insumo</th>
                                <th>Insumo / Porción</th>
                                <th>Stock Inicial</th>
                                <th>Stock Movimientos</th>
                                <th>Stock Ventas</th>
                                <th>Stock V. por Cobrar</th>
                                <th>Stock Actual</th>
                                <th></th>
                            </thead>
                            <tbody> 
                            <?php 

                                $kardexHelper = new KardexHelper();

                                $kardexHelper->setAlmacen($_GET['almacen_id'] ?? '1');

                                $data_historial = $kardexHelper->getDataHistorial($fecha);
                                
                                $data_movimientos = $kardexHelper->getDataMovimientos($fecha);
                                
                                $lista_completa = $kardexHelper->getListaCompleta();

                                $data_platos_vendidos = $kardexHelper->getDataPlatosVendidos($fecha);

                                $query = "select fecha from n_historial_stock_insumo order by id asc limit 1";

                                $res = $db->executeQueryEx($query);
                                
                                $fechaInicioHistorial = date('Y-m-d');

                                while ($row = $db->fecth_array($res)) {
                                    $fechaInicioHistorial = $row['fecha'];
                                }

                                $data_platos_vendidos_por_cobrar = $kardexHelper->getDataPlatosVendidosPorCobrar($fecha);

                                $data_platos_vendidos_por_cobrar_init = [];

                                if ($fechaInicioHistorial != $fecha) {
                                    $data_platos_vendidos_por_cobrar_init = $kardexHelper->getDataPlatosVendidosPorCobrar($fechaInicioHistorial, date("Y-m-d", strtotime($fecha."- 1 day")));
                                } 

                                $data_platos_vendidos_cobrados_hoy = [];

                                if ($fechaInicioHistorial != $fecha) {
                                    $data_platos_vendidos_cobrados_hoy = $kardexHelper->getDataPlatosVendidosPagadosHoy($fechaInicioHistorial, date("Y-m-d", strtotime($fecha."- 1 day")));
                                } 
                                
                                foreach ($lista_completa as $item):

                                    $stock_ayer = $kardexHelper->getStockPorInsumo($item, $data_historial);

                                    $item['stock_ayer'] = is_null($stock_ayer) ? 0 : $stock_ayer['stock'];

                                    $stock_ayer_cobrar = $kardexHelper->getStockPorInsumo($item, $data_platos_vendidos_por_cobrar_init);

                                    $item['stock_ayer_cobrar'] = is_null($stock_ayer_cobrar) ? 0 : $stock_ayer_cobrar['cantidad_insumo'];

                                    $item['stock_ayer'] -= $item['stock_ayer_cobrar'];

                                    $stock_ventas_cobrados_hoy = $kardexHelper->getStockPorInsumo($item, $data_platos_vendidos_cobrados_hoy);

                                    $item['stock_ayer_cobrado'] = (is_null($stock_ventas_cobrados_hoy) ? 0 : $stock_ventas_cobrados_hoy['cantidad_insumo']);
                                    
                                    $item['stock_ayer'] -= $item['stock_ayer_cobrado'];

                                    $stock_movimiento = $kardexHelper->getStockPorInsumo($item, $data_movimientos);

                                    $item['stock_movimiento'] = is_null($stock_movimiento) ? 0 : $stock_movimiento['cantidad'];

                                    $stock_ventas = $kardexHelper->getStockPorInsumo($item, $data_platos_vendidos);

                                    $item['stock_ventas'] = is_null($stock_ventas) ? 0 : $stock_ventas['cantidad_insumo'];

                                    $stock_ventas_cobrar = $kardexHelper->getStockPorInsumo($item, $data_platos_vendidos_por_cobrar);

                                    $item['stock_ventas_cobrar'] = is_null($stock_ventas_cobrar) ? 0 : $stock_ventas_cobrar['cantidad_insumo'];

        

                                    $item['stock'] = $item['stock_ayer'] + $item['stock_movimiento'] - $item['stock_ventas'] - $item['stock_ventas_cobrar'];
                                    
                                    if ($item['stock'] <= 0) continue;
                            ?>
                                <tr>
                                    
                                    <td><?php echo $item['nombre_insumo'] ?></td>
                                    <td><?php echo implode(' ', [
                                        floatval($item['cantidad']) == 0 ? '' : floatval($item['cantidad']),
                                        ($item['nombre_unidad']),
                                        ($item['descripcion']),
                                    ]) ?></td>
                                    <td class="text-right"><?php echo intval($item['stock_ayer']) ?></td>
                                    <td class="text-right"><?php echo intval($item['stock_movimiento']) ?></td>
                                    <td class="text-right"><?php echo intval($item['stock_ventas']) ?></td>
                                    <td class="text-right"><?php echo intval($item['stock_ventas_cobrar']) ?></td>
                                    <td class="text-right"><?php echo intval($item['stock']) ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-success btn-sm" onclick="goPorcionar(<?php echo $item['insumo_id'] ?>, <?php echo $item['insumo_porcion_id'] ?>)">
                                            <span class="glyphicon glyphicon-log-out"></span>
                                            Transferir
                                        </button>
                                    </td>
                                    
                                
                                </tr>    

                            <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                
                </div>
            </div>


        </div>
    </div>
   
    <script>

        $('#cmbAlmacen').on('change', function () {
            let val = $('#cmbAlmacen').val();

            window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=AlmacenTransferencia&action=Show&almacen_id=" + val;
        })

        $(document).ready(function (){
            $('#tblDetalles').DataTable()
        })

        function goPorcionar(insumo_id, insumo_porcion_id) {

            console.log('item', insumo_id, insumo_porcion_id)

            let params = "";

            if ($('#cmbAlmacen').val()) {
                params += "&almacen_id=" + $('#cmbAlmacen').val();
            } else {
                return alert('Seleccione un almacén')
            }

            if (insumo_id) {
                params += "&insumo_id=" + insumo_id;
            }

            if (insumo_porcion_id) {
                params += "&porcion_id=" + insumo_porcion_id;
            }

            window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=AlmacenTransferencia&action=ShowDetail" + params;
        }
        
    </script>
</div>
</body>
</html>
