<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Controllers_TypeDocumentController {

    public function __construct($action) {
        switch ($action) {

            case 'ListAction':
                $this->_listTypeDocument();
                break;
        }
    }
    
    private function _listTypeDocument(){
        $objModelTypeDocument= new Application_Models_TypeDocumentModel();
        $objModelTypeDocument->_listTypeDocument();
    }

}
