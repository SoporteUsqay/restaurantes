<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Controllers_ReportController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {
            case 'TopVentasAction':
                $this->_ListTopVentas();
                break;
            case 'ShowSProductDiaAction':
                $this->_showSalidaProductoPorDia();
                break;
            case 'ShowComparativaPorDiaAction':
                $this->_showComparativaPorDia();
                break;
            case 'ShowTmesAction':
                $this->_showReporteTotalMes();
                break;

            case 'SaleConsumoAction':
                $this->ShowSaleConsumo();
                break;
            
            case 'SaleCajaAction':
                $this->ShowSaleCaja();
                break;
            
            case 'ShowAdminDetalleVentasAction':
                $this->_DetalleVentasConsumo();
                break;

            case 'ShowTVentas2DayAction':
                $this->_showReporteVentasEntre2Fechas();
                break;
            case 'ListSaleDateAction':
                $this->_showReportSaleDate();
                break;

            case 'ListSaleMonthAction':
                $this->_showReportSaleMonth();
                break;

            case 'ListSale2DateAction':
                $this->_showReportSale2Date();
                break;

            case 'ListOutputProductDateAction':
                $this->_showReporOutputProductDate();
                break;

            case 'TVentaAction':
                $this->_showReportforDay();
                break;

            case 'ListadoBoletasAction':
                $this->_ListadoBoletas();
                break;

            case 'ListadodetalleBoletasAction':
                $this->_ListadodetalleBoletas();
                break;

            case 'ListadoFacturasAction':
                $this->_ListadoFacturas();
                break;

            case 'ListadodetalleFacturasAction':
                $this->_ListadodetalleFacturas();
                break;
            case 'ListOutputProductMesAction':
                $this->_showReporOutputProductMes();
                break;

            case 'ListaVentaSemanalesAction':
                $this->_ListaVentaSemanales();
                break;
            case 'ReportePDFVentasDiaAction':
                $this->reportePdfVentasDiarias();
                break;
            case 'TmesAction':
                $this->_showVentasMEs();
                break;
            case 'TVentas2DayAction':
                $this->_shoeTVentas2Day();
                break;
            case 'TVentasMozoAction':
                $this->_showTVentasMozo();
                break;

            case 'ListVentasMozoAction':
                $this->_ListVentasMozo();
                break;
            case 'SProductosDiaAction':
                $this->_showSProductosDia();
                break;
            case 'SmesAction':
                $objView = new Application_Views_ReportView();
                $objView->showReportOutProductoxMoth();
                break;
            case 'SalidaInsumosAction':
                $this->_showSalidaInsumos();
                break;
            case 'ListSalidaInsumoAction':
                $this->_ListadoSalidaInsumoPorDia();
                break;
            case 'ListKardexAction':
                $this->_ListadoKardex();
                break;
            case 'showKardexAction':
                $objView = new Application_Views_ReportView();
                $objView->showKardex();
                break;
            case 'showConsumoxPersonaAction':
                $objView = new Application_Views_ReportView();
                $objView->showConsumoxPersona();
                break;
            case 'showKardexDetalladoAction':
                $objView = new Application_Views_ReportView();
                $objView->showKardexDetallado();
                break;
            case 'showKardexDetallado2Action':
                $objView = new Application_Views_ReportView();
                $objView->showKardexDetallado2();
                break;
            case 'ShowReporPlatosStockAction':
                $objView = new Application_Views_ReportView();
                $objView->showReporteStockPlatos();
                break;
            case 'showPedidosAnuladosAction':
                $objView = new Application_Views_ReportView();
                $objView->showProductosAnulados();
                break;

            case 'VerDetalleTrabajadorAction':
                $this->_verDetalleTrabajador();
                break;

            case 'showNKardexAction':
                require_once 'Application/Views/Reportes/NKardex.php';
                break;

            case 'showNKardexDetalladoAction':
                require_once 'Application/Views/Reportes/NKardexDetallado.php';
                break;

            case 'showNKardexDetallado2Action':
                require_once 'Application/Views/Reportes/NKardexDetallado2.php';
                break;

            case 'showRendimientoPlatoAction':
                require_once 'Application/Views/Reportes/RendimientoPlato.php';
                break;

            case 'showVentasComprasAction':
                require_once 'Application/Views/Reportes/VentasxCompras.php';
                break;

            case 'showFondosExternosAction':
                require_once 'Application/Views/Caja/ReporteFondosExternos.php';
                break;
        }
    }

    private function _showSProductosDia() {
        $objView = new Application_Views_ReportView();
        $objView->showReportOutProductoxDay();
    }

    private function _ListTopVentas() {    
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModel = new Application_Models_ReportModel();

                $fechaInicio = "";
                if (isset($_REQUEST['fechaInicio'])) {
                    $fechaInicio = $_REQUEST['fechaInicio'];
                }

                $fechaFin = "";
                if (isset($_REQUEST['fechaFin'])) {
                    $fechaFin = $_REQUEST['fechaFin'];
                }

                $Tipo = "";
                if (isset($_REQUEST['Tipo'])) {
                    $Tipo = $_REQUEST['Tipo'];
                }

                $Top = "";
                if (isset($_REQUEST['Top'])) {
                    $Top = $_REQUEST['Top'];
                }

                $objModel->ListaTopVentas($fechaInicio, $fechaFin, $Tipo, $Top);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function ShowSaleConsumo() {
        $objModel = new Application_Views_ReportView();
        $objModel->ShowSaleConsumo();
    }
    
    private function ShowSaleCaja() {
        $objModel = new Application_Views_ReportView();
        $objModel->ShowSaleCaja();
    }

    private function _DetalleVentasConsumo() {
        $objModel = new Application_Views_ReportView();
        $objModel->ShowDetalleVentasConsumo();
    }

    private function _showTVentasMozo() {
        $objView = new Application_Views_ReportView();
        $objView->showReportVentasMozo();
    }

    private function _ListVentasMozo() {

        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModel = new Application_Models_ReportModel();

                $fechaInicio = "";
                if (isset($_REQUEST['dateInicio'])) {
                    $fechaInicio = $_REQUEST['dateInicio'];
                }

                $fechaFin = "";
                if (isset($_REQUEST['dateFin'])) {
                    $fechaFin = $_REQUEST['dateFin'];
                }

                $IDTipoTrabajador = "";
                if (isset($_REQUEST['PkTipoTrabajador'])) {
                    $IDTipoTrabajador = $_REQUEST['PkTipoTrabajador'];
                }

                $objModel->VentasMozo($fechaInicio, $fechaFin, $IDTipoTrabajador);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _shoeTVentas2Day() {
        $objView = new Application_Views_ReportView();
        $objView->showReportSaleBetween2Day();
    }

    private function _showComparativaPorDia() {
        $objView = new Application_Views_ReportView();
        $objView->showReportComparativaDia();
    }

    private function _showVentasMEs() {
        $objView = new Application_Views_ReportView();
        $objView->showReportSaleMonth();
    }

    private function _ListaVentaSemanales() {

        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModel = new Application_Models_ReportModel();

                $mes = "";
                if (isset($_REQUEST['filtromes'])) {
                    $mes = $_REQUEST['filtromes'];
                }

                $anio = "";
                if (isset($_REQUEST['filtroAnio'])) {
                    $anio = $_REQUEST['filtroAnio'];
                }

                $semana = "";
                if (isset($_REQUEST['filtrosemana'])) {
                    $semana = $_REQUEST['filtrosemana'];
                }

                $valorClase = "";
                if (isset($_REQUEST['clase'])) {
                    $valorClase = $_REQUEST['clase'];
                }

                $tipo = "";
                if (isset($_REQUEST['tipo_categoria'])) {
                    $tipo = $_REQUEST['tipo_categoria'];
                }

                $IdCategoria = "";
                if (isset($_REQUEST['Idcategoria'])) {
                    $IdCategoria = $_REQUEST['Idcategoria'];
                }

                $objModel->salidaVentasSemanales($mes, $anio, $semana, $valorClase, $IdCategoria, $tipo);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showReporOutputProductMes() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModel = new Application_Models_ReportModel();
                $dateGo = "";
                if (isset($_REQUEST['dateGo'])) {
                    $dateGo = $_REQUEST['dateGo'];
                }

                $anio = "";
                if (isset($_REQUEST['filtroAnio'])) {
                    $anio = $_REQUEST['filtroAnio'];
                }

                $valorProducto = "";
                if (isset($_REQUEST['clase'])) {
                    $valorProducto = $_REQUEST['clase'];
                }

                $tipo = "";
                if (isset($_REQUEST['tipo_categoria'])) {
                    $tipo = $_REQUEST['tipo_categoria'];
                }

                $IdCategoria = "";
                if (isset($_REQUEST['Idcategoria'])) {
                    $IdCategoria = $_REQUEST['Idcategoria'];
                }

                $objModel->salidaProductosPorMes($dateGo, $anio, $valorProducto, $IdCategoria, $tipo);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
//        $objModel->salidaProductosPorMes($_REQUEST['filtro'],$_REQUEST['filtro1']);
    }

    private function _showReportBeween() {
        $objView = new Application_Views_ReportView();
        $objView->showReportSaleBetweenDay();
    }

    private function _showReportSaleDate() {
        $objModel = new Application_Models_ReportModel();
        $objModel->setDateGO($_REQUEST['dateGo']);
        $objModel->_listTotalSaleDate($_REQUEST['estado']);
    }

    private function _ListadoBoletas() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModel = new Application_Models_ReportModel();
                $dateGo = "";
                if (isset($_REQUEST['dateGo'])) {
                    $dateGo = $_REQUEST['dateGo'];
                }

                $dateEnd = "";
                if (isset($_REQUEST['dateEnd'])) {
                    $dateEnd = $_REQUEST['dateEnd'];
                }
                $objModel->_listComprobantes($dateGo, $dateEnd, $_REQUEST['nnumero'], $_REQUEST['tipo']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _ListadoFacturas() {
        $objModel = new Application_Models_ReportModel();
        $objModel->setDateGO($_REQUEST['dateGo']);
        $objModel->setDateEnd($_REQUEST['dateEnd']);
        $objModel->_listComprobantes($_REQUEST['dateGo'], $_REQUEST['dateEnd'], $_REQUEST['nnumero'], $_REQUEST['tipo']);
    }

    private function _showReportSaleMonth() {
        $objModel = new Application_Models_ReportModel();
        $objModel->setDateGO($_REQUEST['dateGo']);
//        echo date('Y')."-".$_REQUEST['dateGo'];
        $objModel->_listTotalSaleMonth($_REQUEST['AniVentas']);
    }

    private function _showReporOutputProductDate() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModel = new Application_Models_ReportModel();
                $dateGo = "";
                if (isset($_REQUEST['dateventadiarias'])) {
                    $dateGo = $_REQUEST['dateventadiarias'];
                }
                $objModel->salidaProductosPorDia($dateGo);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
//        $objModel->salidaProductosPorDia($_REQUEST['filtro']);
    }

    private function _showReportSale2Date() {
        $objModel = new Application_Models_ReportModel();
        $objModel->setDateGO($_REQUEST['dateGo']);
        $objModel->setDateEnd($_REQUEST['dateEnd']);
        $objModel->_listTotalSale2Date();
    }

    private function _showReportforDay() {
        $view = new Application_Views_ReportView();
        $view->showReportSaleBetweenDay();
    }

    private function _showSalidaProductoPorDia() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $view = new Application_Views_ReportView();
                $view->showReportOutProductoxDay();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showSalidaInsumos() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $view = new Application_Views_ReportView();
                $view->showSalidaInsumos();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showReporteVentasEntre2Fechas() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $view = new Application_Views_ReportView();
                $view->showReportSaleBetween2Day();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showReporteTotalMes() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $view = new Application_Views_ReportView();
                $view->showReportSaleMonth();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _ListadodetalleBoletas() {
//      echo"ll";
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelPedidos = new Application_Models_ReportModel();
                $objModelPedidos->listItemPedidosboletas($_GET['boleta']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _ListadodetalleFacturas() {
//      echo"ll";
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelPedidos = new Application_Models_ReportModel();
                $objModelPedidos->listItemPedidosfacturas($_GET['factura']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _ListadoSalidaInsumoPorDia() {
//      echo"ll";
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelPedidos = new Application_Models_ReportModel();
                $objModelPedidos->ListadoSalidaInsumosPorDia($_GET['date']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _ListadoKardex() {
//      echo"ll";
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelPedidos = new Application_Models_ReportModel();
                $objModelPedidos->ListadoSalidaKardex($_GET['date']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function reportePdfVentasDiarias() {
        $sucursal = UserLogin::get_pkSucursal();
        $caja = $_REQUEST["caja"];
        $ventasdia = new Application_Models_ReportModel();
        require_once '././Components/Library/dompdf/dompdf_config.inc.php';


        # Contenido HTML del documento que queremos generar en PDF.
        $fechaInicio = date($_REQUEST['fechaInicio']);
        $fechaFin = date($_REQUEST['fechaFin']);
        $fechaReporte = "";
        if ($fechaInicio != $fechaFin) {
            $fechaReporte = " al " . $fechaFin;
        }
        date_default_timezone_set("America/Lima");
        setlocale(LC_TIME, "spanish");
        $date = strftime("%A, %d de %B de %Y", strtotime(date("Y-m-d")));

        $totalDia = 0;
        $report_ventasdia = $ventasdia->ReporteventasDiarias($fechaInicio, $fechaFin, $caja);
        $table = '<table class="separate" ><thead><tr>'
                . '<th>Codigo</th>'
                . '<th>Caja</th>'
                . '<th>Mesa</th>'
                . '<th>Tiempo en Mesa</th>'
                . '<th>Tipo de pago</th>'
                . '<th>Tarjeta</th>'
                . '<th>T. Efectivo</th>'
                . '<th>T. Tarjeta</th>'
                . '<th>Descuento</th>'
                . '<th>Total</th>'
                . '</tr></thead><tbody>';
        $sumTotalEfecctivo = 0.00;
        $sumTotalTarjeta = 0.00;
        for ($i = 0; $i < sizeof($report_ventasdia); $i++) {
            $totalDia = $totalDia + $report_ventasdia[$i]["total"];
            $totalEfectivo = 0.00;
            $totalTarjeta = 0.00;
            $totalEfectivo = $report_ventasdia[$i]["total_efectivo"];
            $totalTarjeta = $report_ventasdia[$i]["total_tarjeta"];
            $sumTotalEfecctivo = $sumTotalEfecctivo + $report_ventasdia[$i]["total_efectivo"];
            $sumTotalTarjeta = $sumTotalTarjeta + $report_ventasdia[$i]["total_tarjeta"];
            if ($report_ventasdia[$i]["tipo_pago"] == "1") {
                $tipoPago = "Efectivo";
                $nombreTarjeta = "-------------";
            } else {
                $tipoPago = "Tarjeta";
                $nombreTarjeta = $report_ventasdia[$i]["nombreTarjeta"];
            }
            $table = $table . "<tr>" .
                    "<td>" . utf8_decode($report_ventasdia[$i]["pkPediido"]) . "</td>" .
                    "<td>" . utf8_decode($report_ventasdia[$i]["caja"]) . "</td>" .
                    "<td>" . utf8_decode($report_ventasdia[$i]["nmesa"]) . "</td>" .
                    "<td>" . utf8_decode($report_ventasdia[$i]["tiempoEstadia"]) . "</td>" .
                    "<td>" . utf8_decode($tipoPago) . "</td>" .
                    "<td>" . utf8_decode($nombreTarjeta) . "</td>" .
                    "<td>" . utf8_decode($totalEfectivo) . "</td>" .
                    "<td>" . utf8_decode($totalTarjeta) . "</td>" .
                    "<td>" . utf8_decode($report_ventasdia[$i]["descuento"]) . "</td>" .
                    "<td>" . utf8_decode($report_ventasdia[$i]["total"]) . "</td>" .
                    "</tr>";
        }
        $table = $table . '</table>';
        $html = '
        <html>
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Reporte de ventas diarias </title>
        <style>
        body { font-family: verdana, sans-serif;} 
        table {
          margin-bottom: 2em;
        }

        thead {
          background-color: #eeeeee;
        }

        tbody {
          background-color: #ffffee;
        }

        th,td {
          padding: 3pt;
        }

        table.separate {
          border-collapse: separate;
          border-spacing: 5pt;
          border: 3pt solid #33d;
        }

        table.separate td {
          border: 2pt solid #33d;
        }

        table.collapse {
          border-collapse: collapse;
          border: 1pt solid black;  
        }

        table.collapse td {
          border: 1pt solid black;
        }
        </style>
        </head>
        <body>
        <center><h2>Reporte Diario de ventas del ' . $fechaInicio . $fechaReporte . '</h2>
        ' . UserLogin::get_nombreSucursal() . '</center> 
        <p>Dia: ' . utf8_encode($date)
        . '<p><span>Impreso por:' . UserLogin::get_names() . ' ' . UserLogin::get_lastnames() . '</span> <br>'
        . 'Total (S/.) :  ' . number_format($totalDia, 2, '.', ' ') . ' <br>T. Efectivo: ' . number_format($sumTotalEfecctivo, 2, '.', ' ') . '<br>'
        . 'T.Tarjeta: ' . number_format($sumTotalTarjeta, 2, '.', ' ') . '</p>' . $table . ' 
        </body>
        </html>';

        //echo $html;
        $mipdf = new DOMPDF();
        // 
        //# Definimos el tamaño y orientación del papel que queremos.
        //# O por defecto cogerá el que está en el fichero de configuración.
        $mipdf->set_paper("A4", "landscape");

        //# Cargamos el contenido HTML.
        $mipdf->load_html(utf8_decode($html));

        //# Renderizamos el documento PDF.
        $mipdf->render();

        # Enviamos el fichero PDF al navegador.
        $mipdf->stream('ReporteDiario_' . $date . '.pdf');
    }

    private function _verDetalleTrabajador() {
        $objModel = new Application_Views_ReportView();
        $objModel->verDetalleTrabajador();
    }

}
