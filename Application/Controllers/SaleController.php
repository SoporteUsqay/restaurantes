<?php
error_reporting(E_ALL);
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Controllers_SaleController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {
            case 'addPlatoNuevoAction':
                $this->_addPlatoNuevo();
                break;

            case 'FiltroPlatosAction':
                $this->_FiltroPlatos();
                break;

            case 'deletePlatoAction':
                $this->_DeletePlatos();
                break;
            case 'AdminPlatoAction':
                $this->_showAdminPlato();
                break;
            case 'ShowAdminBoletasAction':
                $this->_showAdminBoletas();
                break;

            case 'ShowAdminFacturasAction':
                $this->_showAdminFacturas();
                break;

            case 'SaveplatoAction':
                $this->_saveplato();
                break;

            case 'UpdatePlatoAction':
                $this->_updatePlato();
                break;
            case 'ListCuentasPorPagarAction':
                $this->_ListCuentaPorPagar();
                break;
            case 'ShowProductAction':
                $this->_showAdminProduct();
                break;
            case 'ShowPromocionesAction':
                $this->_showPromociones();
                break;
            case 'ShowRegisterPromocionesAction':
                $this->_showRegisterPromociones();
                break;
            case 'GuardarPromocionAction':
                $this->_guardarPromocion();
                break;
            case 'CategoriasAction':
                $this->_showCategorias();
                break;


            case 'AdminTiposAction':
                $this->_showAdminTipos();
                break;
            case 'CPendientesAction':
                $this->_showCuentasPendientes();
                break;
            case 'ShowBoletaAction':
                $this->_showBoletas();
                break;
            case 'ShowDetalleBoletaAction':
                $this->_detalleComprobante();
                break;
            case 'ShowFacturaAction':
                $this->_showFacturas();
                break;
            case 'DeleteComprobanteAction':
                $this->_deleteComprobante();
                break;

            //Mostrar cuentas por consumo
            //Despues de 5 años aprendi a rutear con usqay :'V
            case 'CConsumoAction':
                $this->_showCuentasConsumo();
                break;

            case 'ShowReporteVentasAction':
                require_once 'Application/Views/Reportes/VentasConsumo.php';
                break;

            case 'ShowClientesAction':
                if (self::$session->validateStartSesion()) {
                    if (!self::$session->validateSesion()) {
                        require_once 'Application/Views/Cliente/ShowClientes.php';
                    } else {
                        self::$session->redirect();
                    }
                } else {
                    self::$session->redirect();
                }
                break;
        }
    }

    private function _showAdminTipos() {
//      echo"ll";
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Views_SaleView();
                $objView->showTipos();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showBoletas() {
//      echo"ll";
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Views_SaleView();
                $objView->showAdminBoletas();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
    private function _detalleComprobante() {
//      echo"ll";
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelComprobante = new Application_Models_ComprobanteModel();
                $objModelComprobante->detalleComprobante($_REQUEST['codComprobante']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
    private function _deleteComprobante() {
//      echo"ll";
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objtipomodel = new Application_Models_ComprobanteModel();
                $objtipomodel->deleteComprobante($_REQUEST['codComprobante'],$_REQUEST['tipoComprobante']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showFacturas() {
//      echo"ll";
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Views_SaleView();
                $objView->showAdminFacturas();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showCuentasPendientes() {
//      echo"ll";
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Views_SaleView();
                $objView->showCuentasPorPagar();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showCuentasConsumo() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Views_SaleView();
                $objView->showCuentasConsumo();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _guardarPromocion() {
        $model = new Application_Models_SaleModel();
        $model = new Application_Models_SaleModel();
        $_descripcion = $_REQUEST['nomb_promo'];
        $_pkTipo = $_REQUEST['pkTipo'];
        $_precioVenta = $_REQUEST['precioVenta'];

        $objModelPlato = new Application_Models_PlatosModel();
        $objModelPlato->set_descripcion($_descripcion);
        $objModelPlato->set_pkTipo($_pkTipo);
        $objModelPlato->set_precio($_precioVenta);

        $pkPromocion = $objModelPlato->_saveplato();
        echo $pkPromocion;
        $promocion = new Application_Models_SaleModel();
        $array = json_decode($_REQUEST['array'], true);
        for ($i = 0; $i < count($array); $i++) {
            $promocion->guardarDetallePromocion($array[$i]['codigo'], $array[$i]['cantidad'], $pkPromocion);
            //        echo $array[$i]['pkPedido'] . " " . $valor;
        }
    }

    private function _showAdminProduct() {
        $model = new Application_Views_SaleView();
        $model->showAdminProduct();
    }

    private function _showCategorias() {
        $model = new Application_Views_SaleView();
        $model->showCategorias();
    }

    private function _showPromociones() {
        $model = new Application_Views_SaleView();
        $model->showPromociones();
    }

    private function _showRegisterPromociones() {
        $model = new Application_Views_SaleView();
        $model->showRegistrarPromociones();
    }

    private function _saveplato() {
        $_descripcion = $_REQUEST['descripcion_plato'];
        $_pkTipo = $_REQUEST['pkTipo'];
        $_precioVenta = $_REQUEST['precioVenta'];
        $_stockMinimo = $_REQUEST['stockMinimo'];
        $_pkCategoria = $_REQUEST['categoria'];

        $objModelPlato = new Application_Models_PlatosModel();
        $objModelPlato->set_descripcion($_descripcion);
        $objModelPlato->set_pkTipo($_pkTipo);
        $objModelPlato->set_precio($_precioVenta);
        $objModelPlato->set_pkCategoria($_pkCategoria);
        $objModelPlato->set_stockMinimo($_stockMinimo);

        echo $objModelPlato->_saveplato($_REQUEST["tipo_sunat"],$_REQUEST["tipo_impuesto"],$_REQUEST["tipo_articulo"]);
    }

    private function _updatePlato() {
        $_descripcion = $_REQUEST['descripcion_plato'];
        $_pkTipo = $_REQUEST['pkTipo'];
        $_precioVenta = $_REQUEST['precioVenta'];
        $_stockMinimo = $_REQUEST['stockMinimo'];
        $_pkCategoria = $_REQUEST['categoria'];

        $objModelPlato = new Application_Models_PlatosModel();
        $objModelPlato->set_descripcion($_descripcion);
        $objModelPlato->set_pkTipo($_pkTipo);
        $objModelPlato->set_precio($_precioVenta);
        $objModelPlato->set_pkCategoria($_pkCategoria);
        $objModelPlato->set_stockMinimo($_stockMinimo);
        
        $objModelPlato->_updateplato($_REQUEST['id'],$_REQUEST["tipo_sunat"],$_REQUEST["tipo_impuesto"],$_REQUEST["tipo_articulo"]);
    }

    private function _FiltroPlatos() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelPlato = new Application_Models_PlatosModel();

                $valor = "";
                if (isset($_REQUEST['valor'])) {
                    $valor = $_REQUEST['valor'];
                }

                $categoria = "0";
                if (isset($_REQUEST['categoria'])) {
                    $categoria = $_REQUEST['categoria'];
                }

//                $tipo = "1";
//                if (isset($_REQUEST['tipo'])) {
//                    $tipo = $_REQUEST['tipo'];
//                }

                $objModelPlato->filtro_plato($valor, $categoria);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _addPlatoNuevo() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelPlato = new Application_Models_PlatosModel();
                $descripcion = "";
                if (isset($_REQUEST['descripcion'])) {
                    $descripcion = $_REQUEST['descripcion'];
                }

                $tipo = "";
                if (isset($_REQUEST['tipo'])) {
                    $tipo = $_REQUEST['tipo'];
                }

                $precio = "";
                if (isset($_REQUEST['precio'])) {
                    $precio = $_REQUEST['precio'];
                }

                $result = $objModelPlato->addNuevoPlato($descripcion, $tipo, $precio);
                echo $result;
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _DeletePlatos() {
//      echo"ll";
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelPlato = new Application_Models_PlatosModel();
                $objModelPlato->deletePlato($_REQUEST['id']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _ListCuentaPorPagar() {
//      echo"ll";
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $fechaInicio=date('Y-m-d');
                if (isset($_REQUEST['txtFechaInicioCredito']))
                   $fechaInicio= $_REQUEST['txtFechaInicioCredito'];
                
                $fechaFin=date('Y-m-d');
                if (isset($_REQUEST['txtFechaFinCredito']))
                   $fechaFin= $_REQUEST['txtFechaFinCredito'];
                
                $objModelPlato = new Application_Models_SaleModel();
                $objModelPlato->_listCuentasPorPagar($fechaInicio, $fechaFin);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showAdminPlato() {
//      echo"ll";
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Views_SaleView();
                $objView->showIngresarPlatos();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showAdminBoletas() {
//      echo"ll";
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Views_SaleView();
                $objView->showAdminBoletas();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showAdminFacturas() {
//      echo"ll";
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Views_SaleView();
                $objView->showAdminFacturas();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

}
