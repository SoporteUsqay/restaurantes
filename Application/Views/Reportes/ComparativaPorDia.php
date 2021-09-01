<?php // if(extension_loaded('zlib')){ob_start('ob_gzhandler');}                                                                                            ?>
<!DOCTYPE html>
<?php
$objUserSystem = new UserLogin();
?>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="shortcut icon" href="../../assets/ico/favicon.ico">

        <title><?php echo Class_config::get('nameApplication') ?></title>
        <link rel="stylesheet" type="text/css" href="Public/jquery-easyui/easyui/themes/default/easyui.css">
        <link rel="stylesheet" type="text/css" href="Public/jquery-easyui/easyui/themes/icon.css">
        <!-- Bootstrap core CSS -->
        <link rel="stylesheet" href="Public/jquery-ui-1.10.4.custom/css/jquery-ui.min.css">

        <link href="Public/Bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="Public/css/style.css">
        <!--<link rel="stylesheet" type="text/css" href="Public/jquery-easyui/easyui/demo.css">-->
        <!-- Custom styles for this template -->
        <!--<link href="navbar-fixed-top.css" rel="stylesheet">-->

        <!-- Just for debugging purposes. Don't actually copy this line! -->
        <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        <script src="Public/js/Chart.js" type="text/javascript"></script>

        <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery-1.10.2.js"></script>
        <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery-ui-1.10.4.custom.js"></script>
        <script type="text/javascript" src="Public/jquery-easyui/easyui/jquery.easyui.min.js"></script> 
        <script type="text/javascript" src="Public/jquery-easyui/easyui/datagrid-filter.js"></script>
        <script type="text/javascript"src="Public/jquery-ui-1.10.4.custom/js/jquery.numeric.js"></script>
        <script type="text/javascript"src="Public/jquery-ui-1.10.4.custom/js/jquery.maskedinput.js"></script>
        <!--<script type="text/javascript"src="Public/scripts/body.js.php"></script>-->
        <script type="text/javascript"src="Public/scripts/listGeneral.js.php"></script>
        <script type="text/javascript"src="Public/scripts/Validation.js.php"></script>
        <script  type="text/javascript" src="Public/Bootstrap/js/bootstrap.min.js"></script>

    </head>
    <body>
    <center>
        <h3>Comparativo de venta diarios</h3>
        <h4><?php echo UserLogin::get_nombreSucursal() ?></h4>

        <form class="form-inline" >
            <div class="form-group">
                <label class="sr-only" for="fechaInicio">Email address</label>
                <input type="text" class="form-control date" id="fechaInicio" placeholder="Fecha Inicio">
            </div>
            <div class="form-group">
                <label class="sr-only" for="fechaFin">Password</label>
                <input type="text" class="form-control date" id="fechaFin" placeholder="Fecha Fin">
            </div>

            <button type="button" onclick="createGrafiComparativa()" class="btn btn-danger">Generar comparativas</button>
        </form>
    </center>
    <canvas id="canvasmes" height="550" width="1200"></canvas>
</body>
<script>
    $('.date').datepicker({dateFormat: 'yy-mm-dd', changeMonth: true});
    function createGrafiComparativa() {
        $("#divGraficProductMes").empty();
        var div = $("#divGraficProductMes");
        $('<canvas id="canvasmes" height="450" width="1000">').appendTo(div);

        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Report&&action=ListOutputProductMes",
            data: $('.form-inline').serialize(),
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                var pedido = new Array;
                var cantidad = new Array;
                for (var i = 0; i < data.length; i++) {
//                     console.log(data[i].mes)
                    pedido[i] = data[i].pedido;
                    cantidad[i] = data[i].Cantidad;
                    var 
                }
                var barChartData = {
                    labels: pedido,
                    datasets: [
                        {
                            label: "My First dataset",
                            fillColor: "rgba(151,187,205,0.5)",
                            strokeColor: "rgba(151,187,205,0.8)",
                            highlightFill: "rgba(151,187,205,0.75)",
                            highlightStroke: "rgba(151,187,205,1)",
                            data: cantidad
                        }
                    ]

                };

                var myLine = new Chart(document.getElementById("canvasmes").getContext("2d")).Bar(barChartData);

            }

        });

    }
</script>
</html>