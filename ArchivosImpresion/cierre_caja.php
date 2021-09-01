<?php require_once '../Components/Config.inc.php'; ?>
<?php require_once '../Application/Models/CajaModel.php'; ?>
<html lang="en">
    <head>
        <title>c</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="../Public/css/impresion_style.css" />
        <style>
            body,
            table{
                font-family: "Lucida Console", Monaco, monospace;
                line-height: 1.0 !important;             
            }
            
            body{
                font-size: 12px;
                font-weight: 200;
            }
            table{
                font-size: 12px;
                font-weight: 300;
            }
            body{
                zoom: 200%;
            }
        </style>
    </head>
    <body style="margin: 0px !important;">
        <table border="0" width="100%">

        <?php 
        
        echo "<center>";
        $db = new SuperDataBase();
        $objModelCaja = new Application_Models_CajaModel();
        //se ha modificado la impresion fechaApertura por fecha cierre, by Jeanmarco Leon
        //Chancho inutil no sirve ni mierda
        //X2 
        //Impresion modificada en base a las nuevas funcionalidades
        //Todo por Gino Lluen, Agosto 2019 , Jeanmarco Leon solo sirve para chupar pija
       
        //Obtenemos variales por url
        $fecha_cierre = $_REQUEST["fecha"];
        $cajero = $_REQUEST["cajero"];
        $corte = $_REQUEST["corte"];
        $caja = $_REQUEST["caja"];
        $inicial = $_REQUEST["inicial"];

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

        //Obtenemos data corte segun tipo impresion
        $data_corte = null;
        if(intval($inicial) == 1){
            $data_corte = $objModelCaja->_TotalDiaCorte($fecha_cierre,$corte,$caja);
        }else{
            $data_corte = $objModelCaja->_TotalDia($fecha_cierre);
        }
        
        $nsucursal = "select * from sucursal where pkSucursal='SU009'";
        $resultn=$db->executeQuery($nsucursal);
        while($row00=$db->fecth_array($resultn))
        {
            echo "<h1>".$row00['nombreSucursal']."</h1>";
            echo $row00['direccion'];
        }
        //Data Cajero
        $nombre_cajero = "";
        $dcajero = $db->executeQuery("Select * from trabajador where pkTrabajador = '".$_GET["cajero"]."'");
        //Procesamos mysql
        while ($row0 = $db->fecth_array($dcajero)) {
            $nombre_cajero = $row0["nombres"];
        }

        echo "<br><br><b>Cajero: ";
        echo $nombre_cajero;
        echo "</b><br><b>Caja: ";
        echo $caja."</b>";
        echo "<br><b>Fecha: ";
        echo date('Y-m-d h:i:s')."</b>";
        if(intval($inicial) == 1){
        echo "<br><b>Corte: ";
        echo $corte."</b>";
        }
        ?>
        <div class='col-lg-12 col-xs-12'>
            <hr/>
        </div>
        <div class="col-lg-12 col-xs-12" style="font-size: 16px;margin-top: -5px;margin-bottom: 15px;">
            <span class="label label-success">REPORTE CIERRE DE CAJA</span>
        </div>
        <div class='col-lg-12 col-xs-12'>
            <hr/>
        </div>

        <div class="col-lg-12 col-xs-12" style="font-size: 14px;margin-top: -5px;margin-bottom: 15px;">
            <span class="label label-success">TOTALES</span>
        </div>

        <?php foreach($medios as $med):?>
        <div class="form-group col-lg-3">
            <label><?php echo $med["nombre"];?></label>
            <label><?php echo round($data_corte["tot_".$med["id_medio"]."_".$med["id_moneda"]],2);?></label>
        </div>
        <?php endforeach;?>

        <div class="form-group col-lg-3">
            <label><?php echo "DETRACCION S/"?></label>
            <label><?php echo round($data_corte["tot_detraccion"],2);?></label>
        </div>

        <div class='col-lg-12 col-xs-12'>
            <hr/>
        </div>

        <div class="col-lg-12 col-xs-12" style="font-size: 14px;margin-top: -5px;margin-bottom: 15px;">
            <span class="label label-info">INGRESO VENTAS</span>
        </div>

        <?php foreach($medios as $med):?>
        <div class="form-group col-lg-3">
            <label><?php echo $med["nombre"];?></label>
            <label><?php echo round($data_corte["ven_".$med["id_medio"]."_".$med["id_moneda"]],2);?></label>
        </div>
        <?php endforeach;?>

        <div class='col-lg-12 col-xs-12'>
            <hr/>
        </div>

        <div class="col-lg-12 col-xs-12" style="font-size: 14px;margin-top: -5px;margin-bottom: 15px;">
            <span class="label label-danger">RESUMEN COMPRAS</span>
        </div>

        <div class="form-group col-lg-3">
            <label>TOTAL COMPRADO</label>
            <label>S/ <?php echo round($data_corte["comprado"],2);?></label>
        </div>

        <div class='col-lg-12 col-xs-12'>
            <hr/>
        </div>

        <div class="col-lg-12 col-xs-12" style="font-size: 14px;margin-top: -5px;margin-bottom: 15px;">
            <span class="label label-info">PROPINAS</span>
        </div>

        <?php foreach($medios as $med):?>
        <div class="form-group col-lg-3">
            <label><?php echo $med["nombre"];?></label>
            <label><?php echo round($data_corte["prop_".$med["id_medio"]."_".$med["id_moneda"]],2);?></label>
        </div>
        <?php endforeach;?>

        <div class='col-lg-12 col-xs-12'>
            <hr/>
        </div>

        <div class="col-lg-12 col-xs-12" style="font-size: 14px;margin-top: -5px;margin-bottom: 15px;">
            <span class="label label-success">MONTOS INICIALES</span>
        </div>

        <?php foreach($monedas as $mo):?>
            <div class="form-group col-lg-3">
                <label><?php echo $mo["nombre"];?> <?php echo $mo["simbolo"];?></label>
                <label><?php echo $data_corte["ini_".$mo["id"]];?></label>
            </div>
        <?php endforeach;?>

        <div class='col-lg-12 col-xs-12'>
            <hr/>
        </div>

        
        <div class="col-lg-12 col-xs-12" style="font-size: 14px;margin-top: -5px;margin-bottom: 15px;">
            <span class="label label-info">INGRESOS ADICIONALES</span>
        </div>

        <?php foreach($medios as $med):?>
            <div class="form-group col-lg-3">
                <label><?php echo $med["nombre"];?></label>
                <label><?php echo round($data_corte["ing_".$med["id_medio"]."_".$med["id_moneda"]],2);?></label>
            </div>
        <?php endforeach;?>

        <div class='col-lg-12 col-xs-12'>
            <hr/>
        </div>

        <div class="col-lg-12 col-xs-12" style="font-size: 14px;margin-top: -5px;margin-bottom: 15px;">
            <span class="label label-danger">SALIDAS</span>
        </div>

        <?php foreach($medios as $med):?>
        <div class="form-group col-lg-3">
            <label><?php echo $med["nombre"];?></label>
            <label><?php echo round($data_corte["sal_".$med["id_medio"]."_".$med["id_moneda"]],2);?></label>
        </div>
        <?php endforeach;?>

        <div class='col-lg-12 col-xs-12'>
            <hr/>
        </div>

        <div class="col-lg-12 col-xs-12" style="font-size: 14px;margin-top: 5px;margin-bottom: 15px;">
            <span class="label label-success">RESUMEN VENTAS</span>
        </div>

        <div class="form-group col-lg-3">
            <label>TOTAL VENDIDO</label>
            <label><?php echo round($data_corte["vendido"],2);?></label>
        </div>

        <div class="form-group col-lg-3">
            <label>TOTAL CREDITOS</label>
            <label><?php echo round($data_corte["credito"],2);?></label>
        </div>

        <div class="form-group col-lg-3">
            <label>TOTAL CONSUMOS</label>
            <label><?php echo round($data_corte["consumo"],2);?></label>
        </div>

        <div class="form-group col-lg-3">
            <label>TOTAL DESCUENTOS</label>
            <label><?php echo round($data_corte["descuento"],2);?></label>
        </div>

        <div class='col-lg-12 col-xs-12'>
            <hr/>
        </div>

        <?php foreach($tipos_gastos as $tip):?>

            <div class="col-lg-12 col-xs-12" style="font-size: 14px;margin-top: -5px;margin-bottom: 15px;">
                <span class="label label-success">RESUMEN <?php echo strtoupper($tip["nombre"]);?></span>
            </div>

            <?php foreach($medios as $med):?>
                <div class="form-group col-lg-3">
                    <label><?php echo $med["nombre"];?></label>
                    <label><?php echo round($data_corte["tip_".$tip["id"]."_".$med["id_medio"]."_".$med["id_moneda"]],2);?></label>
                </div>
            <?php endforeach;?>

            <div class='col-lg-12 col-xs-12'>
                <hr/>
            </div>
        <?php endforeach;?>
        
        <div class="col-lg-12 col-xs-12" style="font-size: 14px;margin-top: 5px;margin-bottom: 15px;text-align:center;">
            <span class="label label-success">SALIDA DE PLATOS Y/O PRODUCTOS</span>
        </div>

        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th><center>Item</center></th>
                    <th><center>Cantidad</center></th>
                </tr>
            </thead>
            <tbody style="text-align:center;">
                <?php foreach($data_corte["platos"] as $pl):?>
                    <tr><td><?php echo $pl["descripcion"];?></td><td><?php echo $pl["salidas"];?></td></tr>
                <?php endforeach;?>
            </tbody>
        </table>

        <div class='col-lg-12 col-xs-12'>
            <hr/>
        </div>

        <div class="col-lg-12 col-xs-12" style="font-size: 14px;margin-top: 5px;margin-bottom: 15px;text-align:center;">
            <span class="label label-success">MOVIMIENTOS CON MEDIOS ELECTRONICOS</span>
        </div>

        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th><center>ID</center></th>
                    <th><center>OP</center></th>
                    <th><center>MEDIO</center></th>
                    <th><center>MONTO</center></th>
                </tr>
            </thead>
            <tbody style="text-align:center;">
                <?php foreach($data_corte["movimientos"] as $mv):?>
                    <tr>
                        <td><?php echo $mv["id"];?></td>
                        <td><?php echo $mv["op"];?></td>
                        <td><?php echo $mv["medio"];?></td>
                        <td><?php echo $mv["moneda"]." ".$mv["total"];?></td>
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>

        <?php 
        echo "</center>";
        ?>

        <div class='col-lg-12 col-xs-12'>
            <hr/>
        </div>

        <div class="col-lg-12 col-xs-12" style="font-size: 14px;margin-top: 5px;margin-bottom: 15px;text-align:center;">
            <span class="label label-success">Ventas por Crédito / Consumo</span>
        </div>

        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Tipo</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody style="text-align:center;">
                <?php foreach($data_corte["consumo_credito"] as $mv):?>
                    <tr>
                        <td><?php echo $mv["id"];?></td>
                        <td><?php echo $mv["cliente"];?></td>
                        <td><?php echo $mv["tipo"];?></td>
                        <td class="text-right"><?php echo $mv["total"];?></td>
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>

    </body>
</html>