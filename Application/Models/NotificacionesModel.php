<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Models_NotificacionesModel {

    private $_id, $_mensaje, $_estado, $_tipo, $_fecha, $_pkTrabajador;

    function __construct() {
        
    }

    function get_id() {
        return $this->_id;
    }

    function get_mensaje() {
        return $this->_mensaje;
    }

    function get_estado() {
        return $this->_estado;
    }

    function get_tipo() {
        return $this->_tipo;
    }

    function get_fecha() {
        return $this->_fecha;
    }

    function get_pkTrabajador() {
        return $this->_pkTrabajador;
    }

    function set_id($_id) {
        $this->_id = $_id;
    }

    function set_mensaje($_mensaje) {
        $this->_mensaje = $_mensaje;
    }

    function set_estado($_estado) {
        $this->_estado = $_estado;
    }

    function set_tipo($_tipo) {
        $this->_tipo = $_tipo;
    }

    function set_fecha($_fecha) {
        $this->_fecha = $_fecha;
    }

    function set_pkTrabajador($_pkTrabajador) {
        $this->_pkTrabajador = $_pkTrabajador;
    }

    public function AgregaNotificacion() {
        $db = new SuperDataBase();
        $objCaja = new Application_Models_CajaModel();
        $fechaCierre = $objCaja->fechaCierre();
        $query = "SELECT pkTrabajador FROM trabajador t where pkTipoTrabajador<>4";
        $result = $db->executeQuery($query);
        while ($row = $db->fecth_array($result)) {
            $queryNot = "insert into notificaciones (mensaje, estado, tipo, fecha, pkTrabajador,fechaCierre)"
                    . " values (upper('$this->_mensaje'),0,$this->_tipo,now()," . $row['pkTrabajador'] . ",'$fechaCierre')";
            $db->executeQuery($queryNot);
            echo $db->getId();
        }
    }

    public function UpdateNotificacion() {
        $db = new SuperDataBase();
        $objCaja = new Application_Models_CajaModel();
        $fechaCierre = $objCaja->fechaCierre();
        $query = "update notificaciones set estado=1, fechaLectura= now() where estado=0 and pkTrabajador=" . UserLogin::get_idTrabajador();
//        echo $query;
        $db->executeQuery($query);
        return $db->getId();
    }

    public function Lista() {
        $db = new SuperDataBase();
        $objCaja = new Application_Models_CajaModel();
        $fechaCierre = $objCaja->fechaCierre();
        $query= "SELECT * FROM notificaciones n where pkTrabajador=" . UserLogin::get_idTrabajador();
//        echo $query;
        $array= array();
        $result =$db->executeQuery($query);
        while($row=$db->fecth_array($result)){
           $array[] = array(
                "id" => $row['id'],
                "mensaje" => utf8_encode($row['mensaje']),
                "estado" => (int) $row['estado'],
                "fecha" => $row['fecha'],
                "pkTrabajador" => $row['pkTrabajador'],
                "fechaLectura" => $row['fechaLectura'],
                "fechaCierre" => $row['fechaCierre'],
            );  
        }
        echo json_encode($array);
    }

}
