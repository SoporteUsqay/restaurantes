<?php

class Application_Controllers_FondosExternosController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {
           
            case 'SaveAction':
                $this->Save();
                break;
        }
    }

    private function Save() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                
                $totales = [];

                foreach ($_REQUEST as $index => $value) {
                    if (substr($index, 0, 5) == "totFE" && $value > 0) {
                        $arr = explode(_, $index);
                        if (count($arr) == 3) {
                            $totales[] = [
                                "medio_pago" => $arr[1],
                                "moneda" => $arr[2],
                                "total" => $value
                            ];
                        }

                    }
                }

                $caja = isset($_REQUEST['caja']) && $_REQUEST['caja'] ? $_REQUEST['caja'] : 'Todas';

                $fecha = isset($_REQUEST['fecha']) && $_REQUEST['fecha'] ? $_REQUEST['fecha'] : null;

                $fechaDia = isset($_REQUEST['fechaDia']) && $_REQUEST['fechaDia'] ? $_REQUEST['fechaDia'] : null;

                $trabajador_id = UserLogin::get_idTrabajador();

                if (!$fecha) {
                    $obj = new Application_Models_CajaModel();
                    $fecha = $obj->fechaCierre(); 
                }

                $query_movimiento_dinero = "insert into movimiento_dinero_fe (id_origen, tipo_origen, monto, id_medio, moneda, fecha_cierre, fecha_hora, id_usuario, caja, comentario, estado) values ";

                $query_movimiento_dinero_items = [];

                foreach ($totales as $total) {

                    $query_movimiento_dinero_items[] = " (1, 'CC', ${total['total']}, ${total['medio_pago']}, ${total['moneda']}, '$fecha', now(), $trabajador_id, 'FE', '$fechaDia, DESDE $caja',  1) ";
                }

                $query_movimiento_dinero .= implode(' , ', $query_movimiento_dinero_items);

                
                $db = new SuperDataBase();

                try {

                    $db->executeQueryEx('SET AUTOCOMMIT=0');
                    $db->executeQueryEx('START TRANSACTION');

                    if (count($query_movimiento_dinero_items) > 0) {
                        $db->executeQueryEx($query_movimiento_dinero);
                    }

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

}
