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
    error_reporting(E_ALL);
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
                m.tipo = 2
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

            <?php
            $db = new SuperDataBase();
            $query = "SELECT c.pkComprobante, c.numeroComprobante, c.tipoComprobante, tc.descripcion,
                date(c.fecha) as fecha, c.fechaModificacion
                FROM comprobante_ingreso c
                left join tipocomprobante tc on tc.pkTipoComprobante=c.tipoComprobante
                where c.tipoComprobante=4 and c.estado = 0 and c.pkComprobante=" . $_REQUEST['Id'] . " 
                order by fechaModificacion desc";
            $result = $db->executeQuery($query);
            $pkComprobante = 0;
            $numeroComprobante = "";
            $tipoComprobante = 0;
            $fecha = "";
            while ($row = $db->fecth_array($result)) {
                $pkComprobante = $row['pkComprobante'];
                $numeroComprobante = $row['numeroComprobante'];
                $tipoComprobante = $row['tipoComprobante'];
                $fecha = $row['fecha'];
            }
            ?>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4><i class="glyphicon glyphicon-list-alt"></i> Detalle de Guía de Salida </h4>
                </div>
                <div class="panel-body">

                    <div class="table-responsive">
                        <table class="table">
                            <tr>
                                <th>Tipo de Guía</th>
                                <td><?php echo 'Guía de Salida' ?></td>
                                <th>N° de Comprobante</th>
                                <td><?php echo $guia['tipo_comprobante'] . ' N° ' . $guia['numero_comprobante'] ?></td>
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
                    

                    <!-- <div class="modal-body" id="formulario" style="display: none">
                        <form id="formGuia" >
                            <center>
                                <label for="rad">Salida Insumo</label>
                                <input type="radio" name="rad" id="CrearMesas" value="1" checked="true" onclick="idinsumo.style.display = 'block', idplato.style.display = 'none', btninsumo.style.display = 'block', btnplato.style.display = 'none'" />
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <label for="rad">Salida Plato</label>
                                <input type="radio" name="rad" id="ActualizarMesas" value="2" onclick="idinsumo.style.display = 'none', idplato.style.display = 'block', btninsumo.style.display = 'none', btnplato.style.display = 'block'" />
                            </center>
                            <div id="idinsumo">
                                <input name="txtIdDetalle" id="txtIdDetalle" class="form-control"  style="display: none;"/>
                                <label>Insumo</label>
                                <input id="txtingreseInsumo" type="text" class="form-control llevar" placeholder="Ingrese el insumo">
                                <input name="txtingreseInsumo-id" id="txtingreseInsumo-id" type="text"  style="display: none" class="form-control"  placeholder="Ingrese el insumo">                            
                                <br>
                                <label >Unidad</label>   
                                <input id="unidadi" type="text" class="form-control llevar" placeholder="Selecciona el Insumo" readonly>
                                <br/>
                                <label for="cantidad">Cantidad</label>
                                <input name="cantidad" class="form-control" id="txtCantidad"  required="true">
                                <br>
                                <label for="descripcion" >Descripción</label>                
                                <input name="descripcion" class="form-control" id="txtDescripcion"  required="true">
                                <br>
                                <label for="precio" style="display: none">Precio Unitario</label>                
                                <input name="precio" style="display: none" class="form-control" id="txtPrecio"  required="true">
                            </div>

                            <div id="idplato">
                                <label>Plato</label>
                                <input id="txtingresePlato" type="text" class="form-control llevar" placeholder="Busque el plato AQUI">
                                <input name="txtingresePlato-id" id="txtingresePlato-id" type="text"  style="display: none" class="form-control"  placeholder="Ingrese el Plato">                            
                                <br>
                                <label>Descripción</label>                
                                <input name="descripcionplato" class="form-control" id="txtDescripcionPlato"  required="true">
                                <br>
                                <label>Cantidad</label>
                                <input name="cantidadplato" class="form-control" id="txtCantidadPlato"  required="true">
                                <br>
                                <label>El plato ha sido para: </label>
                                <br>
                                <select type="text" class="form-control" id="estado">
                                    <option value="0">Para la mesa</option>
                                    <option value="1">Para llevar</option>
                                </select>
                            </div>

                        </form>
                        <div id="btninsumo">
                            <button class="btn btn-primary" onclick="guardarDetalleGuiaSalida(<?php echo $pkComprobante ?>)">Guardar</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close" onclick="abrirFormularioDetalle('formulario')">Cancelar</button>
                        </div>

                        <div id="btnplato">
                            <button class="btn btn-primary" onclick="guardarDetallePlatoSalida(<?php echo $pkComprobante ?>)">Guardar</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close" onclick="abrirFormularioDetalle('formulario')">Cancelar</button>
                        </div>
                    </div> -->
                    <hr>
                    <div class="tab-pane active" id="GuiasActivo">
                        <table id="tblDetalleActivo" title="Detalles" class="table table-borderer" >
                            <thead>
                                <tr>
                                    <th class="text-center">Fecha</th>
                                    <th class="text-center">Almacén</th>
                                    <th class="text-center">Insumo</th>
                                    <th class="text-center">Porción</th>
                                    <th class="text-center">Unidad</th>   
                                    <th class="text-center">Cantidad</th>   
                                    <th class="text-center">Motivo</th>   
                                    <th ></th>                                 
                                </tr>
                            </thead>
                            <tbody>

                                <?php 
                                $query = "SELECT
                                    m.id,
                                    m.fecha,
                                    m.precio,
                                    m.cantidad,
                                    m.motivo,
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
                                    <td class="text-right"><?php echo $row['cantidad'] ?></td>
                                    <td class="text-center"><?php echo $row['motivo'] ?></td>
                                    
                                    <td class="text-center">

                                    <?php if(!$is_eliminada && strtotime($guia['fecha']) === strtotime(date($f["fecha"]))): ?>
                                        <a class="btn" onclick="modalEditarDetalle(
                                            '<?php echo $row['id'] ?>',
                                            '<?php echo $row['precio'] ?>',
                                            '<?php echo $row['cantidad'] ?>',
                                            '<?php echo $row['motivo'] ?>'
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
                            <h4 class="modal-title">Detalle de Guía</h4>
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
                                    <label >Porción de Insumo</label>   
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
                                <label for="">Almacén</label>
                                <select id="cmb_Almacen" name="id_almacen" class="form-control" required="true">
                                    
                                </select>
                                <br>
                                <label for="">Motivo</label>                
                                <input name="motivo" class="form-control"  required="true">
                                <br>
                                
                            </form>
                            
                            <button class="btn btn-primary" onclick="guardarDetalleGuiaSalida(<?php echo $guia['id'] ?>)">Guardar</button>
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
                            <h4 class="modal-title">Editar detalle de Guía</h4>
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
                                <label for="">Motivo</label>                
                                <input name="motivo" class="form-control" id="txtDescripcionE"  required="true">
                                <br>
                                
                            </form>
                            
                            <button type="button" class="btn btn-primary" onclick="guardarEditarDetalle(<?php echo $guia['id'] ?>)">Guardar</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal" onclick="cerrarModal('modalFormDetalle')" aria-label="Close">Cancelar</button>

                        </div>
                    </div>
                </div>
            </div>

            <div id="mdlDetalleGuiaSalida" class="modal fade">
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
                                    <label class="col-sm-4 control-label">Cantidad</label>
                                    <div class="col-sm-8">
                                        <p id="fcantidad" class="form-control-static"></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Descripcion</label>
                                    <div class="col-sm-8">
                                        <p id="fdescripcion" class="form-control-static"></p>
                                    </div>
                                </div>
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
                            </form>
                        </div>
                        <div class="modal-footer">
                            <div id="dlg-buttonsCancelarCuenta">
                                <button class="btn btn-primary" onclick="saveDetalleGuiaSalida()">Guardar</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>
                            </div>                            
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
                                <p id="txtMensajeeliminarDetalle"></p>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button id="btnAceptar" class="btn btn-primary" onclick="deleteDetalleGuiaS()">Aceptar</button>
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
                                    <label for="">Descripción</label>
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
                                    <label for="">Stock Mínimo</label>
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

        </div>
        <link rel="stylesheet" type="text/css" href="Public/select2/css/select2.css" rel="stylesheet">
        <script  type="text/javascript" src="Public/select2/js/select2.js"></script>
        <script src="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.js"></script>
        <script src="reportes/recursos/js/plugins/tablas/dataTables.buttons.min.js"></script>
        <script src="reportes/recursos/js/plugins/tablas/jszip.min.js"></script>
        <script src="reportes/recursos/js/plugins/tablas/pdfmake.min.js"></script>
        <script src="reportes/recursos/js/plugins/tablas/vfs_fonts.js"></script>
        <script src="reportes/recursos/js/plugins/tablas/buttons.html5.min.js"></script>
        <script src="reportes/recursos/js/plugins/tablas/buttons.print.min.js"></script>
        <script type="text/javascript" src="Application/Views/Almacen/js/DetalleGuiaSalida.js.php" ></script>
        <script>
        var can_update = <?php echo json_encode($can_update) ?>;

        $("#tblDetalleActivo").DataTable({
            dom: 'Blfrtip',
            "order": [[0, "desc"]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Detalle Guia - <?php echo $guia['fecha'] . '- N° ' . $guia['numero_comprobante'] ?>',
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
                    title: 'Detalle Guia - <?php echo $guia['fecha'] . '- N° ' . $guia['numero_comprobante'] ?>',
                },
                {
                    extend: 'print',
                    orientation: 'landscape',
                    pageSize: 'LEGAL',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7]
                    },
                    title: 'Detalle Guia - <?php echo $guia['fecha'] . '- N° ' . $guia['numero_comprobante'] ?>',
                }
            ]
        });

        $(document).ready(function () {
            $('#ctntxtInsumoPorcion').hide();
        })
            $(function() {
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
            });

            $(function() {
                $.ajax({
                    url: "<?php echo Class_config::get('urlApp') ?>/?controller=Platos&&action=List",
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        $("#txtingresePlato").autocomplete({
                            source: data,
                            select: function(event, ui) {
                                $("#txtingresePlato").val(ui.item.descripcion);
                                $("#txtingresePlato-id").val(ui.item.pkPlato);
                                return false;
                            }
                        });
                    }
                });
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

                        select__.html('<option value="">Seleccione Porción</option>');

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