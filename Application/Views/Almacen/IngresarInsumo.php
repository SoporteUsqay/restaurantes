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
        <div>
            <!-- <h2>Insumos </h2> -->
            <div class="alert alert-danger alert-dismissable" style="display:none;" id="merror">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                Hubo un error, reintenta
            </div>
            <div class="alert alert-success alert-dismissable" style="display:none;" id="msuccess">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                Operación Completada con Éxito
            </div>

            <div class="panel-body">
                <div class="col-md-12">

                    <h3 class="administrar text-center">Insumos</h3>

                    <form id="frmInsumo">

                        <input name="id" id="id" class="hidden">

                        <div class="input-group input-group-lg mt-2">
                            <span class="input-group-addon" id="basic-addon1">
                                <img src="Public/images/iconos2018/breakfast2.png" alt="">
                            </span>
                            <input id="descripcion" required="true" class="form-control" name="descripcion" placeholder="Descripción" autofocus>
                        </div>
                        <div class="input-group input-group-lg mt-2">
                            <span class="input-group-addon" id="basic-addon1">
                                <img src="Public/images/iconos2018/breakfast2.png" alt="">
                            </span>
                            <select id="cmbInsumo" required="true" class="form-control" name="pkInsumo" placeholder="Unidad">
                            </select>
                        </div>
                        <div class="input-group input-group-lg mt-2">
                            <span class="input-group-addon" id="basic-addon1">
                                <img src="Public/images/iconos2018/breakfast2.png" alt="">
                            </span>
                            <select required="true" id="cmbTipoInsumo" class="form-control" name="pkTipoInsumo"></select>
                            <span class="input-group-addon" id="basic-addon1" onclick="showNuevoTipoInsumo()">
                                    <span class="glyphicon glyphicon-plus"></span>
                            </span>
                        </div>
                        <div class="input-group input-group-lg mt-2">
                            <span class="input-group-addon" id="basic-addon1">
                                <img src="Public/images/iconos2018/breakfast2.png" alt="">
                            </span>
                            <input id="precio_promedio" required="true" class="form-control" name="precio" placeholder="Precio de Compra">
                        </div>
                        <div class="input-group input-group-lg mt-2">
                            <span class="input-group-addon" id="basic-addon1">
                                <img src="Public/images/iconos2018/breakfast2.png" alt="">
                            </span>
                            <input id="stockMinimo" type="number" required="true" class="form-control" name="stockMinimo" placeholder="Stock Mínimo">
                        </div>
                        <div class="input-group input-group-lg mt-2">
                            <span class="input-group-addon" id="basic-addon1">
                                <img src="Public/images/iconos2018/breakfast2.png" alt="">
                            </span>
                            <select required="true"class="form-control" name="provedor" id="cmbProvedor">
                            </select>
                            <span class="input-group-addon" id="basic-addon1" onclick="showNuevoProveedor()">
                                    <span class="glyphicon glyphicon-plus"></span>
                            </span>
                        </div>

                        <div style="display:none;">
                            <select required="true"class="form-control" name="estado" id="estado">
                                <option value="0" selected>Habilitado</option>
                            </select>
                        </div>

                        <div class="input-group input-group-lg mt-2">
                            <span class="input-group-addon" id="basic-addon1">
                                <img src="Public/images/iconos2018/breakfast2.png" alt="">
                            </span>
                            <input id="porcentajeMerma" type="number"   class="form-control" name="porcentajeMerma" placeholder="% Merma / Unidad">
                        </div>


                    </form>
                </div>


                <br>
                <br>
                <br>

                <div class="text-center">
                    <button onclick="guardarInsumo()"   class="btn btn-lg btn-primary"><span class="glyphicon glyphicon-floppy-save" aria-hidden="true"></span> Guardar</button>
                    <button type="reset" class="btn btn-default"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Limpiar</button>
                </div>
            </div>

            <!--            <div id="alertSucces" class="alert alert-success fade">
                            <button type="button" class="close" data-dismiss="alert">&times;</button><strong>Bien!</strong> Se ha registrado correctamente</div>
                        <div id="alertError" class="alert alert-danger fade"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Error</strong>, ha ocurrido un error inesperado</div>-->
            <p></p>
            
            <div class="panel-body">

            <table class="display table" id="tblInsumos">
                <thead>
                <th>
                    Id
                </th>
                <th>
                    Descripcion
                </th>
                <th>
                    Presentacion
                </th>
                <th>
                    Tipo Insumo
                </th>
                <th>
                    Precio
                </th>
                <th>
                    Stock minimo
                </th>
                <th>
                    % Merma
                </th>
                <th>
                    Proveedor
                </th>
                <th>
                    Opciones
                </th>

                </thead>
                <tbody>


                    <?php
                    $db = new SuperDataBase();
//                    $query = "SELECT *, case when pkProvedor>0 then (select razon from provedor p where p.pkProvedor=i.pkProvedor ) end  as provedor, descripcion as unidadDescripcion
//                        FROM insumos i inner join tipo_insumo ti on ti.pkTipoInsumo=i.pkTipoInsumo inner join unidad u on u.pkUnidad = i.pkUnidad;";

                    $query = "SELECT i.pkInsumo, i.descripcionInsumo, i.pkTipoInsumo, i.precio_promedio,
i.pkProvedor, p.razon, i.pkSucursal, i.cantidad, i.pkUnidad, u.descripcion as unidad,
i.estado, i.cantidadParcial, i.stockMinimo, i.porcentaje_merma, ti.pkTipoInsumo, ti.descripcion as tipoInsumo
FROM insumos i inner join tipo_insumo ti on ti.pkTipoInsumo=i.pkTipoInsumo
inner join unidad u on u.pkUnidad = i.pkUnidad
inner join provedor p on i.pkProvedor = p.pkProvedor where i.estado = 0;";
//                   die($query);   
                    $result = $db->executeQuery($query);
                    while ($row = $db->fecth_array($result)) {
                        $presentacion = "";
                        $class = "";
                        $estado = "";
                        $accion = "";
                        if ($row['estado'] == "1") {
                            $class = "danger";
                            $estado = "Inhabilitado";
                            $grilla = "<a class='btn' href='#' onclick='active($row[0])'><span class='glyphicon glyphicon-check' aria-hidden='true'></span></a>";
                        } else {
                            $estado = "Habilitado";
                            $grilla = "<a class='btn' href='#' onclick='del($row[0])'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></a>";
                        }
                        ?>
                        <tr class="<?php echo $class; ?>">
                            <td><?php echo $row[0]; ?>
                            </td>
                            <td>
                                <?php echo utf8_encode($row['descripcionInsumo']); ?>
                            </td>
                            <td>
                                <?php echo utf8_encode($row['unidad']); ?>
                            </td>
                            <td>
                                <?php echo utf8_encode($row['tipoInsumo']); ?>
                            </td>
                            <td>
                                <?php echo $row['precio_promedio']; ?>
                            </td>
                            <td class="text-right">
                                <?php echo $row['stockMinimo']; ?>
                            </td>
                            <td class="text-right">
                                <?php echo floatval($row['porcentaje_merma']) ?>
                            </td>
                            <td>
                                <?php echo $row['razon']; ?>
                            </td>
                            <td>
                                <a class="btn" href="#" onclick=" goToPorciones(<?php echo $row[0]; ?>)"><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span></a>
                                <a class="btn" href="#" onclick=" sel(<?php echo $row[0]; ?>)"><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a>
                                <?php echo $grilla; ?>
    <!--                                <a href="#" onclick="del(<?php echo $row[0]; ?>)"><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></a>-->
                            </td>
                        </tr>
                    <?php }
                    ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>


    <div id="modalNewTipoInsumo" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><label id="tituloModalGuia">Registrar Tipo de Insumo</label></h4>
                </div>
                <form id="formNewTipoInsumo" onsubmit="guardarTipoInsumo()" method="POST">                        
                <div class="modal-body">
                        <div class="form-group">
                            <label for="">Descripción</label>
                            <input name="descripcion" class="form-control" required="true">
                        </div>
                </div>
                <div class="modal-footer">

                    <button id="boton_guardar" class="btn btn-primary">Guardar</button>

                    <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>

                </div>
                </form>
            </div>
        </div>
    </div>

    <div id="modalNewProveedor" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><label id="tituloModalGuia">Registrar Proveedor</label></h4>
                </div>
                <form id="formNewProveedor" onsubmit="guardarProveedor()" method="POST">                        
                <div class="modal-body">
                        <div class="form-group">
                            <label for="">RUC</label>
                            <input name="ruc" class="form-control" required="true">
                        </div>

                        <div class="form-group">
                            <label for="">Razón Social</label>
                            <input name="razon" class="form-control" required="true">
                        </div>

                        <div class="form-group">
                            <label for="">Dirección</label>
                            <input name="direccion" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="">Telefono</label>
                            <input name="telefono" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="">Pagina Web</label>
                            <input name="pagweb" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="">E-mail</label>
                            <input name="email" class="form-control">
                        </div>
                </div>
                <div class="modal-footer">

                    <button id="boton_guardar" class="btn btn-primary">Guardar</button>

                    <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>

                </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="Application/Views/Almacen/js/IngresarInsumo.js.php"></script>
    <link rel="stylesheet" href="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.css">
    <script src="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/dataTables.buttons.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/jszip.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/pdfmake.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/vfs_fonts.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/buttons.html5.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/buttons.print.min.js"></script>
    
    <script>

        //    $('#tblInsumos').DataTable();


        function goToPorciones(insumo_id) {
            console.log('insumo', insumo_id)

            window.location.href= "<?php echo Class_config::get('urlApp') ?>" + "/?controller=Almacen&&action=AdmInsumoPorcion&insumo_id=" + insumo_id;
        }

        function showNuevoTipoInsumo() {
            $('#modalNewTipoInsumo').modal('show')
        }

        function guardarTipoInsumo() {
            
            $('#modalNewTipoInsumo').modal('hide');
            $.post("<?php echo class_config::get('urlApp') ?>/?controller=Almacen&action=SaveTipoInsumo", $('#formNewTipoInsumo').serialize(),
            function() {
                alert('Tipo de Insumo registrado');
                location.reload();
            });
            
            return false;  
        }

        function showNuevoProveedor() {
            $('#modalNewProveedor').modal('show')
        }

        function guardarProveedor() {
            
            $('#modalNewProveedor').modal('hide');
            $.post("<?php echo class_config::get('urlApp') ?>/?controller=Proveedor&action=Save", $('#formNewProveedor').serialize(),
            function() {
                alert('Proveedor registrado');
                location.reload();
            });
            
            return false;  
        }
    </script>

</body>
</html>

