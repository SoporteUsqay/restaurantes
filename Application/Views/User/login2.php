<!DOCTYPE html>
<html class="no-js">

<head>
	
	<meta charset="UTF-8">
    <title><?php echo Class_config::get('nameApplication') ?></title>
    <link rel="stylesheet" href="./././Public/css/style2.css">
    <script type="text/javascript" src="./././Public/scripts/login.js.php"></script>
</head>
<body>
    
	<div class="wrapper">	
        <form action="?controller=User&action=Login" method="post">
	<div class="container">
		<center><img src="Public/images/logo-usqay.png"></center>
                <p></p>
		<form class="form">
			<input  class="form-control" name="userName"  id="userName"
                    title="<?php echo Class_message::get('TxtEnterYourUser') ?>" 
                    placeholder="<?php echo Class_message::get('TxtEnterYourUser') ?>" name="userName" />
			<input style="margin-top: 20px;" type="password" class="form-control" name="userPassword" id="userPassword" required="true" title="<?php echo Class_message::get('TxtEnterYourPassword') ?>" onblur="" placeholder="<?php echo Class_message::get('TxtEnterYourPassword') ?>" >
			<button type="submit" id="">Ingresar</button>
		</form>
		<?php
            if (isset($_GET['message']) && $_GET['message'] == "false") {
                 echo'Usuario y/o Password equivocado';
        }
        ?>
	</div>
	<div class="container2">
	<form class="cal">	
		<div style="float:left">
			<input  onclick="clave('1');" type="button" value="1" name="1" >
			<input  onclick="clave('4');" type="button" value="4" name="4" >
			<input  onclick="clave('7');" type="button" value="7" name="7" >
			<input  onclick="clave('*');" type="button" value="*" name="*" >
		</div>
		<div style="float:left; padding-left:10px">
			<input  onclick="clave('2');" type="button" value="2" name="2" >
			<input  onclick="clave('5');" type="button" value="5" name="5" >
			<input  onclick="clave('8');" type="button" value="8" name="8" >
			<input  onclick="clave('0');" type="button" value="0" name="0" >
		</div>
		<div style="float:left; padding-left:10px">
			<input  onclick="clave('3');" type="button" value="3" name="3" >
			<input  onclick="clave('6');" type="button" value="6" name="6" >
			<input  onclick="clave('9');" type="button" value="9" name="9" >
			<input  onclick="clave('#');" type="button" value="#" name="#" >
		</div>
	</form>

	</div>
</form>
</div>

    <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery-1.10.2.js"></script>

    <script src="js/index.js"></script>
    <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script>
            function clave($val) {
                var $text = $('#userPassword').val();
                $('#userPassword').val($text + $val);


            }

            function loadUsers() {

                var $param = $('#typeuser').val();
                $.ajax({
                    type: 'POST',
                    url: '<?php echo Class_config::get('urlApp') ?>/?controller=User&action=ListUserType',
                    data: {tipo: $param},
                    dataType: 'json',
                    success: function(data) {
                        $('#userName option').remove();
                        for (var i = 0; i < data.length; i++) {

                            $('#userName').append("<option>" + data[i].user + "</option>")
                        }

                    }
                });
            }
            function _logeo() {

                $.ajax({
                    type: 'POST',
                    url: '<?php echo Class_config::get('urlApp') ?>/?controller=User&action=Login',
                    data: {userName: $("#userName").val(), userPassword: $("#userPassword").val()},
                    success: function() {
                        console.log("entro");
                    }
                });

            }
        </script>

</body>

</html>