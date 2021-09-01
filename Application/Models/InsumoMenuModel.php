<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Models_InsumoMenuModel {

    private $pkInsumo;
    private $pkSucursal;
    private $_cantidad;
    private $_pkUnidad;
    private $_pkPlato;

    function __construct() {
        
    }

    function getPkInsumo() {
        return $this->pkInsumo;
    }

    function getPkSucursal() {
        return $this->pkSucursal;
    }

    function get_cantidad() {
        return $this->_cantidad;
    }

    function get_pkUnidad() {
        return $this->_pkUnidad;
    }

    function get_pkPlato() {
        return $this->_pkPlato;
    }

    function setPkInsumo($pkInsumo) {
        $this->pkInsumo = $pkInsumo;
    }

    function setPkSucursal($pkSucursal) {
        $this->pkSucursal = $pkSucursal;
    }

    function set_cantidad($_cantidad) {
        $this->_cantidad = $_cantidad;
    }

    function set_pkUnidad($_pkUnidad) {
        $this->_pkUnidad = $_pkUnidad;
    }

    function set_pkPlato($_pkPlato) {
        $this->_pkPlato = $_pkPlato;
    }

    public function insert() {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "insert into insumo_menu (pkInsumo, pkSucursal, cantidadTotal, pkTipoPedido, pkPlato) values($this->pkInsumo,'$sucursal',$this->_cantidad,$this->_pkUnidad,'$this->_pkPlato')";
//       echo $query;
        $db->executeQuery($query);
        return $db->getId();
    }

    public function update($id, $estado) {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "update insumo_menu set cantidadTotal=$this->_cantidad,pkTipoPedido=$estado where id=$id";
//       echo $query;
        $db->executeQuery($query);
        return $db->getId();
    }

    public function delete($id) {
        $db = new SuperDataBase();
        $query = "delete from insumo_menu  where id=$id";
//       echo $query;
        $db->executeQuery($query);
        return"1";
    }

    public function Listar() {
        $db = new SuperDataBase();
        $query = "SELECT * FROM insumo_menu i inner join plato p on p.pkPlato=i.pkPlato inner join insumos ins on ins.pkInsumo=i.pkInsumo";
//                   echo $query;
        $array = array();
        $result = $db->executeQuery($query);
        while ($row = $db->fecth_array($result)) {
            $estado = "PARA LA MESA";
            if ($row['estado'] == "1")
                $estado = "PARA LLEVAR";
            $array[] = array(
                "id" => $row[0],
                "descripcion" => utf8_encode($row['descripcion']),
                "descripcionInsumo" => utf8_encode($row['descripcionInsumo']),
                "cantidad" => utf8_encode($row['cantidadTotal']),
                "label" => utf8_encode($row['descripcionInsumo']),
                "estado" => utf8_encode($estado)
            );
        }
        echo json_encode($array);
    }

    public function sel($id) {
        $db = new SuperDataBase();
        $query = "SELECT p.descripcion as nplato, ins.descripcionInsumo as ninsumo, i.cantidadTotal, i.pkInsumo, u.descripcion as nunidad, i.pkTipoPedido FROM insumo_menu i inner join plato p on p.pkPlato=i.pkPlato inner join insumos ins on ins.pkInsumo=i.pkInsumo inner join unidad u on ins.pkUnidad = u.pkUnidad where i.id=" . $id;
//                   echo $query;
        $array = array();
        $result = $db->executeQuery($query);
        while ($row = $db->fecth_array($result)) {

            $array[] = array(
                "id" => $row[0],
                "descripcion" => utf8_encode($row['nplato']),
                "descripcionInsumo" => utf8_encode($row['ninsumo']),
                "cantidad" => utf8_encode($row['cantidadTotal']),
                "pkInsumo" => utf8_encode($row['pkInsumo']),
                "unidad" => utf8_encode($row['nunidad']),
                "estado" => utf8_encode($row['pkTipoPedido'])
            );
        }
        echo json_encode($array);
    }


}
