  <?php
  error_reporting(E_ALL);
 
  include_once('reportes/recursos/componentes/MasterConexion.php');
  $conn = new MasterConexion();

  $fechaInicio = date('Y-m-d', strtotime('-1 month'));
  if (isset($_REQUEST['fecha_inicio'])){
    $fechaInicio = $_REQUEST['fecha_inicio'];
  }

  $fechaFin = date('Y-m-d');
  if (isset($_REQUEST['fecha_fin'])){
    $fechaFin = $_REQUEST['fecha_fin'];
  }

  $pkInsumo = 0;
  if (isset($_REQUEST['txt_IDInsumo'])){
    $pkInsumo = $_REQUEST['txt_IDInsumo'];
  }

  $nombreInsumo = "";
  if (isset($_REQUEST['txt_NomInsumo'])){
    $nombreInsumo = $_REQUEST['txt_NomInsumo'];
  }

  //Solucion al problema del kardex cuando se deja abierta una mesa varios dias
  $tiempo_inicio = null;
  $tiempo_fin = null;

  $query_corte_min = "Select * from corte where fecha_cierre = '".$fechaInicio."' order by id ASC LIMIT 1";
  $result_min = $conn->consulta_arreglo($query_corte_min);
  if(is_array($result_min)){
      $tiempo_inicio = $result_min["inicio"];
  }else{
      $tiempo_inicio = $fechaFin." 00:00:00";
  }

  $query_corte_max = "Select * from corte where fecha_cierre = '".$fechaFin."' order by id DESC LIMIT 1";
  $result_max = $conn->consulta_arreglo($query_corte_max);
  if(is_array($result_max)){
      if($result_max["fin"] !== ""){
        $tiempo_fin = $result_max["fin"];
      }else{
        $tiempo_fin = date("Y-m-d H:i:s");
      } 
  }else{
      $tiempo_fin = date("Y-m-d H:i:s");
  }

  $titulo_importante = "Kardex (Seguimiento Diario) del ".$fechaInicio." al ".$fechaFin;

  include 'Application/Views/template/header.php';

  $objViewMenu = new Application_Views_IndexView();
  $objViewMenu->showContent();
?>
  <link rel="stylesheet" href="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.css">
  <style>
      .dt-buttons{
          margin-bottom: 10px !important;
      }
      .usqay-bdy{
          margin-left: 2% !important;
          margin-right: 2% !important;
      }
  </style>
  <br><br><br>
  <div class="container">
    <h3>Kardex (Seguimiento Diario)</h3>
    <form id="frmFiltroKardex" <?php if(isset($_GET["view"])){echo "style='display:none;'";} ?>>
      <div class="panel panel-primary" id='pfecha'>
        <div class="panel-heading">
          Filtros por fechas
        </div>
        <div class="panel-body">

          <div class='control-group' id="dinicio">
            <label>Fecha Inicio</label>
            <input class='form-control' placeholder='AAAA-MM-DD' id='fecha_inicio' name='fecha_inicio' value="<?php echo $fechaInicio ?>"/>
          </div>
          <div class='control-group' id="ffin">
            <label>Fecha Fin</label>
            <input class='form-control' placeholder='AAAA-MM-DD' id='fecha_fin' name='fecha_fin' value="<?php echo $fechaFin ?>"/>
          </div>

          <label >Insumo</label>
          <input id="txt_NomInsumo" name="txt_NomInsumo" type="text" class="form-control llevar" placeholder="Ingrese el insumo" value="<?php echo $nombreInsumo ?>">
          <input name="txt_IDInsumo" id="txt_IDInsumo" type="text"  style="display: none" class="form-control" value="<?php echo $pkInsumo ?>"
          placeholder="Ingrese el insumo">
          <div class='control-group'>
            <br/>
            <button type='button' class='btn btn-primary' onclick='busquedaKardex()'>Buscar</button>
            <br/>
          </div>
        </div>
      </div>
    </form>
    <br>
    <table class='tb display' cellspacing='0' width='100%' border="0">
      <thead>
        <tr>
          <th style="text-align:right">Insumo</th>
          <th style="text-align:right">Stock Anterior</th>
          <th style="text-align:right">T. Ingresos</th>
          <th style="text-align:right">T. Salidas</th>
          <th style="text-align:right">T. Vendido</th>
          <th style="text-align:right">Stock Parcial</th>
          <th style="text-align:center">Fecha</th>
          <th>Movimientos</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $db = new SuperDataBase();
        $db2 = new SuperDataBase();
        $caja= new Application_Models_CajaModel();
        $fecha= $caja->fechaCierre();
        $query =   "SELECT IFNULL(cantidadInicial,0.00000) AS stockAnterior,
        (SELECT IFNULL(SUM(cantidad),0.00000) AS ingreso
        FROM ingresoinsumos i
        WHERE i.fecha = h.fecha
        AND tipo = 1
        AND i.estado=0
        AND i.pkInsumo = h.pkInsumo) as ingreso,
        (SELECT IFNULL(SUM(cantidad),0.00000) AS salida
        FROM ingresoinsumos i
        WHERE i.fecha = h.fecha
        AND tipo = 2
        AND i.estado=0
        AND i.pkInsumo = h.pkInsumo) as salida,
        (SELECT (stockAnterior+ingreso-salida-cantidadFinal)) AS tvendido,
        cantidadFinal as stockActual,
        fecha
        FROM historial_stock_insumos h
        WHERE h.fecha
        BETWEEN '$fechaInicio' AND '$fechaFin'
        AND h.pkInsumo = $pkInsumo;";
        //echo $query;
        $result = $db->executeQuery($query);
        while ($row = $db->fecth_array($result)) {
          echo "<tr style='text-align:right'>";
            echo "<td>".$_GET["txt_NomInsumo"]."</td>";
            echo "<td>" . number_format(floatval($row["stockAnterior"]), 5, '.', ' ')."</td>";  
            echo "<td>" . number_format(floatval($row["ingreso"]), 5, '.', ' '). "</td>";
            echo "<td>" . number_format(floatval($row["salida"]), 5, '.', ' ') . "</td>";
            if($row['fecha']<$fecha){
              echo "<td>" . number_format(floatval($row["tvendido"]), 5, '.', ' ') . "</td>";
              echo "<td>" . number_format(floatval($row["stockActual"]), 5, '.', ' ') . "</td>";
            }else{             
                $total_vendido_d = 0;
                $phase1 = "Select pkPlato,cantidadTotal from insumo_menu where pkInsumo = '".$pkInsumo."'";
                $resultPhase1 = $db->executeQuery($phase1);
                while($rowPhase1 = $db->fecth_array($resultPhase1)){
                    $phase2 = "Select dp.cantidad from detallepedido dp where dp.pkPlato = '".$rowPhase1["pkPlato"]."' AND dp.estado > 0 AND dp.estado < 3 AND dp.horaPedido BETWEEN '".$tiempo_inicio."' AND '".$tiempo_fin."'";
                    //echo $phase2;
                    $cantidad = floatval($rowPhase1["cantidadTotal"]);
                    $resultPhase2 = $db->executeQuery($phase2);
                    while($rowPhase2 = $db->fecth_array($resultPhase2)){
                        $total_vendido_d = $total_vendido_d + $cantidad*floatval($rowPhase2["cantidad"]);
                    }
                }
                echo "<td>" . number_format(abs(floatval($total_vendido_d)), 5, '.', ' ') . "</td>";
                echo "<td>" . number_format((floatval($row["stockAnterior"])+floatval($row["ingreso"])-floatval($row["salida"])-abs(floatval($total_vendido_d))), 5, '.', ' ') . "</td>";
            }
            echo "<td style='text-align:center'>" . $row['fecha'] . "</td>";
            echo "<td style='text-align:center'>";
              echo "<a onclick='KardexDetallado(\"" . $row['fecha'] . "\")' title='Ver Detalles'> <span class='glyphicon glyphicon-log-out'></span></a>";
              echo "</td>";
              echo "</tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </body>

  <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery-1.10.2.js"></script>
  <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery-ui-1.10.4.custom.js"></script>
  <script src="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.js"></script>
  <script src="reportes/recursos/js/plugins/tablas/dataTables.buttons.min.js"></script>
  <script src="reportes/recursos/js/plugins/tablas/jszip.min.js"></script>
  <script src="reportes/recursos/js/plugins/tablas/pdfmake.min.js"></script>
  <script src="reportes/recursos/js/plugins/tablas/vfs_fonts.js"></script>
  <script src="reportes/recursos/js/plugins/tablas/buttons.html5.min.js"></script>
  <script src="reportes/recursos/js/plugins/tablas/buttons.print.min.js"></script>
  <script>
    $('.tb').DataTable( {
        dom: 'Blfrtip',
        "order": [[6, "asc"]],
        buttons: [
        {
            extend: 'excelHtml5',
            title: '<?php echo $titulo_importante;?>',
            exportOptions: {
                columns: [0,1,2,3,4,5,6]
            },
        },
        {
            extend: 'pdfHtml5',
            orientation: 'portrait',
            alignment: 'center',
            pageSize: 'LEGAL',
            exportOptions: {
                columns: [0,1,2,3,4,5,6]
            },
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
            title: '<?php echo $titulo_importante;?>'
        },
        {
            extend: 'print',
            orientation: 'portrait',
            pageSize: 'LEGAL',
            exportOptions: {
                columns: [0,1,2,3,4,5,6]
            },
            title: '<?php echo $titulo_importante;?>'
        }
        ]
    } );

    $(function () {
      $.ajax({
          url: "<?php echo Class_config::get('urlApp') ?>/?controller=Insumo&&action=List",
          type: 'POST',
          dataType: 'json',
          success: function (data) {
            $("#txt_NomInsumo").autocomplete({
              source: data,
              select: function (event, ui) {
                $("#txt_NomInsumo").val(ui.item.descripcion);
                $("#txt_IDInsumo").val(ui.item.id);
                return false;
              }
            });
          }
        });
    });

    $("#fecha_inicio").datepicker({dateFormat: 'yy-mm-dd'});
    $("#fecha_fin").datepicker({dateFormat: 'yy-mm-dd'});

    function busquedaKardex(){
      window.location.href="<?php echo Class_config::get('urlApp') ?>/?controller=Report&action=showKardexDetallado&" +$('#frmFiltroKardex').serialize();
    }
    
    function KardexDetallado(fecha){
      var url = "<?php echo Class_config::get('urlApp') ?>/?controller=Report&action=showKardexDetallado2&fecha_inicio="+fecha+"&fecha_fin="+ fecha+
      "&txt_NomInsumo="+ $('#txt_NomInsumo').val() + "&txt_IDInsumo="+ $('#txt_IDInsumo').val()+"&view=detail";
      window.open(url, '_blank');
    }
</script>
</html>
