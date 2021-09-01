<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Models_PedidosModel {

    private $nMesa;

    function __construct() {
        
    }

    public function getNMesa() {
        return $this->nMesa;
    }

    public function setNMesa($nMesa) {
        $this->nMesa = $nMesa;
    }
    
    //Funcion para maquillar detalles para facturacion electronica
    //Yoyo miau 2018
    
    public function magiaDetalleFE($pkDetalle,$pkPlato,$precio,$cantidad,$pkPedido,$agrupar){
        echo "Holaaa";
        die(0);
        // error_reporting(E_ALL);  
        // $db = new SuperDataBase();
        // if($agrupar == "true"){                 
        //     $query_01 = "Insert into cambio_facturacion values(NULL,'".$pkDetalle."','".$pkPlato."','".$precio."','".$cantidad."','".$pkPedido."')";
        //     $db->executeQuery($query_01);
        //     echo "IEZ";
        // }else{
        //     for($i=0;$i<intval($cantidad);$i++){
        //         $query_01 = "Insert into cambio_facturacion values(NULL,'".$pkDetalle."','".$pkPlato."','".$precio."','1','".$pkPedido."')";
        //         $db->executeQuery($query_01);
        //         echo "IEZ ";
        //     }
        // }
    }
    
    public function listMagiaFE($pkDet) {
        $db = new SuperDataBase();
        $query = "Select cf.id, pl.descripcion, cf.cantidad_cambio, cf.precio_cambio from cambio_facturacion cf, plato pl where cf.pk_detalle = '".$pkDet."' AND cf.pk_plato_cambio = pl.pkPlato";
        $result = $db->executeQuery($query);

        $array = array();
        while ($row = $db->fecth_array($result)) {
            $array[] = array(
                "id" => $row['id'],
                "descripcion" => $row['descripcion'],
                "precio" => $row['precio_cambio'],
                "cantidad" => $row['cantidad_cambio'],
                "total" => floatval($row['precio_cambio'])*floatval($row['cantidad_cambio'])
            );
        }
        echo json_encode($array);
    }
    
    public function delmagiaFE($pkCambio){
        error_reporting(E_ALL);  
        $db = new SuperDataBase();
        $query_01 = "Delete from cambio_facturacion where id = '".$pkCambio."'";
        $db->executeQuery($query_01);
    }
    
    public function cleanMagiaFE($pkDetalle){
        error_reporting(E_ALL);  
        $db = new SuperDataBase();
        $db->executeQuery("SET SQL_SAFE_UPDATES = 0;");
        $query_01 = "Delete from cambio_facturacion where pk_detalle = '".$pkDetalle."'";
        $db->executeQuery($query_01);
        $db->executeQuery("SET SQL_SAFE_UPDATES = 1;");
    }
    
    
    //Funcion para agregar pagos de creditos al dia actual
    //Gino Lluen 2018
    
    public function ajustaCredito($pkPedido){
        error_reporting(E_ALL);
        $db = new SuperDataBase();
        $query_01 = "Select * from pedido where pkPediido = '".$pkPedido."'";
        $res_01 = $db->executeQuery($query_01);
        $total = 0;
        $descuento = 0;
        $total_tarjeta = 0;
        $total_efectivo = 0;
        $nombreTarjeta = "";
        $tipo_pago = 0;
        $documento = "";
        $tipo_cliente = 0;
        $pkMesa = 0;
        $idUser = 0;
        $fecha_cierre_pedido = "";
        //Actualizamos el pedido dejando todo en cero
        if($rw1 = $db->fecth_array($res_01)){
            
            $total = $rw1["total"];
            $descuento = $rw1["descuento"];
            $total_tarjeta = $rw1["total_tarjeta"];
            $total_efectivo = $rw1["total_efectivo"];
            $nombreTarjeta = $rw1["nombreTarjeta"];
            $tipo_pago = $rw1["tipo_pago"];
            $documento = $rw1["documento"];
            $tipo_cliente = $rw1["tipo_cliente"];
            $pkMesa = $rw1["pkMesa"];
            $idUser = $rw1["idUser"];
            $fecha_cierre_pedido = $rw1["fechaCierre"];
            
            $fecha_cierre = "";
            $query_04 = "Select * from cierrediario where pkCierreDiario = 1";
            $res_04 = $db->executeQuery($query_04);
            if($rw4 = $db->fecth_array($res_04)){
                $fecha_cierre = $rw4["fecha"];
            }

            echo $fecha_cierre;
            echo "-".$fecha_cierre_pedido;
            
            if($fecha_cierre <> $fecha_cierre_pedido){
                echo "GG";
                
                $query_02 = "Update pedido set total = '0.00', descuento = '0.00', nombreTarjeta = '-----------', total_tarjeta = '0.00', total_efectivo = '0.00', tipo_pago = '1', documento = '' , tipo_cliente = '1' where pkPediido = '".$pkPedido."'";
                $db->executeQuery($query_02);

                

                $query_05 = "Insert into pedido values(NULL,now(),NULL,'".$pkMesa."','".$total."',NULL,NULL,now(),now(),1,0,'".$descuento."','".$idUser."','".$fecha_cierre."','".$nombreTarjeta."','".$total_tarjeta."','".$total_efectivo."','".$tipo_pago."','".$documento."','".$tipo_cliente."')";

                $db->executeQuery($query_05);

                $nuevo_pedido = 0;
                //Obtenemos codigo del pedido
                $query_nuevo = "SELECT LAST_INSERT_ID() as niu";
                $res_nuevo = $db->executeQuery($query_nuevo);
                if($rwn = $db->fecth_array($res_nuevo)){
                    $nuevo_pedido = $rwn["niu"];
                }

                
                $query_06 = "Insert into creditos values (NULL,'".$pkPedido."','".$nuevo_pedido."')";
                $db->executeQuery($query_06);

                //Insertamos el pedido en que caja fue hecho
                $query_caja = "Insert into accion_caja values(NULL,'".$nuevo_pedido."','PED','".$_COOKIE["c"]."')";
                $db->executeQuery($query_caja);

                echo $nuevo_pedido;
            }else{
                echo "NN";
            }
        }else{
            echo "NEL";
        }
    }

    //Funcion para agregar pedido a cola de impresion
    //Gino Lluen 2017
    public function agregaPedidoCola($pkPedido,$term) {
        if(!isset($_COOKIE["impresion_pantalla"])){
            $db = new SuperDataBase();
            $query = "Insert into cola_impresion values(NULL,'".$pkPedido."','PED','".$term."',NULL,0)";
            //echo $query;
            $db->executeQuery($query);
        }
        //echo '1';
    }
    
    //Funcion para anular pedido en cola de impresion
    //Gino Lluen 2017
    public function quitaPedidoCola($pkPedido,$term) {
        if(!isset($_COOKIE["anulacion_pantalla"])){
            $db = new SuperDataBase();
            $query = "Insert into cola_impresion values(NULL,'".$pkPedido."','ANU','".$term."',NULL,0)";
            $db->executeQuery($query);
        }
        //echo '1';
    }
    
    //Funcion para imprimir cuenta en cola de impresion
    //Gino Lluen 2017
    public function imprimeCuentaenCola($pkPedido,$term,$tipo,$aux) {
        $db = new SuperDataBase();
        //Antes de todo verificamos si el pedido sigue abierto
        $r_abierto = $db->executeQuery("Select * from pedido where pkPediido = '".$pkPedido."' AND (estado = 0 OR estado = 1 OR estado = 4 OR estado = 5)");
        if($row_abierto = $db->fecth_array($r_abierto)){
            //Ponemos en cola
            $query = "Insert into cola_impresion values(NULL,'".$pkPedido."','CUE-".$tipo."','".$term."','".$aux."',0)";
            $db->executeQuery($query);
            //Marcamos si ya fue pedida pre cuenta
            $query01 = "Update pedido set subTotal = 1 where pkPediido = '".$pkPedido."'";
            $db->executeQuery($query01);
            //Devolvemos Resultado
            $resultado = array();
            $resultado["exito"] = 1;
            echo json_encode($resultado);
        }else{
            $r_comprobante = $db->executeQuery("Select * from comprobante where pkComprobante = '".$pkPedido."'");
            if($row_comprobante = $db->fecth_array($r_comprobante)){
                $query = "Insert into cola_impresion values(NULL,'".$pkPedido."','CUE-".$tipo."','".$term."','".$aux."',0)";
                $db->executeQuery($query);
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
    }

    public function imprimeCuentaenColaPeluca($pkPedido,$term,$tipo,$aux) {
        $db = new SuperDataBase();

        $esCredito = "SELECT * FROM creditos WHERE pkNuevo = {$pkPedido}";
        $result1 = $db->executeQuery($esCredito);
        $credito = null;

        if($reg1 = $result1->fetch_object()){
            $credito = $reg1;
        }

        if($credito){
            $pkPedido = $credito->pkOriginal;
        }
        $query = "Insert into cola_impresion values(NULL,'".$pkPedido."','CUE-".$tipo."','".$term."','".$aux."',0)";
        $db->executeQuery($query);
        echo '1';
    }
    
    /**
     * Listado datos de un pedido por una mesa
     * 
     */
    public function listPedidos($mesa) {

        $db = new SuperDataBase();

        $query = "SELECT t.nombres as trabajador, t.pkTrabajador, s.nombre as nsalon, m.pkSalon, m.nmesa, p.* FROM pedido p, mesas m, salon s, trabajador t where p.pkMesa = '".$mesa."' AND p.estado=0 AND p.pkMesa = m.pkMesa AND m.pkSalon = s.pkSalon AND p.idUser = t.pkTrabajador order by p.pkPediido DESC LIMIT 1";

        $result = $db->executeQuery($query);

        $array = null;
        while ($row = $db->fecth_array($result)) {
            $array[] = array(
                "npersonas" => $row['npersonas'],
                "nmesa" => $row['nmesa'],
                "pkMesa" => $row['pkMesa'],
                "nComprobante" => $row['pkPediido'],
                "descuento" => $row['descuento'],
                "documento" => $row['documento'],
                "pkSalon" => $row['pkSalon'],
                "nsalon" => $row['nsalon'],
                "ntrabajador" => $row['trabajador'],
                "pktrabajador" => $row['pkTrabajador']
            );
        }
        echo json_encode($array);
    }

    public function listpkPedido($mesa) {
        $db = new SuperDataBase();
        $query = "SELECT * FROM pedido where pkMesa = '".$mesa."' AND estado=0 order by pkPediido DESC LIMIT 1";
        $result = $db->executeQuery($query);
        $pkPedido = 0;
        if ($row = $db->fecth_array($result)) {
            $pkPedido = $row['pkPediido'];
        }
        return $pkPedido;
    }

    public function listPedidosPk($pkPedido) {
        $db = new SuperDataBase();

        $query = "SELECT t.nombres as trabajador, t.pkTrabajador, s.nombre as nsalon, m.pkSalon, m.nmesa, p.* FROM pedido p, mesas m, salon s, trabajador t where p.pkPediido = '".$pkPedido."' AND p.estado=4 AND p.pkMesa = m.pkMesa AND m.pkSalon = s.pkSalon AND p.idUser = t.pkTrabajador";

        $result = $db->executeQuery($query);

        $array = null;
        while ($row = $db->fecth_array($result)) {
            $array[] = array(
                "npersonas" => $row['npersonas'],
                "nmesa" => $row['nmesa'],
                "pkMesa" => $row['pkMesa'],
                "nComprobante" => $row['pkPediido'],
                "descuento" => $row['descuento'],
                "documento" => $row['documento'],
                "pkSalon" => $row['pkSalon'],
                "nsalon" => $row['nsalon'],
                "ntrabajador" => $row['trabajador'],
                "pktrabajador" => $row['pkTrabajador']
            );
        }
        echo json_encode($array);
    }

    public function detallesVenta($idVenta)
    {
        $db = new SuperDataBase();
        $pkEmpresa = UserLogin::get_pkSucursal();
        
        $esCredito = "SELECT * FROM creditos WHERE pkNuevo = {$idVenta}";
        $result1 = $db->executeQuery($esCredito);
        $credito = null;

        if($reg1 = $result1->fetch_object()){
            $credito = $reg1;
        }

        if($credito){
            $idVenta = $credito->pkOriginal;
        }

        $query = "SELECT d.pkDetallePedido, d.cantidad, d.precio, d.pkPlato, p.descripcion 
                FROM detallepedido d INNER JOIN plato p on d.pkPlato = p.pkPlato 
                WHERE d.pkPediido = {$idVenta}";

        $result = $db->executeQuery($query);
        $array = array();
        
        while($reg = $result->fetch_object()){
			$array[] = $reg;
        }
        
		return $array;
        
    }

    /**
     * Listado de los item de un pedido de una mesa
     * 
     */
    public function listItemPedidos($comprobante) {
        error_reporting(E_ALL);
        if($comprobante != ""){
            $sucursal = UserLogin::get_pkSucursal();
            $db = new SuperDataBase();
            $query = "";
            if((intval($_COOKIE["TYP"]) <> 4) || (intval($_COOKIE["TYP"]) <> 10)){
                $query = "SELECT tipoPedido, descripcionPedido, horaPedido, pkPlato as pkProducto,(select descuento from pedido where pkPediido='')as descuento,pkDetallePedido, cantidad, precio,cantidad*precio as importe , mensaje, d.estado, pkPediido , fechaPedido, horaPedido, horaTermino, pkCocinero,pkMozo,estadoImpresion, t.nombres, apellidos,(select upper(descripcion) from plato pl where pl.pkPlato=d.pkPlato ) as pedido,(select upper(pkTipo) from plato_sucursal pl where pl.pkPlato=d.pkPlato)as tipo FROM detallepedido d inner join trabajador t on t.pkTrabajador=pkMozo   where d.pkPediido='$comprobante' and (d.descripcionPedido is null || d.descripcionPedido <> 'is-for-change') order by d.estado, d.horaPedido desc  ";
            }else{
                $query = "SELECT tipoPedido, descripcionPedido, horaPedido, pkPlato as pkProducto,(select descuento from pedido where pkPediido='')as descuento,pkDetallePedido, cantidad, precio,cantidad*precio as importe , mensaje, d.estado, pkPediido , fechaPedido, horaPedido, horaTermino, pkCocinero,pkMozo,estadoImpresion, t.nombres, apellidos,(select upper(descripcion) from plato pl where pl.pkPlato=d.pkPlato ) as pedido,(select upper(pkTipo) from plato_sucursal pl where pl.pkPlato=d.pkPlato)as tipo FROM detallepedido d inner join trabajador t on t.pkTrabajador=pkMozo   where d.pkPediido='$comprobante' and d.estado<3  order by d.estado, d.horaPedido desc ";
            }
            
            $result = $db->executeQuery($query);
            
            $condicion = "";

            $array = array();
            while ($row = $db->fecth_array($result)) {
                $descripcionEstado = "";
                switch ($row['estado']) {
                    case "0": $descripcionEstado = "<span style='color:#ef6a00;'>Por Solicitar</span>";
                        break;
                    case "1":
                        $descripcionEstado = "<span style='color:#0086cf;'>Enviado</span>";
                        break;
                    case "2":
                        $descripcionEstado = "<span style='color:green;'>Entregado</span>";
                        break;
                    case "3":
                        $descripcionEstado = "<span style='color:red;'>Anulado</span>";
                        break;
                }
                $tipoPedido="";
                if($row['tipoPedido']=="0"){
                    $tipoPedido="Mesa";
                }
                if($row['tipoPedido']=="1"){
                    $tipoPedido="Llevar";
                }
                
                //Revisamos si el pinche pedido se ha truchado
                //Yoyo miau - 2018
                $query_e = "Select cf.id, cf.cantidad_cambio, cf.precio_cambio, cf.pk_plato_cambio, upper(p.descripcion) as pedido from cambio_facturacion cf, plato p where cf.pk_plato_cambio = p.pkPlato AND cf.pk_detalle =  '".$row['pkDetallePedido']."' AND cf.pk_pedido_destino = '$comprobante'";
                
                //echo $query_e;
                
                $result_e = $db->executeQuery($query_e);
                
                $hay = 0;
                while($row_e = $db->fecth_array($result_e)){
                    $condicion = $condicion." AND cf.id <> '".$row_e['id']."'";
                    $hay = 1;
                    $importe_e = floatval($row_e['cantidad_cambio'])*floatval($row_e['precio_cambio']);
                    $array[] = array(
                        "pkPedido" => "C".$row_e['id'],
                        "cantidad" => (int) $row_e['cantidad_cambio'],
                        "precio" => number_format($row_e['precio_cambio'],2,'.',''),
                        "pedido" => "(*) ".$row_e['pedido'],
                        "importe" => $importe_e,
                        "mensaje" => $row['mensaje'],
                        "hora" => $row['horaPedido'],
                        "estado" => $row['estado'],
                        "pkProducto" => $row_e['pk_plato_cambio'],
                        "Tipo" => $row['tipo'],
                        "Destado" => $descripcionEstado,
                        "tipoPedido" => $tipoPedido,
                        "mozo" => $row['nombres'] . " " . $row['apellidos']
                    );
                }
                    
                if($hay === 0){

                    if (!is_null($row['descripcionPedido']) && $row['descripcionPedido'] && strpos('cambio', $row['descripcionPedido']) === false) {
                        // $row['pkDetallePedido'] = "C".$row['pkDetallePedido'];
                        $row['pedido'] = "(*)".$row['pedido'];
                    }

                    $array[] = array(
                        "pkPedido" => $row['pkDetallePedido'],
                        "cantidad" => (int) $row['cantidad'],
                        "precio" => $row['precio'],
                        "pedido" => $row['pedido'],
                        "importe" => (float) round($row['importe'], 2),
                        "mensaje" => $row['mensaje'],
                        "estado" => $row['estado'],
                        "hora" => $row['horaPedido'],
                        "pkProducto" => $row['pkProducto'],
                        "Tipo" => $row['tipo'],
                        "Destado" => $descripcionEstado,
                        "tipoPedido" => $tipoPedido,
                        "mozo" => $row['nombres'] . " " . $row['apellidos']
                    );
                }
            }
            
            //Ahora revisamos detalles truchos huerfanos
            $query_e_e = "Select cf.id, cf.cantidad_cambio, cf.precio_cambio, cf.pk_plato_cambio, upper(p.descripcion) as pedido from cambio_facturacion cf, plato p where cf.pk_plato_cambio = p.pkPlato AND cf.pk_pedido_destino = '$comprobante'".$condicion;
            $result_e_e = $db->executeQuery($query_e_e);
            while($row_e = $db->fecth_array($result_e_e)){
                    $importe_e = floatval($row_e['cantidad_cambio'])*floatval($row_e['precio_cambio']);
                    $array[] = array(
                        "pkPedido" => "C".$row_e['id'],
                        "cantidad" => (int) $row_e['cantidad_cambio'],
                        "precio" => number_format($row_e['precio_cambio'],2,'.',''),
                        "pedido" => utf8_encode("(*) ".$row_e['pedido']),
                        "importe" => $importe_e,
                        "mensaje" => "",
                        "estado" => "1",
                        "hora" => "-",
                        "pkProducto" => $row_e['pk_plato_cambio'],
                        "Tipo" => "",
                        "Destado" => "Impreso",
                        "tipoPedido" => "Virtual",
                        "mozo" => ""
                    );
                }
            
            
            echo json_encode($array);
        }
    }

    public function listItemPedidos2($comprobante) {
        if($comprobante != ""){
            $db = new SuperDataBase();
            $query = "sELECT (select descuento from pedido where pkPediido='$comprobante')as descuento,pkDetallePedido, cantidad, precio,case when mod(cantidad,1) = 0 then  round((cantidad*precio),2) else round((cantidad*precio),0) end as importe, mensaje, d.estado, pkPediido , fechaPedido, horaPedido, horaTermino,  pkCocinero, pkMozo,estadoImpresion,t.nombres, apellidos,case  when character_length(pkProducto) <6 then ( select upper(descripcion) from plato pl where pl.pkPlato=d.pkPlato) when character_length(pkPlato)<6 then (select upper(descripcion) from productos pr where pr.pkProducto=d.pkProducto) end as pedido FROM detallepedido d  inner join trabajador t on t.pkTrabajador=pkMozo   where d.pkPediido='$comprobante' and d.estado<4 and d.estado <>3";

            $result = $db->executeQuery($query);

            $array = array();
            while ($row = $db->fecth_array($result)) {
                $descripcionEstado = "";
                switch ($row['estado']) {
                    case "0": $descripcionEstado = "Por Solicitar";
                        break;
                    case "1":
                        $descripcionEstado = "Impreso";
                        break;
                    case "2":
                        $descripcionEstado = "Entregado";
                        break;
                    case "3":
                        $descripcionEstado = "Entregago";
                        break;
                    case "4":
                        $descripcionEstado = "Anulado";
                        break;
                }
                $array[] = array(
                    "pkPedido" => $row['pkDetallePedido'],
                    "cantidad" => $row['cantidad'],
                    "precio" => $row['precio'],
                    "pedido" => utf8_encode($row['pedido']),
                    "importe" => $row['importe'],
                    "mensaje" => $row['mensaje'],
                    "estado" => $row['estado'],
                    "Destado" => $descripcionEstado,
                    "mozo" => utf8_encode($row['nombres'] . " " . $row['apellidos'])
                );
            }
            echo json_encode($array);
        }
    }

    /**
     * Listado de los item de un pedido de una mesa
     * 
     */
    public function listAllPedidos() {
        $db = new SuperDataBase();
        $query = "SELECT pkDetallePedido, cantidad, precio, cantidad*precio as importe, mensaje, d.estado, d.pkPediido , fechaPedido, horaPedido, horaTermino,  pkCocinero, pkMozo, nombres, lastName,d.pkPlato, pl.descripcion as plato,nmesa ,npersonas FROM detallepedido d  inner join (trabajador t inner join person p on p.documento =t.documento) on t.pkTrabajador=pkMozo inner join plato pl on pl.pkPlato=d.pkPlato inner join (pedido pe inner join mesas m on m.pkMesa=pe.pkMesa) on  pe.pkPediido=d.pkPediido where d.estado=0 order by horaPedido";
        $result = $db->executeQuery($query);

        $array = array();
        while ($row = $db->fecth_array($result)) {
            $array[] = array(
                "pkPedido" => $row['pkDetallePedido'],
                "cantidad" => $row['cantidad'],
                "pedido" => utf8_encode($row['plato']),
                "importe" => $row['importe'],
                "mensaje" => $row['mensaje'],
                "nmesa" => $row['nmesa'],
                "npersonas" => $row['npersonas'],
                "mozo" => utf8_encode($row['nombres'] . " " . $row['lastName'])
            );
        }
        echo json_encode($array);
    }

    /**
     * Agregar un pedido
     * @param int $cantidad Cantidad de productos
     * @param Decimal $precio Precio de venta
     * @param char(6) $pedido fkPedido
     * @param char(10) $Comprobante Número del comprobante Generado
     * @param Stirng $tipo descripcion del tipo de pedido carta o producto
     * 
     */
    public function addPedido($cantidad, $precioVenta, $fkPedido, $pkComprobante, $tipo, $mensaje) {
        error_reporting(E_ALL);
        $db = new SuperDataBase();
        $user = new UserLogin();
        $id = $user->get_idTrabajador();
        //Antes de todo verificamos si el pedido sigue abierto
        $r_abierto = $db->executeQuery("Select * from pedido where pkPediido = '".$pkComprobante."' AND estado = 0");
        if($row_abierto = $db->fecth_array($r_abierto)){
            //Primero verificamos si es pedido con stock
            $rstock = $db->executeQuery("Select * from plato_stock where CONVERT(id_plato USING utf8) COLLATE utf8_spanish2_ci = CONVERT('".$fkPedido."' USING utf8) COLLATE utf8_spanish2_ci");
            if($rows = $db->fecth_array($rstock)){
                if(intval($rows["stock"]) > 0){
                    if(intval($cantidad) <= intval($rows["stock"])){
                        //Actualizamos stock
                        $nstock = intval($rows["stock"]) - intval($cantidad);
                        $db->executeQuery("Update plato_stock set stock = '".$nstock."' where id = '".$rows["id"]."'");
                        //Verificamos amarrados
                        $query_amarrados = "Select pa.*, pl.precio_venta from platos_amarrados pa, plato pl where pkPlato_1 = '".$fkPedido."' AND pkPlato_2 = pl.pkPlato";
                        $r_amr = $db->executeQuery($query_amarrados);
                        while($row_amr = $db->fecth_array($r_amr)){
                            $cantidad_1 = ceil(floatval($cantidad)/floatval($row_amr["cantidad_1"]));
                            $cantidad_2 = floatval($row_amr["cantidad_2"]);
                            $cantidad_agregar = $cantidad_1*$cantidad_2;
                            $precio_venta = floatval($row_amr["precio_venta"]);                   
                            $this->agregar_final($cantidad,$precio_venta,$fkPedido,$pkComprobante,$id,$mensaje);                      
                        }
                        //Agregamos pedido
                        $this->agregar_final($cantidad,$precioVenta,$fkPedido,$pkComprobante,$id,$mensaje);
                    }
                }
            }else{
                //Verificamos menu
                $query = "Select * from componente_menu where CONVERT(pk_plato USING utf8) COLLATE utf8_spanish2_ci = CONVERT('".$fkPedido."' USING utf8) COLLATE utf8_spanish2_ci AND estado = 1 AND fecha_inicio <= '".date("Y-m-d")."' AND fecha_fin >= '".date("Y-m-d")."'";
                //echo $query;
                $mstock = $db->executeQuery($query);
                if($rowss = $db->fecth_array($mstock)){
                    if(intval($rowss["stock"]) > 0){
                        if(intval($cantidad) <= intval($rowss["stock"])){
                            //Actualizamos stock
                            $nstock = intval($rowss["stock"]) - intval($cantidad);
                            $db->executeQuery("Update componente_menu set stock = '".$nstock."' where id = '".$rowss["id"]."'");
                            //Verificamos amarrados
                            $query_amarrados = "Select pa.*, pl.precio_venta from platos_amarrados pa, plato pl where pkPlato_1 = '".$fkPedido."' AND pkPlato_2 = pl.pkPlato";
                            $r_amr = $db->executeQuery($query_amarrados);
                            while($row_amr = $db->fecth_array($r_amr)){
                                $cantidad_1 = ceil(floatval($cantidad)/floatval($row_amr["cantidad_1"]));
                                $cantidad_2 = floatval($row_amr["cantidad_2"]);
                                $cantidad_agregar = $cantidad_1*$cantidad_2;
                                $precio_venta = floatval($row_amr["precio_venta"]);                 
                                $this->agregar_final($cantidad,$precio_venta,$fkPedido,$pkComprobante,$id,$mensaje);                      
                            }
                            //Agregamos pedido
                            $this->agregar_final($cantidad,$precioVenta,$fkPedido,$pkComprobante,$id,$mensaje);
                        }
                    }
                }else{     
                    //Verificamos amarrados
                    $query_amarrados = "Select pa.*, pl.precio_venta from platos_amarrados pa, plato pl where pkPlato_1 = '".$fkPedido."' AND pkPlato_2 = pl.pkPlato";
                    $r_amr = $db->executeQuery($query_amarrados);
                    while($row_amr = $db->fecth_array($r_amr)){
                        $cantidad_1 = ceil(floatval($cantidad)/floatval($row_amr["cantidad_1"]));
                        $cantidad_2 = floatval($row_amr["cantidad_2"]);
                        $cantidad_agregar = $cantidad_1*$cantidad_2;
                        $precio_venta = floatval($row_amr["precio_venta"]);                 
                        $this->agregar_final($cantidad,$precio_venta,$row_amr["pkPlato_2"],$pkComprobante,$id,$mensaje);                      
                    }
                    //Insertamos pedido
                    $this->agregar_final($cantidad,$precioVenta,$fkPedido,$pkComprobante,$id,$mensaje);
                }
            }
            //devolvemos tipo actual para reconsultar
            $rdev = $db->executeQuery("Select pktipo from plato where CONVERT(pkPlato USING utf8) COLLATE utf8_spanish2_ci = CONVERT('".$fkPedido."' USING utf8) COLLATE utf8_spanish2_ci");
            $rowd = $db->fecth_array($rdev);

            //Devolvemos Resultado
            $resultado = array();
            $resultado["exito"] = 1;
            $resultado["pktipo"] = $rowd["pktipo"];
            return $resultado;
        }else{
            //Devolvemos Resultado
            $resultado = array();
            $resultado["exito"] = 0;
            return $resultado;
        }
    }

    public function agregar_final($cantidad,$precio,$plato,$pedido,$usuario,$mensaje){
        error_reporting(E_ALL);
        $terminal = $_COOKIE['t'];
        $db = new SuperDataBase();
        if($mensaje == ""){
            $query_buscar = "Select * from detallepedido where estado = 0 AND pkPediido = '".$pedido."' AND pkPlato = '".$plato."' AND mensaje is NULL";
            $res = $db->executeQuery($query_buscar);
            if($row = $db->fecth_array($res)){
                $query_sumar = "Update detallepedido set cantidad = cantidad + '".$cantidad."' where pkDetallePedido = '".$row["pkDetallePedido"]."'";
                $db->executeQuery($query_sumar);
            }else{
                $query_insertar = "Insert into detallepedido values(NULL,'".$cantidad."','".$precio."',NULL,'0','".$pedido."',NULL,now(),NULL,'".$plato."',NULL,NULL,'".$usuario."','0',NULL,NULL,'0','0', '$terminal')";
                $db->executeQuery($query_insertar);
                $res_id = $db->executeQuery("SELECT LAST_INSERT_ID() as id_detalle");
                $id_detalle = "";
                while($row0 = $db->fecth_array($res_id)){
                    $id_detalle = $row0["id_detalle"];
                }             
                $query_caja = "Insert into accion_caja values(NULL,'".$id_detalle."','DET','".$_COOKIE["c"]."')";
                $db->executeQuery($query_caja);
            }
        }else{
            $query_insertar = "Insert into detallepedido values(NULL,'".$cantidad."','".$precio."','".$mensaje."','0','".$pedido."',NULL,now(),NULL,'".$plato."',NULL,NULL,'".$usuario."','0',NULL,NULL,'0','0', '$terminal')";
            $db->executeQuery($query_insertar);
            $res_id = $db->executeQuery("SELECT LAST_INSERT_ID() as id_detalle");
            $id_detalle = "";
            while($row0 = $db->fecth_array($res_id)){
                $id_detalle = $row0["id_detalle"];
            }             
            $query_caja = "Insert into accion_caja values(NULL,'".$id_detalle."','DET','".$_COOKIE["c"]."')";
            $db->executeQuery($query_caja);
        }
    }

    //Funcion para no esperar envio de correo
    public function execInBackground($cmd) {
        if (substr(php_uname(), 0, 7) == "Windows"){
            pclose(popen("start /B ". $cmd, "r")); 
        }
        else {
            exec($cmd . " > /dev/null &");  
        }
    } 

    //Funcion Para Enviar Correo de Anulacion
    public function envia_correo_anulacion($id_pedido, $cantidad, $plato, $mesa, $salon, $razon){
        $user = new UserLogin();
        $ruta = realpath('');
        $pr = explode("htdocs",$ruta);
        $comando = $pr[0]."php\\php.exe ".$ruta."\\reportes\\envio_anulacion.php ".escapeshellarg($id_pedido)." ".escapeshellarg($cantidad)." ".escapeshellarg($plato)." ".escapeshellarg($mesa)." ".escapeshellarg($salon)." ".escapeshellarg($user->get_names()." ".$user->get_lastnames())." ".escapeshellarg($razon);
        //echo $comando;
        $this->execInBackground($comando);
    }

    //Funcion Para Enviar Correo de Anulacion
    public function envia_correo_liberacion($id_pedido, $items, $total, $mesa, $salon, $razon){
        $user = new UserLogin();
        $ruta = realpath('');
        $pr = explode("htdocs",$ruta);
        $comando = $pr[0]."php\\php.exe ".$ruta."\\reportes\\envio_liberacion.php ".escapeshellarg($id_pedido)." ".escapeshellarg($items)." ".escapeshellarg($total)." ".escapeshellarg($mesa)." ".escapeshellarg($salon)." ".escapeshellarg($user->get_names()." ".$user->get_lastnames())." ".escapeshellarg($razon);
        //echo $comando;
        $this->execInBackground($comando);
    }

    //Funcion Para Enviar Correo de Anulacion
    public function envia_correo_anulacion_venta($pedido, $comprobante, $items, $total, $mesa, $salon){
        $user = new UserLogin();
        $ruta = realpath('');
        $pr = explode("htdocs",$ruta);
        $comando = $pr[0]."php\\php.exe ".$ruta."\\reportes\\envio_anulacion_venta.php ".escapeshellarg($pedido)." ".escapeshellarg($comprobante)." ".escapeshellarg($items)." ".escapeshellarg($total)." ".escapeshellarg($mesa)." ".escapeshellarg($salon)." ".escapeshellarg($user->get_names()." ".$user->get_lastnames());
        //echo $comando;
        $this->execInBackground($comando);
    }

    //Funcion Para Enviar Correo por Credito
    public function envia_correo_credito_venta($pedido, $comprobante, $items, $total, $mesa, $salon, $cliente, $comentario){
        $user = new UserLogin();
        $ruta = realpath('');
        $pr = explode("htdocs",$ruta);
        $comando = $pr[0]."php\\php.exe ".$ruta."\\reportes\\envio_credito_venta.php ".escapeshellarg($pedido)." ".escapeshellarg($comprobante)." ".escapeshellarg($items)." ".escapeshellarg($total)." ".escapeshellarg($mesa)." ".escapeshellarg($salon)." ".escapeshellarg($user->get_names()." ".$user->get_lastnames()) ." ". escapeshellarg($cliente) . " " . escapeshellarg($comentario);
        //echo $comando;
        $this->execInBackground($comando);
    }

    //Funcion Para Enviar Correo por Consumo
    public function envia_correo_consumo_venta($pedido, $comprobante, $items, $total, $mesa, $salon, $cliente, $comentario){
        $user = new UserLogin();
        $ruta = realpath('');
        $pr = explode("htdocs",$ruta);
        $comando = $pr[0]."php\\php.exe ".$ruta."\\reportes\\envio_consumo_venta.php ".escapeshellarg($pedido)." ".escapeshellarg($comprobante)." ".escapeshellarg($items)." ".escapeshellarg($total)." ".escapeshellarg($mesa)." ".escapeshellarg($salon)." ".escapeshellarg($user->get_names()." ".$user->get_lastnames()) ." ". escapeshellarg($cliente) . " " . escapeshellarg($comentario);
        // echo $comando;
        $this->execInBackground($comando);
    }


    public function deletePedido($pkDetallepedido, $razon) {       
        $db = new SuperDataBase();
        $user = new UserLogin();
        $id = $user->get_idTrabajador();
        
        $pos = strpos($pkDetallepedido, "C");
        if ($pos === false) {
            $query = "update detallepedido set estado=3, mensaje='".$razon."',fechaPedido=now(), pkCocinero=$id where pkDetallePedido=$pkDetallepedido";
            $result = $db->executeQuery($query);
            //echo $query;
        }else{
            $id_e = substr($pkDetallepedido, 1);
            $id_e = intval($id_e);
            
            $query_det = "Select * from cambio_facturacion where id = '".$id_e."'";
            $res_det = $db->executeQuery($query_det);
            $id_original = "";
            while($row = $db->fecth_array($res_det)){
                $id_original = $row["pk_detalle"];
            }
            
            $db->executeQuery("SET SQL_SAFE_UPDATES = 0;");
            $db->executeQuery("Delete from cambio_facturacion where pk_detalle = '".$id_original."'");
            $db->executeQuery("SET SQL_SAFE_UPDATES = 1;");
        }
    }
    
    public function deletePedido1($pkDetallepedido,$cantidad,$precio,$pkpedido,$pkplato,$razon) {       
        $db = new SuperDataBase();
        $user = new UserLogin();
        $id = $user->get_idTrabajador();
        $idfinal = 0;
        
        if(intval($cantidad)>1){
            //Actualizamos pedido actual
            $ncant = intval($cantidad)-1;
            $query = "update detallepedido set cantidad = '".$ncant."' where pkDetallePedido = '".$pkDetallepedido."'";
            $db->executeQuery($query);
            //Obtenemos fecha del pedido original
            $fecha_original = "";
            $queryd = "Select horaPedido from detallepedido where pkDetallePedido = '".$pkDetallepedido."'";
            $rda = $db->executeQuery($queryd);
            while ($row = $db->fecth_array($rda)) {
                $fecha_original = $row["horaPedido"];
            }
            //Creamos pedido anulado
            $nquery = "INSERT INTO detallepedido (pkDetallePedido,cantidad,precio,mensaje,estado,pkPediido,fechaPedido,horaPedido,horaTermino,pkPlato,pkProducto,pkCocinero,pkMozo,estadoImpresion,impresopor,descripcionPedido,pkPromocion,tipoPedido) VALUES(NULL,1,'".$precio."','".$razon."',3,'".$pkpedido."',now(),'".$fecha_original."',now(),'".$pkplato."','','".$id."','".$id."',1,'','',0,0);";
            $db->executeQuery($nquery);
            $idquery = "SELECT LAST_INSERT_ID() as nid";
            $rid = $db->executeQuery($idquery);
            while ($row = $db->fecth_array($rid)) {
                $idfinal = intval($row['nid']);
            }
        }else{
            $query = "update detallepedido set estado=3, fechaPedido=now(), pkCocinero=$id , mensaje='".$razon."' where pkDetallePedido=$pkDetallepedido";
            $db->executeQuery($query);
            $idfinal = $pkDetallepedido;
        }
        
        return $idfinal;
    }

    /**
     * Aperturar una mesa Disponible
     * @param int $pkMesa Identificador de la mesa
     * @param int $idUsuario Identificador del usuario
     * 
     */
    public function aperturarMesa($pkMesa, $idUsuario) {
        $db = new SuperDataBase();

        //Obtenemos Fecha Cierre Actual
        $fecha_cierre = "";
        $query_cierre= "Select * from cierrediario where pkCierreDiario = 1";
        $res_cierre = $db->executeQuery($query_cierre);
        if($rw4 = $db->fecth_array($res_cierre)){
            $fecha_cierre = $rw4["fecha"];
        }

        //Si la mesa esta cerrada abrimos
        $query_apertura = "Insert into pedido values(NULL,now(),NULL,'".$pkMesa."',0,NULL,NULL,now(),NULL,0,'1',0,'".$idUsuario."','".$fecha_cierre."',NULL,0,0,NULL,'',2)";
        $db->executeQuery($query_apertura);

        //Obtenemos id pedido              
        $pkPedido = $db->getId();

        //Abrimos mesa
        $query_mesa = "Update mesas set estado = 1 WHERE pkMesa = '".$pkMesa."'";
        $db->executeQuery($query_mesa);

        return $pkPedido;
    }

    public function aperturarMesaMismaCuenta($pkMesa, $idUsuario) {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "CALL sp_aperturar_comprobante_actual($pkMesa,$idUsuario,'$sucursal',@sa);";

        $db->executeQuery($query);
        $resultado = "";
        $query = "select @sa;";
        $result = $db->executeQuery($query);
        while ($row = $db->fecth_array($result)) {
            $resultado = $row['@sa'];
        }
        return $resultado;
    }

    /**
     * Actualizar el numero de personas
     * @param String $pkComprobante 
     * 
     */
    public function updateNPersonas($pkComprobante, $npersonas) {
        $db = new SuperDataBase();
        $query = "CALL sp_update_number_person_attention('$pkComprobante',$npersonas);";
        $result = $db->executeQuery($query);
        echo $query;
    }

    /**
     * Actualizar el Precio o Cantidad de un pedido
     * @param String $pkComprobante 
     * 
     */
    public function updatePedido($pkComprobante, $cantidad, $precio, $descripcion) {
        $db = new SuperDataBase();
        $query = "CALL sp_update_itemsPedidos('$pkComprobante',$cantidad,$precio,'$descripcion');";
        $result = $db->executeQuery($query);
        echo $query;
    }

    public function AnulaPedido($pkComprobante) {
        //Datos para envio de correo
        $pedido = $pkComprobante;
        $comprobante = "";
        $items = "<ul>";
        $total = 0;
        $mesa = "";
        $salon = "";

        $db = new SuperDataBase();
        $id = UserLogin::get_id();
        //Anulamos cabecera del pedido
        $query = "update pedido set estado = 3, idUser=$id, dateModify=now() where pkPediido='$pkComprobante';";
        $db->executeQuery($query);

        //Agregamos inseguridad
        $db->executeQuery("SET SQL_SAFE_UPDATES = 0");

        //Eliminamos propinas
        $db->executeQuery("Delete from pedido_propina where pkPediido = '".$pkComprobante."'");

        //Anulamos movimientos de dinero
        $db->executeQuery("Update movimiento_dinero set estado = '0' where tipo_origen = 'PED' AND id_origen = '".$pkComprobante."'");

        //Obtenemos informacion de pedido
        $data_pedido = $db->executeQuery("Select m.nmesa as mesa, s.nombre as salon from pedido p, mesas m, salon s where p.pkPediido = '".$pedido."' AND p.estado = 0 AND p.pkMesa = m.pkMesa AND m.pkSalon = s.pkSalon");
        if($data = $db->fecth_array($data_pedido)){
            $mesa = $data["mesa"];
            $salon = $data["salon"];
        }

        //Obtenemos detalles del pedido
        $query_detalle = "Select dp.cantidad, dp.precio, pl.descripcion from detallepedido dp, plato pl where dp.pkPediido = '".$pedido."' AND dp.estado = 1 AND dp.pkPlato = pl.pkPlato";
        $detalles = $db->executeQuery($query_detalle);
        while($row_d = $db->fecth_array($detalles)){
            $items .= "<li>(".$row_d["cantidad"].") ".$row_d["descripcion"]."</li>";
            $total = $total + (floatval($row_d["cantidad"])*floatval($row_d["precio"]));
        }
        $items .= "</ul>";

        //Anulamos detalle del pedido
        $query_detalles = "Update detallepedido set estado = 3 where pkPediido = '".$pkComprobante."'";
        $db->executeQuery($query_detalles);

        //Hacemos chambita de comprobante
        $serie = NULL;
        $tipo_c = 0;
        $numero_c = NULL;
        $tipo = NULL;
        //Obtenemos comprobante
        $query_pre = "Select c.* from detallecomprobante dc, comprobante c where dc.pkComprobante = c.pkComprobante AND dc.pkPediido = '".$pkComprobante."'";
        
        $result_pre = $db->executeQuery($query_pre);
        $codComprobante = 0;
        while ($row_pre = $db->fecth_array($result_pre)) {
            $codComprobante = $row_pre["pkComprobante"];
            $numero_c = $row_pre["ncomprobante"];
            $tipo = intval($row_pre["pkTipoComprobante"]);
        }
        
        if(intval($codComprobante) > 0){
            //Obtenemos Llaves de NUBEFACT
            $ruta = NULL;
            $cX = "Select * from cloud_config where parametro = 'rutapce'";
            $sX = $db->executeQuery($cX);
            if ($row = $db->fecth_array($sX)){
                $ruta = $row["valor"];
            }
            
            $token = NULL;
            $cY = "Select * from cloud_config where parametro = 'tokenpce'";
            $sY = $db->executeQuery($cY);
            if ($row = $db->fecth_array($sY)){
                $token = $row["valor"];
            }
            
            if(intval($tipo) == 1){
                $c0 = "Select * from cloud_config where parametro = 'sboleta'";
                $s0 = $db->executeQuery($c0);
                if ($row = $db->fecth_array($s0)){
                    $serie = $row["valor"];
                }
                $tipo_c = 2;
            }else{
                $c0 = "Select * from cloud_config where parametro = 'sfactura'";
                $s0 = $db->executeQuery($c0);
                if ($row = $db->fecth_array($s0)){
                    $serie = $row["valor"];
                }
                $tipo_c = 1;
            }
            
            //Serie para envio de correo
            $comprobante = $serie."-".$numero_c;
            
            $anulacion = array();
            
            $anulacion["operacion"] = "generar_anulacion";
            $anulacion["tipo_de_comprobante"] = $tipo_c;
            $anulacion["serie"] = $serie;      
            $anulacion["numero"] = $numero_c;
            $anulacion["motivo"] = "ERROR DEL SISTEMA";
            $anulacion["codigo_unico"] =  "";
            
            $data_json = json_encode($anulacion);
            
            //echo $data_json;

            $this->envia_correo_anulacion_venta($pedido, $comprobante, $items, $total, $mesa, $salon);
            
            //Invocamos el servicio de NUBEFACT
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $ruta);
            curl_setopt(
                    $ch, CURLOPT_HTTPHEADER, array(
                    'Authorization: Token token="'.$token.'"',
                    'Content-Type: application/json',
                    )
            );
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $respuesta  = curl_exec($ch);
            curl_close($ch);
            
            //Verificamos respuesta
            //print_r($respuesta);
            $leer_respuesta = json_decode($respuesta, true);
            if (isset($leer_respuesta['errors'])) {

                if ($leer_respuesta['codigo'] == 21 && intval($tipo) == 1) {
                    $query = "UPDATE comprobante " .
                        "SET estado = 3 " .
                        "WHERE pkTipoComprobante = $tipo AND " .
                        "pkComprobante = '$codComprobante'";
                    $db->executeQuery($query);
                }

                //Mostramos errores
                echo $leer_respuesta['errors'];
            } else {
                $aceptada = "NO";
                if(boolval($leer_respuesta["aceptada_por_sunat"])){
                    $aceptada = "SI";
                }
                
                $query = "UPDATE comprobante " .
                "SET estado = 3 " .
                "WHERE pkTipoComprobante = $tipo AND " .
                "pkComprobante = '$codComprobante'";
                $db->executeQuery($query);
                
                echo $aceptada;
            }
        }else{
            $this->envia_correo_anulacion_venta($pedido, $comprobante, $items, $total, $mesa, $salon);
            echo "SI";
        }
    }
    
    public function AnulaPedido2($pkDetallePedido) {
        $db = new SuperDataBase();
        $id = UserLogin::get_idTrabajador();
        $query = "update detallepedido set estado = 3, pkCocinero=$id where pkDetallePedido='$pkDetallePedido';";
        $db->executeQuery($query);
        echo $query;
    }

    /**
     * Registra el mensaje de un pedido
     * @param String $pkComprobante 
     * 
     */
    public function saveMessagesPedido($pkPedido, $message) {
        $db = new SuperDataBase();
        $query = "CALL sp_add_message_pedido('$pkPedido','$message');";
        $result = $db->executeQuery($query);
        echo $query;
    }

    public function Imprimir($mesa) {
        $db = new SuperDataBase();
        $query = "select cantidad,pl.descripcion,precio from detallepedido dp inner join pedido p on dp.pkPediido=p.pkPediido inner join plato pl on dp.pkPlato=pl.pkPlato inner join mesas ms on p.pkMesa=ms.pkMesa where ms.pkMesa=$mesa;";
        $result = $db->executeQuery($query);
        $array = array();
        while ($row = $db->fecth_array($result)) {
            $array[] = array(
                "cantidad" => $row['cantidad'],
                "descripcion" => utf8_encode($row['descripcion']),
                "precio" => $row['precio'],
            );
        }
        echo json_encode($array);
    }

    public function CancelaMesa($pkPedido) {
        $db = new SuperDataBase();
        $query = "CALL sp_cancelaMesaPedido('$pkPedido');";
        $db->executeQuery($query);
        echo $query;
    }

    
    //Funcion para cancelar un pedido a credito
    //Actualizado 2019 para verificar si el pedido ya fue cerrado
    public function CancelaPedidoCredito($pkPedido, $tipoCliente, $documento, $total, $comentario) {
        $db = new SuperDataBase();
        //Antes de todo verificamos si el pedido sigue abierto
        $r_abierto = $db->executeQueryEx("Select 
                pedido.*,
                mesas.nmesa as nombre_mesa,
                salon.nombre as nombre_salon
            from pedido

            left join mesas on pedido.pkMesa = mesas.pkMesa
            left join salon on salon.pkSalon = mesas.pkSalon
            
            where pkPediido = '".$pkPedido."' AND pedido.estado = 0");

        if($row_abierto = $db->fecth_array($r_abierto)){
            // Hacemos la cancelacion
            $query = "CALL sp_CancelaCuentaCredito('$pkPedido',$tipoCliente,'$documento',$total);";
            $db->executeQuery($query); 
            //Insertamos el pedido en que caja fue hecho
            $query_caja = "Insert into accion_caja values(NULL,'".$pkPedido."','PED','".$_COOKIE["c"]."')";
            $db->executeQuery($query_caja);

            $query = "select 
                    detallepedido.*,
                    plato.descripcion as nombre_plato
                from detallepedido 

                left join plato on detallepedido.pkPlato = plato.pkPlato
                
                where pkPediido = $pkPedido and detallepedido.estado > 0 and detallepedido.estado < 3";

            $res = $db->executeQueryEx($query);

            $items = "<ul>";
            $total = 0;

            $mesa = $row_abierto['nombre_mesa'];
            $salon = $row_abierto['nombre_salon'];
            $cliente = "<b>".$documento."</b>";

            while ($row = $db->fecth_array($res)) {

                $plato = $row['nombre_plato'];
                $cantidad = $row['cantidad'];

                $items .= "<li>($cantidad) $plato</li>";
                $total += $row['cantidad'] * $row['precio'];
            }

            $items .= "</ul>";

            $cliente .= " <br> <b>" . $_REQUEST['valor1'] . "</b>";
            $cliente .= " <br> <b>" . $_REQUEST['valor2'] . "</b>";

            if ($comentario) {
                $query = "insert into motivo_venta (pkPediido, motivo) values ";
                $query .= "($pkPedido, '$comentario')";

                $db->executeQueryEx($query);
            }

            $this->envia_correo_credito_venta($pkPedido, "", $items, $total, $mesa, $salon, $cliente, $comentario);

            //Imprimimos Resultado
            $resultado = array();
            $resultado["exito"] = 1;
            echo json_encode($resultado);
        }else{
            //Imprimimos Resultado
            $resultado = array();
            $resultado["exito"] = 0;
            echo json_encode($resultado);
        }
    }

    //Funcion para cancelar un pedido por consumo
    //Actualizado 2019 para verificar si el pedido ya fue cerrado
    public function CancelaPedidoACuenta($pkPedido, $tipoCliente, $documento, $total, $comentario) {
        $db = new SuperDataBase();
        $user = UserLogin::get_id();
        //Antes de todo verificamos si el pedido sigue abierto
        $r_abierto = $db->executeQueryEx("Select 
                pedido.*,
                mesas.nmesa as nombre_mesa,
                salon.nombre as nombre_salon
            from pedido

            left join mesas on pedido.pkMesa = mesas.pkMesa
            left join salon on salon.pkSalon = mesas.pkSalon
            
            where pkPediido = '".$pkPedido."' AND (pedido.estado = 0 OR pedido.estado = 4)");
        if($row_abierto = $db->fecth_array($r_abierto)){
            //Hacemos la cancelacion
            $query = "CALL sp_CancelaCuentaSinPago('$pkPedido',$tipoCliente,'$documento',$total,$user);";
            $db->executeQuery($query);
            //Insertamos el pedido en que caja fue hecho
            $query_caja = "Insert into accion_caja values(NULL,'".$pkPedido."','PED','".$_COOKIE["c"]."')";
            $db->executeQuery($query_caja);


            $query = "select 
                    detallepedido.*,
                    plato.descripcion as nombre_plato
                from detallepedido 

                left join plato on detallepedido.pkPlato = plato.pkPlato
                
                where pkPediido = $pkPedido and detallepedido.estado > 0 and detallepedido.estado < 3";

            $res = $db->executeQueryEx($query);

            $items = "<ul>";
            $total = 0;

            $mesa = $row_abierto['nombre_mesa'];
            $salon = $row_abierto['nombre_salon'];
            $cliente = "<b>".$documento."</b>";

            while ($row = $db->fecth_array($res)) {

                $plato = $row['nombre_plato'];
                $cantidad = $row['cantidad'];

                $items .= "<li>($cantidad) $plato</li>";
                $total += $row['cantidad'] * $row['precio'];
            }

            $items .= "</ul>";

            $cliente .= " <br> <b>" . $_REQUEST['valor1'] . "</b>";
            $cliente .= " <br> <b>" . $_REQUEST['valor2'] . "</b>";

            if ($comentario) {
                $query = "insert into motivo_venta (pkPediido, motivo) values ";
                $query .= "($pkPedido, '$comentario')";

                $db->executeQueryEx($query);
            }

            $this->envia_correo_consumo_venta($pkPedido, "", $items, $total, $mesa, $salon, $cliente, $comentario);

            //Imprimimos Resultado
            $resultado = array();
            $resultado["exito"] = 1;
            echo json_encode($resultado);
        }else{
            //Imprimimos Resultado
            $resultado = array();
            $resultado["exito"] = 0;
            echo json_encode($resultado);
        }
    }
    
    public function CambioMesa($pkPedido, $mesaAnterior, $mesaActual) {
        $db = new SuperDataBase();
        $query1 = "update pedido set pkMesa=$mesaActual where pkPediido='$pkPedido';";
        $db->executeQuery($query1);
        $query2 = "update mesas set estado=0 where pkMesa=$mesaAnterior;";
        $db->executeQuery($query2);
        $query3 = "update mesas set estado=1 where pkMesa=$mesaActual;";
        $db->executeQuery($query3);
    }

    public function cambiaPedidoMesa($pkDetalle, $pkPedidoNuevo) {
        $db = new SuperDataBase();
        $query = "update detallepedido set pkPediido='$pkPedidoNuevo' where pkDetallePedido='$pkDetalle';";
        $db->executeQuery($query);
    }

    public function upddate_pedido_comprobante($pkPedido, $detalle) {
        $db = new SuperDataBase();
        $query = "update detallepedido set pkPediido='$pkPedido' where pkDetallePedido='$detalle';";
        $db->executeQuery($query);
    }

    public function upddate_pedido_cliente_Mesas($pkPedido, $documento, $tipo, $nmesas) {
        $db = new SuperDataBase();
        $query = "update pedido set npersonas=$nmesas, documento='$documento', tipo_cliente=$tipo where pkPediido='$pkPedido';";
        $db->executeQuery($query);
        echo $query;
    }

    public function consultaPedidosPromocion($pkPromocion,$cantidad) {
        $sucursal = UserLogin::get_pkSucursal();
        $db = new SuperDataBase();
        $query = "SELECT *, case when character_length(pkProducto) <6 then ( select upper(descripcion) from plato pl where pl.pkPlato=d.pkPlato) when character_length(pkPlato)<6 then (select upper(descripcion) from productos pr where pr.pkProducto=d.pkProducto ) end as pedido,case when character_length(pkProducto) <6 then ( select upper(pkTipo) from plato_sucursal pl where pl.pkPlato=d.pkPlato and pkSucursal='$sucursal') when character_length(pkPlato)<6 then (select upper(pkTipo) from producto_sucursal pr where pr.pkProducto=d.pkProducto and pkSucursal='$sucursal' ) end as tipo FROM detalle_producto d where pkPromocion ='$pkPromocion';";
        $array = array();
        $result = $db->executeQuery($query);

        while ($row = $db->fecth_array($result)) {

            $array[] = array(
                "pedido" => $row['pkPlato']."".$row['pkProducto'],
                "cantidad" => (float) $row['cantidad']*$cantidad,
                "tipo" => $row['tipo'],
            );
        }
        return $array;
    }
    
    public function updateTipoPedido($pkDetalle,$estado){
        $db= new SuperDataBase();
        $query ="update detallepedido set tipoPedido=$estado where pkDetallePedido=$pkDetalle";
        $db->executeQuery($query);
        $db->getId();
    }
    
    public function eliminaEstadocero($id)
    {
        $db = new SuperDataBase();
        $query = "delete from detallepedido where pkdetallepedido=$id";
        $db->executeQuery($query);
    }
    
    public function eliminaEstadocero1($id,$unidades)
    {
        $db = new SuperDataBase();
        $query = "";
        if(intval($unidades) > 1){
            $ncantidad = intval($unidades)-1;
            $query = "Update detallepedido set cantidad = '".$ncantidad."' where pkDetallePedido = '".$id."'";
        }else{
            $query = "Delete from detallepedido where pkDetallePedido = '".$id."'";
        }
        $db->executeQuery($query);
    }
    
    public function descuento($des,$pk) {
        $db = new SuperDataBase();
        $query = "Update pedido set descuento = '".$des."' where pkPediido = '".$pk."'";
        $db->executeQuery($query);
        echo $query;
    }

}
