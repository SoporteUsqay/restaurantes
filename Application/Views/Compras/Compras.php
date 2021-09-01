<?php
$titulo_importante = 'Compras';
include 'Application/Views/template/header.php';
require_once 'reportes/recursos/componentes/MasterConexion.php';
require_once 'ComprasHelper.php';
$objcon = new MasterConexion();
$f = $objcon->consulta_arreglo("Select * from cierrediario where pkCierreDiario = 1");

$obj = new Application_Models_CajaModel();
$fechaCaja = $obj->fechaCierre();

$filter_inicio = date('Y-m-01');
if (isset($_REQUEST['inicio'])) {
    $filter_inicio = $_REQUEST['inicio'];
}

$filter_fin = date('Y-m-d');
if (isset($_REQUEST['fin'])) {
    $filter_fin = $_REQUEST['fin'];
}

$tipo_documento = null;
if (isset($_REQUEST['tipo'])) {
    $tipo_documento = $_REQUEST['tipo'];
}
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
                <h4>
                    <i class="fa fa-clipboard"></i>
                    Administrar Compras
                </h4>
            </div>

            <div class="panel-body">

                <div>

                    <form id="frmFiltro">

                        <div class="row">
                            <div class="col-xs-12 col-md-3">
                                <div class="form-group">
                                    <label for="">Fecha de Inicio</label>
                                    <input type="text" id="fecha_inicio" name="inicio" value="<?php echo $filter_inicio ?>" class="form-control">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-3">
                                <div class="form-group">
                                    <label for="">Fecha de Fin</label>
                                    <input type="text" id="fecha_fin" name="fin" value="<?php echo $filter_fin ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="">Documento</label>
                                <select name="tipo" id="tipo_documento" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="1" <?php echo $tipo_documento == 1 ? 'selected' : '' ?>>BOLETA</option>
                                    <option value="2" <?php echo $tipo_documento == 2 ? 'selected' : '' ?>>FACTURA</option>
                                    <option value="5" <?php echo $tipo_documento == 5 ? 'selected' : '' ?>>TICKET</option>
                                </select>
                            </div>
                            <div class="col-xs-12 col-md-3">
                                <label for="" style="color: transparent">.</label>
                                <button type="button" class="btn btn-primary" style="display: block" onclick="Filtrar()">
                                    <i class="fa fa-filter"></i>
                                    Filtrar
                                </button>
                            </div>
                        </div>
                    </form>

                </div>


                <div class="text-right">
                    <button type="button" class="btn btn-success" onclick="openModal(0)">
                        <i class="fa fa-plus-circle"></i>
                        Nueva Compra
                    </button>
                </div>

                <br>

                <table id="tblCompras" class="table">

                    <thead>
                        <th>#</th>
                        <th>Fecha</th>
                        <th>Fecha Caja</th>
                        <th>Doc</th>
                        <th>Numeración</th>
                        <th>Proveedor</th>
                        <th class="text-right"></th>
                        <th class="text-right">Subtotal</th>
                        <th class="text-right">IGV</th>
                        <th class="text-right">Descuento</th>
                        <th class="text-right">Total</th>
                        <th></th>
                    </thead>

                    <tbody>

                        <?php

                        $comprasHelper = new ComprasHelper();

                        $db = new SuperDataBase();

                        $sql_where_tipo = '';

                        if ($tipo_documento) {
                            $sql_where_tipo = "AND compras.tipo_documento_id = '$tipo_documento'";
                        }

                        $query = "SELECT
                                compras.*, tipocomprobante.descripcion AS comprobante_nombre,
                                CONCAT(
                                    provedor.ruc,
                                    ' ',
                                    provedor.razon
                                ) AS proveedor_nombre,
                                moneda.simbolo as moneda_nombre,
	                            COUNT(compras_cuotas.id) AS cantidad_cuotas
                            FROM
                                compras
                            LEFT JOIN tipocomprobante ON compras.tipo_documento_id = tipocomprobante.pkTipoComprobante
                            LEFT JOIN provedor ON compras.proveedor_id = provedor.pkProvedor
                            LEFT JOIN moneda ON compras.moneda_id = moneda.id
                            LEFT JOIN compras_cuotas ON (
                                compras.id = compras_cuotas.compra_id
                                AND compras_cuotas.fecha_caja IS NULL
                            )
                            WHERE compras_cuotas.fecha_caja is null
                            $sql_where_tipo
                            AND compras.deleted_at is null
                            AND compras.fecha BETWEEN '$filter_inicio' AND '$filter_fin'
                            GROUP BY
	                            compras.id
                            ";

                        $res = $db->executeQueryEx($query);

                        $totalizadores = [];

                        $index = 1;
                        while ($row = $db->fecth_array($res)) :

                            $comprasHelper->setIGV($row['porcentaje_igv']);
                            $comprasHelper->setICBPER($row['tasa_icbper']);

                            $totales = $comprasHelper->calculateTotales($row['id']);

                            foreach ($totales as $i => $value) {
                                if (key_exists($i, $totalizadores)) {
                                    $totalizadores[$i] += $value;
                                } else {
                                    $totalizadores[$i] = $value;
                                }
                            }

                            $can_update = is_null($row['fecha_caja']);

                            $have_cuotas = $row['cantidad_cuotas'] > 0;
                        ?>
                            <tr>
                                <td><?php echo $index++ ?></td>
                                <td><?php echo $row['fecha'] ?></td>
                                <td class="text-success"><?php echo $row['fecha_caja'] ?></td>
                                <td><?php echo $row['comprobante_nombre'] ?></td>
                                <td><?php echo $row['serie'] . '-' . $row['correlativo'] ?></td>
                                <td><?php echo $row['proveedor_nombre'] ?></td>
                                <td class="text-right"><?php echo $row['moneda_nombre'] ?></td>
                                <td class="text-right"><?php echo number_format($totales['subtotal'], 2) ?></td>
                                <td class="text-right"><?php echo number_format($totales['igv'], 2) ?></td>
                                <td class="text-right"><?php echo number_format($totales['descuento'], 2) ?></td>
                                <td class="text-right"><?php echo number_format($totales['total'], 2) ?></td>
                                <td class="text-right">
                                    <a class="btn" onclick="goDetail(<?php echo $row['id'] ?>)">
                                        <i class="fa fa-eye text-info"></i>
                                    </a>
                                    <?php if (!$can_update && $have_cuotas) : ?>
                                        <a class="btn" onclick='LoadCuotas(<?php echo json_encode($row) ?>)'>
                                            <i class="fa fa-money text-info"></i>
                                        </a>
                                    <?php endif ?>
                                    <?php if ($can_update) : ?>
                                        <a class="btn" onclick='openModal(1, <?php echo json_encode($row) ?>)'>
                                            <i class="fa fa-pencil text-info"></i>
                                        </a>
                                        <a class="btn" onclick="deleteCompra(<?php echo $row['id'] ?>)">
                                            <i class="fa fa-trash-o text-danger"></i>
                                        </a>
                                    <?php endif ?>

                                </td>
                            </tr>

                        <?php endwhile ?>

                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-right"><?php echo 'S/' ?></td>
                            <td class="text-right"><?php echo number_format($totalizadores['subtotal'], 2) ?></td>
                            <td class="text-right"><?php echo number_format($totalizadores['igv'], 2) ?></td>
                            <td class="text-right"><?php echo number_format($totalizadores['descuento'], 2) ?></td>
                            <td class="text-right"><?php echo number_format($totalizadores['total'], 2) ?></td>
                            <td class="text-right">
                            </td>
                        </tr>

                    </tbody>
                </table>

            </div>
        </div>

    </div>


    <div id="modalForm" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Registrar Nueva Compra</h4>
                </div>
                <div class="modal-body">

                    <form id="frmCompra">

                        <div class="form-group">
                            <label for="">Documento</label>
                            <select name="documento_id" id="cmbDocumento" class="form-control">
                                <option value="1">BOLETA</option>
                                <option value="2">FACTURA</option>
                                <option value="5">TICKET</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-md-6">
                                <div class="form-group">
                                    <label for="">Serie</label>
                                    <input type="text" name="serie" class="form-control" id="serie" placeholder="Ejemplo: F001">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-6">
                                <div class="form-group">
                                    <label for="">Correlativo</label>
                                    <input type="text" name="correlativo" class="form-control" id="correlativo" placeholder="Ejemplo: 45">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="">Proveedor
                                <button class="btn btn-success btn-sm" onclick="NuevoProveedor()" type="button">
                                    <span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span>
                                </button>
                            </label>
                            <select name="proveedor_id" id="cmbProveedor" class="form-control"></select>
                        </div>

                        <div class="form-group">
                            <label for="">Fecha</label>
                            <input type="date" name="fecha" id="fecha" value="<?php echo $f['fecha'] ?>" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="">Observaciones</label>
                            <textarea name="observaciones" id="observaciones" cols="30" rows="3" class="form-control"></textarea>
                        </div>


                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="guardarCompra()">
                        <i class="fa fa-save"></i>
                        Guardar
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <div id="modalFormEdit" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Editar Compra - <span id="lblEditModal"></span></h4>
                </div>
                <div class="modal-body">

                    <form id="frmCompraEdit">
                        <input type="text" name="id" id="compraID" hidden>

                        <div class="form-group">
                            <label for="">Documento</label>
                            <select name="documento_id" id="cmbDocumentoE" class="form-control">
                                <option value="1">BOLETA</option>
                                <option value="2">FACTURA</option>
                                <option value="5">TICKET</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-md-6">
                                <div class="form-group">
                                    <label for="">Serie</label>
                                    <input type="text" name="serie" class="form-control" id="serieE" placeholder="Ejemplo: F001">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-6">
                                <div class="form-group">
                                    <label for="">Correlativo</label>
                                    <input type="text" name="correlativo" class="form-control" id="correlativoE" placeholder="Ejemplo: 45">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="">Proveedor
                                <button class="btn btn-success btn-sm" onclick="NuevoProveedor()" type="button">
                                    <span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span>
                                </button>
                            </label>
                            <select name="proveedor_id" id="cmbProveedorE" class="form-control"></select>
                        </div>

                        <div class="form-group">
                            <label for="">Fecha</label>
                            <input type="date" name="fecha" id="fechaE" value="<?php echo $f['fecha'] ?>" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="">Observaciones</label>
                            <textarea name="observaciones" id="observacionesE" cols="30" rows="3" class="form-control"></textarea>
                        </div>


                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="editarCompra()">
                        <i class="fa fa-save"></i>
                        Guardar
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <div id="modalFormPagos" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Registrar Salida de Caja</h4>
                </div>
                <div class="modal-body">

                    <form id="frmCaja">
                        <input type="text" name="compra_id" value="<?php echo $id ?>" hidden>

                        <div class="row">
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <label for="">Caja</label>
                                    <select name="caja" id="cajaCaja" class="form-control">
                                        <?php
                                        $query = "select * from cajas";

                                        $res = $db->executeQueryEx($query);

                                        while ($row = $db->fecth_array($res)) :
                                        ?>
                                            <option value="<?php echo $row['caja'] ?>" <?php echo $row['caja'] == $_COOKIE["c"] ? 'selected' : '' ?>><?php echo $row['caja'] ?></option>
                                        <?php endwhile ?>
                                        <option value="FE">Fondos Externos</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <label for="">Medio de Pago</label>
                                    <select name="medio_pago" id="cajaMedioPago" class="form-control">
                                        <?php
                                        $query = "select * from medio_pago where  estado = 1";

                                        $res = $db->executeQueryEx($query);

                                        while ($row = $db->fecth_array($res)) :
                                        ?>
                                            <option value="<?php echo $row['id'] ?>"><?php echo $row['nombre'] ?></option>
                                        <?php endwhile ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <label for="">Fecha Caja</label>
                                    <input type="text" class="form-control" value="<?php echo $fechaCaja ?>" readonly>
                                </div>
                            </div>
                        </div>


                        <table class="table table-striped">
                            <thead>
                                <th class="text-center">Fecha</th>
                                <th class="text-right"></th>
                                <th class="text-right">Total</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center"></th>
                            </thead>
                            <tbody id="tblBodyCuotas">
                            </tbody>
                        </table>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" type="text/css" href="Public/select2/css/select2.css" rel="stylesheet">
    <script type="text/javascript" src="Public/select2/js/select2.js"></script>
    <link rel="stylesheet" href="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.css">
    <script src="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/dataTables.buttons.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/jszip.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/pdfmake.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/vfs_fonts.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/buttons.html5.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/buttons.print.min.js"></script>
    <script src="Application/Views/Compras/js/Compras.js.php"></script>

    <script>
        $(document).ready(function() {
            $("#tblCompras").DataTable({
                "dom": 'Blfrtip',
                "bSort": false,
                "pageLength": 5,
                "lengthMenu": [
                    [5, 10, 20, -1],
                    [5, 10, 20, 'Todos']
                ],
                "buttons": [{
                        extend: 'excelHtml5',
                        title: 'Compras',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
                        },
                    },
                    {
                        extend: 'pdfHtml5',
                        orientation: 'landscape',
                        // orientation: 'portrait',
                        alignment: 'center',
                        pageSize: 'LEGAL',
                        customize: function(doc) {
                            doc.content[1].margin = [100, 0, 100, 0] //left, top, right, bottom
                        },
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
                        },
                        title: 'Compras',
                    },
                    {
                        extend: 'print',
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
                        },
                        title: 'Compras',
                    }
                ]
            });
        });
    </script>

</body>

</html>