<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Models_TypeUserModel {

    function __construct() {
        
    }

    public function _listTypeUser() {
        $db = new SuperDataBase();
        $sucursal=  UserLogin::get_pkSucursal();
        $query = "SELECT * FROM tipotrabajador t";
        $resul = $db->executeQuery($query);
        
//        $array = array();
        while ($row = $db->fecth_array($resul)) {
            $array[] = array("id" => $row[0],
                "description" => utf8_encode($row[1])
            );
        }
        echo json_encode($array);
    }

}
