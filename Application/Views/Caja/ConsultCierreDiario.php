<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js"> <!--<![endif]-->

    <head>
        <meta charset="utf-8">
        <link href="./././Public/Bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="./././Public/Bootstrap/css/signin.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="Public/jquery-easyui/easyui/themes/default/easyui.css">
        <link rel="stylesheet" type="text/css" href="Public/jquery-easyui/easyui/themes/icon.css">

        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
        <title><?php echo Class_config::get('nameApplication') ?></title>

        <link rel="stylesheet" href="Public/Usqay/css/usqay.css">
        <link rel="icon" href="logo.ico"/>

        <!-- Modernizr -->
        <script src="./././Public/js/libs/modernizr-2.6.2.min.js"></script>
        <!-- jQuery-->

        <script type="text/javascript" src="./././Public/jquery-ui-1.10.4.custom/js/jquery-1.10.2.js"></script>
        <script type="text/javascript" src="Public/jquery-easyui/easyui/jquery.easyui.min.js"></script>

        <!-- framework css -->
        <!--[if gt IE 9]><!-->

        <!--<![endif]-->
        <!--[if lte IE 9]>
        <link type="text/css" rel="stylesheet" href="css/groundwork-core.css">
        <link type="text/css" rel="stylesheet" href="css/groundwork-type.css">
        <link type="text/css" rel="stylesheet" href="css/groundwork-ui.css">
        <link type="text/css" rel="stylesheet" href="css/groundwork-anim.css">
        <link type="text/css" rel="stylesheet" href="css/groundwork-ie.css">
        <![endif]-->

    </head>
    <body onload="">

        <div class="panel panel-primary">
            <div class="modal-dialog">
                <div class="modal-content">

                    <form method="post">
                        <div class="alert alert-danger alert-dismissible fade in" role="alert">
                            <center>
                                <h4>¡El ultimo fecha de apertura es diferente a la fecha de hoy!</h4>
                            <p>El sistema ha detectado que el último día que se aperturó fue: <label id="lblFechaCierre"></label></p>
                            <br>
                            <p>
                                <a onclick="verificaFechas()" class="btn btn-danger">Aperturar nuevo día</a>
                                <a href="<?php echo Class_config::get('urlApp')?>/?controller=Index&action=ShowHome" class="btn btn-default">Seguir trabajando con la misma fecha</a>
                                </center>
                            </p>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->


        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->

    </body>
    <!--<script src="./././Public/Bootstrap/js/bootstrap.min.js"></script>-->

</html>
<script language="javascript">

    var cantidad = cantidadesasAbiertas();
    function cantidadesasAbiertas() {
        var cantidad = 0;
        $.post('<?php echo Class_config::get('urlApp') ?>/?controller=Caja&action=VerificarMesasAbiertas', function (result) {
            cantidad = parseInt(result);
            console.log(result);
        }, 'html');

        return cantidad;
    }


    function verificaFechas() {
        if (cantidad !== 0) {
            $.messager.alert('Error!', 'Para poder realizar el cierre debe de estar cerradas/vendidas todas las mesas', 'error');
        }
        else {
            $.messager.confirm('Aperturar Nuevo día', '¿Confirma que dea aperturar un nuevo día?', function (r) {
                if (r) {
                    $.messager.alert('Cargando!', 
                    '<div class="text-center"><img width="80" src="Public/images/loading.gif"></div> El dia se esta aperturando... Por favor espere, se le redireccionará automaticamente cuando el proceso termine.', 
                   );
                    window.location.href="<?php echo Class_config::get('urlApp')?>/?controller=Caja&action=CierreActual";
                }
            });
        }
    }


    $(document).ready(function () {
        cargandoFechas();
    });
    function cargandoFechas() {
        var meses = new Array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

        var diasSemana = new Array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");
        var fecha = new Date();
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Caja&&action=ListFechasCierre",
            type: 'POST',
            cache: true,
            dataType: 'json',
            success: function (data) {
//                fecha =
                $("#lblFechaCierre").html(data[0].actual);
//                $("#lblProximoCierre").html(data[0].proximo);
            }
        });
    }
</script>
