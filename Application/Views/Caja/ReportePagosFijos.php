<?php 
$fechaInicio = date('Y-m-d', strtotime('-1 month'));
if (isset($_REQUEST['fecha_inicio'])){
    $fechaInicio = $_REQUEST['fecha_inicio'];
}

$fechaFin = date('Y-m-d');
if (isset($_REQUEST['fecha_fin'])){
    $fechaFin = $_REQUEST['fecha_fin'];
}

$titulo_importante = "Reporte de pagos fijos";

if($fechaInicio == $fechaFin){
    $titulo_importante = $titulo_importante." del ".$fechaInicio;
}else{
    $titulo_importante = $titulo_importante." del ".$fechaInicio." al ".$fechaFin;
}

include 'Application/Views/template/header.php'; ?>
<body>

    <?php
    $db = new SuperDataBase();
    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showContent();
    $obj = new Application_Models_CajaModel();
    
    //Caja
    $caja = $_COOKIE["c"];
    if (isset($_REQUEST['caja'])){
        $caja = $_REQUEST["caja"];
    }
    
    //Nivel de Usuario
    $estilo_nivel = "style='display:none;'";
    if(intval($_COOKIE["TYP"]) > 0 &&  intval($_COOKIE["TYP"]) < 3){
        $estilo_nivel = "";
    }
    ?>
    <link rel="stylesheet" href="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.css">
    <style>
        .dt-buttons{
            margin-bottom: 10px !important;
        }
    </style>    
    <div class="container">

        <br /><br/><br/>
        <h2>Reporte de Pagos Fijos</h2>
        <form id="frmFiltroGastosDiarios">
            <div class="panel panel-primary" id='pfecha'>
                <div class="panel-heading">
                    Filtros por fechas
                </div>
                <div class="panel-body">

                    <div class='control-group col-lg-4' id="dinicio">
                        <label>Fecha Inicio</label>
                        <input class='form-control date' placeholder='AAAA-MM-DD' id='fecha_inicio' name='fecha_inicio' value="<?php echo $fechaInicio ?>"/>
                    </div>
                    <div class='control-group  col-lg-4' id="ffin">
                        <label>Fecha Fin</label>
                        <input class='form-control date' placeholder='AAAA-MM-DD' id='fecha_fin' name='fecha_fin' value="<?php echo $fechaFin ?>"/>
                    </div>
                    <div class='control-group col-lg-3' <?php echo $estilo_nivel;?>>
                        <label>Caja</label>
                        <select name="caja" class="form-control" id="caja" onclick="muestra_botones();">
                            <option value=''>Todas</option>
                            <?php
                            $rc = $db->executeQuery("Select * from cajas");
                            while($cajas = $db->fecth_array($rc)){
                                if($cajas["caja"] === $caja){
                                        echo "<option value='".$cajas["caja"]."' selected>".$cajas["caja"]."</option>";
                                    }else{
                                        echo "<option value='".$cajas["caja"]."'>".$cajas["caja"]."</option>";
                                    }
                            }
                            ?>
                        </select>
                    </div>

                    <div class='control-group col-lg-1'>
                        <br/>
                        <button type='button' class='btn btn-primary' onclick='buscarPagosFijos()'>Buscar</button>
                        <br/>
                    </div>
                    <div class="control-group col-lg-12" style="text-align: right;">
                        <br/>
                        <button id="btn_excel" style='display: none;' type='button' class='btn btn-success' onclick='exportarExcelPagosFijos()' style="float:right; margin-right:5px">Exportar a Excel</button>
                        <button id="btn_pdf" style='display: none;' type='button' class='btn btn-danger' onclick='exportarPDFPagosFijos()' style="float:right; margin-right:5px">Exportar a PDF</button>
                    </div>
                </div>
            </div>
        </form>
        <table class="table">
            <thead>
                <tr>
                    <th>COD</th>
                    <th>Caja</th>
                    <th>Cantidad</th>
                    <th>Pago o Gasto</th>
                    <th>Fecha y Hora de modificacion</th>
                    <th>Fecha Cierre</th>
                    <th></th>
                    <th></th>
                </tr>

            </thead>
            <tbody>
                <?php
                $item = 0;
                $sucursal = UserLogin::get_pkSucursal();
                $user = UserLogin::get_id();
                $query = "";
                if($caja === ""){
                    $query = "SELECT gd.pkGastosDiarios,gd.cantidad,gd.descripcion,gd.dateModify,gd.fecha,ac.caja FROM gastos_diarios gd, accion_caja ac "
                        . "where gd.estado=3 and gd.fecha between '$fechaInicio' and '$fechaFin' and gd.pkSucursal='$sucursal' "
                        . "and gd.estado_anular=0 AND ac.pk_accion = gd.pkGastosDiarios AND ac.tipo_accion = 'GAS'";
                }else{
                    $query = "SELECT gd.pkGastosDiarios,gd.cantidad,gd.descripcion,gd.dateModify,gd.fecha,ac.caja FROM gastos_diarios gd, accion_caja ac "
                        . "where gd.estado=3 and gd.fecha between '$fechaInicio' and '$fechaFin' and gd.pkSucursal='$sucursal' "
                        . "and gd.estado_anular=0 AND ac.pk_accion = gd.pkGastosDiarios AND ac.tipo_accion = 'GAS' AND ac.caja = '".$caja."'";
                }
                $result = $db->executeQuery($query);
                while ($row = $db->fecth_array($result)) {
                    ?>
                    <tr>
                        <?php $item = $item + 1; ?>
                        <td><?php echo $item; ?></td>
                        <td><?php echo $row['caja'] ?></td>
                        <td><?php echo $row['cantidad'] ?></td> 
                        <td><?php echo $row['descripcion'] ?></td>
                        <td><?php echo date_format(date_create($row['dateModify']), 'd/m/Y h:i A'); ?></td>
                        <td><?php echo $row['fecha'] ?></td>
                        <td><a onclick="modalEditarPagoFijo(<?php echo $row['cantidad']; ?>, '<?php echo $row['descripcion']; ?>',<?php echo $row['pkGastosDiarios']; ?>)"><span class='glyphicon glyphicon-pencil'></span></a></td>
                        <td><a onclick="modalAnularPagoFijo(<?php echo $row['pkGastosDiarios']; ?>)"><span class='glyphicon glyphicon-minus-sign'></span></a></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>

        <div id="modalEditarPagosFijos" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"><label id="tituloModalEditarPagosFijos"></label></h4>
                    </div>
                    <div class="modal-body">
                        <form id="frmPagosFijos">
                            <input id="codigo" name="codigo" style="display: none;">
                            <label>Cantidad</label>
                            <input id="txtcantidadeditarFijo" name="txtcantidadeditarFijo" class="form-control">
                            <br>
                            <label>Descripcion</label>
                            <input id="txtdescripcioneditarFijo" name="txtdescripcioneditarFijo" class="form-control">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="button" data-dismiss="modal" onclick="EditarPagoFijo()">Aceptar</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="modalAnularPAgoFijo" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"><label id="tituloModalAnularPagoFijo"></label></h4>
                    </div>
                    <div class="modal-body">
                        <form id="frmAnularPagoFijo">
                            <input id="codigoanularFijo" name="codigoanularFijo" style="display: none;">
                            <h4><strong id="txtMensajeeliminar"></strong></h4>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button id="btnColor" class="btn btn-primary" onclick="anularPagoFijo()">Aceptar</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>

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
    muestra_botones();
    
    $(".date").datepicker({dateFormat: 'yy-mm-dd'});

    $('.table').DataTable( {
        dom: 'Blfrtip',
        "bSort": true,
        "bFilter": true,
        "bInfo": false,
        "ordering": true,
        "paging": true,
        buttons: [
            {
                extend: 'excelHtml5',
                exportOptions: {
                    columns: [0,1,2,3,4,5]
                },
                title: '<?php echo $titulo_importante;?>'
            },
            {
                extend: 'pdfHtml5',
                orientation: 'portrait',
                alignment: 'center',
                pageSize: 'LEGAL',
                exportOptions: {
                    columns: [0,1,2,3,4,5]
                },
                customize: function(doc) {
                    doc.content[1].margin = [ 50, 0, 50, 0 ] //left, top, right, bottom
                },
                title: '<?php echo $titulo_importante;?>'
            },
            {
                extend: 'print',
                orientation: 'portrait',
                pageSize: 'LEGAL',
                exportOptions: {
                    columns: [0,1,2,3,4,5]
                },
                title: '<?php echo $titulo_importante;?>'
            }
            
        ]
    } );

    function buscarPagosFijos() {
        window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=GastosDiarios&action=showPagosFijos&" + $('#frmFiltroGastosDiarios').serialize();
    }

    function exportarExcelPagosFijos() {
        var url = "<?php echo Class_config::get('urlApp') ?>/xls_Reporte_Pagos_Fijos.php?FechaInicio=" + $("#fecha_inicio").val() + "&FechaFin=" + $("#fecha_fin").val();
        window.open(url, '_blank');
    }

    function exportarPDFPagosFijos() {
        var url = "<?php echo Class_config::get('urlApp') ?>/pdf_Reporte_Pagos_Fijos.php?FechaInicio=" + $("#fecha_inicio").val() + "&FechaFin=" + $("#fecha_fin").val();
        window.open(url, '_blank');
    }

    function modalEditarPagoFijo($cantidad, $descripcion, $codigo)
    {
        $("#modalEditarPagosFijos").modal("show");
        $("#tituloModalEditarPagosFijos").html("Editando el Pago Fijo selecionado...");
        $('#txtcantidadeditarFijo').val($cantidad);
        $('#txtdescripcioneditarFijo').val($descripcion);
        $('#codigo').val($codigo);
        url = "<?php echo class_config::get('urlApp') ?>/?controller=GastosDiarios&action=EditarGastosFijos";
    }

    function EditarPagoFijo() {
        $.post(url, $('#frmPagosFijos').serialize(),
                function() {
                    location.reload();
                });
        $('#modalEditarPagosFijos').modal('hide');
    }

    function modalAnularPagoFijo($codigo)
    {
        $('#modalAnularPAgoFijo').modal('show');
        $('#tituloModalAnularPagoFijo').html('Anulando Pago Fijo...');
        $('#txtMensajeeliminar').html('Â¿Seguro que desea Anular el Pago Fijo Seleccionado?');
        $('#codigoanularFijo').val($codigo);
        $('#btnColor').removeClass();
        $('#btnColor').addClass('btn btn-danger');
        url = "<?php echo class_config::get('urlApp') ?>/?controller=GastosDiarios&action=AnularGastosFijos";
    }

    function anularPagoFijo() {
        $.post(url, {codigo: $('#codigoanularFijo').val()},
        function() {
            location.reload();
        });
        $('#modalAnularPAgoFijo').modal('hide');
    }
    
    function muestra_botones(){
        var caja = $("#caja option:selected").val();
        if(caja === ""){
            $("#btn_excel").show(0);
            $("#btn_pdf").show(0);
        }else{
            $("#btn_excel").hide(0);
            $("#btn_pdf").hide(0);
        }
    }
</script>