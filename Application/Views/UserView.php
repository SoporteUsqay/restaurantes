<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Class Application_Views_UserView {

    public function __construct() {
        
    }

    public function showList() {
//        include 'template/header.php';
//        include 'template/body.tpl.php';

        include_once 'Almacen/prueba.php';
//        include 'template/Footer.tpl.php';
    }

    public function showRegister() {
//        include 'template/header.php';
//        include 'template/body.tpl.php';

        include_once 'User/Register.tpl.php';
//        include 'template/Footer.tpl.php';
    }

    public function showModifyPermissions() {
//        include 'template/header.php';
//        include 'template/body.tpl.php';

        include_once 'User/ModifyPermissions.tpl.php';
//        include 'template/Footer.tpl.php';
    }
    public function showModifyUser(){
        include_once 'User/ModifyUser.tpl.php';
    }
//    public function showModifyUser(){
//        include_once 'User/ModifyUser.tpl.php';
//    }

    public function showCambiarPassword(){
        include_once 'User/CambiarPassword.php';
    }
}
