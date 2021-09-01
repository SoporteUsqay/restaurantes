<?php require_once '../../../../Components/Config.inc.php'; ?>
//<script>

    function _listarCategoria() {
        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Categoriat&&action=_listCategoria', function(data) {
            $("#tbl_categoria tbody").empty();

            $.each(data, function(index, value) {
                $("#tbl_categoria tbody").append("<tr><td>" + value.id + "</td><td>" + value.descripcion +
                        "</td><td>" + "<a onclick='editarCategoria(" + value.id + ",\"" + value.descripcion + "\")'><span class='glyphicon glyphicon-pencil'></span></a>" + "</td><td>" +
                        "<a onclick='eliminarCategoria(" + value.id + ")'><span class='glyphicon glyphicon-remove'></span></a></td></tr>");
            });
            $('#tbl_categoria').dataTable();

        });
    }
