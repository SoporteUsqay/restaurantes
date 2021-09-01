<?php 

require_once '../../../../Components/Config.inc.php'; 

 ?>
//<script>
function init() {



    getDataVentas();
    getDataExtra();
    getDataTables();
    buildGraphVentas();
    buildGraphVentasSalones();
}

function getDataVentas() {

    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Dashboard&&action=GetTotales",
        type: 'GET',
        data: { filter: $('#cmbTipoFilter').val() },
        dataType: 'json',
        success: function (data) {

            $('#montoTotalCaja').html(data['total_caja'].total);
            if (parseFloat(data['total_caja'].total) > 0 ) {
                $('#classMontoTotalCaja').removeClass('text-danger');
                $('#classMontoTotalCaja').addClass('text-success');
            } else {
                $('#classMontoTotalCaja').removeClass('text-success');
                $('#classMontoTotalCaja').addClass('text-danger');
            }

            $('#montoTV').html(data['total'].total);
            $('#montoCompras').html(data['total_compras'].total);
            
            $('#montoMov').html(data['total_movimientos'].total);
            if (parseFloat(data['total_movimientos'].total) > 0 ) {
                $('#classMontoMov').removeClass('text-danger');
                $('#classMontoMov').addClass('text-success');
            } else {
                $('#classMontoMov').removeClass('text-success');
                $('#classMontoMov').addClass('text-danger');
            }

            $('#montoPropina').html(data['total_propinas'].total);
            $('#montoDes').html(data['descuentos'].total);
            $('#montoVMesa').html(data['total_mesas'].total);
            $('#cantidadVMesa').html(data['cantidad_mesas_por_cobrar'].total);
            $('#cantidadVMesa2').html(data['cantidad_mesas_por_cobrar'].total);
            $('#montoTotal').html(data['total_with_mesas'].total);
            $('#montoAnuladas').html(data['total_anulado'].total);
            $('#montoCred').html(data['total_credito'].total);
            $('#cantidadCred').html(data['cantidad_credito'].total);
            $('#montoCon').html(data['total_consumo'].total);
            $('#cantidadCon').html(data['cantidad_consumo'].total);
            $('#cantidadAnuladas').html(data['cantidad_anuladas'].total);
            $('#cantidadAtendidas').html(data['cantidad_atendidas'].total);
            $('#montoTotCredCon').html(data['total_with_cred_con'].total);

            fecha_inicio = data['ref_fechas'].inicio;
            fecha_fin = data['ref_fechas'].fin;

            let html = '';
            let col = "3";

            switch (data['medios'].length) {
                case 0: case 1: case 2: case 4:
                    col = "3"; break;
                case 3:
                    col = "4"; break;
            }

            $(data['medios']).each((index) => {
                let it = data['medios'][index];
                html += `
                <div class="col-xs-12 col-md-3">
                    <div class="panel">
                        <div class="panel-body">

                            <div class="row">
                                <div class="col-xs-8">
                                    <div class="text-amount">
                                        ${it.moneda}
                                        <span class="">${it.total}</span>
                                    </div>
                                    <div class="text-title">
                                        Ventas ${it.nombre}
                                    </div>
                                </div>
                                <div class="col-xs-4">
                                    <img src="${ index == 0 ? 'Public/images/dashboard/3595972.svg' : 'Public/images/dashboard/3595994.svg'}" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                `
            })
            $('#rowMedios').html(html);

            let htmlCompra = '';

            $(data['medios_compras']).each((index) => {
                let it = data['medios_compras'][index];
                htmlCompra += `
                <div class="col-xs-12 col-md-3">
                    <div class="panel">
                        <div class="panel-body">

                            <div class="row">
                                <div class="col-xs-8">
                                    <div class="text-amount text-danger">
                                        ${it.moneda}
                                        <span class="">${it.total}</span>
                                    </div>
                                    <div class="text-title">
                                        Compras ${it.nombre}
                                    </div>
                                </div>
                                <div class="col-xs-4">
                                    <img src="${ index == 0 ? 'Public/images/dashboard/3595972.svg' : 'Public/images/dashboard/3595994.svg'}" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                `
            })
            $('#rowMediosCompra').html(htmlCompra);
        }
    });
}

function getDataExtra() {

    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Dashboard&&action=GetDataExtra",
        type: 'GET',
        data: { filter: $('#cmbTipoFilter').val() },
        dataType: 'json',
        success: function (data) {

            $('#montoProMesa').html(data['promedio_mesa'].total);
            $('#nombreMozo').html(data['mozo'].nombre);
            $('#cantidadMozo').html(data['mozo'].cantidad);
            $('#totalMozo').html(data['mozo'].total);
            $('#tiempoMin').html(data['tiempo_minimo'].total);
            $('#tiempoMax').html(data['tiempo_maximo'].total);
        }
    });
}
function getDataTables() {

    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Dashboard&&action=GetDataTables",
        type: 'GET',
        data: { filter: $('#cmbTipoFilter').val() },
        dataType: 'json',
        success: function (data) {

            // $('.dtbl').DataTable().clear().destroy();

            let html = '';
            $(data['platos']).each((index) => {
                let it = data['platos'][index];
                html += `
                <tr>
                    <td>${it.nombre}</td>
                    <td class="text-right">${it.cantidad}</td>
                    <td class="text-right">S/</td>
                    <td class="text-right">${it.total}</td>
                </tr>
                `
            })
            $('#tblPlatos').html(html);

            html = '';
            $(data['clientes']).each((index) => {
                let it = data['clientes'][index];
                html += `
                <tr>
                    <td>${it.nombre}</td>
                    <td class="text-right">${it.cantidad}</td>
                    <td class="text-right">S/</td>
                    <td class="text-right">${it.total}</td>
                </tr>
                `
            })
            $('#tblClientes').html(html);


            // $('.dtbl').DataTable({
            //     "searching": false,
            //     "paging": false,
            //     "bInfo": false, 
            // });
            
        }
    });
}

var graphVentas;
var graphVentasSalones;

function buildGraphVentas() {

    let ctx = document.getElementById('graphVentas').getContext('2d');

    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Dashboard&&action=GetDataGraphVentas",
        type: 'GET',
        data: { filter: $('#cmbTipoFilter').val() },
        dataType: 'json',
        success: function (data) {

            $('#titleGraphVentas').html(data['title']);

            if (graphVentas) {
                graphVentas.destroy();
            }

            graphVentas = new Chart(ctx, {
                type: 'line',
                data: {
                    // labels: ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'],
                    labels: data['labels'],
                    datasets: [{
                        label: 'Ventas',
                        // data: [1, 2, 3, 15, 18, 12, 3, 5, 2, 6],
                        data: data['data_montos'],
                        borderWidth: 1,
                        backgroundColor: '#ffa726',
                        // fill: false,
                    }],
                },
                options: {
                    responsive: true,
                    // scales: {
                    //     yAxes: [{
                    //         ticks: {
                    //             beginAtZero: true
                    //         }
                    //     }]
                    // }
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var label = 'S/ ';
                                
                                label += data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                                return label;
                            }
                        }
                    }
                }
            });
        }
    });
}

function buildGraphVentasSalones() {

    let ctx = document.getElementById('graphVentasSalones').getContext('2d');

    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Dashboard&&action=GetDataGraphSalones",
        type: 'GET',
        data: { filter: $('#cmbTipoFilter').val() },
        dataType: 'json',
        success: function (data) {

            if (graphVentasSalones) {
                graphVentasSalones.destroy();
            }


            graphVentasSalones = new Chart(ctx, {
                type: 'pie',
                data: {
                    datasets: [{
                        data: data['data_montos'],
                        backgroundColor: ['#dce775', '#64b5f6', '#ef5350']
                    }],
                    labels: data['labels']
                },
                options: {
                    responsive: true,
                    // fill: false,
                    // scales: {
                    //     yAxes: [{
                    //         ticks: {
                    //             beginAtZero: true
                    //         }
                    //     }]
                    // }
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var label = 'S/ ';
                                
                                label += data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                                return label;
                            }
                        }
                    }
                }
            });
        }
    });
}

function sendReport(url, type = 0) {

    switch (type) {

        case 1:
            url += `&inicio=${fecha_inicio}&fin=${fecha_fin}`; 
            break;  

        default:
            url += `&txtfechainicio=${fecha_inicio}&txtfechafin=${fecha_fin}`;   
    }

    window.open(url, '_blank');
}

$(document).ready(function () {

    Chart.defaults.global.defaultFontSize = 12;

    init();

    // $('.dtbl').DataTable({
    //     "searching": false,
    //     "paging": false,
    //     "bInfo": false, 
    // });

    $('#cmbTipoFilter').on('change', function () {
        init();
    })
});