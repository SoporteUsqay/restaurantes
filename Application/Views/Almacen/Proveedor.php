<?php include 'Application/Views/template/header.php'; ?>
<body>

    <?php
    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showContent();
    ?>
    <!-- <h1 style="margin-top: 120px;text-align: center;margin-bottom: -50px;">Administrar Proveedores</h1> -->
    <div class="container">

        <br>
        <br>
        <br>
        <br>     

        <div class="panel panel-primary">
        
            <div class="panel-heading">

                <h3><i class="fa fa-clipboard"></i> Administrar Proveedores</h3>
            </div>

            <div class="panel-body">

                <ul class="nav nav-tabs">
                    <li class="active"><a href="#ProveedorActivo" data-toggle="tab">Activos</a>
                    </li>
                    <li><a href="#ProveedorInactivo" data-toggle="tab">Inactivos</a>
                    </li>
                    <p class="text-right">
                        <button onclick="modalRegistrarProveedor()" type="button" class="btn btn-success"id="btnGuardarProveedor">
                            <span class="glyphicon glyphicon-plus-sign" aria-hidden="true" ></span> Nuevo Proveedor
                        </button>
                    </p>
                </ul>
                <br/>
                <div class="tab-content">
                    <div class="tab-pane active" id="ProveedorActivo" >            
                        <table id="tblProveedorActivo" title="Proveedores de la Empresa" class="table table-borderer" >
                            <thead>
                                <tr>
                                    <th style="display: none">Código</th>
                                    <th>N° RUC</th>
                                    <th>Razón Social</th>
                                    <th style="display: none">Direccion</th>
                                    <th>Telefono</th>
                                    <th style="display: none">Pag. Web</th>
                                    <th style="display: none">Mail</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>            
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $db = new SuperDataBase();
                                $query = "SELECT pkProvedor, ruc, razon, direccion, telefono, pagweb, mail FROM provedor WHERE estado = 0;";
                                $result = $db->executeQuery($query);
                                while ($row = $db->fecth_array($result)) {
                                    echo "<tr class=''>";
                                    echo "<td style='display: none'>" . $row['pkProvedor'] . "</td>";
                                    echo "<td>" . utf8_encode($row['ruc']) . "</td>";
                                    echo "<td>" . utf8_encode($row['razon']) . "</td>";
                                    echo "<td style='display: none'>" . utf8_encode($row['direccion']) . "</td>";
                                    echo "<td>" . utf8_encode($row['telefono']) . "</td>";
                                    echo "<td style='display: none'>" . utf8_encode($row['pagweb']) . "</td>";
                                    echo "<td style='display: none'>" . utf8_encode($row['mail']) . "</td>";
                                    echo "<td>";
                                    echo "<a onclick='modalVerProveedor(\"" . $row['pkProvedor'] . "\",\"" . $row['ruc'] . "\",\"" . $row['razon'] . "\",\"" . $row['direccion'] . "\",\"" . $row['telefono'] . "\",\"" . $row['pagweb'] . "\",\"" . $row['mail'] . "\")' title='Ver Detalles de Proveedor'><span class='glyphicon glyphicon-info-sign'></span></a>";
                                    echo "</td>";
                                    echo "<td>";
                                    echo "<a onclick='modalEditarProveedor(\"" . $row['pkProvedor'] . "\",\"" . $row['ruc'] . "\",\"" . $row['razon'] . "\",\"" . $row['direccion'] . "\",\"" . $row['telefono'] . "\",\"" . $row['pagweb'] . "\",\"" . $row['mail'] . "\")' title='Editar Proveedor'><span class='glyphicon glyphicon-pencil'></span></a>";
                                    echo "</td>";
                                    echo "<td>";
                                    echo "<a onclick='modalEliminarProveedor(\"" . $row['pkProvedor'] . "\")' title='Eliminar Proveedor'><span class='glyphicon glyphicon-remove'></span></a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane" id="ProveedorInactivo">
                        <table id="tblProveedorInactivo" title="Proveedores de la Empresa" class="table table-borderer" >
                            <thead>
                                <tr>
                                    <th style="display: none">Código</th>
                                    <th>N° RUC</th>
                                    <th>Razón Social</th>
                                    <th style="display: none">Direccion</th>
                                    <th>Telefono</th>
                                    <th style="display: none">Pag. Web</th>
                                    <th style="display: none">Mail</th>
                                    <th></th>
                                    <th></th>            
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $db = new SuperDataBase();
                                $query = "SELECT pkProvedor, ruc, razon, direccion, telefono, pagweb, mail FROM provedor WHERE estado = 1;";
                                $result = $db->executeQuery($query);
                                while ($row = $db->fecth_array($result)) {
                                    echo "<tr class='danger'>";
                                    echo "<td style='display: none'>" . $row['pkProvedor'] . "</td>";
                                    echo "<td>" . utf8_encode($row['ruc']) . "</td>";
                                    echo "<td>" . utf8_encode($row['razon']) . "</td>";
                                    echo "<td style='display: none'>" . utf8_encode($row['direccion']) . "</td>";
                                    echo "<td>" . utf8_encode($row['telefono']) . "</td>";
                                    echo "<td style='display: none'>" . utf8_encode($row['pagweb']) . "</td>";
                                    echo "<td style='display: none'>" . utf8_encode($row['mail']) . "</td>";
                                    echo "<td>";
                                    echo "<a onclick='modalVerProveedor(\"" . $row['pkProvedor'] . "\",\"" . $row['ruc'] . "\",\"" . $row['razon'] . "\",\"" . $row['direccion'] . "\",\"" . $row['telefono'] . "\",\"" . $row['pagweb'] . "\",\"" . $row['mail'] . "\")' title='Ver Detalles de Proveedor'><span class='glyphicon glyphicon-info-sign'></span></a>";
                                    echo "</td>";
                                    echo "<td>";
                                    echo "<a onclick='modalActivarProveedor(\"" . $row['pkProvedor'] . "\")' title='Habilitar Proveedor'><span class='glyphicon glyphicon-ok'></span></a>";
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

        


        <div id="modalProveedor" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><label id="tituloModalProveedor"></label></h4>
                    </div>
                    <div class="modal-body">
                        <form id="formProveedor">                        
                            <input name="id" id="txtIdProveedor" style="display: none;"/>
                            N° RUC
                            <input required="true" type="number" min="11" name="ruc" class="form-control" id="txtRuc" placeholder="Ingrese RUC del Proveedor" />
                            <br/>
                            Razón Social
                            <input required="true" type="text" name="razon" class="form-control" id="txtRazon" placeholder="Ingrese Razon Social del Proveedor" />
                            <br/>
                            Dirección
                            <input required="true" type="text" name="direccion" class="form-control" id="txtDireccion" placeholder="Ingrese Dirección del Proveedor" />
                            <br/>
                            Teléfono
                            <input required="true" type="number" min="7" max="11" name="telefono" class="form-control" id="txtTelefono" placeholder="Ingrese Teléfono del Proveedor" />
                            <br/>
                            Página Web  
                            <input required="true" type="text" name="pagweb" class="form-control" id="txtPagWeb" placeholder="Ingrese Dirección del Proveedor" />
                            <br/>
                            E-Mail
                            <input required="true" type="email" name="mail" class="form-control" id="txtMail" placeholder="Ingrese Dirección del Proveedor" />
                        </form>
                    </div>
                    <div class="modal-footer">

                        <button class="btn btn-primary" onclick="guardarProveedor()">Guardar</button>

                        <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>

                    </div>
                </div>
            </div>
        </div>         

        <div id="modalProveedor2" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><label id="tituloModalProveedor2"></label></h4>
                    </div>
                    <div class="modal-body">
                        <form id="formProveedor2">                        
                            <input name="id" id="txtIdProveedor2" style="display: none;"/>
                            <strong id="txtMensajeeliminar"></strong>
                        </form>
                    </div>
                    <div class="modal-footer">

                        <button id="btnColor" class="btn btn-primary" onclick="deleteProveedor()">Aceptar</button>

                        <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>

                    </div>
                </div>
            </div>
        </div> 
        
         <div id="modalVerProveedor" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><label id="tituloModalVerProveedor"></label></h4>
                    </div>
                    <div class="modal-body">
                        <form id="formVerProveedor">                        
                            <input name="id" id="txtIdProveedor2" style="display: none;"/>
                            N° RUC                                 
                            <input type="text" class="form-control" id="txtRuc2" placeholder="El RUC del Proveedor no está registrado" disabled/>
                            <br/>
                            Razón Social
                            <input type="text" class="form-control" id="txtRazon2" placeholder="La Razón Social no ha sido asignada" disabled/>
                            <br/>
                            Dirección
                            <input type="text" class="form-control" id="txtDireccion2" placeholder="No hay Dirección Asignada" disabled/>
                            <br/>
                            Teléfono
                            <input type="text" class="form-control" id="txtTelefono2" placeholder="No tiene teléfono" disabled/>
                            <br/>
                            Página Web
                            <input type="text" class="form-control" id="txtPagWeb2" placeholder="No tiene Página Web" disabled/>
                            <br/>
                            E-Mail
                            <input type="text" class="form-control" id="txtMail2" placeholder="No tiene E-Mail" disabled/>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cerrar</button>

                    </div>
                </div>
            </div>
        </div> 

    </div>
</body>

<script>
    var url="";
    $('#tblProveedorInactivo').DataTable();
    $('#tblProveedorActivo').DataTable();
            
    //Modal de Ver Detalles
    function modalVerProveedor($pkProveedor,$ruc,$razon,$direccion,$telefono,$pagweb,$mail){
        $('#modalVerProveedor').modal('show'); 
        $('#tituloModalVerProveedor').html('Información del Proveedor');
        $('#txtIdProveedor2').val($pkProveedor);
        $('#txtRuc2').val($ruc);
        $('#txtRazon2').val($razon);
        $('#txtDireccion2').val($direccion);
        $('#txtTelefono2').val($telefono);
        $('#txtPagWeb2').val($pagweb);
        $('#txtMail2').val($mail);
    }
    
    //Modal de Edición
    function modalEditarProveedor($pkProveedor,$ruc,$razon,$direccion,$telefono,$pagweb,$mail){
        $('#modalProveedor').modal('show'); 
        $('#tituloModalProveedor').html('Editando Proveedor')  ;
        $('#txtIdProveedor').val($pkProveedor);
        $('#txtRuc').val($ruc);
        $('#txtRazon').val($razon);
        $('#txtDireccion').val($direccion);
        $('#txtTelefono').val($telefono);
        $('#txtPagWeb').val($pagweb);
        $('#txtMail').val($mail);
        url="<?php echo class_config::get('urlApp') ?>/?controller=Proveedor&action=Edit";
    }
    
    //Modal de Registro
    function modalRegistrarProveedor(){
        $('#modalProveedor').modal('show'); 
        $('#tituloModalProveedor').html('Registrar Proveedor')  ;
        $('#txtIdProveedor').val("");
        $('#txtRuc').val("");
        $('#txtRazon').val("");
        $('#txtDireccion').val("");
        $('#txtTelefono').val("");
        $('#txtPagWeb').val("");
        $('#txtMail').val("");
        url="<?php echo class_config::get('urlApp') ?>/?controller=Proveedor&action=Save";
    }
    
    //Acción para el modal de registro y edición
    function guardarProveedor(){
        $.post( url, $('#formProveedor').serialize(),
        function( data ) {            
            location.reload();
        });
        $('#modalProveedor').modal('hide');          
    }
    
    //Modal de Eliminación
    function modalEliminarProveedor($id){
        $('#modalProveedor2').modal('show'); 
        $('#tituloModalProveedor2').html('Eliminando Proveedor')  ;
        $('#txtMensajeeliminar').html('¿Seguro que desea eliminar este Proveedor?');
        $('#txtIdProveedor2').val($id); 
        $('#btnColor').removeClass();
        $('#btnColor').addClass('btn btn-danger');
        url="<?php echo class_config::get('urlApp') ?>/?controller=Proveedor&action=Delete";
    }
    
    //Modal de Habilitación
    function modalActivarProveedor($id){
        $('#modalProveedor2').modal('show'); 
        $('#tituloModalProveedor2').html('Habilitando Proveedor')  ;
        $('#txtMensajeeliminar').html('¿Seguro que desea habilitar el Proveedor Seleccionado?');
        $('#txtIdProveedor2').val($id); 
        $('#btnColor').removeClass();
        $('#btnColor').addClass('btn btn-primary');
        url="<?php echo class_config::get('urlApp') ?>/?controller=Proveedor&action=Active";
    }
    
    //Acción para el modal de habilitar y deshabilitar
    function deleteProveedor(){
        $.post( url, {id2:$('#txtIdProveedor2').val()},
        function( data ) {
            location.reload();
        });
        $('#modalProveedor2').modal('hide');          
    }
       
</script>
