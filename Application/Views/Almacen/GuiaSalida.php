<?php include 'Application/Views/template/header.php';
require_once 'reportes/recursos/componentes/MasterConexion.php'; 
$objcon = new MasterConexion();
$f = $objcon->consulta_arreglo("Select * from cierrediario where pkCierreDiario = 1");
?>
<body>

    <?php
    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showContent();
    ?>
    <div class="container-fluid">

        <br><br><br><br>

        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4><i class="glyphicon glyphicon-list-alt"></i> Guia de salida</h4>
                <!--<h4><i class="glyphicon glyphicon-book"></i> Guias de salida</h4>-->
            </div>
            <div class="panel-body">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#GuiasActivo" data-toggle="tab">Activos</a>
                    </li>
                    <li><a href="#GuiasInactivo" data-toggle="tab">Inactivos</a>
                    </li>
                    <p class="text-right">
                        <button onclick="mdlRegistrarGuiaSalida()" type="button" class="btn btn-success" id="btnGuardarGuia">
                            <span class="glyphicon glyphicon-plus-sign" aria-hidden="true" ></span> Nueva guia
                        </button>
                    </p>
                </ul>
                <br/>    
                <br/>
                <div class="tab-content">
                    <div class="tab-pane active" id="GuiasActivo"> 

                        <table id="tblGuiaActiva" title="Guias de salida" class="table table-borderer" >
                            <thead>
                                <tr>                                    
                                    <th>Fecha</th>                                    
                                    <th>N° Guía</th>                                    
                                    <th>Modificado por</th>
                                    <th></th>  
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                $db = new SuperDataBase();

                                $query = "SELECT
                                    m.id,
                                    m.fecha,
                                    m.tipo_comprobante_id,
                                    m.numero_comprobante,
                                    tc.descripcion AS tipo_comprobante,
                                    a.id AS almacen_id,
                                    a.nombre AS almacen,
                                    concat(p.ruc, ' - ', p.razon) AS proveedor,
                                    concat(
                                        tr.apellidos,
                                        ' ',
                                        tr.nombres
                                    ) AS trabajador
                                FROM
                                    n_movimiento_almacen m
                                LEFT JOIN tipocomprobante tc ON m.tipo_comprobante_id = tc.pkTipoComprobante
                                LEFT JOIN n_almacen a ON m.almacen_id = a.id
                                LEFT JOIN provedor p ON m.proveedor_id = p.pkProvedor
                                LEFT JOIN trabajador tr ON m.trabajador_id = tr.pkTrabajador
                                WHERE
                                    m.tipo = 2
                                AND deleted_at IS NULL";

                                $result = $db->executeQuery($query);
                                while ($row = $db->fecth_array($result)):
                                ?>
                                    <tr class=''>     

                                        <td><?php echo $row['fecha']; ?></td>
                                        <td><?php echo $row['numero_comprobante']; ?></td>
                                        <td><?php echo $row['trabajador']; ?></td>

                                        <td class="text-right">
                                            <?php if(strtotime($row["fecha"]) === strtotime(date($f["fecha"]))): ?>
                                                <a class="btn" onclick="mdlEditarGuiaSalida(
                                                    '<?php echo $row['id'] ?>', 
                                                    '<?php echo $row['numero_comprobante'] ?>', 
                                                    '<?php echo $row['fecha'] ?>')" title='Editar Guia'>
                                                    <span class='glyphicon glyphicon-pencil'></span>
                                                </a>
                                            <?php endif ?>
                                            <a class="btn" onclick="verDetalles('<?php echo $row['id'] ?>')" title='Ver Items'>
                                                <span class='glyphicon glyphicon-log-out'></span>
                                            </a>
                                            <a class="btn" onclick="sendPrint('<?php echo $row['id'] ?>')" title='Imprimir Guia'>
                                                <span class='glyphicon glyphicon-print'></span>
                                            </a>
                                            <?php if(strtotime($row["fecha"]) === strtotime(date($f["fecha"]))): ?>
                                                <a class="btn" onclick="mdlEliminarGuiaSalida('<?php echo $row['id'] ?>')" title='Anular Guías'>
                                                    <span class='glyphicon glyphicon-remove'></span>
                                                </a>
                                            <?php endif ?>
                                            
                                        </td>

                                    </tr>                               
                                <?php   
                                    endwhile
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane" id="GuiasInactivo">
                        <table id="tblGuiaInactiva" title="Guias de salida" class="table table-borderer" >
                            <thead>
                                <tr>                                    
                                    <th>Fecha</th>                                    
                                    <th>N° Guía</th>                                    
                                    <th>Modificado por</th>
                                    <th></th>  
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                $db = new SuperDataBase();

                                $query = "SELECT
                                    m.id,
                                    m.fecha,
                                    m.tipo_comprobante_id,
                                    m.numero_comprobante,
                                    tc.descripcion AS tipo_comprobante,
                                    a.id AS almacen_id,
                                    a.nombre AS almacen,
                                    concat(p.ruc, ' - ', p.razon) AS proveedor,
                                    concat(
                                        tr.apellidos,
                                        ' ',
                                        tr.nombres
                                    ) AS trabajador
                                FROM
                                    n_movimiento_almacen m
                                LEFT JOIN tipocomprobante tc ON m.tipo_comprobante_id = tc.pkTipoComprobante
                                LEFT JOIN n_almacen a ON m.almacen_id = a.id
                                LEFT JOIN provedor p ON m.proveedor_id = p.pkProvedor
                                LEFT JOIN trabajador tr ON m.trabajador_id = tr.pkTrabajador
                                WHERE
                                    m.tipo = 2
                                AND deleted_at IS NOT NULL";

                                $result = $db->executeQuery($query);
                                while ($row = $db->fecth_array($result)):
                                ?>
                                    <tr class=''>     

                                        <td><?php echo $row['fecha']; ?></td>
                                        <td><?php echo $row['numero_comprobante']; ?></td>
                                        <td><?php echo $row['trabajador']; ?></td>

                                        <td class="text-right">
                                            <a class="btn" onclick="verDetalles('<?php echo $row['id'] ?>')" title='Ver Items'>
                                                <span class='glyphicon glyphicon-log-out'></span>
                                            </a>
                                        </td>

                                    </tr>                               
                                <?php   
                                    endwhile
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div id="mdlGuiaSalida" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><label id="tituloModalGuia"></label></h4>
                    </div>
                    <div class="modal-body">
                        <form id="formGuia">                        
                            <input name="id" id="txtIdGuia" style="display: none;"/>                            
                            <p for="txtNroComprobante">N° Guía</p>
                            <input name="txtNroComprobante" class="form-control" id="NroComprobante"  required="true">                            
                            <br>                            
                            <label>Fecha de Referencia</label>
                            <input class="form-control" type="text" name="Fecha" required="true" id="fecha" value="<?php echo $f["fecha"]; ?>" readonly=""/>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" onclick="guardarGuiaSalida()">Guardar</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="mdlGuiaSalidaDetalle" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><label id="tituloModalDetalleMas"></label></h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal">
                            <label id="pk" style="display: none;"></label>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Fecha de registro</label>
                                <div class="col-sm-8">
                                    <p id="fregistro" class="form-control-static"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Fecha de modificación</label>
                                <div class="col-sm-8">
                                    <p id="fmodificacio" class="form-control-static"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Registrado por</label>
                                <div class="col-sm-8">
                                    <p id="registro" class="form-control-static"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Modificado por</label>
                                <div class="col-sm-8">
                                    <p id="modificado" class="form-control-static"></p>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="mdlElimnarGuiaSalida" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><label id="tituloModalGuia2"></label></h4>
                    </div>
                    <div class="modal-body">
                        <form id="formGuia2">                        
                            <input name="id2" id="txtIdGuia2" style="display: none;"/>
                            <p id="txtMensajeeliminar"></p>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button id="btnColor" class="btn btn-primary" onclick="deleteGuiaSalida()">Aceptar</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>
                    </div>
                </div>
            </div>
        </div> 

    </div>
    <script type="text/javascript" src="Application/Views/Almacen/js/GuiaSalida.js.php" ></script>
</body>
