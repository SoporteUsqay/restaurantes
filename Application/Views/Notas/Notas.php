<?php 
error_reporting(E_ALL);
$titulo_importante = 'Notas Electrónicas';
include 'Application/Views/template/header.php';
require_once 'reportes/recursos/componentes/MasterConexion.php'; 
$objcon = new MasterConexion();
$f = $objcon->consulta_arreglo("Select * from cierrediario where pkCierreDiario = 1");

$obj = new Application_Models_CajaModel();
$fechaCaja = $obj->fechaCierre();

$filter_inicio = date('Y-m-01');
if (isset($_REQUEST['inicio'])){
    $filter_inicio = $_REQUEST['inicio'];
}

$filter_fin = date('Y-m-d');
if (isset($_REQUEST['fin'])){
    $filter_fin = $_REQUEST['fin'];
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
                Generar Nota Electrónica</h4>
            </div>

            <div class="panel-body">

                <div>

                    <form id="frmFiltro">

                        <div class="row">
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <label for="">Tipo de Documento</label>
                                    <select name="tipo_documento" id="tipo_documento" class="form-control" required>
                                        <option value="1">BOLETA</option>
                                        <option value="2">FACTURA</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <label for="">Serie</label>
                                    <input type="text" id="serie" name="serie" value="" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <label for="">Numero</label>
                                    <input type="text" id="numero" name="numero" value="" class="form-control" required>
                                </div>
                            </div>
                            <div class="text-center">
                                <!-- <label for="" style="color: transparent">.</label> -->
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search"></i>
                                    Consultar
                                </button>
                            </div>
                        </div>
                    </form>
                    
                </div>

                <hr>

                <div id="contenedorNota">

                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <div class="form-group">
                                <label for="">Tipo de Documento</label>
                                <select name="tipo_nota" id="tipo_nota" class="form-control" onchange="ChangeMotivos()" required>
                                    <option value="3">NOTA DE CRÉDITO</option>
                                    <option value="4">NOTA DE DÉBITO</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <div class="form-group">
                                <label for="">Motivo Nota</label>
                                <select name="motivo_nota" id="motivo_nota" class="form-control" onchange="ShowItems()">
                                    
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <h3 style="margin-bottom: 0">S/ <span id="lblTotal">0.00</span></h3>
                        <p style="margin-bottom: 20px">Total</p>
                    </div>


                    <div id="contenedorItems" class="table-responsive">
                    
                        <table class="table table-bordered table-striped">
                            <thead>
                                <th>Plato</th>
                                <th class="text-right">Cantidad</th>
                                <th class="text-right">Precio</th>
                                <th class="text-right"></th>
                            </thead>
                            <tbody id="contenedorDetalles">
                            
                            </tbody>
                        </table>
                    </div>



                    <div class="text-center">
                        <button type="button" id="btnEmitirNota" class="btn btn-success btn-lg" onclick="EmitirNota()">
                            <i class="fa fa-save"></i>
                            EMITIR NOTA
                        </button>
                    </div>
                </div>



                <br>
            </div>
        </div>
    
    </div>

    <div id="modalEditItem" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Editar Item</h4>
                </div>
                <form id="frmEditItem">
                <div class="modal-body">

                        <input type="text" name="id" id="id" hidden>

                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label for="">Cantidad</label>
                                    <input type="number" step="any" name="cantidad" id="cantidad" class="form-control" required min="0.01">
                                </div>
                            </div>  
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label for="">Precio</label>
                                    <input type="number" step="any" name="precio" id="precio" class="form-control" required min="0.01">
                                </div>
                            </div>  
                        </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i>
                        Guardar
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cerrar</button>
                </div>
                </form>
            </div>
        </div>
    </div> 


    <link rel="stylesheet" type="text/css" href="Public/select2/css/select2.css" rel="stylesheet">
    <script  type="text/javascript" src="Public/select2/js/select2.js"></script>
    <script src="Application/Views/Notas/js/Notas.js.php"></script>

</body>

</html>