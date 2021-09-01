<?php

class UserLogin {

    private static $_userName;
    private static $_id;
    private static $_names;
    private static $_lastnames;
    private static $_pkTypeUsernames;
    private static $_descriptionTypeUser;
    private static $_document;
    private static $_idTrabajador;
    private static $_empresa;
    private static $_pkEmpresa;
    private static $_nombreSucursal;
    private static $_pkSucursal;
    private static $_direccion;
    private static $_telefono;
    private static $_ruc;
    private static $_pagweb;
    private static $_razon;

    function __construct() {
        
    }

    public static function get_userName() {
        self::$_userName = $_SESSION['nameUser'];
        return self::$_userName;
    }

    /**
     * Metodo que muestra el id del usuario
     * @author Jeison Cruz Yesan <jcruzyesan@gmail.com>
     * @return String Id del usuario
     * @copyright (c) 2014, Ghosts Soluciones
     * */
    public static function get_id() {
        self::$_id = $_SESSION['id'];
        return self::$_id;
    }

    public static function get_names() {
        self::$_names = $_SESSION['names'];

        return self::$_names;
    }

    public static function get_lastnames() {
        self::$_lastnames = $_SESSION['lastName'];
        return self::$_lastnames;
    }

    public static function get_pkTypeUsernames() {
        self::$_pkTypeUsernames =$_SESSION['pkTypeUser'];
        return self::$_pkTypeUsernames;
    }

    public static function get_descriptionTypeUser() {
        self::$_descriptionTypeUser = $_SESSION['descriptionTypeUser'];
        return self::$_descriptionTypeUser;
    }

    public static function get_idTrabajador() {
        self::$_idTrabajador = $_SESSION['idTrabajador'];
        return self::$_idTrabajador;
    }

    public static function get_document() {
        self::$_document = $_SESSION['document'];
        return self::$_document;
    }
    public static function get_ruc() {
        self::$_ruc = $_SESSION['ruc'];
        return self::$_ruc;
    }
    public static function get_pagweb() {
        self::$_pagweb = $_SESSION['pagweb'];
        return self::$_pagweb;
    }

    public static function get_pkEmpresa() {
        self::$_pkEmpresa = $_SESSION['pkEmpresa'];
        return self::$_pkEmpresa;
    }    

    public static function get_razon() {
        self::$_razon = $_SESSION['razon'];
        return self::$_razon;
    }
    
    public static function get_empresa() {
        self::$_empresa = $_SESSION['empresa'];
        return self::$_empresa;
    }

    public static function get_pkSucursal() {
        self::$_pkSucursal = $_SESSION['pkSucursal'];
        return self::$_pkSucursal;
    }

    public static function get_nombreSucursal() {
        self::$_nombreSucursal = $_SESSION['nombreSucursal'];
        return self::$_nombreSucursal;
    }
    
    public static function get_direccion() {
        self::$_direccion = $_SESSION['direccion'];
        return self::$_direccion;
    }
    
    public static function get_telefono() {
        self::$_telefono = $_SESSION['telefono'];
        return self::$_telefono;
    }
    
    public static function havePermission($permision) 
    {
        $db = new SuperDataBase();

        $type_trabajador = $_SESSION['pkTypeUser'];

        switch ($type_trabajador) {
            case 1:
            case 2:
            // case 8:
            // case 9:
                return 'usqay-permiso-show';
        }

        $trabajador_id = $_SESSION['idTrabajador'];

        $query = "select * from trabajador_permisos where permiso = '$permision' and pkTrabajador = $trabajador_id";

        $res = $db->executeQueryEx($query);

        while ($row = $db->fecth_array($res)) {
            return 'usqay-permiso-show';
        }

        return 'usqay-permiso-no-show';
    }

}
