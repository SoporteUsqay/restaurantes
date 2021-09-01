<?php
error_reporting("E_ALL");

include_once('../reportes/recursos/componentes/MasterConexion.php');
$conn = new MasterConexion();
?>

<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Ventas por Trabajador</title>
        <link rel="icon" href="../logo.ico"/>

		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<style type="text/css">
#container {
	height: 600px; 
	min-width: 510px; 
	max-width: 1300px;
	margin: 0 auto;
}
		</style>
		<script type="text/javascript">
$(function () {
    $('#container').highcharts({
        chart: {
            type: 'column',
            margin: 95,
            options3d: {
                enabled: true,
                alpha: 10,
                beta: 25,
                depth: 70
            }
        },
        title: {
            text: 'Top Ventas de Trabajador'
        },
        subtitle: {
            text: 'Grafica Barras 3D'
        },
        plotOptions: {
            column: {
                depth: 25
            }
        },
        xAxis: {
            categories: [  ]
        },
        yAxis: {
            title: {
                text: ' Ventas S/.'
            }
        },
        series: [{
            name: 'Total Soles',
            data: [
                
                <?php
                $FechaInicio= $_REQUEST['DateInicio'];
                $FechaFin= $_REQUEST['DateFin'];
                         
                $query="SELECT  t.documento, t.nombres, t.apellidos,count(p.pkPediido) as total_Pedidos , sum(cantidad*precio) as total
                FROM detallepedido d inner join pedido p on p.pkPediido=d.pkPediido
                inner join trabajador t on pkMozo=t.pkTrabajador
                where date(p.fechaCierre) BETWEEN '$FechaInicio' AND '$FechaFin' and p.estado=1 and d.estado<>3
                group by t.documento order by total desc";

                $result = $conn->consulta_matriz($query);
                if(is_array($result)): foreach($result as $res):?>
                ['<?php echo $res['nombres']." ".$res['apellidos']; ?>', <?php echo $res['total'];?>],
                <?php endforeach; endif;?>
            ]
        }]
    });
});
		</script>
	</head>
	<body>

<script src="Highcharts/js/highcharts.js"></script>
<script src="Highcharts/js/highcharts-3d.js"></script>
<script src="Highcharts/js/modules/exporting.js"></script>

<div id="container" style="height: 600px"></div>
	</body>
</html>
