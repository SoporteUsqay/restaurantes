<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Expires" CONTENT="0">
        <meta http-equiv="Cache-Control" CONTENT="no-cache">
        <meta http-equiv="Pragma" CONTENT="no-cache">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title><?php echo Class_config::get('nameApplication') ?></title>

        <link rel="stylesheet" href="Public/jquery-ui-1.10.4.custom/css/jquery-ui.min.css">
        <link href="Public/Bootstrap/css/bootstrap.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="Public/css/style.css">
        <link rel="stylesheet" href="Public/Usqay/css/usqay.css">
        <link rel="icon" href="logo.ico"/>

        <script src="Public/js/Chart.js" type="text/javascript"></script>
        <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery-1.10.2.js"></script>
        <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery-ui-1.10.4.custom.js"></script>
        <script type="text/javascript"src="Public/jquery-ui-1.10.4.custom/js/jquery.numeric.js"></script>
        <script type="text/javascript"src="Public/jquery-ui-1.10.4.custom/js/jquery.maskedinput.js"></script>
        <script type="text/javascript"src="Public/scripts/body.js.php"></script>
        <script type="text/javascript"src="Public/scripts/listGeneral.js.php"></script>
        <script type="text/javascript"src="Public/scripts/Validation.js.php"></script>
        <script  type="text/javascript" src="Public/Bootstrap/js/bootstrap.min.js"></script>
    </head>
    <style>
        button,
        button:focus{
            outline: none !important;
        }
        .container2 {
            max-width: 600px;
            margin: 0 auto;
            padding-top: 10px;
            padding-left: 125px;
            height: 400px;
            text-align: center;
            float: left;
        }
        .container h1 {
            font-size: 40px;
            -webkit-transition-duration: 1s;
            transition-duration: 1s;
            -webkit-transition-timing-function: ease-in-put;
            transition-timing-function: ease-in-put;
            font-weight: 200;
            padding-bottom: 15px;
        }
        .cal button {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            outline: 0;
            background-color: white;
            border: 0;
            padding: 10px 15px;
            color: #53e3a6;
            border-radius: 3px;
            width: 73px;
            cursor: pointer;
            font-size: 38px;
            -webkit-transition-duration: 0.25s;
            transition-duration: 0.25s;
        }
        .cal button:hover {
            background-color: #f5f7f9;
        }
        .calEnter button {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            outline: 0;
            background-color: white;
            border: 0;
            padding: 10px 15px;
            color: #53e3a6;
            border-radius: 3px;
            width: 240px;
            cursor: pointer;
            font-size: 38px;
            -webkit-transition-duration: 0.25s;
            transition-duration: 0.25s;
        }
        .calEnter button:hover {
            background-color: #f5f7f9;
        }
        .btn-usqay{
            background-color: #ef4d4d !important;
            color: #FFF !important;
            border-radius: 13px;
        }
        .btn-usqay-off{
            background-color: #009839 !important;
            color: #FFF !important;
            border-radius: 13px;
        }
        .collapse-usqay{
            background-color: #efeeee !important;
            border-radius: 13px;
        }
        .usqay-btn{
            color: #FFF;
            background-color: #00395a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            border-radius: 50%;
            width: 195px;
            margin: 5px;
            height: 195px;
            display:inline-block;
            vertical-align:top;
        }
        .usqay-btn-red{
            background-color: #ef4d4d !important;
        }
        .usqay-btn:hover{
            color: #ef4d4d !important;
        }
        .usqay-btn-red:hover{
            color: #000 !important;
        }
        .usqay-color{
            color: #FFF;
            background-color: #00395a;
        }
        .usqay-color:hover{
            color: #ef4d4d !important;
        }
    </style>
    <body onload="nobackbutton();">
    <!-- modal para proceso -->
    <div class='modal fade' id='modal_envio_anim' tabindex='-1' role='dialog' data-keyboard="false" data-backdrop="static" style="z-index: 999999 !important;">
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h4 class='modal-title' id='myModalLabel'>Procesando</h4>
                </div>
                <div class='modal-body'>
                    <center>
                        <img src="Public/images/pacman.gif">
                    </center>
                </div>
            </div>
        </div>
    </div>

        <!-- Fixed navbar -->
        <?php
        $objViewMenu = new Application_Views_IndexView();
        $objViewMenu->showContent();
        ?>

        <br><br><br><br>

        <div class="container-fluid">
            <div class="row" style="width: 100% !important;margin:0px !important;">
                <div class="col-md-12 cnt-btn" style="width:100% !important;padding:10px;margin-top:-25px;height:100px !important;overflow-x: scroll;
    overflow-y: hidden;white-space:nowrap;">
                <center>
                <?php
                $db = new SuperDataBase();   
                $sucursal = UserLogin::get_pkSucursal();
                $query = "SELECT s.* FROM salon s, accion_caja ac where s.estado = 0 AND ac.pk_accion = s.pkSalon AND ac.tipo_accion = 'SAL' AND ac.caja = '".$_COOKIE["c"]."' ORDER BY s.pkSalon ASC";
                $resultSalones = $db->executeQuery($query);
                $toff = "";
                while ($rowSalones = $db->fecth_array($resultSalones)) {
                    echo '<a class="' . $rowSalones[0] . ' btn btn-lg btn-usqay'.$toff.' btn-usqay-group" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse' . $rowSalones[0] . '" aria-expanded="false" aria-controls="collapse' . $rowSalones[0] . '" style="margin:5px;font-size:32px;padding:15px;" id="btn-salon-' . $rowSalones[0] . '" onclick="cambia_sel('.$rowSalones[0].')">' . $rowSalones['nombre'] . '</a>';
                    $toff = "-off";
                }
                ?>
                </center>
                </div>
                <div class="col-md-12" style="width:100% !important;">
                    <div id="divAperturarMesas">
                        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="false" style="text-align: center;">
                        <?php
                            $salon_inicial = "";                                                 
                            $query = "SELECT s.* FROM salon s, accion_caja ac where s.estado = 0 AND ac.pk_accion = s.pkSalon AND ac.tipo_accion = 'SAL' AND ac.caja = '".$_COOKIE["c"]."' ORDER BY s.pkSalon ASC";
                            $contador = 0;
                            $resultSalones_1 = $db->executeQuery($query);
                            while ($rowSalones = $db->fecth_array($resultSalones_1)) {
                                $in = "";
                                if ($contador < 1) {
                                    $in = " in ";
                                    $salon_inicial = $rowSalones[0];
                                }
                                echo'<div class="' . $rowSalones[0] . '">
                                <div id="collapse' . $rowSalones[0] . '" class="collapse-usqay panel-collapse collapse ' . $in . '" role="tabpanel" aria-labelledby="heading' . $rowSalones[0] . '">
                                    <div class="panel-body">';
                                
                                //Logica para Salones de Delivery y Llevar
                                if(intval($rowSalones["pkSalon"]) == 43 || intval($rowSalones["pkSalon"]) == 44){
                                
                                echo'<button id="btnMesaTMP" hfre="#" onclick="msfConfirmar(\'TMP\', \'TMP\',\'' . $rowSalones[0] . '\')" class="usqay-btn btn btn-lg">Agregar<br/><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span></button>';
                                    
                                    
                                $query = "SELECT * FROM mesas where pkSalon=" . $rowSalones['pkSalon'] . " and estado<>3 order by pkMesa ASC";
                                $resultMesas = $db->executeQuery($query);
                                while ($rowMesas = $db->fecth_array($resultMesas)) {
                                    $queryPedido = "SELECT pkPediido,idUser FROM pedido where estado = 0 AND pkMesa = '".$rowMesas["pkMesa"]."' order by pkPediido DESC LIMIT 1";
                                    $resultPedido = $db->executeQuery($queryPedido);
                                    while ($rowPedido = $db->fecth_array($resultPedido)) {
                                        $pedido = $rowPedido['pkPediido'];
                                        $id_usuario = $rowPedido["idUser"];
                                        $queryTrabajador = "SELECT nombres,pkTrabajador from trabajador where pkTrabajador = '".$id_usuario."'";
                                        $nombres = "";
                                        $resultTrabajador = $db->executeQuery($queryTrabajador);
                                        while ($rowTrabajador = $db->fecth_array($resultTrabajador)) {
                                            $nombres = $rowTrabajador['nombres'];
                                        }
                                        
                                        //Obtenemos cliente
                                        $nombre_cliente = "";
                                        $query01 = "Select ce.* from delivery_cliente dc, cliente_externo ce where dc.pkPediido = '".$pedido."' AND dc.id_cliente_externo = ce.id";
                                        $query02 = "Select * from pedido_cliente where pkPediido = '".$pedido."'";
                                        $r1 = $db->executeQuery($query01);
                                        $r2 = $db->executeQuery($query02);
                                        $res = null;
                                        if($row01 = $db->fecth_array($r1)){
                                            $nombre_cliente = '<br/><span class="glyphicon glyphicon-user" aria-hidden="true"></span> '.strtoupper($row01['nombres_y_apellidos']);
                                        }else{
                                           if($row02 = $db->fecth_array($r2)){
                                               if($row02['nombre_cliente'] <> ""){
                                                    $nombre_cliente = '<br/><span class="glyphicon glyphicon-user" aria-hidden="true"></span> '.strtoupper($row02['nombre_cliente']);
                                               }
                                            } 
                                        }
                                        
                                        echo'<button id="btnMesa' . $rowMesas['pkMesa'] . '" onclick="load_universal(\'' . $rowMesas['pkMesa'] . '\', \'' . $rowMesas['nmesa'] . '\',\'' . $rowSalones[0] . '\')" class="usqay-btn btn btn-lg usqay-btn-red">' . $rowMesas['nmesa'] . '<br/><span class="glyphicon glyphicon-briefcase" aria-hidden="true"></span> '.$nombres.$nombre_cliente.'</button>';
                                    }
                                }                                    
                                }else{
                                    //Logica para Otros Salones                                 
                                    $query = "SELECT * FROM mesas where pkSalon=" . $rowSalones['pkSalon'] . " and estado<>3 order by pkMesa ASC";
                                    $resultMesas = $db->executeQuery($query);
                                    
                                    while ($rowMesas = $db->fecth_array($resultMesas)) {
                                        $queryPedido = "SELECT pkPediido,idUser FROM pedido where estado = 0 AND pkMesa = '".$rowMesas["pkMesa"]."' order by pkPediido DESC LIMIT 1";
                                        $resultPedido = $db->executeQuery($queryPedido);
                                        $hay_pedido = 0;
                                        while ($rowPedido = $db->fecth_array($resultPedido)) {
                                            $hay_pedido = 1;
                                            $pedido = $rowPedido['pkPediido'];
                                            $id_usuario = $rowPedido["idUser"];
                                            $queryTrabajador = "SELECT nombres,pkTrabajador from trabajador where pkTrabajador = '".$id_usuario."'";
                                            $nombres = "";
                                            $resultTrabajador = $db->executeQuery($queryTrabajador);
                                            while ($rowTrabajador = $db->fecth_array($resultTrabajador)) {
                                                $nombres = $rowTrabajador['nombres'];
                                            }
                                            
                                            //Obtenemos cliente
                                            $nombre_cliente = "";
                                            $query01 = "Select ce.* from delivery_cliente dc, cliente_externo ce where dc.pkPediido = '".$pedido."' AND dc.id_cliente_externo = ce.id";
                                            $query02 = "Select * from pedido_cliente where pkPediido = '".$pedido."'";
                                            $r1 = $db->executeQuery($query01);
                                            $r2 = $db->executeQuery($query02);
                                            $res = null;
                                            if($row01 = $db->fecth_array($r1)){
                                                $nombre_cliente = '<br/><span class="glyphicon glyphicon-user" aria-hidden="true"></span> '.strtoupper($row01['nombres_y_apellidos']);
                                            }else{
                                               if($row02 = $db->fecth_array($r2)){
                                                   if($row02['nombre_cliente'] <> ""){
                                                        $nombre_cliente = '<br/><span class="glyphicon glyphicon-user" aria-hidden="true"></span> '.strtoupper($row02['nombre_cliente']);
                                                   }
                                                } 
                                            }
                                            
                                            echo'<button id="btnMesa' . $rowMesas['pkMesa'] . '" onclick="load_universal(\'' . $rowMesas['pkMesa'] . '\', \'' . $rowMesas['nmesa'] . '\',\'' . $rowSalones[0] . '\')" class="usqay-btn btn btn-lg usqay-btn-red">' . $rowMesas['nmesa'] . '<br/><span class="glyphicon glyphicon-briefcase" aria-hidden="true"></span> '.$nombres.$nombre_cliente.'</button>';
                                        }
                                        
                                        //Si no hay pedido perdido mostramos normalmente
                                        if($hay_pedido === 0){
                                            echo'<button id="btnMesa' . $rowMesas['pkMesa'] . '" hfre="#" onclick="load_universal(\'' . $rowMesas['pkMesa'] . '\', \'' . $rowMesas['nmesa'] . '\',\'' . $rowSalones[0] . '\')" class="usqay-btn btn btn-lg">' . $rowMesas['nmesa'] . '</button>';
                                        }
                                    }       
                                }
                                echo'</div></div></div>';
                                $contador++;
                            }
                            ?>
                            
                            <!-- aqui ponemos los popups de apertura-->
                            <div id="collapseap" class="collapse-usqay panel-collapse collapse" role="tabpanel" aria-labelledby="headingap">
                                <div class="panel-body">
                                    <form id="frmAperturaMesa" >
                                    <input id="txtMesaApertura" style="display: none">
                                    <h4>Nombre del Cliente</h4>
                                    <input name="clmesa" id="txtNombreCliente" class="form-control">
                                    <h4>Número de personas a ocupar <label id="lblNmesa"></label></h4>
                                    <input name="nmesa" id="txtCantidaPersonas" class="form-control" value="1">
                                    </form>
                                    <br>
                                    <form class="cal" style="">
                                        <center>
                                            <input class="btn-cal btn" onclick="Cantidad('1', 'txtCantidaPersonas');" type="button" value="1" name="1" >
                                            <input class="btn-cal btn" onclick="Cantidad('2', 'txtCantidaPersonas');" type="button" value="2" name="2" >
                                            <input class="btn-cal btn" onclick="Cantidad('3', 'txtCantidaPersonas');" type="button" value="3" name="3" >
                                            <br>
                                            <input class="btn-cal btn" onclick="Cantidad('4', 'txtCantidaPersonas');" type="button" value="4" name="4" >
                                            <input class="btn-cal btn" onclick="Cantidad('5', 'txtCantidaPersonas');" type="button" value="5" name="5" >
                                            <input class="btn-cal btn" onclick="Cantidad('6', 'txtCantidaPersonas');" type="button" value="6" name="6" >
                                            <br>
                                            <input class="btn-cal btn" onclick="Cantidad('7', 'txtCantidaPersonas');" type="button" value="7" name="7" >
                                            <input class="btn-cal btn" onclick="Cantidad('8', 'txtCantidaPersonas');" type="button" value="8" name="8" >
                                            <input class="btn-cal btn" onclick="Cantidad('9', 'txtCantidaPersonas');" type="button" value="9" name="9" >
                                            <br>
                                            <a href="#" class="btn btn-cal"> </a>
                                            <input class="btn-cal btn" onclick="Cantidad('0', 'txtCantidaPersonas');" type="button" value="0" name="0" >
                                            <a href="#" class="btn btn-cal"> </a>
                                            <br>
                                            <a  style="padding-top:10px;background-color:#ef4d4d !important;color:#FFF !important;" onclick="regresa_salon()" href="#" class="btn btn-cal"><span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span></a>
                                            <a  style="padding-top:15px;background-color:#ef4d4d !important;color:#FFF !important;" onclick="resetP()" href="#" class="btn btn-cal"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                                            <a style="padding-top:10px;background-color:#009839 !important;color:#FFF !important;" href="#" class="btn btn-cal" onclick="CargarMesa(1)"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></a>
                                        </center>
                                    </form>
                                    <p></p>
                                    <div class="row" style="height: 10px !important;"></div>
                                </div>
                            </div>
                            
                            <!-- aqui ponemos los popups de apertura-->
                            <div id="collapseapd" class="collapse-usqay panel-collapse collapse" role="tabpanel" aria-labelledby="headingap">
                                <div class="panel-body">
                                <form id="frmClientePLllevar" >
                                <table style="width: 100%;">
                                <tr>
                                <td>Teléfono</td>
                                <td><input name="telefonoCliente" class="form-control reset" id="txtPhoneCliente" placeholder="Ingrese el teléfono - Presione Enter para iniciar la búsqueda"></td>
                                </tr>
                                <tr>
                                <td>Cliente</td>
                                <td><input name="nombreCliente" class="form-control reset" id="txtNameCliente" placeholder="Ingrese el nombre del cliente"></td>
                                </tr>
                                <tr>
                                <td>DNI/RUC</td>
                                <td><input name="documentoCliente" class="form-control reset" id="txtDocCliente" placeholder="Ingrese el ID del cliente"></td>
                                </tr>
                                <tr>
                                <td>Dirección</td>
                                <td><input name="direccionCliente" class="form-control reset" id="txtDireccionCliente" placeholder="Ingrese la direccion del cliente"></td>
                                </tr>
                                </table>
                                </form>
                                    <p></p>
                                    <div class="row" style="height: 10px !important;"></div>
                                    <div>
                                        <center>
                                            <button type="button" class="btn btn-danger" onclick="regresa_salon()">Cancelar</button>
                                            <button type="button" class="btn btn-primary" onclick="CargarMesa(2)">Aperturar mesa</button>
                                        </center>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <script>
        var timeout_login;
        
        function resetP(){
            $("#txtCantidaPersonas").val("1");
            contador = 0;
        }
        
        function load_universal(pk_mesa,nombre_mesa,pk_salon){
            $.ajax({
                url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=checkOpenMesa&mesa="+pk_mesa,
                type: 'GET',
                dataType: "json",
                cache: false,
                success: function (data) {
                    if(parseInt(data.estado) === 1){
                        validaMozoApertura(data.moso,pk_mesa);
                    }else{
                        if(pk_salon === "44" || pk_salon === "43"){
                            alert("Este pedido ya fue cerrado");
                            window.location.reload();
                        }else{
                            msfConfirmar(pk_mesa,nombre_mesa,pk_salon);
                        }
                    }
                },
                error: function () {
                  alert("Hubo un error de comunicacion");
                }
            });
        }
            
        function msfConfirmar($mesa, $nombre, $pkSalon) {
            console.log("Timer reiniciado");
            clearTimeout(timeout_login);
            timeout_login = setTimeout(cierra_sesion,45000);
            if($mesa === "TMP" && $nombre === "TMP"){
            var param = {'pkSalon': $pkSalon};
            $.ajax({
                url: "<?php echo Class_config::get('urlApp') ?>/?controller=Config&&action=getMesa",
                type: 'POST',
                data: param,
                cache: true,
                dataType: 'json',
                success: function (data) {
                    $("#lblNmesa").html(data[0].nombre);
                    $("#txtMesaApertura").val(data[0].mesa);
                    cambia_sel("apd");
                }
            });
            }else{
                $("#lblNmesa").html($nombre);
                $("#txtMesaApertura").val($mesa);
                if ($pkSalon === "44" || $pkSalon === "43") {
                    cambia_sel("apd");
                }
                else{
                    cambia_sel("ap");
                }
            }
        }
                       
        $('#txtPhoneCliente').keypress(function (event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if (keycode == '13') {
                var param = {phone: $("#txtPhoneCliente").val()};
                $.ajax({
                    url: "<?php echo Class_config::get('urlApp') ?>/?controller=Cliente&&action=ClientPhone",
                    type: 'POST',
                    data: param,
                    dataType: "json",
                    success: function (data) {
                        $("#txtNameCliente").val(data.nombres);
                        $("#txtDireccionCliente").val(data.direccion);
                        $("#txtDocCliente").val(data.documento);
                    }
                });
            }
        });

        function validaMozoApertura($pkMozo, $pkMesa) {
            console.log("Timer reiniciado");
            clearTimeout(timeout_login);
            timeout_login = setTimeout(cierra_sesion,45000);
            if ('<?php echo UserLogin::get_pkTypeUsernames() ?>' === "4") {
                if ($pkMozo === "<?php echo UserLogin::get_idTrabajador() ?>") {
                    window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ShowPedidos2&pkMesa=" + $pkMesa + "&cliente=" + $('#txtNombres').val()+ "&m=<?php echo date("U");?>";
                }
                else {
                    alert("Usted no puede ingresar a esta mesa");
                }
            }
            else {
                window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ShowPedidos2&pkMesa=" + $pkMesa + "&cliente=" + $('#txtNombres').val()+ "&m=<?php echo date("U");?>";
            }
        }

        function CargarMesa(tipoPedido) {
            console.log("Timer reiniciado");
            clearTimeout(timeout_login);
            timeout_login = setTimeout(cierra_sesion,45000);
            $("#modal_envio_anim").modal("show");
            var param = {pkMesa: $("#txtMesaApertura").val()};
            $.ajax({
                url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=AperturarMesa&" + $('#frmClientePLllevar').serialize() + "&" + $('#frmAperturaMesa').serialize() + "&tipoPedido="+tipoPedido,
                type: 'POST',
                data: param,
                beforeSend: function (xhr) {
                    xhr.overrideMimeType("text/plain; charset=x-user-defined");
                },
                success: function (data) {
                    window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ShowPedidos2&pkMesa=" + $("#txtMesaApertura").val() + "&cliente=" + $('#txtNombres').val()+ "&m=<?php echo date("U");?>";
                }
            });
        }

        var contador = 0;

        function Cantidad($val, $id) {
            console.log(contador);
            if (contador === 0) {
              $('#' + $id).val("");
            }

            var $text = $('#' + $id).val();
            $('#' + $id).val($text + $val);
            contador++;
        }

        var salon_actual = <?php echo $salon_inicial;?>;

       function cambia_sel(id_in){
           console.log("Timer reiniciado");
           clearTimeout(timeout_login);
           timeout_login = setTimeout(cierra_sesion,45000);
           if(id_in == "ap" || id_in == "apd"){
            //Nada
           }else{
            salon_actual = id_in;
            $(".btn-usqay-group").removeClass("btn-usqay-off").removeClass("btn-usqay").addClass("btn-usqay-off");
            $("#btn-salon-"+id_in).removeClass("btn-usqay-off").addClass("btn-usqay");
           }
           $(".collapse-usqay").removeClass("in");
           $("#collapse"+id_in).addClass("in");
       }

       function regresa_salon(){
            console.log("Timer reiniciado");
            clearTimeout(timeout_login);
            timeout_login = setTimeout(cierra_sesion,45000);
            $("#txtPhoneCliente").val("");
            $("#txtNameCliente").val("");
            $("#txtDireccionCliente").val("");
            $("#txtDocCliente").val("");
            $("#txtNombreCliente").val("");
            $("#txtCantidaPersonas").val(1);

            $(".btn-usqay-group").removeClass("btn-usqay-off").removeClass("btn-usqay").addClass("btn-usqay-off");
            $("#btn-salon-"+salon_actual).removeClass("btn-usqay-off").addClass("btn-usqay");
            $(".collapse-usqay").removeClass("in");
            $("#collapse"+salon_actual).addClass("in");
       }
       
        $( document ).ready(function() {
            $.ajaxSetup({ cache: false });

            timeout_login = setTimeout(cierra_sesion,45000);
            
            $('.cnt-btn a').on('click',function(e){
                if($("#accordion").children('div').children('.panel-collapse').hasClass('in')){
                    e.preventDefault();
                    e.stopPropagation();
                }
            });
            
            $("input").click(function(){
                console.log("Timer reiniciado");
                clearTimeout(timeout_login);
                timeout_login = setTimeout(cierra_sesion,45000);
            });
        });
        
        function cierra_sesion(){
            window.location.href = "<?php echo Class_config::get('urlApp') ?>/?t=<?php echo $_COOKIE['t'] ?>";
        }
        </script>
    </body>
</html>
