<?php require_once '../../../../Components/Config.inc.php'; ?>

//<script>
    function buscar() {
        window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Report&action=showNKardex&fecha_inicio=" + $('#txtfechaini').val() + "&fecha_fin="+ $('#txtfechafin').val()+ "&tp="+$("#tipo_plato").val() + "&almacen="+$("#cmbAlmacen").val();
    }
    
    function KardexDetallado(pkInsumo, pkInsumoPorcion, nombreInsumo){

        let params = "";

        params += "&fecha_inicio=" + $('#txtfechaini').val();

        params += "&fecha_fin=" + $('#txtfechafin').val();

        params += "&txt_NomInsumo=" + nombreInsumo;

        params += "&txt_IDInsumo=" + pkInsumo;

        params += "&txt_IDInsumoPorcion=" + pkInsumoPorcion;

        params += "&txt_IDAlmacen=" + almacen_id;

        params += "&view=detail";

        var url = "<?php echo Class_config::get('urlApp') ?>/?controller=Report&action=showNKardexDetallado" + params;   
        window.open(url, '_blank');
    }

    function kardexInsumos() {
        var url = "<?php echo Class_config::get('urlApp') ?>/xls_kardex.php?fecha_inicio="+$("#txtfechaini").val()+"&fecha_fin="+ $('#txtfechafin').val();
        window.open(url, '_blank');
    }
