
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <title><?php echo Class_config::get('nameApplication') ?></title>
        <!--<link rel="stylesheet" type="text/css" href="Public/jquery-easyui/easyui/themes/default/easyui.css">-->
        <!--<link rel="stylesheet" type="text/css" href="Public/jquery-easyui/easyui/themes/icon.css">-->
        <link rel="stylesheet" href="Public/jquery-ui-1.10.4.custom/css/jquery-ui.min.css">

        <link href="Public/Bootstrap/css/bootstrap.css" rel="stylesheet">
        <link href="Public/Bootstrap/media/css/jquery.dataTables.min.css" rel="stylesheet">

        <link rel="stylesheet" type="text/css" href="Public/css/style.css">
        <!--<link rel="stylesheet" type="text/css" href="Public/css/style2.css">-->
        <!--<link rel="stylesheet" type="text/css" href="Public/jquery-easyui/easyui/demo.css">-->

        <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery-1.10.2.js"></script>
        <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery-ui-1.10.4.custom.js"></script>
        <!--<script src="//code.jquery.com/jquery-1.10.2.js"></script>-->
        <!--<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>-->
        <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery.numeric.js"></script>
        <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery.maskedinput.js"></script>
        <!-- <script type="text/javascript" src="Public/scripts/body.js.php"></script> -->
        <!-- <script type="text/javascript" src="Public/scripts/listGeneral.js.php"></script> -->
        <!-- <script type="text/javascript" src="Public/scripts/Validation.js.php"></script> -->
        <script  type="text/javascript" src="Public/Bootstrap/js/bootstrap.min.js"></script>
        <script  type="text/javascript" src="Public/Bootstrap/media/js/jquery.dataTables.min.js"></script>
        <!-- <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/Concurrent.Thread.js"></script> -->
        <link rel="stylesheet" href="Public/Usqay/css/usqay.css">
        <link rel="stylesheet" href="Public/Usqay/css/clc.css">
        
    </head>
<body>
    <?php
        error_reporting(E_ALL);
        $objViewMenu = new Application_Views_IndexView();
        $objViewMenu->showContent();
        
        $db = new SuperDataBase();

        include_once('reportes/recursos/componentes/MasterConexion.php');
        $conn = new MasterConexion();

        $configuracion = $conn->consulta_matriz("SELECT * FROM cloud_config");


        $data_configuracion = [];

        foreach ($configuracion as $item) {
            $data_configuracion[$item['parametro']] = $item['valor'];
        }

    ?>

    <br>
    <br>
    <br>
    <br>

    <div class="container">

        <div class="panel panel-primary">
            <div class="panel-heading">
                Tickets de Asistencia (Soporte)
            </div>
            <div class="panel-body">

                <div class="">
                    <button class="btn btn-success" onclick="openModalSoporte()">
                        Nuevo Ticket
                    </button>
                </div>

                <hr>


                <table id="tblTicketSoporte" class="table">
                    <thead>
                        <th>Fecha</th>
                        <th>Email</th>
                        <th>Nombre</th>
                        <th>Problema</th>
                        <th>Detalle</th>
                        <th>Usuario</th>
                    </thead>
                    <tbody>
                        <?php

                            $query = "SELECT
                                ticket_soporte.*,
                                CONCAT(trabajador.apellidos, ' ', trabajador.nombres) as nombre_trabajador
                            FROM ticket_soporte
                            LEFT JOIN trabajador ON ticket_soporte.trabajador_id = trabajador.pkTrabajador
                            ORDER BY ticket_soporte.fecha DESC
                            ";

                            $res = $db->executeQueryEx($query);

                            while($row = $db->fecth_array($res)):
                        ?>
                        <tr>
                            <td><?php echo $row['fecha'] ?></td>
                            <td><?php echo $row['email'] ?></td>
                            <td><?php echo $row['nombre'] ?></td>
                            <td><?php echo $row['problema'] ?></td>
                            <td><?php echo $row['detalle_problema'] ?></td>
                            <td><?php echo $row['nombre_trabajador'] ?></td>
                        </tr>

                        <?php endwhile ?>

                    </tbody>
                </table>


            </div>

        </div>

        

    </div>

    


    <div id="modalTicketSoporte" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><label id="tituloModalDetalleMas">Ticket para Asistencia</label></h4>
                </div>
                <div class="modal-body">
                    
                    <form id="frmTicket">
                        <div class="row">
                            <div class="form-group col-xs-12 col-md-4">
                                <label for="">Correo Electrónico</label>
                                <input type="text" name="email" class="form-control" value="">
                            </div>
                            <div class="form-group col-xs-12 col-md-4">
                                <label for="">Nombre Completo</label>
                                <input type="text" name="name" class="form-control">
                            </div>
                            <div class="form-group col-xs-12 col-md-4">
                                <label for="">Telefono</label>
                                <input type="text" name="phone" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <!-- <div class="form-group col-xs-12 col-md-4">
                                <label for="">Temas de Ayuda</label>
                                <select name="topic_id" id="" class="form-control"></select>
                            </div> -->
                            <div class="form-group col-xs-12 col-md-12">
                                <label for="">Resumen del Problema</label>
                                <input type="text" name="subject" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Redacta tu problema</label>
                            <textarea name="message" id="" cols="30" rows="3" class="form-control"></textarea>
                        </div>

                        <input type="text" name="op" value="add" hidden>
                    </form>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="sendTicketSoporte()">Aceptar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

        
<script>

    $(document).ready(function () {
        $('#tblTicketSoporte').DataTable({
            "ordering": false
        });
    })

    function openModalSoporte() {
        $('#modalTicketSoporte').modal('show')
    }

    function sendTicketSoporte() {
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Soporte&action=SendTicket",
            type: 'POST',
            data: $('#frmTicket').serialize(),
            dataType: 'json',
            success: function(data) {
                
                if (data == 1) {
                    alert('Ticket agregado correctamente, pronto nos comunicaremos con usted.');
                    location.reload();
                } else {
                    alert('Ocurrió un inconveniente, por favor intente mas tarde.');
                }
            }

        });
    }   
</script>

</body>
</html>



