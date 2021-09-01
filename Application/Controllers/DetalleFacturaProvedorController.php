<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Controllers_DetalleFacturaProvedorController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {

            case 'ListAction':
                $this->_listDetalleFacturaProvedor();
                break;
            case 'CerrarPedidoAction':
                $this->_CerrarPedido();
                break;
            case 'AddDetalleFacturaProvedorAction':
                $this->_addPedidoFactura();
                break;
        }
    }

    private function _addPedidoFactura() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {

                $objModelDetalleFacturaProvedor = new Application_Models_DetalleFacturaProvedorModel();
                $objModelDetalleFacturaProvedor->set_cantidad($_REQUEST['cantidad']);
                $objModelDetalleFacturaProvedor->set_cantidadTotal($_REQUEST['cantidadTotal']);
                $objModelDetalleFacturaProvedor->set_pkFactura($_REQUEST['pkFactura']);
                $objModelDetalleFacturaProvedor->set_pkProducto($_REQUEST['pkProducto']);
                $objModelDetalleFacturaProvedor->set_precioUnitario($_REQUEST['precioUnitario']);
                $objModelDetalleFacturaProvedor->set_subTotal($_REQUEST['total']);
                $objModelDetalleFacturaProvedor->set_valorUnidad($_REQUEST['valorUnidad']);
//                $objModelDetalleFacturaProvedor->set
                $result=$objModelDetalleFacturaProvedor->agregarDetalleFacturaProvedor();
                echo $result;
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _listDetalleFacturaProvedor() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelDetalleFacturaProvedor = new Application_Models_DetalleFacturaProvedorModel();
                $objModelDetalleFacturaProvedor->ListdetallePedidos($_REQUEST['pkFactura']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _CerrarPedido() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelDetalleFacturaProvedor = new Application_Models_DetalleFacturaProvedorsModel();
                $objModelDetalleFacturaProvedor->updateEstados($_REQUEST['pkDetalle'], 1);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

}
