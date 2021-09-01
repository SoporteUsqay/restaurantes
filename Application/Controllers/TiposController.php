<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Controllers_TiposController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {

            case 'ListCategoriaAction':
                $this->_listCategoria();
                break;

            case 'ListCategoria_ProductoAction':
                $this->_listCategoria_Producto();
                break;

            case 'ListAction':
                $this->_listTipos();
                break;

            case 'ListTipoAction':
                $this->_listCategoriaTipo();
                break;

            case 'ListCategoriaProductoAction':
                $this->_listCategoriaProducto();
                break;

            case 'SaveCategoriaAction':
                $this->_SaveCategoria();
                break;

            case 'listadoCategoriasAction':
                $this->_listadoCategorias();
                break;

            case 'UpdateCategoriaAction':
                $this->_UpdateCategoria();
                break;

            case 'deletecategoriaAction':
                $this->_deletecategoria();
                break;

            case 'ListCategoriaSucursalAction':
                $this->_ListCategoriaSucursal();
                break;

            case 'SaveTipoAction':
                $this->_SaveTipo();
                break;

            case 'listadoTiposCategoriasAction':
                $this->_listadoTiposCategorias();
                break;

            case 'deletetipoAction':
                $this->_deletetipo();
                break;
            
            case 'SaveAction':
                $this->_Save();
                break;
            
            case "EditAction":
                $this->_Edit();
                break;
            
            case "DeleteAction":
                $this->_Delete();
                break;            
            
            case "ActiveAction":
                $this->_Active();
                break;
            
            case "ListPageAction":
                $this->_listPage();
                break;
            
            case "getTipoSunatAction":
                $this->_getTipoSunat();
                break;
        }
    }

    private function _deletetipo() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objtipomodel = new Application_Models_TiposModel();
                $objtipomodel->deleteTipo($_REQUEST['pktipo']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _deletecategoria() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objtipomodel = new Application_Models_TiposModel();
                $objtipomodel->deleteCategoria($_REQUEST['pkcategoria']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _UpdateCategoria() {
        $_categoria = $_REQUEST['categoria'];

        $objcategoria = new Application_Models_TiposModel();
        $objcategoria->set_idCategoria($_REQUEST['IdCategoria']);
        $objcategoria->set_NombreCategoria($_categoria);

        $objcategoria->ActualizarCategoria();
    }

    private function _listadoTiposCategorias() {
        $objModelcategoria = new Application_Models_TiposModel();
        $objModelcategoria->_listaTiposCategoria();
    }

    private function _listadoCategorias() {
        $objModelcategoria = new Application_Models_TiposModel();
        $objModelcategoria->_listaCategoria();
    }

    private function _SaveTipo() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objcategoria = new Application_Models_TiposModel();

                $_descripcionTipo = "";
                if (isset($_REQUEST['Tipo'])) {
                    $_descripcionTipo = $_REQUEST['Tipo'];
                }

                $_Idcategoria = "";
                if (isset($_REQUEST['pkCategoria'])) {
                    $_Idcategoria = $_REQUEST['pkCategoria'];
                }

                $objcategoria->RegistrarTipo($_descripcionTipo, $_Idcategoria);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _SaveCategoria() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objcategoria = new Application_Models_TiposModel();

                $_descripcioncategoria = "";
                if (isset($_REQUEST['categoria'])) {
                    $_descripcioncategoria = $_REQUEST['categoria'];
                }

                $objcategoria->RegistrarCategoria($_descripcioncategoria);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _listTipos() {
        $objModelTipos = new Application_Models_TiposModel();
        $objModelTipos->_listTiposForCategoria();
    }
    
    private function _listPage() {
        $objModelTipos = new Application_Models_TiposModel();
        $objModelTipos->set_limit($_REQUEST["page"]);
        $objModelTipos->_listTiposForCategoriaPage();
    }

    private function _listCategoria_Producto() {

        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelcategoria = new Application_Models_TiposModel();
                $tipoProduct = "";
                if (isset($_REQUEST['pkTipoProducto'])) {
                    $tipoProduct = $_REQUEST['pkTipoProducto'];
                }

                $objModelcategoria->_listCategoriaForProductos($tipoProduct);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }

//        $objModelcategoria= new Application_Models_TiposModel();
//        $objModelcategoria->_listCategoriaForProductos($_REQUEST['pkTipoProducto']);
    }

    private function _ListCategoriaSucursal() {
        $objModelcategoria = new Application_Models_TiposModel();
        $objModelcategoria->__ListCategoriaSucursal();
    }

    private function _listCategoria() {
        $objModelcategoria = new Application_Models_TiposModel();
        $objModelcategoria->_listCategoria();
    }

    private function _listCategoriaTipo() {
        $objModelcategTipo = new Application_Models_TiposModel();
//        $objModelcategTipo->set_NombreCategoria($_REQUEST['categoria']);
        $objModelcategTipo->_listTipos();
    }

    private function _listCategoriaProducto() {
        $objModelcategTipo = new Application_Models_TiposModel();

        $objModelcategTipo->_listCategoriaProductos();
    }

    private function _Save() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_TiposModel();
                $obj->saveTipo($_REQUEST['descripcion'],$_REQUEST["tipo_sunat"]);
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
                $obj = new Application_Models_TiposModel();
                $obj->updateTipo($_POST['id'], $_REQUEST['descripcion'],'1');
                //Actualizamos el tipo sunat
                $db = new SuperDataBase();
                $db->executeQuery("SET SQL_o tipo_codigo_sunat values(NULL,'".$_REQUEST['id']."','".$_REQUEST["tipo_sunat"]."')");
                $db->executeQuery("SET SQL_SAFE_UPDATES = 1;");
                
                //Obtenemos todos los platos del tipo
                $r1 = $db->executeQuery("Select pkPlato from plato where pktipo = '".$_POST["id"]."' AND estado = 0");
                while ($row = $db->fecth_array($r1)){
                    $db->executeQuery("SET SQL_SAFE_UPDATES = 0;");
                    $db->executeQuery("update from plato_codigo_sunat set id_codigo_sunat=".$_REQUEST["tipo_sunat"]."' where id_plato = '".$row["pkPlato"]."'");
//                    $db->executeQuery("Insert into plato_codigo_sunat values(NULL,'".$row["pkPlato"]."','".$_REQUEST["tipo_sunat"]."')");
                    $db->executeQuery("SET SQL_SAFE_UPDATES = 1;");
                    //Se realizó el cambio, en vez de delet que tenia anteriormente y se volvia a insertar el cual le faltaban dos parametros, mejor solo se actualizo la linea de codigo.
                    
//                 echo    "SET SQL_SAFE_UPDATES = 0";
//                 echo"<br>".  "update from plato_codigo_sunat set id_codigo_sunat=".$_REQUEST["tipo_sunat"]."' where id_plato = '".$row["pkPlato"]."'";
//                 echo"<br>".  "Insert into plato_codigo_sunat values(NULL,'".$row["pkPlato"]."','".$_REQUEST["tipo_sunat"]."')";
//                 echo"<br>".  "SET SQL_SAFE_UPDATES = 1;";
                    }
                } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
    private function _Delete(){
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_TiposModel();
                  $obj->deleteTipo($_POST['id'])    ;            
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
    private function _Active(){
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_TiposModel();
                  $obj->activeTipo($_POST['id'])    ;            
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
    private function _getTipoSunat() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                //Obtenemos El Tipo de Sunat
                $db = new SuperDataBase();
                $result_sunat = $db->executeQuery("SELECT cs.id, cs.descripcion from tipo_codigo_sunat tc, codigo_sunat cs where tc.id_tipo = '".$_REQUEST["pktipo"]."' AND tc.id_codigo_sunat = cs.id");
                $id_sunat = "";
                $descripcion_sunat = "";
                $array = array();
                if($row1 = $db->fecth_array($result_sunat)){
                    $id_sunat = $row1["id"];
                    $descripcion_sunat = $row1["descripcion"];
                }
                $array[] = array(
                    'id_sunat' => $id_sunat,
                    'descripcion_sunat' => utf8_encode($descripcion_sunat),
                );
                echo json_encode($array);             
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }  
    }

}
