<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (!isset($_SESSION)) {
    session_start();
}

class Application_Controllers_UserController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {
            case 'ValidateUserAction':
                $this->_validateUser();
                break;
            case 'ValidatePasswordAction':
                $this->_validateUserPassword();
                break;
            case 'ShowListAction':
                $this->_showList();
                break;
            case 'ShowIndexAction':
                $this->_showList();
                break;
            case 'ShowRegisterAction':
                $this->_showRegister();
                break;
            case 'ShowModifyPermissionsAction':
                $this->_showModifyPermissions();
                break;

            case 'ShowModifyUserAction':
                $this->_showModifyUserLogin();
                break;

            case 'SearchUserAction':
                $this->_searchUser();
                break;
            case 'SaveAction':
                $this->_saveUser();
                break;
            case 'UpdateAction':
                $this->_updateUser();
                break;
            case 'DeleteAction':
                $this->_deleteUser();
                break;
            case 'ListAction':
                $this->_listUser();
                break;
            case 'LoginAction':
                $this->_loginUser();
                break;
            case 'CloseSessionAction':
                $this->_closeSession();
                break;
            case 'ListUserTypeAction':
                $this->_listUserforType();
                break;           
            case 'CambiarClaveAction':
                $this->_CambiarClave();
                break;
            case 'ListNotUserAction':
                $this->_listNotUser();
                break;
            case 'AddUserAction':
                $this->_AddUser();
                break;
            case 'VerificaPwAction':
                $this->_VerificaContraseña();
                break;

            case 'ChangePassAction':
                $this->_changePass();
                break;
        }
    }

    private function _changePass() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                
                $id = UserLogin::get_id();

                $db = new SuperDataBase();

                $password_actual = $_REQUEST['password_actual'] ? $_REQUEST['password_actual'] : '';

                if (!$password_actual) {
                    echo json_encode([
                        "ok" => false, 
                        "message" => "Ingrese su actual contraseña",
                    ]);
                    return;
                }

                $password_nuevo = $_REQUEST['password_nuevo'] ? $_REQUEST['password_nuevo'] : '';

                if (!$password_nuevo) {
                    echo json_encode([
                        "ok" => false, 
                        "message" => "Ingrese una nueva contraseña",
                    ]);
                    return;
                }

                $query = "select * from trabajador where pkTrabajador = $id and password = md5('$password_actual')";

                $res = $db->executeQueryEx($query);

                $existe = 0;

                while ($row = $db->fecth_array($res)) {
                    $existe = 1;
                }

                if ($existe == 0) {
                    echo json_encode([
                        "ok" => false, 
                        "message" => "La contraseña actual ingresada es incorrecta",
                    ]);
                    return;
                }

                $query = "update trabajador set password = md5('$password_nuevo') where pkTrabajador = $id";

                $db->executeQueryEx($query);

                echo json_encode([
                    "ok" => true, 
                    "message" => "Contraseña cambiada correctamente",
                ]);

            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _AddUser() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModel = new Application_Models_UserModel();
                $tipo = $_REQUEST['clave'];
                echo $tipo;
                if ($tipo == "1") {
                    $objModel->set_user($_REQUEST['trabajador']);
                    $objModel->set_fkTypeUser($objModel->sacarIDtrabajador($_REQUEST['trabajador']));
//                   $objModel->sacarIDtrabajador($_REQUEST['trabajador']);
                    $objModel->set_password($_REQUEST['trabajador']);
                    $objModel->_saveUser();
                } else {
                    $objModel->set_user($_REQUEST['trabajador']);
                    $objModel->set_fkTypeUser($objModel->sacarIDtrabajador($_REQUEST['trabajador']));
                    $objModel->set_password($_REQUEST['textClave']);
//                    die($objModel->get_fkWorkPeople())

                    $objModel->_saveUser();
                }
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _listNotUser() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModel = new Application_Models_UserModel();

                $objModel->_listNotUser();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showModifyUserLogin() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objViewUser = new Application_Views_UserView();
                $objViewUser->showModifyUser();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _VerificaContraseña() {
        $objModel = new Application_Models_UserModel();
        echo $objModel->verificaContraseña(utf8_decode($_REQUEST['value']));
    }

    private function _validateUser() {
        $objModel = new Application_Models_UserModel();
        $objModel->set_user($_POST['userName']);
        echo $objModel->_validateUser();
    }

    private function _validateUserPassword() {
        $objModel = new Application_Models_UserModel();
        $objModel->set_user($_POST['userName']);
        $objModel->set_password($_POST['userPassword']);
        echo $objModel->_validateUserPassword();
    }

    private function _searchUser() {
        $objModel = new Application_Models_UserModel();
        $objModel->set_user($_POST['userName']);
        echo json_encode($objModel->_searchUserValidate());
    }

    private function _showList() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Views_UserView();
                $objView->showList();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showRegister() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objVier = new Application_Views_UserView();
                $objVier->showRegister();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
//        $objVier = new Application_Views_UserView();
//        $objVier->showRegister();
    }

    private function _showModifyPermissions() {
        $objVier = new Application_Views_UserView();
        $objVier->showModifyPermissions();
    }

    private function _saveUser() {
        $objModel = new Application_Models_UserModel();
        $objModel->set_fkTypeUser($_POST['fkTypeUser']);
        $objModel->set_fkWorkPeople($_POST['fkWorkPeople']);
        $objModel->set_user($_POST["UserName"]);
        $objModel->set_password($_POST['UserPassword']);
        $objModel->_saveUser();
    }

    private function _updateUser() {
        $objModel = new Application_Models_UserModel();
        $objModel->set_pkUser($_POST['pkUser']);
        $objModel->set_fkTypeUser($_POST['fkTypeUser']);

        $objModel->_updateUser();
        echo json_encode("true");
    }

    private function _deleteUser() {
        $objModel = new Application_Models_UserModel();
        $objModel->set_pkUser($_POST['pkUser']);
//        $objModel->set_fkTypeUser($_POST['fkTypeUser']);

        $objModel->_deleteUser();
        echo json_encode("true");
    }

    private function _listUser() {
        $objModelUser = new Application_Models_UserModel();
        $objModelUser->_listUser();
    }

    private function _listUserforType() {
        $objModelUser = new Application_Models_UserModel();
        $objModelUser->listUserForType($_REQUEST['tipo']);
    }

    private function _loginUser() {
        $objModelUser = new Application_Models_UserModel();

        $user = "";
        if (isset($_REQUEST['userName'])) {
            $user = $_REQUEST['userName'];
        }
        $objModelUser->set_user($user);
        $objModelUser->set_password($_REQUEST['userPassword']);
        $array = $objModelUser->_login();
        if (count($array) > 0) {
            $tipo = "";
            for ($i = 0; $i < count($array); $i++) {
               
                self::$session->startSession($array[$i]['id'], $array[$i]['user'], $array[$i]['pkTypeUser'], $array[$i]['names'],
                        $array[$i]['lastName'], $array[$i]['descriptionTypeUser'], $array[$i]['pkWorkPeople'], $array[$i]['document'],
                        $array[$i]['empresa'], $array[$i]['pkEmpresa'], $array[$i]['pkSucursal'], $array[$i]['nombreSucursal'], $array[$i]['fechaSalida']
                        )
                ;
                $tipo = $array[$i]['pkTypeUser'];
            }
            
            //Seteamos cookie de permisos
            setcookie("TYP",$tipo, time() + 3600000, '/');
            
            //Actualizamos fecha de ingreso
            $db = new SuperDataBase();
            $query = "update trabajador set fechaIngreso=now() where pkTrabajador='" . UserLogin::get_id()."'";
            $db->executeQuery($query);
            
            //Verificamos caja
            $query_caja = "Select * from cajas where caja = '".$_COOKIE["c"]."'";
            $res_caja = $db->executeQuery($query_caja);
            $existe_caja = 0;
            while($rau = $db->fecth_array($res_caja)){
                $existe_caja = 1;
            }
            if($existe_caja == 0){
                
                //Hacemos verificacion de cajas
                $cajas = $db->executeQuery("Select * from cajas");
                $ncajas = 0;
                while($rcajas = $db->fecth_array($cajas)){
                    $ncajas = $ncajas + 1;
                }
                
                //Si ya existen cajas agregamos la nueva
                if($ncajas > 0){
                    $db->executeQuery("Insert into cajas values(NULL,'".$_COOKIE["c"]."')");
                    //Agregamos corte a esta nueva caja
                    $query_cortes = "Select * from corte where fin is NULL order by id DESC Limit 1";
                    $cortes = $db->executeQuery($query_cortes);
                    while($rcor = $db->fecth_array($cortes)){
                        $query_regulariza_corte = "Insert into corte values(NULL,'".$rcor["fecha_cierre"]."','".$rcor["inicio"]."',NULL,0,1)";
                        $db->executeQuery($query_regulariza_corte);
                        $idc = $db->getId();
                        //Insertamos la accion caja
                        $db->executeQuery("Insert into accion_caja values(NULL,'".$idc."','CUT','".$_COOKIE["c"]."')");
                    }                   
                }else{
                    //Esto es para actualizaciones de sistemas, a futuro debe desaparecer
                    //Si el sistema no ha tenido cajas
                    //Agregamos pedidos, comprobantes, cortes y pagos a caja actual
                    $pedidos = "Select pkPediido from pedido";
                    $comprobantes = "Select * from comprobante";
                    $cortes = "Select * from corte";
                    $gastos = "Select * from gastos_diarios";
                    $salones = "Select * from salon";
                    $tipos = "Select * from tipos";
                    
                    //Pedidos
                    $r_ped = $db->executeQuery($pedidos);
                    while($row_p = $db->fecth_array($r_ped)){
                       $db->executeQuery("Insert into accion_caja values(NULL,'".$row_p["pkPediido"]."','PED','".$_COOKIE["c"]."')"); 
                    }
                    
                    //Comprobantes
                    $r_comp = $db->executeQuery($comprobantes);
                    while($row_c = $db->fecth_array($r_comp)){
                        if(intval($row_cp["pkTipoComprobante"]) === 1){
                            $db->executeQuery("Insert into accion_caja values(NULL,'".$row_cp["ncomprobante"]."','BOL','".$_COOKIE["c"]."')"); 
                        }else{
                            $db->executeQuery("Insert into accion_caja values(NULL,'".$row_cp["ncomprobante"]."','FAC','".$_COOKIE["c"]."')"); 
                        }
                       
                    }
                    
                    //Cortes
                    $r_cor = $db->executeQuery($cortes);
                    while($row_co = $db->fecth_array($r_cor)){
                       $db->executeQuery("Insert into accion_caja values(NULL,'".$row_co["id"]."','CUT','".$_COOKIE["c"]."')"); 
                    }
                    
                    //Gastos
                    $r_gas = $db->executeQuery($gastos);
                    while($row_g = $db->fecth_array($r_gas)){
                       $db->executeQuery("Insert into accion_caja values(NULL,'".$row_g["pkGastosDiarios"]."','GAS','".$_COOKIE["c"]."')"); 
                    }
                    
                    //Salones
                    $r_sal = $db->executeQuery($salones);
                    while($row_s = $db->fecth_array($r_sal)){
                       $db->executeQuery("Insert into accion_caja values(NULL,'".$row_s["pkSalon"]."','SAL','".$_COOKIE["c"]."')"); 
                    }

                    //Tipos
                    $r_typ = $db->executeQuery($tipos);
                    while($row_t = $db->fecth_array($r_typ)){
                       $db->executeQuery("Insert into accion_caja values(NULL,'".$row_t["pkTipo"]."','TYP','".$_COOKIE["c"]."')"); 
                    }
                    
                    //Insertamos caja
                    $db->executeQuery("Insert into cajas values(NULL,'".$_COOKIE["c"]."')");
                }               
            }
            
            //Verificamos redireccionamiento de apertura
            switch ($tipo) {
                case '3':
                case '5':
                case '99':
                case '4':
                case '9':
                case '10':
                    echo '<meta http-equiv="refresh" content="0;URL=\''. Class_config::get('urlApp') . "/?controller=Index&&action=ShowHome" . '\'" />';
                    break;
                
                default:
                    $db = new SuperDataBase();
                    $query = "SELECT * FROM cierrediario c where pkSucursal='" . UserLogin::get_pkSucursal() . "';";
                    $result = $db->executeQuery($query);
                    $fecha = "";
                    while ($row = $db->fecth_array($result)) {
                        $fecha = $row['fecha'];
                    }
                    $fActual = new DateTime();
                    if ($fecha != $fActual->format('Y-m-d')) {
                        echo '<meta http-equiv="refresh" content="0;URL=\''. Class_config::get('urlApp') . "/?controller=Caja&action=ShowConfirm" . '\'" />';
                    } else {
                        $query = "SELECT 1 as valor FROM n_historial_stock_insumo h where h.fecha='$fecha' limit 1;";
                        $result = $db->executeQuery($query);
                        $valor = 0;
                        while ($row = $db->fecth_array($result)) {
                            $valor = $row['valor'];
                        }
                        if($valor == 0){
                            $objInsumo = new Application_Models_InsumoModel();
                            $objInsumo->AgregarHistorialStock();                      
                        }

                        if (in_array($tipo, [1, 2])) {
                            echo '<meta http-equiv="refresh" content="0;URL=\''. Class_config::get('urlApp') . "/?controller=Dashboard&&action=Show" . '\'" />';
                        } else {
                            echo '<meta http-equiv="refresh" content="0;URL=\''. Class_config::get('urlApp') . "/?controller=Index&&action=ShowHome" . '\'" />';
                        }
                    }
                    
                    


                    break;
            }
        } else {
            echo '<meta http-equiv="refresh" content="0;URL=\''. Class_config::get('urlApp') . "/?message=false" . '\'" />';
        }
    }

    private function _closeSession() {
        $db = new SuperDataBase();
       $query = "update trabajador set fechaSalida=now() where pkTrabajador='" . UserLogin::get_id()."'";
            $db->executeQuery($query);
        self::$session->closeSesion();
        self::$session->redirect();
    }

    private function _CambiarClave() {
        $objModelUser = new Application_Models_UserModel();
        $user = UserLogin::get_userName();
        $objModelUser->_cambiarClave(($_REQUEST['usuario']), utf8_decode($_REQUEST['clave']), ($_REQUEST['repetirclave']));
    }

}