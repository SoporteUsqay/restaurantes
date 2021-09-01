<?php
$titulo_pagina = 'Reporte movimientos por medio de pago';
$titulo_sistema = 'Usqay2';

include_once('recursos/componentes/MasterConexion.php');
$conn = new MasterConexion();
$sucursal = "SU009";
$fecha_inicio = date("Y-m-d");
$fecha_fin = date("Y-m-d");
$medio_moneda = "";
$caja = $_COOKIE["c"];

if(isset($_GET["fi"])){
    $fecha_inicio = $_GET["fi"];
}

if(isset($_GET["ff"])){
    $fecha_fin = $_GET["ff"];
}

$titulo_fecha = "";

if($fecha_inicio === $fecha_fin){
    $titulo_fecha = " de ".$fecha_inicio;
}else{
    $titulo_fecha = " del ".$fecha_inicio." al ".$fecha_fin;
}

//Caja
if (isset($_REQUEST['c'])){
    $caja = $_REQUEST["c"];
}

//Medio y moneda
if(isset($_GET["m"])){
    $medio_moneda = $_GET["m"];
}

$consulta_moneda = "";

if($medio_moneda <> ""){
    $consulta_moneda .= " AND ";
    $partes = explode("_",$medio_moneda);
    $medio = $partes[0];
    $moneda = $partes[1];
    $consulta_moneda .= "md.id_medio = '".$medio."' AND md.moneda = '".$moneda."'";
}

//Nivel de Usuario
$estilo_nivel = "style='display:none;'";
if(intval($_COOKIE["TYP"]) > 0 &&  intval($_COOKIE["TYP"]) < 3){
    $estilo_nivel = "";
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

$datos_sucursal = $conn->consulta_arreglo("Select * from sucursal where pkSucursal = '" . $sucursal . "'");
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
            ?>"/>
        </div>
        <div class='control-group col-lg-4'>
            <label>Fecha Fin</label>
            <input class='form-control' placeholder='AAAA-MM-DD' id='fecha_fin' name='fecha_fin' value="<?php
            if (!isset($_GET["ff"])) {
                echo date("Y-m-d");
            }else{
               echo $_GET["ff"]; 
            }
            ?>"/>
        </div>
        <div class='control-group col-lg-4' <?php echo $estilo_nivel;?>>
            <label>Caja</label>
            <select name="caja" class="form-control" id="caja">
                <option value=''>Todas</option>
                <?php
                $res = $conn->consulta_matriz("Select * from cajas");
                if(is_array($res)){
                    foreach($res as $cajas){
                        if($cajas["caja"] === $caja){
                            echo "<option value='".$cajas["caja"]."' selected>".$cajas["caja"]."</option>";
                        }else{
                            echo "<option value='".$cajas["caja"]."'>".$cajas["caja"]."</option>";
                        }
                    }
                }
                ?>
            </select>
        </div>
        <div class="control-group col-lg-4">
            <label>Medio y Moneda</label>
            <select class="form-control" id="medio_moneda">
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
$validos = null;
if(intval($caja) > 0){ 
    $validos = $conn->consulta_matriz("Select md.*, CONCAT(tr.nombres, ' ', tr.apellidos) as trabajador, mp.nombre as mp_nombre, m.simbolo as m_simbolo from movimiento_dinero md, medio_pago mp, moneda m, trabajador tr where md.fecha_cierre BETWEEN '".$fecha_inicio."' AND '".$fecha_fin."' AND md.caja = '".$caja."' AND md.estado = 1 AND md.id_medio = mp.id AND md.moneda = m.id AND md.id_usuario = tr.pkTrabajador ".$consulta_moneda);
}else{
    $validos = $conn->consulta_matriz("Select md.*, CONCAT(tr.nombres, ' ', tr.apellidos) as trabajador, mp.nombre as mp_nombre, m.simbolo as m_simbolo from movimiento_dinero md, medio_pago mp, moneda m, trabajador tr where md.fecha_cierre BETWEEN '".$fecha_inicio."' AND '".$fecha_fin."' AND md.estado = 1 AND md.id_medio = mp.id AND md.moneda = m.id AND md.id_usuario = tr.pkTrabajador ".$consulta_moneda);
}
$total_ventas = 0;
?>
<div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%' border="0">
        <thead>
            <tr>
                <th>#MOV</th>
                <th>Origen</th>
                <th>ID OP</th>
                <th>Detalle</th>
                <th>Caja</th>
                <th>Usuario</th>           
                <th>Medio</th>
                <th>Moneda</th>
                <th>Monto</th>     
            </tr>
        </thead>
        <tbody>
        <?php if(is_array($validos)):?>
            <?php foreach($validos as $rt):?>
            <tr>
                <td><?php echo $rt["id"];?></td>
                <td><?php 
                if($rt["tipo_origen"] === "PED"){
                    echo "PEDIDO";
                }
                
                if($rt["tipo_origen"] === "GAS"){
                    if(intval($rt["id_aux"]) === 1){
                        echo "INGRESO";
                    }else{
                        echo "SALIDA";
                    }
                } 
                ?></td>
                <td><?php 
                if($rt["tipo_origen"] === "PED"){
                    echo $rt["id_origen"];
                }
                
                if($rt["tipo_origen"] === "GAS"){
                    echo $rt["id"];
                } 
                ?></td>
                <td><?php echo $rt["comentario"];?></td>
                <td><?php echo $rt["caja"];?></td>
                <td><?php echo $rt["trabajador"];?></td>
                <td><?php echo $rt["mp_nombre"];?></td>
                <td><?php echo $rt["m_simbolo"];?></td>
                <td>
                <?php echo round(floatval($rt["monto"]),2);
                $total_ventas = $total_ventas + round(floatval($rt["monto"]),2);
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
                <td></td>
                <td></td>
                <td></td>           
                <td><?php 
                    echo $total_ventas;
                ?></td>
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
            function filtrar(){
                location.href = "reporte_tarjetas.php?fi="+$("#fecha_inicio").val()+"&ff="+$("#fecha_fin").val()+"&c="+$("#caja").val()+"&m="+$("#medio_moneda").val();
            }

            $('#tb').DataTable( {
                dom: 'Blfrtip',
                "bSort": false,
                "bFilter": true,
                "bInfo": true,
                "ordering": false,
                "paging": true,
                "language": {
                    "emptyTable": "No se encontraron movimientos de dinero"
                },
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Reporte movimientos '+$( "#medio_moneda option:selected" ).text()+'<?php echo $titulo_fecha;?>'
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
                            columns: [0,1,2,3,4,5,6,7]
                        },
                        title: 'Reporte movimientos '+$( "#medio_moneda option:selected" ).text()+'<?php echo $titulo_fecha;?>'
                    },
                    {
                        extend: 'print',
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                        title: 'Reporte movimientos '+$( "#medio_moneda option:selected" ).text()+'<?php echo $titulo_fecha;?>'
                    }
                ]
            } );
        </script>
</body>
</html>



                            