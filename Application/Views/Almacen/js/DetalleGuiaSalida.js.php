<?php require_once '../../../../Components/Config.inc.php'; ?>

//<script>
    $(document).ready(function() {

        $('#tblDetalleInactivo').DataTable();

        LoadAlmacenes();

        if (can_update) {
            abrirFormulario();
        } 
    });

    function LoadAlmacenes() {

        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=NAlmacen&&action=ListAlmacen",
            dataType: "json",
            success: function (data) {

                var select_almacen = $('#cmb_Almacen');

                var options = [];

                for (let item of data) {
                    options.push(`
                        <option value="${item.id}">
                            ${item.nombre}
                        </option>
                    `);
                }

                select_almacen.html(options.join(' '));
            }
        })
    }

    function abrirFormularioDetalle(cual) {
        var elElemento = document.getElementById(cual);
        if (elElemento.style.display == 'block') {
            elElemento.style.display = 'none';
        } else {
            elElemento.style.display = 'block';
        }
    }

    function guardarDetalleGuiaSalida(pkComprobante) {
        console.log(pkComprobante)
        console.log($('#formGuia').serialize())
        let _data = $('#formGuia').serialize();
        // _data.movimiento_id = pkComprobante;
        $.post(url = "<?php echo class_config::get('urlApp') ?>/?controller=NAlmacen&action=AddDetalleSalida&movimiento_id=" + pkComprobante, _data, 
        function (data) {
            location.reload();
        });
    }

    function guardarDetallePlatoSalida(pkcomprobante)
    {
        $.post(url = "<?php echo class_config::get('urlApp') ?>/?controller=IngresosInsumos&action=SaveDetalleGuiaSalidaPlato&idPlato="
                + $("#txtingresePlato-id").val() + "&descripcion=" + $("#txtDescripcionPlato").val() + "&cantidad=" + $("#txtCantidadPlato").val()
                + "&pedido=" + $("#estado").val() + "&comprobante=" + pkcomprobante,
                function() {
                    location.reload();
                });
    }

    function mdlDetalleGuiaS($pkIngresoInsumo, $cantidad, $descripcion, $unidad, $pkInsumo,
            $descripcioninsumo, $fecha, $fechamodificacion, $trapertura, $trmodificacion) {
        $('#mdlDetalleGuiaSalida').modal('show');
        $('#tituloModalDetalleMas').html('Detalle de ' + $descripcioninsumo);
        $('#pk').html($pkIngresoInsumo);
        $('#fcantidad').html($cantidad);
        $('#fdescripcion').html($descripcion);
        $('#fregistro').html($fecha);
        $('#fmodificacio').html($fechamodificacion);
        $('#registro').html($trapertura);
        $('#modificado').html($trmodificacion);
    }

    function mdlEditarDetalleGuiaS(idGuia, idDetalle, idInsumo, insumo, cantidad) {
        $('#modalMensajesGuias').modal('show');
        $('#tituloModalMensajesGuias').html('Editar detalle de guia');
        $('#txtIdGuia').val(idGuia);
        $('#txtIdDetalleInsumo').val(idDetalle);
        $('#txtingreseInsumo-id2').val(idInsumo);
        $('#txtingreseInsumoDetalle').val(insumo);
        $('#txtCantidadDetalle').val(cantidad);
        url = "<?php echo class_config::get('urlApp') ?>/?controller=IngresosInsumos&action=UpdateDatosGuiaSalida";
    }
    
    function mdlEditarDetalleGuiaSDEL(idGuia, idDetalle, idInsumo, insumo, cantidad) {
        $('#tituloModalMensajesGuias').html('Editar detalle de guia');
        $('#txtIdGuia').val(idGuia);
        $('#txtIdDetalleInsumo').val(idDetalle);
        $('#txtingreseInsumo-id2').val(idInsumo);
        $('#txtingreseInsumoDetalle').val(insumo);
        $('#txtCantidadDetalle').val(0);
        url = "<?php echo class_config::get('urlApp') ?>/?controller=IngresosInsumos&action=UpdateDatosGuiaSalida";
        saveDetalleGuiaSalida();
    }

    function modalEditarDetalle(idDetalle, precio, cantidad, motivo) {
        $('#modalFormEditDetalle').modal('show');
        $('#txtIdDetalleMovimientoE').val(idDetalle);
        $('#txtCantidadE').val(cantidad);
        $('#txtPrecioE').val(precio);
        $('#txtDescripcionE').val(motivo);
    }

    function saveDetalleGuiaSalida() {
        $.post(url, $('#formGuiaUpdate').serialize(),
                function(data) {
                    location.reload();
                });
        $('#modalMensajesGuias').modal('hide');
    }

    function guardarEditarDetalle(pkComprobante) {
        console.log(pkComprobante)
        console.log($('#formGuiaE').serialize())
        let _data = $('#formGuiaE').serialize();
        // _data.movimiento_id = pkComprobante;
        $.post(url = "<?php echo class_config::get('urlApp') ?>/?controller=NAlmacen&action=EditDetalleSalida", _data, 
        function (data) {
            location.reload();
        });
    }

    function modalEliminarDetalleGuiaS($pkcomprobante) {
        $('#modalEliminarDetalle').modal('show');
        $('#tituloModalDetalle').html('Eliminar detalle de guia');
        $('#txtMensajeeliminarDetalle').html('¿Seguro que quieres anular esto?');
        $('#id').val($pkcomprobante);
        $('#btnAceptar').removeClass();
        $('#btnAceptar').addClass('btn btn-danger');
        url = "<?php echo class_config::get('urlApp') ?>/?controller=IngresosInsumos&action=DeleteDetalleGuia";
    }

    function deleteDetalleGuiaS() {
        $.post(url, {id: $('#id').val()},
        function(data) {
            location.reload();
        });
        $('#modalEliminarDetalle').modal('hide');
    }

    function newDeleteDetalle(pkComprobante) {
        if (confirm('¿Está seguro que desea eliminar el ingreso?')) {

            $.get("<?php echo class_config::get('urlApp') ?>/?controller=NAlmacen&action=DeleteDetalleIngreso&id=" + pkComprobante, function (data) {
                location.reload()
            })
        }

    }

    function mdlActivarDetalleGuiaSalida($pkcomprobante) {
        $('#modalEliminarDetalle').modal('show');
        $('#tituloModalDetalle').html('Habilitando detalle de guia');
        $('#txtMensajeeliminarDetalle').html('¿Seguro que quieres habilitar esto?');
        $('#id').val($pkcomprobante);
        $('#btnAceptar').removeClass();
        $('#btnAceptar').addClass('btn btn-primary');
        url = "<?php echo class_config::get('urlApp') ?>/?controller=IngresosInsumos&action=ActiveDetalleGuia";
    }

    function abrirFormulario(tipoComprobante, cual) {
        $('#modalFormDetalle').modal('show')

        setTimeout(() => {
            $('#txtingreseInsumo').select2('open');
        }, 500);
    }