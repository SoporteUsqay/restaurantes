<?php require_once '../../../../Components/Config.inc.php'; ?>

//<script>
    $(document).ready(function () {
        $('#tblInsumo').DataTable();
    });
    function listar(id) {
        $.getJSON('<?php echo Class_config::get('urlApp') ?>?controller=Insumo&&action=ListId&id=' + id, {op: 'get', id: id}, function (data) {

            $('#id').val(data[0].id);
            $('#descripcion').val(data[0].descripcion);
            if (data[0].cantidad == 0) {
                $("#btnQuitar").prop('disabled', true);
            }
            else
                $("#btnQuitar").prop('disabled', false)

        });

    }

    function guardarInsumoEntrada($tipo) {
        if ($('#frmInsumo').valid() === true) {


            $.post("<?php echo Class_config::get('urlApp') ?>/?controller=IngresosInsumos&action=Save&tipo=" + $tipo, $('#frmInsumo').serialize(), function (data) {
                if (data === 0) {
                    $('body,html').animate({
                        scrollTop: 0
                    }, 800);
                    $('#merror').show('fast').delay(3000).hide('fast');
                } else {
                    $('body,html').animate({
                        scrollTop: 0
                    }, 800);
                    $('#msuccess').show('fast').delay(4000).hide('fast');
                    location.reload();
                }

            });
        }
    }
