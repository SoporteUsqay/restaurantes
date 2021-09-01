<?php
  // error_reporting(E_ALL);
 
  include_once('reportes/recursos/componentes/MasterConexion.php');
  $conn = new MasterConexion();

  // $fechaInicio = date('Y-m-d', strtotime('-1 month'));
  $fechaInicio = date('Y-m-d');
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

  $titulo_importante = "Kardex (Seguimiento Diario) del ".$fechaInicio." al ".$fechaFin;

  include 'Application/Views/template/header.php';

  $objViewMenu = new Application_Views_IndexView();
  $objViewMenu->showContent();

  $obj = new Application_Models_CajaModel();

  $fechaCierreCaja = $obj->fechaCierre();

  require_once('Application/Views/Almacen/KardexHelper.php');

  $kardexHelper = new KardexHelper();

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
    <h3>Kardex (Seguimiento Diario)</h3>
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

          

          

          <!-- <label >Insumo</label>
          <input id="txt_NomInsumo" name="txt_NomInsumo" type="text" class="form-control llevar" placeholder="Ingrese el insumo" value="<?php echo $nombreInsumo ?>">
          <input name="txt_IDInsumo" id="txt_IDInsumo" type="text"  style="display: none" class="form-control" value="<?php echo $pkInsumo ?>"
          placeholder="Ingrese el insumo"> -->
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
          <th style="text-align:right">Porción</th>
          <th style="text-align:right">Stock Anterior</th>
          <th style="text-align:right">T. Ingresos</th>
          <th style="text-align:right">T. Salidas</th>
          <th style="text-align:right">T. Vendido</th>
          <th style="text-align:right">T. Por Cobrar</th>
          <th style="text-align:right">Stock Parcial</th>
          <th style="text-align:center">Fecha</th>
          <th>Movimientos</th>
        </tr>
      </thead>
        <tbody>

        <?php 
                $query = "select fecha from n_historial_stock_insumo order by id asc limit 1";

                $res = $db->executeQueryEx($query);
                
                $fechaInicioHistorial = date('Y-m-d');

                while ($row = $db->fecth_array($res)) {
                  $fechaInicioHistorial = $row['fecha'];
                }
                
                $almacen_id = isset($_GET['txt_IDAlmacen']) ? $_GET['txt_IDAlmacen'] : null;

                if ($almacen_id && $pkInsumo) {

                $kardexHelper->setAlmacen($almacen_id);


                $subquery = "";

                if ($pkInsumo && !$pkInsumoPorcion) {
                    $subquery = "AND n_detalle_movimiento_almacen.insumo_porcion_id is null";
                } 

                if ($pkInsumo && $pkInsumoPorcion) {
                    $subquery = "AND n_detalle_movimiento_almacen.insumo_porcion_id = n_historial_stock_insumo.insumo_porcion_id";
                } 

                

                $query = "SELECT
                    n_historial_stock_insumo.*, 
                    (
                        SELECT
                            IFNULL(SUM(n_detalle_movimiento_almacen.cantidad), 0)
                        FROM
                            n_detalle_movimiento_almacen
                        WHERE
                        cast(n_detalle_movimiento_almacen.fecha as date) = n_historial_stock_insumo.fecha
                        AND n_detalle_movimiento_almacen.tipo = 1
                        AND n_detalle_movimiento_almacen.deleted_at IS NULL
                        AND n_detalle_movimiento_almacen.almacen_id = n_historial_stock_insumo.almacen_id
                        AND n_detalle_movimiento_almacen.insumo_id = n_historial_stock_insumo.insumo_id
                        $subquery
                    ) AS ingreso,
                    (
                        SELECT
                            IFNULL(SUM(n_detalle_movimiento_almacen.cantidad), 0)
                        FROM
                            n_detalle_movimiento_almacen
                        WHERE
                        cast(n_detalle_movimiento_almacen.fecha as date) = n_historial_stock_insumo.fecha
                        AND n_detalle_movimiento_almacen.tipo = 2
                        AND n_detalle_movimiento_almacen.deleted_at IS NULL
                        AND n_detalle_movimiento_almacen.almacen_id = n_historial_stock_insumo.almacen_id
                        AND n_detalle_movimiento_almacen.insumo_id = n_historial_stock_insumo.insumo_id
                        $subquery
                    ) AS salida,
                    (SELECT (
                      stock_inicial + ingreso - salida - IFNULL(stock_final, 0)
                    )) AS stock_vendido,
                    insumos.descripcionInsumo AS nombre_insumo,
                    insumo_porcion.cantidad,
                    unidad.descripcion AS nombre_unidad,
                    insumo_porcion.descripcion
                FROM
                    n_historial_stock_insumo
                LEFT JOIN insumos ON n_historial_stock_insumo.insumo_id = insumos.pkInsumo
                LEFT JOIN insumo_porcion ON n_historial_stock_insumo.insumo_porcion_id = insumo_porcion.id
                LEFT JOIN unidad ON insumo_porcion.unidad_id = unidad.pkUnidad
                WHERE 
                    n_historial_stock_insumo.almacen_id = $almacen_id
                and cast(fecha as date) BETWEEN '$fechaInicio' and '$fechaFin'
                ";

                if ($pkInsumo && !$pkInsumoPorcion) {
                    $query .= " and n_historial_stock_insumo.insumo_id = $pkInsumo and n_historial_stock_insumo.insumo_porcion_id is null";
                } 

                if ($pkInsumo && $pkInsumoPorcion) {
                    $query .= " and n_historial_stock_insumo.insumo_id = $pkInsumo and n_historial_stock_insumo.insumo_porcion_id = $pkInsumoPorcion";
                }

                // echo $query;

                $res = $db->executeQueryEx($query);

                $data_ventas = [];

                // if ($fechaCierreCaja == $fechaFin) {

                  //  echo 'Es fecha actual';

                  //  $data_ventas = $kardexHelper->getDataPlatosVendidos($fechaCierreCaja);

                  //  echo json_encode($data_ventas);
                // }

                $last_stock = 0;
                $index = 0;

                while ($item = $db->fecth_array($res)):
                  $copy = array_merge($item, []);

                  // si no es porcion, borramos la key para que el metodo "getStockPorInsumo" funcione
                  if (is_null($item['insumo_porcion_id'])) {
                    unset($copy['insumo_porcion_id']);
                  }

                  $data_platos_vendidos_por_cobrar_init = [];

                  if ($fechaInicioHistorial != $item['fecha']) {
                      $data_platos_vendidos_por_cobrar_init = $kardexHelper->getDataPlatosVendidosPorCobrar($fechaInicioHistorial, $kardexHelper->getDiaAnterior($item['fecha']));
                  } 

                  // echo "<br>" . json_encode($data_platos_vendidos_por_cobrar_init);

                  $stock_ventas_cobrar_init = $kardexHelper->getStockPorInsumo($copy, $data_platos_vendidos_por_cobrar_init);

                  $item['stock_inicial'] = $item['stock_inicial'] - (is_null($stock_ventas_cobrar_init) ? 0 : $stock_ventas_cobrar_init['cantidad_insumo']);

                  
                  $data_platos_vendidos_cobrados_hoy = [];
                  
                  if ($fechaInicioHistorial != $item['fecha']) {
                    $data_platos_vendidos_cobrados_hoy = $kardexHelper->getDataPlatosVendidosPagadosHoy($fechaInicioHistorial, $kardexHelper->getDiaAnterior($item['fecha']));
                  } 

                  // echo "<br>" . json_encode($data_platos_vendidos_cobrados_hoy);

                  $stock_ventas_cobrados_hoy = $kardexHelper->getStockPorInsumo($copy, $data_platos_vendidos_cobrados_hoy);

                  $item['stock_inicial'] = $item['stock_inicial'] - (is_null($stock_ventas_cobrados_hoy) ? 0 : $stock_ventas_cobrados_hoy['cantidad_insumo']);

                  // if ($index++ > 0) {
                  //   if ($last_stock != $item['stock_inicial']) {
                  //     $item['stock_inicial'] = $last_stock;
                  //   }
                  // }

                  // si el rango incluye hoy sumamos las ventas hasta ahora del dia de hoy
                  // if ($fechaCierreCaja == $item['fecha']) {
                    $data_ventas = $kardexHelper->getDataPlatosVendidos($item['fecha'], $item['fecha']);

                    $stock_ventas = $kardexHelper->getStockPorInsumo($copy, $data_ventas);

                    $item['stock_vendido'] = is_null($stock_ventas) ? 0 : $stock_ventas['cantidad_insumo'];

                    $item['stock_final'] = $item['stock_inicial'] + $item['ingreso'] - $item['salida'] - $item['stock_vendido'];
                  // }
                  
                  // si el rango incluye hoy sumamos las ventas hasta ahora del dia de hoy
                  // // if ($fechaCierreCaja == $item['fecha']) {
                    
                  // }
                   
                  $data_platos_vendidos_por_cobrar = $kardexHelper->getDataPlatosVendidosPorCobrar($item['fecha'], $item['fecha']);

                  $stock_ventas_cobrar = $kardexHelper->getStockPorInsumo($copy, $data_platos_vendidos_por_cobrar);

                  $item['stock_vendido_cobrar'] = is_null($stock_ventas_cobrar) ? 0 : $stock_ventas_cobrar['cantidad_insumo'];

                  $item['stock_final'] = $item['stock_inicial'] + $item['ingreso'] - $item['salida'] - $item['stock_vendido'] - $item['stock_vendido_cobrar'];

                  // $last_stock = $item['stock_final'];
                  
                ?>
                <tr>
                    
                    <td><?php echo $item['nombre_insumo'] ?></td>
                    <td><?php echo implode(' ', [
                        floatval($item['cantidad']) == 0 ? '' : floatval($item['cantidad']),
                        ($item['nombre_unidad']),
                        ($item['descripcion']),
                    ]) ?></td>
                    <td class="text-right"><?php echo floatval($item['stock_inicial']) ?></td>
                    <td class="text-right"><?php echo floatval($item['ingreso']) ?></td>
                    <td class="text-right"><?php echo floatval($item['salida']) ?></td>
                    <td class="text-right"><?php echo floatval($item['stock_vendido']) ?></td>
                    <td class="text-right"><?php echo floatval($item['stock_vendido_cobrar']) ?></td>
                    <td class="text-right <?php echo $item['stock_final'] < 0 ? 'alert-danger' : '' ?>"><?php echo floatval($item['stock_final']) ?></td>
                    <td class="text-center"><?php echo $item['fecha'] ?></td>
                    <td class="text-center">
                        <a class="btn" onclick="KardexDetallado('<?php echo $item['fecha'] ?>', '<?php echo $item['insumo_id'] ?>', '<?php echo $item['insumo_porcion_id'] ?>', '<?php echo $item['nombre_insumo'] ?>')" title="Ver Detalles">
                            <span class='glyphicon glyphicon-log-out'></span>
                        </a>
                    </td>
                    

                </tr>    

                    <?php endwhile; }?>

              </tbody>
          </table>
      </div>
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
    $('.tb').DataTable( {
        dom: 'Blfrtip',
        "order": [[8, "asc"]],
        buttons: [
        {
            extend: 'excelHtml5',
            title: '<?php echo $titulo_importante;?>',
            exportOptions: {
                columns: [0,1,2,3,4,5,6,7,8]
            },
        },
        {
            extend: 'pdfHtml5',
            orientation: 'portrait',
            alignment: 'center',
            pageSize: 'LEGAL',
            exportOptions: {
                columns: [0,1,2,3,4,5,6,7,8]
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

   
    function busquedaKardex(){

      console.log($('#frmFiltroKardex').serialize())
      // return

      window.location.href="<?php echo Class_config::get('urlApp') ?>/?controller=Report&action=showNKardexDetallado&" +$('#frmFiltroKardex').serialize();
    }
    
    function KardexDetallado(fecha, insumo_id, insumo_porcion_id, nombre_insumo){

      let params = "";

      params += "&fecha_inicio=" + fecha;

      params += "&fecha_fin=" + fecha;

      params += "&txt_NomInsumo=" + nombre_insumo;

      params += "&txt_IDInsumo=" + insumo_id;

      params += "&txt_IDInsumoPorcion=" + insumo_porcion_id;

      params += "&txt_IDAlmacen=" + "<?php echo $almacen_id ?>";

      params += "&view=detail";

      var url = "<?php echo Class_config::get('urlApp') ?>/?controller=Report&action=showNKardexDetallado2" + params;   
      window.open(url, '_blank');
    }

    $(document).ready(function () {
      $("#fecha_inicio").datepicker({dateFormat: 'yy-mm-dd', maxDate: '<?php echo $fechaCierreCaja ?>', minDate: '<?php echo $fechaInicioHistorial ?>'});
      $("#fecha_fin").datepicker({dateFormat: 'yy-mm-dd', maxDate: '<?php echo $fechaCierreCaja ?>', minDate: '<?php echo $fechaInicioHistorial ?>'});

      console.log($("#fecha_inicio"),{dateFormat: 'yy-mm-dd', maxDate: '<?php echo $fechaCierreCaja ?>', minDate: '<?php echo $fechaInicioHistorial ?>'})
    })
</script>
</html>
