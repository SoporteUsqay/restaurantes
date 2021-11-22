<?php require_once '../../../../Components/Config.inc.php'; ?>

//<script>
    
function init() {

    buildGraph();
}

var graph;

function buildGraph() {

    let ctx = document.getElementById('graph').getContext('2d');

    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=VentasxCompras&&action=GetDataGraph",
        type: 'GET',
        data: $('#frmFiltro').serialize(),
        dataType: 'json',
        success: function (data) {

            if (graph) {
                graph.destroy();
            }


            graph = new Chart(ctx, {
                type: 'bar',
                data: {
                    // labels: ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'],
                    labels: data['labels'],
                    datasets: [
                        {
                            label: 'Ventas',
                            // data: [1, 2, 3, 15, 18, 12, 3, 5, 2, 6],
                            data: data['ventas'],
                            borderWidth: 1,
                            backgroundColor: '#66bb6a',
                            fill: false,
                        },
                        {
                            label: 'Compras',
                            // data: [5, 8, 2, 9, 26, 5, 3, 5, 2, 6],
                            data: data['compras'],
                            borderWidth: 1,
                            backgroundColor: '#ef5350',
                            fill: false,
                        },
                        {
                            label: 'Gastos',
                            // data: [5, 8, 2, 9, 26, 5, 3, 5, 2, 6],
                            data: data['gastos'],
                            borderWidth: 1,
                            backgroundColor: '#ffc107',
                            fill: false,
                        },
                    ],
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
                }
            });

            let table = $('#tblBodyGraph');

            table.html('');

            let html = '';

            for (let i = 0; i < data['labels'].length; i++) {

                let total = Math.round((data['ventas'][i] - data['compras'][i] + data['gastos'][i]) * 100) / 100;

                html += `
                    <tr>
                        <td>${data['labels'][i]}</td>
                        <td class="text-right">${data['ventas'][i]}</td>
                        <td class="text-right">${data['compras'][i]}</td>
                        <td class="text-right">${data['gastos'][i]}</td>
                        <td class="text-right ${total >= 0 ? '' : 'alert-danger'}">${total}</td>
                    </tr>
                `;
            }

            table.html(html);
        }
    });
}

function Filtrar() {

    buildGraph();

    let group = $('#cmbGroup').val();

    switch (parseInt(group)) {
        case 1: $('#lblTable').html('Día'); break;
        case 2: $('#lblTable').html('Mes'); break;
        case 3: $('#lblTable').html('Año'); break;
    }
}

$(document).ready(function () {

    Chart.defaults.global.defaultFontSize = 12;

    $("#fecha_inicio").datepicker({dateFormat: 'yy-mm-dd'});
    $("#fecha_fin").datepicker({dateFormat: 'yy-mm-dd'});

    init();
});
