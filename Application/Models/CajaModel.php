<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Models_CajaModel {

    public function RegistrarMontoInicial($cantidad) {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $user = UserLogin::get_id();
        $query = "Call sp_registro_montoInicial($cantidad,'$sucursal',$user)";
        $db->executeQuery($query);
        echo $query;
    }

    public function RegistrarGastoDiario($descripcion, $cantidad) {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $user = UserLogin::get_id();
        $query = "Call sp_registro_pago_diario('$descripcion','$sucursal',$cantidad,$user)";
        $db->executeQuery($query);
        echo $query;
    }

    public function _listCaja($fecha) {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $user = UserLogin::get_id();
        $query = "SELECT * FROM gastos_diarios g where estado=0 and fecha='$fecha' and pkSucursal='$sucursal';";
        $result = $db->executeQuery($query);
        $array = array();
        while ($row = $db->fecth_array($result)) {
            $array[] = array(
                "pkGastosDiarios" => $row['pkGastosDiarios'],
                "cantidad" => $row['cantidad'],
                "dateModify" => $row['dateModify'],
                "fecha" => $row['fecha'],
                "pkUser" => $row['pkUser'],
                "descripcion" => $row['descripcion'],
                "pkSucursal" => $row['pkSucursal'],
            );
        }
        echo json_encode($array);
    }

    public function _ListMontoInicial() {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $user = UserLogin::get_id();
        $query = "CAll sp_listar_montoInicialDiario('$sucursal')";

        $result = $db->executeQuery($query);
        $array = array();
        while ($row = $db->fecth_array($result)) {
            $array[] = array(
                "cantidad" => (float) $row['cantidad'],
            );
        }
        echo json_encode($array);
    }

    public function _listFechasCierre() {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();

        $query = "CAll sp_getCierresDiarios('$sucursal');";
        $query1 = "Select * from corte where fin is null order by id DESC LIMIT 1";
        
        $corte = "";
        
        $result01 = $db->executeQuery($query1);
        if ($row0 = $db->fecth_array($result01)) {
            $corte = $row0["inicio"];
        }

        $result = $db->executeQuery($query);
        $array = array();
        while ($row = $db->fecth_array($result)) {
            $array[] = array(
                "proximo" => $row[0],
                "actual" => $row[1],
                "corte" => $corte
            );
        }        
        
        echo json_encode($array);
    }

    public function _verificandoMesasAbiertas() {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $user = UserLogin::get_id();
        $query = "CAll sp_verificarVentas('$sucursal');";
        $result = $db->executeQuery($query);
        $cantidad = 0;
        while ($row = $db->fecth_array($result)) {
            $cantidad = $row['cantidad'];
        }
        echo $cantidad;
    }

    public function _cierreDiario() {

        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $user = UserLogin::get_id();
        $query = "CAll sp_actualizarCierreDiario('$sucursal');";

        $db->executeQuery($query);
        echo $query;
    }

    public function _cierreActual() {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $user = UserLogin::get_id();
        $query = "update cierrediario set fecha= now() where pkCierreDiario = 1";

        $db->executeQueryEx($query);
    }

    public function _verificarMesaAbiertas() {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $user = UserLogin::get_id();
        $query = "SELECT count(*) as cantidad FROM mesas m inner join salon s on s.pkSalon=m.pkSalon where m.estado=1 AND s.pkSucursal='$sucursal';";

        $result = $db->executeQuery($query);
        $cantidad = 0;
        while ($row = $db->fecth_array($result)) {
            $cantidad = (float) $row['cantidad'];
        }
        return $cantidad;
        ;
    }

    public function fechaCierre() {
        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $query = "select fecha from cierrediario where pkSucursal='$sucursal';";
        $result= $db->executeQuery($query);
        $fecha = "";
        while ($row = $db->fecth_array($result)) {
            $fecha = $row[0];
        }
        return $fecha;
    }
    
    public function montoInicial() {
        $db = new SuperDataBase();
        $query = "select cantidad from monto_inicial order by pkMontoInicial LIMIT 1";
        $result= $db->executeQuery($query);
        $monto = 0;
        while ($row = $db->fecth_array($result)) {
            $monto = $row[0];
        }
        return $monto;
    }
    
    //Funcion para imprimir
    public function imprimeCierre($cierre,$term,$cajero,$corte,$inicial,$caja) {
        $db = new SuperDataBase();
        $query = "Insert into cola_impresion values(NULL,'".$cierre."','CIE','".$term."','".$cajero.",".$corte.",".$inicial.",".$caja."',0)";
        $db->executeQuery($query);
        echo '1';
    }

    //Funcion para imprimir wincha
    public function imprimeWincha($cierre,$term,$cajero,$corte,$inicial,$caja) {
        $db = new SuperDataBase();
        $query = "Insert into cola_impresion values(NULL,'".$cierre."','WIN','".$term."','".$cajero.",".$corte.",".$inicial.",".$caja."',0)";
        $db->executeQuery($query);
        echo '1';
    }
    
    //Funciones para cortes
    public function _hacerCorte($caja) {

        $db = new SuperDataBase();

        $query1 = "Select c.* from corte c, accion_caja ac where c.fin is null AND ac.pk_accion = c.id AND ac.tipo_accion = 'CUT' AND ac.caja = '".$caja."' order by c.id DESC LIMIT 1";
               
        $result01 = $db->executeQuery($query1);
        $idc = "";
        if ($row0 = $db->fecth_array($result01)) {
            $idc = $row0["id"];            
        }
        
        if($idc !== ""){
            $sqlact = "Update corte set fin = '".date("Y-m-d H:i:s")."' where id = ".$idc."";
            $db->executeQuery($sqlact);
        }
        
        $corte = date("Y-m-d H:i:s");
        
        $query = "SELECT * FROM cierrediario LIMIT 1";
        $result = $db->executeQuery($query);
        $fecha_cierre = "";
        if ($row = $db->fecth_array($result)) {
            $fecha_cierre = $row[2];
        }
        
        $query2 = "INSERT INTO corte values(NULL,'".$fecha_cierre."','".$corte."',NULL,'0','1')";
        $db->executeQuery($query2);
        
        //Obtenemos ID insertado
        $aidi = $db->getId();
        
        //Insertamos el comprobante en que caja fue hecho
        $query_caja = "Insert into accion_caja values(NULL,'".$aidi."','CUT','".$caja."')";
        $db->executeQuery($query_caja);
        
        $array = array();
        $array[] = array(
            "corte" => $corte
        );
             
        echo json_encode($array);
    }
    
    public function _TotalDiaCorte($fecha,$corte_inicio,$caja) {
        error_reporting(E_ALL);
        $db = new SuperDataBase();

        $corte = array();
        
        $query_datos_corte = "Select c.* from corte c, accion_caja ac where c.inicio = '".$corte_inicio."' AND ac.pk_accion = c.id AND ac.tipo_accion = 'CUT' AND ac.caja = '".$caja."'";

        //echo $query_datos_corte;

        $tiempo_inicio = null;
        $tiempo_fin = null;

        $id_corte = null;
        
        //Limitamos corte
        $consulta_corte = " "; 
        $result01 = $db->executeQuery($query_datos_corte);
        if ($row0 = $db->fecth_array($result01)) {
            $id_corte = $row0["id"];
            if($row0["fin"] == ""){
                $tiempo_inicio = $row0["inicio"];
            }else{
                $tiempo_inicio = $row0["inicio"];
                $tiempo_fin = $row0["fin"];
            }
        }

        //Obtenemos los medios a buscar
        $medios = array(); 
        $resultado_medios = $db->executeQuery("Select mp.id, mp.moneda, mp.nombre, m.simbolo from medio_pago mp, moneda m where mp.estado > 0 AND mp.moneda = m.id");
        while ($medio = $db->fecth_array($resultado_medios)) {
            if(intval($medio["id"]) === 1){
                $resultado_monedas = $db->executeQuery("Select * from moneda where estado > 0");
                while ($moneda = $db->fecth_array($resultado_monedas)) {
                    $tmp = array();
                    $tmp["nombre"] = $medio["nombre"]." ".$moneda["simbolo"];
                    $tmp["id_medio"] = $medio["id"];
                    $tmp["id_moneda"] = $moneda["id"];
                    $medios[] = $tmp;
                }
            }else{
                $tmp = array();
                $tmp["nombre"] = $medio["nombre"]." ".$medio["simbolo"];
                $tmp["id_medio"] = $medio["id"];
                $tmp["id_moneda"] = $medio["moneda"];
                $medios[] = $tmp;
            }
        }

        //Obtenemos todos los tipos de pagos
        $tipos_gastos = array();
        $resultado_gastos = $db->executeQuery("Select * from tipo_gasto where estado = 1");
        while ($gasto = $db->fecth_array($resultado_gastos)) {
            $tmp = array();
            $tmp["id"] = $gasto["id"];
            $tmp["nombre"] = $gasto["nombre"];
            $tmp["direccion"] = $gasto["direccion"];
            $tipos_gastos[] = $tmp;
        }

        //Obtenemos todas las monedas
        $monedas = array();
        $resultado_monedas = $db->executeQuery("Select * from moneda where estado > 0");
        while ($moneda = $db->fecth_array($resultado_monedas)) {
            $tmp = array();
            $tmp["id"] = $moneda["id"];
            $tmp["nombre"] = $moneda["nombre"];
            $tmp["simbolo"] = $moneda["simbolo"];
            $monedas[] = $tmp;
        }
        

        //Primero obtenemos indicadores desde tabla pedidos
        $consulta_vendido = "";
        $consulta_credito = "";
        $consulta_consumo = "";
        $consulta_descuento = "";

        $consulta_compras = "";

        if($tiempo_fin <> null){
            $consulta_vendido = "Select sum(p.total) as resultado from pedido p, accion_caja ac where p.estado = 1 AND ac.pk_accion = p.pkPediido AND ac.tipo_accion = 'PED' AND ac.caja = '".$caja."' AND p.dateModify BETWEEN '".$tiempo_inicio."' AND '".$tiempo_fin."'";
            $consulta_credito =  "Select sum(p.total) as resultado from pedido p, accion_caja ac where p.estado = 4 AND ac.pk_accion = p.pkPediido AND ac.tipo_accion = 'PED' AND ac.caja = '".$caja."' AND p.dateModify BETWEEN '".$tiempo_inicio."' AND '".$tiempo_fin."'";
            $consulta_consumo =  "Select sum(p.total) as resultado from pedido p, accion_caja ac where p.estado = 5 AND ac.pk_accion = p.pkPediido AND ac.tipo_accion = 'PED' AND ac.caja = '".$caja."' AND p.dateModify BETWEEN '".$tiempo_inicio."' AND '".$tiempo_fin."'";
            $consulta_descuento =  "Select sum(p.descuento) as resultado from pedido p, accion_caja ac where p.estado <> 3 AND ac.pk_accion = p.pkPediido AND ac.tipo_accion = 'PED' AND ac.caja = '".$caja."' AND p.dateModify BETWEEN '".$tiempo_inicio."' AND '".$tiempo_fin."'";
            
            $consulta_compras = "SELECT
                IFNULL(sum(monto), 0) AS resultado
            FROM
                movimiento_dinero
            LEFT JOIN compras ON compras.id = movimiento_dinero.id_origen
            WHERE
                tipo_origen = 'COM'
            AND fecha_hora BETWEEN '$tiempo_inicio' AND '$tiempo_fin'
            AND compras.deleted_at IS NULL";
        }else{
            $consulta_vendido = "Select sum(p.total) as resultado from pedido p, accion_caja ac where p.estado = 1 AND p.dateModify >= '".$tiempo_inicio."' AND ac.pk_accion = p.pkPediido AND ac.tipo_accion = 'PED' AND ac.caja = '".$caja."'";
            $consulta_credito =  "Select sum(p.total) as resultado from pedido p, accion_caja ac where p.estado = 4 AND p.dateModify >= '".$tiempo_inicio."' AND ac.pk_accion = p.pkPediido AND ac.tipo_accion = 'PED' AND ac.caja = '".$caja."'";
            $consulta_consumo =  "Select sum(p.total) as resultado from pedido p, accion_caja ac where p.estado = 5 AND p.dateModify >= '".$tiempo_inicio."' AND ac.pk_accion = p.pkPediido AND ac.tipo_accion = 'PED' AND ac.caja = '".$caja."'";
            $consulta_descuento =  "Select sum(p.descuento) as resultado from pedido p, accion_caja ac where p.estado <> 3 AND p.dateModify >= '".$tiempo_inicio."' AND ac.pk_accion = p.pkPediido AND ac.tipo_accion = 'PED' AND ac.caja = '".$caja."'";
            
            $consulta_compras = "SELECT
                IFNULL(sum(monto), 0) AS resultado
            FROM
                movimiento_dinero
            LEFT JOIN compras ON compras.id = movimiento_dinero.id_origen
            WHERE
                tipo_origen = 'COM'
            AND fecha_hora >= '$tiempo_inicio'
            AND compras.deleted_at IS NULL";
        }

        $result_vendido = $db->executeQuery($consulta_vendido);
        if ($row0 = $db->fecth_array($result_vendido)) {
            $corte["vendido"] = floatval($row0["resultado"]);
        }

        $result_credito = $db->executeQuery($consulta_credito);
        if ($row0 = $db->fecth_array($result_credito)) {
            $corte["credito"] = floatval($row0["resultado"]);
        }

        $result_consumo = $db->executeQuery($consulta_consumo);
        if ($row0 = $db->fecth_array($result_consumo)) {
            $corte["consumo"] = floatval($row0["resultado"]);
        }

        $result_descuento = $db->executeQuery($consulta_descuento);
        if ($row0 = $db->fecth_array($result_descuento)) {
            $corte["descuento"] = floatval($row0["resultado"]);
        }

        $result_compras = $db->executeQuery($consulta_compras);
        if ($row0 = $db->fecth_array($result_compras)) {
            $corte["comprado"] = floatval($row0["resultado"]);
        }

        if($tiempo_fin <> null){
            //Obtenemos totales desde tabla movimiento dinero
            foreach($medios as $med){
                $query_tmp = "select sum(monto) as resultado from movimiento_dinero where id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND fecha_hora BETWEEN '".$tiempo_inicio."' AND '".$tiempo_fin."' AND caja = '".$caja."' AND estado = 1";
                $result_tmp = $db->executeQuery($query_tmp);
                if ($row0 = $db->fecth_array($result_tmp)) {
                    $corte["tot_".$med["id_medio"]."_".$med["id_moneda"]] = floatval($row0["resultado"]);
                }
            }

            $query_tmp = "select sum(monto) as resultado from movimiento_dinero where id_medio = '0' AND comentario = 'DETRACCION' AND moneda = '1' AND fecha_hora BETWEEN '".$tiempo_inicio."' AND '".$tiempo_fin."' AND caja = '".$caja."' AND estado = 1";
            $result_tmp = $db->executeQuery($query_tmp);
            if ($row0 = $db->fecth_array($result_tmp)) {
                $corte["tot_detraccion"] = floatval($row0["resultado"]);
            }

            //Obtenemos totales en propinas (y los sumamos a los otros totales)
            foreach($medios as $med){
                $query_tmp = "select sum(pp.monto) as resultado from pedido_propina pp, accion_caja ac where pp.id_medio = '".$med["id_medio"]."' AND pp.moneda = '".$med["id_moneda"]."' AND pp.fecha_hora BETWEEN '".$tiempo_inicio."' AND '".$tiempo_fin."' AND pp.pkPediido = ac.pk_accion AND ac.tipo_accion = 'PED' AND ac.caja = '".$caja."'";
                $result_tmp = $db->executeQuery($query_tmp);
                if ($row0 = $db->fecth_array($result_tmp)) {
                    $corte["prop_".$med["id_medio"]."_".$med["id_moneda"]] = floatval($row0["resultado"]);
                    $corte["tot_".$med["id_medio"]."_".$med["id_moneda"]] = $corte["tot_".$med["id_medio"]."_".$med["id_moneda"]] + floatval($row0["resultado"]);
                }
            }

            //Obtenemos totales de ventas desde tabla movimiento dinero 
            foreach($medios as $med){
                $query_tmp = "select sum(monto) as resultado from movimiento_dinero where id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND tipo_origen = 'PED' AND fecha_hora BETWEEN '".$tiempo_inicio."' AND '".$tiempo_fin."' AND caja = '".$caja."' AND estado = 1";          
                $result_tmp = $db->executeQuery($query_tmp);
                if ($row0 = $db->fecth_array($result_tmp)) {
                    $corte["ven_".$med["id_medio"]."_".$med["id_moneda"]] = floatval($row0["resultado"]);
                }
            }

            //Obtenemos totales de ingresos adicionales desde tabla movimiento dinero 
            foreach($medios as $med){
                $query_tmp = "select sum(monto) as resultado from movimiento_dinero where id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND tipo_origen = 'GAS' AND id_aux = 1 AND fecha_hora BETWEEN '".$tiempo_inicio."' AND '".$tiempo_fin."' AND caja = '".$caja."' AND estado = 1";
                $result_tmp = $db->executeQuery($query_tmp);
                if ($row0 = $db->fecth_array($result_tmp)) {
                    $corte["ing_".$med["id_medio"]."_".$med["id_moneda"]] = floatval($row0["resultado"]);
                }
            }

            //Obtenemos totales de salidas desde tabla movimiento dinero 
            foreach($medios as $med){
                $query_tmp = "select sum(monto) as resultado from movimiento_dinero where id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND tipo_origen = 'GAS' AND id_aux = 0 AND fecha_hora BETWEEN '".$tiempo_inicio."' AND '".$tiempo_fin."' AND caja = '".$caja."' AND estado = 1";
                $result_tmp = $db->executeQuery($query_tmp);
                if ($row0 = $db->fecth_array($result_tmp)) {
                    $corte["sal_".$med["id_medio"]."_".$med["id_moneda"]] = floatval($row0["resultado"]);
                }
            }

            //Obtenemos totales por movimiento
            foreach($tipos_gastos as $tip){
                foreach($medios as $med){
                    $query_tmp = "select sum(monto) as resultado from movimiento_dinero where tipo_origen = 'GAS' AND id_origen = '".$tip["id"]."' AND id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND fecha_hora BETWEEN '".$tiempo_inicio."' AND '".$tiempo_fin."' AND caja = '".$caja."' AND estado = 1";
                    $result_tmp = $db->executeQuery($query_tmp);
                    if ($row0 = $db->fecth_array($result_tmp)) {
                        $corte["tip_".$tip["id"]."_".$med["id_medio"]."_".$med["id_moneda"]] = floatval($row0["resultado"]);
                    }
                }
            }
        }else{
            //Obtenemos totales desde tabla movimiento dinero
            foreach($medios as $med){
                $query_tmp = "select sum(monto) as resultado from movimiento_dinero where id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND fecha_hora >= '".$tiempo_inicio."' AND caja = '".$caja."' AND estado = 1";
                $result_tmp = $db->executeQuery($query_tmp);
                if ($row0 = $db->fecth_array($result_tmp)) {
                    $corte["tot_".$med["id_medio"]."_".$med["id_moneda"]] = floatval($row0["resultado"]);
                }
            }

            $query_tmp = "select sum(monto) as resultado from movimiento_dinero where id_medio = '0' AND comentario = 'DETRACCION' AND moneda = '1' AND fecha_hora >= '".$tiempo_inicio."' AND caja = '".$caja."' AND estado = 1";
            $result_tmp = $db->executeQuery($query_tmp);
            if ($row0 = $db->fecth_array($result_tmp)) {
                $corte["tot_detraccion"] = floatval($row0["resultado"]);
            }

            //Obtenemos totales en propinas (y los sumamos a los otros totales)
            foreach($medios as $med){
                $query_tmp = "select sum(pp.monto) as resultado from pedido_propina pp, accion_caja ac where pp.id_medio = '".$med["id_medio"]."' AND pp.moneda = '".$med["id_moneda"]."' AND pp.fecha_hora >= '".$tiempo_inicio."' AND pp.pkPediido = ac.pk_accion AND ac.tipo_accion = 'PED' AND ac.caja = '".$caja."'";
                $result_tmp = $db->executeQuery($query_tmp);
                if ($row0 = $db->fecth_array($result_tmp)) {
                    $corte["prop_".$med["id_medio"]."_".$med["id_moneda"]] = floatval($row0["resultado"]);
                    $corte["tot_".$med["id_medio"]."_".$med["id_moneda"]] = $corte["tot_".$med["id_medio"]."_".$med["id_moneda"]] + floatval($row0["resultado"]);
                }
            }

            //Obtenemos totales de ventas desde tabla movimiento dinero 
            foreach($medios as $med){
                $query_tmp = "select sum(monto) as resultado from movimiento_dinero where id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND tipo_origen = 'PED' AND fecha_hora >= '".$tiempo_inicio."' AND caja = '".$caja."' AND estado = 1";
                $result_tmp = $db->executeQuery($query_tmp);
                if ($row0 = $db->fecth_array($result_tmp)) {
                    $corte["ven_".$med["id_medio"]."_".$med["id_moneda"]] = floatval($row0["resultado"]);
                }
            }

            //Obtenemos totales de ingresos adicionales desde tabla movimiento dinero 
            foreach($medios as $med){
                $query_tmp = "select sum(monto) as resultado from movimiento_dinero where id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND tipo_origen = 'GAS' AND id_aux = 1 AND fecha_hora >= '".$tiempo_inicio."' AND caja = '".$caja."' AND estado = 1";
                $result_tmp = $db->executeQuery($query_tmp);
                if ($row0 = $db->fecth_array($result_tmp)) {
                    $corte["ing_".$med["id_medio"]."_".$med["id_moneda"]] = floatval($row0["resultado"]);
                }
            }

            //Obtenemos totales de salidas desde tabla movimiento dinero 
            foreach($medios as $med){
                $query_tmp = "select sum(monto) as resultado from movimiento_dinero where id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND tipo_origen = 'GAS' AND id_aux = 0 AND fecha_hora >= '".$tiempo_inicio."' AND caja = '".$caja."' AND estado = 1";
                $result_tmp = $db->executeQuery($query_tmp);
                if ($row0 = $db->fecth_array($result_tmp)) {
                    $corte["sal_".$med["id_medio"]."_".$med["id_moneda"]] = floatval($row0["resultado"]);
                }
            }

            //Obtenemos totales por movimiento
            foreach($tipos_gastos as $tip){
                foreach($medios as $med){
                    $query_tmp = "select sum(monto) as resultado from movimiento_dinero where tipo_origen = 'GAS' AND id_origen = '".$tip["id"]."' AND moneda = '".$med["id_moneda"]."' AND id_medio = '".$med["id_medio"]."' AND fecha_hora >= '".$tiempo_inicio."' AND caja = '".$caja."' AND estado = 1";
                    $result_tmp = $db->executeQuery($query_tmp);
                    if ($row0 = $db->fecth_array($result_tmp)) {
                        $corte["tip_".$tip["id"]."_".$med["id_medio"]."_".$med["id_moneda"]] = floatval($row0["resultado"]);
                    }
                }
            }
        }

        //Obtenemos Montos Iniciales
        foreach($monedas as $mo){
            $query_inicial = "Select * from movimiento_dinero where moneda = '".$mo["id"]."' AND id_origen = '".$id_corte."' AND tipo_origen = 'CUT' AND caja = '".$caja."'";
            $result_inicial = $db->executeQuery($query_inicial);
            if ($row0 = $db->fecth_array($result_inicial)) {
                $corte["ini_".$mo["id"]] = floatval($row0["monto"]);
            }else{
                $corte["ini_".$mo["id"]] = 0;
            }
        }

        //Obtenemos Platos vendidos
        $query_platos = "";
        if($tiempo_fin <> null){
            $query_platos = "Select pl.descripcion, sum(dp.cantidad) as total, sum(if(p.estado != 0, dp.cantidad, 0)) as salidas, sum(if(p.estado = 0, dp.cantidad, 0)) as cobrar from detallepedido dp, pedido p, plato pl, accion_caja ac where dp.pkPlato = pl.pkPlato and p.pkPediido = dp.pkPediido and p.estado != 3 AND dp.horaPedido BETWEEN '".$tiempo_inicio."' AND '".$tiempo_fin."' AND dp.estado > 0 AND dp.estado < 3 AND dp.pkDetallePedido = ac.pk_accion AND ac.tipo_accion = 'DET' AND ac.caja = '".$caja."' GROUP BY dp.pkPlato ORDER BY salidas DESC";
        }else{
            $query_platos = "Select pl.descripcion, sum(dp.cantidad) as total, sum(if(p.estado != 0, dp.cantidad, 0)) as salidas, sum(if(p.estado = 0, dp.cantidad, 0)) as cobrar from detallepedido dp, pedido p, plato pl, accion_caja ac where dp.pkPlato = pl.pkPlato and p.pkPediido = dp.pkPediido and p.estado != 3 AND dp.horaPedido >= '".$tiempo_inicio."' AND dp.estado > 0 AND dp.estado < 3 AND dp.pkDetallePedido = ac.pk_accion AND ac.tipo_accion = 'DET' AND ac.caja = '".$caja."' GROUP BY dp.pkPlato ORDER BY salidas DESC";
        }
        $result_platos = $db->executeQueryEx($query_platos);
        $r_platos = array();
        while($row = $db->fecth_array($result_platos)){
            $t_plato = array();
            $t_plato["descripcion"] = $row["descripcion"];
            $t_plato["salidas"] = $row["salidas"];
            $t_plato["cobrar"] = $row["cobrar"];
            $t_plato["total"] = $row["total"];
            $r_platos[] = $t_plato;
        }
        $corte["platos"] = $r_platos;

        //Obtenemos Movimientos con tarjeta
        $query_movimientos = "";
        if($tiempo_fin <> null){
            $query_movimientos = "Select md.*, mp.nombre as mp_nombre, m.simbolo as m_simbolo from movimiento_dinero md, medio_pago mp, moneda m WHERE md.id_medio = mp.id AND md.moneda = m.id AND md.fecha_hora BETWEEN '".$tiempo_inicio."' AND '".$tiempo_fin."' AND md.caja = '".$caja."' AND md.id_medio > 1";
        }else{
            $query_movimientos = "Select md.*, mp.nombre as mp_nombre, m.simbolo as m_simbolo from movimiento_dinero md, medio_pago mp, moneda m WHERE md.id_medio = mp.id AND md.moneda = m.id AND md.fecha_hora >= '".$tiempo_inicio."' AND md.caja = '".$caja."' AND md.id_medio > 1";
        }
        $resultado_movimientos = $db->executeQuery($query_movimientos);
        $r_movs = array();
        while($row = $db->fecth_array($resultado_movimientos)){
            $t_mov = array();
            $t_mov["id"] = $row["id_origen"];
            $t_mov["op"] = $row["comentario"];
            $t_mov["medio"] = $row["mp_nombre"];
            $t_mov["moneda"] = $row["m_simbolo"];
            $t_mov["total"] = $row["monto"];
            $r_movs[] = $t_mov;
        }
        $corte["movimientos"] = $r_movs;

        //get ventas por consumo y credito
        $query = "SELECT
            pkPediido AS id,
            documento AS cliente,
            IF (
                estado = 4,
                'CREDITO',
                'CONSUMO'
            ) AS tipo,
            total
        FROM
            pedido
        WHERE
            estado IN (4, 5) and fechaCierre = '$fecha'";

        $res = $db->executeQueryEx($query);

        $r_cons_cred = [];

        while ($row = $db->fecth_array($res)) {
            $row['total'] = number_format($row['total'], 2);
            $r_cons_cred[] = $row;
        }

        $corte["consumo_credito"] = $r_cons_cred;
        
        return $corte;
    }

    public function _TotalDia($fecha) {
        $db = new SuperDataBase();

        $corte = array();
        
        $query_datos_corte = "Select * from corte where fecha_cierre = '".$fecha."' order by id ASC";

        $tiempo_inicio = null;
        $tiempo_fin = null;
        
        //Limitamos corte
        $vez = 0;
        $result01 = $db->executeQuery($query_datos_corte);
        while ($row0 = $db->fecth_array($result01)) {
            if($vez === 0){
                $vez = 1;
                $tiempo_inicio = $row0["inicio"];
            }

            if($row0["fin"] !== ""){
                $tiempo_fin = $row0["fin"];
            }else{
                $tiempo_fin = null;
            }
        }

        //Obtenemos los medios a buscar
        $medios = array(); 
        $resultado_medios = $db->executeQuery("Select mp.id, mp.moneda, mp.nombre, m.simbolo from medio_pago mp, moneda m where mp.estado > 0 AND mp.moneda = m.id");
        while ($medio = $db->fecth_array($resultado_medios)) {
            if(intval($medio["id"]) === 1){
                $resultado_monedas = $db->executeQuery("Select * from moneda where estado > 0");
                while ($moneda = $db->fecth_array($resultado_monedas)) {
                    $tmp = array();
                    $tmp["nombre"] = $medio["nombre"]." ".$moneda["simbolo"];
                    $tmp["id_medio"] = $medio["id"];
                    $tmp["id_moneda"] = $moneda["id"];
                    $medios[] = $tmp;
                }
            }else{
                $tmp = array();
                $tmp["nombre"] = $medio["nombre"]." ".$medio["simbolo"];
                $tmp["id_medio"] = $medio["id"];
                $tmp["id_moneda"] = $medio["moneda"];
                $medios[] = $tmp;
            }
        }

        //Obtenemos todos los tipos de pagos
        $tipos_gastos = array();
        $resultado_gastos = $db->executeQuery("Select * from tipo_gasto where estado = 1");
        while ($gasto = $db->fecth_array($resultado_gastos)) {
            $tmp = array();
            $tmp["id"] = $gasto["id"];
            $tmp["nombre"] = $gasto["nombre"];
            $tmp["direccion"] = $gasto["direccion"];
            $tipos_gastos[] = $tmp;
        }

        //Obtenemos todas las monedas
        $monedas = array();
        $resultado_monedas = $db->executeQuery("Select * from moneda where estado > 0");
        while ($moneda = $db->fecth_array($resultado_monedas)) {
            $tmp = array();
            $tmp["id"] = $moneda["id"];
            $tmp["nombre"] = $moneda["nombre"];
            $tmp["simbolo"] = $moneda["simbolo"];
            $monedas[] = $tmp;
        }

        //Primero obtenemos indicadores desde tabla pedidos
        $consulta_vendido = "";
        $consulta_credito = "";
        $consulta_consumo = "";
        $consulta_descuento = "";

        $consulta_compras = "";

        if($tiempo_fin <> null){
            $consulta_vendido = "Select sum(total) as resultado from pedido where estado = 1 AND dateModify BETWEEN '".$tiempo_inicio."' AND '".$tiempo_fin."'";
            $consulta_credito =  "Select sum(total) as resultado from pedido where estado = 4 AND dateModify BETWEEN '".$tiempo_inicio."' AND '".$tiempo_fin."'";
            $consulta_consumo =  "Select sum(total) as resultado from pedido where estado = 5 AND dateModify BETWEEN '".$tiempo_inicio."' AND '".$tiempo_fin."'";
            $consulta_descuento =  "Select sum(descuento) as resultado from pedido where estado <> 3 AND dateModify BETWEEN '".$tiempo_inicio."' AND '".$tiempo_fin."'";
        }else{
            $consulta_vendido = "Select sum(total) as resultado from pedido where estado = 1 AND dateModify >= '".$tiempo_inicio."'";
            $consulta_credito =  "Select sum(total) as resultado from pedido where estado = 4 AND dateModify >= '".$tiempo_inicio."'";
            $consulta_consumo =  "Select sum(total) as resultado from pedido where estado = 5 AND dateModify >= '".$tiempo_inicio."'";
            $consulta_descuento =  "Select sum(descuento) as resultado from pedido where estado <> 3 AND dateModify >= '".$tiempo_inicio."'";
        }

        $consulta_compras = "SELECT
            IFNULL(sum(monto), 0) AS resultado
        FROM
            movimiento_dinero
        LEFT JOIN compras ON compras.id = movimiento_dinero.id_origen
        WHERE
            tipo_origen = 'COM'
        AND fecha_cierre = '$fecha'
        AND compras.deleted_at IS NULL";

        $result_vendido = $db->executeQuery($consulta_vendido);
        if ($row0 = $db->fecth_array($result_vendido)) {
            $corte["vendido"] = floatval($row0["resultado"]);
        }

        $result_credito = $db->executeQuery($consulta_credito);
        if ($row0 = $db->fecth_array($result_credito)) {
            $corte["credito"] = floatval($row0["resultado"]);
        }

        $result_consumo = $db->executeQuery($consulta_consumo);
        if ($row0 = $db->fecth_array($result_consumo)) {
            $corte["consumo"] = floatval($row0["resultado"]);
        }

        $result_descuento = $db->executeQuery($consulta_descuento);
        if ($row0 = $db->fecth_array($result_descuento)) {
            $corte["descuento"] = floatval($row0["resultado"]);
        }

        $result_comprado = $db->executeQuery($consulta_compras);
        if ($row0 = $db->fecth_array($result_comprado)) {
            $corte["comprado"] = floatval($row0["resultado"]);
        }

        if($tiempo_fin <> null){
            //Obtenemos totales desde tabla movimiento dinero
            foreach($medios as $med){
                $query_tmp = "select sum(monto) as resultado from movimiento_dinero where id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND fecha_hora BETWEEN '".$tiempo_inicio."' AND '".$tiempo_fin."' AND estado = 1";
                $result_tmp = $db->executeQuery($query_tmp);
                if ($row0 = $db->fecth_array($result_tmp)) {
                    $corte["tot_".$med["id_medio"]."_".$med["id_moneda"]] = floatval($row0["resultado"]);
                }
            }

            $query_tmp = "select sum(monto) as resultado from movimiento_dinero where id_medio = '0' AND comentario = 'DETRACCION' AND moneda = '1' AND fecha_hora BETWEEN '".$tiempo_inicio."' AND '".$tiempo_fin."' AND estado = 1";
            $result_tmp = $db->executeQuery($query_tmp);
            if ($row0 = $db->fecth_array($result_tmp)) {
                $corte["tot_detraccion"] = floatval($row0["resultado"]);
            }

            //Obtenemos totales en propinas (y los sumamos a los otros totales)
            foreach($medios as $med){
                $query_tmp = "select sum(monto) as resultado from pedido_propina where id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND fecha_hora BETWEEN '".$tiempo_inicio."' AND '".$tiempo_fin."'";
                $result_tmp = $db->executeQuery($query_tmp);
                if ($row0 = $db->fecth_array($result_tmp)) {
                    $corte["prop_".$med["id_medio"]."_".$med["id_moneda"]] = floatval($row0["resultado"]);
                    $corte["tot_".$med["id_medio"]."_".$med["id_moneda"]] = $corte["tot_".$med["id_medio"]."_".$med["id_moneda"]] + floatval($row0["resultado"]);
                }
            }

            //Obtenemos totales de ventas desde tabla movimiento dinero 
            foreach($medios as $med){
                $query_tmp = "select sum(monto) as resultado from movimiento_dinero where id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND tipo_origen = 'PED' AND fecha_hora BETWEEN '".$tiempo_inicio."' AND '".$tiempo_fin."' AND estado = 1";
                $result_tmp = $db->executeQuery($query_tmp);
                if ($row0 = $db->fecth_array($result_tmp)) {
                    $corte["ven_".$med["id_medio"]."_".$med["id_moneda"]] = floatval($row0["resultado"]);
                }
            }

            //Obtenemos totales de ingresos adicionales desde tabla movimiento dinero 
            foreach($medios as $med){
                $query_tmp = "select sum(monto) as resultado from movimiento_dinero where id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND tipo_origen = 'GAS' AND id_aux = 1 AND fecha_hora BETWEEN '".$tiempo_inicio."' AND '".$tiempo_fin."' AND estado = 1";
                $result_tmp = $db->executeQuery($query_tmp);
                if ($row0 = $db->fecth_array($result_tmp)) {
                    $corte["ing_".$med["id_medio"]."_".$med["id_moneda"]] = floatval($row0["resultado"]);
                }
            }

            //Obtenemos totales de salidas desde tabla movimiento dinero 
            foreach($medios as $med){
                $query_tmp = "select sum(monto) as resultado from movimiento_dinero where id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND tipo_origen = 'GAS' AND id_aux = 0 AND fecha_hora BETWEEN '".$tiempo_inicio."' AND '".$tiempo_fin."' AND estado = 1";
                $result_tmp = $db->executeQuery($query_tmp);
                if ($row0 = $db->fecth_array($result_tmp)) {
                    $corte["sal_".$med["id_medio"]."_".$med["id_moneda"]] = floatval($row0["resultado"]);
                }
            }

            //Obtenemos totales por movimiento
            foreach($tipos_gastos as $tip){
                foreach($medios as $med){
                    $query_tmp = "select sum(monto) as resultado from movimiento_dinero where tipo_origen = 'GAS' AND id_origen = '".$tip["id"]."' AND moneda = '".$med["id_moneda"]."' AND id_medio = '".$med["id_medio"]."' AND fecha_hora BETWEEN '".$tiempo_inicio."' AND '".$tiempo_fin."' AND estado = 1";
                    $result_tmp = $db->executeQuery($query_tmp);
                    if ($row0 = $db->fecth_array($result_tmp)) {
                        $corte["tip_".$tip["id"]."_".$med["id_medio"]."_".$med["id_moneda"]] = floatval($row0["resultado"]);
                    }
                }
            }
        }else{
            //Obtenemos totales desde tabla movimiento dinero
            foreach($medios as $med){
                $query_tmp = "select sum(monto) as resultado from movimiento_dinero where id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND fecha_hora >= '".$tiempo_inicio."' AND estado = 1";
                $result_tmp = $db->executeQuery($query_tmp);
                if ($row0 = $db->fecth_array($result_tmp)) {
                    $corte["tot_".$med["id_medio"]."_".$med["id_moneda"]] = floatval($row0["resultado"]);
                }
            }

            $query_tmp = "select sum(monto) as resultado from movimiento_dinero where id_medio = '0' AND comentario = 'DETRACCION' AND moneda = '1' AND fecha_hora >= '".$tiempo_inicio."' AND estado = 1";
            $result_tmp = $db->executeQuery($query_tmp);
            if ($row0 = $db->fecth_array($result_tmp)) {
                $corte["tot_detraccion"] = floatval($row0["resultado"]);
            }

            //Obtenemos totales en propinas (y los sumamos a los otros totales)
            foreach($medios as $med){
                $query_tmp = "select sum(monto) as resultado from pedido_propina where id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND fecha_hora >= '".$tiempo_inicio."' ";
                $result_tmp = $db->executeQuery($query_tmp);
                if ($row0 = $db->fecth_array($result_tmp)) {
                    $corte["prop_".$med["id_medio"]."_".$med["id_moneda"]] = floatval($row0["resultado"]);
                    $corte["tot_".$med["id_medio"]."_".$med["id_moneda"]] = $corte["tot_".$med["id_medio"]."_".$med["id_moneda"]] + floatval($row0["resultado"]);
                }
            }

            //Obtenemos totales de ventas desde tabla movimiento dinero 
            foreach($medios as $med){
                $query_tmp = "select sum(monto) as resultado from movimiento_dinero where id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND tipo_origen = 'PED' AND fecha_hora >= '".$tiempo_inicio."' AND estado = 1";
                $result_tmp = $db->executeQuery($query_tmp);
                if ($row0 = $db->fecth_array($result_tmp)) {
                    $corte["ven_".$med["id_medio"]."_".$med["id_moneda"]] = floatval($row0["resultado"]);
                }
            }

            //Obtenemos totales de ingresos adicionales desde tabla movimiento dinero 
            foreach($medios as $med){
                $query_tmp = "select sum(monto) as resultado from movimiento_dinero where id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND tipo_origen = 'GAS' AND id_aux = 1 AND fecha_hora >= '".$tiempo_inicio."' AND estado = 1";
                $result_tmp = $db->executeQuery($query_tmp);
                if ($row0 = $db->fecth_array($result_tmp)) {
                    $corte["ing_".$med["id_medio"]."_".$med["id_moneda"]] = floatval($row0["resultado"]);
                }
            }

            //Obtenemos totales de salidas desde tabla movimiento dinero 
            foreach($medios as $med){
                $query_tmp = "select sum(monto) as resultado from movimiento_dinero where id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND tipo_origen = 'GAS' AND id_aux = 0 AND fecha_hora >= '".$tiempo_inicio."' AND estado = 1";
                $result_tmp = $db->executeQuery($query_tmp);
                if ($row0 = $db->fecth_array($result_tmp)) {
                    $corte["sal_".$med["id_medio"]."_".$med["id_moneda"]] = floatval($row0["resultado"]);
                }
            }

            //Obtenemos totales por movimiento
            foreach($tipos_gastos as $tip){
                foreach($medios as $med){
                    $query_tmp = "select sum(monto) as resultado from movimiento_dinero where tipo_origen = 'GAS' AND id_origen = '".$tip["id"]."' AND moneda = '".$med["id_moneda"]."' AND id_medio = '".$med["id_medio"]."' AND fecha_hora >= '".$tiempo_inicio."' AND estado = 1";
                    $result_tmp = $db->executeQuery($query_tmp);
                    if ($row0 = $db->fecth_array($result_tmp)) {
                        $corte["tip_".$tip["id"]."_".$med["id_medio"]."_".$med["id_moneda"]] = floatval($row0["resultado"]);
                    }
                }
            }
        }

        //Obtenemos Montos Iniciales
        foreach($monedas as $mo){
            $query_inicial = "Select sum(monto) as resultado from movimiento_dinero where moneda = '".$mo["id"]."' AND tipo_origen = 'CUT' AND fecha_cierre = '".$fecha."'";
            $result_inicial = $db->executeQuery($query_inicial);
            if ($row0 = $db->fecth_array($result_inicial)) {
                $corte["ini_".$mo["id"]] = floatval($row0["resultado"]);
            }else{
                $corte["ini_".$mo["id"]] = 0;
            }
        }

        //Obtenemos Platos vendidos
        $tiempo_fin___ = $tiempo_fin ? $tiempo_fin : date('Y-m-d H:i:s');
        $query_platos = "Select pl.descripcion, sum(dp.cantidad) as total, sum(if(p.estado != 0, dp.cantidad, 0)) as salidas, sum(if(p.estado = 0, dp.cantidad, 0)) as cobrar from detallepedido dp, pedido p, plato pl where dp.pkPlato = pl.pkPlato and p.pkPediido = dp.pkPediido and p.estado != 3 AND dp.horaPedido BETWEEN '$tiempo_inicio' and '$tiempo_fin___' AND dp.estado > 0 AND dp.estado < 3 GROUP BY dp.pkPlato ORDER BY salidas DESC";
        // $query_platos = "Select pl.descripcion, sum(dp.cantidad) as salidas from detallepedido dp, pedido p, plato pl where dp.pkPlato = pl.pkPlato and p.pkPediido = dp.pkPediido and p.estado != 3 AND date(dp.horaPedido) = '$fecha' AND dp.estado > 0 AND dp.estado < 3 GROUP BY dp.pkPlato ORDER BY salidas DESC";
        // echo $query_platos;
        $result_platos = $db->executeQuery($query_platos);
        $r_platos = array();
        while($row = $db->fecth_array($result_platos)){
            $t_plato = array();
            $t_plato["descripcion"] = $row["descripcion"];
            $t_plato["salidas"] = $row["salidas"];
            $t_plato["cobrar"] = $row["cobrar"];
            $t_plato["total"] = $row["total"];
            $r_platos[] = $t_plato;
        }
        $corte["platos"] = $r_platos;

        //Obtenemos Movimientos con tarjeta
        $query_movimientos = "";
        if($tiempo_fin <> null){
            $query_movimientos = "Select md.*, mp.nombre as mp_nombre, m.simbolo as m_simbolo from movimiento_dinero md, medio_pago mp, moneda m WHERE md.id_medio = mp.id AND md.moneda = m.id AND md.fecha_hora BETWEEN '".$tiempo_inicio."' AND '".$tiempo_fin."' AND md.id_medio > 1";
        }else{
            $query_movimientos = "Select md.*, mp.nombre as mp_nombre, m.simbolo as m_simbolo from movimiento_dinero md, medio_pago mp, moneda m WHERE md.id_medio = mp.id AND md.moneda = m.id AND md.fecha_hora >= '".$tiempo_inicio."' AND md.id_medio > 1";
        }
        $resultado_movimientos = $db->executeQuery($query_movimientos);
        $r_movs = array();
        while($row = $db->fecth_array($resultado_movimientos)){
            $t_mov = array();
            $t_mov["id"] = $row["id_origen"];
            $t_mov["op"] = $row["comentario"];
            $t_mov["medio"] = $row["mp_nombre"];
            $t_mov["moneda"] = $row["m_simbolo"];
            $t_mov["total"] = $row["monto"];
            $r_movs[] = $t_mov;
        }
        $corte["movimientos"] = $r_movs;

        //get ventas por consumo y credito
        $query = "SELECT
            pkPediido AS id,
            documento AS cliente,
            IF (
                estado = 4,
                'CREDITO',
                'CONSUMO'
            ) AS tipo,
            total
        FROM
            pedido
        WHERE
            estado IN (4, 5) and fechaCierre = '$fecha'";

        $res = $db->executeQueryEx($query);

        $r_cons_cred = [];

        while ($row = $db->fecth_array($res)) {
            $row['total'] = number_format($row['total'], 2);
            $r_cons_cred[] = $row;
        }

        $corte["consumo_credito"] = $r_cons_cred;

        return $corte;
    }

}
