<?php
error_reporting(E_ALL);
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Models_TiposModel {

    private $_descripcion;
    private $_idCategoria;
    private $_NombreCategoria;
    private $_idTipo;
    private $_Limit;

    function __construct() {

    }
    
    public function set_limit($_in){
        $this->_Limit = $_in; 
    }

    public function get_descripcion() {
        return $this->_descripcion;
    }

    public function get_idCategoria() {
        return $this->_idCategoria;
    }

    public function get_idTipo() {
        return $this->_idTipo;
    }

    public function set_descripcion($_descripcion) {
        $this->_descripcion =  utf8_decode( $_descripcion);
    }

    public function set_idCategoria($_idCategoria) {
        $this->_idCategoria = $_idCategoria;
    }

    public function set_idTipo($_idTipo) {
        $this->_idTipo = $_idTipo;
    }

    public function get_NombreCategoria() {
        return $this->_NombreCategoria;
    }

    public function set_NombreCategoria($_NombreCategoria) {
        $this->_NombreCategoria = $_NombreCategoria;
    }

    public function RegistrarCategoria($_descripcioncategoria) {

        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();

        $query = "CALL sp_registrarCategoria('$_descripcioncategoria','$sucursal')";

        $db->executeQuery($query);
        echo $query;
    }

    public function RegistrarTipo($_descripcionTipo, $_Idcategoria) {

        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();

        $query = "CALL sp_registrarTipos('$_descripcionTipo',$_Idcategoria)";

        $db->executeQuery($query);
        echo $query;
    }

     public function deleteCategoria($pkcategoria) {

        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();

        //$id = $user->get_idTrabajador();
        $query = "delete from categoria where pkCategoria = $pkcategoria and pkSucursal='$sucursal'";
        $db->executeQuery($query);
        echo $query;
    }

    public function ActualizarTiposCategoria() {

        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "update tipos set descripcion=upper('$this->_descripcion'), pkCategoria=$this->_idCategoria where pkTipo=$this->_idTipo;";

        $db->executeQuery($query);
//
    }

    public function ActualizarCategoria() {

        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "CALL sp_UpdateCategoria($this->_idCategoria,'$this->_NombreCategoria','$sucursal')";

        $db->executeQuery($query);
//
    }

    public function _listaTiposCategoria() {
        $db = new SuperDataBase();
        $query = "SELECT pkTipo,t.descripcion as tipo,t.pkCategoria,c.descripcion as categoria FROM tipos t inner join categoria c on t.pkCategoria=c.pkCategoria where pkTipo > 0;";
        $resul = $db->executeQuery($query);

//        $array = array();
        while ($row = $db->fecth_array($resul)) {
            $array[] = array(
                "idTipo" => $row['pkTipo'],
                "Tipo" => utf8_encode($row['tipo']),
                "pkCategoria" => utf8_encode($row['pkCategoria']),
                "nom_categoria" => utf8_encode($row['categoria'])
            );
        }
        echo json_encode($array);
    }

    public function _listaCategoria() {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "select * from categoria where pkSucursal='$sucursal' order by 1";

        $resul = $db->executeQuery($query);

        $array = array();
        while ($row = $db->fecth_array($resul)) {
            $array[] = array(
                "idcategoria" => $row['pkCategoria'],
                "categoria" => utf8_encode($row['descripcion'])
            );
        }
        echo json_encode($array);
    }

   public function _listTiposForCategoria() {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "SELECT t.* FROM tipos t, accion_caja ac where t.estado=0 AND t.pkTipo > 0 AND ac.pk_accion = t.pkTipo AND ac.tipo_accion = 'TYP' AND ac.caja = '".$_COOKIE['c']."' ORDER BY t.descripcion";
        $resul = $db->executeQuery($query);

        $array = array();
        while ($row = $db->fecth_array($resul)) {
            $ruta_imagen = "";
            $array[] = array("pkTipoPlato" => $row[0],
                "descripcion" => strtoupper($row[1]),
                "imagen" => $ruta_imagen
            );
        }
        echo json_encode($array);
    }
    
    public function _listTiposForCategoriaPage() {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $ini = $this->_Limit;
        $fin = $ini+10; 
        $query = "SELECT * FROM tipos t where estado = 0 AND pkTipo > 0 LIMIT $ini,$fin;";
        $resul = $db->executeQuery($query);

        $array = array();
//        , , pkCategoria
        while ($row = $db->fecth_array($resul)) {
            $ruta_imagen = "";
            foreach (glob("Public/uploads/tipos/".$row[0].".*") as $filename) {
                $ruta_imagen = $filename;
            }
            $array[] = array("pkTipoPlato" => $row[0],
                "descripcion" => strtoupper($row[1]),
                "imagen" => $ruta_imagen
            );
        }
        echo json_encode($array);
    }
    
    public function _listCategoriaForProductos($pkTipoProducto) {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "CALL sp_listaCategoria_Producto($pkTipoProducto,'$sucursal');";
//        die($query);
        $resul = $db->executeQuery($query);

        $array = array();
//        , , pkCategoria
        while ($row = $db->fecth_array($resul)) {
            $array[] = array(
                "pkcategoria" => $row['pkCategoria'],
                "descripcion" => utf8_encode($row['descripcion'])
            );
        }
        echo json_encode($array);
    }

    public function __ListCategoriaSucursal() {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();

        $query = "select pkCategoria,descripcion from categoria where pkSucursal='$sucursal'";
        $resul = $db->executeQuery($query);

        while ($row = $db->fecth_array($resul)) {
            $array[] = array(
                "id" => $row['pkCategoria'],
                "description" => utf8_encode($row['descripcion'])
            );
        }
        echo json_encode($array);
    }

    public function _listCategoria() {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "select pkCategoria,descripcion from categoria where pkSucursal='$sucursal'";
        $resul = $db->executeQuery($query);

        while ($row = $db->fecth_array($resul)) {
            $array[] = array(
                "id" => $row['pkCategoria'],
                "description" => utf8_encode($row['descripcion'])
            );
        }
        echo json_encode($array);
    }

    public function _listCategoriaProductos() {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "SELECT  distinct(c.pkCategoria),c.descripcion  FROM producto_sucursal p inner join (tipos t inner join categoria c on c.pkCategoria=t.pkCategoria) on t.pkTipo=p.pkTipo where p.pkSucursal='$sucursal' group by t.pkTipo";

        $resul = $db->executeQuery($query);

        $array = array();
        while ($row = $db->fecth_array($resul)) {
            $array[] = array(
                "id" => $row['pkCategoria'],
                "description" => utf8_encode($row['descripcion'])
            );
        }
        echo json_encode($array);
    }


    public function _listTipos() {
        $db = new SuperDataBase();

        $query = "SELECT pkTipo,t.imagen,t.descripcion FROM tipos t inner join categoria c on c.pkCategoria=t.pkCategoria where c.descripcion='productos' AND t.pkTipo > 0;";
        $resul = $db->executeQuery($query);

        $array = array();
        while ($row = $db->fecth_array($resul)) {
            $array[] = array(
                "id" => $row['pkTipo'],
                "description" => utf8_encode($row['descripcion'])
            );
        }
        echo json_encode($array);
    }

    public function saveTipo($descripcion, $sunat) {
        $db = new SuperDataBase();
        //Insertamos Tipo
        $query = "Insert into tipos values(NULL,'".$descripcion."','1',0,NULL)";
        $db->executeQuery($query);
        //Obtenemos ID
        $r1 = $db->executeQuery("select Max(pkTipo) as actid from tipos;");
        $idt =  $db->fecth_array($r1);
        $id = intval($idt["actid"]);
        //Guardamos el tipo sunat
        $query2 = "Insert into tipo_codigo_sunat values(NULL,'".$id."','".$sunat."')";
        //echo $query2;
        $db->executeQuery($query2);
        //Asignamos a caja actual
        $query3 = "Insert into accion_caja values(NULL,'".$id."','TYP','".$_COOKIE['c']."')";
        $db->executeQuery($query3);
        echo "1";
    }

    public function updateTipo($id,$descripcion,$categoria) {
        $db = new SuperDataBase();
        $descripcion= utf8_decode($descripcion);
        $query =    "update tipos
                    set descripcion = upper('$descripcion'),
                        pkCategoria = $categoria
                    where pkTipo=$id;";
        $db->executeQuery($query);
        
        echo $query;
       // return $mensaje;
    }

    public function deleteTipo($id) {
        $db = new SuperDataBase();
        $query =    "update tipos
                    set estado=1
                    where pkTipo=$id";
        $db->executeQuery($query);
        echo $query;
    }
    public function activeTipo($id) {
        $db = new SuperDataBase();
        $query =    "update tipos
                    set estado=0
                    where pkTipo=$id";
        $db->executeQuery($query);
        echo $query;
    }

}
