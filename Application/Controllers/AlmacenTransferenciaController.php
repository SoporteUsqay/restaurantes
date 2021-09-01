<?php

class Application_Controllers_AlmacenTransferenciaController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {

            case 'ShowAction':
                require_once 'Application/Views/Almacen/AlmacenTransferencia.php';
                break;

            case 'AddIngresoAction':
                $this->_createIngreso();
                break;

            case 'AddDetalleIngresoAction':
                $this->_createDetalleIngreso();
                break;
            case 'DeleteDetalleIngresoAction':
                $this->_deleteDetalleIngreso();
                break;

            
            case 'ShowDetailAction':
                require_once 'Application/Views/Almacen/AlmacenTransferenciaDetail.php';
                break;

            case 'AddTransferenciaInsumoAction':
                $this->_addTransferenciaInsumo();
                break;
            case 'AddTransferenciaMermaAction':
                $this->_addMermaInsumo();
                break;
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

                $fecha = "now()";

                $almacen_id = isset($_REQUEST['txtAlmacen']) ? $_REQUEST['txtAlmacen'] : 'null';

                $proveedor_id = isset($_REQUEST['id_proveedor']) && $_REQUEST['id_proveedor'] != '' ? $_REQUEST['id_proveedor'] : 'null';

                $tipo = 1;

                $query = "insert into n_movimiento_almacen (tipo_comprobante_id, numero_comprobante, fecha, almacen_id, proveedor_id, trabajador_id, tipo, created_at) values ";

                $query .= "($tipo_comprobante_id, '$numero_comprobante', $fecha, $almacen_id, $proveedor_id, $trabajador_id, $tipo, now())"; 
                
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

    public function _addTransferenciaInsumo()
    {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                
                $trabajador_id = UserLogin::get_idTrabajador();

                $db = new SuperDataBase();

                $insumo_id = isset($_REQUEST['insumo_id']) ? $_REQUEST['insumo_id'] : 'null';

                if (!$insumo_id) {
                    echo json_encode([
                        "ok" => false,
                        "message" => "El insumo es requerido"
                    ]);
                    return;
                }

                $insumo_porcion_id = isset($_REQUEST['insumo_porcion_id']) && $_REQUEST['insumo_porcion_id'] ? $_REQUEST['insumo_porcion_id'] : 'null';

                $insumo_porcion_destino_id = (isset($_REQUEST['insumo_porcion_destino_id']) && $_REQUEST['insumo_porcion_destino_id']) ? $_REQUEST['insumo_porcion_destino_id'] : 'null';

                if ($insumo_porcion_destino_id == 'null') {
                    $insumo_porcion_destino_id = $insumo_porcion_id;
                }

                $cantidad = isset($_REQUEST['cantidad']) ? $_REQUEST['cantidad'] : '0';

                $valor = isset($_REQUEST['valor']) ? $_REQUEST['valor'] : '1';

                $almacen_origen_id = isset($_REQUEST['almacen_origen_id']) ? $_REQUEST['almacen_origen_id'] : 'null';

                $almacen_destino_id = isset($_REQUEST['almacen_destino_id']) ? $_REQUEST['almacen_destino_id'] : 'null';

                $obj = new Application_Models_CajaModel();
                
                $fecha = $obj->fechaCierre() . date(' H:i:s');

                try {

                    $db->executeQueryEx('SET AUTOCOMMIT=0');
                    $db->executeQueryEx('START TRANSACTION');

                    $cantidad_salida = $cantidad * $valor;
                    
                    $query = "insert into n_detalle_movimiento_almacen (tipo, insumo_id, insumo_porcion_id, cantidad, fecha, almacen_id, motivo, created_at) values ";

                    $query .= "(2, $insumo_id, $insumo_porcion_id, $cantidad_salida, '$fecha', $almacen_origen_id, 'TRANSFERENCIA', now())";
                    
                    $db->executeQueryEx($query);

                    $query = "insert into n_detalle_movimiento_almacen (tipo, insumo_id, insumo_porcion_id, cantidad, fecha, almacen_id, motivo, created_at) values ";

                    $query .= "(1, $insumo_id, $insumo_porcion_destino_id, $cantidad, '$fecha', $almacen_destino_id, 'TRANSFERENCIA', now())";
                    
                    $db->executeQueryEx($query);
                    
                    $db->executeQueryEx('COMMIT');
        
                } catch (Exception $e) {
                    $db->executeQueryEx('ROLLBACK');
                    echo json_encode([
                        "ok" => false
                    ]);
                    return;
                } finally {
                    $db->executeQueryEx('SET AUTOCOMMIT=1');
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

    public function _addMermaInsumo()
    {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                
                $trabajador_id = UserLogin::get_idTrabajador();

                $db = new SuperDataBase();

                $insumo_id = isset($_REQUEST['insumo_id']) ? $_REQUEST['insumo_id'] : 'null';

                if (!$insumo_id) {
                    echo json_encode([
                        "ok" => false,
                        "message" => "El insumo es requerido"
                    ]);
                    return;
                }

                $insumo_porcion_id = isset($_REQUEST['insumo_porcion_id']) ? $_REQUEST['insumo_porcion_id'] : 'null';

                $cantidad = isset($_REQUEST['cantidad']) ? $_REQUEST['cantidad'] : '0';

                $almacen_origen_id = isset($_REQUEST['almacen_origen_id']) ? $_REQUEST['almacen_origen_id'] : 'null';

                $obj = new Application_Models_CajaModel();
                
                $fecha = $obj->fechaCierre() . date(' H:i:s');

                try {

                    $db->executeQueryEx('SET AUTOCOMMIT=0');
                    $db->executeQueryEx('START TRANSACTION');
                    
                    $query = "insert into n_detalle_movimiento_almacen (tipo, insumo_id, insumo_porcion_id, cantidad, fecha, almacen_id, motivo, created_at) values ";

                    $query .= "(2, $insumo_id, $insumo_porcion_id, $cantidad, '$fecha', $almacen_origen_id, 'MERMA',now())";
                    
                    $db->executeQueryEx($query);

                    $db->executeQueryEx('COMMIT');
        
                } catch (Exception $e) {
                    $db->executeQueryEx('ROLLBACK');
                    echo json_encode([
                        "ok" => false
                    ]);
                    return;
                } finally {
                    $db->executeQueryEx('SET AUTOCOMMIT=1');
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