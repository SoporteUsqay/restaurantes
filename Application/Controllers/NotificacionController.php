<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Controllers_NotificacionController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {

            case 'UpdateEstadoAction':
                $this->_updateEstado();
                break;
            case 'ListAction':
                $this->_listado();
                break;

            case 'ListtipoTrabajadorAction':
                $this->_ListtipoTrabajador();
                break;
        }
    }

    private function _ListtipoTrabajador() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelNotificacion = new Application_Models_NotificacionModel();
                $objModelNotificacion->_listTipoTrabajador();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _updateEstado() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelNotificacion = new Application_Models_NotificacionesModel();
               echo $objModelNotificacion->UpdateNotificacion();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    private function _listado() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelNotificacion = new Application_Models_NotificacionesModel();
               echo $objModelNotificacion->Lista();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

}
