<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Factory{
    
    public static function bluildObject($subClass, $SuperClass){
        $ObjectSubClass=null;
        if (class_exists($subClass) and interface_exists($SuperClass)) {
            $ObjectSubClass = new $subClass(); 
//            die('$ObjectSubClass');
        }  
//        die($ObjectSubClass.'ola');
        return $ObjectSubClass;
    }
    
    public static function buildObjectClass($class){
        $objClass = null;
        if(class_exists($class)){
            $objClass = new $class();
        }
        return $objClass;
    }
}
?>
