<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Controllers_SexoController {

    public function __construct($action) {
        switch ($action) {

            case 'ListAction':
                $this->_listSexo();
                break;
        }
    }
    
    private function _listSexo(){
        $objModelSexo= new Application_Models_SexoModel();
        $objModelSexo->_listSexo();
    }
    
    //Ohhh ahhhh durooo ricccoooo ahhhhhhhhhhh ohhhhh

}
