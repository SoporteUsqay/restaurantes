<?php
$titulo_pagina = 'Tipos de Menu';
$titulo_sistema = 'Usqay2';

include_once('recursos/componentes/MasterConexion.php');
$conn = new MasterConexion();
$id = NULL;
$nombre = NULL;
$precio = NULL;

$op = NULL;

if(isset($_GET["i"])){
    $id = $_GET["i"];
}

if(isset($_GET["n"])){
    $nombre = $_GET["n"];
}

if(isset($_GET["p"])){
    $precio = $_GET["p"];
}

if(isset($_GET["o"])){
    $op = $_GET["o"];
}

switch(intval($op)){
    case 1:
        $conn->consulta_simple("Insert into tipo_menu values(NULL,'".$nombre."','".$precio."',1)");
        $id = NULL;
        $nombre = NULL;
        $precio = NULL;
    break;

    case 2:
        $conn->consulta_simple("Update tipo_menu set nombre = '".$nombre."', precio = '".$precio."' where id = '".$id."'");
        $id = NULL;
        $nombre = NULL;
        $precio = NULL;
    break;

    case 3:
        $conn->consulta_simple("Update tipo_menu set estado = '0' where id = '".$id."'");
        $id = NULL;
        $nombre = NULL;
        $precio = NULL;
    break;
}

require_once('recursos/componentes/header.php'); 
?>
<?php if(intval($op)>0):?>
<div class="alert alert-success alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Operación Realizada con Éxito
</div>
<?php endif;?>
<link rel="stylesheet" href="recursos/js/plugins/tablas/jquery.dataTables.min.css">
<h1>Tipos de Menu</h1>

<div class="panel panel-primary" id='pplatos'>
    <div class="panel-heading">
        <h3 class="panel-title"></h3>
    </div>
    <div class="panel-body">
        <input type="hidden" id="id"/>
        <div class='control-group'>
            <label>Nombre</label>
            <input class='form-control' placeholder='Nombre' id='nombre' />
        </div>
        <div class='control-group'>
            <label>Precio</label>
            <input class="form-control" id="precio" value="1" type="number" step="0.01" min="0.01">
        </div>
        <div class='control-group'>
            <p></p>
            <button type='button' class='btn btn-primary' onclick='guardar()'>Guardar</button>
        </div>
    </div>
</div>
</form>
<hr/>
<?php 
    $rtabla = $conn->consulta_matriz("Select * from tipo_menu where estado = 1");
?>
<div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%' border="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>OPC</th>
            </tr>
        </thead>
        <tbody>
        <?php if(is_array($rtabla)):
              foreach($rtabla as $rw):?>
            <tr>
                <td><?php echo $rw["id"];?></td>
                <td><?php echo utf8_encode($rw["nombre"]);?></td>
                <td><?php echo $rw["precio"];?></td>
                <td>
                    <a href="tipo_menu.php?o=3&i=<?php echo $rw["id"];?>">Desactivar</a>
                    <br/>
                    <a onclick="edita(<?php echo $rw["id"];?>,'<?php echo utf8_encode($rw["nombre"]);?>','<?php echo $rw["precio"];?>')">Editar</a>
                </td>
            </tr>
        <?php 
            endforeach;
            endif;
        ?>
<?php
$nombre_tabla = 'tipo_menu';
require_once('recursos/componentes/footer.php');
?>
<script src="recursos/js/plugins/tablas/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function () {
        $('#tb').DataTable();
    });

    function edita(id,nombre,precio){
        $("#id").val(id);
        $("#nombre").val(nombre);
        $("#precio").val(precio);
    }
    
    function guardar(){
        var id = $("#id").val();
        var nombre = $("#nombre").val();
        var precio = $("#precio").val();
        if(parseInt(id)>0){
            location.href = "tipo_menu.php?o=2&i="+id+"&n="+nombre+"&p="+precio;
        }else{
            location.href = "tipo_menu.php?o=1&i="+id+"&n="+nombre+"&p="+precio;
        }
    }
</script>

                            