<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Models_GastosDiariosModel {

    public function _listGastosDiariosReporte($nameCaja,$fechaInicio,$fechaFin){

        $db = new SuperDataBase();
        $query = "SELECT g.*, CONCAT(t.nombres, ' ', t.apellidos) as trabajador,(CASE WHEN g.estado = 0 THEN 'PAGO DIARIO' WHEN g.estado = 2 THEN 'PAGO PLANILLA' WHEN g.estado = 3 THEN 'PAGO FIJO' ELSE '' END) as tipo 
        FROM gastos_diarios g inner join trabajador t on g.pkUser = t.pkTrabajador inner join accion_caja a on g.pkGastosDiarios = a.pk_accion where (g.estado = 0 or g.estado = 2 or g.estado = 3 )and a.tipo_accion = 'GAS' and a.caja = '{$nameCaja}' and g.fecha BETWEEN '".$fechaInicio."' and '".$fechaFin."'";
        
        $result = $db->executeQuery($query);
        $array = array();
        
        while($reg = $result->fetch_object()){
			$array[] = $reg;
        }
        
		return $array;

    }

    public function RegistrarMontoInicial() {
        $db = new SuperDataBase();
        $user = UserLogin::get_id();

        //Primero Obtenemos corte activo de la caja
        $query_corte = "Select c.* from corte c, accion_caja ac where c.fin is null AND ac.pk_accion = c.id AND ac.tipo_accion = 'CUT' AND ac.caja = '".$_COOKIE["c"]."' order by id DESC LIMIT 1";
        
        $fecha_cierre = "";
        
        $result_corte = $db->executeQuery($query_corte);
        if ($row = $db->fecth_array($result_corte)) {
            //Ahora buscamos iniciales de ese corte
            $fecha_cierre = $row["fecha_cierre"];
            $query_iniciales = "Select * from movimiento_dinero where id_origen = '".$row["id"]."' AND tipo_origen = 'CUT' AND caja = '".$_COOKIE["c"]."'";
            $result_iniciales = $db->executeQuery($query_iniciales);
            if($row1 = $db->fecth_array($result_iniciales)){
                //Si ya existen iniciales
                //Agregamos inseguridad
                $db->executeQuery("SET SQL_SAFE_UPDATES = 0");
                $query_monedas = "Select * from moneda where estado > 0";
                $result_monedas = $db->executeQuery($query_monedas);
                while($row2 = $db->fecth_array($result_monedas)){
                    $query_update = "Update movimiento_dinero set monto = '".$_REQUEST["inicial_".$row2["id"]]."' where moneda = '".$row2["id"]."' AND tipo_origen = 'CUT' AND id_origen = '".$row["id"]."' AND caja = '".$_COOKIE["c"]."'";
                    $db->executeQuery($query_update);
                }
            }else{
                //Si aun no existen iniciales
                $query_monedas = "Select * from moneda where estado > 0";
                $result_monedas = $db->executeQuery($query_monedas);
                while($row2 = $db->fecth_array($result_monedas)){
                    $query_insert = "Insert into movimiento_dinero values(NULL,'".$row["id"]."','CUT','".$_REQUEST["inicial_".$row2["id"]]."',1,'".$row2["id"]."','".$fecha_cierre."',now(),'".$user."','".$_COOKIE["c"]."',NULL,NULL,1)";
                    $db->executeQuery($query_insert);
                }
            }
        }
        
        //Agregamos inseguridad
        $db->executeQuery("SET SQL_SAFE_UPDATES = 0");
        //Finalmente actualizamos los tipos de cambio
        //Obtenemos tipos de cambio para monedas registradas
        $query_monedas = "Select * from moneda where estado > 0 AND id >1";
        $result_monedas = $db->executeQuery($query_monedas);
        while($row = $db->fecth_array($result_monedas)){
            $query_cambio = "Select * from tipo_cambio where moneda = '".$row["id"]."' AND fecha_cierre = '".$fecha_cierre."'";
            $result_cambio = $db->executeQuery($query_cambio);
            if($row_0 = $db->fecth_array($result_cambio)){
                //Actualizamos
                $query_up = "Update tipo_cambio set cambio = '".$_REQUEST["moneda_".$row["id"]]."' where moneda = '".$row["id"]."' AND fecha_cierre = '".$fecha_cierre."'";
                $db->executeQuery($query_up);
            }else{
                //Insertamos
                $query_in = "Insert into tipo_cambio values(NULL,'".$_REQUEST["moneda_".$row["id"]]."','".$fecha_cierre."','".$row["id"]."')";
                $db->executeQuery($query_in);
            }
        }

        echo "Usqay te ama";

    }

    public function RegistrarGastoDiario($fecha_cierre, $monto, $tipo_movimiento, $medio_moneda, $comentario) {
        $db = new SuperDataBase();
        $user = UserLogin::get_id();
        //Procesamos informacion
        $array_tipo = explode("_",$tipo_movimiento);
        $array_medio = explode("_",$medio_moneda);
        $id_tipo = $array_tipo[0];
        $direccion = $array_tipo[1];
        $medio = $array_medio[0];
        $moneda = $array_medio[1];

        //Guardamos como positivo o negativo segun direccion
        if(intval($direccion) === 0){
            $monto = floatval($monto)*-1;
        }else{
            $monto = abs(floatval($monto));
        }

        $caja = $_REQUEST['caja'];
        $tabla = '';

        if ($caja == 'FE') {
            $tabla = 'movimiento_dinero_fe';
            $query = "insert into movimiento_dinero_fe values(NULL,'".$id_tipo."','GAS','".$monto."','".$medio."','".$moneda."','".$fecha_cierre."',now(),'".$user."','".$caja."','".$comentario."','".$direccion."',1)";
        } else {
            $tabla = 'movimiento_dinero';
            $query = "insert into movimiento_dinero values(NULL,'".$id_tipo."','GAS','".$monto."','".$medio."','".$moneda."','".$fecha_cierre."',now(),'".$user."','".$caja."','".$comentario."','".$direccion."',1)";
        }
        
        //Insertamos en base de datos
        $db->executeQuery($query);
        $aidi = $db->getId();

        $datos_movimiento = $db->executeQuery("Select tg.nombre as tipo, me.nombre as medio, mo.simbolo as moneda, md.monto, md.comentario, md.caja from $tabla md, tipo_gasto tg, medio_pago me, moneda mo where md.id = '".$aidi."' AND md.id_origen = tg.id AND md.id_medio = me.id AND md.moneda = mo.id");
        if($rd = $db->fecth_array($datos_movimiento)) {
            $this->envio_registro($aidi,$rd["tipo"],$rd["medio"],$rd["moneda"],$rd["monto"],$rd["comentario"],$rd["caja"]);
        }

        return $aidi;
    }

    public function envio_registro($codigo,$tipo,$medio,$moneda,$monto,$comentario,$caja) {
        error_reporting(E_ALL);
        $user = new UserLogin();
        $ruta = realpath('');
        $pr = explode("htdocs",$ruta);
        $comando = $pr[0]."php\\php.exe ".$ruta."\\reportes\\envio_registro_pago.php ".escapeshellarg($codigo)." ".escapeshellarg($tipo)." ".escapeshellarg($medio)." ".escapeshellarg($moneda)." ".escapeshellarg($monto)." ".escapeshellarg($comentario)." ".escapeshellarg($caja)." ".escapeshellarg($user->get_names()." ".$user->get_lastnames());
        //echo $comando;
        $this->execInBackground($comando);
    }

    public function _TotalDia($fecha) {

        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $user = UserLogin::get_id();
        $query = "select (SELECT sum(total) FROM pedido p inner join
  (mesas m  inner join salon s on s.pkSalon=m.pkSalon)on p.pkMesa=m.pkMesa where fechaCierre='$fecha' and pkSucursal='$sucursal') as total_vendido,
 (select sum(cantidad ) from gastos_diarios where fecha='$fecha'  and pkSucursal='$sucursal'  ) as total_gastado, (SELECT cantidad FROM monto_inicial m where fecha='$fecha' and pkSucursal='$sucursal' ) as inicial;
";
//        echo $query;
        $result = $db->executeQuery($query);
        $array = array();
        while ($row = $db->fecth_array($result)) {
            $array[] = array(
                "vendido" => (float) $row['total_vendido'],
                "gastado" => (float) $row['total_gastado'],
                "montoInicial" => (float) $row['inicial'],
                "total" => ( $row['total_vendido'] + $row['inicial'] - $row['total_gastado'] )
            );
        }
        echo json_encode($array);
    }

    public function _ListMontoInicial() {

        $db = new SuperDataBase();
        $resultado = array();
        $fecha_cierre = "";
        $id_corte = null;

        //Obtenemos corte actual
        $query = "Select c.* from corte c, accion_caja ac where c.fin is null AND ac.pk_accion = c.id AND ac.tipo_accion = 'CUT' AND ac.caja = '".$_COOKIE["c"]."' order by id DESC LIMIT 1";
        $result = $db->executeQuery($query);
        if ($row = $db->fecth_array($result)) {
            $fecha_cierre = $row["fecha_cierre"];
            $id_corte = $row["id"];
        }

        //Obtenemos montos iniciales
        $query_monedas = "Select * from moneda where estado > 0";
        $result_monedas = $db->executeQuery($query_monedas);
        while($row = $db->fecth_array($result_monedas)){
            $query_inicial = "Select * from movimiento_dinero where moneda = '".$row["id"]."' AND fecha_cierre = '".$fecha_cierre."' AND id_origen = '".$id_corte."' AND tipo_origen = 'CUT' AND caja = '".$_COOKIE["c"]."'";
            $result_inicial = $db->executeQuery($query_inicial);
            if($row_0 = $db->fecth_array($result_inicial)){
                $resultado["ini_".$row["id"]] = $row_0["monto"];
            }else{
                $resultado["ini_".$row["id"]] = 0; 
            }
        }
        
        //Obtenemos tipos de cambio para monedas registradas
        $query_monedas = "Select * from moneda where estado > 0 AND id >1";
        $result_monedas = $db->executeQuery($query_monedas);
        while($row = $db->fecth_array($result_monedas)){
            $query_cambio = "Select * from tipo_cambio where moneda = '".$row["id"]."' AND fecha_cierre = '".$fecha_cierre."'";
            $result_cambio = $db->executeQuery($query_cambio);
            if($row_0 = $db->fecth_array($result_cambio)){
                $resultado["mon_".$row["id"]] = $row_0["cambio"];
            }else{
                $resultado["mon_".$row["id"]] = 1; 
            }
        }

        echo json_encode($resultado);
    }

    public function _verificandoMesasAbiertas() {

        $db = new SuperDataBase();
        $sucursal = UserLogin::get_pkSucursal();
        $user = UserLogin::get_id();
        $query = "CAll sp_verificarVentas('$sucursal');
";
//        echo $query;
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

    public function _EditarGastoDiarios($cantidad, $descripcion, $codigo) {
        $db = new SuperDataBase();
        $query = "update gastos_diarios set cantidad=$cantidad, descripcion='$descripcion', dateModify=now() where pkGastosDiarios=$codigo and estado_anular=0";
        $db->executeQuery($query);
    }

    public function AnularGastosDiarios($codigo) {
        $user = UserLogin::get_id();
        $db = new SuperDataBase();

        $caja = $_REQUEST['caja'];

        if ($caja == 'FE') {
            $tabla = 'movimiento_dinero_fe';
        } else {
            $tabla = 'movimiento_dinero';
        }

        $datos_movimiento = $db->executeQuery("Select tg.nombre as tipo, me.nombre as medio, mo.simbolo as moneda, md.monto, md.comentario, md.caja from $tabla md, tipo_gasto tg, medio_pago me, moneda mo where md.id = '".$codigo."' AND md.id_origen = tg.id AND md.id_medio = me.id AND md.moneda = mo.id");
        if($rd = $db->fecth_array($datos_movimiento)) {
            $this->correo_anulacion($codigo,$rd["tipo"],$rd["medio"],$rd["moneda"],$rd["monto"],$rd["comentario"],$rd["caja"]);
        }
        $query = "update $tabla set estado=0, fecha_hora=now(), id_usuario = '".$user."' where id='".$codigo."'";
        $db->executeQuery($query);
        echo '1';
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
    public function correo_anulacion($codigo,$tipo,$medio,$moneda,$monto,$comentario,$caja){
        error_reporting(E_ALL);
        $user = new UserLogin();
        $ruta = realpath('');
        $pr = explode("htdocs",$ruta);
        $comando = $pr[0]."php\\php.exe ".$ruta."\\reportes\\envio_anulacion_pago.php ".escapeshellarg($codigo)." ".escapeshellarg($tipo)." ".escapeshellarg($medio)." ".escapeshellarg($moneda)." ".escapeshellarg($monto)." ".escapeshellarg($comentario)." ".escapeshellarg($caja)." ".escapeshellarg($user->get_names()." ".$user->get_lastnames());
        //echo $comando;
        $this->execInBackground($comando);
    }

    public function _EditarGastosFijos($cantidad, $descripcion, $codigo) {
        $db = new SuperDataBase();
        $query = "update gastos_diarios set cantidad=$cantidad, descripcion='$descripcion', dateModify=now() where pkGastosDiarios=$codigo and estado_anular=0";
//        echo die($query);
        $db->executeQuery($query);
    }

    public function AnularGastosFijos($codigo) {
        $db = new SuperDataBase();
        $query = "update gastos_diarios set estado_anular=1, dateModify=now() where pkGastosDiarios=$codigo";
        $db->executeQuery($query);
    }

    public function _EditarGastosPlanilla($cantidad, $descripcion, $codigo) {
        $db = new SuperDataBase();
        $query = "update gastos_diarios set cantidad=$cantidad, descripcion='$descripcion', dateModify=now() where pkGastosDiarios=$codigo and estado_anular=0";
        $db->executeQuery($query);
    }

    public function AnularGastosPlanilla($codigo) {
        $db = new SuperDataBase();
        $query = "update gastos_diarios set estado_anular=1, dateModify=now() where pkGastosDiarios=$codigo";
        $db->executeQuery($query);
    }

    public function ActivarPago($codigo) {
        $user = UserLogin::get_id();
        $db = new SuperDataBase();
        $query = "update movimiento_dinero set estado=1, fecha_hora=now(), id_usuario = '".$user."' where id='".$codigo."'";
        $db->executeQuery($query);
        echo '1';
    }
    
    public function imprimePago($pkPago,$term,$aux) {
        $db = new SuperDataBase();
        $query = "Insert into cola_impresion values(NULL,'".$pkPago."','PAG','".$term."','".$aux."',0)";
        $db->executeQuery($query);
        echo '1';
    }

}
