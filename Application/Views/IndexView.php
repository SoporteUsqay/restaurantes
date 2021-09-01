<?php

Class Application_Views_IndexView{
    
    public function __construct() {
    }
    
    public function showIndex(){
        include_once 'template/index.php';
    }

    public  function showLogin(){
        include_once 'User/login.php';
    }
    public function showContent(){
        include 'template/menuLeft.tpl.php';
    }

    public function showHome(){
        include_once 'template/index.php';
    }

    public function showTemplate(){    
        include_once 'template/index.php';
    }

    public function showFooter(){    
        include_once 'template/Footer.tpl.php';
    }
   
}
?>
