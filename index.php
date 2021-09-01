<?php
error_reporting(0);
require_once('Components/Config.inc.php');
class index{ 
    public function ruteo(){
        $fronController = new FrontController();
        $fronController->route();
    }   
}
$index = new index();
$index->ruteo();

//URL SIN RUTEO
//PINCHE JEISON

$url_conocidas = array();
$t0 = array();
$t0["url"] = "?controller=Almacen&action=PPrima";
$t0["destino"] = "reportes/proceso_materia_prima.php?s=".UserLogin::get_pkSucursal();
array_push($url_conocidas,$t0);

$t2 = array();
$t2["url"] = "?controller=Sale&action=SManual";
$t2["destino"] = "reportes/stock_manual_platos.php?s=".UserLogin::get_pkSucursal();
array_push($url_conocidas,$t2);

$t3 = array();
$t3["url"] = "?controller=Sale&action=TipoMenu";
$t3["destino"] = "reportes/tipo_menu.php";
array_push($url_conocidas,$t3);

$t4 = array();
$t4["url"] = "?controller=Sale&action=TipoComponenteMenu";
$t4["destino"] = "reportes/tipo_componente_menu.php";
array_push($url_conocidas,$t4);

$t5 = array();
$t5["url"] = "?controller=Sale&action=ComponenteMenu";
$t5["destino"] = "reportes/componente_menu.php";
array_push($url_conocidas,$t5);

$t6 = array();
$t6["url"] = "?controller=Report&action=Consolidado";
$t6["destino"] = "reportes/consolidado.php?s=".UserLogin::get_pkSucursal();
array_push($url_conocidas,$t6);

$t7 = array();
$t7["url"] = "?controller=Report&action=Stocks";
$t7["destino"] = "reportes/stocks.php?s=".UserLogin::get_pkSucursal();
array_push($url_conocidas,$t7);

$t8 = array();
$t8["url"] = "?controller=Caja&action=IngresosEgresos";
$t8["destino"] = "reportes/ingresos_vs_egresos.php";
array_push($url_conocidas,$t8);

$t9 = array();
$t9["url"] = "/?controller=Caja&action=RTarjeta";
$t9["destino"] = "reportes/reporte_tarjetas.php";
array_push($url_conocidas,$t9);

$t10 = array();
$t10["url"] = "/?controller=Config&action=CImpresion";
$t10["destino"] = "reportes/configuracion_impresion.php";
array_push($url_conocidas,$t10);

$t11 = array();
$t11["url"] = "/?controller=Almacen&action=Sync";
$t11["destino"] = "reportes/sincronizacion_almacen.php";
array_push($url_conocidas,$t11);

$t12 = array();
$t12["url"] = "/?controller=Caja&action=IngresosSalidas";
$t12["destino"] = "reportes/ingresos_salidas.php";
array_push($url_conocidas,$t12);

$t13 = array();
$t13["url"] = "/?controller=Sale&action=PlatosAmarrados";
$t13["destino"] = "reportes/platos_amarrados.php";
array_push($url_conocidas,$t13);

$t14 = array();
$t14["url"] = "/?controller=Config&action=CFacturacion";
$t14["destino"] = "reportes/configuracion_facturacion.php";
array_push($url_conocidas,$t14);

$t15 = array();
$t15["url"] = "/?controller=Sale&action=ComprobantesPendientes";
$t15["destino"] = "reportes/comprobantes_pendientes.php";
array_push($url_conocidas,$t15);

$t16 = array();
$t16["url"] = "/?controller=Report&action=CutXDay";
$t16["destino"] = "reportes/cortes_entre_dias.php";
array_push($url_conocidas,$t16);

$t17 = array();
$t17["url"] = "/?controller=Report&action=CajaFinal";
$t17["destino"] = "reportes/caja_final.php";
array_push($url_conocidas,$t17);

$t18 = array();
$t18["url"] = "/?controller=Sale&action=FacturacionPersonalizada";
$t18["destino"] = "reportes/facturacion_personalizada.php";
array_push($url_conocidas,$t18);

$t19 = array();
$t19["url"] = "/?controller=Config&action=CMonedaPago";
$t19["destino"] = "reportes/moneda_pagos.php";
array_push($url_conocidas,$t19);

$t20 = array();
$t20["url"] = "/?controller=Config&action=CDescuento";
$t20["destino"] = "reportes/descuentos.php";
array_push($url_conocidas,$t20);

$t21 = array();
$t21["url"] = "/?controller=Sale&action=IPantalla";
$t21["destino"] = "reportes/impresion_pantalla.php";
array_push($url_conocidas,$t21);

$t22 = array();
$t22["url"] = "/?controller=Config&action=TipoGasto";
$t22["destino"] = "reportes/tipo_gasto.php";
array_push($url_conocidas,$t22);

$t23 = array();
$t23["url"] = "/?controller=Caja&action=Propinas";
$t23["destino"] = "reportes/propinas.php";
array_push($url_conocidas,$t23);

$t24 = array();
$t24["url"] = "/?controller=Config&action=Limpieza";
$t24["destino"] = "reportes/limpieza.php";
array_push($url_conocidas,$t24);

if(is_array($url_conocidas)){
    foreach($url_conocidas as $uc){
        if (strpos($_SERVER['REQUEST_URI'],$uc["url"]) !== false) {
            echo '<meta http-equiv="refresh" content="0; url='.$uc["destino"].'" />';
        }
    }
}
