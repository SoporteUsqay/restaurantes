<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="shortcut icon" href="././assets/ico/favicon.ico">

        <title><?php echo Class_config::get('nameApplication') ?></title>
        <link rel="stylesheet" type="text/css" href="Public/jquery-easyui/easyui/themes/default/easyui.css">
        <link rel="stylesheet" type="text/css" href="Public/jquery-easyui/easyui/themes/icon.css">
        <!-- Bootstrap core CSS -->
        <link rel="stylesheet" href="Public/jquery-ui-1.10.4.custom/css/jquery-ui.min.css">
        <link href="Public/Bootstrap/css/bootstrap.min.css" rel="stylesheet">

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
        <script src="Public/jquery-ui-1.10.4.custom/js/jquery-1.10.2.js"></script>
        <script src="Public/jquery-ui-1.10.4.custom/js/jquery-ui-1.10.4.custom.js"></script>
        <script type="text/javascript" src="Public/jquery-easyui/easyui/jquery.easyui.min.js"></script> 
        <script src="Public/Bootstrap/js/bootstrap.min.js"></script>
    </head>

    <body>

        <!-- Fixed navbar -->
        <?php
        $objViewMenu = new Application_Views_IndexView();
        $objViewMenu->showContent();
        ?>
        <br>
        <br>
        <br>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-1">

                </div>
                <div class="col-lg-10">

                    <div class="row">
                        <div class="col-lg-12">
                            <button type="button" class="btn btn-success"><span class="glyphicon glyphicon-file"></span> Nuevo</button>
                            <button type="button" class="btn btn-success"><span class="glyphicon glyphicon-edit"></span> Editar</button>
                            <button type="button" class="btn btn-success"><span class="glyphicon glyphicon-trash"></span> Eliminar</button>
                            <button type="button" class="btn btn-success"><span class="glyphicon glyphicon-print"></span> Imprimir</button>
                            <button type="button" class="btn btn-success">Exportar</button>

                        </div>
                    </div>
                    <br>
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Administrar Mensajes

                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <form>
                                    <div class="col-lg-2">
                                        Codigo <input class="form-control">
                                    </div>    
                                    <div class="col-lg-2">
                                        <br>
                                        Este codigo se autogenera
                                    </div>    

                                    <div class="col-lg-8">
                                        Mensaje <textarea class="form-control"></textarea>
                                    </div>    

                                </form>

                            </div>
                            <button class="btn btn-primary">Aceptar</button>
                            <button class="btn btn-primary">Cancelar</button>
                        </div>
                    </div>


                    <table id="tblinsumo" class="easyui-datagrid" title="Listado de Mensajes" style="width:auto;height:240px;font-size: 12px">
                    </table>



                </div>
            </div>
            <div class="col-lg-1">

            </div>
        </div>

    </div>
</body>
</html>