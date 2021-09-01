<?php include 'Application/Views/template/header.php'; ?>
<body>
    <?php
    error_reporting(E_ALL);
    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showContent();

    $db = new SuperDataBase();

    $query = "select insumos.*, unidad.descripcion as nombre_unidad from insumos left join unidad on unidad.pkUnidad = insumos.pkUnidad where pkInsumo = " . $_GET['insumo_id'];

    $res = $db->executeQueryEx($query);

    $insumo;

    while($row = $db->fecth_array($res)) {
        $insumo = $row;
    }
    ?>

    <div class="container">
        <br>
        <br>
        <br> 
        <div class="panel panel-primary">
            <!-- <h3>Porciones</h3> -->

            <div class="panel-heading">Porciones</div>
            <div class="col-xs-12 col-md-8 col-md-offset-2 panel-body">


                <form id="frmPorcion">
                
                <!-- <input type="text" value="<?php echo $insumo['pkInsumo'] ?>" hidden> -->

                <input type="text" name="insumo_id" value="<?php echo $insumo['pkInsumo'] ?>" hidden>

                <div class=" col-xs-12 col-md-6 form-group">
                    <label for="">Insumo</label>
                    <input type="text" class="form-control" value="<?php echo $insumo['descripcionInsumo'] ?>" readonly>
                </div>

                <div class=" col-xs-12 col-md-6 form-group">
                    <label for="">Unidad Insumo</label>
                    <input type="text" class="form-control" value="<?php echo $insumo['nombre_unidad'] ?>" readonly>
                </div>

                <div class=" col-xs-12 col-md-4 form-group">
                    <label for="">Cantidad</label>
                    <input type="number" id="cantidad" name="cantidad" class="form-control">
                </div>

                <div class=" col-xs-12 col-md-4 form-group">
                    <label for="">Unidad</label>
                    <script>
                        var list_unidades = [];
                    </script>
                    <select name="unidad_id" id="unidad_id" class="form-control">
                        <?php
                            $query = "select * from unidad";

                            $res = $db->executeQueryEx($query);

                            while($row = $db->fecth_array($res)):
                        ?>
                            <script> 
                                list_unidades.push(<?php echo json_encode($row) ?>);
                            </script>
                            <option value="<?php echo $row['pkUnidad'] ?>" 
                                <?php echo $row['pkUnidad'] == $insumo['pkUnidad'] ? 'selected' : '' ?>>
                                <?php echo $row['descripcion'] ?>
                            </option>
                        <?php
                            endwhile
                        ?>
                    </select>
                </div>


                <div class=" col-xs-12 col-md-4 form-group">
                    <label for="">Valor</label>
                    <input type="number" id="valor" name="valor" class="form-control">
                </div>

                <div class=" col-xs-12 form-group">
                    <label for="">Descripción</label>
                    <input type="value" name="descripcion" class="form-control">
                </div>

                
                <div class="col-xs-12 text-center">
                    <button type="button" class="btn btn-success" onclick="save()">
                        Registrar
                    </button>
                </div>

                </form>

            </div>


            <div class="col-xs-12">
                <hr>

                <table id="tblDetail">
                    <thead>
                        <th>#</th>
                        <th class="text-right">Cantidad</th>
                        <th class="text-right">Unidad</th>
                        <th>Descripción</th>
                        <th class="text-right">Valor</th>
                        <th></th>
                    </thead>
                    <tbody> 
                    <?php 

                        $query = "SELECT
                            id,
                            unidad.descripcion AS nombre_unidad,
                            insumo_porcion.valor,
                            insumo_porcion.cantidad,
                            insumo_porcion.descripcion
                        FROM
                            insumo_porcion
                        LEFT JOIN unidad ON insumo_porcion.unidad_id = unidad.pkUnidad
                        where insumo_porcion.insumo_id = " . $_REQUEST['insumo_id'] . "
                        AND deleted_at is null";

                        $res = $db->executeQueryEx($query);

                        $index = 1;
                        
                        while ($item = $db->fecth_array($res)):

                    ?>
                        <tr>
                            
                            <td><?php echo $index++; ?></td>
                            <td class="text-right"><?php echo floatval($item['cantidad']) ?></td>
                            <td class="text-right"><?php echo $item['nombre_unidad'] ?></td>
                            <td class="text-left"><?php echo $item['descripcion'] ?></td>
                            <td class="text-right"><?php echo floatval($item['valor']) ?></td>
                            <td class="text-center">
                                <!-- <a class="btn">
                                    <span class="glyphicon glyphicon-pencil"></span>
                                </a> -->
                                <a class="btn" onclick="destroy(<?php echo $item['id'] ?>)">
                                    <span class="glyphicon glyphicon-trash"></span>
                                </a>
                            </td>
                            
                        
                        </tr>    

                    <?php endwhile ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script>
        var unidad_insumo_id = <?php echo $insumo['pkUnidad'] ?>;

        $('#tblDetail').DataTable()

        var url = "<?php echo Class_config::get('urlApp') ?>" + "/?controller=Almacen&&action=AdmAddInsumoPorcion";

        function save () {

            let params = $('#frmPorcion').serialize()

            console.log(params)

            $.ajax({
                type: "POST",
                url: url,
                data: params,
                dataType: 'json',
                success: function (data) {
                    location.reload()
                }
            });

        }

        function destroy (id) {

            if (!confirm('¿Está seguro que desea eliminar la porción?')) return;

            $.ajax({
                type: "POST",
                url: "<?php echo Class_config::get('urlApp') ?>" + "/?controller=Almacen&&action=AdmDeleteInsumoPorcion&id=" + id,
                data: {},
                dataType: 'json',
                success: function (data) {
                    location.reload()
                }
            });

        }

        $(document).ready(() => {
            $('#cantidad').change(CalculateValue);
            $('#unidad_id').change(CalculateValue);
        });


        function CalculateValue() {

            let cantidad = $('#cantidad').val();
            let unidad_id = $('#unidad_id').val();

            let unidad_insumo = list_unidades.find(i => i.pkUnidad == unidad_insumo_id);

            let unidad_porcion_insumo = list_unidades.find(i => i.pkUnidad == unidad_id);

            let valor = 1;

            if (unidad_insumo.descripcion.toLocaleUpperCase().includes("KILO") && unidad_porcion_insumo.descripcion.toLocaleUpperCase().includes("GRAMO")) {
                valor = cantidad / 1000;
            } else if (unidad_insumo.descripcion.toLocaleUpperCase().includes("LITRO") && unidad_porcion_insumo.descripcion.toLocaleUpperCase().includes("MILI")) {
                valor = cantidad / 1000;
            }


            $('#valor').val(valor);
        }
    </script>
</body>
</html>

