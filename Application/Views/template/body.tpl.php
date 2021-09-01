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
            background-color: #ef6a00 !important;
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
            border-radius: 10%;
            width: 200px;
            margin: 5px;
            height: 200px;
        }
        .usqay-btn-c {
            border-radius: 15% !important;
        }
        .usqay-btn-red{
            background-color: #ef4d4d !important;
        }
        .usqay-btn-yala{
            background-color: #9cac43 !important;
        }
        .usqay-btn-ayer{
            background-color: #0079bf !important;
        }
        .usqay-btn:hover{
            color: #ef4d4d !important;
        }
        .usqay-btn-red:hover{
            color: #ffa0a0 !important;
        }
        .usqay-btn-yala:hover{
            color: #000 !important;
        }
        .usqay-btn-ayer:hover{
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
      <?php if(isset($_COOKIE["APP"])):?>
        <!--Estilos para APP-->
        <style>
        .modal-dialog {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }

        .modal-content {
            height: auto;
            min-height: 100%;
            border-radius: 0;
        }
        </style>
        <?php endif;?>
    <body onload="nobackbutton();">
        <!-- Fixed navbar -->
        <?php
        $objViewMenu = new Application_Views_IndexView();
        $objViewMenu->showContent();
        ?>

        <br><br><br><br>

        <div class="container-fluid">
            <?php
                $db = new SuperDataBase();
                $mensajes = 0;
                $html_mensaje = "";
                $impresoras = 0;

                //Obtenemos Fecha de cierre
                $query_cierre = "Select fecha from cierrediario where pkCierreDiario = 1";
                $rc = $db->executeQuery($query_cierre);
                $fecha_cierre_c = null;
                if ($row = $db->fecth_array($rc)){
                    $fecha_cierre_c = $row["fecha"];
                }
                
                //Solo hacemos la revision si es administrador
                if(UserLogin::get_pkTypeUsernames() <> 4){
                    //Revisamos comprobantes
                    /*$q1 = "Select * from comprobante_hash where aceptada = 'NE'";
                    $rc = $db->executeQuery($q1);
                    if($rw = $db->fecth_array($rc)){
                        $mensajes = 1;
                        $html_mensaje .= '<div class="alert alert-danger" role="alert">Hay comprobantes sin enviar ya que estuviste sin conexion, cuando se restablesca el internet envialos desde <a href="reportes/comprobantes_pendientes.php">aquí</a></div>';
                    }*/
                    
                    //Revisamos configuracion impresion
                    $tipos_imp = $db->executeQuery("Select * from tipos where estado = 0 AND not exists (Select 1 from configuracion_impresion where configuracion_impresion.opcion = tipos.pkTipo AND terminal = '".$_COOKIE["t"]."')");
                    if($rw = $db->fecth_array($tipos_imp)){
                        $impresoras = 1;
                    }
                    
                    $v1 = $db->executeQuery("Select * from configuracion_impresion where opcion = 'b' AND terminal = '".$_COOKIE["t"]."'");
                    if($rw = $db->fecth_array($v1)){
                        //Nada we
                    }else{
                       $impresoras = 1; 
                    }
                    
                    $v2 = $db->executeQuery("Select * from configuracion_impresion where opcion = 'f' AND terminal = '".$_COOKIE["t"]."'");
                    if($rw = $db->fecth_array($v2)){
                        //Nada we
                    }else{
                       $impresoras = 1; 
                    }
                    
                    $v3 = $db->executeQuery("Select * from configuracion_impresion where opcion = 'c' AND terminal = '".$_COOKIE["t"]."'");
                    if($rw = $db->fecth_array($v3)){
                        //Nada we
                    }else{
                       $impresoras = 1; 
                    }

                    $v4 = $db->executeQuery("Select * from configuracion_impresion where opcion = 'cr' AND terminal = '".$_COOKIE["t"]."'");
                    if($rw = $db->fecth_array($v4)){
                        //Nada we
                    }else{
                       $impresoras = 1; 
                    }

                    $v5 = $db->executeQuery("Select * from configuracion_impresion where opcion = 'co' AND terminal = '".$_COOKIE["t"]."'");
                    if($rw = $db->fecth_array($v5)){
                        //Nada we
                    }else{
                       $impresoras = 1; 
                    }
                    
                    if($impresoras == 1){
                        $mensajes = 1;
                        $html_mensaje .= '<div class="alert alert-danger" role="alert">No todas las opciones tienen impresoras asociadas, es probable no salgan algunos tickets. <a href="reportes/configuracion_impresion.php">Revisar Configuracion Impresion</a></div>';
                    }
                    
                    //Revisamos pagos
                    
                    //Revisamos mensajes
                
                }
            ?>
                       
            <div class="row">
                <?php if($mensajes > 0):?>
                <div class="col-md-12" style="margin-top: -10px;">
                    <?php echo $html_mensaje;?>
                </div>
                <?php endif;?>
                
                <?php if(isset($_COOKIE["APP"])):?>
                <div class="col-md-12 cnt-btn" style="width:100% !important;padding:10px;margin-top:-25px;height:100px !important;overflow-x: scroll;
    overflow-y: hidden;white-space:nowrap;" id="divSalones">
                <?php else:?>
                <div  class="col-md-12 cnt-btn" style="padding:10px;margin-top:-25px;" id="divSalones">
                <?php endif;?>
                <center>
                <?php
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
                <div class="col-md-12">
                    <div id="divAperturarMesas">
                        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="false" style="text-align: center;">
                            <?php                                              
                            $query = "SELECT s.* FROM salon s, accion_caja ac where s.estado = 0 AND ac.pk_accion = s.pkSalon AND ac.tipo_accion = 'SAL' AND ac.caja = '".$_COOKIE["c"]."' ORDER BY s.pkSalon ASC";
                            $contador = 0;
                            $resultSalones_1 = $db->executeQuery($query);
                            while ($rowSalones = $db->fecth_array($resultSalones_1)) {
                                $in = "";
                                if ($contador < 1) {
                                    $in = " in ";
                                }
                                echo'<div class="' . $rowSalones[0] . '">
                                <div id="collapse' . $rowSalones[0] . '" class="collapse-usqay panel-collapse collapse ' . $in . '" role="tabpanel" aria-labelledby="heading' . $rowSalones[0] . '">
                                    <div class="panel-body" style="padding: 0">';
                                
                                //Logica para Salones de Delivery y Llevar
                                if(intval($rowSalones["pkSalon"]) == 43 || intval($rowSalones["pkSalon"]) == 44){
                                
                                echo'<button id="btnMesaTMP" hfre="#" onclick="msfConfirmar(\'TMP\', \'TMP\',\'' . $rowSalones[0] . '\')" class="usqay-btn usqay-btn-c btn btn-lg">Agregar<br/><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span></button>';
                                    
                                    
                                $query = "SELECT * FROM mesas where pkSalon=" . $rowSalones['pkSalon'] . " and estado<>3 order by pkMesa ASC";
                                $resultMesas = $db->executeQuery($query);
                                while ($rowMesas = $db->fecth_array($resultMesas)) {
                                    $queryPedido = "SELECT pkPediido,idUser,subTotal,fechaCierre,fechaApertura FROM pedido where estado = 0 AND pkMesa = '".$rowMesas["pkMesa"]."' order by pkPediido DESC LIMIT 1";
                                    $resultPedido = $db->executeQuery($queryPedido);
                                    while ($rowPedido = $db->fecth_array($resultPedido)) {
                                        $pedido = $rowPedido['pkPediido'];
                                        $id_usuario = $rowPedido["idUser"];
                                        $queryTrabajador = "SELECT nombres,pkTrabajador from trabajador where pkTrabajador = '".$id_usuario."'";
                                        $nombres = "";
                                        $resultTrabajador = $db->executeQuery($queryTrabajador);
                                        while ($rowTrabajador = $db->fecth_array($resultTrabajador)) {
                                            $nombres = strtoupper($rowTrabajador['nombres']);
                                        }
                                        
                                        //Obtenemos cliente
                                        $nombre_cliente = "";
                                        $query01 = "Select * from cliente_externo where id_pedido = '".$pedido."'";
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

                                        //obtener total
                                        $query_total = "select sum(cantidad * precio) as total from detallepedido where pkPediido = '${pedido}' and estado in (1, 2)";

                                        $r_total = $db->executeQuery($query_total);

                                        if ($row_total = $db->fecth_array($r_total)) {
                                            $total = number_format($row_total['total'], 2);
                                        }

                                        //obtener tiempo
                                        $fechaApertura = $rowPedido['fechaApertura'];

                                        $label_fecha_apertura = "
                                        <div>
                                            <span class='glyphicon glyphicon-time'></span>
                                            <span timer-mesas id='timer-mesas${rowMesas['pkMesa']}' hora-inicio='$fechaApertura'>00:00:00</span>
                                        </div>
                                        ";

                                        if($rowPedido["fechaCierre"] <> $fecha_cierre_c){
                                            echo'<button id="btnMesa' . $rowMesas['pkMesa'] . '" onclick="load_universal(\'' . $rowMesas['pkMesa'] . '\', \'' . $rowMesas['nmesa'] . '\',\'' . $rowSalones[0] . '\')"  class="usqay-btn usqay-btn-c btn btn-lg usqay-btn-ayer">' . $rowMesas['nmesa'] . '<br/><span class="glyphicon glyphicon-briefcase" aria-hidden="true"></span> ' . $nombres . $nombre_cliente.'<br/>S/ '.$total.$label_fecha_apertura.'</button>';
                                        }else{
                                            if(intval($rowPedido["subTotal"]) === 0){
                                                echo'<button id="btnMesa' . $rowMesas['pkMesa'] . '" onclick="load_universal(\'' . $rowMesas['pkMesa'] . '\', \'' . $rowMesas['nmesa'] . '\',\'' . $rowSalones[0] . '\')"  class="usqay-btn usqay-btn-c btn btn-lg usqay-btn-red">' . $rowMesas['nmesa'] . '<br/><span class="glyphicon glyphicon-briefcase" aria-hidden="true"></span> ' . $nombres . $nombre_cliente.'<br/>S/ '.$total.$label_fecha_apertura.'</button>';
                                            }else{
                                                echo'<button id="btnMesa' . $rowMesas['pkMesa'] . '" onclick="load_universal(\'' . $rowMesas['pkMesa'] . '\', \'' . $rowMesas['nmesa'] . '\',\'' . $rowSalones[0] . '\')"  class="usqay-btn usqay-btn-c btn btn-lg usqay-btn-yala">' . $rowMesas['nmesa'] . '<br/><span class="glyphicon glyphicon-briefcase" aria-hidden="true"></span> ' . $nombres . $nombre_cliente.'<br/>S/ '.$total.$label_fecha_apertura.'</button>';
                                            }
                                        }
                                    }
                                }                                    
                                }else{
                                    //Logica para Otros Salones                                 
                                    $query = "SELECT * FROM mesas where pkSalon=" . $rowSalones['pkSalon'] . " and estado<>3 order by pkMesa ASC";
                                    $resultMesas = $db->executeQuery($query);
                                    
                                    while ($rowMesas = $db->fecth_array($resultMesas)) {
                                        $queryPedido = "SELECT pkPediido,idUser,subTotal,fechaCierre,fechaApertura FROM pedido where estado = 0 AND pkMesa = '".$rowMesas["pkMesa"]."' order by pkPediido DESC LIMIT 1";
                                        $resultPedido = $db->executeQuery($queryPedido);
                                        $haypedido_ = 0;
                                        while ($rowPedido = $db->fecth_array($resultPedido)) {
                                            $haypedido_ = 1;
                                            $pedido = $rowPedido['pkPediido'];
                                            $id_usuario = $rowPedido["idUser"];
                                            $queryTrabajador = "SELECT nombres,pkTrabajador from trabajador where pkTrabajador = '".$id_usuario."'";
                                            $nombres = "";
                                            $resultTrabajador = $db->executeQuery($queryTrabajador);
                                            while ($rowTrabajador = $db->fecth_array($resultTrabajador)) {
                                                $nombres = strtoupper($rowTrabajador['nombres']);
                                            }
                                            
                                            //Obtenemos cliente
                                            $nombre_cliente = "";
                                            $query01 = "Select * from cliente_externo where id_pedido = '".$pedido."'";
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

                                            //obtener total
                                            $query_total = "select sum(cantidad * precio) as total from detallepedido where pkPediido = '${pedido}' and estado in (1, 2)";

                                            $r_total = $db->executeQuery($query_total);

                                            if ($row_total = $db->fecth_array($r_total)) {
                                                $total = number_format($row_total['total'], 2);
                                            }

                                            //obtener tiempo
                                            $fechaApertura = $rowPedido['fechaApertura'];

                                            $label_fecha_apertura = "
                                            <div>
                                                <span class='glyphicon glyphicon-time'></span>
                                                <span timer-mesas id='timer-mesas${rowMesas['pkMesa']}' hora-inicio='$fechaApertura'>00:00:00</span>
                                            </div>
                                            ";

                                            // echo($label_fecha_apertura);

                                            if($rowPedido["fechaCierre"] <> $fecha_cierre_c){
                                                echo'<button id="btnMesa' . $rowMesas['pkMesa'] . '" onclick="load_universal(\'' . $rowMesas['pkMesa'] . '\', \'' . $rowMesas['nmesa'] . '\',\'' . $rowSalones[0] . '\')" class="usqay-btn usqay-btn-c btn btn-lg usqay-btn-ayer">' . $rowMesas['nmesa'] . '<br/><span class="glyphicon glyphicon-briefcase" aria-hidden="true"></span> '.$nombres.$nombre_cliente.'<br/>S/ '.$total.$label_fecha_apertura.'</button>';
                                            }else{
                                                if(intval($rowPedido["subTotal"]) === 0){
                                                    echo'<button id="btnMesa' . $rowMesas['pkMesa'] . '" onclick="load_universal(\'' . $rowMesas['pkMesa'] . '\', \'' . $rowMesas['nmesa'] . '\',\'' . $rowSalones[0] . '\')" class="usqay-btn usqay-btn-c btn btn-lg usqay-btn-red">' . $rowMesas['nmesa'] . '<br/><span class="glyphicon glyphicon-briefcase" aria-hidden="true"></span> '.$nombres.$nombre_cliente.'<br/>S/ '.$total.$label_fecha_apertura.'</button>';
                                                }else{
                                                    echo'<button id="btnMesa' . $rowMesas['pkMesa'] . '" onclick="load_universal(\'' . $rowMesas['pkMesa'] . '\', \'' . $rowMesas['nmesa'] . '\',\'' . $rowSalones[0] . '\')" class="usqay-btn usqay-btn-c btn btn-lg usqay-btn-yala">' . $rowMesas['nmesa'] . '<br/><span class="glyphicon glyphicon-briefcase" aria-hidden="true"></span> '.$nombres.$nombre_cliente.'<br/>S/ '.$total.$label_fecha_apertura.'</button>';
                                                }
                                            }
                                            
                                        }

                                        if($haypedido_ === 0){
                                            echo'<button id="btnMesa' . $rowMesas['pkMesa'] . '" hfre="#" onclick="load_universal(\'' . $rowMesas['pkMesa'] . '\', \'' . $rowMesas['nmesa'] . '\',\'' . $rowSalones[0] . '\')" class="usqay-btn usqay-btn-c btn btn-lg">' . $rowMesas['nmesa'] . '</button>';
                                        }
                                    }       
                                }
                                echo'</div></div></div>';
                                $contador++;
                            }
                            ?>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!--Inicio Modal Ausente-->
        <div class='modal fade' id='modal_idle' tabindex='-1' role='dialog' data-keyboard="false" data-backdrop="static" style="z-index: 999999 !important;">
        <div class='modal-dialog' style="display: table;">
            <div style="color:#FFF;text-align:center; font-size:32px; font-weight:bold;display: table-cell;vertical-align: middle;">
            <span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
            <br/>
            Bloqueado por Inactividad
            <br/>
            <button id="btn_desbloquear" type="button" class="btn btn-success"><b>Presiona aqui para desbloquear</b></button>
            </div>
        </div>
        </div>
        <!--Fin Modal-->

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
        <!-- Modal para apertura de mesa de salon-->
        <div id="modaConfirmAperturaMesa" class="modal fade" tabindex='-1' role='dialog' data-keyboard="false" data-backdrop="static" style="z-index: 999999 !important;">
            <div class="modal-dialog">
                <div class="modal-content" >
                    <div class="modal-header">
                        <h3 class="modal-title" id="modaConfirmAperturaMesaTitle">Confirmar apertura de <label id="lblNmesa"></label></h3>
                    </div>
                    <div class="modal-body">
                        <input id="txtMesaApertura" style="display: none">
                        <form id="frmAperturaMesa" >
                            <h4>Nombre del Cliente</h4>
                            <input name="clmesa" id="txtNombreCliente" class="form-control">
                            <h4>Número de personas a ocupar</h4>
                            <input name="nmesa" id="txtCantidaPersonas" class="form-control" value="1">
                        </form>
                        <br>
                        <form class="cal" style="">
                            <center>
                                <input class="btn-cal btn" onclick="Cantidad('1', 'txtCantidaPersonas');" type="button" value="1" name="1" >
                                <input class="btn-cal btn" onclick="Cantidad('2', 'txtCantidaPersonas');" type="button" value="2" name="2" >
                                <input class="btn-cal btn" onclick="Cantidad('3', 'txtCantidaPersonas');" type="button" value="3" name="3" >
                                <br/>
                                <input class="btn-cal btn" onclick="Cantidad('4', 'txtCantidaPersonas');" type="button" value="4" name="4" >
                                <input class="btn-cal btn" onclick="Cantidad('5', 'txtCantidaPersonas');" type="button" value="5" name="5" >
                                <input class="btn-cal btn" onclick="Cantidad('6', 'txtCantidaPersonas');" type="button" value="6" name="6" >
                                <br/>
                                <input class="btn-cal btn" onclick="Cantidad('7', 'txtCantidaPersonas');" type="button" value="7" name="7" >
                                <input class="btn-cal btn" onclick="Cantidad('8', 'txtCantidaPersonas');" type="button" value="8" name="8" >
                                <input class="btn-cal btn" onclick="Cantidad('9', 'txtCantidaPersonas');" type="button" value="9" name="9" >
                                <br/>
                                <input class="btn-cal btn" type="button" value="">
                                <input class="btn-cal btn" onclick="Cantidad('0', 'txtCantidaPersonas');" type="button" value="0" name="0" >
                                <input class="btn-cal btn" type="button" value="">
                                <br/>
                                <a style="padding-top:15px;background-color:#ef4d4d !important;color:#FFF !important;" class="btn btn-cal"  onclick="CancelaApertura()"><span class="glyphicon glyphicon-fast-backward"></span></a>
                                <a style="padding-top:15px;background-color:#ef4d4d !important;color:#FFF !important;" onclick="resetP()" href="#" class="btn btn-cal"><span class="glyphicon glyphicon-remove"></span></a>
                                <a style="padding-top:15px;background-color:#009839 !important;color:#FFF !important;" href="#" class="btn btn-cal" onclick="CargarMesa(1)"><span class="glyphicon glyphicon-ok"></span></a>
                            </center>
                        </form>
                        <div style="width:100%;height:35px;"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal para apertura de delivery o llevar -->
        <div id="modalAperturaMesaDelivery" class="modal fade" tabindex='-1' role='dialog' data-keyboard="false" data-backdrop="static" style="z-index: 999999 !important;">
            <div class="modal-dialog" >
                <div class="modal-content" >
                    <div class="modal-header">
                        <h2 class="modal-title" id="modalAperturaMesaDeliveryTitle">Datos del cliente</h2>
                    </div>
                    <div class="modal-body ">
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
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" onclick="CancelaAperturaDelivery();">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="CargarMesa(2)">Aperturar mesa</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            //Variable para timeout en dispositivos moviles
            var timeout_idle = null;

            <?php
            if(isset($_COOKIE["APP"])){
            echo "var app = 1;";
            }else{
            echo "var app = 0;";
            }
            ?>

            function cambia_sel(id_in){
                $(".btn-usqay-group").removeClass("btn-usqay-off").removeClass("btn-usqay").addClass("btn-usqay-off");
                $("#btn-salon-"+id_in).removeClass("btn-usqay-off").addClass("btn-usqay");
                $(".collapse-usqay").removeClass("in");
                $("#collapse"+id_in).addClass("in");
                window.history.pushState('Usqay', 'Usqay', '?controller=Index&&action=ShowHome&s='+id_in+'#no-back-button');
            }
            
            $(document).ready(function() {
                $('.cnt-btn a').on('click',function(e){
                    if($("#accordion").children('div').children('.panel-collapse').hasClass('in')){
                        e.preventDefault();
                        e.stopPropagation();
                    }
                });

                $.ajaxSetup({ cache: false });
            
                $("#divAperturarMesas").bind("contextmenu",function(e){
                    return false;
                });
                
                $("#divSalones").bind("contextmenu",function(e){
                    return false;
                });

                if(app == 1){
                    timeout_idle = setTimeout(muestra_bloqueo,60000);
                }

                $("#btn_desbloquear").click(function(event){
                    location.reload();
                });

                $("body").click(function(event){
                    if(app == 1){
                        clearTimeout(timeout_idle);
                        timeout_idle = setTimeout(muestra_bloqueo,60000);
                    }
                });

                $('#txtPhoneCliente').keypress(function (event) {
                    var keycode = (event.keyCode ? event.keyCode : event.which);
                    if (keycode == '13') {
                        var param = {phone: $("#txtPhoneCliente").val()};
                        $.ajax({
                            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Cliente&&action=ClientPhone",
                            type: 'POST',
                            data: param,
                            dataType: "json",
                            cache: false,
                            success: function (data) {
                                $("#txtNameCliente").val(data.nombres);
                                $("#txtDireccionCliente").val(data.direccion);
                                $("#txtDocCliente").val(data.documento);
                            }
                        });
                    }
                });

                // init

                initCounterTimeMesa();

            });

            function initCounterTimeMesa () {

                let _timers = $('[timer-mesas]');

                _timers.each(function (index, _timer) {
                    setInterval(counterstrike, 1000, _timer.id);
                });
            }

            function counterstrike(id){
                var startDateTime = new Date($("#"+id).attr("hora-inicio"));
                var startStamp = startDateTime.getTime();

                var newDate = new Date();
                var newStamp = newDate.getTime();

                newDate = new Date();
                newStamp = newDate.getTime();
                var diff = Math.round((newStamp-startStamp)/1000);
                
                var d = Math.floor(diff/(24*60*60));
                diff = diff-(d*24*60*60);
                var h = Math.floor(diff/(60*60));
                diff = diff-(h*60*60);
                var m = Math.floor(diff/(60));
                diff = diff-(m*60);
                var s = diff;

                h = h + d*24;
                
                $("#"+id).html(h.toString().padStart(2,"0")+":"+m.toString().padStart(2,"0")+":"+s.toString().padStart(2,"0"));
            }

            function validaMozoApertura($pkMozo, $pkMesa) {
                if ('<?php echo UserLogin::get_pkTypeUsernames() ?>' === "4") {
                    if ($pkMozo === "<?php echo UserLogin::get_idTrabajador() ?>") {
                        window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ShowPedidos2&pkMesa=" + $pkMesa + "&m=<?php echo date("U");?>";
                    }
                    else {
                        $("#modal_envio_anim").modal("hide");
                        alert("Usted no puede ingresar a esta mesa");
                        window.location.reload();
                    }
                }
                else {
                    window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ShowPedidos2&pkMesa=" + $pkMesa + "&m=<?php echo date("U");?>";
                }
            }

            function CargarMesa(tipoPedido) {
                $("#modalAperturaMesaDelivery").modal("hide");
                $('#modaConfirmAperturaMesa').modal('hide');
                $("#modal_envio_anim").modal("show");
                if(tipoPedido == 2){
                    var param0 = {'pkSalon': $("#txtMesaApertura").val()};
                    $.ajax({
                        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Config&&action=getMesa",
                        type: 'POST',
                        data: param0,
                        cache: false,
                        dataType: 'json',
                        success: function (data0) {
                            var param = {pkMesa: data0[0].mesa};
                            $.ajax({
                                url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=AperturarMesa&" + $('#frmClientePLllevar').serialize() + "&" + $('#frmAperturaMesa').serialize() + "&tipoPedido="+tipoPedido,
                                type: 'POST',
                                data: param,
                                cache: false,
                                beforeSend: function (xhr) {
                                    xhr.overrideMimeType("text/plain; charset=x-user-defined");
                                },
                                success: function (data) {
                                    if(parseInt(data.estado) === 1){
                                        validaMozoApertura(data.moso,data0[0].mesa);
                                    }else{
                                        window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ShowPedidos2&pkMesa=" + data0[0].mesa + "&m=<?php echo date("U");?>";
                                    }
                                },
                                error: function () {
                                $("#modal_envio_anim").modal("hide");
                                alert("Hubo un error de comunicacion");
                                }
                            });
                        },
                        error: function () {
                        $("#modal_envio_anim").modal("hide");
                        alert("Hubo un error de comunicacion");
                        }
                    });
                }else{
                    var param = {pkMesa: $("#txtMesaApertura").val()};
                    $.ajax({
                        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=AperturarMesa&" + $('#frmClientePLllevar').serialize() + "&" + $('#frmAperturaMesa').serialize() + "&tipoPedido="+tipoPedido,
                        type: 'POST',
                        data: param,
                        cache: false,
                        beforeSend: function (xhr) {
                            xhr.overrideMimeType("text/plain; charset=x-user-defined");
                        },
                        success: function (data) {
                            if(parseInt(data.estado) === 1){
                                validaMozoApertura(data.moso,$("#txtMesaApertura").val());
                            }else{
                                window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ShowPedidos2&pkMesa=" + $("#txtMesaApertura").val() + "&m=<?php echo date("U");?>";
                            }
                        },
                        error: function () {
                        $("#modal_envio_anim").modal("hide");
                        alert("Hubo un error de comunicacion");
                        }
                    });
                }
            }
            
            function load_universal(pk_mesa,nombre_mesa,pk_salon){
                $("#modal_envio_anim").modal("show");
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
                                $("#modal_envio_anim").modal("hide");
                                alert("Este pedido ya fue cerrado");
                                window.location.reload();
                            }else{
                                msfConfirmar(pk_mesa,nombre_mesa,pk_salon);
                            }
                        }
                    },
                    error: function () {
                      $("#modal_envio_anim").modal("hide");
                      alert("Hubo un error de comunicacion");
                    }
                });
            }

            //Script teclado agregar pedido
            var contador = 0;
            function Cantidad($val, $id) {
                if (contador === 0) {
                    $('#' + $id).val("");
                }

                var $text = $('#' + $id).val();
                $('#' + $id).val($text + $val);
                contador++;
            }

            function resetP(){
                $("#txtCantidaPersonas").val("1");
                contador = 0;
            }

            function CancelaApertura(){
                $("#modaConfirmAperturaMesa").modal("hide");
                $("#txtNombreCliente").val("");
                $("#txtCantidaPersonas").val("1");
                contador = 0;  
            }

            function CancelaAperturaDelivery(){
                $("#modalAperturaMesaDelivery").modal("hide");
                $("#txtNameCliente").val("");
                $("#txtDireccionCliente").val("");
                $("#txtDocCliente").val("");
            }
            
            function msfConfirmar($mesa, $nombre, $pkSalon) {
                $("#modal_envio_anim").modal("show");
                if($mesa === "TMP" && $nombre === "TMP"){
                    $("#txtMesaApertura").val($pkSalon);
                    $("#modal_envio_anim").modal("hide");
                    $('#modalAperturaMesaDelivery').modal('show');
                }else{
                    $("#lblNmesa").html($nombre);
                    $("#txtMesaApertura").val($mesa);
                    if ($pkSalon === "44" || $pkSalon === "43") {
                        $("#modal_envio_anim").modal("hide");
                        $('#modalAperturaMesaDelivery').modal('show');
                    }
                    else{
                        $("#modal_envio_anim").modal("hide");
                        $('#modaConfirmAperturaMesa').modal('show');
                    }
                }
            }

            //Funciones para el timeout si es dispositivo movil
            function muestra_bloqueo(){
                /*$('.modal').modal('hide');
                $("#modal_idle").modal("show");*/
                //Parche rapido para la kermes
                location.reload();
            }


        </script>
    </body>
</html>
