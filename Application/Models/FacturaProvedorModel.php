<?php

/*
 * To change this license header, $_choose License Headers in Project Properties.
 * To change this template file, $_choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Models_FacturaProvedorModel {

    private $_pkFactura, $_fecha, $_pkProvedor, $_total, $_dateModify, $_pkUser, $_pkEmpresaPertenece, $_nroFactura, $_pkSucursal;

    function __construct() {
        
    }

    public function get_pkFactura() {
        return $this->_pkFactura;
    }

    public function get_fecha() {
        return $this->_fecha;
    }

    public function get_pkProvedor() {
        return $this->_pkProvedor;
    }

    public function get_total() {
        return $this->_total;
    }

    public function get_dateModify() {
        return $this->_dateModify;
    }

    public function get_pkUser() {
        return $this->_pkUser;
    }

    public function get_pkEmpresaPertenece() {
        return $this->_pkEmpresaPertenece;
    }

    public function get_nroFactura() {
        return $this->_nroFactura;
    }

    public function set_pkFactura($_pkFactura) {
        $this->_pkFactura = $_pkFactura;
    }

    public function set_fecha($_fecha) {
        $this->_fecha = $_fecha;
    }

    public function set_pkProvedor($_pkProvedor) {
        $this->_pkProvedor = $_pkProvedor;
    }

    public function set_total($_total) {
        $this->_total = $_total;
    }

    public function set_dateModify($_dateModify) {
        $this->_dateModify = $_dateModify;
    }

    public function set_pkUser($_pkUser) {
        $this->_pkUser = $_pkUser;
    }

    public function set_pkEmpresaPertenece($_pkEmpresaPertenece) {
        $this->_pkEmpresaPertenece = $_pkEmpresaPertenece;
    }

    public function set_nroFactura($_nroFactura) {
        $this->_nroFactura = $_nroFactura;
    }

    public function get_PkSucursal() {
        return $this->_pkSucursal;
    }

    public function set_PkSucursal($pkSucursal) {
        $this->_pkSucursal = $pkSucursal;
    }

    /**
     * Funcion de guardar una factura de proveedor por sucursal
     * @return String false cuando no se ha generado un codigo interno o el codigo generado
     */
    public function GuardarFacturaProvedor() {
        $db = new SuperDataBase();
        $user = UserLogin::get_id();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "CALL sp_add_factura_proveedor('$this->_fecha','$this->_pkProvedor',$user,'$sucursal','$this->_nroFactura',@sa)";
//        echo $query;
        $db->executeQuery($query);
        $query2 = "select @sa";
        $result = $db->executeQuery($query2);
        $message = "";
        while ($row = $db->fecth_array($result)) {
            $message = $row['@sa'];
        }
        return $message;
    }

    public function searchFacturaProvedor($nroFactura, $provedor, $fecha) {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "SELECT * FROM factura_provedor f inner join (provedor pro inner join persona_juridica pj on pj.ruc=pro.ruc) on pro.pkProvedor=f.pkProvedor where pkSucursal='$sucursal' ";
        if ($nroFactura != null) {
            $query.=" and nroFactura='$nroFactura' ";
        }
        if ($provedor != null) {
            $query.=" and f.pkProvedor='$provedor' ";
        }
        if ($fecha != null) {
            $query.=" and f.fecha='$fecha'";
        }
         $array =array();
//        echo $query;
        $result = $db->executeQuery($query);
        while ($row = $db->fecth_array($result)) {
//            pkFactura, fecha, total, dateModify, nroFactura, pkSucursal, idUser, pkProvedor, estado, pkProvedor, ruc, pkComprobante, ruc, razonSocial, address, webpage, nombreCorto
            $array[] = array("pkFactura" => $row['pkFactura'],
                "nroFactura" => utf8_encode($row['nroFactura']),
                "fecha" => utf8_encode($row['fecha']),
                "estado" => utf8_encode($row['estado']),
                "pkProvedor" => utf8_encode($row['pkProvedor']),
                "ruc" => utf8_encode($row['ruc']),
                "razonSocial" => utf8_encode($row['razonSocial']),
                "nombreCorto" => utf8_encode($row['nombreCorto']),
                "total" => utf8_encode($row['total']),
            );
        }
        echo json_encode($array);
    }

}
