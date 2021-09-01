<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Models_ClienteModel {

    private $_pkCliente;
    private $_ruc;
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

    public function get_ruc() {
        return $this->_ruc;
    }

    public function set_ruc($_ruc) {
        $this->_ruc = $_ruc;
    }

    public function get_pkCliente() {
        return $this->_pkCliente;
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

    public function set_pkCliente($_pkCliente) {
        $this->_pkCliente = $_pkCliente;
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

    public function _listAllCliente() {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "SELECT * FROM cliente c inner join person p on p.documento=t.documento";
        $result = $db->executeQuery($query);
        $array = array();
        while ($row = $db->fecth_array($result)) {

            $array[] = array(
                "pkCliente" => $row['pkCliente'],
                'names' => $row['nombres'],
                "lastNames" => $row['lastName'],
                "document" => $row['documento'],
                'label' => $row['lastName'] . " " . $row['nombres'],
                'value' => $row['pkCliente'],
                'email' => $row['email'],
                'sexo' => $row['SEXO'],
                'date' => $row['dateBirth'],
                'descriptionArea' => $row['descripcion'],
                'ruc' => $row['ruc']
            );
        }

        echo json_encode($array);
    }

    public function _save() {

        $db = new SuperDataBase();
        $sp = "CALL sp_add_cliente('$this->_names','$this->_surname','$this->_document','$this->_email',$this->_fkProfesion,"
                . "                    $this->_fkStatusCivil,$this->_fkSexo,$this->_fkUbigeo,'$this->_address','$this->_celPhone',"
                . "                     '$this->_telf',$this->_fkAreaTrabajo,$this->_fkWorkStation,$this->_fkTypeDocument,'$this->_dateBirth','$this->_ruc);";

        $result = $db->executeQuery($sp);
        echo $sp;
    }

    /**
     * Busca Si existe un dni
     * Function Validate Dni
     * @author Jeison Cruz Yesan <jcruzyesan@gmail.com>
     * @access public
     * @param string dni
     * @version 1.1
     * @copyright (c) 2014, Jeison Cruz (Marik)
     * @return bool 
     * * */
    public function _validationIdentification($docuemnt) {

        $db = new SuperDataBase();
        $sp = "SELECT count(document) FROM cliente w where document='$docuemnt';";

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
        $sp = "SELECT count(email) FROM cliente w where email='$email';";

        $result = $db->executeQuery($sp);
        while ($row = $db->fecth_array($result)) {
            $cantidad = $row[0];
        }
        echo json_encode($cantidad);
    }

    //Funciones para busqueda mejorada
    //Ahora buscamos desde SUNAT y RENIEC

    public function listCustomerXRUC($param) {
        error_reporting(E_ALL);
        $db = new SuperDataBase();
        $query = "SELECT * FROM `persona_juridica` WHERE `ruc` = {$param}";
        $result = $db->executeQuery($query);
        $array = array();
        if($row = $db->fecth_array($result)) {
            $array[] = array(
                "pk" => $row['ruc'],
                'document' => $row['ruc'],
                "companyName" => urldecode($row['razonSocial']),
                "email" => $row['email'],
                "address" => urldecode($row['address']),
            );
        }else{

            $ch = curl_init(); 
            curl_setopt($ch, CURLOPT_URL, "http://clientapi.sistemausqay.com/ruc.php?documento=".$param.""); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            $output = curl_exec($ch);
            $output_ = json_decode($output);
            
            if(curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) {                                   
                // echo json_encode([]);
            }else{
                $res=json_decode($output,TRUE);

                $array[] = array(
                    "pk" => $res["ruc"],
                    "document" => $res["ruc"],
                    "companyName" => $res["razon_social"],
                    "address" => $res["direccion"],
                    "email" => '',
                );
            }             
            curl_close($ch);

        }

        echo json_encode($array);
    }

    public function listCustomerXDNI($param) {
        error_reporting(E_ALL);
        $db = new SuperDataBase();
        $query = "Select * from person where documento = '".$param."'";
        $result = $db->executeQuery($query);
        $array = array();
        if($row = $db->fecth_array($result)) {
            $array[] = array(
                "pk" => $row['documento'],
                'nombres' => urldecode($row['nombres']),
                "direccion" => urldecode($row['address']),
                "email" => $row['email'],
            );
        }else{
            $ch = curl_init(); 
            curl_setopt($ch, CURLOPT_URL, "http://clientapi.sistemausqay.com/dni.php?documento=".$param.""); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            $output = curl_exec($ch);
            $output_ = json_decode($output);
            
            if(curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) {                                   
                // echo json_encode([]);
            }else{
                $res=json_decode($output,TRUE);

                $array[] = array(
                    "pk" => $res["dni"],
                    'nombres' => $res["nombres"]." ".$res["apellidos"],
                    "direccion" => "-",
                    "email" => '',
                );
            }             
            curl_close($ch);
        }

        echo json_encode($array);
    }

    public function listCustomerXDNI2($param) {
        $db = new SuperDataBase();
        $query = "select * from person where documento = '".$param."'";
        $result = $db->executeQuery($query);
        $array = array();
        while ($row = $db->fecth_array($result)) {
            $array[] = array(
               "pk" => $row['documento'],
               "nombres" => urldecode($row['nombres']),
               "address" => urldecode($row['address']),
               "email" => $row['email'],
            );
        }

        return json_encode($array);
    }

    //Funciones para clientes amigables
    //Febrero 2018
    //Gino Lluen
    
    public function getClientByPhone($phone) {
        $db = new SuperDataBase();
        $query = "select * from cliente_externo where telefono = '".$phone."' order by id DESC LIMIT 1";
        $result = $db->executeQuery($query);
        $res = null;
        while ($row = $db->fecth_array($result)) {
            $res = array(
                "id" => $row['id'],
                "documento" => $row['documento'],
                "nombres" => $row['nombres_y_apellidos'],
                "telefono" => $row['telefono'],
                "direccion" => $row['direccion'],
            );
        }
        echo json_encode($res);
    }
    
    public function updateAsignCliente($phone,$name,$dir,$doc,$pedido) {
        $db = new SuperDataBase();
        $query_cliente = "Insert into cliente_externo values(NULL,'".$doc."','".$name."','".$phone."','".$dir."','".$pedido."')";
        $db->executeQuery($query_cliente);
    }
    
    public function updateClienteSalon($name,$pedido) {
        $db = new SuperDataBase();
        $query = "Insert into pedido_cliente values (NULL,'".$pedido."','".$name."')";
        $db->executeQuery($query);
    }
    
    public function getClientePedido($pedido) {
        $db = new SuperDataBase();
        $query01 = "Select * from cliente_externo where id_pedido = '".$pedido."'";
        $query02 = "Select * from pedido_cliente where pkPediido = '".$pedido."'";
        $r1 = $db->executeQuery($query01);
        $r2 = $db->executeQuery($query02);
        $res = null;
        if($row01 = $db->fecth_array($r1)){
            $res = array(
                "id" => $row01['id'],
                "documento" => $row01['documento'],
                "nombres" => $row01['nombres_y_apellidos'],
                "telefono" => $row01['telefono'],
                "direccion" => $row01['direccion'],
                "tipop" => 2
            );
        }else{
           if($row02 = $db->fecth_array($r2)){
                $res = array(
                    "id" => $row02['id'],
                    "cliente" => $row02['nombre_cliente'],
                    "tipop" => 1
                );
            } 
        }
        echo json_encode($res);
    }
    
    function updateClientePedido($pedido,$tipo,$nombre,$telefono,$documento,$direccion){
        $query =  "";
        if(intval($tipo) === 1){
            $query = "Update pedido_cliente set nombre_cliente = '".$nombre."' where pkPediido = '".$pedido."'";
        }else{
            $query = "Update cliente_externo set nombres_y_apellidos = '".$nombre."', telefono = '".$telefono."', direccion = '".$direccion."', documento  = '".$documento."' where id_pedido = '".$pedido."'";
        }
        $db = new SuperDataBase();
        $db->executeQuery($query);
        echo json_encode(1);
    }
    
    /**
     * Function que Ingresa a un cliente juridico - natural

     * @access public
     * @author Jeison Cruz Yesan <jcruzyesan@gmail.com>
     * @copyright (c) 2014, Ghosts Soluciones
     */
    public function AddCliente($documento, $valor1, $valor2, $tipo) {
        $db = new SuperDataBase();
        if ($tipo == "1") {
            $query = "CALL sp_inserta_clientejuridico('$documento','$valor1','$valor2')";
        } else {
            $query = "CALL sp_inserta_cliente('$documento','$valor1','$valor2')";
        }

        $db->executeQuery($query);
    }

}
