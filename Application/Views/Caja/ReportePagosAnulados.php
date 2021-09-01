<?php 
$fechaInicio = date('Y-m-d', strtotime('-1 month'));
if (isset($_REQUEST['fecha_inicio'])){
    $fechaInicio = $_REQUEST['fecha_inicio'];
}

$fechaFin = date('Y-m-d');
if (isset($_REQUEST['fecha_fin'])){
    $fechaFin = $_REQUEST['fecha_fin'];
}

$tipopago = -1;
if (isset($_REQUEST['cmbtipopago'])){
    $tipopago = intval($_REQUEST['cmbtipopago']);
}

$titulo_importante = "";

switch($tipopago){
    case -1:
        $titulo_importante = "Pagos y/o ingresos anulados";
    break;

    case 0:
        $titulo_importante = "Pagos diarios anulados";
    break;

    case 1:
        $titulo_importante = "Ingresos adicionales anulados";
    break;

    case 2:
        $titulo_importante = "Pagos de planilla anulados";
    break;

    case 3:
        $titulo_importante = "Pagos fijos anulados";
    break;

    case 4:
        $titulo_importante = "Devoluciones anuladas";
    break;
}

if($fechaInicio == $fechaFin){
    $titulo_importante = $titulo_importante." del ".$fechaInicio;
}else{
    $titulo_importante = $titulo_importante." del ".$fechaInicio." al ".$fechaFin;
}

include 'Application/Views/template/header.php'; ?>
<body>
<link rel="stylesheet" href="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.css">
<style>
    .dt-buttons{
        margin-bottom: 10px !important;
    }
</style>   

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
    <div class="container">

        <br /><br/><br/>
        <h2><?php echo $titulo_importante?></h2>
        <form id="frmFiltroGastosDiarios">
            <div class="panel panel-primary" id='pfecha'>
                <div class="panel-heading">
                    Filtros por fechas
                </div>
                <div class="panel-body">

                    <div class='control-group col-lg-2' id="dinicio">
                        <label>Fecha Inicio</label>
                        <input class='form-control date' placeholder='AAAA-MM-DD' id='fecha_inicio' name='fecha_inicio' value="<?php echo $fechaInicio ?>"/>
                    </div>
                    <div class='control-group  col-lg-2' id="ffin">
                        <label>Fecha Fin</label>
                        <input class='form-control date' placeholder='AAAA-MM-DD' id='fecha_fin' name='fecha_fin' value="<?php echo $fechaFin ?>"/>
                    </div>
                    <div class='control-group  col-lg-3'>
                        <label>Tipo Movimiento</label>
                        <select class="form-control" id="cmbtipopago" name="cmbtipopago">
                            <option value="-1" <?php if($tipopago === -1){echo "Selected";}?>>Todos</option>
                            <option value="0" <?php if($tipopago === 0){echo "Selected";}?>>Pago Diario</option>
                            <option value="2" <?php if($tipopago === 2){echo "Selected";}?>>Pago Planilla</option>
                            <option value="3" <?php if($tipopago === 3){echo "Selected";}?>>Pago Fijo</option>
                            <option value="4" <?php if($tipopago === 4){echo "Selected";}?>>Devoluciones</option>
                            <option value="1" <?php if($tipopago === 1){echo "Selected";}?>>Ingreso de Dinero</option>                          
                        </select>
                    </div>
                    <div class='control-group col-lg-3' <?php echo $estilo_nivel;?>>
                        <label>Caja</label>
                        <select name="caja" class="form-control" id="caja">
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
                    <div class='control-group col-lg-2'>
                        <br/>
                        <button type='button' class='btn btn-primary' onclick='buscarPagosAnulados()'>Buscar</button>
                        <br/>
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
                    <th>Concepto</th>
                    <th>Tipo</th>
                    <th>Fecha y Hora de Anulacion</th>
                    <th></th>
                </tr>

            </thead>
            <tbody>
                <?php
                $item = 0;
                $user = UserLogin::get_id();                
                $query = "";
                if($caja === ""){
                    if($tipopago === -1){
                        $query = "SELECT gd.pkGastosDiarios,gd.cantidad,gd.descripcion,gd.dateModify,gd.fecha,ac.caja,gd.estado FROM gastos_diarios gd, accion_caja ac "
                        . "where gd.fecha between '$fechaInicio' and '$fechaFin' and gd.estado_anular=1 AND ac.pk_accion = gd.pkGastosDiarios AND ac.tipo_accion = 'GAS'";
                    }else{
                        $query = "SELECT gd.pkGastosDiarios,gd.cantidad,gd.descripcion,gd.dateModify,gd.fecha,ac.caja,gd.estado FROM gastos_diarios gd, accion_caja ac "
                            . "where gd.estado='".$tipopago."' and gd.fecha between '$fechaInicio' and '$fechaFin' and gd.estado_anular=1 AND ac.pk_accion = gd.pkGastosDiarios AND ac.tipo_accion = 'GAS'";
                    }
                }else{
                    if($tipopago === -1){
                        $query = "SELECT gd.pkGastosDiarios,gd.cantidad,gd.descripcion,gd.dateModify,gd.fecha,ac.caja,gd.estado FROM gastos_diarios gd, accion_caja ac "
                        . "where gd.fecha between '$fechaInicio' and '$fechaFin' and gd.estado_anular=1 AND ac.pk_accion = gd.pkGastosDiarios AND ac.tipo_accion = 'GAS' AND ac.caja = '".$caja."'";
                    }else{
                        $query = "SELECT gd.pkGastosDiarios,gd.cantidad,gd.descripcion,gd.dateModify,gd.fecha,ac.caja,gd.estado FROM gastos_diarios gd, accion_caja ac "
                            . "where gd.estado='".$tipopago."' and gd.fecha between '$fechaInicio' and '$fechaFin' and gd.estado_anular=1 AND ac.pk_accion = gd.pkGastosDiarios AND ac.tipo_accion = 'GAS' AND ac.caja = '".$caja."'";
                    }
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
                        <td><?php switch(intval($row["estado"])){
                            case 0:
                                echo "Pago Diario";
                            break;
                        
                            case 1:
                                echo "Ingreso Adicional";
                            break;
                        
                            case 2:
                                echo "Pago de Planilla";
                            break;
                        
                            case 3:
                                echo "Pago Fijo";
                            break;
                        
                            case 4:
                                echo "Devolucion";
                            break; 
                        } ?></td>
                        <td><?php echo date_format(date_create($row['dateModify']), 'd/m/Y h:i A'); ?></td>
                        <td><a onclick="modalActivarPago(<?php echo $row['pkGastosDiarios']; ?>)"><span class='glyphicon glyphicon-ok'></span></a></td>
                    </tr>
                    <?php
                }
                ?>

            </tbody>
        </table>
        <div id="modalActivarPago" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"><label id="tituloModalActivarPago"></label></h4>
                    </div>
                    <div class="modal-body">
                        <form id="frmActivarPago">
                            <input id="codigoActivarPago" name="codigoActivarPago" style="display: none;">
                            <h4><strong id="txtMensajeeliminar"></strong></h4>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button id="btnColor" class="btn btn-primary" onclick="ActivarPago()">Aceptar</button>
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

    function buscarPagosAnulados() {
        window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Caja&action=PagosAnulados&" + $('#frmFiltroGastosDiarios').serialize();
    }

    function modalActivarPago($codigo)
    {
        $('#modalActivarPago').modal('show');
        $('#tituloModalActivarPago').html('Activando Pago...');
        $('#txtMensajeeliminar').html('¿Seguro que desea Activar el Pago Seleccionado?');
        $('#codigoActivarPago').val($codigo);
        $('#btnColor').removeClass();
        $('#btnColor').addClass('btn btn-danger');
        url = "<?php echo class_config::get('urlApp') ?>/?controller=GastosDiarios&action=ActivarPago";
    }

    function ActivarPago() {
        $.post(url, {codigo: $('#codigoActivarPago').val()},
        function() {
            location.reload();
        });
        $('#modalActivarPago').modal('hide');
    }
</script>