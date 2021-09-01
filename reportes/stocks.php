<?php
error_reporting(0);
$titulo_pagina = 'Consolidado Stocks';
$titulo_sistema = 'Usqay2';
require_once('recursos/componentes/header.php'); 
include_once('recursos/componentes/MasterConexion.php');
require_once('../Application/Views/Almacen/KardexHelper.php');
require_once('../Application/Models/CajaModel.php');

$conn = new MasterConexion();
$sucursal = $_GET["s"];
$datos_sucursal = $conn->consulta_arreglo("Select * from sucursal where pkSucursal = '" . $sucursal . "'");

$obj = new Application_Models_CajaModel();
$fecha = $obj->fechaCierre();

$db = new SuperDataBase();

?>
<link rel="stylesheet" href="recursos/js/plugins/tablas/jquery.dataTables.min.css">
<h1>Consolidado Stocks de Insumos</h1>
</form>
<hr/>

    <div class="row">

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
    
    </div>

<div class='contenedor-tabla'>
<table id='tb' class='display' cellspacing='0' width='100%' border="0">
<thead>
    <th>Insumo</th>
    <th></th>
    <th class="text-right">Stock Actual</th>
    <th class="text-right">Stock Min.</th>
    <th class="text-center">Ult. Actualización</th>
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
        
        // if ($item['stock'] <= 0) continue;
    ?>
    <tr>
        
        <td><?php echo $item['nombre_insumo'] ?></td>
        <td><?php echo implode(' ', [
            floatval($item['cantidad']) == 0 ? '' : floatval($item['cantidad']),
            ($item['nombre_unidad']),
            ($item['descripcion']),
        ]) ?></td>
        <td class="text-right"><?php echo $item['stock'] ?></td>
        <td class="text-right <?php echo $item['stock'] < $item['stockMinimo'] ? 'alert-danger' : '' ?>" ><?php echo floatval($item['stockMinimo']) ?></td>
        <td class="text-center">
            <?php 

                $query = "Select * from n_historial_stock_insumo where insumo_id = ${item['insumo_id']} ";

                if (array_key_exists('insumo_porcion_id', $item) && !is_null($item['insumo_porcion_id'])) {
                    $query .= " and insumo_porcion_id =  ${item['insumo_porcion_id']}";
                } else {
                    $query .= " and insumo_porcion_id is null ";
                }

                $query .= " order by fecha DESC Limit 1";

                $mov = $conn->consulta_arreglo($query);

                echo $mov["fecha"];
            ?>
        </td>
    </tr>    

<?php
endforeach;
$nombre_tabla = 'stocks';
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
    

    });

    $('#cmbAlmacen').on('change', function () {
        let val = $('#cmbAlmacen').val();

        window.location.href = "<?php echo Class_config::get('urlApp') ?>/reportes/stocks.php?almacen_id=" + val;
    })
</script>
                            
                            
                            
                            
                           