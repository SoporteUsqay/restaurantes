<?php include 'Application/Views/template/header.php' ?>
<body>

<?php
$objViewMenu = new Application_Views_IndexView();
$objViewMenu->showContent();

$db = new SuperDataBase();
$query = "SELECT * from sucursal LIMIT 1";
$result = $db->executeQuery($query);
$array = array();
while ($row = $db->fecth_array($result)) {
    $razon = $row['razon'];
    $comercial = $row['nombreSucursal'];
    $direccion = $row['direccion'];
    $telefono = $row['telefono'];
    $ruc = $row['ruc'];
    $ciudad = $row['ciudad'];
}

$correos = "";
$query_correos = "SELECT * from cloud_config where parametro = 'correos_notificaciones'";
$result_correos = $db->executeQuery($query_correos);
if ($row_c = $db->fecth_array($result_correos)) {
    $correos = $row_c['valor'];
}

?>

<div class="container">

<br><br>
<br><br>


<div class="alert alert-success alert-dismissable" style="display:none;" id="msuccess">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    Operación Completada con Éxito
</div>

<div class="row">
<div>
<div class="panel panel-default">
<div class="panel-heading">
<h4><i class="glyphicon glyphicon-briefcase"></i> Datos Empresa</h4>
</div>
<div class="panel-body">
<fieldset>
    <form class="form-horizontal" id="formEmpresa" role="form" validate>
        <div class="form-group">
            <label for="razon" class="col-sm-2 control-label">Razón Social</label>
            <div class="col-sm-10">
                <input name="razon" type="text" class="form-control" id="razon" placeholder="Razón Social" value="<?php echo $razon;?>" required="true">
            </div>
        </div>
        <div class="form-group">
            <label for="comercial" class="col-sm-2 control-label">Nombre Comercial</label>
            <div class="col-sm-10">
                <input name="comercial" type="text" class="form-control" id="comercial" placeholder="Nombre Comercial"  value="<?php echo $comercial; ?>" required="true">
            </div>
        </div>
        <div class="form-group">
            <label for="direccion" class="col-sm-2 control-label" >Direccion</label>
            <div class="col-sm-10">
                <input name="direccion" class="form-control" id="direccion" placeholder="Dirección del Negocio (Según SUNAT)" value="<?php echo $direccion; ?>" required="true">
            </div>
        </div>
        <div class="form-group">
            <label for="ciudad" class="col-sm-2 control-label" >Ciudad</label>
            <div class="col-sm-10">
                <input name="ciudad" class="form-control" id="ciudad" placeholder="Ciudad del Negocio (Según SUNAT)" value="<?php echo $ciudad; ?>" required="true">
            </div>
        </div>
        <div class="form-group">
            <label for="telefono" class="col-sm-2 control-label">Teléfono</label>
            <div class="col-sm-10">
                <input  type="number" min="7" max="11" name="telefono" class="form-control" id="telefono" placeholder="Ingrese Teléfono" value="<?php echo $telefono; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="ruc" class="col-sm-2 control-label">RUC</label>
            <div class="col-sm-10">
                <input type="text" name="ruc" class="form-control" id="ruc" placeholder="RUC del negocio" value="<?php echo $ruc; ?> " required="true" maxlength="11">
            </div>
        </div>
        <div class="form-group">
            <label for="ruc" class="col-sm-2 control-label">Correos para Notificaciones</label>
            <div class="col-sm-10">
                <input type="text" name="correos" class="form-control" id="correos" placeholder="Correo o correos para envio de notificaciones separados por comas" value="<?php echo $correos; ?>" required="false">
            </div>
        </div>
        <div class="form-group">
            <label for="logo" class="col-sm-2 control-label">Logo</label>
            <div class="col-sm-10">
                <input placeholder='Logo del Negocio' id='log' name='log' type="file" class="form-control"/>
            </div>
        </div>
    </form>
    <div class="text-right">
        <button onclick="guardarDatosSucursal()" type="submit" class="btn btn-primary">Guardar</button>
    </div>
</fieldset>
</div>
</div>
</div>
</div>

</div>

<script>
    var url = "";
    function guardarDatosSucursal() {

        var archivo = document.getElementById("log");

        var arc = 0;
        try {
            arc = archivo.files;
        }
        catch (err) {
        }

        var razon = $('#razon').val();
        var comercial = $("#comercial").val();
        var direccion = $('#direccion').val();
        var ciudad = $('#ciudad').val();
        var telefono = $('#telefono').val();
        var ruc = $('#ruc').val();
        var correos = $("#correos").val();

        var data = new FormData();
        data.append('img', arc[0]);
        data.append('razonsocial', razon);
        data.append('nombre', comercial);
        data.append('direccion', direccion);
        data.append('ciudad', ciudad);
        data.append('telefono', telefono);
        data.append('ruc', ruc);
        data.append('correos',correos);

        var request = $.ajax({
        url: "<?php echo class_config::get('urlApp') ?>/?controller=Empresa&action=Edit",
        type: 'POST',
        contentType: false,
        data: data,
        processData: false,
        cache: false
        });

        request.done(function () {
            $('#msuccess').show('fast').delay(3000).hide('fast');
           
        });

        request.fail(function () {
            alert("¡Hubo un error al procesar!");
        });
    }
</script>
