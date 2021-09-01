<?php include 'Application/Views/template/header.php'; ?>

<body>
    <?php
    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showContent();
    ?>

    <br><br><br><br>
    <div class="container">            
        <form id="frmFiltroBoletas">
            <div class="panel panel-primary" id='pfecha'>
                <div class="panel-heading">
                    Filtros por fechas
                </div>
                <div class="panel-body">

                    <div class='control-group' id="dinicio">
                        <label>Fecha Inicio</label>
                        <input class='form-control date' placeholder='AAAA-MM-DD' id='fecha_inicio' name='fecha_inicio'/>
                    </div>
                    <div class='control-group' id="ffin">
                        <label>Fecha Fin</label>
                        <input class='form-control date' placeholder='AAAA-MM-DD' id='fecha_fin' name='fecha_fin'/>
                    </div>

                    <div class='control-group' id="ano" style="display: none;">
                        <label>Año</label>
                        <input class='form-control' placeholder='AAAA' id='ano_busqueda' name='ano_busqueda' value="2015" type="number"/>
                    </div>
                    <div class='control-group'>        
                        <br/>
                        <button type='button' class='btn btn-success' onclick='imprimirBoletas()' style="float:right; margin-right:5px">Imprimir</button>
                        <br/>
                    </div>
                </div>
            </div>                    
        </form>
        <div class="row">
            <div class="col-lg-12">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#boleta" data-toggle="tab">Boletas</a></li>
                    <li><a href="#detalle" data-toggle="tab">Detalle</a></li>
                </ul>
                <br>
                <div class="tab-content">
                    <div class="tab-pane active" id="boleta" >  
                        <table id="tbl_boleta" title="Boletas" class="table table-borderer">
                            <thead>
                                <tr>
                                    <th>Active</th>
                                    <th class="text-center">N° Comprobante</th>
                                    <th>Documento</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>T. Pago</th>
                                    <th>Tarjeta</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $db = new SuperDataBase();
                                $sucursal = UserLogin::get_pkSucursal();
                                $query = "SELECT c.pkComprobante, c.ncomprobante, c.pkTipoComprobante, t.descripcion, IFNULL (c.total, 0.00) AS total, " .
                                        "c.estado AS pkEstado, " .
                                        "CASE c.estado WHEN 0 THEN 'EMITIDO' WHEN 3 THEN 'ANULADO' END AS estado, " .
                                        "CASE c.tipo_pago WHEN 1 THEN 'EFECTIVO' WHEN 2 THEN 'TARJETA' END AS tipo_pago, c.fecha, c.nombreTarjeta " .
                                        "FROM comprobante c, tipocomprobante t " .
                                        "WHERE c.pkTipoComprobante = t.pkTipoComprobante AND " .
                                        "c.pkTipoComprobante = '1' AND " .
                                        "c.pkSucursal = '$sucursal' AND " .
                                        "c.estado IS NOT NULL;";
                                $result = $db->executeQuery($query);
                                while ($row = $db->fecth_array($result)) {
                                    $class = "";
                                    if ($row['pkEstado'] == "3") {
                                        $class = "danger";
                                        $grilla = "btn disabled";
                                    } else {
                                        $grilla = "btn";
                                    }
                                    echo "<tr class='$class'>";
                                    echo "<td>";
                                     echo "<input type='checkbox' class='editor-active'>";
                                    echo "</td>";
                                    echo "<td>";
                                    echo $row['ncomprobante'];
                                    echo "</td>";
                                    echo "<td>";
                                    echo utf8_encode($row['descripcion']);
                                    echo "</td>";
                                    echo "<td>";
                                    echo $row['fecha'];
                                    echo "</td>";
                                    echo "<td>";
                                    echo $row['total'];
                                    echo "</td>";
                                    echo "<td>";
                                    echo $row['estado'];
                                    echo "</td>";
                                    echo "<td>";
                                    echo $row['tipo_pago'];
                                    echo "</td>";
                                    echo "<td>";
                                    echo $row['nombreTarjeta'];
                                    echo "</td>";
//                                    echo "<td>";
//                                    echo "<a id='cod_comprobante' class='btn' onclick='detalleBoleta(\"" . 'SU0091000000116' . "\")'><span class='glyphicon glyphicon-print'>Detalle</span></a>";
//                                    echo "</td>";
                                    echo "<td>";
                                    echo "<a class='btn' onclick='imprimirBoleta(\"" . $row['pkComprobante'] . "\")'><span class='glyphicon glyphicon-print'></span></a>";
                                    echo "</td>";
                                    echo "<td>";
                                    echo "<a class='$grilla' onclick='anularBoleta(\"" . $row['pkComprobante'] . "\")'><span class='glyphicon glyphicon-remove-circle'></span></a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                ?>                                
                            </tbody>
                        </table>
                    </div>

                    <div class="tab-pane" id="detalle">
                        <table id="tbl_detalle" class="table table-borderer">
                            <thead>
                                <tr>
                                    <th>Pedido</th>
                                    <th>Precio</th>
                                    <th>Cantidad</th>
                                    <th>Importe</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $db = new SuperDataBase();
                                $query = "SELECT d.pkPediido , c.ncomprobante, c.dateModify, d.pkDetallePedido, d.cantidad, d.precio, " .
                                        "ROUND((d.cantidad*d.precio),2) AS importe, pr.descripcion AS pedido " .
                                        "FROM comprobante c, detalle_comprobante2 dc, detallepedido d, pedido p, productos pr " .
                                        "WHERE p.pkPediido = d.pkPediido AND " .
                                        "d.pkProducto = pr.pkProducto AND " .
                                        "c.pkComprobante = dc.pkDetalleComprobante AND " .
                                        "d.pkDetallePedido = dc.pkDetallePedido AND " .
                                        "c.pkComprobante = 'SU0091000000116' " .
                                        "UNION " .
                                        "SELECT d.pkPediido , c.ncomprobante, c.dateModify, d.pkDetallePedido, d.cantidad, d.precio, " .
                                        "ROUND((d.cantidad*d.precio),2) AS importe, pl.descripcion AS pedido " .
                                        "FROM comprobante c, detalle_comprobante2 dc, detallepedido d, pedido p, plato pl " .
                                        "WHERE p.pkPediido = d.pkPediido AND " .
                                        "d.pkPlato = pl.pkPlato AND " .
                                        "c.pkComprobante = dc.pkDetalleComprobante AND " .
                                        "d.pkDetallePedido = dc.pkDetallePedido AND " .
                                        "c.pkComprobante = 'SU0091000000116'";

                                $result = $db->executeQuery($query);
                                while ($row = $db->fecth_array($result)) {
                                    echo "<tr>";
                                    echo "<td>";
                                    echo $row['pedido'];
                                    echo "</td>";
                                    echo "<td>";
                                    echo utf8_encode($row['precio']);
                                    echo "</td>";
                                    echo "<td>";
                                    echo $row['cantidad'];
                                    echo "</td>";
                                    echo "<td>";
                                    echo $row['importe'];
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

        <br><br>

        <!-- Modal que solicita la confirmación para anular boleta -->
        <div id="mdl_anular_boleta" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><label id="title_mdl_boleta"></label></h4>
                    </div>
                    <div class="modal-body">
                        <form id="formTipo2">                        
                            <input name="id" id="txt_cod_comprobante" style="display: none;"/>
                            <p id="txt_mensaje"></p>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button id="btnColor" class="btn btn-primary" onclick="deleteTipo()">Aceptar</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>
                    </div>
                </div>
            </div>
        </div> 

    </div>
</body>

<script>
                            date('date');
                            $('#tbl_boleta').DataTable();
                            $('#tbl_detalle').DataTable();                            

                            function imprimirBoleta($codComprobante) {
                                var url = "<?php echo Class_config::get('urlApp') ?>/impresionBoleta.php?codComprobante=" + $codComprobante;
                                var myWindow = window.open(url, "", 'width=1,height=1');
                                myWindow.focus();
                            }
                            
                            function imprimirBoletas() {
                                var url ="";
                                
                                <?php
                                $db2 = new SuperDataBase();
                                $sucursal = UserLogin::get_pkSucursal();
                                $query2 = "SELECT c.pkComprobante, c.ncomprobante, c.pkTipoComprobante, t.descripcion, IFNULL (c.total, 0.00) AS total, " .
                                        "c.estado AS pkEstado, " .
                                        "CASE c.estado WHEN 0 THEN 'EMITIDO' WHEN 3 THEN 'ANULADO' END AS estado, " .
                                        "CASE c.tipo_pago WHEN 1 THEN 'EFECTIVO' WHEN 2 THEN 'TARJETA' END AS tipo_pago, c.fecha, c.nombreTarjeta " .
                                        "FROM comprobante c, tipocomprobante t " .
                                        "WHERE c.pkTipoComprobante = t.pkTipoComprobante AND " .
                                        "c.pkTipoComprobante = '1' AND " .
                                        "c.pkSucursal = '$sucursal' AND " .
                                        "c.estado IS NOT NULL and fecha between '2015-08-14' and '2015-08-14';";
                                $result2 = $db2->executeQuery($query2);
                                while ($row2 = $db2->fecth_array($result2)) {
                                    $cod = $row2['pkComprobante'];
                                    ?>
                                    var url = "<?php echo Class_config::get('urlApp') ?>/impresionBoleta.php?codComprobante=<?php echo $cod; ?>";
                                    var myWindow = window.open(url, "", 'width=1,height=1');
                                myWindow.focus();
                               <?php }
                               ?>                                
                            }                                                       
                            
                            function anularBoleta($codComprobante) {
                                $('#mdl_anular_boleta').modal('show');
                                $('#title_mdl_boleta').html('Anular boleta');
                                $('#txt_mensaje').html('¿Seguro que quieres anular esta boleta?');
                                $('#txt_cod_comprobante').val($codComprobante);
                                $('#btnColor').removeClass();
                                $('#btnColor').addClass('btn btn-danger');
                                url = "<?php echo class_config::get('urlApp') ?>/?controller=Sale&action=DeleteComprobante";
                            }

                            function deleteTipo() {
                                $.post(url, {codComprobante: $('#txt_cod_comprobante').val(), tipoComprobante: 1},
                                function(data) {
                                    location.reload();
                                });
                                $('#mdl_anular_boleta').modal('hide');
                            }
</script>