<?php
error_reporting(E_ALL);
$obj = new Application_Models_CajaModel();
$fechaVar=$obj->fechaCierre();
$fechaInicio = $obj->fechaCierre();
$fechaFin = $obj->fechaCierre();
$fechaCierre=$obj->fechaCierre();
if (isset($_GET['fecha_inicio'])){
    $fechaInicio = $_GET['fecha_inicio'];
    $fechaVar = $fechaInicio;
}

if (isset($_GET['fecha_fin'])){
    $fechaFin = $_GET['fecha_fin'];
}

$titulo_importante = "Consumo por persona";

if($fechaInicio === $fechaFin){
    $titulo_importante = $titulo_importante." del ".$fechaInicio;
}else{
    $titulo_importante = $titulo_importante." del ".$fechaInicio." al ".$fechaFin;
}

include 'Application/Views/template/header.php'; 
?>
<link rel="stylesheet" href="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.css">
<style>
    .dt-buttons{
        margin-bottom: 10px !important;
    }
</style>
<body>
    <?php
    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showContent();
    
    ?>   

    <div class="container">

        <br /><br /><br />
        <h3>Consumo Por Persona</h3>
        <div class="panel panel-primary" id='pfecha'>
            <div class="panel-heading">
                <h3 class="panel-title">Filtros por fechas</h3>
            </div>
            <div class="panel-body">

                <div class='control-group' id="dinicio">
                    <label>Fecha Inicio</label>
                    <input id="txtfechaini" type="date" class='form-control' placeholder='AAAA-MM-DD' name='fecha_inicio' value="<?php echo $fechaInicio ?>"/>
                    <label>Fecha Fin</label>
                    <input id="txtfechafin" type="date" class='form-control' placeholder='AAAA-MM-DD' name='fecha_fin' value="<?php echo $fechaFin ?>"/>
                    <br>
                    <button type="button" onclick="buscar2()" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Buscar</button>
                    <br>
                </div>
            </div>
        </div>
        <table id="tb" title="Consumo por Persona" class="display dataTable no-footer" >
            <thead>
                <tr>
                    <th><center>#Personas</center></th>
                    <th><center>#Mesas Atendidas</center></th>
                    <th><center>Total en Soles Consumido</center></th>
                    <th><center>Gasto por persona promedio</center></th>
                           
                </tr>
            </thead>

            <tbody>
                <?php
                $db = new SuperDataBase();                
                              
                $query = "SELECT sum(npersonas)as npersonas, count(pkPediido) as mesasatendidas , sum(total) as total, sum(total)/sum(npersonas) as consumoxpersona FROM pedido p where estado=1 and fechaCierre BETWEEN '$fechaInicio' AND '$fechaFin' ";
                $result = $db->executeQuery($query);
                while ($row = $db->fecth_array($result)) {
                    ?>
                    <tr>
                        <td>
                            <?php echo utf8_encode($row['npersonas']); ?>
                        </td>
                        <td>
                            <?php echo utf8_encode($row['mesasatendidas']); ?>
                        </td>
                        <td>
                            <?php echo round($row['total'],2); ?>
                        </td>
                        <td>
                            <?php echo round($row['consumoxpersona'],2); ?>
                        </td>
                    </tr>   
               <?php
                }
                ?>

            </tbody>
        </table>
    </div>        
    <script type="text/javascript" src="Application/Views/Reportes/js/ConsumoPorPersona.js.php" ></script>
    <script src="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/dataTables.buttons.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/jszip.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/pdfmake.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/vfs_fonts.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/buttons.html5.min.js"></script>
    <script src="reportes/recursos/js/plugins/tablas/buttons.print.min.js"></script>
    <script>             
        $(document).ready(function() {
            $('#tb').DataTable( {
                dom: 'Blfrtip',
                "bSort": false,
                "bFilter": false,
                "bInfo": false,
                "ordering": false,
                "paging": false,
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: '<?php echo $titulo_importante;?>'
                    },
                    {
                        extend: 'pdfHtml5',
                        orientation: 'portrait',
                        alignment: 'center',
                        pageSize: 'LEGAL',
                        customize: function(doc) {
                            doc.content[1].margin = [ 50, 0, 50, 0 ] //left, top, right, bottom
                        },
                        title: '<?php echo $titulo_importante;?>'
                    },
                    {
                        extend: 'print',
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                        title: '<?php echo $titulo_importante;?>'
                    }
                    
                ]
            } );
        });
    </script>
</body>
