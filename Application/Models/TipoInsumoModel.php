<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Models_TipoInsumoModel {

    private $_descripcion;
    private $_idTipo;

    function __construct() {
        
    }

    public function get_descripcion() {
        return $this->_descripcion;
    }

    public function get_idTipo() {
        return $this->_idTipo;
    }

    public function set_descripcion($_descripcion) {
        $this->_descripcion = utf8_decode($_descripcion);
    }

    public function set_idTipo($_idTipo) {
        $this->_idTipo = $_idTipo;
    }

    public function listInsumo() {
        $db = new SuperDataBase();
        $query = "SELECT * FROM tipo_insumo t;";
        $resul = $db->executeQuery($query);
//die($query);
        $array = array();
        while ($row = $db->fecth_array($resul)) {
            $array[] = array(
                "id" => $row[0],
                "descripcion" => utf8_encode($row['descripcion'])
            );
        }
        echo json_encode($array);
    }

    public function SaveTipoInsumo($descripcion) {
        $db = new SuperDataBase();
        $query = "insert into tipo_insumo(descripcion,estado) values('$descripcion',0)";
        $db->executeQuery($query);
    }

    public function EditTipoInsumo($id, $descripcion) {
        $db = new SuperDataBase();
        $query = "update tipo_insumo set descripcion='$descripcion' where pkTipoInsumo=$id";
        $db->executeQuery($query);
    }

    public function DeleteTipoInsumo($id) {
        $db = new SuperDataBase();
        $query = "update tipo_insumo set estado=1 where pkTipoInsumo=$id";
        $db->executeQuery($query);
    }

    public function ActivarTipoInsumo($id) {
        $db = new SuperDataBase();
        $query = "update tipo_insumo set estado=0 where pkTipoInsumo=$id";
        $db->executeQuery($query);
    }

}
