<?php
include 'Application/Views/template/header.php';
$pkSalon = "0";
if (isset($_GET['pkSalon'])) {
    $pkSalon = $_GET['pkSalon'];
}
?>
<script>
    function getpkSalon() {
        return "<?php echo $pkSalon ?>";
    }
</script>
<body>

    <?php
    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showContent();
    ?>    
    <div class="container">
        <br>
        <br>
        <br> 
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4><i ></i> Administrando Mesas</h4>
                        <div class="panel-body">
                            <label>Salon</label>
                            <select onchange="buscarMesas()" name="IdSalon" value="<?php echo $pkSalon; ?>" class="form-control" id="cmbSalon" required="true">
                            </select>
                            <br>
                            <br>
                            <fieldset>
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="#Mesas_Activas" data-toggle="tab"> Mesas Activas</a>
                                    </li>
                                    <li><a href="#MesasInactivo" data-toggle="tab"> Mesas Inactivas</a>
                                    </li>
                                    <p class="text-right">
                                        <button onclick="registrarMesas()" type="button" class="btn btn-success">
                                            <span class="glyphicon glyphicon-plus-sign" aria-hidden="true" ></span> Nuevas Mesas
                                        </button>
                                    </p>
                                </ul>
                                <br>
                                <br>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="Mesas_Activas" >
                                        <br>
                                        <table id="tblMesasActivas" class="table table-borderer">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Descripcion</th>
                                                    
                                                    <th>Opciones</th>
                                                </tr>

                                            </thead>
                                            <tbody>
                                                <?php 
                                                $db = new SuperDataBase();
                                                if ($pkSalon == "0")
                                                    $query = "SELECT * FROM mesas where estado=0 AND pkSalon <> 43 AND pkSalon <> 44";
                                                else
                                                    $query = "SELECT * FROM mesas m where pkSalon=$pkSalon and estado=0";

                                                $result = $db->executeQuery($query);
                                                while ($row = $db->fecth_array($result)) {
                                                    
                                                    echo "<tr class='success'>";
                                                    echo "<td>";
                                                    echo $row['pkMesa'];
                                                    echo "</td>";
                                                    echo "<td>";
                                                    echo utf8_encode($row['nmesa']);
                                                    echo "</td>";                                                                                                      
                                                    echo "<td>";
                                                    echo "<a href='#' onclick='DesHabilitarMesas(" . $row[0] . ")'><span class='glyphicon glyphicon-minus-sign' title='Ocultar una Salon'></span></a>";
                                                    echo "</td>";
                                                   
                                                    echo "</tr>";
                                                ?>
                                                <?php }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="tab-pane" id="MesasInactivo">
                                        <table id="tblMesasInactivo" title="Mesas" class="table display" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Descripcion</th>
                                                    <!--<th>Estado</th>-->
                                                    <th>Opciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $db = new SuperDataBase();
                                                if ($pkSalon == "0")
                                                    $query = "SELECT * FROM mesas where estado=3 AND pkSalon <> 43 AND pkSalon <> 44";
                                                else
                                                    $query = "SELECT * FROM mesas m where pkSalon=$pkSalon and estado=3";

                                                $result = $db->executeQuery($query);
                                                while ($row = $db->fecth_array($result)) {
                                                    
                                                    echo "<tr class='danger'>";
                                                    echo "<td>";
                                                    echo $row['pkMesa'];
                                                    echo "</td>";
                                                    echo "<td>";
                                                    echo utf8_encode($row['nmesa']);
                                                    echo "</td>";                                                                                                      
                                                    echo "<td>";
                                                    echo "<a href='#' onclick='HabilitarMesas(" . $row[0] . ",3)'><span class='glyphicon glyphicon-ok' title='Ocultar una Salon'></span></a>";
                                                    echo "</td>";
                                                   
                                                    echo "</tr>";
                                                ?>
                                                <?php }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    
                                </div>
                                
                                <div id="modalCrear_Mesas" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title"><label id="tituloModalCrear_Mesas"></label></h4>
                                            </div>
                                            <div class="modal-body">
                                                <form id="frmCrear_Mesas">
                                                    
                                                    <center>
                                                        <label for="rad">Crear Nuevas Mesas</label>
                                                        <input type="radio" name="rad"  id="CrearMesas" value="1" onclick="txtMesa.disabled=true,txtNombreMesa.disabled=false " />
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        <label for="rad">Actualizar Mesas</label>
                                                        <input type="radio" name="rad" checked="true" id="ActualizarMesas" value="2" onclick="txtNombreMesa.disabled=true,txtMesa.disabled=false " />
                                                    </center>  
                                                    
                                                    <input style="display: none" id="id">
                                                    <label>Salon</label>
                                                    <select name="txtIdSalon" value="<?php echo $pkSalon; ?>" class="form-control" onclick="_listPrefijoMesa('cmbCrearMesas','cmbPrefijoMesas')" id="cmbCrearMesas" required="true">

                                                    </select>
                                                    
                                                    <label>Mesa</label>
                                                    <select name="txtMesa"  class="form-control" id="cmbPrefijoMesas" >

                                                    </select>
                                                    
                                                    <label>Nombre Mesa</label>
                                                    <input id="nomMesa" required="true" name="txtNombreMesa" type="text" class="form-control" >
                                                    <br>
                                                    <center>
                                                        <label for="radcantidad">Inicializar mesas</label>
                                                        <input type="radio" name="radcantidad"  id="Inicializar_Mesas" value="3" onclick="txtDesde.disabled=true,txtHasta.disabled=true,txtTotalMesas.disabled=false " />
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        <label for="rad">Mesas Desde - Hasta</label>
                                                        <input type="radio" name="radcantidad" checked="true" id="Desde_Hasta" value="4" onclick="txtDesde.disabled=false,txtHasta.disabled=false,txtTotalMesas.disabled=true " />
                                                    </center>  

                                                    <label>Cantidad</label>
                                                    <input id="cantidad" required="true" name="txtTotalMesas" type="text" class="form-control" onKeyPress="return Numero(event);">
                                                    <br>
                                                    
                                                    <label>Desde</label>
                                                    <input id="desde" required="true" name="txtDesde" type="text" class="form-control" onKeyPress="return Numero(event);">
                                                    <br>
                                                    
                                                    <label>Hasta</label>
                                                    <input id="hasta" required="true" name="txtHasta" type="text" class="form-control" onKeyPress="return Numero(event);">
                                                    <br>
                                                   
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <div id="dlg-buttonsCancelarCuenta">

                                                    <button class="btn btn-primary" onclick="CrearMesas()">Guardar</button>

                                                    <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>
                                                </div>                            
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="modalEliminarMesa" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title"><label id="tituloModalMesa"></label></h4>
                                            </div>
                                            <div class="modal-body">
                                                <form id="formTipo2">                        
                                                    <input name="id" id="id" style="display: none;"/>
                                                    <strong id="txtMensajeeliminarMesa"></strong>
                                                </form>
                                            </div>
                                            <div class="modal-footer">

                                                <button id="btnColor" class="btn btn-primary" onclick="deleteMesa()">Aceptar</button>

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
        </div>
    </div> 
    <script type="text/javascript" src="Application/Views/Configurations/js/ShowMesas.js.php"></script>
</body>

