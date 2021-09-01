<?php

class Application_Controllers_WorkPeopleController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {
            case 'ListAction':
                $this->_listAllWorkPeople();
                break;
            case 'List2Action':
                $this->_listAllWorkPeople2();
                break;
            case 'ShowAdminPersonalAction':
                $this->_showAdminPersonal();
                break;

            case 'SaveAction':
                $this->_save();
                break;
            case 'DeleteAction':
                $this->_deleteWorkPeople();
                break;
            case 'ValidateDocumentAction':
                $this->_validateDocument();
                break;
            case 'ValidateEmailAction':
                $this->_validateEmail();
                break;
            case 'DropAction':
                $this->_DropWorkPeople();
                break;
            case 'UpdateAction':
                $this->_update();
                break;
            case 'ListTrabadoresAction':
                $this->_ListTrabadores();
                break;
            case "ActiveAction":
                $this->_Active();
                break;
            case 'ListTrabadoresIDAction':
                if (self::$session->validateStartSesion()) {
                    if (!self::$session->validateSesion()) {
                        $objViewWorkPeople = new Application_Models_WorkPeopleModel();
                        $objViewWorkPeople->listTrabajadoresId($_REQUEST['id']);
                    } else {
                        self::$session->redirect();
                    }
                } else {
                    self::$session->redirect();
                }
                break;
            case 'PersonalAction':
                $this->_showpersonal();
                break;
            case 'ChangePermisoBotonAction':
                $this->_changePermisoBoton();
                break;
        }
    }

    private function _listAllWorkPeople() {
        $objModelWorkPeople = new Application_Models_WorkPeopleModel();
        $objModelWorkPeople->_listAllWorkPeople();
    }

    private function _showpersonal() {
        $objModelWorkPeople = new Application_Views_WorkPeopleView();
        $objModelWorkPeople->showAdminPersonal();
    }

    private function _listAllWorkPeople2() {
        $objModelWorkPeople = new Application_Models_WorkPeopleModel();
        $objModelWorkPeople->_listAllWorkPeople2();
    }

    private function _showRegister() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objViewWorkPeople = new Application_Views_WorkPeopleView();
                $objViewWorkPeople->showRegister();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _DropWorkPeople() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objViewWorkPeople = new Application_Models_WorkPeopleModel();
                $objViewWorkPeople->set_pkWorkPeople($_REQUEST['pkWorkPeople']);
                $objViewWorkPeople->_drop();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _deleteWorkPeople() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelWorkPeople = new Application_Models_WorkPeopleModel();
                $objModelWorkPeople->deleteWorkPeople($_REQUEST['id']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showAdminPersonal() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objViewWorkPeople = new Application_Views_WorkPeopleView();
                $objViewWorkPeople->showAdminPersonal();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _ListTrabadores() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objViewWorkPeople = new Application_Models_WorkPeopleModel();
                $objViewWorkPeople->listTrabajadores();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _save() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $_dni=$_REQUEST['documento'];
                $_nombre=$_REQUEST['nombres'];
                $_apellido=$_REQUEST['apellidos'];
                $_direccion=$_REQUEST['direccion'];
                $_tipousuario=$_REQUEST['pkTipo'];
                $_clave=(utf8_decode($_REQUEST['clave']));
                $objModelWorkPeople = new Application_Models_WorkPeopleModel();
                $objModelWorkPeople->_save($_dni,$_nombre,$_apellido,$_direccion,$_clave,$_tipousuario);
             //   $objModelWorkPeople->_save('12369854','MANUEL E','MALAVER A','PIURA R','125635874',4);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _validateDocument() {
        $objModelWorkPeople = new Application_Models_WorkPeopleModel();
        $objModelWorkPeople->_validationIdentification($_POST['document']);
    }

    private function _validateEmail() {
        $objModelWorkPeople = new Application_Models_WorkPeopleModel();
        $objModelWorkPeople->_validationEmail($_POST['email']);
    }

    private function _update() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $_id=$_REQUEST['pkTrabajador'];
                $_dni=$_REQUEST['documento'];
                $_nombre=$_REQUEST['nombres'];
                $_apellido=$_REQUEST['apellidos'];
                $_direccion=$_REQUEST['direccion'];
                $_tipousuario=$_REQUEST['pkTipo'];
                $_clave=utf8_decode($_REQUEST['clave']);
                $objModelWorkPeople = new Application_Models_WorkPeopleModel();
                $objModelWorkPeople->_update($_id,$_dni,$_nombre,$_apellido,$_direccion,$_clave,$_tipousuario);
             //   $objModelWorkPeople->_save('12369854','MANUEL E','MALAVER A','PIURA R','125635874',4);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }

    }

    private function _Active() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelWorkPeople = new Application_Models_WorkPeopleModel();
                $objModelWorkPeople->activePersonal($_POST['id']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _changePermisoBoton() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                
                $db = new SuperDataBase();

                // $trabajador_id = UserLogin::get_idTrabajador();
            
                $trabajador_id = $_REQUEST['trabajador_id'];

                $permiso = $_REQUEST['permiso'];

                $action = $_REQUEST['action'];

                if ($action == "add") {

                    $query = "insert into trabajador_permisos (pkTrabajador, permiso) values ($trabajador_id, '$permiso')";
                } else {

                    $query = "delete from trabajador_permisos where pkTrabajador = $trabajador_id and permiso = '$permiso'";
                }

                $res = $db->executeQueryEx($query);

                echo json_encode("ok");

            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

}
