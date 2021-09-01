<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Controllers_ProductosFacturaController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {
            case 'ListProductosFacturaItemAction':
                $this->_listProductosFactura();
                break;
        }
    }
/*Listar todos los productos que puedan contener una factura*/
    private function _listProductosFactura() {
//      echo"ll";
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelPFactura = new Application_Models_ProductoFacturaModel();
                $objModelPFactura->ListProductosFactura();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

}
