<?php
error_reporting("E_ALL");
include_once('recursos/componentes/MasterConexion.php');
$conn = new MasterConexion();
echo "Procesando...";
//Migramos informacion ventas
$query_ventas = "Select p.*, ac.caja from pedido p, accion_caja ac where ac.pk_accion = p.pkPediido AND tipo_accion = 'PED' AND p.estado = 1 order by p.pkPediido ASC";
$ventas = $conn->consulta_matriz($query_ventas);
if(is_array($ventas)){
    foreach($ventas as $ven){
        //Primero detectamos el tipo de pago
        if(intval($ven["tipo_pago"]) === 1){
            //Si es efectivo insertamos directo
            $query_movimiento = "Insert into movimiento_dinero values(NULL,'".$ven["pkPediido"]."','PED','".$ven["total_efectivo"]."','1','1','".$ven["fechaCierre"]."','".$ven["fechaFin"]."','".$ven["idUser"]."','".$ven["caja"]."',NULL,NULL,'1')";
            $conn->consulta_simple($query_movimiento);

            $query_update_medio = "Update pedido set nombreTarjeta = 'EFECTIVO' where pkPediido = '".$ven["pkPediido"]."'";
            $conn->consulta_simple($query_update_medio);
        }else{
            //Si es tarjeta insertamos segun la tarjeta
            $string_medio = "";
            if($ven["nombreTarjeta"] == "VISA"){
                $query_movimiento = "Insert into movimiento_dinero values(NULL,'".$ven["pkPediido"]."','PED','".$ven["total_tarjeta"]."','2','1','".$ven["fechaCierre"]."','".$ven["fechaFin"]."','".$ven["idUser"]."','".$ven["caja"]."',NULL,NULL,'1')";
                $conn->consulta_simple($query_movimiento);
                $string_medio = $string_medio."VISA";
            }else{
                $query_movimiento = "Insert into movimiento_dinero values(NULL,'".$ven["pkPediido"]."','PED','".$ven["total_tarjeta"]."','3','1','".$ven["fechaCierre"]."','".$ven["fechaFin"]."','".$ven["idUser"]."','".$ven["caja"]."',NULL,NULL,'1')";
                $conn->consulta_simple($query_movimiento);
                $string_medio = $string_medio."MASTERCARD";
            }

            //Y ademas verificamos si se pago una parte con efectivo
            if(floatval($ven["total_efectivo"])>0){
                $query_movimiento = "Insert into movimiento_dinero values(NULL,'".$ven["pkPediido"]."','PED','".$ven["total_efectivo"]."','1','1','".$ven["fechaCierre"]."','".$ven["fechaFin"]."','".$ven["idUser"]."','".$ven["caja"]."',NULL,NULL,'1')";
                $conn->consulta_simple($query_movimiento);
                $string_medio = $string_medio."|EFECTIVO";
            }

            $query_update_medio = "Update pedido set nombreTarjeta = '".$string_medio."' where pkPediido = '".$ven["pkPediido"]."'";
            $conn->consulta_simple($query_update_medio);
        }
        
    }
}

//Migramos informacion comprobantes
$query_comprobantes = "Select * from comprobante";
$comprobantes = $conn->consulta_matriz($query_comprobantes);
if(is_array($comprobantes)){
    foreach($comprobantes as $cp){
        $query_impuesto = "Insert into comprobante_impuestos values(NULL,'".$cp["pkComprobante"]."','".$cp["subTotal"]."','".$cp["impuesto"]."','0','0','".$cp["descuento"]."','0','0','".$cp["total"]."')";
        $conn->consulta_simple($query_impuesto);
    }
}

//Agregamos inseguridad
$conn->consulta_simple("SET SQL_SAFE_UPDATES = 0");
//Actualizamos medio de pago de comprobantes
$conn->consulta_simple("Update comprobante set nombreTarjeta = 'EFECTIVO' WHERE nombreTarjeta = '-----------'");

//Migramos informacion gastos
$query_pagos = "Select g.*, ac.caja from gastos_diarios g, accion_caja ac where ac.pk_accion = g.pkGastosDiarios AND tipo_accion = 'GAS' AND estado_anular = 0 order by g.pkGastosDiarios ASC";
$pagos = $conn->consulta_matriz($query_pagos);
if(is_array($pagos)){
    foreach($pagos as $pg){
        //Estados antiguos
        //Estado 1 ingreso
        //Estado 0 Pago Diario
        //Estado 2 Pago Planilla
        //Estado 3 Pago Fijo
        //Estado 4 Devolucion
        switch(intval($pg["estado"])){
            case 0:
                $query_movimiento = "Insert into movimiento_dinero values(NULL,'2','GAS','-".$pg["cantidad"]."','1','1','".$pg["fecha"]."','".$pg["dateModify"]."','".$pg["pkUser"]."','".$pg["caja"]."','".$pg["descripcion"]."','0','1')";
                $conn->consulta_simple($query_movimiento);
            break;

            case 1:
                $query_movimiento = "Insert into movimiento_dinero values(NULL,'1','GAS','".$pg["cantidad"]."','1','1','".$pg["fecha"]."','".$pg["dateModify"]."','".$pg["pkUser"]."','".$pg["caja"]."','".$pg["descripcion"]."','1','1')";
                $conn->consulta_simple($query_movimiento);
            break;

            case 2:
                $query_movimiento = "Insert into movimiento_dinero values(NULL,'4','GAS','-".$pg["cantidad"]."','1','1','".$pg["fecha"]."','".$pg["dateModify"]."','".$pg["pkUser"]."','".$pg["caja"]."','".$pg["descripcion"]."','0','1')";
                $conn->consulta_simple($query_movimiento);
            break;

            case 3:
                $query_movimiento = "Insert into movimiento_dinero values(NULL,'3','GAS','-".$pg["cantidad"]."','1','1','".$pg["fecha"]."','".$pg["dateModify"]."','".$pg["pkUser"]."','".$pg["caja"]."','".$pg["descripcion"]."','0','1')";
                $conn->consulta_simple($query_movimiento);
            break;

            case 4:
                $query_movimiento = "Insert into movimiento_dinero values(NULL,'5','GAS','-".$pg["cantidad"]."','1','1','".$pg["fecha"]."','".$pg["dateModify"]."','".$pg["pkUser"]."','".$pg["caja"]."','".$pg["descripcion"]."','0','1')";
                $conn->consulta_simple($query_movimiento);
            break;
        }
    }
}

//Montos iniciales
$query_cortes = "Select ac.caja, c.* from corte c, accion_caja ac where c.id = ac.pk_accion AND ac.tipo_accion = 'CUT' order by c.id ASC";
$cortes = $conn->consulta_matriz($query_cortes);
if(is_array($cortes)){
    foreach($cortes as $co){
        $query_movimiento = "Insert into movimiento_dinero values(NULL,'".$co["id"]."','CUT','".$co["monto_inicial"]."','1','1','".$co["fecha_cierre"]."','".$co["inicio"]."','1','".$co["caja"]."',NULL,NULL,'1')";
        $conn->consulta_simple($query_movimiento);
    }
}

//Limpiamos tablas
$conn->consulta_simple("TRUNCATE TABLE creditos");
$conn->consulta_simple("TRUNCATE TABLE pedido_cliente");
$conn->consulta_simple("TRUNCATE TABLE delivery_cliente");
$conn->consulta_simple("TRUNCATE TABLE cliente_externo");
$conn->consulta_simple("TRUNCATE TABLE submodule");

//Insertamos Monedas
$conn->consulta_simple("Insert into moneda values('1','SOL PERUANO','S/','2')");
//Insertamos Medios de Pago
$conn->consulta_simple("Insert into medio_pago values('1','EFECTIVO','1','','1')");
$conn->consulta_simple("Insert into medio_pago values('2','VISA','1','','1')");
$conn->consulta_simple("Insert into medio_pago values('3','MASTERCARD','1','','1')");
//Insertamos tipos de gastos
$conn->consulta_simple("INSERT INTO tipo_gasto VALUES (1, 'Ingreso Adicional', 1, 1),(2, 'Pago Diario', 0, 1),(3, 'Pago Fijo', 0, 1),(4, 'Pago Planilla', 0, 1),(5, 'Devolucion', 0, 1);");
//Insertamos tipos de impuestos
$conn->consulta_simple("INSERT INTO tipo_impuesto VALUES (1, 'GRAVADA'),(2, 'INAFECTA'),(3, 'EXONERADA'),(4, 'GRATUITA'),(5, 'ICBPER');");
//Insertamos ICBPER
$conn->consulta_simple("Insert into cloud_config values(NULL,'icbper','0.10')");
//Insertamos Submodulos Actualizados
$conn->consulta_simple("INSERT INTO submodule VALUES (3, 1, 'Administrar Receta', 0, 'AdmInsumo'),(4, 1, 'Administrar Insumo', 0, 'AddInsumo'),(14, 2, 'Administrar Platos', 0, 'AdminPlato'),(15, 2, 'Administrar Boleta', 0, 'ShowBoleta'),(16, 2, 'Administrar Factura', 0, 'ShowFactura'),(20, 3, 'Administrar Personal', 0, 'Personal'),(22, 10, 'Total Ventas por Mes', 0, 'Tmes'),(23, 10, 'Platos con mayor rotacion', 0, 'SProductosDia'),(30, 11, 'Cierre Diario', 0, 'ShowCierreDiario'),(31, 11, 'Reporte Pagos Diarios', 1, 'ShowReportePagosDiarios'),(32, 11, 'Reporte Pagos Fijos', 1, 'ShowReportePagosFijos'),(33, 11, 'Reporte Pagos Planillas', 1, 'PagosPlanillas'),(34, 11, 'Monto Inicial', 0, 'MontoInicial'),(36, 12, 'Empresa', 0, 'Emp'),(42, 1, 'Administrar Proveedores', 0, 'ShowAdminProveedor'),(46, 2, 'Cuentas Pendientes', 0, 'CPendientes'),(47, 10, 'Consolidado de Ventas', 0, 'Consolidado'),(52, 12, 'Administrar Salones', 0, 'AdmSalon'),(53, 10, 'Ventas por Trabajador', 0, 'TVentasMozo'),(55, 2, 'Administrar Tipos', 0, 'AdminTipos'),(56, 12, 'Administrar Mensajes Pedido', 0, 'ShowAdminMensajes'),(57, 12, 'Administrar Mesas', 0, 'ShowMesas'),(59, 10, 'Kardex (Resumen)', 0, 'showKardex'),(62, 1, 'Administrar guias de ingreso', 0, 'AdminGuias'),(63, 10, 'Reporte Ventas (por tipo)', 0, 'SaleConsumo'),(64, 10, 'Kardex (Seguimiento Diario)', 0, 'showKardexDetallado'),(65, 1, 'Administrar Guias de Salida', 0, 'AdminGuiaSalida'),(66, 1, 'Administrar Unidades', 0, 'AdminUnidades'),(67, 1, 'Administrar Tipos de Insumo', 0, 'AdminTipoInsumo'),(68, 10, 'Kardex (Seguimiento Detallado)', 0, 'showKardexDetallado2'),(69, 11, 'Registrar Ingreso o Salida de dinero', 0, 'RegistrarPago'),(70, 11, 'Reporte Ingresos y Salidas', 0, 'IngresosSalidas'),(71, 11, 'Reporte Ingresos VS Egresos', 0, 'IngresosEgresos'),(72, 10, 'Consolidado Stocks', 0, 'Stocks'),(73, 2, 'Stock Manual Platos', 0, 'SManual'),(75, 11, 'Reporte Medios de Pago', 0, 'RTarjeta'),(77, 10, 'Consumo Por Persona', 0, 'showConsumoxPersona'),(78, 10, 'Pedidos Anulados', 0, 'showPedidosAnulados'),(79, 11, 'Ventas por tipo', 0, 'SaleCaja'),(80, 2, 'Tipos de Menu', 0, 'TipoMenu'),(81, 2, 'Estructura de Menu (Entrada, fondo, postre)', 0, 'TipoComponenteMenu'),(82, 2, 'Arma tu Menu', 0, 'ComponenteMenu'),(83, 12, 'Configuracion Impresion', 0, 'CImpresion'),(85, 11, 'Reporte Pagos Anulados', 1, 'PagosAnulados'),(86, 2, 'Platos Amarrados', 0, 'PlatosAmarrados'),(87, 12, 'Configuracion PSE', 0, 'CFacturacion'),(89, 10, 'Consolidado Maestro', 0, 'CajaFinal'),(90, 2, 'Facturacion Personalizada', 0, 'FacturacionPersonalizada'),(91, 2, 'Reporte Consumos', 0, 'CConsumo'),(92, 12, 'Monedas y Medios de Pago', 0, 'CMonedaPago'),(93, 12, 'Descuentos y Convenios', 0, 'CDescuento'),(94, 2, 'Impresion por Pantalla', 0, 'IPantalla'),(95, 12, 'Tipo de Movimientos', 0, 'TipoGasto'),(96, 11, 'Propinas', 0, 'Propinas');");

echo "...Listo!";