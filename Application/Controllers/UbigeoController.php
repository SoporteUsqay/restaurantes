<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Controllers_UbigeoController {

    public function __construct($action) {
        switch ($action) {

            case 'ListDepartamentAction':
                $this->_listUbigeoDeparmet();
                break;
            case 'ListProvinceAction':
                $this->_listUbigeoProvince();
                break;
            case 'ListDistrictAction':
                $this->_listUbigeoDistrict();
                break;
        }
    }
    
    private function _listUbigeoDeparmet(){
        $objModelUbigeo= new Application_Models_UbigeoModel();
        $objModelUbigeo->_listUbigeoDeparment();
    }
    private function _listUbigeoProvince(){
        $objModelUbigeo= new Application_Models_UbigeoModel();
        $objModelUbigeo->set_departamento($_REQUEST['departament']);
        $objModelUbigeo->_listUbigeoProvince();
    }
    private function _listUbigeoDistrict(){
        $objModelUbigeo= new Application_Models_UbigeoModel();
        $objModelUbigeo->set_provincia($_REQUEST['province']);
        $objModelUbigeo->_listUbigeoDistrict();
    }

}
