<?php

class Application_Controllers_IndexController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {

            case "ShowHomeAction":
                $this->_showHome();
                break;

            case "ShowLoginAction":
                $this->_showLoginAction();
                break;
            case "ShowContentIndexAction":
                $this->_showContentIndex();
                break;
            case "TestEmailAction":
                $this->_testEmail();
                break;
//            case 'ValidateLoginAction';
//                $this->_validateLogin();
//                break;
//            case 'RegisterViewAction':
//                $this->_showRegister();
//                break;
//            default : $this->_showIndex();
        }
    }

    private function _showRegister() {
        
    }

    private function _testEmail() {
        $objEmail->_sendEmail();
        $ps = Class_App::generatePassword();
        echo $ps;
    }

    private function _showIndex() {
        if (!isset($_SESSION)) {
            $view = new Application_Views_IndexView();
            $view->showIndex();
        }
//        die("No ha Iniciado Session");
    }

    private function _showLoginAction() {
        $view = new Application_Views_IndexView();
        $view->showLogin();
    }

    private function _showHome() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $view = new Application_Views_IndexView();
                $view->showHome();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showContentIndex() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $view = new Application_Views_IndexView();
                $view->showContent();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

}
?>