<?php
$titulo_pagina = 'Permisos Trabajador';
$titulo_sistema = 'Usqay2';

include_once('recursos/componentes/MasterConexion.php');
$conn = new MasterConexion();
$sucursal = "SU009";
$trabajador = "0";
if(isset($_GET["s"])){
    $sucursal = $_GET["s"];
}
if(isset($_GET["idt"])){
    $trabajador = $_GET["idt"];
}

$datos_sucursal = $conn->consulta_arreglo("Select * from sucursal where pkSucursal = '" . $sucursal . "'");
require_once('recursos/componentes/header.php'); 
?>

<div class="panel panel-primary" id='pplatos'>
    <div class="panel-heading">
        <h3 class="panel-title">Asigne los Permisos al Trabajador</h3>
    </div>
    <div class="panel-body">
        <?php 
            $modulos = $conn->consulta_matriz("Select * from module where estadoModule = '0'");
            if(is_array($modulos)):
                foreach($modulos as $mod):
        ?>
        <input type="checkbox" id="mod<?php echo $mod["pkModule"];?>" onchange="change_module(<?php echo $trabajador;?>,<?php echo $mod["pkModule"];?>)" <?php 
        $existe = $conn->consulta_arreglo("Select * from trabajador_modulo where id_trabajador = '".$trabajador."' AND id_modulo = '".$mod["pkModule"]."'");
        if(is_array($existe)){
            echo "checked";
        }
        ?>> <?php echo $mod["nameModule"];?><br/>
            <?php 
                $submodulos = $conn->consulta_matriz("Select * from submodule where fkModule = '".$mod["pkModule"]."' AND status = '0'");
                if(is_array($submodulos)):
                    foreach($submodulos as $smod):
            ?>
                <input style="margin-left:10px;" type="checkbox" id="smod<?php echo $smod["pkSubModule"];?>" onchange="change_submodule(<?php echo $trabajador;?>,<?php echo $smod["pkSubModule"];?>)" <?php 
                $existe0 = $conn->consulta_arreglo("Select * from trabajador_submodulo where id_trabajador = '".$trabajador."' AND id_submodulo = '".$smod["pkSubModule"]."'");
                if(is_array($existe0)){
                    echo "checked";
                }
                ?>> <?php echo $smod["nameSubModule"];?><br/>
            <?php
                    endforeach;
                endif;
            ?>
        <?php   
                endforeach;
            endif;
        ?>
        


        <div class='control-group'>
            <p></p>
            <button type='button' class='btn btn-primary' onclick='terminar()'>Terminar</button>
        </div>
    </div>
</div>
</form>

<div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%' border="0">
        <thead>
        </thead>
        <tbody>
<?php
$nombre_tabla = 'permisos';
require_once('recursos/componentes/footer.php');
?>

<script>
    function terminar(){
        location.href = "../?controller=WorkPeople&action=Personal";
    }
    
    function change_module(tr,mod){  
        if($("#mod"+mod).is(':checked')) { 
            $.post('ws/permisos.php', {op: 'addmodule',modulo:mod,trabajador:tr}, function(data) {
                if(data === 0){
                    $('#merror').show('fast').delay(4000).hide('fast');
                }
                else{
                    $('#msuccess').show('fast').delay(4000).hide('fast');
                }
            }, 'json');
        }else{
           $.post('ws/permisos.php', {op: 'removemodule',modulo:mod,trabajador:tr}, function(data) {
                if(data === 0){
                    $('#merror').show('fast').delay(4000).hide('fast');
                }
                else{
                    $('#msuccess').show('fast').delay(4000).hide('fast');
                }
            }, 'json'); 
        }
    }
    
    function change_submodule(tr,smod){
        if($("#smod"+smod).is(':checked')) { 
            $.post('ws/permisos.php', {op: 'addsubmodule',submodulo:smod,trabajador:tr}, function(data) {
                if(data === 0){
                    $('#merror').show('fast').delay(4000).hide('fast');
                }
                else{
                    $('#msuccess').show('fast').delay(4000).hide('fast');
                }
            }, 'json');
        }else{
           $.post('ws/permisos.php', {op: 'removesubmodule',submodulo:smod,trabajador:tr}, function(data) {
                if(data === 0){
                    $('#merror').show('fast').delay(4000).hide('fast');
                }
                else{
                    $('#msuccess').show('fast').delay(4000).hide('fast');
                }
            }, 'json'); 
        }       
    }

</script>


                            