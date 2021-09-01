<?php
$titulo_pagina = 'Estructura de Menu';
$titulo_sistema = 'Usqay2';

include_once('recursos/componentes/MasterConexion.php');
$conn = new MasterConexion();
$id = NULL;
$nombre = NULL;

$op = NULL;

if(isset($_GET["i"])){
    $id = $_GET["i"];
}

if(isset($_GET["n"])){
    $nombre = $_GET["n"];
}

if(isset($_GET["o"])){
    $op = $_GET["o"];
}

switch(intval($op)){
    case 1:
        $conn->consulta_simple("Insert into tipo_componente_menu values(NULL,'".$nombre."',1)");
        $id = NULL;
        $nombre = NULL;
    break;

    case 2:
        $conn->consulta_simple("Update tipo_componente_menu set nombre = '".$nombre."' where id = '".$id."'");
        $id = NULL;
        $nombre = NULL;
    break;

    case 3:
        $conn->consulta_simple("Delete from tipo_componente_menu where id = '".$id."'");
        $id = NULL;
        $nombre = NULL;
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
<h1>Estructura de Menu</h1>

<div class="panel panel-primary" id='pplatos'>
    <div class="panel-heading">
        <h3 class="panel-title"></h3>
    </div>
    <div class="panel-body">
        <input type="hidden" id="id"/>
        <div class='control-group'>
            <label>Nombre</label>
            <input class='form-control' placeholder='Nombre' id='nombre'/>
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
    $rtabla = $conn->consulta_matriz("Select * from tipo_componente_menu where estado = 1");
?>
<div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%' border="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>OPC</th>
            </tr>
        </thead>
        <tbody>
        <?php if(is_array($rtabla)):
              foreach($rtabla as $rw):?>
            <tr>
                <td><?php echo $rw["id"];?></td>
                <td><?php echo utf8_encode($rw["nombre"]);?></td>
                <td>
                    <a href="tipo_componente_menu.php?o=3&i=<?php echo $rw["id"];?>">Desactivar</a>
                    <br/>
                    <a style="cursor:pointer;" onclick="edita('<?php echo $rw['id'] ?>','<?php echo utf8_encode($rw['nombre']);?>')">Editar</a>
                </td>
            </tr>
        <?php 
            endforeach;
            endif;
        ?>
<?php
$nombre_tabla = 'tipo_componente_menu';
require_once('recursos/componentes/footer.php');
?>
<script src="recursos/js/plugins/tablas/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function () {
        $('#tb').DataTable();
    });

    function edita(id,nombre){
        $("#id").val(id);
        $("#nombre").val(nombre);
    }
    
    function guardar(){
        var id = $("#id").val();
        var nombre = $("#nombre").val();
        if(parseInt(id)>0){
            location.href = "tipo_componente_menu.php?o=2&i="+id+"&n="+nombre;
        }else{
            location.href = "tipo_componente_menu.php?o=1&i="+id+"&n="+nombre;
        }
    }
</script>

                            