<?php

class Application_Controllers_ComprasController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {

            case 'ShowAction':
                require_once 'Application/Views/Compras/Compras.php';
                break;
            case 'ShowDetailsAction':
                require_once 'Application/Views/Compras/ComprasDetails.php';
                break;

            case 'AddAction':
                $this->_add();
                break;

            case 'EditAction':
                $this->_edit();
                break;

            case 'DeleteAction':
                $this->_delete();
                break;

            case 'AddDetailAction':
                $this->_addDetail();
                break;

            case 'EditDetailAction':
                $this->_editDetail();
                break;

            case 'DeleteDetailAction':
                $this->_DeleteDetail();
                break;

            case 'GetCuotasAction':
                $this->_GetCuotas();
                break;

            case 'GetConceptosAction':
                $this->_GetConceptos();
                break;

            case 'AddConceptoAction':
                $this->_AddConcepto();
                break;
        }
    }

    public function _add()
    {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
            
                $trabajador_id = UserLogin::get_idTrabajador();

                $db = new SuperDataBase();

                $tipo_documento_id = isset($_REQUEST['documento_id']) ? $_REQUEST['documento_id'] : 'null';

                $serie = isset($_REQUEST['serie']) ? $_REQUEST['serie'] : "";
                
                $correlativo = isset($_REQUEST['correlativo']) ? $_REQUEST['correlativo'] : '';

                $proveedor_id = isset($_REQUEST['proveedor_id']) && $_REQUEST['proveedor_id'] ? $_REQUEST['proveedor_id'] : 'null';

                $fecha = isset($_REQUEST['fecha']) ? $_REQUEST['fecha'] : '';

                $observaciones = isset($_REQUEST['observaciones']) ? $_REQUEST['observaciones'] : '';

                // $obj = new Application_Models_CajaModel();
                
                // $fecha = $obj->fechaCierre() . date(' H:i:s');

                if ($tipo_documento_id != 5) {
                    $query = "select fecha, 1 from compras 
                        where tipo_documento_id = $tipo_documento_id and 
                            serie = '$serie' and correlativo = '$correlativo' and 
                            ";

                    if ($proveedor_id == 'null') {
                        $query .= "proveedor_id is null
                            limit 1";
                    } else {
                        $query .= "proveedor_id = $proveedor_id
                            limit 1";
                    }

                    $res = $db->executeQueryEx($query);

                    while ($row = $db->fecth_array($res)) {
                        echo json_encode([
                            "ok" => false,
                            "message" => "El comprobante ya ha sido previamente registrado con fecha " . $row['fecha']
                        ]);
                        return;
                    }
                }

                $porcentaje_igv = Class_config::get('igv');

                $tasa_icbper = 0;
                $query_icbper = "Select * from cloud_config where parametro = 'icbper' limit 1";
                $res1 = $db->executeQueryEx($query_icbper);
                if($row1 = $db->fecth_array($res1)){
                    $tasa_icbper = floatval($row1["valor"]);
                }
                    
                $query = "insert into compras (tipo_documento_id, serie, correlativo, proveedor_id, moneda_id, fecha, trabajador_id, porcentaje_igv, tasa_icbper, observaciones, created_at) values ";

                $query .= "($tipo_documento_id, '$serie', '$correlativo', $proveedor_id, 1, '$fecha', $trabajador_id, $porcentaje_igv, $tasa_icbper, '$observaciones', now())";
                
                $db->executeQueryEx($query);
                
                echo json_encode([
                    "ok" => true,
                    "id" => $db->getId(),
                ]);

            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    public function _edit()
    {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
            
                $trabajador_id = UserLogin::get_idTrabajador();

                $db = new SuperDataBase();

                $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 'null';

                $tipo_documento_id = isset($_REQUEST['documento_id']) ? $_REQUEST['documento_id'] : 'null';

                $serie = isset($_REQUEST['serie']) ? $_REQUEST['serie'] : "";
                
                $correlativo = isset($_REQUEST['correlativo']) ? $_REQUEST['correlativo'] : '';

                $proveedor_id = isset($_REQUEST['proveedor_id']) && $_REQUEST['proveedor_id'] ? $_REQUEST['proveedor_id'] : 'null';

                $fecha = isset($_REQUEST['fecha']) ? $_REQUEST['fecha'] : '';

                $observaciones = isset($_REQUEST['observaciones']) ? $_REQUEST['observaciones'] : '';

                if ($tipo_documento_id != 5) {
                    $query = "select fecha, 1 from compras 
                        where 
                            id != $id and
                            tipo_documento_id = $tipo_documento_id and 
                            serie = '$serie' and correlativo = '$correlativo' and 
                            ";

                    if ($proveedor_id == 'null') {
                        $query .= "proveedor_id is null
                            limit 1";
                    } else {
                        $query .= "proveedor_id = $proveedor_id
                            limit 1";
                    }

                    $res = $db->executeQueryEx($query);

                    while ($row = $db->fecth_array($res)) {
                        echo json_encode([
                            "ok" => false,
                            "message" => "El comprobante ya ha sido previamente registrado con fecha " . $row['fecha']
                        ]);
                        return;
                    }
                }
            
                $query = "insert into compras (tipo_documento_id, serie, correlativo, proveedor_id, moneda_id, fecha, trabajador_id, observaciones, created_at) values ";

                $query .= "($tipo_documento_id, '$serie', '$correlativo', $proveedor_id, 1, '$fecha', $trabajador_id, $porcentaje_igv, $tasa_icbper, '$observaciones', now())";

                $query = "UPDATE compras
                    SET
                        tipo_documento_id = $tipo_documento_id,
                        serie = '$serie',
                        correlativo = '$correlativo',
                        proveedor_id = $proveedor_id,
                        fecha = '$fecha',
                        observaciones = '$observaciones',
                        trabajador_id = $trabajador_id,
                        updated_at = now()
                    WHERE 
                        id = $id";
                
                $db->executeQueryEx($query);
                
                echo json_encode([
                    "ok" => true,
                    "id" => $id,
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

                $query = "update compras set 
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

    public function _addDetail()
    {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
            
                $trabajador_id = UserLogin::get_idTrabajador();

                $db = new SuperDataBase();

                $compra_id = isset($_REQUEST['compra_id']) ? $_REQUEST['compra_id'] : 'null';

                $insumo_id = isset($_REQUEST['insumo_id']) && $_REQUEST['insumo_id'] ? $_REQUEST['insumo_id'] : 'null';

                $descripcion_id = isset($_REQUEST['descripcion_id']) ? $_REQUEST['descripcion_id'] : 'null';

                if ($_REQUEST['tipo_concepto'] == 1) {
                    $descripcion_id = 'null';
                } else if ($_REQUEST['tipo_concepto'] == 2) {
                    $insumo_id = 'null';
                }
                
                $tipo_impuesto_id = isset($_REQUEST['tipo_impuesto_id']) && $_REQUEST['tipo_impuesto_id'] ? $_REQUEST['tipo_impuesto_id'] : 'null';
                
                $cantidad = isset($_REQUEST['cantidad']) ? $_REQUEST['cantidad'] : '0';
                
                $precio = isset($_REQUEST['precio']) ? $_REQUEST['precio'] : '0';

                $subtotal = $cantidad * $precio;

                $igv = 0;

                $descuento = isset($_REQUEST['descuento']) ? $_REQUEST['descuento'] : '0';

                $total = $subtotal - $descuento;

                $query = "insert into detalle_compras (compra_id, insumo_id, concepto_id, tipo_impuesto_id, cantidad, precio, subtotal, igv, descuento, total, created_at) values ";

                $query .= "($compra_id, $insumo_id, $descripcion_id, $tipo_impuesto_id, $cantidad, $precio, $subtotal, $igv, $descuento, $total, now())";
                
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

    public function _editDetail()
    {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
            
                $db = new SuperDataBase();

                $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 'null';

                // $tipo_impuesto_id = isset($_REQUEST['tipo_impuesto_id']) && $_REQUEST['tipo_impuesto_id'] ? $_REQUEST['tipo_impuesto_id'] : 'null';
                
                $cantidad = isset($_REQUEST['cantidad']) ? $_REQUEST['cantidad'] : '0';
                
                $precio = isset($_REQUEST['precio']) ? $_REQUEST['precio'] : '0';

                $subtotal = $cantidad * $precio;

                $igv = 0;

                $descuento = isset($_REQUEST['descuento']) ? $_REQUEST['descuento'] : '0';

                $total = $subtotal - $descuento;

                $query = "UPDATE detalle_compras
                    SET
                        cantidad = $cantidad,
                        precio = $precio,
                        subtotal = $subtotal,
                        descuento = $descuento,
                        total = $total
                    
                    WHERE 
                        id = $id
                ";

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

    public function _DeleteDetail()
    {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
            
                $db = new SuperDataBase();

                $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 'null';

                $query = "update detalle_compras set 
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

    public function _GetCuotas()
    {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
            
                $db = new SuperDataBase();

                $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 'null';

                $query = "SELECT 
                        compras.*,
                        moneda.simbolo as moneda
                    FROM compras
                    LEFT JOIN moneda ON compras.moneda_id = moneda.id
                    WHERE compras.id = $id";

                $res = $db->executeQueryEx($query);
                
                $compra = [];

                while ($row = $db->fecth_array($res)) {
                    $compra = $row;
                }

                $query = "SELECT 
                        * 
                    FROM compras_cuotas
                    WHERE compra_id = $id";

                $res = $db->executeQueryEx($query);
                
                $data = [];

                while ($row = $db->fecth_array($res)) {
                    $row['moneda'] = $compra['moneda'];
                    $data[] = $row;
                }

                echo json_encode([
                    "ok" => true,
                    "lista" => $data
                ]);

            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    public function _GetConceptos() 
    {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
            
                $db = new SuperDataBase();

                $query = "SELECT * from compras_concepto ";

                $res = $db->executeQueryEx($query);
                
                $lista = [];

                while ($row = $db->fecth_array($res)) {
                    $lista[] = $row;
                }

                echo json_encode($lista);

            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    public function _AddConcepto()
    {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
            
                $db = new SuperDataBase();

                $nombre = isset($_REQUEST['nombre']) ? $_REQUEST['nombre'] : '';
                
                $precio = isset($_REQUEST['precio']) ? $_REQUEST['precio'] : '0';

                $query = "insert into compras_concepto (nombre, precio) values ";

                $query .= "('$nombre', $precio)";
                
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
}