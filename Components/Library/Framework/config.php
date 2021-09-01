<?php

Class Class_config {

    public static function get($key) {
        global $config;

        if (!empty($key) and !empty($config[$key])) {
            return $config[$key];
        }
        else
            echo("Variable de Configuracion no Existe " . $key);
    }

    public static function set($key, $value) {
        global $config;
        if (empty($config[$key])) {
            $config[$key] = $value;
        }
      else
           $config[$key] = $value;
    }

}

/*
 * 
 */
//Class Class_config {
//
//    public static function get($key) {
//        global $config;
//        if (!empty($key) and !empty($config[$key])) {
//
//            return $config[$key];
//        }
//        else
//            die('Variable no de configuracion no existe');
//    }

//}