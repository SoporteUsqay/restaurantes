<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Views_ReportView {

    function __construct() {
        
    }

    public function showKardex() {
        require_once 'Reportes/NKardex.php';
    }

    public function showKardexDetallado() {
        require_once 'Reportes/NKardexDetallado.php';
    }
     public function showConsumoxPersona() {
        require_once 'Reportes/ConsumoPorPersona.php';
    }

    public function showKardexDetallado2() {
        require_once 'Reportes/NKardexDetallado2.php';
    }

    public function ShowSaleConsumo() {
        require_once 'Reportes/VentasConsumo.php';
    }
    
    public function ShowSaleCaja() {
        $es_caja = 1;
        require_once 'Reportes/VentasConsumo.php';
    }

    public function ShowDetalleVentasConsumo() {
        require_once 'Reportes/DetalleVentasConsumo.php';
    }

    public function showReporteStockPlatos() {
        require_once 'Reportes/ReporteStockPlatos.php';
    }

    public function showReportSaleBetweenDay() {
        require_once 'Reportes/ventasDia.php';
    }

    public function showReportComparativaDia() {
        require_once 'Reportes/ComparativaPorDia.php';
    }

    public function showReportSaleBetween2Day() {
        require_once 'Reportes/ventas_entre_fechas.php';
    }

    public function showReportOutProductoxMoth() {
        require_once 'Reportes/Salida_productos_xmes.php';
    }

    public function showReportOutProductoxDay() {
        require_once 'Reportes/Salida_productos_xdia.php';
    }

    public function showReportSaleMonth() {
        require_once 'Reportes/ventas_mes.php';
    }

    public function showReportSemanal_Mes_Anual() {
        require_once 'Reportes/Ventas_semanal_mes_anual.php';
    }

    public function showReportVentasMozo() {
        require_once 'Reportes/Ventas_mozo.php';
    }

    public function showSalidaInsumos() {
        require_once 'Reportes/consolidadoInsumos.php';
    }

    public function showProductosAnulados() {
        require_once 'Reportes/ReportePedidosAnulados.php';
    }

    public function verDetalleTrabajador() {
        require_once 'Reportes/DetalleTrabajadorVentas.php';
    }

}
