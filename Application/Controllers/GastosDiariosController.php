<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Controllers_GastosDiariosController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {

            case 'ListAction':
                $this->_listGastosDiarios();
                break;
            case 'ListMontoInicialAction':
                $this->_listMontoInicial();
                break;
            case 'ListTotalDiaAction':
                $this->_listTotalVendido();
                break;
            case 'ShowRegisterAction':
                $this->_showRegister();
                break;
            case 'SaveGastoAction':
                $this->_saveGasto();
                break;
            case 'SaveMontoInicialAction':
                $this->_saveMontoInicial();
                break;
            case 'VerificarVentasAbiertasAction':
                $this->_verificarVentasAbiertas();
                break;
            case 'CierreDiarioAction':
                $this->_cierreDiario();
                break;
            case 'showGastosDiariosAction':
                $objView = new Application_Views_CajaView();
                $objView->showGastosDiarios();
                break;
            case 'showPagosFijosAction':
                $objView = new Application_Views_CajaView();
                $objView->showPagosFijos();
                break;

            case 'EditarGastosDiariosAction':
                $this->EditarGastosDiarios();
                break;

            case 'AnularGastosDiariosAction':
                $this->AnularGastosDiarios();
                break;

            case 'EditarGastosFijosAction':
                $this->EditarGastosFijos();
                break;

            case 'AnularGastosFijosAction':
                $this->AnularGastosFijos();
                break;

            case 'EditarGastosPlanillasAction':
                $this->EditarGastosPlanilla();
                break;

            case 'AnularGastosPlanillaAction':
                $this->AnularGastosPlanilla();
                break;

            case 'ActivarPagoAction':
                $this->ActivarPago();
                break;
            
            case 'ImprimePagoAction':
                $this->_imprimePago();
                break;

            case 'EgresosReporteAction':
                $this->_egresosReporte();
                break;
        }
    }

    private function _egresosReporte(){
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelGastosDiarios = new Application_Models_GastosDiariosModel();
                $nameCaja = $_POST["nameCaja"];
                $fechaIncio = $_POST["fechaIncio"];
                $fechaFin = $_POST["fechaFin"];
                $data = $objModelGastosDiarios->_listGastosDiariosReporte($nameCaja,$fechaIncio, $fechaFin);
                echo json_encode($data);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _listGastosDiarios() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelGastosDiarios = new Application_Models_GastosDiariosModel();
                $fechaIncio = "";
                $fechaFin = "";
                if (isset($_REQUEST['fechaIncio'])) {
                    $fechaIncio = $_REQUEST['fechaIncio'];
                }
                if (isset($_REQUEST['fechaFin'])) {
                    $fechaFin = $_REQUEST['fechaFin'];
                }
                $objModelGastosDiarios->_listGastosDiarios($fechaIncio, $fechaFin);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _listTotalVendido() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelGastosDiarios = new Application_Models_GastosDiariosModel();
                $fecha = date('Y-m-d');
                if (isset($_REQUEST['fecha'])) {
                    $fecha = $_REQUEST['fecha'];
                }
                $objModelGastosDiarios->_TotalDia($fecha);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _listMontoInicial() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelGastosDiarios = new Application_Models_GastosDiariosModel();
                $objModelGastosDiarios->_ListMontoInicial();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showRegister() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Views_GastosDiariosView();
                $objView->showRegister();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _saveGasto() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Models_GastosDiariosModel();
                echo $objView->RegistrarGastoDiario($_REQUEST["fecha_cierre"],$_REQUEST["monto"],$_REQUEST["tipo_movimiento"],$_REQUEST["medio_moneda"],$_REQUEST["descripcion"]);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _saveMontoInicial() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Models_GastosDiariosModel();
                $objView->RegistrarMontoInicial();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _verificarVentasAbiertas() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Models_GastosDiariosModel();


                $objView->_verificandoMesasAbiertas();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _cierreDiario() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Models_GastosDiariosModel();


                $objView->_cierreDiario();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function EditarGastosDiarios() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Models_GastosDiariosModel();
                $objView->_EditarGastoDiarios($_REQUEST['txtcantidadeditar'], $_REQUEST['txtdescripcioneditar'], $_REQUEST['codigo']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function AnularGastosDiarios() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Models_GastosDiariosModel();
                $objView->AnularGastosDiarios($_REQUEST['codigo']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function EditarGastosFijos() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Models_GastosDiariosModel();
                $objView->_EditarGastosFijos($_REQUEST['txtcantidadeditarFijo'], $_REQUEST['txtdescripcioneditarFijo'], $_REQUEST['codigo']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function AnularGastosFijos() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Models_GastosDiariosModel();
                $objView->AnularGastosFijos($_REQUEST['codigo']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function EditarGastosPlanilla() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Models_GastosDiariosModel();
                $objView->_EditarGastosPlanilla($_REQUEST['txtcantidadeditarPlanilla'], $_REQUEST['txtdescripcioneditarPlanilla'], $_REQUEST['codigo']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function AnularGastosPlanilla() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Models_GastosDiariosModel();
                $objView->AnularGastosPlanilla($_REQUEST['codigo']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
     private function ActivarPago() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Models_GastosDiariosModel();
                $objView->ActivarPago($_REQUEST['codigo']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
    //Actualizado 2017 - Gino lluen
    private function _imprimePago() {        
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Models_GastosDiariosModel();
                $objView->imprimePago($_REQUEST["pkPago"],$_REQUEST["terminal"],$_REQUEST["aux"]);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

}
