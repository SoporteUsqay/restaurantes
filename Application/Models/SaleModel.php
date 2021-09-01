<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Models_SaleModel {

    private $dateGO;
    private $dateEnd;

    function __construct() {
        
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

    public function _listTotalBoletaBetwenDate() {
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

    /**
     * Tota de ventas por fecha
     */
    public function _listTotalBoletaDate() {
        $db = new SuperDataBase();
        $query = "CALL sp_get_report_sale_pordia('$this->dateGO')";
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
     * Tota de ventas por mes
     * Desarrollado por Ing Jeanmarco Leon
     */
    public function _listTotalBoletaMonth() {
        $db = new SuperDataBase();
        $query = "CALL sp_get_total_ventas_mes('$this->dateGO')";
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
     * Tota de ventas entre dos fechas
     */
    public function _listTotalBoleta2Date() {
        $db = new SuperDataBase();
        $pkEmpresa = UserLogin::get_pkSucursal();
        $query = "CALL sp_get_listboleta('$this->dateGO','$this->dateEnd','$pkEmpresa')";
        $resul = $db->executeQuery($query);

        $array = array();
        $total = 0;

        while ($row = $db->fecth_array($resul)) {
            $array[] = array("pkComprobante" => $row['ncomprobante'],
                "total" => $row['total'],
                "ruc" => $row['ruc'],
                "subTotal" => $row['subTotal'],
                "impuesto" => $row['impuesto'],
                "totalEfectivo" => $row['totalEfectivo'],
                "totalTarjeta" => $row['totalTarjeta'],
                "nombreTarjeta" => $row['nombreTarjeta'],
                "fecha" => $row['fecha'],
                "pkCajero" => $row['pkCajero'],
                "descuento" => $row['descuento'],
                "pkCliente" => $row['pkCliente'],
                "Nombre_Trabajador" => $row['Nombre_Trabajador'],
            );
//            $total=$total+$row['total_venta'];
        }
//        $array[]=  array('Total')
//        echo $query;
        echo json_encode($array);
    }

    /**
     * Cuentas por pagar
     */
    public function _listCuentasPorPagar($fecha1, $fecha2) {
        $db = new SuperDataBase();
        $pkEmpresa = UserLogin::get_pkSucursal();
        $query = "SELECT *,case when tipo_cliente=1 then (select concat(nombres,' ',lastName ) from person where documento=p.documento)
else (select razonSocial from persona_juridica where ruc=p.documento ) end as cliente FROM pedido p
inner join detallepedido d on p.pkPediido=d.pkPediido
inner join (trabajador t
inner join person pe on pe.documento=t.documento) on d.pkMozo=t.pkTrabajador
inner join mesas m on m.pkMesa=p.pkMesa where p.estado=4 and DATE_FORMAT(fechaApertura,'%Y-%m-%d') between '$fecha1' and '$fecha2';";
//  echo $query;     
$resul = $db->executeQuery($query);

        $array = array();
        $total = 0;
//die($query);
        while ($row = $db->fecth_array($resul)) {
            $array[] = array("pkPedido" => $row[0],
                "total" => $row['total'],
                "pkMesa" => $row['pkMesa'],
                "mesa" => $row['nmesa'],
                "fecha" => $row['fechaApertura'],
                "cliente" => $row['cliente'],
                "nombres" => $row['nombres'].' '.$row['lastName'],
            );
//            $total=$total+$row['total_venta'];
        }
//        $array[]=  array('Total')
//        echo $query;
        die($query);
        echo json_encode($array);
    }

    /**
     * Salida de Productos por dia
     * 
     */
  public function guardarPromocion($descripcion,$total) {
        $db = new SuperDataBase();
        $sucursal= UserLogin::get_pkSucursal();
//        id, descripcion, fechaiInicio, fechaFin, estado, total, tipo_promocion
        $query = "CALL sp_addPromocion('$descripcion',146,$total,'$sucursal',@sa)";
        $db->executeQuery($query);
        echo $query." ". $db->getId();
    }
    public function guardarDetallePromocion($codigo,$cantidad,$pkPromocion) {
        $db = new SuperDataBase();
        $query = "CALL sp_addDetallePromocion('$codigo',$cantidad,'$pkPromocion')";
        $db->executeQuery($query);
        echo $query;
    }

}
