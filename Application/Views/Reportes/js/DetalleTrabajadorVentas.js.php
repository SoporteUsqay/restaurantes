<?php
require_once '../../../../Components/Config.inc.php';
?>
//<script>

    function exportarPDFVentasDetallesTrabajador($dni, $inicio, $fin, $nombres, $apellidos) {
        var url = "<?php echo Class_config::get('urlApp') ?>/pdf_DetalleVentasTrabajador.php?dni=" + $dni + "&fechainicio=" + $inicio + "&fechafin=" + $fin + "&nombres=" + $nombres + "&apellidos=" + $apellidos;
        window.open(url, '_blank');
    }

    function exportarExcelVentasDetallesTrabajador($dni, $inicio, $fin, $nombres, $apellidos) {
        var url = "<?php echo Class_config::get('urlApp') ?>/xls_DetalleVentasTrabajador.php?dni=" + $dni + "&fechainicio=" + $inicio + "&fechafin=" + $fin + "&nombres=" + $nombres + "&apellidos=" + $apellidos;
        window.open(url, '_blank');
    }