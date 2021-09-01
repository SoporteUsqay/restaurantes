<?php

class Application_Controllers_ComprasDocumentosController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {

            case 'AddDetraccionAction':
                $this->_addDetraccion();
                break;
            case 'DeleteAction':
                $this->_delete();
                break;

        }
    }

    public function _addDetraccion()
    {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
            
                $trabajador_id = UserLogin::get_idTrabajador();

                $db = new SuperDataBase();

                $compra_id = isset($_REQUEST['compra_id']) ? $_REQUEST['compra_id'] : 'null';

                $tipo_documento_id = isset($_REQUEST['documento_id']) ? $_REQUEST['documento_id'] : 'null';

                $fecha = isset($_REQUEST['fecha']) ? $_REQUEST['fecha'] : '';

                $porcentaje = isset($_REQUEST['porcentaje']) ? $_REQUEST['porcentaje'] : '0';

                $query = "insert into compras_documentos (compra_id, documento_id, porcentaje, fecha, total, trabajador_id, created_at) values ";

                $query .= "($compra_id, $tipo_documento_id, $porcentaje, '$fecha', 0, $trabajador_id, now())";
                
                $db->executeQueryEx($query);
                
                echo json_encode([
                    "ok" => true
                ]);

            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    public function _delete()
    {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
            
                $db = new SuperDataBase();

                $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 'null';

                $query = "update compras_documentos set 
                        deleted_at = now()
                        where id = $id";

                $db->executeQueryEx($query);
                
                echo json_encode([
                    "ok" => true
                ]);

            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }


    
}