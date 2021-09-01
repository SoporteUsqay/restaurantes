<?php require_once '../../../../Components/Config.inc.php'; ?>

//<script>
    $(document).ready(function() {
        $("#txtfechaini").datepicker({dateFormat: 'yy-mm-dd'});

    });

    function buscar() {
        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Report&&action=ListSalidaInsumo', {'date': $("#txtfechaini").val()}, function(data) {
            $("#tblInsumos tbody").empty();
            /* Vemos que la respuesta no este vacía y sea una arreglo */
//                if (data != null && $.isArray(data)) {
            /* Recorremos tu respuesta con each */

            $.each(data, function(index, value) {
                $("#tblInsumos tbody").append("<tr><td style='visibility: hidden; display:none'>" + value.id + "</td><td>" + value.descripcioninsumo + "</td><td>" + value.Total + "</td><td>" + value.descripcion + "</td></tr>");

            });
            $('#tblInsumos').dataTable();

        });
    }

    function consolidadoInsumosExcel() {
        var url = "<?php echo Class_config::get('urlApp') ?>/xls_consolidadoInsumos.php?fecha="+$("#txtfechaini").val();
        window.open(url, '_blank');
    }