<?php require_once '../../../Components/Config.inc.php'; ?>
//<script>
    var timeWait = 5;

//    $.datepicker.setDefaults($.datepicker.regional['es']);

    /*
     * 
     * Function que envia por post los valores y guarda en la base de datos 
     * */
    function _sendSaveWorkPeople($names, $surnames, $_fkWorkStation, $_telf, $_fkUbigeo,
            $_fkTypeDocument, $_fkSexo, $_fkStatusCivil, $_fkProfesion, $_address, $_celPhone, $_dateBirth, $_document,
            $_email, $_fkAreaTrabajo) {
        var params = {
            names: $names,
            surname: $surnames,
            fkWorkStation: $_fkWorkStation,
            telf: $_telf,
            fkUbigeo: $_fkUbigeo,
            fkTypeDocument: $_fkTypeDocument,
            fkSexo: $_fkSexo,
            fkStatusCivil: $_fkStatusCivil,
            fkProfesion: $_fkProfesion,
            address: $_address,
            celPhone: $_celPhone,
            dateBirth: $_dateBirth,
            document: $_document,
            email: $_email,
            fkAreaTrabajo: $_fkAreaTrabajo

        }

        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=WorkPeople&&action=Save",
            type: 'POST',
            data: params,
            beforeSend: function() {
                beforeLoadImage('msgRgisterWorkPeople');
            },
//            dataType: 'json',
            success: function(data) {
                $('#msgRgisterWorkPeople').html("Se ha guardado Correctamente");

            }

        });


    }
    /**
     * function validate document
     * **/
    function _validateDocument() {
        var params = {document: $('#txtDocumentIdentityWorkPeopleRegister').val()}
        var img = $("img"),
                div = $("div");
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=WorkPeople&&action=ValidateDocument",
            type: 'POST',
            data: params,
            dataType: 'json',
            beforeSend: function() {
                beforeLoadImage('msgValidateDni');
            }, error: function() {

            },
            success: function(data) {
                setTimeout(function() {
//                    $('#loginRegisterWorkPeople').html("");
                    if (data == '0') {
                        $('#msgValidateDni').html("");
                        $('#btnSaveWorkPeople').prop('disabled', false);
                    }
                    else {
                        $('#msgValidateDni').html("Este Documenta ya Existe");
                        $('#txtDocumentIdentityWorkPeopleRegister').focus();
                        $('#btnSaveWorkPeople').prop('disabled', true);

                    }

                }, timeWait);
//                console.log(data);


            }

        });
    }
    /**
     * function validate email
     * **/
    function _validateEmail() {
        var params = {email: $('#txtEmailRegisterWorkPeople').val()}

        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=WorkPeople&&action=ValidateEmail",
            type: 'POST',
            data: params,
            dataType: 'json',
            beforeSend: function() {
                beforeLoadImage('msgValidateEmail');
            },
            success: function(data) {
                setTimeout(function() {
                    if (data == '0') {
                        $('#msgValidateEmail').html("");
                        $('#btnSaveWorkPeople').prop('disabled', false);
                    }
                    else {
                        $('#msgValidateEmail').html("Este email ya Existe");
                        $('#txtEmailRegisterWorkPeople').focus();
                        $('#btnSaveWorkPeople').prop('disabled', true);

                    }
//                    $('#loginRegisterWorkPeople').html("");
                }, timeWait);
//                console.log(data);


            }

        });
    }
    /*function que valida los datos antes de ser enviados*/
    function _saveWorkPeople() {

        if ($('#txtNameWorkPeopleRegister').val().length < 1) {

//            $('#txtNameWorkPeopleRegister').attr('title', "Campo es Obligatorio");
            $('#txtNameWorkPeopleRegister').focus();
            $('#txtNameWorkPeopleRegister').tooltip();
            $('#msgRgisterWorkPeople').html("El nombre es Obligatorio");
        }
        else if ($('#txtLastNameWorkPeopleRegister').val().length < 1) {
            $('#txtLastNameWorkPeopleRegister').focus();
            $('#txtLastNameWorkPeopleRegister').tooltip();
            $('#msgRgisterWorkPeople').html("Los Apellidos son Obligatorio");
        }
        else if ($('#txtMaternalSurnameWorkPeopleRegister').val().length < 1) {
            $('#txtMaternalSurnameWorkPeopleRegister').focus();
            $('#txtMaternalSurnameWorkPeopleRegister').tooltip();
            $('#msgRgisterWorkPeople').html("El campo es Obligatorio");
        }
        else if ($('#cmbRegisterTypeDocument').val() == 0) {
            $('#cmbRegisterTypeDocument').focus();
            $('#cmbRegisterTypeDocument').attr('title', "Debe Seleccionar una opcion");
            $('#cmbRegisterTypeDocument').tooltip();
            $('#msgRgisterWorkPeople').html("Debe Seleccionar una opcion");
        }
        else if ($('#txtDocumentIdentityWorkPeopleRegister').val().length != 8) {
            $('#txtDocumentIdentityWorkPeopleRegister').attr('title', "Debe Escribir los 8 ditos");
            $('#txtDocumentIdentityWorkPeopleRegister').focus();
            $('#txtDocumentIdentityWorkPeopleRegister').tooltip();
            $('#msgRgisterWorkPeople').html("El Dni es Obligatorio");
        }
        else if ($('#txtdatBirthRegisterWorkPeople').val().length < 1) {
//            $('#txtdatBirthRegisterWorkPeople').attr('title', "Fecha de nacimiento");
            $('#txtdatBirthRegisterWorkPeople').focus();
//            $('#txtdatBirthRegisterWorkPeople').tooltip();
            $('#msgRgisterWorkPeople').html("Fecha Obligatoria");
        }
        else if ($('#txtEmailRegisterWorkPeople').val().length < 1) {
            $('#txtEmailRegisterWorkPeople').attr('title', "Ingresar su correo electronico");
            $('#txtEmailRegisterWorkPeople').focus();
            $('#txtEmailRegisterWorkPeople').tooltip();
            $('#msgValidateEmail').html("Ingrese su Correo ");
        }
        else if ($('#cmbRegisterProfessions').val() == 0) {
            $('#cmbRegisterProfessions').focus();
            $('#cmbRegisterProfessions').attr('title', "Debe Seleccionar una opcion");
            $('#cmbRegisterProfessions').tooltip();
            $('#msgRgisterWorkPeople').html("Debe Seleccionar una opcion");
        }
        else if ($('#cmbRegisterStatusCivil').val() == 0) {
            $('#cmbRegisterStatusCivil').focus();
            $('#cmbRegisterStatusCivil').attr('title', "Debe Seleccionar una opcion");
            $('#cmbRegisterStatusCivil').tooltip();
            $('#msgRgisterWorkPeople').html("Debe Seleccionar una opcion");
        }
        else if ($('#cmbRegisterSexo').val() == 0) {
            $('#cmbRegisterSexo').focus();
            $('#cmbRegisterSexo').attr('title', "Debe Seleccionar una opcion");
            $('#cmbRegisterSexo').tooltip();
            $('#msgRgisterWorkPeople').html("Debe Seleccionar una opcion");
        }
        else if ($('#cmbRegisterDeparment').val() == 0) {
            $('#cmbRegisterDeparment').focus();
            $('#cmbRegisterDeparment').attr('title', "Debe Seleccionar una opcion");
            $('#cmbRegisterDeparment').tooltip();
            $('#msgRgisterWorkPeople').html("Debe Seleccionar una opcion");
        }
        else if ($('#cmbRegisterProvince').val() == 0) {
            $('#cmbRegisterProvince').focus();
            $('#cmbRegisterProvince').attr('title', "Debe Seleccionar una opcion");
            $('#cmbRegisterProvince').tooltip();
            $('#msgRgisterWorkPeople').html("Debe Seleccionar una opcion");
        }
        else if ($('#cmbRegisterDisctrict').val() == 0) {
            $('#cmbRegisterDisctrict').focus();
            $('#cmbRegisterDisctrict').attr('title', "Debe Seleccionar una opcion");
            $('#cmbRegisterDisctrict').tooltip();
            $('#msgRgisterWorkPeople').html("Debe Seleccionar una opcion");
        } else if ($('#txtAddressWorkPeopleRegister').val().length < 1) {
            $('#txtAddressWorkPeopleRegister').attr('title', "Ingresar su correo electronico");
            $('#txtAddressWorkPeopleRegister').focus();
            $('#txtAddressWorkPeopleRegister').tooltip();
            $('#msgRgisterWorkPeople').html("Correo Electronico");
        }
        else if ($('#cmbRegisterArea').val() == 0) {
            $('#cmbRegisterArea').focus();
            $('#cmbRegisterArea').attr('title', "Debe Seleccionar una opcion");
            $('#cmbRegisterArea').tooltip();
            $('#msgRgisterWorkPeople').html("Debe Seleccionar una opcion");
        }
        else if ($('#cmbRegisterWorkStation').val() == 0) {
            $('#cmbRegisterWorkStation').focus();
            $('#cmbRegisterWorkStation').attr('title', "Debe Seleccionar una opcion");
            $('#cmbRegisterWorkStation').tooltip();
            $('#msgRgisterWorkPeople').html("Debe Seleccionar una opcion");
        }
        else {
            var $names = $('#txtNameWorkPeopleRegister').val(),
                    $surnames = $('#txtLastNameWorkPeopleRegister').val() + " " + $('#txtMaternalSurnameWorkPeopleRegister').val(),
                    $_fkWorkStation = $('#cmbRegisterWorkStation').val(),
                    $_telf = $('#txtTelfRegisterWorkPeople').val(),
                    $_fkUbigeo = $('#cmbRegisterDisctrict').val(),
                    $_fkTypeDocument = $('#cmbRegisterTypeDocument').val(),
                    $_fkSexo = $('#cmbRegisterSexo').val(),
                    $_fkStatusCivil = $('#cmbRegisterStatusCivil').val(),
                    $_fkProfesion = $('#cmbRegisterProfessions').val(),
                    $_address = $('#txtAddressWorkPeopleRegister').val(),
                    $_celPhone = $('#txtPhoneRegisterWorkPeople').val(),
                    $_dateBirth = $('#txtdatBirthRegisterWorkPeople').val(),
                    $_document = $('#txtDocumentIdentityWorkPeopleRegister').val(),
                    $_email = $('#txtEmailRegisterWorkPeople').val(),
                    $_fkAreaTrabajo = $('#cmbRegisterArea').val();
            _sendSaveWorkPeople($names, $surnames, $_fkWorkStation, $_telf, $_fkUbigeo,
                    $_fkTypeDocument, $_fkSexo, $_fkStatusCivil, $_fkProfesion, $_address, $_celPhone, $_dateBirth, $_document,
                    $_email, $_fkAreaTrabajo);

        }


    }
    
    function beforeLoadImage(nameid) {
        $('#' + nameid).html('<img src="<?php echo Class_config::get('urlApp') ?>/Public/images/preload.gif" />procesando...');

    }

