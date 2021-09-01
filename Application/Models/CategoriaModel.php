<?php

class Application_Models_CategoriaModel {

    private $_pkCategoria, $_descripcion;

    function __construct() {
        
    }

    public function get_pkCategoria() {
        return $this->_pkCategoria;
    }

    public function get_descripcion() {
        return $this->_descripcion;
    }

    public function set_pkCategoria($_pkCategoria) {
        $this->_pkCategoria = $_pkCategoria;
    }

    public function set_descripcion($_descripcion) {
        $this->_descripcion = $_descripcion;
    }

    public function registrarCategoria() {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "insert into categoria(descripcion, pkSucursal)
                  values ('$this->_descripcion','$sucursal')";
//        $query = "call sp_add_categoria('$this->_descripcion','$sucursal')";
//        echo $query;
        $db->executeQuery($query);
//        $query = "select @sa";
//        $result = $db->executeQuery($query);
//        $Categoria;
//        while ($row = $db->fecth_array($result)) {
//            $Categoria = $row['@sa'];
//        }
//        return $Categoria;
    }

    public function updateCategoria($id, $descripcion) {
        $db = new SuperDataBase();
        $descripcion = utf8_decode($descripcion);
        $query = "update Categoria set descripcion = upper('$descripcion') where pkCategoria = $id";
        $db->executeQuery($query);
        echo $query;
    }

    public function deleteCategoria($id) {
        $db = new SuperDataBase();
        $query = "delete from Categoria where pkCategoria = $id";
        $db->executeQuery($query);
        echo $query;
    }

    public function listCategoria() {
        $db = new SuperDataBase();
        $query = "SELECT pkCategoria, UPPER(descripcion) as descripcion FROM categoria WHERE pkSucursal = '" . UserLogin::get_pkSucursal() . "';";
        $result = $db->executeQuery($query);
        $array = array();
        while ($row = $db->fecth_array($result)) {
            $array[] = array(
                "id" => $row['pkCategoria'],
                "descripcion" => utf8_encode($row['descripcion']),
            );
        }
        echo json_encode($array);
    }

}
