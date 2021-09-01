<?php require_once '../../../../Components/Config.inc.php'; ?>

//<script>
var url = "";

    function modalRegistrarUnidades(){
        $('#modalUnidades').modal('show'); 
        $('#tituloModalUnidades').html('Registrando Unidades')  ;
        $('#txtIdUnidad').val("");
        $('#txtDescripcionUnidad').val("");
        url="<?php echo class_config::get('urlApp') ?>/?controller=Almacen&action=SaveUnidad";
    }

    function modalEditarUnidad($pk,$descripcion){
        $('#modalUnidades').modal('show'); 
        $('#tituloModalUnidades').html('Editando Unidades')  ;
        $('#txtIdUnidad').val($pk);
        $('#txtDescripcionUnidad').val($descripcion);
        url="<?php echo class_config::get('urlApp') ?>/?controller=Almacen&action=EditUnidad";
    }
    
    function guardarUnidad(){
        $.post( url, $('#formUnidad').serialize(),
        function() {            
            location.reload();
        });
        $('#modalUnidades').modal('hide');          
    }
    
    function modalEliminarUnidad($id){
        $('#modalEliminarUnidad').modal('show'); 
        $('#tituloModalEliminarUnidad').html('Eliminando Unidad')  ;
        $('#txtMensajeeliminar').html('¿Seguro que desea eliminar esta Unidad?');
        $('#txtIdEliminarUnidad').val($id); 
        $('#btnColor').removeClass();
        $('#btnColor').addClass('btn btn-danger');
        url="<?php echo class_config::get('urlApp') ?>/?controller=Almacen&action=DeleteUnidad";
    }

    function modalActivarUnidad($id){
        $('#modalEliminarUnidad').modal('show'); 
        $('#tituloModalEliminarUnidad').html('Habilitando Unidad')  ;
        $('#txtMensajeeliminar').html('¿Seguro que desea habilitar la Unidad Seleccionado?');
        $('#txtIdEliminarUnidad').val($id); 
        $('#btnColor').removeClass();
        $('#btnColor').addClass('btn btn-primary');
        url="<?php echo class_config::get('urlApp') ?>/?controller=Almacen&action=ActiveUnidad";
    }

    function deleteUnidad(){
        $.post( url, {id:$('#txtIdEliminarUnidad').val()},
        function() {
            location.reload();
        });
        $('#modalEliminarUnidad').modal('hide');          
    }
    