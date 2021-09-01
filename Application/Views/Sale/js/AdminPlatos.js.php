<?php require_once '../../../../Components/Config.inc.php'; ?>
//<script>
    var url = "<?php echo Class_config::get('urlApp') ?>/?controller=Sale&&action=Saveplato";
    $(document).ready(function ()
    {
        $("#tblPlatos").DataTable({
            dom: 'Blfrtip',
            stateSave: true,
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Platos',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6]
                    },
                },
            ]
        });

        _listCategoriasSucursal('cmbRegisterCategoria');
        $("#cmbRegisterCategoria").val(1);
        $('#tipo_sunat').select2({
          ajax: { 
           url: "<?php echo Class_config::get('urlApp') ?>/reportes/ws/codigos_sunat.php",
           type: "post",
           dataType: 'json',
           delay: 250,
           data: function (params) {
            return {
               term: params.term
            };
           },
           processResults: function (data) {
                return {
                    results: $.map(data.results, function (item) {
                        return {
                            text: item.id+" - "+decodeURIComponent(unescape(item.descripcion)),
                            id: item.id
                        };
                    })
                };
            }
          },
          placeholder: 'Codigo Sunat (Obligatorio)',
          minimumInputLength: 0
         });
    });
    function guardarPlatos() {
        $.validator.messages.required = '';
        if ($('#frmPlatos').valid() === true) {
            $.ajax({
                type: "POST",
                url: url,
                data: $("#frmPlatos").serialize(), // Adjuntar los campos del formulario enviado.
                dataType: 'html',
                success: function (data) {
                    if (data === "false") {
                        $('body,html').animate({
                            scrollTop: 0
                        }, 500);
                        $('#merror').show('fast').delay(3000).hide('fast');

                    } else {
                        $('body,html').animate({
                            scrollTop: 0
                        }, 500);
                        $('#msuccess').show('fast').delay(3000).hide('fast');
                        location.reload();
                    }
                }
            });
        }else{
            alert("Todos los campos son obligatorios!");
        }
    }
    
    function sel(id) {
        url = "<?php echo Class_config::get('urlApp') ?>/?controller=Sale&&action=UpdatePlato";
        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Platos&&action=ListId&id=' + id, {op: 'get', id: id}, function (data) {
            $('#id').val(data[0].id);
            $('#descripcion').val(data[0].descripcion);
            $('#cmbRegisterCategoria').val(data[0].pkCategoria);
            _loadTiposCategoria('cmbRegisterCategoria', 'cmbTipo');
            setTimeout(function () {
                $('#cmbTipo').val(data[0].pkTipo);
            }, 1500);

            $('#precioventa').val(data[0].precioVenta);
            $('#stockMinimo').val(data[0].stockMinimo);

            $("#tipo_articulo").val(data[0].tipo_articulo);
            $("#tipo_impuesto").val(data[0].tipo_impuesto);
            
            $('#tipo_sunat')
            .empty()
            .append('<option selected value="'+data[0].id_sunat+'">'+data[0].id_sunat+' - '+decodeURIComponent(escape(data[0].descripcion_sunat))+'</option>');
            $('#tipo_sunat').select2('data', {
              id: data[0].id_sunat,
              label: decodeURIComponent(escape(data[0].descripcion_sunat))
            });
            $('#tipo_sunat').trigger('change');
        });

    }