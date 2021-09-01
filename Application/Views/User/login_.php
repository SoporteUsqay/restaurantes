
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
        <link rel="stylesheet" href="ReportesGraficos/Highcharts/api/css/font-awesome.css">
        
        <style>
            html {
                height: 100%;
            }
            
            body {
                overflow: hidden !important;
                margin: 0px;
                height: 100%;
                background-image: url('Public/images/usqay-restaurante-min.png'),linear-gradient(90deg,#4286f4,#0f4b81);
                background-repeat: no-repeat;
                background-position: bottom;
                background-size: 100%;
                padding: 1em;
            }

            #loginform {
                background-color: rgb(0, 0, 0, 0.2);
                padding: 1em;
                /* margin-right: 40px; */
                height: 80vh;
                border-radius: 20px;
            }

            #userPassword{
                border: none;
                background-color: transparent;
                width: 70%;
                height: 50px;
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
                width: 70%;
                height: 47px;
                background-image: url('Public/next/submit.png');
                background-size: 100% 100%; 
                margin-top: 20px;
                padding: 10px;
            }

            .mensajes {
                text-align: center;
                color: #fff;
                font-weight: bold;
                margin-top: 30px;
            }

            #pinpad {
                /* background-image: url('Public/next/pinpad.png'); */
                width: 55%;
                height: 60%;
                background-size: 100% 100%;              
                margin: auto;
                margin-top: 30px;
                margin-bottom: 30px;
            }
            #pinpad td {
                /* padding: 5px; */
                text-align: center;
                vertical-align: middle;
            }
            
            .btnpin {
                width: 90%;
                height: 60px;
                /* opacity: 0; */
                border-radius: 50%;
                margin: auto;
                border: 0;
                margin-bottom: 10px;
                font-size: 20px;
                color: #02027c;
                font-weight: bold;
                user-select: none;
                box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
            }

            .btnpin:focus {
                border: none;
                outline: none;
            }

            #icoLo {
                font-size: 15px;
            }

            #cabe {
                margin-bottom: 40px;
            }

            @media (min-width: 1200px) {
                .btnpin {
                    height: 75px;
                    width: 80%;
                }

                #loginform {
                    margin-right: 40px;
                }

                #icoLo {
                    font-size: 30px;
                }

                #cabe {
                    margin-bottom: 0;
                }
            }

            @media (min-width: 992px) and (max-width: 1200px) {
                .btnpin {
                    height: 70px;
                }

                #icoLo {
                    font-size: 20px;
                }

                #cabe {
                    margin-bottom: 0;
                }

                #loginform {
                    margin-right: 40px;
                }
            }

            @media (min-width: 768px) and (max-width: 992px) {
                .btnpin {
                    height: 60px;
                }

                #icoLo {
                    font-size: 20px;
                }

                #cabe {
                    margin-bottom: 0;
                }
            }

            @media (min-width: 680px) and (max-width: 768px) {
                .btnpin {
                    height: 70px;
                    width: 70%;
                }

                #icoLo {
                    font-size: 20px;
                }
            }

            @media (min-width: 512px) and (max-width: 680px) {
                .btnpin {
                    height: 80px;
                }

                #icoLo {
                    font-size: 20px;
                }
            }

        
        </style>

    </head>
    <body>

        <div class="text-right" id="cabe">
            
            <img src="Public/images/usqay-large-inverse.svg" width="25%">
        
        </div>
    
        <div class="container">
            
            <div class="row">
                
                <div class="col-xs-12 col-sm-7 col-md-6">

                    <div id="loginform">
                        <form action="<?php echo Class_config::get('urlApp') ?>/?controller=User&action=Login" method="post">
                        <div id="p1">
                            <center><img src="Public/images/usqay-circle-icon.svg" style="width: 30%;margin-top: -7rem;"></center>
                                <p></p>
                                <div class="form">
                                    <center>
                                    <input style="margin-top: 20px;" type="password" name="userPassword" id="userPassword" required="true" onclick="resetpass()" autofocus />
                                    <!-- <button id="userSubmit" type="submit" id=""></button> -->
                                    </center>
                                </div>
                                <p></p>
                                <div class="mensajes" style="color:#fff !important;">
                                    <?php
                                    if (isset($_GET['message']) && $_GET['message'] == "false") {
                                        echo '¡Clave Erronea!';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div id="p3">
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
                                    <td style="width: 33.333%;">
                                        <!-- <input class="btnpin" onclick="clave('#');" type="button" value="#" name="#" > -->
                                        <button class="btnpin" type="submit" id="" style="background: #ef6a00">
                                            <!-- <img src="Public/images/1828391.svg" width="40px" alt=""> -->
                                            <i id="icoLo" class="fa fa-2x fa-sign-in" style="color: #fff"></i>
                                        </button>
                                    </td>
                                </tr>
                            </table>   
                        </div>
                        </form>
                        
                        
                        <!-- <div class="mensajes">
                                    Ventas: 949641210
                                    <br/>
                                    www.sistemausqay.com
                                </div> -->
                        
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