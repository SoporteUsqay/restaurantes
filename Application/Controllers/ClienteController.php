<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Controllers_ClienteController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {
            case 'ListAction':
                $this->_listAllCliente();
                break;
            case 'List2Action':
                $this->_listAllCliente2();
                break;
            case 'ShowAdminClienteAction':
                $this->_showAdminCliente();
                break;
            case 'ShowListAction':
                $this->_showList();
                break;
            case 'ShowIndexAction':
                $this->_showRegister();
                break;
            case 'SaveAction':
                $this->_save();
                break;
            case 'ValidateDocumentAction':
                $this->_validateDocument();
                break;
            case 'ValidateEmailAction':
                $this->_validateEmail();
                break;
            case 'ClienteRucAction':
                $this->_lisClientexRuc();
                break;
            case 'ClienteDniAction':
                $this->_lisClientexDni();
                break;
            case 'ClientPhoneAction':
                $this->_listClientXPhone();
                break;
            case 'getPedidoAction':
                $this->_getClientePedido();
                break;
            case 'updatePedidoAction':
                $this->_updateClientePedido();
                break;
            
        }
    }

    private function _listAllCliente() {
        $objModelCliente = new Application_Models_ClienteModel();
        $objModelCliente->_listAllCliente();
    }
    
    private function _listClientXPhone() {
        $objModelCliente = new Application_Models_ClienteModel();
        $objModelCliente->getClientByPhone($_REQUEST["phone"]);
    }
    
    private function _getClientePedido() {
        $objModelCliente = new Application_Models_ClienteModel();
        $objModelCliente->getClientePedido($_REQUEST["pedido"]);
    }
    
    private function _updateClientePedido(){
        $objModelCliente = new Application_Models_ClienteModel();
        $objModelCliente->updateClientePedido($_REQUEST["pedido"],$_REQUEST["tipop"],$_REQUEST['nombres'],$_REQUEST['telefono'],$_REQUEST['documento'],$_REQUEST['direccion']);
    }

    private function _listAllCliente2() {
        $objModelCliente = new Application_Models_ClienteModel();
        $objModelCliente->_listAllCliente2();
    }

    private function _showAdminCliente() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objViewCliente = new Application_Views_ClienteView();
                $objViewCliente->showAdminCliente();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showList() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objViewCliente = new Application_Views_ClienteView();
                $objViewCliente->showList();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _save() {
        $_address = $_POST['address'];
        $_celPhone = $_POST['celPhone'];
        $_dateBirth = $_POST['dateBirth'];
        $_document = $_POST['document'];
        $_email = $_POST['email'];
        $_fkAreaTrabajo = $_POST['fkAreaTrabajo'];
        $_fkProfesion = $_POST['fkProfesion'];
        $_fkStatusCivil = $_POST['fkStatusCivil'];
        $_fkSexo = $_POST['fkSexo'];
        $_fkTypeDocument = $_POST['fkTypeDocument'];
        $_fkUbigeo = $_POST['fkUbigeo'];
        $_fkWorkStation = $_POST['fkWorkStation'];
        $_names = $_POST['names'];
        $_surname = $_POST['surname'];
        $_telf = $_POST['telf'];
        $_ruc = $_POST['ruc'];
        $objModelCliente = new Application_Models_ClienteModel();
        $objModelCliente->set_address($_address);
        $objModelCliente->set_celPhone($_celPhone);
        $objModelCliente->set_dateBirth($_dateBirth);
        $objModelCliente->set_document($_document);
        $objModelCliente->set_email($_email);
        $objModelCliente->set_fkAreaTrabajo($_fkAreaTrabajo);
        $objModelCliente->set_fkProfesion($_fkProfesion);
        $objModelCliente->set_fkStatusCivil($_fkStatusCivil);
        $objModelCliente->set_fkSexo($_fkSexo);
        $objModelCliente->set_fkTypeDocument($_fkTypeDocument);
        $objModelCliente->set_fkUbigeo($_fkUbigeo);
        $objModelCliente->set_fkWorkStation($_fkWorkStation);
        $objModelCliente->set_names($_names);
        $objModelCliente->set_surname($_surname);
        $objModelCliente->set_telf($_telf);
        $objModelCliente->set_ruc($_ruc);
        $objModelCliente->_save();
    }

    private function _validateDocument() {
        $objModelCliente = new Application_Models_ClienteModel();
        $objModelCliente->_validationIdentification($_POST['document']);
    }

    private function _validateEmail() {
        $objModelCliente = new Application_Models_ClienteModel();
        $objModelCliente->_validationEmail($_POST['email']);
    }

    private function _lisClientexRuc() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {

                $objModel = new Application_Models_ClienteModel();

                $objModel->listCustomerXRUC($_REQUEST['document']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _lisClientexDni() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {

                $objModel = new Application_Models_ClienteModel();

                $objModel->listCustomerXDNI($_REQUEST['document']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

}

//aaassjijkjkjkjkjkjkjkjk
//Puto el que lo LEA
