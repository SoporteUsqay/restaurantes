<?php 
include_once('reportes/recursos/componentes/MasterConexion.php');
$conn = new MasterConexion();
$obj = new Application_Models_CajaModel();
$fechaVar=$obj->fechaCierre();
$fechaInicio = $obj->fechaCierre();
$fechaFin = $obj->fechaCierre();
$fechaCierre=$obj->fechaCierre();
if (isset($_GET['fecha_inicio'])){
    $fechaInicio = $_GET['fecha_inicio'];
    $fechaVar = $fechaInicio;
}

if (isset($_GET['fecha_fin'])){
    $fechaFin = $_GET['fecha_fin'];
}

//Solucion al problema del kardex cuando se deja abierta una mesa varios dias
$tiempo_inicio = null;
$tiempo_fin = null;

$query_corte_min = "Select * from corte where fecha_cierre = '".$fechaInicio."' order by id ASC LIMIT 1";
$result_min = $conn->consulta_arreglo($query_corte_min);
if(is_array($result_min)){
    $tiempo_inicio = $result_min["inicio"];
}else{
    $tiempo_inicio = $fechaFin." 00:00:00";
}

$query_corte_max = "Select * from corte where fecha_cierre = '".$fechaFin."' order by id DESC LIMIT 1";
$result_max = $conn->consulta_arreglo($query_corte_max);
if(is_array($result_max)){
    if($result_max["fin"] !== ""){
        $tiempo_fin = $result_max["fin"];
    }else{
        $tiempo_fin = date("Y-m-d H:i:s");
    } 
}else{
    $tiempo_fin = date("Y-m-d H:i:s");
}
//echo $tiempo_inicio;

$stockAnterior = 0;
$stockIngreso = 0;
$stockSalida = 0;

if($fechaInicio === $fechaFin){
    $titulo_importante = "Kardex Resumen ".$fechaInicio;
}else{
    $titulo_importante = "Kardex Resumen del ".$fechaInicio." al ".$fechaFin;
}

include 'Application/Views/template/header.php';
$objViewMenu = new Application_Views_IndexView();
$objViewMenu->showContent();
?>
<link rel="stylesheet" href="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.css">
<body>
    <style>
        .dt-buttons{
            margin-bottom: 10px !important;
        }
    </style>
    <div class="container">

        <br /><br /><br />
        <h3>Kardex Resumen</h3>
        <div class="panel panel-primary" id='pfecha'>
            <div class="panel-heading">
                <h3 class="panel-title">Filtros por fechas</h3>
            </div>
            <div class="panel-body">

                <div class='control-group' id="dinicio">
                    <label>Fecha Inicio</label>
                    <input id="txtfechaini" type="text" class='form-control' placeholder='AAAA-MM-DD' name='fecha_inicio' value="<?php echo $fechaInicio ?>"/>
                    <label>Fecha Fin</label>
                    <input id="txtfechafin" type="text" class='form-control' placeholder='AAAA-MM-DD' name='fecha_fin' value="<?php echo $fechaFin ?>"/>
                    <div class='control-group'>
                        <label>Tipo</label>    
                        <select class="form-control" id="tipo_plato" multiple="yes">
                            <option value="0">**MOSTRAR TODO**</option>
                            <?php
                            $tipos = $conn->consulta_matriz("Select * from tipos where estado = 0");                            
                            foreach ($tipos as $pl):
                                ?>
                                <option value="<?php echo $pl["pkTipo"]; ?>"><?php echo $pl["descripcion"]; ?></option>
                                <?php
                            endforeach;
                            ?>
                        </select>
                    </div>
                    <br>
                    <button type="button" onclick="buscar()" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Buscar</button>
                    <br>
                </div>
            </div>
        </div>
        <table id="tblKardex" title="Kardex (Resumen)" class="display dataTable no-footer" >
            <thead>
                <tr>
                    <th><center>Descripcion</center></th>
                    <th><center>Stock Inicial</center></th>
                    <th><center>Ingresos</center></th>
                    <th><center>Salidas</center></th>
                    <th><center>Vendido</center></th>
                    <th><center>Stock Final</center></th>
                    <th><center>Unidad</center></th>            
                    <th><center>Detalle</center></th>            
                </tr>
            </thead>

            <tbody>
                <?php
                $db = new SuperDataBase();                
                $query = "SELECT IFNULL((SELECT 0 FROM cierrediario WHERE '$fechaCierre' BETWEEN '$fechaInicio' AND '$fechaFin'),1) AS valor;";
                $result = $db->executeQuery($query);
                $valor=0;
                while ($row = $db->fecth_array($result)) {
                  $valor = $row["valor"];
                }
                
                //Aqui procesamos los insumos
                
                $query_tipo = " ";
                
                $query_todo = "";
                
                if(intval($_GET["tp"])> 0){
                    $query_tipo = " AND (";
                    $tipos = explode(",",$_GET["tp"]);
                    foreach($tipos as $tp){
                        if(intval($tp) === 0){
                            $query_todo = "UNION SELECT DISTINCT * FROM insumos";
                        }else{
                            $query_tipo .= "pl.pktipo = '".$tp."' OR ";
                        }
                    }
                    $query_tipo = substr($query_tipo,0,-4);
                    $query_tipo .= ") ";
                }else{
                    $query_todo = "UNION SELECT DISTINCT * FROM insumos";
                }
                
                
                
                $query = "SELECT DISTINCT i.* FROM insumos i,insumo_menu im, plato pl where im.pkInsumo = i.pkInsumo AND im.pkPlato = pl.pkPlato".$query_tipo.$query_todo." order by descripcioninsumo";
                
                //echo $query;
                
                $result = $db->executeQuery($query);
                while ($row = $db->fecth_array($result)) {
                    $query2 =  "SELECT IFNULL(cantidadInicial,0.0000) AS cantidadInicial, fecha
                               FROM historial_stock_insumos
                               WHERE pkInsumo = " . $row['pkInsumo'] . 
                               " AND fecha BETWEEN '$fechaVar' AND '$fechaFin'
                               ORDER BY fecha LIMIT 1;";
                $result2 = $db->executeQuery($query2);
                while ($row2 = $db->fecth_array($result2)) {                                        
                        $fechaInicio = $row2["fecha"];
                    }
                    ?>
                    <tr>
                        <td>
                            <?php echo utf8_encode($row['descripcionInsumo']) ?>
                        </td>
                        <td style="text-align: right">
                            <?php
                            $queryActual = "SELECT ifnull((select ifnull(cantidadInicial,0.0000) as cantidad FROM historial_stock_insumos h
                                            where fecha between '$fechaInicio' and '$fechaFin' and h.pkInsumo=" .$row['pkInsumo'] . " order by fecha limit 1),0) as cantidad;";
                            $resultAnterior = $db->executeQuery($queryActual);
//                            echo $queryActual;
                            while ($rowSA = $db->fecth_array($resultAnterior)) {
                                ?>
                                <?php 
                                echo number_format(floatval($rowSA['cantidad']), 3, '.', ' ');
                                $stockAnterior = $rowSA['cantidad'];
                                ?><?php
                            }
                            ?>
                        </td>
                        <td style="text-align: right">
                            <?php
                            $queryActual = "SELECT ifnull(sum(cantidad),0.0000) as cantidad FROM ingresoinsumos i where estado=0 AND fecha between '$fechaInicio' and '$fechaFin' and tipo=1 and pkInsumo=" . $row['pkInsumo'];
                            $resultInsumoMenu = $db->executeQuery($queryActual);
//                            echo $queryActual;
                            while ($rowIM = $db->fecth_array($resultInsumoMenu)) {
                                ?>
                                <?php echo number_format(floatval($rowIM['cantidad']), 3, '.', ' ');
                                $stockIngreso = $rowIM['cantidad'];
                                ?><?php
                            }
                            ?>
                        </td>

                        <td style="text-align: right">
                            <?php
                            $queryActual = "SELECT ifnull(sum(cantidad),0.0000) as cantidad FROM ingresoinsumos i where estado=0 AND fecha between '$fechaInicio' and '$fechaFin' and tipo=2 and pkInsumo=" . $row['pkInsumo'];
                            $resultInsumoMenu = $db->executeQuery($queryActual);
                            while ($rowIM = $db->fecth_array($resultInsumoMenu)) {
                                ?>
                                <?php
                                echo number_format(floatval($rowIM['cantidad']), 3, '.', ' ');
                                $stockSalida = $rowIM['cantidad'];
                                ?><?php
                    }
                    ?>
                        </td>
                        <td style="text-align: right">
                            <?php
                            $totalV = 0;
                            //Si la fecha de fin es la de cierre sumamos los pedidos hechos hoy
                            if($valor==0){
                                $phase1 = "Select pkPlato,cantidadTotal from insumo_menu where pkInsumo = '".$row['pkInsumo']."'";
                                //echo $phase1;
                                $resultPhase1 = $db->executeQuery($phase1);
                                while($rowPhase1 = $db->fecth_array($resultPhase1)){
                                    $phase2 = "Select dp.cantidad from detallepedido dp where dp.pkPlato = '".$rowPhase1["pkPlato"]."' AND dp.estado > 0 AND dp.estado < 3 AND dp.horaPedido BETWEEN CAST('".$tiempo_inicio."' as DATETIME) AND CAST('".$tiempo_fin."' as DATETIME)";
                                    //echo $phase2;
                                    $cantidad = floatval($rowPhase1["cantidadTotal"]);
                                    $resultPhase2 = $db->executeQuery($phase2);
                                    while($rowPhase2 = $db->fecth_array($resultPhase2)){
                                        $totalV = $totalV + $cantidad*floatval($rowPhase2["cantidad"]);
                                    }
                                }                                                        
                            }

                            $querTotalV="SELECT IFNULL(SUM(ip.cantidad),0.0000) AS tvendido FROM insumoporpedido ip WHERE ip.pkInsumo=" . $row['pkInsumo']. " AND ip.fecha BETWEEN '$fechaInicio' AND '$fechaFin';";
                            $resultTV = $db->executeQuery($querTotalV);
                            while ($rowTv = $db->fecth_array($resultTV)) {
                                $totalV = $totalV + $rowTv[0];
                            }                       
                            echo number_format(floatval($totalV), 3, '.', ' ');
                            ?>
                        </td>
                        <td style="text-align: right">
                            <?php echo number_format(floatval($stockAnterior + $stockIngreso - $stockSalida - $totalV), 3, '.', ' ');?>
                        </td>
                        <td style="text-align: center">
                            <?php
                            $queryActual = "select u.descripcion from unidad u, insumos i where i.pkunidad=u.pkunidad and i.pkinsumo=" . $row['pkInsumo'];
                            $resultInsumoMenu = $db->executeQuery($queryActual);
                            while ($rowIM = $db->fecth_array($resultInsumoMenu)) {
                                ?>
                                <?php echo $rowIM['descripcion'];
                                ?><?php
                            }
                            ?>
                        </td>
                        <td>
                            <center>
                            <?php 
                                echo "<a onclick='KardexDetallado(" . $row['pkInsumo'] . ",\"" . utf8_encode($row['descripcionInsumo']) . "\")' title='Ver Detalles'> <span class='glyphicon glyphicon-log-out'></span></a>";                               
                            ?>
                            </center>
                        </td>
                    </tr>
<?php }
?>
            </tbody>
        </table>
    </div>        
    <script type="text/javascript" src="Application/Views/Reportes/js/kardex.js.php" ></script>
    <script src="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/dataTables.buttons.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/jszip.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/pdfmake.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/vfs_fonts.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/buttons.html5.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/buttons.print.min.js"></script>
    <script>
    $(document).ready(function() {
        <?php
        if (isset($_GET["tp"])) {
            echo '$("#tipo_plato").val([' . $_GET["tp"] . ']);';
        }else{
            echo '$("#tipo_plato").val(0);';
        }
        ?>

        $("#txtfechaini").datepicker({dateFormat: 'yy-mm-dd'});
        $("#txtfechafin").datepicker({dateFormat: 'yy-mm-dd'});

        $('#tblKardex').DataTable( {
            dom: 'Blfrtip',
            "bSort": false,
            "bFilter": true,
            "bInfo": true,
            "ordering": false,
            "paging": true,
            buttons: [
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6]
                    },
                    title: '<?php echo $titulo_importante;?>'
                },
                {
                    extend: 'pdfHtml5',
                    orientation: 'landscape',
                    alignment: 'center',
                    pageSize: 'LEGAL',
                    customize: function(doc) {
                        doc.content[1].margin = [ 100, 0, 100, 0 ] //left, top, right, bottom
                    },
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6]
                    },
                    title: '<?php echo $titulo_importante;?>'
                },
                {
                    extend: 'print',
                    orientation: 'landscape',
                    pageSize: 'LEGAL',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6]
                    },
                    title: '<?php echo $titulo_importante;?>'
                }
                
            ]
        } );
    });
    </script>
</body>
