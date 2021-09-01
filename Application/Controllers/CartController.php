<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Controllers_CartController {
 private static $session;
    public function __construct($action) {
         self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {

            case 'ListAction':
                $this->_listCart();
                break;
            case 'ShowRegisterAction':
                $this->_showRegister();
                break;
        }
    }

    private function _listCart() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
//                $objModelCart = new Application_Models_CartModel();
//                $objModelCart->_listCart();
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
              $objView = new Application_Views_CartView();
              $objView->showRegister();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

}
