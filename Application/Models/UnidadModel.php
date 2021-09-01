<?php

class Application_Models_UnidadModel {

    public function SaveUnidades($descripcion) {
        $db = new SuperDataBase();
        $query = "insert into unidad(descripcion,valor,unidadEquivalente,estado) values (UPPER('$descripcion'),0,0,0);";
        $db->executeQuery($query);
    }

    public function EditUnidades($id, $descripcion) {
        $db = new SuperDataBase();
        $query = "update unidad set descripcion='$descripcion' where pkUnidad=$id;";
        $db->executeQuery($query);
    }

    public function DeleteUnidades($id) {
        $db = new SuperDataBase();
        $query = "update unidad set estado=1 where pkUnidad=$id;";
        $db->executeQuery($query);
    }

    public function ActivarUnidad($id) {
        $db = new SuperDataBase();
        $query = "update unidad set estado=0 where pkUnidad=$id;";
        $db->executeQuery($query);
    }

    public function _listinsumo() {
        $db = new SuperDataBase();
        $query = "select * from unidad where estado=0";
        $result = $db->executeQuery($query);
        while ($row = $db->fecth_array($result)) {
            $array[] = array("id" => $row['pkUnidad'],
                "descripcion" => utf8_encode($row['descripcion'])
            );
        }
        echo json_encode($array);
    }

}
