
<?php

/**
 * Validacion de usuario en caso no este creado para que solicite registrarlo
 * @autor: Jeison Cruz
 * @año: 2021
 * 1. Se debe preguntar si existe un ruc registrado en la table cloud_config 
 */




    $caja = "01";
    $terminal = "01";
    if($ruc->ob_get_length1!=""){
        die("si hay credencial");
    }else{
        die("no hay credencial");
    }
    //Verficaciones para caja
    //Verificamos si ya existe la cookie
    if(isset($_COOKIE["c"])){
        $caja = $_COOKIE["c"];
    }

    //Verficamos valor por URL
    if(isset($_GET["c"])){
        if($_GET["c"] !== ""){
            $caja = $_GET["c"];
        }
    }   

    //Verficaciones para terminal
    //Verificamos si ya existe la cookie
    if(isset($_COOKIE["t"])){
        $terminal = $_COOKIE["t"];
    }

    //Verficamos valor por URL
    if(isset($_GET["t"])){
        if($_GET["t"] !== ""){
            $terminal = $_GET["t"];
        }
    }
    
    //Verificaciones para APP
    if(isset($_GET["app"])){
        if(intval($_GET["app"]) == 1){
            setcookie("APP",'IEZ', time() + 3600000, '/');
        }else{
            setcookie("APP",'NOU', time() - 3600000, '/');
        }  
    }

    //Verficaciones para Segunda Verificacion
    if(isset($_GET["sv"])){
        if(intval($_GET["sv"]) == 1){
            setcookie("SV",'IEZ', time() + 3600000, '/');
        }else{
            setcookie("SV",'NOU', time() - 3600000, '/');
        }  
    }
    
    setcookie("c",$caja, time() + 3600000, '/');
    setcookie("t",$terminal, time() + 3600000, '/');
    //Cookie que se mantiene porciacasini
    setcookie("pcimpresion","USQAY RULZ", time() + 3600000, '/');

    $test_conection = new Application_Models_TestConexionModel();

    // $test_conection->test_create();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usqay</title>

    <link rel="manifest" href="manifest.json">
    <link rel="shortcut icon" type="image/x-icon" href="logo.ico">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="apple-touch-icon" href="Public/images/apple-icon.png">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="Usqay Cloud">


    <link rel="stylesheet" href="Application/Views/User/login.css">
    <link rel="stylesheet" href="Public/css/bootstrap5.min.css">
</head>
<body>
    
    <!-- <div class="container-img">
        <img src="Public/images/usqay-large.svg">
        <img src="Public/images/usqay-circle-icon.svg">
    </div> -->

    <div class="container-padre">
    
        <div class="container-login">


            <div class="row">

                <div class="col-sm-12 col-lg-6 col-form">

                    <div class="text-center">
                        <!-- <img class="img-logo" src="Public/images/usqay-circle-icon.svg"> -->
                        <img class="img-logo" src="Public/images/usqay-large.svg">
                    </div>

                    <form id="frmLogin" action="<?php echo Class_config::get('urlApp') ?>/?controller=User&action=Login" method="post">

                        <input type="password" id="userPassword" name="userPassword" class="form-control" onclick="resetpass()" required autofocus>

                        <div class="text-center">

                            <button class="btn btn-block btn-submit">
                                ENTRAR
                            </button>
                        </div>

                        <div class="mensajes" style="color:red !important;">
                            <?php
                            if (isset($_GET['message']) && $_GET['message'] == "false") {
                                echo '¡Clave Erronea!';
                            }
                            ?>
                        </div>

                        <div class="mensajes men1">
                            Central telefónica: <b>(01) 642 9247</b>
                            <br>
                            www.sistemausqay.com
                            <br>
                            V 7.1.0 - 2021
                        </div>
                  

                    </form>

                </div>

                <div class="col-sm-12 col-lg-6 col-pad">

                    <table id="pinpad">
                        <tr style="width: 100%; height: 25%;">
                            <td style="width: 33.333%;"><input class="btnpin" onclick="clave('1');" type="button" value="1" name="1" ></td>
                            <td style="width: 33.333%;"><input class="btnpin" onclick="clave('2');" type="button" value="2" name="2" ></td>
                            <td style="width: 33.333%;"><input class="btnpin" onclick="clave('3');" type="button" value="3" name="3" ></td>
                        </tr>
                        <tr style="width: 100%; height: 25%;">
                            <td style="width: 33.333%;"><input class="btnpin" onclick="clave('4');" type="button" value="4" name="4" ></td>
                            <td style="width: 33.333%;"><input class="btnpin" onclick="clave('5');" type="button" value="5" name="5" ></td>
                            <td style="width: 33.333%;"><input class="btnpin"onclick="clave('6');" type="button" value="6" name="6" ></td>
                        </tr>
                        <tr style="width: 100%; height: 25%;">
                            <td style="width: 33.333%;"><input class="btnpin" onclick="clave('7');" type="button" value="7" name="7" ></td>
                            <td style="width: 33.333%;"><input class="btnpin" onclick="clave('8');" type="button" value="8" name="8" ></td>
                            <td style="width: 33.333%;"><input class="btnpin" onclick="clave('9');" type="button" value="9" name="9" ></td>
                        </tr>
                        <tr style="width: 100%; height: 25%;">
                            <td style="width: 33.333%;"><input class="btnpin" onclick="clave('.');" type="button" value="." name="." ></td>
                            <td style="width: 33.333%;"><input class="btnpin" onclick="clave('0');" type="button" value="0" name="0" ></td>
                            <td style="width: 33.333%;"><input class="btnpin" onclick="clave('#');" type="button" value="#" name="#" ></td>
                        </tr>
                    </table> 

                    <div class="mensajes men2">
                        Central telefónica: <b>(01) 642 9247</b>
                        <br>
                        www.sistemausqay.com<br>
                        V 7.1.0 - 2021                          
                    </div>
                </div>

            </div>

        </div>
    </div>

    <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery-1.10.2.js"></script>
    <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery-ui-1.10.4.custom.js"></script>

    <!-- Bootstrap core JavaScript
            ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script>
        //Evitamos anticlick para pantallas tactiles
        document.oncontextmenu = function(){return false;};    
            
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
                success: function (data) {
                    $('#userName option').remove();
                    for (var i = 0; i < data.length; i++) {
                        $('#userName').append("<option>" + data[i].user + "</option>");
                    }

                }
            });
        }
        function _logeo() {

            $.ajax({
                type: 'POST',
                url: '<?php echo Class_config::get('urlApp') ?>/?controller=User&action=Login',
                data: {userName: $("#userName").val(), userPassword: $("#userPassword").val()},
                success: function () {
                    console.log("lentro");
                }
            });

        }
        
        function cerrar() {
            open(location, '_self').close();
        }
        
        function resetpass() {
            $('#userPassword').val("");
        }
        </script>

</body>
</html>