<?php
$titulo_pagina = 'Moneda y Pagos';
$titulo_sistema = 'Usqay2';

include_once('recursos/componentes/MasterConexion.php');
$conn = new MasterConexion();

$id_moneda = 0;
$nombre_moneda = null;
$simbolo_moneda = null;
$estado_moneda = 1;


$id_medio = 0;
$nombre_medio = null;
$moneda_medio = null;
$formula_medio = "";
$estado_medio = 1;

$op = NULL;

if(isset($_REQUEST["op"])){
    $op = $_REQUEST["op"];
}

if(isset($_REQUEST["id_moneda"])){
    $id_moneda = $_REQUEST["id_moneda"];
}

if(isset($_REQUEST["nombre_moneda"])){
    $nombre_moneda = $_REQUEST["nombre_moneda"];
}

if(isset($_REQUEST["simbolo_moneda"])){
    $simbolo_moneda = $_REQUEST["simbolo_moneda"];
}

if(isset($_REQUEST["estado_moneda"])){
    $estado_moneda = $_REQUEST["estado_moneda"];
}


if(isset($_REQUEST["id_medio"])){
    $id_medio = $_REQUEST["id_medio"];
}

if(isset($_REQUEST["nombre_medio"])){
    $nombre_medio = $_REQUEST["nombre_medio"];
}

if(isset($_REQUEST["moneda_medio"])){
    $moneda_medio = $_REQUEST["moneda_medio"];
}

/*if(isset($_REQUEST["formula_medio"])){
    $formula_medio = $_REQUEST["formula_medio"];
}*/

if(isset($_REQUEST["estado_medio"])){
    $estado_medio = $_REQUEST["estado_medio"];
}

switch($op){
    case "ADDMON":
        $conn->consulta_simple("Insert into moneda values(NULL,'".$nombre_moneda."','".utf8_encode($simbolo_moneda)."','".$estado_moneda."')");
        $nombre_moneda = null;
        $simbolo_moneda = null;
        $estado_moneda = 1;
    break;

    case "EDITMOM":
        $conn->consulta_simple("Update moneda set nombre = '".$nombre_moneda."', simbolo = '".utf8_encode($simbolo_moneda)."' where id = '".$id_moneda."'");
        $id_moneda = 0;
        $nombre_moneda = null;
        $simbolo_moneda = null;
        $estado_moneda = 1;
    break;

    case "DELMON":
        $conn->consulta_simple("Update moneda set estado = 0 where id = '".$id_moneda."'");
        $id_moneda = 0;
    break;

    case "GETMON":
        $res = $conn->consulta_arreglo_ex("Select * from moneda where id = '".$id_moneda."'");
        $nombre_moneda = $res["nombre"];
        $simbolo_moneda = $res["simbolo"];
        $estado_moneda = $res["estado"];
    break;

    case "ADDMED":
        $conn->consulta_simple("Insert into medio_pago values(NULL,'".$nombre_medio."','".$moneda_medio."','".$formula_medio."','".$estado_medio."')");
        $nombre_medio = null;
        $moneda_medio = null;
        $estado_medio = 1;
    break;

    case "EDITMED":
        $conn->consulta_simple("Update medio_pago set nombre = '".$nombre_medio."', moneda = '".$moneda_medio."' where id = '".$id_medio."'");
        $id_medio = 0;
        $nombre_medio = null;
        $moneda_medio = null;
        $estado_medio = 1;
    break;

    case "DELMED":
        $conn->consulta_simple("Update medio_pago set estado = 0 where id = '".$id_medio."'");
        $id_medio = 0;
    break;

    case "GETMED":
        $res = $conn->consulta_arreglo("Select * from medio_pago where id = '".$id_medio."'");
        $nombre_medio = $res["nombre"];
        $moneda_medio = $res["moneda"];
    break;
}

require_once('recursos/componentes/header.php'); 
?>
<?php if($op !== null):?>
<div class="alert alert-success alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Operación Realizada con Éxito
</div>
<?php endif;?>
<link rel="stylesheet" href="recursos/js/plugins/tablas/jquery.dataTables.min.css">
<h1>Monedas y Medios de Pago</h1>
<div class="row">
    <div class="col-lg-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Monedas</h3>
            </div>
            <div class="panel-body row">
                <div class='control-group col-lg-6'>
                    <label>Nombre</label>
                    <input class="form-control" id="nombre_moneda" name="nombre_moneda" type="text" value="<?php echo $nombre_moneda;?>">
                </div>

                <div class='control-group col-lg-6'>
                    <label>Simbolo</label>
                    <input class="form-control" id="simbolo_moneda" name="simbolo_moneda" type="text" value="<?php echo $simbolo_moneda;?>">
                </div>

                <input id="estado_moneda" name="estado_moneda" type="hidden" value="<?php echo $estado_moneda;?>">
                <input id="id_moneda" name="id_moneda" type="hidden" value="<?php echo $id_moneda;?>">

                <div class='control-group col-lg-6'>
                    <p></p>
                    <button type='button' class='btn btn-primary' onclick='guardar_moneda()'>Guardar</button>
                </div>

                <?php $monedas = $conn->consulta_matriz_ex("Select * from moneda where estado > 0");?>
                <div class="col-lg-12" style="height:10px;"></div>
                <div class='col-lg-12'>
                    <table id='tb_moneda' class='display' cellspacing='0' width='100%' border="0">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Simbolo</th>
                            <th>Tipo</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(is_array($monedas)): foreach($monedas as $rw):?>
                        <tr>
                            <td><?php echo $rw["nombre"];?></td>
                            <td><?php echo $rw["simbolo"];?></td>
                            <td><?php if(intval($rw["estado"]) === 2){echo "Nacional";}else{echo "Extranjera";};?></td>
                            <td>
                            <?php 
                                if(intval($rw["id"])>1){
                                    echo "<a href='#' onclick='elimina_moneda(".$rw["id"].")'><span class='glyphicon glyphicon-trash' aria-hidden='true'></span></a>";
                                }
                            ?>
                            </td>
                            <td>
                            <?php
                                echo "<a href='#' onclick='edita_moneda(".$rw["id"].")'><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a>";
                            ?>
                            </td>
                        </tr>
                    <?php endforeach; endif;?>
                    </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Pagos</h3>
        </div>
        <div class="panel-body row">
            <div class='control-group col-lg-6'>
                <label>Nombre</label>
                    <input class="form-control" id="nombre_medio" name="nombre_medio" type="text" value="<?php echo $nombre_medio;?>">
            </div>
            <div class='control-group col-lg-6'>
            <label>Moneda</label>
                <select class="form-control" id="moneda_medio" name="moneda_medio">
                    <?php if(is_array($monedas)): foreach($monedas as $rw):?>
                        <?php if(intval($rw["id"]) === intval($moneda_medio)):?>
                            <option value='<?php echo $rw["id"]?>' selected><?php echo $rw["nombre"]." ".$rw["simbolo"];?></option>
                        <?php else:?>
                            <option value='<?php echo $rw["id"]?>'><?php echo $rw["nombre"]." ".$rw["simbolo"];?></option>
                        <?php endif;?>
                    <?php endforeach; endif;?>                
                </select>
            </div>

            <input id="estado_medio" name="estado_medio" type="hidden" value="<?php echo $estado_medio;?>">
            <input id="id_medio" name="id_medio" type="hidden" value="<?php echo $id_medio;?>">

            <div class='control-group col-lg-6'>
                <p></p>
                <button type='button' class='btn btn-primary' onclick='guardar_medio()'>Guardar</button>
            </div>

            <?php $pagos = $conn->consulta_matriz("Select mp.*, m.simbolo from medio_pago mp, moneda m where mp.estado > 0 AND mp.moneda = m.id");?>
            <div class="col-lg-12" style="height:10px;"></div>
            <div class='col-lg-12'>
                <table id='tb_pagos' class='display' cellspacing='0' width='100%' border="0">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Moneda</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php if(is_array($pagos)): foreach($pagos as $rw):?>
                    <tr>
                        <td><?php echo $rw["nombre"];?></td>
                        <td><?php 
                        if(intval($rw["id"])>1){
                            echo $rw["simbolo"];
                        }else{
                            echo "-";
                        }
                        
                        ?></td>
                        <td>
                        <?php 
                            if(intval($rw["id"])>1){
                                echo "<a href='#' onclick='elimina_medio(".$rw["id"].")'><span class='glyphicon glyphicon-trash' aria-hidden='true'></span></a>";
                            }
                        ?>
                        </td>
                        <td>
                        <?php 
                            if(intval($rw["id"])>1){
                                echo "<a href='#' onclick='edita_medio(".$rw["id"].")'><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a>";
                            }
                        ?>
                        </td>
                    </tr>
                <?php endforeach; endif;?>
                </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
</div><!--/row-->
    <hr>
</div><!--/.container-->

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="recursos/js/jquery.js"></script>
<script src="recursos/js/jquery-ui.js"></script>
<script src="recursos/js/<?php echo $nombre_tabla; ?>.js"></script>
<script src="recursos/js/bootstrap.min.js"></script>
<script src="recursos/js/offcanvas.js"></script>
<script src="../Public/select2/js/select2.js"></script>
<script src="recursos/btable/bootstrap-table.min.js"></script>
<script src="recursos/btable/bootstrap-table-group-by.js"></script>
<script src="recursos/js/plugins/tablas/jquery.dataTables.min.js"></script>
<script>
history.pushState(null, "", 'moneda_pagos.php');

$('#tb_moneda').DataTable( {
dom: 'Blfrtip',
"bSort": false,
"bFilter": false,
"bInfo": false,
"ordering": false,
"paging": false});

$('#tb_pagos').DataTable( {
dom: 'Blfrtip',
"bSort": false,
"bFilter": false,
"bInfo": false,
"ordering": false,
"paging": false});


//Funciones para moneda
function guardar_moneda(){
    var id_moneda = $("#id_moneda").val();
    var nombre_moneda = $("#nombre_moneda").val();
    var simbolo_moneda = $("#simbolo_moneda").val();
    if(parseInt(id_moneda) === 0){
        location.href = "?op=ADDMON&nombre_moneda="+nombre_moneda+"&simbolo_moneda="+simbolo_moneda;
    }else{
        location.href = "?op=EDITMON&id_moneda="+id_moneda+"&nombre_moneda="+nombre_moneda+"&simbolo_moneda="+simbolo_moneda;
    }
}

function elimina_moneda(id_in){
    if (confirm("Esta accion no se puede deshacer ¿Continuar?")) {
        location.href = "?op=DELMON&id_moneda="+id_in;
    }
}

function edita_moneda(id_in){
    location.href = "?op=GETMON&id_moneda="+id_in;
}


//Funciones para medios
function guardar_medio(){
    var id_medio = $("#id_medio").val();
    var nombre_medio = $("#nombre_medio").val();
    var moneda_medio = $("#moneda_medio").val();
    if(parseInt(id_medio) === 0){
        location.href = "?op=ADDMED&nombre_medio="+nombre_medio+"&moneda_medio="+moneda_medio;
    }else{
        location.href = "?op=EDITMED&id_medio="+id_medio+"&nombre_medio="+nombre_medio+"&moneda_medio="+moneda_medio;
    }
}

function edita_medio(id_in){
    location.href = "?op=GETMED&id_medio="+id_in;
}

function elimina_medio(id_in){
    if (confirm("Esta accion no se puede deshacer ¿Continuar?")) {
        location.href = "?op=DELMED&id_medio="+id_in;
    }
}

</script>
</body>
</html>

                            