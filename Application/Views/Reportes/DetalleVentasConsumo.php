<?php
include 'Application/Views/template/header.php';
$objViewMenu = new Application_Views_IndexView();
$objViewMenu->showContent();

$fechaInicio = date('Y-m-d');
if (isset($_REQUEST['f1']))
    $fechaInicio = $_REQUEST['f1'];

$fechaFin = date('Y-m-d');
if (isset($_REQUEST['f2']))
    $fechaFin = $_REQUEST['f2'];

$estadoVentas = 0;
if (isset($_REQUEST['estado']))
    $estadoVentas = $_REQUEST['estado'];

$id = "";
if (isset($_REQUEST['id']))
    $id = $_REQUEST['id'];

$nsucursal = "";
if (isset($_REQUEST['nsucursal']))
    $nsucursal = $_REQUEST['nsucursal'];
$total = 0.00;
$descuento = 0.00;
?>
<div class="container">

    <br><br><br><br>
    <input value="<?php echo  $id;?>" id="codigo" name="codigo" style="display: none;">
    <input value="<?php echo $estadoVentas; ?>" id="estadoV" name="estadoV" style="display: none;">
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4><i class="glyphicon glyphicon-list-alt"></i> Ventas Detalladas 
                        <?php
                        if ($estadoVentas == 1 or $estadoVentas == 0) {
                            echo " del Pedido: $id";
                        }
                        if ($estadoVentas == 3) {
                            echo " Anuladas del Pedido: $id";
                        }
                        if ($estadoVentas == 4) {
                            echo " por Credito del Pedido: $id";
                        }
                        if ($estadoVentas == 5) {
                            echo " por Consumo del Pedido: $id";
                        }
                        ?>
                    </h4>
                </div>
                <div class="panel-body">

                    <div class="tab-content">
                        <div class="tab-pane active"> 
                            <br>
                            <table id="tblVentasDetalladas" name="tblVentasDetalladas" class="table table-borderer">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Fecha</th>
                                        <th>Cliente</th>
                                        <th>Mozo</th>
                                        <th>Mesa</th>
                                        <th>Precio</th>
                                        <th>Cantidad</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $db = new SuperDataBase();
                                    $query = "select descuento as descu,dp.pkdetallepedido, pl.descripcion,date(p.fechaApertura) as fecha,
                                    case when (tipo_cliente = 1 AND character_length(p.documento) > 0 AND character_length(p.documento) < 8) then 
                                    (select nombre from cliente_generico where id=p.documento)
                                    when (tipo_cliente = 1 AND character_length(p.documento) = 8) then 
                                    (select nombres from person where documento =p.documento)
                                    when (tipo_cliente = 2) then
                                    (select razonSocial from persona_juridica where ruc=p.documento ) 
                                    end as cliente,CONCAT(t.apellidos,', ',t.nombres) as mozo,nmesa,dp.precio,dp.cantidad,dp.precio*dp.cantidad as total from pedido p
                                    inner join mesas m on p.pkmesa=m.pkmesa inner join detallepedido dp on p.pkpediido=dp.pkpediido AND dp.estado > 0 AND dp.estado < 3 inner join plato pl on dp.pkplato=pl.pkplato
                                    inner join trabajador t on dp.pkMozo=t.pktrabajador
                                    where p.pkPediido=$id";
                                    $result = $db->executeQuery($query);
                                    while ($row = $db->fecth_array($result)) {
                                        echo "<tr>";
                                        echo "<td>";
                                        echo $row['descripcion'];
                                        echo "</td>";
                                        echo "<td>";
                                        echo $row['fecha'];
                                        echo "</td>";
                                        echo "<td>";
                                        echo $row['cliente'];
                                        echo "</td>";
                                        echo "<td>";
                                        echo $row['mozo'];
                                        echo "</td>";
                                        echo "<td>";
                                        echo $row['nmesa'];
                                        echo "</td>";
                                        echo "<td>";
                                        echo $row['precio'];
                                        echo "</td>";
                                        echo "<td>";
                                        echo $row['cantidad'];
                                        echo "</td>";
                                        echo "<td>";
                                        echo $row['precio'] * $row['cantidad'];
                                        $total = $total + $row['total'];
                                        $descuento = $row['descu'];
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-primary">
        <div class="panel-heading"><h4>Resumen Soles</h4></div>
        <div class="panel-body">
            <table class="table table-borderer">
                <tr>
                    <td>Sub - Total</td>
                    <td>
                        <label><?php echo $total ?></label> 
                    </td>
                    <td>Descuento</td>
                    <td>
                        <label><?php echo $descuento ?></label>  
                    </td>
                    <td>Total Final</td>
                    <td>
                        <label><?php echo $total - $descuento ?></label>
                    </td>
                </tr>
            </table>

            <br>

                <?php

                    $query = "select * from motivo_venta where pkPediido = $id";

                    $res = $db->executeQueryEx($query);

                    $i = 0;
                    while($row = $db->fecth_array($res)) {

                        if ($i == 0) {
                            echo '<h4>Comentarios:</h4>';
                        }
                        
                        echo '<h6>'. $row['motivo'] .'</h6>';
                    }
                ?>
        </div>
    </div>
</div>
<script>
    function exportarPDFVentasDetalles() {
        var url = "<?php echo Class_config::get('urlApp') ?>/pdf_detalle_ventas.php?sucursal=" + $("#lblsucursal").val() + "&Estado_Venta=" + $("#estadoV").val() + "&id=" +$("#codigo").val();
        window.open(url, '_blank');
    }

    function exportarExcelVentasDetalles() {
        var url = "<?php echo Class_config::get('urlApp') ?>/xls_detalle_ventas.php?sucursal=" + $("#lblsucursal").val() + "&Estado_Venta=" + $("#estadoV").val() + "&id=" +$("#codigo").val();
        window.open(url, '_blank');
    }

</script>