<?php
$titulo_pagina = 'Descuentos y Convenios';
$titulo_sistema = 'Usqay2';

include_once('recursos/componentes/MasterConexion.php');
$conn = new MasterConexion();

$id = 0;
$nombre = null;
$monto = null;
$tipo =  1;
$maximo = 0;
$estado = 1;

$op = NULL;

if(isset($_REQUEST["op"])){
    $op = $_REQUEST["op"];
}

if(isset($_REQUEST["id"])){
    $id = $_REQUEST["id"];
}

if(isset($_REQUEST["nombre"])){
    $nombre = $_REQUEST["nombre"];
}

if(isset($_REQUEST["monto"])){
    $monto = $_REQUEST["monto"];
}

if(isset($_REQUEST["tipo"])){
    $tipo = $_REQUEST["tipo"];
}

if(isset($_REQUEST["maximo"])){
    $maximo = $_REQUEST["maximo"];
}

switch($op){
    case "ADD":
        $conn->consulta_simple("Insert into descuento_prefijado values(NULL,'".$nombre."','".$monto."','".$tipo."','".$maximo."','".$estado."')");
        $nombre = null;
        $monto = null;
        $tipo =  null;
        $maximo = null;
        $estado = 1;
    break;

    case "EDIT":
        $conn->consulta_simple("Update descuento_prefijado set nombre = '".$nombre."', monto = '".$monto."', tipo = '".$tipo."', maximo = '".$maximo."' where id = '".$id."'");
        $id = 0;
        $nombre = null;
        $monto = null;
        $tipo =  null;
        $maximo = null;
        $estado = 1;
    break;

    case "DEL":
        $conn->consulta_simple("Update descuento_prefijado set estado = 0 where id = '".$id."'");
        $id = 0;
    break;

    case "GET":
        $res = $conn->consulta_arreglo("Select * from descuento_prefijado where id = '".$id."'");
        $nombre = $res["nombre"];
        $monto = $res["monto"];
        $tipo =  $res["tipo"];
        $maximo = $res["maximo"];
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
<h1>Descuentos y Convenios</h1>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Datos descuento</h3>
            </div>
            <div class="panel-body row">
                <div class='control-group col-lg-6'>
                    <label>Nombre</label>
                    <input class="form-control" id="nombre" name="nombre" type="text" value="<?php echo $nombre;?>">
                </div>

                <div class='control-group col-lg-6'>
                    <label>Monto</label>
                    <div class="input-group">              
                    <input aria-describedby="basic-addon1" class="form-control" id="monto" name="monto" type="number" min="0" value="<?php echo $monto;?>">
                    <span class="input-group-addon" id="basic-addon1">%</span>
                    </div>
                </div>         

                <div class='control-group col-lg-6'>
                    <label>Maximo</label>
                    <input class="form-control" id="maximo" name="maximo" type="number" min="0" value="<?php echo $maximo;?>">
                    <p class="help-block">0 = Sin Limite</p>
                </div>

                <input id="tipo" name="tipo" type="hidden" value="<?php echo $tipo;?>">
                <input id="estado" name="estado" type="hidden" value="<?php echo $estado;?>">
                <input id="id" name="id" type="hidden" value="<?php echo $id;?>">

                <div class='control-group col-lg-6'>
                    <label>   </label>
                    <p><button type='button' class='btn btn-primary' onclick='guardar()'>Guardar</button></p>
                </div>

                <?php $descuentos = $conn->consulta_matriz("Select * from descuento_prefijado where estado > 0");?>
                <div class="col-lg-12" style="height:10px;"></div>
                <div class='col-lg-12'>
                    <table id='tb' class='display' cellspacing='0' width='100%' border="0">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Monto</th>
                            <th>Maximo</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(is_array($descuentos)): foreach($descuentos as $rw):?>
                        <tr>
                            <td><?php echo $rw["nombre"];?></td>                      
                            <td><?php echo $rw["monto"];?>%</td>
                            <td><?php 
                            if(intval($rw["maximo"]) === 0){
                                echo "Sin Limite";
                            }else{
                                echo $rw["maximo"];
                            }
                            ?></td>
                            <td>
                            <?php 
                                echo "<a href='#' onclick='elimina(".$rw["id"].")'><span class='glyphicon glyphicon-trash' aria-hidden='true'></span></a>";
                            ?>
                            </td>
                            <td>
                            <?php
                                echo "<a href='#' onclick='edita(".$rw["id"].")'><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a>";
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
history.pushState(null, "", 'descuentos.php');

$('#tb').DataTable( {
dom: 'Blfrtip',
"bSort": false,
"bFilter": false,
"bInfo": false,
"ordering": false,
"paging": false});

function guardar(){
    var id = $("#id").val();
    var nombre = $("#nombre").val();
    var tipo = $("#tipo").val();
    var monto = $("#monto").val();
    var maximo = $("#maximo").val();
    if(parseInt(id) === 0){
        location.href = "?op=ADD&nombre="+nombre+"&tipo="+tipo+"&monto="+monto+"&maximo="+maximo;
    }else{
        location.href = "?op=EDIT&id="+id+"&nombre="+nombre+"&tipo="+tipo+"&monto="+monto+"&maximo="+maximo;
    }
}

function elimina(id_in){
    if (confirm("Esta accion no se puede deshacer ¿Continuar?")) {
        location.href = "?op=DEL&id="+id_in;
    }
}

function edita(id_in){
    location.href = "?op=GET&id="+id_in;
}

</script>
</body>
</html>

                            