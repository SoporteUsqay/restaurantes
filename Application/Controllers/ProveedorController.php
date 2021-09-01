<?php

class Application_Controllers_ProveedorController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {

            case "SaveAction":
                $this->_Save();
                break;
            case "EditAction":
                $this->_Edit();
                break;
            case "DeleteAction":
                $this->_Delete();
                break;
            case "ActiveAction":
                $this->_Active();
                break;
            
            case "FiltroProveedorAction":
                $this->_FiltroProveedor();
                break;
        }
    }

    
    private function _FiltroProveedor() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelProvedor = new Application_Models_ProvedorModel();
                
                $valor = "";
                if (isset($_REQUEST['valor'])) 
                  {
                    $valor = $_REQUEST['valor']; 
                  }                  
                
                $objModelProvedor->filtro_proveedor($valor);
                
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

                $obj = new Application_Models_ProvedorModel();
                $obj->set_pkProveedor($_REQUEST['id']);
                $obj->set_ruc($_REQUEST['ruc']);
                $obj->set_razon($_REQUEST['razon']);
                $obj->set_direccion($_REQUEST['direccion']);
                $obj->set_telefono($_REQUEST['telefono']);
                $obj->set_pagweb($_REQUEST['pagweb']);
                $obj->set_mail($_REQUEST['mail']);
                $obj->registrarProveedor();
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
                $obj = new Application_Models_ProvedorModel();
                $obj->set_pkProveedor($_REQUEST['id']);
                $obj->set_ruc($_REQUEST['ruc']);
                $obj->set_razon($_REQUEST['razon']);
                $obj->set_direccion($_REQUEST['direccion']);
                $obj->set_telefono($_REQUEST['telefono']);
                $obj->set_pagweb($_REQUEST['pagweb']);
                $obj->set_mail($_REQUEST['mail']);
                $obj->updateProveedor();
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
                $obj = new Application_Models_ProvedorModel();
                $obj->set_pkProveedor($_REQUEST['id2']);
                $obj->deleteProveedor();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
    private function _Active() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_ProvedorModel();
                $obj->set_pkProveedor($_REQUEST['id2']);
                $obj->activeProveedor();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

}
