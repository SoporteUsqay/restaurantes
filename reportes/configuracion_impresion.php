<?php
$titulo_pagina = 'Configuracion Impresion';
$titulo_sistema = 'Usqay2';

include_once('recursos/componentes/MasterConexion.php');
$conn = new MasterConexion();
$id = NULL;
$terminal = $_COOKIE["t"];
$impresora = NULL;
$opcion = NULL;

$op = NULL;

if(isset($_GET["i"])){
    $id = $_GET["i"];
}

if(isset($_GET["im"])){
    $impresora = $_GET["im"];
}

if(isset($_GET["op"])){
    $opcion = $_GET["op"];
}


if(isset($_GET["o"])){
    $op = $_GET["o"];
}

switch(intval($op)){
    case 1:
        //Creamos configuracion o configuraciones
        $tipos = explode(",",$impresora);
        foreach($tipos as $tp){
            $conn->consulta_simple("Insert into configuracion_impresion values(NULL,'".$terminal."','".str_replace('\\', '\\\\', $tp)."','".$opcion."')");
        }   
    break;

    case 3:
        //Eliminamos configuracion
        $conn->consulta_simple("Delete from configuracion_impresion where id = '".$id."'");
    break;
}

require_once('recursos/componentes/header.php'); 
?>
<link rel="stylesheet" href="recursos/js/plugins/tablas/jquery.dataTables.min.css">
<?php if(intval($op)>0):?>
<div class="alert alert-success alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Operación Realizada con Éxito
</div>
<?php endif;?>

<h1>Configuracion Impresion</h1>

<div class="panel panel-primary" id='pplatos'>
    <div class="panel-heading">
        <h3 class="panel-title">Terminal: <?php echo $_COOKIE["t"];?></h3>
    </div>
    <div class="panel-body">
        <input type="hidden" id="id" value=""/>
        <div class='control-group'>
            <label>Opcion</label>
            <select class="form-control" id="opcion">
                <?php 
                    $tipos1 = $conn->consulta_matriz("Select * from tipos where estado = 0 AND not exists (Select 1 from configuracion_impresion where configuracion_impresion.opcion = tipos.pkTipo AND terminal = '".$_COOKIE["t"]."')");
                    if(is_array($tipos1)){
                        foreach ($tipos1 as $tp){
                            echo "<option value='".$tp["pkTipo"]."'>".utf8_encode($tp["descripcion"])."</option>";
                        }
                    }
                ?>
                
                <?php 
                $v1 = $conn->consulta_arreglo("Select * from configuracion_impresion where opcion = 'b' AND terminal = '".$_COOKIE["t"]."'");
                if(!is_array($v1)):
                ?>
                <option value='b'>BOLETA</option>
                <?php endif;?>
                <?php 
                $v2 = $conn->consulta_arreglo("Select * from configuracion_impresion where opcion = 'f' AND terminal = '".$_COOKIE["t"]."'");
                if(!is_array($v2)):
                ?>
                <option value='f'>FACTURA</option>
                <?php endif;?>
                <?php 
                $v3 = $conn->consulta_arreglo("Select * from configuracion_impresion where opcion = 'c' AND terminal = '".$_COOKIE["t"]."'");
                if(!is_array($v3)):
                ?>
                <option value='c'>CUENTA</option>
                <?php endif;?>
                <?php 
                $v4 = $conn->consulta_arreglo("Select * from configuracion_impresion where opcion = 'cr' AND terminal = '".$_COOKIE["t"]."'");
                if(!is_array($v4)):
                ?>
                <option value='cr'>CREDITO</option>
                <?php endif;?>
                <?php 
                $v5 = $conn->consulta_arreglo("Select * from configuracion_impresion where opcion = 'co' AND terminal = '".$_COOKIE["t"]."'");
                if(!is_array($v5)):
                ?>
                <option value='co'>CONSUMO</option>
                <?php endif;?>
            </select>
        </div>
        <div class='control-group'>
            <label>Impresora</label>
            <select class="form-control" id="impresora" multiple="multiple">
                <?php 
                    $printers = $conn->consulta_matriz("Select * from impresoras");
                    if(is_array($printers)){
                        foreach ($printers as $tp){
                            echo "<option value='".$tp["nombre"]."'>".utf8_encode($tp["nombre"])."</option>";
                        }
                    }
                ?>
            </select>
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
    $rtabla = $conn->consulta_matriz("Select * from configuracion_impresion where terminal = '".$_COOKIE["t"]."'");
?>
<div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%' border="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Terminal</th>
                <th>Impresora</th>
                <th>Opcion</th>
                <th>OPC</th>
            </tr>
        </thead>
        <tbody>
        <?php if(is_array($rtabla)):
              foreach($rtabla as $rw):?>
            <tr>
                <td><?php echo $rw["id"];?></td>
                <td><?php echo $_COOKIE["t"];?></td>
                <td><a href="#" onclick="ver_margenes('<?php echo str_replace('\\','\\\\',$rw["impresora"]);?>')"><?php echo $rw["impresora"];?></a></td>
                <td><?php
                if(!is_numeric($rw["opcion"])){
                    switch($rw["opcion"]){
                        case 'b': echo "Boleta";
                        break;
                    
                        case 'f': echo "Factura";
                        break;
                    
                        case 'c': echo "Cuenta";
                        break;

                        case 'cr': echo "Credito";
                        break;

                        case 'co': echo "Consumo";
                        break;
                    }
                }else{
                    $query = "Select * from tipos where pkTipo = '".$rw["opcion"]."'";
                    $r0 = $conn->consulta_arreglo($query);
                    if(is_array($r0)){
                        echo $r0["descripcion"];
                    }
                }
                ?></td>
                <td>
                    <a href="configuracion_impresion.php?o=3&i=<?php echo $rw["id"];?>">Eliminar</a>
                </td>
            </tr>
        <?php 
            endforeach;
            endif;
        ?>
</tbody>
</table>
</div> <!--contenedor tabla-->
   </div><!--/row-->
      <hr>
    </div><!--/.container-->
    
    
    <div class="modal fade" tabindex="-1" role="dialog" id="modal_ajuste">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Porcentaje de Ajuste</h4>
      </div>
      <div class="modal-body">
          <span id="nimpresora">Nombre Impresora</span>
          <p></p>
          <p><input type="number" id="ratio" step="0.1" value="0.0"></p>
          <input type="hidden" id="nombre_impresora" value=""/>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="guardar_ajuste()">Guardar Cambios</button>
      </div>
    </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="recursos/js/jquery.js"></script>
    <script src="recursos/js/jquery-ui.js"></script>
    <script src="recursos/js/configuracion_impresion.js"></script>
    <script src="recursos/js/bootstrap.min.js"></script>
    <script src="recursos/js/offcanvas.js"></script>
    <script src="recursos/js/plugins/tablas/jquery.dataTables.min.js"></script>
    <script src="../Public/select2/js/select2.js"></script>
    
    <script>
    function ver_margenes(nombre){
        $("#nimpresora").html("Impresora: "+nombre);
        $("#nombre_impresora").val(nombre);
        $.post( "ws/margenes.php", {op:'getmargen',impresora:nombre}, function( data ) {
            $("#ratio").val(data);
        }, "json");
        $("#modal_ajuste").modal("show");
    }
    
    function guardar_ajuste(){
        var nombre = $("#nombre_impresora").val();
        var ratio = $("#ratio").val();
        $("#nimpresora").html("Impresora: "+nombre);
        $.post( "ws/margenes.php", {op:'addmargen',impresora:nombre,ratio:ratio}, function( data ) {
            $("#modal_ajuste").modal("hide");
        }, "json");
    }  
        
    function guardar(){
        var id = $("#id").val();
        var impresora = $("#impresora").val();
        var opcion = $("#opcion").val();
        if(parseInt(id)>0){
            location.href = "configuracion_impresion.php?o=2&i="+id+"&im="+impresora+"&op="+opcion;
        }else{
            location.href = "configuracion_impresion.php?o=1&i="+id+"&im="+impresora+"&op="+opcion;
        }
    }
    
    $(document).ready(function () {
        $('#opcion').select2();
        $('#impresora').select2(); 
    });
    </script>
  </body>
</html>

                            