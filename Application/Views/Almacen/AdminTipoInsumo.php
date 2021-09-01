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
        <br>        

        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4><i class="fa fa-clipboard"></i> Administrar Tipos de Insumo</h4>
            </div>

            <div class="panel-body">

                <ul class="nav nav-tabs">
                    <li class="active"><a href="#TipoInsumoActivo" data-toggle="tab">Activos</a>
                    </li>
                    <li><a href="#TipoInsumoInactivo" data-toggle="tab">Inactivos</a>
                    </li>
                    <p class="text-right">
                        <button onclick="modalRegistrarTipoInsumo()" type="button" class="btn btn-success">
                            <span class="glyphicon glyphicon-plus-sign" aria-hidden="true" ></span> Nuevo Tipo de Insumo
                        </button>
                    </p>
                </ul>
                <br/>
                <div class="tab-content">
                    <div class="tab-pane active" id="TipoInsumoActivo" >            
                        <table id="tblTipoInsumoActivo" class="table table-borderer" >
                            <thead>
                                <tr>
                                    <th>Codigo</th>
                                    <th>Descripcion</th>        
                                    <th></th>
                                    <th></th>            
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $db = new SuperDataBase();
                                $query = "SELECT * from tipo_insumo where estado=0";
                                $result = $db->executeQuery($query);
                                while ($row = $db->fecth_array($result)) {
                                    echo "<tr class=''>";
                                    echo "<td>" . $row['pkTipoInsumo'] . "</td>";
                                    echo "<td>" . utf8_encode($row['descripcion']) . "</td>";
                                    echo "<td>";
                                    echo "<a onclick='modalEditarTipoInsumo(" . $row['pkTipoInsumo'] . ",\"" . $row['descripcion'] . "\")'><span class='glyphicon glyphicon-pencil' title='Editar Tipo Insumo'></span></a>";
                                    echo "</td>";
                                    echo "<td>";
                                    echo "<a onclick='modalEliminarTipoInsumo(" . $row['pkTipoInsumo'] . ")'><span class='glyphicon glyphicon-minus-sign' title='Eliminar Tipo Insumo'></span></a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane" id="TipoInsumoInactivo">
                        <table id="tblTipoInsumoInactivo" class="table table-borderer" >
                            <thead>
                                <tr>
                                    <th>Descripcion</th>
                                    <th></th>            
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $db = new SuperDataBase();
                                $query = "SELECT * from tipo_insumo where estado=1";
                                $result = $db->executeQuery($query);
                                while ($row = $db->fecth_array($result)) {
                                    echo "<tr class='danger'>";
                                    echo "<td>" . utf8_encode($row['descripcion']) . "</td>";
                                    echo "<td>";
                                    echo "<a onclick='modalActivarTipoInsumo(" . $row['pkTipoInsumo'] . ")'><span class='glyphicon glyphicon-ok' title='Habilitar un Tipo de Insumo'></span></a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>  
        </div>

        

        <div id="modalTipoInsumo" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><label id="tituloModalTipoInsumo"></label></h4>
                    </div>
                    <div class="modal-body">
                        <form id="formTipoInsumo">                        
                            <input name="id" id="txtIdTipoInsumo" style="display: none;"/>
                            Descripcion
                            <input required="true" type="text" name="descripcion" class="form-control" id="txtDescripcionTipoInsumo" placeholder="Ingrese la Descripcion del Tipo de Insumo " />
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" onclick="guardarTipoInsumo()">Guardar</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>

                    </div>
                </div>
            </div>
        </div>        

        <div id="modalEliminarTipoInsumo" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><label id="tituloModalEliminarTipoUnidad"></label></h4>
                    </div>
                    <div class="modal-body">
                        <form id="formEliminarTipoInsumo">                        
                            <input name="id" id="txtIdEliminarTipoUnidad" style="display: none;"/>
                            <strong id="txtMensajeeliminar"></strong>
                        </form>
                    </div>
                    <div class="modal-footer">

                        <button id="btnColor" class="btn btn-primary" onclick="deleteTipoInsumo()">Aceptar</button>

                        <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>

                    </div>
                </div>
            </div>
        </div>        
    </div>  
</body>
<script type="text/javascript" src="Application/Views/Almacen/js/AdminTipoInsumo.js.php" ></script>
<script>

                            $('#tblTipoInsumoActivo').DataTable();
                            $('#tblTipoInsumoInactivo').DataTable();

</script>