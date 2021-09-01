<?php
require_once '../../../../Components/Config.inc.php';

$ultimoId = 0;
$idProvisional = 0;
$valor = 0;
?>
//<script>

    var idEditar = 0;
    var parametro = 0;
    var url = "";

    $(document).ready(function () {
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


    function modalEliminarDetalle($id) {
        $('#modalEliminarDetalle').modal('show');
        $('#tituloModalDetalle').html('Eliminando Detalle');
        $('#txtMensajeeliminarDetalle').html('¿Seguro que desea eliminar este Detalle?');
        $('#id').val($id);
        $('#btnAceptar').removeClass();
        $('#btnAceptar').addClass('btn btn-danger');
        url = "<?php echo class_config::get('urlApp') ?>/?controller=IngresosInsumos&action=DeleteDetalleGuia";
    }

    function modalHabilitarDetalle($id) {
        $('#modalEliminarDetalle').modal('show');
        $('#tituloModalDetalle').html('Habilitando Detalle');
        $('#txtMensajeeliminarDetalle').html('¿Seguro que desea habilitar este Detalle Seleccionado?');
        $('#id').val($id);
        $('#btnAceptar').removeClass();
        $('#btnAceptar').addClass('btn btn-primary');
        url = "<?php echo class_config::get('urlApp') ?>/?controller=IngresosInsumos&action=ActiveDetalleGuia";
    }
    function deleteDetalle() {
        $.post(url, {id: $('#id').val()},
        function (data) {
            location.reload();
        });
        $('#modalEliminarDetalle').modal('hide');
    }

    function condicion(value) {
        // if (value == "3") {
        //     $("#txtPrecio").val("0.00");
        // } else {
        //     document.getElementById("txtPrecio").disabled = false;
        //     $("#txtPrecio").val("");
        // }
    }

    function abrirFormulario(tipoComprobante, cual) {
        // condicion(tipoComprobante);
        // var elElemento = document.getElementById(cual);
        // if (elElemento.style.display == 'block') {
        //     elElemento.style.display = 'none';
        // } else {
        //     elElemento.style.display = 'block';
        // }

        $('#modalFormDetalle').modal('show')

        setTimeout(() => {
            $('#txtingreseInsumo').select2('open');
        }, 500);
    }

    function cerrarModal(modal) {
        $('#' + modal).modal('hide');
    }

    function buscarproveedor() {
        var params = {valor: $('#ruc').val()};
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Proveedor&&action=FiltroProveedor",
            data: params,
            type: "POST",
            contentType: "application/x-www-form-urlencoded",
            dataType: "json",
            success: function (data) {
                var Idproveedor = "";
                var proveedor = "";

                $.each(data, function (index, value) {
                    Idproveedor = value.idproveedor;
                    proveedor = value.razon;
                    $('#IdProveedor').val(Idproveedor);
                    $('#proveedor').val(proveedor);
                });
            }
        });
    }

    //Modal de Ver Detalles
    function modalVerProveedor($pkProveedor, $ruc, $razon, $direccion, $telefono, $pagweb, $mail) {
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



    function modalverMasDetalles()
    {
        $('#modalMasDetalles').modal('show');
        $('#tituloModalDetalleMas').html('Informacion mas Detallada');
    }


    //Modal de Registro
    function modalRegistrarDetalle(tipoComprobante, pkComprobante) {
        condicion(tipoComprobante);
        $('#modalGuia').modal('show');
        $('#tituloModalGuia').html('Agregar Detalle...');
        $('#txtIdDetalle').val(pkComprobante);
        $('#txtIdInsumo').val("");
        $('#txtCantidad').val("");
        $('#txtPrecio').val("");
    }


    function guardarDetalle(pkComprobante) {
        console.log(pkComprobante)
        console.log($('#formGuia').serialize())
        let _data = $('#formGuia').serialize();
        // _data.movimiento_id = pkComprobante;
        $.post(url = "<?php echo class_config::get('urlApp') ?>/?controller=NAlmacen&action=AddDetalleIngreso&movimiento_id=" + pkComprobante, _data, 
        function (data) {
            location.reload();
        });
    }

    function guardarEditarDetalle(pkComprobante) {
        console.log(pkComprobante)
        console.log($('#formGuiaE').serialize())
        let _data = $('#formGuiaE').serialize();
        // _data.movimiento_id = pkComprobante;
        $.post(url = "<?php echo class_config::get('urlApp') ?>/?controller=NAlmacen&action=EditDetalleIngreso", _data, 
        function (data) {
            location.reload();
        });
    }

    function newDeleteDetalle(pkComprobante) {
        if (confirm('¿Está seguro que desea eliminar el ingreso?')) {

            $.get("<?php echo class_config::get('urlApp') ?>/?controller=NAlmacen&action=DeleteDetalleIngreso&id=" + pkComprobante, function (data) {
                location.reload()
            })
        }

    }


    function MostrarDatosDetalleGuia(id) {
        $('#modalMensajesGuias').modal('show');
        $('#tituloModalMensajesGuias').html('Editando Detalle de la Guia...');
        url = "<?php echo class_config::get('urlApp') ?>/?controller=IngresosInsumos&action=UpdateDatosGuias";
    }

    function modalEditarDetalle(idDetalle, precio, cantidad) {
        // $('#modalMensajesGuias').modal('show');
        // $('#tituloModalMensajesGuias').html('Editando Detalle de la Guia...');
        // $('#txtIdGuia').val(idGuia);
        // $('#txtIdDetalleInsumo').val(idDetalle);
        // $('#txtingreseInsumo-id2').val(idInsumo);
        // $('#txtingreseInsumoDetalle').val(insumo);
        // $('#txtCantidadDetalle').val(cantidad);
        // $('#txtPrecioDetalle').val(precio);
        // url = "<?php echo class_config::get('urlApp') ?>/?controller=IngresosInsumos&action=UpdateDatosGuias";

        $('#modalFormEditDetalle').modal('show');
        $('#txtIdDetalleMovimientoE').val(idDetalle);
        $('#txtCantidadE').val(cantidad);
        $('#txtPrecioE').val(precio);
    }
    
    function modalEditarDetalleDEL(idGuia, idDetalle, idInsumo, insumo, cantidad, precio) {
        $('#tituloModalMensajesGuias').html('Anulando este detalle...');
        $('#txtIdGuia').val(idGuia);
        $('#txtIdDetalleInsumo').val(idDetalle);
        $('#txtingreseInsumo-id2').val(idInsumo);
        $('#txtingreseInsumoDetalle').val(insumo);
        $('#txtCantidadDetalle').val(0);
        $('#txtPrecioDetalle').val(precio);
        url = "<?php echo class_config::get('urlApp') ?>/?controller=IngresosInsumos&action=UpdateDatosGuias";
        saveDetalleGuia();
    }

    function saveDetalleGuia()
    {
        $.post(url, $('#formGuiaUpdate').serialize(),
                function (data) {
                    location.reload();
                });
        $('#modalMensajesGuias').modal('hide');
    }


    function hallarUltimoId() {
    <?php
    $db = new SuperDataBase();
    $query = "select pkComprobante from comprobante_ingreso order by 1 desc limit 1;";
    $result = $db->executeQuery($query);
    $ultimoId = 0;
    while ($row = $db->fecth_array($result)) {
        $ultimoId = $row['pkComprobante'];
    }
    ?>
    }

    url = "<?php echo class_config::get('urlApp') ?>/?controller=IngresosInsumos&action=SaveDetalle";
    //Acción para el modal de registro y edición
    function guardarDetalle1() {
        $.post(url, $('#formGuia').serialize(),
        function (data) {
            //Nada WE
        });        
    }

    //Acción para ver los detalles de la guía
    function verDetalles($pkGuia) {
        window.open('<?php echo Class_config::get('urlApp') ?>/?controller=Almacen&action=AdminDetalleGuias&Id=' + $pkGuia, '_self');
    }

    function NuevoProveedor() {
        window.open('<?php echo Class_config::get('urlApp') ?>/?controller=Almacen&action=ShowAdminProveedor', '_blank');
        condicion(1);
    }


    // funcion para validar solo numeros
    function soloNumeros(e)
    {
        var key = window.Event ? e.which : e.keyCode
        return (key >= 48 && key <= 57)
    }

    // funcion para validar solo letras
    function soloLetras(e) {
        key = e.keyCode || e.which;
        tecla = String.fromCharCode(key).toLowerCase();
        letras = " áéíóúabcdefghijklmnñopqrstuvwxyz";
        especiales = "8-37-39-46";

        tecla_especial = false
        for (var i in especiales) {
            if (key == especiales[i]) {
                tecla_especial = true;
                break;
            }
        }

        if (letras.indexOf(tecla) == -1 && !tecla_especial) {
            return false;
        }
    }
    