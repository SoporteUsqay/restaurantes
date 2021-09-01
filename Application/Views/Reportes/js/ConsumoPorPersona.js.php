<?php require_once '../../../../Components/Config.inc.php'; ?>

//<script>
    function buscar2() {
        window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Report&action=showConsumoxPersona&fecha_inicio=" + $('#txtfechaini').val() + 
            "&fecha_fin="+ $('#txtfechafin').val();
    }