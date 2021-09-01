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
		<title>Platos con mayor rotacion</title>
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
            text: 'Platos Mas Vendidos'
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
                text: 'Monto Vendido (<?php echo $moneda?>)'
            }
        },
        series: [{
            name: 'Total Vendido (<?php echo $moneda?>)',
            data: [
                
                <?php
                    $fechaInicio= $_REQUEST['fechaInicio'];
                    $fechaFin= $_REQUEST['fechaFin'];
                    $tipoClase= $_REQUEST['tipoClase'];
                    $top=$_REQUEST['top'];

                $query = "";
                if(intval($tipoClase)>0){
                    $query = "Select sum(dp.cantidad) as vendido, sum(dp.cantidad*dp.precio) as total, pl.descripcion, pl.precio_venta from plato pl, detallepedido dp where dp.pkPlato = pl.pkPlato AND dp.estado <> 3 AND pl.pktipo = '".$tipoClase."' AND dp.horaPedido BETWEEN '".$fechaInicio." 00:00:00' AND '".$fechaFin." 23:59:59' GROUP BY dp.pkPlato ORDER BY total desc, cantidad desc LIMIT ".$top;
                }else{
                    $query = "Select sum(dp.cantidad) as vendido, sum(dp.cantidad*dp.precio) as total, pl.descripcion, pl.precio_venta from plato pl, detallepedido dp where dp.pkPlato = pl.pkPlato AND dp.estado <> 3 AND dp.horaPedido BETWEEN '".$fechaInicio." 00:00:00' AND '".$fechaFin." 23:59:59' GROUP BY dp.pkPlato ORDER BY total desc, cantidad desc LIMIT ".$top;
                }

                $result = $conn->consulta_matriz($query);
                if(is_array($result)): foreach($result as $res):?>
                ['<?php echo $res['descripcion']; ?>', <?php echo $res['total'];?>],
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
