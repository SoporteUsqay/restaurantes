<?php require_once '../../../../Components/Config.inc.php'; ?>
//<script>
    
     function Numero(e)
    {
        var keynum = window.event ? window.event.keyCode : e.which;
        if ((keynum == 8) || (keynum == 46))
            return true;

        return /\d/.test(String.fromCharCode(keynum));
    }
    
    $(document).ready(function () {
        
        $('#tblMesasActivas').DataTable();
        $('#tblMesasInactivo').DataTable();
        
        ListComboSalon();
        ListComboCrearMesas();
        
        document.getElementById("nomMesa").disabled=true;
        document.getElementById("cmbPrefijoMesas").disabled=false;

    });
    
    function HabilitarMesas($id) {
            $('#modalEliminarMesa').modal('show');
            $('#tituloModalMesa').html('Habilitando Mesa...');
            $('#txtMensajeeliminarMesa').html('¿Seguro que desea habilitar la Mesa Seleccionado?');
            $('#id').val($id);
            $('#btnColor').removeClass();
            $('#btnColor').addClass('btn btn-primary');
            url = "<?php echo class_config::get('urlApp') ?>/?controller=Config&action=ActiveMesa";
        }
    
    function DesHabilitarMesas($id) {
            $('#modalEliminarMesa').modal('show');
            $('#tituloModalMesa').html('Eliminando Mesa...');
            $('#txtMensajeeliminarMesa').html('¿Seguro que desea eliminar esta Mesa?');
            $('#id').val($id);
            $('#btnColor').removeClass();
            $('#btnColor').addClass('btn btn-danger');
            url = "<?php echo class_config::get('urlApp') ?>/?controller=Config&action=DeleteMesa";
        }
    
    function deleteMesa() {
            $.post(url, {id: $('#id').val()},
            function (data) {
                location.reload();
            });
            $('#modalEliminarMesa').modal('hide');
        }
    
    function ListComboSalon(){
        
        var $id = "cmbSalon";
        $('#' + $id + ' option').remove();
        $('#' + $id).append("<option value=\"0\">Seleccione una opcion</option>");

        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Config&action=ListSalones', function (data) {

            for (var i = 0; i < data.length; i++) {
                if((parseInt(data[i].idsalon) < 43) || (parseInt(data[i].idsalon) > 44)){
                    $('#' + $id).append("<option value=\"" + data[i].idsalon + "\">" + data[i].salon + "</option>");
                }
            }
            $('#cmbSalon').val(getpkSalon());

        });
    }
    
    function _listPrefijoMesa($idsalon,$idmesa) {
        $('#'+$idmesa+' option').remove();
        var pro= $('#'+$idsalon).val();
        $('#'+$idmesa).append("<option value=\"0\">Seleccione una opcion</option>")
        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Config&action=ListNombreMesas&pkIdSalon='+pro, function(data) {

            for (var i = 0; i < data.length; i++) {

                $('#'+$idmesa).append("<option value=\"" + data[i].id + "\">" + data[i].nombre_mesa + "</option>")
//            $('#txtModifyUserType').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
//            $('#cmbWorkStationModifyPermissions').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
            }

        });
    }
    
    
    function ListComboCrearMesas(){
        
        var $id = "cmbCrearMesas";
        $('#' + $id + ' option').remove();
        $('#' + $id).append("<option value=\"0\">Seleccione una opcion</option>")

        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Config&action=ListSalones', function (data) {

            for (var i = 0; i < data.length; i++) {

                $('#' + $id).append("<option value=\"" + data[i].idsalon + "\">" + data[i].salon + "</option>")

            }
            $('#cmbCrearMesas').val();

        });
    }
    
     function registrarMesas() {
        $('#modalCrear_Mesas').modal('show');
        $('#tituloModalCrear_Mesas').html('Registrando Mesas');
        $('#id').val("");
        $('#cmbCrearMesas').val(0);
        $('#nomMesa').val("");
        $('#cantidad').val("");
        $('#desde').val("");
        $('#hasta').val("");
        url1 = "<?php echo class_config::get('urlApp') ?>/?controller=Config&action=CrearMesas";
    }
    
    function CrearMesas()
           {
            $.post(url1,$("#frmCrear_Mesas").serialize(),
            function (data) {
                location.reload();
            });
            $('#modalCrear_Mesas').modal('hide');
        }
    
//    var url1 = "<?php echo Class_config::get('urlApp') ?>/?controller=Config&&action=CrearMesas";
//    $("#frmCrear_Mesas").submit(function (event) {
//        CrearMesas();
//    });
    function CrearMesas1() {
        $.post(url, $("#frmCrear_Mesas").serialize(), function (data) {
            if (data === 0) {
                $('body,html').animate({
                    scrollTop: 0
                }, 800);
                $('#merror').show('fast').delay(4000).hide('fast');
            }
            else {
//                $('#frmEmpleados').reset();
                $('body,html').animate({
                    scrollTop: 0
                }, 800);
                $('#msuccess').show('fast').delay(4000).hide('fast');
//                location.reload();
            }
        }, 'json');
//        }
    }
    
    
    function CrearMesas10() {
//        var param = {IdSalon: $('#cmbCrearMesas').val(), Mesa: $('#txtNombreMesa').val(), Totalmesa: $('#txtTotalMesas').val()};
        
         $.post('<?php echo Class_config::get('urlApp') ?>?controller=Config&&action=CrearMesas&IdSalon='+$('#cmbCrearMesas').val()+'&Mesa='+$('#txtNombreMesa').val()+'&Totalmesa='+$('#txtTotalMesas').val(), function (result) {
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
    
   
    
    var url = "<?php echo Class_config::get('urlApp') ?>/?controller=Config&&action=SaveMesa";
//    $("#frmMesas").submit(function (event) {
//        GuardarMesas();
//    });
    function GuardarMesas() {
        if ($("#frmMesas").valid()) {

        $.post(url, $("#frmMesas").serialize(), function (data) {
            if (data === 0) {
                $('body,html').animate({
                    scrollTop: 0
                }, 800);
                $('#merror').show('fast').delay(4000).hide('fast');
            }
            else {
//                $('#frmEmpleados').reset();
                $('body,html').animate({
                    scrollTop: 0
                }, 800);
                $('#msuccess').show('fast').delay(4000).hide('fast');
                location.reload();
            }
        }, 'json');
        }
    }
    function sel(id) {
        url = "<?php echo Class_config::get('urlApp') ?>/?controller=Config&&action=UpdateMesa&id=" + id;
//        console.log(id);
        $.getJSON('<?php echo Class_config::get('urlApp') ?>?controller=Config&&action=ListMesaID&id=' + id, {op: 'get', id: id}, function (data) {

//        if (data !== 0) {

            $('#id').val(data[0].id);

            $('#descripcion').val(data[0].nmesa);

            $('#cmbSalon').val(data[0].pkSalon);

            $('#estado').val(data[0].estado);

        });
    }
    
    function CambiarEstadoMesas($idMesa,$estado){
        
        $.post('<?php echo Class_config::get('urlApp') ?>?controller=Config&&action=EliminarMesaEspecifica&id='+$idMesa+'&estado='+$estado, function (result) {
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
    
    function buscarMesas() {
        window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Config&action=ShowMesas&pkSalon=" + $("#cmbSalon").val();
    }
    
    
    
    