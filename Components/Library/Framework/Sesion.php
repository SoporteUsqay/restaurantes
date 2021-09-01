<?php

session_start();

class Start_sesion {

    public function __construct() {
        
    }

    /**
     * @author Jeison Cruz Yesan
     * @param string $id identification for User
     * @param string $nameUser Name User
     * * */
    public function startSession($id, $nameUser, $pkTypeUser, $names, $lastName, $descriptionTypeUser, $idTrabajador, 
            $document, $empresa, $pkempresa, $pkSucursal, $nombreSucursal,$fechaSalida) {
        $_SESSION['nameUser'] = $nameUser;
        $_SESSION['id'] = $id;
        $_SESSION['names'] = $names;
        $_SESSION['lastName'] = $lastName;
        $_SESSION['pkTypeUser'] = $pkTypeUser;
        $_SESSION['descriptionTypeUser'] = $descriptionTypeUser;
        $_SESSION['idTrabajador'] = $idTrabajador;
        $_SESSION['document'] = $document;
        $_SESSION['pkEmpresa'] = $pkempresa;
        $_SESSION['nombreSucursal'] = $nombreSucursal;
        $_SESSION['pkSucursal'] = $pkSucursal;
        $_SESSION['empresa'] = $empresa;
        $_SESSION['fechaSalida'] = $fechaSalida;
//        $_SESSION['direccion'] = $direccion;
//        $_SESSION['telefono'] = $telefono;
//        $_SESSION['ruc'] = $ruc;
//        $_SESSION['pagweb'] = $pagweb;
//        $_SESSION['razon'] = $razon;
        $_SESSION['endAccess'] = date("Y-n-j H:i:s");
        $_SESSION['valida'] = true;
    }

    public function validateSesion() {
        $now = date("Y-n-j H:i:s");
        $timeSesion = (strtotime($now) - strtotime($_SESSION['endAccess']));
        if ($timeSesion >= 20000) {
            $this->closeSesion();
            return true;
        } else {
            $_SESSION['endAccess'] = date("Y-n-j H:i:s");
            return false;
        }
    }

    public function validateStartSesion() {
        if (isset($_SESSION['valida'])) {
            if ($_SESSION['valida'] == true && !empty($_SESSION['nameUser'])) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function redirect() {
        header("Location: " . Class_config::get('urlApp')."/");
        exit();
    }

    public function closeSesion() {
        unset($_SESSION['nameUser']);
        unset($_SESSION['id']);
        unset($_SESSION['names']);
        unset($_SESSION['lastName']);
        unset($_SESSION['pkTypeUser']);
        unset($_SESSION['descriptionTypeUser']);
        unset($_SESSION['idTrabajador']);
        unset($_SESSION['document']);
        unset($_SESSION['pkEmpresa']);
        unset($_SESSION['pkEmpresa']);
        unset($_SESSION['document']);
        unset($_SESSION['nombreSucursal']);
        unset($_SESSION['empresa']);
       
        unset($_SESSION['pkSucursal']);
        unset($_SESSION['endAccess']);
        unset($_SESSION['endAccess']);
        unset($_SESSION['validate']);
        session_destroy();
    }

}

?>