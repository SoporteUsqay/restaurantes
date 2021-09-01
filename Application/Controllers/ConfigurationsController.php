<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Controllers_ConfigurationsController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {

            case 'ListAction':
                $this->_listConfigurations();
                break;
            case 'ShowReportarProblemaAction':
                $this->_showReportarProblema();
                break;
            case 'ShowEmpresaAction':
                $this->_showEmpresa();
                break;
            case 'ShowSistemaAction':
                $this->_showSistema();
                break;
            case 'ShowUsuariosAction':
                $this->_showUsuarios();
                break;
            case 'ShowInformacionAction':
                $this->_showInformacion();
                break;
            case 'ShowMesasAction':
                if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelConfigurations = new Application_Views_ConfigurationsView();
                $objModelConfigurations->showMesas();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
                break;
            case 'ListSalonesAction':
                $this->_ListSalones();
                break;
        } 
    }

//    private function _listConfigurations() {
//        if (self::$session->validateStartSesion()) {
//            if (!self::$session->validateSesion()) {
//                $objModelConfigurations = new Application_Models_ConfigurationsModel();
//                $objModelConfigurations->_listConfigurations();
//            } else {
//                self::$session->redirect();
//            }
//        } else {
//            self::$session->redirect();
//        }
//    }

    private function _showReportarProblema() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelConfigurations = new Application_Views_ConfigurationsView();
                $objModelConfigurations->showReportarProblema();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showEmpresa() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelConfigurations = new Application_Views_ConfigurationsView();
                $objModelConfigurations->showEmpresa();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showSistema() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelConfigurations = new Application_Views_ConfigurationsView();
                $objModelConfigurations->showSistema();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showUsuarios() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelConfigurations = new Application_Views_ConfigurationsView();
                $objModelConfigurations->showUser();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showInformacion() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelConfigurations = new Application_Views_ConfigurationsView();
                $objModelConfigurations->showInformacion();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _ListSalones() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelMozo = new Application_Models_MesaModel();
                $objModelMozo->_listSalones();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

}
