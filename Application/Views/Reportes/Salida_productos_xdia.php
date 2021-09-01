<?php
error_reporting(E_ALL);
$inicio = date("Y-m-d");
$fin = date("Y-m-d");
$tipo = 0;
$top = 5;

if(isset($_GET["i"])){
    $inicio = $_GET["i"];
}

if(isset($_GET["f"])){
    $fin = $_GET["f"];
}

if(isset($_GET["ti"])){
    $tipo = intval($_GET["ti"]);
}

if(isset($_GET["to"])){
    $top = intval($_GET["to"]);
}

$titulo_importante = "Platos con mayor rotacion del ".$inicio." al ".$fin;

include 'Application/Views/template/header.php';

include_once('reportes/recursos/componentes/MasterConexion.php');
$conn = new MasterConexion();

//Obtenemos moneda nacional
$query_moneda = "Select * from moneda where id = 1";
$r_moneda = $conn->consulta_arreglo($query_moneda);
?>
<body>

    <?php
    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showContent();
    $db = new SuperDataBase();
    $query = "";
    ?>    
    <div class="container">

        <br>
        <br>
        <br> 

        <div class="panel panel-success">
            <div class="panel-body panel-success row">

                <div class="form-group col-md-3">
                    <label for="">Inicio</label>
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                        <input name="inicio" aria-describedby="basic-addon1" class="form-control" id="inicio" value="<?php echo $inicio; ?>">
                    </div>
                </div>

                <div class="form-group col-md-3">
                    <label for="">Fin</label>
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                        <input name="fin" aria-describedby="basic-addon1" class="form-control" id="fin" value="<?php echo $fin; ?>">
                    </div>
                </div>

                <div class="form-group col-md-3">
                    <label for="">Tipo</label>
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1">
                            <span class="glyphicon glyphicon-th-list"></span>
                        </span>
                        <select name="tipo" aria-describedby="basic-addon1" class="form-control" id="tipo">
                            <option value="0">--TODOS--</option>
                            <?php 
                            $rt = $conn->consulta_matriz("Select * from tipos where estado = 0");
                            foreach ($rt as $row) {
                                if(intval($row["pkTipo"]) === $tipo){
                                    echo '<option value="'.$row["pkTipo"].'" selected>'.$row["descripcion"].'</option>';
                                }else{
                                    echo '<option value="'.$row["pkTipo"].'">'.$row["descripcion"].'</option>';
                                }
                            }
                            ?>                                         
                        </select>
                    </div>
                </div>

                <div class="form-group col-md-3">
                    <label for="">Top</label>
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1">
                            <span class="glyphicon glyphicon-sort-by-attributes"></span>
                        </span>
                        <select aria-describedby="basic-addon1" class="form-control" id="top" name="top">
                            <option value="5" <?php if($top === 5){echo "selected";}?>>5</option>
                            <option value="10" <?php if($top === 10){echo "selected";}?>>10</option>
                            <option value="20" <?php if($top === 20){echo "selected";}?>>20</option>
                            <option value="30" <?php if($top === 30){echo "selected";}?>>30</option>
                        </select>
                    </div>
                </div>

                <div class="form-group col-md-6">
                    <label for="">Tipo Grafico</label>
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1">
                            <span class="glyphicon glyphicon-signal"></span>
                        </span>
                        <select aria-describedby="basic-addon1" class="form-control" id="grafico" name="grafico">
                            <option value="1">Grafico Circular</option>
                            <option value="2">Grafico Barras 3D</option>
                            <option value="3">Grafico Lineal</option>   
                        </select>
                    </div>
                </div> 

                <div class="form-group col-md-3">
                    <center>
                        <label for=""><br/></label>
                        <button class="btn btn-primary form-control" onclick="filtrar()"><span class="glyphicon glyphicon-search"></span> Buscar</button>
                    </center>
                </div>

                <div class="form-group col-md-3">
                    <center>
                        <label for=""><br/></label>
                        <button class="btn btn-success form-control" onclick="grafica()"><span class="glyphicon glyphicon-print"></span> Generar Grafica</button>
                    </center>
                </div>   

                

            </div>
        </div>
                  
        <!--FIN filtro-->
            <div class="row-border">
                <div class="col-lg-12">                   
                    <div class="tab-content">
                        <div class="tab-pane active" id="Ventas">
                            <table class='tb display' cellspacing='0' width='100%' border="0">
                                <thead>
                                    <tr>
                                        <th>Plato</th>
                                        <th class='text-right'>Precio Unitario (<?php echo $r_moneda["simbolo"];?>)</th>
                                        <th class='text-right'>Vendido</th>
                                        <th class='text-right'>Por Cobrar</th>
                                        <th class='text-right'>Total Vendido (<?php echo $r_moneda["simbolo"];?>)</th>
                                        <th class='text-right'>Total Por Cobrar (<?php echo $r_moneda["simbolo"];?>)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php

                                $dcI = $conn->consulta_arreglo("Select * from corte where fecha_cierre = '".$inicio."'  order by inicio asc limit 1");

                                $ffecha = "";

                                if (!is_array($dcI)) {
                                    $ffecha = " AND dp.horaPedido BETWEEN '" .$inicio . "' AND '" . $fin . "'";
                                } else {
                                    $dcF = $conn->consulta_arreglo("Select * from corte where fecha_cierre = '".$fin."' order by inicio desc limit 1");
    
                                    if ($dcF['fin']) {
                                        $ffecha = " AND dp.horaPedido BETWEEN '" .$dcI['inicio'] . "' AND '" . $dcF['fin'] . "'";
                                    } else {
                                        $ffecha = " AND dp.horaPedido >= '" .$dcI['inicio']."'";
                                    }
                                }


                                // echo $ffecha;
                                
                                $query_platos = "";

                                $total_vendido = 0;
                                $total_cobrar = 0;
                                $total_total = 0;

                                $query_platos = "Select sum(if(p.estado != 0, dp.cantidad, 0)) as vendido, sum(if(p.estado = 0, dp.cantidad, 0)) as cobrar, sum(dp.cantidad*dp.precio) as total, dp.pkPlato, pl.descripcion, pl.precio_venta 
                                    from plato pl, detallepedido dp, pedido p
                                    where dp.pkPlato = pl.pkPlato AND dp.pkPediido = p.pkPediido AND dp.estado > 0 AND dp.estado < 3 and p.estado != 3";
                                
                                if ($tipo > 0) {
                                    $query_platos .= " AND pl.pktipo = '".$tipo."' ";
                                }   

                                $query_platos .= "$ffecha GROUP BY dp.pkPlato ORDER BY total desc, cantidad desc LIMIT ".$top;

                                // if($tipo > 0){
                                //     $query_platos = "Select sum(dp.cantidad) as vendido, sum(dp.cantidad*dp.precio) as total, dp.pkPlato, pl.descripcion, pl.precio_venta from plato pl, detallepedido dp where dp.pkPlato = pl.pkPlato AND dp.estado > 0 AND dp.estado < 3 $ffecha GROUP BY dp.pkPlato ORDER BY total desc, cantidad desc LIMIT ".$top;
                                // }else{
                                //     $query_platos = "Select sum(dp.cantidad) as vendido, sum(dp.cantidad*dp.precio) as total, dp.pkPlato, pl.descripcion, pl.precio_venta from plato pl, detallepedido dp where dp.pkPlato = pl.pkPlato AND dp.estado > 0 AND dp.estado < 3 $ffecha GROUP BY dp.pkPlato ORDER BY total desc, cantidad desc LIMIT ".$top;
                                // }
                                // echo $query_platos;
                                // die(0);
                                $resultado_top = $conn->consulta_matriz($query_platos);
                                if(is_array($resultado_top)){
                                    foreach($resultado_top as $t){
                                        echo "<tr>";
                                        echo "<td>".$t["descripcion"]."</td>";
                                        echo "<td class='text-right'>".round(floatval($t["precio_venta"]),2)."</td>";
                                        echo "<td class='text-right'>".intval($t["vendido"])."</td>";
                                        echo "<td class='text-right'>".intval($t["cobrar"])."</td>";
                                        echo "<td class='text-right'>".(intval($t["vendido"])*round(floatval($t["precio_venta"]),2))."</td>";
                                        echo "<td class='text-right'>".(intval($t["cobrar"])*round(floatval($t["precio_venta"]),2))."</td>";
                                        echo "</tr>";
    
                                        $total_vendido = $total_vendido + intval($t["vendido"]);
                                        $total_cobrar = $total_cobrar + intval($t["cobrar"]);
                                        $total_total = $total_total + (intval($t["vendido"])*round(floatval($t["precio_venta"]),2)) + (intval($t["cobrar"])*round(floatval($t["precio_venta"]),2));
                                    }

                                    echo "<tr>";
                                    echo "<td></td>";
                                    echo "<td></td>";
                                    echo "<td class='text-right'>".$total_vendido."</td>";
                                    echo "<td class='text-right'>".$total_cobrar."</td>";
                                    echo "<td class='text-right'>"."TOTAL"."</td>";
                                    echo "<td class='text-right'>".$total_total."</td>";
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

    <script src="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/dataTables.buttons.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/jszip.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/pdfmake.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/vfs_fonts.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/buttons.html5.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/buttons.print.min.js"></script>
    <script>

        function grafica() {
            if($('#grafico').val()==1){
                var url = "<?php echo Class_config::get('urlApp') ?>/ReportesGraficos/PiesBasic.php?fechaInicio="+$("#inicio").val() + "&fechaFin=" + $("#fin").val() + "&tipoClase=" + $("#tipo").val() + "&top=" + $("#top").val() ; 
                window.open(url, '_blank');
            }
            else
               if($('#grafico').val()==2){
                var url = "<?php echo Class_config::get('urlApp') ?>/ReportesGraficos/GraficoBarras.php?fechaInicio="+$("#inicio").val() + "&fechaFin=" + $("#fin").val() + "&tipoClase=" + $("#tipo").val() + "&top=" + $("#top").val() ; 
                window.open(url, '_blank');
            }
            else
                if($('#grafico').val()==3){
                var url = "<?php echo Class_config::get('urlApp') ?>/ReportesGraficos/GraficaLineal.php?fechaInicio="+$("#inicio").val() + "&fechaFin=" + $("#fin").val() + "&tipoClase=" + $("#tipo").val() + "&top=" + $("#top").val() ; 
                window.open(url, '_blank');
            }
        }

        $("#inicio").datepicker({dateFormat: 'yy-mm-dd'});
        $("#fin").datepicker({dateFormat: 'yy-mm-dd'});

        function filtrar() {
            location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Report&action=SProductosDia&i="+$("#inicio").val()+"&f="+$("#fin").val()+"&ti="+$("#tipo").val()+"&to="+$("#top").val();
        }

        $('.tb').DataTable( {
            dom: 'Blfrtip',
            "bSort": false,
            "bFilter": false,
            "bInfo": false,
            "ordering": false,
            "paging": false,
            buttons: [
            {
                extend: 'excelHtml5',
                title: '<?php echo $titulo_importante;?>'
            },
            {
                extend: 'pdfHtml5',
                orientation: 'portrait',
                alignment: 'center',
                pageSize: 'LEGAL',
                customize: function(doc) {
                    doc.defaultStyle.alignment = 'center';
                    var objLayout = {};
                    objLayout['hLineWidth'] = function(i) { return .5; };
                    objLayout['vLineWidth'] = function(i) { return .5; };
                    objLayout['hLineColor'] = function(i) { return '#aaa'; };
                    objLayout['vLineColor'] = function(i) { return '#aaa'; };
                    objLayout['paddingLeft'] = function(i) { return 4; };
                    objLayout['paddingRight'] = function(i) { return 4; };
                    doc.content[1].layout = objLayout;
                },
                title: '<?php echo $titulo_importante;?>'
            },
            {
                extend: 'print',
                orientation: 'portrait',
                pageSize: 'LEGAL',
                title: '<?php echo $titulo_importante;?>'
            }
            ]
        } );

    </script>
