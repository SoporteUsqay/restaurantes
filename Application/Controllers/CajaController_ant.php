<?php
// error_reporting(E_ALL);
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Application_Controllers_CajaController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {

            case 'ListAction':
                $this->_listCaja();
                break;
            case 'ListMontoInicialAction':
                $this->_listMontoInicial();
                break;
            case 'ListTotalDiaAction':
                $this->_listTotalVendido();
                break;
            case 'ShowRegisterAction':
                $this->_showRegister();
                break;
            case 'ShowCierreDiarioAction':
                $this->_showCierreDiario();
                break;
            case 'ShowMontoInicialAction':
                $this->_showMontoInicial();
                break;
            case 'ShowRegistrarPagoAction':
                $this->_showRegistrarPago();
                break;
            case 'ShowReportePagosDiariosAction':
                $this->_showReportePagoDiarios();
                break;
            case 'SaveGastoAction':
                $this->_saveGasto();
                break;
            case 'SaveMontoInicialAction':
                $this->_saveMontoInicial();
                break;
            case 'VerificarVentasAbiertasAction':
                $this->_verificarVentasAbiertas();
                break;
            case 'CierreDiarioAction':
                $this->_cierreDiario();
                break;
            case 'ListFechasCierreAction':
                $this->_proximosCierres();
                break;
            case 'ShowIniciarDiaAction':
                $this->showIniciarDia();
                break;
            case 'ShowConfirmAction':
                $this->showConfirmCierre();
                break;
            case 'CierreActualAction':
                $this->_CierreActual();
                break;
            case 'SaveMontoInicial2Action':
                $this->_SaveMontonInicial2();
                break;
            case 'ReportePagosDiariosAction':
                $this->_SaveMontonInicial2();
                break;
            case 'IngresoDineroAction':
                $this->_showIngresoDinero();
                break;
            
            case 'SaleCajaAction':
                $this->_showSaleCaja();
                break;
            
            case 'RegistrarPagoAction':
                $this->_showRegistrarPago();
                break;
            case 'MontoInicialAction':
                $this->_showMontoInicial();
                break;
            case 'VerificarMesasAbiertasAction':
                $this->_verificarMesasAbiertas();
                break;

            case 'ShowReportePagosFijosAction':
                $this->_verReportePagosFijos();
                break;

            case 'PagosPlanillasAction':
                $this->verPagosPLanillas();
                break;

            case 'PagosAnuladosAction':
                $this->verPagosAnulados();
                break;
            
            case 'ImprimeCierreAction':
                $this->imprimeCierre();
                break;

            case 'ImprimeWinchaAction':
                $this->imprimeWincha();
                break;
            
            case 'HacerCorteAction':
                $this->_Corte();
                break;
            
            case 'DataCutAction':
            $this->_dataCut();
            break;
                
            case 'ListCortesDiaAction':
                $this->_ListCortes();
                break;
            
            case 'ListTotalDiaxCorteAction':
                $this->_listTotalVendidoCorte();
                break;
            
            case 'IniciaDiaMultiCajaAction':
                $this->_IniciaDiaMultiCaja();
                break;

            case 'CorreoCierreAction':
                $this->_CorreoCierre();
                break;

            case 'showFondosExternosAction':
                require_once 'Application/Views/Caja/ReporteFondosExternos.php';
                break;

            case 'createBackupAction':
                $this->createBackUp();
                break;
        }
    }

    //Funcion para no esperar envio de correo
    public function execInBackground($cmd) {
        error_reporting(E_ALL);
        if (substr(php_uname(), 0, 7) == "Windows"){
            pclose(popen("start /B ". $cmd, "r")); 
        }
        else {
            exec($cmd . " > /dev/null &");  
        }
    }
    
    //Funcion Para Enviar Correo de Anulacion
    public function _CorreoCierre(){
        error_reporting(E_ALL);
        $user = new UserLogin();
        $ruta = realpath('');
        $pr = explode("htdocs",$ruta);
        $comando = $pr[0]."php\\php.exe ".$ruta."\\reportes\\envio_cierre.php ".escapeshellarg($_REQUEST["fecha"])." ".escapeshellarg($user->get_names()." ".$user->get_lastnames())." ".escapeshellarg($_REQUEST["corte"])." ".escapeshellarg($_REQUEST["inicial"])." ".escapeshellarg($_REQUEST["caja"])." ".escapeshellarg($_REQUEST["tipo"]);
        //echo $comando;
        $this->execInBackground($comando);
    }

    private function _verificarMesasAbiertas() {
        $view = new Application_Models_CajaModel();
        echo $view->_verificarMesaAbiertas();
    }

    private function _showIngresoDinero() {
        $view = new Application_Views_CajaView();
        $view->showIngresoDinero();
    }
    
    private function _showSaleCaja() {
        $view = new Application_Views_CajaView();
        $view->showSaleCaja();
    }

    private function _verReportePagosFijos() {
        $view = new Application_Views_CajaView();
        $view->showPagosFijos();
    }

    private function _SaveMontonInicial2() {
        $objView = new Application_Models_GastosDiariosModel();
        $descripcion = "";
        $cantidad = 0;

        if (isset($_REQUEST['cantidad'])){
            $cantidad = $_REQUEST['cantidad'];
        }
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $user = UserLogin::get_id();
        $query = "Call sp_registro_montoInicial($cantidad,'$sucursal',$user)";
        $db->executeQuery($query);
        
        
        $query1 = "Select * from corte where fin is null order by id DESC LIMIT 1";
               
        $result01 = $db->executeQuery($query1);
        $idc = "";
        if ($row0 = $db->fecth_array($result01)) {
            $idc = $row0["id"];            
        }
        
        if($idc !== ""){
            $sqlact = "Update corte set fin = '".date("Y-m-d H:i:s")."' where id = ".$idc."";
            $db->executeQuery($sqlact);
        }
        
        $query0 = "SELECT * FROM cierrediario LIMIT 1";
        $result0 = $db->executeQuery($query0);
        $fecha_cierre = "";
        if ($row = $db->fecth_array($result0)) {
            $fecha_cierre = $row[2];
        }
        
        $corte = date("Y-m-d H:i:s");
        $query2 = "INSERT INTO corte values(NULL,'".$fecha_cierre."','".$corte."',NULL,'".$cantidad."','1')";
        $db->executeQuery($query2);

        echo "<script>window.location.href='" . Class_config::get('urlApp') . "?controller=Index&&action=ShowHome'</script>";
    }
    
    private function _IniciaDiaMultiCaja($db = null) {
        $need_commit = true;
        if (is_null($db)) {
            // $need_commit = false;
            $db = new SuperDataBase();
        }
        //Agregamos inseguridad
        $db->executeQueryEx("SET SQL_SAFE_UPDATES = 0");

        //Primero cerramos los cortes
        $query1 = "Select * from corte where fin is null order by id DESC";
        $res1 = $db->executeQueryEx($query1);
        while($row1 = $db->fecth_array($res1)){
            $sqlact = "Update corte set fin = '".date("Y-m-d H:i:s")."' where id = ".$row1["id"]."";
            $db->executeQueryEx($sqlact);
        }
        
        //Obtenemos Fecha de Cierre
        $query0 = "SELECT * FROM cierrediario LIMIT 1";
        $result0 = $db->executeQueryEx($query0);
        $fecha_cierre = "";
        if ($row0 = $db->fecth_array($result0)) {
            $fecha_cierre = $row0[2];
        }

        //Eliminamos cortes porciacaso
        $query1 = "Select * from corte where fecha_cierre = '".$fecha_cierre."'";
        $res1 = $db->executeQueryEx($query1);
        while($row1 = $db->fecth_array($res1)){
            $sqldel = "Delete from corte where id = ".$row1["id"]."";
            $db->executeQueryEx($sqldel);

            $sqldelcorte = "Delete from accion_caja where pk_accion = '".$row1["id"]."' AND tipo_accion = 'CUT'";
            $db->executeQueryEx($sqldelcorte);
        }

        
        //Sacamos DateTime Corte
        $corte = date("Y-m-d H:i:s");
        
        //Creamos un nuevo corte para todas las cajas
        $query2 = "Select * from cajas";
        $res2 = $db->executeQueryEx($query2);
        while($row2 = $db->fecth_array($res2)){
            $queryc = "INSERT INTO corte values(NULL,'".$fecha_cierre."','".$corte."',NULL,'0','1')";
            $db->executeQueryEx($queryc);
            $rc = $db->executeQueryEx("select last_insert_id() as lid");
            if($rowc = $db->fecth_array($rc)){
                $sqldet = "Insert into accion_caja values(NULL,'".$rowc["lid"]."','CUT','".$row2["caja"]."')";
                $db->executeQuery($sqldet);
            }
        }

        //Obtenemos tipos de cambio para monedas registradas
        $query_monedas = "Select * from moneda where estado > 0 AND id >1";
        $result_monedas = $db->executeQueryEx($query_monedas);
        while($row = $db->fecth_array($result_monedas)){
            $query_cambio = "Select * from tipo_cambio where moneda = '".$row["id"]."' ORDER BY id DESC LIMIT 1";
            $result_cambio = $db->executeQueryEx($query_cambio);
            $cambio = 1;
            if($row_0 = $db->fecth_array($result_cambio)){
                $cambio = $row0["cambio"];
            }
            //Insertamos
            $query_in = "Insert into tipo_cambio values(NULL,'".$cambio."','".$fecha_cierre."','".$row["id"]."')";
            $db->executeQueryEx($query_in);
        }

        if ($need_commit) {
            // $db->executeQuery('select21321');

            $db->executeQueryEx('COMMIT');
        } 

        //Vamos a la pagina inicial
        echo "<script>window.location.href='" . Class_config::get('urlApp') . "?controller=Index&&action=ShowHome'</script>";
    }

    private function _CierreActual() {
        echo '
        <script language="JavaScript" type="text/javascript"> 
            window.onbeforeunload = preguntarAntesDeSalir;
            
            function preguntarAntesDeSalir()
            {
                return "El sistema se encuentra haciendo un proceso importante, si cierras la ventana se detendra y causara graves inconsistencias de datos. ¿Deseas salir?";
            }
        </script>';   

        try {

            $db = new SuperDataBase();
        
            $db->executeQueryEx('SET AUTOCOMMIT=0');
            $db->executeQueryEx('START TRANSACTION');

            
            $objInsumo = new Application_Models_InsumoModel();
            $objInsumo->actualizaVentasDiaAnterior();
            $objInsumo->actualizaCantidadHistorial();
            $objInsumo->actualizaCantidadPedido();
            
            $caja = new Application_Models_CajaModel();
            $caja->_cierreActual();
            
            $objInsumo->AgregarHistorialStock();
            
            // $db->executeQueryEx('delete from almacen');
            // $db->executeQueryEx('select * from cajas11');

            $this->_IniciaDiaMultiCaja();

        } catch (Exception $e) {
            $db->executeQuery('ROLLBACK');
        } finally {
            $db->executeQuery('SET AUTOCOMMIT=1');
        }

        //$this->createBackUp();
    }

    private function createBackUp() {

        error_reporting(E_ALL);

        $user = Class_config::get('userDataBase');
        $pass = Class_config::get('passwordDataBase');
        $dbName = Class_config::get('nameDataBase');

        $Dump = new MySQLBackup('localhost', $user, trim($pass), $dbName);

        $Dump->setCompress('zip');
        $Dump->dump();

        $fecha_actual = date("Y-m-d");
        if (file_exists('DataBase/backups/dump_'.$dbName.'_'.date('Ymd', strtotime($fecha_actual."- 1 week")))) {
            unlink('DataBase/backups/dump_'.$dbName.'_'.date('Ymd', strtotime($fecha_actual."- 1 week")));
        }
    }

    private function showIniciarDia() {
        $caja = new Application_Views_CajaView();
        $caja->showOpenVenta();
    }

    private function showConfirmCierre() {
        $caja = new Application_Views_CajaView();
        $caja->showConfirmCierre();
    }

    private function _listCaja() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelCaja = new Application_Models_CajaModel();
                $fecha = date('Y-m-d');
                if (isset($_REQUEST['fecha'])) {
                    $fecha = $_REQUEST['fecha'];
                }
                $objModelCaja->_listCaja($fecha);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _listTotalVendido() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelCaja = new Application_Models_CajaModel();
                $fecha = date('Y-m-d');
                if (isset($_REQUEST['fecha'])) {
                    $fecha = $_REQUEST['fecha'];
                }
                $corte = $objModelCaja->_TotalDia($fecha);
                echo json_encode($corte);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _listMontoInicial() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelCaja = new Application_Models_CajaModel();
                $objModelCaja->_ListMontoInicial();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showRegister() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Views_CajaView();
                $objView->showRegister();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showCierreDiario() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Views_CajaView();
                $objView->showCierreDiario();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showRegistrarPago() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Views_CajaView();
                $objView->showRegistrarPago();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showMontoInicial() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Views_CajaView();
                $objView->showMontoInicial();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showReportePagoDiarios() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Views_CajaView();
                $objView->showReportePagosDiarios();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _saveGasto() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Models_CajaModel();
                $descripcion = "";
                $cantidad = 0;
                if (isset($_REQUEST['descripcion']))
                    $descripcion = $_REQUEST['descripcion'];
                if (isset($_REQUEST['cantidad']))
                    $cantidad = $_REQUEST['cantidad'];
                $objView->RegistrarGastoDiario($descripcion, $cantidad);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _saveMontoInicial() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Models_CajaModel();
                $descripcion = "";
                $cantidad = 0;

                if (isset($_REQUEST['cantidad']))
                    $cantidad = $_REQUEST['cantidad'];
                $objView->RegistrarMontoInicial($cantidad);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _verificarVentasAbiertas() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Models_CajaModel();


                $objView->_verificandoMesasAbiertas();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _cierreDiario() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Models_CajaModel();
                $objInsumo = new Application_Models_InsumoModel();

                $objInsumo->actualizaCantidadHistorial();
                $objInsumo->actualizaCantidadPedido();


                $objView->_cierreDiario();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function verPagosPLanillas() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Views_CajaView();
                $objView->VerPagoPlanilla();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function verPagosAnulados() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Views_CajaView();
                $objView->VerPagosAnulados();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _proximosCierres() {
        $objView = new Application_Models_CajaModel();


        $objView->_listFechasCierre();
    }
    
    //Actualizado 2019 - Gino lluen
    //Ahora tambien envia correo :3
    private function imprimeCierre() {        
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelCaja = new Application_Models_CajaModel();
                $this->_CorreoCierre();
                $objModelCaja->imprimeCierre($_REQUEST["fecha"],$_REQUEST["terminal"],$_REQUEST["cajero"],$_REQUEST["corte"],$_REQUEST["inicial"],$_REQUEST["caja"]);                       
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    //Funcion para imprimir wincha
    private function imprimeWincha() {        
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelCaja = new Application_Models_CajaModel();
                $objModelCaja->imprimeWincha($_REQUEST["fecha"],$_REQUEST["terminal"],$_REQUEST["cajero"],$_REQUEST["corte"],$_REQUEST["inicial"],$_REQUEST["caja"]);                       
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
    //Funciones para el corte
    private function _Corte() {
        $objView = new Application_Models_CajaModel();
        $objView->_hacerCorte($_REQUEST["caja"]);
    }
    
    private function _ListCortes(){
        $db = new SuperDataBase();
        $array = array();
        $res = $db->executeQuery("Select c.* from corte c, accion_caja ac where c.fecha_cierre = '".$_REQUEST["fecha"]."' AND ac.pk_accion = c.id AND ac.tipo_accion = 'CUT' AND ac.caja = '".$_REQUEST["caja"]."' order by c.id DESC");
        while ($row0 = $db->fecth_array($res)) {
            $array[] = $row0["inicio"];           
        }
        echo json_encode($array);
    }

    public function _dataCut(){
        $db = new SuperDataBase();
        $array = array();
        $res = $db->executeQuery("Select c.* from corte c, accion_caja ac where c.inicio = '".$_REQUEST["fecha"]."' AND ac.pk_accion = c.id AND ac.tipo_accion = 'CUT' AND ac.caja = '".$_REQUEST["caja"]."' order by c.id DESC");
        if ($row0 = $db->fecth_array($res)) {
            $array[] = $row0;           
        }
        echo json_encode($array);
    }
    
    private function _listTotalVendidoCorte() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelCaja = new Application_Models_CajaModel();
                $fecha = date('Y-m-d');
                if (isset($_REQUEST['fecha'])) {
                    $fecha = $_REQUEST['fecha'];
                }
                $corte = "";
                if (isset($_REQUEST['corte'])) {
                    $corte = $_REQUEST['corte'];
                }
                $caja = $_COOKIE["c"];
                if(isset($_REQUEST['caja'])){
                    $caja = $_REQUEST["caja"];
                }
                $corte = $objModelCaja->_TotalDiaCorte($fecha,$corte,$caja);
                echo json_encode($corte);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

}
