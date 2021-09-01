<?php 
include 'Application/Views/template/header.php';
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

        <br>
        <br>
        <br>
        <br>       
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4><i class="glyphicon glyphicon-random"></i> Administrar Guias</h4>
            </div>
            <div class="panel-body">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#GuiasActivo" data-toggle="tab">Activos</a>
                    </li>
                    <li><a href="#GuiasInactivo" data-toggle="tab">Inactivos</a>
                    </li>
                    <p class="text-right">
                        <button onclick="modalRegistrarGuia()" type="button" class="btn btn-success" id="btnGuardarGuia">
                            <span class="glyphicon glyphicon-plus-sign" aria-hidden="true" ></span> Nueva Guia
                        </button>
                    </p>
                </ul>
                <br/>    
                <br/>
                <div class="tab-content">
                    <div class="tab-pane active" id="GuiasActivo"> 

                        <table id="tblGuiaActiva" title="Guias de Ingreso" class="table table-borderer" >
                            <thead>
                                <tr>                                    
                                    <th>Fecha</th>                            
                                    <!-- <th>N° Comprobante/Guía</th>                             -->
                                    <th>Documento</th>  
                                    <th>Almacén</th>  
                                    <th>Proveedor</th>
                                    <th>Trabajador</th>  
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
                                    m.tipo = 1
                                AND deleted_at IS NULL";

                                $result = $db->executeQuery($query);
                                while ($row = $db->fecth_array($result)):
                                ?>
                                    <tr class=''>     

                                        <td><?php echo $row['fecha']; ?></td>
                                        <!-- <td><?php echo $row['tipo_comprobante']; ?></td> -->
                                        <td><?php echo $row['numero_comprobante']; ?></td>
                                        <td><?php echo $row['almacen']; ?></td>
                                        <td><?php echo $row['proveedor']; ?></td>
                                        <td><?php echo $row['trabajador']; ?></td>

                                        <td class="text-center">
                                            <?php if(strtotime($row["fecha"]) === strtotime(date($f["fecha"]))): ?>
                                                <a class="btn" onclick="modalEditarGuia(
                                                    '<?php echo $row['id'] ?>', 
                                                    // '<?php echo $row['tipo_comprobante_id'] ?>', 
                                                    '<?php echo $row['numero_comprobante'] ?>', 
                                                    '<?php echo $row['fecha'] ?>', 
                                                    '<?php echo $row['almacen_id'] ?>', 
                                                    '<?php echo $row['proveedor_id'] ?>')" title='Editar Guia'>
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
                                                <a class="btn" onclick="modalEliminarGuia('<?php echo $row['id'] ?>')" title='Anular Guías'>
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

                        <table id="tblGuiaInactiva" title="Guias de Ingreso" class="table table-borderer" >
                            <thead>
                                <tr>                                    
                                    <th>Fecha</th>                            
                                    <!-- <th>N° Comprobante/Guía</th>                             -->
                                    <th>Documento</th>  
                                    <th>Almacén</th>  
                                    <th>Proveedor</th>
                                    <th>Trabajador</th>  
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
                                    m.tipo = 1
                                AND deleted_at IS NOT NULL";

                                $result = $db->executeQuery($query);
                                while ($row = $db->fecth_array($result)):
                                ?>
                                    <tr class=''>     

                                        <td><?php echo $row['fecha']; ?></td>
                                        <!-- <td><?php echo $row['tipo_comprobante']; ?></td> -->
                                        <td><?php echo $row['numero_comprobante']; ?></td>
                                        <td><?php echo $row['almacen']; ?></td>
                                        <td><?php echo $row['proveedor']; ?></td>
                                        <td><?php echo $row['trabajador']; ?></td>

                                        <td class="text-center">
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

        <div id="modalGuia" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><label id="tituloModalGuia"></label></h4>
                    </div>
                    <form id="formGuia" onsubmit="guardarGuia()" method="POST">                        
                    <div class="modal-body">
                            <input name="id" id="txtIdGuia" style="display: none;"/>    

                            <!-- <div class="form-group">
                                <label for="">Tipo de Guía</label>
                                <select id="cmb_TipoComprobante"  name="txtTipoComprobante" class="form-control" required="true">
                                    <option value="1" >BOLETA</option>
                                    <option value="2" >FACTURA</option>
                                    <option value="3" >TRANSFERENCIA</option>
                                </select>
                            </div> -->

                            <div class="form-group">
                                <label for="">Nro Comprobante / Guía</label>
                                <input name="txtNroComprobante" class="form-control" id="NroComprobante" required="true">
                            </div>

                            <div class="form-group">
                                <label for="">Fecha</label>
                                <input name="fecha" class="form-control" id="" value="<?php echo $f["fecha"]; ?>" readonly>
                            </div>

                            <div class="form-group">
                                <label for="">
                                    Proveedor
                                    <button class="btn btn-success btn-sm" onclick="showNuevoProveedor()" type="button">
                                        <span class="glyphicon glyphicon-plus-sign" aria-hidden="true" ></span>
                                    </button>
                                </label>
                                <select id="cmb_Proveedor" name="id_proveedor" class="form-control">
                                    
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="">Almacén</label>
                                <select id="cmb_Almacen" name="id_almacen" class="form-control" required="true">
                                    
                                </select>
                            </div>

                    </div>
                    <div class="modal-footer">

                        <button id="boton_guardar" class="btn btn-primary" >Guardar</button>

                        <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>

                    </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="modalMasDetalles" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><label id="tituloModalDetalleMas"></label></h4>
                    </div>
                    <div class="modal-body">
                        <label id="pk" style="display: none;"></label>
                        <label id="tipo" style="display: none;"></label>
                        <label>Tipo de Guía</label>
                        <br>
                        <label name="tguia"></label>
                        <br>
                        <label>Número de la Guía</label>
                        <br>
                        <label id="nguia"></label>
                        <br>
                        <label>Fecha de Registro</label>
                        <br>
                        <label id="fregistro"></label>
                        <br>
                        <label>Última Fecha de Modificación</label>
                        <br>
                        <label id="fmodificacio "></label>
                        <br>
                        <label>Empresa Proveedora</label>
                        <br>
                        <label id="empresa"></label>
                        <br>
                        <label>RUC</label>
                        <br>
                        <label id="ruc"></label>
                        <br>
                        <label>Razón Social</label>
                        <br>
                        <label id="rsocial"></label>
                        <br>
                        <label>Registrado Por: </label>
                        <br>
                        <label id="registro"></label>
                        <br>
                        <label>Modificado Por: </label>
                        <br>
                        <label id="modificado"></label>
                        <br>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="modalGuia2" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><label id="tituloModalGuia2"></label></h4>
                    </div>
                    <div class="modal-body">
                        <form id="formGuia2">                        
                            <input name="id2" id="txtIdGuia2" style="display: none;"/>
                            <strong id="txtMensajeeliminar"></strong>
                        </form>
                    </div>
                    <div class="modal-footer">

                        <button id="btnColor" class="btn btn-primary" onclick="deleteGuia()">Aceptar</button>

                        <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>

                    </div>
                </div>
            </div>
        </div> 

        <div id="modalVerProveedor" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><label id="tituloModalVerProveedor"></label></h4>
                    </div>
                    <div class="modal-body">
                        <form id="formVerProveedor">                        
                            <input name="id" id="txtIdProveedor2" style="display: none;"/>

                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cerrar</button>

                    </div>
                </div>
            </div>
        </div> 

        <div id="modalNewProveedor" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><label id="tituloModalGuia">Registrar Proveedor</label></h4>
                    </div>
                    <form id="formNewProveedor" onsubmit="guardarProveedor()" method="POST">                        
                    <div class="modal-body">
                            <div class="form-group">
                                <label for="">RUC</label>
                                <input name="ruc" class="form-control" required="true">
                            </div>

                            <div class="form-group">
                                <label for="">Razón Social</label>
                                <input name="razon" class="form-control" required="true">
                            </div>

                            <div class="form-group">
                                <label for="">Dirección</label>
                                <input name="direccion" class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="">Telefono</label>
                                <input name="telefono" class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="">Pagina Web</label>
                                <input name="pagweb" class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="">E-mail</label>
                                <input name="email" class="form-control">
                            </div>
                    </div>
                    <div class="modal-footer">

                        <button id="boton_guardar1" class="btn btn-primary">Guardar</button>

                        <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>

                    </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
    <link rel="stylesheet" type="text/css" href="Public/select2/css/select2.css" rel="stylesheet">
    <script  type="text/javascript" src="Public/select2/js/select2.js"></script>
    <script type="text/javascript" src="Application/Views/Almacen/js/Guias.js.php" ></script>

    <script>

        function showNuevoProveedor() {
            $('#modalNewProveedor').modal('show')
        }

        function guardarProveedor() {
            
            $('#modalNewProveedor').modal('hide');
            $.post("<?php echo class_config::get('urlApp') ?>/?controller=Proveedor&action=Save", $('#formNewProveedor').serialize(),
            function() {
                alert('Proveedor registrado');
                location.reload();
            });
            
            return false;  
        }
    </script>
</body>

<script>

</script>
