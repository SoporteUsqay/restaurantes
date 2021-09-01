<?php

class Application_Controllers_NAlmacenController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {

            case 'ListAlmacenAction':
                $this->_listAlmacen();
                break;

            case 'AddIngresoAction':
                $this->_createIngreso();
                break;
            case 'EditIngresoAction':
                $this->_editIngreso();
                break;
            case 'AddDetalleIngresoAction':
                $this->_createDetalleIngreso();
                break;
            case 'EditDetalleIngresoAction':
                $this->_editDetalleIngreso();
                break;
            case 'DeleteDetalleIngresoAction':
                $this->_deleteDetalleIngreso();
                break;

            case 'DeleteMovimientoAction':
                $this->_deleteMovimiento();
                break;

            case 'AddSalidaAction':
                $this->_createSalida();
                break;
            case 'EditSalidaAction':
                $this->_editSalida();
                break;
            case 'AddDetalleSalidaAction':
                $this->_createDetalleSalida();
                break;
            case 'EditDetalleSalidaAction':
                $this->_editDetalleSalida();
                break;

            case 'AddAlmacenAction':
                $this->_addAlmacen();
                break;
            case 'EditAlmacenAction':
                $this->_editAlmacen();
                break;
            case 'DeleteAlmacenAction':
                $this->_deleteAlmacen();
                break;


            case 'SendPrintAction':
                $this->_sendPrint();
                break;
        }
    }

    public function _listAlmacen()
    {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                
                $db = new SuperDataBase();

                $query = "select * from n_almacen";

                $res = $db->executeQueryEx($query);

                $data = [];

                while($row = $db->fecth_array($res)) {
                    $data[] = $row;
                }

                echo json_encode($data);

            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    public function _addAlmacen()
    {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                
                $db = new SuperDataBase();
                
                $nombre = isset($_REQUEST['nombre']) ? $_REQUEST['nombre'] : '';

                if (!$nombre) {
                    echo json_encode([
                        "ok" => false,
                        "message" => "Debe ingresar un nombre"
                    ]);
                    return;
                }

                $query = "insert into n_almacen (nombre) values ";

                $query .= "('$nombre')";

                $db->executeQueryEx($query);

                echo json_encode([
                    "ok" => true,
                ]);

            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    public function _editAlmacen()
    {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                
                $db = new SuperDataBase();
                
                $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
                
                $nombre = isset($_REQUEST['nombre']) ? $_REQUEST['nombre'] : '';

                if (!$id) {
                    echo json_encode([
                        "ok" => false,
                        "message" => "Debe seleccionar un almacen"
                    ]);
                    return;
                }

                if (!$nombre) {
                    echo json_encode([
                        "ok" => false,
                        "message" => "Debe ingresar un nombre"
                    ]);
                    return;
                }

                $query = "update n_almacen 
                    set nombre = '$nombre' 
                    where id = $id";

                $db->executeQueryEx($query);

                echo json_encode([
                    "ok" => true,
                ]);

            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    public function _deleteAlmacen()
    {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                
                $db = new SuperDataBase();
                
                $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
                
                if (!$id) {
                    echo json_encode([
                        "ok" => false,
                        "message" => "Debe seleccionar un almacen"
                    ]);
                    return;
                }

                $query = "delete from n_almacen where id = $id";

                $db->executeQueryEx($query);

                echo json_encode([
                    "ok" => true,
                ]);

            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }


    public function _deleteMovimiento()
    {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {

                $db = new SuperDataBase();

                $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 'null';

                if ($id == 'null') {
                    echo json_encode([
                        "ok" => false,
                        "message" => "No se encontró un ID"
                    ]);
                    return;
                }

                $query = "update n_detalle_movimiento_almacen set deleted_at = now() where movimiento_id = $id";

                $db->executeQueryEx($query);

                $query = "update n_movimiento_almacen set deleted_at = now() where id = $id";

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

    public function _createIngreso()
    {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                
                $trabajador_id = UserLogin::get_idTrabajador();

                $db = new SuperDataBase();

                $tipo_comprobante_id = isset($_REQUEST['txtTipoComprobante']) ? $_REQUEST['txtTipoComprobante'] : 'null';

                $numero_comprobante = isset($_REQUEST['txtNroComprobante']) ? $_REQUEST['txtNroComprobante'] : '';

                $obj = new Application_Models_CajaModel();
                
                $fecha = $obj->fechaCierre();

                $almacen_id = isset($_REQUEST['id_almacen']) ? $_REQUEST['id_almacen'] : 'null';

                $proveedor_id = isset($_REQUEST['id_proveedor']) && $_REQUEST['id_proveedor'] != '' ? $_REQUEST['id_proveedor'] : 'null';

                $tipo = 1;

                $query = "insert into n_movimiento_almacen (tipo_comprobante_id, numero_comprobante, fecha, almacen_id, proveedor_id, trabajador_id, tipo, created_at) values ";

                $query .= "($tipo_comprobante_id, '$numero_comprobante', '$fecha', $almacen_id, $proveedor_id, $trabajador_id, $tipo, now())"; 
                
                $db->executeQueryEx($query);
                
                echo json_encode([
                    "ok" => true,
                    "id" => $db->getId()
                ]);

            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    public function _editIngreso()
    {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {

                $trabajador_id = UserLogin::get_idTrabajador();

                $db = new SuperDataBase();

                $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 'null';

                if ($id == 'null') {
                    echo json_encode([
                        "ok" => false,
                        "message" => "No se encontró un ID"
                    ]);
                    return;
                }

                $numero_comprobante = isset($_REQUEST['txtNroComprobante']) ? $_REQUEST['txtNroComprobante'] : '';

                $almacen_id = isset($_REQUEST['id_almacen']) ? $_REQUEST['id_almacen'] : 'null';

                $proveedor_id = isset($_REQUEST['id_proveedor']) && $_REQUEST['id_proveedor'] != '' ? $_REQUEST['id_proveedor'] : 'null';

                $query = "update n_movimiento_almacen 
                    set numero_comprobante = '$numero_comprobante',
                        almacen_id = $almacen_id,
                        proveedor_id = $proveedor_id,
                        updated_at = now()

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

    public function _createDetalleIngreso()
    {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                
                $trabajador_id = UserLogin::get_idTrabajador();

                $db = new SuperDataBase();

                $movimiento_id = isset($_REQUEST['movimiento_id']) ? $_REQUEST['movimiento_id'] : 'null';

                $insumo_id = isset($_REQUEST['txtingreseInsumo']) ? $_REQUEST['txtingreseInsumo'] : 'null';

                if (!$insumo_id) {
                    echo json_encode([
                        "ok" => false,
                        "message" => "El insumo es requerido"
                    ]);
                    return;
                }

                $insumo_porcion_id = isset($_REQUEST['txtInsumoPorcion']) && $_REQUEST['txtInsumoPorcion'] ? $_REQUEST['txtInsumoPorcion'] : 'null';

                $unidad_id = isset($_REQUEST['unidad_id']) ? $_REQUEST['unidad_id'] : 'null';

                $cantidad = isset($_REQUEST['cantidad']) ? $_REQUEST['cantidad'] : '0';

                $precio = isset($_REQUEST['precio']) ? $_REQUEST['precio'] : '0';

                $almacen_id = isset($_REQUEST['id_almacen']) ? $_REQUEST['id_almacen'] : 'null';

                $obj = new Application_Models_CajaModel();
                
                $fecha = $obj->fechaCierre() . date(' H:i:s');

                $query = "insert into n_detalle_movimiento_almacen (tipo, movimiento_id, insumo_id, insumo_porcion_id, unidad_id, cantidad, precio, fecha, almacen_id, motivo, created_at) values ";

                $query .= "(1, $movimiento_id, $insumo_id, $insumo_porcion_id, $unidad_id, $cantidad, $precio, '$fecha', $almacen_id, 'INGRESO', now())";
                
                // echo $query;

                $db->executeQueryEx($query);

                // $query = "update insumos set precio_promedio = $precio where pkInsumo = $insumo_id ";

                // $db->executeQueryEx($query);
                
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

    
    public function _editDetalleIngreso()
    {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {

                $trabajador_id = UserLogin::get_idTrabajador();

                $db = new SuperDataBase();

                $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 'null';

                if ($id == 'null') {
                    echo json_encode([
                        "ok" => false,
                        "message" => "No se encontró un ID"
                    ]);
                    return;
                }

                $cantidad = isset($_REQUEST['cantidad']) ? $_REQUEST['cantidad'] : '0';

                $precio = isset($_REQUEST['precio']) ? $_REQUEST['precio'] : '0';

                $query = "update n_detalle_movimiento_almacen 
                    set cantidad = $cantidad,
                        precio = $precio,
                        updated_at = now()

                        where id = $id";
                
                $db->executeQueryEx($query);

                // $query = "update insumos set precio_promedio = $precio where pkInsumo = (
                    // select insumo_id from n_detalle_movimiento_almacen where id = $id limit 1
                // ) ";

                // $db->executeQueryEx($query);
                
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

    public function _deleteDetalleIngreso()
    {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                
                $db = new SuperDataBase();

                $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 'null';

                $query = "update n_detalle_movimiento_almacen set deleted_at = now() where id = $id";

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

    public function _createSalida()
    {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                
                $trabajador_id = UserLogin::get_idTrabajador();

                $db = new SuperDataBase();

                $obj = new Application_Models_CajaModel();
                
                $fecha = $obj->fechaCierre();

                $numero_comprobante = isset($_REQUEST['txtNroComprobante']) ? $_REQUEST['txtNroComprobante'] : '';

                $tipo = 2;

                $query = "insert into n_movimiento_almacen (tipo_comprobante_id, numero_comprobante, fecha, trabajador_id, tipo, created_at) values ";

                $query .= "(4, '$numero_comprobante', '$fecha', $trabajador_id, $tipo, now())"; 
                
                $db->executeQueryEx($query);
                
                echo json_encode([
                    "ok" => true,
                    "id" => $db->getId()
                ]);

            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    public function _editSalida()
    {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {

                $trabajador_id = UserLogin::get_idTrabajador();

                $db = new SuperDataBase();

                $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 'null';

                if ($id == 'null') {
                    echo json_encode([
                        "ok" => false,
                        "message" => "No se encontró un ID"
                    ]);
                    return;
                }

                $numero_comprobante = isset($_REQUEST['txtNroComprobante']) ? $_REQUEST['txtNroComprobante'] : '';

                $query = "update n_movimiento_almacen 
                    set numero_comprobante = '$numero_comprobante',
                        updated_at = now()

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

    public function _createDetalleSalida()
    {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                
                $trabajador_id = UserLogin::get_idTrabajador();

                $db = new SuperDataBase();

                $movimiento_id = isset($_REQUEST['movimiento_id']) ? $_REQUEST['movimiento_id'] : 'null';

                $insumo_id = isset($_REQUEST['txtingreseInsumo']) ? $_REQUEST['txtingreseInsumo'] : 'null';

                if (!$insumo_id) {
                    echo json_encode([
                        "ok" => false,
                        "message" => "El insumo es requerido"
                    ]);
                    return;
                }

                $insumo_porcion_id = isset($_REQUEST['txtInsumoPorcion']) && $_REQUEST['txtInsumoPorcion'] ? $_REQUEST['txtInsumoPorcion'] : 'null';

                $unidad_id = isset($_REQUEST['unidad_id']) ? $_REQUEST['unidad_id'] : 'null';

                $cantidad = isset($_REQUEST['cantidad']) ? $_REQUEST['cantidad'] : '0';

                $precio = isset($_REQUEST['precio']) ? $_REQUEST['precio'] : '0';

                $almacen_id = isset($_REQUEST['id_almacen']) ? $_REQUEST['id_almacen'] : 'null';

                $motivo = isset($_REQUEST['motivo']) ? $_REQUEST['motivo'] : '';

                $obj = new Application_Models_CajaModel();
                
                $fecha = $obj->fechaCierre() . date(' H:i:s');

                $query = "insert into n_detalle_movimiento_almacen (tipo, movimiento_id, insumo_id, insumo_porcion_id, unidad_id, cantidad, precio, fecha, almacen_id, motivo, created_at) values ";

                $query .= "(2, $movimiento_id, $insumo_id, $insumo_porcion_id, $unidad_id, $cantidad, $precio, '$fecha', $almacen_id, '$motivo', now())";
                
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

    public function _editDetalleSalida()
    {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {

                $trabajador_id = UserLogin::get_idTrabajador();

                $db = new SuperDataBase();

                $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 'null';

                if ($id == 'null') {
                    echo json_encode([
                        "ok" => false,
                        "message" => "No se encontró un ID"
                    ]);
                    return;
                }

                $cantidad = isset($_REQUEST['cantidad']) ? $_REQUEST['cantidad'] : '0';

                $precio = isset($_REQUEST['precio']) ? $_REQUEST['precio'] : '0';

                $motivo = isset($_REQUEST['motivo']) ? mb_strtoupper($_REQUEST['motivo']) : '';

                $query = "update n_detalle_movimiento_almacen 
                    set cantidad = $cantidad,
                        precio = $precio,
                        motivo = '$motivo',
                        updated_at = now()

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

    public function _sendPrint()
    {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {

                $trabajador_id = UserLogin::get_idTrabajador();

                $db = new SuperDataBase();

                $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 'null';

                $terminal = $_COOKIE['t'];
                
                $query = "insert into cola_impresion (codigo, tipo, terminal, aux, estado) values ";

                $query .= "($id, 'GUI', '$terminal', null, 0)";
                
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