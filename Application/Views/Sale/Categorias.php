<?php include 'Application/Views/template/header.php'; ?>
<body>

    <?php
    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showContent();
    ?>    

    <div class="container">

        <br><br><br><br>

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4><i class="glyphicon glyphicon-list-alt"></i> Categoria</h4>
                    </div>
                    <div class="panel-body">
                        <fieldset>
                            <br>
                            <table id="tbl_categoria" title="Categoria" class="table table-borderer" >
                                <thead>
                                    <tr>
                                        <th data-options="field:'pkCategoria',hidden:'true'">Código</th>
                                        <th field="descripcion" >Descripción</th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $db = new SuperDataBase();
                                    $query = "SELECT pkCategoria, UPPER(descripcion) as descripcion FROM categoria WHERE pkSucursal = '" . UserLogin::get_pkSucursal() . "';";
                                    $result = $db->executeQuery($query);
                                    while ($row = $db->fecth_array($result)) {
                                        echo "<tr>";
                                        echo "<td>" . $row['pkCategoria'] . "</td>";
                                        echo "<td>" . utf8_encode($row['descripcion']) . "</td>";
                                        echo "<td>";
                                        echo "<a onclick='editarCategoria(" . $row['pkCategoria'] . ",\"" . $row['descripcion'] . "\")'><span class='glyphicon glyphicon-pencil'></span></a>";
                                        echo "</td>";
                                        echo "<td>";
                                        echo "<a onclick='eliminarCategoria(" . $row['pkCategoria'] . ")'><span class='glyphicon glyphicon-remove'></span></a>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>

                        </fieldset>                        
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->

        <div class="text-right">
            <button onclick="registrarCategoria()" type="button" class="btn btn-success">
                <span class="glyphicon glyphicon-plus-sign" aria-hidden="true" ></span> Nuevo
            </button>
        </div>

        <div id="mdl_categoria" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title"><label id="ttl_mdl_categoria"></label></h4>
                    </div>
                    <div class="modal-body">
                        <form id="frm_categoria">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <input name="id" id="txt_id_categoria" style="display: none;"/>
                                        <label>Categoria</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><span class="glyphicon glyphicon-list-alt"></span></span>
                                            <input name="descripcion" id="txt_descripcion" class="form-control" placeholder="Categoria"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <div id="dlg-buttonsCancelarCuenta">
                            <button id="btn_registrar_categoria" class="btn btn-primary" onclick="saveCategoria()">Guardar</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>
                        </div>                            
                    </div>
                </div>
            </div>
        </div>

        <div id="mdl_eliminar_categoria" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><label id="ttl_mdl_categoria2"></label></h4>
                    </div>
                    <div class="modal-body">
                        <input name="id2" id="txt_id_categoria2" style="display: none;"/>
                        <p>¿Seguro que quieres eliminar esto?</p>
                    </div>
                    <div class="modal-footer">
                        <div id="dlg-buttonsCancelarCuenta">
                            <button class="btn btn-danger" onclick="deleteCategoria()">Aceptar</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>
                        </div>                            
                    </div>
                </div>
            </div>
        </div>

    </div>

</body>

<script type="text/javascript" src="Application/Views/Sale/js/Categorias.js.php" ></script>

<script>
                                var url = "";
                                $(document).ready(function() {
                                    $('#tbl_categoria').DataTable();
                                });

                                function editarCategoria($id, $descripcion) {
                                    $('#mdl_categoria').modal('show');
                                    $('#ttl_mdl_categoria').html('Editar categoria');
                                    $('#txt_id_categoria').val($id);
                                    $('#txt_descripcion').val($descripcion);
                                    url = "<?php echo class_config::get('urlApp') ?>/?controller=Categoria&action=Edit";
                                }

                                function registrarCategoria() {
                                    $('#mdl_categoria').modal('show')
                                    $('#ttl_mdl_categoria').html('Registrar categoria');
                                    $('#txt_id_categoria').val("");
                                    $('#txt_descripcion').val("");
                                    url = "<?php echo class_config::get('urlApp') ?>/?controller=Categoria&action=Save";
                                }

                                function eliminarCategoria($id) {
                                    $('#mdl_eliminar_categoria').modal('show');
                                    $('#ttl_mdl_categoria2').html('Eliminar categoria');
                                    $('#txt_id_categoria').val($id);
                                    url = "<?php echo class_config::get('urlApp') ?>/?controller=Categoria&action=Delete";
                                }

                                function saveCategoria() {
                                    $.post(url, $('#frm_categoria').serialize(),
                                            function(data) {
                                                location.reload();
                                            });
                                    $('#mdl_categoria').modal('hide');
                                }

                                function deleteCategoria() {
                                    $.post(url, {id: $('#txt_id_categoria').val()},
                                    function(data) {
                                        location.reload();
                                    });
                                    $('#mdl_eliminar_categoria').modal('hide');
                                }

</script>