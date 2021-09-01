<?php require_once '../../../../Components/Config.inc.php'; ?>

//<script>

    $(document).ready(function() {
        $('#tblGuiaActiva').DataTable({
            "order": [[0, "desc"]]
        });
        $('#tblGuiaInactiva').DataTable();
    });

    function mdlRegistrarGuiaSalida() {
        $('#mdlGuiaSalida').modal('show');
        $('#tituloModalGuia').html('Registrar guia de salida');
        $('#NroComprobante').val("");
        //$('#fecha').val("");
        url="<?php echo class_config::get('urlApp') ?>/?controller=NAlmacen&action=AddSalida";
    }

    function mdlDetalleGuiaSalida($pkcomprobante, $nrocomprobante, $fecha, $fechamodificacion, $trapertura, $trmodificacion) {
        $('#mdlGuiaSalidaDetalle').modal('show');
        $('#tituloModalDetalleMas').html('Guía de salida N° ' + $nrocomprobante);
        $('#pk').html($pkcomprobante);
        $('#fregistro').html($fecha);
        $('#fmodificacio').html($fechamodificacion);
        $('#registro').html($trapertura);
        $('#modificado').html($trmodificacion);
    }

    function mdlEditarGuiaSalida($pkcomprobante, $numCom, $fecha) {
        $('#mdlGuiaSalida').modal('show');
        $('#tituloModalGuia').html('Editar guia de salida');
        $('#txtIdGuia').val($pkcomprobante);
        $('#NroComprobante').val($numCom);
        $('#fecha').val($fecha);
        url = "<?php echo class_config::get('urlApp') ?>/?controller=NAlmacen&action=EditSalida";
    }

    function guardarGuiaSalida() {
        $('#mdlGuiaSalida').modal('hide')
        $.post(url, $('#formGuia').serialize(),
                function(data) {
                    data = JSON.parse(data);
                    if (data.id) {
                        alert('Guia de Salida registrada.');
                        verDetalles(data.id);
                    }
                });
    }
    
    function verDetalles($pkcomprobante){
        window.open('<?php echo Class_config::get('urlApp') ?>/?controller=Almacen&action=AdminDetalleGuiaSalida&Id='+$pkcomprobante,'_self');
    }
    
    function mdlEliminarGuiaSalida($pkcomprobante){
        $('#mdlElimnarGuiaSalida').modal('show'); 
        $('#tituloModalGuia2').html('Eliminar guia de salida')  ;
        $('#txtMensajeeliminar').html('¿Seguro que quieres anular esto?');
        $('#txtIdGuia2').val($pkcomprobante); 
        $('#btnColor').removeClass();
        $('#btnColor').addClass('btn btn-danger');
        url="<?php echo class_config::get('urlApp') ?>/?controller=NAlmacen&action=DeleteMovimiento";
    }
    
    function mdlActivarGuiaSalida($pkcomprobante){
        $('#mdlElimnarGuiaSalida').modal('show'); 
        $('#tituloModalGuia2').html('Habilitar guia de salida')  ;
        $('#txtMensajeeliminar').html('¿Seguro que quieres habilitar esto?');
        $('#txtIdGuia2').val($pkcomprobante); 
        $('#btnColor').removeClass();
        $('#btnColor').addClass('btn btn-primary');
        url="<?php echo class_config::get('urlApp') ?>/?controller=Comprobante&action=ActiveGuia";
    }
    
    function deleteGuiaSalida(){
        $.post( url, {id:$('#txtIdGuia2').val()},
        function( data ) {
            location.reload();
        });
        $('#mdlElimnarGuiaSalida').modal('hide');          
    }

    function soloNumeros(e) {
        var key = window.Event ? e.which : e.keyCode
        return (key >= 48 && key <= 57)
    }

    function sendPrint(id) {
        $.post("<?php echo class_config::get('urlApp') ?>/?controller=NAlmacen&action=SendPrint", {id},
        function() {
            alert('Impresión enviada');
            // location.reload();
        });
    }