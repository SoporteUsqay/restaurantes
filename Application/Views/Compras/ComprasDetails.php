<?php 
include 'Application/Views/template/header.php';
require_once 'reportes/recursos/componentes/MasterConexion.php'; 
require_once 'ComprasHelper.php';
$objcon = new MasterConexion();
$f = $objcon->consulta_arreglo("Select * from cierrediario where pkCierreDiario = 1");

$obj = new Application_Models_CajaModel();
$fechaCaja = $obj->fechaCierre();
?>

<body>

    <?php
        $objViewMenu = new Application_Views_IndexView();
        $objViewMenu->showContent();

        $db = new SuperDataBase();
    ?>
    
    <div class="container-fluid">

        <br>
        <br>
        <br>
        <br>


        <div class="panel panel-primary">

            <div class="panel-heading">
                <h4><i class="fa fa-clipboard"></i> Detalle de la Compra </h4>
            </div>

            <div class="panel-body">

                <?php

                    $id = $_GET['Id'];

                    $query = "SELECT
                        compras.*, tipocomprobante.descripcion AS comprobante_nombre,
                        CONCAT(
                            provedor.ruc,
                            ' ',
                            provedor.razon
                        ) AS proveedor_nombre,
                        CONCAT(
                            trabajador.apellidos,
                            ' ',
                            trabajador.nombres
                        ) AS trabajador_nombre,
                        moneda.nombre as moneda_nombre,
                        moneda.simbolo as moneda_simbolo
                    FROM
                        compras
                    LEFT JOIN tipocomprobante ON compras.tipo_documento_id = tipocomprobante.pkTipoComprobante
                    LEFT JOIN provedor ON compras.proveedor_id = provedor.pkProvedor
                    LEFT JOIN trabajador ON compras.trabajador_id = trabajador.pkTrabajador
                    LEFT JOIN moneda ON compras.moneda_id = moneda.id
                    WHERE compras.id = $id
                    LIMIT 1";

                    $res = $db->executeQueryEx($query);

                    $main = [];

                    while ($row = $db->fecth_array($res)) {
                        $main = $row;
                    }

                    $can_update = is_null($main['fecha_caja']);

                ?>

                <table class="table">
                    <tr>
                        <td>Fecha</td>
                        <th><?php echo $main['fecha'] ?></th>
                        <td>Documento</td>
                        <th><?php echo $main['comprobante_nombre'] ?></th>
                    </tr>
                    <tr>
                        <td>Serie</td>
                        <th><?php echo $main['serie'] ?></th>
                        <td>Correlativo</td>
                        <th><?php echo $main['correlativo'] ?></th>
                    </tr>
                    <tr>
                        <td>Proveedor</td>
                        <th colspan="3"><?php echo $main['proveedor_nombre'] ?></th>
                    </tr>
                    <tr>
                        <td>Registrada por</td>
                        <th><?php echo $main['trabajador_nombre'] ?></th>
                        <td>Moneda</td>
                        <th><?php echo $main['moneda_nombre'] ?> (<?php echo $main['moneda_simbolo'] ?>)</th>
                    </tr>
                </table>

                
                
                <?php if ($can_update): ?>
                <div class="text-right">
                    <button type="button" class="btn btn-success" onclick="openModal(0)">
                        <i class="fa fa-plus-circle"></i>
                        Nuevo Detalle 
                    </button>
                    <!-- <button type="button" class="btn btn-success" onclick="openModal(0)">
                        <i class="fa fa-plus-circle"></i>
                        Nuevo Detalle Servicio
                    </button> -->
                    <button type="button" class="btn btn-primary" onclick="openModal(7)">
                        <i class="fa fa-money"></i>
                        Ingresar a Caja
                    </button>
                </div>
                <?php endif ?>

                <br>

                <table id="tblCompras" class="table">

                    <thead>
                        <th>#</th>
                        <th>Concepto</th>
                        <th>Tipo Impuesto</th>
                        <th class="text-right">Cantidad</th>
                        <th class="text-right">Precio</th>
                        <!-- <th class="text-right">V. Unt.</th> -->
                        <th class="text-right">SubTotal</th>
                        <th class="text-right">IGV</th>
                        <th class="text-right">Descuento</th>
                        <th class="text-right">Total</th>
                        <th></th>
                    </thead>
                
                    <tbody>

                        <?php 
                            error_reporting(E_ALL);

                            $comprasHelper = new ComprasHelper();

                            $comprasHelper->setIGV($main['porcentaje_igv']);
                            $comprasHelper->setICBPER($main['tasa_icbper']);

                            $totales = [
                                "gravado" => 0,
                                "inafecto" => 0,
                                "exonerado" => 0,
                                "gratuito" => 0, 
                                "icbper" => 0,
                    
                                "subtotal" => 0,
                                "igv" => 0,
                                "descuento" => 0,
                                "total" => 0,
                    
                                "total_detraccion" => 0,
                                "total_percepcion" => 0,
                                "total_retencion" => 0,
                            ];

                            $query = $comprasHelper->getSQLDetalles($id);

                            $res = $db->executeQueryEx($query);
                            
                            $index = 1;
                            while ($row = $db->fecth_array($res)):

                                $_temp = $comprasHelper->calculateDetailTotales($row);
                                
                                foreach ($totales as $index_total => $total) {
                                    if (array_key_exists($index_total, $_temp)) {
                                        $totales[$index_total] += $_temp[$index_total];
                                    }
                                }
                        ?>
                        <tr>
                            <td><?php echo $index++ ?></td>
                            <td><?php echo is_null($row['insumo_nombre']) ? $row['concepto_nombre'] : $row['insumo_nombre'] ?></td>
                            <td><?php echo $row['tipo_impuesto_nombre'] ?></td>
                            <td class="text-right"><?php echo floatval($row['cantidad']) ?></td>
                            <td class="text-right"><?php echo floatval($row['precio']) ?></td>
                            <!-- <td class="text-right"><?php echo round($valor_unitario * $row['cantidad'], 2) ?></td> -->
                            <td class="text-right"><?php echo floatval($row['cantidad'] * $row['precio']) ?></td>
                            <td class="text-right"><?php echo round($_temp['igv'], 2) ?></td>
                            <td class="text-right"><?php echo floatval($row['descuento']) ?></td>
                            <td class="text-right"><?php echo floatval(($row['cantidad'] * $row['precio']) - $row['descuento']) ?></td>
                            <td class="text-right">
                                <?php if ($can_update): ?>
                                <a class="btn" onclick='openModal(6, <?php echo json_encode($row) ?>)'>
                                    <i class="fa fa-pencil text-info"></i>
                                </a>
                                <a class="btn" onclick="deleteDetail(<?php echo $row['id'] ?>)">
                                    <i class="fa fa-trash-o text-danger"></i>
                                </a>
                                <?php endif ?>
                            </td>
                        </tr>

                        <?php 
                            endwhile;
                        ?> 

                    </tbody>
                </table>

                <br>

                <div class="">
                    <div class="col-xs-12 col-md-6">
                        
                        <?php if ($can_update): ?>
                        <div class="text-center">
                            <div class="btn-group textce">
                                <button class="btn btn-warning" onclick="openModal(3)">
                                    Detracción
                                </button>
                                <button class="btn btn-warning" onclick="openModal(4)">
                                    Retención
                                </button>
                                <button class="btn btn-warning" onclick="openModal(5)">
                                    Percepción
                                </button>
                            </div>
                        </div>
                        <?php endif ?>

                        <br>
                        <br>
    
                        <table id="tblDocs" class="table">
                            <thead>
                                <th>Documento</th>
                                <th class="text-right">%</th>
                                <th class="text-right">Total</th>
                                <th class="text-right"></th>
                            </thead>
                            <tbody id="tblBodyDocs">
                            <?php 

                                $query = $comprasHelper->getSQLDocumentos($id);

                                $res = $db->executeQueryEx($query);

                                $existen_docs = false;

                                while ($row = $db->fecth_array($res)):
                                    $existen_docs = true;
                                    
                                    $_temp = $comprasHelper->calculateDocumentosTotales($row, $totales['total']);

                                    foreach ($totales as $index_total => $total) {
                                        if (array_key_exists($index_total, $_temp)) {
                                            $totales[$index_total] += $_temp[$index_total];
                                        }
                                    }
                            ?>
                                <tr>
                                    <td><?php 
                                        switch ($row['documento_id']) {
                                            case 1: 
                                                echo 'DETRACCIÓN'; 
                                                break;
                                            case 2: 
                                                echo 'PERCEPCIÓN'; 
                                                break;
                                            case 3: 
                                                echo 'RETENCIÓN';
                                                break;
                                        }
                                    ?></td>
                                    <td class="text-right"><?php echo floatval($row['porcentaje']) ?>%</td>
                                    <td class="text-right"><?php echo number_format($totales['total'] * $row['porcentaje'] / 100, 2) ?></td>
                                    <td class="text-right">
                                        <?php if ($can_update): ?>
                                        <a class="btn" onclick="deleteDocument(<?php echo $row['id'] ?>)">
                                            <i class="fa fa-trash-o text-danger"></i>
                                        </a>
                                        <?php endif ?>
                                    </td>
                                </tr>
                            <?php 
                                endwhile;

                                if (!$existen_docs) {
                                    echo "<script>$('#tblDocs').hide()</script>";
                                }
                            ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-xs-12 col-md-4 col-md-offset-2">
                        <table class="table">
                            
                            <?php if ($totales['gravado'] > 0): ?>
                            <tr>
                                <td>Gravado</td>
                                <td class="text-right"><?php echo $main['moneda_simbolo'] ?></td>
                                <th class="text-right"><?php echo number_format($totales['gravado'], 2) ?></th>
                            </tr>
                            <?php endif ?>
        
                            <?php if ($totales['inafecto'] > 0): ?>
                            <tr>
                                <td>Inafecto</td>
                                <td class="text-right"><?php echo $main['moneda_simbolo'] ?></td>
                                <th class="text-right"><?php echo number_format($totales['inafecto'], 2) ?></th>
                            </tr>
                            <?php endif ?>
        
                            <?php if ($totales['exonerado'] > 0): ?>
                            <tr>
                                <td>Exonerado</td>
                                <td class="text-right"><?php echo $main['moneda_simbolo'] ?></td>
                                <th class="text-right"><?php echo number_format($totales['exonerado'], 2) ?></th>
                            </tr>
                            <?php endif ?>
                                
                            <?php if ($totales['gratuito'] > 0): ?>
                            <tr>
                                <td>Gratuito</td>
                                <td class="text-right"><?php echo $main['moneda_simbolo'] ?></td>
                                <th class="text-right"><?php echo number_format($totales['gratuito'], 2) ?></th>
                            </tr>
                            <?php endif ?>
                                
                            <?php if ($totales['icbper'] > 0): ?>
                            <tr>
                                <td>ICBPER (<?php echo floatval($main['tasa_icbper']) ?>)</td>
                                <td class="text-right"><?php echo $main['moneda_simbolo'] ?></td>
                                <th class="text-right"><?php echo number_format($totales['icbper'], 2) ?></th>
                            </tr>
                            <?php endif ?>
                            
                            <tr>
                                <td>IGV (<?php echo $main['porcentaje_igv'] ?>%)</td>
                                <td class="text-right"><?php echo $main['moneda_simbolo'] ?></td>
                                <th class="text-right"><?php echo number_format($totales['igv'], 2) ?></th>
                            </tr>
                            
                            <tr>
                                <td>SubTotal</td>
                                <td class="text-right"><?php echo $main['moneda_simbolo'] ?></td>
                                <th class="text-right"><?php echo number_format($totales['subtotal'], 2) ?></th>
                            </tr>
        
                            <tr>
                                <td>Descuento</td>
                                <td class="text-right"><?php echo $main['moneda_simbolo'] ?></td>
                                <th class="text-right"><?php echo number_format($totales['descuento'], 2) ?></th>
                            </tr>
        
                            <tr>
                                <td><strong>Total</strong></td>
                                <td class="text-right"><strong><?php echo $main['moneda_simbolo'] ?></strong></td>
                                <th class="text-right"><strong><?php echo number_format($totales['total'], 2) ?></strong></th>
                            </tr>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    
    </div>

    
    <div id="modalForm" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Registrar Detalle de Compra</h4>
                </div>
                <div class="modal-body">

                    <form id="frmCompra">

                        <input type="text" name="compra_id" value="<?php echo $id ?>" hidden>

                        <div class="text-center">
                            <div class="row">
                                <div class="col-xs-12 col-md-3">
                                    <input type="radio" id="tipo_insumo" value="1" name="tipo_concepto" checked>
                                    <label for="tipo_insumo">Insumo</label>
                                </div>
                                <div class="col-xs-12 col-md-3">
                                    <input type="radio" id="tipo_descripcion" value="2" name="tipo_concepto">
                                    <label for="tipo_descripcion">Otro</label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-md-8">
                                <div class="form-group" id="ctnInsumo">
                                    <label for="">Insumo
                                        <button type="button" class="btn btn-success btn-sm" onclick="openModal(9)">
                                            <i class="fa fa-plus-circle"></i>
                                        </button>
                                    </label>
                                    <select name="insumo_id" id="cmbInsumo" class="form-control"></select>
                                </div>
                                <div class="form-group" id="ctnDescripcion">
                                    <label for="">Descripción 
                                        <button type="button" class="btn btn-success btn-sm" onclick="openModal(8)">
                                            <i class="fa fa-plus-circle"></i>
                                        </button>
                                    </label>
                                    <select name="descripcion_id" id="cmbDescripcion" class="form-control"></select>
                                </div>
                            </div>  
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <label for="">Tipo de Impuesto</label>
                                    <select name="tipo_impuesto_id" id="cmbTipoInsumo" class="form-control">
                                    <?php 

                                        $query = "SELECT
                                            *
                                        FROM
                                            tipo_impuesto
                                        ";

                                        $res = $db->executeQueryEx($query);
                                        
                                        while ($row = $db->fecth_array($res)):
                                    ?>
                                        <option value="<?php echo $row['id'] ?>"><?php echo $row['nombre'] ?></option>
                                    <?php endwhile ?>
                                    </select>
                                </div>
                            </div>  
                            <!-- <div class="col-xs-12 col-md-6">
                                <div class="form-group">
                                    <label for="">Descripción</label>
                                    <input type="text" name="descripcion" class="form-control">
                                </div>
                            </div>   -->
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <label for="">Cantidad</label>
                                    <input type="number" name="cantidad" id="cantidad" class="form-control" value="1">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <label for="">Precio</label>
                                    <input type="number" name="precio" id="precio" class="form-control" value="0">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <label for="">SubTotal</label>
                                    <input type="text" name="subtotal" id="subtotal" class="form-control" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            
                            
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <label for="">Descuento</label>
                                    <input type="number" name="descuento" id="descuento" class="form-control" value="0">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <label for="">Total</label>
                                    <input type="number" name="total" id="total" class="form-control" readonly>
                                </div>
                            </div>
                        </div>

                        


                    </form>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="guardarDetalleCompra()">
                        <i class="fa fa-save"></i>
                        Guardar
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cerrar</button>
                </div>
            </div>
        </div>
    </div> 
    
    <div id="modalFormEdit" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Editar Detalle de Compra - <span id="lblEditModal"></span></h4>
                </div>
                <div class="modal-body">

                    <form id="frmCompraE">

                        <input type="text" name="id" id="detalleID" hidden>

                        <div class="row">
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <label for="">Cantidad</label>
                                    <input type="number" name="cantidad" id="cantidadE" class="form-control" value="1">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <label for="">Precio</label>
                                    <input type="number" name="precio" id="precioE" class="form-control" value="0">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <label for="">SubTotal</label>
                                    <input type="text" name="subtotal" id="subtotalE" class="form-control" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            
                            
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <label for="">Descuento</label>
                                    <input type="number" name="descuento" id="descuentoE" class="form-control" value="0">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <label for="">Total</label>
                                    <input type="number" name="total" id="totalE" class="form-control" readonly>
                                </div>
                            </div>
                        </div>

                        


                    </form>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="editarDetalleCompra()">
                        <i class="fa fa-save"></i>
                        Guardar
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cerrar</button>
                </div>
            </div>
        </div>
    </div> 
    
    <div id="modalDetraccion" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Registrar Detracción</h4>
                </div>
                <div class="modal-body">

                    <form id="frmDetraccion">
                        <input type="text" name="documento_id" value="1" hidden>
                        <input type="text" name="compra_id" value="<?php echo $id ?>" hidden>

                        <div class="form-group">
                            <label for="">Definiciones</label>
                            <select name="definicion_id" id="cmbDefinicion" class="form-control">
                                <option value="">Seleccione</option>
                            <script>
                                var lista_porcentajes_detraccion = [];
                            </script>
                            <?php 

                                $query = "SELECT
                                    *
                                FROM
                                    porcentaje_detraccion
                                ";

                                $res = $db->executeQueryEx($query);
                                
                                while ($row = $db->fecth_array($res)):
                            ?>
                                <script>
                                    lista_porcentajes_detraccion.push(<?php echo json_encode($row) ?>)
                                </script>
                                <option value="<?php echo $row['id'] ?>"><?php echo $row['nombre'] ?></option>
                            <?php endwhile ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-md-6">
                                <div class="form-group">
                                    <label for="">Porcentaje (%)</label>
                                    <input type="text" name="porcentaje" id="porcentajeDetraccion" class="form-control" readonly>
                                </div>
                            </div>  
                            <div class="col-xs-12 col-md-6">
                                <div class="form-group">
                                    <label for="">Fecha</label>
                                    <input type="date" name="fecha" value="<?php echo $main['fecha'] ?>" class="form-control">
                                </div>
                            </div>  
                        </div>

                        <div class="form-group">
                            <label for="">N° Voucher</label>
                            <input type="text" name="voucher" class="form-control">
                        </div>


                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="guardarDetraccion()">
                        <i class="fa fa-save"></i>
                        Guardar
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cerrar</button>
                </div>
            </div>
        </div>
    </div> 
    
    <div id="modalPercepcion" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Registrar Percepción</h4>
                </div>
                <div class="modal-body">

                    <form id="frmPercepcion">
                        <input type="text" name="documento_id" value="2" hidden>
                        <input type="text" name="compra_id" value="<?php echo $id ?>" hidden>

                        <div class="row">
                            <div class="col-xs-12 col-md-6">
                                <div class="form-group">
                                    <label for="">Porcentaje Percepción (%)</label>
                                    <input type="text" name="porcentaje" id="porcentajePercepcion" class="form-control">
                                </div>
                            </div>  
                            <div class="col-xs-12 col-md-6">
                                <div class="form-group">
                                    <label for="">Fecha</label>
                                    <input type="date" name="fecha" value="<?php echo $main['fecha'] ?>" class="form-control">
                                </div>
                            </div>  
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="guardarPercepcion()">
                        <i class="fa fa-save"></i>
                        Guardar
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cerrar</button>
                </div>
            </div>
        </div>
    </div> 
    
    <div id="modalRetencion" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Registrar Retención</h4>
                </div>
                <div class="modal-body">

                    <form id="frmRetencion">
                        <input type="text" name="documento_id" value="3" hidden>
                        <input type="text" name="compra_id" value="<?php echo $id ?>" hidden>

                        <div class="row">
                            <div class="col-xs-12 col-md-6">
                                <div class="form-group">
                                    <label for="">Porcentaje Retención (%)</label>
                                    <input type="text" name="porcentaje" id="porcentajeRetencion" class="form-control">
                                </div>
                            </div>  
                            <div class="col-xs-12 col-md-6">
                                <div class="form-group">
                                    <label for="">Fecha</label>
                                    <input type="date" name="fecha" value="<?php echo $main['fecha'] ?>" class="form-control">
                                </div>
                            </div>  
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="guardarRetencion()">
                        <i class="fa fa-save"></i>
                        Guardar
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cerrar</button>
                </div>
            </div>
        </div>
    </div> 
    
    <div id="modalCaja" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Registrar Salida de Caja - (<?php echo $main['moneda_simbolo'] ?> <?php echo $totales['total'] ?>)</h4>
                </div>
                <div class="modal-body">

                    <form id="frmCaja">
                        <input type="text" name="compra_id" value="<?php echo $id ?>" hidden>

                        <div class="alert alert-danger text-center">
                            <i class="fa fa-warning"></i>
                            Una vez ingresada la compra a caja ya no se podrá editar.
                        </div>

                        <div class="text-center">
                            <div class="row">
                                <div class="col-xs-12 col-md-6">
                                    <input type="radio" id="tipo_unico" value="1" name="tipo_pago" checked>
                                    <label for="tipo_unico">Pago Único</label>
                                </div>
                                <div class="col-xs-12 col-md-6">
                                    <input type="radio" id="tipo_cuotas" value="2" name="tipo_pago">
                                    <label for="tipo_cuotas">Pago en Cuotas</label>
                                </div>
                            </div>
                        </div>

                        <hr style="margin: 10px 0;">

                        <div id="pnl_unico" class="row">
                            <div class="col-xs-12 col-md-6">
                                <div class="form-group">
                                    <label for="">Caja</label>
                                    <!-- <input type="text" name="caja" value="<?php echo $_COOKIE["c"] ?>" class="form-control" readonly> -->
                                    <select name="caja" class="form-control">
                                        <?php
                                            $query = "select * from cajas";

                                            $res = $db->executeQueryEx($query);

                                            while($row = $db->fecth_array($res)):
                                        ?>
                                            <option value="<?php echo $row['caja'] ?>" <?php echo $row['caja'] == $_COOKIE["c"] ? 'selected' : '' ?>><?php echo $row['caja'] ?></option>
                                        <?php endwhile ?>
                                        <option value="FE">Fondos Externos</option>
                                    </select>
                                </div>
                            </div>  
                            <div class="col-xs-12 col-md-6">
                                <div class="form-group">
                                    <label for="">Medio de Pago</label>
                                    <select name="medio_pago" class="form-control">
                                        <?php
                                            $query = "select * from medio_pago where moneda = ${main['moneda_id']} and estado = 1";

                                            $res = $db->executeQueryEx($query);

                                            while($row = $db->fecth_array($res)):
                                        ?>
                                            <option value="<?php echo $row['id'] ?>"><?php echo $row['nombre'] ?></option>
                                        <?php endwhile ?>
                                    </select>
                                </div>
                            </div>  
                            <div class="col-xs-12 col-md-6">
                                <div class="form-group">
                                    <label for="">Fecha</label>
                                    <input type="date" name="fecha" class="form-control" value="<?php echo $fechaCaja ?>" readonly>
                                </div>
                            </div>  
                            <div class="col-xs-12 col-md-6">
                                <div class="form-group">
                                    <label for="">Total <?php echo $main['moneda_simbolo'] ?></label>
                                    <input type="text" name="total" value="<?php echo $totales['total'] ?>" class="form-control" readonly>
                                </div>
                            </div>  
                        </div>

                        <div id="pnl_cuotas" class="">

                            <div class="row">
                                
                                <div class="col-xs-12 col-md-4">
                                    <div class="form-group">
                                        <label for="">Fecha</label>
                                        <input type="date" name="fecha_cuotas" id="cuotaFecha" class="form-control" value="<?php echo $fechaCaja ?>">
                                    </div>
                                </div>  
                                <div class="col-xs-12 col-md-4">
                                    <div class="form-group">
                                        <label for="">Total</label>
                                        <input type="number" name="total_cuotas" id="cuotaTotal" value="<?php echo $totales['total'] ?>" class="form-control">
                                    </div>
                                </div>  

                                <div class="col-xs-12 col-md-4">
                                    <div class="form-group">
                                        <label for="" style="color: transparent; display: block">.</label>
                                        <input type="checkbox" name="pago_efectuado" id="pago_efectuado" value="1">
                                        <label for="pago_efectuado">¿Es pago efectuado?</label>
                                    </div>
                                </div>  
                                
                            </div>
                            <div class="row">

                                <div class="col-xs-12 col-md-4" id="ctnPagoEfectuado1">
                                    <div class="form-group">
                                        <label for="">Caja</label>
                                        <select name="caja_cuotas" id="cuotaCaja" class="form-control">
                                        <?php
                                            $query = "select * from cajas";

                                            $res = $db->executeQueryEx($query);

                                            while($row = $db->fecth_array($res)):
                                        ?>
                                            <option value="<?php echo $row['caja'] ?>" <?php echo $row['caja'] == $_COOKIE["c"] ? 'selected' : '' ?>><?php echo $row['caja'] ?></option>
                                        <?php endwhile ?>
                                        <option value="FE">Fondos Externos</option>
                                    </select>
                                    </div>
                                </div>  

                                <div class="col-xs-12 col-md-4" id="ctnPagoEfectuado2">
                                    <div class="form-group">
                                        <label for="">Medio de Pago</label>
                                        <select name="medio_pago_cuotas" id="cuotaMedioPago" class="form-control">
                                            <?php
                                                $query = "select * from medio_pago where moneda = ${main['moneda_id']} and estado = 1";

                                                $res = $db->executeQueryEx($query);

                                                while($row = $db->fecth_array($res)):
                                            ?>
                                                <option value="<?php echo $row['id'] ?>"><?php echo '(' . $row['id'] . ') ' . $row['nombre'] ?></option>
                                            <?php endwhile ?>
                                        </select>
                                    </div>
                                </div>  
 
                                <div class="col-xs-12 col-md-4">
                                    <label for="" style="color: transparent" id="ctnPagoEfectuado3">.</label>
                                    <button type="button" class="btn btn-default btn-block" onclick="addCuota(<?php echo $totales['total'] ?>, '<?php echo $fechaCaja ?>')">
                                        <i class="fa fa-plus"></i>
                                        Agregar
                                    </button>
                                </div> 
                            </div>

                            <hr>
                        
                            <table class="table table-striped">
                                <thead>
                                    <th class="text-center">Fecha</th>
                                    <th class="text-right"></th>
                                    <th class="text-right">Total</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Caja</th>
                                    <th class="text-center">MP</th>
                                    <th class="text-center"></th>
                                </thead>
                                <tbody id="tblBodyCuotas">
                                </tbody>
                            </table>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="guardarMovimientoCaja(<?php echo $id ?>, <?php echo $totales['total'] ?>)">
                        <i class="fa fa-save"></i>
                        Guardar
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cerrar</button>
                </div>
            </div>
        </div>
    </div> 
    
    <div id="modalAddConcepto" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Registrar Concepto de Compra</h4>
                </div>
                <div class="modal-body">

                    <form id="frmAddConcepto">

                        <div class="form-group" id="ctnInsumo">
                            <label for="">Descripción</label>
                            <input type="text" name="nombre" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="">Precio</label>
                            <input type="number" name="precio" class="form-control" value="0">
                        </div>

                    </form>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="guardarConceptoCompra()">
                        <i class="fa fa-save"></i>
                        Guardar
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cerrar</button>
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
                            <input type="number" name="precio" class="form-control" step="0.01" required>
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
    <script src="Application/Views/Compras/js/ComprasDetails.js.php"></script>

    <script>
        var compra_id = <?php echo $id ?>;
        var total_compra = <?php echo $totales['total'] ?>;
        var can_update = <?php echo json_encode($can_update) ?>;


        $(document).ready(function () {
            $("#tblCompras").DataTable({
                "dom": 'Blfrtip',
                "bSort": false,
                "pageLength" : 5,
                "lengthMenu": [[5, 10, 20, -1], [5, 10, 20, 'Todos']],
                "buttons": [
                    {
                        extend: 'excelHtml5',
                        title: 'Compra - <?php echo $main['fecha'] . '- N° ' . $main['serie'] . ' ' . $main['correlativo'] ?>',
                        exportOptions: {
                            columns: [0,1,2,3,4,5,6,7,8]
                        },
                    },
                    {
                        extend: 'pdfHtml5',
                        orientation: 'landscape',
                        // orientation: 'portrait',
                        alignment: 'center',
                        pageSize: 'LEGAL',
                        customize: function(doc) {
                            doc.content[1].margin = [ 100, 0, 100, 0 ] //left, top, right, bottom
                        },
                        exportOptions: {
                            columns: [0,1,2,3,4,5,6,7,8]
                        },
                        title: 'Compra - <?php echo $main['fecha'] . '- N° ' . $main['serie'] . ' ' . $main['correlativo'] ?>',
                    },
                    {
                        extend: 'print',
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                        exportOptions: {
                            columns: [0,1,2,3,4,5,6,7,8]
                        },
                        title: 'Compra - <?php echo $main['fecha'] . '- N° ' . $main['serie'] . ' ' . $main['correlativo'] ?>',
                    }
                ]
            });
        });
    </script>

</body>

</html>