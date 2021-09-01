<?php
error_reporting("E_ALL");

include_once('../reportes/recursos/componentes/MasterConexion.php');
$conn = new MasterConexion();

//Obtenemos moneda nacional
$moneda = "";
$query_moneda = "Select * from moneda where estado = 2";
$res_m = $conn->consulta_arreglo($query_moneda);
if(is_array($res_m)){
    $moneda = $res_m["simbolo"];
}
?>

<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Ventas Mensuales</title>
        <link rel="icon" href="../logo.ico"/>

		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<style type="text/css">
		</style>
		<script type="text/javascript">
$(function () {
    $('#container').highcharts({
        chart: {
            type: 'line'
        },
        title: {
            text: 'Ventas Por Mes'
        },
        subtitle: {
            text: 'Grafica Lineal'
        },
        xAxis: {
            categories: [ ]
        },
        yAxis: {
            title: {
                text: ' Monto Vendido (<?php echo $moneda?>)'
            }
        },
        plotOptions: {
            line: {
                dataLabels: {
                    enabled: true
                },
                enableMouseTracking: true
            }
        },
        series: [{
            name: 'Total Vendido (<?php echo $moneda?>)',
            data: [
                
                <?php
                $query_mes = "Select fechaCierre, count(*) as pedidos, sum(total) as vendido, sum(descuento) as descuentos from pedido where estado = 1 AND month(fechaCierre) = '".$_REQUEST["m"]."' AND year(fechaCierre) = '".$_REQUEST["a"]."' group by fechaCierre";
                $result = $conn->consulta_matriz($query_mes);

                if(is_array($result)): foreach($result as $res):?>
                ['<?php echo $res['fechaCierre']; ?>', <?php echo $res['vendido'];?>],
                <?php endforeach; endif;?>
                                        
                ]
        }, {
            name: 'Pedidos Atendidos',
            data: [

                <?php
                $query_mes = "Select fechaCierre, count(*) as pedidos, sum(total) as vendido, sum(descuento) as descuentos from pedido where estado = 1 AND month(fechaCierre) = '".$_REQUEST["m"]."' AND year(fechaCierre) = '".$_REQUEST["a"]."' group by fechaCierre";
                $result = $conn->consulta_matriz($query_mes);

                if(is_array($result)): foreach($result as $res):?>
                ['<?php echo $res['fechaCierre']; ?>', <?php echo $res['pedidos'];?>],
                <?php endforeach; endif;?>
                             
               ]
        }]
    });
});
		</script>
	</head>
	<body>
<script src="Highcharts/js/highcharts.js"></script>
<script src="Highcharts/js/modules/exporting.js"></script>

<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

	</body>
</html>
