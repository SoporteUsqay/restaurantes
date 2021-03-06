<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title><?php echo Class_config::get('nameApplication') ?></title>

        <link rel="stylesheet" href="Public/jquery-ui-1.10.4.custom/css/jquery-ui.min.css">

        <link href="Public/Bootstrap/css/bootstrap.css" rel="stylesheet">
        <link href="Public/Bootstrap/media/css/jquery.dataTables.min.css" rel="stylesheet">

        <link rel="stylesheet" type="text/css" href="Public/css/style.css">


        <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery-1.10.2.js"></script>
        <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery-ui-1.10.4.custom.js"></script>

        <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery.numeric.js"></script>
        <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery.maskedinput.js"></script>
        <script type="text/javascript" src="Public/scripts/body.js.php"></script>
        <script type="text/javascript" src="Public/scripts/listGeneral.js.php"></script>
        <script type="text/javascript" src="Public/scripts/Validation.js.php"></script>
        <script  type="text/javascript" src="Public/Bootstrap/js/bootstrap.min.js"></script>

        <script  type="text/javascript" src="Public/Bootstrap/media/js/jquery.dataTables.min.js"></script>
        <!-- <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/Concurrent.Thread.js"></script> -->

        <link rel="stylesheet" href="Public/Usqay/css/usqay.css">
        <link rel="icon" href="logo.ico"/>

        <link rel="stylesheet" href="ReportesGraficos/Highcharts/api/css/font-awesome.css">

    </head>
<body>
    <?php
    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showContent();
    require_once 'reportes/recursos/componentes/MasterConexion.php'; 
    $objcon = new MasterConexion();
    $f = $objcon->consulta_arreglo("Select * from cierrediario where pkCierreDiario = 1");
    ?>
    <div class="container">

        <br>
        <br>
        <br>
        <br>       
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
            ) AS trabajador,
            m.deleted_at
        FROM
            n_movimiento_almacen m
        LEFT JOIN tipocomprobante tc ON m.tipo_comprobante_id = tc.pkTipoComprobante
        LEFT JOIN n_almacen a ON m.almacen_id = a.id
        LEFT JOIN provedor p ON m.proveedor_id = p.pkProvedor
        LEFT JOIN trabajador tr ON m.trabajador_id = tr.pkTrabajador
        WHERE
            m.tipo = 1
        AND m.id = ".$_REQUEST['Id'] ."
        ";
        $result = $db->executeQuery($query);

        $guia = [];

        $is_eliminada = false;

        while ($row = $db->fecth_array($result)) {
            $guia = $row;

            $is_eliminada = !is_null($guia['deleted_at']);
        }

        $can_update = !$is_eliminada && strtotime($guia['fecha']) === strtotime(date($f["fecha"]));
        ?>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4><i class="glyphicon glyphicon-random"></i> Detalle de Gu??a de Ingreso</h4>
            </div>
            <div class="panel-body">

                <div class="table-responsive">
                    <table class="table">
                        <tr>
                            <th>Tipo de Gu??a</th>
                            <td><?php echo 'Gu??a de Ingreso' ?></td>
                            <th>N?? de Comprobante</th>
                            <!-- echo $guia['tipo_comprobante'] .  -->
                            <td><?php echo 'INGRESO N?? ' . $guia['numero_comprobante'] ?></td>
                        </tr>
                        <tr>
                            <th>Almac??n</th>
                            <td><?php echo $guia['almacen'] ?? '-' ?></td>
                            <th>Proveedor</th>
                            <td><?php echo $guia['proveedor'] ?? '-' ?></td>
                        </tr>
                        <tr>
                            <th>Fecha</th>
                            <td><?php echo $guia['fecha'] ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                </div>

                <?php if(!$is_eliminada && strtotime($guia['fecha']) === strtotime(date($f["fecha"]))): ?>
                    <button class="btn btn-success" id="btnGuardarGuia" onclick="abrirFormulario(<?php echo $guia['id'] ?>,'formulario')">
                        <i class="fa fa-plus"></i>
                        Agregar Detalle
                    </button>
                <?php endif ?>

                <hr>
                <div class="tab-pane active" id="GuiasActivo"> 


                    <table id="tblDetalleActivo" class="table" >
                        <thead>
                                <th >Fecha</th>
                                <th >Almac??n</th>
                                <th >Insumo</th>
                                <th >Porci??n</th>
                                <th >Unidad</th>   
                                <th >Cantidad</th>   
                                <th >P. Unitario</th>   
                                <th >P.Total</th>   
                                <th ></th>   
                        </thead>
                        <tbody>

                        <?php 
                            $query = "SELECT
                                m.id,
                                m.fecha,
                                m.precio,
                                m.cantidad,
                                i.descripcionInsumo AS insumo,
                                u.descripcion AS unidad,
                                a.nombre AS almacen,
                                ip.cantidad as cantidad_porcion,
                                u1.descripcion as nombre_unidad,
                                ip.descripcion
                            FROM
                                n_detalle_movimiento_almacen m
                            LEFT JOIN insumos i ON m.insumo_id = i.pkInsumo
                            LEFT JOIN insumo_porcion ip ON m.insumo_porcion_id = ip.id
                            LEFT JOIN unidad u ON m.unidad_id = u.pkUnidad
                            LEFT JOIN unidad u1 ON ip.unidad_id = u1.pkUnidad
                            LEFT JOIN n_almacen a ON m.almacen_id = a.id
                            WHERE
                                m.movimiento_id = ".$_REQUEST['Id'] ."
                            ";

                            if (!$is_eliminada) {
                                $query .= " AND m.deleted_at IS NULL";
                            }

                            $query .= " order by m.fecha desc";

                            $res = $db->executeQueryEx($query);

                            while ($row = $db->fecth_array($res)) :
                            ?>

                            <tr>
                                <td><?php echo $row['fecha'] ?></td>
                                <td><?php echo $row['almacen'] ?></td>
                                <td><?php echo $row['insumo'] ?></td>
                                <td><?php echo implode(' ', [
                                    floatval($row['cantidad_porcion']) == 0 ? '' : floatval($row['cantidad_porcion']),
                                    ($row['nombre_unidad']),
                                    ($row['descripcion']),
                                ]) ?></td>
                                <td><?php echo $row['unidad'] ?></td>
                                <td class="text-right"><?php echo number_format($row['cantidad'], 2) ?></td>
                                <td class="text-right"><?php echo number_format($row['precio'], 2) ?></td>
                                <td class="text-right"><?php echo number_format($row['precio'] * $row['cantidad'], 2) ?></td>
                                
                                <td class="text-center">

                                <?php if(!$is_eliminada && strtotime($guia['fecha']) === strtotime(date($f["fecha"]))): ?>
                                    <a class="btn" onclick="modalEditarDetalle(
                                            '<?php echo $row['id'] ?>',
                                            '<?php echo $row['precio'] ?>',
                                            '<?php echo $row['cantidad'] ?>'
                                        )">
                                        <span class='glyphicon glyphicon-pencil' title='Modificar Detalle'></span>
                                    </a>

                                    <a class="btn" onclick="newDeleteDetalle(<?php echo $row['id'] ?>)">
                                        <span class='glyphicon glyphicon-minus-sign' title='Eliminar Detalle'></span>
                                    </a>
                                <?php endif ?>
                                
                                </td>

                                <!-- ".$pkComprobante."," . $row['pkIngresoInsumo'] . ",".$row['pkInsumo'].",\"".$row['descripcioninsumo']."\",".$row['cantidad'].",".$row['precioU']." -->
                            </tr>
                            
                            <?php
                            endwhile;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="modalFormDetalle" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Detalle de Gu??a</h4>
                    </div>
                    <div class="modal-body" id="formulario">
                        <form id="formGuia" >    

                            <input type="text" name="movimiento_id" value="<?php echo $_REQUEST['Id'] ?>" hidden>
    
                            <input name="txtIdDetalle" id="txtIdDetalle" class="form-control"  style="display: none;"/>
                            <label >Insumo 
                                <button type="button" class="btn btn-success btn-sm" onclick="showNuevoInsumo()">
                                    <i class="fa fa-plus-circle"></i>
                                </button>
                            </label>   
                            <select name="txtingreseInsumo" id="txtingreseInsumo"></select>
                            <br>
                            <div id="ctntxtInsumoPorcion">
                                <br>
                                <label >Porci??n de Insumo</label>   
                                <select class="form-control select2-lg" name="txtInsumoPorcion" id="txtInsumoPorcion"></select>
                                <br>
                            </div>
                            
                            <br>
                            <label >Unidad</label>   
                            <input id="unidadi" type="text" class="form-control llevar" placeholder="Selecciona el Insumo" readonly>
                            <input id="unidadiD" name="unidad_id" style="display: none;">
                            <br/>
                            <label for="cantidad">Cantidad</label>                
                            <input name="cantidad" class="form-control" id="txtCantidad"  required="true">
                            <br>
                            <label for="precio">Precio Unitario</label>                
                            <input name="precio" class="form-control" id="txtPrecio"  required="true">
                            <br>
                            <label for="">Almac??n</label>
                            <select id="cmb_Almacen" name="id_almacen" class="form-control" required="true">
                                
                            </select>
                            <br>
                            
                        </form>
                        
                        <button class="btn btn-primary" onclick="guardarDetalle(<?php echo $guia['id'] ?>)">Guardar</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal" onclick="cerrarModal('modalFormDetalle')" aria-label="Close">Cancelar</button>

                    </div>
                </div>
            </div>
        </div>

        <div id="modalFormEditDetalle" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Editar detalle de Gu??a</h4>
                    </div>
                    <div class="modal-body" id="formulario">
                        <form id="formGuiaE" >    

                            <input type="text" name="movimiento_id" value="<?php echo $_REQUEST['Id'] ?>" hidden>

                            <input type="text" name="id" id="txtIdDetalleMovimientoE" hidden>
    
                            <label for="cantidad">Cantidad</label>                
                            <input name="cantidad" class="form-control" id="txtCantidadE"  required="true">
                            <br>
                            <label for="precio">Precio Unitario</label>                
                            <input name="precio" class="form-control" id="txtPrecioE"  required="true">
                            <br>
                            
                        </form>
                        
                        <button type="button" class="btn btn-primary" onclick="guardarEditarDetalle(<?php echo $guia['id'] ?>)">Guardar</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal" onclick="cerrarModal('modalFormDetalle')" aria-label="Close">Cancelar</button>

                    </div>
                </div>
            </div>
        </div>

        <div id="modalMensajesGuias" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><label id="tituloModalMensajesGuias"></label></h4>
                    </div>
                    <div class="modal-body">
                        <form id="formGuiaUpdate" >    
                            <input name="txtIdGuia" id="txtIdGuia" class="form-control"  style="display: none;"/>
                            <input name="txtIdDetalleInsumo" id="txtIdDetalleInsumo" class="form-control"  style="display: none;"/>
                            <label >Insumo</label>                              
                            <input disabled="disabled" name="insumo" id="txtingreseInsumoDetalle" type="text" class="form-control" placeholder="Ingrese el insumo">
                            <input name="txtingreseInsumo-id2" id="txtingreseInsumo-id2" type="text"  style="display: none" class="form-control"  placeholder="Ingrese el insumo">
                            <br>
                            <label >Cantidad</label>                
                            <input name="Cantidad" class="form-control" id="txtCantidadDetalle"  required="true">
                            <br>
                            <label >Precio Unitario</label>                
                            <input name="Precio" class="form-control" id="txtPrecioDetalle"  required="true">                        

                        </form>
                    </div>
                    <div class="modal-footer">
                        <div id="dlg-buttonsCancelarCuenta">

                            <button class="btn btn-primary" onclick="saveDetalleGuia()">Guardar</button>

                            <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>
                        </div>                            
                    </div>
                </div>
            </div>
        </div>

        <div id="modalProveedor2" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><label id="tituloModalProveedor2"></label></h4>
                    </div>
                    <div class="modal-body">
                        <form id="formProveedor2">                        
                            <input name="id" id="txtIdProveedor21" style="display: none;"/>
                            <strong id="txtMensajeeliminar"></strong>
                        </form>
                    </div>
                    <div class="modal-footer">

                        <button id="btnColor" class="btn btn-primary" onclick="deleteProveedor()">Aceptar</button>

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
        
        <div id="modalEliminarDetalle" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><label id="tituloModalDetalle"></label></h4>
                    </div>
                    <div class="modal-body">
                        <form id="formTipo2">                        
                            <input name="id" id="id" style="display: none;"/>
                            <strong id="txtMensajeeliminarDetalle"></strong>
                        </form>
                    </div>
                    <div class="modal-footer">

                        <button id="btnAceptar" class="btn btn-primary" onclick="deleteDetalle()">Aceptar</button>

                        <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>

                    </div>
                </div>
            </div>
        </div>  

        <div id="modalNewInsumo" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><label id="tituloModalGuia">Registrar Nuevo Insumo</label></h4>
                    </div>
                    <form id="formNewInsumo" onsubmit="guardarInsumo()" method="POST">                        
                    <div class="modal-body">
                            <div class="form-group">
                                <label for="">Descripci??n</label>
                                <input name="descripcion" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="">Unidad de Medida</label>
                                <select name="pkInsumo" class="form-control" required>
                                    <option value="">--Seleccione--</option>
                                    <?php
                                        $query = "SELECT
                                            *
                                        FROM unidad 
                                        WHERE estado = 0";
                                        
                                        $res = $db->executeQueryEx($query);

                                        while ($row = $db->fecth_array($res)) :
                                    ?>
                                        <option value="<?php echo $row['pkUnidad'] ?>"><?php echo $row['descripcion'] ?></option>
                                    <?php endwhile ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="">Tipo de Insumo</label>
                                <select name="pkTipoInsumo" class="form-control" required>
                                    <option value="">--Seleccione--</option>
                                    <?php
                                        $query = "SELECT
                                            *
                                        FROM tipo_insumo 
                                        WHERE estado = 0";
                                        
                                        $res = $db->executeQueryEx($query);

                                        while ($row = $db->fecth_array($res)) :
                                    ?>
                                        <option value="<?php echo $row['pkTipoInsumo'] ?>"><?php echo $row['descripcion'] ?></option>
                                    <?php endwhile ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="">Precio de Compra</label>
                                <input type="number" name="precio" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="">Stock M??nimo</label>
                                <input type="number" name="stockMinimo" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="">Proveedor Principal</label>
                                <select name="provedor" class="form-control" required>
                                    <option value="">--Seleccione--</option>
                                    <?php
                                        $query = "SELECT
                                            *
                                        FROM provedor 
                                        WHERE estado = 0";
                                        
                                        $res = $db->executeQueryEx($query);

                                        while ($row = $db->fecth_array($res)) :
                                    ?>
                                        <option value="<?php echo $row['pkProvedor'] ?>"><?php echo $row['ruc'] . ' - ' . $row['razon'] ?></option>
                                    <?php endwhile ?>
                                </select>
                            </div>

                            <div style="display:none;">
                                <select required="true"class="form-control" name="estado" id="estado">
                                    <option value="0" selected>Habilitado</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="">% Merma / Unidad</label>
                                <input type="number" name="porcentajeMerma" value="0" class="form-control" required>
                            </div>

                    </div>
                    <div class="modal-footer">

                        <button id="boton_guardar" class="btn btn-primary">Guardar</button>

                        <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>

                    </div>
                    </form>
                </div>
            </div>
        </div>
    
    <link rel="stylesheet" type="text/css" href="Public/select2/css/select2.css" rel="stylesheet">
    <script  type="text/javascript" src="Public/select2/js/select2.js"></script>
    <link rel="stylesheet" href="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.css">
    <script src="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/dataTables.buttons.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/jszip.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/pdfmake.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/vfs_fonts.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/buttons.html5.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/buttons.print.min.js"></script>
    <script type="text/javascript" src="Application/Views/Almacen/js/DetalleGuias.js.php" ></script>    
    </div>


<script>

    var can_update = <?php echo json_encode($can_update) ?>;

    $("#tblDetalleActivo").DataTable({
        dom: 'Blfrtip',
        "order": [[0, "desc"]],
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Detalle Guia - <?php echo $guia['fecha'] . '- N?? ' . $guia['numero_comprobante'] ?>',
                exportOptions: {
                    columns: [0,1,2,3,4,5,6,7]
                },
            },
            {
                extend: 'pdfHtml5',
                orientation: 'landscape',
                alignment: 'center',
                pageSize: 'LEGAL',
                customize: function(doc) {
                    doc.content[1].margin = [ 100, 0, 100, 0 ] //left, top, right, bottom
                },
                exportOptions: {
                    columns: [0,1,2,3,4,5,6,7]
                },
                title: 'Detalle Guia - <?php echo $guia['fecha'] . '- N?? ' . $guia['numero_comprobante'] ?>',
            },
            {
                extend: 'print',
                orientation: 'landscape',
                pageSize: 'LEGAL',
                exportOptions: {
                    columns: [0,1,2,3,4,5,6,7]
                },
                title: 'Detalle Guia - <?php echo $guia['fecha'] . '- N?? ' . $guia['numero_comprobante'] ?>',
            }
        ]
    });

    $(document).ready(function () {
        $('#ctntxtInsumoPorcion').hide();
    })
       
$(function () {

    var lista_insumos = [];

    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Insumo&&action=List",
        type: 'POST',
        dataType: 'json',
        success: function (data) {
            lista_insumos = data;

            var select_insumo = $('#txtingreseInsumo');

            select_insumo.html('<option value="">Seleccione</option>');

            for (let i of data) {
                select_insumo.append(`
                    <option value="${i.id}">${i.label}</option>
                `)
            }

            select_insumo.select2({
                width: '100%',
                dropdownParent: $('#modalFormDetalle')
            });

            select_insumo.on('select2:select', function (e) {
                var data = e.params.data;

                var insumo = lista_insumos.find(it => it.id == data.id);

                if (!insumo) {
                    $('#txtInsumoPorcion').val('')
                    $('#txtInsumoPorcion').trigger('change')
                    return
                }

                $("#txtPrecio").val(insumo.price);
                $("#unidadi").val(insumo.unidad);
                $("#unidadiD").val(insumo.unidad_id);
                $("#txtCantidad").val(1);
                setTimeout(() => {
                    $("#txtCantidad").select();
                }, 100);

                loadPorciones(insumo.id);
            });
        }
    }); 

    $('#txtInsumoPorcion').select2({
        width: '100%',
    });

    function loadPorciones(insumo_id) {
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Almacen&&action=ListInsumoPorcion&insumo_id="+ insumo_id ,
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                lista_platos = data;

                var select__ = $('#txtInsumoPorcion');

                select__.html('<option value="">Seleccione Porci??n</option>');

                for (let i of data) {
                    select__.append(`
                        <option value="${i.id}">${i.descripcion}</option>
                    `)
                }

                $('#ctntxtInsumoPorcion').hide();

                if (data.length > 0) {
                    $('#ctntxtInsumoPorcion').show();
                }

                select__.select2({
                    width: '100%',
                });

                select__.on('select2:select', function (e) {
                    var data = e.params.data;

                    var item = lista_platos.find(it => it.id == data.id);

                    setTimeout(() => {
                        $("#txtCantidad").select();
                    }, 100);
                });
            }

        });
    }

    
           
});

    function showNuevoInsumo() {
        $('#modalNewInsumo').modal('show')
    }

    function guardarInsumo() {
        
        $('#modalNewInsumo').modal('hide');
        $.post("<?php echo class_config::get('urlApp') ?>/?controller=Insumo&action=Save", $('#formNewInsumo').serialize(),
        function() {
            alert('Nuevo Insumo registrado');
            location.reload();
        });
        
        return false;  
    }
       
      
</script>
</body>
</html>