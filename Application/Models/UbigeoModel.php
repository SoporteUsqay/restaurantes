<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Models_UbigeoModel {

    private $_departamento;
    private $_provincia;
    private $_distrito;
    private $_idUbicacion;

    function __construct() {
        
    }

    public function get_departamento() {
        return $this->_departamento;
    }

    public function get_provincia() {
        return $this->_provincia;
    }

    public function get_distrito() {
        return $this->_distrito;
    }

    public function get_idUbicacion() {
        return $this->_idUbicacion;
    }

    public function set_departamento($_departamento) {
        $this->_departamento = $_departamento;
    }

    public function set_provincia($_provincia) {
        $this->_provincia = $_provincia;
    }

    public function set_distrito($_distrito) {
        $this->_distrito = $_distrito;
    }

    public function set_idUbicacion($_idUbicacion) {
        $this->_idUbicacion = $_idUbicacion;
    }

    public function _listUbigeoDeparment() {
        $db = new SuperDataBase();
        $query = "SELECT distinct(departamento) FROM ubicacion u;";
        $resul = $db->executeQuery($query);

//        $array = array();
        while ($row = $db->fecth_array($resul)) {
            $array[] = array(
//                "idUbicacion" => $row['idUbicacion'],
                "description" => utf8_encode($row['departamento'])
            );
        }
        echo json_encode($array);
    }
    public function _listUbigeoProvince() {
        $db = new SuperDataBase();
        $query = "SELECT distinct(provincia) FROM ubicacion u where departamento='$this->_departamento';";
        $resul = $db->executeQuery($query);

//        $array = array();
        while ($row = $db->fecth_array($resul)) {
            $array[] = array(
//                "idUbicacion" => $row['idUbicacion'],
                "description" => utf8_encode($row['provincia'])
            );
        }
        echo json_encode($array);
    }
    public function _listUbigeoDistrict() {
        $db = new SuperDataBase();
        $query = "SELECT idUbicacion,distrito FROM ubicacion u where provincia='$this->_provincia';";
        $resul = $db->executeQuery($query);

//        $array = array();
        while ($row = $db->fecth_array($resul)) {
            $array[] = array(
                "idUbicacion" => $row['idUbicacion'],
                "description" => utf8_encode($row['distrito'])
            );
        }
        echo json_encode($array);
    }

}
