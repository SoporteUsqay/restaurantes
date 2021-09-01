<?php
$titulo_pagina = 'Platos Amarrados';
$titulo_sistema = 'Usqay2';

include_once('recursos/componentes/MasterConexion.php');
$conn = new MasterConexion();

$id = NULL;
$pk1 = NULL;
$c1 = NULL;
$pk2 = NULL;
$c2 = NULL;

$op = NULL;

if(isset($_GET["id"])){
    $id = $_GET["id"];
}

if(isset($_GET["pk1"])){
    $pk1 = $_GET["pk1"];
}

if(isset($_GET["c1"])){
    $c1 = $_GET["c1"];
}
if(isset($_GET["pk2"])){
    $pk2 = $_GET["pk2"];
}

if(isset($_GET["c2"])){
    $c2 = $_GET["c2"];
}


if(isset($_GET["op"])){
    $op = $_GET["op"];
}

switch(intval($op)){
    case 1:
        $conn->consulta_simple("Insert into platos_amarrados values(NULL,'".$pk1."','".$c1."','".$pk2."','".$c2."')");
    break;

    case 2:
        $conn->consulta_simple("Update platos_amarrados set pkPlato_1 = '".$pk1."', pkPlato_2 = '".$pk2."', cantidad_1 = '".$c1."', cantidad_2 = '".$c2."' where id = '".$id."'");
    break;

    case 3:
        $conn->consulta_simple("Delete from platos_amarrados where id = '".$id."'");
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
<h1>Platos Amarrados</h1>

<div class="panel panel-primary" id='pplatos'>
    <div class="panel-heading">
        <h3 class="panel-title">Selecciona un plato y que plato se autoagregará</h3>
    </div>
    <div class="panel-body">
        <input type="hidden" id="id" value=""/>
        <div class='control-group'>
            <label>Plato</label>
            <select class="form-control" id="pk1">
                <?php 
                    $platos = $conn->consulta_matriz("Select * from plato where estado = 0");
                    if(is_array($platos)){
                        foreach ($platos as $tp){
                            echo "<option value='".$tp["pkPlato"]."'>".utf8_encode($tp["descripcion"])." - S/ ".$tp["precio_venta"]."</option>";
                        }
                    }
                ?>
            </select>
        </div>
        
        <div class='control-group'>
            <label>Cantidad</label>
            <input type="number" value="1" id="c1" class="form-control">
        </div>
        
        <div class='control-group'>
            <label>Plato a agregar</label>
            <select class="form-control" id="pk2">
                <?php 
                    if(is_array($platos)){
                        foreach ($platos as $tp){
                            echo "<option value='".$tp["pkPlato"]."'>".utf8_encode($tp["descripcion"])." - S/ ".$tp["precio_venta"]."</option>";
                        }
                    }
                ?>
            </select>
        </div>
        
        <div class='control-group'>
            <label>Cantidad a agregar</label>
            <input type="number" value="1" id="c2" class="form-control">
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
    $rtabla = $conn->consulta_matriz("Select * from platos_amarrados");
?>
<div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%' border="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Plato</th>
                <th>Cantidad</th>
                <th>Plato Agregado</th>
                <th>Cantidad Agregada</th>
                <th>OPC</th>
            </tr>
        </thead>
        <tbody>
        <?php if(is_array($rtabla)):
              foreach($rtabla as $rw):?>
            <tr>
                <td><?php echo $rw["id"];?></td>
                <td><?php
                $p1 = $conn->consulta_arreglo("Select * from plato where pkPlato = '".$rw["pkPlato_1"]."'");
                echo $p1["descripcion"];
                ?></td>
                <td><?php echo $rw["cantidad_1"];?></td>
                <td><?php
                $p2 = $conn->consulta_arreglo("Select * from plato where pkPlato = '".$rw["pkPlato_2"]."'");
                echo $p2["descripcion"];
                ?></td>
                <td><?php echo $rw["cantidad_2"];?></td>
                <td>
                    <a href="platos_amarrados.php?op=3&id=<?php echo $rw["id"];?>">Eliminar</a>
                    <br/>
                    <a onclick="edita(<?php echo $rw["id"];?>,'<?php echo $rw["pkPlato_1"];?>',<?php echo $rw["cantidad_1"];?>,'<?php echo $rw["pkPlato_2"];?>',<?php echo $rw["cantidad_2"];?>)">Editar</a>
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
    

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="recursos/js/jquery.js"></script>
    <script src="recursos/js/jquery-ui.js"></script>
    <script src="recursos/js/bootstrap.min.js"></script>
    <script src="recursos/js/offcanvas.js"></script>
    <script src="../Public/select2/js/select2.js"></script>
    <script src="recursos/js/plugins/tablas/jquery.dataTables.min.js"></script>
    <script>
    var id_edicion = 0;
    
    function edita(id,p1,c1,p2,c2){
        id_edicion = id;
        $("#pk1").val(p1);
        $("#c1").val(c1);
        $("#pk2").val(p2);
        $("#c2").val(c2);
    }
    
    function guardar(){
        var plato1 = $("#pk1").val();
        var plato2 = $("#pk2").val();
        var c1 = $("#c1").val();
        var c2 = $("#c2").val();
        if(id_edicion > 0){
            location.href = "platos_amarrados.php?op=2&id="+id_edicion+"&pk1="+plato1+"&c1="+c1+"&pk2="+plato2+"&c2="+c2;
        }else{
            location.href = "platos_amarrados.php?op=1&id=0&pk1="+plato1+"&c1="+c1+"&pk2="+plato2+"&c2="+c2;
        }
    }
    
    jQuery.fn.reset = function () {
        $(this).each(function () {
            this.reset();
        });
    };


    $(document).ready(function () {
        $('#pk1').select2();
        $('#pk2').select2();
        history.pushState(null, "", 'platos_amarrados.php');
        $('#tb').DataTable(); 
    });

    </script>
  </body>
</html>

                            