<?php require_once '../../../../Components/Config.inc.php'; ?>
//<script>

$(document).ready(function () {
    // $('#tblCompras').DataTable({
    //     // "ordering": false,
    //     "bSort": false,
    //     "pageLength" : 5,
    //     "lengthMenu": [[5, 10, 20, -1], [5, 10, 20, 'Todos']]
    // });
    // $('#tblDocs').DataTable({
    //     // "ordering": false,
    //     "bSort": false,
    // });

    LoadInsumos();
    LoadDescripciones();

    $('#cantidad').change(() => CalculateSubtotal());
    $('#precio').change(() => CalculateSubtotal());
    $('#descuento').change(() => CalculateSubtotal());

    $('#cantidadE').change(() => CalculateSubtotal('E'));
    $('#precioE').change(() => CalculateSubtotal('E'));
    $('#descuentoE').change(() => CalculateSubtotal('E'));

    $('#cmbDefinicion').select2({
        width: '100%',
        dropdownParent: $('#modalDetraccion')
    });

    $('#cmbDefinicion').on('select2:select', function (e) {
        var data = e.params.data;

        var porcentaje = lista_porcentajes_detraccion.find(it => it.id == data.id);

        if (!porcentaje) {
            $('#porcentajeDetraccion').val(0)
            return
        }

        $('#porcentajeDetraccion').val(parseFloat(porcentaje.porcentaje))
    });

    if (can_update) {
        openModal(0);
    }
});

function openModal (type, data) {
    switch (type) {

        case 0:
            $('#modalForm').modal('show')

            $('input:radio[name=tipo_concepto]').change(OnChangeTipoConcepto)

            OnChangeTipoConcepto();

            break;

        case 3:
            $('#modalDetraccion').modal('show')
            break;
        case 4:
            $('#modalRetencion').modal('show')
            break;
        case 5:
            $('#modalPercepcion').modal('show')
            break;
        case 6:
            $('#modalFormEdit').modal('show')

            $('#lblEditModal').html(data.insumo_nombre ? data.insumo_nombre : data.concepto_nombre)

            $('#detalleID').val(data.id)

            $('#cantidadE').val(parseFloat(data.cantidad))
            $('#precioE').val(parseFloat(data.precio))
            $('#descuentoE').val(parseFloat(data.descuento))

            CalculateSubtotal('E');

            break;
        case 7:
            $('#modalCaja').modal('show')

            $('input:radio[name=tipo_pago]').change(OnChangeTipoPago)

            OnChangeTipoPago();

            $('#pago_efectuado').change(OnChangePagoEfectuado)

            OnChangePagoEfectuado();

            fillTableCuotas();

            break;
        case 8:
            $('#modalAddConcepto').modal('show')
            break;
        case 9:
            $('#modalNewInsumo').modal('show')
            break;
    }
}

function OnChangeTipoPago() {
    let val = $('input:radio[name=tipo_pago]:checked').val();

    if (val == 1) {
        $('#pnl_unico').show();
        $('#pnl_cuotas').hide();
    } else if (val == 2) {
        $('#pnl_unico').hide();
        $('#pnl_cuotas').show();
    }
}

function OnChangeTipoConcepto() {
    let val = $('input:radio[name=tipo_concepto]:checked').val();

    if (val == 1) {
        $('#ctnInsumo').show();
        $('#ctnDescripcion').hide();

        setTimeout(() => {
            $('#cmbInsumo').select2('open');
        }, 500);
    } else if (val == 2) {
        $('#ctnInsumo').hide();
        $('#ctnDescripcion').show();

        setTimeout(() => {
            $('#cmbDescripcion').select2('open');
        }, 500);
    }
}

function OnChangePagoEfectuado() {
    let pago_efectuado = $('#pago_efectuado').is(":checked");

    $('#ctnPagoEfectuado1').hide();
    $('#ctnPagoEfectuado2').hide();
    $('#ctnPagoEfectuado3').hide();

    if (pago_efectuado) {
        $('#ctnPagoEfectuado1').show();
        $('#ctnPagoEfectuado2').show();
        $('#ctnPagoEfectuado3').show();
    } else {
        $('#ctnPagoEfectuado1').hide();
        $('#ctnPagoEfectuado2').hide();
        $('#ctnPagoEfectuado3').hide();
    }
}

function CalculateSubtotal(concat) {

    if (!concat) concat = '';

    let cantidad = $('#cantidad' + concat).val();
    let precio = $('#precio' + concat).val();
    let descuento = $('#descuento' + concat).val();

    let elSubtotal = $('#subtotal' + concat);
    let elTotal = $('#total' + concat);

    if (!cantidad || !precio) {
        elSubtotal.val(0);
        elTotal.val(0);
        return;
    }

    elSubtotal.val(cantidad * precio);

    if (!descuento) {
        elTotal.val(0);
        return;
    }

    elTotal.val((cantidad * precio) - descuento);
}

var lista_insumos = [];
var lista_conceptos = [];

function LoadInsumos() {

    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Insumo&&action=List",
        type: 'POST',
        dataType: 'json',
        success: function (data) {

            lista_insumos = data;

            var select_insumo = $('#cmbInsumo');

            select_insumo.html('<option value="">Seleccione</option>');

            for (let i of data) {
                select_insumo.append(`
                    <option value="${i.id}">${i.label}</option>
                `)
            }

            select_insumo.select2({
                width: '100%',
                dropdownParent: $('#modalForm')
            });

            select_insumo.on('select2:select', function (e) {
                var data = e.params.data;

                var insumo = lista_insumos.find(it => it.id == data.id);

                if (!insumo) {
                    $("#precio").val(0);
                } else {
                    $("#precio").val(parseFloat(insumo.price));
                }

                $("#cantidad").val(1);
                setTimeout(() => {
                    $("#cantidad").select();
                }, 200);

                CalculateSubtotal();
            });

            if ($('input:radio[name=tipo_concepto]:checked').val() == 1) {
                setTimeout(() => {
                    select_insumo.select2('open');
                }, 500);
            }
        }
    }); 
}

function LoadDescripciones(id) {

    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Compras&&action=GetConceptos",
        type: 'POST',
        dataType: 'json',
        success: function (data) {

            lista_conceptos = data;

            var select_descripcion = $('#cmbDescripcion');

            select_descripcion.html('<option value="">Seleccione</option>');

            for (let i of data) {
                select_descripcion.append(`
                    <option value="${i.id}">${i.nombre}</option>
                `)
            }

            select_descripcion.select2({
                width: '100%',
                dropdownParent: $('#modalForm')
            });

            select_descripcion.on('select2:select', function (e) {
                var data = e.params.data;

                var insumo = lista_conceptos.find(it => it.id == data.id);

                if (!insumo) {
                    $("#precio").val(0);
                } else {
                    $("#precio").val(parseFloat(insumo.precio));
                }

                $("#cantidad").val(1);
                setTimeout(() => {
                    $("#cantidad").select();
                }, 200);

                CalculateSubtotal();
            });

            if (!id && $('input:radio[name=tipo_concepto]:checked').val() == 2) {
                setTimeout(() => {
                    select_descripcion.select2('open');
                }, 500);
            }

            if (id) {
                select_descripcion.val(id);
                select_descripcion.trigger('change')

                var insumo = lista_conceptos.find(it => it.id == id);

                if (!insumo) {
                    $("#precio").val(0);
                } else {
                    $("#precio").val(parseFloat(insumo.precio));
                }

                $("#cantidad").val(1);

                CalculateSubtotal();
            }
        }
    }); 
    
    
}

function guardarDetalleCompra() {

    console.log($('#frmCompra').serialize())

    let data = $('#frmCompra').serialize();

    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Compras&&action=AddDetail",
        dataType: "json",
        method: 'POST',
        data: data,
        success: function (data) {
            if (data.ok) {
                alert('Detalle agregado correctamente');
                location.reload();
            } else {
                alert(data.message);
            }

        }
    })
}

function editarDetalleCompra() {

    console.log($('#frmCompraE').serialize())

    let data = $('#frmCompraE').serialize();

    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Compras&&action=EditDetail",
        dataType: "json",
        method: 'POST',
        data: data,
        success: function (data) {
            if (data.ok) {
                alert('Detalle actualizado correctamente');
                location.reload();
            } else {
                alert(data.message);
            }

        }
    })
}

function guardarDetraccion() {

    console.log($('#frmDetraccion').serialize())

    let data = $('#frmDetraccion').serialize();

    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=ComprasDocumentos&&action=AddDetraccion",
        dataType: "json",
        method: 'POST',
        data: data,
        success: function (data) {
            if (data.ok) {
                alert('Documento Detracción agregado correctamente');
                location.reload();
            } else {
                alert(data.message);
            }
        }
    })
}

function guardarPercepcion() {

    console.log($('#frmPercepcion').serialize())

    let data = $('#frmPercepcion').serialize();

    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=ComprasDocumentos&&action=AddDetraccion",
        dataType: "json",
        method: 'POST',
        data: data,
        success: function (data) {
            if (data.ok) {
                alert('Documento Percepción agregado correctamente');
                location.reload();
            } else {
                alert(data.message);
            }
        }
    })
}

function guardarRetencion() {

    console.log($('#frmRetencion').serialize())

    let data = $('#frmRetencion').serialize();

    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=ComprasDocumentos&&action=AddDetraccion",
        dataType: "json",
        method: 'POST',
        data: data,
        success: function (data) {
            if (data.ok) {
                alert('Documento Retención agregado correctamente');
                location.reload();
            } else {
                alert(data.message);
            }
        }
    })
}

function deleteDetail(id) {
    if (!confirm('¿Está seguro que desea eliminar este registro?')) return;

    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Compras&&action=DeleteDetail",
        dataType: "json",
        method: 'POST',
        data: {id: id},
        success: function (data) {
            if (data.ok) {
                alert('Detalle eliminado correctamente');
                location.reload();
            } else {
                alert(data.message);
            }
        }
    })
}

function deleteDocument(id) {
    if (!confirm('¿Está seguro que desea eliminar este registro?')) return;

    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=ComprasDocumentos&&action=Delete",
        dataType: "json",
        method: 'POST',
        data: {id: id},
        success: function (data) {
            if (data.ok) {
                alert('Documento eliminado correctamente');
                location.reload();
            } else {
                alert(data.message);
            }
        }
    })
}

var array_cuotas = [];

function guardarMovimientoCaja(compra_id, total_compra) {

    let data = $('#frmCaja').serialize();

    if ($('input:radio[name=tipo_pago]:checked').val() == 2) {

        let total_ = 0;
        for (let i of array_cuotas) {
            total_ += i.total;
        }

        if (total_ < total_compra) {
            return alert('Debe completar el total de la compra.');
        }

        data = {
            compra_id: compra_id,
            tipo_pago: 2,
            cuotas: array_cuotas,
        }
    }

    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=ComprasCaja&&action=AddCaja",
        dataType: "json",
        method: 'POST',
        data: data,
        success: function (data) {
            if (data.ok) {
                alert('Registro en Caja registrado correctamente');
                location.reload();
            } else {
                alert(data.message);
            }
        }
    })
}

function addCuota(total_compra, fecha_caja) {

    let fecha = $('#cuotaFecha').val();
    let caja = $('#cuotaCaja').val();
    let medio_pago = $('#cuotaMedioPago').val();
    let total = parseFloat($('#cuotaTotal').val());
    let pago_efectuado = $('#pago_efectuado').is(":checked");

    if (!fecha) {
        return alert('Ingrese una fecha válida');
    } 

    if (total <= 0) {
        return alert('Ingrese un total válido');
    } 

    let total_ = 0;
    for (let i of array_cuotas) {
        total_ += i.total;
    }

    if (total_ + total > total_compra) {
        return alert('El total no puede ser mayor al total de la compra');
    }

    if (!pago_efectuado) {
        caja = undefined;
        medio_pago = undefined;
    } else {

        if (fecha != fecha_caja) {
            return alert('Cuando el pago es efectuado, la fecha debe ser hoy: ' + fecha_caja);
        }
    }

    array_cuotas.push({
        fecha, total, pago_efectuado, caja, medio_pago
    });

    $('#cuotaTotal').val(total_compra - total_ - total)

    fillTableCuotas();
}

function fillTableCuotas() {

    $("#tblBodyCuotas").html("");
    var html_cuotas = "";
    $.each( array_cuotas, function( key, value ) {
        html_cuotas += `
            <tr>
                <td class="text-center">${value.fecha}</td>
                <td class="text-right">S/</td>
                <td class="text-right">${value.total}</td>
                <td class="text-center" style="vertical-align: middle">
                    ${value.pago_efectuado ? '<label class="label label-success">Pagado</label>' : '<label class="label label-danger">Por Pagar</label>'}
                </td>
                <td class="text-center">${value.caja ? value.caja : '-'}</td>
                <td class="text-center">${value.medio_pago ? value.medio_pago : '-'}</td>
                <td class="text-center">
                    <a class="btn" onclick="deleteCuota(${key})">
                        <i class="fa fa-trash-o text-danger"></i>
                    </a>
                </td>
            </tr>
        `;

        total = total + parseFloat(value.total);
    });
    $("#tblBodyCuotas").html(html_cuotas);
}

function deleteCuota(index) {

    array_cuotas.splice(index, 1);

    let total_ = 0;
    for (let i of array_cuotas) {
        total_ += i.total;
    }

    $('#cuotaTotal').val(total_compra - total_)

    fillTableCuotas();
}

function guardarConceptoCompra() {

    let data = $('#frmAddConcepto').serialize();

    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Compras&&action=AddConcepto",
        dataType: "json",
        method: 'POST',
        data: data,
        success: function (data) {
            if (data.ok) {
                alert('Concepto agregado correctamente');
                $('#modalAddConcepto').modal('hide');

                LoadDescripciones(data.id);
            } else {
                alert(data.message);
            }
        }
    })
}

function guardarInsumo() {
    $('#modalNewInsumo').modal('hide');
    $.post("<?php echo class_config::get('urlApp') ?>/?controller=Insumo&action=Save", $('#formNewInsumo').serialize(),
    function() {
        alert('Nuevo Insumo registrado');
        location.reload();
    });
    
    return false;  
}
