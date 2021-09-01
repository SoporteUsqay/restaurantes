<?php

require_once 'Application/Views/Compras/ComprasHelper.php';

class Application_Controllers_ComprasCajaController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {

            case 'AddCajaAction':
                $this->_add();
                break;

            case 'AddCuotaCajaAction':
                $this->_addCuota();
                break;

            case 'AddCierreCajaFEAction':
                $this->_addFondosExternosCierre();
                break;
        }
    }

    public function _add()
    {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {

                $obj = new Application_Models_CajaModel();
                $fechaCierreCaja = $obj->fechaCierre(); 
            
                $trabajador_id = UserLogin::get_idTrabajador();

                $compra_id = isset($_REQUEST['compra_id']) ? $_REQUEST['compra_id'] : 'null';

                $db = new SuperDataBase();

                $query = "select * from compras where id = $compra_id limit 1";

                $res = $db->executeQueryEx($query);

                $main_compra = null;

                while ($row = $db->fecth_array($res)) {
                    $main_compra = $row;
                }

                if (is_null($main_compra)) {
                    echo json_encode([
                        "ok" => false,
                        "message" => "La compra con id: $compra_id no puede ser ingresada a caja."
                    ]);
                    return;
                }

                if (!is_null($main_compra['fecha_caja'])) {
                    echo json_encode([
                        "ok" => false,
                        "message" => "La compra con id: $compra_id no puede ser ingresada a caja, porque ya ha sido agregada con fecha: ${main_compra['fecha_caja']}."
                    ]);
                    return;
                }

                $medio_pago = isset($_REQUEST['medio_pago']) ? $_REQUEST['medio_pago'] : '1';

                $tipo_pago = isset($_REQUEST['tipo_pago']) ? $_REQUEST['tipo_pago'] : 0;

                if ($tipo_pago != 1 && $tipo_pago != 2) {
                    echo json_encode([
                        "ok" => false,
                        "message" => "El tipo de pago $tipo_pago no se encontró"
                    ]);
                    return;
                }

                $query_movimiento_dinero = "insert into movimiento_dinero (id_origen, tipo_origen, monto, id_medio, moneda, fecha_cierre, fecha_hora, id_usuario, caja, comentario, estado) values ";

                $query_movimiento_dinero_fe = "insert into movimiento_dinero_fe (id_origen, tipo_origen, monto, id_medio, moneda, fecha_cierre, fecha_hora, id_usuario, caja, comentario, estado) values ";

                $items_query_movimiento_dinero = [];

                $items_query_movimiento_dinero_fe = [];

                $comprasHelper = new ComprasHelper();

                $comprasHelper->setIGV($main_compra['porcentaje_igv']);
                $comprasHelper->setICBPER($main_compra['tasa_icbper']);

                $totales = $comprasHelper->calculateTotales($compra_id);

                if ($tipo_pago == 1) {

                    $caja = isset($_REQUEST['caja']) ? $_REQUEST['caja'] : 'null';

                    if ($caja == 'FE') {
                        $items_query_movimiento_dinero_fe[] = " ($compra_id, 'COM', -${totales['total']}, $medio_pago, ${main_compra['moneda_id']}, '$fechaCierreCaja', now(), $trabajador_id, '$caja', null, 1) ";
                    } else {
                        $items_query_movimiento_dinero[] = " ($compra_id, 'COM', -${totales['total']}, $medio_pago, ${main_compra['moneda_id']}, '$fechaCierreCaja', now(), $trabajador_id, '$caja', null, 1) ";
                    }


                } else if ($tipo_pago == 2) {
                    
                    $cuotas = isset($_REQUEST['cuotas']) ? $_REQUEST['cuotas'] : [];
                    
                    if (count($cuotas) == 0) {
                        echo json_encode([
                            "ok" => false,
                            "message" => "Las cuotas no pueden estar vacías"
                            ]);
                            return;
                        }
                        
                        $query_cuotas = "insert into compras_cuotas (compra_id, fecha, total, fecha_caja, created_at) values ";
                        
                    foreach ($cuotas as $index => $cuota) {
                        
                        $fecha_caja = 'null';
                        
                        if ($cuota['pago_efectuado'] === "true") {

                            $fecha_caja = "'$fechaCierreCaja'";
                            $caja = $cuota['caja'];
                            $medio_pago = $cuota['medio_pago'];

                            if ($caja == 'FE') {
                                $items_query_movimiento_dinero_fe[] = " ($compra_id, 'COM', -${cuota['total']}, $medio_pago, ${main_compra['moneda_id']}, '$fechaCierreCaja', now(), $trabajador_id, '$caja', 'CUOTA-INICIAL', 1) ";
                            } else {
                                $items_query_movimiento_dinero[] = " ($compra_id, 'COM', -${cuota['total']}, $medio_pago, ${main_compra['moneda_id']}, '$fechaCierreCaja', now(), $trabajador_id, '$caja', 'CUOTA-INICIAL', 1) ";
                            }
                        }

                        $query_cuotas .= "($compra_id, '${cuota['fecha']}', ${cuota['total']}, $fecha_caja, now()) " . (count($cuotas) - 1 != $index ? ' , ' : '');
                    }
                }

                $query_update = "UPDATE compras 
                    SET    
                        fecha_caja = '$fechaCierreCaja',
                        total = ${totales['total']}
                    WHERE
                        id = $compra_id
                ";

                $query_movimiento_dinero .= implode(' , ', $items_query_movimiento_dinero);
                $query_movimiento_dinero_fe .= implode(' , ', $items_query_movimiento_dinero_fe);

                try {

                    $db->executeQueryEx('SET AUTOCOMMIT=0');
                    $db->executeQueryEx('START TRANSACTION');

                    if (count($items_query_movimiento_dinero) > 0) {
                        $db->executeQueryEx($query_movimiento_dinero);
                    }

                    if (count($items_query_movimiento_dinero_fe) > 0) {
                        $db->executeQueryEx($query_movimiento_dinero_fe);
                    }

                    if ($query_cuotas) {
                        $db->executeQueryEx($query_cuotas);
                    }

                    $db->executeQueryEx($query_update);

                    $db->executeQueryEx('COMMIT');

                    echo json_encode([
                        "ok" => true,
                    ]);
        
                } catch (Exception $e) {
                    $db->executeQuery('ROLLBACK');
                } finally {
                    $db->executeQuery('SET AUTOCOMMIT=1');
                }

            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    public function _addCuota()
    {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {

                $obj = new Application_Models_CajaModel();
                $fechaCierreCaja = $obj->fechaCierre(); 
            
                $trabajador_id = UserLogin::get_idTrabajador();

                $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 'null';

                $caja = isset($_REQUEST['caja']) ? $_REQUEST['caja'] : 'null';

                $medio_pago = isset($_REQUEST['medio_pago']) ? $_REQUEST['medio_pago'] : 'null';

                $db = new SuperDataBase();

                $query = "select * from compras_cuotas where id = $id limit 1";

                $res = $db->executeQueryEx($query);

                $main_cuota = null;

                while ($row = $db->fecth_array($res)) {
                    $main_cuota = $row;
                }

                $query = "select * from compras where id = ${main_cuota['compra_id']} limit 1";

                $res = $db->executeQueryEx($query);

                $main_compra = null;

                while ($row = $db->fecth_array($res)) {
                    $main_compra = $row;
                }

                if (is_null($main_cuota)) {
                    echo json_encode([
                        "ok" => false,
                        "message" => "La cuota con id: $id no puede ser ingresada a caja."
                    ]);
                    return;
                }

                if (!is_null($main_cuota['fecha_caja'])) {
                    echo json_encode([
                        "ok" => false,
                        "message" => "La cuota con id: $id no puede ser ingresada a caja, porque ya ha sido agregada con fecha: ${main_cuota['fecha_caja']}."
                    ]);
                    return;
                }

                if ($caja == 'FE') {
                    $tabla = 'movimiento_dinero_fe';
                } else {
                    $tabla = 'movimiento_dinero';
                }

                $query_movimiento_dinero = "insert into $tabla (id_origen, tipo_origen, monto, id_medio, moneda, fecha_cierre, fecha_hora, id_usuario, caja, comentario, estado) values ";

                $query_movimiento_dinero .= " (${main_cuota['compra_id']}, 'COM', -${main_cuota['total']}, $medio_pago, ${main_compra['moneda_id']}, '$fechaCierreCaja', now(), $trabajador_id, '$caja', 'CUOTA-$id',  1) ";

                $query_update = "UPDATE compras_cuotas 
                    SET    
                        fecha_caja = '$fechaCierreCaja'
                    WHERE
                        id = $id
                ";

                try {

                    $db->executeQueryEx('SET AUTOCOMMIT=0');
                    $db->executeQueryEx('START TRANSACTION');

                    // echo $query_movimiento_dinero;
                    // echo $query_update;

                    $db->executeQueryEx($query_movimiento_dinero);
                    $db->executeQueryEx($query_update);

                    $db->executeQueryEx('COMMIT');

                    echo json_encode([
                        "ok" => true,
                    ]);
        
                } catch (Exception $e) {
                    $db->executeQuery('ROLLBACK');
                } finally { 
                    $db->executeQuery('SET AUTOCOMMIT=1');
                }

            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    public function _addFondosExternosCierre()
    {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {

               

            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
}