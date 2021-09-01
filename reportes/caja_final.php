<?php
error_reporting(E_ALL);
$titulo_pagina = 'Reporte Cierre de caja';
$titulo_sistema = 'usqay2';

//Clase Moderna de Base de Datos
include_once('recursos/componentes/MasterConexion.php');
$conn = new MasterConexion();

//Obtenemos Fecha de Cierre Actual
$fecha_cierre_actual = $conn->consulta_arreglo("Select * from cierrediario LIMIT 1");
$fecha_cierre_actual = $fecha_cierre_actual["fecha"];

//Solo se muestra la caja a usuario 1 o 2
//Obtenemos Listado de Cajas
$query_cajas = $conn->consulta_matriz("Select * from cajas");
//Obtenemos Ultimo Corte
$data_corte = $conn->consulta_arreglo("Select c.* from corte c, accion_caja ac where c.fin IS NULL AND ac.pk_accion = c.id AND ac.tipo_accion = 'CUT' AND ac.caja = '".$_COOKIE["c"]."' order by c.id DESC LIMIT 1");

//Nivel de Usuario
$estilo_nivel = "style='display:none;'";
if(intval($_COOKIE["TYP"]) > 0 &&  intval($_COOKIE["TYP"]) < 3){
    $estilo_nivel = "";
}

//Variables Para Busqueda
$caja = $_COOKIE["c"];
$fecha_cierre = $fecha_cierre_actual;
$corte = $data_corte["inicio"];
$fin_corte = date("Y-m-d H:i:s");

//Verificamos si no nos enviaron la variable por URL
if(isset($_REQUEST["ci"])){
    $fecha_cierre = $_REQUEST["ci"];
}

if(isset($_REQUEST["ca"])){
    $caja = $_REQUEST["ca"];
}

if(isset($_REQUEST["co"])){
    $corte = $_REQUEST["co"];
    //Si hay corte por URL, obtenemos nuevamente sus datos
    $data_corte = $conn->consulta_arreglo("Select c.* from corte c, accion_caja ac where c.inicio = '".$corte."' AND ac.pk_accion = c.id AND ac.tipo_accion = 'CUT' AND ac.caja = '".$caja."' order by c.id DESC LIMIT 1");
    if($data_corte["fin"] <> ""){
        $fin_corte = $data_corte["fin"];
    }
}

//Obtenemos Todos los Medios de Pago
$medios = array(); 
$resultado_medios = $conn->consulta_matriz("Select mp.id, mp.moneda, mp.nombre, m.simbolo from medio_pago mp, moneda m where mp.estado > 0 AND mp.moneda = m.id");
if(is_array($resultado_medios)){
    foreach($resultado_medios as $medio){
        if(intval($medio["id"]) === 1){
            $resultado_monedas = $conn->consulta_matriz("Select * from moneda where estado > 0");
            if(is_array($resultado_monedas)){
                foreach($resultado_monedas as $moneda){
                    $tmp = array();
                    $tmp["nombre"] = $medio["nombre"];
                    $tmp["simbolo"] = $moneda["simbolo"];
                    $tmp["id_medio"] = $medio["id"];
                    $tmp["id_moneda"] = $moneda["id"];
                    $medios[] = $tmp;
                }
            }
        }else{
            $tmp = array();
            $tmp["nombre"] = $medio["nombre"];
            $tmp["simbolo"] = $medio["simbolo"];
            $tmp["id_medio"] = $medio["id"];
            $tmp["id_moneda"] = $medio["moneda"];
            $medios[] = $tmp;
        }
    }
}

//Obtenemos todas las monedas
$monedas = array();
$resultado_monedas = $conn->consulta_matriz("Select * from moneda where estado > 0");
if(is_array($resultado_monedas)){
    foreach($resultado_monedas as $moneda){
        $tmp = array();
        $tmp["id"] = $moneda["id"];
        $tmp["nombre"] = $moneda["nombre"];
        $tmp["simbolo"] = $moneda["simbolo"];
        $monedas[] = $tmp;
    }
}

//Obtenemos todos los tipos de pagos
$tipos_gastos = array();
$resultado_gastos = $conn->consulta_matriz("Select * from tipo_gasto where estado = 1");
if(is_array($resultado_gastos)){
    foreach($resultado_gastos as $gasto){
        $tmp = array();
        $tmp["id"] = $gasto["id"];
        $tmp["nombre"] = $gasto["nombre"];
        $tmp["direccion"] = $gasto["direccion"];
        $tipos_gastos[] = $tmp;
    }
}

require_once('recursos/componentes/header.php'); 
?>
<style>
    body{
        background: #f8f9fa;
    }

    .p2{
        padding: 20px;
    }

    .arriba{
        margin-top: 25px;
        margin-left: 10px;
    }
    .tarjeta{
        border: 1px solid #eeeeee
    }
    .oculto{
        display: none;
    }
    .caja{
        background: #eaeaea;
        padding: 30px 20px;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    .caja h3{
        color: #787878;
        font-weight: bold;
    }

    .caja .parrafo{
        color: #00395a;
        font-weight: bold;
    }

    .caja .parra{
        color: #5bc0de;
    }

    .resumen h4{
        color: #787878;
        font-weight: bold;
    }

    .resumen .celeste{
        background: #00395a !important;
        color: #fff;
    }

    .resumen-caja h5{
        font-weight: bold;
        margin-left: 20px;
    }

    .celeste{
        background: #00395a !important;
        color: #fff;
    }
    
    .verde{
        background: #6aae27 !important;
        color: #fff;
    }


    .movimientos h5{
        font-weight: bold;
        margin-bottom: 20px;
    }

    .movimientos h6{
        font-weight: bold;
    }

    .movimientos .mpa{
        color: #dae2db;
        font-style: italic;
    }

    .ingre h5,h6{
        font-weight: bold;
    }

    table th{
        font-size: 1.1rem;
    }

    .ingre .mpa{
        color: #dae2db;
        font-style: italic;
    }

    .ventas h5{
        font-weight: bold;
    }

    .ventas .descuento{
        color: #ea5252;
    }

     .ventas-creditos h5{
        font-weight: bold;
        margin-bottom: 20px;
    }

    .ventas-creditos .mpa{
        color: #dae2db;
        font-style: italic;
    }

    .reservas h5{
        font-weight: bold;
        margin-bottom: 20px;
    }
    .mpa{
        color: #dae2db;
        font-style: italic;
    }
    .reservas .mpa{
        color: #dae2db;
        font-style: italic;
    }

    .dataTables_wrapper{
        margin: 20px 10px 0px 10px;
    }
    .dt-buttons{
        margin-bottom: 15px;
    }
    
    @media print {
        .hideprint {
            display: none !important;
        }
        body{
            margin: 0px !important;
        }
        .fixed-table-toolbar{
            display: none !important;
        }
    }
</style>

<div class="panel">

    <div class="panel-body">

        <!--Filtros Para Busqueda-->
        <div class="container-fluid hideprint">
        <div class="panel panel-success">
            <div class="panel-body panel-success">

                <div class="col-md-3">
                    <label for="">Fecha</label>
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                        <input type="date" name="fecha" aria-describedby="basic-addon1" class="form-control date" id="txtFechaCierreDiario" value="<?php echo $fecha_cierre ?>" onchange="carga_cortes()">
                    </div>
                </div>

                <div class="col-md-3" <?php echo $estilo_nivel;?>>
                    <label for="">Caja</label>
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1">
                            <span class="glyphicon glyphicon-inbox"></span>
                        </span>
                        <select aria-describedby="basic-addon1" name="caja" class="form-control" id="txtCajaAct" onchange="carga_cortes()">
                            <option value='' <?php if($caja == ""){echo " selected";}?>>Todas</option>
                            <?php 
                            if(is_array($query_cajas)){
                                foreach($query_cajas as $cajas){
                                    if($cajas["caja"] == $caja){
                                        echo "<option value='".$cajas["caja"]."' selected>".$cajas["caja"]."</option>";
                                    }else{
                                        echo "<option value='".$cajas["caja"]."'>".$cajas["caja"]."</option>";
                                    }
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-3" id="div_corte">
                    <label for="">Corte</label>
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1">
                            <span class="glyphicon glyphicon-flash"></span>
                        </span>
                        <select aria-describedby="basic-addon1" name="fecha" class="form-control" id="txtCorteAct">
                        </select>
                    </div>
                </div>

                <div class="col-md-3" style="text-align:center;">
                        <p></p>
                        <a onclick="buscar()" class="btn btn-primary" id="btnCut" style='margin-right:5px;margin-top:5px;'><span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar</a>
                </div>

            </div>
        </div>
        </div>

        <!--Resumenes por medio-->
        <div class="container-fluid">
            <div class="panel tarjeta">
                <div class="panel-body">
                <div class="col-md-4">
                    <div class="caja">
                    <?php if($caja <> ""):?>
                    <h3>CAJA <?php echo $caja;?></h3>
                    <?php else:?>
                    <h3>Todas las Cajas</h3>
                    <?php endif;?>
                    <?php foreach($monedas as $mon):?>
                        <p class="parrafo">Efectivo en <?php echo $mon["nombre"];?>: <?php echo $mon["simbolo"];?> 
                        <?php
                        $query_efectivo = "";
                        if($caja <> ""){
                            $query_efectivo = "Select sum(monto) as total from movimiento_dinero where estado = 1 AND id_medio = 1 AND moneda = '".$mon["id"]."' AND fecha_hora between '".$corte."' AND '".$fin_corte."' AND caja = '".$caja."'";
                        }else{
                            $query_efectivo = "Select sum(monto) as total from movimiento_dinero where estado = 1 AND id_medio = 1 AND moneda = '".$mon["id"]."' AND fecha_cierre  = '".$fecha_cierre."'";
                        }
                        $resultado_efectivo = $conn->consulta_arreglo($query_efectivo);
                        echo round(floatval($resultado_efectivo["total"]),2);
                        ?>
                        </p>
                    <?php endforeach;?>
        
                    <p><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Inicio Corte: <?php echo $corte;?></p>
                    <?php if($data_corte["fin"] == ""):?>
                        <p><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Conteo Hasta: <?php echo date("Y-m-d H:i:s");?></p>
                    <?php else:?>
                        <p><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Fin Corte: <?php echo $data_corte["fin"];?></p>
                    <?php endif;?>
                    

                    <p class="parra"><b><i class="fa fa-info"></i> Efectivo en Caja = </b> (Apertura + Ventas + Ingresos) - Egresos</p>
                    </div>
                    
                </div>
                <div class="col-md-8 resumen">
                    <?php if($caja <> ""):?>
                        <h4>Resumen desde <?php echo $corte;?> hasta <?php echo $fin_corte;?></h4>
                    <?php else:?>
                        <h4>Resumen <?php echo $fecha_cierre;?></h4>
                    <?php endif;?> 
                    
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                <?php foreach($medios as $med):?>
                                    <?php if(intval($med["id_medio"]) === 1):?>
                                        <!--Monto Inicial-->
                                        <?php
                                        $query_inicial = "";
                                        if($caja <> ""){
                                            $query_inicial = "Select sum(monto) as total from movimiento_dinero where estado = 1 AND id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND tipo_origen = 'CUT' AND fecha_hora between '".$corte."' AND '".$fin_corte."' AND caja = '".$caja."'";
                                        }else{
                                            $query_inicial = "Select sum(monto) as total from movimiento_dinero where estado = 1 AND id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND tipo_origen = 'CUT' AND fecha_cierre  = '".$fecha_cierre."'";
                                        }
                                        $resultado_inicial = $conn->consulta_arreglo($query_inicial);
                                        ?>
                                        <tr>
                                            <td>MONTO INICIAL</td>
                                            <td><?php echo $med["nombre"];?></td>
                                            <td><?php echo $med["simbolo"];?> <?php echo round(floatval($resultado_inicial["total"]),2);?></td>
                                        </tr>
                                    <?php endif;?>
                                    <!--Buscamos Ventas-->
                                    <?php
                                    $query_ventas = "";
                                    if($caja <> ""){
                                        $query_ventas = "Select sum(monto) as total from movimiento_dinero where estado = 1 AND id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND tipo_origen = 'PED' AND fecha_hora between '".$corte."' AND '".$fin_corte."' AND caja = '".$caja."'";
                                    }else{
                                        $query_ventas = "Select sum(monto) as total from movimiento_dinero where estado = 1 AND id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND tipo_origen = 'PED' AND fecha_cierre  = '".$fecha_cierre."'";
                                    }
                                    $resultado_ventas = $conn->consulta_arreglo($query_ventas);
                                    ?>
                                    <tr>
                                        <td>VENTAS</td>
                                        <td><?php echo $med["nombre"];?></td>
                                        <td><?php echo $med["simbolo"];?> <?php echo round(floatval($resultado_ventas["total"]),2);?></td>
                                    </tr>
                                    <!--Buscamos Ingresos Adicionales-->
                                    <?php
                                    $query_ingresos = "";
                                    if($caja <> ""){
                                        $query_ingresos = "Select sum(monto) as total from movimiento_dinero where estado = 1 AND id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND tipo_origen = 'GAS' AND id_aux = 1 AND fecha_hora between '".$corte."' AND '".$fin_corte."' AND caja = '".$caja."'";
                                    }else{
                                        $query_ingresos = "Select sum(monto) as total from movimiento_dinero where estado = 1 AND id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND tipo_origen = 'GAS' AND id_aux = 1 AND fecha_cierre  = '".$fecha_cierre."'";
                                    }
                                    $resultado_ingresos = $conn->consulta_arreglo($query_ingresos);
                                    ?>
                                    <tr>
                                        <td>INGRESOS ADICIONALES</td>
                                        <td><?php echo $med["nombre"];?></td>
                                        <td><?php echo $med["simbolo"];?> <?php echo round(floatval($resultado_ingresos["total"]),2);?></td>
                                    </tr>
                                    <!--Buscamos Egresos -->
                                    <?php
                                    $query_egresos = "";
                                    if($caja <> ""){
                                        $query_egresos = "Select sum(monto) as total from movimiento_dinero where estado = 1 AND id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND tipo_origen = 'GAS' AND id_aux = 0 AND fecha_hora between '".$corte."' AND '".$fin_corte."' AND caja = '".$caja."'";
                                    }else{
                                        $query_egresos = "Select sum(monto) as total from movimiento_dinero where estado = 1 AND id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND tipo_origen = 'GAS' AND id_aux = 0 AND fecha_cierre  = '".$fecha_cierre."'";
                                    }
                                    $resultado_egresos = $conn->consulta_arreglo($query_egresos);
                                    ?>
                                    <tr>
                                        <td>EGRESOS</td>
                                        <td><?php echo $med["nombre"];?></td>
                                        <td><?php echo $med["simbolo"];?> <?php echo round(floatval($resultado_egresos["total"]),2);?></td>
                                    </tr>
                                    <!--Buscamos Compras -->
                                    <?php
                                    $query_compras = "";
                                    if($caja <> ""){
                                        $query_compras = "Select sum(monto) as total from movimiento_dinero where estado = 1 AND id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND tipo_origen = 'COM' AND fecha_hora between '".$corte."' AND '".$fin_corte."' AND caja = '".$caja."'";
                                    }else{
                                        $query_compras = "Select sum(monto) as total from movimiento_dinero where estado = 1 AND id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND tipo_origen = 'COM' AND fecha_cierre  = '".$fecha_cierre."'";
                                    }
                                    $resultado_compras = $conn->consulta_arreglo($query_compras);
                                    ?>
                                    <tr>
                                        <td>COMPRAS</td>
                                        <td><?php echo $med["nombre"];?></td>
                                        <td><?php echo $med["simbolo"];?> <?php echo round(floatval($resultado_compras["total"]),2);?></td>
                                    </tr>
                                    <!--Total -->
                                    <?php
                                    $query_total = "";
                                    if($caja <> ""){
                                        $query_total = "Select sum(monto) as total from movimiento_dinero where estado = 1 AND id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND fecha_hora between '".$corte."' AND '".$fin_corte."' AND caja = '".$caja."'";
                                    }else{
                                        $query_total = "Select sum(monto) as total from movimiento_dinero where estado = 1 AND id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND fecha_cierre  = '".$fecha_cierre."'";
                                    }
                                    $resultado_total = $conn->consulta_arreglo($query_total);
                                    ?>
                                    
                                    <?php if(intval($med["id_medio"]) === 1):?>
                                        <tr>
                                        <td class="celeste">TOTAL</td>
                                        <td class="celeste"><?php echo $med["nombre"];?></td>
                                        <td class="celeste"><?php echo $med["simbolo"];?> <?php echo round(floatval($resultado_total["total"]),2);?></td>
                                        </tr>                                      
                                    <?php else:?>
                                        <tr>
                                        <td class="verde">TOTAL</td>
                                        <td class="verde"><?php echo $med["nombre"];?></td>
                                        <td class="verde"><?php echo $med["simbolo"];?> <?php echo round(floatval($resultado_total["total"]),2);?></td>
                                        </tr>
                                    <?php endif;?>
                                    
                                <?php endforeach;?>

                            </tbody>

                        </table>
                       
                    </div>
                    
                    <button id="btn-imprimir" type="button" onclick="window.print();" class="btn btn-info hideprint"><i class="fa fa-print"></i> Imprimir Reporte</button>
                    <!--<button class="btn btn-info"><i class="fa fa-print"></i> Imprimir Paloteo</button>
                    <button class="btn btn-info"><i class="fa fa-print"></i> Imprimir Kardex Insumos</button>-->
                </div>
            </div>
                
            </div>
        </div>

        <!--Resumenes Globales-->
        <div class="container-fluid">
            <div class="panel tarjeta">
                <div class="panel-body resumen-caja">
                    <h5>Resumen de Caja</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="panel tarjeta">
                                <div class="panel-body">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Ingresos</th>
                                                <th>Monto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach($monedas as $mon):?>
                                            <?php $total_moneda = 0;?>
                                            <!--Montos Apertura-->
                                            <?php
                                            $query_apertura = "";
                                            if($caja <> ""){
                                                $query_apertura = "Select sum(monto) as total from movimiento_dinero where estado = 1 AND moneda = '".$mon["id"]."' AND fecha_hora between '".$corte."' AND '".$fin_corte."' AND caja = '".$caja."' AND tipo_origen = 'CUT'";
                                            }else{
                                                $query_apertura = "Select sum(monto) as total from movimiento_dinero where estado = 1 AND moneda = '".$mon["id"]."' AND fecha_cierre  = '".$fecha_cierre."' AND tipo_origen = 'CUT'";
                                            }
                                            $resultado_apertura = $conn->consulta_arreglo($query_apertura);
                                            $total_moneda = $total_moneda + floatval($resultado_apertura["total"]);
                                            ?>
                                            <tr>
                                                <td>Apertura</td>
                                                <td><?php echo $mon["simbolo"];?> <?php echo round(floatval($resultado_apertura["total"]),2);?></td>
                                            </tr>
                                            <!--Montos Ventas-->
                                            <?php
                                            $query_venta = "";
                                            if($caja <> ""){
                                                $query_venta = "Select sum(monto) as total from movimiento_dinero where estado = 1 AND moneda = '".$mon["id"]."' AND fecha_hora between '".$corte."' AND '".$fin_corte."' AND caja = '".$caja."' AND tipo_origen = 'PED'";
                                            }else{
                                                $query_venta = "Select sum(monto) as total from movimiento_dinero where estado = 1 AND moneda = '".$mon["id"]."' AND fecha_cierre  = '".$fecha_cierre."' AND tipo_origen = 'PED'";
                                            }
                                            $resultado_venta = $conn->consulta_arreglo($query_venta);
                                            $total_moneda = $total_moneda + floatval($resultado_venta["total"]);
                                            ?>
                                            <tr>
                                                <td>Ventas</td>
                                                <td><?php echo $mon["simbolo"];?> <?php echo round(floatval($resultado_venta["total"]),2);?></td>
                                            </tr>
                                            <!--Montos Compras-->
                                            <?php
                                            $query_compra = "";
                                            if($caja <> ""){
                                                $query_compra = "Select sum(monto) as total from movimiento_dinero where estado = 1 AND moneda = '".$mon["id"]."' AND fecha_hora between '".$corte."' AND '".$fin_corte."' AND caja = '".$caja."' AND tipo_origen = 'COM'";
                                            }else{
                                                $query_compra = "Select sum(monto) as total from movimiento_dinero where estado = 1 AND moneda = '".$mon["id"]."' AND fecha_cierre  = '".$fecha_cierre."' AND tipo_origen = 'COM'";
                                            }
                                            $resultado_compra = $conn->consulta_arreglo($query_compra);
                                            $total_moneda = $total_moneda + floatval($resultado_compra["total"]);
                                            ?>
                                            <tr>
                                                <td>Compras</td>
                                                <td><?php echo $mon["simbolo"];?> <?php echo round(floatval($resultado_compra["total"]),2);?></td>
                                            </tr>
                                            <!--Montos Ingresos-->
                                            <?php
                                            $query_ingreso = "";
                                            if($caja <> ""){
                                                $query_ingreso = "Select sum(monto) as total from movimiento_dinero where estado = 1 AND moneda = '".$mon["id"]."' AND fecha_hora between '".$corte."' AND '".$fin_corte."' AND caja = '".$caja."' AND tipo_origen = 'GAS'";
                                            }else{
                                                $query_ingreso = "Select sum(monto) as total from movimiento_dinero where estado = 1 AND moneda = '".$mon["id"]."' AND fecha_cierre  = '".$fecha_cierre."' AND tipo_origen = 'GAS'";
                                            }
                                            $resultado_ingreso = $conn->consulta_arreglo($query_ingreso);
                                            $total_moneda = $total_moneda + floatval($resultado_ingreso["total"]);
                                            ?>
                                            <tr>
                                                <td>Ingresos Adicionales</td>
                                                <td><?php echo $mon["simbolo"];?> <?php echo round(floatval($resultado_ingreso["total"]),2);?></td>
                                            </tr>
                                            <?php if(intval($mon["id"]) === 1):?>
                                                <tr class="celeste">
                                            <?php else:?>
                                                <tr class="verde">
                                            <?php endif;?>                                           
                                                <td>Total <?php echo $mon["nombre"];?></td>                                              
                                                <td><?php echo $mon["simbolo"];?> <?php echo round(floatval($total_moneda),2);?></td>
                                            </tr>
                                        <?php endforeach;?>
                                    </table>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="panel tarjeta">
                                <div class="panel-body">
                                    <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Egresos</th>
                                                    <th>Monto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach($monedas as $mon):?>
                                                <?php $conteo_gastos = 0;?>
                                                <?php foreach($tipos_gastos as $tipog):?>
                                                    <?php if(intval($tipog["direccion"]) === 0): ?>
                                                    <?php 
                                                        $query_gasto = "";
                                                        if($caja <> ""){
                                                            $query_gasto = "Select sum(monto) as total from movimiento_dinero where estado = 1 AND moneda = '".$mon["id"]."' AND fecha_hora between '".$corte."' AND '".$fin_corte."' AND caja = '".$caja."' AND tipo_origen = 'GAS' AND id_origen = '".$tipog["id"]."'";
                                                        }else{
                                                            $query_gasto = "Select sum(monto) as total from movimiento_dinero where estado = 1 AND moneda = '".$mon["id"]."' AND fecha_cierre  = '".$fecha_cierre."' AND tipo_origen = 'GAS' AND id_origen = '".$tipog["id"]."'";
                                                        }
                                                        $resultado_gasto = $conn->consulta_arreglo($query_gasto);
                                                        $conteo_gastos = $conteo_gastos + floatval($resultado_gasto["total"]);
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $tipog["nombre"];?></td>
                                                        <td><?php echo $mon["simbolo"];?> <?php echo round(floatval($resultado_gasto["total"]),2);?></td>
                                                    </tr>
                                                    <?php endif;?>
                                                <?php endforeach;?>
                                                <?php if(intval($mon["id"]) === 1):?>
                                                    <tr class="celeste">
                                                        <td class="celeste">Total <?php echo $mon["nombre"];?></td>
                                                        <td class="celeste"><?php echo $mon["simbolo"];?> <?php echo round(floatval($conteo_gastos),2);?></td>
                                                    </tr>
                                                <?php else:?>
                                                    <tr class="verde">
                                                        <td class="verde">Total <?php echo $mon["nombre"];?></td>
                                                        <td class="verde"><?php echo $mon["simbolo"];?> <?php echo round(floatval($conteo_gastos),2);?></td>
                                                    </tr>
                                                <?php endif;?>                                                  
                                            <?php endforeach;?>
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="panel tarjeta">
                                <div class="panel-body">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Operacion</th>
                                                <th>Cantidad</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Mesas Atendidas</td>
                                                <td>
                                                <?php 
                                                $query_mesas = "";
                                                if($caja <> ""){
                                                    $query_mesas = "Select count(*) as total from pedido p, accion_caja ac where p.estado = 1 AND p.dateModify between '".$corte."' AND '".$fin_corte."' AND ac.pk_accion = p.pkPediido AND ac.tipo_accion = 'PED' AND ac.caja = '".$caja."'";
                                                }else{
                                                    $query_mesas = "Select count(*) as total from pedido p where estado = 1 AND fechaCierre = '".$fecha_cierre."'";
                                                }
                                                $resultado_mesas = $conn->consulta_arreglo($query_mesas);
                                                echo intval($resultado_mesas["total"]);
                                                ?></td>
                                            </tr>
                                            <tr>
                                                <td>Personas Atendidas</td>
                                                <td><?php 
                                                $query_personas = "";
                                                if($caja <> ""){
                                                    $query_personas = "Select sum(p.npersonas) as total from pedido p, accion_caja ac where p.estado = 1 AND p.dateModify between '".$corte."' AND '".$fin_corte."' AND ac.pk_accion = p.pkPediido AND ac.tipo_accion = 'PED' AND ac.caja = '".$caja."'";
                                                }else{
                                                    $query_personas = "Select count(npersonas) as total from pedido p where estado = 1 AND fechaCierre = '".$fecha_cierre."'";
                                                }
                                                $resultado_personas = $conn->consulta_arreglo($query_personas);
                                                echo intval($resultado_personas["total"]);
                                                ?></td>
                                            </tr>
                                            <tr>
                                                <td>Pedidos Comandados</td>
                                                <td><?php 
                                                $query_comandados = "";
                                                if($caja <> ""){
                                                    $query_comandados = "Select count(*) as total from detallepedido dp, pedido p, accion_caja ac where ac.caja = '".$caja."' AND ac.tipo_accion = 'PED' AND ac.pk_accion = p.pkPediido AND dp.pkPediido = p.pkPediido AND dp.estado = 1 AND dp.horaPedido BETWEEN '".$corte."' AND '".$fin_corte."'";
                                                }else{
                                                    $query_comandados = "Select count(*) as total from detallepedido where estado = 1 AND fechaCierre = '".$fecha_cierre."'";
                                                }
                                                $resultado_comandados = $conn->consulta_arreglo($query_comandados);
                                                echo intval($resultado_comandados["total"]);
                                                ?></td>
                                            </tr>
                                            <tr style="background-color:red;color:#FFF;">
                                                <td>Pedidos Anulados</td>
                                                <td><?php 
                                                $query_anulados = "";
                                                if($caja <> ""){
                                                    $query_anulados = "Select count(*) as total from detallepedido dp, pedido p, accion_caja ac where ac.caja = '".$caja."' AND ac.tipo_accion = 'PED' AND ac.pk_accion = p.pkPediido AND dp.pkPediido = p.pkPediido AND dp.estado = 3 AND dp.horaPedido BETWEEN '".$corte."' AND '".$fin_corte."'";
                                                }else{
                                                    $query_anulados = "Select count(*) as total from detallepedido where estado = 3 AND fechaCierre = '".$fecha_cierre."'";
                                                }
                                                $resultado_anulados = $conn->consulta_arreglo($query_anulados);
                                                echo intval($resultado_anulados["total"]);
                                                ?></td>
                                            </tr>                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="panel tarjeta">
                                <div class="panel-body">
                                <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Otros Movimientos</th>
                                                <th>Cantidad</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Consumo Otorgado</td>
                                                <?php 
                                                $query_consumo = "";
                                                if($caja <> ""){
                                                    $query_consumo = "Select count(*) as numero, sum(p.total) as total from pedido p, accion_caja ac where p.estado = 5 AND p.dateModify BETWEEN '".$corte."' AND '".$fin_corte."' AND ac.pk_accion = p.pkPediido AND ac.tipo_accion = 'PED' AND ac.caja = '".$caja."'";
                                                }else{
                                                    $query_consumo = "Select count(*) as numero, sum(total) as total from pedido where estado = 5 AND fechaCierre = '".$fecha_cierre."'";
                                                }
                                                //echo $query_consumo;
                                                $resultado_consumo = $conn->consulta_arreglo($query_consumo);
                                                echo "<td>".intval($resultado_consumo["numero"])."</td>";
                                                echo "<td>".$monedas[0]["simbolo"]." ".intval($resultado_consumo["total"])."</td>";
                                                ?>
                                            </tr>
                                            <tr>
                                                <td>Credito Otorgado</td>
                                                <?php 
                                                $query_credito = "";
                                                if($caja <> ""){
                                                    $query_credito = "Select count(*) as numero, sum(p.total) as total from pedido p, accion_caja ac where p.estado = 4 AND p.dateModify BETWEEN '".$corte."' AND '".$fin_corte."' AND ac.pk_accion = p.pkPediido AND ac.tipo_accion = 'PED' AND ac.caja = '".$caja."'";
                                                }else{
                                                    $query_credito = "Select count(*) as numero, sum(total) as total from pedido where estado = 4 AND fechaCierre = '".$fecha_cierre."'";
                                                }
                                                //echo $query_credito;
                                                $resultado_credito = $conn->consulta_arreglo($query_credito);
                                                echo "<td>".intval($resultado_credito["numero"])."</td>";
                                                echo "<td>".$monedas[0]["simbolo"]." ".intval($resultado_credito["total"])."</td>";
                                                ?>
                                            </tr>
                                            <tr>
                                                <td>Credito Cobrado</td>
                                                <?php 
                                                $query_cobrado = "";
                                                if($caja <> ""){
                                                    $query_cobrado = "Select count(*) as numero, sum(p.total) as total from pedido p, accion_caja ac, creditos c where p.estado = 1 AND p.dateModify BETWEEN '".$corte."' AND '".$fin_corte."' AND ac.pk_accion = p.pkPediido AND ac.tipo_accion = 'PED' AND ac.caja = '".$caja."' AND c.id_pedido = p.pkPediido";
                                                }else{
                                                    $query_cobrado = "Select count(*) as numero, sum(p.total) as total from pedido p, creditos c where p.estado = 1 AND p.fechaCierre = '".$fecha_cierre."' AND c.id_pedido = p.pkPediido";
                                                }
                                                //echo $query_cobrado;
                                                $resultado_cobrado = $conn->consulta_arreglo($query_cobrado);
                                                echo "<td>".intval($resultado_cobrado["numero"])."</td>";
                                                echo "<td>".$monedas[0]["simbolo"]." ".intval($resultado_cobrado["total"])."</td>";
                                                ?>
                                            </tr>
                                            <tr>
                                                <td>Descuento Otorgado</td>
                                                <?php 
                                                $query_descuento = "";
                                                if($caja <> ""){
                                                    $query_descuento = "Select count(*) as numero, sum(p.descuento) as total from pedido p, accion_caja ac where p.estado <> 3 AND p.descuento > 0 AND p.dateModify BETWEEN '".$corte."' AND '".$fin_corte."' AND ac.pk_accion = p.pkPediido AND ac.tipo_accion = 'PED' AND ac.caja = '".$caja."'";
                                                }else{
                                                    $query_descuento = "Select count(*) as numero, sum(descuento) as total from pedido where estado <> 3 AND descuento > 0 AND fechaCierre = '".$fecha_cierre."'";
                                                }
                                                // echo $query_descuento;
                                                $resultado_descuento = $conn->consulta_arreglo($query_descuento);
                                                echo "<td>".intval($resultado_descuento["numero"])."</td>";
                                                echo "<td>".$monedas[0]["simbolo"]." ".intval($resultado_descuento["total"])."</td>";
                                                ?>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="panel tarjeta">
                                <div class="panel-body">
                                <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Comprobante</th>
                                                <th>Cantidad</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Boletas</td>
                                                <?php 
                                                $query_boletas = "";
                                                if($caja <> ""){
                                                    $query_boletas = "SELECT
                                                        count(c.pkComprobante) AS numero,
                                                        sum(dc.total) AS total
                                                    FROM
                                                        comprobante c,	
                                                        detallecomprobante dc,
                                                        pedido p,
                                                        accion_caja ac
                                                    WHERE
                                                        ac.pk_accion = c.ncomprobante
                                                    AND dc.pkComprobante = c.pkComprobante
                                                    AND p.pkPediido = dc.pkPediido
                                                    AND p.estado <> 3
                                                    AND ac.tipo_accion = 'BOL'
                                                    AND ac.caja = '$caja'
                                                    AND c.pkTipoComprobante = 1
                                                    AND c.fechaImpresion BETWEEN '$corte'
                                                    AND '$fin_corte'";
                                                }else{
                                                    $query_boletas = "SELECT
                                                        count(comprobante.pkComprobante) AS numero,
                                                        sum(detallecomprobante.total) AS total
                                                    FROM
                                                        comprobante
                                                    LEFT JOIN detallecomprobante ON comprobante.pkComprobante = detallecomprobante.pkComprobante
                                                    left join pedido on detallecomprobante.pkPediido = pedido.pkPediido
                                                    WHERE
                                                        comprobante.pkTipoComprobante = 1
                                                    AND comprobante.fecha = '$fecha_cierre'
                                                    and pedido.estado <> 3";
                                                }
                                                // echo $query_boletas;
                                                $resultado_boletas = $conn->consulta_arreglo($query_boletas);
                                                echo "<td>".intval($resultado_boletas["numero"])."</td>";
                                                echo "<td>".$monedas[0]["simbolo"]." ".round($resultado_boletas["total"], 2)."</td>";
                                                ?>
                                            </tr>
                                            <tr>
                                                <td>Facturas</td>
                                                <?php 
                                                $query_facturas = "";
                                                if($caja <> ""){
                                                    $query_facturas = "SELECT
                                                        count(c.pkComprobante) AS numero,
                                                        sum(dc.total) AS total
                                                    FROM
                                                        comprobante c,	
                                                        detallecomprobante dc,
                                                        pedido p,
                                                        accion_caja ac
                                                    WHERE
                                                        ac.pk_accion = c.ncomprobante
                                                    AND dc.pkComprobante = c.pkComprobante
                                                    AND p.pkPediido = dc.pkPediido
                                                    AND p.estado <> 3
                                                    AND ac.tipo_accion = 'FAC'
                                                    AND ac.caja = '$caja'
                                                    AND c.pkTipoComprobante = 2
                                                    AND c.fechaImpresion BETWEEN '$corte'
                                                    AND '$fin_corte'";
                                                }else{
                                                    $query_facturas = "SELECT
                                                        count(comprobante.pkComprobante) AS numero,
                                                        sum(detallecomprobante.total) AS total
                                                    FROM
                                                        comprobante
                                                    LEFT JOIN detallecomprobante ON comprobante.pkComprobante = detallecomprobante.pkComprobante
                                                    left join pedido on detallecomprobante.pkPediido = pedido.pkPediido
                                                    WHERE
                                                        comprobante.pkTipoComprobante = 2
                                                    AND comprobante.fecha = '$fecha_cierre'
                                                    and pedido.estado <> 3";
                                                }
                                                // echo $query_facturas;
                                                $resultado_facturas = $conn->consulta_arreglo($query_facturas);
                                                echo "<td>".intval($resultado_facturas["numero"])."</td>";
                                                echo "<td>".$monedas[0]["simbolo"]." ".round($resultado_facturas["total"], 2)."</td>";
                                                ?>
                                            </tr>
                                            <tr>
                                                <td>Tickets</td>
                                                <?php 
                                                $query_tickets = "";
                                                if($caja <> ""){
                                                    $query_tickets = "Select count(*) as numero, sum(p.total) as total from pedido p, accion_caja ac where ac.pk_accion = p.pkPediido AND ac.tipo_accion = 'PED' AND ac.caja = '".$caja."' AND p.estado = 1 AND p.dateModify BETWEEN '".$corte."' AND '".$fin_corte."' AND NOT EXISTS (SELECT NULL from detallecomprobante dc where dc.pkPediido = p.pkPediido)";
                                                }else{
                                                    $query_tickets = "Select count(*) as numero, sum(p.total) as total from pedido p WHERE p.estado = 1 AND p.fechaCierre = '".$fecha_cierre."' AND NOT EXISTS (SELECT NULL from detallecomprobante dc where dc.pkPediido = p.pkPediido)";
                                                }
                                                // echo $query_tickets;
                                                $resultado_tickets = $conn->consulta_arreglo($query_tickets);
                                                echo "<td>".intval($resultado_tickets["numero"])."</td>";
                                                echo "<td>".$monedas[0]["simbolo"]." ".round($resultado_tickets["total"], 2)."</td>";
                                                ?>
                                            </tr>
                                            
                                        </tbody>
                                        
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="panel tarjeta">
                                <div class="panel-body">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Medio</th>
                                                <th>Propinas Otorgadas</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($medios as $med):?>
                                                <?php
                                                $query_total = "";
                                                if($caja <> ""){
                                                    $query_total = "Select sum(pr.monto) as total from pedido_propina pr, pedido p, accion_caja ac where pr.id_medio = '".$med["id_medio"]."' AND pr.moneda = '".$med["id_moneda"]."' AND pr.pkPediido = p.pkPediido AND p.dateModify BETWEEN '".$corte."' AND '".$fin_corte."' AND ac.pk_accion = p.pkPediido AND ac.tipo_accion = 'PED' AND ac.caja = '".$caja."' ";
                                                }else{
                                                    $query_total = "Select sum(monto) as total from pedido_propina where id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND fecha_cierre = '".$fecha_cierre."'";
                                                }
                                                $resultado_total = $conn->consulta_arreglo($query_total);
                                                ?>
                                                <tr>
                                                    <td><?php echo $med["nombre"];?></td>
                                                    <td><?php echo $med["simbolo"]." ".round(floatval($resultado_total["total"]),2);?></td>
                                                </tr>
                                            <?php endforeach;?>                                            
                                        </tbody>
                                        
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                </div>  
            </div>
        </div>

        <?php
        $query_entregas = "";
        if($caja === ""){
            $query_entregas = "SELECT * FROM detallepedido where estado = 2 and date_format(horaPedido,'%Y-%m-%d') = '".$fecha_cierre."'";
        }else{
            $query_entregas = "SELECT * FROM detallepedido where estado = 2 and horaPedido BETWEEN '".$corte."' AND '".$fin_corte."'";
        }
        $result_entregas = $db->executeQuery($query_entregas);
        if($row_e = $db->fecth_array($result_entregas)):?> 

        <!--Pedido mas lento y mas rapido -->
            <div class="container-fluid">
            <div class="panel tiempos">
                <div class="panel-body movimientos">
                    <h5>Tiempo en Pedidos</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Pedidos mas rápidos</th>
                                        <th>Pedidos mas lentos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                        <?php
                                        $query_rapidos = "";
                                        if($caja === ""){
                                            $query_rapidos = "SELECT *,(select upper(descripcion) from plato pl where pl.pkPlato=d.pkPlato) as pedido, TIMEDIFF(horaTermino, horaPedido) as tiempo, m.nmesa as mesa, sa.nombre as salon,case  when length(pkCocinero)>0 then (select concat(nombres,' ', apellidos) from  trabajador t where t.pkTrabajador=pkCocinero) end cocinero, case when length(pkMozo)>0 then (select concat(nombres,' ', apellidos) from  trabajador t where t.pkTrabajador=pkMozo) end mozo FROM detallepedido d, pedido pe, mesas m, salon sa where estado =2 and date_format(horaPedido,'%Y-%m-%d') = '".$fecha_cierre."' and d.pkPediido = pe.pkPediido and pe.pkMesa = m.pkMesa and m.pkSalon = sa.pkSalon order by TIMEDIFF(horaTermino, horaPedido) ASC LIMIT 10";
                                        }else{
                                            $query_rapidos = "SELECT d.*,(select upper(descripcion) from plato pl where pl.pkPlato=d.pkPlato) as pedido, TIMEDIFF(horaTermino, horaPedido) as tiempo, m.nmesa as mesa, sa.nombre as salon, case  when length(pkCocinero)>0 then (select concat(nombres,' ', apellidos) from  trabajador t where t.pkTrabajador=pkCocinero) end cocinero, case when length(pkMozo)>0 then (select concat(nombres,' ', apellidos) from  trabajador t where t.pkTrabajador=pkMozo) end mozo FROM detallepedido d, pedido p, accion_caja ac, mesas m, salon sa where d.estado =2 and d.horaPedido BETWEEN '".$corte."' AND '".$fin_corte."' AND d.pkPediido = p.pkPediido AND ac.pk_accion = p.pkPediido and p.pkMesa = m.pkMesa and m.pkSalon = sa.pkSalon AND ac.tipo_accion = 'PED' AND ac.caja = '".$caja."' order by TIMEDIFF(horaTermino, horaPedido) ASC LIMIT 10";
                                        }
                                        $result_rapidos = $db->executeQuery($query_rapidos);
                                        while ($row_r = $db->fecth_array($result_rapidos)) {
                                            ?>
                                                <?php echo $row_r['pedido'] ?> X <?php echo $row_r['cantidad'] ?><br/>
                                                Pedido por: <?php echo $row_r['mozo'] ?><br/>
                                                Atendido por: <?php echo $row_r['cocinero'] ?><br/>
                                                Mesa: <?php echo $row_r["mesa"];?> - <?php echo $row_r["salon"];?><br/>
                                                Tiempo : <?php echo $row_r['tiempo'] ?> 
                                                <?php echo "<a class='hideprint' onclick='verDetallesVentas(\"" . $row_r['pkPediido'] . "\")' title='Ver Detalle'><span class='glyphicon glyphicon-log-out'></span></a>";?><br/>
                                                <hr/>
                                            <?php
                                        }
                                        ?>
                                        </td>
                                        <td>
                                        <?php
                                        $query_lentos = "";
                                        if($caja === ""){
                                            $query_lentos = "SELECT *,(select upper(descripcion) from plato pl where pl.pkPlato=d.pkPlato) as pedido, TIMEDIFF(horaTermino, horaPedido) as tiempo, m.nmesa as mesa, sa.nombre as salon, case  when length(pkCocinero)>0 then (select concat(nombres,' ', apellidos) from  trabajador t where t.pkTrabajador=pkCocinero) end cocinero, case when length(pkMozo)>0 then (select concat(nombres,' ', apellidos) from  trabajador t where t.pkTrabajador=pkMozo) end mozo FROM detallepedido d, pedido pe, mesas m, salon sa where estado =2 and date_format(horaPedido,'%Y-%m-%d') = '".$fecha_cierre."' and d.pkPediido = pe.pkPediido and pe.pkMesa = m.pkMesa and m.pkSalon = sa.pkSalon order by TIMEDIFF(horaTermino, horaPedido) DESC LIMIT 10";
                                        }else{
                                            $query_lentos = "SELECT d.*,(select upper(descripcion) from plato pl where pl.pkPlato=d.pkPlato) as pedido, TIMEDIFF(horaTermino, horaPedido) as tiempo, m.nmesa as mesa, sa.nombre as salon, case  when length(pkCocinero)>0 then (select concat(nombres,' ', apellidos) from  trabajador t where t.pkTrabajador=pkCocinero) end cocinero, case when length(pkMozo)>0 then (select concat(nombres,' ', apellidos) from  trabajador t where t.pkTrabajador=pkMozo) end mozo FROM detallepedido d, pedido p, accion_caja ac, mesas m, salon sa where d.estado =2 and d.horaPedido BETWEEN '".$corte."' AND '".$fin_corte."' AND d.pkPediido = p.pkPediido AND ac.pk_accion = p.pkPediido and p.pkMesa = m.pkMesa and m.pkSalon = sa.pkSalon AND ac.tipo_accion = 'PED' AND ac.caja = '".$caja."' order by TIMEDIFF(horaTermino, horaPedido) DESC LIMIT 10";
                                        }
                                        $result_lentos = $db->executeQuery($query_lentos);
                                        while ($row_l = $db->fecth_array($result_lentos)) {
                                            ?>
                                                <?php echo $row_l['pedido'] ?> X <?php echo $row_l['cantidad'] ?><br/>
                                                Pedido por: <?php echo $row_l['mozo'] ?><br/>
                                                Atendido por: <?php echo $row_l['cocinero'] ?><br/>
                                                Mesa: <?php echo $row_l["mesa"];?> - <?php echo $row_l["salon"];?><br/>
                                                Tiempo : <?php echo $row_l['tiempo'] ?> 
                                                <?php echo "<a class='hideprint' onclick='verDetallesVentas(\"" . $row_l['pkPediido'] . "\")' title='Ver Detalle'><span class='glyphicon glyphicon-log-out'></span></a>";?><br/>
                                                <hr/>
                                            <?php
                                        }
                                        ?>
                                        </td>
                                    </tr>
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>  
            </div>
        </div>
        <?php endif;?>

        <!--Ingresos y Salidas Extras -->
        <div class="container-fluid">
            <div class="panel tarjeta">
                <div class="panel-body movimientos">
                    <h5>Movimientos de ingresos y egresos extras</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#MOV</th>
                                        <th>Tipo</th>
                                        <th>Tiempo</th>
                                        <th>Medio</th>
                                        <th>Moneda</th>
                                        <th>Monto</th>
                                        <th>Comentario</th>
                                        <th>Usuario</th>
                                        <th class="hideprint"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $query_movimientos = "";
                                if($caja <> ""){
                                    $query_movimientos = "Select md.*, tg.nombre as tipo_gasto, CONCAT(tr.nombres, ' ', tr.apellidos) as trabajador, mp.nombre as mp_nombre, m.simbolo as m_simbolo from movimiento_dinero md, medio_pago mp, moneda m, tipo_gasto tg, trabajador tr where md.tipo_origen = 'GAS' AND md.fecha_hora BETWEEN '".$corte."' AND '".$fin_corte."' AND md.caja = '".$caja."' AND md.estado = 1 AND md.id_origen = tg.id AND md.id_medio = mp.id AND md.moneda = m.id AND md.id_usuario = tr.pkTrabajador";
                                }else{
                                    $query_movimientos = "Select md.*, tg.nombre as tipo_gasto, CONCAT(tr.nombres, ' ', tr.apellidos) as trabajador, mp.nombre as mp_nombre, m.simbolo as m_simbolo from movimiento_dinero md, medio_pago mp, moneda m, tipo_gasto tg, trabajador tr where md.tipo_origen = 'GAS' AND md.fecha_cierre = '".$fecha_cierre."' AND md.estado = 1 AND md.id_origen = tg.id AND md.id_medio = mp.id AND md.moneda = m.id AND md.id_usuario = tr.pkTrabajador";
                                }
                                $validos = $conn->consulta_matriz($query_movimientos);
                                if(is_array($validos)):
                                    foreach($validos as $val):
                                ?>
                                    <tr>
                                        <td><?php echo $val["id"];?></td>
                                        <td>
                                        <?php 
                                            if(intval($val["id_aux"]) === 1){
                                                echo "<span style='color:green;'>".$val["tipo_gasto"]."</span>";
                                            }else{
                                                echo "<span style='color:red;'>".$val["tipo_gasto"]."</span>";
                                            }
                                        ?>
                                        </td>
                                        <td>
                                        <?php echo $val["fecha_hora"];?>
                                        </td>
                                        <td>
                                        <?php echo $val["mp_nombre"];?>
                                        </td>                            
                                        <td>
                                        <?php echo $val["m_simbolo"];?>
                                        </td>
                                        <td>
                                        <?php echo round(floatval($val["monto"]),2);?>
                                        </td>
                                        <td>
                                        <?php echo $val["comentario"];?>
                                        </td>
                                        <td>
                                        <?php echo $val["trabajador"];?>
                                        </td>
                                        <td class="hideprint">
                                        <?php
                                        echo "<a href='#' onclick='reImprimirPago(\"" . $val['id'] . "\")' title='ReImprimir'><span class='glyphicon glyphicon-print'></span></a>";
                                        ?>
                                        </td>
                                    </tr>
                                <?php
                                    endforeach;
                                endif;
                                ?>
                                </tbody>
                                </table>
                            </div>
                            <a target="_blank" style="float:right;" href="../?controller=Caja&action=RegistrarPago">Ver más en Registro de Ingresos y Salidas <span class="glyphicon glyphicon-new-window" aria-hidden="true"></span></a>
                        </div>
                    </div>
                </div>  
            </div>
        </div>

        <!--Ventas Realizadas -->
        <div class="container-fluid">
            <div class="panel tarjeta">
                <div class="panel-body ventas">
                    <h5>Ventas Realizadas</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <?php $totales_medios = array();?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="hideprint"></th>
                                        <th class="hideprint"></th>
                                        <th>#</th>
                                        <th>Tipo</th>
                                        <th>Serie</th>
                                        <th>Cajero</th>
                                        <th>Salon</th>
                                        <th>Mesa</th>
                                        <th>Caja</th>
                                        <th>Fecha</th>
                                        <th style='text-align: center;'>Inicio</th>
                                        <th style='text-align: center;'>Fin</th>
                                        <th style='text-align: center;'>Tiempo</th>
                                        <th style='text-align: center;'>Medios</th>
                                        <th style='text-align: center;'>SUB-T</th>
                                        <th style='text-align: center;'>DESC</th>
                                        <th style='text-align: center;'>TOTAL</th>
                                        <?php foreach($medios as $med):?>
                                        <?php $totales_medios[$med["id_medio"]."_".$med["id_moneda"]] = 0;?>
                                        <th style='text-align: center;'><?php echo $med["nombre"]." ".$med["simbolo"];?></th>
                                        <?php endforeach;?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $subTotal_f = 0;
                                    $descuento_f = 0;
                                    $total_f = 0;
                                    $efectivo_f = 0;
                                    $tarjeta_f = 0;

                                    $query = "";
                                    
                                    if($caja === ""){
                                        $query = "select p.pkpediido,date(p.fechaCierre) as fecha,p.total, p.descuento, p.pkmesa, p.total_tarjeta, p.total_efectivo, p.nombreTarjeta, t.nombres, t.apellidos, ac.caja, m.nmesa, s.nombre, p.fechaApertura, p.fechaFin from pedido p, accion_caja ac, trabajador t, mesas m, salon s where p.idUser = t.pkTrabajador AND ac.pk_accion = p.pkPediido AND ac.tipo_accion = 'PED' AND p.estado = 1 AND p.pkMesa = m.pkMesa AND m.pkSalon = s.pkSalon and p.fechaCierre = '".$fecha_cierre."' order by p.pkpediido ASC";
                                    }else{
                                        $query = "select p.pkpediido,date(p.fechaCierre) as fecha,p.total, p.descuento, p.pkmesa, p.total_tarjeta, p.total_efectivo, p.nombreTarjeta, t.nombres, t.apellidos, ac.caja, m.nmesa, s.nombre, p.fechaApertura, p.fechaFin from pedido p, accion_caja ac, trabajador t, mesas m, salon s where p.idUser = t.pkTrabajador AND p.estado = 1 and p.dateModify between '".$corte."' and '".$fin_corte."' AND ac.pk_accion = p.pkPediido AND ac.tipo_accion = 'PED' AND ac.caja = '".$caja."' AND p.pkMesa = m.pkMesa AND m.pkSalon = s.pkSalon order by p.pkpediido ASC"; 
                                    }
                                    
                                    //echo $query;
                                    
                                    $result = $conn->consulta_matriz($query);
                                    if(is_array($result)){foreach($result as $row){                               
                                        //Mostramos la tabla
                                        echo "<tr>";
                                        //Opciones para eliminar o reimprimir
                                        echo "<td style='text-align: center' class='hideprint'>";
                                        echo "<a onclick='verDetallesVentas(\"" . $row['pkpediido'] . "\")' title='Ver Detalle'><span class='glyphicon glyphicon-log-out'></span></a>";

                                        echo "</td>";

                                        //Obtenemos data Comprobante
                                        $query_comprobante = "Select c.* from detallecomprobante dc, comprobante c where dc.pkComprobante = c.pkComprobante AND dc.pkPediido = '".$row['pkpediido']."' AND c.estado = 0";
                                        $result_c = $db->executeQuery($query_comprobante);
                                        $existe_comprobante = 0;
                                        while ($row_c = $db->fecth_array($result_c)) {
                                            $tipo_impresion = "";
                                            $existe_comprobante = 1;
                                            $serie = "";

                                            if(intval($row_c["pkTipoComprobante"]) === 1){
                                                $query_serie = "Select * from cloud_config where parametro = 'sboleta'";
                                                $result_serie = $db->executeQuery($query_serie);
                                                while ($row_s = $db->fecth_array($result_serie)) {
                                                    $serie = $row_s["valor"];
                                                }
                                                $tipo_impresion = "BOLETA";
                                            }else{
                                                $tipo_impresion = "FACTURA";
                                                $query_serie = "Select * from cloud_config where parametro = 'sfactura'";
                                                $result_serie = $db->executeQuery($query_serie);
                                                while ($row_s = $db->fecth_array($result_serie)) {
                                                    $serie = $row_s["valor"];
                                                }
                                            }
                                            
                                            echo "<td style='text-align: center' class='hideprint'>";
                                            echo "<a href='#' onclick='openImprimirCuenta(\"" . $row_c["pkComprobante"]. "\",\"" . $tipo_impresion. "\")' title='Re Imprimir'><span class='glyphicon glyphicon-print'></span></a>";
                                            echo "</td>";
                                            
                                            echo "<td style='text-align: center'>";
                                            echo $row['pkpediido'];
                                            echo "</td>";

                                            echo "<td style='text-align: center; color: green; font-weight: bold;'>";
                                            echo $tipo_impresion;
                                            $query_credito = "Select * from creditos where id_pedido = '".$row['pkpediido']."'";
                                            $result_cre = $db->executeQuery($query_credito);
                                            if($row_cre = $db->fecth_array($result_cre)) {
                                                echo "<br/>(DESDE CREDITO ".$row_cre["fecha_credito"].")";
                                            }

                                            echo "</td>";

                                            echo "<td style='text-align: center'>";
                                            echo $serie."-".str_pad($row_c["ncomprobante"], 6, "0", STR_PAD_LEFT);
                                            echo "</td>";
                                        }

                                        if($existe_comprobante === 0){
                                            echo "<td style='text-align: center' class='hideprint'>";
                                            echo "<a href='#' onclick='openImprimirCuenta(\"" . $row['pkpediido']. "\",\"CUENTA\")' title='Re Imprimir'><span class='glyphicon glyphicon-print'></span></a>";
                                            echo "</td>";
                                            
                                            echo "<td style='text-align: center'>";
                                            echo $row['pkpediido'];
                                            echo "</td>";
                                            echo "<td style='text-align: center'>";
                                            echo "TICKET";
                                            $query_credito = "Select * from creditos where id_pedido = '".$row['pkpediido']."'";
                                            $result_cre = $db->executeQuery($query_credito);
                                            if($row_cre = $db->fecth_array($result_cre)) {
                                                echo "<br/>(DESDE CREDITO ".$row_cre["fecha_credito"].")";
                                            }
                                            echo "</td>";
                                            echo "<td style='text-align: center'>";
                                            echo $row['pkpediido'];
                                            echo "</td>";
                                        }

                                        echo "<td>";
                                        echo $row['nombres']." ".$row["apellidos"];
                                        echo "</td>";
                                        echo "<td style='text-align: center'>";
                                        echo $row['nombre'];
                                        echo "</td>";
                                        echo "<td style='text-align: center'>";
                                        echo $row['nmesa'];
                                        echo "</td>";
                                        echo "<td style='text-align: center'>";
                                        echo $row['caja'];
                                        echo "</td>";
                                        echo "<td>";
                                        echo $row['fecha'];
                                        echo "</td>";
                                        echo "<td>";
                                        echo date("h:i A", strtotime($row['fechaApertura']));
                                        echo "</td>";
                                        echo "<td>";
                                        echo date("h:i A", strtotime($row['fechaFin']));
                                        echo "</td>";
                                        echo "<td>";
                                        $apertura = new DateTime($row['fechaApertura']);
                                        $cierre = new DateTime($row['fechaFin']);

                                        $tiempo = $apertura->diff($cierre);

                                        echo $tiempo->format('%H:%I:%S');
                                        //echo $row['fecha'];
                                        echo "</td>";
                                        echo "<td style='text-align: center'>";
                                        echo $row['nombreTarjeta'];
                                        echo "</td>";
                                        echo "<td style='text-align: center'>";
                                        $subTotal_f = $subTotal_f + $row['total'] + $row['descuento'];
                                        echo number_format(floatval($row['total'] + $row['descuento']), 2, '.', ' ');
                                        echo "</td>";

                                        echo "<td style='text-align: center'>";
                                        $descuento_f = $descuento_f + $row['descuento'];
                                        echo number_format(floatval($row['descuento']), 2, '.', ' ');
                                        echo "</td>";
                                        echo "<td style='text-align: center'>";
                                        $total_f = $total_f + $row['total'];
                                        echo number_format(floatval($row['total']), 2, '.', ' ');
                                        echo "</td>";

                                        foreach($medios as $med){
                                            $query_parcial = "Select sum(monto) as parcial from movimiento_dinero where id_origen = '".$row['pkpediido']."' AND tipo_origen = 'PED' AND estado = 1 AND id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."'";
                                            $r_parcial = $db->executeQuery($query_parcial);
                                            if($row_p = $db->fecth_array($r_parcial)){
                                                $totales_medios[$med["id_medio"]."_".$med["id_moneda"]] += floatval($row_p['parcial']);
                                                echo "<td style='text-align: center'>";
                                                echo number_format(floatval($row_p['parcial']), 2, '.', ' ');
                                                echo "</td>";
                                            }
                                        }

                                        echo "</tr>";
                                    }}
                                    ?>
                                    <tr>
                                        <td class="hideprint"></td>
                                        <td class="hideprint"></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td style='text-align: center'><?php
                                        echo number_format($subTotal_f, 2, '.', '');
                                        ?></td>
                                        <td style='text-align: center'><?php
                                        echo number_format($descuento_f, 2, '.', '');
                                        ?></td>
                                        <td style='text-align: center'><?php
                                        echo number_format($total_f, 2, '.', '');
                                        ?></td>
                                        <?php foreach($medios as $med):?>
                                            <td style='text-align: center'><?php
                                            echo number_format($totales_medios[$med["id_medio"]."_".$med["id_moneda"]], 2, '.', '');
                                            ?></td>
                                        <?php endforeach;?>
                                    </tr>
                                </tbody>
                            </table>
                            </div>
                            <a target="_blank" style="float:right;" href="../?controller=Report&action=SaleConsumo">Ver más en Ventas por Tipo <span class="glyphicon glyphicon-new-window" aria-hidden="true"></span></a>
                        </div>
                    </div>
                </div>  
            </div>
        </div>

        <!--Consumos Otorgados -->
        <div class="container-fluid">
            <div class="panel tarjeta">
                <div class="panel-body ventas-creditos">
                    <h5>Consumos Otorgados</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Fecha</th>
                                        <th>Total</th>
                                        <th>Mesa</th>
                                        <th>Mozo</th>
                                        <th class="hideprint"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $suma_total = 0;
                                    $query = "";
                                    if($caja === ""){
                                        $query = "SELECT *,
                                        case when (tipo_cliente = 1 AND character_length(p.documento) > 0 AND character_length(p.documento) < 8) then 
                                        (select nombre from cliente_generico where id=p.documento)
                                        when (tipo_cliente = 1 AND character_length(p.documento) = 8) then 
                                        (select nombres from person where documento =p.documento)
                                        when (tipo_cliente = 2) then
                                        (select razonSocial from persona_juridica where ruc=p.documento ) 
                                        end as cliente FROM pedido p
                                        inner join detallepedido d on p.pkPediido=d.pkPediido
                                        inner join trabajador t
                                        on d.pkMozo=t.pkTrabajador
                                        inner join mesas m on m.pkMesa=p.pkMesa where p.estado=5 and p.fechaCierre = '".$fecha_cierre."' group by p.pkPediido;";
                                    }else{
                                        $query = "SELECT *,
                                        case when (tipo_cliente = 1 AND character_length(p.documento) > 0 AND character_length(p.documento) < 8) then 
                                        (select nombre from cliente_generico where id=p.documento)
                                        when (tipo_cliente = 1 AND character_length(p.documento) = 8) then 
                                        (select nombres from person where documento =p.documento)
                                        when (tipo_cliente = 2) then
                                        (select razonSocial from persona_juridica where ruc=p.documento ) 
                                        end as cliente FROM pedido p
                                        inner join accion_caja ac on ac.pk_accion = p.pkPediido
                                        inner join detallepedido d on p.pkPediido = d.pkPediido
                                        inner join trabajador t
                                        on d.pkMozo=t.pkTrabajador
                                        inner join mesas m on m.pkMesa=p.pkMesa WHERE p.estado=5 AND p.dateModify BETWEEN '".$corte."' AND '".$fin_corte."' AND ac.tipo_accion = 'PED' AND ac.caja = '".$caja."' group by p.pkPediido;";
                                    }
                                        
                                    $result = $db->executeQuery($query);
                                    while ($row = $db->fecth_array($result)) {
                                        echo "<tr>";
                                        echo "<td>";
                                        echo $row['pkPediido'];
                                        echo "</td>";
                                        echo "<td>";
                                        echo utf8_encode($row['cliente']);
                                        echo "</td>";
                                        echo "<td>";
                                        echo $row['fechaApertura'];
                                        echo "</td>";
                                        echo "<td>";
                                        //Consumos no guardan total a la fecha 01/05/2019
                                        //Codigo sera removido luego
                                        //Buscamos los detalles de cada pedido y los sumamos
                                        $total_actual = 0;
                                        $query_total = "Select sum(cantidad*precio) as total from detallepedido where pkPediido = '".$row['pkPediido']."'";
                                        $result_total = $db->executeQuery($query_total);
                                        if($rt = $db->fecth_array($result_total)){
                                            $total_actual = floatval($rt["total"]);
                                        }
                                        echo $total_actual;
                                        $suma_total = $suma_total + $total_actual;
                                        echo "</td>";
                                        echo "<td>";
                                        echo $row['nmesa'];
                                        echo "</td>";
                                        echo "<td>";
                                        echo $row['apellidos'] . ", " . $row['nombres'];
                                        echo "</td>";
                                        echo "<td class='hideprint'>";
                                        echo "<a onclick='verDetallesVentas(\"" . $row['pkPediido'] . "\")' title='Ver Detalle'><span class='glyphicon glyphicon-log-out'></span></a>";
                                        echo "</td>";
                                        ?>
                                        </td>
                                        </tr>
                                    <?php }
                                    ?>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td><b>TOTAL</b></td>
                                        <td><?php echo $suma_total?></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                                </table>
                            </div>
                            <a target="_blank" style="float:right;" href="../?controller=Sale&action=CConsumo">Ver más en Reporte Consumos <span class="glyphicon glyphicon-new-window" aria-hidden="true"></span></a>
                        </div>
                    </div>
                </div>  
            </div>
        </div>

        <!--Creditos Otorgados -->
        <div class="container-fluid">
            <div class="panel tarjeta">
                <div class="panel-body ventas-creditos">
                    <h5>Creditos Otorgados</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Fecha</th>
                                        <th>Total</th>
                                        <th>Mesa</th>
                                        <th>Mozo</th>
                                        <th class="hideprint"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $suma_total = 0;
                                    $query = "";
                                    if($caja === ""){
                                        $query = "SELECT *,
                                        case when (tipo_cliente = 1 AND character_length(p.documento) > 0 AND character_length(p.documento) < 8) then 
                                        (select nombre from cliente_generico where id=p.documento)
                                        when (tipo_cliente = 1 AND character_length(p.documento) = 8) then 
                                        (select nombres from person where documento =p.documento)
                                        when (tipo_cliente = 2) then
                                        (select razonSocial from persona_juridica where ruc=p.documento ) 
                                        end as cliente FROM pedido p
                                        inner join detallepedido d on p.pkPediido=d.pkPediido
                                        inner join trabajador t
                                        on d.pkMozo=t.pkTrabajador
                                        inner join mesas m on m.pkMesa=p.pkMesa where p.estado=4 and p.fechaCierre = '".$fecha_cierre."' group by p.pkPediido;";
                                    }else{
                                        $query = "SELECT *,
                                        case when (tipo_cliente = 1 AND character_length(p.documento) > 0 AND character_length(p.documento) < 8) then 
                                        (select nombre from cliente_generico where id=p.documento)
                                        when (tipo_cliente = 1 AND character_length(p.documento) = 8) then 
                                        (select nombres from person where documento =p.documento)
                                        when (tipo_cliente = 2) then
                                        (select razonSocial from persona_juridica where ruc=p.documento ) 
                                        end as cliente FROM pedido p
                                        inner join accion_caja ac on ac.pk_accion = p.pkPediido
                                        inner join detallepedido d on p.pkPediido = d.pkPediido
                                        inner join trabajador t
                                        on d.pkMozo=t.pkTrabajador
                                        inner join mesas m on m.pkMesa=p.pkMesa WHERE p.estado=4 AND p.dateModify BETWEEN '".$corte."' AND '".$fin_corte."' AND ac.tipo_accion = 'PED' AND ac.caja = '".$caja."' group by p.pkPediido;";
                                    }
                                        
                                    $result = $db->executeQuery($query);
                                    while ($row = $db->fecth_array($result)) {
                                        echo "<tr>";
                                        echo "<td>";
                                        echo $row['pkPediido'];
                                        echo "</td>";
                                        echo "<td>";
                                        echo utf8_encode($row['cliente']);
                                        echo "</td>";
                                        echo "<td>";
                                        echo $row['fechaApertura'];
                                        echo "</td>";
                                        echo "<td>";
                                        //Consumos no guardan total a la fecha 01/05/2019
                                        //Codigo sera removido luego
                                        //Buscamos los detalles de cada pedido y los sumamos
                                        $total_actual = 0;
                                        $query_total = "Select sum(cantidad*precio) as total from detallepedido where pkPediido = '".$row['pkPediido']."'";
                                        $result_total = $db->executeQuery($query_total);
                                        if($rt = $db->fecth_array($result_total)){
                                            $total_actual = floatval($rt["total"]);
                                        }
                                        echo $total_actual;
                                        $suma_total = $suma_total + $total_actual;
                                        echo "</td>";
                                        echo "<td>";
                                        echo $row['nmesa'];
                                        echo "</td>";
                                        echo "<td>";
                                        echo $row['apellidos'] . ", " . $row['nombres'];
                                        echo "</td>";
                                        echo "<td class='hideprint'>";
                                        echo "<a onclick='verDetallesVentas(\"" . $row['pkPediido'] . "\")' title='Ver Detalle'><span class='glyphicon glyphicon-log-out'></span></a>";
                                        echo "</td>";
                                        ?>
                                        </td>
                                        </tr>
                                    <?php }
                                    ?>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td><b>TOTAL</b></td>
                                        <td><?php echo $suma_total?></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                                </table>
                            </div>
                            <a target="_blank" style="float:right;" href="../?controller=Sale&action=CPendientes">Ver más en Cuentas Pendientes <span class="glyphicon glyphicon-new-window" aria-hidden="true"></span></a>
                        </div>
                    </div>
                </div>  
            </div>
        </div>

        <!--Pedidos Anulados -->
        <div class="container-fluid">
            <div class="panel tarjeta">
                <div class="panel-body ventas-creditos">
                    <h5>Pedidos Anulados</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>COD</th>
                                        <th>ID Venta</th>
                                        <th>Cant</th>
                                        <th>Pedido</th>
                                        <th>Fecha Pedido</th>
                                        <th>Fecha Anulación</th>
                                        <th>Total</th>
                                        <th>Motivo</th>
                                        <th>Pedido por</th>
                                        <th>Anulado por</th>
                                    </tr>

                                </thead>
                                <tbody>
                                    <?php
                                    $query = "";
                                    if($caja === ""){
                                        $query = "SELECT *,(select upper(descripcion) from plato pl where pl.pkPlato=d.pkPlato) as pedido, case  when length(pkCocinero)>0 then (select concat(nombres,' ', apellidos) from  trabajador t where t.pkTrabajador=pkCocinero) end cocinero, case when length(pkMozo)>0 then (select concat(nombres,' ', apellidos) from  trabajador t where t.pkTrabajador=pkMozo) end mozo FROM detallepedido d where estado =3 and date_format(fechaPedido,'%Y-%m-%d') = '".$fecha_cierre."'";
                                    }else{
                                        $query = "SELECT d.*,(select upper(descripcion) from plato pl where pl.pkPlato=d.pkPlato) as pedido, case  when length(pkCocinero)>0 then (select concat(nombres,' ', apellidos) from  trabajador t where t.pkTrabajador=pkCocinero) end cocinero, case when length(pkMozo)>0 then (select concat(nombres,' ', apellidos) from  trabajador t where t.pkTrabajador=pkMozo) end mozo FROM detallepedido d, pedido p, accion_caja ac where d.estado =3 and d.fechaPedido BETWEEN '".$corte."' AND '".$fin_corte."' AND d.pkPediido = p.pkPediido AND ac.pk_accion = p.pkPediido AND ac.tipo_accion = 'PED' AND ac.caja = '".$caja."'";
                                    }
                                    
                                    $result = $db->executeQuery($query);
                                    while ($row = $db->fecth_array($result)) {
                                        ?>
                                        <tr>
                                            <td><?php echo $row['pkDetallePedido'] ?></td> 
                                            <td><?php echo $row['pkPediido'] ?></td> 
                                            <td><?php echo $row['cantidad'] ?></td> 
                                            <td><?php echo $row['pedido'] ?></td>
                                            <td><?php echo $row['horaPedido'] ?></td>
                                            <td><?php echo $row['fechaPedido'] ?></td>
                                            <td><?php echo $row['cantidad'] * $row['precio'] ?></td>
                                            <td><?php echo $row['mensaje'];?></td>
                                            <td><?php echo $row['mozo'] ?></td>
                                            <td><?php echo $row['cocinero'] ?></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                            </div>
                            <a target="_blank" style="float:right;" href="../?controller=Report&action=showPedidosAnulados">Ver más en Pedidos Anulados <span class="glyphicon glyphicon-new-window" aria-hidden="true"></span></a>
                        </div>
                    </div>
                </div>  
            </div>
        </div>
        
    </div>
</div>



<?php
    $nombre_tabla = 'caja_final';
    require_once('recursos/componentes/footer.php');
?>
<script>
<?php 
echo "var corte_actual_js = '".$corte."';"; 
?>

$(function() {
    carga_cortes(); 
});

function buscar(){
    var fecha = $("#txtFechaCierreDiario").val();
    var caja = $("#txtCajaAct option:selected").val();
    var corte = $("#txtCorteAct option:selected").val();

    location.href = "?ci="+fecha+"&ca="+caja+"&co="+corte;
}
            
function carga_cortes(){
    var caja = $("#txtCajaAct option:selected").val();
    if(caja === ""){
        $("#div_corte").hide(0);
    }else{
        $("#div_corte").show(0);

        var param = {'fecha': $('#txtFechaCierreDiario').val(), 'caja': caja};
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Caja&&action=ListCortesDia",
            type: 'POST',
            data: param,
            cache: true,
            dataType: 'json',
            success: function(data) {
                if(data != ""){
                    var txt = "";
                    $.each(data, function( key, value ) {
                        if(value == corte_actual_js){
                            txt += "<option value='"+value+"' selected>"+value+"</option>";
                        }else{
                            txt += "<option value='"+value+"'>"+value+"</option>";
                        }                  
                    });
                    $("#txtCorteAct").html(txt);
                }else{
                    alert("No Hubo actividad ese día");
                    var corte_actual = $("#txtCorteAct option:selected").val();
                    var dia = corte_actual.split(" ");
                    $('#txtFechaCierreDiario').val(dia[0]);
                }
            }
        });
    }
}

function reImprimirPago(id_pago){
    var param = {'pkPago': id_pago, 'terminal': '<?php echo $_COOKIE["t"];?>', 'aux' : '<?php echo UserLogin::get_id(); ?>'};
    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=GastosDiarios&&action=ImprimePago",
            type: 'POST',
            data: param,
            cache: false,
            dataType: 'json',
            success: function() {
                //Nada we
            }
    });
}

function verDetallesVentas($pkpedido)
{
    window.open('<?php echo Class_config::get('urlApp') ?>/?controller=Report&action=ShowAdminDetalleVentas&id='+$pkpedido,'_blank');
}   
            
function openImprimirCuenta(idp,tipo) {
    var param = {'pkPedido': idp, 'terminal': '<?php echo $_COOKIE["t"]?>','tipo': tipo, 'aux': '<?php echo $_SESSION['id'];?>,1'};
    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ImprimeCuenta",
            type: 'POST',
            data: param,
            cache: false,
            dataType: 'json',
            success: function() {
                ///Nada
            }
    });
}      
</script>