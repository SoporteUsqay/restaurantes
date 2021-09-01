<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Class SuperDataBase {

    private static $_conection;

    public function __construct() {
        if (!isset(self::$_conection)) {
            self::$_conection = Factory::bluildObject('db_' . Class_config::get('DataBase'), 'InterfaceDataBase');
//             die( 'LLEGO 1');
            self::$_conection->getConnection();
//            die( 'LLEGO');
        }
        return self:: $_conection;
        // die('Mato database');
    }

//
    public function fecth_array($result) {
        return self::$_conection->fetch_array($result);
    }

//
    public function affected_rows() {
        self::$_conection->affected_rows();
        /* afecta a la escritura */
    }

    public function field_count() {
        //devuelve el numero de campos devueltos por la consulta
        self::$_conection->affected_count();
    }

    /* ejecuta una consulta sql */

    public function executeQuery($query) {
        return self::$_conection->executeQuery($query);
    }

    public function executeQueryEx($query) {

        $res = self::$_conection->executeQuery($query);

        if ($res === false) {
            $e = new Exception('Error al ejecutar la consulta: ' . $query);
            echo $e;
            throw $e;
        }

        return $res;
    }

    public function close_connection() {

        return self:: $_conection->Close();
    }
    public function getId(){
        return self::$_conection->getId();
    }
}

?>
