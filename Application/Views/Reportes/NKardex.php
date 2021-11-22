<?php 
include_once('reportes/recursos/componentes/MasterConexion.php');
$conn = new MasterConexion();
$obj = new Application_Models_CajaModel();

$fechaInicio = $obj->fechaCierre();

$fechaVar=$obj->fechaCierre();
$fechaFin = $obj->fechaCierre();
$fechaCierre=$obj->fechaCierre();
if (isset($_GET['fecha_inicio'])){
    $fechaInicio = $_GET['fecha_inicio'];
    $fechaVar = $fechaInicio;
}

if (isset($_GET['fecha_fin'])){
    $fechaFin = $_GET['fecha_fin'];
}

//Solucion al problema del kardex cuando se deja abierta una mesa varios dias
$tiempo_inicio = null;
$tiempo_fin = null;

$query_corte_min = "Select * from corte where fecha_cierre = '".$fechaInicio."' order by id ASC LIMIT 1";
$result_min = $conn->consulta_arreglo($query_corte_min);
if(is_array($result_min)){
    $tiempo_inicio = $result_min["inicio"];
}else{
    $tiempo_inicio = $fechaFin." 00:00:00";
}

$query_corte_max = "Select * from corte where fecha_cierre = '".$fechaFin."' order by id DESC LIMIT 1";
$result_max = $conn->consulta_arreglo($query_corte_max);
if(is_array($result_max)){
    if($result_max["fin"] !== ""){
        $tiempo_fin = $result_max["fin"];
    }else{
        $tiempo_fin = date("Y-m-d H:i:s");
    } 
}else{
    $tiempo_fin = date("Y-m-d H:i:s");
}
//echo $tiempo_inicio;

$stockAnterior = 0;
$stockIngreso = 0;
$stockSalida = 0;

$titulo_importante = "Kardex Resumen";

if($fechaInicio === $fechaFin){
    $titulo_importante = "Kardex Resumen ".$fechaInicio;
}else{
    $titulo_importante = "Kardex Resumen del ".$fechaInicio." al ".$fechaFin;
}

include 'Application/Views/template/header.php';
$objViewMenu = new Application_Views_IndexView();
$objViewMenu->showContent();

$tipos_ = null;
if (isset($_GET['tp']) && $_GET['tp'] != 0) {
    $tipos_ = $_GET['tp'];
}

$db = new SuperDataBase();   

// error_reporting(E_ALL);
require_once('Application/Views/Almacen/KardexHelper.php');

?>
<link rel="stylesheet" href="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.css">
<body>
    <style>
        .dt-buttons{
            margin-bottom: 10px !important;
        }
    </style>
    <div class="container-fluid">

        <br /><br /><br />
        <h3><?php echo $titulo_importante ?></h3>
        <div class="panel panel-primary" id='pfecha'>
            <div class="panel-heading">
                <h3 class="panel-title">Filtros por fechas</h3>
                <!-- <div class="pull-right">&time</div> -->
            </div>
            <div class="panel-body">

                <div class='control-group' id="dinicio">
                    
                    <div class="col-md-4">
                        <label>Fecha Inicio</label>
                        <input id="txtfechaini" type="text" class='form-control' placeholder='AAAA-MM-DD' name='fecha_inicio' value="<?php echo $fechaInicio ?>"/>
                    </div>
                    
                    <div class="col-md-4">
                        <label>Fecha Fin</label>
                        <input id="txtfechafin" type="text" class='form-control' placeholder='AAAA-MM-DD' name='fecha_fin' value="<?php echo $fechaFin ?>"/>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">Almacén</label>
                            <select class="form-control" name="" id="cmbAlmacen">
                                <?php 
                                    $query = "select * from n_almacen";

                                    $res = $db->executeQueryEx($query);

                                    while($row = $db->fecth_array($res)):
                                ?>
                                    <option value="<?php echo $row['id'] ?>" 
                                        <?php echo ($_GET['almacen'] == $row['id']) ? 'selected' : '' ?> >
                                        <?php echo $row['nombre'] ?>
                                    </option>    
                                <?php endwhile ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12">

                        <div class='control-group'>
                            <label>Tipo</label>    
                            <select class="form-control" id="tipo_plato" multiple="yes">
                                <option value="0">**MOSTRAR TODO**</option>
                                <?php
                                    $tipos = $conn->consulta_matriz("Select * from tipos where estado = 0");                            
                                    foreach ($tipos as $pl):
                                ?>
                                    <option value="<?php echo $pl["pkTipo"]; ?>"><?php echo $pl["descripcion"]; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class=" col-xs-12 text-center" style="margin-top: 1em">
                        <button type="button" onclick="buscar()" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Buscar</button>
                    </div>
                </div>
            </div>
        </div>
                        
        <table id="tblKardex" class="table">
            <thead>
                <th>Insumo</th>
                <th>Porción</th>
                <th class="text-right">Stock Inicial</th>
                <th class="text-right">Ingresos</th>
                <th class="text-right">Salidas</th>
                <th class="text-right">Ventas</th>
                <th class="text-right">V. por Cobrar</th>
                <th class="text-right">Stock Final</th>
                <th class="text-center">Unidad</th>
                <th></th>
            </thead>
            <tbody>
                <?php 

                $kardexHelper = new KardexHelper();

                $kardexHelper->setAlmacen($_GET['almacen'] ?? '1');
                $data_historial = $kardexHelper->getDataHistorial($fechaInicio);

                $data_movimientos = $kardexHelper->getDataMovimientosSeparados($fechaInicio, $fechaFin);
                
                $lista_completa = $kardexHelper->getListaCompleta();
                
                $data_platos_vendidos = $kardexHelper->getDataPlatosVendidos($fechaInicio, $fechaFin);
                
                $data_platos_vendidos_por_cobrar = $kardexHelper->getDataPlatosVendidosPorCobrar($fechaInicio, $fechaFin);

                $query = "select fecha from n_historial_stock_insumo order by id asc limit 1";

                $res = $db->executeQueryEx($query);
                
                $fechaInicioHistorial = date('Y-m-d');

                while ($row = $db->fecth_array($res)) {
                  $fechaInicioHistorial = $row['fecha'];
                }

                $data_platos_vendidos_por_cobrar_init = [];

                if ($fechaInicioHistorial != $fechaInicio) {
                    $data_platos_vendidos_por_cobrar_init = $kardexHelper->getDataPlatosVendidosPorCobrar($fechaInicioHistorial, $kardexHelper->getDiaAnterior($fechaInicio));
                } 

                // echo "<br>" . json_encode($data_platos_vendidos_por_cobrar_init);

                $data_platos_vendidos_cobrados_hoy = [];

                if ($fechaInicioHistorial != $fechaInicio) {
                    $data_platos_vendidos_cobrados_hoy = $kardexHelper->getDataPlatosVendidosPagadosHoy($fechaInicioHistorial, $kardexHelper->getDiaAnterior($fechaInicio));
                } 

                // echo "<br>" . json_encode($data_platos_vendidos_cobrados_hoy);

                // echo "<br>" . json_encode($data_historial);
                // echo "<br>" . json_encode($data_movimientos);
                // echo "<br>" . json_encode($data_platos_vendidos);

                $query_update_arreglar_error = "";

                foreach ($lista_completa as $item):

                    if ($tipos_) {

                        $query = "SELECT
                            *
                        FROM
                            n_receta
                        WHERE
                            insumo_id = ${item['insumo_id']}
                        AND plato_id IN (
                            SELECT
                                pkPlato
                            FROM
                                plato
                            WHERE
                                plato.pktipo IN ($tipos_)
                        )";
                        $res = $db->executeQueryEx($query);
                        if ($row = $db->fecth_array($res)) {
                        } else {
                            continue;
                        }
                    }

                    $stock_ayer = $kardexHelper->getStockPorInsumo($item, $data_historial);

                    $item['stock_ayer'] = is_null($stock_ayer) ? 0 : $stock_ayer['stock'];

                    $stock_ayer_cobrar = $kardexHelper->getStockPorInsumo($item, $data_platos_vendidos_por_cobrar_init);

                    $item['stock_ayer_cobrar'] = is_null($stock_ayer_cobrar) ? 0 : $stock_ayer_cobrar['cantidad_insumo'];

                    $item['stock_ayer'] -= $item['stock_ayer_cobrar'];

                    $stock_ventas_cobrados_hoy = $kardexHelper->getStockPorInsumo($item, $data_platos_vendidos_cobrados_hoy);

                    $item['stock_ayer_cobrado'] = (is_null($stock_ventas_cobrados_hoy) ? 0 : $stock_ventas_cobrados_hoy['cantidad_insumo']);
                    
                    $item['stock_ayer'] -= $item['stock_ayer_cobrado'];

                    $stock_movimiento = $kardexHelper->getStockPorInsumo($item, $data_movimientos);

                    $item['stock_ingresos'] = is_null($stock_movimiento) ? 0 : $stock_movimiento['stock_ingresos'];

                    $item['stock_salidas'] = is_null($stock_movimiento) ? 0 : $stock_movimiento['stock_salidas'];

                    $stock_ventas = $kardexHelper->getStockPorInsumo($item, $data_platos_vendidos);

                    $item['stock_ventas'] = is_null($stock_ventas) ? 0 : $stock_ventas['cantidad_insumo'];

                    $stock_ventas_cobrar = $kardexHelper->getStockPorInsumo($item, $data_platos_vendidos_por_cobrar);

                    $item['stock_ventas_cobrar'] = is_null($stock_ventas_cobrar) ? 0 : $stock_ventas_cobrar['cantidad_insumo'];


                    $item['stock'] = $item['stock_ayer'] + $item['stock_ingresos'] - $item['stock_salidas'] - $item['stock_ventas'] - $item['stock_ventas_cobrar'];

                    // echo json_encode($item);

                    $query_update_arreglar_error .= "
                        update n_historial_stock_insumo
                            set stock_inicial =  {$item['stock']}
                            where fecha = '$fechaCierre'
                            and insumo_id = {$item['insumo_id']}";

                    if ($item['insumo_porcion_id']) {
                        $query_update_arreglar_error .= " and insumo_porcion_id = {$item['insumo_porcion_id']} ";
                    }
                            
                    $query_update_arreglar_error .= "
                        ; 
                            <br>
                    ";

                    
                ?>
                <tr>
                    
                    <td><?php echo $item['nombre_insumo'] ?></td>
                    <td><?php echo implode(' ', [
                        floatval($item['cantidad']) == 0 ? '' : floatval($item['cantidad']),
                        ($item['nombre_unidad']),
                        ($item['descripcion']),
                    ]) ?></td>
                    <td class="text-right"><?php echo floatval($item['stock_ayer']) ?></td>
                    <td class="text-right"><?php echo floatval($item['stock_ingresos']) ?></td>
                    <td class="text-right"><?php echo floatval($item['stock_salidas']) ?></td>
                    <td class="text-right"><?php echo floatval($item['stock_ventas']) ?></td>
                    <td class="text-right"><?php echo floatval($item['stock_ventas_cobrar']) ?></td>
                    <td class="text-right <?php echo $item['stock'] < 0 ? 'alert-danger' : '' ?>"><?php echo floatval($item['stock']) ?></td>
                    <td class="text-center"><?php echo $item['insumo_porcion_id'] ? 'PORCIÓN' : $item['unidad'] ?></td>
                    <td class="text-center">
                        <a class="btn" onclick="KardexDetallado('<?php echo $item['insumo_id'] ?>', '<?php echo $item['insumo_porcion_id'] ?>', '<?php echo $item['nombre_insumo'] ?>')" title="Ver Detalles">
                            <span class='glyphicon glyphicon-log-out'></span>
                        </a>
                    </td>
                    

                </tr>    

                <?php endforeach ?>

            </tbody>
        </table>

        <br>
        <br>
        <br>
        <br>

    </div>        
    <script type="text/javascript" src="Application/Views/Reportes/js/kardex.js.php" ></script>
    <script src="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/dataTables.buttons.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/jszip.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/pdfmake.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/vfs_fonts.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/buttons.html5.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/buttons.print.min.js"></script>
    <script>
        var almacen_id = "<?php echo $_GET['almacen'] ?? '1' ?>";
    $(document).ready(function() {

        <?php
        if (isset($_GET["tp"])) {
            echo '$("#tipo_plato").val([' . $_GET["tp"] . ']);';
        }else{
            echo '$("#tipo_plato").val(0);';
        }
        ?>

        $("#txtfechaini").datepicker({dateFormat: 'yy-mm-dd', maxDate: '<?php echo $fechaCierre ?>', minDate: '<?php echo $fechaInicioHistorial ?>'});
        $("#txtfechafin").datepicker({dateFormat: 'yy-mm-dd', maxDate: '<?php echo $fechaCierre ?>', minDate: '<?php echo $fechaInicioHistorial ?>'});

        $('#tblKardex').DataTable( {
            dom: 'Blfrtip',
            // "bSort": false,
            // "bFilter": true,
            // "bInfo": true,
            "ordering": false,
            // "paging": true,
            buttons: [
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7]
                    },
                    title: '<?php echo $titulo_importante;?>'
                },
                {
                    extend: 'pdfHtml5',
                    orientation: 'landscape',
                    alignment: 'center',
                    pageSize: 'LEGAL',
                    customize: function(doc) {
                        doc.content[1].margin = [ 100, 0, 100, 0 ] //left, top, right, bottom
                    },
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7]
                    },
                    title: '<?php echo $titulo_importante;?>'
                },
                {
                    extend: 'print',
                    orientation: 'landscape',
                    pageSize: 'LEGAL',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7]
                    },
                    title: '<?php echo $titulo_importante;?>'
                }
                
            ]
        } );
    });
    </script>
</body>
