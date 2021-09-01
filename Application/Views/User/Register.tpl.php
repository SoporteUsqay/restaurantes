<?php require 'Components/Config.inc.php'; ?>
<div id="dialoRegister">

    <form  id="formRegisterUser" role="form">
        <fieldset>
            <legend>Registrar Usuario</legend>
            <div class="form-group">
                <label class="col-lg-2 control-label" name="lblRegisterEmployed" for="txtRegisterEmployed" ><?php echo Class_message::get('TxtEmployedName') ?></label>
                <input type="text" class="form-control" name="txtRegisterEmployed" onkeyup="_validationInputNames()" required="true" id="txtRegisterEmployed" title="<?php echo Class_message::get('TxtWritenForSearchEmployed') ?>" placeholder="<?php echo Class_message::get('TxtWritenForSearchEmployed') ?>" >
                <input type="text" name="txtRegisterEmployed-id" id="txtRegisterEmployed-id" hidden="true" style="display:none">

            </div>
            <div class="form-group">
                <label name="lblRegisterUser" ><?php echo Class_message::get('TxtUserName') ?></label>
                <input type="text"class="form-control" disabled="true" onchange="_validationInputUser()" onkeyup="_validationInputUser()" name="txtRegisterUser" required="true" id="txtRegisterUser" title="<?php echo Class_message::get('TxtEnterYourUser') ?>" placeholder="<?php echo Class_message::get('TxtEnterYourUser') ?>">

            </div>
            <!--</th>-->
            <!--</tr>-->
            <!--<tr>-->
                <!--<th style="width: 100px">-->
            <div class="form-group">
                <label name="lblRegisterPassword" ><?php echo Class_message::get('TxtPassword') ?></label>
                <input class="form-control" type="password" disabled="true" name="txtRegisterPassword" required="true" id="txtRegisterPassword" title="<?php echo Class_message::get('TxtEnterYourPassword') ?>" placeholder="<?php echo Class_message::get('TxtEnterYourPassword') ?>">
                <span id="resultRegisterPassword"></span>

            </div>
            <div class="form-group">
                <label name="lblRegisterConfirmPassword" ><?php echo Class_message::get('msgTextConfirmPassword') ?></label>
                <input  class="form-control" type="password" disabled="true" name="txtRegisterConfirmPassword" required="true" id="txtRegisterConfirmPassword" title="<?php echo Class_message::get('msgTextConfirmPassword') ?>" placeholder="<?php echo Class_message::get('msgTextConfirmPassword') ?>">
                <b><span class="blue" id="MsgRegisterConfirmPassword"></span></b>
            </div>
            <div class="form-group">
                <label name="lblTypeUser" ><?php echo Class_message::get('TxtTyeUser') ?></label>
                <select class="form-control" required="true" disabled="true" id="cmbRegisterTypeUser" onchange="validateCmbRegisterOption()">
                    <option value="0"><?php echo Class_message::get('TxtSelectionOneOption') ?></option>
                </select>
            </div>
            <div class="form-group">
                <input class="btn btn-default" type="button" onclick="_registerUser()" disabled="true"  id="BtnRegisterUser" value="<?php echo Class_message::get('BtnSave') ?>">
               
                <input class="btn btn-default" type="reset" onclick="clearInputsRegiserUser()" id="BtnRegisterClear" value="<?php echo Class_message::get('BtnClear') ?>" >
                <input class="btn btn-default" type="reset" onclick="_onLoadPage('User', 'List')" id="" value="Retonar" >

            </div>


            <b><label class="red" id="msgRegisterUser"></label></b>
    </form>
</fieldset>
</div>
<script>
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
        else if (result == '<?php echo Class_message::get('msgTextPasswordWeak') ?>') {             /*Deshabilitar el combo cmbRegisterTypeUser*/
            $('#txtRegisterConfirmPassword').prop('disabled', true);
        }
        else if (result == '<?php echo Class_message::get('msgTextPasswordGood') ?>') {             /*habilitar el combo cmbRegisterTypeUser*/
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
            $('#MsgRegisterConfirmPassword').html("<?php echo Class_message::get('msgPasswordEquals')?>");
            $('#cmbRegisterTypeUser').prop('disabled', false);
        }
        else {
            $('#MsgRegisterConfirmPassword').html("<?php echo Class_message::get('msgPasswordNotEquals')?>");
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

    $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=TypeUser&&action=List', function(data) {
        for (var i = 0; i < data.length; i++) {

            $('#cmbRegisterTypeUser').append("<option value=\"" + data[i].pkTypeUser + "\">" + data[i].description + "</option>")
            $('#txtModifyUserType').append("<option value=\"" + data[i].pkTypeUser + "\">" + data[i].description + "</option>")
            $('#cmbTypeUserModifyPermissions').append("<option value=\"" + data[i].pkTypeUser + "\">" + data[i].description + "</option>")
        }

    });
</script>


