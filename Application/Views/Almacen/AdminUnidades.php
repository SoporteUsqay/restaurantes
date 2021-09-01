<?php include 'Application/Views/template/header.php'; ?>
<body>

    <?php
    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showContent();
    ?>
    <!-- <h1 style="margin-top: 120px;text-align: center;margin-bottom: -50px;">Administrar Unidades</h1> -->
    <div class="container">
    
        <br>
        <br>
        <br>
        <br>        

        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4><i class="fa fa-clipboard"></i> Administrar Unidades</h4>
            </div>

            <div class="panel-body">

                <ul class="nav nav-tabs">
                    <li class="active"><a href="#UnidadActivo" data-toggle="tab">Activos</a>
                    </li>
                    <li><a href="#UnidadInactivo" data-toggle="tab">Inactivos</a>
                    </li>
                    <p class="text-right">
                    <button onclick="modalRegistrarUnidades()" type="button" class="btn btn-success" id="btnGuardarPagoDiario">
                        <span class="glyphicon glyphicon-plus-sign" aria-hidden="true" ></span> Nueva Unidad
                    </button>
                </p>
                </ul>
                <br/>
                <div class="tab-content">
                    <div class="tab-pane active" id="UnidadActivo" >            
                        <table id="tblUnidadActivo" class="table table-borderer" >
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
                                $query = "SELECT * from unidad where estado=0";
                                $result = $db->executeQuery($query);
                                while ($row = $db->fecth_array($result)) {
                                    echo "<tr class=''>";
                                    echo "<td>" . $row['pkUnidad'] . "</td>";
                                    echo "<td>" . utf8_encode($row['descripcion']) . "</td>";
                                    echo "<td>";
                                    echo "<a onclick='modalEditarUnidad(".$row['pkUnidad'].",\"".$row['descripcion']."\")'><span class='glyphicon glyphicon-pencil' title='Editar Unidad'></span></a>";
                                    echo "</td>";
                                    echo "<td>";
                                    echo "<a onclick='modalEliminarUnidad(".$row['pkUnidad'].")'><span class='glyphicon glyphicon-minus-sign' title='Eliminar Unidad'></span></a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane" id="UnidadInactivo">
                        <table id="tblUnidadInactivo" title="Tipos de Platos y Productos" class="table table-borderer" >
                            <thead>
                                <tr>
                                    <th>Descripcion</th>
                                    <th></th>            
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $db = new SuperDataBase();
                                $query = "SELECT * from unidad where estado=1";
                                $result = $db->executeQuery($query);
                                while ($row = $db->fecth_array($result)) {
                                    echo "<tr class='danger'>";
                                    echo "<td>" . utf8_encode($row['descripcion']) . "</td>";
                                    echo "<td>";
                                    echo "<a onclick='modalActivarUnidad(".$row['pkUnidad'].")'><span class='glyphicon glyphicon-ok' title='Habilitar una Unidad'></span></a>";
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

        

        <div id="modalUnidades" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><label id="tituloModalUnidades"></label></h4>
                    </div>
                    <div class="modal-body">
                        <form id="formUnidad">                        
                            <input name="id" id="txtIdUnidad" style="display: none;"/>
                            Descripcion
                            <input required="true" type="text" name="descripcion" class="form-control" id="txtDescripcionUnidad" placeholder="Ingrese la Descripcion de la Unidad " />
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" onclick="guardarUnidad()">Guardar</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>

                    </div>
                </div>
            </div>
        </div>        

        <div id="modalEliminarUnidad" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><label id="tituloModalEliminarUnidad"></label></h4>
                    </div>
                    <div class="modal-body">
                        <form id="formTipo2">                        
                            <input name="id" id="txtIdEliminarUnidad" style="display: none;"/>
                            <strong id="txtMensajeeliminar"></strong>
                        </form>
                    </div>
                    <div class="modal-footer">

                        <button id="btnColor" class="btn btn-primary" onclick="deleteUnidad()">Aceptar</button>

                        <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>

                    </div>
                </div>
            </div>
        </div>        
    </div>  
</body>
<script type="text/javascript" src="Application/Views/Almacen/js/AdminUnidades.js.php" ></script>
<script>
    
    $('#tblUnidadActivo').DataTable(); 
    $('#tblUnidadInactivo').DataTable(); 
    
</script>