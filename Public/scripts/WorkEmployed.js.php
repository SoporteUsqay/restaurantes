<?php require_once '../../Components/Config.inc.php'; ?>
//<script>
    $(document).ready(function() {


        var availableTags;
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=WorkPeople&&action=List",
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                $("#txtRegisterEmployed").autocomplete({
                    source: data,
                    select: function(event, ui) {
                        $("#txtRegisterEmployed").val(ui.item.label);
                        $("#txtRegisterEmployed-id").val(ui.item.value);
//                        $("#txtRegisterEmployed-description").html(ui.item.desc);
//                        $("#txtRegisterEmployed-icon").attr("src", "images/" + ui.item.icon);

                        return false;
                    }
                });
//           
            }

        });
        $('#txtRegisterPassword').keyup(function()
        {
            var result = checkStrength($('#txtRegisterPassword').val());
            $('#resultRegisterPassword').html(result)
            if (result == '<?php echo Class_message::get('msgTextPasswordLentShort') ?>') {
                /*Deshabilitar el combo cmbRegisterTypeUser*/
                $('#txtRegisterConfirmPassword').prop('disabled', true);
            }
            else if (result == '<?php echo Class_message::get('msgTextPasswordWeak') ?>') {
                /*Deshabilitar el combo cmbRegisterTypeUser*/
                $('#txtRegisterConfirmPassword').prop('disabled', true);
            }
            else if (result == '<?php echo Class_message::get('msgTextPasswordGood') ?>') {
                /*habilitar el combo cmbRegisterTypeUser*/
                $('#txtRegisterConfirmPassword').prop('disabled', false);
                $('#MsgRegisterConfirmPassword').html("<?php echo Class_message::get('msgTextConfirmPasswordUser') ?>");
//                $('#msgRegisterUser').html("<?php echo Class_message::get('msgTextSelectedOption') ?>");
            }
            else if (result == '<?php echo Class_message::get('msgTextPasswordStrong') ?>') {
                /*habilitar el combo cmbRegisterTypeUser*/
                $('#txtRegisterConfirmPassword').prop('disabled', false);
//                $('#msgRegisterUser').html("<?php echo Class_message::get('msgTextSelectedOption') ?>");
                $('#MsgRegisterConfirmPassword').html("<?php echo Class_message::get('msgTextConfirmPasswordUser') ?>");
            }
        });
        /*Validar si las contraseñan coinciden*/
        $('#txtRegisterConfirmPassword').keyup(function()
        {
            var password = $('#txtRegisterPassword').val();
            var confirmPassword = $('#txtRegisterConfirmPassword').val();

            if (password == confirmPassword) {
                $('#msgRegisterUser').html("<?php echo Class_message::get('msgTextSelectedOption') ?>");
                $('#MsgRegisterConfirmPassword').html("Las contraseñas coindiden");
                $('#cmbRegisterTypeUser').prop('disabled', false);
            }
            else {
                $('#MsgRegisterConfirmPassword').html("Las contraseñas no coindiden");
                $('#cmbRegisterTypeUser').prop('disabled', true);
            }
        });

        /*
         checkStrength is function which will do the 
         main password strength checking for us
         */

        function checkStrength(password)
        {
            //initial strength
            var strength = 0

            //if the password length is less than 6, return message.
            if (password.length < 6) {
                $('#resultRegisterPassword').removeClass()
                $('#resultRegisterPassword').addClass('short');
                return '<?php echo Class_message::get('msgTextPasswordLentShort') ?>'
            }

            //length is ok, lets continue.

            //if length is 8 characters or more, increase strength value
            if (password.length > 7)
                strength += 1

            //if password contains both lower and uppercase characters, increase strength value
            if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/))
                strength += 1

            //if it has numbers and characters, increase strength value
            if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/))
                strength += 1

            //if it has one special character, increase strength value
            if (password.match(/([!,%,&,@,#,$,^,*,?,_,~])/))
                strength += 1

            //if it has two special characters, increase strength value
            if (password.match(/(.*[!,%,&,@,#,$,^,*,?,_,~].*[!,%,&,@,#,$,^,*,?,_,~])/))
                strength += 1

            //now we have calculated strength value, we can return messages

            //if value is less than 2
            if (strength < 2)
            {
                $('#resultRegisterPassword').removeClass()
                $('#resultRegisterPassword').addClass('weak');
                return '<?php echo Class_message::get('msgTextPasswordWeak') ?>'
            }
            else if (strength == 2)
            {
                $('#resultRegisterPassword').removeClass()
                $('#resultRegisterPassword').addClass('good')

                return '<?php echo Class_message::get('msgTextPasswordGood') ?>';
            }
            else
            {
                $('#resultRegisterPassword').removeClass()
                $('#resultRegisterPassword').addClass('strong');
                return '<?php echo Class_message::get('msgTextPasswordStrong') ?>'
            }
        }

    });
    /*Valida que el campo txtRegisterEmployed no se encuentre vacio, caso contrario habilita
     * el campo para ingresar un usuario*/
    function _validationInputNames() {
        if ($('#txtRegisterEmployed').val().length < 1) {
            $('#msgRegisterUser').html("<?php echo Class_message::get('msgTextNull') ?>");
//            $('#txtRegisterEmployed').focus();
//            $('#txtRegisterEmployed').tooltip();
            $('#txtRegisterUser').prop('disabled', true);
        }
        else {
            $('#txtRegisterUser').prop('disabled', false);
            $('#msgRegisterUser').html("<?php echo Class_message::get('TxtEnterYourUser') ?>");
        }

    }
    /*valida el registro de un usuario, ademas busca si no existe en la base de datos,
     * en caso este correcto se habilita el campo de txtRegisterPassword*/
    function _validationInputUser() {
        var userLength = $('#txtRegisterUser').val().length;
        if (userLength < 1) {
            $('#msgRegisterUser').html("<?php echo Class_message::get('msgTextNull') ?>");
            $('#txtRegisterPassword').prop('disabled', true);
        }
        else if (userLength < 4) {
            $('#msgRegisterUser').html("<?php echo Class_message::get('msgTextUserLentShort') ?>");
            $('#txtRegisterPassword').prop('disabled', true);
        }
        else {
            $.ajax({
                url: "<?php echo Class_config::get('urlApp') ?>/?controller=User&&action=SearchUser",
                data: {userName: $('#txtRegisterUser').val()},
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    if (data == "0") {
                        $('#msgRegisterUser').html("<?php echo Class_message::get('msgUserInputExits') ?>");
                        $('#txtRegisterPassword').prop('disabled', true);
                    }
                    else if (data == "1") {
                        $('#msgRegisterUser').html("<?php echo Class_message::get('msgUserCorrect') ?>");
                        $('#txtRegisterPassword').prop('disabled', false);
                    }
//           
                }

            });
        }

    }

    /*Valida que aya seleccionado una opcion
     * 
     * */

    function validateCmbRegisterOption() {
        var selected = $('#cmbRegisterTypeUser').val();
        if (selected == 0) {
            $('#BtnRegisterUser').prop('disabled', true);
            $('#msgRegisterUser').html("<?php echo Class_message::get('msgTextSelectedOption') ?>");
        }
        else {
            $('#BtnRegisterUser').prop('disabled', false);
            $('#msgRegisterUser').html("<?php echo Class_message::get('msgTextAceeptSave') ?>");
        }

    }
    /*Limpia todos los campos
     * 
     * */

    function clearInputsRegiserUser() {
        $('#resultRegisterPassword').html("");
        $('#msgRegisterUser').html("");
        $('#BtnRegisterUser').prop('disabled', true);
        $('#cmbRegisterTypeUser').prop('disabled', true);
        $('#txtRegisterPassword').prop('disabled', true);
        $('#txtRegisterUser').prop('disabled', true);
        $('#txtRegisterEmployed').prop('disabled', false);
//        $("#formRegisterUser").reset();
    }

    /*Function para guardar un usuario*/
    function _registerUser() {
        var workPeople = $('#txtRegisterEmployed').val();
        if (workPeople.length == 0) {
            ('#msgRegisterUser').html("No se Puede Guardar, no hay empleado");
        }
        else {
            var params = {fkWorkPeople: $('#txtRegisterEmployed-id').val(),
                fkTypeUser: $('#cmbRegisterTypeUser').val(),
                UserName: $('#txtRegisterUser').val(),
                UserPassword: $('#txtRegisterPassword').val()
            }

            $.ajax({
                url: "<?php echo Class_config::get('urlApp') ?>/?controller=User&&action=Save",
                type: 'POST',
                data: params,
//            dataType: 'json',
                success: function(data) {
                    $('#msgRegisterUser').html("Se ha guardado Correctamente");
                    $('#resultRegisterPassword').html("");
//                    $('#msgRegisterUser').html("");
                    $('#BtnRegisterUser').prop('disabled', true);
                    $('#cmbRegisterTypeUser').prop('disabled', true);
                    $('#txtRegisterPassword').prop('disabled', true);
                    $('#txtRegisterUser').prop('disabled', true);
                    $('#txtRegisterEmployed').prop('disabled', true);
                    _onLoadPage('User','List')
//                    $("#formRegisterUser").reset();
                }
//                    break;
//                    clearInputsRegiserUser();
//           
//            }

            });
        }

    }
//  