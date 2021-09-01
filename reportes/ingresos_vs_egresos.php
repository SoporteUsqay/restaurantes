<?php
$titulo_pagina = 'Ingresos VS Egresos';
$titulo_sistema = 'usqay2';

include_once('recursos/componentes/MasterConexion.php');
include_once('../Components/Library/Framework/SuperDataBase.php');
$conn = new MasterConexion();

$ano_bus = date("Y");
$inicio_ = date("Y-m-d");
$fin_ = date("Y-m-d");
$tipo_bus = "a";

if(isset($_GET["a"])){
    $ano_bus = $_GET["a"];
}

if(isset($_GET["i"])){
    $inicio_ = $_GET["i"];
}

if(isset($_GET["f"])){
    $fin_ = $_GET["f"];
}

if(isset($_GET["t"])){
    $tipo_bus = $_GET["t"];
}

if($tipo_bus == "a"){
    $titulo_pagina .= " ".$ano_bus;
}else{
    $titulo_pagina .= " del ".$inicio_." al ".$fin_;
}

//Obtenemos todos los medios de pago validos
$medios = array(); 
$resultado_medios = $conn->consulta_matriz("Select mp.id, mp.moneda, mp.nombre, m.simbolo from medio_pago mp, moneda m where mp.estado > 0 AND mp.moneda = m.id");
if(is_array($resultado_medios)){
    foreach($resultado_medios as $medio){
        if(intval($medio["id"]) === 1){
            $resultado_monedas = $conn->consulta_matriz("Select * from moneda where estado > 0");
            if(is_array($resultado_monedas)){
                foreach($resultado_monedas as $moneda){
                    $tmp = array();
                    $tmp["nombre"] = $medio["nombre"]." ".$moneda["simbolo"];
                    $tmp["id_medio"] = $medio["id"];
                    $tmp["id_moneda"] = $moneda["id"];
                    $medios[] = $tmp;
                }
            }
        }else{
            $tmp = array();
            $tmp["nombre"] = $medio["nombre"]." ".$medio["simbolo"];
            $tmp["id_medio"] = $medio["id"];
            $tmp["id_moneda"] = $medio["moneda"];
            $medios[] = $tmp;
        }
    }
}

//Obtenemos todas las monedas
$monedas = array();
$resultado_monedas = $conn->consulta_matriz("Select * from moneda where estado > 0");
if(is_array($resultado_monedas)){
    foreach($resultado_monedas as $moneda){
        $tmp = array();
        $tmp["id"] = $moneda["id"];
        $tmp["nombre"] = $moneda["nombre"];
        $tmp["simbolo"] = $moneda["simbolo"];
        $monedas[] = $tmp;
    }
}

require_once('recursos/componentes/header.php'); 
?>
<link rel="stylesheet" href="recursos/js/plugins/tablas/jquery.dataTables.min.css">
<style>
    .dt-buttons{
        margin-bottom: 10px !important;
    }
</style>
</form>

<div>
  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" <?php if($tipo_bus == "a"){echo "class='active'";}?>><a href="#anual" aria-controls="anual" role="tab" data-toggle="tab">Anual</a></li>
    <li role="presentation" <?php if($tipo_bus == "d"){echo "class='active'";}?>><a href="#fechas" aria-controls="fechas" role="tab" data-toggle="tab">Entre Fechas</a></li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane <?php if($tipo_bus == "a"){echo "active";}?>" id="anual">
        <h1>Ingresos VS Egresos <?php echo $ano_bus;?></h1>

        <div class="panel panel-primary" id='pfecha'>
        <div class="panel-heading">
        <h3 class="panel-title">Año Reporte</h3>
        </div>
        <div class="panel-body row">
        <div class='control-group col-md-10'>
            <label>Año</label>
            <input class="form-control" type="number" id="ano_reporte" value="<?php echo $ano_bus;?>"/>
        </div>

        <div class='control-group col-md-2'>
            <label> </label>
            <p>
                <button type='button' class='btn btn-primary' onclick='filtrar_a()'>Filtrar</button>
            </p>
        </div>

        </div>
        </div>
        <hr/>
        <div class='contenedor-tabla' style="overflow-x: auto;">
        <table class='tb display' cellspacing='0' width='100%' border="0">
        <thead>
        <tr>
            <th></th>
            <?php
            foreach($monedas as $mo){
                echo "<th>INICIALES ".$mo["simbolo"]."</th>";
            }
            ?>
            <?php
            foreach($medios as $med){
                echo "<th>VENTAS ".$med["nombre"]."</th>";
            }
            ?>
            <?php
            foreach($medios as $med){
                echo "<th>INGRESO ADICIONAL ".$med["nombre"]."</th>";
            }
            ?>
            <?php
            foreach($medios as $med){
                echo "<th>SALIDAS ".$med["nombre"]."</th>";
            }
            ?>
            <?php
            foreach($medios as $med){
                echo "<th>SALDO FINAL ".$med["nombre"]."</th>";
            }
            ?>
        </tr>
    </thead>
    <tbody>
        <?php 
        $meses = array("Miau","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        for($i=1;$i<=12;$i++):?>
        <tr>
            <td><?php echo $meses[$i];?></td>
            <?php
            $finales = array();
            foreach($monedas as $mo){
                echo "<td style='color: #00ff00'>";
                $consulta_iniciales = "select sum(monto) as resultado from movimiento_dinero where id_medio = '1' AND moneda = '".$mo["id"]."' AND tipo_origen = 'CUT' AND year(fecha_cierre) = '".$ano_bus."' AND month(fecha_cierre) = '".$i."' AND estado = 1";
                //echo $consulta_ventas;
                $resultado_iniciales = $conn->consulta_arreglo($consulta_iniciales);
                echo round(floatval($resultado_iniciales["resultado"]),2);
                echo "</td>";
            }
            ?>
            <?php
            $finales = array();
            foreach($medios as $med){
                echo "<td style='color: #0033ff'>";
                $consulta_ventas = "select sum(monto) as resultado from movimiento_dinero where id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND tipo_origen = 'PED' AND year(fecha_cierre) = '".$ano_bus."' AND month(fecha_cierre) = '".$i."' AND estado = 1";
                //echo $consulta_ventas;
                $resultado_ventas = $conn->consulta_arreglo($consulta_ventas);
                $finales[$med["nombre"]] = floatval($resultado_ventas["resultado"]);
                echo round(floatval($resultado_ventas["resultado"]),2);
                echo "</td>";
            }
            ?>
            <?php
            foreach($medios as $med){
                echo "<td style='color: #1B8990'>";
                $consulta_ingresos = "select sum(monto) as resultado from movimiento_dinero where id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND tipo_origen = 'GAS' AND year(fecha_cierre) = '".$ano_bus."' AND month(fecha_cierre) = '".$i."' AND estado = 1 AND id_aux = 1";
                $resultado_ingresos = $conn->consulta_arreglo($consulta_ingresos);
                $finales[$med["nombre"]] = $finales[$med["nombre"]] + floatval($resultado_ingresos["resultado"]);
                echo round(floatval($resultado_ingresos["resultado"]),2);
                echo "</td>";
            }
            ?>
            <?php
            foreach($medios as $med){
                echo "<td style='color: #ff3333'>";
                $consulta_salidas = "select sum(monto) as resultado from movimiento_dinero where id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND tipo_origen = 'GAS' AND year(fecha_cierre) = '".$ano_bus."' AND month(fecha_cierre) = '".$i."' AND estado = 1 AND id_aux = 0";
                $resultado_salidas = $conn->consulta_arreglo($consulta_salidas);
                $finales[$med["nombre"]] = $finales[$med["nombre"]] + floatval($resultado_salidas["resultado"]);
                echo round(floatval($resultado_salidas["resultado"]),2);
                echo "</td>";
            }
            ?>
            <?php
            foreach($finales as $fin){
                echo "<td style='color: #0033ff'>";
                echo round(floatval($fin),2);
                echo "</td>";
            }
            ?>
            
        </tr>
        <?php endfor;?>

        </tbody>
        </table>
        </div> <!--contenedor tabla-->
    </div>
    <div role="tabpanel" class="tab-pane <?php if($tipo_bus == "d"){echo "active";}?>" id="fechas">
        <h1>Ingresos VS Egresos del <?php echo $inicio_;?> al <?php echo $fin_;?></h1>

        <div class="panel panel-primary" id='pfecha'>
        <div class="panel-heading">
        <h3 class="panel-title">Parametros de busqueda</h3>
        </div>
        <div class="panel-body row">
        <div class='control-group col-md-5'>
            <label>Inicio</label>
            <input class="form-control" type="date" id="inicio" value="<?php echo $inicio_;?>"/>
        </div>
        <div class='control-group col-md-5'>
            <label>Fin</label>
            <input class="form-control" type="date" id="fin" value="<?php echo $fin_;?>"/>
        </div>

        <div class='control-group col-md-2'>
        <label> </label>
        <p>
            <button type='button' class='btn btn-primary' onclick='filtrar_d()'>Filtrar</button>
        </p>
        </div>

        </div>
        </div>
        <hr/>
        <div class='contenedor-tabla' style="overflow-x: auto;">
        <table class='tb display' cellspacing='0' width='100%' border="0">
        <thead>
        <tr>
            <th></th>
            <?php
            foreach($monedas as $mo){
                echo "<th>INICIALES ".$mo["simbolo"]."</th>";
            }
            ?>
            <?php
            foreach($medios as $med){
                echo "<th>VENTAS ".$med["nombre"]."</th>";
            }
            ?>
            <?php
            foreach($medios as $med){
                echo "<th>INGRESO ADICIONAL ".$med["nombre"]."</th>";
            }
            ?>
            <?php
            foreach($medios as $med){
                echo "<th>SALIDAS ".$med["nombre"]."</th>";
            }
            ?>
            <?php
            foreach($medios as $med){
                echo "<th>SALDO FINAL ".$med["nombre"]."</th>";
            }
            ?>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><?php echo $inicio_." al ".$fin_;?></td>
            <?php
            $finales = array();
            foreach($monedas as $mo){
                echo "<td style='color: #00ff00'>";
                $consulta_iniciales = "select sum(monto) as resultado from movimiento_dinero where id_medio = '1' AND moneda = '".$mo["id"]."' AND tipo_origen = 'CUT' AND fecha_cierre BETWEEN '".$inicio_."' AND '".$fin_."' AND estado = 1";
                //echo $consulta_ventas;
                $resultado_iniciales = $conn->consulta_arreglo($consulta_iniciales);
                echo round(floatval($resultado_iniciales["resultado"]),2);
                echo "</td>";
            }
            ?>
            <?php
            $finales = array();
            foreach($medios as $med){
                echo "<td style='color: #0033ff'>";
                $consulta_ventas = "select sum(monto) as resultado from movimiento_dinero where id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND tipo_origen = 'PED' AND fecha_cierre BETWEEN '".$inicio_."' AND '".$fin_."' AND estado = 1";
                //echo $consulta_ventas;
                $resultado_ventas = $conn->consulta_arreglo($consulta_ventas);
                $finales[$med["nombre"]] = floatval($resultado_ventas["resultado"]);
                echo round(floatval($resultado_ventas["resultado"]),2);
                echo "</td>";
            }
            ?>
            <?php
            foreach($medios as $med){
                echo "<td style='color: #1B8990'>";
                $consulta_ingresos = "select sum(monto) as resultado from movimiento_dinero where id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND tipo_origen = 'GAS' AND fecha_cierre BETWEEN '".$inicio_."' AND '".$fin_."' AND estado = 1 AND id_aux = 1";
                $resultado_ingresos = $conn->consulta_arreglo($consulta_ingresos);
                $finales[$med["nombre"]] = $finales[$med["nombre"]] + floatval($resultado_ingresos["resultado"]);
                echo round(floatval($resultado_ingresos["resultado"]),2);
                echo "</td>";
            }
            ?>
            <?php
            foreach($medios as $med){
                echo "<td style='color: #ff3333'>";
                $consulta_salidas = "select sum(monto) as resultado from movimiento_dinero where id_medio = '".$med["id_medio"]."' AND moneda = '".$med["id_moneda"]."' AND tipo_origen = 'GAS' AND fecha_cierre BETWEEN '".$inicio_."' AND '".$fin_."' AND estado = 1 AND id_aux = 0";
                $resultado_salidas = $conn->consulta_arreglo($consulta_salidas);
                $finales[$med["nombre"]] = $finales[$med["nombre"]] + floatval($resultado_salidas["resultado"]);
                echo round(floatval($resultado_salidas["resultado"]),2);
                echo "</td>";
            }
            ?>
            <?php
            foreach($finales as $fin){
                echo "<td style='color: #0033ff'>";
                echo round(floatval($fin),2);
                echo "</td>";
            }
            ?>
            
        </tr>
        </tbody>
        </table>
        </div> <!--contenedor tabla-->
    </div>
  </div>

</div>


</div><!--/row-->
<hr>
</div><!--/.container-->

<div class="panel panel-success" style="margin-top: 20px;">
<div class="panel-heading">
    Leyenda
</div>
<div class="panel-body">
    <b>Montos Iniciales: </b>Son todas los montos que se ingresan al abrir caja en efectivo en moneda nacional.<br/>
    <b>Ventas: </b>Son todos los montos ingresados al cobrar las ventas realizadas.<br/>
    <b>Ingreso Adicional: </b>Son todos los montos que se ingresaron mediante la pantalla "Registrar Ingreso o Salida de dinero"<br/>
    <b>Salidas: </b>Son todos los montos que salieron del sistema mediante la pantalla "Registrar Ingreso o Salida de dinero"<br/>
    <b>Total: </b>El resultado de las ventas + ingresos adicionales - salidas por medio de pago<br/>
</div>
</div>

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="recursos/js/jquery.js"></script>
<script src="recursos/js/jquery-ui.js"></script>
<script src="recursos/js/consolidado.js"></script>
<script src="recursos/js/bootstrap.min.js"></script>
<script src="recursos/js/offcanvas.js"></script>
<script src="recursos/js/plugins/tablas/jquery.dataTables.min.js"></script>
<script src="recursos/js/plugins/tablas/dataTables.buttons.min.js"></script>
<script src="recursos/js/plugins/tablas/jszip.min.js"></script>
<script src="recursos/js/plugins/tablas/pdfmake.min.js"></script>
<script src="recursos/js/plugins/tablas/vfs_fonts.js"></script>
<script src="recursos/js/plugins/tablas/buttons.html5.min.js"></script>
<script src="recursos/js/plugins/tablas/buttons.print.min.js"></script>
<script>

function filtrar_a(){
location.href = "ingresos_vs_egresos.php?a="+$("#ano_reporte").val()+"&t=a";
}

function filtrar_d(){
location.href = "ingresos_vs_egresos.php?i="+$("#inicio").val()+"&f="+$("#fin").val()+"&t=d";
}

$('.tb').DataTable( {
dom: 'Blfrtip',
"bSort": false,
"bFilter": false,
"bInfo": false,
"ordering": false,
"paging": false,
buttons: [
{
    extend: 'excelHtml5',
    title: '<?php echo $titulo_pagina;?>'
},
{
    extend: 'pdfHtml5',
    orientation: 'landscape',
    alignment: 'center',
    pageSize: 'LEGAL',
    customize: function(doc) {
        doc.defaultStyle.alignment = 'center';
        var objLayout = {};
        objLayout['hLineWidth'] = function(i) { return .5; };
        objLayout['vLineWidth'] = function(i) { return .5; };
        objLayout['hLineColor'] = function(i) { return '#aaa'; };
        objLayout['vLineColor'] = function(i) { return '#aaa'; };
        objLayout['paddingLeft'] = function(i) { return 4; };
        objLayout['paddingRight'] = function(i) { return 4; };
        doc.content[1].layout = objLayout;
    },
    title: '<?php echo $titulo_pagina;?>'
},
{
    extend: 'print',
    orientation: 'landscape',
    pageSize: 'LEGAL',
    title: '<?php echo $titulo_pagina;?>'
}
]
} );
</script>
</body>
</html>


                            