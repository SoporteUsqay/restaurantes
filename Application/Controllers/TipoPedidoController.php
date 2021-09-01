<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Controllers_TipoPedidoController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {


            case 'ListAction':
                $this->_List();
                break;
        }
    }

    private function _List() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_TipoPedidoModel();


                $obj->listInsumo();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

}
