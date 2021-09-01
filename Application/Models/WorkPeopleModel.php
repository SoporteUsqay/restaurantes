<?php
error_reporting(E_ALL);
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Models_WorkPeopleModel {

    private $_pkWorkPeople;
    private $_names;
    private $_surname;
    private $_document;
    private $_email;
    private $_fkProfesion;
    private $_fkStatusCivil;
    private $_fkSexo;
    private $_fkUbigeo;
    private $_address;
    private $_telf;
    private $_celPhone;
    private $_fkAreaTrabajo;
    private $_fkWorkStation;
    private $_fkTypeDocument;
    private $_dateBirth;
    private $_array = array();

    function __construct() {
        
    }

    public function get_pkWorkPeople() {
        return $this->_pkWorkPeople;
    }

    public function get_names() {
        return $this->_names;
    }

    public function get_surname() {
        return $this->_surname;
    }

    public function get_document() {
        return $this->_document;
    }

    public function get_email() {
        return $this->_email;
    }

    public function get_fkProfesion() {
        return $this->_fkProfesion;
    }

    public function get_fkStatusCivil() {
        return $this->_fkStatusCivil;
    }

    public function get_fkSexo() {
        return $this->_fkSexo;
    }

    public function get_fkUbigeo() {
        return $this->_fkUbigeo;
    }

    public function get_address() {
        return $this->_address;
    }

    public function get_telf() {
        return $this->_telf;
    }

    public function get_celPhone() {
        return $this->_celPhone;
    }

    public function get_fkAreaTrabajo() {
        return $this->_fkAreaTrabajo;
    }

    public function get_fkWorkStation() {
        return $this->_fkWorkStation;
    }

    public function get_fkTypeDocument() {
        return $this->_fkTypeDocument;
    }

    public function get_dateBirth() {
        return $this->_dateBirth;
    }

    public function get_array() {
        return $this->_array;
    }

    public function set_pkWorkPeople($_pkWorkPeople) {
        $this->_pkWorkPeople = $_pkWorkPeople;
    }

    public function set_names($_names) {
        $this->_names = $_names;
    }

    public function set_surname($_surname) {
        $this->_surname = $_surname;
    }

    public function set_document($_document) {
        $this->_document = $_document;
    }

    public function set_email($_email) {
        $this->_email = $_email;
    }

    public function set_fkProfesion($_fkProfesion) {
        $this->_fkProfesion = $_fkProfesion;
    }

    public function set_fkStatusCivil($_fkStatusCivil) {
        $this->_fkStatusCivil = $_fkStatusCivil;
    }

    public function set_fkSexo($_fkSexo) {
        $this->_fkSexo = $_fkSexo;
    }

    public function set_fkUbigeo($_fkUbigeo) {
        $this->_fkUbigeo = $_fkUbigeo;
    }

    public function set_address($_address) {
        $this->_address = $_address;
    }

    public function set_telf($_telf) {
        $this->_telf = $_telf;
    }

    public function set_celPhone($_celPhone) {
        $this->_celPhone = $_celPhone;
    }

    public function set_fkAreaTrabajo($_fkAreaTrabajo) {
        $this->_fkAreaTrabajo = $_fkAreaTrabajo;
    }

    public function set_fkWorkStation($_fkWorkStation) {
        $this->_fkWorkStation = $_fkWorkStation;
    }

    public function set_fkTypeDocument($_fkTypeDocument) {
        $this->_fkTypeDocument = $_fkTypeDocument;
    }

    public function set_dateBirth($_dateBirth) {
        $this->_dateBirth = $_dateBirth;
    }

    public function set_array($_array) {
        $this->_array = $_array;
    }

    public function _listAllWorkPeople() {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "SELECT * FROM trabajador t inner join person p on p.documento=t.documento inner join tipotrabajador tt on tt.pkTipoTrabajador=t.pkTipoTrabajador where pkSucursal='$sucursal' and estado=0;";
        $result = $db->executeQuery($query);
        $array = array();
        while ($row = $db->fecth_array($result)) {

            $array[] = array(
                "pkWorkPeople" => $row['pkTrabajador'],
                'names' => $row['nombres'],
                "lastNames" => $row['lastName'],
                "document" => $row['documento'],
                'label' => $row['lastName'] . " " . $row['nombres'],
                'value' => $row['pkTrabajador'],
                'email' => $row['email'],
                'sexo' => $row['SEXO'],
                'date' => $row['dateBirth'],
                'descriptionArea' => $row['descripcion'],
                'pkArea' => $row['pkTipoTrabajador']
            );
        }

        echo json_encode($array);
    }

    public function _listAllWorkPeople2() {
        $db = new SuperDataBase();
        $query = "CALL sp_get_WorkPeopleNotUser()";
        $result = $db->executeQuery($query);
        $array = array();
        while ($row = $db->fecth_array($result)) {

            $array['Demo'][] = array(
                "pkWorkPeople" => $row['pkWorkPeople'],
                'names' => $row['names'],
                "lastNames" => $row['lastNames'],
                "document" => $row['document'],
                'label' => $row['lastNames'] . " " . $row['names'],
                'value' => $row['pkWorkPeople']
            );
        }

        return $array;
    }

    /**
     * Guarda un nuevo trabajador -
     * Function Save Work People
     * @author Jeison Cruz Yesan <jcruzyesan@gmail.com>
     * @access public
     * @param object WorkPeople
     * @version 1.1
     * @copyright (c) 2014, Jeison Cruz
     * * */
    public function _save($dni,$nombre,$apellido,$direccion,$password,$tipo) {

        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();

        // $password = password_hash($password, PASSWORD_BCRYPT);
        $password = md5($password);

        $sp = "insert into trabajador (nombres,apellidos,documento,direccion, pkTipoTrabajador, pkSucursal, estado, user, password,fechaIngreso) values(upper('$nombre'),upper('$apellido'),'$dni','$direccion',"
                . "$tipo,'$sucursal',0,'$dni','$password',now());";

        echo $sp;

        $db->executeQuery($sp);
    }

    /**
     * Busca Si existe un dni
     * Function Validate Dni
     * @author Jeison Cruz Yesan <jcruzyesan@gmail.com>
     * @access public
     * @param string dni
     * @version 1.1
     * @copyright (c) 2014, Jeison Cruz
     * @return bool 
     * * */
    public function _validationIdentification($docuemnt) {

        $db = new SuperDataBase();
        $sp = "SELECT count(document) FROM workpeople w where document='$docuemnt';";

        $result = $db->executeQuery($sp);
        while ($row = $db->fecth_array($result)) {
            $cantidad = $row[0];
        }
        echo json_encode($cantidad);
    }

    /**
     * Busca Si existe un emai
     * Function Validate email
     * @author Jeison Cruz Yesan <jcruzyesan@gmail.com>
     * @access public
     * @param string dni
     * @version 1.1
     * @copyright (c) 2014, Jeison Cruz
     * @return bool 
     * * */
    public function _validationEmail($email) {

        $db = new SuperDataBase();
        $sp = "SELECT count(email) FROM workpeople w where email='$email';";

        $result = $db->executeQuery($sp);
        while ($row = $db->fecth_array($result)) {
            $cantidad = $row[0];
        }
        echo json_encode($cantidad);
    }

    /**
     * Elimina untrabajador -
     * Function drop Work People
     * @author Jeison Cruz Yesan <jcruzyesan@gmail.com>
     * @access public
     * @param object WorkPeople
     * @version 1.1
     * @copyright (c) 2014, Jeison Cruz
     * * */
    public function _drop() {

        $db = new SuperDataBase();
        $sp = "CALL sp_delele_WorkPeople('$this->_pkWorkPeople');";

        $result = $db->executeQuery($sp);
//        echo $sp;
        echo json_encode("true");
    }

     public function deleteWorkPeople($id) {
        $db = new SuperDataBase();
        $query =    "update trabajador 
                    set estado=1
                    where pkTrabajador=".$id;
        $db->executeQuery($query);
    }
    
    /**
     * Modifica untrabajador -
     * Function update Work People
     * @author Jeison Cruz Yesan <jcruzyesan@gmail.com>
     * @access public
     * @param object WorkPeople
     * @version 1.1
     * @copyright (c) 2014, Jeison Cruz
     * * */
    public function _update($id,$dni,$nombre,$apellido,$direccion,$clave,$tipo) {
        $db = new SuperDataBase();
        $claveMD5 = md5($clave);

        $_pass = "";
        if ($clave) {
            $_pass = "password='$claveMD5',";
        }

        $sp = "update trabajador set documento='$dni', nombres=upper('$nombre'), apellidos=upper('$apellido'), direccion='$direccion', 
            $_pass pkTipoTrabajador='$tipo' 
            where pkTrabajador='$id'";

        $result = $db->executeQuery($sp);
//        echo $sp;
        echo $db->getId();
    }

    public function _verficaPersona($documento, $nombres, $direccion, $email) {
        $dr = 0;
        $db = new SuperDataBase();
        
        $nombres = urldecode($nombres);
        $direccion = urldecode($direccion);
        $email = urldecode($email);

        if($documento == "-"){
            $q1 = "Select * from cliente_generico where nombre = '".strtoupper($nombres)."'";
            $r1 = $db->executeQuery($q1);
            if($rw1 = $db->fecth_array($r1)){
                $q2 = "Update cliente_generico set nombres = '".strtoupper($nombres)."', direccion = '".$direccion."' where id = '".$rw1["id"]."'";
                $db->executeQuery($q2);
                $dr = $rw1["id"];
            }else{
                $qc = "select count(*) as actual from cliente_generico";
                $rc = $db->executeQuery($qc);
                if($rwc = $db->fecth_array($rc)){
                    $correlativo = (intval($rwc["actual"])*-1)-1;
                    $q2 = "Insert into cliente_generico values('".$correlativo."','".strtoupper($nombres)."','".$direccion."')";
                    $db->executeQuery($q2);
                    $dr = $correlativo;
                }
            }
        }else{
            $dr = $documento;
            $q1 = "Select * from person where documento = '".$documento."'";
            $r1 = $db->executeQuery($q1);
            
            if($rw1 = $db->fecth_array($r1)){
                $q2 = "Update person set nombres = '".$nombres."', address = '".$direccion."', email = '".$email."' where documento = '".$documento."'";
                $db->executeQuery($q2);
            }else{
                $q2 = "Insert into person values('".$documento."','".$nombres."',NULL,NULL,'".$direccion."','".$email."',0,NULL)";
                $db->executeQuery($q2);
            }
        }
        return $dr;
    }

    public function _verficaPersonaJuridica($documento, $razon_social, $direccion, $email) {
        $db = new SuperDataBase();
        $documento_c = utf8_decode($documento);
        $razon_c = urldecode($razon_social);
        //echo $razon_c;
        $direccion_c = utf8_decode($direccion);
        $email_c = utf8_decode($email);

        $q1 = "Select * from persona_juridica where ruc = '".$documento_c."'";
        $r1 = $db->executeQuery($q1);
        
        if($rw1 = $db->fecth_array($r1)){
            $q2 = "Update persona_juridica set razonSocial = '".$razon_c."', address = '".$direccion_c."', email = '".$email_c."' where ruc = '".$documento_c."'";
            $db->executeQuery($q2);
        }else{
            $q2 = "Insert into persona_juridica values('".$documento_c."','".$razon_c."','".$direccion_c."',NULL,NULL,'".$email_c."')";
            $db->executeQuery($q2);
        }
    }

    public function listTrabajadores() {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "SELECT * FROM trabajador t inner join  person p on p.documento=t.documento and pkSucursal='$sucursal' and estado=0 and pkTipoTrabajador<5  order by pkSucursal ;";
        $result = $db->executeQuery($query);
//        die($query);
        $array = array();
        while ($row = $db->fecth_array($result)) {
//pkTrabajador, documento, pkTipoTrabajador, pkSucursal, estado, documento, nombres, lastName, dateBirth, address, email, idUbicacion, SEXO
            if ($row['lastName'] == null) {
                $row['lastName'] = "";
            }
            $array[] = array(
                "pkTrabajador" => $row['pkTrabajador'],
                'nombres' => $row['nombres'] . " " . $row['lastName'] . " " . $row['pkTipoTrabajador'],
//                "lastName" => $row['lastName']
            );
        }
        echo json_encode($array);
    }

    public function listTrabajadoresId($id) {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "SELECT * FROM trabajador t where  pkSucursal='$sucursal' and pkTrabajador='$id' ";
        $result = $db->executeQuery($query);
//        die($query);
        $array = array();
        while ($row = $db->fecth_array($result)) {

            $array[] = array(
                "pkTrabajador" => $row['pkTrabajador'],
                'nombres' => utf8_encode($row['nombres']),
                "apellidos" => utf8_encode($row['apellidos']),
                "pkTipoTrabajador" => $row['pkTipoTrabajador'],
                "contra" => $row['password'],
                "estado" => $row['estado'],
                "direccion" => $row['direccion'],
                "documento" => $row['documento'],
            );
        }
        echo json_encode($array);
    }
    
    public function activePersonal($id) {
        $db = new SuperDataBase();
        $query =    "update trabajador 
                    set estado=0
                    where pkTrabajador=".$id;
        $db->executeQuery($query);
        echo $query;
    }

}
