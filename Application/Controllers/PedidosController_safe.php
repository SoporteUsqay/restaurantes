<?php
error_reporting(E_ALL);
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 * Estado del pedido
 * 0 Activo
 * 1 Pagado
 * 2 Despachado
 * 3 Eliminado
 * 4 A Credito
 * 5 A cuenta
 */

class Application_Controllers_PedidosController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {
            case 'ShowPedidosAction':
                $this->_showPedidos();
                break;
            case 'ShowPedidos2Action':
                $this->_showPedidos2();
                break;
            case "ShowCocinaAction":
                $this->_showCocina();
                break;
            case "ShowBarAction":
                $this->_ShowBar();
                break;

            case 'ListAllProductAction':
                $this->_listAllProduct();
                break;
            case 'ListAllPedidosPlatosAction':
                $this->_listAllPedidos();
                break;
            case 'ListPedidosAction':
                $this->_listPedidos();
                break;
            case 'ListPedidosPkAction':
                $this->_listPedidosPk();
                break;
            case 'ListPedidosItemAction':
                $this->_listItemPedidos();
                break;
            case 'ListPedidosItem2Action':
                $this->_listItemPedidos2();
            break;
            case 'ListDetalleAction':
                $this->_detallesVenta();
                break;
            case 'AddPedidoAction':
                $this->_AddPedido();
                break;
            case 'DeletePedidoAction':
                $this->_DeletePedido();
                break;
            case 'AperturarMesaAction':
                $this->_aperturarMesa();
                break;
            case 'UpdateNPersonasAction':
                $this->_UpdateNPersonas();
                break;
            case 'UpdateDescuentoAction':
                $this->_UpdateNPersonas();
                break;
            case 'UpdatePedidoAction':
                $this->_UpdatePedido();
                break;
            case 'SaveMessagePedidoAction':
                $this->_saveMessagePedido();
                break;
            case 'PedidosImprimirAction':
                $this->_PedidosImprimir();
                break;
            case 'ShowMesasAction':
                $this->_ShowMesas();
                break;
            case 'ShowPorPagarAction':
                $this->_showPedidoPorPagar();
                break;
            case 'CancelaMesaAction':
                $this->_cancelaPedido();
                break;
            case 'CancelaPedidoCreditoAction':
                $this->_cancelaPedidoCredito();
                break;
            case 'CancelaPedidoACuentaAction':
                $this->_cancelaPedidoACuenta();
                break;
            case 'ChangeMesaAction':
                $this->_changeMesa();
                break;
            case 'AnulaPedidoAction':
                $this->_AnulaPedido();
                break;
            case 'EnviaPedidoAction':
                $this->_enviaPedidos();
                break;
            case 'UpdateTipoPedidoAction':
                $this->_UpdateTipoPedido();
                break;
            case 'ListProductDescripcionAction':
                $this->_listAllDescripcion();
                break;
            case 'TiposMenusAction':
                $this->_listTiposMenus();
                break;
            case 'ComponentesPorTipoAction':
                $this->_componentesPorTipo();
                break;
            case 'ImprimeCuentaAction':
                $this->_imprimeCuenta();
                break;
            case 'ImprimeCuentaPelucaAction':
                $this->_imprimeCuentaPeluca();
                break;
            case 'AddMenuAction':
                $this->_AddMenu();
                break;
            case 'DescuentoAction':
                $this->_descuento();
                break;
            
            case 'ajustaCreditoAction':
                $this->_ajustaCredito();
                break;
            
            case 'magiaDetalleFEAction':
                $this->_magiaDetalleFE();
                break;
            
            case 'getDetallesFEAction':
                $this->_getDetallesFE();
                break;
            
            case 'delDetalleFEAction':
                $this->_delDetalleFE();
                break;
            
            case 'cleanMagiaFEAction':
                $this->_cleanMagiaFE();
                break;
            
            //Verificamos estado de mesa antes de cualquier proceso
            //Gino LLuen 2019
            case 'checkOpenMesaAction':
                $this->_checkOpenMesa();
                break;
            
            //Eliminamos 1 item del detalle
            case 'DeletePedido1Action':
                $this->_DeletePedido1();
                break;
            
            //Cambio de precio en pedido
            case 'cambiaPrecioAction':
                $this->_cambiaPrecio();
                break;


            //Impresion de pedidos por pantalla - 2019
            case 'obtienePedidosPantallaAction':
                $this->_obtienePedidosPantalla();
                break;

            case 'entregaPedidoPantallaAction':
                $this->_entregaPedidoPantalla();
                break;

            case 'anulaPedidoPantallaAction':
                $this->_anulaPedidoPantalla();
                break;

            case 'obtieneHistorialPantallaAction':
                $this->_obtieneHistorialPantalla();
                break;

            case 'obtieneTipoPlatoPantallaAction':
                $this->_obtieneTipoPlatoPantalla();
                break;

            case 'cookieImpresionPantallaAction':
                $this->_cookieImpresionPantalla();
                break;

            case 'obtieneHistorialDelDiaPantallaAction':
                $this->_obtieneHistorialDelDiaPantalla();
                break;
        }
    }

    //Impresion de pedidos por pantalla  - 2019 

    private function _obtienePedidosPantalla(){
        $tipos = "";
        $consulta_tipos = "";
        if(isset($_REQUEST["tipos"])){
            $consulta_tipos .= " AND (";
            $tipos = $_REQUEST["tipos"];
            $tipos_array = explode(",",$tipos);
            foreach($tipos_array as $tp){
                $consulta_tipos .= "pl.pkTipo = '".$tp."' OR ";
            }
            $consulta_tipos = substr($consulta_tipos,0,-3);
            $consulta_tipos .= ")";
        }
        $db = new SuperDataBase();
        $query_pedidos = "SELECT dp.pkDetallePedido as id, dp.cantidad, dp.mensaje, 
        dp.horaPedido as tiempo, pl.descripcion as plato, tr.pkTrabajador as id_trabajador, 
        tr.pkTipoTrabajador as tipo_trabajador, CONCAT(tr.nombres, ' ', tr.apellidos) as 
        nombre_trabajador, m.nmesa as mesa, s.nombre as salon, s.pkSalon as pkSalon, p.npersonas as personas 
        FROM detallepedido dp, pedido p, plato pl, trabajador tr, mesas m, salon s 
        WHERE dp.pkMozo = tr.pkTrabajador AND 
        dp.pkPlato = pl.pkPlato AND dp.pkPediido = p.pkPediido AND p.pkMesa = m.pkMesa AND 
        m.pkSalon = s.pkSalon AND dp.estado = 1".$consulta_tipos;
        $resultado = array();
        $result = $db->executeQuery($query_pedidos);
        while ($row = $db->fecth_array($result)) {
            $tmp = array();
            $tmp["id"] = $row["id"];
            $tmp["cantidad"] = $row["cantidad"];
            $tmp["mensaje"] = $row["mensaje"];
            $tmp["tiempo"] = $row["tiempo"];
            $tmp["plato"] = $row["plato"];
            $tmp["id_trabajador"] = $row["id_trabajador"];
            $tmp["tipo_trabajador"] = $row["tipo_trabajador"];
            $tmp["nombre_trabajador"] = $row["nombre_trabajador"];
            $tmp["mesa"] = $row["mesa"];
            $tmp["salon"] = $row["salon"];
            $tmp["personas"] = $row["personas"];
            $tmp["pkSalon"] = $row["pkSalon"];
            $resultado[] = $tmp;
        }
        echo json_encode($resultado);
    }

    private function _entregaPedidoPantalla(){
        $db = new SuperDataBase();
        $usuario = UserLogin::get_id();
        // '".DATE(NOW())." ".DATE_FORMAT(NOW( ), "%H:%i:%S" )."'
        $datetime = date("Y-m-d H:i:s");
        $query_entrega = "Update detallepedido set estado = 2, horaTermino = '".$datetime."', pkCocinero = '".$usuario."' where pkDetallePedido = '".$_REQUEST["id"]."'";
        $db->executeQuery($query_entrega);
        //Revisamos si no existe la cookie de impresion
        if(isset($_COOKIE["impresion_pantalla"])){
            $query_impresion = "Insert into cola_impresion values(NULL,'".$_REQUEST["id"]."','PED','".$_COOKIE["t"]."',NULL,0)";
            $db->executeQuery($query_impresion);
        }
        echo json_encode(1);
    }

    private function _anulaPedidoPantalla(){
        $db = new SuperDataBase();
        $usuario = UserLogin::get_id();
        $query_entrega = "Update detallepedido set estado = 3, pkCocinero = '".$usuario."', pkMozo = '".$usuario."' where pkDetallePedido = '".$_REQUEST["id"]."'";
        $db->executeQuery($query_entrega);
        //Revisamos si no existe la cookie de impresion
        if(isset($_COOKIE["anulacion_pantalla"])){
            $query_impresion = "Insert into cola_impresion values(NULL,'".$_REQUEST["id"]."','ANU','".$_COOKIE["t"]."',NULL,0)";
            $db->executeQuery($query_impresion);
        }
        echo json_encode(1);
    }

    private function _obtieneHistorialPantalla(){
        $db = new SuperDataBase();
        $tipos = "";
        $consulta_tipos = "";
        $inicio = 0;
        $fin = 100;
        if(isset($_REQUEST["tipos"])){
            $consulta_tipos .= " AND (";
            $tipos = $_REQUEST["tipos"];
            $tipos_array = explode(",",$tipos);
            foreach($tipos_array as $tp){
                $consulta_tipos .= "pl.pkTipo = '".$tp."' OR ";
            }
            $consulta_tipos = substr($consulta_tipos,0,-3);
            $consulta_tipos .= ")";
        }

        if(isset($_REQUEST["inicio"])){
            $inicio = intval($_REQUEST["inicio"]);
        }

        if(isset($_REQUEST["fin"])){
            $fin = intval($_REQUEST["fin"]);
        }

        $db = new SuperDataBase();
        $query_pedidos = "Select dp.pkDetallePedido as id, dp.estado, dp.cantidad, dp.mensaje, dp.horaPedido as tiempo, 
        pl.descripcion as plato, tr.pkTrabajador as id_trabajador, tr.pkTipoTrabajador as tipo_trabajador, 
        CONCAT(tr.nombres, '', tr.apellidos) as nombre_trabajador, m.nmesa as mesa, s.nombre as salon, s.pkSalon as pkSalon FROM detallepedido dp, 
        pedido p, plato pl, trabajador tr, mesas m, salon s WHERE dp.pkMozo = tr.pkTrabajador AND dp.pkPlato = pl.pkPlato AND 
        dp.pkPediido = p.pkPediido AND p.pkMesa = m.pkMesa AND m.pkSalon = s.pkSalon AND 
        dp.estado > 1" . $consulta_tipos . " LIMIT $inicio,$fin";
        $resultado = array();
        $result = $db->executeQuery($query_pedidos);
        while ($row = $db->fecth_array($result)) {
            $tmp = array();
            $tmp["id"] = $row["id"];
            $tmp["estado"] = $row["estado"];
            $tmp["cantidad"] = $row["cantidad"];
            $tmp["mensaje"] = $row["mensaje"];
            $tmp["tiempo"] = $row["tiempo"];
            $tmp["plato"] = $row["plato"];
            $tmp["id_trabajador"] = $row["id_trabajador"];
            $tmp["tipo_trabajador"] = $row["tipo_trabajador"];
            $tmp["nombre_trabajador"] = $row["nombre_trabajador"];
            $tmp["mesa"] = $row["mesa"];
            $tmp["salon"] = $row["salon"];
            $tmp["pkSalon"] = $row["pkSalon"];
            $resultado[] = $tmp;
        }
        echo json_encode($resultado);
    }

    private function _obtieneHistorialDelDiaPantalla(){
        $db = new SuperDataBase();
        $tipos = "";
        $consulta_tipos = "";
        $inicio = 0;
        $fin = 100;
        if(isset($_REQUEST["tipos"])){
            $consulta_tipos .= " AND (";
            $tipos = $_REQUEST["tipos"];
            $tipos_array = explode(",",$tipos);
            foreach($tipos_array as $tp){
                $consulta_tipos .= "pl.pkTipo = '".$tp."' OR ";
            }
            $consulta_tipos = substr($consulta_tipos,0,-3);
            $consulta_tipos .= ")";
        }

        if(isset($_REQUEST["inicio"])){
            $inicio = intval($_REQUEST["inicio"]);
        }

        if(isset($_REQUEST["fin"])){
            $fin = intval($_REQUEST["fin"]);
        }

        $db = new SuperDataBase();
        $query_pedidos = "Select dp.pkDetallePedido as id, dp.estado, dp.cantidad, dp.mensaje, dp.horaPedido as tiempo, 
        pl.descripcion as plato, tr.pkTrabajador as id_trabajador, tr.pkTipoTrabajador as tipo_trabajador, 
        CONCAT(tr.nombres, ' ', tr.apellidos) as nombre_trabajador, m.nmesa as mesa, s.nombre as salon FROM detallepedido dp, 
        pedido p, plato pl, trabajador tr, mesas m, salon s WHERE dp.pkMozo = tr.pkTrabajador AND dp.pkPlato = pl.pkPlato AND 
        dp.pkPediido = p.pkPediido AND p.pkMesa = m.pkMesa AND m.pkSalon = s.pkSalon AND
        dp.estado > 1 AND DATE(dp.horaPedido) = DATE(NOW())" . $consulta_tipos . "ORDER BY dp.horaTermino DESC LIMIT $inicio,$fin";
        $resultado = array();
        $result = $db->executeQuery($query_pedidos);
        while ($row = $db->fecth_array($result)) {
            $tmp = array();
            $tmp["id"] = $row["id"];
            $tmp["estado"] = $row["estado"];
            $tmp["cantidad"] = $row["cantidad"];
            $tmp["mensaje"] = $row["mensaje"];
            $tmp["tiempo"] = $row["tiempo"];
            $tmp["plato"] = $row["plato"];
            $tmp["id_trabajador"] = $row["id_trabajador"];
            $tmp["tipo_trabajador"] = $row["tipo_trabajador"];
            $tmp["nombre_trabajador"] = $row["nombre_trabajador"];
            $tmp["mesa"] = $row["mesa"];
            $tmp["salon"] = $row["salon"];
            $resultado[] = $tmp;
        }
        echo json_encode($resultado);
    }

    private function _obtieneTipoPlatoPantalla(){
        $db = new SuperDataBase();
        $query_tipos = "Select * from tipos where estado = 0";
        $resultado = array();
        $result = $db->executeQuery($query_tipos);
        while ($row = $db->fecth_array($result)) {
            $tmp = array();
            $tmp["id"] = $row["pkTipo"];
            $tmp["descripcion"] = $row["descripcion"];
            $resultado[] = $tmp;    
        }
        echo json_encode($resultado);
    }

    //Funcion para activar o desactivar la salida por impresora de la impresion por pantalla
    function _cookieImpresionPantalla(){
        $funcion = "";
        $estado = 0;

        if(isset($_REQUEST["f"])){
            $funcion = $_REQUEST["f"];
        }

        if(isset($_REQUEST["e"])){
            $estado = intval($_REQUEST["e"]);
        }

        if($estado === 0){
            if($funcion === "imp"){
                unset($_COOKIE['impresion_pantalla']);
                setcookie('impresion_pantalla', '', time() - 3600, '/');
            }
            if($funcion === "anu"){
                unset($_COOKIE['anulacion_pantalla']);
                setcookie('anulacion_pantalla', '', time() - 3600, '/');
            }
        }else{
            if($funcion === "imp"){
                setcookie("impresion_pantalla", "CookieValue", 2147483647);
            }
            if($funcion === "anu"){
                setcookie("anulacion_pantalla", "CookieValue", 2147483647);
            }
        }

        echo $funcion.": ".$estado;
    }
    
    //Funcion para cambiar precio de un detalle en pantalla pedido
    private function _cambiaPrecio(){
        $usuario = UserLogin::get_id();
        $db = new SuperDataBase();
        $query_actualiza = "Update detallepedido set precio = '".$_REQUEST["precio_nuevo"]."' where pkDetallePedido = '".$_REQUEST["pk_detalle"]."'";
        $query_testigo = "Insert into cambio_de_precio values(NULL,'".$_REQUEST["pk_detalle"]."','".$_REQUEST["pk_pedido"]."','".$_REQUEST["precio_anterior"]."','".$_REQUEST["precio_nuevo"]."','".$usuario."', '".$_COOKIE["c"]."')";
        $db->executeQuery($query_actualiza);
        $db->executeQuery($query_testigo);
        echo "1";
    }
    
    //Funcion para ver si hay algun pedido abierto en mesa
    
    private function _checkOpenMesa(){
        $db = new SuperDataBase();
        $query_abierto = "Select * from pedido where pkMesa = '".$_REQUEST["mesa"]."' AND estado = '0' order by pkPediido DESC LIMIT 1";
        $r_abierto = $db->executeQuery($query_abierto);
        if($row_a = $db->fecth_array($r_abierto)){
            //Porciacaso mantenemos estado de mesa
            
            $resultado = array();
            $resultado["estado"] = 1;
            $resultado["moso"] = $row_a["idUser"];
            echo json_encode($resultado);
        }else{
            $resultado = array();
            $resultado["estado"] = 0;
            echo json_encode($resultado);
        }
    }

    //Funcion para maquillar detalles para facturacion electronica
    //Yoyo miau 2018
    private function _magiaDetalleFE(){
        $objModelPedidos = new Application_Models_PedidosModel();
        $objModelPedidos->magiaDetalleFE($_REQUEST['pkDet'],$_REQUEST['pkpN'],$_REQUEST['preN'],$_REQUEST['caN'],$_REQUEST['pkP'],$_REQUEST["agrupar"]);
    }
    
    private function _getDetallesFE(){
        $objModelPedidos = new Application_Models_PedidosModel();
        $objModelPedidos->listMagiaFE($_REQUEST['pkDet']);
    }
    
    private function _delDetalleFE(){
        $objModelPedidos = new Application_Models_PedidosModel();
        $objModelPedidos->delMagiaFE($_REQUEST['pkCambio']);
    }
    
    private function _cleanMagiaFE(){
        $objModelPedidos = new Application_Models_PedidosModel();
        $objModelPedidos->cleanMagiaFE($_REQUEST['pkDet']);
    }
    
    //Funcion para agregar pagos de creditos al dia actual
    //Gino Lluen 2018
    private function _ajustaCredito(){
        $objModelPedidos = new Application_Models_PedidosModel();
        $objModelPedidos->ajustaCredito($_REQUEST['pkPedido']);
    }
    
    //Enviar Pedidos A Cada Impresora
    //Actualizado en Agosto 2019 para obtener pedidos pendientes desde servidor
    private function _enviaPedidos() {
        $db = new SuperDataBase();
        //Antes de todo verificamos si el pedido sigue abierto
        $r_abierto = $db->executeQuery("Select * from pedido where pkPediido = '".$_REQUEST["pkPedido"]."' AND estado = 0");
        if($row_abierto = $db->fecth_array($r_abierto)){
            $objModelPedidos = new Application_Models_PedidosModel();
            $detallePedido = new Application_Models_DetallePedidosModel();
            $query_pedidos = "Select * from detallepedido where pkPediido = '".$_REQUEST["pkPedido"]."' AND estado = 0";
            $pendientes = $db->executeQuery($query_pedidos);
            while($row_p = $db->fecth_array($pendientes)){
                $detallePedido->updateEstados($row_p['pkDetallePedido'], "1");
                $objModelPedidos->agregaPedidoCola($row_p['pkDetallePedido'],$_REQUEST["terminal"]); 
            }
            //Devolvemos Resultado
            $resultado = array();
            $resultado["exito"] = 1;
            echo json_encode($resultado);
        }else{
            //Devolvemos Resultado
            $resultado = array();
            $resultado["exito"] = 0;
            echo json_encode($resultado);
        }
    }

    private function _AnulaPedido() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelPedidos = new Application_Models_PedidosModel();
                $objModelPedidos->AnulaPedido($_REQUEST['id']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    //Libra Mesa
    private function _cancelaPedido() {
        ERROR_REPORTING(E_ALL);
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $db = new SuperDataBase();
                //Antes de todo verificamos si el pedido sigue abierto
                $r_abierto = $db->executeQuery("Select p.*, m.nmesa as mesa, s.nombre as salon from pedido p, mesas m, salon s where p.pkPediido = '".$_REQUEST["pkPedido"]."' AND p.estado = 0 AND p.pkMesa = m.pkMesa AND m.pkSalon = s.pkSalon");
                if($row_abierto = $db->fecth_array($r_abierto)){
                    //Primero anulamos todos los detalles
                    $lista_detalles = "<ul>";
                    $total_anulado = 0;
                    //$query_pedidos = "Select * from detallepedido where pkPediido = '".$_REQUEST["pkPedido"]."'";
                    $query_pedidos = "Select dp.*, pl.descripcion from detallepedido dp, plato pl where dp.pkPediido = '".$_REQUEST["pkPedido"]."' AND dp.pkPlato = pl.pkPlato";
                    $detalles = $db->executeQuery($query_pedidos);
                    while($row_d = $db->fecth_array($detalles)){
                        //Si el pedido no habia sido enviado
                        if(intval($row_d["estado"]) === 0){
                            //Devolvemos stock (Si tiene)
                            //Primero verificamos stock manual
                            $rstock = $db->executeQuery("Select * from plato_stock where CONVERT(id_plato USING utf8) COLLATE utf8_spanish2_ci = CONVERT('".$row_d["pkPlato"]."' USING utf8) COLLATE utf8_spanish2_ci");
                            if($rows = $db->fecth_array($rstock)){
                                //Actualizamos stock
                                $nstock = intval($rows["stock"]) + intval($row_d["cantidad"]);
                                $db->executeQuery("Update plato_stock set stock = '".$nstock."' where id = '".$rows["id"]."'");              
                            }else{
                                //Luego verificamos stock menu
                                $mstock = $db->executeQuery("Select * from componente_menu where CONVERT(pk_plato USING utf8) COLLATE utf8_spanish2_ci = CONVERT('".$row_d["pkPlato"]."' USING utf8) COLLATE utf8_spanish2_ci AND estado = 1 AND fecha_inicio <= '".date("Y-m-d")."' AND fecha_fin >= '".date("Y-m-d")."'");
                                if($rowss = $db->fecth_array($mstock)){
                                    //Actualizamos stock
                                    $nstock = intval($rowss["stock"]) + intval($row_d["cantidad"]);
                                    $db->executeQuery("Update componente_menu set stock = '".$nstock."' where id = '".$rowss["id"]."'");
                                }
                            }
                            //Eliminamos pedido de base de datos
                            $db->executeQuery("Delete from detallepedido where pkDetallePedido = '".$row_d["pkDetallePedido"]."'");
                        }

                        //Si el pedido ya habia sido enviado
                        if((intval($row_d["estado"]) > 0) && (intval($row_d["estado"]) < 3)){
                            //Devolvemos stock (Si tiene)
                            //Primero verificamos stock manual
                            $rstock = $db->executeQuery("Select * from plato_stock where CONVERT(id_plato USING utf8) COLLATE utf8_spanish2_ci = CONVERT('".$row_d["pkPlato"]."' USING utf8) COLLATE utf8_spanish2_ci");
                            if($rows = $db->fecth_array($rstock)){
                                //Actualizamos stock
                                $nstock = intval($rows["stock"]) + intval($row_d["cantidad"]);
                                $db->executeQuery("Update plato_stock set stock = '".$nstock."' where id = '".$rows["id"]."'");              
                            }else{
                                //Luego verificamos stock menu
                                $mstock = $db->executeQuery("Select * from componente_menu where CONVERT(pk_plato USING utf8) COLLATE utf8_spanish2_ci = CONVERT('".$row_d["pkPlato"]."' USING utf8) COLLATE utf8_spanish2_ci AND estado = 1 AND fecha_inicio <= '".date("Y-m-d")."' AND fecha_fin >= '".date("Y-m-d")."'");
                                if($rowss = $db->fecth_array($mstock)){
                                    //Actualizamos stock
                                    $nstock = intval($rowss["stock"]) + intval($row_d["cantidad"]);
                                    $db->executeQuery("Update componente_menu set stock = '".$nstock."' where id = '".$rowss["id"]."'");
                                }
                            }
                            //Eliminamos si hubo un cambio de facturacion
                            $db->executeQuery("SET SQL_SAFE_UPDATES = 0;");
                            $db->executeQuery("Delete from cambio_facturacion where pk_detalle = '".$row_d["pkDetallePedido"]."'");
                            //Anulamos Detalle
                            $objUser = new UserLogin();
                            $db->executeQuery("update detallepedido set estado=3, mensaje = '".$_REQUEST["razon"]."', fechaPedido=now(), pkCocinero='".$objUser->get_id()."' where pkDetallePedido = '".$row_d["pkDetallePedido"]."'");
                            //Ponemos en cola de impresion
                            $objModelPedidos = new Application_Models_PedidosModel();
                            $objModelPedidos->quitaPedidoCola($row_d["pkDetallePedido"],$_REQUEST["terminal"]);
                            //Agregamos a Listado para correo
                            $lista_detalles .= "<li>(".$row_d["cantidad"].") ".$row_d["descripcion"]."</li>";
                            $total_anulado = $total_anulado + (floatval($row_d["cantidad"])*floatval($row_d["precio"]));
                        }
                        
                        //Si el pedido ya estaba anulado ya ni pa que
                    }
                    $lista_detalles .= "</ul>";

                    //Anulamos pedido en si
                    $db->executeQuery("update pedido set estado=3 where pkPediido = '".$_REQUEST["pkPedido"]."'");
                    $db->executeQuery("update mesas set estado=0 where pkMesa = '".$_REQUEST["pkMesa"]."'");

                    //Insertamos el pedido en que caja fue hecho
                    $query_caja = "Insert into accion_caja values(NULL,'".$_REQUEST["pkPedido"]."','PED','".$_COOKIE["c"]."')";
                    $db->executeQuery($query_caja);

                    //Enviamos Correo
                    $objModelPedidos = new Application_Models_PedidosModel();
                    $objModelPedidos->envia_correo_liberacion($_REQUEST["pkPedido"],$lista_detalles,$total_anulado,$row_abierto["mesa"],$row_abierto["salon"],$_REQUEST["razon"]);


                    //Devolvemos Resultado
                    $resultado = array();
                    $resultado["exito"] = 1;
                    echo json_encode($resultado);
                }else{
                    //Devolvemos Resultado
                    $resultado = array();
                    $resultado["exito"] = 0;
                    echo json_encode($resultado);
                }
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showPedidos() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objViewPedidos = new Application_Views_PedidosView();
                $objViewPedidos->showPedidos();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showPedidoPorPagar() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objViewPedidos = new Application_Views_PedidosView();
                $objViewPedidos->showPedidoPorPagar();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showPedidos2() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objViewPedidos = new Application_Views_PedidosView();
                $objViewPedidos->showPedidos2();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _showCocina() {

        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $view = new Application_Views_PedidosView();
                $view->showCocina();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _listAllProduct() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objViewPedidos = new Application_Models_ProductosModel();
                $tipo = "";
                $page = "";
                $size = "";
                if (isset($_REQUEST['page'])) {
                    $page = $_REQUEST['page'];
                }

                if (isset($_REQUEST['size'])) {
                    $size = $_REQUEST['size'];
                }
                if (!empty($_REQUEST['tipo'])) {
                    $tipo = $_REQUEST['tipo'];
                }
                $objViewPedidos->listAllProduct($tipo, $page, $size);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
    
    //Agregado por Gino Lluen
    //Los odio a todos los ineptos que hicieron esto
    //Valen mierda, mueranse
    
    private function _componentesPorTipo() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objViewPedidos = new Application_Models_ProductosModel();
                $objViewPedidos->componentesPorTipo($_REQUEST["idt"]);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
    private function _listTiposMenus() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objViewPedidos = new Application_Models_ProductosModel();
                $objViewPedidos->listAllTipoMenu();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
    private function _listAllDescripcion() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objViewPedidos = new Application_Models_ProductosModel();
                $descripcion = "";
                $page = "";
                $size = "";
                if (isset($_REQUEST['page'])) {
                    $page = $_REQUEST['page'];
                }

                if (isset($_REQUEST['size'])) {
                    $size = $_REQUEST['size'];
                }
                if (!empty($_REQUEST['descripcion'])) {
                    $descripcion = $_REQUEST['descripcion'];
                }
                $objViewPedidos->listAllProductDescripcion($descripcion, $page, $size);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _listPedidos() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelPedidos = new Application_Models_PedidosModel();
                $objModelPedidos->listPedidos($_GET['mesa']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _listPedidosPk() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelPedidos = new Application_Models_PedidosModel();
                $objModelPedidos->listPedidosPk($_GET['pkPedido']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _listAllPedidos() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelPedidos = new Application_Models_PedidosModel();
                $objModelPedidos->listAllPedidos();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _listItemPedidos2() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelPedidos = new Application_Models_PedidosModel();
                $objModelPedidos->listItemPedidos2($_GET['comprobante']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _listItemPedidos() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelPedidos = new Application_Models_PedidosModel();
                $objModelPedidos->listItemPedidos($_GET['comprobante']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _detallesVenta()
    {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $id = $_POST['id'];
                $objModelPedidos = new Application_Models_PedidosModel();
                $detalles = $objModelPedidos->detallesVenta($id);
                echo json_encode($detalles);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    /**
     * Llamar a la funcion addPedido
     */
    private function _AddPedido() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelPedidos = new Application_Models_PedidosModel();
                $resultado = $objModelPedidos->addPedido($_POST['cantidad'], $_POST['precio_venta'], $_POST['fkPedido'], $_POST['pkComprobante'], $_POST['tipo'], $_POST["mensaje"]);
                echo json_encode($resultado);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    /**
     * Llamar a la funcion delete pedido
     */
    //Eliminamos detalle de un pedido pero
    //Verificamos Si el pedido sigue activo
    //Y Tambien verificamos si el detalle ya fue anulado
    private function _DeletePedido() {        
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelPedidos = new Application_Models_PedidosModel();
                $db = new SuperDataBase();
                //Antes de todo verificamos si el pedido sigue abierto
                $r_abierto = $db->executeQuery("Select * from pedido where pkPediido = '".$_REQUEST["idPedido"]."' AND estado = 0");
                if($row_abierto = $db->fecth_array($r_abierto)){
                    $array = json_decode($_REQUEST['array'], true);
                    $exito_total = 1;
                    for ($i = 0; $i < count($array); $i++){
                        //Verificamos si el pedido no ha sido ya anulado
                        $r_valido= $db->executeQuery("Select dp.*, pl.descripcion, m.nmesa, s.nombre as salon from detallepedido dp, plato pl, pedido p, mesas m, salon s where dp.pkDetallePedido = '".$array[$i]['pkPedido']."' AND dp.estado < 3 AND dp.pkPlato = pl.pkPlato AND dp.pkPediido = p.pkPediido AND p.pkMesa = m.pkMesa AND m.pkSalon = s.pkSalon");
                        if($row_valido = $db->fecth_array($r_valido)){
                            //Primero verificamos stock manual               
                            $rstock = $db->executeQuery("Select * from plato_stock where CONVERT(id_plato USING utf8) COLLATE utf8_spanish2_ci = CONVERT('".$array[$i]['pkProducto']."' USING utf8) COLLATE utf8_spanish2_ci");
                            if($rows = $db->fecth_array($rstock)){
                                //Actualizamos stock
                                $nstock = intval($rows["stock"]) + intval($array[$i]['cantidad']);
                                $db->executeQuery("Update plato_stock set stock = '".$nstock."' where id = '".$rows["id"]."'");              
                            }else{
                                //Verificamos menu
                                $mstock = $db->executeQuery("Select * from componente_menu where CONVERT(pk_plato USING utf8) COLLATE utf8_spanish2_ci = CONVERT('".$array[$i]['pkProducto']."' USING utf8) COLLATE utf8_spanish2_ci AND estado = 1 AND fecha_inicio <= '".date("Y-m-d")."' AND fecha_fin >= '".date("Y-m-d")."'");
                                if($rowss = $db->fecth_array($mstock)){
                                    //Actualizamos stock
                                    $nstock = intval($rowss["stock"]) + intval($array[$i]['cantidad']);
                                    $db->executeQuery("Update componente_menu set stock = '".$nstock."' where id = '".$rowss["id"]."'");
                                }
                            }
                            
                            //Si es estado CERO solo eliminamos
                            //Si es otro estado eliminamos e imprimimos anulacion
                            if ($array[$i]['estado'] == "0"){
                                $objModelPedidos ->eliminaEstadocero($array[$i]['pkPedido']);                        
                            }else{
                                if (UserLogin::get_pkTypeUsernames() == "8" || UserLogin::get_pkTypeUsernames() == "1" || UserLogin::get_pkTypeUsernames() == "2" || UserLogin::get_pkTypeUsernames() == "9"){
                                    $objModelPedidos->deletePedido($array[$i]['pkPedido'], $_REQUEST["razon"]);
                                    $objModelPedidos->quitaPedidoCola($array[$i]['pkPedido'],$_REQUEST["terminal"]);

                                    //Finalmente Enviamos Correo
                                    $objModelPedidos->envia_correo_anulacion($row_valido["pkPediido"],$row_valido["cantidad"], $row_valido["descripcion"], $row_valido["nmesa"], $row_valido["salon"], $_REQUEST["razon"]);        
                                }
                            }
                        }else{
                            $pos = strpos($array[$i]['pkPedido'], "C");
                            if($pos === false){
                                $exito_total = 2;
                            }else{
                                $objModelPedidos->deletePedido($array[$i]['pkPedido'], $_REQUEST["razon"]);
                            }
                        }                     
                    }
                    //Devolvemos Resultado
                    $resultado = array();
                    $resultado["exito"] = $exito_total;
                    echo json_encode($resultado);
                }else{
                    //Devolvemos Resultado
                    $resultado = array();
                    $resultado["exito"] = 0;
                    echo json_encode($resultado);
                }
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
    //Para restar 1 unidad del pedido
    private function _DeletePedido1() {        
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelPedidos = new Application_Models_PedidosModel();
                $db = new SuperDataBase();
                //Antes de todo verificamos si el pedido sigue abierto
                $r_abierto = $db->executeQuery("Select * from pedido where pkPediido = '".$_REQUEST["idPedido"]."' AND estado = 0");
                if($row_abierto = $db->fecth_array($r_abierto)){
                    $array = json_decode($_REQUEST['array'], true);
                    $exito_total = 1;
                    for ($i = 0; $i < count($array); $i++){
                        //Verificamos si el pedido no ha sido ya anulado
                        $r_valido= $db->executeQuery("Select dp.*, pl.descripcion, m.nmesa, s.nombre as salon from detallepedido dp, plato pl, pedido p, mesas m, salon s where dp.pkDetallePedido = '".$array[$i]['pkPedido']."' AND dp.estado < 3 AND dp.pkPlato = pl.pkPlato AND dp.pkPediido = p.pkPediido AND p.pkMesa = m.pkMesa AND m.pkSalon = s.pkSalon");
                        if($row_valido = $db->fecth_array($r_valido)){
                            //Primero verificamos stock manual
                            $rstock = $db->executeQuery("Select * from plato_stock where CONVERT(id_plato USING utf8) COLLATE utf8_spanish2_ci = CONVERT('".$array[$i]['pkProducto']."' USING utf8) COLLATE utf8_spanish2_ci");
                            if($rows = $db->fecth_array($rstock)){
                                //Actualizamos stock
                                $nstock = intval($rows["stock"]) + 1;
                                $db->executeQuery("Update plato_stock set stock = '".$nstock."' where id = '".$rows["id"]."'");              
                            }else{
                                //Verificamos menu
                                $mstock = $db->executeQuery("Select * from componente_menu where CONVERT(pk_plato USING utf8) COLLATE utf8_spanish2_ci = CONVERT('".$array[$i]['pkProducto']."' USING utf8) COLLATE utf8_spanish2_ci AND estado = 1 AND fecha_inicio <= '".date("Y-m-d")."' AND fecha_fin >= '".date("Y-m-d")."'");
                                if($rowss = $db->fecth_array($mstock)){
                                    //Actualizamos stock
                                    $nstock = intval($rowss["stock"]) + 1;
                                    $db->executeQuery("Update componente_menu set stock = '".$nstock."' where id = '".$rowss["id"]."'");
                                }
                            }
        
                            //Si es estado CERO solo eliminamos
                            //Si es otro estado eliminamos e imprimimos anulacion
                            if ($array[$i]['estado'] == "0"){
                                $objModelPedidos ->eliminaEstadocero1($array[$i]['pkPedido'],$array[$i]['cantidad']);          
                            }else{
                                if (UserLogin::get_pkTypeUsernames() == "8" || UserLogin::get_pkTypeUsernames() == "1" || UserLogin::get_pkTypeUsernames() == "2" || UserLogin::get_pkTypeUsernames() == "9"){
                                    $idn = $objModelPedidos->deletePedido1($array[$i]['pkPedido'],$array[$i]['cantidad'],$array[$i]['precio'],$_REQUEST["idPedido"],$array[$i]['pkProducto'],$_REQUEST["razon"]);
                                    $objModelPedidos->quitaPedidoCola($idn,$_REQUEST["terminal"]);

                                    //Finalmente Enviamos Correo
                                    $objModelPedidos->envia_correo_anulacion($row_valido["pkPediido"],1, $row_valido["descripcion"], $row_valido["nmesa"], $row_valido["salon"], $_REQUEST["razon"]);
                                }
                            }
                        }else{
                            $pos = strpos($array[$i]['pkPedido'], "C");
                            if($pos === false){
                                $exito_total = 2;
                            }else{
                                $objModelPedidos->deletePedido($array[$i]['pkPedido'], $_REQUEST["razon"]);                             
                            }
                        }                     
                    }
                    //Devolvemos Resultado
                    $resultado = array();
                    $resultado["exito"] = $exito_total;
                    echo json_encode($resultado);
                }else{
                   //Devolvemos Resultado
                   $resultado = array();
                   $resultado["exito"] = 0;
                   echo json_encode($resultado); 
                }
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    /**
     * Llamar a la funcion addPedido
     */
    private function _aperturarMesa() {
        //Funcion para aperturar una mesa
        //Actualizada Agosto 2019 - Gino Lluen
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                //Primero verificamos si la mesa que queremos abrir no esta en uso
                $db = new SuperDataBase();
                $objUser = new UserLogin();
                $query_abierto = "Select * from pedido where pkMesa = '".$_REQUEST["pkMesa"]."' AND estado = '0' order by pkPediido DESC LIMIT 1";
                $r_abierto = $db->executeQuery($query_abierto);
                if($row_a = $db->fecth_array($r_abierto)){
                    //Si la mesa esta abierta 
                    $resultado = array();
                    $resultado["estado"] = 1;
                    $resultado["moso"] = $row_a["idUser"];
                    echo json_encode($resultado);
                }else{
                    //Obtenemos Fecha Cierre Actual
                    $fecha_cierre = "";
                    $query_cierre= "Select * from cierrediario where pkCierreDiario = 1";
                    $res_cierre = $db->executeQuery($query_cierre);
                    if($rw4 = $db->fecth_array($res_cierre)){
                        $fecha_cierre = $rw4["fecha"];
                    }

                    //Si la mesa esta cerrada abrimos
                    $query_apertura = "Insert into pedido values(NULL,now(),NULL,'".$_REQUEST["pkMesa"]."',0,NULL,NULL,now(),NULL,0,'".$_REQUEST['nmesa']."',0,'".$objUser->get_id()."','".$fecha_cierre."',NULL,0,0,NULL,'',2)";
                    $db->executeQuery($query_apertura);

                    //Obtenemos id pedido              
                    $pkPedido = $db->getId();

                    //Abrimos mesa
                    $query_mesa = "Update mesas set estado = 1 WHERE pkMesa = '".$_REQUEST["pkMesa"]."'";
                    $db->executeQuery($query_mesa);

                    //Aqui asignamos el cliente a la mesa tanto si es en salon o llevar o delivery
                    $objModelCliente = new Application_Models_ClienteModel();
                    if(intval($_REQUEST["tipoPedido"]) == 2){    
                        $objModelCliente->updateAsignCliente($_REQUEST["telefonoCliente"],$_REQUEST["nombreCliente"],$_REQUEST["direccionCliente"],$_REQUEST["documentoCliente"],$pkPedido);
                    }else{
                        $objModelCliente->updateClienteSalon($_REQUEST["clmesa"],$pkPedido);
                    }

                    //Imprimimos Resultado
                    $resultado = array();
                    $resultado["estado"] = 0;
                    $resultado["pkPedido"] = $pkPedido;
                    echo json_encode($resultado);
                }
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _UpdateNPersonas() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objUser = new UserLogin();
                $objModelPedidos = new Application_Models_PedidosModel();
                $objModelPedidos->updateNPersonas($_POST['pkComprobante'], $_POST['npersonas']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _UpdateTipoPedido() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objUser = new UserLogin();
                $objModelPedidos = new Application_Models_PedidosModel();
                $array = json_decode($_REQUEST['array'], true);

                for ($i = 0; $i < count($array); $i++) {
                    $objModelPedidos->updateTipoPedido($array[$i]['pkPedido'], 1);
                }
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _UpdatePedido() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelPedidos = new Application_Models_PedidosModel();
                if ($_POST['estado'] != '0')
                    $objModelPedidos->updatePedido($_POST['pkPedido'], $_POST['cantidad'], $_POST['precio'], $_REQUEST['pedido']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _saveMessagePedido() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelPedidos = new Application_Models_PedidosModel();
                $objModelPedidos->saveMessagesPedido($_POST['pkPedido'], utf8_encode($_POST['message']));
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _PedidosImprimir() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelPedidos = new Application_Models_PedidosModel();
                $objModelPedidos->Imprimir($_REQUEST['mesa']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _ShowMesas() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelPedidos = new Application_Views_PedidosView();
                $objModelPedidos->showMesas();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _ShowBar() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelPedidos = new Application_Views_PedidosView();
                $objModelPedidos->showBar();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    //Cancela pedido por credito
    private function _cancelaPedidoCredito() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelPedidos = new Application_Models_PedidosModel();
                $objModelTrabajador = new Application_Models_WorkPeopleModel();
                $documento = null;
                if (intval($_REQUEST['tipo_cliente']) == "1") {
                    $documento = $objModelTrabajador->_verficaPersona($_REQUEST['documento'], $_REQUEST['valor1'], $_REQUEST['valor2'], '');
                } else {
                        $documento = $_REQUEST['documento'];
                        $objModelTrabajador->_verficaPersonaJuridica($_REQUEST['documento'], $_REQUEST['valor1'], $_REQUEST['valor2'],'');
                }
                $objModelPedidos->CancelaPedidoCredito($_POST['pkPedido'], $_REQUEST['tipo_cliente'], $documento, $_REQUEST['total']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    //Cancela pedidos por consumo
    private function _cancelaPedidoACuenta() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelPedidos = new Application_Models_PedidosModel();
                $objModelTrabajador = new Application_Models_WorkPeopleModel();
                $documento = null;
                if ($_REQUEST['tipo_cliente'] == "1") {
                    $documento = $objModelTrabajador->_verficaPersona($_REQUEST['documento'], $_REQUEST['valor1'], $_REQUEST['valor2'], '');
                } else {
                    if (isset($_REQUEST['documento'])) {
                        $documento = $_REQUEST['documento'];
                        $objModelTrabajador->_verficaPersonaJuridica($_REQUEST['documento'], $_REQUEST['valor1'], $_REQUEST['valor2'],'');
                    }
                }
                $objModelPedidos->CancelaPedidoACuenta($_POST['pkPedido'], $_REQUEST['tipo_cliente'], $documento, $_REQUEST['total']);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _changeMesa() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelPedidos = new Application_Models_PedidosModel();
                $db = new SuperDataBase();
                $destino = "";
               //Antes de todo verificamos si el pedido sigue abierto
               $r_abierto = $db->executeQuery("Select * from pedido where pkPediido = '".$_REQUEST['pkPedido']."' AND estado = 0");
               if($row_abierto = $db->fecth_array($r_abierto)){
                    if ($_REQUEST['array'] <> "0") {
                        //Cuando me llevo parte del pedido
                        $array = json_decode($_REQUEST['array'], true);                    
                        if($_REQUEST['mesaActual'] <> "TMP"){
                            $destino = $_REQUEST["mesaActual"];
                            $objModelPedidos = new Application_Models_PedidosModel();
                            $pkPedidoNuevo = $objModelPedidos->listpkPedido($_REQUEST['mesaActual']);
                            //Ahora validamos si hay pedido o no en la mesa desde el servidor
                            if(intval($pkPedidoNuevo)>0){
                                //Cuando la mesa de destino esta abierta
                                for ($i = 0; $i < count($array); $i++) {
                                    $pos = strpos($array[$i]['pkPedido'], "C");
                                    if ($pos === false) {
                                        $consulta = "update detallepedido set pkPediido='$pkPedidoNuevo' where pkDetallePedido='" . $array[$i]['pkPedido'] . "';";
                                        $db->executeQuery($consulta);
                                    }else{
                                        $nid = substr($array[$i]['pkPedido'],1);
                                        $nid = intval($nid);
                                        $consulta = "update cambio_facturacion set pk_pedido_destino = '$pkPedidoNuevo' where id = '".$nid."'";
                                        $db->executeQuery($consulta);
                                    }
                                }
                            }else{
                                //Cuando la mesa de destino esta cerrada
                                $pkPedido = $objModelPedidos->aperturarMesa($_REQUEST['mesaActual'], $row_abierto["idUser"]);
                                for ($i = 0; $i < count($array); $i++) {
                                    $pos = strpos($array[$i]['pkPedido'], "C");
                                    if ($pos === false) {
                                        $objModelPedidos->cambiaPedidoMesa($array[$i]['pkPedido'], $pkPedido);
                                    }else{
                                        $nid = substr($array[$i]['pkPedido'],1);
                                        $nid = intval($nid);
                                        $consulta = "update cambio_facturacion set pk_pedido_destino = '$pkPedido' where id = '".$nid."'";
                                        $db->executeQuery($consulta);
                                    }
                                }
                            }
                        }else{
                            $objMesa = new Application_Models_MesaModel();
                            $pkMesa_nuevo = $objMesa->getMesa($_REQUEST["pkSalon"]);
                            $pkMesa_nuevo = $pkMesa_nuevo[0]["mesa"];
                            $destino = $pkMesa_nuevo;

                            //Cuando es delivery o llevar obtenemos la mesa
                            //Siempre nos dara una mesa cerrada
                            $pkPedido = $objModelPedidos->aperturarMesa($pkMesa_nuevo, $row_abierto["idUser"]);
                            for ($i = 0; $i < count($array); $i++) {
                                $pos = strpos($array[$i]['pkPedido'], "C");
                                if ($pos === false) {
                                    $objModelPedidos->cambiaPedidoMesa($array[$i]['pkPedido'], $pkPedido);
                                }else{
                                    $nid = substr($array[$i]['pkPedido'],1);
                                    $nid = intval($nid);
                                    $consulta = "update cambio_facturacion set pk_pedido_destino = '$pkPedido' where id = '".$nid."'";
                                    $db->executeQuery($consulta);
                                }
                            }
                        }
                          
                        //Devolvemos Resultado
                        $resultado = array();
                        $resultado["exito"] = 1;
                        $resultado["tipo"] = "parcial";
                        $resultado["destino"] = $destino;
                        echo json_encode($resultado); 
                    }else{
                        //Cuando me llevo todo el pedido                        
                        if($_REQUEST['mesaActual'] <> "TMP"){
                            $destino = $_REQUEST["mesaActual"];
                            $objModelPedidos = new Application_Models_PedidosModel();
                            $pkPedidoNuevo = $objModelPedidos->listpkPedido($_REQUEST['mesaActual']);
                            //Ahora validamos si hay pedido o no en la mesa desde el servidor
                            if(intval($pkPedidoNuevo)>0){
                                //Cuando la mesa de destino esta abierta
                                $pkPedido = $_REQUEST['pkPedido'];
                                $db->executeQuery("SET SQL_SAFE_UPDATES = 0;");
                                $db->executeQuery("update detallepedido set pkPediido='$pkPedidoNuevo' where pkPediido='$pkPedido'");
                                $db->executeQuery("Update cambio_facturacion set pk_pedido_destino = '$pkPedidoNuevo' where pk_pedido_destino = '$pkPedido'");
                            }else{
                                //Cuando la mesa destino esta cerrada
                                $objModelPedidos->CambioMesa($_REQUEST['pkPedido'], $_REQUEST['mesaAnterior'], $_REQUEST['mesaActual']);
                            }
                        }else{
                            $objMesa = new Application_Models_MesaModel();
                            $pkMesa_nuevo = $objMesa->getMesa($_REQUEST["pkSalon"]);
                            $pkMesa_nuevo = $pkMesa_nuevo[0]["mesa"];
                            $destino = $pkMesa_nuevo;

                            //Cuando es delivery o llevar obtenemos la mesa
                            //Siempre nos dara una mesa cerrada
                            $objModelPedidos->CambioMesa($_REQUEST['pkPedido'], $_REQUEST['mesaAnterior'], $pkMesa_nuevo);
                        }
                        //Devolvemos Resultado
                        $resultado = array();
                        $resultado["exito"] = 1;
                        $resultado["tipo"] = "total";
                        $resultado["destino"] = $destino;
                        echo json_encode($resultado); 
                    }
               }else{
                    //Devolvemos Resultado
                    $resultado = array();
                    $resultado["exito"] = 0;
                    echo json_encode($resultado);
               }
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
    //Actualizado 2017 - Gino lluen
    private function _imprimeCuenta() {        
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelPedidos = new Application_Models_PedidosModel();
                $objModelPedidos->imprimeCuentaenCola($_REQUEST["pkPedido"],$_REQUEST["terminal"],$_REQUEST["tipo"],$_REQUEST["aux"]);            
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _imprimeCuentaPeluca() {        
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelPedidos = new Application_Models_PedidosModel();
                $objModelPedidos->imprimeCuentaenColaPeluca($_REQUEST["pkPedido"],$_REQUEST["terminal"],$_REQUEST["tipo"],$_REQUEST["aux"]);            
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
    //Funcion para agregar menus
    //Gino Lluen 2017
    private function _AddMenu() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelPedidos = new Application_Models_PedidosModel();
                $array = json_decode($_REQUEST['platos'], true);
                $resultado = null;
                for ($i = 0; $i < count($array); $i++) {
                    if(intval(current($array)['cantidad'])>0){
                        $resultado = $objModelPedidos->addPedido(current($array)['cantidad'], current($array)['precio'], key($array), $_POST['pkComprobante'], $_POST['tipo'],'');
                    }
                    next($array);
                }
                echo json_encode($resultado);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
    
    //funcion para agregar descuento
    private function _descuento() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objModelPedidos = new Application_Models_PedidosModel();
                $objModelPedidos->descuento($_POST["dsc"],$_POST["pk"]);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

}
