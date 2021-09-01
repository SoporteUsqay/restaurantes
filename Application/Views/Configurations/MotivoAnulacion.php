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
            <h2>Motivos de Anulación Predefinidos </h2>

            <div class="form-group">
                <div class="col-md-2">
                    <button onclick="nuevo()" type="button" class="btn btn-success">
                        <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true" ></span> 
                        Registrar Nuevo Motivo
                    </button>
                </div>
            </div>
        </div>

        <br><br>

        <table id="tblMotivoAnulacion" class="table table-borderer">

            <thead>
                <th class="text-center">#</th>
                <th class="text-center">Nombre</th>
                <th class="text-center">Operaciones</th>
            </thead>

            <tbody>
                <?php 
                    $query = "select * from motivo_anulacion_predefinido";

                    $db = new SuperDataBase();

                    $res = $db->executeQueryEx($query);

                    $index = 1;

                    while ($row = $db->fecth_array($res)): 
                ?>

                    <tr>

                        <td><?php echo $index++; ?></td>
                        <td><?php echo $row['nombre'] ?></td>
                        
                        <td class="text-center">
                            <a class="btn" onclick="edit('<?php echo $row['id'] ?>', '<?php echo $row['nombre'] ?>')">
                                <span class="glyphicon glyphicon-pencil"></span>
                            </a>
                            <a class="btn" onclick="destroy('<?php echo $row['id'] ?>', '<?php echo $row['nombre'] ?>')">
                                <span class="glyphicon glyphicon-trash"></span>
                            </a>
                        </td>
                    </tr>

                <?php 
                    endwhile
                ?>

            </tbody>
    
        </table>

    </div>


    <div id="modalTableMotivoAnulacion" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><label id="tituloModalMotivoAnulacion"></label></h4>
                </div>
                <div class="modal-body">
                    <form id="formTableMotivoAnulacion">
                        <input name="id" id="txtIdMotivoAnulacion" style="display: none;"/>
                        Nombre
                        <input name="nombre" class="form-control" id="txtNombreMotivoAnulacion" placeholder="" />
                    </form>
                </div>
                <div class="modal-footer">
                    <div id="dlg-buttonsCancelarCuenta">
                        <button class="btn btn-primary" onclick="save()">Guardar</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>
                    </div>                            
                </div>
            </div>
        </div>
    </div>
</body>

<script>

    var url = ''

    $(document).ready(function() {
        $('#tblMotivoAnulacion').DataTable();
    });

    function nuevo() {
        $('#modalTableMotivoAnulacion').modal('show')
        $('#tituloModalMotivoAnulacion').html('Registrar Nuevo Motivo de Anulación Predefinido')
        $('#txtNombreMotivoAnulacion').val('')
        $('#txtIdMotivoAnulacion').val('')
        url = "<?php echo class_config::get('urlApp') ?>/?controller=Config&action=MotivoAnulacionAdd";
    }

    function save() {
        $.post(url, $('#formTableMotivoAnulacion').serialize(),
            function(data) {
                $('#modalTableMotivoAnulacion').modal('hide');
                location.reload();
            });
    }

    function edit(id, nombre) {
        $('#modalTableMotivoAnulacion').modal('show')
        $('#tituloModalMotivoAnulacion').html('Editando motivo')
        $('#txtIdMotivoAnulacion').val(id)
        $('#txtNombreMotivoAnulacion').val(nombre)
        url = "<?php echo class_config::get('urlApp') ?>/?controller=Config&action=MotivoAnulacionEdit";
    }

    function destroy(id, nombre) {
        if (confirm('¿Está seguro que desea eliminar el motivo de anulación:  '+nombre+'?')) {

            $.post("<?php echo class_config::get('urlApp') ?>/?controller=Config&action=MotivoAnulacionDelete", {id},
            function(data) {
                $('#modalTableMotivoAnulacion').modal('hide');
                location.reload();
            });

        }
    }

</script>