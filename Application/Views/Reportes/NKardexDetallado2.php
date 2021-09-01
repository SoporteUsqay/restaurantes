
  <?php
  // error_reporting(E_ALL);
  include_once('reportes/recursos/componentes/MasterConexion.php');
  $conn = new MasterConexion();

  $fechaInicio = date('Y-m-d');
  // $fechaInicio = date('Y-m-d', strtotime('-1 month'));
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

  $pkInsumoPorcion = 0;
  if (isset($_REQUEST['txt_IDInsumoPorcion'])){
    $pkInsumoPorcion = $_REQUEST['txt_IDInsumoPorcion'];
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

  $titulo_importante = "Kardex (Seguimiento Detallado) del ".$fechaInicio." al ".$fechaFin;

  include 'Application/Views/template/header.php';
  require_once('Application/Views/Almacen/KardexHelper.php');


  $objViewMenu = new Application_Views_IndexView();
  $objViewMenu->showContent();
  
  $obj = new Application_Models_CajaModel();
  $fechaCierreCaja = $obj->fechaCierre();

  $db = new SuperDataBase();
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
    <h3>Kardex (Seguimiento Detallado)</h3>
    <form id="frmFiltroKardex" <?php if(isset($_GET["view"])){echo "style='display:none;'";} ?>>
      <div class="panel panel-primary" id='pfecha'>
        <div class="panel-heading">
          Filtros por fechas
        </div>
        <div class="panel-body">

          <div class="row">

            <div class="col-md-4">
              <div class='control-group' id="dinicio">
                <label>Fecha Inicio</label>
                <input class='form-control' placeholder='AAAA-MM-DD' id='fecha_inicio' name='fecha_inicio' value="<?php echo $fechaInicio ?>"/>
              </div>
            </div>
            
            <div class="col-md-4">
            
              <div class='control-group' id="ffin">
                <label>Fecha Fin</label>
                <input class='form-control' placeholder='AAAA-MM-DD' id='fecha_fin' name='fecha_fin' value="<?php echo $fechaFin ?>"/>
              </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="">Almacén</label>
                    <select class="form-control" name="txt_IDAlmacen" id="cmbAlmacen">
                        <?php 
                            $query = "select * from n_almacen";

                            $res = $db->executeQueryEx($query);

                            while($row = $db->fecth_array($res)):
                        ?>
                            <option value="<?php echo $row['id'] ?>" 
                                <?php echo ($_GET['almacen'] == $row['id']) ? 'selected' : '' ?> >
                                <?php echo $row['nombre'] ?>
                            </option>    
                        <?php endwhile ?>
                    </select>
                </div>
            </div>

            <div class="col-md-12">
            
              <label for="">Insumo</label>

              <select class="form-control" name="txt_IDInsumo" id="txt_IDInsumo"></select>
            </div>

          </div>

            <div class='control-group'>
              <br/>
              <button type='button' class='btn btn-primary' onclick='busquedaKardex()'>Buscar</button>
              <br/>
            </div>
        </div>
      </div>
    </form>
    <br>

    <table class="tb display" cellspacing="0" width="100%" border="0">
        <thead>
            <th>#</th>
            <th>Cód / Num</th>
            <th>Tipo</th>
            <th>Descripción</th>
            <th class="text-right">Cantidad</th>
            <th class="text-right">Stock Parcial</th>
            <th>Fecha</th>
        </thead>
        <tbody>
            <?php

                $query = "select fecha from n_historial_stock_insumo order by id asc limit 1";

                $res = $db->executeQueryEx($query);

                $fechaInicioHistorial = date('Y-m-d');

                while ($row = $db->fecth_array($res)) {
                  $fechaInicioHistorial = $row['fecha'];
                }

                $kardexHelper = new KardexHelper();

                $almacen_id = $_GET['txt_IDAlmacen'];

                if ($almacen_id && $pkInsumo) {


                $kardexHelper->setAlmacen($almacen_id);

                $data_historial = $kardexHelper->_getDataHistorial($fechaInicio, $pkInsumo, $pkInsumoPorcion);

                $data_movimientos_almacen = $kardexHelper->getDataMovimientosDetail($fechaInicio, $fechaFin, $pkInsumo, $pkInsumoPorcion);

                $data_movimientos_ventas = $kardexHelper->getDataVentasDetail($fechaInicio, $fechaFin, $pkInsumo, $pkInsumoPorcion);

                $data_movimientos_ventas_por_cobrar = $kardexHelper->getDataVentasDetailPorCobrar($fechaInicio, $fechaFin, $pkInsumo, $pkInsumoPorcion);

                
                
                $data_platos_vendidos_por_cobrar_init = [];
                
                if ($fechaInicioHistorial != $fechaInicio) {
                  $data_platos_vendidos_por_cobrar_init = $kardexHelper->getDataPlatosVendidosPorCobrar($fechaInicioHistorial, $kardexHelper->getDiaAnterior($fechaInicio));
                } 
                
               

                $data_platos_vendidos_cobrados_hoy = [];

                if ($fechaInicioHistorial != $fechaInicio) {
                  $data_platos_vendidos_cobrados_hoy = $kardexHelper->getDataPlatosVendidosPagadosHoy($fechaInicioHistorial, $kardexHelper->getDiaAnterior($fechaInicio));
                } 
               
                // echo "<br>----------<br>";
                // echo json_encode($data_historial);
                // echo "<br>----------<br>";
                // echo json_encode($data_movimientos_almacen);
                // echo "<br>---DMV-------<br>";
                // echo json_encode($data_movimientos_ventas);

                $data = array_merge([], $data_movimientos_almacen, $data_movimientos_ventas, $data_movimientos_ventas_por_cobrar);

                usort($data, function($a, $b) {
                    return strtotime($a["fecha"]) - strtotime($b["fecha"]);
                });

                $temp = [
                  'insumo_id' => $pkInsumo,
                ];

                if ($pkInsumoPorcion) {
                  $temp['insumo_porcion_id'] = $pkInsumoPorcion;
                } 

                $stock_ventas_cobrar_init = $kardexHelper->getStockPorInsumo($temp, $data_platos_vendidos_por_cobrar_init);

                $stock_ventas_cobrados_hoy = $kardexHelper->getStockPorInsumo($temp, $data_platos_vendidos_cobrados_hoy);

                $stock_ayer = $kardexHelper->getStockPorInsumo($temp, $data_historial);

                $stock_acumulativo = is_null($stock_ayer) ? floatval(0) : floatval($stock_ayer['stock']);

                $stock_acumulativo = $stock_acumulativo - (is_null($stock_ventas_cobrar_init) ? 0 : $stock_ventas_cobrar_init['cantidad_insumo']);

                $stock_acumulativo = $stock_acumulativo - (is_null($stock_ventas_cobrados_hoy) ? 0 : $stock_ventas_cobrados_hoy['cantidad_insumo']);

            ?>  
              <tr class="<?php echo $class ?>">
                  
                  <td></td>
                  <td>Stock Inicial</td>
                  <td></td>
                  <td></td>
                  <td class="text-right"><?php echo floatval($stock_acumulativo) ?></td>
                  <td class="text-right"><?php echo floatval($stock_acumulativo) ?></td>
                  <td><?php echo $fechaInicio ?></td>
              </tr>
            <?php

                foreach ($data as $index => $item):

                  if ($item['tipo'] == 1) {
                    $stock_acumulativo = floatval($stock_acumulativo + $item['cantidad']);
                    $class = 'alert-success';
                  } else if ($item['tipo'] == 2) {
                    $stock_acumulativo = floatval($stock_acumulativo - $item['cantidad']);
                    $class = 'alert-danger';
                  } else {
                    $stock_acumulativo = floatval($stock_acumulativo - $item['cantidad']);
                    $class = 'alert-info';
                  }
            ?>  
              <tr class="<?php echo $class ?>">
                  
                  <td><?php echo $index+1; ?></td>
                  <td><?php echo $item['code'] ?></td>
                  <td><?php echo $item['descripcion'] . ($item['tipo'] == 3 ? ' (Por Cobrar)' : '') ?></td>
                  <td><?php echo $item['motivo'] ?></td>
                  <td class="text-right"><?php echo floatval($item['cantidad']) ?></td>
                  <td class="text-right"><?php echo floatval(round($stock_acumulativo, 4)) ?></td>
                  <td><?php echo $item['fecha'] ?></td>
              </tr>
            
                <?php endforeach; } ?>
        </tbody>
    </table>

  </div>
</body>

<script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery-1.10.2.js"></script>
<script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery-ui-1.10.4.custom.js"></script>
<link rel="stylesheet" href="Public/select2/css/select2.css">
<script type="text/javascript" src="Public/select2/js/select2.js"></script>
<script src="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.js"></script>
<script src="reportes/recursos/js/plugins/tablas/dataTables.buttons.min.js"></script>
<script src="reportes/recursos/js/plugins/tablas/jszip.min.js"></script>
<script src="reportes/recursos/js/plugins/tablas/pdfmake.min.js"></script>
<script src="reportes/recursos/js/plugins/tablas/vfs_fonts.js"></script>
<script src="reportes/recursos/js/plugins/tablas/buttons.html5.min.js"></script>
<script src="reportes/recursos/js/plugins/tablas/buttons.print.min.js"></script>
<script>

$(function () {

  $.ajax({
    url: "<?php echo Class_config::get('urlApp') ?>/?controller=Insumo&&action=List",
    type: 'POST',
    dataType: 'json',
    success: function (data) {
      // $("#txt_NomInsumo").autocomplete({
      //   source: data,
      //   select: function (event, ui) {
      //     $("#txt_NomInsumo").val(ui.item.descripcion);
      //     $("#txt_IDInsumo").val(ui.item.id);
      //     return false;
      //   }
      // });

      var select_insumo = $('#txt_IDInsumo');

        select_insumo.html('<option value="">Seleccione</option>');

        for (let i of data) {
            select_insumo.append(`
                <option value="${i.id}">${i.label}</option>
            `)
        }

        select_insumo.select2({
            width: '100%',
            // dropdownParent: $('#modalFormDetalle')
        });

        <?php if ($pkInsumo):  ?>
            select_insumo.val("<?php echo $pkInsumo ?>");
            select_insumo.trigger('change'); 
        <?php endif ?>
    }

  });
});

$('.tb').DataTable( {
    dom: 'Blfrtip',
    // "order": [[7, "asc"]],
    ordering: false,
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
        title: '<?php echo $titulo_importante;?>'
    }
    ]
} );

$("#fecha_inicio").datepicker({dateFormat: 'yy-mm-dd', maxDate: '<?php echo $fechaCierreCaja ?>', minDate: '<?php echo $fechaInicioHistorial ?>'});
$("#fecha_fin").datepicker({dateFormat: 'yy-mm-dd', maxDate: '<?php echo $fechaCierreCaja ?>', minDate: '<?php echo $fechaInicioHistorial ?>'});


function busquedaKardex(){
  window.location.href="<?php echo Class_config::get('urlApp') ?>/?controller=Report&action=showNKardexDetallado2&" +$('#frmFiltroKardex').serialize();
}

</script>
</html>
