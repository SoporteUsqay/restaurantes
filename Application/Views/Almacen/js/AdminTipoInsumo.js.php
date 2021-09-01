<?php require_once '../../../../Components/Config.inc.php'; ?>

//<script>
    var url = "";

    function modalRegistrarTipoInsumo() {
        $('#modalTipoInsumo').modal('show');
        $('#tituloModalTipoInsumo').html('Registrando Tipo de Insumo');
        $('#txtIdTipoInsumo').val("");
        $('#txtDescripcionTipoInsumo').val("");
        url = "<?php echo class_config::get('urlApp') ?>/?controller=Almacen&action=SaveTipoInsumo";
    }

    function guardarTipoInsumo() {
        $.post(url, $('#formTipoInsumo').serialize(),
                function() {
                    location.reload();
                });
        $('#modalTipoInsumo').modal('hide');
    }

    function modalEditarTipoInsumo($pk, $descripcion) {
        $('#modalTipoInsumo').modal('show');
        $('#tituloModalTipoInsumo').html('Editando Tipos de Insumo');
        $('#txtIdTipoInsumo').val($pk);
        $('#txtDescripcionTipoInsumo').val($descripcion);
        url = "<?php echo class_config::get('urlApp') ?>/?controller=Almacen&action=EditTipoInsumo";
    }

    function modalEliminarTipoInsumo($id) {
        $('#modalEliminarTipoInsumo').modal('show');
        $('#tituloModalEliminarTipoUnidad').html('Eliminando Tipo de Insumo');
        $('#txtMensajeeliminar').html('¿Seguro que desea eliminar este Tipo de Insumo?');
        $('#txtIdEliminarTipoUnidad').val($id);
        $('#btnColor').removeClass();
        $('#btnColor').addClass('btn btn-danger');
        url = "<?php echo class_config::get('urlApp') ?>/?controller=Almacen&action=DeleteTipoInsumo";
    }

    function deleteTipoInsumo() {
        $.post(url, {id: $('#txtIdEliminarTipoUnidad').val()},
        function() {
            location.reload();
        });
        $('#modalEliminarTipoInsumo').modal('hide');
    }

    function modalActivarTipoInsumo($id) {
        $('#modalEliminarTipoInsumo').modal('show');
        $('#tituloModalEliminarTipoUnidad').html('Habilitando Tipo de Insumo');
        $('#txtMensajeeliminar').html('¿Seguro que desea habilitar el Tipo de Insumo Seleccionado?');
        $('#txtIdEliminarTipoUnidad').val($id);
        $('#btnColor').removeClass();
        $('#btnColor').addClass('btn btn-primary');
        url = "<?php echo class_config::get('urlApp') ?>/?controller=Almacen&action=ActiveTipoInsumo";
    }