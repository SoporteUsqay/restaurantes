<?php
$titulo_pagina = 'Comprobantes sin enviar';
$titulo_sistema = 'Usqay2';

include_once('recursos/componentes/MasterConexion.php');
$conn = new MasterConexion();
require_once('recursos/componentes/header.php');
//Obtenemos Series
$sboleta = "";
$c0 = "Select * from cloud_config where parametro = 'sboleta'";
$r0 = $conn->consulta_arreglo($c0);
if(is_array($r0)){
    $sboleta = $r0["valor"];
}

$sfactura = "";
$c1 = "Select * from cloud_config where parametro = 'sfactura'";
$r1 = $conn->consulta_arreglo($c1);
if(is_array($r1)){
    $sfactura = $r1["valor"];
}

?>

<div class="alert alert-success alert-dismissible" role="alert" style="display: none;" id="mensaje">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    Se completó la operación
</div>


<h1>Comprobantes electronicos sin enviar</h1>

<div class="panel panel-primary" id='pplatos'>
    <div class="panel-heading">
        <h3 class="panel-title">Relación de comprobantes que no fueron enviados</h3>
    </div>
    <div class="panel-body">
        <div class="tab-pane active" id="boleta" >  
                        <table class="table table-borderer">
                            <thead>
                                <tr>
                                    <th class="text-center">TIPO</th>
                                    <th class="text-center">SERIE</th>
                                    <th class="text-center">CLIENTE</th>
                                    <th class="text-center">FECHA</th>
                                    <th class="text-center">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php 
                            $query = "Select c.* from comprobante_hash ch, comprobante c where ch.aceptada = 'NE' AND ch.pkComprobante = c.pkComprobante";
                            $r = $conn->consulta_matriz($query);
                            if(is_array($r)){
                                foreach($r as $cmp){
                                    echo "<tr>";
                                    if(intval($cmp["pkTipoComprobante"]) === 1){
                                        echo "<td class='text-center'>BOLETA</td>";
                                        echo "<td class='text-center'>".$sboleta."-".str_pad($cmp["ncomprobante"], 6, "0", STR_PAD_LEFT)."</td>";
                                    }else{
                                        echo "<td class='text-center'>FACTURA</td>";
                                        echo "<td class='text-center'>".$sfactura."-".str_pad($cmp["ncomprobante"], 6, "0", STR_PAD_LEFT)."</td>";
                                    }
                                    
                                    if(intval($cmp["pkTipoComprobante"]) === 1){
                                        $query0 = "Select nombres, lastName from person where documento = '".$cmp["documento"]."'";
                                        $r0 = $conn->consulta_arreglo($query0);
                                        if(is_array($r0)){
                                            echo "<td class='text-center'>".$r0["nombres"]." ".$r0["lastName"]." - ".$cmp["documento"]."</td>"; 
                                        }else{
                                            echo "<td class='text-center'></td>";
                                        }
                                    }else{
                                        $query0 = "Select razonSocial from persona_juridica where ruc = '".$cmp["ruc"]."'";
                                        $r0 = $conn->consulta_arreglo($query0);
                                        if(is_array($r0)){
                                            echo "<td class='text-center'>".$r0["razonSocial"]." - ".$cmp["ruc"]."</td>"; 
                                        }
                                    }
                                    echo "<td class='text-center'>".$cmp["fecha"]."</td>";
                                    echo "<td class='text-center'>".$cmp["total"]."</td>";
                                    echo "</tr>";
                                }
                                
                                
                                
                            }
                            ?>
                            </tbody>
                        </table>

        <div class='control-group'>
            <p></p>
            <button type='button' class='btn btn-primary' onclick='procesar()' style="float: right;">Procesar</button>
        </div>
    </div>
</div>
</form>
<div class="progress" id='divcarga' style='display:none;'>
    <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%">
        Procesando...
    </div> 
</div>
   </div><!--/row-->
      <hr>
    </div><!--/.container-->
    
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="recursos/js/jquery.js"></script>
    <script src="recursos/js/jquery-ui.js"></script>
    <script src="recursos/js/plugins/datatables/jquery-datatables.js"></script>
    <script src="recursos/js/plugins/datatables/dataTables.tableTools.js"></script>
    <script src="recursos/js/bootstrap.min.js"></script>
    <script src="recursos/js/offcanvas.js"></script>
    <script src="../Public/select2/js/select2.js"></script>
    <script>

    function procesar(){
        $("#divcarga").show(0);
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Comprobante&&action=procesaPendientesElectronica",
                type: 'GET',
                success: function() {
                    $("#mensaje").show(0);
                    location.reload();
                }
        });

    }
    
    jQuery.fn.reset = function () {
        $(this).each(function () {
            this.reset();
        });
    };


    $(document).ready(function () {
        history.pushState(null, "", 'comprobantes_pendientes.php');
    });

    </script>
  </body>
</html>

                      