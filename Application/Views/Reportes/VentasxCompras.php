<?php 
error_reporting(E_ALL);
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
                <i class="fa fa-bar-chart-o"></i>
                Ventas x Compras</h4>
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
                            <div class="col-xs-12 col-md-3">
                                <div class="form-group">
                                    <label for="">Agrupar por</label>
                                    <select name="group_by" id="cmbGroup" class="form-control">
                                        <option value="1">Día</option>
                                        <option value="2">Mes</option>
                                        <option value="3">Año</option>
                                    </select>
                                </div>
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

                <br>

                <div class="row row-graph">
                    <div class="col-xs-12 col-md-12">
                        <div class="panel panel-primar">
                            <div class="panel-heading">
                            </div>

                            <div class="panel-body">
                                <canvas id="graph" width="300" height="160"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-12">
                        <div class="panel panel-primar">
                            <div class="panel-heading">
                                <i class="fa fa-chart-line"></i>
                            </div>
                            <div class="panel-body">
                                <table class="table table-striped">
                                    <thead>
                                        <th id="lblTable">Día</th>
                                        <th class="text-right">Ventas</th>
                                        <th class="text-right">Compras</th>
                                        <th class="text-right">Gastos</th>
                                        <th class="text-right">Total</th>
                                    </thead>
                                    <tbody id="tblBodyGraph">

                                    </tbody>
                                </table>   
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    
    </div>

    <link rel="stylesheet" type="text/css" href="Public/select2/css/select2.css" rel="stylesheet">
    <script  type="text/javascript" src="Public/select2/js/select2.js"></script>
    <script type="text/javascript" src="Application/Views/Reportes/js/VentasxCompras.js.php" ></script>
    <script  type="text/javascript" src="Public/js/NChart.js"></script>

    <script>    
        $(document).ready(() => {

            $('#tblRP').DataTable();
        });
    </script>
</body>

</html>