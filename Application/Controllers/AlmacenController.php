<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Controllers_AlmacenController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {
            case 'AdmInsumoAction':
                $this->VistaAdministrarInsumo();
                break;
            case 'ResumenISInsumosAction':
                $objView = new Application_Views_AlmacenView();
                $objView->showResumenISInsumos();
                break;
            case 'FacturaProductAction':
                $this->VistaFacturaProducto();
                break;
            case 'ListProductAction':
                $this->ListadoProdutos();
                break;
            case 'ShowIngresarProductosAction':
                $this->showIngresaProductos();
                break;
            case 'ShowAdministrarInsumoAction':
                $this->showAdministrarInsumo();
                break;
            case 'ShowAdministrarProductoFacturaAction':
                $this->showAdministrarProductoFactura();
                break;
            case 'AddInsumoAction':
                $view = new Application_Views_AlmacenView();
                $view->showIngresoInsumos();
                break;
            case 'AdmRecetaAction':
                $view = new Application_Views_AlmacenView();
                $view->showAdmRecetas();
                break;
            case 'InOutInsumosAction':
                $view = new Application_Views_AlmacenView();
                $view->showIngresoSalidaInsumo();
                break;
            case 'ResumenISProductosAction':
                $view = new Application_Views_AlmacenView();
                $view->showResumenISProductos();
                break;
            case 'ShowAdminProveedorAction':
                $this->_showAdminProveedor();
                break;
            case 'AdminGuiasAction':
                $this->_showAdminGuias();
                break;
            case 'AdminDetalleGuiasAction':
                $this->_showAdminDetalleGuias();
                break;
            case 'AdminGuiaSalidaAction':
                $this->_showAdminGuiaSalida();
                break;
            case 'AdminDetalleGuiaSalidaAction':
                $this->_showAdminDetalleGuiaSalida();
                break;
            case 'SaveUnidadAction':
                $this->_SaveUnidad();
                break;
            case 'EditUnidadAction':
                $this->_EditUnidad();
                break;
            case 'DeleteUnidadAction':
                $this->_DeleteUnidad();
                break;
            case 'ActiveUnidadAction':
                $this->_ActivarUnidad();
                break;
            case 'SaveTipoInsumoAction':
                $this->_SaveTipoInsumo();
                break;
            case 'EditTipoInsumoAction':
                $this->_EditTipoInsumo();
                break;
            case 'DeleteTipoInsumoAction':
                $this->_DeleteTipoInsumo();
                break;
            case 'ActiveTipoInsumoAction':
                $this->_ActivarTipoInsumo();
                break;
            case 'AdminUnidadesAction':
                $this->showAdminUnidades();
                break;
            case 'AdminTipoInsumoAction':
                $this->showTipoInsumo();
                break;
            case 'ListInsumoAction':
                $this->_listInsumo();
                break;
            case 'ListAlmacenAction':
                $this->_listAlmacen();
                break;
            case 'AdmInsumoPorcionAction':
                $this->_showViewInsumoPorcion();
                break;
            case 'ListInsumoPorcionAction':
                $this->_ListInsumoPorcion();
                break;
            case 'AdmAddInsumoPorcionAction':
                $this->_addInsumoPorcion();
                break;
            case 'AdmDeleteInsumoPorcionAction':
                $this->_deleteInsumoPorcion();
                break;

            case 'ShowTransferenciaAction':
                require_once 'Application/Views/Almacen/AlmacenTransferencia.php';
                break;

            case 'ShowAlmacenesAction':
                require_once 'Application/Views/Almacen/AdministrarAlmacenes.php';
                break;

            case 'ShowComprasAction':
                require_once 'Application/Views/Compras/Compras.php';
                break;

            case 'showKardexAction':
                require_once 'Application/Views/Reportes/NKardex.php';
                break;

            case 'showKardexDetalladoAction':
                require_once 'Application/Views/Reportes/NKardexDetallado.php';
                break;

            case 'showKardexDetallado2Action':
                require_once 'Application/Views/Reportes/NKardexDetallado2.php';
                break;

            case 'StocksAction':
                echo '<meta http-equiv="refresh" content="0; url=reportes/stocks.php" />';
                break;
        }
    }

    private function _showViewInsumoPorcion() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Views_AlmacenView();
                $obj->showViewInsumoPorcion();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    private function _deleteInsumoPorcion() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                
                $db = new SuperDataBase();

                $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 'null';
                
                $query = "update insumo_porcion set deleted_at = now() where id = $id";

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
    private function _addInsumoPorcion() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {

                $obj = new Application_Models_CajaModel();

                $fecha = $obj->fechaCierre();

                $pkTrabajador = UserLogin::get_idTrabajador();
                
                $db = new SuperDataBase();

                $insumo_id = isset($_REQUEST['insumo_id']) ? $_REQUEST['insumo_id'] : 'null';

                $unidad_id = isset($_REQUEST['unidad_id']) ? $_REQUEST['unidad_id'] : 'null';

                $cantidad = isset($_REQUEST['cantidad']) ? $_REQUEST['cantidad'] : 0;

                $descripcion = isset($_REQUEST['descripcion']) ? strtoupper($_REQUEST['descripcion']) : '';

                $valor = isset($_REQUEST['valor']) ? $_REQUEST['valor'] : 0;

                $query = "insert into insumo_porcion (insumo_id, unidad_id, cantidad, valor, descripcion) values";

                $query .= "($insumo_id, $unidad_id, $cantidad, $valor, '$descripcion')";

                $res = $db->executeQueryEx($query);

                $query = "INSERT INTO n_historial_stock_insumo ( insumo_id, insumo_porcion_id, almacen_id, stock_inicial, stock_final, fecha, trabajador_id, created_at)
                    SELECT $insumo_id , " . $db->getId() . ", n_almacen.id, 0, 0, '$fecha', $pkTrabajador, now() from n_almacen";
                
                $db->executeQuery($query);

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
    private function _ListInsumoPorcion() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                
                $db = new SuperDataBase();

                $insumo_id = isset($_REQUEST['insumo_id']) ? $_REQUEST['insumo_id'] : 'null';

                $query = "
                    SELECT
                        i.*,
                        u.descripcion as unidad
                    FROM
                        insumo_porcion i
                    LEFT JOIN unidad u ON u.pkUnidad = i.unidad_id
                    WHERE
                        insumo_id = $insumo_id and i.deleted_at is null
                ";

                $res = $db->executeQueryEx($query);

                $data = [];
                
                while ($row = $db->fecth_array($res)) {

                    $data[] = [
                        "id" => $row['id'],
                        "descripcion" => implode(' ', [
                            floatval($row['cantidad']),
                            $row['unidad'],
                            $row['descripcion'],
                        ])
                    ];
                }

                echo json_encode($data);

            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function VistaAdministrarInsumo() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Views_AlmacenView();
                $obj->showAdministrarInsumo();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function ListadoProdutos() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_ProductosModel();
                $obj->listProduct();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function showIngresaProductos() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Views_AlmacenView();
                $obj->showIngresarProductos();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function VistaFacturaProducto() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Views_AlmacenView();
                $obj->showProductoFactura();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function showAdministrarInsumo() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Views_AlmacenView();
                $obj->showAdministrarInsumo();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function showAdministrarProductoFactura() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Views_AlmacenView();
                $obj->showProductoFactura();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showAdminProveedor() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objViewProveedor = new Application_Views_AlmacenView();
                $objViewProveedor->showAdminProveedor();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showAdminGuias() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objViewProveedor = new Application_Views_AlmacenView();
                $objViewProveedor->showAdminGuias();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
     private function showTipoInsumo() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objViewProveedor = new Application_Views_AlmacenView();
                $objViewProveedor->showTipoInsumo();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showAdminDetalleGuias() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objViewProveedor = new Application_Views_AlmacenView();
                $objViewProveedor->showAdminDetalleGuias();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
    private function _showAdminDetalleGuiaSalida() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objViewProveedor = new Application_Views_AlmacenView();
                $objViewProveedor->showAdminDetalleGuiaSalida();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    private function showAdminUnidades() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objViewProveedor = new Application_Views_AlmacenView();
                $objViewProveedor->showAdminUnidades();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
     private function _SaveUnidad() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objViewAlmacen = new Application_Models_UnidadModel();
                $objViewAlmacen->SaveUnidades($_REQUEST['descripcion']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
    private function _EditUnidad() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objViewAlmacen = new Application_Models_UnidadModel();
                $objViewAlmacen->EditUnidades($_REQUEST['id'],$_REQUEST['descripcion']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
    private function _DeleteUnidad() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objViewAlmacen = new Application_Models_UnidadModel();
                $objViewAlmacen->DeleteUnidades($_REQUEST['id']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
    private function _ActivarUnidad() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objViewAlmacen = new Application_Models_UnidadModel();
                $objViewAlmacen->ActivarUnidad($_REQUEST['id']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
      private function _SaveTipoInsumo() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objViewAlmacen = new Application_Models_TipoInsumoModel();
                $objViewAlmacen->SaveTipoInsumo($_REQUEST['descripcion']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
    private function _EditTipoInsumo() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objViewAlmacen = new Application_Models_TipoInsumoModel();
                $objViewAlmacen->EditTipoInsumo($_REQUEST['id'],$_REQUEST['descripcion']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
    private function _DeleteTipoInsumo() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objViewAlmacen = new Application_Models_TipoInsumoModel();
                $objViewAlmacen->DeleteTipoInsumo($_REQUEST['id']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
    private function _ActivarTipoInsumo() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objViewAlmacen = new Application_Models_TipoInsumoModel();
                $objViewAlmacen->ActivarTipoInsumo($_REQUEST['id']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showAdminGuiaSalida() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objViewGuia = new Application_Views_AlmacenView();
                $objViewGuia->showAdminGuiaSalida();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
     private function _listInsumo() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelProvedor = new Application_Models_UnidadModel();
                $objModelProvedor->_listinsumo();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
     private function _listAlmacen() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
            
                $db = new SuperDataBase();

                $query = "select * from n_almacen";

                $res = $db->executeQuery($query);

                $data = [];

                while($row = $db->fecth_array($res)) {
                    $data[] = [
                        "id" => $row['id'],
                        "descripcion" => $row['nombre'],
                    ];
                }

                echo json_encode($data);

            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

}
