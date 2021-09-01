<?php require_once '../../../../Components/Config.inc.php'; ?>

//<script>
    function buscarCuentasPorPagar()
    {
                window.location.href="<?php echo Class_config::get('urlApp') ?>/?controller=Sale&action=CPendientes&" +$('#frmCuentasPendientes').serialize();
                 
    }