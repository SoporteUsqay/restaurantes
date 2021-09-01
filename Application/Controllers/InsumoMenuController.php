<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Controllers_InsumoMenuController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {
            case "SaveAction":
                $this->_Save();
                break;
            case "SaveContenedoresAction":
                $this->_SaveContenedores();
                break;
            case "ListAction":
                $this->_list();
                break;
            case "UpdateAction":
                $this->_update();
                break;
            case "DeleteAction":
                $this->_delete();
                break;
            case "ListIdAction":
                $this->_listId();
                break;
            case "SaveRecetaAction":
                $this->_saveReceta();
                break;
            case "EditRecetaAction":
                $this->_editReceta();
                break;
            case "DeleteRecetaAction":
                $this->_deleteReceta();
                break;
            case "SaveSubRecetaAction":
                $this->_saveSubReceta();
                break;
        }
    }

    private function _Save() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_InsumoMenuModel();

                $obj->setPkInsumo($_REQUEST['pkInsumo']);
                $obj->set_cantidad($_REQUEST['cantidad']);
                $obj->set_pkPlato($_REQUEST['pkPlato']);
                $obj->set_pkUnidad($_REQUEST['estado']);

                echo $result = (float) $obj->insert();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _SaveContenedores() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_InsumoMenuModel();
                for ($i = 0; $i < $_REQUEST['contador']; $i++) {
                    if (isset($_REQUEST['chk' . $i])) {
                        $obj->setPkInsumo($_REQUEST['pkInsumo' . $i]);
                        $obj->set_cantidad($_REQUEST['cantidad' . $i]);
                        $obj->set_pkPlato($_REQUEST['pkPlato']);
                        $obj->set_pkUnidad(1);
                        $result = (float) $obj->insert();
                        
                    }
                    echo $result;
                }
                
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _list() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_InsumoMenuModel();


                $obj->Listar();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _update() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_InsumoMenuModel();

                $obj->set_cantidad($_REQUEST['cantidad']);
//                $obj->set_($_REQUEST['cantidad']);
                $obj->update($_REQUEST['id'],$_REQUEST['estado']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    private function _delete() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_InsumoMenuModel();

               echo $obj->delete($_REQUEST['id']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _listId() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_InsumoMenuModel();
                $obj->sel($_REQUEST["id"]);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _saveReceta() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                
                $db = new SuperDataBase();

                $plato_id = isset($_REQUEST['plato_id']) ? $_REQUEST['plato_id'] : 'null';

                $insumo_id = isset($_REQUEST['insumo_id']) ? $_REQUEST['insumo_id'] : 'null';

                $insumo_porcion_id = isset($_REQUEST['insumo_porcion_id']) && $_REQUEST['insumo_porcion_id'] ? $_REQUEST['insumo_porcion_id'] : 'null';

                $cantidad = isset($_REQUEST['cantidad']) ? $_REQUEST['cantidad'] : '0';

                $almacen_id = isset($_REQUEST['almacen_id']) ? $_REQUEST['almacen_id'] : 'null';

                $terminal = isset($_REQUEST['terminal']) && $_REQUEST['terminal'] ? "'".$_REQUEST['terminal']."'" : "null";

                $query = "insert into n_receta (plato_id, insumo_id, insumo_porcion_id, cantidad, almacen_id, terminal, created_at) values ";

                $query .= "('$plato_id', $insumo_id, $insumo_porcion_id, $cantidad, $almacen_id, $terminal, now())";

                echo $query;

                $res = $db->executeQueryEx($query);

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

    private function _editReceta() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                
                $db = new SuperDataBase();

                $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 'null';

                $plato_id = isset($_REQUEST['plato_id']) ? $_REQUEST['plato_id'] : 'null';

                $insumo_id = isset($_REQUEST['insumo_id']) ? $_REQUEST['insumo_id'] : 'null';

                $insumo_porcion_id = isset($_REQUEST['insumo_porcion_id']) && $_REQUEST['insumo_porcion_id'] ? $_REQUEST['insumo_porcion_id'] : 'null';

                $cantidad = isset($_REQUEST['cantidad']) ? $_REQUEST['cantidad'] : '0';

                $almacen_id = isset($_REQUEST['almacen_id']) ? $_REQUEST['almacen_id'] : 'null';

                $terminal = isset($_REQUEST['terminal']) && $_REQUEST['terminal'] ? "'".$_REQUEST['terminal']."'" : "null";

                $query = "update n_receta 
                    set plato_id = '$plato_id',
                        insumo_id = $insumo_id,
                        insumo_porcion_id = $insumo_porcion_id,
                        cantidad = $cantidad,
                        almacen_id = $almacen_id, 
                        terminal = $terminal

                    where id = $id 
                ";

                // echo $query;

                $res = $db->executeQueryEx($query);

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

    private function _deleteReceta() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                
                $db = new SuperDataBase();

                $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 'null';

                $query = "update n_receta set deleted_at = now() where id = $id";

                $res = $db->executeQueryEx($query);

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

    private function _saveSubReceta() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                
                $db = new SuperDataBase();

                $plato_id = isset($_REQUEST['plato_id']) ? $_REQUEST['plato_id'] : 'null';

                $plato_copiar_id = isset($_REQUEST['plato_copiar_id']) ? $_REQUEST['plato_copiar_id'] : 'null';

                $cantidad = isset($_REQUEST['cantidad']) ? $_REQUEST['cantidad'] : 0;

                try {

                    $db->executeQueryEx('SET AUTOCOMMIT=0');
                    $db->executeQueryEx('START TRANSACTION');

                    if ($plato_copiar_id != 'null' && $plato_id != 'null' && $cantidad > 0) {

                        $query = "select * from n_receta where plato_id = '$plato_copiar_id'";
    
                        $res = $db->executeQueryEx($query);
    
                        while($row = $db->fecth_array($res)) {

                            $query = "insert into n_receta (plato_id, insumo_id, insumo_porcion_id, cantidad, almacen_id, receta_id, created_at) values ";

                            $_insumo_porcion_id = $row['insumo_porcion_id'] ? $row['insumo_porcion_id'] : 'null';
                            
                            $cantidad_= $row['cantidad'] * $cantidad;

                            $query .= "('$plato_id', {$row['insumo_id']}, $_insumo_porcion_id, $cantidad_, {$row['almacen_id']}, {$row['id']}, now())";
    
                            // echo "<br> $query";
                            $db->executeQueryEx($query);
                        }
                    } 

                    $db->executeQueryEx('COMMIT');
        
                } catch (Exception $e) {
                    $db->executeQuery('ROLLBACK');
                } finally {
                    $db->executeQuery('SET AUTOCOMMIT=1');
                }
                
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
