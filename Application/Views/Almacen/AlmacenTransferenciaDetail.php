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
            <br>

            <div class="panel panel-primary">
            
                <div class="panel-heading">
                    <h4>Transferir</h4>
                </div>
                <div class="panel-body">

                    <?php 

                    $main_insumo_id = $_GET['insumo_id'];

                    $main_insumo_porcion_id = isset($_GET['porcion_id']) ? $_GET['porcion_id'] : null;

                    $kardexHelper = new KardexHelper();

                    $kardexHelper->setAlmacen($_GET['almacen_id']);

                    $main = $kardexHelper->getStockPorInsumoPorcionDetail($fecha, $main_insumo_id, $main_insumo_porcion_id);

                    // if ($main['stock'] <= 0) {
                    //     echo "<script>window.location.href='" . Class_config::get('urlApp') . "/?controller=AlmacenTransferencia&action=Show&almacen_id={$_GET['almacen_id']}'</script>";
                    // }

                    // echo "<br>RES: ";
                    // echo json_encode($main);

                    $query = "select * from n_almacen";

                    $list_almacen = [];

                    $almacen = [];

                    $res = $db->executeQueryEx($query);

                    while ($row = $db->fecth_array($res)) {
                        if ($_GET['almacen_id'] == $row['id']) {
                            $almacen = $row;
                        } else {
                            $list_almacen[] = $row;
                        }
                    }
                    ?>


                    <div class="col-xs-12 col-md-8 col-md-offset-2 panel panel-body">

                    <div class="table-responsive">
                        <table class="table">
                            <tr>
                                <th>Insumo</th>
                                <td><?php echo $main['nombre_insumo'] ?></td>
                                <?php 
                                    if ($main_insumo_porcion_id) :
                                ?>
                                <th>Porción</th>
                                <td><?php echo implode(' ', [
                                    floatval($main['cantidad']) == 0 ? '' : floatval($main['cantidad']),
                                    ($main['nombre_unidad']),
                                    ($main['descripcion']),
                                ]) ?></td>
                                <?php else: ?>
                                <td></td>
                                <td></td>
                                <?php endif ?>
                            </tr>
                            <tr>
                                <th>Almacén</th>
                                <td colspan="3"><?php echo $almacen['nombre'] ?? '-' ?></td>
                            </tr>
                            <tr>
                                <th>Unidad</th>
                                <td><?php echo $main['unidad'] ?></td>
                                <th>Stock</th>
                                <td><?php echo $main['stock'] ?></td>
                            </tr>
                        </table>
                    </div>

                    <div class="text-center" style="margin-top: 1em">
                        <!-- <button class="btn btn-success">
                            Registrar Transferencia
                        </button> -->
                    </div>
                    </div>

                    <div class="col-xs-12">
                                    
                    <table id="tblDetalles">
                        <thead>
                            <th>#</th>
                            <th>Insumo</th>
                            <th>Insumo / Porción</th>
                            <th>Valor</th>
                            <th>Stock Máx.</th>
                            <th>Cantidad</th>
                            <th>Almacén</th>
                            <th></th>
                        </thead>
                        <tbody> 
                        <?php 

                            $query = "SELECT
                                insumos.pkInsumo as id,
                                insumos.descripcionInsumo AS nombre_insumo
                            FROM
                                insumos
                            where insumos.pkInsumo = $main_insumo_id";

                            $res = $db->executeQueryEx($query);

                            $index = 1;

                            if (!$main_insumo_porcion_id) {
                            
                            while ($item = $db->fecth_array($res)):
                        ?>
                            <tr>
                                <td><?php echo $index++; ?></td>
                                <td><?php echo $item['nombre_insumo'] ?></td>
                                <td></td>
                                <td class="text-right">1.0000</td>
                                <td class="text-right"><?php echo $main['stock'] < 0 ? 0 : $main['stock'] ?></td>
                                <td class="text-center">
                                    <input id="cantidad-insumo-" type="number" value="0" class="form-control">
                                </td>
                                <td class="text-center">
                                    <select name="" id="almacen-insumo-" class="form-control">
                                        <?php foreach ($list_almacen as $al) : ?>
                                            <option value="<?php echo $al['id'] ?>"><?php echo $al['nombre'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <button id="btn-insumo-" class="btn btn-success" onclick="registrarTransferencia('insumo', '', 1)">
                                        <span class="glyphicon glyphicon-share"></span>
                                        Registrar
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; } ?>
                        <?php 

                            $query = "SELECT
                                insumo_porcion.id as insumo_porcion_id,
                                id,
                                insumo_porcion.insumo_id,
                                insumos.descripcionInsumo AS nombre_insumo,
                                unidad.descripcion AS nombre_unidad,
                                insumo_porcion.valor,
                                insumo_porcion.cantidad,
                                insumo_porcion.descripcion
                            FROM
                                insumo_porcion
                            LEFT JOIN unidad ON insumo_porcion.unidad_id = unidad.pkUnidad
                            LEFT JOIN insumos ON insumo_porcion.insumo_id = insumos.pkInsumo
                            where insumo_porcion.deleted_at is null and insumo_porcion.insumo_id = $main_insumo_id";

                            if ($main_insumo_porcion_id) {
                                $query .= " and insumo_porcion.id = $main_insumo_porcion_id";
                            }

                            $res = $db->executeQueryEx($query);

                            $index = 1;
                            
                            while ($item = $db->fecth_array($res)):

                                // echo json_encode($item);

                        ?>
                            <tr>
                                
                                <td><?php echo $index++; ?></td>
                                <td><?php echo $item['nombre_insumo'] ?></td>
                                <td><?php echo implode(' ', [
                                    floatval($item['cantidad']) == 0 ? '' : floatval($item['cantidad']),
                                    ($item['nombre_unidad']),
                                    ($item['descripcion']),
                                ]) ?></td>
                                <td class="text-right"><?php echo $item['valor'] ?></td>
                                <td class="text-right"><?php echo floatval($main['stock'] / $item['valor']) < 0 ? 0 : floatval($main['stock'] / $item['valor']) ?></td>
                                <td class="text-center">
                                    <input id="cantidad-<?php echo $item['insumo_id'] ?>-<?php echo $item['insumo_porcion_id'] ?>" value="0" type="number" class="form-control">
                                </td>
                                <td class="text-center">
                                    <select name="" id="almacen-<?php echo $item['insumo_id'] ?>-<?php echo $item['insumo_porcion_id'] ?>" class="form-control">
                                        <?php foreach ($list_almacen as $al) : ?>
                                            <option value="<?php echo $al['id'] ?>"><?php echo $al['nombre'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <button id="btn-<?php echo $item['insumo_id'] ?>-<?php echo $item['insumo_porcion_id'] ?>" class="btn btn-success"
                                        onclick="registrarTransferencia(<?php echo $item['insumo_id'] ?>, <?php echo $item['insumo_porcion_id'] ?>, <?php echo $item['valor'] ?>)">
                                        <span class="glyphicon glyphicon-share"></span>
                                        Registrar
                                    </button>
                                </td>
                                
                            
                            </tr>    

                        <?php endwhile ?>
                            <tr>
                                <td><?php echo $index++; ?></td>
                                <td>Merma <label id="lbl-por-merma"></label></td>
                                <td></td>
                                <td class="text-right">1.0000</td>
                                <td class="text-right"><?php echo $main['stock'] < 0 ? 0 : $main['stock'] ?></td>
                                <td class="text-center">
                                    <input id="cantidad-merma" type="number" value="0" class="form-control">
                                </td>
                                <td></td>
                                <td class="text-center">
                                    <button id="btn-merma" class="btn btn-success" onclick="registrarMerma()">
                                        <span class="glyphicon glyphicon-share"></span>
                                        Registrar
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>


            
        </div>
    </div>
   
    <script>

        $(document).ready(function (){
            $('#tblDetalles').DataTable()

            LoadPorcentajeMerma();
        })

        var almacen_main_id = "<?php echo $_GET['almacen_id'] ?>";
        var insumo_main_id = "<?php echo $_GET['insumo_id'] ?>";
        var insumo_porcion_main_id = "<?php echo $_GET['porcion_id'] ?>";
        var stock_main = <?php echo $main['stock'] ?? 0 ?>;

        console.log(almacen_main_id)

        function LoadPorcentajeMerma() {

            if (insumo_porcion_main_id) {
                return;
            }
            
            $.getJSON('<?php echo Class_config::get('urlApp') ?>?controller=Insumo&&action=ListId&id=' + insumo_main_id, function(data) {
                if (data[0].porcentaje_merma) {
                    $('#lbl-por-merma').html("(" + data[0].porcentaje_merma + "%)");
                    if (stock_main > 0) {
                        $('#cantidad-merma').val(data[0].porcentaje_merma * stock_main / 100);
                    }
                }
            });
        }

        function registrarTransferencia(insumo_id, insumo_porcion_id, valor) {

            if (!confirm('¿Esta seguro que desea realizar la transferencia?')) return; 

            console.log($('#tblDetalles').DataTable().rows().data().toArray())

            let cantidad = $('#cantidad-' + insumo_id + '-' + insumo_porcion_id).val();

            console.log(cantidad)

            if (!cantidad || cantidad == 0) {
                return alert('Ingrese una cantidad válida y mayor a 0');
            }

            let almacen_destino_id = $('#almacen-' + insumo_id + '-' + insumo_porcion_id).val();

            let params = {
                insumo_id: insumo_main_id,
                almacen_origen_id: almacen_main_id,
                cantidad: cantidad,
                valor: valor,
                insumo_porcion_destino_id: insumo_porcion_id,
                almacen_destino_id: almacen_destino_id,
            };

            if (insumo_porcion_id) {
                params.insumo_porcion_destino_id = insumo_porcion_id;
            }

            console.log(params)

            if (insumo_porcion_main_id) {
                params.insumo_porcion_id = insumo_porcion_main_id;
            }

            if (cantidad * valor > stock_main) {
                return alert("La cantidad que quiere transferir supera al stock actual");
            } 

            $('#btn-' + insumo_id + '-' + insumo_porcion_id).attr('disabled', true)

            $.ajax({
                url: "<?php echo Class_config::get('urlApp') ?>/?controller=AlmacenTransferencia&&action=AddTransferenciaInsumo",
                type: 'POST',
                data: params,
                dataType: 'json',
                success: function(data) {
                    
                    console.log('res', data)
                    alert('Transferencia Realizada')
                    location.reload()
                }

            });
        }

        function registrarMerma() {

            if (!confirm('¿Esta seguro que desea realizar el registro de merma?')) return; 

            let cantidad = $('#cantidad-merma').val();

            let params = {
                insumo_id: insumo_main_id,
                almacen_origen_id: almacen_main_id,
                cantidad: cantidad,
            };

            if (insumo_porcion_main_id) {
                params.insumo_porcion_id = insumo_porcion_main_id;
            }

            console.log(cantidad)

            if (!cantidad || cantidad == 0) {
                return alert('Ingrese una cantidad válida y mayor a 0');
            }

            if (cantidad > stock_main) {
                return alert("La cantidad que quiere transferir supera al stock actual");
            } 

            $('#btn-merma').attr('disabled', true)

            $.ajax({
                url: "<?php echo Class_config::get('urlApp') ?>/?controller=AlmacenTransferencia&&action=AddTransferenciaMerma",
                type: 'POST',
                data: params,
                dataType: 'json',
                success: function(data) {
                    
                    console.log('res', data)
                    alert('Merma Registrada')
                    location.reload()
                }

            });
        }
        
    </script>

</div>
</body>
</html>
