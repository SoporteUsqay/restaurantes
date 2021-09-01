<?php require_once '../../../../Components/Config.inc.php'; ?>

//<script>
    $(document).ready(function() {
        $('#tblInsumos').DataTable({
            dom: 'Blfrtip',
            "order": [[0, "desc"]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Insumos',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7]
                    },
                },
                {
                    extend: 'pdfHtml5',
                    orientation: 'landscape',
                    alignment: 'center',
                    pageSize: 'LEGAL',
                    customize: function(doc) {
                        doc.content[1].margin = [ 100, 0, 100, 0 ] //left, top, right, bottom
                    },
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7]
                    },
                    title: 'Insumos',
                },
                {
                    extend: 'print',
                    orientation: 'landscape',
                    pageSize: 'LEGAL',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7]
                    },
                    title: 'Insumos',
                }
        ]
        });
        loadTipoInsumo('cmbTipoInsumo');
        loadProvedor('cmbProvedor');
        loadInsumo('cmbInsumo');
        $(".alert button.close").click(function(e) {
            $(this).parent().fadeOut('slow');
        });
        $(".alert-dismissable").click(function(e) {  
            $(this).fadeOut('slow');
        });
    });

    function loadTipoInsumo($id) {
        $('#' + $id + ' option').remove();
        $('#' + $id).append("<option value=\"\">Seleccione Tipo de Insumo</option>")
        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=TipoInsumo&&action=List', function(data) {
            for (var i = 0; i < data.length; i++) {

                $('#' + $id).append("<option value=\"" + data[i].id + "\">" + data[i].descripcion + "</option>")
            }

        });
    }
    
    function loadProvedor($id) {
        $('#' + $id + ' option').remove();
        $('#' + $id).append("<option value=''>Seleccione Proveedor Principal</option>")
        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Provedor&&action=List', function(data) {
            for (var i = 0; i < data.length; i++) {

                $('#' + $id).append("<option value=" + data[i].id + ">" + data[i].descripcion + "</option>")
            }

        });
    }
    
    function loadInsumo($id) {
        $('#' + $id + ' option').remove();
        $('#' + $id).append("<option value=\"\">Seleccione Unidad de Medida</option>")
        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Almacen&&action=ListInsumo', function(data) {
            for (var i = 0; i < data.length; i++) {
                $('#' + $id).append("<option value=\"" + data[i].id + "\">" + data[i].descripcion + "</option>")
            }

        });
    }
    
    url = "<?php echo Class_config::get('urlApp') ?>/?controller=Insumo&action=Save";
    function guardarInsumo() {
        if ($('#frmInsumo').valid() === true)
            $.post(url, $('#frmInsumo').serialize(), function(data) {
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
    function sel(id) {
        url = "<?php echo Class_config::get('urlApp') ?>/?controller=Insumo&&action=Update&id=" + id;
        $.getJSON('<?php echo Class_config::get('urlApp') ?>?controller=Insumo&&action=ListId&id=' + id, function(data) {

            //        if (data !== 0) {

            $('#id').val(data[0].id);

            $('#descripcion').val(data[0].descripcion);

            $('#stockMinimo').val(data[0].stockMinimo);

            $('#precio_promedio').val(data[0].precio_promedio);
            
            //$('#cmbInsumo').val(data[0].provedor);
            $('#cmbProvedor').val(data[0].provedor);
            $('#cmbInsumo').val(data[0].pkUnidad);
            $('#cmbTipoInsumo').val(data[0].pkTipoInsumo);
            $('#estado').val(data[0].estado);
            $('#porcentajeMerma').val(data[0].porcentaje_merma);

        });

    }
    function del($id) {

        if (!confirm("¿Está seguro que desea eliminar este insumo?")) return

        $.post("<?php echo Class_config::get('urlApp') ?>/?controller=Insumo&action=Delete", {id: $id}, function(data) {
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

    function active($id) {
        $.post("<?php echo Class_config::get('urlApp') ?>/?controller=Insumo&action=Active", {id: $id}, function(data) {
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

    function RegistrarProveedor()
    {
        window.open('<?php echo Class_config::get('urlApp') ?>/?controller=Almacen&action=ShowAdminProveedor', '_blank');
    }

    function RegistrarTipo()
    {
        window.open('<?php echo Class_config::get('urlApp') ?>/?controller=Almacen&action=AdminTipoInsumo', '_blank');
    }