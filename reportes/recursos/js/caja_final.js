function verDetallesVenta(idVenta) {

    console.log(idVenta);
    $.post("../?controller=Pedidos&&action=ListDetalle", { id: idVenta }, function(data) {
        $('#tbl-ventas > tbody').html("");
        console.log(data);
        html = "";
        $.each(data, function(index, value) {
            const subtotal = Number(value['cantidad']) * Number(value['precio']);
            html += "<tr>";
            html += "<td>" + value['descripcion'] + "</td>";
            html += "<td>" + value['cantidad'] + "</td>";
            html += "<td>" + value['precio'] + "</td>";
            html += "<td>" + subtotal.toFixed(2) + "</td>";
            html += "</tr>";
        });
        $('#tbl-ventas > tbody').html(html);
        $('#myModalD').modal();
    }, 'json');
}

function verDetalleComprobante(event, serie) {
    event.preventDefault();

    $.post("../?controller=Comprobante&&action=DetalleComprobante", { serie: serie }, function(data) {
        console.log(data);
        if (data.length > 0) {
            $('#tipoComprobante').text(data[0]['pkTipoComprobante'] ? data[0]['pkTipoComprobante'] : '');
            $('#estadoComprobante').text(data[0]['estado'] ? data[0]['estado'] : '');
            $('#totalComprobante').text(data[0]['total'] ? data[0]['total'] : '');
            $('#subTotalComprobante').text(data[0]['subTotal'] ? data[0]['subTotal'] : '');
            $('#impuestoComprobante').text(data[0]['impuesto'] ? data[0]['impuesto'] : '');
            $('#tipoPagoComprobante').text(data[0]['tipo_pago'] ? data[0]['tipo_pago'] : '');
            $('#totalEfectivoComprobante').text(data[0]['totalEfectivo'] ? data[0]['totalEfectivo'] : '');
            $('#totalTarjetaComprobante').text(data[0]['totalTarjeta'] ? data[0]['totalTarjeta'] : '');
            $('#nombreTarjetaComprobante').text(data[0]['nombreTarjeta'] ? data[0]['nombreTarjeta'] : '');
            $('#fechaImpresionComprobante').text(data[0]['fechaImpresion'] ? data[0]['fechaImpresion'] : '');
            $('#fechaComprobante').text(data[0]['fecha'] ? data[0]['fecha'] : '');
            $('#cajeroComprobante').text(data[0]['pkCajero'] ? data[0]['pkCajero'] : '');
            $('#descuentoComprobante').text(data[0]['descuento'] ? data[0]['descuento'] : '');
            $('#clienteComprobante').text(data[0]['pkCliente'] ? data[0]['pkCliente'] : '');
            $('#UsuarioComprobante').text(data[0]['idUser'] ? data[0]['idUser'] : '');
            $('#serirComprobante').text(data[0]['ncomprobante'] ? data[0]['ncomprobante'] : '');
            $('#rucComprobante').text(data[0]['ruc'] ? data[0]['ruc'] : '');
            $('#DocumentoComprobante').text(data[0]['documento'] ? data[0]['documento'] : '');
            $('#myModalC').modal();
        }

    }, 'json');

}

function reporteGastos(nameCaja, fechaIncio, fechaFin) {
    $.post("../?controller=GastosDiarios&&action=EgresosReporte", { nameCaja: nameCaja, fechaIncio: fechaIncio, fechaFin: fechaFin }, function(response) {
        let t = 0;


        $(function() {
            $("#table").bootstrapTable({ data: response });
            $("#table").bootstrapTable('hideColumn', 'id');
            $("#table").bootstrapTable('hideColumn', 'pkUser');
            $("#table").bootstrapTable('hideColumn', 'tipo');
        });

        $.each(response, function(indexInArray, valueOfElement) {
            t += Number(valueOfElement['cantidad']);
        });
        let html = `<b>Total: </b> S/.${t.toFixed(2)}`;
        $('#egresos-parrafo').html(html);
    }, 'json');
}

function runningFormatter(value, row, index) {
    return index + 1;
}