<?php include 'Application/Views/template/header.php'; ?>
<meta charset="UTF-8">
<body>
    <?php
    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showContent();
    ?>    
    <div class="container">

        <br><br><br><br>

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4><i class="glyphicon glyphicon-user"></i> Personal</h4>
                    </div>
                    <div class="panel-body">
                        <fieldset>
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#PersonalActivo" data-toggle="tab">Activos</a>
                                </li>
                                <li><a href="#PersonalInactivo" data-toggle="tab">Inactivos</a>
                                </li>
                                <p class="text-right">
                                    <button onclick="modalRegistrarPersonal()" type="button" class="btn btn-success">
                                        <span class="glyphicon glyphicon-plus-sign" aria-hidden="true" ></span> Nuevo Personal
                                    </button>
                                </p>
                            </ul>
                            <br>
                            <br>
                            <div class="tab-content">
                                <div class="tab-pane active" id="PersonalActivo" >  
                                    <table id="tblPersonalActivo" title="Empleados" class="table table-borderer">
                                        <thead>
                                            <tr>
                                                <th class="text-center">ID</th>
                                                <th>Nombres</th>
                                                <th>Apellidos</th>
                                                <th>Tipo de Trabajador</th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $db = new SuperDataBase();
                                            $sucursal = UserLogin::get_pkSucursal();
                                            $query = "SELECT * FROM trabajador t  inner join tipotrabajador tt on tt.pkTipoTrabajador=t.pkTipoTrabajador where pkTrabajador > 1 and estado=0;";
                                            $result = $db->executeQuery($query);
                                            while ($row = $db->fecth_array($result)) {
                                                echo "<tr class='success'>";
                                                echo "<td>";
                                                echo $row['pkTrabajador'];
                                                echo "</td>";
                                                echo "<td>";
                                                echo utf8_encode($row['nombres']);
                                                echo "</td>";
                                                echo "<td>";
                                                echo $row['apellidos'];
                                                echo "</td>";
                                                echo "<td>";
                                                echo $row['descripcion'];
                                                echo "</td>";
                                                echo "<td>";
                                                if($row["descripcion"] == "ANULADOR" || $row["descripcion"] == "PERSONALIZADO"|| $row["descripcion"] == "CAJERO"){
                                                    echo "<a href='reportes/permisos_trabajador.php?idt=".$row['pkTrabajador']."'>Permisos</a>";
                                                }
                                                echo "</td>";
                                                echo "<td>";
                                                // if($row["descripcion"] == "ANULADOR" || $row["descripcion"] == "PERSONALIZADO"|| $row["descripcion"] == "CAJERO"){
                                                    echo "<a href='reportes/permisos_botones_trabajador.php?idt=".$row['pkTrabajador']."'>Botones</a>";
                                                // }
                                                echo "</td>";
                                                echo "<td>";
                                                echo "<a onclick='modalEditarPersonal(" . $row['pkTrabajador'] . ",\"" . $row['documento'] . "\",\"" . $row['nombres'] . "\",\"" . $row['apellidos'] . "\",\"" . $row['direccion'] . "\",\"" . $row['pkTipoTrabajador'] . "\",\"" . '' . "\",\"" . $row['estado'] . "\")'><span class='glyphicon glyphicon-pencil'></span></a>";
                                                echo "</td>";
                                                echo "<td>";
                                                echo "<a onclick='modalEliminarPersonal(" . $row['pkTrabajador'] . ")'><span class='glyphicon glyphicon-minus-sign' title='Eliminar Personal'></span></a>";
                                                echo "</td>";
                                                echo "<td>";
                                                echo "<a onclick='modalExpandirPersonal(" . $row['pkTrabajador'] . ",\"" . $row['documento'] . "\",\"" . $row['nombres'] . "\",\"" . $row['apellidos'] . "\",\"" . $row['direccion'] . "\",\"" . $row['descripcion'] . "\",\"" . $row['password'] . "\",\"" . $row['estado'] . "\")'><span class='glyphicon glyphicon-zoom-in' title='Expandir Personal'></span></a>";
                                                echo "</td>";
                                                echo "</tr>";
                                                ?>
                                            <?php }
                                            ?>

                                        </tbody>
                                    </table>
                                </div>

                                <div class="tab-pane" id="PersonalInactivo">
                                    <table id="tblPersonalInactivo" title="Empleados" class="table display" width="100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nombres</th>
                                                <th>Apellidos</th>
                                                <th>Tipo de Trabajador</th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $db = new SuperDataBase();
                                            $sucursal = UserLogin::get_pkSucursal();
                                            $query = "SELECT * FROM trabajador t  inner join tipotrabajador tt on tt.pkTipoTrabajador=t.pkTipoTrabajador where estado=1;";
                                            $result = $db->executeQuery($query);
                                            while ($row = $db->fecth_array($result)) {
                                                echo "<tr class='danger'>";
                                                echo "<td>";
                                                echo $row['pkTrabajador'];
                                                echo "</td>";
                                                echo "<td>";
                                                echo utf8_encode($row['nombres']);
                                                echo "</td>";
                                                echo "<td>";
                                                echo $row['apellidos'];
                                                echo "</td>";
                                                echo "<td>";
                                                echo $row['descripcion'];
                                                echo "</td>";
                                                echo "<td>";
                                                echo "<a onclick='modalActivarPersonal(" . $row[0] . ")'><span class='glyphicon glyphicon-ok' title='Habilitar un Trabajador'></span></a>";
                                                echo "</td>";
                                                echo "<td>";
                                                echo "<a onclick='modalExpandirPersonal(" . $row['pkTrabajador'] . ",\"" . $row['documento'] . "\",\"" . $row['nombres'] . "\",\"" . $row['apellidos'] . "\",\"" . $row['direccion'] . "\",\"" . $row['descripcion'] . "\",\"" . $row['password'] . "\",\"" . $row['estado'] . "\")'><span class='glyphicon glyphicon-zoom-in' title='Expandir Personal'></span></a>";
                                                ?>
                                                </td
                                                </tr>
                                            <?php }
                                            ?>

                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div id="modalPersonal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title"><label id="tituloModalPersonal"></label></h4>
                                        </div>
                                        <div class="modal-body">
                                            <form id="frmEmpleados">
                                                <input type='hidden' id='id' name='id' value='0'/>
                                                <br>
                                                <label>DNI:</label>
                                                <input type="number" id="dni" maxlength="20" name="document" title="Documento de Identidad" class="form-control" required="true">
                                                <br>
                                                <label>Nombres:</label>
                                                <input name="names" id="nombres" class="form-control" required="true" title="Ingrese aquí el nombre del empleado">
                                                <br>
                                                <label>Apellidos:</label>
                                                <input id="txtApellidosTrabajador" name="apellidos" class="form-control" title="Ingrese aquí los apellidos del empleado" required="true">
                                                <br>
                                                <label>Direccion:</label>
                                                <input name="address" class="form-control" required="true" id="direccion">
                                                <br>
                                                <label>Tipo de trabajador:</label>
                                                <select name="pkArea" class="form-control" id="cmbTipoUsuario"  title="Debe Elegir una area" required="true"
                                                        ></select>
                                                <br>
                                                <label>Contraseña</label>
                                                <input type="password" id="contra" class="form-control" name="password1" maxlength="500">
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-primary" type="button" onclick="guardarPersonal()">Guardar</button>
                                            <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="modalExpandirPersonal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title"><label id="tituloModalExpandirPersonal"></label></h4>
                                        </div>
                                        <div class="modal-body">
                                            <form id="frmEmpleados">
                                                <input type='hidden' id='idExp' name='id' value='0'/>
                                                <br>
                                                <label>DNI:</label>
                                                <input id="dniExp" name="documentExp" class="form-control" onKeyPress="return Numero(event);"  readonly>
                                                <br>
                                                <label>Nombres:</label>
                                                <input name="names" id="nombresExp" class="form-control" title="Ingrese aquí el nombre del empleado" readonly>
                                                <br>
                                                <label>Apellidos:</label>
                                                <input id="txtApellidosTrabajadorExp" name="apellidos" class="form-control" title="Ingrese aquí los apellidos del empleado" readonly>
                                                <br>
                                                <label>Direccion:</label>
                                                <input name="address" class="form-control" id="direccionExp" readonly>
                                                <br>
                                                <label>Tipo de trabajador:</label>
                                                <input name="address" class="form-control" id="tipoTrabajadorExp" readonly>
                                                <br>
                                                <label>Contraseña</label>
                                                <input type="password" id="contraExp" class="form-control" name="password1" readonly>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">Aceptar</button>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="modalEliminarPersonal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title"><label id="tituloModalPersonal2"></label></h4>
                                        </div>
                                        <div class="modal-body">
                                            <form id="formTipo2">                        
                                                <input name="id" id="id" style="display: none;"/>
                                                <strong id="txtMensajeeliminar"></strong>
                                            </form>
                                        </div>
                                        <div class="modal-footer">

                                            <button id="btnColor" class="btn btn-primary" onclick="deletePersonal()">Aceptar</button>

                                            <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>

                                        </div>
                                    </div>
                                </div>
                            </div>  

                        </fieldset>

                    </div>
                </div>
            </div>
        </div>

        <script src="Application/Views/Personal/js/AdminPersonal.js.php"></script>
        <script type="text/javascript">
            $('#tblPersonalActivo').DataTable();
            $('#tblPersonalInactivo').DataTable();
            function modalRegistrarPersonal() {
                $('#modalPersonal').modal('show');
                $('#tituloModalPersonal').html('Registrando Nuevo Personal');
                $('#document').val("");
                $('#names').val("");
                $('#apellidos').val("");
                $('#address').val("");
                $('#pkarea').val("");
                $('#password1').val("");
                url = "<?php echo Class_config::get('urlApp') ?>/?controller=WorkPeople&&action=Save";
            }
            function modalEditarPersonal($pkTrabajador, $documento, $nombres, $apellidos, $direccion, $descripcion, $contra) {
                $('#modalPersonal').modal('show');
                $('#tituloModalPersonal').html('Editando Trabajador');
                $('#id').val($pkTrabajador);
                $('#dni').val($documento);
                $('#nombres').val($nombres);
                $('#txtApellidosTrabajador').val($apellidos);
                $('#direccion').val($direccion);
                $('#cmbTipoUsuario').val($descripcion);
                $('#contra').val($contra);
                url = "<?php echo Class_config::get('urlApp') ?>/?controller=WorkPeople&&action=Update";
            }

            function guardarPersonal()
            {
                $.post(url,{pkTrabajador: $('#id').val(),documento: $('#dni').val(),nombres: $('#nombres').val(),apellidos: $('#txtApellidosTrabajador').val(),direccion: $('#direccion').val(),pkTipo: $('#cmbTipoUsuario').val(),clave: $('#contra').val()},
                function(data) {
                    location.reload();
                });
                $('#modalPersonal').modal('hide');
            }

            function modalEliminarPersonal($id) {
                $('#modalEliminarPersonal').modal('show');
                $('#tituloModalPersonal2').html('Eliminando Personal');
                $('#txtMensajeeliminar').html('¿Seguro que desea eliminar este Trabajador?');
                $('#id').val($id);
                $('#btnColor').removeClass();
                $('#btnColor').addClass('btn btn-danger');
                url = "<?php echo class_config::get('urlApp') ?>/?controller=WorkPeople&action=Delete";
            }
            function modalActivarPersonal($id) {
                $('#modalEliminarPersonal').modal('show');
                $('#tituloModalPersonal2').html('Habilitando Trabajador');
                $('#txtMensajeeliminar').html('¿Seguro que desea habilitar el Personal Seleccionado?');
                $('#id').val($id);
                $('#btnColor').removeClass();
                $('#btnColor').addClass('btn btn-primary');
                url = "<?php echo class_config::get('urlApp') ?>/?controller=WorkPeople&action=Active";
            }
            function deletePersonal() {
                $.post(url, {id: $('#id').val()},
                function(data) {
                    location.reload();
                });
                $('#modalEliminarPersonal').modal('hide');
            }
            function modalExpandirPersonal($pkTrabajador, $documento, $nombres, $apellidos, $direccion, $descripcion, $contra) {
                $('#modalExpandirPersonal').modal('show');
                $('#tituloModalExpandirPersonal').html('Datos Completos del Trabajador');
                $('#idExp').val($pkTrabajador);
                $('#dniExp').val($documento);
                $('#nombresExp').val($nombres);
                $('#txtApellidosTrabajadorExp').val($apellidos);
                $('#direccionExp').val($direccion);
                $('#tipoTrabajadorExp').val($descripcion);
                $('#contraExp').val($contra);
            }
        </script>
    </div>
</body>