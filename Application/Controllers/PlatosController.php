<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Controllers_PlatosController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {
            case 'ListAction':
                $this->_list();
                break;
            case 'ListIdAction':
                $this->_listId();
                break;
            case 'ChangeAccesoRapidoAction':
                $this->_changeAccesoRapido();
                break;
            case 'GetCartaQrAction':
                $this->_getCartaQr();
                break;
            case 'SaveCartaQrAction':
                $this->_saveCartaQr();
                break;
        }
    }

    public function _list() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelPedidos = new Application_Models_PlatosModel();
                $objModelPedidos->listar();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    public function _listId() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelPedidos = new Application_Models_PlatosModel();
                $objModelPedidos->listId($_REQUEST['id']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    public function _changeAccesoRapido() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {

                $pkPlato = $_POST['pkPlato'];
                $isAccesoRapido = !$_POST['isAccesoRapido'];

                $db = new SuperDataBase();

                $query = "update plato set isAccesoRapido = '$isAccesoRapido' where pkPLato='$pkPlato'";

                $db->executeQueryEx($query);
                
                echo "ok";

            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
    public function _getCartaQr() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                require_once('vendor/phpqrcode/phpqrcode.php');

                $db = new SuperDataBase();

                $query = "select * from carta_qr where id = 1";

                $res = $db->executeQueryEx($query);

                $row_ = null;                
                
                while($row = $db->fecth_array($res)) {
                    if ($row['url']) {
                        $row_ = $row;
                        QRcode::png($row_['url'], 'carta-qr.png', QR_ECLEVEL_L, 10);
                    }
                }

                echo json_encode($row_);

            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    public function _saveCartaQr() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {

                $url = $_POST['url'];

                $db = new SuperDataBase();

                $query = "update carta_qr set url = '$url' where id = 1";

                $db->executeQueryEx($query);
                
                echo "ok";

            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

}
