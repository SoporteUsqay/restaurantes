<?php
error_reporting(E_ALL); 
$titulo_importante = "Ingreso/Salida de Dinero";
include 'Application/Views/template/header.php'; ?>
<?php require_once 'reportes/recursos/componentes/MasterConexion.php'; 
$objcon = new MasterConexion();
$f = $objcon->consulta_arreglo("Select * from cierrediario where pkCierreDiario = 1");
//Obtenemos todos los medios de pago validos
$medios = array(); 
$resultado_medios = $objcon->consulta_matriz("Select mp.id, mp.moneda, mp.nombre, m.simbolo from medio_pago mp, moneda m where mp.estado > 0 AND mp.moneda = m.id");
if(is_array($resultado_medios)){
    foreach($resultado_medios as $medio){
        if(intval($medio["id"]) === 1){
            $resultado_monedas = $objcon->consulta_matriz("Select * from moneda where estado > 0");
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

//Obtenemos el permiso
$puede_borrar = 0;
if(intval($_COOKIE["TYP"]) > 0 &&  intval($_COOKIE["TYP"]) < 3){
    $puede_borrar = 1;
}

//Obtenemos los tipos de movimientos
$resultado_tipos = $objcon->consulta_matriz("select * from tipo_gasto where estado = 1");
?>
<body>
<link rel="stylesheet" href="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.css">
<style>
    .dataTables_wrapper{
        margin: 20px 10px 0px 10px;
    }
    .dt-buttons{
        margin-bottom: 15px;
    }
</style>
<?php
$objViewMenu = new Application_Views_IndexView();
$objViewMenu->showContent();
?>    
    <div class="panel-ventas">
    <br/><br/>
    <br/><br/>
    <div class="col-lg-12">
    <div class="panel panel-primary">
            <div class="panel-heading"><b>Registrar Ingreso o Salida de Dinero</b></div>
            <div class="panel-body row">
                <div class="form-group col-lg-3">
                    <label for="">Caja</label>
                    <select name="caja" id="caja" class="form-control">
                        <?php
                            $query = "select * from cajas";

                            $res = $objcon->consulta_matriz($query);

                            foreach($res as $row):
                        ?>
                            <option value="<?php echo $row['caja'] ?>" <?php echo $row['caja'] == $_COOKIE["c"] ? 'selected' : '' ?>><?php echo $row['caja'] ?></option>
                        <?php endforeach ?>
                        <option value="FE">Fondos Externos</option>
                    </select>
                </div>
                <div class="form-group col-lg-3">
                    <label>Fecha Cierre</label>
                    <input value="<?php echo $f["fecha"];?>" class="form-control" id="fecha_cierre" readonly="">
                </div>
                <div class="form-group col-lg-3">
                    <label>Monto</label>
                    <input placeholder="0.00" class="form-control" id="monto" type="number" min="0">
                </div>
                <div class="form-group col-lg-3">
                    <label>Tipo de Movimiento</label>
                    <select class="form-control" id="tipo_movimiento">
                    <?php if(is_array($resultado_tipos)):?>
                        <?php foreach($resultado_tipos as $med):?>
                            <option value="<?php echo $med["id"]."_".$med["direccion"]; ?>" style="<?php if(intval($med["direccion"]) === 0){echo "color:red;";}else{echo "color:green;";}?>"><?php echo $med["nombre"];?></option>
                        <?php endforeach;?>
                    <?php endif;?>
                    </select>
                </div>
                <div class="form-group col-lg-4">
                    <label>Medio y Moneda</label>
                    <select class="form-control" id="medio_moneda">
                    <?php foreach($medios as $med):?>
                        <option value="<?php echo $med["id_medio"]."_".$med["id_moneda"]; ?>"><?php echo $med["nombre"];?></option>
                    <?php endforeach;?>
                    </select>
                </div>
                <div class="form-group col-lg-8">
                    <label>Descipcion</label>
                    <input placeholder="Motivo del movimiento" class="form-control" id="descripcion">
                </div>
                <div class="form-group col-lg-6">
                    <p><input id="generar_comprobante" type="checkbox" value="gen"> ¿Imprimir Comprobante?</p>

                    <a onclick="guarda_gasto()" class="btn btn-primary" id="btn_guardar" style='margin-right:5px;margin-top:5px;'><span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Registrar</a>              
                </div>

                <div class='col-lg-12'>
                    <hr/>
                </div>

                <div class="col-lg-12">

                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#validos" aria-controls="validos" role="tab" data-toggle="tab">Pagos Válidos</a></li>
                    <li role="presentation"><a href="#fondos_externos" aria-controls="fondos_externos" role="tab" data-toggle="tab">Fondos Externos</a></li>
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
                                <th>Caja</th>
                                <th>Tipo</th>
                                <th>Tiempo</th>
                                <th>Medio</th>
                                <th>Moneda</th>
                                <th>Monto</th>
                                <th>Comentario</th>
                                <th>Usuario</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                        $validos = $objcon->consulta_matriz("Select md.*, tg.nombre as tipo_gasto, CONCAT(tr.nombres, ' ', tr.apellidos) as trabajador, mp.nombre as mp_nombre, m.simbolo as m_simbolo from movimiento_dinero md, medio_pago mp, moneda m, tipo_gasto tg, trabajador tr where md.tipo_origen = 'GAS' AND md.fecha_cierre = '".$f["fecha"]."' AND md.estado = 1 AND md.id_origen = tg.id AND md.id_medio = mp.id AND md.moneda = m.id AND md.id_usuario = tr.pkTrabajador");
                        if(is_array($validos)):
                            foreach($validos as $val):
                        ?>
                            <tr>
                                <td><?php echo $val["id"];?></td>
                                <td><?php echo $val["caja"];?></td>
                                <td>
                                <?php 
                                    if(intval($val["id_aux"]) === 1){
                                        echo "<span style='color:green;'>".$val["tipo_gasto"]."</span>";
                                    }else{
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
                                echo "<a href='#' onclick='reImprimirPago(\"" . $val['id'] . "\", \"${val['caja']}\")' title='ReImprimir'><span class='glyphicon glyphicon-print'></span></a>";
                                ?>
                                </td>
                                <td>
                                <?php 
                                if($puede_borrar === 1){
                                    echo "<a href='#' onclick='anularPago(\"" . $val['id'] . "\", \"${val['caja']}\")' title='Anular Pago'><span class='glyphicon glyphicon-remove'></span></a>";
                                }
                                ?>
                                </td>
                            </tr>
                        <?php
                            endforeach;
                        endif;
                        ?>
                        </tbody>
                        </table>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="fondos_externos">

                        <div class="text-right" style="padding-top: 1em">
                            <a href="<?php echo Class_Config::get('urlApp') . "/?controller=Report&action=showFondosExternos" ?>" class="btn btn-info">
                                <i class="fa fa-plane"></i>
                                Ir a Reporte de Fondos Externos
                            </a>
                        </div>

                        <div class='contenedor-tabla'>
                        <table id='tb-fe' class='display' cellspacing='0' width='100%' border="0">
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
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 

                        $query = "SELECT
                            movimiento_dinero_fe.*,
                            moneda.simbolo as moneda_simbolo,
                            medio_pago.nombre as mp_nombre,
                            CONCAT(trabajador.nombres, ' ', trabajador.apellidos) as trabajador_nombre,
                            tipo_gasto.nombre as tipo_gasto_nombre
                        FROM
                            movimiento_dinero_fe
                        LEFT JOIN moneda ON movimiento_dinero_fe.moneda = moneda.id
                        LEFT JOIN medio_pago ON movimiento_dinero_fe.id_medio = medio_pago.id
                        LEFT JOIN trabajador ON movimiento_dinero_fe.id_usuario = trabajador.pkTrabajador
                        LEFT JOIN tipo_gasto ON movimiento_dinero_fe.id_origen = tipo_gasto.id
                        WHERE 
                            movimiento_dinero_fe.fecha_cierre = '${f['fecha']}'
                        AND movimiento_dinero_fe.tipo_origen = 'GAS'
                        AND movimiento_dinero_fe.estado = 1";

                        $validos = $objcon->consulta_matriz($query);
                        if(is_array($validos)):
                            foreach($validos as $val):
                        ?>
                            <tr>
                                <td><?php echo $val["id"];?></td>
                                <td>
                                <?php 
                                    switch ($val['tipo_origen']) {
                                        case 'COM':
                                            echo "<span style='color:red;'>Compras</span>";
                                            break;
                                        case 'GAS':
                                            if(intval($val["id_aux"]) === 1){
                                                echo "<span style='color:green;'>".$val["tipo_gasto_nombre"]."</span>";
                                            }else{
                                                echo "<span style='color:red;'>".$val["tipo_gasto_nombre"]."</span>";
                                            }
                                            break;
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
                                <?php echo $val["moneda_simbolo"];?>
                                </td>
                                <td>
                                <?php echo round(floatval($val["monto"]),2);?>
                                </td>
                                <td>
                                <?php echo $val["comentario"];?>
                                </td>
                                <td>
                                <?php echo $val["trabajador_nombre"];?>
                                </td>
                                <td>
                                <?php
                                echo "<a href='#' onclick='reImprimirPago(\"" . $val['id'] . "\", \"FE\")' title='ReImprimir'><span class='glyphicon glyphicon-print'></span></a>";
                                ?>
                                </td>
                                <td>
                                <?php 
                                if($puede_borrar === 1){
                                    echo "<a href='#' onclick='anularPago(\"" . $val['id'] . "\", \"FE\")' title='Anular Pago'><span class='glyphicon glyphicon-remove'></span></a>";
                                }
                                ?>
                                </td>
                            </tr>
                        <?php
                            endforeach;
                        endif;
                        ?>
                        </tbody>
                        </table>
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
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                        $anulados = $objcon->consulta_matriz("Select md.*, tg.nombre as tipo_gasto, CONCAT(tr.nombres, ' ', tr.apellidos) as trabajador, mp.nombre as mp_nombre, m.simbolo as m_simbolo from movimiento_dinero md, medio_pago mp, moneda m, tipo_gasto tg, trabajador tr where md.tipo_origen = 'GAS' AND md.fecha_cierre = '".$f["fecha"]."' AND md.estado = 0 AND md.id_origen = tg.id AND md.id_medio = mp.id AND md.moneda = m.id AND md.id_usuario = tr.pkTrabajador");
                        if(is_array($anulados)):
                            foreach($anulados as $val):
                        ?>
                            <tr>
                                <td><?php echo $val["id"];?></td>
                                <td>
                                <?php 
                                    if(intval($val["id_aux"]) === 1){
                                        echo "<span style='color:green;'>".$val["tipo_gasto"]."</span>";
                                    }else{
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
                                if($puede_borrar === 1){
                                    echo "<a href='#' onclick='activarPago(\"" . $val['id'] . "\")' title='Activar Pago'><span class='glyphicon glyphicon-ok'></span></a>";
                                }
                                ?>
                                </td>
                            </tr>
                        <?php
                            endforeach;
                        endif;
                        ?>
                        </tbody>
                        </table>
                        </div>
                    </div>
                </div>

                </div>

            </div>
    </div></div>
    </div>
    <script src="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/dataTables.buttons.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/jszip.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/pdfmake.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/vfs_fonts.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/buttons.html5.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/buttons.print.min.js"></script>
    <script>

    $('#tb-validos').DataTable( {
            dom: 'Blfrtip',
            "bSort": false,
            "bFilter": true,
            "bInfo": true,
            "ordering": false,
            "language": {
                "emptyTable": "No hay movimientos de dinero hoy"
            },
            "paging": true,
            buttons: [
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7,8]
                    },
                    title: 'Movimientos de dinero <?php echo $f["fecha"];?>'
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
                        columns: [0,1,2,3,4,5,6,7,8]
                    },
                    title: 'Movimientos de dinero <?php echo $f["fecha"];?>'
                },
                {
                    extend: 'print',
                    orientation: 'landscape',
                    pageSize: 'LEGAL',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7,8]
                    },
                    title: 'Movimientos de dinero <?php echo $f["fecha"];?>'
                }
                
            ]
        } );
        $('#tb-fe').DataTable( {
            dom: 'Blfrtip',
            "bSort": false,
            "bFilter": true,
            "bInfo": true,
            "ordering": false,
            "language": {
                "emptyTable": "No hay movimientos de dinero hoy"
            },
            "paging": true,
            buttons: [
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7]
                    },
                    title: 'Movimientos de dinero FE <?php echo $f["fecha"];?>'
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
                    title: 'Movimientos de dinero FE <?php echo $f["fecha"];?>'
                },
                {
                    extend: 'print',
                    orientation: 'landscape',
                    pageSize: 'LEGAL',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7]
                    },
                    title: 'Movimientos de dinero FE <?php echo $f["fecha"];?>'
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
                "emptyTable": "No se han anulado movimientos de dinero hoy"
            },
            "paging": true,
            buttons: [
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7]
                    },
                    title: 'Movimientos anulados <?php echo $f["fecha"];?>'
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
                    title: 'Movimientos anulados <?php echo $f["fecha"];?>'
                },
                {
                    extend: 'print',
                    orientation: 'landscape',
                    pageSize: 'LEGAL',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7]
                    },
                    title: 'Movimientos anulados <?php echo $f["fecha"];?>'
                }
                
            ]
        } );


        function guarda_gasto() {
            var fecha_cierre = $("#fecha_cierre").val();
            var monto = $("#monto").val();
            var tipo_movimiento = $("#tipo_movimiento").val();
            var medio_moneda = $("#medio_moneda").val();
            var descripcion = $("#descripcion").val();
            var caja = $("#caja").val();
            var datos_envio = {'fecha_cierre':fecha_cierre, 'monto':monto, 'tipo_movimiento':tipo_movimiento, 'medio_moneda':medio_moneda,'descripcion':descripcion, 'caja': caja};
            if(parseFloat(monto)>0){
                $.ajax({
                    type: "POST",
                    url: "<?php echo Class_config::get('urlApp') ?>/?controller=GastosDiarios&&action=SaveGasto",
                    dataType: 'html',
                    data: datos_envio,
                    success: function (data){
                        $.messager.show({
                            title: 'Estado',
                            msg: "Se ha Registrado el pago Correctamente"
                        });
                        
                        if($("#generar_comprobante").is(':checked')) {  
                            var param = {'pkPago': data, 'terminal': '<?php echo $_COOKIE["t"];?>', 'aux' : '<?php echo UserLogin::get_id(); ?>'};
                            $.ajax({
                                url: "<?php echo Class_config::get('urlApp') ?>/?controller=GastosDiarios&&action=ImprimePago",
                                    type: 'POST',
                                    data: param,
                                    cache: false,
                                    dataType: 'json',
                                    success: function() {
                                        location.reload();
                                    }
                            });
                        }else{
                            location.reload();
                        } 
                    }

                });
            }else{
                alert("¡No puedes registrar un pago con monto cero!");
            }     
        }

        function reImprimirPago(id_pago, caja){
            var param = {'pkPago': id_pago, 'terminal': '<?php echo $_COOKIE["t"];?>', 'aux' : '<?php echo UserLogin::get_id(); ?>', 'caja': caja};
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

        function anularPago(id_pago, caja){

            if (!confirm('¿Esta seguro que desea eliminar este movimiento de dinero?')) {
                return;
            }

            var param = {'codigo': id_pago, 'caja': caja};
            $.ajax({
                url: "<?php echo Class_config::get('urlApp') ?>/?controller=GastosDiarios&&action=AnularGastosDiarios",
                    type: 'POST',
                    data: param,
                    cache: false,
                    dataType: 'json',
                    success: function() {
                        location.reload();
                    }
            });
        }

        function activarPago(id_pago){
            var param = {'codigo': id_pago};
            $.ajax({
                url: "<?php echo Class_config::get('urlApp') ?>/?controller=GastosDiarios&&action=ActivarPago",
                    type: 'POST',
                    data: param,
                    cache: false,
                    dataType: 'json',
                    success: function() {
                        location.reload();
                    }
            });
        }
    </script>