<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Views_CajaView {

    function __construct() {
        
    }

    public function showMontoInicial() {

        require_once 'Caja/MontoInicial.php';
    }

    public function showCierreDiario() {

        require_once 'Caja/CierreDiario.php';
    }
    
    public function showSaleCaja() {
         require_once 'Reportes/VentasConsumo.php';
    }

    public function showRegistrarPago() {

        require_once 'Caja/RegistrarPago.php';
    }

    public function showReportePagosDiarios() {

        require_once 'Caja/ReportePagosDiarios.php';
    }

    public function showIngresoDinero() {

        require_once 'Caja/IngresoDinero.php';
    }

    public function showOpenVenta() {

        require_once 'Caja/IniciarDia.php';
    }

    public function showConfirmCierre() {
        require_once 'Caja/ConsultCierreDiario.php';
    }

    public function showGastosDiarios() {
        require_once 'Caja/ReportePagosDiarios.php';
    }

    public function showPagosFijos() {
        require_once 'Caja/ReportePagosFijos.php';
    }

    public function VerPagoPlanilla() {
        require_once 'Caja/ReportePagoPlanillas.php';
    }

    public function VerPagosAnulados() {
        require_once 'Caja/ReportePagosAnulados.php';
    }

}

?>
