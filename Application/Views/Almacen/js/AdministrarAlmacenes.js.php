<?php require_once '../../../../Components/Config.inc.php'; ?>
//<script>

$(document).ready(function () {
    $('#tblCompras').DataTable({
        // "ordering": false,
        // "bSort": false,
    });
});

function openModal (type, data) {
    switch (type) {

        case 0:
            $('#modalForm').modal('show')
            break;
        case 1:
            $('#modalFormEdit').modal('show')

            $('#compraID').val(data.id)
            $('#nombreE').val(data.nombre)
            break;
    }
}

function guardarCompra() {
    
    let data = $('#frmAlmacen').serialize();

    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=NAlmacen&&action=AddAlmacen",
        dataType: "json",
        method: 'POST',
        data: data,
        success: function (data) {
            
            if (data.ok) {
                alert("Almacén registrado correctamente.");
                location.reload();
            } else {
                alert(data.message);
            }
        }
    })
}

function editarCompra() {

    let data = $('#frmAlmacenEdit').serialize();

    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=NAlmacen&&action=EditAlmacen",
        dataType: "json",
        method: 'POST',
        data: data,
        success: function (data) {
            
            console.log(data)

            if (data.ok) {
                alert("Almacén actualizado correctamente.");
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
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=NAlmacen&&action=DeleteAlmacen",
        dataType: "json",
        method: 'POST',
        data: {id: id},
        success: function (data) {
            if (data.ok) {
                alert('Almacén eliminado correctamente');
                location.reload();
            } else {
                alert(data.message);
            }
        }
    })
}

