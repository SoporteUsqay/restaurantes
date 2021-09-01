<?php require_once '../Components/Config.inc.php'; ?>
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
       
        //Obtenemos variales por url
        $id = $_REQUEST["id"];

        $query = "SELECT
            m.id,
            m.fecha,
            m.tipo_comprobante_id,
            m.numero_comprobante,
            m.tipo,
            tc.descripcion AS tipo_comprobante,
            a.id AS almacen_id,
            a.nombre AS almacen,
            concat(p.ruc, ' - ', p.razon) AS proveedor,
            concat(
                tr.apellidos,
                ' ',
                tr.nombres
            ) AS trabajador
        FROM
            n_movimiento_almacen m
        LEFT JOIN tipocomprobante tc ON m.tipo_comprobante_id = tc.pkTipoComprobante
        LEFT JOIN n_almacen a ON m.almacen_id = a.id
        LEFT JOIN provedor p ON m.proveedor_id = p.pkProvedor
        LEFT JOIN trabajador tr ON m.trabajador_id = tr.pkTrabajador
        WHERE
            m.id = $id";

        $res = $db->executeQueryEx($query);
        
        $guia = [];
        
        while($row = $db->fecth_array($res))
        {
            $guia = $row;
        }

        $query = "SELECT
            m.precio,
            m.cantidad,
            i.descripcionInsumo AS insumo,
            u.descripcion AS unidad,
            a.nombre AS almacen,
            ip.cantidad as cantidad_porcion,
            u1.descripcion as nombre_unidad,
            ip.descripcion
        FROM
            n_detalle_movimiento_almacen m
        LEFT JOIN insumos i ON m.insumo_id = i.pkInsumo
        LEFT JOIN insumo_porcion ip ON m.insumo_porcion_id = ip.id
        LEFT JOIN unidad u ON m.unidad_id = u.pkUnidad
        LEFT JOIN unidad u1 ON ip.unidad_id = u1.pkUnidad
        LEFT JOIN n_almacen a ON m.almacen_id = a.id
        WHERE
            m.movimiento_id = $id";

        $res = $db->executeQueryEx($query);
        
        $detalles = [];
        
        while($row = $db->fecth_array($res))
        {
            $detalles[] = $row;
        }

        // echo "<br><br><b>Cajero: ";
        // echo $nombre_cajero;
        // echo "</b><br><b>Caja: ";
        // echo $caja."</b>";
        echo "<br><b>Fecha: ";
        echo date('Y-m-d h:i:s')."</b>";
        ?>
        <div class='col-lg-12 col-xs-12'>
            <hr/>
        </div>
        
        <div class="col-lg-12 col-xs-12" style="font-size: 14px;margin-top: 5px;margin-bottom: 15px;text-align:center;">
            <span class="label label-success"><?php echo ($guia['tipo'] == 1) ? 'INGRESO' : 'SALIDA' ?></span>
            <br>
            <span class="label label-success"><?php echo $guia['numero_comprobante'] ?></span>
        </div>

        <table class="table table-striped table-bordered" style="width: 100%">
            <thead>
                <tr>
                    <th style="text-align: left; border-bottom: 1px solid;">Item</th>
                    <th style="text-align: right; border-bottom: 1px solid;">Cantidad</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($detalles as $dt):?>
                    <tr>
                        <td width="70%" style="text-align: left; border-bottom: 1px solid;"><?php echo $dt["insumo"] . implode(' ', [
                                    floatval($row['cantidad_porcion']) == 0 ? '' : floatval($row['cantidad_porcion']),
                                    ($row['nombre_unidad']),
                                    ($row['descripcion'])]) ;?></td>
                        <td width="30%" style="text-align: right; border-bottom: 1px solid;"><?php echo floatval($dt["cantidad"]);?></td>
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>


        <?php 
        echo "</center>";
        ?>

    </body>
</html>