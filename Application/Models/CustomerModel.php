<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Models_CustomerModel {

    private $_pk;
    private $_fkTypeCustomer;
    private $_document;
    private $_nameShort;
    private $_companyName;
    private $_ubigeo;
    private $_address;
    private $_celPhone;
    private $_emai;
    private $_webSite;
    private $_typeUser;
    private $_dateBirth;

    function __construct() {
        
    }

    public function get_pk() {
        return $this->_pk;
    }

    public function get_fkTypeCustomer() {
        return $this->_fkTypeCustomer;
    }

    public function get_document() {
        return $this->_document;
    }

    public function get_nameShort() {
        return $this->_nameShort;
    }

    public function get_companyName() {
        return $this->_companyName;
    }

    public function set_pk($_pk) {
        $this->_pk = $_pk;
    }

    public function set_fkTypeCustomer($_fkTypeCustomer) {
        $this->_fkTypeCustomer = $_fkTypeCustomer;
    }

    public function set_document($_document) {
        $this->_document = $_document;
    }

    public function set_nameShort($_nameShort) {
        $this->_nameShort = $_nameShort;
    }

    public function set_companyName($_companyName) {
        $this->_companyName = $_companyName;
    }
    
    
    public function get_ubigeo() {
        return $this->_ubigeo;
    }

    public function get_address() {
        return $this->_address;
    }

    public function get_celPhone() {
        return $this->_celPhone;
    }

    public function get_emai() {
        return $this->_emai;
    }

    public function get_webSite() {
        return $this->_webSite;
    }

    public function get_typeUser() {
        return $this->_typeUser;
    }

    public function get_dateBirth() {
        return $this->_dateBirth;
    }

    public function set_ubigeo($_ubigeo) {
        $this->_ubigeo = $_ubigeo;
    }

    public function set_address($_address) {
        $this->_address = $_address;
    }

    public function set_celPhone($_celPhone) {
        $this->_celPhone = $_celPhone;
    }

    public function set_emai($_emai) {
        $this->_emai = $_emai;
    }

    public function set_webSite($_webSite) {
        $this->_webSite = $_webSite;
    }

    public function set_typeUser($_typeUser) {
        $this->_typeUser = $_typeUser;
    }

    public function set_dateBirth($_dateBirth) {
        $this->_dateBirth = $_dateBirth;
    }

        /* =========================================================================
      Metodos de la clase
      ====================================================================
     */

    /**
     * Function que lista los datos principales de un cliente
     * @return json {pk,name,nameShort, docuement}
     * @param int $param 1-> Clientes Naturales
     * $param 2->Clientes Juridicos
     * $param 3-> Todos los clientes
     * @access public
     * @author Jeison Cruz Yesan <jcruzyesan@gmail.com>
     * @copyright (c) 2014, Ghosts Soluciones
     */
    public function listCustomer($param) {
        $db = new SuperDataBase();
        $query = "CALL sp_get_customer($param)";
        $result = $db->executeQuery($query);
        $array = array();
        while ($row = $db->fecth_array($result)) {

            $array[] = array(
                "pk" => $row['pkCurstomer'],
                'document' => $row['document'],
                "companyName" => $row['companyName'],
                "nameShort" => $row['nameShort'],
                "label" => $row['nameShort'],
            );
        }

        echo json_encode($array);
    }

    /**
     * Function que lista los datos principales de todos los clientes
     * @return json {pk,name,nameShort, doment}
     * 
     * $param 3-> Todos los clientes
     * @access public
     * @author Jeison Cruz Yesan <jcruzyesan@gmail.com>
     * @copyright (c) 2014, Ghosts Soluciones
     */
    public function listCustomerAll() {
        $db = new SuperDataBase();
        $query = "CALL sp_get_customer_all()";
        $result = $db->executeQuery($query);
        $array = array();
        while ($row = $db->fecth_array($result)) {

            $array[] = array(
                "pk" => $row['pkCurstomer'],
                'document' => $row['document'],
                "companyName" => $row['companyName'],
                "label" => $row['companyName'],
            );
        }

        echo json_encode($array);
    }

    /**
     * Funcion para guardar un nuevo cliente
     * @access public
     * @package Customer Model
     * 
     */
    public function SaveCustomer() {
        $db= new SuperDataBase();
        
        $query= "call sp_add_customer('$this->_document','$this->_companyName','$this->_nameShort',$this->_ubigeo,'$this->_address','$this->_celPhone','$this->_emai','$this->_webSite',$this->_fkTypeCustomer,'$this->_dateBirth',@result);";
        
        $db->executeQuery($query);
        $sa= "select @result;";
        $result= $db->executeQuery($sa);
        while ($row = $db->fecth_array($result)) {
            $message= $row[0];
        }
        return $message;
    }

}
