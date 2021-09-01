<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Controllers_FacturaProvedorController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {

            case 'ListAction':
                $this->_listFacturaProvedor();
                break;
            case "SaveFacturaProvedorAction":
                $this->_saveFacturaProvedor();
                break;
            case 'ListFactureAction':
                $this->_listFacturaProvedorFilter();
                break;
        }
    }

    private function _listFacturaProvedor() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelFacturaProvedor = new Application_Models_FacturaProvedorModel();
                $objModelFacturaProvedor->_listFacturaProvedor();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _listFacturaProvedorFilter() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelFacturaProvedor = new Application_Models_FacturaProvedorModel();
                $nroFactura = null;
                if (isset($_REQUEST['nroFactura'])) {
                    $nroFactura = $_REQUEST['nroFactura'];
                }
                $fecha = null;
                if (isset($_REQUEST['fecha'])) {
                    $fecha = $_REQUEST['fecha'];
                }
                $provedor = null;
                if (isset($_REQUEST['pkProvedor'])) {
                    $provedor = $_REQUEST['pkProvedor'];
                }
                $objModelFacturaProvedor->searchFacturaProvedor($nroFactura, $provedor, $fecha);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _saveFacturaProvedor() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelFacturaProvedor = new Application_Models_FacturaProvedorModel();
                $objModelFacturaProvedor->set_fecha($_REQUEST['fecha']);
                $objModelFacturaProvedor->set_nroFactura($_REQUEST['nroFactura']);
                $objModelFacturaProvedor->set_pkProvedor($_REQUEST['pkProvedor']);
//                $objModelFacturaProvedor->se
                $result = $objModelFacturaProvedor->GuardarFacturaProvedor();
                echo $result;
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

}
