<?php require_once '../../../../Components/Config.inc.php'; ?>
//<script>

$(document).ready(function () {
    $('#contenedorNota').hide() 
    
    $('#frmFiltro').bind('submit', ConsultarDocumento)
    $('#frmEditItem').bind('submit', EditItem)

    LoadMotivos()
});

var data_comprobante = null
var data_send_nota = {}

function ConsultarDocumento() {

    $('#contenedorNota').hide() 
    data_comprobante = null
    
    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Notas&&action=GetComprobante",
        dataType: "json",
        method: 'POST',
        data: $(this).serialize(),
        success: function (data) {
            console.log(data)
            if (data.ok) {
                data_comprobante = data.data
                buildTable(data_comprobante)
                $('#contenedorNota').show() 
                data_send_nota = {
                    tipo_documento: $('#tipo_documento').val(),
                    serie: $('#serie').val(),
                    numero: $('#numero').val(),
                }
            } else {
                alert(data.message)
            }
        }
    })
    

    return false
}

function buildTable(data) {
    $('#contenedorDetalles').html('')
    $('#lblTotal').html('0.00')

    let html = ''
    let total = 0

    for (let item of data.detalles) {

        html += `
            <tr>
                <td>${item.descripcion}</td>
                <td class="text-right">${item.cantidad}</td>
                <td class="text-right">${item.precio}</td>
                <td class="text-right">

                    <div class="contenedorOpItems">
                        <button class="btn btn-warning btn-sm" onclick="loadEdit(${item.id}, ${item.cantidad}, ${item.precio})">
                            <i class="fa fa-pencil"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="loadDelete(${item.id})">
                            <i class="fa fa-trash-o"></i>
                        </button>
                    </div>
                    
                </td>
            </tr>
        `

        total += item.cantidad * item.precio
    }

    $('#contenedorDetalles').html(html)
    $('#lblTotal').html((Math.round(total * 100) / 100).toFixed(2))

    ShowItems()
}

function loadEdit(id, cantidad, precio) {
    console.log('loadEdit', id, cantidad, precio)

    $('#id').val(id)
    $('#cantidad').val(cantidad)
    $('#precio').val(precio)

    $('#modalEditItem').modal('show')
}

function loadDelete(id) {
    console.log('loadDelete', id)

    if (data_comprobante.detalles.length <= 1) {
        // return alert('El comprobante debe tener por lo menos 1 item')
    }

    if (!confirm('¿Está seguro que desea eliminar el item?')) return

    let index = data_comprobante.detalles.findIndex(i => i.id == id)

    console.log(index)

    data_comprobante.detalles.splice(index, 1)

    buildTable(data_comprobante)
}

function EditItem() {

    let index = data_comprobante.detalles.findIndex(i => i.id == $('#id').val())

    data_comprobante.detalles[index]['cantidad'] = $('#cantidad').val()
    data_comprobante.detalles[index]['precio'] = $('#precio').val()

    $('#modalEditItem').modal('hide')

    buildTable(data_comprobante)

    return false
}

var motivos = [];

function LoadMotivos() {
    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Notas&&action=GetMotivos",
        dataType: "json",
        method: 'GET',
        success: function (data) {
            motivos = data

            ChangeMotivos()
        }
    })
}

function ChangeMotivos() {
    let tipo = $('#tipo_nota').val()

    let filter = motivos.filter(i => i.nota_id == tipo)

    let html = ''

    for (let item of filter) {
        html += `
            <option value="${item.id}">${item.nombre}</option>
        `
    }

    $('#motivo_nota').html(html)

    ShowItems()
}

function ShowItems() {
    let motivo_id = $('#motivo_nota').val()

    let motivo = motivos.filter(i => i.id == motivo_id)[0]

    if (motivo.show_items == 1) {
        $('.contenedorOpItems').show()
    } else {
        $('.contenedorOpItems').hide()
    }
}

function EmitirNota() {
    
    let comprobante = {

    }

    if (data_comprobante.documento) {
        comprobante.documento = data_comprobante.documento
    } else if (data_comprobante.ruc) {
        comprobante.ruc = data_comprobante.ruc
    }

    let data = Object.assign(data_send_nota, comprobante)

    if ($('#tipo_nota').val()) {
        data.tipo_nota = $('#tipo_nota').val()
    } else {
        return alert('Seleccione un tipo de nota')
    }

    let motivo = motivos.filter(i => i.id == $('#motivo_nota').val())[0]
    if (motivo) {
        data.motivo_nota = motivo.codigo
    } else {
        return alert('Seleccione un tipo de nota')
    }

    data.detalles = []

    $.each(data_comprobante.detalles, (index, item) => {
        console.log(item)

        data.detalles.push({
            plato_id: item.id_plato,
            cantidad: item.cantidad,
            precio: item.precio
        })
    })

    console.log(data)

    $('#btnEmitirNota').attr('disabled', true)
    $('#btnEmitirNota').html('Emitiendo....')

    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Notas&&action=AddNota",
        dataType: "json",
        method: 'POST',
        data: data,
        // data: JSON.stringify(data),
        // contentType: 'application/json',
        success: function (data) {
            console.log(data)
            window.location.reload()
        }
    })
}

