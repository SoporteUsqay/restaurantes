<?php

class Conectar {

    public static function con() {

        $conexion = mysql_connect("localhost", "root", "");
        //   mysql_query("SET NAMES 'utf8'");
        mysql_select_db("restaurantes");
        return $conexion;
    }

}

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Models_ReportModel {

    private $dateGO;
    private $dateEnd;
    private $datos;

    function __construct() {
        $this->datos = array();
    }

    public function getDateGO() {
        return $this->dateGO;
    }

    public function getDateEnd() {
        return $this->dateEnd;
    }

    public function setDateGO($dateGO) {
        $this->dateGO = $dateGO;
    }

    public function setDateEnd($dateEnd) {
        $this->dateEnd = $dateEnd;
    }

    /**
     * Tota de ventas entre dos fechas
     */
    public function _listTotalSaleBetwenDate() {
        $db = new SuperDataBase();
        $query = "CALL sp_get_sale_date('$this->dateGO','$this->dateEnd')";
        $resul = $db->executeQuery($query);

        $array = array();
        while ($row = $db->fecth_array($resul)) {
            $array[] = array("mes" => $row['mes'],
                "total" => $row['total'],
                "year" => $row['year']
            );
        }
//        echo $query;
        echo json_encode($array);
    }

    public function _listComprobantes($dateGo, $dateEnd, $nnumero, $tipo) {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
//        $query = "select *, c.estado as estadoC,  c.documento, c.fecha,(ifnull(c.totaltarjeta,0.00)+ifnull(c.totalEfectivo,0.00)) as totalC from comprobante c  where c.fecha  between '$dateGo' and  '$dateEnd' and c.pkSucursal='$sucursal' and pkTipoComprobante=$tipo and ncomprobante like '%$nnumero%';";
        $query = "select *, c.estado as estadoC,  c.documento, c.fecha,(ifnull(c.totaltarjeta,0.00)+ifnull(c.totalEfectivo,0.00)) as totalC
from comprobante c,persona_juridica pj
where pj.ruc = c.ruc and c.fecha  between '$dateGo' and  '$dateEnd' and c.pkSucursal='$sucursal' and pkTipoComprobante=$tipo and ncomprobante like '%$nnumero%';";

        $resul = $db->executeQuery($query);
        $array = array();
        $total = 0;
        while ($row = $db->fecth_array($resul)) {
            if ($row['totalC'] > 0) {

                if ($row['estadoC'] == "3") {
                    $estado = "Anulada";
                } else
                    $estado = "Emitida";
                if ($row['tipo_pago'] == "1") {
                    $tipoPago = "Efectivo";
                    $tipoTarjeta = "";
                } else {
                    $tipoPago = "Tarjeta";
                    $tipoTarjeta = $row['nombreTarjeta'];
                }
                if ($tipo == "2") {
                    $documento = $row['ruc'];
                } else {
                    $documento = $row['documento'];
                }

                $array[] = array("pkComprobante" => $row['pkComprobante'],
                    "documento" => $documento,
                    "fecha" => $row['fecha'],
                    "ncomprobante" => $row['ncomprobante'],
                    "descuento" => $row['descuento'],
//                "pkPediido" => $row['pkPediido'],
                    "total_tarjeta" => $row['nombreTarjeta'],
                    "razon_social" => $row['razonSocial'],
                    "total_efectivo" => $row['totalEfectivo'],
                    "total" => $row['totalC'],
                    "estado" => $estado,
                    "ntarjeta" => $tipoTarjeta,
                    "tpago" => $tipoPago,
//                "tTarjeta" => $row['']
                );
//            $total=$total+$row['total_venta'];
            }
        }
//        $array[]=  array('Total')
//        echo $query;
        echo json_encode($array);
    }

    public function _listFacturas($dateGo, $dateEnd, $nnumero) {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "select *, c.estado as estadoC,  c.documento, c.fecha from comprobante c  where c.fecha  between '$dateGo' and  '$dateEnd' and c.pkSucursal='$sucursal' and pkTipoComprobante=2 and ncomprobante like '%$nnumero%';";
        $resul = $db->executeQuery($query);

        $array = array();
        $total = 0;
        while ($row = $db->fecth_array($resul)) {
            if ($row['estadoC'] == "3") {
                $estado = "Anulado";
            } else
                $estado = "";

            $array[] = array("pkFactura" => $row['pkComprobante'],
                "documento" => $row['documento'],
                "fecha" => $row['fecha'],
                "total" => $row['total'],
                "ncomprobante" => $row['ncomprobante'],
                "estado" => $estado
            );
//            $total=$total+$row['total_venta'];
        }
//        $array[]=  array('Total')
//        echo $query;
        echo json_encode($array);
    }

    public function listItemPedidosboletas($boleta) {

        $db = new SuperDataBase();
        $query = "SELECT *,case when mod(cantidad,1) = 0 then  round((cantidad*precio),2) else round((cantidad*precio),0) end as importe,
case  when character_length(pkProducto) <6 then ( select upper(descripcion) from plato pl
  where pl.pkPlato=dp.pkPlato )
when character_length(pkPlato)<6 then (select upper(descripcion) from productos pr where pr.pkProducto=dp.pkProducto )
 end as pedido
 FROM detalle_comprobante2 d
inner join detallepedido dp on d.pkDetallePedido=dp.pkDetallePedido where pkDetalleComprobante='$boleta';";
//        die($query);
        $result = $db->executeQuery($query);

        $array = array();
        while ($row = $db->fecth_array($result)) {
            $array[] = array(
//                , , , pkProducto, pkPlato, mensaje, pkComprobante, estado_pedido, hora_pedido, hora_entrega_pedido
                "pkPedido" => $row['pkDetallePedido'],
                "cantidad" => $row['cantidad'],
                "precio" => $row['precio'],
                "nompedido" => utf8_encode($row['pedido']),
                "importe" => $row['importe'],
                "mensaje" => $row['mensaje'],
//                "mozo" => utf8_encode($row['nombres'] . " " . $row['lastName'])
            );
        }
        echo json_encode($array);
    }

    public function listItemPedidosfacturas($factura) {

        $db = new SuperDataBase();
        $query = "call sp_listadetalle_Boleta('$factura');";
//        die($query);
        $result = $db->executeQuery($query);

        $array = array();
        while ($row = $db->fecth_array($result)) {
            $array[] = array(
//                , , , pkProducto, pkPlato, mensaje, pkComprobante, estado_pedido, hora_pedido, hora_entrega_pedido
                "pkPedido" => $row['pkDetallePedido'],
                "cantidad" => $row['cantidad'],
                "precio" => $row['precio'],
                "nompedido" => utf8_encode($row['pedido']),
                "importe" => $row['importe'],
                "mensaje" => $row['mensaje'],
                "mozo" => utf8_encode($row['nombres'] . " " . $row['lastName'])
            );
        }
        echo json_encode($array);
    }

    /**
     * Tota de ventas por fecha
     */
    public function _listTotalSaleDate($estado) {
        $db = new SuperDataBase();
        switch ($estado) {
            case '1':
                $consulta = " and tipo_pago=1";
                break;
            case '2':
                $consulta = " and tipo_pago=2";
                break;
            case '3':
                $consulta = " and p.estado=3";
                break;
            case '4':
                $consulta = " and tipo_pago=4";
                break;
            case '5':
                $consulta = " and tipo_pago=5";
                break;
            default : $consulta = " and p.estado=1";
                break;
        }
        $query = "SELECT *,time(fechaApertura) as horaEntrada FROM pedido p  inner join mesas m on m.pkMesa = p.pkMesa where date(fechaCierre)='$this->dateGO' " . $consulta;
        $resul = $db->executeQuery($query);
//pkComprobante, pkMesa, estado_pago, tipoComprobante, total_tarjeta, total_efectivo, descuento, total_venta, tipo_tarjeta, pkCliente, pkMozo, fechaPago, hora_entrada, hora_salida, fecha_modificacion, idUsuario, npersonas
        $array = array();
        $total = 0;
        while ($row = $db->fecth_array($resul)) {
            if ($row['nombreTarjeta'] == null) {
                $row['nombreTarjeta'] = "";
            }
            if ($row['total_tarjeta'] == null) {
                $row['total_tarjeta'] = "0";
            }
            if ($row['npersonas'] == null) {
                $row['npersonas'] = "";
            }
            $array[] = array("pkComprobante" => $row['pkPediido'],
                "pkMesa" => $row['pkMesa'],
                "total_venta" => $row['total'],
                "horaEntrada" => $row['horaEntrada'],
                "nmesa" => $row['nmesa'],
                "npersonas" => $row['npersonas'],
//                "tcomprobante" => $row['tipo_comprobante'],
                "descuento" => $row['descuento'],
                "totalTarjeta" => $row['total_tarjeta'],
                "total_efectivo" => $row['total_efectivo'],
                "tipo_tarjeta" => $row['nombreTarjeta'],
            );
//            $total=$total+$row['total_venta'];
        }
//        $array[]=  array('Total')
//        echo $query;
        echo json_encode($array);
    }

    /**
     * Tota de ventas por mes
     * Desarrollado por Ing Jeanmarco Leon
     * --
     * Calla Concha de tu madre
     * Ingeniero de estafas seras
     */
    public function _listTotalSaleMonth($AniVentas) {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "
SELECT *, sum(total) as total2,sum(descuento) as descuento2,time(fechaApertura) as horaEntrada FROM pedido p  inner join (mesas m inner join salon s on s.pkSalon=m.pkSalon) on m.pkMesa = p.pkMesa
where month(fechaCierre)=$this->dateGO and year(fechaCierre)=$AniVentas  and p.estado=1 and pkSucursal='$sucursal' group by fechaCierre order by fechaCierre;";
//       echo $query;
        $resul = $db->executeQuery($query);
//pkComprobante, pkMesa, estado_pago, tipoComprobante, total_tarjeta, total_efectivo, descuento, total_venta, tipo_tarjeta, pkCliente, pkMozo, fechaPago, hora_entrada, hora_salida, fecha_modificacion, idUsuario, npersonas
        $array = array();
        $total = 0;
        while ($row = $db->fecth_array($resul)) {
            $array[] = array("pkComprobante" => $row['pkPediido'],
                "pkMesa" => $row['pkMesa'],
                "total_venta" => $row['total2'],
                "fechaCierre" => $row['fechaCierre'],
                "nmesa" => $row['nmesa'],
                "npersonas" => $row['npersonas'],
//                "tcomprobante" => $row['tipo_comprobante'],
                "descuento" => $row['descuento2'],
//                "totalTarjeta" => $row['total_tarjeta'],
            );
//            $total=$total+$row['total_venta'];
        }
//        $array[]=  array('Total')
//        echo $query;
        echo json_encode($array);
    }

    /**
     * Tota de ventas entre dos fechas
     */
    public function _listTotalSale2Date() {
        $db = new SuperDataBase();
        $query = "CALL sp_get_report_sale_betweendates('$this->dateGO','$this->dateEnd')";
        $resul = $db->executeQuery($query);
//pkComprobante, pkMesa, estado_pago, tipoComprobante, total_tarjeta, total_efectivo, descuento, total_venta, tipo_tarjeta, pkCliente, pkMozo, fechaPago, hora_entrada, hora_salida, fecha_modificacion, idUsuario, npersonas
        $array = array();
        $total = 0;
        while ($row = $db->fecth_array($resul)) {
            $array[] = array("pkComprobante" => $row['pkPediido'],
                "pkMesa" => $row['pkMesa'],
                "total_venta" => $row['total'],
                "horaEntrada" => $row['horaEntrada'],
                "nmesa" => $row['nmesa'],
                "npersonas" => $row['npersonas'],
//                "tcomprobante" => $row['tipo_comprobante'],
                "descuento" => $row['descuento'],
//                "totalTarjeta" => $row['total_tarjeta'],
            );
//            $total=$total+$row['total_venta'];
        }
//        $array[]=  array('Total')
//        echo $query;
        echo json_encode($array);
    }

    /**
     * Salida de Productos por dia
     * 
     */
    public function salidaProductosPorDia($dateGo) {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
//        $query = "CALL sp_get_product_sale_pordia($valorProducto,'$dateGo','$sucursal',$IdCategoria,$tipo)";
        $query = "Select p.pkProducto as pk, p.descripcion,
(Select COUNT(dp.precio)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=5) AND
(dp.pkProducto =  p.pkProducto) AND p.fechaCierre BETWEEN '2015-08-09' AND '2015-08-09'>0) as cantidad_consumo,
(Select COUNT(dp.precio)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=4) AND
(dp.pkProducto =  p.pkProducto) AND p.fechaCierre BETWEEN '2015-08-09' AND '2015-08-09'>0) as cantidad_credito,
(Select COUNT(dp.precio)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=1) AND
(dp.pkProducto =  p.pkProducto) AND p.fechaCierre BETWEEN '2015-08-09' AND '2015-08-09'>0) as cantidad_venta,
(Select sum(dp.precio)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=5) AND
(dp.pkProducto =  p.pkProducto) AND p.fechaCierre BETWEEN '2015-08-09' AND '2015-08-09'>0) as importe_consumo,
(Select sum(dp.precio)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=4) AND
(dp.pkProducto =  p.pkProducto) AND p.fechaCierre BETWEEN '2015-08-09' AND '2015-08-09'>0) as importe_credito,
(Select SUM(dp.precio)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=1) AND
(dp.pkProducto =  p.pkProducto) AND p.fechaCierre BETWEEN '2015-08-09' AND '2015-08-09'>0) as importe_venta,
(Select count(dp.precio)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=1 or p.estado=4 or p.estado=5) AND
(dp.pkProducto =  p.pkProducto) AND p.fechaCierre BETWEEN '2015-08-09' AND '2015-08-09'>0) as cant_total,
(Select sum(dp.precio)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=1 or p.estado=4 or p.estado=5) AND
(dp.pkProducto =  p.pkProducto) AND p.fechaCierre BETWEEN '2015-08-09' AND '2015-08-09'>0) as impor_total
 from productos p, producto_sucursal ps
where ps.pkProducto = p.pkProducto and ps.pkSucursal = 'SU009' and
((Select count(dp.pkDetallePedido) as cnt from detallepedido dp, pedido p
where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND p.estado <>3 AND dp.pkProducto = p.pkProducto AND
p.fechaCierre BETWEEN '2015-08-09' AND '2015-08-09')>0) union

(Select p.pkPlato as pk, p.descripcion,
(Select COUNT(dp.precio)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=5) AND
(dp.pkPlato =  p.pkPlato) aND p.fechaCierre BETWEEN '2015-08-09' AND '2015-08-09' >0) as cantidad_consumo,
(Select COUNT(dp.precio)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=4) AND
(dp.pkPlato =  p.pkPlato)AND p.fechaCierre BETWEEN '2015-08-09' AND '2015-08-09'>0) as cantidad_credito,
(Select COUNT(dp.precio)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=1) AND
(dp.pkPlato =  p.pkPlato)AND p.fechaCierre BETWEEN '2015-08-09' AND '2015-08-09'>0) as cantidad_venta,
(Select sum(dp.precio)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=5) AND
(dp.pkPlato =  p.pkPlato)AND p.fechaCierre BETWEEN '2015-08-09' AND '2015-08-09'>0) as importe_consumo,
(Select sum(dp.precio)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=4) AND
(dp.pkPlato =  p.pkPlato) AND p.fechaCierre BETWEEN '2015-08-09' AND '2015-08-09'>0) as importe_credito,
(Select SUM(dp.precio)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=1) AND
(dp.pkPlato =  p.pkPlato) and p.fechaCierre BETWEEN '2015-08-09' AND '2015-08-09'>0) as importe_venta,
(Select count(dp.precio)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=1 or p.estado=4 or p.estado=5) AND
(dp.pkPlato =  p.pkPlato) and p.fechaCierre BETWEEN '2015-08-09' AND '2015-08-09'>0) as cant_total,
(Select sum(dp.precio)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=1 or p.estado=4 or p.estado=5) AND
(dp.pkPlato =  p.pkPlato) and p.fechaCierre BETWEEN '2015-08-09' AND '2015-08-09'>0) as impor_total
 from plato p, plato_sucursal ps
where ps.pkPlato = p.pkPlato and ps.pkSucursal = 'SU009' and
((Select count(dp.pkDetallePedido) as cnt from detallepedido dp, pedido p
where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND p.estado <>3 AND dp.pkPlato = p.pkPlato AND
p.fechaCierre BETWEEN '2015-08-09' AND '2015-08-09')>0)order by p.pkTipo)";
        $resul = $db->executeQuery($query);
//        die($query);
        $array = array();
        while ($row = $db->fecth_array($resul)) {
            $array[] = array(
                "categoria" => utf8_encode($row['categoria']),
                "tipo" => utf8_encode($row['tipo']),
                "pedido" => utf8_encode($row['pedido']),
                "Cantidad" => $row['cantidad'],
                "Precio" => $row['precio'],
                "Importe" => $row['total'],
                "Fecha" => $row['fecha'],
                "Hora" => $row['horapedido']
            );
//            $total=$total+$row['total_venta'];
        }
//        $array[]=  array('Total')
//        echo $query;
        echo json_encode($array);
    }

    public function totalPorAÃƒÂ±os($mesInicio, $AÃƒÂ±oInicio, $meFin, $AÃƒÂ±oFin) {
        $db = new SuperDataBase();
        $query = "CALL sp_reporTotalSaleAnos_meses('$AÃƒÂ±oInicio','$mesInicio','$meFin','$AÃƒÂ±oFin')";
        $resul = $db->executeQuery($query);
//echo $query;
        $array = array();
        while ($row = $db->fecth_array($resul)) {

            $array[] = array(
                "aÃƒÂ±o" => $row['ano'],
                "mes" => $row['mes'],
                "total" => $row['total'],
            );
        }
        echo json_encode($array);
    }

    public function salidaVentasSemanales($mes, $anio, $semana, $valorClase, $IdCategoria, $tipo) {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "CALL sp_ventas_Semanales($anio,$mes,$semana,$valorClase,$IdCategoria,$tipo,'$sucursal')";
        $resul = $db->executeQuery($query);
        $array = array();
//       echo $query;
        while ($row = $db->fecth_array($resul)) {
            $array[] = array(
                "categoria" => utf8_encode($row['categoria']),
                "tipo" => utf8_encode($row['tipo']),
                "pedido" => utf8_encode($row['pedido']),
                "Cantidad" => $row['cantidad'],
                "Precio" => $row['precio'],
                "Importe" => $row['total'],
                "Fecha" => $row['fecha'],
                "Hora" => $row['horapedido'],
                "dia" => $row['dia_semana']
            );
        }

        echo json_encode($array);
    }

    public function salidaProductosPorMes($dateGo, $anio, $valorProducto, $IdCategoria, $tipo) {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "CALL sp_get_product_sale_porMes('$dateGo',$anio,$valorProducto,$IdCategoria,$tipo,'$sucursal')";
        $resul = $db->executeQuery($query);
        $array = array();
//       echo $query;
        while ($row = $db->fecth_array($resul)) {
            $array[] = array(
                "categoria" => utf8_encode($row['categoria']),
                "tipo" => utf8_encode($row['tipo']),
                "pedido" => utf8_encode($row['pedido']),
                "Cantidad" => $row['cantidad'],
                "Precio" => $row['precio'],
                "Importe" => $row['total'],
                "Fecha" => $row['fecha'],
                "Hora" => $row['horapedido']
            );
        }

        echo json_encode($array);
    }

    // reporte en pdf

    public function ReportelistBoletas($dateGo, $dateEnd, $sucursal) {

        $query = "CALL sp_listadoBoletas('$dateGo','$dateEnd','$sucursal')";
        $result = mysql_query($query, Conectar::con());

        while ($reg = mysql_fetch_assoc($result)) {
            $this->datos[] = $reg;
        }
        return $this->datos;
    }

    public function ReporteVentasMes($dateGo, $anio, $sucursal) {
//        $db = new SuperDataBase();
//        $sucursal=  UserLogin::get_pkSucursal();

        $query = "SELECT *, sum(total) as total2,sum(descuento) as descuento2,time(fechaApertura) as horaEntrada FROM pedido p  inner join (mesas m inner join salon s on s.pkSalon=m.pkSalon) on m.pkMesa = p.pkMesa
where month(fechaCierre)=$dateGo and year(fechaCierre)=$anio  and p.estado=1 and pkSucursal='$sucursal' group by fechaCierre order by fechaCierre;";
        //  echo die($query);
        $result = mysql_query($query, Conectar::con());

        while ($reg = mysql_fetch_assoc($result)) {
            $this->datos[] = $reg;
        }
        return $this->datos;
    }

    public function ReportesalidaProductosPorDia($filtro, $dateGo, $valorProducto, $Idcategoria, $sucursal) {
        //       $db = new SuperDataBase();
        $query = "CALL sp_get_product_sale_pordia($valorProducto,'$dateGo','$sucursal',$Idcategoria,$filtro)";
        //       $resul = $db->executeQuery($query);
        $result = mysql_query($query, Conectar::con());

        while ($reg = mysql_fetch_assoc($result)) {
            $this->datos[] = $reg;
        }
        return $this->datos;
    }

    public function ReporteConsolidadoVentasPDF($sucursal, $tpf, $fi, $ff, $di, $mb, $ab, $df, $m, $a, $ano, $tpd) {

        $ffecha = "";
        if (isset($tpf)) {
            switch ($tpf) {
                case "d":
                    if ($ff !== "") {
                        $ffecha = " AND p.fechaCierre BETWEEN '" . $fi . "' AND '" . $ff . "'";
                    } else {
                        $ffecha = " AND p.fechaCierre = '" . $fi . "' ";
                    }
                    break;

                case "m":
                    $ffecha = " AND MONTH(p.fechaCierre) = '" . $mb . "' AND YEAR(p.fechaCierre) = '" . $ab . "'";
                    break;

                case "a":
                    $ffecha = " AND YEAR(p.fechaCierre) = '" . $ab . "'";
                    break;
            }
        }

        if (isset($_GET["tpd"])) {
            switch ($_GET["tpd"]) {
                case "to":
                    $consulta = "Select p.pkProducto as pk, p.descripcion,
    (Select SUM(dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=5) AND
    (dp.pkProducto =  p.pkProducto)" . $ffecha . ">0) as cantidad_consumo,
    (Select SUM(dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=4) AND
    (dp.pkProducto =  p.pkProducto)" . $ffecha . ">0) as cantidad_credito,
    (Select SUM(dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=1) AND
    (dp.pkProducto =  p.pkProducto)" . $ffecha . ">0) as cantidad_venta,
    (Select SUM(dp.precio*dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=5) AND
    (dp.pkProducto =  p.pkProducto)" . $ffecha . ">0) as importe_consumo,
    (Select SUM(dp.precio*dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=4) AND
    (dp.pkProducto =  p.pkProducto)" . $ffecha . ">0) as importe_credito,
    (Select SUM(dp.precio*dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=1) AND
    (dp.pkProducto =  p.pkProducto)" . $ffecha . ">0) as importe_venta,
    (Select SUM(dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=1 or p.estado=4 or p.estado=5) AND
    (dp.pkProducto =  p.pkProducto)" . $ffecha . ">0) as cant_total,
    (Select SUM(dp.precio*dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=1 or p.estado=4 or p.estado=5) AND
    (dp.pkProducto =  p.pkProducto)" . $ffecha . ">0) as impor_total
    from productos p, producto_sucursal ps
    where ps.pkProducto = p.pkProducto and ps.pkSucursal = '" . $sucursal . "' and
    ((Select count(dp.pkDetallePedido) as cnt from detallepedido dp, pedido p
    where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND p.estado <>3 AND dp.pkProducto = p.pkProducto" . $ffecha . ")>0) union

    (Select p.pkPlato as pk, p.descripcion,
    (Select SUM(dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=5) AND
    (dp.pkPlato =  p.pkPlato)" . $ffecha . ">0) as cantidad_consumo,
    (Select SUM(dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=4) AND
    (dp.pkPlato =  p.pkPlato)" . $ffecha . ">0) as cantidad_credito,
    (Select SUM(dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=1) AND
    (dp.pkPlato =  p.pkPlato)" . $ffecha . ">0) as cantidad_venta,
    (Select SUM(dp.precio*dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=5) AND
    (dp.pkPlato =  p.pkPlato)" . $ffecha . ">0) as importe_consumo,
    (Select SUM(dp.precio*dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=4) AND
    (dp.pkPlato =  p.pkPlato)" . $ffecha . ">0) as importe_credito,
    (Select SUM(dp.precio*dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=1) AND
    (dp.pkPlato =  p.pkPlato)" . $ffecha . ">0) as importe_venta,
    (Select SUM(dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=1 or p.estado=4 or p.estado=5) AND
    (dp.pkPlato =  p.pkPlato)" . $ffecha . ">0) as cant_total,
    (Select SUM(dp.precio*dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=1 or p.estado=4 or p.estado=5) AND
    (dp.pkPlato =  p.pkPlato)" . $ffecha . ">0) as impor_total
    from plato p, plato_sucursal ps
    where ps.pkPlato = p.pkPlato and ps.pkSucursal = '" . $sucursal . "' and
    ((Select count(dp.pkDetallePedido) as cnt from detallepedido dp, pedido p
    where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND p.estado <>3 AND dp.pkPlato = p.pkPlato" . $ffecha . ")>0)order by p.pkTipo);";
                    break;

                case "pl":

                    if ($_GET["plato"] === "0") {
                        $consulta = "(Select p.pkPlato as pk, p.descripcion,
    (Select SUM(dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=5) AND
    (dp.pkPlato =  p.pkPlato)" . $ffecha . ">0) as cantidad_consumo,
    (Select SUM(dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=4) AND
    (dp.pkPlato =  p.pkPlato)" . $ffecha . ">0) as cantidad_credito,
    (Select SUM(dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=1) AND
    (dp.pkPlato =  p.pkPlato)" . $ffecha . ">0) as cantidad_venta,
    (Select SUM(dp.precio*dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=5) AND
    (dp.pkPlato =  p.pkPlato)" . $ffecha . ">0) as importe_consumo,
    (Select SUM(dp.precio*dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=4) AND
    (dp.pkPlato =  p.pkPlato)" . $ffecha . ">0) as importe_credito,
    (Select SUM(dp.precio*dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=1) AND
    (dp.pkPlato =  p.pkPlato)" . $ffecha . ">0) as importe_venta,
    (Select SUM(dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=1 or p.estado=4 or p.estado=5) AND
    (dp.pkPlato =  p.pkPlato)" . $ffecha . ">0) as cant_total,
    (Select SUM(dp.precio*dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=1 or p.estado=4 or p.estado=5) AND
    (dp.pkPlato =  p.pkPlato)" . $ffecha . ">0) as impor_total
    from plato p, plato_sucursal ps
    where ps.pkPlato = p.pkPlato and ps.pkSucursal = '" . $sucursal . "' and
    ((Select count(dp.pkDetallePedido) as cnt from detallepedido dp, pedido p
    where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND p.estado <>3 AND dp.pkPlato = p.pkPlato" . $ffecha . ")>0)order by p.pkTipo);";
                    } else {
                        $consulta = "(Select p.pkPlato as pk, p.descripcion,
    (Select SUM(dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=5) AND
    (dp.pkPlato =  p.pkPlato) AND p.pkPlato = '" . $_GET["plato"] . "'" . $ffecha . ">0) as cantidad_consumo,
    (Select SUM(dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=4) AND
    (dp.pkPlato =  p.pkPlato) AND p.pkPlato = '" . $_GET["plato"] . "'" . $ffecha . ">0) as cantidad_credito,
    (Select SUM(dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=1) AND
    (dp.pkPlato =  p.pkPlato) AND p.pkPlato = '" . $_GET["plato"] . "'" . $ffecha . ">0) as cantidad_venta,
    (Select SUM(dp.precio*dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=5) AND
    (dp.pkPlato =  p.pkPlato) AND p.pkPlato = '" . $_GET["plato"] . "'" . $ffecha . ">0) as importe_consumo,
    (Select SUM(dp.precio*dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=4) AND
    (dp.pkPlato =  p.pkPlato) AND p.pkPlato = '" . $_GET["plato"] . "'" . $ffecha . ">0) as importe_credito,
    (Select SUM(dp.precio*dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=1) AND
    (dp.pkPlato =  p.pkPlato) AND p.pkPlato = '" . $_GET["plato"] . "'" . $ffecha . ">0) as importe_venta,
    (Select SUM(dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=1 or p.estado=4 or p.estado=5) AND
    (dp.pkPlato =  p.pkPlato) AND p.pkPlato = '" . $_GET["plato"] . "'" . $ffecha . ">0) as cant_total,
    (Select SUM(dp.precio*dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=1 or p.estado=4 or p.estado=5) AND
    (dp.pkPlato =  p.pkPlato) AND p.pkPlato = '" . $_GET["plato"] . "'" . $ffecha . ">0) as impor_total
    from plato p, plato_sucursal ps
    where ps.pkPlato = p.pkPlato AND p.pkPlato = '" . $_GET["plato"] . "' and ps.pkSucursal = '" . $sucursal . "' and
    ((Select count(dp.pkDetallePedido) as cnt from detallepedido dp, pedido p
    where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND p.estado <>3 AND dp.pkPlato = p.pkPlato AND p.pkPlato = '" . $_GET["plato"] . "'" . $ffecha . ")>0)order by p.pkTipo);";
                    }

                    break;
                case "pr":
                    if ($_GET["producto"] === "0") {
                        $consulta = "Select p.pkProducto as pk, p.descripcion,
    (Select SUM(dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=5) AND
    (dp.pkProducto =  p.pkProducto)" . $ffecha . ">0) as cantidad_consumo,
    (Select SUM(dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=4) AND
    (dp.pkProducto =  p.pkProducto)" . $ffecha . ">0) as cantidad_credito,
    (Select SUM(dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=1) AND
    (dp.pkProducto =  p.pkProducto)" . $ffecha . ">0) as cantidad_venta,
    (Select SUM(dp.precio*dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=5) AND
    (dp.pkProducto =  p.pkProducto)" . $ffecha . ">0) as importe_consumo,
    (Select SUM(dp.precio*dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=4) AND
    (dp.pkProducto =  p.pkProducto)" . $ffecha . ">0) as importe_credito,
    (Select SUM(dp.precio*dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=1) AND
    (dp.pkProducto =  p.pkProducto)" . $ffecha . ">0) as importe_venta,
    (Select SUM(dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=1 or p.estado=4 or p.estado=5) AND
    (dp.pkProducto =  p.pkProducto)" . $ffecha . ">0) as cant_total,
    (Select SUM(dp.precio*dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=1 or p.estado=4 or p.estado=5) AND
    (dp.pkProducto =  p.pkProducto)" . $ffecha . ">0) as impor_total
    from productos p, producto_sucursal ps
    where ps.pkProducto = p.pkProducto and ps.pkSucursal = '" . $sucursal . "' and
    ((Select count(dp.pkDetallePedido) as cnt from detallepedido dp, pedido p
    where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND p.estado <>3 AND dp.pkProducto = p.pkProducto" . $ffecha . ")>0) order by p.descripcion";
                        //echo $consulta;
                    } else {
                        $consulta = "Select p.pkProducto as pk, p.descripcion,
    (Select SUM(dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=5) AND
    (dp.pkProducto =  p.pkProducto) AND p.pkProducto = '" . $_GET["producto"] . "'" . $ffecha . ">0) as cantidad_consumo,
    (Select SUM(dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=4) AND
    (dp.pkProducto =  p.pkProducto)AND p.pkProducto = '" . $_GET["producto"] . "'" . $ffecha . ">0) as cantidad_credito,
    (Select SUM(dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=1) AND
    (dp.pkProducto =  p.pkProducto) AND p.pkProducto = '" . $_GET["producto"] . "'" . $ffecha . ">0) as cantidad_venta,
    (Select SUM(dp.precio*dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=5) AND
    (dp.pkProducto =  p.pkProducto) AND p.pkProducto = '" . $_GET["producto"] . "'" . $ffecha . ">0) as importe_consumo,
    (Select SUM(dp.precio*dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=4) AND
    (dp.pkProducto =  p.pkProducto) AND p.pkProducto = '" . $_GET["producto"] . "'" . $ffecha . ">0) as importe_credito,
    (Select SUM(dp.precio*dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=1) AND
    (dp.pkProducto =  p.pkProducto) AND p.pkProducto = '" . $_GET["producto"] . "'" . $ffecha . ">0) as importe_venta,
    (Select SUM(dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=1 or p.estado=4 or p.estado=5) AND
    (dp.pkProducto =  p.pkProducto) AND p.pkProducto = '" . $_GET["producto"] . "'" . $ffecha . ">0) as cant_total,
    (Select SUM(dp.precio*dp.cantidad)  from detallepedido dp, pedido p where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND (p.estado=1 or p.estado=4 or p.estado=5) AND
    (dp.pkProducto =  p.pkProducto) AND p.pkProducto = '" . $_GET["producto"] . "'" . $ffecha . ">0) as impor_total
    from productos p, producto_sucursal ps
    where ps.pkProducto = p.pkProducto AND p.pkProducto = '" . $_GET["producto"] . "' and ps.pkSucursal = '" . $sucursal . "' and
    ((Select count(dp.pkDetallePedido) as cnt from detallepedido dp, pedido p
    where dp.pkPediido = p.pkPediido AND dp.estado<>3  AND p.estado <>3 AND dp.pkProducto = p.pkProducto AND p.pkProducto = '" . $_GET["producto"] . "'" . $ffecha . ")>0) order by p.descripcion";
                        //   echo $consulta;
                    }

                    break;
            }
        }

        $result = mysql_query($consulta, Conectar::con());

        while ($reg = mysql_fetch_assoc($result)) {
            $this->datos[] = $reg;
        }
        return $this->datos;
    }

    public function ReportesalidaProductosPorMes($mes, $anio, $valorProducto, $Idcategoria, $tipo, $sucursal) {
        //       $db = new SuperDataBase();
        $query = "CALL sp_get_product_sale_porMes($mes,$anio,$valorProducto,$Idcategoria,$tipo,'$sucursal')";
        //       $resul = $db->executeQuery($query);
        $result = mysql_query($query, Conectar::con());

        while ($reg = mysql_fetch_assoc($result)) {
            $this->datos[] = $reg;
        }
        return $this->datos;
    }

    // reportes en excel
    public function Reportelistfacturas($dateGo, $dateEnd, $sucursal) {

        $query = "CALL sp_listadoFacturas('$dateGo','$dateEnd','$sucursal');";
        $result = mysql_query($query, Conectar::con());

        while ($reg = mysql_fetch_assoc($result)) {
            $this->datos[] = $reg;
        }
        return $this->datos;
    }

    public function ReporteventasDiarias($fechaInicio, $fechaFin, $caja) {
        
        $query = "SELECT *,time(p.fechaApertura) as horaEntrada,SEC_TO_TIME(TIMESTAMPDIFF(SECOND, p.fechaApertura ,p.fechaFin)) as tiempoEstadia from pedido p, mesas m, accion_caja ac where p.pkMesa = m.PkMesa AND ac.pk_accion = p.pkPediido AND ac.tipo_accion = 'PED' AND ac.caja = '$caja' AND date(p.fechaCierre) between '$fechaInicio' and '$fechaFin' and p.estado=1";
        
        //echo $query;
        
        $result = mysql_query($query, Conectar::con());
        while ($reg = mysql_fetch_assoc($result)) {
            $this->datos[] = $reg;
        }

        return $this->datos;
    }

    public function VentasMozo($fechaInicio, $fechaFin, $IDTipoTrabajador) {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "select t.documento, t.nombres, t.apellidos,count(DISTINCT p.pkPediido) as total_Pedidos , sum(dp.cantidad*dp.precio) as total from pedido p inner join detallepedido dp on p.pkpediido=dp.pkpediido inner join trabajador t on dp.pkMozo=t.pktrabajador where p.fechaCierre BETWEEN '$fechaInicio' AND '$fechaFin' AND p.estado <> 3 and dp.estado > 0 and dp.estado <3 group by t.documento order by 5 desc";
        $resul = $db->executeQuery($query);
        $array = array();
//       echo $query;
        while ($row = $db->fecth_array($resul)) {
            $array[] = array(
                "DniTrabajador" => $row['documento'],
                "nombres" => $row['nombres'],
                "apellidos" => $row['apellidos'],
                "NumeroVentas" => $row['total_Pedidos'],
                "VentasTotales" => $row['total'],
//                "total_efectivo" => $row['total_Efectivo'],
//                "VentasTotales" => $row['Importe_total'],
            );
        }

        echo json_encode($array);
    }

    public function ReporteVentasporTrabajador($fechaInicio, $fechaFin, $IDTipoTrabajador, $IDsucursal) {
        $db = new SuperDataBase();

        if ($IDTipoTrabajador != '0') {
            $query = "SELECT  t.documento, t.nombres, t.apellidos,count(p.pkPediido) as total_Pedidos , sum(cantidad*precio) as total
                             FROM detallepedido d inner join pedido p on p.pkPediido=d.pkPediido
                             inner join trabajador t on pkMozo=t.pkTrabajador
                             where date(p.fechaCierre) BETWEEN '$fechaInicio' AND '$fechaFin' and t.pkSucursal='$IDsucursal' and pkTipoTrabajador=$IDTipoTrabajador and p.estado=1 and d.estado<>3
                             group by t.documento order by total desc";
        } else {
            $query = "SELECT  t.documento, t.nombres, t.apellidos,count(p.pkPediido) as total_Pedidos , sum(cantidad*precio) as total
                             FROM detallepedido d inner join pedido p on p.pkPediido=d.pkPediido
                             inner join trabajador t on pkMozo=t.pkTrabajador
                             where date(p.fechaCierre) BETWEEN '$fechaInicio' AND '$fechaFin' and t.pkSucursal='$IDsucursal' and p.estado=1 and d.estado<>3
                             group by t.documento order by total desc";
        }


        $result = $db->executeQuery($query);
//        $result = mysql_query($query, Conectar::con());

        while ($reg = $db->fecth_array($result)) {
            $this->datos[] = $reg;
        }
        return $this->datos;
    }

    public function ListaTopVentas($fechaInicio, $fechaFin, $Tipo, $Top) {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "";
        if(intval($Tipo)>0){
            $query = "select pl.descripcion as pedido,sum(cantidad) as cantidad,precio, sum(cantidad*precio) as total from pedido p inner join detallepedido dp on p.pkPediido=dp.pkPediido inner join plato pl on pl.pkPlato=dp.pkPlato inner join plato_sucursal ps on pl.pkPlato=ps.pkPlato where p.estado=1 and dp.estado<>3 and pl.pktipo = '".$Tipo."'and date(p.fechaCierre) BETWEEN '".$fechaInicio."' AND '".$fechaFin."' group by pedido order by total desc,cantidad desc limit ".$Top."";
        }else{
            $query = "select pl.descripcion as pedido,sum(cantidad) as cantidad,precio, sum(cantidad*precio) as total from pedido p inner join detallepedido dp on p.pkPediido=dp.pkPediido inner join plato pl on pl.pkPlato=dp.pkPlato inner join plato_sucursal ps on pl.pkPlato=ps.pkPlato where p.estado=1 and dp.estado<>3 and date(p.fechaCierre) BETWEEN '".$fechaInicio."' AND '".$fechaFin."' group by pedido order by total desc,cantidad desc limit ".$Top."";  
        }
        $resul = $db->executeQuery($query);
        $array = array();
        //echo $query;
        while ($row = $db->fecth_array($resul)) {
            $array[] = array(
                "Pedido" => utf8_encode($row['pedido']),
                "Cantidad" => utf8_encode($row['cantidad']),
                "Precio" => utf8_encode($row['precio']),
                "Total" => $row['total']
            );
        }

        echo json_encode($array);
    }

    public function ListadoSalidaInsumosPorDia($fecha) {
        $db = new SuperDataBase();
        $query = "select im.pkinsumo, i.descripcioninsumo,
round(ifnull(sum(im.cantidadTotal*(select sum(d.cantidad) from
(detallepedido d inner join (pedido p inner join mesas m on m.pkMesa=p.pkMesa) on p.pkPediido=d.pkPediido)
where d.pkplato!='' and p.estado<>3   and d.pkplato=im.pkplato and date(fechaCierre)='$fecha')),0),2) as Total,
u.descripcion
from  insumos i,insumo_menu im, unidad u
where i.pkinsumo=im.pkinsumo and i.pkunidad=u.pkunidad and pkTipoPedido=0
group by im.pkinsumo union
(select im.pkinsumo, i.descripcioninsumo,
round(ifnull(sum(im.cantidadTotal*(select sum(d.cantidad) from
(detallepedido d inner join (pedido p inner join mesas m on m.pkMesa=p.pkMesa) on p.pkPediido=d.pkPediido)
where d.pkplato!='' and (tipoPedido=1 or pkSalon=43 or pkSalon=44) and p.estado<>3   and d.pkplato=im.pkplato and date(fechaCierre)='$fecha')),0),2) as Total,
u.descripcion
from  insumos i,insumo_menu im, unidad u
where i.pkinsumo=im.pkinsumo and i.pkunidad=u.pkunidad and pkTipoPedido=1
group by im.pkinsumo);";
//       die($query);
        $result = $db->executeQuery($query);
        $array = array();
        while ($row = $db->fecth_array($result)) {
            $array[] = array(
                "id" => $row[0],
                "descripcioninsumo" => utf8_encode($row['descripcioninsumo']),
                "Total" => utf8_encode($row['Total']),
                "descripcion" => utf8_encode($row['descripcion']),
            );
        }
        echo json_encode($array);
    }

    public function ListadoSalidaKardex($fecha) {
        $db = new SuperDataBase();
        $query = "select i.cantidad,im.pkinsumo, i.descripcioninsumo,
round(ifnull(sum(im.cantidadTotal*(select sum(d.cantidad) from
(detallepedido d inner join (pedido p inner join mesas m on m.pkMesa=p.pkMesa) on p.pkPediido=d.pkPediido)
where d.pkplato!='' and p.estado<>3   and d.pkplato=im.pkplato and date(fechaCierre)='$fecha')),0),2) as Total,
u.descripcion
from  insumos i,insumo_menu im, unidad u
where i.pkinsumo=im.pkinsumo and i.pkunidad=u.pkunidad and pkTipoPedido=0
group by im.pkinsumo union
(select i.cantidad,im.pkinsumo, i.descripcioninsumo,
round(ifnull(sum(im.cantidadTotal*(select sum(d.cantidad) from
(detallepedido d inner join (pedido p inner join mesas m on m.pkMesa=p.pkMesa) on p.pkPediido=d.pkPediido)
where d.pkplato!='' and tipoPedido=1 and p.estado<>3   and d.pkplato=im.pkplato and date(fechaCierre)='$fecha')),0),2) as Total,
u.descripcion
from  insumos i,insumo_menu im, unidad u
where i.pkinsumo=im.pkinsumo and i.pkunidad=u.pkunidad and pkTipoPedido=1
group by im.pkinsumo);";
//       die($query);
        $result = $db->executeQuery($query);
        $array = array();
        while ($row = $db->fecth_array($result)) {
            $array[] = array(
                "id" => $row[0],
                "descripcioninsumo" => utf8_encode($row['descripcioninsumo']),
                "cantidad" => (int) $row['cantidad'],
                "Total" => utf8_encode($row['Total']),
                "descripcion" => utf8_encode($row['descripcion']),
            );
        }
        echo json_encode($array);
    }

    public function ReporteVentaDiaria($fechaInicio, $fechaFin, $estado) {
        $consulta = "SELECT p.pkPediido,m.nmesa,SEC_TO_TIME(TIMESTAMPDIFF(SECOND, p.fechaApertura ,p.fechaFin)) as tiempoEstadia,
                    p.tipo_pago,ifnull(p.nombreTarjeta,'') as nombreTarjeta,
                    IFNULL(p.total_efectivo,0.00) as total_efectivo,
                    IFNULL(p.total_tarjeta,0.00) as total_tarjeta,
                    p.descuento,
                    (select total_efectivo + total_tarjeta + descuento) as total
                    FROM pedido p
                    inner join (mesas m inner join salon sa on sa.pkSalon=m.pkSalon) on m.pkMesa = p.pkMesa
                    where date(fechaCierre) between '$fechaInicio' and '$fechaFin' and p.estado=$estado;";
        $result = mysql_query($consulta, Conectar::con());

        while ($reg = mysql_fetch_assoc($result)) {
            $this->datos[] = $reg;
        }
        return $this->datos;
    }

    public function ReporteCierreDiario($fecha, $sucursal) {
        //       $db = new SuperDataBase();
        $query = "SELECT
                 IFNULL((SELECT SUM(cantidad ) FROM gastos_diarios WHERE fecha='$fecha'  AND pkSucursal='$sucursal' AND (estado=0 OR estado=2 OR estado=3 OR estado=4)),0.00) AS total_gastado,
                 IFNULL((SELECT cantidad FROM monto_inicial m WHERE fecha='$fecha' AND pkSucursal='$sucursal'),0.00) AS inicial,
                 IFNULL((SELECT SUM(cantidad ) FROM gastos_diarios WHERE fecha='$fecha'  AND pkSucursal='$sucursal' AND estado=1),0.00) AS total_ingresado,
                 IFNULL((SELECT SUM(total_efectivo) FROM pedido p WHERE fechaCierre='$fecha' AND p.estado=1),0.00) AS total_vendidoET,
                 IFNULL((SELECT SUM(total_tarjeta) FROM pedido p WHERE fechaCierre='$fecha' AND nombreTarjeta='VISA' AND p.estado=1),0.00) AS visa,
                 IFNULL((SELECT SUM(total_tarjeta) FROM pedido p WHERE fechaCierre='$fecha' AND nombreTarjeta='MASTERCARD' AND p.estado=1),0.00) as mastercard,
                 IFNULL((Select visa + mastercard),0.00)AS total_vendidot,
                 IFNULL((Select total_vendidoET + total_vendidot),0.00)AS total_V;";
        //       $resul = $db->executeQuery($query);
        $result = mysql_query($query, Conectar::con());

        while ($reg = mysql_fetch_assoc($result)) {
            $this->datos[] = $reg;
        }
        return $this->datos;
    }

    public function ReporteVentasDetallePDF($nsucursal, $id) {
        $db = new SuperDataBase();
        $query = "select descuento as descu,dp.pkdetallepedido, pl.descripcion,date(p.fechaApertura) as fecha,case when tipo_cliente=1 then (select concat(nombres,' ',lastName ) from person where documento=p.documento) 
                        else (select razonSocial from persona_juridica where ruc=p.documento ) end as cliente,CONCAT(t.apellidos,', ',t.nombres) as mozo,nmesa,dp.precio,dp.cantidad,dp.precio*dp.cantidad as total from pedido p
                        inner join mesas m on p.pkmesa=m.pkmesa inner join detallepedido dp on p.pkpediido=dp.pkpediido inner join plato pl on dp.pkplato=pl.pkplato
                        inner join trabajador t on dp.pkMozo=t.pktrabajador
                        where p.pkPediido=$id and t.pkSucursal='$nsucursal' 
                        union 
                        select descuento as descu,dp.pkdetallepedido, pr.descripcion,date(p.fechaApertura) as fecha,case when tipo_cliente=1 then (select concat(nombres,' ',lastName ) from person where documento=p.documento) 
                        else (select razonSocial from persona_juridica where ruc=p.documento ) end as cliente,CONCAT(t.apellidos,', ',t.nombres) as mozo,nmesa,dp.precio,dp.cantidad,dp.precio*dp.cantidad as total from pedido p
                        inner join mesas m on p.pkmesa=m.pkmesa inner join detallepedido dp on p.pkpediido=dp.pkpediido inner join productos pr on dp.pkproducto=pr.pkproducto
                        inner join trabajador t on dp.pkMozo=t.pktrabajador
                        where p.pkPediido=$id and t.pkSucursal='$nsucursal';";

        $result = $db->executeQuery($query);
//        $result = mysql_query($query, Conectar::con());

        while ($reg = $db->fecth_array($result)) {
            $this->datos[] = $reg;
        }
        return $this->datos;
    }

    public function ReportePedidosAnuladosPDF($fechainicio, $fechafin) {
        $db = new SuperDataBase();
        $query = "SELECT *, case  when character_length(pkProducto) <6 then ( select upper(descripcion) from plato pl
  where pl.pkPlato=d.pkPlato )
when character_length(pkPlato)<6 then (select upper(descripcion) from productos pr where pr.pkProducto=d.pkProducto )
 end as pedido, case  when length(pkCocinero)>0 then (select concat(nombres,' ', apellidos) from  trabajador t where t.pkTrabajador=pkCocinero) end cocinero,
case  when length(pkMozo)>0 then (select concat(nombres,' ', apellidos) from  trabajador t where t.pkTrabajador=pkMozo) end mozo 
FROM detallepedido d where estado =3 and date_format(fechaPedido,'%Y-%m-%d') between '$fechainicio' and '$fechafin';";
        $result = $db->executeQuery($query);
//        $result = mysql_query($query, Conectar::con());

        while ($reg = $db->fecth_array($result)) {
            $this->datos[] = $reg;
        }
        return $this->datos;
    }

    public function DetalleVentasTrabajadorPDF($dni, $inicio, $fin) {
//        $db = new SuperDataBase();
//        $sucursal=  UserLogin::get_pkSucursal();

        $query = "SELECT sum(d.cantidad) as total, p.descripcion as plato, t.nombres  from detallepedido d inner join plato p on d.pkPlato=p.pkPlato
                                        inner join trabajador t on d.pkMozo=pkTrabajador inner join pedido pe on d.pkPediido=pe.pkPediido
                                        where fechaCierre between'$inicio' and '$fin' and t.documento LIKE '%$dni%' and d.estado<>3 and pe.estado<>7
                                        group by p.descripcion
                                        union
                                        SELECT sum(d.cantidad) , p.descripcion, t.nombres  from detallepedido d inner join productos p on d.pkProducto=p.pkProducto
                                        inner join trabajador t on d.pkMozo=pkTrabajador inner join pedido pe on d.pkPediido=pe.pkPediido
                                        where fechaCierre between'$inicio' and '$fin' and t.documento LIKE '%$dni%' and d.estado<>3 
                                        and pe.estado<>7 group by p.descripcion;";

//          echo die($query);
        $result = mysql_query($query, Conectar::con());

        while ($reg = mysql_fetch_assoc($result)) {
            $this->datos[] = $reg;
        }
        return $this->datos;
    }

    public function ReportePagosFijosPDF($inicio, $fin) {
        $query = "SELECT pkGastosDiarios,cantidad,descripcion,dateModify,fecha FROM gastos_diarios g "
                . "where estado=3 and fecha between '$inicio' and '$fin' "
                . "and estado_anular=0;";

//          echo die($query);
        $result = mysql_query($query, Conectar::con());

        while ($reg = mysql_fetch_assoc($result)) {
            $this->datos[] = $reg;
        }
        return $this->datos;
    }

    public function ReporteGastosDiariosPDF($fechaInicio, $fechaFin) {

        $query = "SELECT pkGastosDiarios,cantidad,descripcion,dateModify,fecha FROM gastos_diarios g where estado=0 and fecha between '$fechaInicio' and '$fechaFin'";
        $result = mysql_query($query, Conectar::con());

        while ($reg = mysql_fetch_assoc($result)) {
            $this->datos[] = $reg;
        }
        return $this->datos;
    }

    public function ReportePagosPlanillasPDF($fechainicio, $fechafin) {
        $query = "SELECT pkGastosDiarios,cantidad,descripcion,dateModify,fecha FROM gastos_diarios g "
                . "where estado=2 and fecha between '$fechainicio' and '$fechafin' "
                . "and estado_anular=0;";
        $result = mysql_query($query, Conectar::con());

        while ($reg = mysql_fetch_assoc($result)) {
            $this->datos[] = $reg;
        }
        return $this->datos;
    }

    public function ReportePagosAnuladosPDF($fechainicio, $fechafin, $tipo) {

        $query = "SELECT pkGastosDiarios,cantidad,descripcion,dateModify,fecha FROM gastos_diarios g "
                . "where estado=$tipo and fecha between '$fechainicio' and '$fechafin' "
                . "and estado_anular=1;";
        $result = mysql_query($query, Conectar::con());

        while ($reg = mysql_fetch_assoc($result)) {
            $this->datos[] = $reg;
        }
        return $this->datos;
    }

}
