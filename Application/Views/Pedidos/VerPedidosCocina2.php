<?php
$self = Class_config::get('urlApp') . "/?controller=Pedidos&&action=ShowCocina"; //Obtenemos la página en la que nos encontramos
header("refresh:20; url=$self"); //Refrescamos cada 300 segundos
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title><?php echo Class_config::get('nameApplication') ?></title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="shortcut icon" href="../../assets/ico/favicon.ico">



        <!-- Bootstrap core CSS -->
        <link rel="stylesheet" type="text/css" href="Public/jquery-easyui/easyui/themes/default/easyui.css">
        <link rel="stylesheet" type="text/css" href="Public/jquery-easyui/easyui/themes/icon.css">
        <link rel="stylesheet" type="text/css" href="Public/jquery-easyui/easyui/themes/icon.css">
        <link rel="stylesheet" href="Public/jquery-ui-1.10.4.custom/css/jquery-ui.min.css">
        <link href="Public/Bootstrap/css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <!--<link href="navbar-fixed-top.css" rel="stylesheet">-->

        <!-- Just for debugging purposes. Don't actually copy this line! -->
        <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

        <script src="Public/jquery-ui-1.10.4.custom/js/jquery-1.10.2.js"></script>
        <script src="Public/jquery-ui-1.10.4.custom/js/jquery-ui-1.10.4.custom.js"></script>
        <script type="text/javascript" src="Public/jquery-easyui/easyui/jquery.easyui.min.js"></script> 
        <script src="Public/jquery-ui-1.10.4.custom/js/jquery.numeric.js"></script>
        <script src="Public/Bootstrap/js/bootstrap.min.js"></script>
    </head>

    <body>
        <script language="javascript">
            var timestamp = null;
            function cargar_pedidos()
            {
                $.ajax({
                    async: true,
                    type: "POST",
                    url: "httpush.php",
                    dataType: "html",
                    data: "&timestamp=" + timestamp,
                    success: function(data)
                    {
//                        console.log(data)
                        var json = eval("(" + data + ")");

                        timestamp = json.horaPedido;
                        mensaje = json.mensaje;
//                        id = json.id;
//                        status = json.status;
//                        tipo = json.tipo;
//                        
                        console.log(timestamp);
                        if (timestamp == null)
                        {

                        }
                        else
                        {

                            $.ajax({
                                async: true,
                                type: "POST",
                                url: "mensajes.php",
                                data: "",
                                dataType: "html",
                                success: function(data)
                                {
//                                    console.log(data);
                                    $('#botones').html(data);
                                }
                            });
                        }
//                        console.log(data);
                        setTimeout('cargar_pedidos()', 500);

                    }
                });
            }
//
          

        </script>
        <div class="navbar navbar-default navbar-fixed-top" role="navigation">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">Sistema de Restaurante</a>
                </div>
                <div class="navbar-collapse collapse">

                    <ul class="nav navbar-nav navbar-right">

                        <li><a href="javascript:openConfigurePassword();"><span class="glyphicon glyphicon-user" aria-hidden="true"></span><?php
                                $user = new UserLogin();
                                echo" " . $user->get_names() . " " . $user->get_lastnames() . " " . $user->get_idTrabajador()
                                ?></a></li>
                        <li class="active"><a href="<?php echo Class_config::get('urlApp') ?>/?controller=User&action=CloseSession"><span class="glyphicon glyphicon-off"></span> Salir</a></li>
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </div>
        <br><br><br>
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Listado de pedidos</h3>
                    </div>
                    <div class="panel-body">
                        <div id="botones">

                        </div>
                        <?php
                        $db = new SuperDataBase();
                        $q = "SELECT pkDetallePedido, cantidad, precio, cantidad*precio as importe, mensaje, d.estado, d.pkPediido , fechaPedido, horaPedido, horaTermino,  pkCocinero, pkMozo,
 nombres, lastName,d.pkPlato, pl.descripcion as plato,nmesa ,npersonas FROM detallepedido d  inner join (trabajador t inner join person p on p.documento =t.documento)
 on t.pkTrabajador=pkMozo
inner join plato pl on pl.pkPlato=d.pkPlato
inner join (pedido pe inner join mesas m on m.pkMesa=pe.pkMesa) on  pe.pkPediido=d.pkPediido
where d.estado=0
 order by horaPedido";
                        $res = $db->executeQuery($q) or die(mysql_error());
                        while ($row = $db->fecth_array($res)) {
                            echo "<button onClick='openModalConfirm(" . $row['pkDetallePedido'] . " )' class='btn btn-default form-control'>Cant: " . $row['cantidad'] . " Pedido: " . utf8_encode($row['plato']) . " Mozo : " . $row['nombres'] . " " . $row['lastName'] . " Mensaje: <span class='label label-danger'>" . $row['mensaje'] . "</span> Mesa :" . $row['nmesa'] . " nro Personas: " . $row['npersonas'] . "</button>";
                            ;
                        }
                        ?>

                    </div>
                </div>
            </div>

        </div>
        <div id="modalConfirmarCerrarPedido" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">

            <!--<div id="modalConfirmarCerrarPedido" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">-->
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title" >Confirmar cierre de pedido</h4>
                    </div>
                    <div class="modal-content">

                        <!--<input style="display: none" id="txtpkDetalle">-->
                        ¿Esta seguro que desea cerrar el pedido? 
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default" onclick="cerrarPedidoCocina()">Aceptar</button>
                        <button class="btn btn-default" onclick="$('#modalConfirmarCerrarPedido').modal('hide');">Cancelar</button>
                    </div>
                </div>

            </div>
        </div>
    </body>

    <script>
        var pkDetalle_e;
        function openModalConfirm($pkDetalle) {
            $('#modalConfirmarCerrarPedido').modal('show');
//            $('#txtpkDetalle').val($pkDetalle);
            pkDetalle_e = $pkDetalle;
        }
        function cerrarPedidoCocina() {
            var param = {pkDetalle: pkDetalle_e};
            $.ajax({
                url: "<?php echo Class_config::get('urlApp') ?>/?controller=DetallePedido&&action=CerrarPedido",
                type: 'POST',
                data: param,
//            dataType: 'json',
                success: function(data) {
//                    loadDetalles();
                    $('#modalConfirmarCerrarPedido').modal('hide');


                }

            });
        }
    </script>
</html>
