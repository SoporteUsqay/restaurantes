<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Models_DetalleFacturaProvedorModel {

    private $_pkDetalleFacturaProvedor, $_cantidad, $_precioUnitario, $_subTotal, $_cantidadTotal, $_valorUnidad, $_pkProducto, $_pkFactura;

    function __construct() {
        
    }

    public function get_pkDetalleFacturaProvedor() {
        return $this->_pkDetalleFacturaProvedor;
    }

    public function get_cantidad() {
        return $this->_cantidad;
    }

    public function get_precioUnitario() {
        return $this->_precioUnitario;
    }

    public function get_subTotal() {
        return $this->_subTotal;
    }

    public function get_cantidadTotal() {
        return $this->_cantidadTotal;
    }

    public function get_valorUnidad() {
        return $this->_valorUnidad;
    }

    public function get_pkProducto() {
        return $this->_pkProducto;
    }

    public function get_pkFactura() {
        return $this->_pkFactura;
    }

    public function set_pkDetalleFacturaProvedor($_pkDetalleFacturaProvedor) {
        $this->_pkDetalleFacturaProvedor = $_pkDetalleFacturaProvedor;
    }

    public function set_cantidad($_cantidad) {
        $this->_cantidad = $_cantidad;
    }

    public function set_precioUnitario($_precioUnitario) {
        $this->_precioUnitario = $_precioUnitario;
    }

    public function set_subTotal($_subTotal) {
        $this->_subTotal = $_subTotal;
    }

    public function set_cantidadTotal($_cantidadTotal) {
        $this->_cantidadTotal = $_cantidadTotal;
    }

    public function set_valorUnidad($_valorUnidad) {
        $this->_valorUnidad = $_valorUnidad;
    }

    public function set_pkProducto($_pkProducto) {
        $this->_pkProducto = $_pkProducto;
    }

    public function set_pkFactura($_pkFactura) {
        $this->_pkFactura = $_pkFactura;
    }

    /**
     * Agregar un detalle a una factura de compra
     * @return type Description
     */
    public function agregarDetalleFacturaProvedor() {
        $db = new SuperDataBase();

        $sucursal = UserLogin::get_pkSucursal();
        $query = "CALL sp_add_detalle_factura_provedor($this->_cantidad,$this->_precioUnitario,$this->_subTotal,$this->_cantidadTotal,$this->_valorUnidad,'$this->_pkProducto','$this->_pkFactura','$sucursal',@sa)";
        echo $query;
        $db->executeQuery($query);
        $query2 = "select @sa";
        $result = $db->executeQuery($query2);
        $message = "";
        while ($row = $db->fecth_array($result)) {
            $message = $row['@sa'];
        }
        return $message;
    }

    /**
     * Cambia el estado de un pedidoe
     */
    public function updateEstados($pkDetallePedido, $estado) {
        $db = new SuperDataBase();
        $pkCocinero = UserLogin::get_idTrabajador();
        $query = "update detallepedido set estado=$estado, horaTermino=now(), pkCocinero='$pkCocinero' where pkDetallePedido=$pkDetallePedido";
        $db->executeQuery($query);
        echo $query;
    }

    public function ListdetallePedidos($codigo) {
        $db = new SuperDataBase();
        $query = "SELECT pkDetalleFacturaProvedor, cantidad, precioUnitario, subTotal, cantidadTotal, valorUnidad,   p.pkProducto, descripcion, d.pkFactura FROM detalle_factura_provedor d inner join productos p on p.pkProducto=d.pkProducto WHERE d.pkFactura='$codigo';";
        $array = array();
//        echo $query;
//        pkDetalleFacturaProvedor, cantidad, precioUnitario, subTotal, cantidadTotal, valorUnidad, pkProducto, descripcion, pkFactura, fecha, total, dateModify, nroFactura, pkSucursal, idUser, pkProvedor, estado
        $result = $db->executeQuery($query);
        while ($row = $db->fecth_array($result)) {
            $array[] = array("id" => $row['pkDetalleFacturaProvedor'], 
                 "cantidad" => utf8_encode($row['cantidad']),
                 "precioUnitario" => utf8_encode($row['precioUnitario']),
                 "subTotal" => utf8_encode($row['subTotal']),
                 "cantidadTotal" => utf8_encode($row['cantidadTotal']),
                 "valorUnidad" => utf8_encode($row['valorUnidad']),
                 "pkProducto" => utf8_encode($row['pkProducto']),
//                 "pkProducto" => utf8_encode($row['pkProducto']),
                 "descripcion" => utf8_encode($row['descripcion']),
                
                );
        }
          echo json_encode($array);
    }

}
