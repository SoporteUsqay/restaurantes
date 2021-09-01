<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Controllers_ProfessionsController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {

            case 'ListAction':
                $this->_listProfessions();
                break;
        }
    }

    private function _listProfessions() {
        $objModelProfessions = new Application_Models_ProfessionsModel();
        $objModelProfessions->_listProfessions();
    }

}
