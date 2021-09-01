<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Models_TipoPedidoModel {

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
        $query = "SELECT * FROM tipo_pedido t;;";
        $resul = $db->executeQuery($query);
//die($query);
        $array = array();
        while ($row = $db->fecth_array($resul)) {
            $array[] = array(
                "id" => $row[0],
                "descripcion" => utf8_encode($row['nombre'])
            );
        }
        echo json_encode($array);
    }

}
