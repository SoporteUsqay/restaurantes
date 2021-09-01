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
            
            body,
            table{
                font-size: 14px;
                font-weight: 500;
            }
            body{
                zoom: 200%;
            }
        </style>
    </head>
    <!--<body style="font-size: 12px">-->
        <body>
        <table border="0" width="100%">

            <?php
            $db = new SuperDataBase();
            
            $estado = null;
            $cantidad = null;
            $descripcion = null;
            $medio = null;
            $simbolo = null;
            $fecha = null;
            $direccion = null;
            //Obtenemos datos pago
            $result20 = $db->executeQuery("select tg.nombre as tipo, me.nombre as medio, mo.simbolo as moneda, md.monto, md.comentario, md.fecha_hora, md.id_aux from movimiento_dinero md, medio_pago me, moneda mo, tipo_gasto tg where md.id = '".$_GET["id"]."' AND md.id_medio = me.id AND md.moneda = mo.id AND md.id_origen = tg.id");
            while ($row4 = $db->fecth_array($result20)) {
                $estado = $row4["tipo"];
                $cantidad = $row4["monto"];
                $descripcion = $row4["comentario"];
                $medio = $row4["medio"];
                $simbolo = $row4["moneda"];
                $fecha = $row4["fecha_hora"];

                if(intval($row4["id_aux"]) === 1){
                    $direccion = "un INGRESO";
                }else{
                    $direccion = "una SALIDA";
                }
            }

            
            echo "<center>";
            echo"<h1>".$estado."</h1>";
            echo "<hr/>";
            echo "</center>";

            
            $query4 = "select * from sucursal where nombreSucursal='SU009'";
            $nombre_cajero = "";
            //Obtenemos la data del cajero
            $dcajero = $db->executeQuery("Select * from trabajador where pkTrabajador = '".$_GET["cajero"]."'");
            //Procesamos mysql
            while ($row0 = $db->fecth_array($dcajero)) {
                $nombre_cajero = $row0["nombres"];
            }
            
            $result10 = $db->executeQuery($query4);
            while ($row3 = $db->fecth_array($result10)) {
                echo utf8_encode($row3["nombreSucursal"]);
                echo "<br>";
                echo utf8_encode($row3['direccion']);
            }
                                 
            /* CCAJEROOOOO */
            echo "<br><br>Impreso Por: ";
            echo $nombre_cajero;

            echo "<br>Fecha y Hora de Emision: ";
            echo $fecha;
            echo "<br><br/>";
            echo "<center>Se ha hecho ".$direccion." de ".$simbolo." " . abs(floatval($cantidad)) . " en ".$medio." por concepto de : <br>";
            echo $descripcion."</center>";
           
            ?>
        </table>
        <p></p>
        <hr/>  
        <br/>
        Nombres:<br/>
        <hr/>
        Direccion:<br/>
        <hr/>
        Firma:<br/>
        <hr/>
    </body>
</html>