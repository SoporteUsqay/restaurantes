<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Controllers_ConfigController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {
            //Funcion para obtener una mesa libre en salon delivery
            case 'getMesaAction':
                $this->_getMesa($_POST["pkSalon"]);
                break;

            case 'ListUserTypeAction':
                $this->_listTypeUser();
                break;
            case 'EmpAction':
                $this->showEmpresa();
                break;
            case 'ShowCategoryAction':
                $this->showCategoria();
                break;
            case 'ShowTiposAction':
                $this->showTipos();
                break;
            case 'ShowImpresionAction':
                $this->showImpresion();
                break;
            case 'AdmSalonAction':
                $this->_AdminSalon();
                break;

            case 'SaveSalonAction':
                $this->_SaveSalon();
                break;
            
            case 'CrearMesasAction':
                $this->_CrearMesas();
                break;

            case 'SaveMesaAction':
                $this->_SaveMesas();
                break;

            case 'ActualizarSalonesAction':
                $this->_ActualizarSalones();
                break;

            case 'EliminarSalonesAction':
                $this->_EliminarSalones();
                break;

            case 'EliminarMesasAction':
                $this->_EliminarMesas();
                break;

            case 'EliminarMesaEspecificaAction':
                $this->_EliminarMesaEspecifica();
                break;

            case 'ListSalonesAction':
                $this->_ListSalones();
                break;

            case 'ListNombreMesasAction':
                $this->_ListNombreMesas();
                break;

            case 'ListMesasAction':
                $this->_ListMesas();
                break;

            case 'UserAction':
                $this->_showUsuarios();
                break;
            case 'ProblemAction':
                $this->_showProblem();
                break;
            case "ShowAdminMensajesAction":
                $this->_showAdminMensajes();
                break;
            
             case "DeleteMesaAction":
                $this->_DeleteMesa();
                break;
            
             case "ActiveMesaAction":
                $this->_ActiveMesa();
                break;
            
            case "ListModulosAction":
                $this->_LisModulos();
                break;
            
            
            case "LisSubModulosAction":
                $this->_LisSubModulos();
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
            case "UpdateMesaAction":
                $this->_updateMesa();
                break;
            case 'ShowMesasAction':
                if (self::$session->validateStartSesion()) {
                    if (!self::$session->validateSesion()) {
                        $objModelConfigurations = new Application_Views_ConfigurationsView();
                        $objModelConfigurations->showMesas();
                    } else {
                        self::$session->redirect();
                    }
                } else {
                    self::$session->redirect();
                }
                break;
            case 'showBuckupsAction':
                if (self::$session->validateStartSesion()) {
                    if (!self::$session->validateSesion()) {
                        $objModelConfigurations = new Application_Views_ConfigurationsView();
                        $objModelConfigurations->showBuckups();
                    } else {
                        self::$session->redirect();
                    }
                } else {
                    self::$session->redirect();
                }
                break;
            case 'ListMesaIDAction':
                if (self::$session->validateStartSesion()) {
                    if (!self::$session->validateSesion()) {
                        $objModelConfigurations = new Application_Models_MesaModel();
                        $objModelConfigurations->_listMesasID($_REQUEST['id']);
                    } else {
                        self::$session->redirect();
                    }
                } else {
                    self::$session->redirect();
                }
                break;
                
                
            //Funciones para salones y cajas
            case "QuitaSalonCajaAction":
                $this->_QuitaSalonCaja();
                break;
            
            case "PonSalonCajaAction":
                $this->_PonSalonCaja();
                break;

            //Funciones para Tipos y Cajas
            case "QuitaTipoCajaAction":
                $this->_QuitaTipoCaja();
                break;
            
            case "PonTipoCajaAction":
                $this->_PonTipoCaja();
                break;

            //Funciones para Mensajes y Cajas
            case "QuitaMensajeCajaAction":
                $this->_QuitaMensajeCaja();
                break;
            
            case "PonMensajeCajaAction":
                $this->_PonMensajeCaja();
                break;

            // motivos anulacion
            case 'MotivoAnulacionAction':
                if (self::$session->validateStartSesion()) {
                    if (!self::$session->validateSesion()) {
                        $objModelConfigurations = new Application_Views_ConfigurationsView();
                        $objModelConfigurations->showMotivosAnulacion();
                    } else {
                        self::$session->redirect();
                    }
                } else {
                    self::$session->redirect();
                }
                break;
            case 'MotivoAnulacionAddAction':
                $this->_motivoAnulacionAdd();
                break;
            case 'MotivoAnulacionEditAction':
                $this->_motivoAnulacionEdit();
                break;
            case 'MotivoAnulacionDeleteAction':
                $this->_motivoAnulacionDelete();
                break;
        }
    }
    
    //Funciones para salones por cajas
    private function _QuitaSalonCaja(){
        $objSalon = new Application_Models_SalonModel();
        $objSalon->QuitaSalonCaja($_REQUEST["pkSalon"],$_REQUEST["caja"]);
    }
    
    private function _PonSalonCaja(){
        $objSalon = new Application_Models_SalonModel();
        $objSalon->PonSalonCaja($_REQUEST["pkSalon"],$_REQUEST["caja"]);
    }

     //Funciones para mensajes por cajas
     private function _QuitaMensajeCaja(){
        $objMsg = new Application_Models_MensajeModel();
        $objMsg->QuitaMensajeCaja($_REQUEST["pkMensaje"],$_REQUEST["caja"]);
    }
    
    private function _PonMensajeCaja(){
        $objMsg = new Application_Models_MensajeModel();
        $objMsg->PonMensajeCaja($_REQUEST["pkMensaje"],$_REQUEST["caja"]);
    }

    //Funciones para tipos por cajas
    private function _QuitaTipoCaja(){
        $db = new SuperDataBase();
        $db->executeQuery("SET SQL_SAFE_UPDATES = 0;");
        $query_del = "Delete from accion_caja where pk_accion = '".$_REQUEST["pkTipo"]."' AND caja = '".$_REQUEST["caja"]."' AND tipo_accion = 'TYP'";
        $db->executeQuery($query_del);
        $db->executeQuery("SET SQL_SAFE_UPDATES = 1;");
        echo "<script>window.location.href='" . Class_config::get('urlApp') . "?controller=Sale&action=AdminTipos'</script>";
    }
    
    private function _PonTipoCaja(){
        $db = new SuperDataBase();
        $query_add = "Insert into accion_caja values(NULL,'".$_REQUEST["pkTipo"]."','TYP','".$_REQUEST["caja"]."')";
        $db->executeQuery($query_add);
        echo "<script>window.location.href='" . Class_config::get('urlApp') . "?controller=Sale&action=AdminTipos'</script>";
    }
    
    
   //Funcion para obtener mesa abierta
   private function _getMesa($pkSalon){
       $objMesa = new Application_Models_MesaModel();
       $resultado = $objMesa->getMesa($pkSalon);
       echo json_encode($resultado);
   }
    
   private function _CrearMesas() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objMesa = new Application_Models_MesaModel();

                $_valor = "";
                if (isset($_REQUEST['rad'])) {
                    $_valor = $_REQUEST['rad'];
                }
                                                              
                $_IdSalon = "";
                if (isset($_REQUEST['txtIdSalon'])) {
                    $_IdSalon = $_REQUEST['txtIdSalon'];
                }

                $_Prefijo_mesa = "";
                if (isset($_REQUEST['txtMesa'])) {
                    $_Prefijo_mesa = $_REQUEST['txtMesa'];
                }
                
                $_descripcionMesa = "";
                if (isset($_REQUEST['txtNombreMesa'])) {
                    $_descripcionMesa = $_REQUEST['txtNombreMesa'];
                }
                
                $_valorCantidadMesas = "";
                if (isset($_REQUEST['radcantidad'])) {
                    $_valorCantidadMesas = $_REQUEST['radcantidad'];
                }

                $cantidadMesas = "";
                if (isset($_REQUEST['txtTotalMesas'])) {
                    $cantidadMesas = $_REQUEST['txtTotalMesas'];
                }
                
                $MesasDesde = "";
                if (isset($_REQUEST['txtDesde'])) {
                    $MesasDesde = $_REQUEST['txtDesde'];
                }
                
                $MesasHsta = "";
                if (isset($_REQUEST['txtHasta'])) {
                    $MesasHsta = $_REQUEST['txtHasta'];
                }

                echo $objMesa->CrearMesas($_valor,$_IdSalon, $_Prefijo_mesa,$_descripcionMesa,$_valorCantidadMesas, $cantidadMesas,$MesasDesde,$MesasHsta);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
    
    private function _ListMesas() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelMesas = new Application_Models_MesaModel();

                $NombreMesa = "";
                if (isset($_REQUEST['NombreMesa'])) {
                    $NombreMesa = $_REQUEST['NombreMesa'];
                }

                $_IDSalon = "";
                if (isset($_REQUEST['PkSalon'])) {
                    $_IDSalon = $_REQUEST['PkSalon'];
                }

                $objModelMesas->_listMesas($NombreMesa, $_IDSalon);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _ListNombreMesas() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelMesas = new Application_Models_MesaModel();

                $IdSalon = "";
                if (isset($_REQUEST['pkIdSalon'])) {
                    $IdSalon = $_REQUEST['pkIdSalon'];
                }

                $objModelMesas->_listNombreMesas($IdSalon);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _ListSalones() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelSalon = new Application_Models_MesaModel();
                $objModelSalon->_listSalones();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _EliminarMesaEspecifica() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objMesa = new Application_Models_MesaModel();

//                $_IDMesa = "";
//                if (isset($_REQUEST['eliminar_PknombreMesa'])) {
//                    $_IDMesa = $_REQUEST['eliminar_PknombreMesa'];
//                }

                $objMesa->EliminarMesaEspecifica($_REQUEST['id'],$_REQUEST['estado']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _EliminarMesas() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objMesa = new Application_Models_MesaModel();

                $_IDSalon = "";
                if (isset($_REQUEST['pkSalon'])) {
                    $_IDSalon = $_REQUEST['pkSalon'];
                }

                $_descripcionMesa = "";
                if (isset($_REQUEST['Actualiza_nombreMesa'])) {
                    $_descripcionMesa = $_REQUEST['Actualiza_nombreMesa'];
                }

                $cantidadMesas = "";
                if (isset($_REQUEST['Actualiza_cantidad_Mesas'])) {
                    $cantidadMesas = $_REQUEST['Actualiza_cantidad_Mesas'];
                }

                $objMesa->EliminarMesas($_IDSalon, $_descripcionMesa, $cantidadMesas);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _ActualizarSalones() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objMesa = new Application_Models_MesaModel();

                $_IDSalon = "";
                if (isset($_REQUEST['pkSalon'])) {
                    $_IDSalon = $_REQUEST['pkSalon'];
                }

                $_descripcionMesa = "";
                if (isset($_REQUEST['Actualiza_nombreMesa'])) {
                    $_descripcionMesa = $_REQUEST['Actualiza_nombreMesa'];
                }

                $cantidadMesas = "";
                if (isset($_REQUEST['Actualiza_cantidad_Mesas'])) {
                    $cantidadMesas = $_REQUEST['Actualiza_cantidad_Mesas'];
                }

                $objMesa->ActualizarSalones($_IDSalon, $_descripcionMesa, $cantidadMesas);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _SaveMesas() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objMesa = new Application_Models_MesaModel();

                $_IdSalon = "";
                if (isset($_REQUEST['IdSalon'])) {
                    $_IdSalon = $_REQUEST['IdSalon'];
                }

                $_descripcionMesa = "";
                if (isset($_REQUEST['Mesa'])) {
                    $_descripcionMesa = $_REQUEST['Mesa'];
                }

                $estado = "";
                if (isset($_REQUEST['estado'])) {
                    $cantidadMesas = $_REQUEST['estado'];
                }

                echo $objMesa->RegistrarMesas($_IdSalon, $_descripcionMesa, $cantidadMesas);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _deleteMesa() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_MesaModel();
                $obj->deleteMesa($_REQUEST['id']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
    private function _ActiveMesa() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_MesaModel();
                $obj->ActiveMesa($_REQUEST['id']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
    private function _SaveSalon() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objSalon = new Application_Models_SalonModel();
                
                $_NombreSalon = "";
                if (isset($_REQUEST['nombre'])) {
                    $_NombreSalon = $_REQUEST['nombre'];
                }
                $objSalon->set_nombre($_REQUEST['nombre']);
                $objSalon->registrarSalon($_NombreSalon);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    
    private function _LisModulos() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_MesaModel();

               $obj->_listModulos();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
    
    private function _LisSubModulos() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_MesaModel();

                $IdModulo = "";
                if (isset($_REQUEST['pkIdModulo'])) {
                    $IdModulo = $_REQUEST['pkIdModulo'];
                }

                $obj->_listSubModulos($IdModulo);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
    private function _showProblem() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelConfigurations = new Application_Views_ConfigurationsView();
                $objModelConfigurations->showReportarProblema();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showUsuarios() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelConfigurations = new Application_Views_ConfigurationsView();
                $objModelConfigurations->showUser();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _AdminSalon() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelConfigurations = new Application_Views_ConfigurationsView();
                $objModelConfigurations->showAdminSalon();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function showCategoria() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelTypeUser = new Application_Views_ConfigurationsView();
                $objModelTypeUser->showCategoria();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function showImpresion() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelTypeUser = new Application_Views_ConfigurationsView();
                $objModelTypeUser->showImpresion();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function showTipos() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelTypeUser = new Application_Views_ConfigurationsView();
                $objModelTypeUser->showTipos();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function showEmpresa() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelTypeUser = new Application_Views_ConfigurationsView();
                $objModelTypeUser->showEmpresa();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showAdminMensajes() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objViewMensajes = new Application_Views_ConfigurationsView();
                $objViewMensajes->showAdminMensajes();
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
                $obj = new Application_Models_SalonModel();
                $obj->updateSalon($_POST['id'], $_REQUEST['nombre']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _Delete() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_SalonModel();
                $obj->deleteSalon($_POST['id']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _Active() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $obj = new Application_Models_SalonModel();
                $obj->activeSalon($_POST['id']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _updateMesa() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objMesa = new Application_Models_MesaModel();

                $_IdSalon = "";
                if (isset($_REQUEST['IdSalon'])) {
                    $_IdSalon = $_REQUEST['IdSalon'];
                }

                $_descripcionMesa = "";
                if (isset($_REQUEST['Mesa'])) {
                    $_descripcionMesa = $_REQUEST['Mesa'];
                }

                $estado = "";
                if (isset($_REQUEST['estado'])) {
                    $cantidadMesas = $_REQUEST['estado'];
                }

                echo $objMesa->ModificarMEsa($_REQUEST['id'], $_IdSalon, $_descripcionMesa, $cantidadMesas);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _motivoAnulacionAdd() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                
                $db = new SuperDataBase();

                $nombre = $_POST['nombre'];

                $query = "insert into motivo_anulacion_predefinido (nombre) values('$nombre')";

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

    private function _motivoAnulacionEdit() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {

                $db = new SuperDataBase();

                $id = $_POST['id'];
                $nombre = $_POST['nombre'];

                $query = "update motivo_anulacion_predefinido set nombre = '$nombre' where id = '$id'";

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

    private function _motivoAnulacionDelete() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {

                $db = new SuperDataBase();

                $id = $_POST['id'];

                $query = "delete from motivo_anulacion_predefinido where id = '$id'";

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

}
