<?php
error_reporting(E_ALL);
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Controllers_ComprobanteController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {
            case 'AnulaComprobanteAction':
                $this->_anulaComprobante();
                break;

            case 'SaveGuiaComprobanteAction':
                $this->_SaveGuiaComprobante();
                break;

            case 'EditGuiaAction':
                $this->_EditComprobante();
                break;

            case 'DeleteGuiaAction':
                $this->_DeleteComprobante();
                break;

            case 'ActiveGuiaAction':
                $this->_ActiveComprobante();
                break;

            case 'SaveGuiaSalidaAction':
                $this->_SaveGuiaSalida();
                break;

            case 'EditGuiaSalidaAction':
                $this->_EditGuiaSalida();
                break;

            case 'DetalleComprobanteAction':
                $this->_DetalleComprobante();
            break;

            case "ListComprobantesAction":
                $objComprobante = new Application_Models_ComprobanteModel();
                $fechaInicio = date('Y-m-d');
                if (isset($_REQUEST['fecha_inicio']))
                    $fechaInicio = $_REQUEST['fecha_inicio'];

                $fechaFin = date('Y-m-d');
                if (isset($_REQUEST['fecha_fin']))
                    $fechaFin = $_REQUEST['fecha_fin'];

                $objComprobante->listadoComprobante($_REQUEST['tipo'], $fechaInicio, $fechaFin);
                break;
                
            //Generamos un comprobante electronico en Diferido
            //Gino Lluen 2019
            case 'DiferidoAction':
                $this->_generarComprobanteDiferido();
                break;

            //Generamos un comprobante electronico personalizado
            case 'PersonalizadoAction':
                $this->_generarComprobantePersonalizado();
                break; 

            //Procedimiento de pago Definitivo - Gino Lluen 2019
            case 'Comprobante2019Action':
                $this->_comprobante2019();
            break;

        }
    }

    private function _DetalleComprobante()
    {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $serie = $_POST['serie'];
                $obj = new Application_Models_ComprobanteModel();
                $detalles = $obj->detallesComprobante($serie);
                echo json_encode($detalles);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _SaveGuiaComprobante() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_ComprobanteModel();

                $_valor = "";
                if (isset($_REQUEST['txtTipoComprobante'])) {
                    $_valor = $_REQUEST['txtTipoComprobante'];
                }

                $_TipoComprobante = "";
                if (isset($_REQUEST['txtTipoComprobante'])) {
                    $_TipoComprobante = $_REQUEST['txtTipoComprobante'];
                }
                $_NroComprobante = "";
                if (isset($_REQUEST['txtNroComprobante'])) {
                    $_NroComprobante = $_REQUEST['txtNroComprobante'];
                }
                $_fechaComprobante = "";
                if (isset($_REQUEST['Fecha'])) {
                    $_fechaComprobante = $_REQUEST['Fecha'];
                }
                if ($_valor == '1' || $_valor == '2') {
                    $_id_proveedor = "";
                    if (isset($_REQUEST['id_proveedor'])) {
                        $_id_proveedor = $_REQUEST['id_proveedor'];
                    }
                    $_Procedencia = "";
                } else

                if ($_valor == '3') {

                    $_id_proveedor = "";
                    $_Procedencia = "";
                    if (isset($_REQUEST['txtProcedencia'])) {
                        $_Procedencia = $_REQUEST['txtProcedencia'];
                    }
                }

                echo $result = $obj->_saveGuia($_valor, $_TipoComprobante, $_NroComprobante,$_fechaComprobante, $_id_proveedor, $_Procedencia);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _SaveGuiaSalida() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_ComprobanteModel();

                $_valor = 4;
                $_TipoComprobante = 4;
                $_NroComprobante = "";
                if (isset($_REQUEST['txtNroComprobante'])) {
                    $_NroComprobante = $_REQUEST['txtNroComprobante'];
                }
                $_fechaComprobante = "";
                if (isset($_REQUEST['Fecha'])) {
                    $_fechaComprobante = $_REQUEST['Fecha'];
                }
                echo $result = $obj->_saveGuia($_valor, $_TipoComprobante, $_NroComprobante,$_fechaComprobante,"", "");
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _EditGuiaSalida() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_ComprobanteModel();

                $_valor = 4;
                $_PkGuia = "";
                if (isset($_REQUEST['id'])) {
                    $_PkGuia = $_REQUEST['id'];
                }
                $_TipoComprobante = 4;
                $_NroComprobante = "";
                if (isset($_REQUEST['txtNroComprobante'])) {
                    $_NroComprobante = $_REQUEST['txtNroComprobante'];
                }
                $_fechaComprobante = "";
                if (isset($_REQUEST['Fecha'])) {
                    $_fechaComprobante = $_REQUEST['Fecha'];
                }
                echo $result = $obj->_editGuia($_valor, $_PkGuia, $_TipoComprobante, $_NroComprobante,$_fechaComprobante, "", "");
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _EditComprobante() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_ComprobanteModel();

                $_valor = "";
                if (isset($_REQUEST['txtTipoComprobante'])) {
                    $_valor = $_REQUEST['txtTipoComprobante'];
                }

                $_PkGuia = "";
                if (isset($_REQUEST['id'])) {
                    $_PkGuia = $_REQUEST['id'];
                }

                $_TipoComprobante = "";
                if (isset($_REQUEST['txtTipoComprobante'])) {
                    $_TipoComprobante = $_REQUEST['txtTipoComprobante'];
                }
                $_NroComprobante = "";
                if (isset($_REQUEST['txtNroComprobante'])) {
                    $_NroComprobante = $_REQUEST['txtNroComprobante'];
                }
                $_fechaComprobante = "";
                if (isset($_REQUEST['Fecha'])) {
                    $_fechaComprobante = $_REQUEST['Fecha'];
                }
                $_Procedencia="";
                if ($_valor == '1' || $_valor == '2') {
                    $_id_proveedor = "";
                    if (isset($_REQUEST['id_proveedor'])) {
                        $_id_proveedor = $_REQUEST['id_proveedor'];
                    }
                    $_Procedencia = "";
                } else

                if ($_valor == '3') {
                    $_id_proveedor = "";
                    $_Procedencia = "";
                    if (isset($_REQUEST['txtProcedencia'])) {
                        $_Procedencia = $_REQUEST['txtProcedencia'];
                    }
                }

                echo $result = $obj->_editGuia($_valor, $_PkGuia, $_TipoComprobante, $_NroComprobante,$_fechaComprobante, $_id_proveedor, $_Procedencia);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _DeleteComprobante() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelComprobante = new Application_Models_ComprobanteModel();
                $objModelComprobante->eliminarGuia($_REQUEST['id2']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _ActiveComprobante() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelComprobante = new Application_Models_ComprobanteModel();
                $objModelComprobante->activarGuia($_REQUEST['id2']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _anulaComprobante() {
        $obj = new Application_Models_ComprobanteModel();
        $obj->_AnulaComprobante($_POST['id']);
    }

    //Funcion definitiva para generacion de comprobantes desde pedido
    function _comprobante2019(){
        error_reporting(E_ALL);
        $db = new SuperDataBase();
        //Antes de todo verificamos si el pedido sigue abierto
        $r_abierto = $db->executeQuery("Select * from pedido where pkPediido = '".$_REQUEST["pkPediido"]."' AND (estado = 0 OR estado = 4)");
        if($row_abierto = $db->fecth_array($r_abierto)){
            //Obtenemos fecha cierre original  y total original del pedido

            //Establecemos si se va a poner en el ticket la propina
            if(isset($_REQUEST["propina"])){
                $query_ipropina = "Update pedido set subTotal = '".$_REQUEST["propina"]."' where pkPediido = '".$_REQUEST["pkPediido"]."'";
                $db->executeQuery($query_ipropina);

                if(intval($_REQUEST["propina"]) === 1){
                    setcookie("fpropina",'IEZ', time() + 3600000, '/');
                }else{
                    setcookie("fpropina",'NOU', time() - 3600000, '/');
                }
            }

            //Util para creditos
            $fecha_original = $row_abierto["fechaCierre"];
            $total_original = $row_abierto["total"];

            //Obtenemos Fecha de cierre
            $query_cierre = "Select fecha from cierrediario where pkCierreDiario = 1";
            $rc = $db->executeQuery($query_cierre);
            $fecha_cierre_c = null;
            if ($row = $db->fecth_array($rc)){
                $fecha_cierre_c = $row["fecha"];
            }

            //Obtenemos tipos de cambio
            $cambios = array();
            $query_cambio = "Select * from tipo_cambio where fecha_cierre = '".$fecha_cierre_c."'";
            $result_cambio = $db->executeQuery($query_cambio);
            while($row = $db->fecth_array($result_cambio)){
                $cambios[$row["moneda"]] = $row["cambio"];
            }

            $usuario = UserLogin::get_id();
            //Para conservar logica tipo pago de tabla pedido
            $tipo_pago = 1;
            $nombre_tarjeta = "";
            $vuelto_procesado = 0;
            $total_otros = floatval($_REQUEST["pagado_otros"]);
            $total_efectivo = floatval($_REQUEST["pagado_efectivo"]);
            $vuelto = floatval($_REQUEST["vuelto"]);
            $total_cuenta = floatval($_REQUEST["total"]);
            $descuento_dinero = floatval($_REQUEST["descuento_monto"]);

            $id_pedido = $_REQUEST['pkPediido'];

            //Logica para pagos parciales
            $parcial = intval($_REQUEST["parcial"]);
            if($parcial === 1){
                //Insertamos nuevo pedido
                $query_nuevo_pedido = "insert into pedido (pkPediido,pkMesa,fechaApertura,idUser,dateModify, fechaCierre) values(NULL,'".$_REQUEST["mesa"]."',now(),'".$usuario."',now(),'".$fecha_cierre_c."');";
                $db->executeQuery($query_nuevo_pedido);
                //Obtenemos codigo generado                
                $id_pedido = $db->getId();
            }

            //Array para respuesta
            $array_respuesta = array();
            $array_respuesta["exito"] = 1;
            $array_respuesta["mensaje"] = "";
            $array_respuesta["id_comprobante"] = $id_pedido;

            //Verficamos si existe la variable sin pago
            //Si existe es un credito
            //Si es un credito guardamos datos adicionales
            //Y verificamos si no se deben ingresar pagos a caja
            $credito = 0;
            if(isset($_REQUEST["sin_pago"])){
                if(intval($_REQUEST["sin_pago"]) === 0){
                    $credito = 1;
                }else{
                    $credito = 2;
                }
            }

            if($credito < 2){

                $total_detraccion = 0;

                if (isset($_REQUEST['total_detraccion'])) {
                    if ($_REQUEST['total_detraccion'] > 0) {

                        $query_detraccion = "Select * from cloud_config where parametro = 'porcentaje_detraccion'"; 
                        $res_detraccion = $db->executeQueryEx($query_detraccion);
                        if ($row_detraccion = $db->fecth_array($res_detraccion)) {
                            $query_detraccion2 = "select * from porcentaje_detraccion where id = ${row_detraccion['valor']}";
                            $res_detraccion2 = $db->executeQueryEx($query_detraccion2);
                            if ($row_detraccion2 = $db->fecth_array($res_detraccion2)) {
                                
                                $codigo_detraccion = $row_detraccion2['codigo'];
                                $porcentaje_detraccion = floatval($row_detraccion2['porcentaje']);
                                $total_detraccion = floatval($row_detraccion2['porcentaje'] * $total_cuenta / 100);
                            }
                        }
                    }

                }

                //Guardamos medios de pago y propinas
                $array_pagos = json_decode($_REQUEST['pagos'], true);
                $hubo_pagos = 0;
                for ($i = 0; $i < count($array_pagos); $i++) {
                    if($array_pagos[$i][0] === "PAGO"){
                        $monto_insertar = floatval($array_pagos[$i][7]);
                        if(intval($array_pagos[$i][1])>1){
                            $tipo_pago = 2;
                            $nombre_tarjeta .= $array_pagos[$i][5]."|";
                        }else{
                            $tipo_pago = 1;
                            $nombre_tarjeta .= $array_pagos[$i][5]."|";
                            //Procesamos vuelto para pagos en efectivo
                            if(intval($array_pagos[$i][2]) === 1){
                                //Si es moneda nacional
                                if($vuelto > 0){
                                    if($vuelto_procesado === 0){
                                        if($monto_insertar > $vuelto){
                                            $monto_insertar = $monto_insertar - $vuelto;
                                            $vuelto_procesado = 1;
                                        }
                                    }
                                } 
                            }else{
                                //Si es moneda extranjera
                                if($vuelto > 0){
                                    if($vuelto_procesado === 0){
                                        $monto_con_cambio = round(($monto_insertar*$cambios[$array_pagos[$i][2]]),2);
                                        $vuelto_con_cambio = round(($vuelto/$cambios[$array_pagos[$i][2]]),2);
                                        if($monto_con_cambio > $vuelto_con_cambio){
                                            $monto_insertar = $monto_insertar - $vuelto_con_cambio;
                                            $vuelto_procesado = 1;
                                        }
                                    }
                                } 
                            }
                        }
                        $hubo_pagos = 1;
                        $query_pago = "Insert into movimiento_dinero values(NULL,'".$id_pedido."','PED','".$monto_insertar."','".$array_pagos[$i][1]."','".$array_pagos[$i][2]."','".$fecha_cierre_c."',now(),'".$usuario."','".$_COOKIE["c"]."','".$array_pagos[$i][4]."',NULL,1)";
                        $db->executeQuery($query_pago);
                    }else{
                        $query_propina = "Insert into pedido_propina values(NULL,'".$id_pedido."','".$array_pagos[$i][3]."','".$array_pagos[$i][1]."','".$array_pagos[$i][2]."','".$array_pagos[$i][7]."','".$fecha_cierre_c."',now())";
                        $db->executeQuery($query_propina);
                    }
                }
                //Si no hay pago pagamos toda la cuenta en efectivo
                if($hubo_pagos === 0){
                    $tipo_pago = 1;
                    $nombre_tarjeta .= "EFECTIVO";
                    $total_efectivo = $total_cuenta;

                    $query_default = "Insert into movimiento_dinero values(NULL,'".$id_pedido."','PED','".($total_cuenta - $total_detraccion)."','1','1','".$fecha_cierre_c."',now(),'".$usuario."','".$_COOKIE["c"]."',NULL,NULL,1)";
                    $db->executeQuery($query_default);
                }else{
                    $nombre_tarjeta = substr($nombre_tarjeta,0,-1);
                }

                if ($total_detraccion > 0) {

                    $query_detra = "insert into pedido_detraccion (pedido_id, codigo_detraccion, porcentaje_detraccion, total) values ";
                    $query_detra .= "($id_pedido, '$codigo_detraccion', $porcentaje_detraccion, $total_detraccion)";
                    $db->executeQuery($query_detra);

                    $query_detra = "Insert into movimiento_dinero values(NULL,'".$id_pedido."','PED','".($total_detraccion)."','0','1','".$fecha_cierre_c."',now(),'".$usuario."','".$_COOKIE["c"]."','DETRACCION',NULL,1)";
                    $db->executeQuery($query_detra);
                }
            }

            if (intval($_REQUEST["tipo_comprobante"]) === 0){
                //Pago sin comprobante
                //Si es pago parcial actualizamos los detalles enviados
                if($parcial === 1){
                    $db->executeQuery("SET SQL_SAFE_UPDATES = 0;");
                    $array_platos = json_decode($_POST['productos'], true);
                    for ($i = 0; $i < count($array_platos); $i++) {
                        //Recorremos array enviado
                        $pos = strpos($array_platos[$i]['pkPedido'], "C");
                        if ($pos === false) {
                            //Si es detalle normal
                            $query_cambio = "Update detallepedido set pkPediido = '".$id_pedido."' where pkDetallePedido = '".$array_platos[$i]['pkPedido']."'";
                            $db->executeQuery($query_cambio);
                        }else{
                            // //Si es detalle cambiado
                            // //Actualizamos cambio facturacion
                            // $id_cambio = substr($array_platos[$i]['pkPedido'],1);
                            // $query_cambio_c = "Update cambio_facturacion set pk_pedido_destino = '".$id_pedido."' where id = '".$id_cambio."'";
                            // $db->executeQuery($query_cambio_c);
                            // //Obtenemos id detalle original
                            // $query_detalle_original = "Select pk_detalle from cambio_facturacion where id = '".$id_cambio."'";
                            // $id_detalle_original = null;
                            // $rdet = $db->executeQuery($query_detalle_original);
                            // if ($rowd = $db->fecth_array($rdet)){
                            //     $id_detalle_original = $rowd["pk_detalle"];
                            // }
                            // //Actualizamos detalle original
                            // $query_cambio = "Update detallepedido set pkPediido = '".$id_pedido."' where pkDetallePedido = '".$id_detalle_original."'";
                            // $db->executeQuery($query_cambio);
                        }
                    }
                }

                //Finalizamos pedido
                $query_pago = "Update pedido set dateModify=now(), total='".$total_cuenta."', fechaFin=now(), descuento='".$descuento_dinero."',idUser='".$usuario."', estado=1, tipo_pago='".$tipo_pago."', fechaCierre='".$fecha_cierre_c."', total_efectivo='".$total_efectivo."', total_tarjeta='".$total_otros."', nombreTarjeta='".$nombre_tarjeta."' where pkPediido = '".$id_pedido."'";
                $db->executeQuery($query_pago);

                //Efectivo Usado
                $db->executeQuery("Insert into pedido_efectivo values (NULL,'".$id_pedido."','".$total_efectivo."')");

                //Cerramos mesa
                $query_mesa = "update mesas set estado = 0 where pkMesa = '".$_REQUEST["mesa"]."'";
                $db->executeQuery($query_mesa);

                //Insertamos el pedido en que caja fue hecho
                $query_caja = "Insert into accion_caja values(NULL,'".$id_pedido."','PED','".$_COOKIE["c"]."')";
                $db->executeQuery($query_caja);

                //Verificamos si es credito e insertamos
                if($credito > 0){
                    $query_credito = "Insert into creditos values (NULL,'".$id_pedido."','".$fecha_original."','".$fecha_cierre_c."','".$total_original."','".$total_cuenta."','".$credito."')";
                    $db->executeQuery($query_credito);
                }

                //Finalizamos e imprimimos
                echo json_encode($array_respuesta);
            }else{
                //Pago con comprobante
                $objModelComprobante = new Application_Models_ComprobanteModel();
                $correlativo = NULL;
                $dni = "";
                $ruc = "";

                //Operaciones segun tipo de comprobante
                if(intval($_REQUEST["tipo_comprobante"]) == 1){
                    
                    if (strlen($_REQUEST['documento']) == 8) {
                        //Actualizamos cliente
                        $dni = $_REQUEST['documento'];
                        $rdoc = 0;
                        if ($dni != "") {
                            $objPersona = new Application_Models_WorkPeopleModel();
                            $rdoc = $objPersona->_verficaPersona($dni, urlencode($_REQUEST['cliente']), urlencode($_REQUEST['direccion']), urlencode($_REQUEST['correo']));
                        }

                        if($_REQUEST["documento"] == "-"){
                            $dni = $rdoc;
                        }
                        
                    } else if (strlen($_REQUEST['documento']) == 11) {
                        //Actualizamos cliente
                        $ruc = $_REQUEST['documento'];
                        if ($ruc != "") {
                            $objPersona = new Application_Models_WorkPeopleModel();
                            $objPersona->_verficaPersonaJuridica($ruc, urlencode($_REQUEST['cliente']), urlencode($_REQUEST['direccion']) ,$_REQUEST['correo']);
                        }
                    }

                    //Obtenemos correlativo
                    $c1 = "Select max(ncomprobante) as actual from comprobante where pkTipoComprobante = 1";
                    $s1 = $db->executeQuery($c1);
                    if ($row = $db->fecth_array($s1)){
                        $correlativo = intval($row["actual"])+1;
                    }
                }else{
                    //Actualizamos cliente
                    $ruc = $_REQUEST['documento'];
                    if ($ruc != "") {
                        $objPersona = new Application_Models_WorkPeopleModel();
                        $objPersona->_verficaPersonaJuridica($ruc, urlencode($_REQUEST['cliente']), urlencode($_REQUEST['direccion']) ,$_REQUEST['correo']);
                    }

                    //Obtenemos correlativo
                    $c1 = "Select max(ncomprobante) as actual from comprobante where pkTipoComprobante = 2";
                    $s1 = $db->executeQuery($c1);
                    if ($row = $db->fecth_array($s1)){
                        $correlativo = intval($row["actual"])+1;
                    }
                }

                //Si es pago parcial actualizamos los detalles enviados
                if($parcial === 1){
                    $db->executeQuery("SET SQL_SAFE_UPDATES = 0;");
                    $array_platos = json_decode($_POST['productos'], true);
                    for ($i = 0; $i < count($array_platos); $i++) {
                        //Recorremos array enviado
                        $pos = strpos($array_platos[$i]['pkPedido'], "C");
                        if ($pos === false) {
                            //Si es detalle normal
                            $query_cambio = "Update detallepedido set pkPediido = '".$id_pedido."' where pkDetallePedido = '".$array_platos[$i]['pkPedido']."'";
                            $db->executeQuery($query_cambio);
                        }else{
                            // //Si es detalle cambiado
                            // //Actualizamos cambio facturacion
                            // $id_cambio = substr($array_platos[$i]['pkPedido'],1);
                            // $query_cambio_c = "Update cambio_facturacion set pk_pedido_destino = '".$id_pedido."' where id = '".$id_cambio."'";
                            // $db->executeQuery($query_cambio_c);
                            // //Obtenemos id detalle original
                            // $query_detalle_original = "Select pk_detalle from cambio_facturacion where id = '".$id_cambio."'";
                            // $id_detalle_original = null;
                            // $rdet = $db->executeQuery($query_detalle_original);
                            // if ($rowd = $db->fecth_array($rdet)){
                            //     $id_detalle_original = $rowd["pk_detalle"];
                            // }
                            // //Actualizamos detalle original
                            // $query_cambio = "Update detallepedido set pkPediido = '".$id_pedido."' where pkDetallePedido = '".$id_detalle_original."'";
                            // $db->executeQuery($query_cambio);
                        }
                    }
                }

                //series
                $serie_comprobante = "";
                if(intval($_REQUEST["tipo_comprobante"]) == 1){
                    $c0 = "Select * from cloud_config where parametro = 'sfactura'";
                    $s0 = $db->executeQuery($c0);
                    if ($row = $db->fecth_array($s0)){
                        $serie_comprobante = $row["valor"];
                    }
                }else{
                    $c0 = "Select * from cloud_config where parametro = 'sboleta'";
                    $s0 = $db->executeQuery($c0);
                    if ($row = $db->fecth_array($s0)){
                        $serie_comprobante = $row["valor"];
                    }
                }


                //Insertamos cabecera comprobante
                $query_cabecera = "Insert into comprobante values(NULL,'".$_REQUEST["tipo_comprobante"]."',0,0,0,0,0,'".$tipo_pago."','".$total_efectivo."','".$total_otros."','".$nombre_tarjeta."',now(),now(),'".$usuario."','".$descuento_dinero."','0','".$usuario."',now(),'SU009','".$serie_comprobante."','".$correlativo."','".$ruc."','".$dni."')";
                //echo $query_cabecera;
                $db->executeQuery($query_cabecera);
                
                //Obtenemos codigo generado                
                $serie_interna = $db->getId();
                        
                //Agregamos detalle
                $query_detalle_primario = "Insert into detallecomprobante values(NULL,'".$serie_interna."','".$id_pedido."','".$total_cuenta."')";                
                $db->executeQuery($query_detalle_primario);

                //Actualizamos pagos
                $query_pago = "Update pedido set dateModify=now(), total='".$total_cuenta."', fechaFin=now(), descuento='".$descuento_dinero."',idUser='".$usuario."', tipo_pago='".$tipo_pago."', fechaCierre='".$fecha_cierre_c."', total_efectivo='".$total_efectivo."', total_tarjeta='".$total_otros."', nombreTarjeta='".$nombre_tarjeta."' where pkPediido = '".$id_pedido."'";
                $db->executeQuery($query_pago);
                
                //Agregamos items a la factura

                $array_platos = json_decode($_POST['productos'], true);
                for ($i = 0; $i < count($array_platos); $i++) {
                    //Recorremos array enviado
                    if(floatval($array_platos[$i]['precio'])>0 && intval($array_platos[$i]['estado'])<>3){
                        //Solo agregamos detalles con precio mayor a cero
                        $pos = strpos($array_platos[$i]['pkPedido'], "C");
                        if ($pos === false) {
                            $objModelComprobante->addDetallePedidoComprobante($serie_interna, $array_platos[$i]['pkPedido']);
                        }
                    }
                }

                //Verificamos si es credito e insertamos
                if($credito > 0){
                    $query_credito = "Insert into creditos values (NULL,'".$id_pedido."','".$fecha_original."','".$fecha_cierre_c."','".$total_original."','".$total_cuenta."','".$credito."')";
                    $db->executeQuery($query_credito);
                }

                $extras_parcial = [
                    "pedido_original" => $_REQUEST['pkPediido'],
                    "platos" => json_decode($_POST['productos'], true),
                ];

                //Finalmente invocamos a FE
                if($parcial === 1){
                    $this->generaElectronica($serie_interna,$id_pedido,2,$extras_parcial);
                }else{
                    $this->generaElectronica($serie_interna,$id_pedido,1); 
                }

                //Efectivo Usado
                $db->executeQuery("Insert into pedido_efectivo values (NULL,'".$id_pedido."','".$total_efectivo."')");
            }
        }else{
            //Array para respuesta
            $array_respuesta = array();
            $array_respuesta["exito"] = 2;
            $array_respuesta["mensaje"] = "¡El pedido ya finalizo, no se puede cobrar!";
            $array_respuesta["id_comprobante"] = 0;
        }
    }
    
    //Funcion para eliminar comprobante en caso algo salga mal
    function elimina_comprobante($idComprobante,$idPedido){
        $db = new SuperDataBase();
        $db->executeQuery("SET SQL_SAFE_UPDATES = 0;");
        //Eliminamos por si es credito
        $db->executeQuery("Delete from creditos where id_pedido = '".$idPedido."'");
        //Eliminamos Pagos registrados
        $db->executeQuery("Delete from movimiento_dinero where id_origen = '".$idPedido."' AND tipo_origen = 'PED'");
        //Eliminamos Propinas registradas
        $db->executeQuery("Delete from pedido_propina where pkPediido = '".$idPedido."'");
        //Eliminamos los detalles2
        $db->executeQuery("Delete from detalle_comprobante2 where pkDetalleComprobante = '".$idComprobante."'");
        //Eliminamos los detalles
        $db->executeQuery("Delete from detallecomprobante where pkComprobante = '".$idComprobante."'");      
        //Eliminamos el hash 
        $db->executeQuery("Delete from comprobante_hash where pkComprobante = '".$idComprobante."'");  
        //Eliminamos la cabecera
        $db->executeQuery("Delete from comprobante where pkComprobante = '".$idComprobante."'");
        //Eliminamos los detalles de impuestos
        $db->executeQuery("Delete from comprobante_impuestos where pkComprobante = '".$idComprobante."'");
    }
    
    private $contador_repeticion = 0;
    //Funcion para generar factura electronica
    function generaElectronica($idComprobante,$id_pedido,$accion,$extras_parcial = null){
        error_reporting(E_ALL);
        //Codigo Para Conexion con NubeFACT - Gino Lluen 2018
        //Actualizado para aceptar productos, servicios y distintos tipos de afectacion de IGV - Agosto 2019
        //Actualizado para considerar impuesto a las bolsas plasticas - Agosto 2019
        //Obtenemos cabecera desde Base de Datos
        $db = new SuperDataBase();
        $query_cabecera = "Select * from comprobante where pkComprobante = '".$idComprobante."'";
        //echo $query_cabecera;
        $r0 = $db->executeQuery($query_cabecera);
        $data_cabecera = NULL;
        
        while($ra = $db->fecth_array($r0)){
            $data_cabecera = $ra;
        }

        //Obtenemos monto de impuesto de bolsa plastica
        $monto_icbper = 0;
        $query_icbper = "Select * from cloud_config where parametro = 'icbper'";
        $result_i = $db->executeQuery($query_icbper);

        if($row = $db->fecth_array($result_i)){
            $monto_icbper = floatval($row["valor"]);
        }

        //Para facturacion electronica revisamos los tipos de impuesto

        $total_gravada = 0;
        $total_inafecta = 0;
        $total_exonerada = 0;
        $total_gratuita = 0;

        $total_igv = 0;
        $total_impuestos_bolsas = 0;

        $total_descuentos = 0;

        $total_venta = 0;
        
        //Hacemos cabecera
        $cabecera = array();
        $cabecera["operacion"] = "generar_comprobante";
        $serie = NULL;
        $email = "";
        if(intval($data_cabecera["pkTipoComprobante"]) == 1){
            //Obtenemos Serie           
            $c0 = "Select * from cloud_config where parametro = 'sboleta'";
            $s0 = $db->executeQuery($c0);
            if ($row = $db->fecth_array($s0)){
                $serie = $row["valor"];
            }
            $cabecera["tipo_de_comprobante"] = 2;
            if(intval($data_cabecera["documento"]) > 0 || intval($data_cabecera["ruc"]) > 0){
                $cabecera["cliente_tipo_de_documento"] = 1;
                $cabecera["cliente_numero_de_documento"] = $data_cabecera["documento"];
                //Obtenemos datos cliente
                $c1 = "Select * from person where documento = '".$data_cabecera["documento"]."'";
                $s1 = $db->executeQuery($c1);
                if ($row1 = $db->fecth_array($s1)){
                    $cabecera["cliente_denominacion"] = urldecode($row1["nombres"]);
                    $cabecera["cliente_direccion"] = urldecode($row1["address"]);
                    $email = $row1["email"];
                }
                if (strlen($data_cabecera["ruc"]) == 11) {
                    $cabecera["cliente_tipo_de_documento"] = 6;
                    $cabecera["cliente_numero_de_documento"] = $data_cabecera["ruc"];
                    //Obtenemos datos cliente
                    $c1 = "Select razonSocial,address,email from persona_juridica where ruc = '".$data_cabecera["ruc"]."'";
                    $s1 = $db->executeQuery($c1);
                    if ($row1 = $db->fecth_array($s1)){
                        $cabecera["cliente_denominacion"] = urldecode($row1["razonSocial"]);
                        $cabecera["cliente_direccion"] = urldecode($row1["address"]);
                        $email = $row1["email"];
                    }
                }
                
            }else{
                if(intval($data_cabecera["documento"]) === 0){
                    $cabecera["cliente_tipo_de_documento"] = "-";
                    $cabecera["cliente_numero_de_documento"] = "---";
                    $cabecera["cliente_denominacion"] = "---";
                    $cabecera["cliente_direccion"] = "---";
                }else{
                    if(intval($data_cabecera["documento"]) < 0){
                        $cabecera["cliente_tipo_de_documento"] = "-";
                        $cabecera["cliente_numero_de_documento"] = "---";
                        //Obtenemos datos cliente
                        $c1 = "Select * from cliente_generico where id = '".$data_cabecera["documento"]."'";
                        $s1 = $db->executeQuery($c1);
                        if ($row1 = $db->fecth_array($s1)){
                            $cabecera["cliente_denominacion"] = urldecode($row1["nombre"]);
                            $cabecera["cliente_direccion"] = urldecode($row1["direccion"]);
                        }
                    }
                }

            }
        }else{
            //Obtenemos Serie
            $serie = NULL;
            $c0 = "Select * from cloud_config where parametro = 'sfactura'";
            $s0 = $db->executeQuery($c0);
            if ($row = $db->fecth_array($s0)){
                $serie = $row["valor"];
            }
            $cabecera["tipo_de_comprobante"] = 1;
            $cabecera["cliente_tipo_de_documento"] = 6;
            $cabecera["cliente_numero_de_documento"] = $data_cabecera["ruc"];
            //Obtenemos datos cliente
            $c1 = "Select razonSocial,address,email from persona_juridica where ruc = '".$data_cabecera["ruc"]."'";
            $s1 = $db->executeQuery($c1);
            if ($row1 = $db->fecth_array($s1)){
                $cabecera["cliente_denominacion"] = urldecode($row1["razonSocial"]);
                $cabecera["cliente_direccion"] = urldecode($row1["address"]);
                $email = $row1["email"];
            }
        }
        $cabecera["serie"] = $serie;
        $cabecera["numero"] = $data_cabecera["ncomprobante"];
        $cabecera["sunat_transaction"] = 1;
        if($email === ""){
            $cabecera["cliente_email"] = "";
        }else{
            $cabecera["cliente_email"] = $email;
        }
        $cabecera["cliente_email_1"] = "";
        $cabecera["cliente_email_2"] = "";
        $cabecera["fecha_de_emision"] = date("d-m-Y");
        $cabecera["fecha_de_vencimiento"] = "";
        $cabecera["moneda"] = 1;
        $cabecera["tipo_de_cambio"] = "";
        $cabecera["porcentaje_de_igv"] = "18.00";   
        $cabecera["total_anticipo"] = "";
        $cabecera["total_otros_cargos"] = "";
        $cabecera["percepcion_tipo"] = "";
        $cabecera["percepcion_base_imponible"] = "";
        $cabecera["total_percepcion"] = "";
        $cabecera["total_incluido_percepcion"] = "";
        $cabecera["detraccion"] = "false";
        $cabecera["observaciones"] = "";
        $cabecera["documento_que_se_modifica_tipo"] = "";
        $cabecera["documento_que_se_modifica_serie"] = "";
        $cabecera["documento_que_se_modifica_numero"] = "";
        $cabecera["tipo_de_nota_de_credito"] = "";
        $cabecera["tipo_de_nota_de_debito"] = "";
        $cabecera["enviar_automaticamente_a_la_sunat"] = "true";
        if($email === ""){
            $cabecera["enviar_automaticamente_al_cliente"] = "false";
        }else{
            $cabecera["enviar_automaticamente_al_cliente"] = "true";
        }   
        $cabecera["codigo_unico"] = "";
        $cabecera["condiciones_de_pago"] = "contado";
        $cabecera["medio_de_pago"] = $data_cabecera["nombreTarjeta"];
        $cabecera["placa_vehiculo"] = "";
        $cabecera["orden_compra_servicio"] = "";
        $cabecera["tabla_personalizada_codigo"] = "";
        $cabecera["formato_de_pdf"] = "TICKET";
        
        //Array de items para enviar a nubeFUCK
        $items = array();
        
        //Obtenemos items de comprobante
        $query_detalles = "Select dp.pkPediido, dp.pkDetallePedido, dp.pkPlato, pl.descripcion, dp.cantidad, dp.precio, pc.id_codigo_sunat, pc.id_tipo_impuesto, pc.tipo_articulo from detalle_comprobante2 dc, detallepedido dp, plato pl, plato_codigo_sunat pc where dc.pkDetalleComprobante = '".$idComprobante."' AND dc.pkDetallePedido = dp.pkDetallePedido AND dp.pkPlato = pl.pkPlato AND pc.id_plato = pl.pkPlato";
        
        $r_items = $db->executeQuery($query_detalles);
        while($rwd = $db->fecth_array($r_items)){        
            $item = array();
            if(intval($rwd["tipo_articulo"]) === 1){
                $item["unidad_de_medida"] = "NIU";
            }else{
                $item["unidad_de_medida"] = "ZZ";
            }

            $valor_unitario = 0;
            $igv_unitario = 0;
            $precio_unitario = 0;
            $impuesto_bolsa = 0;
            $bolsa_gratis = 0;

            $tipo_impuesto = 0;

            switch(intval($rwd["id_tipo_impuesto"])){
                case 1:
                    $valor_unitario = (floatval($rwd["precio"])/1.18);
                    $igv_unitario = floatval($rwd["precio"]) - $valor_unitario;
                    $precio_unitario = round(floatval($rwd["precio"]),2);
                    // $valor_unitario = ($valor_unitario;

                    $total_gravada = $total_gravada + ($valor_unitario*floatval($rwd["cantidad"]));
                    $total_venta = $total_venta + (floatval($rwd["precio"])*floatval($rwd["cantidad"]));
                    $total_igv = $total_igv + ($igv_unitario*floatval($rwd["cantidad"]));

                    $tipo_impuesto = 1;
                break;

                case 2:
                    $valor_unitario = round(floatval($rwd["precio"]),2);
                    $igv_unitario = 0;
                    $precio_unitario = round(floatval($rwd["precio"]),2);

                    $total_inafecta = $total_inafecta + ($valor_unitario*floatval($rwd["cantidad"]));
                    $total_venta = $total_venta + ($precio_unitario*floatval($rwd["cantidad"]));

                    $tipo_impuesto = 9;
                break;

                case 3:
                    $valor_unitario = round(floatval($rwd["precio"]),2);
                    $igv_unitario = 0;
                    $precio_unitario = round(floatval($rwd["precio"]),2);

                    $total_exonerada = $total_exonerada + ($valor_unitario*floatval($rwd["cantidad"]));
                    $total_venta = $total_venta + ($precio_unitario*floatval($rwd["cantidad"]));

                    $tipo_impuesto = 8;
                break;

                case 4:
                    $valor_unitario = round(floatval($rwd["precio"]),2);
                    $igv_unitario = 0;
                    $precio_unitario = round(floatval($rwd["precio"]),2);

                    $total_gratuita = $total_gratuita + ($valor_unitario*floatval($rwd["cantidad"]));

                    $tipo_impuesto = 6;
                break;

                case 5:
                    //Verificamos si el monto despues del impuesto es mayor a cero
                    $precio_sin_impuesto = round(floatval($rwd["precio"]),2) - $monto_icbper;
                    if($precio_sin_impuesto > 0){
                        $valor_unitario = round(($precio_sin_impuesto/1.18),2);
                        $igv_unitario = $precio_sin_impuesto - $valor_unitario;
                        $precio_unitario = $precio_sin_impuesto;

                        $total_gravada = $total_gravada + ($valor_unitario*floatval($rwd["cantidad"]));
                        $total_venta = $total_venta + ($precio_unitario*floatval($rwd["cantidad"]));
                        $total_igv = $total_igv + ($igv_unitario*floatval($rwd["cantidad"]));

                        $impuesto_bolsa = $monto_icbper*floatval($rwd["cantidad"]);

                        $total_impuestos_bolsas = $total_impuestos_bolsas + ($monto_icbper*floatval($rwd["cantidad"]));

                        $total_venta = $total_venta + ($precio_unitario*floatval($rwd["cantidad"]));

                        $tipo_impuesto = 1;
                    }else{
                        $bolsa_gratis = 1;

                        $total_impuestos_bolsas = $total_impuestos_bolsas + ($monto_icbper*floatval($rwd["cantidad"]));

                        $total_venta = $total_venta + ($monto_icbper*floatval($rwd["cantidad"]));
                    }
                break;
            }

            if($bolsa_gratis === 0){
                $item["codigo"] = $rwd["pkPlato"];
                $item["descripcion"] = $rwd["descripcion"];
                $item["cantidad"] = $rwd["cantidad"];

                $item["valor_unitario"] = $valor_unitario;
                $item["precio_unitario"] = $precio_unitario;
                $item["descuento"] = "";

                $item["subtotal"] = $valor_unitario*floatval($rwd["cantidad"]);
                $item["tipo_de_igv"] = $tipo_impuesto;
                $item["igv"] =  $igv_unitario*floatval($rwd["cantidad"]);
                $item["total"] = $precio_unitario*floatval($rwd["cantidad"]);
                $item["anticipo_regularizacion"] = "false";
                $item["anticipo_documento_serie"] = "";
                $item["anticipo_documento_numero"] = "";
                
                $item["codigo_producto_sunat"] = $rwd["id_codigo_sunat"];

                if($impuesto_bolsa > 0){
                    $item["impuesto_bolsas"] = $impuesto_bolsa;
                }
                
                $items[] = $item;
            }
        }
        
        //Ahora revisamos detalles truchos
        $query_ex = "Select cf.id, cf.cantidad_cambio, cf.precio_cambio, cf.pk_plato_cambio, p.descripcion, pc.id_codigo_sunat, pc.id_tipo_impuesto, pc.tipo_articulo from cambio_facturacion cf, plato p, plato_codigo_sunat pc where cf.pk_plato_cambio = p.pkPlato AND cf.pk_pedido_destino = '".$id_pedido."' AND pc.id_plato = cf.pk_plato_cambio";
        $result_ex = $db->executeQuery($query_ex);
        while($row_ex = $db->fecth_array($result_ex)){
            $item = array();
            if(intval($row_ex["tipo_articulo"]) === 1){
                $item["unidad_de_medida"] = "NIU";
            }else{
                $item["unidad_de_medida"] = "ZZ";
            }

            $valor_unitario = 0;
            $igv_unitario = 0;
            $precio_unitario = 0;
            $impuesto_bolsa = 0;
            $bolsa_gratis = 0;

            $tipo_impuesto = 0;

            switch(intval($row_ex["id_tipo_impuesto"])){
                case 1:
                    $valor_unitario = round((floatval($row_ex["precio_cambio"])/1.18),2);
                    $igv_unitario = floatval($row_ex["precio_cambio"]) - $valor_unitario;
                    // $precio_unitario = round(floatval($row_ex["precio_cambio"]),2);

                    $total_gravada = $total_gravada + ($valor_unitario*floatval($row_ex["cantidad_cambio"]));
                    $total_venta = $total_venta + ($precio_unitario*floatval($row_ex["cantidad_cambio"]));
                    $total_igv = $total_igv + ($igv_unitario*floatval($row_ex["cantidad_cambio"]));

                    $tipo_impuesto = 1;
                break;

                case 2:
                    $valor_unitario = round(floatval($row_ex["precio_cambio"]),2);
                    $igv_unitario = 0;
                    $precio_unitario = round(floatval($row_ex["precio_cambio"]),2);

                    $total_inafecta = $total_inafecta + ($valor_unitario*floatval($row_ex["cantidad_cambio"]));
                    $total_venta = $total_venta + ($precio_unitario*floatval($row_ex["cantidad_cambio"]));

                    $tipo_impuesto = 9;
                break;

                case 3:
                    $valor_unitario = round(floatval($row_ex["precio_cambio"]),2);
                    $igv_unitario = 0;
                    $precio_unitario = round(floatval($row_ex["precio_cambio"]),2);

                    $total_exonerada = $total_exonerada + ($valor_unitario*floatval($row_ex["cantidad_cambio"]));
                    $total_venta = $total_venta + ($precio_unitario*floatval($row_ex["cantidad_cambio"]));

                    $tipo_impuesto = 8;
                break;

                case 4:
                    $valor_unitario = round(floatval($row_ex["precio_cambio"]),2);
                    $igv_unitario = 0;
                    $precio_unitario = round(floatval($row_ex["precio_cambio"]),2);

                    $total_gratuita = $total_gratuita + ($valor_unitario*floatval($row_ex["cantidad_cambio"]));

                    $tipo_impuesto = 6;
                break;

                case 5:
                    //Verificamos si el monto despues del impuesto es mayor a cero
                    $precio_sin_impuesto = round(floatval($row_ex["precio_cambio"]),2) - $monto_icbper;
                    if($precio_sin_impuesto > 0){
                        $valor_unitario = round(($precio_sin_impuesto/1.18),2);
                        $igv_unitario = $precio_sin_impuesto - $valor_unitario;
                        $precio_unitario = $precio_sin_impuesto;

                        $total_gravada = $total_gravada + ($valor_unitario*floatval($row_ex["cantidad_cambio"]));
                        $total_venta = $total_venta + ($precio_unitario*floatval($row_ex["cantidad_cambio"]));
                        $total_igv = $total_igv + ($igv_unitario*floatval($row_ex["cantidad_cambio"]));

                        $impuesto_bolsa = $monto_icbper*floatval($row_ex["cantidad_cambio"]);

                        $total_impuestos_bolsas = $total_impuestos_bolsas + ($monto_icbper*floatval($row_ex["cantidad_cambio"]));

                        $total_venta = $total_venta + ($precio_unitario*floatval($row_ex["cantidad_cambio"]));

                        $tipo_impuesto = 1;
                    }else{
                        $bolsa_gratis = 1;

                        $total_impuestos_bolsas = $total_impuestos_bolsas + ($monto_icbper*floatval($row_ex["cantidad_cambio"]));

                        $total_venta = $total_venta + ($monto_icbper*floatval($row_ex["cantidad_cambio"]));
                    }
                break;
            }

            if($bolsa_gratis === 0){
                $item["codigo"] = $row_ex["pk_plato_cambio"];
                $item["descripcion"] = $row_ex["descripcion"];
                $item["cantidad"] = $row_ex["cantidad_cambio"];

                $item["valor_unitario"] = $valor_unitario;
                $item["precio_unitario"] = $precio_unitario;
                $item["descuento"] = "";

                $item["subtotal"] = $valor_unitario*floatval($row_ex["cantidad_cambio"]);
                $item["tipo_de_igv"] = $tipo_impuesto;
                $item["igv"] =  $igv_unitario*floatval($row_ex["cantidad_cambio"]);
                $item["total"] = $precio_unitario*floatval($row_ex["cantidad_cambio"]);
                $item["anticipo_regularizacion"] = "false";
                $item["anticipo_documento_serie"] = "";
                $item["anticipo_documento_numero"] = "";
                
                $item["codigo_producto_sunat"] = $row_ex["id_codigo_sunat"];

                if($impuesto_bolsa > 0){
                    $item["impuesto_bolsas"] = $impuesto_bolsa;
                }
                
                $items[] = $item;
            }
        }

        //Finalmente buscamos si el comprobante es directo
        $query_directo = "Select dc.*, p.descripcion, pc.id_codigo_sunat, pc.id_tipo_impuesto, pc.tipo_articulo from detalle_comprobante_directo dc, plato p, plato_codigo_sunat pc where dc.id_comprobante = '".$idComprobante."' AND dc.id_plato = p.pkPlato AND pc.id_plato = dc.id_plato";
        $result_directo = $db->executeQuery($query_directo);
        while($row_ex = $db->fecth_array($result_directo)){
            $item = array();
            if(intval($row_ex["tipo_articulo"]) === 1){
                $item["unidad_de_medida"] = "NIU";
            }else{
                $item["unidad_de_medida"] = "ZZ";
            }

            $valor_unitario = 0;
            $igv_unitario = 0;
            $precio_unitario = 0;
            $impuesto_bolsa = 0;
            $bolsa_gratis = 0;

            $tipo_impuesto = 0;

            switch(intval($row_ex["id_tipo_impuesto"])){
                case 1:
                    $valor_unitario = round((floatval($row_ex["precio_unitario"])/1.18),2);
                    $igv_unitario = floatval($row_ex["precio_unitario"]) - $valor_unitario;
                    $precio_unitario = round(floatval($row_ex["precio_unitario"]),2);

                    $total_gravada = $total_gravada + ($valor_unitario*floatval($row_ex["cantidad"]));
                    $total_venta = $total_venta + ($precio_unitario*floatval($row_ex["cantidad"]));
                    $total_igv = $total_igv + ($igv_unitario*floatval($row_ex["cantidad"]));

                    $tipo_impuesto = 1;
                break;

                case 2:
                    $valor_unitario = round(floatval($row_ex["precio_unitario"]),2);
                    $igv_unitario = 0;
                    $precio_unitario = round(floatval($row_ex["precio_unitario"]),2);

                    $total_inafecta = $total_inafecta + ($valor_unitario*floatval($row_ex["cantidad"]));
                    $total_venta = $total_venta + ($precio_unitario*floatval($row_ex["cantidad"]));

                    $tipo_impuesto = 9;
                break;

                case 3:
                    $valor_unitario = round(floatval($row_ex["precio_unitario"]),2);
                    $igv_unitario = 0;
                    $precio_unitario = round(floatval($row_ex["precio_unitario"]),2);

                    $total_exonerada = $total_exonerada + ($valor_unitario*floatval($row_ex["cantidad"]));
                    $total_venta = $total_venta + ($precio_unitario*floatval($row_ex["cantidad"]));

                    $tipo_impuesto = 8;
                break;

                case 4:
                    $valor_unitario = round(floatval($row_ex["precio_unitario"]),2);
                    $igv_unitario = 0;
                    $precio_unitario = round(floatval($row_ex["precio_unitario"]),2);

                    $total_gratuita = $total_gratuita + ($valor_unitario*floatval($row_ex["cantidad"]));

                    $tipo_impuesto = 6;
                break;

                case 5:
                    //Verificamos si el monto despues del impuesto es mayor a cero
                    $precio_sin_impuesto = round(floatval($row_ex["precio_unitario"]),2) - $monto_icbper;
                    if($precio_sin_impuesto > 0){
                        $valor_unitario = round(($precio_sin_impuesto/1.18),2);
                        $igv_unitario = $precio_sin_impuesto - $valor_unitario;
                        $precio_unitario = $precio_sin_impuesto;

                        $total_gravada = $total_gravada + ($valor_unitario*floatval($row_ex["cantidad"]));
                        $total_venta = $total_venta + ($precio_unitario*floatval($row_ex["cantidad"]));
                        $total_igv = $total_igv + ($igv_unitario*floatval($row_ex["cantidad"]));

                        $impuesto_bolsa = $monto_icbper*floatval($row_ex["cantidad"]);

                        $total_impuestos_bolsas = $total_impuestos_bolsas + ($monto_icbper*floatval($row_ex["cantidad"]));

                        $total_venta = $total_venta + ($precio_unitario*floatval($row_ex["cantidad"]));

                        $tipo_impuesto = 1;
                    }else{
                        $bolsa_gratis = 1;

                        $total_impuestos_bolsas = $total_impuestos_bolsas + ($monto_icbper*floatval($row_ex["cantidad"]));

                        $total_venta = $total_venta + ($monto_icbper*floatval($row_ex["cantidad"]));
                    }
                break;
            }

            if($bolsa_gratis === 0){
                $item["codigo"] = $row_ex["id_plato"];
                $item["descripcion"] = $row_ex["descripcion"];
                $item["cantidad"] = $row_ex["cantidad"];

                $item["valor_unitario"] = $valor_unitario;
                $item["precio_unitario"] = $precio_unitario;
                $item["descuento"] = "";

                $item["subtotal"] = $valor_unitario*floatval($row_ex["cantidad"]);
                $item["tipo_de_igv"] = $tipo_impuesto;
                $item["igv"] =  $igv_unitario*floatval($row_ex["cantidad"]);
                $item["total"] = $precio_unitario*floatval($row_ex["cantidad"]);
                $item["anticipo_regularizacion"] = "false";
                $item["anticipo_documento_serie"] = "";
                $item["anticipo_documento_numero"] = "";
                
                $item["codigo_producto_sunat"] = $row_ex["id_codigo_sunat"];

                if($impuesto_bolsa > 0){
                    $item["impuesto_bolsas"] = $impuesto_bolsa;
                }
                
                $items[] = $item;
            }
        }

        //Validamos si se va a facturar la propina
        $query_ipropina = "Select subTotal from pedido where pkPediido = '".$id_pedido."'";
        $rp = $db->executeQuery($query_ipropina);
        if ($row = $db->fecth_array($rp)){
            $fpropina = intval($row["subTotal"]);

            if($fpropina > 0){
                //obtenemos total de propina
                $monto_propina = 0;
                $query_tp = "Select sum(monto) as propina from pedido_propina where pkPediido = '".$id_pedido."'";
                $tp = $db->executeQuery($query_tp);
                if ($rowp = $db->fecth_array($tp)){
                    $monto_propina = floatval($rowp["propina"]);
                }

                if($monto_propina > 0){
                    $valor_unitario = round(($monto_propina/1.18),2);
                    $igv_unitario = $monto_propina - $valor_unitario;
                    $precio_unitario = round($monto_propina,2);

                    $total_gravada = $total_gravada + $valor_unitario;
                    $total_venta = $total_venta + $precio_unitario;
                    $total_igv = $total_igv + $igv_unitario;

                    $tipo_impuesto = 1;

                    //Agregamos a items
                    $item = array();
                    $item["unidad_de_medida"] = "ZZ";

                    $item["codigo"] = 'SERU';
                    $item["descripcion"] = 'CARGO POR SERVICIO';
                    $item["cantidad"] = 1;

                    $item["valor_unitario"] = $valor_unitario;
                    $item["precio_unitario"] = $precio_unitario;
                    $item["descuento"] = "";

                    $item["subtotal"] = $valor_unitario;
                    $item["tipo_de_igv"] = $tipo_impuesto;
                    $item["igv"] =  $igv_unitario;
                    $item["total"] = $precio_unitario;
                    $item["anticipo_regularizacion"] = "false";
                    $item["anticipo_documento_serie"] = "";
                    $item["anticipo_documento_numero"] = "";
                    
                    $item["codigo_producto_sunat"] = '90101600';

                    $items[] = $item;
                }
            }
        }
        
        //Validamos si es por consumo
        $consumo = 1;
        if(isset($_REQUEST["consumo"])){
            $consumo = intval($_REQUEST["consumo"]);
        }

        //Calculamos descuento con y sin igv
        $descuento_porcentaje = 0;
        if(isset($_REQUEST["descuento_porcentaje"])){
            $descuento_porcentaje = floatval($_REQUEST["descuento_porcentaje"]);
        }

        //Si es por consumo ignoramos totales
        if($consumo === 2){
            $items = null;
            //Si es por consumo
            $item = array();
            $item["unidad_de_medida"] = "NIU";
            $item["codigo"] = "[CON]";
            $item["descripcion"] = "POR CONSUMO";
            $item["cantidad"] = 1;

            $precio_unitario = $total_venta;
            $valor_unitario = round($precio_unitario/1.18,2);
            $igv_unitario = $precio_unitario-$valor_unitario;

            $total_gravada = $valor_unitario;
            $total_igv = $igv_unitario;

            $item["valor_unitario"] = $valor_unitario;
            $item["precio_unitario"] = $precio_unitario;
            $item["descuento"] = "";

            $item["subtotal"] = $valor_unitario;
            $item["tipo_de_igv"] = "1";
            $item["igv"] =  $igv_unitario;
            $item["total"] = $precio_unitario;
            $item["anticipo_regularizacion"] = "false";
            $item["anticipo_documento_serie"] = "";
            $item["anticipo_documento_numero"] = "";
            
            $item["codigo_producto_sunat"] = 50192701;

            $items[0] = $item;

            if($descuento_porcentaje>0){
                $precio_descuento = round((($precio_unitario*$descuento_porcentaje)/100),2);
                $valor_descuento = round((($valor_unitario*$descuento_porcentaje)/100),2);
                $total_descuentos = $valor_descuento;

                $total_gravada = $total_gravada - $valor_descuento;
                $total_venta = $total_venta - $precio_descuento;

                $nuevo_igv = $total_venta - $total_gravada;
                $total_igv = $nuevo_igv;

                $cabecera["descuento_global"] = $valor_descuento;
                $cabecera["total_descuento"] = $valor_descuento;
            }else{
                $cabecera["descuento_global"] = "";
                $cabecera["total_descuento"] = "";
            }

            //Modificamos cabecera
            $cabecera["total_gravada"] = $total_gravada;
            $cabecera["total_inafecta"] = "";
            $total_inafecta = 0;
            $cabecera["total_exonerada"] = "";
            $total_exonerada = 0;
            $cabecera["total_gratuita"] = "";
            $total_gratuita = 0;
            $total_impuestos_bolsas = 0;

            $cabecera["total_igv"] = $total_igv;
            $cabecera["total"] = $total_venta;
        }else{
            //Vemos donde hacemos el descuento
            if($descuento_porcentaje>0){

                //Revisamos los totales
                if($total_gravada>0){
                    //Revisar esto luego
                    //Se deberia mandar desde la vista el porcentaje separado del impuesto de la bolsa
                    $monto_descuento = (($total_gravada+$total_impuestos_bolsas)*$descuento_porcentaje)/100;
                    $total_gravada = $total_gravada - $monto_descuento;
                    $total_venta = $total_venta - ($monto_descuento*1.18);
                    $total_descuentos = $total_descuentos + $monto_descuento;

                    $nuevo_igv = $total_venta - $total_impuestos_bolsas - $total_gravada;
                    $total_igv = $nuevo_igv;
                }

                if($total_inafecta>0){
                    $monto_descuento = ($total_inafecta*$descuento_porcentaje)/100;
                    $total_inafecta = $total_inafecta - $monto_descuento;
                    $total_venta = $total_venta - $monto_descuento;
                    $total_descuentos = $total_descuentos + $monto_descuento;
                }

                if($total_exonerada>0){
                    $monto_descuento = ($total_exonerada*$descuento_porcentaje)/100;
                    $total_exonerada = $total_exonerada - $monto_descuento;
                    $total_venta = $total_venta - $monto_descuento;
                    $total_descuentos = $total_descuentos + $monto_descuento;
                }

                $cabecera["descuento_global"] = $total_descuentos;
                $cabecera["total_descuento"] = $total_descuentos;

            }else{
                $cabecera["descuento_global"] = "";
                $cabecera["total_descuento"] = "";
            }

            //Revisamos los totales
            if($total_gravada>0){
                $cabecera["total_gravada"] = $total_gravada;
            }else{
                $cabecera["total_gravada"] = "";
            }

            if($total_inafecta>0){
                $cabecera["total_inafecta"] = $total_inafecta;
            }else{
                $cabecera["total_inafecta"] = "";
            }

            if($total_exonerada>0){
                $cabecera["total_exonerada"] = $total_exonerada;
            }else{
                $cabecera["total_exonerada"] = "";
            }

            if($total_gratuita>0){
                $cabecera["total_gratuita"] = $total_gratuita;
            }else{
                $cabecera["total_gratuita"] = "";
            }

            if($total_impuestos_bolsas>0){
                $cabecera["total_impuestos_bolsas"] = $total_impuestos_bolsas;
            }

            $cabecera["total_igv"] = $total_igv;
            $cabecera["total"] = $total_venta;
        }
        
        // $isDetraccion = "false";
        if (isset($_REQUEST['total_detraccion'])) {

            if ($_REQUEST['total_detraccion'] > 0) {
                $cabecera["sunat_transaction"] = "30";
                $cabecera["detraccion"] = "true";

                $query_detraccion = "Select * from cloud_config where parametro = 'porcentaje_detraccion'"; 
                $res_detraccion = $db->executeQueryEx($query_detraccion);
                if ($row_detraccion = $db->fecth_array($res_detraccion)) {
                    $query_detraccion2 = "select * from porcentaje_detraccion where id = ${row_detraccion['valor']}";
                    $res_detraccion2 = $db->executeQueryEx($query_detraccion2);
                    if ($row_detraccion2 = $db->fecth_array($res_detraccion2)) {
                        
                        $cabecera["detraccion_tipo"] = $row_detraccion2['id'];
                        $cabecera["detraccion_porcentaje"] = floatval($row_detraccion2['porcentaje']);
                        $cabecera["detraccion_total"] = floatval($row_detraccion2['porcentaje'] * $total_venta / 100);
                    }
                }


            }
        }

        //Agregamos los items
        $cabecera["items"] = $items;
        
        $data_json = json_encode($cabecera);
        
        // echo $data_json;

        //Actualizamos impuestos
        $query_impuestos = "Insert into comprobante_impuestos values(NULL,'".$idComprobante."','".$total_gravada."','".$total_igv."','".$total_inafecta."','".$total_exonerada."','".$total_descuentos."', '".$total_gratuita."', '".$total_impuestos_bolsas."','".$total_venta."')";

        $db->executeQuery($query_impuestos);
        
        //Obtenemos Llaves de NUBEFACT
        $ruta = NULL;
        $cX = "Select * from cloud_config where parametro = 'rutapce'";
        $sX = $db->executeQuery($cX);
        if ($row = $db->fecth_array($sX)){
            $ruta = $row["valor"];
        }
        
        $token = NULL;
        $cY = "Select * from cloud_config where parametro = 'tokenpce'";
        $sY = $db->executeQuery($cY);
        if ($row = $db->fecth_array($sY)){
            $token = $row["valor"];
        }
        
        //Invocamos el servicio de NUBEFACT
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $ruta);
        curl_setopt(
                $ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Token token="'.$token.'"',
                'Content-Type: application/json',
                )
        );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 90);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $respuesta  = curl_exec($ch);
        $array_respuesta = array();
        $array_respuesta["exito"] = 1;
        $array_respuesta["mensaje"] = "";
        $array_respuesta["id_comprobante"] = $idComprobante;
        if(intval(curl_errno($ch)) === 0){
            curl_close($ch);
            //Verificamos respuesta
            //print_r($respuesta);
            $leer_respuesta = json_decode($respuesta, true);
            if (isset($leer_respuesta['errors'])) {
                //Validamos error 23
                if(intval($leer_respuesta["codigo"]) === 23){
                    //Si el comprobante ya existe realizamos otro proceso
                    $this->ConsultaComprobanteExistente($cabecera["tipo_de_comprobante"],$cabecera["serie"],$cabecera["numero"],$cabecera["cliente_numero_de_documento"],$cabecera["total"],$idComprobante,$accion,$id_pedido,$extras_parcial);
                }else{
                    //Si hay un error eliminamos el comprobante y sus detalles
                    $this->elimina_comprobante($idComprobante,$id_pedido);
                    //Si es accion 2 eliminamos el nuevo pedido
                    if(intval($accion) === 2){
                        $query_eliminacion = "delete from pedido where pkPediido = '".$id_pedido."'";
                        $db->executeQueryEx($query_eliminacion);
                        $this->RegresaDetallesAPedidoOriginal($extras_parcial['platos'], $extras_parcial['pedido_original']);
                    }
                    //Mostramos errores
                    $array_respuesta["exito"] = 0;
                    $array_respuesta["mensaje"] = $leer_respuesta['errors'];
                    echo json_encode($array_respuesta);
                }
            } else {
                if(isset($leer_respuesta["codigo_hash"])){
                    $aceptada = "SI";
                    $qr = "Insert into comprobante_hash values('".$idComprobante."','".$aceptada."','".$leer_respuesta["codigo_hash"]."','".$leer_respuesta["cadena_para_codigo_qr"]."','".$leer_respuesta['sunat_description']."')";
                    $db->executeQuery($qr);
                    
                    // $qr1 = "update comprobante set ncomprobante = {$leer_respuesta["numero"]}, serie = '{$leer_respuesta["serie"]}' where pkComprobante = $idComprobante";
                    // $db->executeQuery($qr1);

                    // $data_cabecera["ncomprobante"] = $leer_respuesta["numero"];

                    //Insertamos accion caja
                    if(intval($data_cabecera["pkTipoComprobante"]) == 1){
                        $query_caja = "Insert into accion_caja values(NULL,'".$data_cabecera["ncomprobante"]."','BOL','".$_COOKIE["c"]."')";
                        $db->executeQuery($query_caja);
                    }else{
                        $query_caja = "Insert into accion_caja values(NULL,'".$data_cabecera["ncomprobante"]."','FAC','".$_COOKIE["c"]."')";
                        $db->executeQuery($query_caja);
                    }

                    //Si es accion 1 o 2 cerramos pedido
                    if(intval($accion) > 0){
                        //Obtenemos Fecha de cierre
                        $query_cierre = "Select fecha from cierrediario where pkCierreDiario = 1";
                        $rc = $db->executeQuery($query_cierre);
                        $fecha_cierre_c = null;
                        if ($row = $db->fecth_array($rc)){
                            $fecha_cierre_c = $row["fecha"];
                        }

                        //Cancelamos cuenta 
                        $query_cancelar = "update pedido set dateModify=now(), fechaFin=now(), estado=1, fechaCierre='".$fecha_cierre_c."' where pkPediido = '".$id_pedido."'";
                        $db->executeQuery($query_cancelar);

                        //Solo si es accion 1 liberamos mesa
                        if(intval($accion) === 1){
                            $query_mesa = "update mesas set estado = 0 where pkMesa = '".$_REQUEST["mesa"]."'";
                            $db->executeQuery($query_mesa);
                        }

                        //Si es accion 2 actualizamos los detalles de ese pedido
                        if(intval($accion) === 2){
                            $db->executeQuery("SET SQL_SAFE_UPDATES = 0;");
                            $array_platos = json_decode($_POST['productos'], true);
                            for ($i = 0; $i < count($array_platos); $i++) {
                                //Recorremos array enviado
                                $pos = strpos($array_platos[$i]['pkPedido'], "C");
                                if ($pos === false) {
                                    //Si es detalle normal
                                    $query_cambio = "Update detallepedido set pkPediido = '".$id_pedido."' where pkDetallePedido = '".$array_platos[$i]['pkPedido']."'";
                                    $db->executeQuery($query_cambio);
                                }else{
                                    //Si es detalle cambiado
                                    //Actualizamos cambio facturacion
                                    // $id_cambio = substr($array_platos[$i]['pkPedido'],1);
                                    // $query_cambio_c = "Update cambio_facturacion set pk_pedido_destino = '".$id_pedido."' where id = '".$id_cambio."'";
                                    // $db->executeQuery($query_cambio_c);
                                    // //Obtenemos id detalle original
                                    // $query_detalle_original = "Select pk_detalle from cambio_facturacion where id = '".$id_cambio."'";
                                    // $id_detalle_original = null;
                                    // $rdet = $db->executeQuery($query_detalle_original);
                                    // if ($rowd = $db->fecth_array($rdet)){
                                    //     $id_detalle_original = $rowd["pk_detalle"];
                                    // }
                                    // //Actualizamos detalle original
                                    // $query_cambio = "Update detallepedido set pkPediido = '".$id_pedido."' where pkDetallePedido = '".$id_detalle_original."'";
                                    // $db->executeQuery($query_cambio);
                                }
                            }
                        }
                        
                        //Insertamos el pedido en que caja fue hecho
                        $query_caja = "Insert into accion_caja values(NULL,'".$id_pedido."','PED','".$_COOKIE["c"]."')";
                        $db->executeQuery($query_caja);
                    }

                    //Mostramos Respuesta
                    $array_respuesta["mensaje"] = $leer_respuesta['sunat_description'];
                    echo json_encode($array_respuesta);
                }else{
                    if($this->contador_repeticion === 0){
                        $this->contador_repeticion = $this->contador_repeticion + 1;
                        $this->generaElectronica($idComprobante,$id_pedido,$accion);
                    }else{
                        //Si la pagina no responde como deberia tambien eliminamos
                        $this->elimina_comprobante($idComprobante,$id_pedido);
                        //Si es accion 2 eliminamos el nuevo pedido
                        if(intval($accion) === 2){
                            $query_eliminacion = "delete from pedido where pkPediido = '".$id_pedido."'";
                            $db->executeQueryEx($query_eliminacion);
                            $this->RegresaDetallesAPedidoOriginal($extras_parcial['platos'], $extras_parcial['pedido_original']);
                        }
                        //Mostramos errores
                        $array_respuesta["exito"] = 0;
                        $array_respuesta["mensaje"] = isset($leer_respuesta['message']) ? $leer_respuesta['message'] : "El OSE tardo mucho en responder, reintente";
                        $contador_repeticion = 0;
                        echo json_encode($array_respuesta);
                    }
                }
            }       
        }else{
            curl_close($ch);
            //Eliminamos el comprobante
            $this->elimina_comprobante($idComprobante,$id_pedido);
            //Si es accion 2 eliminamos el nuevo pedido
            if(intval($accion) === 2){
                $query_eliminacion = "delete from pedido where pkPediido = '".$id_pedido."'";
                $db->executeQueryEx($query_eliminacion);
                $this->RegresaDetallesAPedidoOriginal($extras_parcial['platos'], $extras_parcial['pedido_original']);
            }
            //Mostramos errores
            $array_respuesta["exito"] = 0;
            $array_respuesta["mensaje"] = "No se pudo establecer conexion con OSE, puede emitir un comprobante fisico de contingencia o canjear el ticket por un comprobante mas adelante";
            echo json_encode($array_respuesta);
        }        
        
    }

    private function RegresaDetallesAPedidoOriginal($array_platos, $id_pedido)
    {
        $db = new SuperDataBase();
        //Si es pago parcial actualizamos los detalles enviados
        // if($parcial === 1){
            $db->executeQuery("SET SQL_SAFE_UPDATES = 0;");
            // $array_platos = json_decode($_POST['productos'], true);
            for ($i = 0; $i < count($array_platos); $i++) {
                //Recorremos array enviado
                $pos = strpos($array_platos[$i]['pkPedido'], "C");
                if ($pos === false) {
                    //Si es detalle normal
                    $query_cambio = "Update detallepedido set pkPediido = '".$id_pedido."' where pkDetallePedido = '".$array_platos[$i]['pkPedido']."'";
                    $db->executeQueryEx($query_cambio);
                }else{
                    // //Si es detalle cambiado
                    // //Actualizamos cambio facturacion
                    // $id_cambio = substr($array_platos[$i]['pkPedido'],1);
                    // $query_cambio_c = "Update cambio_facturacion set pk_pedido_destino = '".$id_pedido."' where id = '".$id_cambio."'";
                    // $db->executeQueryEx($query_cambio_c);
                    // //Obtenemos id detalle original
                    // $query_detalle_original = "Select pk_detalle from cambio_facturacion where id = '".$id_cambio."'";
                    // $id_detalle_original = null;
                    // $rdet = $db->executeQueryEx($query_detalle_original);
                    // if ($rowd = $db->fecth_array($rdet)){
                    //     $id_detalle_original = $rowd["pk_detalle"];
                    // }
                    // //Actualizamos detalle original
                    // $query_cambio = "Update detallepedido set pkPediido = '".$id_pedido."' where pkDetallePedido = '".$id_detalle_original."'";
                    // $db->executeQueryEx($query_cambio);
                }
            }
        // }
    }

    //Validacion extra cuando nubefact responde que documento ya existe en PSE
    private function ConsultaComprobanteExistente($tipo,$serie,$numero,$documento,$total,$idComprobante,$accion,$id_pedido,$extras_parcial = null) {
        $db = new SuperDataBase();
        
        //Obtenemos Llaves de NUBEFACT
        $ruta = NULL;
        $cX = "Select * from cloud_config where parametro = 'rutapce'";
        $sX = $db->executeQuery($cX);
        if ($row = $db->fecth_array($sX)){
            $ruta = $row["valor"];
        }
        
        $token = NULL;
        $cY = "Select * from cloud_config where parametro = 'tokenpce'";
        $sY = $db->executeQuery($cY);
        if ($row = $db->fecth_array($sY)){
            $token = $row["valor"];
        }
        
        $consulta = array();
        
        $consulta["operacion"] = "consultar_comprobante";
        $consulta["tipo_de_comprobante"] = $tipo;
        $consulta["serie"] = $serie;      
        $consulta["numero"] = $numero;
        
        $data_json = json_encode($consulta);
        
        //Invocamos el servicio de NUBEFACT
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $ruta);
        curl_setopt(
                $ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Token token="'.$token.'"',
                'Content-Type: application/json',
                )
        );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 90);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $respuesta  = curl_exec($ch);
       
        $array_respuesta = array();
        $array_respuesta["exito"] = 1;
        $array_respuesta["mensaje"] = "";
        $array_respuesta["id_comprobante"] = $idComprobante;
        
        //Verificamos respuesta
        if(intval(curl_errno($ch)) === 0){
            curl_close($ch);
            $leer_respuesta = json_decode($respuesta, true);
            if (isset($leer_respuesta['errors'])) {
                //Eliminamos el comprobante
                $this->elimina_comprobante($idComprobante,$id_pedido);
                //Si es accion 2 eliminamos el nuevo pedido
                if(intval($accion) === 2){
                    $query_eliminacion = "delete from pedido where pkPediido = '".$id_pedido."'";
                    $db->executeQueryEx($query_eliminacion);
                    $this->RegresaDetallesAPedidoOriginal($extras_parcial['platos'], $extras_parcial['pedido_original']);
                }
                //Si no existe || Imposible pero cierto avisamos
                $array_respuesta["exito"] = 0;
                $array_respuesta["mensaje"] = "Error de comprobacion de correlativo, comunicarse con soporte";
                echo json_encode($array_respuesta);
            } else {
                $datos_factura = explode("|", $leer_respuesta["cadena_para_codigo_qr"]);
                if((floatval($documento) === floatval($datos_factura[8])) && (floatval($total) === floatval($datos_factura[5]))){
                    //Si es el mismo comprobante avanzamos
                    $qr = "Insert into comprobante_hash values('".$idComprobante."','SI','".$leer_respuesta["codigo_hash"]."','".$leer_respuesta["cadena_para_codigo_qr"]."','".$leer_respuesta['sunat_description']."')";
                    $db->executeQuery($qr);
                    //Insertamos accion caja
                    if(intval($tipo) == 2){
                        $query_caja = "Insert into accion_caja values(NULL,'".$numero."','BOL','".$_COOKIE["c"]."')";
                        $db->executeQuery($query_caja);
                    }else{
                        $query_caja = "Insert into accion_caja values(NULL,'".$numero."','FAC','".$_COOKIE["c"]."')";
                        $db->executeQuery($query_caja);
                    }
                    
                    if(intval($accion) > 0){
                        //Obtenemos Fecha de cierre
                        $query_cierre = "Select fecha from cierrediario where pkCierreDiario = 1";
                        $rc = $db->executeQuery($query_cierre);
                        $fecha_cierre_c = null;
                        if ($row = $db->fecth_array($rc)){
                            $fecha_cierre_c = $row["fecha"];
                        }
                        //Cancelamos cuenta 
                        $query_cancelar = "update pedido set dateModify=now(), fechaFin=now(), estado=1, fechaCierre='".$fecha_cierre_c."' where pkPediido = '".$id_pedido."'";
                        $db->executeQuery($query_cancelar);

                        //Solo si es accion 1 liberamos mesa
                        if(intval($accion) === 1){
                            $query_mesa = "update mesas set estado = 0 where pkMesa = '".$_REQUEST["mesa"]."'";
                            $db->executeQuery($query_mesa);
                        }

                        //Si es accion 2 actualizamos los detalles de ese pedido
                        if(intval($accion) === 2){
                            $db->executeQuery("SET SQL_SAFE_UPDATES = 0;");
                            $array_platos = json_decode($_POST['productos'], true);
                            for ($i = 0; $i < count($array_platos); $i++) {
                                //Recorremos array enviado
                                $pos = strpos($array_platos[$i]['pkPedido'], "C");
                                if ($pos === false) {
                                    //Si es detalle normal
                                    $query_cambio = "Update detallepedido set pkPediido = '".$id_pedido."' where pkDetallePedido = '".$array_platos[$i]['pkPedido']."'";
                                    $db->executeQuery($query_cambio);
                                }else{
                                    //Si es detalle cambiado
                                    //Actualizamos cambio facturacion
                                    // $id_cambio = substr($array_platos[$i]['pkPedido'],1);
                                    // $query_cambio_c = "Update cambio_facturacion set pk_pedido_destino = '".$id_pedido."' where id = '".$id_cambio."'";
                                    // $db->executeQuery($query_cambio_c);
                                    // //Obtenemos id detalle original
                                    // $query_detalle_original = "Select pk_detalle from cambio_facturacion where id = '".$id_cambio."'";
                                    // $id_detalle_original = null;
                                    // $rdet = $db->executeQuery($query_detalle_original);
                                    // if ($rowd = $db->fecth_array($rdet)){
                                    //     $id_detalle_original = $rowd["pk_detalle"];
                                    // }
                                    // //Actualizamos detalle original
                                    // $query_cambio = "Update detallepedido set pkPediido = '".$id_pedido."' where pkDetallePedido = '".$id_detalle_original."'";
                                    // $db->executeQuery($query_cambio);
                                }
                            }
                        }

                        //Insertamos el pedido en que caja fue hecho
                        $query_caja = "Insert into accion_caja values(NULL,'".$id_pedido."','PED','".$_COOKIE["c"]."')";
                        $db->executeQuery($query_caja);
                    }

                    //Mostramos Respuesta
                    $array_respuesta["mensaje"] = $leer_respuesta['sunat_description'];
                    echo json_encode($array_respuesta);
                }else{
                    //Si no es sumamos uno y reintentamos
                    $query_suma = "Update comprobante set ncomprobante = ncomprobante+1 where pkComprobante = $idComprobante";
                    $db->executeQuery($query_suma);
                    $this->generaElectronica($idComprobante,$id_pedido,$accion);
                }
            }
        }else{
            curl_close($ch);
            //Eliminamos el comprobante
            $this->elimina_comprobante($idComprobante,$id_pedido);
            //Si es accion 2 eliminamos el nuevo pedido
            if(intval($accion) === 2){
                $query_eliminacion = "delete from pedido where pkPediido = '".$id_pedido."'";
                $db->executeQueryEx($query_eliminacion);
                $this->RegresaDetallesAPedidoOriginal($extras_parcial['platos'], $extras_parcial['pedido_original']);
            }
            //Si hay error de conexion avisamos
            $array_respuesta["exito"] = 0;
            $array_respuesta["mensaje"] = "Error de conexion en segunda comprobacion, comunicarse con soporte";
            echo json_encode($array_respuesta);
        }
    }

    private function _generarComprobanteDiferido() {
        //2019 Gino Lluen TUKA
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $db = new SuperDataBase();

                $total_venta = 0;
                $descuento = 0;
                $total_efectivo = 0;
                $total_tarjeta = 0;
                $tipo_pago = 0;
                $nombre_tarjeta = "";

                $query_pedido = "Select * from pedido where pkPediido = '".$_REQUEST["pkPediido"]."'";
                $rped = $db->executeQuery($query_pedido);
                if($row = $db->fecth_array($rped)){
                    $total_venta = $row["total"];
                    $descuento = $row["descuento"];
                    $total_efectivo = $row["total_efectivo"];
                    $total_tarjeta = $row["total_tarjeta"];
                    $tipo_pago = $row["tipo_pago"];
                    $nombre_tarjeta = $row["nombreTarjeta"];
                }
                              
                $correlativo = NULL;
                $dni = "";
                $ruc = "";
                $serie_comprobante = "";
                if(intval($_REQUEST["tipo_comprobante"]) == 1){
                    $c0 = "Select * from cloud_config where parametro = 'sboleta'";
                    $s0 = $db->executeQuery($c0);
                    if ($row = $db->fecth_array($s0)){
                        $serie_comprobante = $row["valor"];
                    }
                }else{
                    $c0 = "Select * from cloud_config where parametro = 'sfactura'";
                    $s0 = $db->executeQuery($c0);
                    if ($row = $db->fecth_array($s0)){
                        $serie_comprobante = $row["valor"];
                    }
                }
                
                //Operaciones segun tipo de comprobante
                if(intval($_REQUEST["tipo_comprobante"]) == 1){
                    //Actualizamos cliente
                    $dni = $_REQUEST['documento'];
                    if ($dni != "") {
                        $objPersona = new Application_Models_WorkPeopleModel();
                        $objPersona->_verficaPersona($dni, urlencode($_REQUEST['cliente']), urlencode($_REQUEST['direccion']), urlencode($_REQUEST['correo']));
                    }

                    //Obtenemos correlativo
                    $c1 = "Select max(ncomprobante) as actual from comprobante where pkTipoComprobante = 1";
                    $s1 = $db->executeQuery($c1);
                    if ($row = $db->fecth_array($s1)){
                        $correlativo = intval($row["actual"])+1;
                    }
                }else{
                    //Actualizamos cliente
                    $ruc = $_REQUEST['documento'];
                    if ($ruc != "") {
                        $objPersona = new Application_Models_WorkPeopleModel();
                        $objPersona->_verficaPersonaJuridica($ruc, urlencode($_REQUEST['cliente']), urlencode($_REQUEST['direccion']) ,$_REQUEST['correo']);
                    }

                    //Obtenemos correlativo
                    $c1 = "Select max(ncomprobante) as actual from comprobante where pkTipoComprobante = 2";
                    $s1 = $db->executeQuery($c1);
                    if ($row = $db->fecth_array($s1)){
                        $correlativo = intval($row["actual"])+1;
                    }
                }
                
                //Aqui insertar cabecera a DB
                   
                //Total Global
                $total_global = floatval($total_venta) +  floatval($descuento);
                //Sub Total
                $sub_total = round(($total_global/1.18),2);
                //Calculamos descuento antes de IGV
                $dsc_pre_igv = round((floatval($descuento)/1.18),2);
                //Sub total con descuento
                $sub_total_dsc = $sub_total - $dsc_pre_igv;
                //IGV
                $igv = floatval($total_venta) - $sub_total_dsc;
                //Total Final
                $total_final = floatval($total_venta);
                
                $usuario = UserLogin::get_id();

                //Insertamos Cabecera
                $query_cabecera = "Insert into comprobante values(NULL,'".$_REQUEST["tipo_comprobante"]."',0,0,'".$total_final."','".$sub_total_dsc."','".$igv."','".$tipo_pago."','".$total_efectivo."','".$total_tarjeta."','".$nombre_tarjeta."',now(),now(),'".$usuario."','".$dsc_pre_igv."','".$ruc."','".$usuario."',now(),'SU009','$serie_comprobante','".$correlativo."','".$ruc."','".$dni."')";
                $db->executeQuery($query_cabecera);
                
                //Obtenemos codigo generado                
                $serie_interna = $db->getId();
                         
                //Agregamos detalle
                $query_detalle_primario = "Insert into detallecomprobante values(NULL,'".$serie_interna."','".$_REQUEST['pkPediido']."','".$total_final."')";
                
                $db->executeQuery($query_detalle_primario);

                //agregamos detalle del comprobante
                //Obtenemos detalles del pedido
                $objModelComprobante = new Application_Models_ComprobanteModel();
                $query_detalle_pedido = "Select pkDetallePedido from detallepedido where pkPediido = '".$_REQUEST['pkPediido']."' AND estado = 1 AND precio > 0";

                $rdet = $db->executeQuery($query_detalle_pedido);
                while($row = $db->fecth_array($rdet)){
                    $objModelComprobante->addDetallePedidoComprobante($serie_interna, $row['pkDetallePedido']);
                }

                //Finalmente invocamos a FE
                $this->generaElectronica($serie_interna,$_REQUEST["pkPediido"],0);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _generarComprobantePersonalizado() {
        //2019 Gino Lluen TUKA
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $db = new SuperDataBase();
                $tipo_pago = 1;
                              
                $correlativo = NULL;
                $dni = "";
                $ruc = "";
                
                //Operaciones segun tipo de comprobante
                if(intval($_REQUEST["tipo_comprobante"]) == 1){
                    //Actualizamos cliente
                    $dni = $_REQUEST['documento'];
                    if ($dni != "") {
                        $objPersona = new Application_Models_WorkPeopleModel();
                        $objPersona->_verficaPersona($dni, urlencode($_REQUEST['cliente']), urlencode($_REQUEST['direccion']), urlencode($_REQUEST['correo']));
                    }

                    if (strlen($dni) == 11) {
                        $dni = '';
                        //Actualizamos cliente
                        $ruc = $_REQUEST['documento'];
                        if ($ruc != "") {
                            $objPersona = new Application_Models_WorkPeopleModel();
                            $objPersona->_verficaPersonaJuridica($ruc, urlencode($_REQUEST['cliente']), urlencode($_REQUEST['direccion']) ,$_REQUEST['correo']);
                        }
                    }

                    //Obtenemos correlativo
                    $c1 = "Select max(ncomprobante) as actual from comprobante where pkTipoComprobante = 1";
                    $s1 = $db->executeQuery($c1);
                    if ($row = $db->fecth_array($s1)){
                        $correlativo = intval($row["actual"])+1;
                    }
                }else{
                    //Actualizamos cliente
                    $ruc = $_REQUEST['documento'];
                    if ($ruc != "") {
                        $objPersona = new Application_Models_WorkPeopleModel();
                        $objPersona->_verficaPersonaJuridica($ruc, urlencode($_REQUEST['cliente']), urlencode($_REQUEST['direccion']) ,$_REQUEST['correo']);
                    }

                    //Obtenemos correlativo
                    $c1 = "Select max(ncomprobante) as actual from comprobante where pkTipoComprobante = 2";
                    $s1 = $db->executeQuery($c1);
                    if ($row = $db->fecth_array($s1)){
                        $correlativo = intval($row["actual"])+1;
                    }
                }
                
                //Aqui insertar cabecera a DB
                   
                $usuario = UserLogin::get_id();

                //series
                $serie_comprobante = "";
                if(intval($_REQUEST["tipo_comprobante"]) == 1){
                    $c0 = "Select * from cloud_config where parametro = 'sboleta'";
                    $s0 = $db->executeQuery($c0);
                    if ($row = $db->fecth_array($s0)){
                        $serie_comprobante = $row["valor"];
                    }
                }else{
                    $c0 = "Select * from cloud_config where parametro = 'sfactura'";
                    $s0 = $db->executeQuery($c0);
                    if ($row = $db->fecth_array($s0)){
                        $serie_comprobante = $row["valor"];
                    }
                }

                //Insertamos Cabecera
                $query_cabecera = "Insert into comprobante values(NULL,'".$_REQUEST["tipo_comprobante"]."',0,0,0,0,0,'".$tipo_pago."',0,0,'EFECTIVO',now(),now(),'".$usuario."',0,0,'".$usuario."',now(),'SU009','".$serie_comprobante."','".$correlativo."','".$ruc."','".$dni."')";
                //echo $query_cabecera;
                $db->executeQuery($query_cabecera);
                
                //Obtenemos codigo generado                
                $serie_interna = $db->getId();

                if (isset($_REQUEST['total_detraccion'])) {
                    if ($_REQUEST['total_detraccion'] > 0) {

                        $query_detraccion = "Select * from cloud_config where parametro = 'porcentaje_detraccion'"; 
                        $res_detraccion = $db->executeQueryEx($query_detraccion);
                        if ($row_detraccion = $db->fecth_array($res_detraccion)) {
                            $query_detraccion2 = "select * from porcentaje_detraccion where id = ${row_detraccion['valor']}";
                            $res_detraccion2 = $db->executeQueryEx($query_detraccion2);
                            if ($row_detraccion2 = $db->fecth_array($res_detraccion2)) {
                                
                                $codigo_detraccion = $row_detraccion2['codigo'];
                                $porcentaje_detraccion = floatval($row_detraccion2['porcentaje']);
                                $total_detraccion = floatval($row_detraccion2['porcentaje'] * $_REQUEST['total'] / 100);

                                if ($total_detraccion > 0) {

                                    $query_detra = "insert into pedido_detraccion (comprobante_id, codigo_detraccion, porcentaje_detraccion, total) values ";
                                    $query_detra .= "($serie_interna, '$codigo_detraccion', $porcentaje_detraccion, $total_detraccion)";
                                    $db->executeQueryEx($query_detra);
                                }
                            }
                        }
                    }
                }
                         
                //agregamos detalle del comprobante
                $array = json_decode($_POST['productos'], true);
                for ($i = 0; $i < count($array); $i++) {
                    //Recorremos array enviado
                    if(floatval($array[$i]['subtotal'])>0){
                        //Solo agregamos detalles con precio mayor a cero
                            $query_detalle = "Insert into detalle_comprobante_directo values(NULL,'".$serie_interna."', '".$array[$i]["id"]."', '".$array[$i]["precio_venta"]."', '".$array[$i]["cantidad"]."', '".$array[$i]["subtotal"]."')";
                            $db->executeQuery($query_detalle);
                    }
                }          
                //Finalmente invocamos a FE
                $this->generaElectronica($serie_interna,0,0);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
}
