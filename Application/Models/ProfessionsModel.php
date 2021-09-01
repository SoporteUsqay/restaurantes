<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Models_ProfessionsModel {

    function __construct() {
        
    }

    public function _listProfessions() {
        $db = new SuperDataBase();
        $query = "SELECT * FROM professions t;";
        $resul = $db->executeQuery($query);
        
//        $array = array();
        while ($row = $db->fecth_array($resul)) {
            $array[] = array("pkProfessions" => $row['pkProfessions'],
                "description" =>  utf8_encode( $row['descriptionProfessions'])
            );
        }
        echo json_encode($array);
    }

}
