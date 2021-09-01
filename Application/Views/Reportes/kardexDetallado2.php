
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

  $titulo_importante = "Kardex (Seguimiento Detallado) del ".$fechaInicio." al ".$fechaFin;

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
    <h3>Kardex (Seguimiento Detallado)</h3>
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
          <th>INSUMO</th>
          <th>S. ANTERIOR</th>
          <th>CODIGO / NUMERO</th>
          <th>TIPO</th>
          <th>DESCRIPCION</th>
          <th style="text-align:center">CANTIDAD</th>
          <th>S. PARCIAL</th>
          <th style="text-align:center">Fecha</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $caja= new Application_Models_CajaModel();
        $fecha= $caja->fechaCierre();        
        $db = new SuperDataBase();
        $stock = 0.00;
        $query = "SELECT IFNULL(cantidadInicial,0.00000) as cantidadInicial, fecha FROM historial_stock_insumos WHERE pkInsumo = $pkInsumo AND fecha BETWEEN '$fechaInicio' AND '$fechaFin' ORDER BY fecha LIMIT 1;";
        $result = $db->executeQuery($query);
        if($row = $db->fecth_array($result)){
          $stock = $row["cantidadInicial"];
          $fechaInicio = $row["fecha"];
          while ($row = $db->fecth_array($result)) {
            $stock = $row["cantidadInicial"];
            $fechaInicio = $row["fecha"];
          }
        }
        $cadena="";
        //consultando si la fecha de cierre está enre las fechas asignadas para el reporte
        $query = "SELECT IFNULL((SELECT 0 FROM cierrediario WHERE '$fecha' BETWEEN '$fechaInicio' AND '$fechaFin'),1) AS valor;";
        $result = $db->executeQuery($query);
        while ($row = $db->fecth_array($result)) {
          if($row["valor"]==0){
          $cadena = "UNION (SELECT null,p.pkPediido,3, 'PEDIDO','VENTA',((SELECT  ifnull(SUM(ROUND((cantidadTotal*dp.cantidad),5)),0.00000) FROM insumo_menu im INNER JOIN detallepedido dp ON dp.pkPlato=im.pkPlato WHERE im.pkInsumo=$pkInsumo AND p.pkPediido = dp.pkPediido AND dp.estado > 0 AND dp.estado <3 AND dp.horaPedido BETWEEN CAST('".$tiempo_inicio."' as DATETIME) AND CAST('".$tiempo_fin."' as DATETIME)))AS tvendido,p.fechaCierre FROM pedido p )";
          }
        }
        
        $query =   "SELECT ing.pkIngresoInsumo,c.numeroComprobante, ing.tipo,
        CASE ing.tipo
        WHEN 1 THEN 'GUIA DE INGRESO'
        WHEN 2 THEN 'GUIA DE SALIDA'
        END AS guia,
        CASE c.tipoComprobante
        WHEN 1 THEN 'COMPRA (BOLETA)'
        WHEN 2 THEN 'COMPRA (FACTURA)'
        WHEN 3 THEN 'TRANSFERENCIA'
        WHEN 4 THEN CONCAT((ing.descripcion),'')
        END AS descripcion,
        ing.cantidad, DATE (c.fecha) AS fecha
        FROM ingresoinsumos ing INNER JOIN comprobante_ingreso c ON ing.pkComprobante=c.pkComprobante
        INNER JOIN tipocomprobante tc ON c.tipoComprobante=tc.pktipoComprobante
        WHERE c.fecha BETWEEN '$fechaInicio' AND '$fechaFin' AND ing.pkInsumo=$pkInsumo and ing.estado=0
        UNION
        (SELECT null,ip.pkPediido,3, 'PEDIDO','VENTA',ip.cantidad AS tvendido,fecha
          FROM insumoporpedido ip
          WHERE ip.pkInsumo=$pkInsumo AND ip.fecha BETWEEN '$fechaInicio' AND '$fechaFin') ".$cadena." ORDER BY fecha,tipo;";
          //echo $query;
          $result = $db->executeQuery($query);
          $class='';
          while ($row = $db->fecth_array($result)) {
            if($row["cantidad"] > 0){
              switch ($row["tipo"]){
                case 1: $class='success';
                break;
                case 2: $class='danger';
                break;
                case 3: $class='';
                break;
              }
              echo "<tr class='$class'>";
              echo "<td>".$_GET["txt_NomInsumo"]."</td>";
              echo "<td style='text-align:right'>" . number_format(floatval($stock), 3, '.', ' ')."</td>";
              echo "<td style='text-align:center'>" . $row["numeroComprobante"]."</td>";
              echo "<td>" . $row["guia"]."</td>";
              echo "<td style='width: 250px'>" . $row["descripcion"]."</td>";
              echo "<td style='text-align:center'>" . number_format(floatval($row["cantidad"]), 3, '.', ' ') . "</td>";
              if($row["tipo"] == 1){
                $stock = number_format(floatval($stock + $row["cantidad"]), 3, '.', ' ');
              }else{
                $stock = number_format(floatval($stock - $row["cantidad"]), 3, '.', ' ');
              }
              echo "<td style='text-align:right'>" . $stock ."</td>";
              echo "<td style='text-align:center'>" . $row['fecha'] . "</td>";
              echo "</tr>";
            }
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

$('.tb').DataTable( {
    dom: 'Blfrtip',
    "order": [[7, "asc"]],
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

$("#fecha_inicio").datepicker({dateFormat: 'yy-mm-dd'});
$("#fecha_fin").datepicker({dateFormat: 'yy-mm-dd'});

function busquedaKardex(){
  window.location.href="<?php echo Class_config::get('urlApp') ?>/?controller=Report&action=showNKardexDetallado2&" +$('#frmFiltroKardex').serialize();
}

</script>
</html>
