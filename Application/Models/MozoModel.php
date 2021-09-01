<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Models_MozoModel {

    function __construct() {
        
    }

    /**
     * Listado de los mosos
     * @access public
     * @return json array
     */
    public function _listMozo($tipoTrabajador){
        $db= new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query="select p.documento, concat(nombres,' ',lastName) as nombres_apellidos from person p inner join trabajador t on p.documento=t.documento where pkSucursal='$sucursal' and pkTipoTrabajador=$tipoTrabajador;";
        $result = $db->executeQuery($query);
        
        $array = array();
        while ($row = $db->fecth_array($result)) {
            $array[] = array("id" => $row['documento'],
                "nombres" => utf8_encode($row['nombres_apellidos'])
//                "nombre" => $row['precio_venta']
            );
        }
        echo json_encode($array);
                
    }
    
    
    public function _listTipoTrabajador(){
        $db= new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query="SELECT t.pkTipoTrabajador,descripcion FROM trabajador t
                inner join tipotrabajador tt on t.pkTipoTrabajador=tt.pkTipoTrabajador where pkSucursal='$sucursal' group by 1;";      
  
        $result = $db->executeQuery($query);
        $array = array();
        while ($row = $db->fecth_array($result)) {
            $array[] = array("idTipoTrabajador" => $row['pkTipoTrabajador'],
                "descripcionTipoTrabajador" => utf8_encode($row['descripcion'])
//                
            );
        }
        echo json_encode($array);
                
    }
}