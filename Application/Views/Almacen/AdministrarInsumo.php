<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
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
    <?php
    error_reporting(E_ALL);
    $concat = "where r.deleted_at is null ";
    $value = "";
    $descripcion = "";
    if (isset($_GET['pkPlato'])) {
        $concat .= " and p.pkPlato='" . $_GET['pkPlato'] . "'";
        $value = $_GET['pkPlato'];
    }
    if (isset($_GET['descripcion'])) {

        $descripcion = $_GET['descripcion'];
    }
    ?>
    <body>
        <?php
        $objViewMenu = new Application_Views_IndexView();
        $objViewMenu->showContent();
        ?>
        <div class="container-fluid mt-12 mb-5">
            <div class="row">
                <div class="col-md-4">
                    <div class="panel">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <img src="Public/images/iconos2018/breakfast.png" class="center-block img-frm" alt="">
                                </div>
                            </div>
                            <div class="alert alert-danger alert-dismissable" style="display:none;" id="merror">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                Hubo un error, reintenta
                            </div>
                            <div class="alert alert-success alert-dismissable" style="display:none;" id="msuccess">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                Operación Completada con Éxito
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <h3 class="administrar text-center">Administrando Receta</h3>

                                    <input type="text" id="txtId" hidden>

                                    <div class="input-group input-group-lg mt-2">
                                        <span class="input-group-addon" id="basic-addon1">
                                            <img src="Public/images/iconos2018/serving-dish.png" alt="">
                                        </span>
                                        <!-- <input value="<?php echo $descripcion; ?>" id="inputPlato" type="text" class="form-control" placeholder="Plato" aria-describedby="basic-addon1">
                                        <input value="<?php echo $value; ?>" type="text" class="form-control" style="display: none" id="inputPlato-id" placeholder="Plato">
                                        <input value="" type="text" class="form-control" style="display: none" id="id"> -->
                                        <select class="form-control select2-lg" name="" id="inputPlato"></select>
                                    </div>
                                    <div class="input-group input-group-lg mt-1">
                                        <span class="input-group-addon" id="basic-addon2">
                                            <img src="Public/images/iconos2018/barbecue.png" alt="">
                                        </span>
                                        <!-- <input id="txtInsumo" type="text" type="text" class="form-control" placeholder="Insumo" aria-describedby="basic-addon2">
                                        <input id="txtInsumo-id" type="text"  style="display: none" class="form-control"  placeholder="Ingrese el insumo"> -->
                                        <select class="form-control select2-lg" name="" id="txtInsumo"></select>
                                    </div>
                                    <div class="input-group input-group-lg mt-1" id="ctntxtInsumoPorcion">
                                        <span class="input-group-addon" id="basic-addon2">
                                            <img src="Public/images/iconos2018/barbecue.png" alt="">
                                        </span>
                                        <!-- <input id="txtInsumo" type="text" type="text" class="form-control" placeholder="Insumo" aria-describedby="basic-addon2">
                                        <input id="txtInsumo-id" type="text"  style="display: none" class="form-control"  placeholder="Ingrese el insumo"> -->
                                        <select class="form-control select2-lg" name="" id="txtInsumoPorcion">
                                            <option value="">Seleccione Porción</option>
                                        </select>
                                    </div>
                                    <div class="input-group input-group-lg mt-1">
                                        <span class="input-group-addon" id="basic-addon3">
                                            <img src="Public/images/iconos2018/kilograms.png" alt="">
                                        </span>
                                        <input type="text" name="unidad" id="unidadi" readonly class="form-control" placeholder="Unidad" aria-describedby="basic-addon3">
                                    </div>
                                    <div class="input-group input-group-lg mt-1">
                                        <span class="input-group-addon" id="basic-addon4">
                                            <img src="Public/images/iconos2018/plus.png" alt="">
                                        </span>
                                        <input type="number" name="cantidad" id="cantidad" class="form-control" placeholder="Cantidad" aria-describedby="basic-addon4">
                                    </div>
                                    <div class="input-group input-group-lg mt-1">
                                        <span class="input-group-addon" id="basic-addon5">
                                            <img src="Public/images/iconos2018/almacen.svg" style="width:20px" alt="">
                                        </span>
                                        <select name="almacen_id" class="form-control" id="txtAlmacen">
                                        </select>
                                    </div>
                                    <div class="input-group input-group-lg mt-1">
                                        <span class="input-group-addon" id="basic-addon5">
                                            <img src="Public/images/iconos2018/almace1.svg" style="width:20px" alt="">
                                        </span>
                                        <select name="terminal" class="form-control" id="txtTerminal">
                                            <option value="">-- Sin Terminal --</option>
                                            <option value="01">Terminal 01</option>
                                            <option value="02">Terminal 02</option>
                                            <option value="03">Terminal 03</option>
                                        </select>
                                    </div>
                                    <div class="form-group mt-2">
                                        <button onclick="guardarInsumoMenu()" class="btn btn-primary btn-block btn-lg">Guardar</button>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                <div class="panel">
                    <div class="panel-body">

                    <?php if ($value):  ?>
                        <button class="btn btn-success" onclick="openModalSubRecetas()">
                            Agregar Sub Receta
                        </button>
                    <?php endif ?>

                    <br>
                    <br>

                    <div class="table-responsive">
                        <table id="tblInsumoMenu" class=" table display">
                        <thead>
                            <th>Id</th>
                            <th>Plato</th>
                            <th>Insumo</th>
                            <th>Insumo/Porción</th>
                            <!-- <th>Unidad</th> -->
                            <th class="text-right">Cantidad</th>
                            <th>Almacén</th>
                            <th>Terminal</th>
                            <th>SubReceta</th>
                            <th>Opciones</th>
                        </thead>
                        <tbody>
                            <?php
                            $db = new SuperDataBase();
                            // $query = "SELECT * FROM insumo_menu i inner join plato p on p.pkPlato=i.pkPlato inner join insumos ins on ins.pkInsumo=i.pkInsumo inner join n_almacen al on al.id=i.pkAlmacen " . $concat . " order by p.pkPlato";
                            $query = "
                            SELECT
                                r.id,
                                r.plato_id,
                                r.terminal,
                                p.descripcion AS plato,
                                r.insumo_id,
                                i.descripcionInsumo AS insumo,
                                r.insumo_porcion_id,
                                ip.cantidad AS insumo_porcion_cantidad,
                                u.descripcion AS insumo_porcion_unidad,
                                ip.descripcion AS insumo_porcion_descripcion,
                                r.unidad_id,
                                u1.descripcion AS unidad,
                                r.cantidad,
                                r.almacen_id,
                                a.nombre AS almacen,
                                p2.descripcion AS sub_receta
                            FROM
                                n_receta r
                            LEFT JOIN insumos i ON r.insumo_id = i.pkInsumo
                            LEFT JOIN insumo_porcion ip ON r.insumo_porcion_id = ip.id
                            LEFT JOIN unidad u ON ip.unidad_id = u.pkUnidad
                            LEFT JOIN unidad u1 ON r.unidad_id = u1.pkUnidad
                            LEFT JOIN plato p ON r.plato_id = p.pkPlato
                            LEFT JOIN n_almacen a ON r.almacen_id = a.id 
                            LEFT JOIN n_receta r2 ON r2.id = r.receta_id 
                            LEFT JOIN plato p2 ON r2.plato_id = p2.pkPlato
                            $concat ORDER BY
                            sub_receta, insumo, insumo_porcion_descripcion";

                            $result = $db->executeQueryEx($query);
                            while ($row = $db->fecth_array($result)) {
                            ?>
                                <tr>
                                    <td><?php echo $row['id'] ?></td>
                                    <td><?php echo utf8_encode($row['plato']) ?></td>
                                    <td><?php echo utf8_encode($row['insumo']) ?></td>
                                    <td><?php echo implode(' ', [
                                        (floatval($row['insumo_porcion_cantidad']) == 0 ? '-' : floatval($row['insumo_porcion_cantidad'])),
                                        $row['insumo_porcion_unidad'],
                                        $row['insumo_porcion_descripcion'],
                                    ]) ?></td>
                                    <!-- <td><?php echo utf8_encode($row['unidad']) ?></td> -->
                                    <td class="text-right"><?php echo floatval($row['cantidad']) ?></td>
                                    <td><?php echo utf8_encode($row['almacen']) ?></td>
                                    <td><?php echo $row['terminal'] ?></td>
                                    <td><?php echo $row['sub_receta'] ?></td>
                                    <td class="text-center">
                                        <a href="#" class="btn" onclick="selAI(
                                            <?php echo $row['id'] ?>,
                                            '<?php echo $row['plato_id'] ?>',
                                            <?php echo $row['insumo_id'] ?>,
                                            '<?php echo $row['insumo_porcion_id'] ?>',
                                            '<?php echo $row['cantidad'] ?>',
                                            '<?php echo $row['almacen_id'] ?>',
                                            '<?php echo $row['terminal'] ?>',

                                        )">
                                            <span class="glyphicon glyphicon-pencil"></span></a>
                                        <a href="#" class="btn" onclick="del(<?php echo $row[0] ?>)"> 
                                            <span class="glyphicon glyphicon-remove"> </span></a> </td>
                                </tr>
                                <?php
                            }
                            ?>

                        </tbody>
                </table>
                    </div>

                    
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modalAddSubRecetas" class="modal fade " tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="">Sub Recetas</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label for="">Sub Receta</label>
                            <select name="" id="cmbSubReceta" class="form-control">

                            <?php 
                                $query_motivos_anulacion = "SELECT
                                    *
                                FROM
                                    plato
                                WHERE
                                    pkPlato IN (
                                        SELECT DISTINCT
                                            (plato_id) AS plato_id
                                        FROM
                                            n_receta where plato_id != '$value'
                                    )";

                                $res_motivo = $db->executeQuery($query_motivos_anulacion);

                                while($row = $db->fecth_array($res_motivo)):
                            ?>

                                <option value="<?php echo $row['pkPlato'] ?>"><?php echo $row['descripcion'] ?></option>

                            <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label for="">Cantidad</label>
                            <input type="number" id="cantidadSubReceta" name="cantidad" value="1" class="form-control">  
                        </div>
                    </div>
                
                    <div class="col-xs-12 text-center">
                        <button class="btn btn-primary btn-lg btn-block" style="margin-top: 15px" onclick="saveSubReceta('<?php echo $value ?>')">Agregar Sub Receta</button>
                    </div>
                </div>
               
            </div>
        </div>
    </div>

    <link rel="stylesheet" type="text/css" href="Public/select2/css/select2.css" rel="stylesheet">
    <script  type="text/javascript" src="Public/select2/js/select2.js"></script>
    <script type="text/javascript" src="Application/Views/Almacen/js/administrarInsumo.js.php" ></script>
    <script>
        

        $(function() {

            $('#ctntxtInsumoPorcion').hide();

            $('#cmbSubReceta').select2({
                width: '100%',
                dropdownParent: $('#modalAddSubRecetas')
            })

            var lista_platos = [];
            var lista_insumos = [];

            $.ajax({
                url: "<?php echo Class_config::get('urlApp') ?>/?controller=Insumo&&action=List",
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    lista_insumos = data;

                    var select__ = $('#txtInsumo');

                    select__.html('<option value="">Seleccione Insumo</option>');

                    for (let i of data) {
                        select__.append(`
                            <option value="${i.id}">${i.descripcion}</option>
                        `)
                    }

                    select__.select2({
                        width: '100%',
                    });

                    select__.on('select2:select', function (e) {

                        console.log('on-insumo')
                        var data = e.params.data;

                        var item = lista_insumos.find(it => it.id == data.id);

                        $("#unidadi").val(item.unidad);

                        loadPorciones(item.id);
                    });
                }

            });
            $.ajax({
                url: "<?php echo Class_config::get('urlApp') ?>/?controller=Almacen&&action=ListAlmacen",
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    var options = [];
                    for (let item of data) {
                        options.push("<option value="+item.id+">"+item.descripcion+"</option>");
                    }

                    $('#txtAlmacen').html(options.join(''));
                }

            });
            $.ajax({
                url: "<?php echo Class_config::get('urlApp') ?>/?controller=Platos&&action=List",
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    lista_platos = data;

                    var select__ = $('#inputPlato');

                    select__.html('<option value="">Seleccione Plato</option>');

                    for (let i of data) {
                        select__.append(`
                            <option value="${i.pkPlato}">${i.descripcion}</option>
                        `)
                    }

                    select__.select2({
                        width: '100%',
                    });

                    <?php if ($value):  ?>
                        select__.val("<?php echo $value ?>");
                        select__.trigger('change'); 
                    <?php endif ?>


                    select__.on('select2:select', function (e) {
                        var data = e.params.data;

                        var item = lista_platos.find(it => it.pkPlato == data.id);

                        window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Almacen&action=AdmInsumo&pkPlato=" + item.pkPlato + "&descripcion=" + item.descripcion;
                    });
                }

            });
        });

        function loadPorciones(insumo_id, onload) {
            $.ajax({
                url: "<?php echo Class_config::get('urlApp') ?>/?controller=Almacen&&action=ListInsumoPorcion&insumo_id="+ insumo_id ,
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    lista_platos = data;

                    var select__ = $('#txtInsumoPorcion');

                    select__.html('<option value="">Seleccione Porción</option>');

                    for (let i of data) {
                        select__.append(`
                            <option value="${i.id}">${i.descripcion}</option>
                        `)
                    }

                    $('#ctntxtInsumoPorcion').hide()

                    if (data.length > 0) {
                        console.log('hide')
                        $('#ctntxtInsumoPorcion').show()
                    }

                    select__.select2({
                        width: '100%',
                    });

                    if (onload) {
                        onload();
                    }

                    select__.on('select2:select', function (e) {
                        var data = e.params.data;

                        var item = lista_platos.find(it => it.id == data.id);
                    });
                }

            });
        }

        var pkplato = "";
        function setPkPlato() {
            pkplato = "<?php echo $value ?>";
        }
        function getpkPlato() {

            return pkplato;
        }
    </script>
</div>
</body>
</html>
