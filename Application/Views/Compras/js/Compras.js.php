<?php require_once '../../../../Components/Config.inc.php'; ?>
//<script>

$(document).ready(function () {
    // $('#tblCompras').DataTable({
    //     // "ordering": false,
    //     // "bSort": false,
    // });

    $("#fecha_inicio").datepicker({dateFormat: 'yy-mm-dd'});
    $("#fecha_fin").datepicker({dateFormat: 'yy-mm-dd'});

    // LoadDocumentos();
    LoadProveedores();
});

function Filtrar() {

    console.log("<?php echo Class_config::get('urlApp') ?>/?controller=Compras&action=Show&" + $('#frmFiltro').serialize())

    window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Compras&action=Show&" + $('#frmFiltro').serialize();
}

function openModal (type, data) {
    switch (type) {

        case 0:
            $('#modalForm').modal('show')
            break;
        case 1:
            $('#modalFormEdit').modal('show')

            $('#lblEditModal').html(data.serie + " - " + data.correlativo)

            $('#compraID').val(data.id)

            $('#cmbDocumentoE').val(data.tipo_documento_id)
            $('#serieE').val(data.serie)
            $('#correlativoE').val(data.correlativo)
            $('#fechaE').val(data.fecha)
            $('#observacionesE').val(data.observaciones)
            
            $('#cmbProveedorE').val(data.proveedor_id)
            $('#cmbProveedorE').trigger('change')

            break;
    }
}

function LoadProveedores() {

    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Provedor&&action=List",
        dataType: "json",
        success: function (data) {

            var selected_proveedor = $('#cmbProveedor');
            var selected_proveedorE = $('#cmbProveedorE');

            var options = [];

            options.push(`
                    <option value="">
                        SIN PROVEEDOR
                    </option>
                `);

            for (let item of data) {
                options.push(`
                    <option value="${item.id}">
                        RUC: <strong>${item.ruc}</strong>
                        - 
                        <div>"${item.descripcion}"</div>
                    </option>
                `);
            }

            selected_proveedor.html(options.join(' '));
            selected_proveedorE.html(options.join(' '));

            selected_proveedor.select2({
                width: '100%',
                dropdownParent: $('#modalForm')
            });
            selected_proveedorE.select2({
                width: '100%',
                dropdownParent: $('#modalFormEdit')
            });
        }
    })
}

function NuevoProveedor() {
    window.open('<?php echo Class_config::get('urlApp') ?>/?controller=Almacen&action=ShowAdminProveedor','_blank');
}

function guardarCompra() {

    let data = $('#frmCompra').serialize();

    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Compras&&action=Add",
        dataType: "json",
        method: 'POST',
        data: data,
        success: function (data) {
            
            if (data.ok) {
                alert("Compra registrada correctamente.");
                goDetail(data.id);
            } else {
                alert(data.message);
            }
        }
    })
}

function editarCompra() {

    let data = $('#frmCompraEdit').serialize();

    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Compras&&action=Edit",
        dataType: "json",
        method: 'POST',
        data: data,
        success: function (data) {
            
            console.log(data)

            if (data.ok) {
                alert("Compra actualizada correctamente.");
                location.reload();
            } else {
                alert(data.message);
            }
        }
    })
}

function deleteCompra(id) {
    if (!confirm('¿Está seguro que desea eliminar este registro?')) return;

    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Compras&&action=Delete",
        dataType: "json",
        method: 'POST',
        data: {id: id},
        success: function (data) {
            if (data.ok) {
                alert('Compra eliminada correctamente');
                location.reload();
            } else {
                alert(data.message);
            }
        }
    })
}

function goDetail(id) {

    window.open('<?php echo Class_config::get('urlApp') ?>/?controller=Compras&action=ShowDetails&Id=' + id, '_self');
}

function LoadCuotas(compra) {
    $('#modalFormPagos').modal('show')

    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Compras&&action=GetCuotas",
        dataType: "json",
        data: {id: compra.id},
        success: function (data) {

            let lista = data.lista;

            let html = "";

            for (let item of lista) {

                html += `
                    <tr>
                        <td>${item.fecha}</td>
                        <td class="text-right">${item.moneda}</td>
                        <td class="text-right">${parseFloat(item.total)}</td>
                        <td class="text-center" style="vertical-align: middle">
                            ${item.fecha_caja ? '<label class="label label-success">Pagado</label>' : '<label class="label label-danger">Por Pagar</label>'}
                        </td>
                        <td class="text-center">
                            ${item.fecha_caja ? 
                                `` : 
                                `<a class="btn text-success" onclick="ingresarCaja(${item.id})">
                                    <i class="fa fa-check-circle"></i>
                                    Ingresar
                                </a>`}
                            
                        </td>
                    </tr>
                `;
            }

            $('#tblBodyCuotas').html(html);
        }
    })
}

function ingresarCaja(id) {

    let caja = $('#cajaCaja').val();
    let medio_pago = $('#cajaMedioPago').val();

    if (!caja || !medio_pago) {
        return alert('Debe seleccionar la Caja y el Medio de Pago con el que se registrará la salida de Caja.');
    }

    if (!confirm('¿Está seguro que desea registrar la salida de caja?')) return

    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=ComprasCaja&&action=AddCuotaCaja",
        dataType: "json",
        data: {id, caja, medio_pago},
        success: function (data) {

            if (data.ok) {
                alert('Salida de Caja registrado correctamente.');
                location.reload();
            } else {
                alert(data.message);
            }
        }
    })
}
