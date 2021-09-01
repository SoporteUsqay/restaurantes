<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Models_ComprobanteModel {

    function __construct() {
        
    }

    public function detallesComprobante($serie){
        
        $db = new SuperDataBase();
        $query = "SELECT * FROM comprobante WHERE ncomprobante = {$serie}";

        $result = $db->executeQuery($query);
        $array = array();

        while($reg = $result->fetch_object()){
            $array[] = $reg;
        }

        return $array;
    }

    public function _saveGuia($_valor, $_TipoComprobante, $_NroComprobante, $_fechaComprobante,$_id_proveedor, $_Procedencia) {
        $db = new SuperDataBase();
        $objCaja = new Application_Models_CajaModel();
        $fecha = $objCaja->fechaCierre();
        $pkTrabajador = UserLogin::get_idTrabajador();
        if ($_valor == '1' || $_valor == '2') {
            $query = "insert into comprobante_ingreso (tipoComprobante,numeroComprobante,fecha,fechaModificacion,sucursalProcedencia,pkProvedor, estado,pkTrabajadorApertura,pkTrabajadorEdita) 
                      values($_TipoComprobante,'$_NroComprobante','$_fechaComprobante',now(),null,'$_id_proveedor',0,$pkTrabajador,$pkTrabajador)";
        }

        if ($_valor == '3') {
            $query = "insert into comprobante_ingreso (tipoComprobante,numeroComprobante,fecha,fechaModificacion,sucursalProcedencia,pkProvedor, estado,pkTrabajadorApertura,pkTrabajadorEdita) 
                      values($_TipoComprobante,'$_NroComprobante','$_fechaComprobante',now(),'$_Procedencia',null,0,$pkTrabajador,$pkTrabajador)";
        }

        if ($_valor == '4') {
            $query = "insert into comprobante_ingreso (tipoComprobante,numeroComprobante,fecha,fechaModificacion,sucursalProcedencia,pkProvedor, estado,pkTrabajadorApertura,pkTrabajadorEdita) 
                    values($_TipoComprobante,'$_NroComprobante','$_fechaComprobante',now(),null,null,0,$pkTrabajador,$pkTrabajador)";
        }

        $db->executeQuery($query);
        return $db->getId();
    }

    public function _editGuia($_tipoGuia, $_PkGuia, $_TipoComprobante, $_NroComprobante, $_fechaComprobante,$_id_proveedor, $_Procedencia) {
        $db = new SuperDataBase();
        $objIngreso = new Application_Models_IngresoInsumosModel();
        $pkTrabajador = UserLogin::get_idTrabajador();
        $fechaAntigua="";
        //Ver la fecha de la Guía
        $query = "SELECT DATE(fecha)AS fecha FROM comprobante_ingreso WHERE pkComprobante=$_PkGuia AND estado = 0";
        $result = $db->executeQuery($query);
        while ($row = $db->fecth_array($result)) {
            $fechaAntigua = $row["fecha"];
        }
        
        //comparar si las fechas son iguales
        if($fechaAntigua != $_fechaComprobante){        
        //Tomar los datos de los detalles de la guía
        $query = "SELECT pkInsumo,cantidad FROM ingresoinsumos WHERE pkComprobante = $_PkGuia AND estado = 0";
        $result = $db->executeQuery($query);
            while ($row = $db->fecth_array($result)) {
                $objIngreso->ModificarGuiasFecha($row["pkInsumo"],$row["cantidad"],$fechaAntigua,$_fechaComprobante,$_tipoGuia);
            }//Fin del recorrido        
        
        }//Fin Comprobación de fechas
        
        
        if ($_tipoGuia == '1' || $_tipoGuia == '2') {
            $query = "UPDATE comprobante_ingreso 
                    SET tipoComprobante = $_TipoComprobante,
                        numeroComprobante = '$_NroComprobante',
                        fecha = '$_fechaComprobante',
                        fechaModificacion = now(),
                        sucursalProcedencia = null,
                        pkProvedor = '$_id_proveedor',
                        pkTrabajadorEdita = $pkTrabajador
                    WHERE pkComprobante=$_PkGuia";
        }

        if ($_tipoGuia == '3') {
            $query = "UPDATE comprobante_ingreso 
                    SET tipoComprobante = $_TipoComprobante,
                        numeroComprobante = '$_NroComprobante',
                        fecha = '$_fechaComprobante',
                        fechaModificacion = now(),
                        sucursalProcedencia = '$_Procedencia',
                        pkProvedor = null,
                        pkTrabajadorEdita = $pkTrabajador
                    WHERE pkComprobante=$_PkGuia";
        }

        if ($_tipoGuia == '4') {
            $query = "UPDATE comprobante_ingreso 
                    SET numeroComprobante = '$_NroComprobante',                        
                        fecha = '$_fechaComprobante',
                        fechaModificacion = now(),
                        pkTrabajadorEdita = $pkTrabajador
                    WHERE pkComprobante=$_PkGuia";
        }
        $db->executeQuery($query);
        $query = "UPDATE ingresoinsumos
                  SET fecha = '$_fechaComprobante'
                  WHERE pkComprobante=$_PkGuia";
        $db->executeQuery($query);
    }

    public function eliminarGuia($pkGuia) {
        $db = new SuperDataBase();
        $objDetalle = new Application_Models_IngresoInsumosModel();

        $query = "UPDATE comprobante_ingreso 
                    SET estado = 1                        
                    WHERE pkComprobante=$pkGuia";
        $db->executeQuery($query);   

        $query = "SELECT * FROM ingresoinsumos
                  WHERE pkComprobante=$pkGuia";
        $result = $db->executeQuery($query);

        while ($row = $db->fecth_array($result)) {
            $objDetalle->deletedetalleGuia($row['pkIngresoInsumo']);
        }
    }

    public function activarGuia($pkGuia) {
        $db = new SuperDataBase();
        $objDetalle = new Application_Models_IngresoInsumosModel();

        $query = "UPDATE comprobante_ingreso 
                  SET estado = 0
                  WHERE pkComprobante=$pkGuia";
        $db->executeQuery($query);     

        $query = "SELECT * FROM ingresoinsumos
                  WHERE pkComprobante=$pkGuia";
        $result = $db->executeQuery($query);

        while ($row = $db->fecth_array($result)) {
            $objDetalle->ActivedetalleGuia($row['pkIngresoInsumo']);
        }
    }

    public function _AnulaComprobante($id) {
        $db = new SuperDataBase();
        $query = "update comprobante set estado=3 where pkComprobante='$id';";
        $db->executeQuery($query);
    }

    public function CancelaCuentaSolo($pkComprobante, $descuento, $total_venta) {
        $user = new UserLogin();
        $db = new SuperDataBase();
        $id = $user->get_id();
        $query = "update pedido set dateModify=now(),total=$total_venta,fechaFin=now(),descuento=$descuento,idUser='$id', estado=1, tipo_pago=1
where pkPediido='$pkComprobante';";
        $db->executeQuery($query);
        $query = "update pedido  set total_tarjeta=total-total_efectivo where tipo_pago=2;'";
        $db->executeQuery($query);
    }

    /**
     * Cancelar Cuenta
     * Comprobante Model
     * @param String $pkComprobante 
     * @param Decimal $descuento Descuento de la venta
     * @param Decimal $total Total de la venta
     * @param type $PkMesa Identificador de la mesa de atencion
     */
    public function CancelarCuenta($pkComprobante, $descuento, $total_venta, $pkMesa, $efectivo) {
        $db = new SuperDataBase();
        $user = new UserLogin();
        $id = $user->get_id();
        $query = "CALL sp_cancel_cuenta('$pkComprobante', $descuento, $total_venta, $pkMesa,'$id');";
        //echo $query;
        $db->executeQuery($query);
        $query2 = "update pedido  set total_efectivo=total, total_tarjeta=0, nombreTarjeta='-----------' where tipo_pago=1;";
        //echo $query2;
        $db->executeQuery($query2);
        $db->executeQuery("Insert into pedido_efectivo values (NULL,'".$pkComprobante."','".$efectivo."')");
        
        //Insertamos el pedido en que caja fue hecho
        $query_caja = "Insert into accion_caja values(NULL,'".$pkComprobante."','PED','".$_COOKIE["c"]."')";
        $db->executeQuery($query_caja);
    }

    /**
     * Cancelar Cuenta
     * Comprobante Model
     * @param String $pkComprobante 
     * @param Decimal $descuento Descuento de la venta
     * @param Decimal $total Total de la venta
     * @param type $PkMesa Identificador de la mesa de atencion
     */
    public function CancelarCuentaItem($pkDetalle) {
        $db = new SuperDataBase();

        $query = "update detallepedido set estadoImpresion=1 where pkDetallePedido=$pkDetalle;";
        $result = $db->executeQuery($query);
        echo $query;
    }

    public function CancelarCuentaSeleccion($pkComprobante, $descuento, $total_venta, $pkMesa, $efectivo) {
        $db = new SuperDataBase();
        $user = new UserLogin();
        $id = $user->get_id();
        $query = "update pedido set dateModify=now(),total=$total_venta,fechaFin=now(),descuento=$descuento,idUser='$id', estado=1, tipo_pago=1
where pkPediido='$pkComprobante' ;";
        $result = $db->executeQuery($query);
        $query2 = "update pedido  set total_efectivo=total, total_tarjeta=0, nombreTarjeta='-----------' where tipo_pago=1;";
        $db->executeQuery($query2);
        $db->executeQuery("Insert into pedido_efectivo values (NULL,'".$pkComprobante."','".$efectivo."')");
    }

    /**
     * Cancelar Cuenta Con Tarjeta
     * Comprobante Model
     * @param String $pkComprobante 
     * @param Decimal $descuento Descuento de la venta
     * @param Decimal $total Total de la venta
     * @param Decimal $total_tarjeta Total con tarjeta a pagar
     * @param Decimal $total_efectivo Total de Efectivo a pagar
     * @param String $tipoTarjeta Tipo de Tarjeta
     * @param type $PkMesa Identificador de la mesa de atencion
     */
    public function CancelarCuentaConTarjeta($pkComprobante, $descuento, $total_venta, $total_tarjeta, $total_efectivo, $tipo_tarjeta, $pkMesa) {
        //JOJOJOJOJOJOJO
        $objUser = new UserLogin();
        $id = $objUser->get_id();
        $db = new SuperDataBase();
        $query = "CALL sp_cancel_cuenta_tarjeta('$pkComprobante', $descuento, $total_venta,$total_tarjeta,$total_efectivo,
            '$tipo_tarjeta',$pkMesa,'$id');";
        $result = $db->executeQuery($query);
        $query2 = "update pedido  set total_efectivo=total, total_tarjeta=0, nombreTarjeta='-----------' where tipo_pago=1;";
        $db->executeQuery($query);
        echo $query;
        
        //Insertamos el pedido en que caja fue hecho
        $query_caja = "Insert into accion_caja values(NULL,'".$pkComprobante."','PED','".$_COOKIE["c"]."')";
        $db->executeQuery($query_caja);
    }

    public function CancelarCuentaConTarjetaSeleccion($pkComprobante, $descuento, $total_venta, $total_tarjeta, $total_efectivo, $tipo_tarjeta, $pkMesa) {
        $objUser = new UserLogin();
        $id = $objUser->get_id();
        $db = new SuperDataBase();
        $query = "update pedido set fechaFin=now(),
estado=0, descuento=$descuento, idUser='$id', total=$total_venta,dateModify=now(),
nombreTarjeta='$tipo_tarjeta',  total_tarjeta=$total_tarjeta,total_efectivo=$total_efectivo, tipo_pago=2,estado=1 where pkPediido='$pkComprobante';";
        $result = $db->executeQuery($query);
    }

    public function CancelarCuentaComprobante($pkComprobante, $descuento, $total_venta, $pkMesa, $tipoComprobante, $tipo_pago, $TotalEfectivo, $TotalTarjeta, $InNombreTarjeta, $pkCajero, $pkCliente, $user, $pkSucursal, $dni, $ruc) {
        $this->CancelarCuenta($pkComprobante, $descuento, $total_venta, $pkMesa,$TotalEfectivo);
        $db = new SuperDataBase();
        if ($tipo_pago == "1") {
            $TotalEfectivo = $total_venta;
            $TotalTarjeta = 0;
            $InNombreTarjeta = "---------------";
        }

        $query = "Call sp_generate_comprobante($tipoComprobante,$tipo_pago,$TotalEfectivo,$TotalTarjeta,"
                . "'$InNombreTarjeta',$pkCajero,'$pkCliente',$user,'$pkSucursal','$pkComprobante','$ruc','$dni',$descuento,@sa)";
        $result = $db->executeQuery($query);

        $resultado = "";
        while ($row = $db->fecth_array($result)) {
            $resultado = $row['codgen'];
        }
        $query = "update  comprobante set totalEfectivo=total-totalTarjeta;";
        $db->executeQuery($query);
        return $resultado;
    }

    public function CancelarCuentaComprobanteSeleccion($pkComprobante, $descuento, $total_venta, $pkMesa, $tipoComprobante, $tipo_pago, $TotalEfectivo, $TotalTarjeta, $InNombreTarjeta, $pkCajero, $pkCliente, $user, $pkSucursal, $dni, $ruc) {
        if ($tipo_pago == "1") {
            $TotalEfectivo=$total_venta;
            $TotalTarjeta=0;
            $InNombreTarjeta='-----------';
        }
        $db = new SuperDataBase();
        $query = "Call sp_generate_comprobanteSeleccion($tipoComprobante,$tipo_pago,$TotalEfectivo,$TotalTarjeta,"
                . "'$InNombreTarjeta',$pkCajero,'$pkCliente',$user,'$pkSucursal','$pkComprobante','$ruc','$dni',$descuento,@sa)";
        $result = $db->executeQuery($query);
        $resultado = "";
        while ($row = $db->fecth_array($result)) {
            $resultado = $row['codgen'];
        }

        return $resultado;
    }

    /**
     * Function que lista boletas
     * @param date $fechaInicio
     * @param date $fechaFin
     * @return json ListadoBoletas
     * @access public
     * @author Jeison cruz <jcruzyesan@gmail.com>
     * fecha:17/09/2015
     * hora: 9:20 pm17/09/2015
     */
    public function listadoComprobante($tipo, $fechaInicio, $fechaFin) {

        $db = new SuperDataBase();
        $query = "SELECT * FROM comprobante c where pkTipoComprobante=$tipo and fecha between '$fechaInicio' and '$fechaFin';";
        $result = $db->executeQuery($query);
        $array = array();
        while ($row = $db->fecth_array($result)) {
            $array[] = array(
                "pkComprobante" => $row['pkComprobante'],
                "pkTipoComprobante" => $row['pkTipoComprobante'],
                "estado" => $row['estado'],
                "total" => $row['total']
            );
        }
        echo json_encode($array);
    }

    public function detalleComprobante($codComprobante) {
        $db = new SuperDataBase();
        $query = "SELECT d.pkPediido , c.ncomprobante, c.dateModify, d.pkDetallePedido, d.cantidad, d.precio, " .
                "ROUND((d.cantidad*d.precio),2) AS importe, pr.descripcion AS pedido " .
                "FROM comprobante c, detalle_comprobante2 dc, detallepedido d, pedido p, productos pr " .
                "WHERE p.pkPediido = d.pkPediido AND " .
                "d.pkProducto = pr.pkProducto AND " .
                "c.pkComprobante = dc.pkDetalleComprobante AND " .
                "d.pkDetallePedido = dc.pkDetallePedido AND " .
                "c.pkComprobante = '$codComprobante' " .
                "UNION " .
                "SELECT d.pkPediido , c.ncomprobante, c.dateModify, d.pkDetallePedido, d.cantidad, d.precio, " .
                "ROUND((d.cantidad*d.precio),2) AS importe, pl.descripcion AS pedido " .
                "FROM comprobante c, detalle_comprobante2 dc, detallepedido d, pedido p, plato pl " .
                "WHERE p.pkPediido = d.pkPediido AND " .
                "d.pkPlato = pl.pkPlato AND " .
                "c.pkComprobante = dc.pkDetalleComprobante AND " .
                "d.pkDetallePedido = dc.pkDetallePedido AND " .
                "c.pkComprobante = '$codComprobante'";
        $result = $db->executeQuery($query);
        $array = array();
        while ($row = $db->fecth_array($result)) {
            $array[] = array(
                "pkPedido" => $row['pkPediido'],
                "numComprobante" => $row['ncomprobante'],
                "cantidad" => $row['cantidad'],
                "precio" => $row['precio'],
                "pedido" => utf8_encode($row['pedido']),
                "importe" => $row['importe']
            );
        }
        echo json_encode($array);
    }

    /**
     * Esta ahora es mi funcion putos
     */
    public function deleteComprobante($codComprobante, $tipo) {
        error_reporting(E_ALL);
        
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
        
        $serie = NULL;
        $tipo_c = 0;
        $numero_c = NULL;
        
        if(intval($tipo) == 1){
            $c0 = "Select * from cloud_config where parametro = 'sboleta'";
            $s0 = $db->executeQuery($c0);
            if ($row = $db->fecth_array($s0)){
                $serie = $row["valor"];
            }
            $tipo_c = 2;
        }else{
            $c0 = "Select * from cloud_config where parametro = 'sfactura'";
            $s0 = $db->executeQuery($c0);
            if ($row = $db->fecth_array($s0)){
                $serie = $row["valor"];
            }
            $tipo_c = 1;
        }
        
        //Obtenemos autoincremental
        $q_au = "Select ncomprobante from comprobante where pkComprobante = '$codComprobante'";
        $r_au = $db->executeQuery($q_au);
        if($rowA = $db->fecth_array($r_au)){
            $numero_c = $rowA["ncomprobante"];
        }
        
        $anulacion = array();
        
        $anulacion["operacion"] = "generar_anulacion";
        $anulacion["tipo_de_comprobante"] = $tipo_c;
        $anulacion["serie"] = $serie;      
        $anulacion["numero"] = $numero_c;
        $anulacion["motivo"] = "ERROR DEL SISTEMA";
        $anulacion["codigo_unico"] =  "";
        
        $data_json = json_encode($anulacion);
        
        //Invocamos el servicio de NUBEFACT
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $ruta);
        curl_setopt(
                $ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Token token="'.$token.'"',
                'Content-Type: application/json',
                )
        );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $respuesta  = curl_exec($ch);
        curl_close($ch);
        
        //Verificamos respuesta
        //print_r($respuesta);
        $leer_respuesta = json_decode($respuesta, true);
        if (isset($leer_respuesta['errors'])) {
            if ($leer_respuesta['codigo'] == 21 && intval($tipo) == 1) {
                $query = "UPDATE comprobante " .
                    "SET estado = 3 " .
                    "WHERE pkTipoComprobante = $tipo AND " .
                    "pkComprobante = '$codComprobante'";
                $db->executeQuery($query);
            }
            //Mostramos errores
            echo $leer_respuesta['errors'];
        } else {
            if(isset($leer_respuesta["aceptada_por_sunat"])){
                $query = "UPDATE comprobante " .
                "SET estado = 3 " .
                "WHERE pkTipoComprobante = $tipo AND " .
                "pkComprobante = '$codComprobante'";
                $db->executeQuery($query);               
                echo "SI";
            }else{
                echo "NO";
            }           
        }
    }

    public function addDetallePedidoComprobante($compobante, $pkDetalle) {
        $db = new SuperDataBase();
        $query = "insert into detalle_comprobante2 values('$compobante','$pkDetalle')";
        $db->executeQuery($query);
    }

}
