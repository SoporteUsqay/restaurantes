<?php
    $caja = "01";
    
    if(isset($_COOKIE["c"])){
        $caja = $_COOKIE["c"];
    }

    setcookie("pcimpresion","LUIGI_ES_GAY", time() + 3600000, '/');
    if(isset($_GET["t"])){
        setcookie("t",$_GET["t"], time() + 3600000, '/');
    }
    
    if(isset($_GET["app"])){
        if($_GET["app"] == "1"){
            setcookie("APP",'IEZ', time() + 3600000, '/');
        }else{
            setcookie("APP",'NOU', time() - 3600000, '/');
        }  
    }
    
    if(isset($_GET["c"])){
        if($_GET["c"] !== ""){
            $caja = $_GET["c"];
        }
    }
    setcookie("c",$caja, time() + 3600000, '/');
?>
<!DOCTYPE html>
<html class="no-js">
    <head>
        <meta charset="UTF-8">
        <title>Usqay</title>
        <meta name="mobile-web-app-capable" content="yes"/>
        <link rel="icon" href="logo.ico"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <style>
            html{
                height: 100%;
            }
            
            body{
                overflow: hidden !important;
                margin: 0px;
                height: 100%;
                background-image: url('Public/next/bgnigga.png');
                background-size: cover;
                background-position: center;
            }
            
            #loginform{
                background-image: url('Public/next/pinpadbg.png');
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

        <div id="loginform">
            <div id="p1">
                <form action="#" method="post">
                <center><img src="Public/next/logo.png" style="width: 70%;margin-top: 50px;"></center>
                    <p></p>
                    <div class="form">
                        <center>
                        <input style="margin-top: 20px;" type="password" name="userPassword" id="userPassword" required="true" onclick="resetpass()"/>
                        <button id="userSubmit" type="submit" id=""></button>
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
                    <div class="mensajes">
                        Un producto de C3L Soluciones Tecnológicas
                        <br/>
                        www.corporacionc3l.com
                    </div>
                    </form>
            </div>
            <div id="p2"></div>
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
                        <td style="width: 33.333%;"><input class="btnpin" onclick="clave('#');" type="button" value="#" name="#" ></td>
                    </tr>
                </table>   
            </div>
            
        </div>
        <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery-1.10.2.js"></script>
        <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery-ui-1.10.4.custom.js"></script>

        <!-- Bootstrap core JavaScript
            ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script>

       	alert("Su servicio se encuentra suspendido por un recibo pendiente de S/ 870 vencido el día 5/10/18");

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