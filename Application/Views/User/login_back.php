<?php
    $caja = "01";
    $terminal = "01";
    
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
<html class="no-js">
    <head>
        <meta charset="UTF-8">
        <title>Usqay</title>
        <link rel="shortcut icon" type="image/x-icon" href="logo.ico">
        <meta name="mobile-web-app-capable" content="yes"/>
        <meta name="apple-mobile-web-app-capable" content="yes">
        <link rel="apple-touch-icon" href="Public/images/apple-icon.png">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="apple-mobile-web-app-title" content="Usqay Cloud">
        <link rel="manifest" href="manifest.json">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <link href="Public/Bootstrap/css/bootstrap.css" rel="stylesheet">
        <style>
            html{
                height: 100%;
            }
            
            body{
                overflow: hidden !important;
                margin: 0px;
                height: 100%;
                background-image: url('Public/images/usqay-restaurante-min.png'),linear-gradient(90deg,#4286f4,#0f4b81);
                background-repeat: no-repeat;
                background-position: bottom;
                background-size: 100%;
            }
            
            #loginform{
                background: rgb(0, 0, 0, 0.1);
                border-radius: 20px;
                width: 900px;
                height: 480px;
                background-size: 100% 100%;              
                margin: auto;
                position: absolute;
                top: 0; left: 0; bottom: 0; right: 0;
            }
            
            #p1{
                width: 48%;
                height: 100%;
                float: left;
            }
            #p2{
                width: 2%;
                height: 60%;
                margin-top: 10%;
                border-right: #e86927 solid 2px;
                float: left;
            }
            #p3{
                width: 48%;
                height: 100%;
                float: left;
            }
            
            #userPassword{
                border: none;
                background-color: transparent;
                width: 50%;
                height: 40px;
                background-image: url('Public/next/numeric.png');
                color: #e86927;
                background-size: 100% 100%;
                padding: 5px;
                text-align: center;
                font-size: 22px;
            }
            
            #userSubmit{
                border: none;
                background-color: transparent;
                width: 52%;
                height: 47px;
                background-image: url('Public/next/submit.png');
                background-size: 100% 100%; 
                margin-top: 20px;
                padding: 10px;
            }
            
            .mensajes{
                text-align: center;
                color: #00395e;
                font-weight: bold;
                margin-top: 30px;
            }
            
            #pinpad{
                background-image: url('Public/next/pinpad.png');
                width: 55%;
                height: 60%;
                background-size: 100% 100%;              
                margin: auto;
                margin-top: 90px;
            }
            
            .btnpin{
                width: 100%;
                height: 100%;
                opacity: 0;
            }
            
            @media (max-width: 950px) {
                #p1{
                    width: 100%;
                    height: 100%;
                    float: none;
                }
                #p2{
                    display: none;
                }
                #p3{
                    display: none;
                }
                
                #loginform{
                    width: 100%;
                    height: 100%;
                    background-color: rgba(153, 164, 193, 0.6);
                    background-image:none;
                }
                
                #userPassword{
                    width: 55%;
                }
            
                #userSubmit{
                    width: 55%;
                }
            }
            @media (min-width: 650px) and (max-width: 950px) {
                #userPassword{
                    width: 55%;
                    height: 70px;
                }
            
                #userSubmit{
                    width: 50%;
                    height: 65px;
                }
            }
            </style>

    </head>
    <body onload="$('.form-control').focus();">

        <div class="row">
            
            <div class="col-xd-12 col-md-6">

                <div id="loginform">
                    <div id="p1">
                        <form action="<?php echo Class_config::get('urlApp') ?>/?controller=User&action=Login" method="post">
                        <center><img src="Public/images/usqay-circle-icon.svg" style="width: 30%;margin-top: -4rem;"></center>
                            <p></p>
                            <div class="form">
                                <center>
                                <input style="margin-top: 20px;" type="password" name="userPassword" id="userPassword" required="true" onclick="resetpass()" autofocus />
                                <!-- <button id="userSubmit" type="submit" id=""></button> -->
                                </center>
                            </div>
                            <p></p>
                            <div class="mensajes" style="color:red !important;">
                                <?php
                                if (isset($_GET['message']) && $_GET['message'] == "false") {
                                    echo '¡Clave Erronea!';
                                }
                                ?>
                            </div>
                            <!-- <div class="mensajes">
                                Ventas: 949641210
                                <br/>
                                www.sistemausqay.com
                            </div> -->
                            </form>
                    </div>
                    <!-- <div id="p2"></div> -->
                    <!-- <div id="p3">
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
                    </div> -->
                    
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