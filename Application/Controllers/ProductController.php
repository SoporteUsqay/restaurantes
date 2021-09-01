<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Controllers_ProductController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {

            case 'ListAction':
                $this->_listProduct();
                break;
            case 'addCantidad':
                $this->_addCantidad();
                break;
            case 'addCantidadProductosAction':
                $this->_addCantidadStockProducto();
                break;
            case 'ListProductProvedorAction':
                $this->_listProductProvedor();
                break;
            case 'FiltroProductosAction':
                $this->_filltroPoductos();
                break;
            
            case 'ShowAdminProductoAction':
                $this->_showAdminProducto();
                break;
            
            case 'BusquedaProductosAction':
                $this->_BusquedaProductos();
                break;
            
             case 'eliminarProductoAction':
                $this->_eliminarProducto();
                break;
            
            case 'addProductonuevoAction':
                $this->_addProductonuevo();
                break;
            
            case 'SaveproductoAction':
                $this->_saveproduct();
                break;
            
            case 'UpdateProductAction':
                $this->_updateProduct();
                break;
            
            case 'SaveAction':
                $this->_Save();
                break;
            
            case 'EditAction':
                $this->_Edit();
                break;
            
        }
    }
    
    private function _saveproduct() {
        $_descripcion = $_REQUEST['descripcion_producto'];
        $_pkTipo = $_REQUEST['pkTipoProducto'];
        $_precioVenta = $_REQUEST['precioVenta'];
        $_precioCompra = $_REQUEST['precioCompra'];
        $_stock = $_REQUEST['stock'];
        
        $objModelProducto = new Application_Models_ProductosModel();
        $objModelProducto->set_descripcion($_descripcion);
        $objModelProducto->set_pkTipo($_pkTipo);
        $objModelProducto->set_precioVenta($_precioVenta);
        $objModelProducto->set_precioCompra($_precioCompra);
        $objModelProducto->set_stock($_stock);
        
           $objModelProducto->_saveprodu();
    }
    
    
     private function _updateProduct() {
        $_descripcion = $_REQUEST['descripcion_producto'];
        $_pkTipo = $_REQUEST['pkTipocatg'];
        $_precioVenta = $_REQUEST['precioVenta'];
        $_precioCompra = $_REQUEST['precioCompra'];
        $_stock = $_REQUEST['stock'];
        
        $objModelProducto = new Application_Models_ProductosModel();
//      $objModelProducto->set_pkProductoSucursal($_REQUEST['pkProductoSucursal']);
        $objModelProducto->set_pkProducto($_REQUEST['pkProducto']);
        $objModelProducto->set_descripcion($_descripcion);
        $objModelProducto->set_pkTipo($_pkTipo);
        $objModelProducto->set_precioVenta($_precioVenta);
        $objModelProducto->set_precioCompra($_precioCompra);
        $objModelProducto->set_stock($_stock);
        
           $objModelProducto->_updateprodu();
    }
    
    
    
     private function _addProductonuevo() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelProducto = new Application_Models_ProductosModel();
                $_descripcion = "";
                if (isset($_REQUEST['descripcionproducto'])) {
                    $_descripcion = $_REQUEST['descripcionproducto'];
                }
                
                $tipo = "";
                if (isset($_REQUEST['pkTipo'])) {
                    $tipo = $_REQUEST['pkTipo'];
                }
                
                $precioventa="";
                if (isset($_REQUEST['precioVenta'])) {
                    $precioventa = $_REQUEST['precioVenta'];
                }
                
                $preciocompra="";
                if (isset($_REQUEST['precioCompra'])) {
                    $preciocompra = $_REQUEST['precioCompra'];
                }
                
                $stock="";
                if (isset($_REQUEST['stock'])) {
                    $stock = $_REQUEST['stock'];
                }

                $result = $objModelProducto->addNuevoProducto($_descripcion, $tipo,$precioventa,$preciocompra,$stock);
                echo $result;
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
    private function _eliminarProducto() {
//      echo"ll";
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelProducto = new Application_Models_ProductosModel();
                $objModelProducto->eliminar_producto($_REQUEST['pkProducto']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
      private function _BusquedaProductos() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelProducto = new Application_Models_ProductosModel();
                
                 $valor = "";
                if (isset($_REQUEST['valor'])) {
                    $valor = $_REQUEST['valor'];
                }
                
                $tipo = "0";
                if (isset($_REQUEST['tipo'])) {
                    $tipo = $_REQUEST['tipo'];
                }
                
               
                $objModelProducto->listProduct($valor, $tipo );
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _listProduct() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelProduct = new Application_Models_ProductosModel();
                $objModelProduct->listProduct();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _addCantidadStockProducto() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelProduct = new Application_Models_ProductosModel();
                $cantidad = "0";
                if (isset($_REQUEST['cantidad'])) {
                    $cantidad = $_REQUEST['cantidad'];
                }
                $comentario="";
                if (isset($_REQUEST['comentario'])) {
                    $comentario = $_REQUEST['comentario'];
                }

                $result = $objModelProduct->addCantidadProducto($_REQUEST['pkProduct'], $cantidad, $comentario,$_REQUEST['tipo']);
                echo $result;
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _listProductProvedor() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelProduct = new Application_Models_ProductosModel();
                $objModelProduct->ListProductPorProvedor($_REQUEST['pkProvedor']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _addCantidad() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelProduct = new Application_Models_ProductosModel();
                $cantidad = 0;
                if (isset($_REQUEST['cantidad'])) {
                    $cantidad = $_REQUEST['cantidad'];
                }
                $user = UserLogin::get_id();
                $objModelProduct->addCantidad($cantidad, $user);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _filltroPoductos() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelProduct = new Application_Models_ProductosModel();
                $categoria = "0";
                if (isset($_REQUEST['categoria'])) {
                    $categoria = $_REQUEST['categoria'];
                }
//                die("aas");
                $valor = "";
                if (isset($_REQUEST['valor'])) {
                    $valor = $_REQUEST['valor'];
                }
                $tipo = "1";
                if (isset($_REQUEST['tipo'])) {
                    $tipo = $_REQUEST['tipo'];
                }

                $objModelProduct->filtro_Product($categoria, $valor, $tipo);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
     private function _showAdminProducto() {
//      echo"ll";
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Views_SaleView();
                $objView->showIngresarProducto();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }    
    
    private function _Save() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelProducto = new Application_Models_ProductosModel();
                $objModelProducto->saveProducto($_REQUEST['producto'],$_REQUEST['tipo'],$_REQUEST['precioVenta'],$_REQUEST['precioCompra'], $_REQUEST['stock']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
    private function _Edit() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelProducto = new Application_Models_ProductosModel();
                $objModelProducto->editProducto($_REQUEST['id'],$_REQUEST['producto'],$_REQUEST['tipo'],$_REQUEST['precioVenta'],$_REQUEST['precioCompra'], $_REQUEST['stock']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

}
