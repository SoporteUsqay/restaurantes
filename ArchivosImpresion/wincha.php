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
        //Impresion de Wincha
       
        //Obtenemos variales por url
        $fecha_cierre = $_REQUEST["fecha"];
        $cajero = $_REQUEST["cajero"];
        $corte = $_REQUEST["corte"];
        $caja = $_REQUEST["caja"];
        $inicial = $_REQUEST["inicial"];


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
        ?>
        <div class='col-lg-12 col-xs-12'>
            <hr/>
        </div>
        
        <div class="col-lg-12 col-xs-12" style="font-size: 14px;margin-top: 5px;margin-bottom: 15px;text-align:center;">
            <span class="label label-success">SALIDA DE PLATOS Y/O PRODUCTOS</span>
        </div>

        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th><center>Item</center></th>
                    <th><center>Cantidad</center></th>
                    <th><center>Cobrar</center></th>
                    <th><center>Total</center></th>
                </tr>
            </thead>
            <tbody style="text-align:center;">
                <?php foreach($data_corte["platos"] as $pl):?>
                    <tr>
                        <td><?php echo $pl["descripcion"];?></td>
                        <td><?php echo $pl["salidas"];?></td>
                        <td><?php echo $pl["cobrar"];?></td>
                        <td><?php echo $pl["total"];?></td>
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>


        <?php 
        echo "</center>";
        ?>

    </body>
</html>