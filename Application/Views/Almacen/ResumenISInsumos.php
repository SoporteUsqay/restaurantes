<?php include 'Application/Views/template/header.php'; ?>
<body>
    <?php
    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showContent();
    ?>

    <div class="container">
        <br>
        <br>
        <br> 

        <h2>Resumen de Ingreso/Salida de Insumos </h2>
        <div style="background: #000000;">
            <table class="table" style=" color: #ffffff">
                <tr>
<!--                    <td>
                        Insumo
                        <input class="form-control" type="text" id="" value="">

                    </td>-->
                    <td>
                        Fecha
                        <input class="form-control" type="text" id="dateInto" value="<?php echo date('Y-m-d') ?>">

                    </td>
                    <td>
                        Tipo
                        <select id="cmbFilterEstadoInsumo" class="form-control" onchange="loadTableSale2()">
                            <option value="0">Todo</option>
                            <option value="1">Entrada</option>
                            <option value="2">Salida</option>

                        </select>    
                    </td>

                    <td>
                        <br>
                        <!--</div>-->
                        <!--<div class="col-sm-2">-->
                        <button class="btn btn-primary" onclick=""><span class="glyphicon glyphicon-search"></span></button> 

                        <button class="btn btn-success" onclick="ventasdiariasPDF()"><span class="glyphicon glyphicon-arrow-down"></span>PDF</button> 
                        <!--<button class="btn btn-danger" onclick="EliminaVentaDia()"><span class="glyphicon glyphicon-remove"></span>Anular Venta</button>--> 

                        <!--</div>-->
                    </td>
                </tr>
            </table>
        </div>
        <table class="display table" id="tblResumenISInsumo">
            <thead>
            <th>
                ID
            </th>
            <th>
                Descripcion
            </th>
            <th>
                Insumo
            </th>
            <th>
                Cant.
            </th>
            <th>
                Tipo
            </th>
            <th>
                Fecha
            </th>
            <th>
                Ingresado por
            </th>
            </thead>
            <tbody>
                <?php
                $db = new SuperDataBase();
                $query = "SELECT *,i.cantidad as cant FROM ingresoinsumos i inner join insumos ins on ins.pkInsumo=i.pkInsumo inner join trabajador t on t.pkTrabajador=i.pkTrabajador;";
                $result = $db->executeQuery($query);
                while ($row = $db->fecth_array($result)) {
                    $tipo = "";
                    if ($row['tipo'] == "1") {
                        $tipo = "Entrada";
                    } else {
                        $tipo = "Salida";
                    }
                    ?>
                    <tr>
                        <td><?php echo $row[0]; ?></td>
                        <td><?php echo utf8_encode($row['descripcion']); ?></td>
                        <td><?php echo $row['descripcionInsumo']; ?></td>
                        <td><?php echo $row['cant']; ?></td>
                        <td><?php echo $tipo; ?></td>
                        <td><?php echo $row['fecha']; ?></td>
                        <td><?php echo utf8_encode($row['nombres']. " " .$row['apellidos']); ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
<script type="text/javascript" src="Application/Views/Almacen/js/ResumenISInsumos.js.php" ></script>
    <?php
    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showFooter();
    ?>

</body>
</html>
