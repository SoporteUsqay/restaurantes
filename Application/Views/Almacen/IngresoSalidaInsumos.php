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
        <h2>Registro de Ingreso/Salida de Insumos </h2>
        <div class="alert alert-danger alert-dismissable" style="display:none;" id="merror">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            Hubo un error, reintenta
        </div>
        <div class="alert alert-success alert-dismissable" style="display:none;" id="msuccess">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            Operación Completada con Éxito
        </div>
        <form id="frmInsumo">
            <label>Insumo</label>
            <input required="true" readonly="true" id="id" name="id" class="form-control" style="display: none">    
            <input class="form-control" id="descripcion">    
            <label>Cantidad</label>
            <input required="true" min="0" name="cantidad" type="number" class="form-control">    
            <label>Descripcion</label>
            <textarea required="true" name="descripcion" type="text" class="form-control"> </textarea>   
        </form>
        <button onclick="guardarInsumoEntrada(1)" class="btn btn-primary">Agregar</button>
        <button id="btnQuitar" onclick="guardarInsumoEntrada(2)" class="btn btn-danger">Quitar</button>

        <table class="table" id="tblInsumo">
            <thead>
            <th></th>
            <th>Id</th>
            <th>Descripción</th>
            <th>Cantidad</th>

            </thead>
            <tbody>
                <?php
                $db = new SuperDataBase();
                $query = "SELECT * FROM insumos i order by descripcionInsumo;";
                $result = $db->executeQuery($query);
                while ($row = $db->fecth_array($result)) {
                    ?>
                    <tr>
                        <td>
                            <a href="#" onclick="listar(<?php echo $row[0] ?>)">Seleccionar </a> 
                        </td>
                        <td>
                            <?php echo $row[0] ?>
                        </td>
                        <td>
                            <?php echo utf8_encode($row['descripcionInsumo']) ?>
                        </td>
                        <td>
                            <?php echo $row['cantidad'] ?>
                        </td>

                    </tr>
                <?php } ?>

            </tbody>
        </table>
    </div>
    <script type="text/javascript" src="Application/Views/Almacen/js/IngresoSalidaInsumo.js.php" ></script>
    <?php
    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showFooter();
    ?>

</body>
</html>
