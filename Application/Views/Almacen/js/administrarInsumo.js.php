<?php require_once '../../../../Components/Config.inc.php'; ?>

//<script>
    $(document).ready(function () {
        $('#tblInsumoMenu').DataTable({
            // "ordering": false,
            "bSort": false,
            
        });
        loadTipoPedido('cmbTipoPedido');
    });
    /**
     * Guarda el insumo menu
     * @param {char} $pkPlato el id del plato
     * @param {int} $cantidad el id del plato
     * @param {int} $pkInsumo el id del plato
     * */
    var url = "<?php echo Class_config::get('urlApp') ?>/?controller=InsumoMenu&action=SaveReceta";
    function guardarInsumoMenu() {

        let params = {
            id: $('#txtId').val(),
            plato_id: $('#inputPlato').val(),
            insumo_id: $('#txtInsumo').val(),
            insumo_porcion_id: $('#txtInsumoPorcion').val(),
            cantidad: $('#cantidad').val(),
            almacen_id: $('#txtAlmacen').val(),
            terminal: $('#txtTerminal').val()
        }

        $.post(url, params, function (data) {
            if (data === "0") {
                $('body,html').animate({
                    scrollTop: 0
                }, 800);
                $('#merror').show('fast').delay(4000).hide('fast');
            } else {
                $('body,html').animate({
                    scrollTop: 0
                }, 800);
                $('#msuccess').show('fast').delay(4000).hide('fast');
//                actualizaInsumo();
               location.reload();
            }

        });
    }
    function del($id) {

        if (!confirm("¿Está seguro que desea eliminar este ingrediente al plato?")) return
 
        $.post("<?php echo Class_config::get('urlApp')?>/?controller=InsumoMenu&action=DeleteReceta", {id:$id}, function (data) {
            if (data === "0") {
                $('body,html').animate({
                    scrollTop: 0
                }, 800);
                $('#merror').show('fast').delay(4000).hide('fast');
            } else {
                $('body,html').animate({
                    scrollTop: 0
                }, 800);
                $('#msuccess').show('fast').delay(4000).hide('fast');
               
               location.reload();
            }

        });
    }

    function guardarContenedores($pkPlato) {
        $.post("<?php echo Class_config::get('urlApp') ?>/?controller=InsumoMenu&action=SaveContenedores&pkPlato=" + $pkPlato, $('#frmTappers').serialize(), function (data) {
            if (data === "0") {
                $('body,html').animate({
                    scrollTop: 0
                }, 800);
                $('#merror').show('fast').delay(4000).hide('fast');
            } else {
                $('body,html').animate({
                    scrollTop: 0
                }, 800);
                $('#msuccess').show('fast').delay(4000).hide('fast');
                actualizaInsumo();
//                location.reload();
            }

        });
    }
    function selAI(id, plato_id, insumo_id, insumo_porcion_id, cantidad, almacen_id, terminal) {
        // url = "<?php echo Class_config::get('urlApp') ?>/?controller=InsumoMenu&&action=Update&id=" + id+"&cantidad="+$('#cantidad').val()+"&estado="+$('#estado').val();
        // $.getJSON('<?php echo Class_config::get('urlApp') ?>?controller=InsumoMenu&&action=ListId&id=' + id, {op: 'get', id: id}, function (data) {
        //     $('#id').val(data[0].id);
        //     $('#inputPlato').val(data[0].descripcion);
        //     $('#txtInsumo').val(data[0].descripcionInsumo);
        //     $('#txtInsumo-id').val(data[0].pkInsumo);
        //     $('#cantidad').val(data[0].cantidad);
        //     $('#estado').val(data[0].estado);
        //     $('#unidadi').val(data[0].unidad);
        //     $('#inputPlato').prop("disabled", true);
        //     $('#txtInsumo').prop("disabled", true);
        //     $('#btnGuardarTaper').prop("disabled", true);


        // });

        $('#txtId').val(id);
        $('#inputPlato').val(plato_id); 
        $('#inputPlato').trigger('change');
        $('#txtInsumo').val(insumo_id);
        $('#txtInsumo').trigger('change');
        
        $('#cantidad').val(cantidad);
        $('#txtAlmacen').val(almacen_id);
        $('#txtTerminal').val(terminal);

        loadPorciones(insumo_id, () => {
            $('#txtInsumoPorcion').val(insumo_porcion_id);
            $('#txtInsumoPorcion').trigger('change');
        });

        url = "<?php echo Class_config::get('urlApp') ?>/?controller=InsumoMenu&action=EditReceta";

    }

    function actualizaInsumo() {
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=InsumoMenu&&action=List", /* Llamamos a tu archivo */
            type: "POST",
            params: {pkPlato: getpkPlato()},
            dataType: "json", /* Esto es lo que indica que la respuesta será un objeto JSon */
            success: function (data) {
                /* Supongamos que #contenido es el tbody de tu tabla */
                /* Inicializamos tu tabla */
                $("#tblInsumoMenu tbody").empty();
                /* Vemos que la respuesta no este vacía y sea una arreglo */
//                if (data != null && $.isArray(data)) {
                /* Recorremos tu respuesta con each */

                $.each(data, function (index, value) {
                    $("#tblInsumoMenu tbody").append("<tr><td>" + value.id + "</td><td>" + value.descripcion + "</td><td>" + value.descripcionInsumo + "</td><td>" + value.cantidad + "</td><td>" + value.estado + "</td><td></td></tr>");
                });
                $('#tblInsumoMenu').dataTable();
            }
        });
    }
    $("#dateInto").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true});
    function loadTablePedidos2($id) {

        $('#myTabReportSaleDa2te a:last').tab('show');
        var params =
                {comprobante: $id
                };
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ListPedidosItem2", /* Llamamos a tu archivo */
            data: params, /* Ponemos los parametros de ser necesarios */
            type: "GET",
            contentType: "application/x-www-form-urlencoded",
            dataType: "json", /* Esto es lo que indica que la respuesta será un objeto JSon */
            success: function (data) {
                /* Supongamos que #contenido es el tbody de tu tabla */
                /* Inicializamos tu tabla */
                $("#tblPedido2 tbody").empty();
                /* Vemos que la respuesta no este vacía y sea una arreglo */
//                if (data != null && $.isArray(data)) {
                /* Recorremos tu respuesta con each */
                var total = 0;
                $.each(data, function (index, value) {
//                    if (value.totalTarjeta === "null") {
//                        value.totalTarjeta = "0";
//                    }
                    total = total + parseFloat(value.importe);
                    /* Vamos agregando a nuestra tabla las filas necesarias */
                    $("#tblPedido2 tbody").append("<tr><td>" + value.pedido + "</td><td>" + value.precio + "</td><td>" + value.cantidad + "</td><td>" + value.importe + "</td><td>" + value.mensaje + "</td> <td>" + value.pkPedido + "</td></tr>");
                });
                $("#lblTotalPedido2").html(total);
            }

//            }
        });
    }

    function loadTipoPedido($id) {
        $('#' + $id + ' option').remove();
        $('#' + $id).append("<option value=\"0\">Seleccione una opcion</option>")
        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=TipoPedido&&action=List', function (data) {
            for (var i = 0; i < data.length; i++) {

                $('#' + $id).append("<option value=\"" + data[i].id + "\">" + data[i].descripcion + "</option>")
            }

        });
    }


    function openModalSubRecetas() {
        $('#modalAddSubRecetas').modal('show')


    }

    function saveSubReceta(copy_id) {

        let params = {
            plato_id: copy_id,
            plato_copiar_id: $('#cmbSubReceta').val(),
            cantidad: $('#cantidadSubReceta').val(),
        }

        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=InsumoMenu&action=SaveSubReceta", /* Llamamos a tu archivo */
            data: params, 
            type: "GET",
            // contentType: "application/x-www-form-urlencoded",
            dataType: "json", 
            success: function (data) {
                location.reload()
            }
        });
    }