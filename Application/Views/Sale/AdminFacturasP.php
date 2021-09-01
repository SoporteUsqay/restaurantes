<?php
    $fechaInicio = date('Y-m-d');
    if (isset($_REQUEST['fecha_inicio'])){
        $fechaInicio = $_REQUEST['fecha_inicio'];
    }

    $fechaFin = date('Y-m-d');
    if (isset($_REQUEST['fecha_fin'])){
        $fechaFin = $_REQUEST['fecha_fin'];
    }
    
    $titulo_importante = "Consolidado de Facturas ";
    if($fechaInicio === $fechaFin){
        $titulo_importante .= $fechaInicio;
    }else{
        $titulo_importante .= "del ".$fechaInicio." al ".$fechaFin;
    }
    
    include 'Application/Views/template/header.php';
    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showContent();
?>
<body>
<link rel="stylesheet" href="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.css">
    <style>
        .dt-buttons{
            margin-bottom: 10px !important;
        }
        .usqay-bdy{
            margin-left: 2% !important;
            margin-right: 2% !important;
        }
    </style>
    <br><br><br><br>
    <div class="row usqay-bdy">            
        <form id="frmFiltroFacturas">
            <div class="panel panel-primary" id='pfecha'>
                <div class="panel-heading">
                    Filtros por fechas
                </div>
                <div class="panel-body row">
                    <div class='control-group col-md-5' id="dinicio">
                        <label>Fecha Inicio</label>
                        <input type="date" class='form-control date' placeholder='AAAA-MM-DD' id='fecha_inicio' name='fecha_inicio' value="<?php
                        echo $fechaInicio
                        ?>"/>
                    </div>
                    <div class='control-group col-md-5' id="ffin">
                        <label>Fecha Fin</label>
                        <input type="date" class='form-control date' placeholder='AAAA-MM-DD' id='fecha_fin' name='fecha_fin' value="<?php
                        echo $fechaFin
                        ?>"/>
                    </div>
                    <div class='control-group col-md-2'>
                        <label> </label>
                        <p>
                            <button type='button' class='btn btn-primary' onclick='busquedaFacturas()'>Filtrar</button>
                        </p>
                    </div>
                </div>
            </div>                    
        </form>
        <div class="row">
            <div class="col-lg-12" style="overflow-x: scroll !important;"> 
                <table id="tbl_factura" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th class="text-center">SERIE</th>
                            <th class="text-center">NUMERO</th>
                            <th class="text-center">TIPO</th>
                            <th class="text-center">FECHA</th>
                            <th class="text-center">RUC</th>
                            <th class="text-center">CLIENTE</th>
                            <th class="text-center">DESC</th>
                            <th class="text-center">GRAV</th>
                            <th class="text-center">INAF</th>
                            <th class="text-center">EXON</th>
                            <th class="text-center">GRAT</th>
                            <th class="text-center">ICBPER</th>
                            <th class="text-center">IGV</th>
                            <th class="text-center">TOTAL</th>
                            <th class="text-center">MONEDA</th>
                            <th class="text-center">MEDIO</th>
                            <th class="text-center">ESTADO</th>
                            <th class="text-center">ACEPTADA</th>
                            <th class="text-center">RESPUESTA</th>                    
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $db = new SuperDataBase();
                        
                        //Totales
                        $gravada_t = 0;
                        $igv_t = 0;
                        $inafecta_t = 0;
                        $exonerada_t = 0;
                        $descuento_t = 0;
                        $gratuita_t = 0;
                        $icbper_t = 0;
                        $final_t = 0;

                        //Obtenemos series
                        $serie_factura = "";
                        $query_s = "Select * from cloud_config where parametro = 'sfactura'";
                        $result_s = $db->executeQuery($query_s);
                        if($row_s = $db->fecth_array($result_s)){
                            $serie_factura = $row_s["valor"];
                        }
                    
                        $sucursal = UserLogin::get_pkSucursal();
                        $query = "SELECT c.pkComprobante, c.ncomprobante, c.fecha, c.tipo_pago, c.nombreTarjeta, c.estado AS pkEstado, ".
                        "CASE c.estado WHEN 0 THEN 'EMITIDO' WHEN 3 THEN 'ANULADO' END AS estado, " .
                        "CASE WHEN (character_length(c.ruc) = 11) THEN " .
                        "(SELECT c.ruc) " .
                        "WHEN (character_length(c.documento) = 8) THEN " .
                        "(SELECT c.documento) " .
                        "END AS documento, " .
                        "CASE WHEN (character_length(c.ruc) = 11) THEN " .
                        "(SELECT RazonSocial FROM persona_juridica pj WHERE pj.ruc = c.ruc) " .
                        "WHEN (character_length(c.documento) = 8) THEN " .
                        "(SELECT CONCAT(Nombres ,' ', lastName) FROM person p WHERE p.documento = c.documento) " .
                        "END AS cliente " .
                        "FROM comprobante c " .
                        "WHERE c.fecha  BETWEEN '$fechaInicio' AND '$fechaFin' AND " .
                        "c.pkSucursal = '$sucursal' AND " .
                        "c.pkTipoComprobante = 2 " .
                        "GROUP BY pkComprobante";

                        $result = $db->executeQuery($query);
                        while ($row = $db->fecth_array($result)) {
                            //Obtenemos respuesta
                            $qr = "Select * from comprobante_hash where pkComprobante = '".$row["pkComprobante"]."'";
                            $rh = $db->executeQuery($qr);
                            $aceptada = "";
                            $respuesta = "";
                            if($rwh = $db->fecth_array($rh)){
                                $aceptada = $rwh["aceptada"];
                                $respuesta = $rwh["respuesta_sunat"];
                            }
                            
                            $gravada_ = null;
                            $igv_ = null;
                            $inafecta_ = null;
                            $exonerada_ = null;
                            $descuento_ = null;
                            $gratuita_ = null;
                            $icbper_ = null;
                            $final_ = null;

                            //Obtenemos totales
                            $qi = "Select * from comprobante_impuestos where pkComprobante = '".$row["pkComprobante"]."'";
                            $ri = $db->executeQuery($qi);
                            if($rwi = $db->fecth_array($ri)){
                                $gravada_ = $rwi["total_gravada"];
                                $igv_ = $rwi["total_igv"];
                                $inafecta_ = $rwi["total_inafecta"];
                                $exonerada_ = $rwi["total_exonerada"];
                                $descuento_ = $rwi["total_descuento"];
                                $gratuita_ = $rwi["total_gratuita"];
                                $icbper_ = $rwi["total_icbper"];
                                $final_ = $rwi["total_final"];

                                $gravada_t += floatval($rwi["total_gravada"]);
                                $igv_t += floatval($rwi["total_igv"]);
                                $inafecta_t += floatval($rwi["total_inafecta"]);
                                $exonerada_t += floatval($rwi["total_exonerada"]);
                                $descuento_t += floatval($rwi["total_descuento"]);
                                $gratuita_t += floatval($rwi["total_gratuita"]);
                                $icbper_t += floatval($rwi["total_icbper"]);
                                $final_t += floatval($rwi["total_final"]);
                            }


                            //Tranajamos Data
                            $style = "";

                            if (intval($row['pkEstado']) == 3) {
                                $style = "background-color:orangered !important;";
                                $grilla = "btn disabled";
                            } else {
                                $grilla = "btn";
                            }

                            echo "<tr style='".$style."'>";
                            echo "<td class='text-center'>";
                            echo "<a class='btn' title='Re Imprimir' onclick='imprimirFactura(\"" . $row['pkComprobante'] . "\")'><span class='glyphicon glyphicon-print'></span></a>";
                            echo "</td>";
                            echo "<td class='text-center'>";
                            echo "<a class='$grilla' title='Anular' onclick='anularFactura(\"" . $row['pkComprobante'] . "\")'><span class='glyphicon glyphicon-remove-circle'></span></a>";
                            echo "</td>";
                            echo "<td class='text-center'>" . $serie_factura . "</td>";
                            echo "<td class='text-center'>" . str_pad($row['ncomprobante'], 8, "0", STR_PAD_LEFT) . "</td>";
                            echo "<td class='text-center'>01</td>";
                            echo "<td class='text-center'>" . $row['fecha'] . "</td>";
                            echo "<td class='text-center'>" . $row['documento'] . "</td>";
                            echo "<td>" . $row['cliente'] . "</td>";
                            echo "<td class='text-center'>".number_format($descuento_,2)."</td>";
                            echo "<td class='text-center'>".number_format($gravada_,2)."</td>";
                            echo "<td class='text-center'>".number_format($inafecta_,2)."</td>";
                            echo "<td class='text-center'>".number_format($exonerada_,2)."</td>";
                            echo "<td class='text-center'>".number_format($gratuita_,2)."</td>";
                            echo "<td class='text-center'>".number_format($icbper_,2)."</td>";
                            echo "<td class='text-center'>".number_format($igv_,2)."</td>";
                            echo "<td class='text-center'>".number_format($final_,2)."</td>";
                            echo "<td class='text-center'>PEN</td>";
                            echo "<td class='text-center'>" . $row['nombreTarjeta'] . "</td>";
                            echo "<td class='text-center'>" . $row['estado'] . "</td>";
                            echo "<td class='text-center'>" . $aceptada . "</td>";
                            echo "<td class='text-center'>" . $respuesta . "</td>"; 
                            echo "</tr>";
                        }
                        ?>
                        <?php 
                        echo "<tr>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td class='text-center'>".number_format($descuento_t,2)."</td>";
                        echo "<td class='text-center'>".number_format($gravada_t,2)."</td>";
                        echo "<td class='text-center'>".number_format($inafecta_t,2)."</td>";
                        echo "<td class='text-center'>".number_format($exonerada_t,2)."</td>";
                        echo "<td class='text-center'>".number_format($gratuita_t,2)."</td>";
                        echo "<td class='text-center'>".number_format($icbper_t,2)."</td>";
                        echo "<td class='text-center'>".number_format($igv_t,2)."</td>";
                        echo "<td class='text-center'>".number_format($final_t,2)."</td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "</tr>";
                        ?>                                     
                    </tbody>
                </table>
            </div>
        </div>

        <br><br>

        <!-- Modal que solicita la confirmación para anular factura -->
        <div id="mdl_anular_factura" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><label id="title_mdl_factura"></label></h4>
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
        
        <!--Inicio Modal-->
        <div class='modal fade' id='modal_envio_anim' tabindex='-1' role='dialog' data-keyboard="false" data-backdrop="static" style="z-index: 999999 !important;">
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h4 class='modal-title' id='myModalLabel'>Procesando</h4>
                    </div>
                    <div class='modal-body'>
                        <center>
                            <img src="Public/images/pacman.gif">
                        </center>
                    </div>
                </div>
            </div>
        </div>
        <!--Fin Modal-->

    </div>
</body>
<script src="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.js"></script>
<script src="reportes/recursos/js/plugins/tablas/dataTables.buttons.min.js"></script>
<script src="reportes/recursos/js/plugins/tablas/jszip.min.js"></script>
<script src="reportes/recursos/js/plugins/tablas/pdfmake.min.js"></script>
<script src="reportes/recursos/js/plugins/tablas/vfs_fonts.js"></script>
<script src="reportes/recursos/js/plugins/tablas/buttons.html5.min.js"></script>
<script src="reportes/recursos/js/plugins/tablas/buttons.print.min.js"></script>
<script>
    $(document).ready(function() {
        $('#tbl_factura').DataTable( {
            dom: 'Blfrtip',
            "bSort" : false,
            buttons: [
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: [ 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20]
                    },
                    title: '<?php echo $titulo_importante;?>'
                },
                {
                    extend: 'pdfHtml5',
                    orientation: 'landscape',
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
                    exportOptions: {
                        columns: [ 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20]
                    },
                    title: '<?php echo $titulo_importante;?>'
                },
                {
                    extend: 'print',
                    orientation: 'landscape',
                    pageSize: 'LEGAL',
                    exportOptions: {
                        columns: [ 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20]
                    },
                    title: '<?php echo $titulo_importante;?>'
                }
                
            ]
        } );
    } );
    
    function imprimirFactura($codComprobante) {        
        var consumo = 1;
        var param2 = {'pkPedido': $codComprobante, 'terminal': '<?php echo $_COOKIE['t'] ?>', 'tipo': 'FACTURA', 'aux': '<?php echo UserLogin::get_id();?>,'+consumo};
    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ImprimeCuenta",
            type: 'POST',
            data: param2,
            success: function() {
                //Matar a Jeanmarco
            }
        });
    }
    
    function anularFactura($codComprobante) {
        $('#mdl_anular_factura').modal('show');
        $('#title_mdl_factura').html('Anular factura');
        $('#txt_mensaje').html('¿Seguro que quieres anular esta factura?');
        $('#txt_cod_comprobante').val($codComprobante);
        $('#btnColor').removeClass();
        $('#btnColor').addClass('btn btn-danger');
        url = "<?php echo class_config::get('urlApp') ?>/?controller=Sale&action=DeleteComprobante";
    }
    
    function deleteTipo() {
        $('#mdl_anular_factura').modal('hide');
        $('#modal_envio_anim').modal('show');
        $.post(url, {codComprobante: $('#txt_cod_comprobante').val(), tipoComprobante: 2},
        function(data) {
            location.reload();
        });       
    }
    
    function busquedaFacturas(){
        window.location.href="<?php echo Class_config::get('urlApp') ?>/?controller=Sale&action=ShowFactura&" +$('#frmFiltroFacturas').serialize();    
    }
</script>