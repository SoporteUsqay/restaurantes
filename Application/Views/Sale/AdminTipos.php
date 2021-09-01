<?php include 'Application/Views/template/header.php'; ?>
<body>
    <link href="Public/select2/css/select2.css" rel="stylesheet">
    <style>  
        .select2-container{
            width: 100% !important;
            height: 34px !important;
        }
        .select2-selection{
            width: 100% !important;
            height: 34px !important;
        }
    </style>
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
                <h4><i class="fa fa-clipboard"></i> Administrar Tipos de Plato</h4>
            </div>

            <div class="panel-body">

                <ul class="nav nav-tabs">
                    <li class="active"><a href="#TipoActivo" data-toggle="tab">Activos</a>
                    </li>
                    <li><a href="#TipoInactivo" data-toggle="tab">Inactivos</a>
                    </li>
                    <p class="text-right">
                    <button onclick="modalRegistrarTipo()" type="button" class="btn btn-success"id="btnGuardarPagoDiario">
                        <span class="glyphicon glyphicon-plus-sign" aria-hidden="true" ></span> Nuevo Tipo
                    </button>
                </p>
                </ul>
                <br/>
                <div class="tab-content">
                    <div class="tab-pane active" id="TipoActivo" >            
                        <table id="tblTipoActivo" title="Tipos de Platos y Productos" class="table table-borderer" >
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tipo</th>
                                    <th>Codigo SUNAT</th>
                                    <?php 
                                    $db = new SuperDataBase();
                                    $query_c = "SELECT * FROM cajas";
                                    $result_c = $db->executeQuery($query_c);
                                    while ($row_c = $db->fecth_array($result_c)) {
                                        echo "<th>CAJA ".$row_c["caja"]."</th>";
                                    }
                                    ?>
                                    <th></th>
                                    <th></th>            
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                
                                $query = "SELECT pkTipo,upper(t.descripcion) as descripcionTipo,t.pkCategoria,upper(c.descripcion) as descripcionCategoria, t.estado FROM tipos t inner join categoria c on t.pkCategoria=c.pkCategoria where estado=0 order by pkTipo ASC";
                                $result = $db->executeQuery($query);
                                while ($row = $db->fecth_array($result)) {
                                    echo "<tr class='success'>";
                                    echo "<td>" . $row['pkTipo']. "</td>";
                                    echo "<td>" . $row['descripcionTipo'] . "</td>";
                                    $result_sunat = $db->executeQuery("SELECT cs.id, cs.descripcion from tipo_codigo_sunat tc, codigo_sunat cs where tc.id_tipo = '".$row['pkTipo']."' AND tc.id_codigo_sunat = cs.id");
                                    
                                    $id_sunat = "";
                                    $descripcion_sunat = "";
                                    if($row1 = $db->fecth_array($result_sunat)){
                                        $id_sunat = $row1["id"];
                                        $descripcion_sunat = $row1["descripcion"];
                                    }
                                    
                                    echo "<td>".$id_sunat." - ".$descripcion_sunat."</td>";
                                    
                                    $query_c = "SELECT * FROM cajas";
                                    $result_c = $db->executeQuery($query_c);
                                    while ($row_c = $db->fecth_array($result_c)) {
                                        $query_sal = "Select * from accion_caja where pk_accion = '".$row[0]."' AND tipo_accion = 'TYP' AND caja = '".$row_c["caja"]."'";
                                        $result_sal = $db->executeQuery($query_sal);
                                        $asociado = 0;
                                        while ($row_sal = $db->fecth_array($result_sal)) {
                                            echo "<td><a href='?controller=Config&action=QuitaTipoCaja&pkTipo=".$row[0]."&caja=".$row_c["caja"]."'><span class='glyphicon glyphicon-ok' title='Tipo Activo en Caja'></span></a></td>";
                                            $asociado = 1;
                                        }
                                        
                                        if($asociado === 0){
                                            echo "<td><a href='?controller=Config&action=PonTipoCaja&pkTipo=".$row[0]."&caja=".$row_c["caja"]."'><span class='glyphicon glyphicon-minus-sign' title='Tipo Inactivo en Caja'></span></a></td>";                     
                                        }
                                    }

                                    echo "<td>";
                                    echo "<a onclick='modalEditarTipo(" . $row[0] . ",\"" . $row[1] . "\",\"" . $id_sunat . "\",\"".utf8_encode($descripcion_sunat)."\")'><span class='glyphicon glyphicon-pencil' title='Editar Tipo'></span></a>";
                                    echo "</td>";
                                    echo "<td>";
                                    echo "<a onclick='modalEliminarTipo(" . $row[0] . ")'><span class='glyphicon glyphicon-minus-sign' title='Eliminar Tipo'></span></a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane" id="TipoInactivo">
                        <table id="tblTipoInactivo" title="Tipos de Platos y Productos" class="table table-borderer" >
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tipo</th>        
                                    <th></th>            
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $db = new SuperDataBase();
                                $query = "SELECT pkTipo,upper(t.descripcion) as descripcionTipo,t.pkCategoria,upper(c.descripcion) as descripcionCategoria, t.estado FROM tipos t "
                                        . "inner join categoria c on t.pkCategoria=c.pkCategoria where pkSucursal = '" . UserLogin::get_pkSucursal() . "' and estado=1;";
                                $result = $db->executeQuery($query);
                                while ($row = $db->fecth_array($result)) {
                                    echo "<tr class='danger'>";
                                    echo "<td>" . $row['pkTipo'] . "</td>";
                                    echo "<td>" . $row['descripcionTipo'] . "</td>";
                                    echo "<td>";
                                    echo "<a onclick='modalActivarTipo(" . $row[0] . ")'><span class='glyphicon glyphicon-ok' title='Habilitar un Tipo'></span></a>";
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


        <div id="modalTipos" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><label id="tituloModalTipos"></label></h4>
                    </div>
                    <div class="modal-body">
                        <form id="formTipo">                        
                            <input name="id" id="txtIdTipo" style="display: none;"/>
                            Descripci&oacute;n
                            <input required="true" type="text" name="descripcion" class="form-control" id="txtDescripcionTipo" placeholder="Ingrese la Descripcion del Tipo" />
                            <br/>
                            Codigo SUNAT<br/>
                            <select class="form-control" title="Obligatorio" id="tipo_sunat" required="true" name="tipo_sunat">
                            </select>
                            <p></p>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" onclick="guardarTipo()">Guardar</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>        

        <div id="modalTipos2" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><label id="tituloModalTipos2"></label></h4>
                    </div>
                    <div class="modal-body">
                        <form id="formTipo2">                        
                            <input name="id" id="txtIdTipo2" style="display: none;"/>
                            <strong id="txtMensajeeliminar"></strong>
                        </form>
                    </div>
                    <div class="modal-footer">

                        <button id="btnColor" class="btn btn-primary" onclick="deleteTipo()">Aceptar</button>

                        <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>

                    </div>
                </div>
            </div>
        </div>        
    </div>  
</body>
<?php
$objViewMenu = new Application_Views_IndexView();
$objViewMenu->showFooter();
?>
<script src="Public/select2/js/select2.js"></script>
<script>
    
    _listCategorias('cmbCategoria');
    $('#tblTipoActivo').DataTable({
        stateSave: true
    }); 
    $('#tblTipoInactivo').DataTable(); 
    
    var url = "";
    
    //Modal de Registro
    function modalRegistrarTipo(){
        $('#modalTipos').modal('show');
        $('#tituloModalTipos').html('Registrando Tipo')  ;
        $('#txtIdTipo').val("");
        $('#txtDescripcionTipo').val("");
        $('#cmbCategoria').val("");
        url="<?php echo class_config::get('urlApp') ?>/?controller=Tipos&action=Save";
    }
    
    //Modal de Edicion
    function modalEditarTipo($pkTipo,$descripciontipo,$idSunat,$descripcionSunat){
        $('#modalTipos').modal('show');
        $('#tituloModalTipos').html('Editando Tipo')  ;
        $('#txtIdTipo').val($pkTipo);
        $('#txtDescripcionTipo').val($descripciontipo);
        
        $('#tipo_sunat')
        .empty()
        .append('<option selected value="'+$idSunat+'">'+$idSunat+' - '+decodeURIComponent(escape($descripcionSunat))+'</option>');
        $('#tipo_sunat').select2('data', {
          id: $idSunat,
          label: decodeURIComponent(escape($descripcionSunat))
        });
        $('#tipo_sunat').trigger('change');
        
        url="<?php echo class_config::get('urlApp') ?>/?controller=Tipos&action=Edit";
    }
    
    //Accion para el modal de registro y edicion
    function guardarTipo(){        
        $.ajax({
            type: "POST",
            url: url,
            data: $("#formTipo").serialize(), // Adjuntar los campos del formulario enviado.
            dataType: 'html',
            success: function (data) {
                if (data === "false") {
                    $('body,html').animate({
                        scrollTop: 0
                    }, 500);
                    $('#merror').show('fast').delay(3000).hide('fast');

                } else {
                    $('body,html').animate({
                        scrollTop: 0
                    }, 500);
                    $('#msuccess').show('fast').delay(3000).hide('fast');
                    location.reload();
                    $('#modalTipos').modal('hide');
                }
            }
        });
    }
    
    //Modal de Eliminacion
    function modalEliminarTipo($id){
        $('#modalTipos2').modal('show'); 
        $('#tituloModalTipos2').html('Eliminando Tipo')  ;
        $('#txtMensajeeliminar').html('¿Seguro que desea eliminar este Tipo?');
        $('#txtIdTipo2').val($id); 
        $('#btnColor').removeClass();
        $('#btnColor').addClass('btn btn-danger');
        url="<?php echo class_config::get('urlApp') ?>/?controller=Tipos&action=Delete";
    }
    
    //Modal de Habilitacion
    function modalActivarTipo($id){
        $('#modalTipos2').modal('show'); 
        $('#tituloModalTipos2').html('Habilitando Tipo')  ;
        $('#txtMensajeeliminar').html('¿Seguro que desea habilitar el Tipo Seleccionado?');
        $('#txtIdTipo2').val($id); 
        $('#btnColor').removeClass();
        $('#btnColor').addClass('btn btn-primary');
        url="<?php echo class_config::get('urlApp') ?>/?controller=Tipos&action=Active";
    }

    //Accion para el modal de habilitar y deshabilitar
    function deleteTipo(){
        $.post( url, {id:$('#txtIdTipo2').val()},
        function() {
            location.reload();
        });
        $('#modalTipos2').modal('hide');          
    }
    
    $(document).ready(function (){
        $('#tipo_sunat').select2({
          dropdownParent: $('#modalTipos'),
          ajax: { 
           url: "<?php echo Class_config::get('urlApp') ?>/reportes/ws/codigos_sunat.php",
           type: "post",
           dataType: 'json',
           delay: 250,
           data: function (params) {
            return {
               term: params.term
            };
           },
           processResults: function (data) {
                return {
                    results: $.map(data.results, function (item) {
                        return {
                            text: item.id+" - "+decodeURIComponent(unescape(item.descripcion)),
                            id: item.id
                        };
                    })
                };
            }
          },
          placeholder: 'Codigo Sunat (Obligatorio)',
          minimumInputLength: 0
        }); 
    });

</script>