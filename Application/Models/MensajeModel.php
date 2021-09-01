<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Models_MensajeModel {

    private $_pkMensaje, $_descripcion;

    function __construct() {
        
    }

    public function get_pkMensaje() {
        return $this->_pkMensaje;
    }

    public function get_descripcion() {
        return $this->_descripcion;
    }

    public function set_pkMensaje($_pkMensaje) {
        $this->_pkMensaje = $_pkMensaje;
    }

    public function set_descripcion($_descripcion) {
        $this->_descripcion = $_descripcion;
    }

    public function registraMensaje() {

        $db = new SuperDataBase();
        $query_mensaje = "insert into mensaje values (NULL,'$this->_descripcion')";
        $db->executeQuery($query_mensaje);
        $res_id = $db->executeQuery("SELECT LAST_INSERT_ID() as id_mensaje");
        $id_mensaje = "";
        while($row0 = $db->fecth_array($res_id)){
            $id_mensaje = $row0["id_mensaje"];
        }
        $query_caja = "Insert into accion_caja values(NULL,'".$id_mensaje."','MSG','".$_COOKIE["c"]."')";
        $db->executeQuery($query_caja);
        
    }

    public function updateMensaje($id, $descripcion) {

        $db = new SuperDataBase();
        $descripcion = utf8_decode($descripcion);
        $query = "update mensaje set descripcion =upper('$descripcion') where pkMensaje=$id";
        $db->executeQuery($query);
        echo $query;
        // return $mensaje;
    }

    public function deleteMensaje($id) {

        $db = new SuperDataBase();
        $query = "delete from mensaje where pkMensaje=$id";
        $db->executeQuery($query);
        echo $query;
        // return $mensaje;
    }

    public function QuitaMensajeCaja($mensaje,$caja) {
        $db = new SuperDataBase();
        $db->executeQuery("SET SQL_SAFE_UPDATES = 0;");
        $db->executeQuery("Delete from accion_caja where pk_accion = '".$mensaje."' AND tipo_accion = 'MSG' AND caja = '".$caja."'");
        $db->executeQuery("SET SQL_SAFE_UPDATES = 1;");
        echo "<script>window.location.href='" . Class_config::get('urlApp') . "?controller=Config&action=ShowAdminMensajes'</script>";
    }
    
    public function PonMensajeCaja($mensaje,$caja) {
        $db = new SuperDataBase();
        $db->executeQuery("Insert into accion_caja values(NULL,'".$mensaje."','MSG','".$caja."')");
        echo "<script>window.location.href='" . Class_config::get('urlApp') . "?controller=Config&action=ShowAdminMensajes'</script>";
    }

}

