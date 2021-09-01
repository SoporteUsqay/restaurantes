<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <title><?php echo Class_config::get('nameApplication') ?></title>
        <!--<link rel="stylesheet" type="text/css" href="Public/jquery-easyui/easyui/themes/default/easyui.css">-->
        <!--<link rel="stylesheet" type="text/css" href="Public/jquery-easyui/easyui/themes/icon.css">-->
        <link rel="stylesheet" href="Public/jquery-ui-1.10.4.custom/css/jquery-ui.min.css">

        <link href="Public/Bootstrap/css/bootstrap.css" rel="stylesheet">
        <link href="Public/Bootstrap/media/css/jquery.dataTables.min.css" rel="stylesheet">

        <link rel="stylesheet" type="text/css" href="Public/css/style.css">
        <!--<link rel="stylesheet" type="text/css" href="Public/css/style2.css">-->
        <!--<link rel="stylesheet" type="text/css" href="Public/jquery-easyui/easyui/demo.css">-->

        <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery-1.10.2.js"></script>
        <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery-ui-1.10.4.custom.js"></script>
        <!--<script src="//code.jquery.com/jquery-1.10.2.js"></script>-->
        <!--<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>-->
        <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery.numeric.js"></script>
        <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery.maskedinput.js"></script>
        <!-- <script type="text/javascript" src="Public/scripts/body.js.php"></script> -->
        <!-- <script type="text/javascript" src="Public/scripts/listGeneral.js.php"></script> -->
        <!-- <script type="text/javascript" src="Public/scripts/Validation.js.php"></script> -->
        <script  type="text/javascript" src="Public/Bootstrap/js/bootstrap.min.js"></script>
        <script  type="text/javascript" src="Public/Bootstrap/media/js/jquery.dataTables.min.js"></script>
        <!-- <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/Concurrent.Thread.js"></script> -->
        <link rel="stylesheet" href="Public/Usqay/css/usqay.css">
        <link rel="stylesheet" href="Public/Usqay/css/clc.css">

        <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="ReportesGraficos/Highcharts/api/css/font-awesome.css">
        
        <link rel="icon" href="logo.ico"/>

        <style>
            .select2-container--default .select2-selection--single {
                height: 46px !important;
            }
            .panel-body {
                /* color: #7e7e7e; */
            }
            .text-amount {
                font-size: 18pt;
                font-weight: bold;
                color: #4ebe50 !important;
            }
            .text-danger {
                color: #f17d7a !important;
            }
            .text-info {
                color: #558cff !important;
            }
            .text-black {
                color: #000 !important;
            }
            img {
                width: 40px
            }
            .text-title {
                /* border-bottom: 1px solid #eee; */
                /* margin-bottom: 5px;
                padding-bottom: 5px; */
            }
            .container-dashboard * {
                font-family: 'Rubik', 'Circular', sans-serif;
            }
            .row-second .panel {
                height: 96px;
            }
            .panel {
                box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
            }
            .mb-2 {
                margin-bottom: 12px;
            }
            .cursor-pointer {
                cursor: pointer !important;
            }
            
        </style>

    </head>
<body>
    <?php
        error_reporting(E_ALL);
        $objViewMenu = new Application_Views_IndexView();
        $objViewMenu->showContent();

        $obj = new Application_Models_CajaModel();
        $fecha = $obj->fechaCierre();
        
        $db = new SuperDataBase();
    ?>

    <script>
        var fecha_inicio = '';
        var fecha_fin = '';
    </script>

    <div class="container-fluid container-dashboard" style="margin-top: 70px">

        <div class="text-right mb-2">
            <select name="" id="cmbTipoFilter" class="form-contol">
                <option value="1">Hoy</option>
                <option value="2">Última Semana</option>
                <option value="3">Último Mes</option>
            </select>
        </div>

        <div class="row">

            <!-- Total Caja -->
            <div class="col-xs-12 col-md-3">
                <div class="panel">
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-xs-8">
                                <div class="text-amount" id="classMontoTotalCaja">
                                    <span>S/</span>
                                    <span id="montoTotalCaja">0.00</span>
                                </div>
                                <div class="text-title">
                                    Total
                                </div>
                            </div>
                            <div class="col-xs-4 cursor-pointer">
                                <img src="Public/images/dashboard/2424508.svg"  alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Ventas -->
            <div class="col-xs-12 col-md-3">
                <div class="panel">
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-xs-8">
                                <div class="text-amount">
                                    <span>S/</span>
                                    <span id="montoTV">0.00</span>
                                </div>
                                <div class="text-title">
                                    Total Ventas
                                </div>
                            </div>
                            <div class="col-xs-4 cursor-pointer">
                                <a onclick="sendReport('<?php echo Class_config::get('urlApp') ?>/?controller=Report&action=SaleConsumo&cmbventas=1')">
                                    <img src="Public/images/dashboard/3081315.svg"  alt="">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Compras -->
            <div class="col-xs-12 col-md-3">
                <div class="panel">
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-xs-8">
                                <div class="text-amount text-danger">
                                    <span>S/</span>
                                    <span id="montoCompras">0.00</span>
                                </div>
                                <div class="text-title">
                                    Compras
                                </div>
                            </div>
                            <div class="col-xs-4 cursor-pointer">
                                <a onclick="sendReport('<?php echo Class_config::get('urlApp') ?>/?controller=Compras&action=Show', 1)">
                                    <img src="Public/images/dashboard/3081559.svg"  alt="">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total movimientos -->
            <div class="col-xs-12 col-md-3">
                <div class="panel">
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-xs-8">
                                <div class="text-amount" id="classMontoMov">
                                    <span>S/</span>
                                    <span id="montoMov">0.00</span>
                                </div>
                                <div class="text-title">
                                    Movimientos de Dinero
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <img src="Public/images/dashboard/1875506.svg"  alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
        </div>

        <div class="row">

            <!-- Total Propinas -->
            <div class="col-xs-12 col-md-3">
                <div class="panel">
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-xs-8">
                                <div class="text-amount text-info">
                                    <span>S/</span>
                                    <span id="montoPropina">0.00</span>
                                </div>
                                <div class="text-title">
                                    Propinas
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <img src="Public/images/dashboard/2282152.svg"  alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total descuentos -->
            <div class="col-xs-12 col-md-3">
                <div class="panel">
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-xs-8">
                                <div class="text-amount text-danger">
                                    <span>S/</span>
                                    <span id="montoDes">0.00</span>
                                </div>
                                <div class="text-title">
                                    Descuentos
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <img src="Public/images/dashboard/2282152.svg"  alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total en mesas -->
            <div class="col-xs-12 col-md-3">
                <div class="panel">
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-xs-8">
                                <div class="text-amount text-info">
                                    <span>S/</span>
                                    <span id="montoVMesa">0.00</span>
                                </div>
                                <div class="text-title">
                                    Total por Cobrar ( <span id="cantidadVMesa">0</span> )
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <img src="Public/images/dashboard/2615109.svg"  alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total en mesas -->
            <div class="col-xs-12 col-md-3">
                <div class="panel">
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-xs-8">
                                <div class="text-amount text-info">
                                    <span>S/</span>
                                    <span id="montoTotal">0.00</span>
                                </div>
                                <div class="text-title">
                                    Total (+ por Cobrar)
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <img src="Public/images/dashboard/2615109.svg"  alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">

            <!-- Total Anuladas -->
            <div class="col-xs-12 col-md-3">
                <div class="panel">
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-xs-8">
                                <div class="text-amount text-danger">
                                    <span>S/</span>
                                    <span id="montoAnuladas">0.00</span>
                                </div>
                                <div class="text-title">
                                    Anuladas
                                </div>
                            </div>
                            <div class="col-xs-4 cursor-pointer">
                                <a onclick="sendReport('<?php echo Class_config::get('urlApp') ?>/?controller=Report&action=SaleConsumo&cmbventas=3')">
                                    <img src="Public/images/dashboard/1282444.svg"  alt="">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Creditos -->
            <div class="col-xs-12 col-md-3">
                <div class="panel">
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-xs-8">
                                <div class="text-amount text-info">
                                    <span>S/</span>
                                    <span id="montoCred">0.00</span>
                                </div>
                                <div class="text-title">
                                    Ventas a Crédito ( <span id="cantidadCred">0</span> )
                                </div>
                            </div>
                            <div class="col-xs-4 cursor-pointer">
                                <a onclick="sendReport('<?php echo Class_config::get('urlApp') ?>/?controller=Report&action=SaleConsumo&cmbventas=4')">
                                    <img src="Public/images/dashboard/3579937.svg"  alt="">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Consumos -->
            <div class="col-xs-12 col-md-3">
                <div class="panel">
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-xs-8">
                                <div class="text-amount text-info">
                                    <span>S/</span>
                                    <span id="montoCon">0.00</span>
                                </div>
                                <div class="text-title">
                                    Ventas por Consumo ( <span id="cantidadCon">0</span> )
                                </div>
                            </div>
                            <div class="col-xs-4 cursor-pointer">
                                <a onclick="sendReport('<?php echo Class_config::get('urlApp') ?>/?controller=Report&action=SaleConsumo&cmbventas=5')">
                                    <img src="Public/images/dashboard/2898482.svg"  alt="">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total With Credito y Consumo -->
            <div class="col-xs-12 col-md-3">
                <div class="panel">
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-xs-8">
                                <div class="text-amount text-info">
                                    <span>S/</span>
                                    <span id="montoTotCredCon">0.00</span>
                                </div>
                                <div class="text-title">
                                    Total (+ Crédito y Consumos)
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <img src="Public/images/dashboard/2615109.svg"  alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row" id="rowMedios">
                
            
        </div>

        <div class="row" id="rowMediosCompra">
                
            
        </div>

        <div class="row row-graph">
            <div class="col-xs-12 col-md-6">
                <div class="panel panel-primar">
                    <div class="panel-heading">
                        <i class="fa fa-chart-line"></i>
                        Ventas: 
                        <strong id="titleGraphVentas"></strong>
                    </div>

                    <div class="panel-body">
                        <canvas id="graphVentas" width="300" height="160"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-6">
                <div class="panel panel-primar">
                    <div class="panel-heading">
                        <i class="fas fa-chart-pie"></i>
                        Ventas por Salones
                    </div>

                    <div class="panel-body">
                        <canvas id="graphVentasSalones" width="300" height="160"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row row-second">
            
            <div class="col-xs-12 col-md-3">
                <div class="panel">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-8">
                                <div class="text-amoun" style="font-size:18pt">
                                    S/ 
                                    <span class="" id="montoProMesa">0.00</span>
                                </div>
                                <div class="text-title">
                                    Promedio de Consumo por Mesa
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <img src="Public/images/dashboard/2534197.svg" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-3">
                <div class="panel">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-8">
                                <div class="text-amoun" style="font-size: 12pt">
                                    <strong id="tiempoMin"></strong> Min -
                                </div>
                                <div class="text-amoun" style="font-size: 12pt">
                                    <strong id="tiempoMax"></strong> Max
                                </div>
                                <div class="text-title">
                                    Tiempo en Mesa 
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <img src="Public/images/dashboard/1187560.svg" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-6">
                <div class="panel">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-8">
                                <div class="text-amoun" style="font-size: 15pt" id="nombreMozo">
                                    <!-- Jansen Moscol Requena -->
                                </div>
                                <div class="text-amunt" style="font-size: 12pt">
                                    <span id="cantidadMozo">0</span> pedidos realizados ( S/ <span id="totalMozo">0</span> )
                                </div>
                                <div class="text-title">
                                    Mozo Destacado
                                </div>
                            </div>
                            <div class="col-xs-4 text-center">
                                <img src="Public/images/dashboard/2453340.svg" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-md-4">
                <div class="panel">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-8">
                                <div class="text-amoun"  style="font-size:18pt" id="cantidadAtendidas">
                                </div>
                                <div class="text-title">
                                    Mesas atendidas
                                </div>
                            </div>
                            <div class="col-xs-4 text-center">
                                <img src="Public/images/dashboard/391175.svg" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-md-4">
                <div class="panel">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-8">
                                <div class="text-amoun" style="font-size:18pt" id="cantidadVMesa2">
                                </div>
                                <div class="text-title">
                                    Mesas Abiertas
                                </div>
                            </div>
                            <div class="col-xs-4 text-center">
                                <img src="Public/images/dashboard/3579704.svg" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-md-4">
                <div class="panel">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-8">
                                <div class="text-amoun" style="font-size:18pt" id="cantidadAnuladas">
                                </div>
                                <div class="text-title">
                                    Ventas anuladas
                                </div>
                            </div>
                            <div class="col-xs-4 text-center">
                                <img src="Public/images/dashboard/1721977.svg" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
        </div>


        <div class="row">
            
            <div class="col-xs-12 col-md-6">
                <div class="panel panel-primar">
                    <div class="panel-heading">
                        <i class="fas fa-clipboard-list"></i>
                        Platos mas Vendidos
                    </div>
                    <div class="panel-body">

                        <table class="dtbl table table-striped table-bordere">
                            <thead>
                                <th>Plato</th>
                                <th class="text-right">Cantidad</th>
                                <th class="text-right"></th>
                                <th class="text-right">Importe</th>
                            </thead>
                            <tbody id="tblPlatos">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-xs-12 col-md-6">
                <div class="panel panel-primar">
                    <div class="panel-heading">
                        <i class="fas fa-clipboard-list"></i>
                        Clientes
                    </div>
                    <div class="panel-body">

                        <table class="dtbl table table-striped table-bordere">
                            <thead>
                                <th>Documento</th>
                                <th class="text-right">Cantidad</th>
                                <th class="text-right"></th>
                                <th class="text-right">Importe</th>
                            </thead>
                            <tbody id="tblClientes">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>


        


       
    </div>


    

    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.4/Chart.min.css"> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.4/Chart.js"></script> -->
    <script  type="text/javascript" src="Public/js/NChart.js"></script>

    <script src="Application/Views/Dashboard/js/Dashboard.js.php"></script>
</body>
</html>
