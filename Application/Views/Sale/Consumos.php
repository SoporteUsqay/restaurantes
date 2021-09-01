<?php
error_reporting(E_ALL);
    $db = new SuperDataBase();
    $fechaInicio = date('Y-m-d');
    if (isset($_REQUEST['txtFechaInicioCredito'])){
        $fechaInicio = $_REQUEST['txtFechaInicioCredito'];
    }

    $fechaFin = date('Y-m-d');
    if (isset($_REQUEST['txtFechaFinCredito'])){
        $fechaFin = $_REQUEST['txtFechaFinCredito'];
    }

    $cliente = null;
    if(isset($_REQUEST["cliente"])){
        $cliente = $_REQUEST["cliente"];
    }

    $titulo_importante = "Reporte de Consumos del ";

    if($fechaInicio == $fechaFin){
        $titulo_importante = $titulo_importante.$fechaInicio;
    }else{
        $titulo_importante = $titulo_importante.$fechaFin;
    }

    $cliente_id = null;
    $cliente_codigo = null;
    $cliente_nombre = null;
    if(!is_null($cliente)){
        $query = "(Select id, 'GENERICO' as dsp, nombre from cliente_generico where id = '".$cliente."') UNION (Select documento as id, documento as dsp, nombres as nombre from person where documento = '".$cliente."') UNION (Select ruc as id, ruc as dsp, razonSocial as nombre from persona_juridica where ruc = '".$cliente."')";
        $result = $db->executeQuery($query);
        if ($row = $db->fecth_array($result)) {
            $cliente_id = $row["id"];
            $cliente_codigo = $row["dsp"];
            $cliente_nombre = $row["nombre"];
        }
    }

    if(!is_null($cliente_id)){
        $titulo_importante = $titulo_importante." de ".$cliente_nombre;
    }

    include 'Application/Views/template/header.php';
    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showContent();
    ?>    
    <link href="Public/select2/css/select2.css" rel="stylesheet">
    <link rel="stylesheet" href="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.css">
    <style>
        .select2-container{
            height: 34px !important;
        }
        .select2-selection{
            height: 34px !important;
            padding: 2px 4px !important;
            font-size: 14px !important;
            border-radius: 4px !important;
        }
        .dt-buttons{
            margin-bottom: 10px !important;
        }
    </style>
    <div class="container">
        <!--<div class="jumbotron">-->
        <br>
        <br>
        <br>  <br>

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4><i class="glyphicon glyphicon-list-alt"></i> Consumos</h4>
                    </div>
                    <form id="frmCuentasPendientes" style="padding:1%;"> 
                        <div class='control-group row'>
                            <div class="col-lg-3 col-xs-12">
                                <label>Fecha Inicio</label>
                                <input class="form-control date" id="txtFechaInicioCredito" name="txtFechaInicioCredito" value="<?php echo $fechaInicio ?>">
                            </div>
                            <div class="col-lg-3 col-xs-12">
                                <label>Fecha Fin</label>
                                <input class="form-control date" id="txtFechaFinCredito" name="txtFechaFinCredito" value="<?php echo $fechaFin ?>">
                            </div>
                            <div class="col-lg-5 col-xs-12">
                                <label>Cliente</label>
                                <select name="cliente" required="true" id="cliente" class="form-control">
                                <?php if(!is_null($cliente_id)){
                                    echo '<option selected value="'.$cliente_id.'">'.$cliente_codigo.' - '.$cliente_nombre.'</option>';
                                }?>
                                </select>
                            </div>
                            <div class="col-lg-1 col-xs-12">
                                <br>
                                <button type="button" class="btn btn-primary" onclick='buscarCuentasPorPagar()' style="float:right; margin-right:5px">Buscar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <br>
        <div class="tab-content">
            <div class="tab-pane active" id="CuenctasPorPagar" >  
                <table id="tblCuentasPorPagar" class="table table-borderer">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Mesa</th>
                            <th>Mozo</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $suma_total = 0;
                        $query = "";
                        if(is_null($cliente)){
                            $query = "SELECT *,
                            case when (tipo_cliente = 1 AND character_length(p.documento) > 0 AND character_length(p.documento) < 8) then 
                            (select nombre from cliente_generico where id=p.documento)
                            when (tipo_cliente = 1 AND character_length(p.documento) = 8) then 
                            (select nombres from person where documento =p.documento)
                            when (tipo_cliente = 2) then
                            (select razonSocial from persona_juridica where ruc=p.documento ) 
                            end as cliente FROM pedido p
                            inner join detallepedido d on p.pkPediido=d.pkPediido
                            inner join trabajador t
                            on d.pkMozo=t.pkTrabajador
                            inner join mesas m on m.pkMesa=p.pkMesa where p.estado=5 and DATE_FORMAT(fechaApertura,'%Y-%m-%d') between '$fechaInicio' and '$fechaFin' group by p.pkPediido;";
                        }else{
                            $query = "SELECT *,
                            case when (tipo_cliente = 1 AND character_length(p.documento) > 0 AND character_length(p.documento) < 8) then 
                            (select nombre from cliente_generico where id=p.documento)
                            when (tipo_cliente = 1 AND character_length(p.documento) = 8) then 
                            (select nombres from person where documento =p.documento)
                            when (tipo_cliente = 2) then
                            (select razonSocial from persona_juridica where ruc=p.documento ) 
                            end as cliente FROM pedido p
                            inner join detallepedido d on p.pkPediido=d.pkPediido
                            inner join trabajador t
                            on d.pkMozo=t.pkTrabajador
                            inner join mesas m on m.pkMesa=p.pkMesa where p.estado=5 and DATE_FORMAT(fechaApertura,'%Y-%m-%d') between '$fechaInicio' and '$fechaFin' AND p.documento = '$cliente' group by p.pkPediido;";
                        }
                        $result = $db->executeQuery($query);
                        while ($row = $db->fecth_array($result)) {
                            echo "<tr>";
                            echo "<td>";
                            echo $row['pkPediido'];
                            echo "</td>";
                            echo "<td>";
                            echo utf8_encode($row['cliente']);
                            echo "</td>";
                            echo "<td>";
                            echo $row['fechaApertura'];
                            echo "</td>";
                            echo "<td>";
                            //Consumos no guardan total a la fecha 01/05/2019
                            //Codigo sera removido luego
                            //Buscamos los detalles de cada pedido y los sumamos
                            $total_actual = 0;
                            $query_total = "Select sum(cantidad*precio) as total from detallepedido where pkPediido = '".$row['pkPediido']."'";
                            $result_total = $db->executeQuery($query_total);
                            if($rt = $db->fecth_array($result_total)){
                                $total_actual = floatval($rt["total"]);
                            }
                            echo $total_actual;
                            $suma_total = $suma_total + $total_actual;
                            echo "</td>";
                            echo "<td>";
                            echo $row['nmesa'];
                            echo "</td>";
                            echo "<td>";
                            echo $row['apellidos'] . ", " . $row['nombres'];
                            echo "</td>";
                            echo "<td>";
                            echo "<a href='javascript:void(0)' onclick='VerPedidoPorPagar(\"" . $row['pkPediido'] . "\"," . $row['pkMesa'] . ")'>Detalle</a>";
                            echo "</td>";
                            ?>
                            </td>
                            </tr>
                        <?php }
                        ?>
                        <tr>
                            <td></td>
                            <td></td>
                            <td><b>TOTAL</b></td>
                            <td><?php echo $suma_total?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
    <script src="Public/select2/js/select2.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/dataTables.buttons.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/jszip.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/pdfmake.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/vfs_fonts.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/buttons.html5.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/buttons.print.min.js"></script>
    <script>
        function buscarCuentasPorPagar()
        {
                    window.location.href="<?php echo Class_config::get('urlApp') ?>/?controller=Sale&action=CConsumo&" +$('#frmCuentasPendientes').serialize();
                    
        }

        $('#tblCuentasPorPagar').DataTable( {
            dom: 'Blfrtip',
            "bSort" : false,
            buttons: [
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: [0,1,2,3,4,5]
                    },
                    title: '<?php echo $titulo_importante;?>'
                },
                {
                    extend: 'pdfHtml5',
                    orientation: 'portrait',
                    alignment: 'center',
                    pageSize: 'LEGAL',                    
                    customize: function(doc) {
                        doc.defaultStyle.alignment = 'center';
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
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
                    title: '<?php echo $titulo_importante;?>'
                },
                {
                    extend: 'print',
                    orientation: 'portrait',
                    pageSize: 'LEGAL',
                    exportOptions: {
                        columns: [0,1,2,3,4,5]
                    },
                    title: '<?php echo $titulo_importante;?>'
                }
                
            ]
        } );
        date('date');

        function VerPedidoPorPagar($pedido, $mesa) {
            window.open('<?php echo Class_config::get('urlApp') ?>/?controller=Report&action=ShowAdminDetalleVentas&id=' + $pedido + '&estado=1&nsucursal=SU009', '_blank');
            
        }

        $('#cliente').select2({
          ajax: { 
           url: "<?php echo Class_config::get('urlApp') ?>/reportes/ws/clientes.php",
           type: "post",
           dataType: 'json',
           delay: 250,
           data: function (params) {
            return {
               term: params.term
            };
           },
           processResults: function (data) {
                return {
                    results: $.map(data.results, function (item) {
                        return {
                            text: item.dsp+" - "+decodeURIComponent(escape(item.nombre)),
                            id: item.id
                        };
                    })
                };
            }
          },
          placeholder: 'Digita el nombre o documento',
          minimumInputLength: 0
         });

        <?php if(!is_null($cliente_id)){
            echo " $('#cliente').val(".$cliente_id.");";
        }?>
    </script>