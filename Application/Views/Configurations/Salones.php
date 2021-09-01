<?php include 'Application/Views/template/header.php'; ?>
<body>

    <?php
    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showContent();
    ?>

    <div class="container">

        <br>
        <br>
        <br>

        <div>
            <h2>Salones </h2>
            <form class="form-horizontal" id="frmRegistrarSalones" method="get">
                <div class="form-group">
                    <div class="col-md-2">
                        <button onclick="registrarSalon()" type="button" class="btn btn-success"id="btnGuardarPagoDiario">
                            <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true" ></span> Registrar Nuevo Salon
                        </button>
                    </div>
                </div>
            </form>

        </div>

        <table id="tblSalones" class="table table-borderer">
            <thead>
            <th>Código</th>
            <th>Descripcion</th>
            <th></th>
            <th></th>
            <th>Estado</th>
            <?php 
            $db = new SuperDataBase();
            $query_c = "SELECT * FROM cajas";
            $result_c = $db->executeQuery($query_c);
            while ($row_c = $db->fecth_array($result_c)) {
                echo "<th>CAJA ".$row_c["caja"]."</th>";
            }
            ?>
            </thead>
            <tbody>
                <?php
                $sucursal = UserLogin::get_pkSucursal();
                $query = "SELECT * FROM salon s";
                $result = $db->executeQuery($query);

                while ($row = $db->fecth_array($result)) {
                    $class = "";
                    $grilla = "";
                    if(intval($row["pkSalon"]) <> 43 && intval($row["pkSalon"]) <> 44){                    
                        if ($row['estado'] == "0") {
                            $class = "success";
                            $grilla = "<a onclick='eliminarSalon(" . $row[0] .
                                      ")'><span class='glyphicon glyphicon-minus-sign' title='Ocultar Salon'></span></a>";
                        } else {
                            $class = "danger";
                            $grilla = "<a onclick='activarSalon(" . $row[0] .
                                      ")'><span class='glyphicon glyphicon-ok' title='Activar Salon'></span></a>";                        
                        }
                    }
                    echo "<tr class='" . $class . "'>";
                    echo "<td>" . $row['pkSalon'] . "</td>";
                    echo "<td>" . utf8_encode($row['nombre']) . "</td>";
                    ?>
                <td>
                    <?php if(intval($row["pkSalon"]) <> 43 && intval($row["pkSalon"]) <> 44):?>
                        <a href="?controller=Config&action=ShowMesas&pkSalon=<?php echo $row[0] ?>">Ver mesas</a>
                    <?php endif;?>
                </td>

                <?php
                echo "<td>";
                if(intval($row["pkSalon"]) <> 43 && intval($row["pkSalon"]) <> 44){  
                    echo "<a onclick='abrirModal(" . $row[0] . ",\"" . $row[1] .
                    "\")'><span class='glyphicon glyphicon-pencil' title='Editar los datos de un Salon'></span></a>";
                }
                echo "</td>";
                echo "<td>";
                echo $grilla;
                echo "</td>";
                $query_c = "SELECT * FROM cajas";
                $result_c = $db->executeQuery($query_c);
                while ($row_c = $db->fecth_array($result_c)) {
                    $query_sal = "Select * from accion_caja where pk_accion = '".$row[0]."' AND tipo_accion = 'SAL' AND caja = '".$row_c["caja"]."'";
                    $result_sal = $db->executeQuery($query_sal);
                    $asociado = 0;
                    while ($row_sal = $db->fecth_array($result_sal)) {
                        echo "<td><a href='?controller=Config&action=QuitaSalonCaja&pkSalon=".$row[0]."&caja=".$row_c["caja"]."'><span class='glyphicon glyphicon-ok' title='Salon Activo en Caja'></span></a></td>";
                        $asociado = 1;
                    }
                    
                    if($asociado === 0){
                        echo "<td><a href='?controller=Config&action=PonSalonCaja&pkSalon=".$row[0]."&caja=".$row_c["caja"]."'><span class='glyphicon glyphicon-minus-sign' title='Salon Inactivo en Caja'></span></a></td>";                     
                    }
                }
                echo "</tr>";
            }
            ?>

            <div id="modalSalones" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title"><label id="tituloModalSalones"></label></h4>
                        </div>
                        <div class="modal-body">
                            <form id="formSalones">
                                <input name="id" id="txtIdSalon" style="display: none;"/>
                                Escriba Aqui el nombre del Salon
                                <input name="nombre" class="form-control" id="txtNombreSalon" placeholder="Ejemplo: Salon 01, VIP , etc." />
                            </form>
                        </div>
                        <div class="modal-footer">
                            <div id="dlg-buttonsCancelarCuenta">

                                <button class="btn btn-primary" onclick="saveSalon2()">Guardar</button>

                                <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>
                            </div>                            
                        </div>
                    </div>
                </div>
            </div>

            <div id="modalSaloneseliminar" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title"><label id="tituloModalSalones2"></label></h4>
                        </div>
                        <div class="modal-body">
                            <form id="formSalon2" class="form">
                                <input name="id1" id="txtIdSalon" style="display: none;"/>                                        
                                <strong id="txtMensajeeliminar"></strong>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <div id="dlg-buttonsCancelarCuenta">
                                <button id="btnColor" class="btn btn-danger" onclick="deleteSalon()">Aceptar</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>
                            </div>                            
                        </div>
                    </div>
                </div>
            </div>

            </tbody>


        </table>

    </center>




    <!--// fin registro-->

</div>
</body>

<script>
    var url = "";
    var mensajeeliminar = "";
    function abrirModal($id, $nombre) {
        $('#modalSalones').modal('show');
        $('#tituloModalSalones').html('Editando Salon');
        $('#txtIdSalon').val($id);
        $('#txtNombreSalon').val($nombre);
        url = "<?php echo class_config::get('urlApp') ?>/?controller=Config&action=Edit";

    }
    function registrarSalon() {
        $('#modalSalones').modal('show');
        $('#tituloModalSalones').html('Registrando Salon');
        $('#txtIdSalon').val("");
        $('#txtNombreSalon').val("");
        url = "<?php echo class_config::get('urlApp') ?>/?controller=Config&action=SaveSalon";
    }

    function eliminarSalon($id) {
        $('#modalSaloneseliminar').modal('show');
        $('#tituloModalSalones2').html('Eliminando Salon');
        $('#txtMensajeeliminar').html('¿Seguro que desea deshabilitar este Salón?');
        $('#txtIdSalon').val($id);
        $('#btnColor').removeClass();
        $('#btnColor').addClass('btn btn-danger');
        url = "<?php echo class_config::get('urlApp') ?>/?controller=Config&action=Delete";
    }

    function activarSalon($id) {
        $('#modalSaloneseliminar').modal('show');
        $('#tituloModalSalones2').html('Habilitando Salon');
        $('#txtMensajeeliminar').html('¿Seguro que desea habilitar este Salón?');
        $('#txtIdSalon').val($id);
        $('#btnColor').removeClass();
        $('#btnColor').addClass('btn btn-primary');
        url = "<?php echo class_config::get('urlApp') ?>/?controller=Config&action=Active";
    }

    function saveSalon2() {
        $.post(url, $('#formSalones').serialize(),
                function(data) {
                    location.reload();
                });
        $('#modalSalones').modal('hide');
    }

    function deleteSalon() {
        $.post(url, {id: $('#txtIdSalon').val()},
        function(data) {
            location.reload();
        });
        $('#modalSaloneseliminar').modal('hide');
    }

    function activeSalon() {
        $.post(url, {id: $('#txtIdSalon').val()},
        function(data) {
            location.reload();
        });
        $('#modalSaloneseliminar').modal('hide');
    }


    $(document).ready(function() {
        $('#tblSalones').DataTable();
    });
    $('#txtTotalMesas').numeric({negative: false});
    $('#txtActualizarcantidadMesas').numeric({negative: false});

    $(document).ready(function()
    {
        _listSalones('cmb_Salon');
        _listSalones('eliminar_cmb_Salon');
        _listSalones('cmb_RegistrarSalon');

    });

    function registrarNuevoSalon() {
        $('#modalSalones').show();
        $('#tituloSalones').dialog('setTitle', 'Ingresando nuevo Salon');
        $('#frmSalon').form('clear');
        url = '<?php echo Class_config::get('urlApp') ?>?controller=Config&&action=SaveSalon';
    }


    function saveSalon() {
        //            console.log($("#frmEmpleados").form('validate'));
        var param = {NombreSalon: $('#txtnuevosalon').val()};
        if ($("#frmSalon").form('validate') == true) {
            $.ajax({
                type: "GET",
                url: url,
                data: param, //$("#frmCategoria").serialize(), // Adjuntar los campos del formulario enviado.
                dataType: 'html',
                success: function(data)

                {
                    if (data == "true") {
                        //                        $('#dlg-Plato').dialog('close'); // close the dialog
                        //                        $('#tblCategorias').datagrid('reload');
                    }
                    else {
                        $.messager.show({
                            title: 'estado',
                            msg: "Se ha registrado el Salon correctamente"
                        });
                    }
                    //                    loadCategoria();
                    $('#dlg-Salon').dialog('close');
                }
            });
        }
        else {

            $.messager.show({
                title: 'Error',
                msg: "No se han Completado los campos requeridos"
            });
        }
    }

    function registrarMesas() {
        var param = {IdSalon: $('#cmb_RegistrarSalon').val(), Mesa: $('#txtNombreMesa').val(), Totalmesa: $('#txtTotalMesas').val()};
        $.ajax({
            type: "GET",
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Config&&action=SaveMesa",
            data: param, //$("#frmRegistrarSalones").serialize(), // Adjuntar los campos del formulario enviado.
            dataType: 'html',
            success: function(data)

            {
                $.messager.show({
                    title: 'Estado',
                    msg: "Se ha Registrado los salones Correctamente"
                });
                $("#frmRegistrarSalones").form('clear');

            }

        });
    }


    function ActualizarSalones() {
        $.ajax({
            type: "GET",
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Config&&action=ActualizarSalones",
            data: $("#frmActualizarSalones").serialize(), // Adjuntar los campos del formulario enviado.
            dataType: 'html',
            success: function(data)
            {
                $.messager.show({
                    title: 'Estado',
                    msg: "Se han Actualizado las mesas Correctamente"
                });
                $("#frmActualizarSalones").form('clear');
            }

        });
    }


    function EliminarMesas() {
        $.ajax({
            type: "GET",
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Config&&action=EliminarMesas",
            data: $("#frmActualizarSalones").serialize(), // Adjuntar los campos del formulario enviado.
            dataType: 'html',
            success: function(data)

            {
                $.messager.show({
                    title: 'Estado',
                    msg: "Se han Actualizado las mesas Correctamente"
                });
                $("#frmActualizarSalones").form('clear');

            }

        });
    }


    function EliminarMesaEspecifica() {
        $.ajax({
            type: "GET",
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Config&&action=EliminarMesaEspecifica",
            data: $("#frmEliminarMesas").serialize(), // Adjuntar los campos del formulario enviado.
            dataType: 'html',
            success: function(data)

            {
                $.messager.show({
                    title: 'Estado',
                    msg: "Se ha eliminado la mesa Correctamente"
                });
                $("#frmEliminarMesas").form('clear');

                //
                //
            }

        });
    }

</script>