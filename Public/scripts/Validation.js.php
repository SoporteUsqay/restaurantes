<?php require_once '../../Components/Config.inc.php'; ?>
//<script>
    /* ================================================ *
     *      Validando el formulario de Registro         *
     * ================================================ *
     */

    /**
     * Validando combos
     * @param {String} $id id del elemento a validar
     * @return {String} Mensaje Validador
     * 
     **/
    function validateCombos($id) {
        console.log()
        if ($('#' + $id).val() == "0") {

            $('#' + $id).attr('title', "Campo es Obligatorio");
            $('#' + $id).focus();
            $('#' + $id).tooltip();
//            $('#msgRgisterWorkPeople').html("El nombre es Obligatorio");
            return  false;
        }
        else
            return true;
    }
    /**
     * Valida que el Campo no este vacio
     * */
    function validateCamposVacios($id) {
        $clase = $("#div" + $id).attr('class');
        if ($('#' + $id).val().length < 1) {
            $nuevaClase = $clase + ' form-group has-error has-feedback';
            $("#div" + $id).attr('class', $nuevaClase);
            $('#' + $id).attr('title', "Campo no Puede ser vacio");
            $('#' + $id).focus();
            $('#' + $id).tooltip();
//            $('#msgRgisterWorkPeople').html("El nombre es Obligatorio");
            return  false;
        }
        else {
            $nuevaClase = $clase;
            $("#div" + $id).attr('class', $nuevaClase);
            return true;
        }
    }


    function beforeLoadImage(nameid) {
        $('#' + nameid).html('<img src="<?php echo Class_config::get('urlApp') ?>/Public/images/preload.gif" />procesando...');

    }

    function datePicker($nameId) {
        $("."+$nameId).datepicker({dateFormat: 'yy-mm-dd'});
    }
    /*Funcion para dar mascara a la factura*/

    function txtSerieSale($nameId) {
        $("#" + $nameId).mask("999-99999999");
    }
    function txtFacture($nameId) {
        $("#" + $nameId).mask("999-9999999");
    }
    function txtGuie($nameId) {
        $("#" + $nameId).mask("999-99999999");
    }
    function nDni($nameId) {
        $("#" + $nameId).mask("99999999");
    }
    function nRuc($nameId) {
        $("#" + $nameId).mask("99999999999");
    }
    function formatTelefono($nameId) {
        $("#" + $nameId).mask("(9999)-(99999)");
    }
    function date($id){
        $("."+$id).datepicker({dateFormat: 'yy-mm-dd', changeMonth: true});
    }

