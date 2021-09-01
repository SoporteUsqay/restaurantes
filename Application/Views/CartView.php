<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Class Application_Views_CartView {

    public function __construct() {
        
    }

    public function showList() {
//        include 'template/header.php';
//        include 'template/body.tpl.php';

        include_once 'Cart/List.tpl.php';
//        include 'template/Footer.tpl.php';
    }

    public function showRegister() {
//        include 'template/header.php';
//        include 'template/body.tpl.php';

        include_once 'Carta/Register.tpl.php';
//        include 'template/Footer.tpl.php';
    }

    public function showModifyPermissions() {
//        include 'template/header.php';
//        include 'template/body.tpl.php';

        include_once 'Cart/ModifyPermissions.tpl.php';
//        include 'template/Footer.tpl.php';
    }
    public function showModifyCart(){
        include_once 'Cart/ModifyCart.tpl.php';
    }

}
