<?php
$titulo_pagina = 'Reporte Ingresos y Salidas';
$titulo_sistema = 'usqay2';

include_once('recursos/componentes/MasterConexion.php');
include_once('../Components/Library/Framework/SuperDataBase.php');
$conn = new MasterConexion();

$fechaInicio = date("Y-m-d");
$fechaFin = date("Y-m-d");
$tipo_movimiento = "";
$medio_moneda = "";
$caja = $_COOKIE["c"];

if(isset($_GET["i"])){
    $fechaInicio = $_GET["i"];
}

if(isset($_GET["f"])){
    $fechaFin = $_GET["f"];
}

if(isset($_GET["t"])){
    $tipo_movimiento = $_GET["t"];
}

if(isset($_GET["m"])){
    $medio_moneda = $_GET["m"];
}

if(isset($_GET["c"])){
    $caja = $_GET["c"];
}

//Obtenemos el permiso
$puede_borrar = 0;
if(intval($_COOKIE["TYP"]) > 0 &&  intval($_COOKIE["TYP"]) < 3){
    $puede_borrar = 1;
}

//Obtenemos los tipos de movimientos
$resultado_tipos = $conn->consulta_matriz("select * from tipo_gasto where estado = 1");

//Obtenemos Listado de Cajas
$query_cajas = $conn->consulta_matriz("Select * from cajas");

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

$consulta_tipo = "";
$consulta_moneda = "";

if($tipo_movimiento <> ""){
    $consulta_tipo .= " AND (";
    $tipos = explode(",",$tipo_movimiento);
    foreach($tipos as $tp){
        $consulta_tipo .= "md.id_origen = '".$tp."' OR ";
    }
    $consulta_tipo = substr($consulta_tipo,0,-4);
    $consulta_tipo .= ") ";
}

if($medio_moneda <> ""){
    $consulta_moneda .= " AND (";
    $tipos = explode(",",$medio_moneda);
    foreach($tipos as $tp){
        $partes = explode("_",$tp);
        $medio = $partes[0];
        $moneda = $partes[1];
        $consulta_moneda .= "(md.id_medio = '".$medio."' AND md.moneda = '".$moneda."') OR ";
    }
    $consulta_moneda = substr($consulta_moneda,0,-4);
    $consulta_moneda .= ") ";
}

require_once('recursos/componentes/header.php'); 
?>
<link rel="stylesheet" href="recursos/js/plugins/tablas/jquery.dataTables.min.css">
<style>
    .dataTables_wrapper{
        margin: 20px 10px 0px 10px;
    }
    .dt-buttons{
        margin-bottom: 15px;
    }
</style>
<div class="panel panel-primary" id='pfecha'>
    <div class="panel-heading">
       <b>Reporte Ingresos y Salidas</b>
    </div>
    <div class="panel-body row">
        <div class='control-group col-lg-3'>
            <label>Fecha Inicio</label>
            <input class='form-control date' placeholder='AAAA-MM-DD' id='inicio' name='inicio' value="<?php echo $fechaInicio ?>" type="date"/>
        </div>
        <div class='control-group col-lg-3'>
            <label>Fecha Fin</label>
            <input class='form-control date' placeholder='AAAA-MM-DD' id='fin' name='fin' value="<?php echo $fechaFin ?>" type="date"/>
        </div>
        <div class="control-group col-lg-3">
            <label>Caja</label>
            <select class="form-control" id="caja">
                <option value='0'>Todas</option>
                <?php 
                if(is_array($query_cajas)){
                    foreach($query_cajas as $cajas){
                        if($caja === $cajas["caja"]){
                            echo "<option value='".$cajas["caja"]."' selected>".$cajas["caja"]."</option>";
                        }else{
                            echo "<option value='".$cajas["caja"]."'>".$cajas["caja"]."</option>";
                        }
                    }
                }
                ?>
            </select>
        </div>
        <div class='control-group col-lg-3'>
            <label>Tipo Movimiento</label>
            <select class="form-control" id="tipo_movimiento" multiple>
            <?php if(is_array($resultado_tipos)):?>
                <?php foreach($resultado_tipos as $med):?>
                    <option value="<?php echo $med["id"]."_".$med["direccion"]; ?>" style="<?php if(intval($med["direccion"]) === 0){echo "color:red;";}else{echo "color:green;";}?>"><?php echo $med["nombre"];?></option>
                <?php endforeach;?>
            <?php endif;?>
            </select>
        </div>
        <div class="control-group col-lg-3">
            <label>Medio y Moneda</label>
            <select class="form-control" id="medio_moneda" multiple>
            <?php foreach($medios as $med):?>
                <option value="<?php echo $med["id_medio"]."_".$med["id_moneda"]; ?>"><?php echo $med["nombre"];?></option>
            <?php endforeach;?>
            </select>
        </div>
        

        <div class='control-group col-lg-3'>
            <label> </label>
            <p>
            <button type='button' class='btn btn-primary' onclick='buscar()'>Buscar</button>
            </p>
        </div>
    </div>
</div>
<hr/>
<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#validos" aria-controls="validos" role="tab" data-toggle="tab">Pagos Válidos</a></li>
    <li role="presentation"><a href="#anulados" aria-controls="anulados" role="tab" data-toggle="tab">Pagos Anulados</a></li>
</ul>


<!-- Tab panes -->
<div class="tab-content">
<div role="tabpanel" class="tab-pane active" id="validos">
<div class='contenedor-tabla'>
    <table id='tb-validos' class='display' cellspacing='0' width='100%' border="0">
        <thead>
            <tr>
                <th>#MOV</th>
                <th>Tipo</th>
                <th>Tiempo</th>
                <th>Medio</th>
                <th>Moneda</th>
                <th>Monto</th>
                <th>Comentario</th>
                <th>Usuario</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php
        $total_ingresos = 0;
        $total_egresos = 0;
        $validos = null;
        if(intval($caja) > 0){ 
            $validos = $conn->consulta_matriz("Select md.*, tg.nombre as tipo_gasto, CONCAT(tr.nombres, ' ', tr.apellidos) as trabajador, mp.nombre as mp_nombre, m.simbolo as m_simbolo from movimiento_dinero md, medio_pago mp, moneda m, tipo_gasto tg, trabajador tr where md.tipo_origen = 'GAS' AND md.fecha_cierre BETWEEN '".$fechaInicio."' AND '".$fechaFin."' AND md.caja = '".$caja."' AND md.estado = 1 AND md.id_origen = tg.id AND md.id_medio = mp.id AND md.moneda = m.id AND md.id_usuario = tr.pkTrabajador ".$consulta_tipo.$consulta_moneda);
        }else{
            $validos = $conn->consulta_matriz("Select md.*, tg.nombre as tipo_gasto, CONCAT(tr.nombres, ' ', tr.apellidos) as trabajador, mp.nombre as mp_nombre, m.simbolo as m_simbolo from movimiento_dinero md, medio_pago mp, moneda m, tipo_gasto tg, trabajador tr where md.tipo_origen = 'GAS' AND md.fecha_cierre BETWEEN '".$fechaInicio."' AND '".$fechaFin."' AND md.estado = 1 AND md.id_origen = tg.id AND md.id_medio = mp.id AND md.moneda = m.id AND md.id_usuario = tr.pkTrabajador ".$consulta_tipo.$consulta_moneda);
        }
        if(is_array($validos)):
            foreach($validos as $val):
        ?>
            <tr>
                <td><?php echo $val["id"];?></td>
                <td>
                <?php 
                    if(intval($val["id_aux"]) === 1){
                        $total_ingresos = $total_ingresos + floatval($val["monto"]);
                        echo "<span style='color:green;'>".$val["tipo_gasto"]."</span>";
                    }else{
                        $total_egresos = $total_egresos + floatval($val["monto"]);
                        echo "<span style='color:red;'>".$val["tipo_gasto"]."</span>";
                    }
                ?>
                </td>
                <td>
                <?php echo $val["fecha_hora"];?>
                </td>
                <td>
                <?php echo $val["mp_nombre"];?>
                </td>                            
                <td>
                <?php echo $val["m_simbolo"];?>
                </td>
                <td>
                <?php echo round(floatval($val["monto"]),2);?>
                </td>
                <td>
                <?php echo $val["comentario"];?>
                </td>
                <td>
                <?php echo $val["trabajador"];?>
                </td>
                <td>
                <?php
                echo "<a href='#' onclick='reImprimirPago(\"" . $val['id'] . "\")' title='ReImprimir'><span class='glyphicon glyphicon-print'></span></a>";
                ?>
                </td>
            </tr>
        <?php
            endforeach;
        endif;
        ?>
        </tbody>
        </table>
        </div> <!--contenedor tabla-->
        <div class="panel panel-danger" style="margin-top: 10px;">
             <div class="panel-heading">
                 Resumen
             </div>
             <div class="panel-body">
                 <b>Total Ingresos: <span style="color: #00ff00"><?php echo $total_ingresos;?></span></b><br/>
                 <b>Total Salidas:  <span style="color: #ff3333"><?php echo $total_egresos;?></span></b><br/>
             </div>
         </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="anulados">
        <div class='contenedor-tabla'>
        <table id='tb-anulados' class='display' cellspacing='0' width='100%' border="0">
        <thead>
            <tr>
                <th>#MOV</th>
                <th>Tipo</th>
                <th>Tiempo</th>
                <th>Medio</th>
                <th>Moneda</th>
                <th>Monto</th>
                <th>Comentario</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $total_ingresos_a = 0;
        $total_egresos_a = 0;  
        $anulados = $conn->consulta_matriz("Select md.*, tg.nombre as tipo_gasto, CONCAT(tr.nombres, ' ', tr.apellidos) as trabajador, mp.nombre as mp_nombre, m.simbolo as m_simbolo from movimiento_dinero md, medio_pago mp, moneda m, tipo_gasto tg, trabajador tr where md.tipo_origen = 'GAS' AND md.fecha_cierre BETWEEN '".$fechaInicio."' AND '".$fechaFin."' AND md.caja = '".$caja."' AND md.estado = 0 AND md.id_origen = tg.id AND md.id_medio = mp.id AND md.moneda = m.id AND md.id_usuario = tr.pkTrabajador ".$consulta_tipo.$consulta_moneda);
        if(is_array($anulados)):
            foreach($anulados as $val):
        ?>
            <tr>
                <td><?php echo $val["id"];?></td>
                <td>
                <?php 
                    if(intval($val["id_aux"]) === 1){
                        $total_ingresos_a = $total_ingresos_a + floatval($val["monto"]);
                        echo "<span style='color:green;'>".$val["tipo_gasto"]."</span>";
                    }else{
                        $total_egresos_a = $total_egresos_a + floatval($val["monto"]);
                        echo "<span style='color:red;'>".$val["tipo_gasto"]."</span>";
                    }
                ?>
                </td>
                <td>
                <?php echo $val["fecha_hora"];?>
                </td>
                <td>
                <?php echo $val["mp_nombre"];?>
                </td>                            
                <td>
                <?php echo $val["m_simbolo"];?>
                </td>
                <td>
                <?php echo round(floatval($val["monto"]),2);?>
                </td>
                <td>
                <?php echo $val["comentario"];?>
                </td>
                <td>
                <?php echo $val["trabajador"];?>
                </td>
            </tr>
        <?php
            endforeach;
        endif;
        ?>
        </tbody>
        </table>
        <div class="panel panel-danger" style="margin-top: 10px;">
             <div class="panel-heading">
                 Resumen
             </div>
             <div class="panel-body">
                 <b>Total Ingresos: <span style="color: #00ff00"><?php echo $total_ingresos_a;?></span></b><br/>
                 <b>Total Salidas:  <span style="color: #ff3333"><?php echo $total_egresos_a;?></span></b><br/>
             </div>
         </div>
        </div> <!--contenedor tabla-->
        </div>
        </div>
        </div><!--/row-->
        <p></p>
        <p></p>

         </div><!--/.container-->
         
            <!-- Bootstrap core JavaScript
            ================================================== -->
            <!-- Placed at the end of the document so the pages load faster -->
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
            jQuery.fn.reset = function () {
                $(this).each(function () {
                    this.reset();
                });
            };


            $(document).ready(function () {
                $('#tipo_movimiento option').prop('selected', true);
                $('#medio_moneda option').prop('selected', true);

                history.pushState(null, "", 'ingresos_salidas.php');

                $('#tb-validos').DataTable( {
                    dom: 'Blfrtip',
                    "bSort": false,
                    "bFilter": true,
                    "bInfo": true,
                    "ordering": false,
                    "language": {
                        "emptyTable": "No se encontraron movimientos de dinero"
                    },
                    "paging": true,
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            exportOptions: {
                                columns: [0,1,2,3,4,5,6,7]
                            },
                            title: 'Movimientos de dinero <?php echo $fechaInicio;?> al <?php echo $fechaFin;?>'
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
                            title: 'Movimientos de dinero <?php echo $fechaInicio;?> al <?php echo $fechaFin;?>'
                        },
                        {
                            extend: 'print',
                            orientation: 'landscape',
                            pageSize: 'LEGAL',
                            exportOptions: {
                                columns: [0,1,2,3,4,5,6,7]
                            },
                            title: 'Movimientos de dinero <?php echo $fechaInicio;?> al <?php echo $fechaFin;?>'
                        }
                        
                    ]
                } );

                $('#tb-anulados').DataTable( {
                    dom: 'Blfrtip',
                    "bSort": false,
                    "bFilter": true,
                    "bInfo": true,
                    "ordering": false,
                    "language": {
                        "emptyTable": "No se encontraron anulaciones"
                    },
                    "paging": true,
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            exportOptions: {
                                columns: [0,1,2,3,4,5,6,7]
                            },
                            title: 'Movimientos anulados <?php echo $fechaInicio;?> al <?php echo $fechaFin;?>'
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
                            title: 'Movimientos anulados <?php echo $fechaInicio;?> al <?php echo $fechaFin;?>'
                        },
                        {
                            extend: 'print',
                            orientation: 'landscape',
                            pageSize: 'LEGAL',
                            exportOptions: {
                                columns: [0,1,2,3,4,5,6,7]
                            },
                            title: 'Movimientos anulados <?php echo $fechaInicio;?> al <?php echo $fechaFin;?>'
                        }
                        
                    ]
                } );

                $.datepicker.regional['es'] = 
                {
                closeText: 'Cerrar', 
                prevText: 'Previo', 
                nextText: 'Próximo',

                monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
                monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
                'Jul','Ago','Sep','Oct','Nov','Dic'],
                monthStatus: 'Ver otro mes', yearStatus: 'Ver otro año',
                dayNames: ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'],
                dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sáb'],
                dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sa'],
                dateFormat: 'yy-mm-dd', firstDay: 0, 
                initStatus: 'Selecciona la fecha', isRTL: false};
                $.datepicker.setDefaults($.datepicker.regional['es']);

                $('#inicio').datepicker({dateFormat: 'yy-mm-dd',
                    changeMonth: true,
                    changeYear: true
                });

                $('#fin').datepicker({dateFormat: 'yy-mm-dd',
                    changeMonth: true,
                    changeYear: true
                });
            });    
            
            function buscar(){
                location.href = "ingresos_salidas.php?i="+$("#inicio").val()+"&f="+$("#fin").val()+"&t="+$("#tipo_movimiento").val()+"&m="+$("#medio_moneda").val()+"&c="+$("#caja").val();
            }

            function reImprimirPago(id_pago){
                var param = {'pkPago': id_pago, 'terminal': '<?php echo $_COOKIE["t"];?>', 'aux' : '<?php echo UserLogin::get_id(); ?>'};
                $.ajax({
                    url: "<?php echo Class_config::get('urlApp') ?>/?controller=GastosDiarios&&action=ImprimePago",
                        type: 'POST',
                        data: param,
                        cache: false,
                        dataType: 'json',
                        success: function() {
                            //Nada we
                        }
                });
            }
            </script>
          </body>
        </html>

        


                            