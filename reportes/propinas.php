<?php
$titulo_pagina = 'Reporte Propinas';
$titulo_sistema = 'Usqay2';

include_once('recursos/componentes/MasterConexion.php');
$conn = new MasterConexion();
$fecha_inicio = date("Y-m-d");
$fecha_fin = date("Y-m-d");
$medio_moneda = "";
$trabajador = "";

//Primero verificamos si hay algo que eliminar
if(isset($_GET["del"])){
    $query_del = "Delete from pedido_propina where id = '".$_GET["del"]."'";
    $conn->consulta_simple($query_del);
}

if(isset($_GET["fi"])){
    $fecha_inicio = $_GET["fi"];
}

if(isset($_GET["ff"])){
    $fecha_fin = $_GET["ff"];
}

//Trabajador
if(isset($_GET["t"])){
    $trabajador = $_GET["t"];
}

//Medio y Moneda
if(isset($_GET["m"])){
    $medio_moneda = $_GET["m"];
}

$consulta_trabajador = "";
$consulta_moneda = "";

if($trabajador <> ""){
    $consulta_trabajador .= " AND (";
    $trabajadores = explode(",",$trabajador);
    foreach($trabajadores as $tr){
        $consulta_trabajador .= "p.pkTrabajador = '".$tr."' OR ";
    }
    $consulta_trabajador = substr($consulta_trabajador,0,-4);
    $consulta_trabajador .= ") ";
}

if($medio_moneda <> ""){
    $consulta_moneda .= " AND (";
    $tipos = explode(",",$medio_moneda);
    foreach($tipos as $tp){
        $partes = explode("_",$tp);
        $medio = $partes[0];
        $moneda = $partes[1];
        $consulta_moneda .= "(p.id_medio = '".$medio."' AND p.moneda = '".$moneda."') OR ";
    }
    $consulta_moneda = substr($consulta_moneda,0,-4);
    $consulta_moneda .= ") ";
}

$titulo_fecha = "";

if($fecha_inicio === $fecha_fin){
    $titulo_fecha = " de ".$fecha_inicio;
}else{
    $titulo_fecha = " del ".$fecha_inicio." al ".$fecha_fin;
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

$datos_sucursal = $conn->consulta_arreglo("Select * from sucursal LIMIT 1");
require_once('recursos/componentes/header.php'); 
?>
<link rel="stylesheet" href="recursos/js/plugins/tablas/jquery.dataTables.min.css">
<style>
    .dt-buttons{
        margin-bottom: 10px !important;
    }
</style> 
<h1><?php echo $titulo_pagina;?></h1>
<div class="panel panel-primary" id='pplatos'>
    <div class="panel-heading">
        <h3 class="panel-title">Parametros de busqueda</h3>
    </div>
    <div class="panel-body row">
        <div class='control-group col-lg-4'>
            <label>Fecha Inicio</label>
            <input class='form-control' placeholder='AAAA-MM-DD' id='fecha_inicio' name='fecha_inicio' value="<?php
            if (!isset($_GET["fi"])) {
                echo date("Y-m-d");
            }else{
               echo $_GET["fi"]; 
            }
            ?>" type="date"/>
        </div>
        <div class='control-group col-lg-4'>
            <label>Fecha Fin</label>
            <input class='form-control' placeholder='AAAA-MM-DD' id='fecha_fin' name='fecha_fin' value="<?php
            if (!isset($_GET["ff"])) {
                echo date("Y-m-d");
            }else{
               echo $_GET["ff"]; 
            }
            ?>" type="date"/>
        </div>

        <div class='control-group col-lg-4'>
                <label>Trabajador</label>
                <select class='form-control' id="trabajador" name="trabajador" multiple>
                <?php
                $query_tra = "SELECT * FROM trabajador where estado = 0;";
                $result_tra = $db->executeQuery($query_tra);
                while ($row = $db->fecth_array($result_tra)) {
                  echo "<option value='".$row["pkTrabajador"]."'>".$row["nombres"]." ".$row["apellidos"]."</option>";
                }
                ?>              
                </select> 
        </div>
        
        <div class="control-group col-lg-4">
            <label>Medio y Moneda</label>
            <select class="form-control" id="medio_moneda" multiple>
            <?php foreach($medios as $med):?>
                <option value="<?php echo $med["id_medio"]."_".$med["id_moneda"]; ?>"
                <?php if($medio_moneda === $med["id_medio"]."_".$med["id_moneda"]){echo " selected";}?>><?php echo $med["nombre"];?></option>
            <?php endforeach;?>
            </select>
        </div>

        <div class='control-group col-lg-4'>
            <p></p>
            <p><a onclick="filtrar()" class="btn btn-primary" id="btn_filtrar" style='margin-right:5px;margin-top:5px;'><span class="glyphicon glyphicon-search" aria-hidden="true"></span> Filtrar</a></p>
        </div>
</div>
</div>
</form>
<?php
$query_validos = "Select p.*, CONCAT(tr.nombres, ' ', tr.apellidos) as trabajador, mp.nombre as mp_nombre, m.simbolo as m_simbolo from pedido_propina p, medio_pago mp, moneda m, trabajador tr where p.fecha_cierre BETWEEN '".$fecha_inicio."' AND '".$fecha_fin."' AND p.id_medio = mp.id AND p.moneda = m.id AND p.pkTrabajador = tr.pkTrabajador ".$consulta_moneda.$consulta_trabajador;

//echo $query_validos;

$validos = $conn->consulta_matriz($query_validos);

$total_propinas = 0;
?>
<div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%' border="0">
        <thead>
            <tr>
                <th>#Mov</th>
                <th>#Ped</th>
                <th>Usuario</th>           
                <th>Medio</th>
                <th>Moneda</th>
                <th>Monto</th>
                <th></th>     
            </tr>
        </thead>
        <tbody>
        <?php if(is_array($validos)):?>
            <?php foreach($validos as $rt):?>
            <tr>
                <td><?php echo $rt["id"];?></td>
                <td><?php echo $rt["pkPediido"];?></td>
                <td><?php echo utf8_encode($rt["trabajador"]);?></td>
                <td><?php echo $rt["mp_nombre"];?></td>
                <td><?php echo $rt["m_simbolo"];?></td>
                <td>
                <?php echo round(floatval($rt["monto"]),2);
                $total_propinas = $total_propinas + round(floatval($rt["monto"]),2);
                ?>
                </td>
                <td>
                <?php 
                echo "<a href='#' onclick='eliminar(".$rt["id"].")'><span class='glyphicon glyphicon-trash' aria-hidden='true'></span></a>";
                ?>
                </td>
            </tr>
            <?php endforeach;?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>        
                <td><?php 
                    echo $total_propinas;
                ?></td>
                <td></td>
            </tr> 
        <?php endif;?>
        </tbody>
        </table>    
        </div><!--/row-->
        <p></p>
        <p></p>

         </div><!--/.container-->
        <script src="recursos/js/jquery.js"></script>
        <script src="recursos/js/jquery-ui.js"></script>
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
            $('#trabajador option').prop('selected', true);
            $('#medio_moneda option').prop('selected', true);

            function filtrar(){
                location.href = "propinas.php?fi="+$("#fecha_inicio").val()+"&ff="+$("#fecha_fin").val()+"&m="+$("#medio_moneda").val()+"&t="+$("#trabajador").val();
            }

            function eliminar(id){
                location.href = "propinas.php?fi="+$("#fecha_inicio").val()+"&ff="+$("#fecha_fin").val()+"&m="+$("#medio_moneda").val()+"&t="+$("#trabajador").val()+"&del="+id;
            }

            $('#tb').DataTable( {
                dom: 'Blfrtip',
                "bSort": false,
                "bFilter": true,
                "bInfo": true,
                "ordering": false,
                "paging": true,
                "language": {
                    "emptyTable": "No se encontraron propinas"
                },
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Reporte Propinas <?php echo $titulo_fecha;?>',
                        exportOptions: {
                            columns: [0,1,2,3,4,5]
                        },
                    },
                    {
                        extend: 'pdfHtml5',
                        orientation: 'portrait',
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
                        exportOptions: {
                            columns: [0,1,2,3,4,5]
                        },
                        title: 'Reporte Propinas <?php echo $titulo_fecha;?>'
                    },
                    {
                        extend: 'print',
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                        title: 'Reporte Propinas <?php echo $titulo_fecha;?>'
                    }
                ]
            } );
        </script>
</body>
</html>



                            