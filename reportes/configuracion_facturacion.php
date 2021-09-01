<?php
$titulo_pagina = 'Configuracion Comprobantes Electronicos';
$titulo_sistema = 'Usqay2';

include_once('recursos/componentes/MasterConexion.php');
$conn = new MasterConexion();

$sboleta = NULL;
$sfactura = NULL;
$ruta = NULL;
$token = NULL;
$url = NULL;
$porcentaje_detraccion = NULL;
$cuenta_detraccion = NULL;

$cambios = 0;


if(isset($_GET["sboleta"])){
    $sboleta = $_GET["sboleta"];
    $cambios = 1;
}

if(isset($_GET["sfactura"])){
    $sfactura = $_GET["sfactura"];
    $cambios = 1;
}

if(isset($_GET["ruta"])){
    $ruta = $_GET["ruta"];
    $cambios = 1;
}

if(isset($_GET["token"])){
    $token = $_GET["token"];
    $cambios = 1;
}

if(isset($_GET["url"])){
    $url = $_GET["url"];
    $cambios = 1;
}

if(isset($_GET["porcentaje_detraccion"])){
    $porcentaje_detraccion = $_GET["porcentaje_detraccion"];
    $cambios = 1;
}

if(isset($_GET["cuenta_detraccion"])){
    $cuenta_detraccion = $_GET["cuenta_detraccion"];
    $cambios = 1;
}

if($cambios == 1){
    $conn->consulta_simple("SET SQL_SAFE_UPDATES = 0;");
    $conn->consulta_simple("Update cloud_config set valor = '".$sboleta."' where parametro = 'sboleta'");
    $conn->consulta_simple("Update cloud_config set valor = '".$sfactura."' where parametro = 'sfactura'");
    $conn->consulta_simple("Update cloud_config set valor = '".$ruta."' where parametro = 'rutapce'");
    $conn->consulta_simple("Update cloud_config set valor = '".$token."' where parametro = 'tokenpce'");
    $conn->consulta_simple("Update cloud_config set valor = '".$url."' where parametro = 'urlpce'");
    $conn->consulta_simple("Update cloud_config set valor = '".$porcentaje_detraccion."' where parametro = 'porcentaje_detraccion'");
    $conn->consulta_simple("Update cloud_config set valor = '".$cuenta_detraccion."' where parametro = 'cuenta_detraccion'");
    $conn->consulta_simple("SET SQL_SAFE_UPDATES = 1;");
}

//Obtenemos Datos

$c0 = "Select * from cloud_config where parametro = 'sboleta'";
$r0 = $conn->consulta_arreglo($c0);
if(!is_array($r0)){
    $conn->consulta_simple("Insert into cloud_config values(NULL,'sboleta','')");
}else{
    $sboleta = $r0["valor"];
}

$c1 = "Select * from cloud_config where parametro = 'sfactura'";
$r1 = $conn->consulta_arreglo($c1);
if(!is_array($r1)){
    $conn->consulta_simple("Insert into cloud_config values(NULL,'sfactura','')");
}else{
    $sfactura = $r1["valor"];
}

$c2 = "Select * from cloud_config where parametro = 'rutapce'";
$r2 = $conn->consulta_arreglo($c2);
if(!is_array($r2)){
    $conn->consulta_simple("Insert into cloud_config values(NULL,'rutapce','')");
}else{
    $ruta = $r2["valor"];
}

$c3 = "Select * from cloud_config where parametro = 'tokenpce'";
$r3 = $conn->consulta_arreglo($c3);
if(!is_array($r3)){
    $conn->consulta_simple("Insert into cloud_config values(NULL,'tokenpce','')");
}else{
    $token = $r3["valor"];
}

$c4 = "Select * from cloud_config where parametro = 'urlpce'";
$r4 = $conn->consulta_arreglo($c4);
if(!is_array($r4)){
    $conn->consulta_simple("Insert into cloud_config values(NULL,'urlpce','')");
}else{
    $url = $r4["valor"];
}

$c5 = "Select * from cloud_config where parametro = 'porcentaje_detraccion'";
$r5 = $conn->consulta_arreglo($c5);
if(!is_array($r5)){
    $conn->consulta_simple("Insert into cloud_config values(NULL,'porcentaje_detraccion','')");
}else{
    $porcentaje_detraccion = $r5["valor"];
}

$c6 = "Select * from cloud_config where parametro = 'cuenta_detraccion'";
$r6 = $conn->consulta_arreglo($c6);
if(!is_array($r6)){
    $conn->consulta_simple("Insert into cloud_config values(NULL,'cuenta_detraccion','')");
}else{
    $cuenta_detraccion = $r6["valor"];
}

require_once('recursos/componentes/header.php'); 
?>
<?php if(intval($cambios)>0):?>
<div class="alert alert-success alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Operación Realizada con Éxito
</div>
<?php endif;?>

<h1>Configuración comprobantes electrónicos</h1>

<div class="panel panel-primary" id='pplatos'>
    <div class="panel-heading">
        <h3 class="panel-title">Ingresa los datos proporcionados por el PSE</h3>
    </div>
    <div class="panel-body">
        <input type="hidden" id="id" value=""/>

        <div class='control-group'>
            <label>Serie Boleta</label>
            <input type="text" value="<?php echo $sboleta;?>" id="sboleta" class="form-control">
        </div>
        <div class='control-group'>
            <label>Serie Factura</label>
            <input type="text" value="<?php echo $sfactura;?>" id="sfactura" class="form-control">
        </div>
        <div class='control-group'>
            <label>Ruta Facturador</label>
            <input type="text" value="<?php echo $ruta;?>" id="ruta" class="form-control">
        </div>
        <div class='control-group'>
            <label>Token Facturador</label>
            <input type="text" value="<?php echo $token;?>" id="token" class="form-control">
        </div>
        <div class='control-group'>
            <label>URL Consulta</label>
            <input type="text" value="<?php echo $url;?>" id="url" class="form-control">
        </div>
        <div class='control-group'>
            <label>Porcentaje Detracción</label>
            <select id="porcentaje_detraccion" class="form-control">
                <option value="">Seleccione</option>
                <?php 

                    $query = "SELECT * FROM porcentaje_detraccion";

                    $res = $db->executeQueryEx($query);
                    
                    while ($row = $db->fecth_array($res)):

                        if(!$row['porcentaje']) continue;
                ?>
                    <option value="<?php echo $row['id'] ?>" <?php echo $row['id'] == $porcentaje_detraccion ? 'selected' : '' ?>>
                        <?php echo floatval($row['porcentaje']) . '% - ' . $row['nombre'] ?>
                    </option>
                <?php endwhile ?>
            </select>
        </div>
        <div class='control-group'>
            <label>N° Cuenta Detracción</label>
            <input type="text" value="<?php echo $cuenta_detraccion;?>" id="cuenta_detraccion" class="form-control">
        </div>

        <div class='control-group'>
            <p></p>
            <button type='button' class='btn btn-primary' onclick='guardar()'>Guardar</button>
        </div>
    </div>
</div>
</form>
<hr/>

   </div><!--/row-->
      <hr>
    </div><!--/.container-->
    

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="recursos/js/jquery.js"></script>
    <script src="recursos/js/jquery-ui.js"></script>
    <script src="recursos/js/plugins/datatables/jquery-datatables.js"></script>
    <script src="recursos/js/plugins/datatables/dataTables.tableTools.js"></script>
    <script src="recursos/js/bootstrap.min.js"></script>
    <script src="recursos/js/offcanvas.js"></script>
    <script src="../Public/select2/js/select2.js"></script>
    <script>

    function guardar(){
        var sboleta = $("#sboleta").val();
        var sfactura = $("#sfactura").val();
        var ruta = $("#ruta").val();
        var token = $("#token").val();
        var url = $("#url").val();
        var porcentaje_detraccion = $("#porcentaje_detraccion").val();
        var cuenta_detraccion = $("#cuenta_detraccion").val();
        location.href = "configuracion_facturacion.php?sboleta="+sboleta+"&sfactura="+sfactura
        +"&ruta="+ruta+"&token="+token+"&url="+url+"&porcentaje_detraccion="+porcentaje_detraccion
        +"&cuenta_detraccion="+cuenta_detraccion

    }
    
    jQuery.fn.reset = function () {
        $(this).each(function () {
            this.reset();
        });
    };


    $(document).ready(function () {
        history.pushState(null, "", 'configuracion_facturacion.php');
    });

    </script>
  </body>
</html>

                            