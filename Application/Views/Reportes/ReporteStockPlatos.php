<?php 
include 'Application/Views/template/header.php';
require_once('Application/Views/Almacen/KardexHelper.php');

error_reporting(E_ALL);
?>

<body>

    <?php
    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showContent();
    $obj = new Application_Models_CajaModel();
    $fecha = $obj->fechaCierre();

    $db = new SuperDataBase();

    $query = "select * from n_almacen";

    $res = $db->executeQueryEx($query);

    $por_almacenes = [];

    $kardexHelper = new KardexHelper();

    while ($row = $db->fecth_array($res)) {

        $kardexHelper->setAlmacen($row['id']);
        
        $list = $kardexHelper->getStocksFinal($fecha);

        $por_almacenes[] = $list;

    } 

    // echo json_encode($por_almacenes);

    ?>   

    <div class="container">

        <br /><br/><br/>
    <div class="panel panel-primary">

    <div class="panel-heading">
        <h4> 
        <i class="fa fa-clipboard"></i>
        Reporte de Stocks por Platos</h4>
    </div>

    <div class="panel-body">

        Este se reporte se basa en calcular el stock de insumos registrados, en comparativa con la receta,
        es de alli que sale la cantidad de platos del menu que pueden venderse. (Solo aparecen los platos que tengan receta)
        <br>Fecha Actual - <strong><?php $obj=new Application_Models_CajaModel();
        echo $obj->fechaCierre();?></strong>
        <br>
        <br>
        <a href="" class="btn btn-primary">Actualizar</a>
        <br>
        <br>
        <!--<br>-->
        <table id="tblReporteStockPlatoa"  class="table display">
            <thead>

            <th>Plato</th>
            <th class="text-right">Cantidad que se Pueden Preparar</th>
            <th class="text-center">Estado</th>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM plato where pkPlato in (select DISTINCT(plato_id) from n_receta where deleted_at is null );";
                $result = $db->executeQueryEx($query);
                while ($row = $db->fecth_array($result)) {
                    ?>
                    <tr>

                        <td><?php echo $row['descripcion'] ?></td>
                        <td class="text-right" width="25%"><?php
                            
                            $query = "select * from n_receta where plato_id = '${row['pkPlato']}' and deleted_at is null";

                            $res = $db->executeQueryEx($query);

                            $valor = 999999999999;
                            while ($row1 = $db->fecth_array($res)) {

                                if (is_null($row1['insumo_porcion_id'])) {
                                    unset($row1['insumo_porcion_id']);
                                }

                                $s = 0;

                                foreach ($por_almacenes as $it) {

                                    $stock = $kardexHelper->getStockPorInsumo($row1, $it);

                                    $s += is_null($stock) ? 0 : $stock['stock'];
                                }

                                $s = floatval($s / $row1['cantidad']);
                                
                                // echo $s;
                                // echo "-";

                                if ($s < $valor) {
                                    $valor = $s;
                                }
                            } 

                            if ($valor < 0) {
                                $valor = 0;
                            }

                            echo floatval($valor);
                            ?></td>
                        <td class="text-center"><?php
                            $valor = (float) $valor;
                            $stockMinimo = 10;

                            if ($valor == 0) {
                                ?>
                                <img src="Public/images/mal.png">
                                <?php
                            } else
                            if ($valor > 0 && $valor < $stockMinimo) {
                                ?>
                                <img src="Public/images/estable.png">
                                <?php
                            } else if ($valor > $stockMinimo) {
                                ?>
                                <img src="Public/images/ok.png">
                                <?php
                            }
                            ?>

                        </td>
                    </tr>                

                <?php }
                ?>
                
            </tbody>

        </table>

    </div>

</div>        

    <script type="text/javascript" src="Application/Views/Reportes/js/ReporteStockPlatos.js.php" ></script>
</body>