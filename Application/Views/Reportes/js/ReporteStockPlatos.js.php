<?php require_once '../../../../Components/Config.inc.php'; ?>

//<script>
    $(document).ready(function () {
        $("#txtFecha").datepicker({dateFormat: 'yy-mm-dd'});
         $("#tblReporteStockPlatoa").DataTable();
    });