<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Controllers_DetallePedidoController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {

            case 'ListAction':
                $this->_listDetallePedido();
                break;
            case 'CerrarPedidoAction':
                $this->_CerrarPedido();
                break;
        }
    }

    private function _listDetallePedido() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelDetallePedido = new Application_Models_DetallePedidosModel();
                $objModelDetallePedido->_listDetallePedido();
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
                $objModelDetallePedido = new Application_Models_DetallePedidosModel();
                $objModelDetallePedido->updateEstados($_REQUEST['pkDetalle'],$_REQUEST['tipo']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

}
