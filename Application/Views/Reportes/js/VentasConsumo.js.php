<?php require_once '../../../../Components/Config.inc.php'; ?>

//<script>
    
    function buscarVentasConsumo()
    {
        window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Report&action=SaleConsumo&" + $('#frmBus').serialize();        
    }
    
    function verDetallesVentas($pkpedido,$estado,$fecha1,$fecha2,$sucursal)
    {
        window.open('<?php echo Class_config::get('urlApp') ?>/?controller=Report&action=ShowAdminDetalleVentas&id='+$pkpedido+'&f1='+$fecha1+'&f2='+$fecha2+'&estado='+$estado+'&nsucursal='+$sucursal,'_blank');
    }    
     
    function ventasdiariasPDF() {
        var url = "<?php echo Class_config::get('urlApp') ?>/?controller=Report&action=ReportePDFVentasDia&fechaInicio=" + $("#txtfechainicio").val() + 
            "&fechaFin=" + $("#txtfechafin").val() + "&caja="+$("#caja").val();
        window.open(url, '_blank');
    }
    
 function EliminaVentaDia(id) {
        var r = confirm("¿Deseas eliminar esta venta?");
        if (r) {
            $.post('<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&action=AnulaPedido', {id:id}, function() {
                location.reload();
            }, 'html');
        } 
    }