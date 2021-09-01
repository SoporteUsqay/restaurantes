<?php
$titulo_pagina = 'Administracion de Sistema';
$titulo_sistema = 'Usqay2';

include_once('recursos/componentes/MasterConexion.php');
$conn = new MasterConexion();

//Validamos si se realizo un proceso
//Estado 0 no se hizo nada
//Estado 1 si
//Estado 2 error en clave
$proceso = 0;
$adelante = 0;
if(isset($_POST["pass"])){
    $res = $conn->consulta_arreglo("Select * from trabajador where pkTrabajador = 1 AND password = '".md5($_POST["pass"])."';");
    if(is_array($res)){
        $adelante = 1;
    }else{
        $proceso = 2;
    }
}

if($adelante === 1){
    //Limpieza de cajas
    if(isset($_POST["caj"])){
        $proceso = 1;
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 0;");
        $conn->consulta_simple("TRUNCATE cajas;");
        $conn->consulta_simple("TRUNCATE accion_caja;");
        $conn->consulta_simple("Insert into cajas values(1,'01');");
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 1;");
    }

    //Limpieza de comprobantes
    if(isset($_POST["cmp"])){
        $proceso = 1;
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 0;");
        $conn->consulta_simple("TRUNCATE comprobante;");
        $conn->consulta_simple("TRUNCATE comprobante_hash;");
        $conn->consulta_simple("TRUNCATE comprobante_impuestos;");
        $conn->consulta_simple("TRUNCATE detallecomprobante;");
        $conn->consulta_simple("TRUNCATE detalle_comprobante2;");
        $conn->consulta_simple("TRUNCATE detalle_comprobante_directo;");
        $conn->consulta_simple("TRUNCATE cambio_facturacion;");
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 1;");
    }

    //Limpieza de Ventas
    if(isset($_POST["ven"])){
        $proceso = 1;
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 0;");
        $conn->consulta_simple("TRUNCATE cambio_de_precio;");
        $conn->consulta_simple("TRUNCATE corte;");
        $conn->consulta_simple("TRUNCATE creditos;");
        $conn->consulta_simple("TRUNCATE detallepedido;");
        $conn->consulta_simple("TRUNCATE monto_inicial;");
        $conn->consulta_simple("DELETE FROM movimiento_dinero where tipo_origen = 'PED';");
        $conn->consulta_simple("DELETE FROM movimiento_dinero where tipo_origen = 'CUT';");
        $conn->consulta_simple("TRUNCATE pedido;");
        $conn->consulta_simple("TRUNCATE pedido_cliente;");
        $conn->consulta_simple("TRUNCATE pedido_descuento;");
        $conn->consulta_simple("TRUNCATE pedido_efectivo;");
        $conn->consulta_simple("TRUNCATE pedido_propina;");
        $conn->consulta_simple("TRUNCATE TABLE delivery_cliente");
        $conn->consulta_simple("TRUNCATE TABLE cliente_externo");
        $conn->consulta_simple("Insert into corte values(NULL,CURDATE()-1,now()-1,NULL,0,1);");
        $conn->consulta_simple("Update cierrediario set fecha = CURDATE()-1 where pkCierreDiario = 1;");
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 1;");
    }

    //Limpieza de Kardex
    if(isset($_POST["kar"])){
        $proceso = 1;
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 0;");
        // $conn->consulta_simple("TRUNCATE comprobante_ingreso;");
        // $conn->consulta_simple("TRUNCATE historial_stock_insumos;");
        // $conn->consulta_simple("TRUNCATE ingresoinsumos;");
        // $conn->consulta_simple("TRUNCATE insumoporpedido;");
        
        $conn->consulta_simple("TRUNCATE n_movimiento_almacen;");
        $conn->consulta_simple("TRUNCATE n_detalle_movimiento_almacen;");
        $conn->consulta_simple("TRUNCATE n_historial_stock_insumo;");

        $conn->consulta_simple("INSERT INTO n_historial_stock_insumo ( insumo_id, insumo_porcion_id, almacen_id, stock_inicial, stock_final, fecha, trabajador_id, created_at)
                 SELECT pkInsumo, NULL, 1, 0, 0, date(now()) ,1,now()
                 FROM insumos i WHERE i.estado = 0");

        $conn->consulta_simple("INSERT INTO n_historial_stock_insumo ( insumo_id, insumo_porcion_id, almacen_id, stock_inicial, stock_final, fecha, trabajador_id, created_at)
                SELECT insumo_id, id, 1, 0, 0, date(now()), 1, now()
                FROM insumo_porcion i WHERE i.deleted_at is null");

        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 1;");
    }

    //Limpieza de insumos
    if(isset($_POST["ins"])){
        $proceso = 1;
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 0;");
        $conn->consulta_simple("TRUNCATE insumos;");
        $conn->consulta_simple("TRUNCATE provedor;");
        $conn->consulta_simple("TRUNCATE tipo_insumo;");
        $conn->consulta_simple("TRUNCATE unidad;");
        $conn->consulta_simple("Insert into provedor values(1,'000111222P','PROVEEDOR GENERICO','','','','',0);");
        $conn->consulta_simple("Insert into tipo_insumo values(NULL,'Verduras',0);");
        $conn->consulta_simple("Insert into tipo_insumo values(NULL,'Frutas',0);");
        $conn->consulta_simple("Insert into tipo_insumo values(NULL,'Pescados y Mariscos',0);");
        $conn->consulta_simple("Insert into tipo_insumo values(NULL,'Carnes Rojas',0);");
        $conn->consulta_simple("Insert into tipo_insumo values(NULL,'Carnes Blancas',0);");
        $conn->consulta_simple("Insert into tipo_insumo values(NULL,'Bebidas Embotelladas',0);");
        $conn->consulta_simple("Insert into tipo_insumo values(NULL,'Licores',0);");
        $conn->consulta_simple("Insert into tipo_insumo values(NULL,'Otros',0);");
        $conn->consulta_simple("Insert into unidad values(1,'UNIDAD',1,0,0);");
        $conn->consulta_simple("Insert into unidad values(2,'PORCION',1,0,0);");
        $conn->consulta_simple("Insert into unidad values(3,'KILO',1,0,0);");
        $conn->consulta_simple("Insert into unidad values(4,'GRAMO',1,0,0);");
        $conn->consulta_simple("Insert into unidad values(5,'LITRO',1,0,0);");
        $conn->consulta_simple("Insert into unidad values(6,'MILILITRO',1,0,0);");

        $conn->consulta_simple("TRUNCATE insumo_porcion;");

        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 1;");
    }

    //Limpieza de recetas
    if(isset($_POST["rec"])){
        $proceso = 1;
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 0;");
        $conn->consulta_simple("TRUNCATE insumo_menu;");

        $conn->consulta_simple("TRUNCATE n_receta;");

        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 1;");
    }

    //Limpieza de platos
    if(isset($_POST["pla"])){
        $proceso = 1;
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 0;");
        $conn->consulta_simple("TRUNCATE plato;");
        $conn->consulta_simple("TRUNCATE platos_amarrados;");
        $conn->consulta_simple("TRUNCATE plato_codigo_sunat;");
        $conn->consulta_simple("TRUNCATE plato_stock;");
        $conn->consulta_simple("TRUNCATE plato_sucursal;");
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 1;");
    }

    //Limpieza de Menus
    if(isset($_POST["men"])){
        $proceso = 1;
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 0;");
        $conn->consulta_simple("TRUNCATE componente_menu;");
        $conn->consulta_simple("TRUNCATE tipo_componente_menu;");
        $conn->consulta_simple("TRUNCATE tipo_menu;");
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 1;");
    }

    //Limpieza de mesas
    if(isset($_POST["mes"])){
        $proceso = 1;
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 0;");
        $conn->consulta_simple("TRUNCATE mesas;");
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 1;");
    }

    //Limpieza de Salones
    if(isset($_POST["sal"])){
        $proceso = 1;
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 0;");
        $conn->consulta_simple("TRUNCATE salon;");
        $conn->consulta_simple("Insert into salon values(43,'DELIVERY','SU009',0);");
        $conn->consulta_simple("Insert into salon values(44,'LLEVAR','SU009',0);");
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 1;");
    }

    //Limpieza de usuarios
    if(isset($_POST["usu"])){
        $proceso = 1;
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 0;");
        $conn->consulta_simple("DELETE from trabajador where pkTrabajador > 1");
        $conn->consulta_simple("TRUNCATE trabajador_modulo;");
        $conn->consulta_simple("TRUNCATE trabajador_submodulo;");
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 1;");
    }

    //Limpieza de categorias y tipos
    if(isset($_POST["ctp"])){
        $proceso = 1;
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 0;");
        $conn->consulta_simple("TRUNCATE categoria;");
        $conn->consulta_simple("TRUNCATE tipos;");
        $conn->consulta_simple("TRUNCATE tipo_codigo_sunat;");
        $conn->consulta_simple("Insert into categoria values(1,'COMIDA','SU009');");
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 1;");
    }

    //Limpieza Gastos Diarios
    if(isset($_POST["gad"])){
        $proceso = 1;
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 0;");
        $conn->consulta_simple("TRUNCATE gastos_diarios;");
        $conn->consulta_simple("DELETE FROM movimiento_dinero where tipo_origen = 'GAS';");
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 1;");
    }

    //Limpieza Otros Datos
    if(isset($_POST["otr"])){
        $proceso = 1;
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 0;");
        $conn->consulta_simple("TRUNCATE cloud_config;");
        $conn->consulta_simple("TRUNCATE descuento_prefijado;");
        $conn->consulta_simple("TRUNCATE medio_pago;");
        $conn->consulta_simple("TRUNCATE mensajes_cloud;");
        $conn->consulta_simple("TRUNCATE moneda;");
        $conn->consulta_simple("TRUNCATE tipo_cambio;");
        $conn->consulta_simple("TRUNCATE tipo_gasto;");
        $conn->consulta_simple("TRUNCATE tipo_impuesto;");
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
        $conn->consulta_simple("Insert into cloud_config values(NULL,'icbper','0.20')");
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 1;");
    }

    //Limpieza Mensajes
    if(isset($_POST["msg"])){
        $proceso = 1;
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 0;");
        $conn->consulta_simple("TRUNCATE mensaje;");
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 1;");
    }

    //Limpieza Impresion
    if(isset($_POST["imp"])){
        $proceso = 1;
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 0;");
        $conn->consulta_simple("TRUNCATE cola_impresion;");
        $conn->consulta_simple("TRUNCATE configuracion_impresion;");
        $conn->consulta_simple("TRUNCATE impresoras;");
        $conn->consulta_simple("TRUNCATE margenes_impresion;");
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 1;");
    }

    //Limpieza Clientes
    if(isset($_POST["cli"])){
        $proceso = 1;
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 0;");
        $conn->consulta_simple("TRUNCATE person;");
        $conn->consulta_simple("TRUNCATE persona_juridica;");
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 1;");
    }

    //Cambios de version

    //SET FULL
    if(isset($_POST["full"])){
        $proceso = 1;
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 0;");
        $conn->consulta_simple("Update module set estadoModule = 0 where pkModule = 1");
        $conn->consulta_simple("TRUNCATE TABLE submodule");
        //Insertamos Submodulos Actualizados
        $conn->consulta_simple("INSERT into submodule VALUES
            (null, 1, 'Recetas', 0 , 'AdmInsumo'),
            (null, 1, 'Insumos', 0 , 'AddInsumo'),
            (null, 1, 'Guias de Ingreso', 0 , 'AdminGuias'),
            (null, 1, 'Guias de Salida', 0 , 'AdminGuiaSalida'),
            (null, 1, 'Transferencia de Almacen', 0 , 'ShowTransferencia'),
            (null, 1, 'Compras', 0 , 'ShowCompras'),
            (null, 1, 'Proveedores', 0 , 'ShowAdminProveedor'),
            (null, 1, 'Unidades', 0 , 'AdminUnidades'),
            (null, 1, 'Tipos de Insumo', 0 , 'AdminTipoInsumo'),
            (null, 1, 'Almacenes', 0 , 'ShowAlmacenes'),
            (null, 1, 'Kardex (Resumen)', 2 , 'showKardex'),
            (null, 1, 'Kardex (Seguimiento Diario)', 0 , 'showKardexDetallado'),
            (null, 1, 'Kardex (Seguimiento Detallado)', 0 , 'showKardexDetallado2'),
            (null, 1, 'Consolidado Stocks', 0 , 'Stocks'),
            (null, 2, 'Platos', 0 , 'AdminPlato'),
            (null, 2, 'Tipos de Platos', 0 , 'AdminTipos'),
            (null, 2, 'Stock Manual Platos', 0 , 'SManual'),
            (null, 2, 'Tipos de Menu', 0 , 'TipoMenu'),
            (null, 2, 'Estructura de Menu (Entrada, fondo, postre)', 0 , 'TipoComponenteMenu'),
            (null, 2, 'Arma tu Menu', 0 , 'ComponenteMenu'),
            (null, 2, 'Platos Amarrados', 0 , 'PlatosAmarrados'),
            (null, 2, 'Impresion por Pantalla', 2 , 'IPantalla'),
            (null, 2, 'Cuentas Pendientes', 0 , 'CPendientes'),
            (null, 2, 'Facturacion Personalizada', 0 , 'FacturacionPersonalizada'),
            (null, 2, 'Notas Electronicas', 0 , 'ShowAddNota'),
            (null, 2, 'Reporte de Ventas', 2 , 'ShowReporteVentas'),
            (null, 2, 'Reporte de Boletas', 0 , 'ShowBoleta'),
            (null, 2, 'Reporte de Facturas', 0 , 'ShowFactura'),
            (null, 2, 'Reporte Consumos', 0 , 'CConsumo'),
            (null, 2, 'Reporte Clientes', 0 , 'ShowClientes'),
            (null, 3, 'Administrar Personal', 0 , 'Personal'),
            (null, 10, 'Total Ventas por Mes', 0 , 'Tmes'),
            (null, 10, 'Platos con mayor rotacion', 0 , 'SProductosDia'),
            (null, 10, 'Rendimiento de Platos', 0 , 'showRendimientoPlato'),
            (null, 10, 'Stock de Platos', 0 , 'ShowReporPlatosStock'),
            (null, 10, 'Consolidado de Ventas', 0 , 'Consolidado'),
            (null, 10, 'Ventas por Trabajador', 0 , 'TVentasMozo'),
            (null, 10, 'Reporte de Ventas (por tipo)', 0 , 'SaleConsumo'),
            (null, 10, 'Consumo por Persona', 0 , 'showConsumoxPersona'),
            (null, 10, 'Pedidos Anulados', 0 , 'showPedidosAnulados'),
            (null, 10, 'Consolidado Caja', 0 , 'CajaFinal'),
            (null, 10, 'Ventas vs Compras', 0 , 'showVentasCompras'),
            (null, 11, 'Cierre Diario', 0 , 'ShowCierreDiario'),
            (null, 11, 'Monto Inicial', 0 , 'MontoInicial'),
            (null, 11, 'Ingresos o Salidas de dinero', 0 , 'RegistrarPago'),
            (null, 11, 'Reporte de Ingresos y Salidas de dinero', 2 , 'IngresosSalidas'),
            (null, 11, 'Reporte Fondos Externos', 0 , 'showFondosExternos'),
            (null, 11, 'Reporte Ingresos VS Egresos', 0 , 'IngresosEgresos'),
            (null, 11, 'Reporte Medios de Pago', 0 , 'RTarjeta'),
            (null, 11, 'Reporte Propinas', 0 , 'Propinas'),
            (null, 12, 'Empresa', 0 , 'Emp'),
            (null, 12, 'Salones', 0 , 'AdmSalon'),
            (null, 12, 'Mesas', 0 , 'ShowMesas'),
            (null, 12, 'Mensajes en Pedido', 0 , 'ShowAdminMensajes'),
            (null, 12, 'Monedas y Medios de Pago', 0 , 'CMonedaPago'),
            (null, 12, 'Descuentos y Convenios', 0 , 'CDescuento'),
            (null, 12, 'Tipo Movimiento de Dinero', 0 , 'TipoGasto'),
            (null, 12, 'Motivos Anulacion', 0 , 'MotivoAnulacion'),
            (null, 12, 'Configuracion Impresion', 0 , 'CImpresion'),
            (null, 12, 'Configuracion PSE', 0 , 'CFacturacion'),
            (null, 12, 'Administracion del Sistema', 0 , 'Limpieza')
        ;");
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 1;");
    }

    //SET LITE
    if(isset($_POST["lite"])){
        $proceso = 1;
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 0;");
        $conn->consulta_simple("Update module set estadoModule = 1 where pkModule = 1");
        $conn->consulta_simple("TRUNCATE TABLE submodule");
        //Insertamos Submodulos Actualizados
        $conn->consulta_simple("INSERT into submodule VALUES
            (null, 1, 'Recetas', 1 , 'AdmInsumo'),
            (null, 1, 'Insumos', 1 , 'AddInsumo'),
            (null, 1, 'Guias de Ingreso', 1 , 'AdminGuias'),
            (null, 1, 'Guias de Salida', 1 , 'AdminGuiaSalida'),
            (null, 1, 'Transferencia de Almacen', 1 , 'ShowTransferencia'),
            (null, 1, 'Compras', 1 , 'ShowCompras'),
            (null, 1, 'Proveedores', 1 , 'ShowAdminProveedor'),
            (null, 1, 'Unidades', 1 , 'AdminUnidades'),
            (null, 1, 'Tipos de Insumo', 1 , 'AdminTipoInsumo'),
            (null, 1, 'Almacenes', 1 , 'ShowAlmacenes'),
            (null, 1, 'Kardex (Resumen)', 1 , 'showKardex'),
            (null, 1, 'Kardex (Seguimiento Diario)', 1 , 'showKardexDetallado'),
            (null, 1, 'Kardex (Seguimiento Detallado)', 1 , 'showKardexDetallado2'),
            (null, 1, 'Consolidado Stocks', 1 , 'Stocks'),
            (null, 2, 'Platos', 0 , 'AdminPlato'),
            (null, 2, 'Tipos de Platos', 0 , 'AdminTipos'),
            (null, 2, 'Stock Manual Platos', 0 , 'SManual'),
            (null, 2, 'Tipos de Menu', 0 , 'TipoMenu'),
            (null, 2, 'Estructura de Menu (Entrada, fondo, postre)', 0 , 'TipoComponenteMenu'),
            (null, 2, 'Arma tu Menu', 0 , 'ComponenteMenu'),
            (null, 2, 'Platos Amarrados', 0 , 'PlatosAmarrados'),
            (null, 2, 'Impresion por Pantalla', 2 , 'IPantalla'),
            (null, 2, 'Cuentas Pendientes', 0 , 'CPendientes'),
            (null, 2, 'Facturacion Personalizada', 0 , 'FacturacionPersonalizada'),
            (null, 2, 'Notas Electronicas', 0 , 'ShowAddNota'),
            (null, 2, 'Reporte de Ventas', 2 , 'ShowReporteVentas'),
            (null, 2, 'Reporte de Boletas', 0 , 'ShowBoleta'),
            (null, 2, 'Reporte de Facturas', 0 , 'ShowFactura'),
            (null, 2, 'Reporte Consumos', 0 , 'CConsumo'),
            (null, 3, 'Administrar Personal', 0 , 'Personal'),
            (null, 10, 'Total Ventas por Mes', 0 , 'Tmes'),
            (null, 10, 'Platos con mayor rotacion', 0 , 'SProductosDia'),
            (null, 10, 'Rendimiento de Platos', 1 , 'showRendimientoPlato'),
            (null, 10, 'Stock de Platos', 1 , 'ShowReporPlatosStock'),
            (null, 10, 'Consolidado de Ventas', 0 , 'Consolidado'),
            (null, 10, 'Ventas por Trabajador', 0 , 'TVentasMozo'),
            (null, 10, 'Reporte de Ventas (por tipo)', 0 , 'SaleConsumo'),
            (null, 10, 'Consumo por Persona', 0 , 'showConsumoxPersona'),
            (null, 10, 'Pedidos Anulados', 0 , 'showPedidosAnulados'),
            (null, 10, 'Consolidado Caja', 0 , 'CajaFinal'),
            (null, 10, 'Ventas vs Compras', 1 , 'showVentasCompras'),
            (null, 11, 'Cierre Diario', 0 , 'ShowCierreDiario'),
            (null, 11, 'Monto Inicial', 0 , 'MontoInicial'),
            (null, 11, 'Ingresos o Salidas de dinero', 0 , 'RegistrarPago'),
            (null, 11, 'Reporte de Ingresos y Salidas de dinero', 2 , 'IngresosSalidas'),
            (null, 11, 'Reporte Fondos Externos', 0 , 'showFondosExternos'),
            (null, 11, 'Reporte Ingresos VS Egresos', 0 , 'IngresosEgresos'),
            (null, 11, 'Reporte Medios de Pago', 0 , 'RTarjeta'),
            (null, 11, 'Reporte Propinas', 0 , 'Propinas'),
            (null, 12, 'Empresa', 0 , 'Emp'),
            (null, 12, 'Salones', 0 , 'AdmSalon'),
            (null, 12, 'Mesas', 0 , 'ShowMesas'),
            (null, 12, 'Mensajes en Pedido', 0 , 'ShowAdminMensajes'),
            (null, 12, 'Monedas y Medios de Pago', 0 , 'CMonedaPago'),
            (null, 12, 'Descuentos y Convenios', 0 , 'CDescuento'),
            (null, 12, 'Tipo Movimiento de Dinero', 0 , 'TipoGasto'),
            (null, 12, 'Motivos Anulacion', 0 , 'MotivoAnulacion'),
            (null, 12, 'Configuracion Impresion', 0 , 'CImpresion'),
            (null, 12, 'Configuracion PSE', 0 , 'CFacturacion'),
            (null, 12, 'Administracion del Sistema', 0 , 'Limpieza')
        ;");
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 1;");
    }
}

require_once('recursos/componentes/header.php'); 
?>

<?php if(intval($proceso) === 1):?>
<div class="alert alert-success" role="alert">
    Operación Realizada con Éxito
</div>
<?php endif;?>

<?php if(intval($proceso) === 2):?>
<div class="alert alert-danger" role="alert">
    La Clave de Administrador es Incorrecta
</div>
<?php endif;?>

<h1>Administracion del Sistema</h1>

<div class="panel panel-primary" id='pplatos'>
    <div class="panel-heading">
        <h3 class="panel-title">Selecciona la opción que corresponda</h3>
    </div>
    <div class="panel-body">
        <input type="checkbox" name="caj" value="iez"> Limpiar Cajas<br>
        <input type="checkbox" name="cmp" value="iez"> Limpiar Comprobantes<br>
        <input type="checkbox" name="ven" value="iez"> Limpiar Ventas<br>
        <input type="checkbox" name="kar" value="iez"> Limpiar Kardex (Insumos - Recetas)<br>
        <input type="checkbox" name="ins" value="iez"> Limpiar Insumos y Proveedores<br>
        <input type="checkbox" name="rec" value="iez"> Limpiar Recetas<br>
        <input type="checkbox" name="pla" value="iez"> Limpiar Platos<br>
        <input type="checkbox" name="men" value="iez"> Limpiar Menus<br>
        <input type="checkbox" name="mes" value="iez"> Limpiar Mesas<br>
        <input type="checkbox" name="sal" value="iez"> Limpiar Salones<br>
        <input type="checkbox" name="usu" value="iez"> Limpiar Usuarios<br>
        <input type="checkbox" name="ctp" value="iez"> Limpiar Categorias y Tipos<br>
        <input type="checkbox" name="gad" value="iez"> Limpiar Gastos Diarios<br>
        <input type="checkbox" name="msg" value="iez"> Limpiar Mensajes<br>
        <input type="checkbox" name="imp" value="iez"> Limpiar Impresion<br>
        <input type="checkbox" name="cli" value="iez"> Limpiar Clientes<br>
        <input type="checkbox" name="otr" value="iez"> Limpiar Otros Datos [Configuracion Facturacion, Monedas, Descuentos Prefijados,Tipos de Gasto, Mensajes desde Cloud, Historial Tipo de Cambio, etc]<br>
        <hr/>
        <input type="checkbox" name="full" value="iez"> Establecer Version FULL<br>
        <input type="checkbox" name="lite" value="iez"> Establecer Version LITE<br>
        <hr/>
        Contraseña de Administrador (Para Aplicar Cambios)<br>
        <input type="password" name="pass"><br>
        <div class='control-group'>
            <p></p>
            <input type="submit" class='btn btn-primary' value="Proceder"/>
        </div>
    </div>
</div>
</form>
<hr/>

   </div><!--/row-->
      <hr>
    </div><!--/.container-->
    

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="recursos/js/jquery.js"></script>
    <script src="recursos/js/jquery-ui.js"></script>
    <script src="recursos/js/plugins/datatables/jquery-datatables.js"></script>
    <script src="recursos/js/plugins/datatables/dataTables.tableTools.js"></script>
    <script src="recursos/js/bootstrap.min.js"></script>
    <script src="recursos/js/offcanvas.js"></script>
    <script src="../Public/select2/js/select2.js"></script>
    <script>
   
    jQuery.fn.reset = function () {
        $(this).each(function () {
            this.reset();
        });
    };


    $(document).ready(function () {
        history.pushState(null, "", 'limpieza.php');
    });

    </script>
  </body>
</html>

                            