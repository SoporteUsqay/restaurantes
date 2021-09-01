<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Models_UserModel {

    private $_pkUser;
    private $_user;
    private $_password;
    private $_fkWorkPeople;
    private $_fkTypeUser;

    function __construct() {
        
    }

    public function get_user() {
        return $this->_user;
    }

    public function set_user($_user) {
        $this->_user = $_user;
    }

    public function get_password() {
        return $this->_password;
    }

    public function set_password($_password) {
        $this->_password = utf8_decode($_password);
    }

    public function get_fkWorkPeople() {
        return $this->_fkWorkPeople;
    }

    public function get_fkTypeUser() {
        return $this->_fkTypeUser;
    }

    public function set_fkWorkPeople($_fkWorkPeople) {
        $this->_fkWorkPeople = $_fkWorkPeople;
    }

    public function set_fkTypeUser($_fkTypeUser) {
        $this->_fkTypeUser = $_fkTypeUser;
    }

    public function get_pkUser() {
        return $this->_pkUser;
    }

    public function set_pkUser($_pkUser) {
        $this->_pkUser = $_pkUser;
    }

    //funcionalidad

    public function _cambiarClave($usuario, $clave, $repetirclave) {
        $db = new SuperDataBase();
        $query = "update user
    set password = '$clave'
  where userName ='$usuario';";
        $db->executeQuery($query);
        echo $query;
    }

    public function _validateUser() {
        $db = new SuperDataBase();
        $query = "CALL sp_user_validate('$this->_user',@sa)";
        $result = $db->executeQuery($query);
        $query2 = "select @sa";
        $result2 = $db->executeQuery($query2);
        while ($row = $db->fecth_array($result2)) {
//            $this->array['Sale'][] = array("Resultado" =>$row['@a']);
            $value = $row['@sa'];
        }

        return $value;
    }

    public function _validateUserPassword() {
        $db = new SuperDataBase();
        $query = "CALL sp_user_validate_password('$this->_user','$this->_password',@sa)";
        $result = $db->executeQuery($query);
        $query2 = "select @sa";
        $result2 = $db->executeQuery($query2);
        while ($row = $db->fecth_array($result2)) {

            $value = $row['@sa'];
        }


        return $value;
    }

    /**
     * @author Jeison Cruz Yesan
     * @access public
     * @copyright Ghosts Solutions 21014
     * @return bool true exists false no exits
     */
    public function _searchUserValidate() {
        $db = new SuperDataBase();
        $query = "CALL sp_searchUserValidate('$this->_user',@s);";
        $db->executeQuery($query);
        $query1 = "select @s";
        $result = $db->executeQuery($query1);
        while ($row = $db->fecth_array($result)) {

            $value = $row['@s'];
        }
        return $value;
    }

    /**
     * @author Jeison Cruz Yesan
     * @access public
     * @copyright Ghosts Solutions 21014
     * Save User
     */
    public function _saveUser() {


        $db = new SuperDataBase();
        $query = "CALL sp_addUser('$this->_user','$this->_password','$this->_fkTypeUser');";
        $result = $db->executeQuery($query);
//        echo $query;
////         $afectedRow= $db->affected_rows();
//         echo $db->affected_rows($query);
    }

    /**
     * @author Jeison Cruz Yesan
     * @access public
     * @copyright Ghosts Solutions 21014
     * Update User
     */
    public function _updateUser() {
        $db = new SuperDataBase();
        $query = "CALL sp_modify_user($this->_pkUser,$this->_fkTypeUser);";
        $result = $db->executeQuery($query);
    }

    /**
     * @author Jeison Cruz Yesan
     * @access public
     * @copyright Ghosts Solutions 21014
     * Delete User
     */
    public function _deleteUser() {
        $db = new SuperDataBase();
        $query = "CALL sp_delete_user($this->_pkUser);";
        $result = $db->executeQuery($query);
    }

    /**
     * @author Jeison Cruz Yesan (El putote)
     * @access public
     * @copyright Ghosts Solutions 21014 (Ghost como su tecnica de programacion)
     * List User
     */
    public function _listUser() {
        $sucursal = UserLogin::get_pkSucursal();
        $db = new SuperDataBase();

        $query = "CALL sp_get_user('$sucursal')";
        $resul = $db->executeQuery($query);
//        die($query);
        $array = array();
//        idUser, userName, password, pkTrabajador, 
//        pkTrabajador, documento, pkTipoTrabajador, 
//        pkSucursal, estado, documento, nombres, lastName, dateBirth, address, email, idUbicacion, SEXO
        while ($row = $db->fecth_array($resul)) {
            $array[] = array("lastName" => $row['lastName'],
                "names" => $row['nombres'],
                "user" => $row['userName'],
                "description" => $row['descripcion'],
                "id" => $row['idUser'],
                "pkTypeUser" => $row['pkTipoTrabajador'],);
        }
        echo json_encode($array);
    }

    public function _listNotUser() {
        $sucursal = UserLogin::get_pkSucursal();
        $db = new SuperDataBase();

        $query = "SELECT * FROM trabajador t inner join person p on p.documento= t.documento  where pkTrabajador not in(select pkTrabajador from user) and pkSucursal='$sucursal'";
        $resul = $db->executeQuery($query);
//        die($query);
        $array = array();
//        idUser, userName, password, pkTrabajador, 
//        pkTrabajador, documento, pkTipoTrabajador, 
//        pkSucursal, estado, documento, nombres, lastName, dateBirth, address, email, idUbicacion, SEXO
        while ($row = $db->fecth_array($resul)) {
            $array[] = array("lastName" => $row['lastName'],
                "names" => $row['nombres'],
                "trabajador" => utf8_encode($row['lastName'] . " " . $row['nombres']),
//                "description" => $row['descripcion'],
                "pkTrabajador" => $row['pkTrabajador'],
                "documento" => $row['documento'],
                "pkTypeUser" => $row['pkTipoTrabajador'],);
        }
        echo json_encode($array);
    }

    public function _login() {
        $this->_password = md5($this->_password);
        $db = new SuperDataBase();
        $query = "select user,password,wp.pkTrabajador,nombres,apellidos,documento,fechaSalida,
tu.pkTipoTrabajador, tu.descripcion as descriptionTypeUser,su.pkSucursal, su.nombreSucursal,su.pkEmpresa, e.nombreEmpresa,
su.direccion, su.telefono, su.ruc, su.pagweb, su.razon from trabajador wp
inner join  tipotrabajador tu on tu.pkTipoTrabajador=wp.pkTipoTrabajador inner join (sucursal su inner join empresa e on e.pkEmpresa=su.pkEmpresa) on wp.pkSucursal=su.pkSucursal
where wp.password = '$this->_password' and  wp.user like'%%' and wp.estado = 0";
// 
        $resul = $db->executeQuery($query);
        $array = array();
        while ($row = $db->fecth_array($resul)) {

            $array[] = array(
                "user" => $row['documento'],
                "id" => $row['pkTrabajador'],
                "pkTypeUser" => $row['pkTipoTrabajador'],
                "descriptionTypeUser" => $row[9],
                "pkWorkPeople" => $row['pkTrabajador'],
                "names" => $row['nombres'],
                "lastName" => $row['apellidos'],
                "email" => "",
                "document" => $row['documento'],
                "empresa" => utf8_encode($row['nombreEmpresa']),
                "pkEmpresa" => $row['pkEmpresa'],
                "nombreSucursal" => utf8_encode($row['nombreSucursal']),
                "pkSucursal" => $row['pkSucursal'],
                "fechaSalida" => $row['fechaSalida'],
                "direccion" => $row['direccion'],
                "telefono" => $row['telefono'],
                "ruc" => $row['ruc'],
                "pagweb" => $row['pagweb'],
                "razon" => $row['razon']
            );
        }
        return $array;
    }

    /**
     * Listado de los usuarios
     */
    public function listUserForType($param) {
        $db = new SuperDataBase();
        $query = "CALL sp_get_user_for_type($param)";
        $resul = $db->executeQuery($query);

        $array = array();
        while ($row = $db->fecth_array($resul)) {
            $array[] = array(
                "user" => $row[1],
                "id" => $row[0],
//                "pkTypeUser" => $row['pkTypeUser'],
//                "descriptionTypeUser" => $row['descriptionTypeUser'],
//                "pkWorkPeople" => $row['pkWorkPeople'],
//                "names" => $row['names'],
//                "lastName" => $row['lastNames'],
//                "email" => $row['email'],
//                "document" => $row['document']
            );
        }
        echo json_encode($array);
    }

    public function verificaContraseña($value) {
        $db = new SuperDataBase();
        $query = "SELECT password FROM user u where password='$value';";
        $result = $db->executeQuery($query);
//die($query);
        return count($db->fecth_array($result));
    }

    public function sacarIDtrabajador($value) {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "SELECT pkTrabajador FROM trabajador t where documento='$value' and pkSucursal='$sucursal';";
//        echo $query;
        $result = $db->executeQuery($query);
        $trabajador = "";
        while ($row = $db->fecth_array($result)) {
            $trabajador = $row[0];
        }
//        echo $trabajador;
        return $trabajador;
    }

}
