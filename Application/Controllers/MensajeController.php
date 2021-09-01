<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Application_Controllers_MensajeController {
    private static $session;
    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {

         
            case "SaveAction":
                $this->_Save();
                break;
            case "EditAction":
                $this->_Edit();
                break;
            case "DeleteAction":
                $this->_Delete();
                break;
            
        }
    }
    private function _Save(){
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_MensajeModel();
      
                $obj->set_descripcion($_REQUEST['descripcion']);
//                            
               $result= $obj->registraMensaje();
               if($result!=null){
                   echo "{success:true,data:'$result'}";
               }               
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
private function _Edit(){
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_MensajeModel();
                  $obj->updateMensaje($_POST['id'],$_REQUEST['descripcion'])    ;            
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
 private function _Delete(){
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_MensajeModel();
                  $obj->deleteMensaje($_POST['id'])    ;            
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
   
}
