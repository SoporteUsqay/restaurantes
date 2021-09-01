<?php
error_reporting("E_ALL");

include_once('../reportes/recursos/componentes/MasterConexion.php');
$conn = new MasterConexion();
?>

<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Platos con mayor rotacion</title>
        <link rel="icon" href="../logo.ico"/>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<style type="text/css">
/*${demo.css}*/
		</style>
		<script type="text/javascript">
$(function () {
    $('#container').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text: 'Platos Mas Vendidos'
        },
        subtitle: {
            text: 'Grafica Circular'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,  
                    
                    format: '<b>{point.name}</b>: {point.percentage:.1f}% ',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Top Ventas',
            data: [
                
                <?php

                    $fechaInicio= $_REQUEST['fechaInicio'];
                    $fechaFin= $_REQUEST['fechaFin'];
                    $tipoClase= $_REQUEST['tipoClase'];
                    $top=$_REQUEST['top'];
             
                    // $query = "";
                    // if(intval($tipoClase)>0){
                    //     $query = "Select sum(dp.cantidad) as vendido, sum(dp.cantidad*dp.precio) as total, pl.descripcion, pl.precio_venta from plato pl, detallepedido dp where dp.pkPlato = pl.pkPlato AND dp.estado <> 3 AND pl.pktipo = '".$tipoClase."' AND dp.horaPedido BETWEEN '".$fechaInicio." 00:00:00' AND '".$fechaFin." 23:59:59' GROUP BY dp.pkPlato ORDER BY total desc, cantidad desc LIMIT ".$top;
                    // }else{
                    //     $query = "Select sum(dp.cantidad) as vendido, sum(dp.cantidad*dp.precio) as total, pl.descripcion, pl.precio_venta from plato pl, detallepedido dp where dp.pkPlato = pl.pkPlato AND dp.estado <> 3 AND dp.horaPedido BETWEEN '".$fechaInicio." 00:00:00' AND '".$fechaFin." 23:59:59' GROUP BY dp.pkPlato ORDER BY total desc, cantidad desc LIMIT ".$top;
                    // }

                    $dcI = $conn->consulta_arreglo("Select * from corte where fecha_cierre = '".$fechaInicio."'  order by inicio asc limit 1");

                    $ffecha = "";

                    if (!is_array($dcI)) {
                        $ffecha = " AND dp.horaPedido >= '" .$fechaFin."'";
                    }

                    $dcF = $conn->consulta_arreglo("Select * from corte where fecha_cierre = '".$fechaFin."' order by inicio desc limit 1");

                    if ($dcF['fin']) {
                        $ffecha = " AND dp.horaPedido BETWEEN '" .$dcI['inicio'] . "' AND '" . $dcF['fin'] . "'";
                    } else {
                        $ffecha = " AND dp.horaPedido >= '" .$dcI['inicio']."'";
                    }

                    $query_platos = "Select sum(dp.cantidad*dp.precio) as total, dp.pkPlato, pl.descripcion, pl.precio_venta 
                                    from plato pl, detallepedido dp, pedido p
                                    where dp.pkPlato = pl.pkPlato AND dp.pkPediido = p.pkPediido AND dp.estado > 0 AND dp.estado < 3 and p.estado != 3";
                                
                    if ($tipoClase > 0) {
                        $query_platos .= " AND pl.pktipo = '".$tipo."' ";
                    }   

                    $query_platos .= " $ffecha GROUP BY dp.pkPlato ORDER BY total desc, cantidad desc LIMIT ".$top;

                    $result = $conn->consulta_matriz($query_platos);
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
<script src="Highcharts/js/modules/exporting.js"></script>

<div id="container" style="min-width: 410px; height: 700px; max-width: 900px; margin: 0 auto"></div>

	</body>
</html>
