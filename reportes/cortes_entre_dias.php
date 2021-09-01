<?php
$titulo_pagina = 'Cortes entre fechas';
$titulo_sistema = 'Usqay2';

include_once('recursos/componentes/MasterConexion.php');
$conn = new MasterConexion();

$inicio = NULL;
$fin = NULL;
$caja = NULL;

if(isset($_GET["in"])){
    $inicio = $_GET["in"];
}

if(isset($_GET["fn"])){
    $fin = $_GET["fn"];
}

if(isset($_GET["ca"])){
    $caja = $_GET["ca"];
}

$cortes = NULL;

if($inicio <> NULL && $fin <> NULL && $caja <> NULL){
    if($caja !== "ALL"){
        $cortes_sql = "Select c.*, ac.caja from corte c, accion_caja ac where ac.pk_accion = c.id AND ac.tipo_accion = 'CUT' AND ac.caja = '".$caja."' AND c.fecha_cierre BETWEEN '".$inicio."' AND '".$fin."'";
    }else{
        $cortes_sql = "Select c.*, ac.caja from corte c, accion_caja ac where ac.pk_accion = c.id AND ac.tipo_accion = 'CUT' AND fecha_cierre BETWEEN '".$inicio."' AND '".$fin."'";
    }
    
    //echo $cortes_sql;
    
    $cortes = $conn->consulta_matriz($cortes_sql);
}

if($inicio === NULL){
    $inicio = date("Y-m-d");
    $fin = date("Y-m-d");
}

function _TotalDiaCorte($fecha,$corte,$caja) {
    $conn = new MasterConexion();
    
    $query1 = "Select c.*, ac.caja from corte c, accion_caja ac where c.inicio = '".$corte."' AND ac.pk_accion = c.id AND ac.tipo_accion = 'CUT' AND ac.caja = '".$caja."'";

    $minicial = 0;

    $consulta_corte = " ";

    $r01 = $conn->consulta_arreglo($query1);
    if (is_array($r01)) {
        $minicial = floatval($r01["monto_inicial"]);
        if($r01["fin"] == ""){
            $consulta_corte = " AND dateModify >= '".$r01["inicio"]."' ";
        }else{
            $consulta_corte = " AND dateModify BETWEEN '".$r01["inicio"]."' AND '".$r01["fin"]."'";
        }
    }

    $query = "SELECT
             IFNULL((SELECT SUM(gd.cantidad) FROM gastos_diarios gd, accion_caja ac WHERE gd.fecha='$fecha'".$consulta_corte."AND gd.estado_anular=0 AND (gd.estado=0 OR gd.estado=2 OR gd.estado=3) AND ac.pk_accion = gd.pkGastosDiarios AND ac.tipo_accion = 'GAS' AND ac.caja = '".$caja."'),0.00) AS total_gastado,
             IFNULL((SELECT SUM(gd.cantidad) FROM gastos_diarios gd, accion_caja ac WHERE gd.fecha='$fecha'".$consulta_corte."AND gd.estado_anular=0 AND gd.estado=4 AND ac.pk_accion = gd.pkGastosDiarios AND ac.tipo_accion = 'GAS' AND ac.caja = '".$caja."'),0.00) AS total_devuelto,
             IFNULL((SELECT SUM(gd.cantidad) FROM gastos_diarios gd, accion_caja ac WHERE gd.fecha='$fecha'".$consulta_corte."AND gd.estado_anular=0 AND gd.estado=1 AND ac.pk_accion = gd.pkGastosDiarios AND ac.tipo_accion = 'GAS' AND ac.caja = '".$caja."'),0.00) AS total_ingresado,
             IFNULL((SELECT SUM(p.total_efectivo) FROM pedido p, accion_caja ac WHERE p.fechaCierre='$fecha'".$consulta_corte."AND p.estado=1 AND ac.pk_accion = p.pkPediido AND ac.tipo_accion = 'PED' AND ac.caja = '".$caja."'),0.00) AS total_vendidoET,
             IFNULL((SELECT SUM(p.total) FROM pedido p, accion_caja ac WHERE p.fechaCierre='$fecha'".$consulta_corte."AND p.estado=4 AND ac.pk_accion = p.pkPediido AND ac.tipo_accion = 'PED' AND ac.caja = '".$caja."'),0.00) AS total_credito,
             IFNULL((SELECT SUM(p.total_tarjeta) FROM pedido p, accion_caja ac WHERE p.fechaCierre='$fecha'".$consulta_corte."AND p.nombreTarjeta='VISA' AND p.estado=1 AND ac.pk_accion = p.pkPediido AND ac.tipo_accion = 'PED' AND ac.caja = '".$caja."'),0.00) AS visa,
             IFNULL((SELECT SUM(p.total_tarjeta) FROM pedido p, accion_caja ac WHERE p.fechaCierre='$fecha'".$consulta_corte."AND p.nombreTarjeta='MASTERCARD' AND p.estado=1 AND ac.pk_accion = p.pkPediido AND ac.tipo_accion = 'PED' AND ac.caja = '".$caja."'),0.00) as mastercard,
             IFNULL((Select visa + mastercard),0.00)AS total_vendidot,
             IFNULL((Select total_vendidoET + total_vendidot),0.00)AS total_V;";
    $r02 = $conn->consulta_arreglo($query);
    $array = array();
    if (is_array($r02)) {
        $array[] = array(
            "vendidoD" => ( $r02['total_V']),
            "vendido" => $r02['total_vendidoET'],
            "vendidoT" =>  $r02['total_vendidot'],
            "gastado" => $r02['total_gastado'],
            "devuelto" => $r02['total_devuelto'],
            "ingresado" => $r02['total_ingresado'],
            "credito" => $r02['total_credito'],
            "montoInicial" =>  sprintf('%0.2f', $minicial) ,
            "visa" => $r02['visa'],
            "mastercard" => $r02['mastercard'],
            "total" => ($r02['total_vendidoET'] + $minicial + $r02['total_ingresado'])- $r02['total_gastado']
        );
    }
    
    return $array;
}

require_once('recursos/componentes/header.php'); 
?>
<h1>Cortes entre Fechas</h1>

<div class="panel panel-primary" id='pplatos'>
    <div class="panel-heading">
        <h3 class="panel-title"></h3>
    </div>
    <div class="panel-body">

        <div class='control-group'>
            <label>Caja</label>
            <select class="form-control" id="id_caja">
                <option value="ALL">---Todas--</option>
                <?php 
                    $cajas = $conn->consulta_matriz("Select * from cajas");
                    if(is_array($cajas)){
                        foreach ($cajas as $ca){
                            echo "<option value='".$ca["caja"]."'";
                            if($ca["caja"] == $caja){
                                echo "SELECTED";
                            }
                            echo ">".utf8_encode($ca["caja"])."</option>";
                        }
                    }
                ?>
            </select>
        </div>
        <div class='control-group'>
            <label>Inicio</label>
            <input class='form-control' placeholder='AAAA-MM-DD' id='inicio' value="<?php echo $inicio; ?>"/>
        </div>
        <div class='control-group'>
            <label>Fin</label>
            <input class='form-control' placeholder='AAAA-MM-DD' id='fin' value="<?php echo $fin; ?>"/>
        </div>
        <div class='control-group'>
            <p></p>
            <button type='button' class='btn btn-primary' onclick='filtrar()'>Filtrar</button>
        </div>
    </div>
</div>
</form>
<hr/>
<div class='contenedor-tabla' style="overflow-x: scroll !important;">
    <table id='tb' class='display' cellspacing='0' width='100%' border="0">
        <thead>
            <tr>
                <th>Caja</th>
                <th>Cierre</th>
                <th>Inicio</th>
                <th>Fin</th>
                <th>Total Vendido</th>
                <th>Credito</th>
                <th>Efectivo</th>
                <th>Tarjeta</th>
                <th>Visa</th>
                <th>Mastercard</th>
                <th>Monto Inicial</th>
                <th>Ingreso Adicional</th>
                <th>Gastado</th>
                <th>Devoluciones</th>
                <th>Total en Caja</th>
            </tr>
        </thead>
        <tbody>
        <?php if(is_array($cortes)):
              $total_vendido = 0;
              $total_credito = 0;
              $total_efectivo = 0;
              $total_tarjeta = 0;
              $total_visa = 0;
              $total_master = 0;
              $total_iniciales = 0;
              $total_ingresos = 0;
              $total_gastado = 0;
              $total_devoluciones = 0;
              $total_en_caja = 0;
              foreach($cortes as $rw):
              $data_corte = _TotalDiaCorte($rw["fecha_cierre"],$rw["inicio"],$rw["caja"]);
              $data_corte = $data_corte[0];
              ?>
            <tr>
                <td><?php echo $rw["caja"];?></td>
                <td><?php echo $rw["fecha_cierre"];?></td>
                <td><?php echo $rw["inicio"];?></td>
                <td><?php echo $rw["fin"];?></td>
                <td>S/ <?php echo $data_corte["vendidoD"];
                $total_vendido = $total_vendido + floatval($data_corte["vendidoD"]);
                ?></td>
                <td>S/ <?php echo $data_corte["credito"];
                $total_credito = $total_credito + floatval($data_corte["credito"]);
                ?></td>
                <td>S/ <?php echo $data_corte["vendido"];
                $total_efectivo = $total_efectivo + floatval($data_corte["vendido"]);
                ?></td>
                <td>S/ <?php echo $data_corte["vendidoT"];
                $total_tarjeta = $total_tarjeta + floatval($data_corte["vendidoT"]);
                ?></td>
                <td>S/ <?php echo $data_corte["visa"];
                $total_visa = $total_visa + floatval($data_corte["visa"]);
                ?></td>
                <td>S/ <?php echo $data_corte["mastercard"];
                $total_master = $total_master + floatval($data_corte["mastercard"]);
                ?></td>
                <td>S/ <?php echo $data_corte["montoInicial"];
                $total_iniciales = $total_iniciales + floatval($data_corte["montoInicial"]);
                ?></td>
                <td>S/ <?php echo $data_corte["ingresado"];
                $total_ingresos = $total_ingresos + floatval($data_corte["ingresado"]);
                ?></td>
                <td>S/ <?php echo $data_corte["gastado"];
                $total_gastado = $total_gastado + floatval($data_corte["gastado"]);
                ?></td>
                <td>S/ <?php echo $data_corte["devuelto"];
                $total_devoluciones = $total_devoluciones + floatval($data_corte["devuelto"]);
                ?></td>
                <td>S/ <?php echo $data_corte["total"];
                $total_en_caja = $total_en_caja + floatval($data_corte["total"]);
                ?></td>
            </tr>
        <?php 
            endforeach;
        ?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>S/ <?php echo number_format($total_vendido, 2, '.', '');?></td>
                <td>S/ <?php echo number_format($total_credito, 2, '.', '');?></td>
                <td>S/ <?php echo number_format($total_efectivo, 2, '.', '');?></td>
                <td>S/ <?php echo number_format($total_tarjeta, 2, '.', '');?></td>
                <td>S/ <?php echo number_format($total_visa, 2, '.', '');?></td>
                <td>S/ <?php echo number_format($total_master, 2, '.', '');?></td>
                <td>S/ <?php echo number_format($total_iniciales, 2, '.', '');?></td>
                <td>S/ <?php echo number_format($total_ingresos, 2, '.', '');?></td>
                <td>S/ <?php echo number_format($total_gastado, 2, '.', '');?></td>
                <td>S/ <?php echo number_format($total_devoluciones, 2, '.', '');?></td>
                <td>S/ <?php echo number_format($total_en_caja, 2, '.', '');?></td>
            </tr>
        <?php
            endif;
        ?>
<?php
$nombre_tabla = 'cortes_entre_dias';
require_once('recursos/componentes/footer.php');
?>
<script>

    
    function filtrar(){
        var id_caja = $("#id_caja").val();
        var inicio = $("#inicio").val();
        var fin = $("#fin").val();
        location.href = "cortes_entre_dias.php?in="+inicio+"&fn="+fin+"&ca="+id_caja;
    }
    
    $(document).ready(function() {
        $('.form-control').select2();
    });
</script>