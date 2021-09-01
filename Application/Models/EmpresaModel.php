<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Models_EmpresaModel {

    public function _ListEmpresa() {

        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "select nombresucursal,direccion from sucursal where pksucursal='$sucursal';";
        $result = $db->executeQuery($query);
        $array = array();
        while ($row = $db->fecth_array($result)) {
            $array[] = array(
                "nombresucursal" => $row['nombresucursal'],
                "direccion" => $row['direccion'],
            );
        }
        echo json_encode($array);
    }

    public function updateEmpresa($razon, $nombre, $direccion, $ciudad, $telefono, $ruc, $correos) {
        $db = new SuperDataBase();
        $razon = utf8_decode($razon);
        $direccion = utf8_decode($direccion);
        $ciudad = utf8_decode($ciudad);
        //Actualizamos en tabla sucursal
        $query = "update sucursal set razon=upper('$razon'), nombreSucursal=upper('$nombre'), direccion=upper('$direccion'),telefono='$telefono', ruc='$ruc', ciudad='$ciudad' where pkSucursal='SU009';";
        $db->executeQuery($query);
        //Actualizamos en tabla empresa
        $query1 = "update empresa set nombreEmpresa=upper('$nombre') where pkEmpresa='PIU0007';";
        $db->executeQuery($query1);
        //Actualizamos Correos de Envio
        $query_2 = "SET SQL_SAFE_UPDATES = 0;";
        $db->executeQuery($query_2);

        $query_3 = "Delete from cloud_config where parametro = 'correos_notificaciones'";
        $db->executeQuery($query_3);

        $query_4 = "Insert into cloud_config values(NULL,'correos_notificaciones','".$correos."')";
        $db->executeQuery($query_4);
    }

}
