<?php 
error_reporting(E_ALL);
include 'Application/Views/template/header.php';
require_once 'reportes/recursos/componentes/MasterConexion.php'; 
$objcon = new MasterConexion();
$f = $objcon->consulta_arreglo("Select * from cierrediario where pkCierreDiario = 1");

$obj = new Application_Models_CajaModel();
$fechaCaja = $obj->fechaCierre();
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
                Rendimiento de Platos</h4>
            </div>

            <div class="panel-body">


                <br>

                <table id="tblRP" class="table">

                    <thead>
                        <th>#</th>
                        <th>Plato</th>
                        <th class="text-right">Precio Venta (S/)</th>
                        <th class="text-right">Costo Receta (S/)</th>
                        <!-- <th class="text-right">Costo Receta Merma</th> -->
                        <th class="text-right">Rendimiento (S/)</th>
                    </thead>
                
                    <tbody>

                        <?php 
                            
                            $db = new SuperDataBase();

                            $query = "SELECT
                                plato.pkPlato,
                                plato.descripcion,
                                plato.precio_venta,
                                sum(
                                    n_receta.cantidad * (
                                        (
                                            insumos.precio_promedio * (
                                                IF (
                                                    insumo_porcion.id IS NULL,
                                                    1,
                                                    insumo_porcion.valor
                                                )
                                            )
                                        ) / (
                                            1 - (
                                                IFNULL(insumos.porcentaje_merma, 0) / 100
                                            )
                                        )
                                    )
                                ) AS costo_receta,
                                sum(
                                    n_receta.cantidad * (
                                        insumos.precio_promedio * (
                            
                                            IF (
                                                insumo_porcion.id IS NULL,
                                                1,
                                                insumo_porcion.valor
                                            )
                                        )
                                    )
                                ) AS costo_receta_merma
                            FROM
                                n_receta
                            LEFT JOIN plato ON plato.pkPlato = n_receta.plato_id
                            LEFT JOIN insumos ON insumos.pkInsumo = n_receta.insumo_id
                            LEFT JOIN insumo_porcion ON insumo_porcion.insumo_id = n_receta.insumo_porcion_id
                            WHERE
                                n_receta.deleted_at IS NULL
                            AND insumo_porcion.deleted_at IS NULL
                            AND insumos.estado = 0
                            AND plato.estado = 0
                            GROUP BY
                                plato.pkPlato";

                            $res = $db->executeQueryEx($query);
                            
                            $index = 1;
                            while ($row = $db->fecth_array($res)):

                                $porcentaje = round($row['precio_venta'] - $row['costo_receta'], 4);

                        ?>
                        <tr>
                            <td><?php echo $index++ ?></td>
                            <td><?php echo $row['descripcion'] ?></td>
                            <td class="text-right"><?php echo number_format($row['precio_venta'], 2) ?></td>
                            <td class="text-right"><?php echo number_format($row['costo_receta'], 2) ?></td>
                            <!-- <td class="text-right"><?php echo round($row['costo_receta_merma'], 4) ?></td> -->
                            <td class="text-right <?php echo ($porcentaje > 0) ? 'text-success' : 'alert-danger text-danger' ?>">
                                <?php echo number_format($porcentaje, 2) ?>
                            </td>
                        </tr>

                        <?php endwhile ?> 

                    </tbody>
                </table>

            </div>
        </div>
    
    </div>

    <link rel="stylesheet" type="text/css" href="Public/select2/css/select2.css" rel="stylesheet">
    <script  type="text/javascript" src="Public/select2/js/select2.js"></script>
    <script>    
        $(document).ready(() => {

            $('#tblRP').DataTable();
        });
    </script>
</body>

</html>