<?php

class Application_Controllers_CategoriaController {

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
        }
    }

    private function _Save() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_CategoriaModel();
                $obj->set_descripcion($_REQUEST['descripcion']);//                            
                $obj->registrarCategoria();
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
                $obj = new Application_Models_CategoriaModel();
                $obj->updateCategoria($_POST['id'], $_REQUEST['descripcion']);
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
                $obj = new Application_Models_CategoriaModel();
                $obj->deleteCategoria($_POST['id']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
    private function _listCategoria() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_CategoriaModel();
                $obj->listCategoria();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

}
