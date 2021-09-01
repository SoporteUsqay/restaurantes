<?php
$titulo_pagina = 'Componentes de Menu';
$titulo_sistema = 'Usqay2';

include_once('recursos/componentes/MasterConexion.php');
$conn = new MasterConexion();
$id = NULL;
$tipo_menu = NULL;
$tipo_componente_menu = NULL;
$plato = NULL;
$precio = NULL;
$stock = NULL;
$inicio = NULL;
$fin = NULL;

$op = NULL;

if(isset($_GET["i"])){
    $id = $_GET["i"];
}

if(isset($_GET["tm"])){
    $tipo_menu = $_GET["tm"];
}

if(isset($_GET["tc"])){
    $tipo_componente_menu = $_GET["tc"];
}

if(isset($_GET["pl"])){
    $plato = $_GET["pl"];
}

if(isset($_GET["pr"])){
    $precio = $_GET["pr"];
}

if(isset($_GET["st"])){
    $stock = $_GET["st"];
}

if(isset($_GET["in"])){
    $inicio = $_GET["in"];
}

if(isset($_GET["fn"])){
    $fin = $_GET["fn"];
}

if(isset($_GET["o"])){
    $op = $_GET["o"];
}

switch(intval($op)){
    case 1:
        $conn->consulta_simple("Insert into componente_menu values(NULL,'".$tipo_menu."','".$plato."','".$precio."','".$stock."','".$inicio."','".$fin."','".$tipo_componente_menu."',1)");
    break;

    case 2:
        $conn->consulta_simple("Update componente_menu set id_tipo_menu = '".$tipo_menu."', pk_plato = '".$plato."', precio = '".$precio."',stock = '".$stock."',fecha_inicio = '".$inicio."',fecha_fin = '".$fin."', id_tipo_componente_menu = '".$tipo_componente_menu."' where id = '".$id."'");
    break;

    case 3:
        $conn->consulta_simple("Update componente_menu set estado = '0' where id = '".$id."'");
    break;
}

require_once('recursos/componentes/header.php'); 
?>
<style>
    .select2-container{
        height: 34px !important;
    }
    .select2-selection{
        height: 34px !important;
        padding: 2px 4px !important;
        font-size: 14px !important;
        border-radius: 4px !important;
    }
</style>
<?php if(intval($op)>0):?>
<div class="alert alert-success alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Operación Realizada con Éxito
</div>
<?php endif;?>
<link rel="stylesheet" href="recursos/js/plugins/tablas/jquery.dataTables.min.css">
<h1>Componentes de Menu</h1>

<div class="panel panel-primary" id='pplatos'>
    <div class="panel-heading">
        <h3 class="panel-title"></h3>
    </div>
    <div class="panel-body row">
        <input type="hidden" id="id" value=""/>
        <div class='control-group col-lg-3 col-xs-12'>
            <label>Tipo Menu</label>
            <select class="form-control" id="tipo_menu">
                <?php 
                    $tipos = $conn->consulta_matriz("Select * from tipo_menu where estado = 1");
                    if(is_array($tipos)){
                        foreach ($tipos as $tp){
                            echo "<option value='".$tp["id"]."'>".utf8_encode($tp["nombre"])."</option>";
                        }
                    }
                ?>
            </select>
        </div>
        <div class='control-group col-lg-3 col-xs-12'>
            <label>Tipo Componente Menu</label>
            <select class="form-control" id="tipo_componente_menu">
                <?php 
                    $tipos1 = $conn->consulta_matriz("Select * from tipo_componente_menu where estado = 1");
                    if(is_array($tipos1)){
                        foreach ($tipos1 as $tp){
                            echo "<option value='".$tp["id"]."'>".utf8_encode($tp["nombre"])."</option>";
                        }
                    }
                ?>
            </select>
        </div>
        <div class='control-group col-lg-3 col-xs-12'>
            <label>Plato</label>
            <select class="form-control" id="id_plato">
                <?php 
                    $platos = $conn->consulta_matriz("Select * from plato where estado = 0");
                    $flag = 0;
                    if(is_array($platos)){
                        foreach ($platos as $pl){
                            echo "<option value='".$pl["pkPlato"]."'>".utf8_encode($pl["descripcion"])."</option>";
                        }
                    }
                ?>
            </select>
        </div>
        <div class='control-group col-lg-3 col-xs-12'>
            <label>Precio</label>
            <input class="form-control" id="precio" value="1" type="number" step="0.01" min="0.01">
        </div>
        <div class='control-group col-lg-3 col-xs-12'>
            <label>Stock</label>
            <input class="form-control" id="stock" value="1" type="number" step="0.01" min="0.01">
        </div>
        <div class='control-group col-lg-3 col-xs-12'>
            <label>Inicio</label>
            <input class='form-control' placeholder='AAAA-MM-DD' id='inicio' />
        </div>
        <div class='control-group col-lg-3 col-xs-12'>
            <label>Fin</label>
            <input class='form-control' placeholder='AAAA-MM-DD' id='fin' />
        </div>
        <div class='control-group col-lg-3 col-xs-12'>
            <p></p>
            <button type='button' class='btn btn-primary' onclick='guardar()'>Guardar</button>
        </div>
    </div>
</div>
</form>
<hr/>
<?php 
    $rtabla = $conn->consulta_matriz("Select * from componente_menu where estado = 1 order by id DESC");
?>
<div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%' border="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tipo</th>
                <th>Componente</th>
                <th>Plato</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Inicio</th>
                <th>Fin</th>
                <th>OPC</th>
            </tr>
        </thead>
        <tbody>
        <?php if(is_array($rtabla)):
              foreach($rtabla as $rw):?>
            <tr>
                <td><?php echo $rw["id"];?></td>
                <td><?php 
                $r0 = $conn->consulta_arreglo("Select * from tipo_menu where id = '".$rw["id_tipo_menu"]."'");
                echo $r0["nombre"];?></td>
                <td><?php 
                $r1 = $conn->consulta_arreglo("Select * from tipo_componente_menu where id = '".$rw["id_tipo_componente_menu"]."'");
                echo $r1["nombre"];?></td>
                <td><?php 
                $r2 = $conn->consulta_arreglo("Select * from plato where pkPlato = '".$rw["pk_plato"]."'");
                echo $r2["descripcion"];?></td>
                <td><?php echo $rw["precio"];?></td>
                <td><?php echo $rw["stock"];?></td>
                <td><?php echo $rw["fecha_inicio"];?></td>
                <td><?php echo $rw["fecha_fin"];?></td>
                <td>
                    <a href="componente_menu.php?o=3&i=<?php echo $rw["id"];?>">Desactivar</a>
                    <br/>
                    <a onclick="edita(<?php echo $rw["id"];?>,<?php echo $rw["id_tipo_menu"];?>,<?php echo $rw["id_tipo_componente_menu"];?>,'<?php echo $rw["pk_plato"];?>','<?php echo $rw["precio"];?>',<?php echo $rw["stock"];?>,'<?php echo $rw["fecha_inicio"];?>','<?php echo $rw["fecha_fin"];?>')">Editar</a>
                </td>
            </tr>
        <?php 
            endforeach;
            endif;
        ?>
<?php
$nombre_tabla = 'componente_menu';
require_once('recursos/componentes/footer.php');
?>
<script src="recursos/js/plugins/tablas/jquery.dataTables.min.js"></script>
<script>
    function edita(id,id_tipo_menu,id_tipo_componente_menu,pk_plato,precio,stock,inicio,fin){
        $("#id").val(id);
        $("#tipo_menu").val(id_tipo_menu);
        $('#tipo_menu').trigger('change');
        $("#tipo_componente_menu").val(id_tipo_componente_menu);
        $('#tipo_componente_menu').trigger('change');
        $("#id_plato").val(pk_plato);
        $('#id_plato').trigger('change');      
        $("#precio").val(precio);
        $("#stock").val(stock);
        $("#inicio").val(inicio);
        $("#fin").val(fin);
    }
    
    function guardar(){
        var id = $("#id").val();
        var id_tipo_menu = $("#tipo_menu").val();
        var id_tipo_componente_menu = $("#tipo_componente_menu").val();
        var id_plato = $("#id_plato").val();      
        var precio = $("#precio").val();
        var stock = $("#stock").val();
        var inicio = $("#inicio").val();
        var fin = $("#fin").val();
        if(parseInt(id)>0){
            location.href = "componente_menu.php?o=2&i="+id+"&tm="+id_tipo_menu+"&tc="+id_tipo_componente_menu+"&pl="+id_plato+"&pr="+precio+"&st="+stock+"&in="+inicio+"&fn="+fin;
        }else{
            location.href = "componente_menu.php?o=1&i="+id+"&tm="+id_tipo_menu+"&tc="+id_tipo_componente_menu+"&pl="+id_plato+"&pr="+precio+"&st="+stock+"&in="+inicio+"&fn="+fin;
        }
    }
    
    $(document).ready(function() {
        $('#tb').DataTable();
        $("#tipo_menu").select2();
        $("#tipo_componente_menu").select2();
        $("#id_plato").select2();
        $("#tipo_menu").focus();
    });
</script>

                            