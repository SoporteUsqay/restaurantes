<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Controllers_IngresosInsumosController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {


            case "SaveAction":
                $this->_Save();
                break;

            case "SaveDetalleAction":
                $this->_saveDetalle();
                break;

            case 'DeleteDetalleGuiaAction':
                $this->_DeleteDetalleGuia();
                break;

            case 'ActiveDetalleGuiaAction':
                $this->_ActiveDetalleGuia();
                break;

            case 'MostrarDatosGuiasAction':
                $this->_MostrarDatosGuias();
                break;

            case 'UpdateDatosGuiasAction':
                $this->_UpdateDatosGuias();
                break;

            case "SaveDetalleGuiaSalidaAction":
                $this->_saveDetalleGuiaSalida();
                break;

            case 'UpdateDatosGuiaSalidaAction':
                $this->_UpdateDatosGuiaSalida();
                break;

            case 'SaveDetalleGuiaSalidaPlatoAction':
                $this->_SaveDetalleInsumoPlato();
                break;
        }
    }

    private function _UpdateDatosGuias() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_IngresoInsumosModel();

                $pkGuia = "";
                if (isset($_REQUEST['txtIdGuia'])) {
                    $pkGuia = $_REQUEST['txtIdGuia'];
                }

                $pkIngresoInsumo = "";
                if (isset($_REQUEST['txtIdDetalleInsumo'])) {
                    $pkIngresoInsumo = $_REQUEST['txtIdDetalleInsumo'];
                }

                $pkInsumo = "";
                if (isset($_REQUEST['txtingreseInsumo-id2'])) {
                    $pkInsumo = $_REQUEST['txtingreseInsumo-id2'];
                }

                $cantidad = "";
                if (isset($_REQUEST['Cantidad'])) {
                    $cantidad = $_REQUEST['Cantidad'];
                }

                $precio = 0;
                if (isset($_REQUEST['Precio'])) {
                    $precio = $_REQUEST['Precio'];
                }

                $tipo = 1;

                $obj->ModificarDatosGuias($pkGuia, $pkIngresoInsumo, $pkInsumo, $cantidad, $precio, $tipo);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _UpdateDatosGuiaSalida() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_IngresoInsumosModel();

                $pkGuia = "";
                if (isset($_REQUEST['txtIdGuia'])) {
                    $pkGuia = $_REQUEST['txtIdGuia'];
                }

                $pkIngresoInsumo = "";
                if (isset($_REQUEST['txtIdDetalleInsumo'])) {
                    $pkIngresoInsumo = $_REQUEST['txtIdDetalleInsumo'];
                }

                $pkInsumo = "";
                if (isset($_REQUEST['txtingreseInsumo-id2'])) {
                    $pkInsumo = $_REQUEST['txtingreseInsumo-id2'];
                }

                $cantidad = "";
                if (isset($_REQUEST['Cantidad'])) {
                    $cantidad = $_REQUEST['Cantidad'];
                }

                $precio = 0;
                $tipo = 2;

                $obj->ModificarDatosGuias($pkGuia, $pkIngresoInsumo, $pkInsumo, $cantidad, $precio, $tipo);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _MostrarDatosGuias() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_IngresoInsumosModel();
                $obj->_ListDatosDetalleGuia($_REQUEST['id']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _ActiveDetalleGuia() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_IngresoInsumosModel();
                $obj->ActivedetalleGuia($_REQUEST['id']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _DeleteDetalleGuia() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_IngresoInsumosModel();
                $obj->deletedetalleGuia($_REQUEST['id']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _saveDetalle() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objIngreso = new Application_Models_IngresoInsumosModel();

                $pkInsumo = "";
                if (isset($_REQUEST['idInsumo'])) {
                    $pkInsumo = $_REQUEST['idInsumo'];
                }

                $cantidad = 0;
                if (isset($_REQUEST['cantidad'])) {
                    $cantidad = $_REQUEST['cantidad'];
                }

                $precio = 0;
                if (isset($_REQUEST['Valorprecio'])) {
                    $precio = $_REQUEST['Valorprecio'];
                }

                $pkComprobante = "";
                if (isset($_REQUEST['PkComprobante'])) {
                    $pkComprobante = $_REQUEST['PkComprobante'];
                }

                $tipo = 1;
                $descripcion = "";
//                $objIngreso->saveDetalle($_REQUEST['cantidad'],$_REQUEST['idInsumo'],$_REQUEST['idDetalle'],$precio);
                $objIngreso->saveDetalle($descripcion, $cantidad, $pkInsumo, $pkComprobante, $precio, $tipo);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _saveDetalleGuiaSalida() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objIngreso = new Application_Models_IngresoInsumosModel();

                $pkInsumo = "";
                if (isset($_REQUEST['idInsumo'])) {
                    $pkInsumo = $_REQUEST['idInsumo'];
                }

                $cantidad = 0;
                if (isset($_REQUEST['cantidad'])) {
                    $cantidad = $_REQUEST['cantidad'];
                }

                $descripcion = "";
                if (isset($_REQUEST['descripcion'])) {
                    $descripcion = $_REQUEST['descripcion'];
                } else {
                    $descripcion = "Salida de insumo";
                }

                $precio = 0;

                $pkComprobante = "";
                if (isset($_REQUEST['PkComprobante'])) {
                    $pkComprobante = $_REQUEST['PkComprobante'];
                }
                $tipo = 2;
                $objIngreso->saveDetalle($descripcion, $cantidad, $pkInsumo, $pkComprobante, $precio, $tipo);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _Save() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_InsumoModel();

                $obj->updateCantidad($_POST['id'], $_REQUEST['tipo'], $_REQUEST['cantidad']);
                $objIngreso = new Application_Models_IngresoInsumosModel();
                $objIngreso->set_cantidad($_REQUEST['cantidad']);
                $objIngreso->set_descripcion($_REQUEST['descripcion']);
                $objIngreso->set_pkInsumo($_REQUEST['id']);
                $objIngreso->set_tipo($_REQUEST['tipo']);
                //tipo 1= Sumar   
                echo $objIngreso->insert();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _Edit() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_InsumoModel();
                $obj->updateInsumo($_POST['id'], $_REQUEST['descripcion']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _updateCantidad() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {

                $obj = new Application_Models_InsumoModel();
                $obj->updateCantidad($_POST['id'], $_REQUEST['tipo'], $_REQUEST['cantidad']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _Delete() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_InsumoModel();
                $obj->deleteInsumo($_POST['id']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _list() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_InsumoModel();
                $obj->listInsumos();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _listID() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_InsumoModel();
                $obj->listInsumosId($_REQUEST['id']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _SaveDetalleInsumoPlato() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $precio = 0;
                $obj = new Application_Models_IngresoInsumosModel();
                $obj->_saveInsumoporPlato($_REQUEST['idPlato'], $_REQUEST['descripcion'], $_REQUEST['cantidad'], $_REQUEST['pedido'], $_REQUEST['comprobante'], $precio);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

}
