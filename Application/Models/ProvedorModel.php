<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Models_ProvedorModel {

    private $_pkProveedor, $_ruc, $_razon, $_direccion, $_telefono, $_pagweb, $_mail, $_estado;
    
    function __construct() {
        
    }
    
   public function get_pkProveedor() {
        return $this->_pkProveedor;
    }

    public function get_ruc() {
        return $this->_ruc;
    }

    public function get_razon() {
        return $this->_razon;
    }
    
    public function get_direccion() {
        return $this->_direccion;
    }
    
    public function get_telefono() {
        return $this->_telefono;
    }
    
    public function get_pagweb() {
        return $this->_pagweb;
    }
    
    public function get_mail() {
        return $this->_mail;
    }
    
    public function get_estado() {
        return $this->_estado;
    }

    public function set_pkProveedor($_pkProveedor) {
        $this->_pkProveedor = $_pkProveedor;
    }
    
    public function set_ruc($_ruc) {
        $this->_ruc = $_ruc;
    }
    
    public function set_razon($_razon) {
        $this->_razon = $_razon;
    }
    
    public function set_direccion($_direccion) {
        $this->_direccion = $_direccion;
    }

    public function set_telefono($_telefono) {
        $this->_telefono = $_telefono;
    }
    
    public function set_pagweb($_pagweb) {
        $this->_pagweb = $_pagweb;
    }
    
    public function set_mail($_mail) {
        $this->_mail = $_mail;
    }
    
    public function set_estado($_estado) {
        $this->_estado = $_estado;
    }    
    
    
    public function filtro_proveedor($valor) {
        $db = new SuperDataBase();
//        $query1 = "select HClinica, Dni,Nombres, Apellidos, Direccion,upper(Sexo) as Sexo,per.Telefono,per.FechaNacimiento,upper(EstadoCivil) as EstadoCivil, pa.estado from persona per inner join paciente pa on per.Dni=pa.DniPaciente   ";
        $query="SELECT * FROM provedor p where ruc='$valor';";
       
//die( $query);
        $result = $db->executeQuery($query);
        $array = array();
        while ($row = $db->fecth_array($result)) {
            $array[] = array("idproveedor" => $row['pkProvedor'],
                "razon" => $row['razon']
            );
        }
        if(empty($array)){
            $array[] = array("idproveedor" => "",
                "razon" => "No se encuentra el Proveedor");
        }
        echo json_encode($array);
    }
    
    
    
    public function _listProvedor() {
        $db = new SuperDataBase();
        $query = "SELECT * FROM provedor p inner join persona_juridica pj on pj.ruc=p.ruc;";
        $resul = $db->executeQuery($query);
//        pkProvedor, ruc, pkComprobante, ruc, razonSocial, address, webpage, nombreCorto
//        $array = array();
        while ($row = $db->fecth_array($resul)) {
            $array[] = array("id" => $row['pkProvedor'],
                "descripcion" =>  utf8_encode( $row['nombreCorto'])
            );
        }
        echo json_encode($array);
    }
    public function _list() {
        $db = new SuperDataBase();
        $query = "SELECT pkProvedor, ruc, upper(razon) as razon FROM provedor;";
        $resul = $db->executeQuery($query);
//        pkProvedor, ruc, pkComprobante, ruc, razonSocial, address, webpage, nombreCorto
//        $array = array();
        while ($row = $db->fecth_array($resul)) {
            $array[] = array("id" => $row['pkProvedor'],
                "descripcion" =>  utf8_encode($row['razon']),
                "ruc" => $row['ruc'],
            );
        }
        echo json_encode($array);
    }
    
    public function registrarProveedor() {
        $db = new SuperDataBase();
        $codigo="";
        $query = "SELECT concat('PR',Lpad(ifnull(Max(SUBSTR(pkProvedor,3)),0)+1,3,'0')) as codigo FROM provedor ;";
        $result=$db->executeQuery($query);
                while ($row = $db->fecth_array($result)) {
                $codigo= $row['codigo'];
                }        
        $query = "INSERT INTO provedor(pkProvedor, ruc, razon, direccion, telefono, pagweb, mail, estado)
                  VALUES ('$codigo','$this->_ruc',upper('$this->_razon'),upper('$this->_direccion'),'$this->_telefono','$this->_pagweb','$this->_mail', 0);";
        $db->executeQuery($query);
        echo $query;
    }
    
    public function updateProveedor() {
        $db = new SuperDataBase();
        $query =    "UPDATE provedor
                    set ruc = '$this->_ruc',
                        razon = upper('$this->_razon'),
                        direccion = upper('$this->_direccion'),
                        telefono = '$this->_telefono',
                        pagweb = '$this->_pagweb',
                        mail = '$this->_mail'
                    WHERE pkProvedor='$this->_pkProveedor';";
        $db->executeQuery($query);
        echo $query;
    }       
    
    public function deleteProveedor() {       
        $db = new SuperDataBase();
        $query =    "UPDATE provedor
                    set estado = 1
                    WHERE pkProvedor='$this->_pkProveedor';";
        $db->executeQuery($query);
        echo $query;
       // return $mensaje;
    }
    
    public function activeProveedor() {       
        $db = new SuperDataBase();
        $query =    "UPDATE provedor
                    set estado = 0
                    WHERE pkProvedor='$this->_pkProveedor';";
        $db->executeQuery($query);
        echo $query;
    }
    

}
