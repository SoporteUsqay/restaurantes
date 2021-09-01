<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Controllers_MozoController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {

            case 'ListAction':
                $this->_listMozo();
                break;

            case 'ListtipoTrabajadorAction':
                $this->_ListtipoTrabajador();
                break;
        }
    }

   private function _ListtipoTrabajador() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelMozo = new Application_Models_MozoModel();
                $objModelMozo->_listTipoTrabajador();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _listMozo() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelMozo = new Application_Models_MozoModel();
                
                $tipoTrabajador = "";
                if (isset($_REQUEST['pkTipoTrabajador'])) {
                    $tipoTrabajador = $_REQUEST['pkTipoTrabajador'];
                }
                $objModelMozo->_listMozo($tipoTrabajador);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }


}