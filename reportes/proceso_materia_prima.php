<?php
$titulo_pagina = 'Proceso Materia Prima';
$titulo_sistema = 'Usqay2';

include_once('recursos/componentes/MasterConexion.php');
$conn = new MasterConexion();
$sucursal = "SU009";
$materia_prima = "0";
$insumo = "0";
$guia_salida = "0";
$guia_entrada = "0";
$exito = -1;
if(isset($_GET["s"])){
    $sucursal = $_GET["s"];
}
if(isset($_GET["mp"])){
    $materia_prima = $_GET["mp"];
}
if(isset($_GET["i"])){
    $insumo = $_GET["i"];
}
if(isset($_GET["gs"])){
    $guia_salida = $_GET["gs"];
}
if(isset($_GET["ge"])){
    $guia_entrada = $_GET["ge"];
}
if(isset($_GET["ins"])){
    //PROCESO INSERSION
    if(intval($insumo) == 0){
        //Solo sacamos la materia prima
        $conn->consulta_simple("Insert into ingresoinsumos values(NULL,'".$_GET["cant"]."',' ','".$materia_prima."','".$sucursal."','".date("Y-m-d")."','".date("Y-m-d h:i:s")."','1','2','0','1','".$guia_salida."','0.00')");
        $q0 = "UPDATE historial_stock_insumos SET cantidadFinal = cantidadFinal - ".$_GET["cant"]." WHERE  pkInsumo = '".$materia_prima."' AND fecha >= '".date("Y-m-d")."'";
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 0;");
        $conn->consulta_simple($q0);

        $q1 = "UPDATE historial_stock_insumos SET cantidadInicial = cantidadInicial - ".$_GET["cant"]." WHERE  pkInsumo = '".$materia_prima."' AND fecha > '".date("Y-m-d")."'";
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 0;");
        $conn->consulta_simple($q1);
    }else{
        //sacamos materia prima
        $conn->consulta_simple("Insert into ingresoinsumos values(NULL,'".$_GET["cant"]."',' ','".$materia_prima."','".$sucursal."','".date("Y-m-d")."','".date("Y-m-d h:i:s")."','1','2','0','1','".$guia_salida."','0.00')");
        
        $q0 = "UPDATE historial_stock_insumos SET cantidadFinal = cantidadFinal - ".$_GET["cant"]." WHERE  pkInsumo = '".$materia_prima."' AND fecha >= '".date("Y-m-d")."'";
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 0;");
        $conn->consulta_simple($q0);

        $q1 = "UPDATE historial_stock_insumos SET cantidadInicial = cantidadInicial - ".$_GET["cant"]." WHERE  pkInsumo = '".$materia_prima."' AND fecha > '".date("Y-m-d")."'";
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 0;");
        $conn->consulta_simple($q1);
        
        //Ingresamos insumo
         $conn->consulta_simple("Insert into ingresoinsumos values(NULL,'".$_GET["cant"]."',' ','".$insumo."','".$sucursal."','".date("Y-m-d")."','".date("Y-m-d h:i:s")."','1','1','0','1','".$guia_entrada."','0.00')");
         
         $q0 = "UPDATE historial_stock_insumos SET cantidadFinal = cantidadFinal + ".$_GET["cant"]." WHERE  pkInsumo = '".$insumo."' AND fecha >= '".date("Y-m-d")."'";
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 0;");
        $conn->consulta_simple($q0);

        $q1 = "UPDATE historial_stock_insumos SET cantidadInicial = cantidadInicial + ".$_GET["cant"]." WHERE  pkInsumo = '".$insumo."' AND fecha > '".date("Y-m-d")."'";
        $conn->consulta_simple("SET SQL_SAFE_UPDATES = 0;");
        $conn->consulta_simple($q1);
    }
    $exito = 2;
}
if(isset($_GET["gen"])){
    //PROCESO GENERACION
    $guia_entrada = $conn->consulta_id("Insert into comprobante_ingreso values(NULL,3,'".time()."','".date("Y-m-d h:i:s")."','".date("Y-m-d h:i:s")."',NULL,NULL,'0','1','1')");
    $guia_salida = $conn->consulta_id("Insert into comprobante_ingreso values(NULL,4,'".time()."','".date("Y-m-d h:i:s")."','".date("Y-m-d h:i:s")."',NULL,NULL,'0','1','1')");
    $exito = 1;
}
$datos_sucursal = $conn->consulta_arreglo("Select * from sucursal where pkSucursal = '" . $sucursal . "'");
require_once('recursos/componentes/header.php'); 
?>
<?php if($exito == 1):?>
<div class="alert alert-success alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Ticket generado con éxito
</div>
<?php endif;?>
<?php if($exito == 2):?>
<div class="alert alert-success alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Proceso guardado éxito
</div>
<?php endif;?>

<h1>Proceso Materia Prima</h1>
<?php if($guia_entrada == 0):?>
    <div class="panel panel-primary" id='pplatos'>
    <div class="panel-heading">
        <h3 class="panel-title">Generar Ticket de Entrada y Salida</h3>
    </div>
        <div class="panel-body">
            <div class='control-group'>
                <p></p>
                <button type='button' class='btn btn-primary' onclick='inicializar()'>Iniciar Proceso</button>
            </div>
        </div>
    </div>
<?php endif;?>

<?php if($guia_entrada > 0):?>
<div class="panel panel-primary" id='pplatos'>
    <div class="panel-heading">
        <h3 class="panel-title">Selecciona la matera Prima y el insumo en el que se convertirá</h3>
    </div>
    <div class="panel-body">
        <div class='control-group'>
            <label>Materia Prima</label>
            <select class="form-control" id="id_materia_prima">
                <?php 
                    $mps = $conn->consulta_matriz("Select * from insumos where estado = 2");
                    if(is_array($mps)){
                        foreach ($mps as $tp){
                            if($tp["pkInsumo"] == $materia_prima){
                                echo "<option value='".$tp["pkInsumo"]."' selected>".utf8_encode($tp["descripcionInsumo"])."</option>";
                            }else{
                                echo "<option value='".$tp["pkInsumo"]."'>".utf8_encode($tp["descripcionInsumo"])."</option>";
                            }
                        }
                    }
                ?>
            </select>
        </div>
        <div class='control-group'>
            <label>Insumo</label>
            <select class="form-control" id="id_insumo">
                <option value='0'>Sin Insumo</option>
                <?php 
                    $ins = $conn->consulta_matriz("Select * from insumos where estado = 0");
                    if(is_array($ins)){
                        foreach ($ins as $tp){
                            if($tp["pkInsumo"] == $insumo){
                                echo "<option value='".$tp["pkInsumo"]."' selected>".utf8_encode($tp["descripcionInsumo"])."</option>";
                            }else{
                                echo "<option value='".$tp["pkInsumo"]."'>".utf8_encode($tp["descripcionInsumo"])."</option>";
                            }
                        }
                    }
                ?>
            </select>
        </div>
        <div class='control-group'>
            <label>Cantidad</label>
            <input class="form-control" id="cantidad" value="1" type="number" step="0.001" min="0.001">
        </div>

<div class='control-group'>
    <p></p>
    <button type='button' class='btn btn-primary' onclick='procesar()'>Procesar</button>
    <button type='button' class='btn btn-alert' onclick='terminar()'>Terminar</button>
</div>
    </div>
</div>
<?php endif;?>
</form>

<div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%' border="0">
        <thead>
        </thead>
        <tbody>
<?php
$nombre_tabla = 'stock';
require_once('recursos/componentes/footer.php');
?>

<script>
    function procesar(){
        location.href = "proceso_materia_prima.php?s=<?php echo $sucursal;?>&ins=TRUE&ge=<?php echo $guia_entrada;?>&gs=<?php echo $guia_salida;?>&mp="+$("#id_materia_prima").val()+"&i="+$("#id_insumo").val()+"&cant="+$("#cantidad").val();
    }
    function inicializar(){
        location.href = "proceso_materia_prima.php?s=<?php echo $sucursal;?>&gen=TRUE";
    }
    function terminar(){
        location.href = "proceso_materia_prima.php";
    }
</script>


                            