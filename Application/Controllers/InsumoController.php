<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Controllers_InsumoController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {


            case "SaveAction":
                $this->_Save();
                break;

            case "UpdateAction":
                $this->_Edit();
                break;
            case "DeleteAction":
                $this->_Delete();
                break;
            case "ActiveAction":
                $this->_Active();
                break;
            case "ListAction":
                $this->_list();
                break;
            case "ListIdAction":
                $this->_listID();
                break;
            case "UpdateCantidadAction":
                $this->_listID();
                break;
        }
    }

    private function _Save() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_InsumoModel();

                $obj->set_descripcion($_REQUEST['descripcion']);
                $obj->set_estado($_REQUEST['estado']);
                $obj->setPrecio_p($_REQUEST['precio']);
//                $proveedor="";
//                if(isset($_REQUEST['provedor'])){
                $obj->setProvedor($_REQUEST['provedor']);
                
//                }
                $obj->setPresentacion($_REQUEST['pkInsumo']);
                $obj->setPkTipoInsumo($_REQUEST['pkTipoInsumo']);
                $obj->set_stockMinimo($_REQUEST['stockMinimo']);
                $obj->set_porcentajeMerma($_REQUEST['porcentajeMerma']);
//                            
                echo $result = $obj->guardar();
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
                $obj = new Application_Models_InsumoModel();
                $obj->set_descripcion($_REQUEST['descripcion']);
                $obj->set_estado($_REQUEST['estado']);
                $obj->setPrecio_p($_REQUEST['precio']);
                $obj->setProvedor($_REQUEST['provedor']);
                $obj->setPresentacion($_REQUEST['pkInsumo']);
                $obj->setPkTipoInsumo($_REQUEST['pkTipoInsumo']);
                $obj->set_stockMinimo($_REQUEST['stockMinimo']);
                $obj->set_porcentajeMerma($_REQUEST['porcentajeMerma']);
                $obj->updateInsumo($_REQUEST['id']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _updateCantidad() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                
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
                $obj = new Application_Models_InsumoModel();
                $obj->deleteInsumo($_POST['id']);
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
                $obj = new Application_Models_InsumoModel();
                $obj->activeInsumo($_POST['id']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _list() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_InsumoModel();
                $obj->listInsumos();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _listID() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_InsumoModel();
                $obj->listInsumosId($_REQUEST['id']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

}
