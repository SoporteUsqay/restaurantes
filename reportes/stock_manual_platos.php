<?php
$titulo_pagina = 'Stock Manual Platos';
$titulo_sistema = 'Usqay2';

include_once('recursos/componentes/MasterConexion.php');
$conn = new MasterConexion();
$sucursal = "SU009";
$tipo = "1";
$plato = "PL0001";
if(isset($_GET["s"])){
    $sucursal = $_GET["s"];
}
if(isset($_GET["t"])){
    $tipo = $_GET["t"];
}
if(isset($_GET["p"])){
    $plato = $_GET["p"];
}
if(isset($_GET["nstock"])){
    $existe = $conn->consulta_arreglo("Select * from plato_stock where id_plato = '".$plato."'");
    if(is_array($existe)){
        $conn->consulta_simple("Update plato_stock set stock = '".$_GET["nstock"]."' where id = '".$existe["id"]."'");
    }else{
        $conn->consulta_simple("Insert into plato_stock values (NULL,'".$plato."','".$_GET["nstock"]."')");
    }
}

if(isset($_GET["del"])){
    $conn->consulta_simple("Delete from plato_stock where id = '".$_GET["del"]."'");
}
$datos_sucursal = $conn->consulta_arreglo("Select * from sucursal where pkSucursal = '" . $sucursal . "'");
require_once('recursos/componentes/header.php'); 
?>
<?php if(isset($_GET["nstock"]) || isset($_GET["del"])):?>
<div class="alert alert-success alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Stock Actualizado con Éxito
</div>
<?php endif;?>
<link rel="stylesheet" href="recursos/js/plugins/tablas/jquery.dataTables.min.css">
<h1>Stock Manual Platos</h1>

<div class="panel panel-primary" id='pplatos'>
    <div class="panel-heading">
        <h3 class="panel-title">Selecciona el Plato</h3>
    </div>
    <div class="panel-body">
        <div class='control-group'>
            <label>Tipo</label>
            <select class="form-control" id="id_tipo" onchange="actualiza_tipo()">
                <?php 
                    $tipos = $conn->consulta_matriz("Select * from tipos where estado = 0");
                    if(is_array($tipos)){
                        foreach ($tipos as $tp){
                            if($tp["pkTipo"] == $tipo){
                                echo "<option value='".$tp["pkTipo"]."' selected>".utf8_encode($tp["descripcion"])."</option>";
                            }else{
                                echo "<option value='".$tp["pkTipo"]."'>".utf8_encode($tp["descripcion"])."</option>";
                            }
                        }
                    }
                ?>
            </select>
        </div>
        <div class='control-group'>
            <label>Plato</label>
            <select class="form-control" id="id_plato">
                <?php 
                    $platos = $conn->consulta_matriz("Select * from plato where estado = 0 AND pkTipo = '".$tipo."'");
                    $flag = 0;
                    if(is_array($platos)){
                        foreach ($platos as $pl){
                            if($pl["pkPlato"] == $plato){
                                echo "<option value='".$pl["pkPlato"]."' selected>".utf8_encode($pl["descripcion"])."</option>";
                            }else{
                                echo "<option value='".$pl["pkPlato"]."'>".utf8_encode($pl["descripcion"])."</option>";
                                if($flag == 0){
                                    $plato = $pl["pkPlato"];
                                    $flag = 1;
                                }
                            }
                        }
                    }
                ?>
            </select>
        </div>
        <div class='control-group'>
            <label>Nuevo Stock</label>
            <input class="form-control" id="nstock" value="1" type="number" step="1" min="1">             
        </div>

<div class='control-group'>
    <p></p>
    <button type='button' class='btn btn-primary' onclick='insertar_stock()'>Actualizar</button>
</div>
    </div>
</div>
</form>
<hr/>
<?php 
    $rtabla = $conn->consulta_matriz("Select ps.id,ps.stock,p.descripcion as plato,t.descripcion as tipo from plato_stock ps,plato p, tipos t where CONVERT(ps.id_plato USING utf8) COLLATE utf8_spanish2_ci = CONVERT(p.pkPlato USING utf8) COLLATE utf8_spanish2_ci AND p.pktipo = t.pkTipo");
?>
<div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%' border="0">
        <thead>
            <tr>
                <th>Plato</th>
                <th>Tipo</th>
                <th>Stock</th>
                <th>Opciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if(is_array($rtabla)):
              foreach($rtabla as $rw):?>
            <tr>
                <td><?php echo utf8_encode($rw["plato"]);?></td>
                <td><?php echo utf8_encode($rw["tipo"]);?></td>
                <td><?php echo $rw["stock"];?></td>
                <td><a href="stock_manual_platos.php?del=<?php echo $rw["id"];?>">Quitar Stock</a></td>
            </tr>
        <?php 
            endforeach;
            endif;
        ?>
<?php
$nombre_tabla = 'stock';
require_once('recursos/componentes/footer.php');
?>
<script src="recursos/js/plugins/tablas/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function () {
        $('#tb').DataTable();
    });

    function actualiza_tipo(){
        location.href = "stock_manual_platos.php?s=<?php echo $sucursal;?>&t="+$("#id_tipo").val();
    }
    function insertar_stock(){
        location.href = "stock_manual_platos.php?s=<?php echo $sucursal;?>&t="+$("#id_tipo").val()+"&p="+$("#id_plato").val()+"&nstock="+$("#nstock").val();
    }
</script>


                            