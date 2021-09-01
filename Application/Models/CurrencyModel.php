<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Models_CurrencyModel {

    function __construct() {
        
    }

    public function _listCurrency() {
        $db = new SuperDataBase();
        $query = "SELECT * FROM sfg_currency a;";
        $resul = $db->executeQuery($query);
        
//        $array = array();
        while ($row = $db->fecth_array($resul)) {
            $array[] = array("pkCurrency" => $row['pkCurrency'],
                "description" => $row['description']
            );
        }
        echo json_encode($array);
    }

}
