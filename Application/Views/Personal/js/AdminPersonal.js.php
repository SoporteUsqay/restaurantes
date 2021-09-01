<?php require_once '../../../../Components/Config.inc.php'; ?>

//<script>
    $(document).ready(function() {
        $('#tblEmpleados').DataTable();
        loadTypeUser('cmbTipoUsuario');
    });

    function loadTypeUser($id) {
        $('#' + $id + ' option').remove();
        $('#' + $id).append("<option value=\"0\">Seleccione una opcion</option>")
        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=TypeUser&&action=ListUserType', function(data) {
            for (var i = 0; i < data.length; i++) {

                $('#' + $id).append("<option value=\"" + data[i].id + "\">" + data[i].description + "</option>")
            }
        });
    }

    function insert() {
        var url = "<?php echo Class_config::get('urlApp') ?>/?controller=WorkPeople&&action=Save";
        $.post(url, $("#frmEmpleados").serialize(), function(data) {
            if (data === 0) {
                $('body,html').animate({
                    scrollTop: 0
                }, 800);
                $('#merror').show('fast').delay(4000).hide('fast');
            }
            else {
                $('body,html').animate({
                    scrollTop: 0
                }, 800);
                $('#msuccess').show('fast').delay(4000).hide('fast');
                location.reload();
            }
        }, 'json');


    }
    function destroyEmpleado($id) {

        $.post('<?php echo Class_config::get('urlApp') ?>?controller=WorkPeople&&action=Drop', {pkWorkPeople: $id}, function(result) {
            if (result === 0) {
                $('body,html').animate({
                    scrollTop: 0
                }, 800);
                $('#merror').show('fast').delay(4000).hide('fast');
            }
            else {
                $('body,html').animate({
                    scrollTop: 0
                }, 800);
                $('#msuccess').show('fast').delay(4000).hide('fast');
                location.reload();
            }
        }, 'json');

    }
    function activaUlBuscar() {
        url = "<?php echo Class_config::get('urlApp') ?>/?controller=WorkPeople&&action=Save";
    }

    function sel(id) {
        url = "<?php echo Class_config::get('urlApp') ?>/?controller=WorkPeople&&action=Update&id=" + id;
        $.getJSON('<?php echo Class_config::get('urlApp') ?>?controller=WorkPeople&&action=ListTrabadoresID&id=' + id, {op: 'get', id: id}, function(data) {
            $('#id').val(data[0].pkTrabajador);

            $('#nombres').val(data[0].nombres);

            $('#dni').val(data[0].documento);

            $('#usuario').val(data[0].usuario);

            $('#contra').val(data[0].contra);
            $('#direccion').val(data[0].direccion);
            $('#cmbTipoUsuario').val(data[0].pkTipoTrabajador);
            $('#txtApellidosTrabajador').val(data[0].apellidos);

        });
    }