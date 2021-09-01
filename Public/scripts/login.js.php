<?php require_once '../../Components/Config.inc.php'; ?>
//<script>

    function _validateLogin() {
        if ($("#userName").val().length < 4) {
            $("#messageValidation").html("");
            $("#userPassword").prop("disabled", true);

        }
        else if ($("#userName").val().length > 3) {
            $.ajax({
                type: 'POST',
                url: '<?php echo Class_config::get('urlApp') ?>/?controller=User&action=ValidateUser',
                data: {userName: $("#userName").val()},
                beforeSend: function() {
                    beforeLoadImage('messageValidation');
                },
                success: function(msj) {
                    setTimeout(function() {
                        if (msj == "0") {
                            $("#userPassword").prop("disabled", true);
                            $('#messageValidation').css('color', '#ff0e0e');
                            $("#messageValidation").html("<?php echo Class_message::get('msgUserNotValidate') ?>");
                        }
                        else {
                            $("#userPassword").prop("disabled", false);
                            $("#userPassword").focus();
                            $('#messageValidation').css('color', '#0ab53a');
                            $("#messageValidation").html("<?php echo Class_message::get('TxtEnterYourPassword') ?>");


                        }
                    }, 2);

                }
            });
        }


    }
    function _validateLoginPassword() {
        if ($("#userPassword").val().length < 4) {
            $("#messageValidation").html("");
            $("#btnLogin").prop("disabled", true);

        }
        else if ($("#userPassword").val().length > 3) {
            $.ajax({
                type: 'POST',
                url: '<?php echo Class_config::get('urlApp') ?>/?controller=User&action=ValidatePassword',
                data: {userName: $("#userName").val(), userPassword: $("#userPassword").val()},
                beforeSend: function() {
                    beforeLoadImage('messageValidation');
                },
                success: function(msj) {
                    setTimeout(function() {
                        if (msj == "0") {
                            $("#btnLogin").prop("disabled", true);
                            $('#messageValidation').css('color', '#ff0e0e');
                            $("#messageValidation").html("<?php echo Class_message::get('TxtPasswordIsNotValidate') ?>");
                            $("#password").focus();
                        }
                        else {
                            $("#btnLogin").prop("disabled", false);
                            $('#messageValidation').css('color', '#0ab53a');
                            $("#messageValidation").html("<?php echo Class_message::get('TxtWelcomeUser') ?>");
                        }
                    }, 1);



                }
            });
        }


    }

    
   function beforeLoadImage(nameid) {
        $('#' + nameid).html('<img src="<?php echo Class_config::get('urlApp') ?>/Public/images/preload.gif" />procesando...');

    }