<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Models_ModuleModel {

    private $_pkSubModuel;
    private $_array = array();

    public function get_pkSubModuel() {
        return $this->_pkSubModuel;
    }

    public function set_pkSubModuel($_pkSubModuel) {
        $this->_pkSubModuel = $_pkSubModuel;
    }

    public function _validaExistsModule() {
        $db = new SuperDataBase();
        $q = "CALL sp_list_subModule(2);";
        $re = $db->executeQuery($q);

        while ($raaaow = $db->fecth_array($re)) {
            $this->_array = $raaaow[0];
            echo "<li><a>" . $raaaow[0] . "</a></li>";
        }
//    $db->close_connection();
//    if(!empty($this->_array))
//    {
//        echo "true";
//    }
//    else echo "false";
    }

    /**
     * @author Jeison Cruz Yesan
     * @access public
     * Add Module
     * * */
    public function _addModule() {
        
    }

}
