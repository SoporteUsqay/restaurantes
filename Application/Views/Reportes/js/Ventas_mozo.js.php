<?php
require_once '../../../../Components/Config.inc.php';
$sucursal = UserLogin::get_pkSucursal();
?>
//<script>
    function getpkSucursal() {
        return "<?php echo $sucursal ?>";
    }

    $(document).ready(function()
    {
//        _listMozos('cmb_mozo');
        _listComboTipoTrabajador('cmb_TipoTrabajador');
    });

    function _listComboTipoTrabajador($id) {
        $('#' + $id + ' option').remove();
        $('#' + $id).append("<option value=\"0\">Todos</option>")
        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Mozo&action=ListtipoTrabajador', function(data) {

            for (var i = 0; i < data.length; i++) {

                $('#' + $id).append("<option value=\"" + data[i].idTipoTrabajador + "\">" + data[i].descripcionTipoTrabajador + "</option>")
//            $('#txtModifyUserType').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
//            $('#cmbWorkStationModifyPermissions').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
            }

        });
    }

    function loadTableVentasMozo() {
        var params = {dateInicio: $("#FInicioventasTrabajador").val(),
            dateFin: $("#FFinventasTrabajador").val(),
            PkTipoTrabajador: $("#cmb_TipoTrabajador").val()}
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Report&&action=ListVentasMozo", /* Llamamos a tu archivo */
            data: params, /* Ponemos los parametros de ser necesarios */
            type: "POST",
            dataType: "json", /* Esto es lo que indica que la respuesta será un objeto JSon */
            success: function(data) {
                /* Supongamos que #contenido es el tbody de tu tabla */
                /* Inicializamos tu tabla */
                $("#tblReporVentasMozo tbody").empty();
                /* Vemos que la respuesta no este vacía y sea una arreglo */
//                if (data != null && $.isArray(data)) {
                /* Recorremos tu respuesta con each */
                var contador = 0;
                var totalVendido = 0;
                $.each(data, function(index, value) {
                    /* Vamos agregando a nuestra tabla las filas necesarias */
                    $("#tblReporVentasMozo tbody").append("<tr><td>" + value.DniTrabajador + "</td><td>" + value.nombres + " " + value.apellidos + "</td><td>" + value.NumeroVentas + "</td><td>" + value.VentasTotales + "</td><td>" + "<a onclick='modalVerDetalle(" + value.DniTrabajador + ",\"" + value.nombres + "\",\"" + value.apellidos + "\",\"" + $("#FInicioventasTrabajador").val() + "\",\"" + $("#FFinventasTrabajador").val() + "\")'><span class='glyphicon glyphicon-list' title='Ver Detalle de Ventas'></span></a>" + "</td></tr>");
                    contador++;
                    totalVendido = totalVendido + parseFloat(value.VentasTotales);
                });
//                console.log(totalVendido)
                $("#lblnPedidos3").html(contador);

                $("#lbltotalVentasTrabajador").html(totalVendido);
            }
        });
    }


    function exportarPDFVentasTrabajador() {
//             var $id =getpkSucursal();          
        var url = "<?php echo Class_config::get('urlApp') ?>/pdf_ventasTrabajador.php?DateInicio=" + $("#FInicioventasTrabajador").val() + "&DateFin=" + $("#FFinventasTrabajador").val() + "&pkTipotrabajador=" + $("#cmb_TipoTrabajador").val();
        window.open(url, '_blank');
    }

    function exportarEXCELVentasTrabajador() {
//             var $id =getpkSucursal();          
        var url = "<?php echo Class_config::get('urlApp') ?>/xls_ventasTrabajador.php?DateInicio=" + $("#FInicioventasTrabajador").val() + "&DateFin=" + $("#FFinventasTrabajador").val() + "&pkTipotrabajador=" + $("#cmb_TipoTrabajador").val() + "&sucursal=" + $("#lblsucursal").val();
        window.open(url, '_blank');
    }


    function generarGraficaVentasTrabajador() {
        var $id = getpkSucursal();
        var url = "<?php echo Class_config::get('urlApp') ?>/ReportesGraficos/GraficaBarrasVentasMozo.php?DateInicio=" + $("#FInicioventasTrabajador").val() + "&DateFin=" + $("#FFinventasTrabajador").val() + "&pkTipotrabajador=" + $("#cmb_TipoTrabajador").val() + "&Sucursal=" + $id;
        ;
        window.open(url, '_blank');
    }

    function modalVerDetalle($dni, $nombres, $apellidos, $fechainicio, $fechafin)
    {
        window.open('<?php echo Class_config::get('urlApp') ?>/?controller=Report&action=VerDetalleTrabajador&dni=' + $dni + '&nombres=' + $nombres + '&apellidos=' + $apellidos + '&inicio=' + $fechainicio + '&fin=' + $fechafin, '_self');
    }