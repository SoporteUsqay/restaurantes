<?php require_once '../../Components/Config.inc.php'; ?>
//<script>
    $(document).ready(function () {

    });

    function nobackbutton() {

        window.location.hash = "no-back-button";
        window.location.hash = "Again-No-back-button"; //chrome
        window.onhashchange = function () {
            window.location.hash = "no-back-button";
        };
    }
    function clave($val) {
        var $text = $('#userPassword').val();
        $('#userPassword').val($text + $val);
    }
    
    function searchCustomerDNI($document, $nombres, $apellidos) {
        $("#" + $nombres).val("");
        $("#" + $apellidos).val("");
        var param = {'document': $document
        };
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Cliente&&action=ClienteDni",
            type: 'POST',
            data: param,
            cache: true,
            dataType: 'json',
            success: function (data) {
                $("#" + $nombres).val(data[0].nombres);
                $("#" + $apellidos).val(data[0].apellidos);
            }
        });
    }
    
    $(window).load(function () {
        _listCategoriasProductos('cmbCategoriaFiltro');
        _loadProvedor('txtEmpresaProvedoraFP');

        $("#dateInto2Day").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true,
        onClose: function (selectedDate) {
            $("#dateEnd2Day").datepicker("option", "minDate", selectedDate);
        }});
    
        $("#dateEnd2Day").datepicker({dateFormat: 'yy-mm-dd', defaultDate: "+1w",
        changeMonth: true,
        onClose: function (selectedDate) {
            $("#dateInto2Day").datepicker("option", "maxDate", selectedDate);
        }});
    });
    