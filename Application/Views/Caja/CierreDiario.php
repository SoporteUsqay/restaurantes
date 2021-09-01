<?php
$titulo_importante = "Cierre de Caja";
error_reporting(E_ALL);
include 'Application/Views/template/header.php';
include_once('reportes/recursos/componentes/MasterConexion.php');
$conn = new MasterConexion();

$objViewMenu = new Application_Views_IndexView();
$objViewMenu->showContent();
$caja = new Application_Models_CajaModel();
$fecha = $caja->fechaCierre(); 

//Solo se muestra la caja a usuario 1 o 2
//Obtenemos Listado de Cajas
$query_cajas = $conn->consulta_matriz("Select * from cajas");
//Nivel de Usuario
$estilo_nivel = "style='display:none;'";
if(intval($_COOKIE["TYP"]) > 0 &&  intval($_COOKIE["TYP"]) < 3){
    $estilo_nivel = "";
}

?>
<body>
    <div class="container">
        <input value="<?php echo UserLogin::get_pkSucursal(); ?>" id="lblsucursal" name="lblsucursal" >
        <br>
        <br>
        <br>

        <div class="panel panel-success">
            <div class="panel-body panel-success row">

                <div class="form-group col-xs-3">
                    <label for="">Fecha</label>
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                        <input name="fecha" aria-describedby="basic-addon1" class="form-control date" id="txtFechaCierreDiario" value="<?php echo $fecha ?>" onchange="carga_cortes()">
                    </div>
                </div>

                <div class="form-group col-xs-2" <?php echo $estilo_nivel;?>>
                    <label for="">Caja</label>
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1">
                            <span class="glyphicon glyphicon-inbox"></span>
                        </span>
                        <select aria-describedby="basic-addon1" name="caja" class="form-control" id="txtCajaAct" onchange="carga_cortes()">
                            <option value=''>Todas</option>
                            <?php 
                            if(is_array($query_cajas)){
                                foreach($query_cajas as $cajas){
                                    if($_COOKIE["c"] === $cajas["caja"]){
                                        echo "<option value='".$cajas["caja"]."' selected>".$cajas["caja"]."</option>";
                                    }else{
                                        echo "<option value='".$cajas["caja"]."'>".$cajas["caja"]."</option>";
                                    }
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group col-xs-3" id="divcorte">
                    <label for="">Corte</label>
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1">
                            <span class="glyphicon glyphicon-flash"></span>
                        </span>
                        <select aria-describedby="basic-addon1" name="fecha" class="form-control" id="txtCorteAct" onchange="carga_data_corte()">
                        </select>
                    </div>
                </div>

                <div class="form-group col-xs-4" style="text-align:center;">
                        <a onclick="openImprimirCierreCuentaDiario()" class="btn btn-primary" id="btnImprimirTicket" style='margin-right:5px;margin-top:5px;'><span class='glyphicon glyphicon-print' aria-hidden='true'></span> Imprimir</a>

                        <a onclick="corte()" class="btn btn-primary" id="btnCut" style='margin-right:5px;margin-top:5px;'><span class="glyphicon glyphicon-flash" aria-hidden="true"></span> Corte</a>

                        <a onclick="generaPDF2019()" class="btn btn-primary" id="btnPDF" style='margin-right:5px;margin-top:5px;'><span class="glyphicon glyphicon-save" aria-hidden="true"></span> PDF</a>

                        <a onclick="enviaMail2019()" class="btn btn-primary" id="btnEmail" style='margin-right:5px;margin-top:5px;'><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> E-Mail</a>

                        <a onclick="wincha()" class="btn btn-primary" id="btnWincha" style='margin-right:5px;margin-top:5px;'><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Wincha Platos</a>
                        
                        <a onclick="sendFondosExternos()" class="btn btn-primary" id="btnFE" style='margin-right:5px;margin-top:5px;'>
                            <span class="fa fa-money" aria-hidden="true"></span> 
                            Enviar a Fondos Externos
                        </a>
                </div>

            </div>
        </div>

        <?php
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
        
        //Obtenemos todas las monedas
        $monedas = array();
        $resultado_monedas = $conn->consulta_matriz("Select * from moneda where estado > 0");
        if(is_array($resultado_monedas)){
            foreach($resultado_monedas as $moneda){
                $tmp = array();
                $tmp["id"] = $moneda["id"];
                $tmp["nombre"] = $moneda["nombre"];
                $tmp["simbolo"] = $moneda["simbolo"];
                $monedas[] = $tmp;
            }
        }

        //Obtenemos todos los tipos de pagos
        $tipos_gastos = array();
        $resultado_gastos = $conn->consulta_matriz("Select * from tipo_gasto where estado = 1");
        if(is_array($resultado_gastos)){
            foreach($resultado_gastos as $gasto){
                $tmp = array();
                $tmp["id"] = $gasto["id"];
                $tmp["nombre"] = $gasto["nombre"];
                $tmp["direccion"] = $gasto["direccion"];
                $tipos_gastos[] = $tmp;
            }
        }
        ?>
        
        <div class="panel panel-primary" id="panel_data">
            <div class="panel-heading">CONSOLIDADO DEL CIERRE</div>
            <div class="panel-body">

            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active" style="width:50%;font-weight: bold; text-align: center;">
                    <a href="#dinero" aria-controls="dinero" role="tab" data-toggle="tab">Dinero</a>
                </li>
                <li role="presentation" style="width:50%;font-weight: bold; text-align: center;">
                    <a href="#platos" aria-controls="platos" role="tab" data-toggle="tab">Platos y/o Productos</a>
                </li>
            </ul>

            <div class="tab-content">
            <div role="tabpanel" class="tab-pane active row" id="dinero">
                
                <div class="alert alert-success alert-dismissible" role="alert" style="display: none;" id="exito">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  Operación Realizada con Éxito
                </div>

                <div class="alert alert-danger alert-dismissible" role="alert" style="display: none;" id="fracaso">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  Hubo un error, reintenta
                </div>

                <div class="col-lg-12 col-xs-12" style="font-size: 22px;margin-top: 15px;margin-bottom: 15px;">
                    <span class="label label-success">TOTALES</span>
                </div>

                <?php foreach($medios as $med):?>
                <div class="form-group col-lg-3">
                    <label><?php echo $med["nombre"];?></label>
                    <input readonly type="number" class="form-control" id="tot_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>" value="0.00">
                </div>
                <?php endforeach;?>

                <div class="form-group col-lg-3">
                    <label>DETRACCIÓN</label>
                    <input readonly type="number" class="form-control" id="tot_detraccion" value="0.00">
                </div>

                <div class='col-lg-12 col-xs-12'>
                    <hr/>
                </div>


                <div class="col-lg-12 col-xs-12" style="font-size: 22px;margin-top: -5px;margin-bottom: 15px;">
                    <span class="label label-info">INGRESO VENTAS</span>
                </div>

                <?php foreach($medios as $med):?>
                <div class="form-group col-lg-3">
                    <label><?php echo $med["nombre"];?></label>
                    <input readonly type="number" class="form-control" id="ven_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>" value="0.00">
                </div>
                <?php endforeach;?>

                <div class='col-lg-12 col-xs-12'>
                    <hr/>
                </div>

                <div class="col-lg-12 col-xs-12" style="font-size: 22px;margin-top: 5px;margin-bottom: 15px;">
                    <span class="label label-danger">RESUMEN COMPRAS</span>
                </div>

                <div class="form-group col-lg-3">
                    <label>TOTAL COMPRADO</label>
                    <input readonly type="number" class="form-control" id="comprado" value="0.00">
                </div>

                <div class='col-lg-12 col-xs-12'>
                    <hr/>
                </div>

                <div class="col-lg-12 col-xs-12" style="font-size: 22px;margin-top: -5px;margin-bottom: 15px;">
                    <span class="label label-success">PROPINAS</span>
                </div>

                <?php foreach($medios as $med):?>
                <div class="form-group col-lg-3">
                    <label><?php echo $med["nombre"];?></label>
                    <input readonly type="number" class="form-control" id="prop_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>" value="0.00">
                </div>
                <?php endforeach;?>

                <div class='col-lg-12 col-xs-12'>
                    <hr/>
                </div>

                <div class="col-lg-12 col-xs-12" style="font-size: 22px;margin-top: -5px;margin-bottom: 15px;">
                    <span class="label label-success">MONTOS INICIALES</span>
                </div>

                <?php foreach($monedas as $mo):?>
                    <div class="form-group col-lg-3">
                        <label><?php echo $mo["nombre"];?> <?php echo $mo["simbolo"];?></label>
                        <input readonly type="number" class="form-control" id="ini_<?php echo $mo["id"];?>" value="0.00">
                    </div>
                <?php endforeach;?>

                <div class='col-lg-12 col-xs-12'>
                    <hr/>
                </div>

                <div class="col-lg-12 col-xs-12" style="font-size: 22px;margin-top: -5px;margin-bottom: 15px;">
                    <span class="label label-info">INGRESOS ADICIONALES</span>
                </div>

                <?php foreach($medios as $med):?>
                    <div class="form-group col-lg-3">
                        <label><?php echo $med["nombre"];?></label>
                        <input readonly type="number" class="form-control" id="ing_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>" value="0.00">
                    </div>
                <?php endforeach;?>

                <div class='col-lg-12 col-xs-12'>
                    <hr/>
                </div>

                <div class="col-lg-12 col-xs-12" style="font-size: 22px;margin-top: -5px;margin-bottom: 15px;">
                    <span class="label label-danger">SALIDAS</span>
                </div>

                <?php foreach($medios as $med):?>
                <div class="form-group col-lg-3">
                    <label><?php echo $med["nombre"];?></label>
                    <input readonly type="number" class="form-control" id="sal_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>" value="0.00">
                </div>
                <?php endforeach;?>

                <div class='col-lg-12 col-xs-12'>
                    <hr/>
                </div>

                <div class="col-lg-12 col-xs-12" style="font-size: 22px;margin-top: 5px;margin-bottom: 15px;">
                    <span class="label label-success">RESUMEN VENTAS</span>
                </div>

                <div class="form-group col-lg-3">
                    <label>TOTAL VENDIDO</label>
                    <input readonly type="number" class="form-control" id="vendido" value="0.00">
                </div>

                <div class="form-group col-lg-3">
                    <label>TOTAL CREDITOS</label>
                    <input readonly type="number" class="form-control" id="credito" value="0.00">
                </div>

                <div class="form-group col-lg-3">
                    <label>TOTAL CONSUMOS</label>
                    <input readonly type="number" class="form-control" id="consumo" value="0.00">
                </div>

                <div class="form-group col-lg-3">
                    <label>TOTAL DESCUENTOS</label>
                    <input readonly type="number" class="form-control" id="descuento" value="0.00">
                </div>

                <div class='col-lg-12 col-xs-12'>
                    <hr/>
                </div>

                <?php foreach($tipos_gastos as $tip):?>
                    <div class="col-lg-12 col-xs-12" style="font-size: 22px;margin-top: -5px;margin-bottom: 15px;">
                        <span class="label label-warning">RESUMEN <?php echo strtoupper($tip["nombre"]);?></span>
                    </div>

                    <?php foreach($medios as $med):?>
                        <div class="form-group col-lg-3">
                            <label><?php echo $med["nombre"];?></label>
                            <input readonly type="number" class="form-control" id="tip_<?php echo $tip["id"];?>_<?php echo $med["id_medio"];?>_<?php echo $med["id_moneda"]?>" value="0.00">
                        </div>
                    <?php endforeach;?>

                    <div class='col-lg-12 col-xs-12'>
                        <hr/>
                    </div>
                <?php endforeach;?>
            
                <div class="col-xs-12">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th colspan="4" class="text-center">Ventas por Crédito / Consumo</th>
                            </tr>
                            <tr>
                                <th>#</th>
                                <th>Cliente</th>
                                <th>Tipo</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody id="tbl_consumo_credito">
                        </tbody>
                    </table>
                </div>

            </div>
            <div role="tabpanel" class="tab-pane row" id="platos">
                <div class="col-lg-12 col-xs-12" style="font-size: 22px;margin-top: 15px;margin-bottom: 15px;">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th><center>Item</center></th>
                                <th><center>Cantidad</center></th>
                                <th><center>Cantidad por Cobrar</center></th>
                                <th><center>Total</center></th>
                            </tr>
                        </thead>
                        <tbody id="tbl_platos" style="text-align:center;">
                        </tbody>
                    </table>
                </div>
            </div>
            </div>
                
            </div>
            <div class="panel-footer" style="text-align:center;">
                <div class="form-group col-lg-4">
                    Corte Actual: <label id="lblUltimoCorte"></label>
                </div>
                <div class="form-group col-lg-4">
                    Cierre Actual: <label id="lblFechaCierre"></label> 
                </div>
                <div class="form-group col-lg-4">
                    Proximo Cierre: <label id="lblProximoCierre"></label>
                </div>
            </div>





            
            <div id="modalFormFE" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Registrar Ingreso a Fondos Externos</h4>
                        </div>
                        <div class="modal-body">

                            <form id="frmCajaFE">

                                <div class="row">
                                    <div class="col-xs-12 col-md-6">
                                        <!-- <div class="text-success text-center">
                                            Caja: <br><strong style="font-size: 20px">FE</strong>
                                        </div>
                                        <hr> -->
                                        <div class="form-group">
                                            <label for="">Caja</label>
                                            <input type="text" class="form-control" value="Fondos Externos" readonly>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-6">
                                        <div class="form-group">
                                            <label for="">Fecha Caja</label>
                                            <input type="date" class="form-control" id="feFechaCaja">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">

                                    <?php foreach($medios as $med):?>
                                    <div class="col-xs-12 col-md-4">
                                        <div class="form-group">
                                            <label for=""><?php echo $med["nombre"];?></label>
                                            <input type="number" name="totFE_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>" id="totFE_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>" class="form-control" value="0.00">
                                        </div>
                                    </div>
                                    <?php endforeach;?>
                                    
                                </div>

                                <div class="text-right">
                                    <button type="button" class="btn btn-primary" onclick="saveFondosExternos()">
                                        <i class="fa fa-save"></i>
                                        Registrar
                                    </button>
                                    <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cerrar</button>
                                </div>
                                
                            </form>
                        </div>
                        <!-- <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">Cerrar</button>
                        </div> -->
                    </div>
                </div>
            </div> 

        </div>



        <script src="reportes/recursos/js/jspdf.js"></script>
        <script src="reportes/recursos/js/html2canvas.js"></script>
        <script>


            function generaPDF2019(){
                var HTML_Width = $("#panel_data").width();
                var HTML_Height = $("#panel_data").height();
                var top_left_margin = 5;
                var PDF_Width = HTML_Width+(top_left_margin*2);
                var PDF_Height = (PDF_Width*1.5)+(top_left_margin*2);
                var canvas_image_width = HTML_Width;
                var canvas_image_height = HTML_Height;

                var totalPDFPages = Math.ceil(HTML_Height/PDF_Height)-1;


                html2canvas($("#panel_data")[0],{allowTaint:true}).then(function(canvas) {
                    canvas.getContext('2d');
                    
                    console.log(canvas.height+"  "+canvas.width);
                    
                    
                    var imgData = canvas.toDataURL("image/jpeg", 1.0);
                    var pdf = new jsPDF('p', 'pt',  [PDF_Width, PDF_Height]);
                    pdf.addImage(imgData, 'JPG', top_left_margin, top_left_margin,canvas_image_width,canvas_image_height);
                    
                    
                    for (var i = 1; i <= totalPDFPages; i++) { 
                        pdf.addPage(PDF_Width, PDF_Height);
                        pdf.addImage(imgData, 'JPG', top_left_margin, -(PDF_Height*i)+(top_left_margin*4),canvas_image_width,canvas_image_height);
                    }
                    
                    pdf.save("cierre_de_caja.pdf");
                });
            };



            carga_cortes();
            cargandoFechasProximas();
            date('date');
            
            function carga_cortes(){
                var caja = $("#txtCajaAct option:selected").val();
                if(caja === ""){
                    $("#divcorte").hide(0);
                    $("#btnCut").hide(0);
                    loadTotalDiario();
                }else{
                    $("#divcorte").show(0);
                    $("#btnCut").show(0);

                    var param = {'fecha': $('#txtFechaCierreDiario').val(), 'caja': caja};
                    $.ajax({
                        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Caja&&action=ListCortesDia",
                        type: 'POST',
                        data: param,
                        cache: true,
                        dataType: 'json',
                        success: function(data) {
                            if(data != ""){
                                var txt = "";
                                $.each(data, function( key, value ) {
                                    txt += "<option value='"+value+"'>"+value+"</option>";
                                });
                                //txt += "<option value='TODO'>Todo el Día</option>";
                                $("#txtCorteAct").html(txt);
                                carga_data_corte();
                            }else{
                                alert("No Hubo actividad ese día");
                                var corte_actual = $("#txtCorteAct option:selected").val();
                                var dia = corte_actual.split(" ");
                                $('#txtFechaCierreDiario').val(dia[0]);
                            }
                        }
                    });
                }
            }
            
            function carga_data_corte(){
                var corte = $("#txtCorteAct option:selected").val();
                var caja = $("#txtCajaAct option:selected").val();

                var param = {'fecha': $('#txtFechaCierreDiario').val(), 'corte': corte, 'caja': caja};
                $.ajax({
                    url: "<?php echo Class_config::get('urlApp') ?>/?controller=Caja&&action=ListTotalDiaxCorte",
                    type: 'POST',
                    data: param,
                    cache: true,
                    dataType: 'json',
                    success: function(data) {
                        $("#vendido").val(parseFloat(data.vendido).toFixed(2));
                        $("#credito").val(parseFloat(data.credito).toFixed(2));
                        $("#consumo").val(parseFloat(data.consumo).toFixed(2));
                        $("#descuento").val(parseFloat(data.descuento).toFixed(2));
                        $("#comprado").val(parseFloat(data.comprado).toFixed(2));
                        $("#tot_detraccion").val(parseFloat(data.tot_detraccion).toFixed(2));
                        <?php foreach($monedas as $mo):?>
                        $("#ini_<?php echo $mo["id"]; ?>").val(parseFloat(data.ini_<?php echo $mo["id"]; ?>).toFixed(2));   
                        <?php endforeach;?>
                        <?php foreach($tipos_gastos as $tip):?>
                            <?php foreach($medios as $med):?>
                                $("#tip_<?php echo $tip["id"]; ?>_<?php echo $med["id_medio"]; ?>_<?php echo $med["id_moneda"]; ?>").val(parseFloat(data.tip_<?php echo $tip["id"]; ?>_<?php echo $med["id_medio"]; ?>_<?php echo $med["id_moneda"];?>).toFixed(2));
                            <?php endforeach;?> 
                        <?php endforeach;?>
                        <?php foreach($medios as $med):?>
                            $("#tot_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>").val(parseFloat(data.tot_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>).toFixed(2));                
                        <?php endforeach;?>
                        <?php foreach($medios as $med):?>
                            $("#totFE_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>").val(parseFloat(data.tot_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>).toFixed(2));                
                        <?php endforeach;?>
                        <?php foreach($medios as $med):?>
                            $("#prop_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>").val(parseFloat(data.prop_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>).toFixed(2));                
                        <?php endforeach;?>
                        <?php foreach($medios as $med):?>
                            $("#ven_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>").val(parseFloat(data.ven_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>).toFixed(2));                
                        <?php endforeach;?>
                        <?php foreach($medios as $med):?>
                            $("#ing_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>").val(parseFloat(data.ing_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>).toFixed(2));                
                        <?php endforeach;?>
                        <?php foreach($medios as $med):?>
                            $("#sal_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>").val(parseFloat(data.sal_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>).toFixed(2));                
                        <?php endforeach;?>
                        var html_platos = "";
                        $.each( data.platos, function( key, value ) {
                            html_platos += "<tr><td>"+value.descripcion+"</td><td>"+value.salidas+"</td><td>"+value.cobrar+"</td><td>"+value.total+"</td></tr>";
                        }); 
                        $("#tbl_platos").html(html_platos);                    
                        var html_consumo_credito = "";
                        $.each( data.consumo_credito, function( key, value ) {
                            html_consumo_credito += "<tr><td>"+value.id+"</td><td>"+value.cliente+"</td><td>"+value.tipo+"</td><td class='text-right'>"+(value.total)+"</td></tr>";
                        }); 
                        $("#tbl_consumo_credito").html(html_consumo_credito);                    
                    }
                });
            }

            function loadTotalDiario() {
                var param = {'fecha': $('#txtFechaCierreDiario').val()};
                $.ajax({
                    url: "<?php echo Class_config::get('urlApp') ?>/?controller=Caja&&action=ListTotalDia",
                    type: 'POST',
                    data: param,
                    cache: true,
                    dataType: 'json',
                    success: function(data) {
                        $("#vendido").val(parseFloat(data.vendido).toFixed(2));
                        $("#credito").val(parseFloat(data.credito).toFixed(2));
                        $("#consumo").val(parseFloat(data.consumo).toFixed(2));
                        $("#descuento").val(parseFloat(data.descuento).toFixed(2));
                        $("#comprado").val(parseFloat(data.comprado).toFixed(2));
                        $("#tot_detraccion").val(parseFloat(data.tot_detraccion).toFixed(2));
                        <?php foreach($monedas as $mo):?>
                        $("#ini_<?php echo $mo["id"]; ?>").val(parseFloat(data.ini_<?php echo $mo["id"]; ?>).toFixed(2));   
                        <?php endforeach;?>
                        <?php foreach($tipos_gastos as $tip):?>
                            <?php foreach($medios as $med):?>
                                $("#tip_<?php echo $tip["id"]; ?>_<?php echo $med["id_medio"]; ?>_<?php echo $med["id_moneda"]; ?>").val(parseFloat(data.tip_<?php echo $tip["id"]; ?>_<?php echo $med["id_medio"]; ?>_<?php echo $med["id_moneda"];?>).toFixed(2));
                            <?php endforeach;?> 
                        <?php endforeach;?>
                        <?php foreach($medios as $med):?>
                        $("#tot_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>").val(parseFloat(data.tot_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>).toFixed(2));                
                        <?php endforeach;?>
                        <?php foreach($medios as $med):?>
                        $("#totFE_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>").val(parseFloat(data.tot_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>).toFixed(2));                
                        <?php endforeach;?>
                        <?php foreach($medios as $med):?>
                        $("#prop_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>").val(parseFloat(data.prop_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>).toFixed(2));                
                        <?php endforeach;?>
                        <?php foreach($medios as $med):?>
                        $("#ven_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>").val(parseFloat(data.ven_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>).toFixed(2));                
                        <?php endforeach;?>
                        <?php foreach($medios as $med):?>
                        $("#ing_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>").val(parseFloat(data.ing_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>).toFixed(2));                
                        <?php endforeach;?>
                        <?php foreach($medios as $med):?>
                        $("#sal_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>").val(parseFloat(data.sal_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>).toFixed(2));                
                        <?php endforeach;?>
                        var html_platos = "";
                        $.each( data.platos, function( key, value ) {
                            html_platos += "<tr><td>"+value.descripcion+"</td><td>"+value.salidas+"</td><td>"+value.cobrar+"</td><td>"+value.total+"</td></tr>";
                        }); 
                        $("#tbl_platos").html(html_platos);   
                        var html_consumo_credito = "";
                        $.each( data.consumo_credito, function( key, value ) {
                            html_consumo_credito += "<tr><td>"+value.id+"</td><td>"+value.cliente+"</td><td>"+value.tipo+"</td><td class='text-right'>"+(value.total)+"</td></tr>";
                        }); 
                        $("#tbl_consumo_credito").html(html_consumo_credito); 
                    }
                });
            }
            
            function cargandoFechasProximas() {
                $.ajax({
                    url: "<?php echo Class_config::get('urlApp') ?>/?controller=Caja&&action=ListFechasCierre",
                    type: 'POST',
                    cache: true,
                    dataType: 'json',
                    success: function(data) {
                        $("#lblFechaCierre").html(data[0].actual);
                        $("#lblProximoCierre").html(data[0].proximo);
                        $("#lblUltimoCorte").html(data[0].corte);
                    }
                });
            }
            
            function corte() {
                if(confirm("Se hara un corte. ¿Deseas Continuar?")){
                    //Imprimimos corte actual
                    var caja = $("#txtCajaAct option:selected").val();
                    var corte = $("#txtCorteAct option:selected").val();
                    var inicial = 1;
                    
                    if(caja === ""){
                        corte = "";
                        inicial = 0;
                    }
                    
                    var param = {
                        'fecha': $('#txtFechaCierreDiario').val(),
                        'terminal': '<?php echo $_COOKIE["t"];?>',
                        'cajero': '<?php echo UserLogin::get_id(); ?>',
                        'corte' : corte,
                        'inicial' : inicial,
                        'caja' : caja,
                        'tipo': 2
                    };
                    $.ajax({
                        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Caja&&action=ImprimeCierre",
                            type: 'POST',
                            data: param,
                            cache: false,
                            dataType: 'json',
                            success: function() {
                                //Hacemos corte en si
                                $.ajax({
                                    url: "<?php echo Class_config::get('urlApp') ?>/?controller=Caja&&action=HacerCorte&&caja="+caja,
                                    type: 'POST',
                                    cache: true,
                                    dataType: 'json',
                                    success: function(data) {
                                        location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Caja&&action=MontoInicial";
                                    }
                                });
                            }
                    });
                }
            }

            function openImprimirCierreCuentaDiario() {
                var caja = $("#txtCajaAct option:selected").val();
                var corte = $("#txtCorteAct option:selected").val();
                var inicial = 1;
                
                if(caja === ""){
                    corte = "";
                    inicial = 0;
                }
                
                var param = {
                    'fecha': $('#txtFechaCierreDiario').val(),
                    'terminal': '<?php echo $_COOKIE["t"];?>',
                    'cajero': '<?php echo UserLogin::get_id(); ?>',
                    'corte' : corte,
                    'inicial' : inicial,
                    'caja' : caja,
                    'tipo': 1
                };
                $.ajax({
                    url: "<?php echo Class_config::get('urlApp') ?>/?controller=Caja&&action=ImprimeCierre",
                        type: 'POST',
                        data: param,
                        cache: false,
                        dataType: 'json',
                        success: function() {
                            ///Nada
                        }
                });
            }

            function enviaMail2019(){
                var caja = $("#txtCajaAct option:selected").val();
                var corte = $("#txtCorteAct option:selected").val();
                var inicial = 1;
                
                if(caja === ""){
                    corte = "";
                    inicial = 0;
                }
                
                var param = {
                    'fecha': $('#txtFechaCierreDiario').val(),
                    'corte' : corte,
                    'inicial' : inicial,
                    'caja' : caja,
                    'tipo' : 1
                };
                $.ajax({
                    url: "<?php echo Class_config::get('urlApp') ?>/?controller=Caja&&action=CorreoCierre",
                        type: 'POST',
                        data: param,
                        cache: false,
                        dataType: 'json',
                        success: function() {
                            ///Nada
                        }
                });                
            }

            function wincha() {
                var caja = $("#txtCajaAct option:selected").val();
                var corte = $("#txtCorteAct option:selected").val();
                var inicial = 1;
                
                if(caja === ""){
                    corte = "";
                    inicial = 0;
                }
                
                var param = {
                    'fecha': $('#txtFechaCierreDiario').val(),
                    'terminal': '<?php echo $_COOKIE["t"];?>',
                    'cajero': '<?php echo UserLogin::get_id(); ?>',
                    'corte' : corte,
                    'inicial' : inicial,
                    'caja' : caja,
                    'tipo': 1
                };
                $.ajax({
                    url: "<?php echo Class_config::get('urlApp') ?>/?controller=Caja&&action=ImprimeWincha",
                        type: 'POST',
                        data: param,
                        cache: false,
                        dataType: 'json',
                        success: function() {
                            ///Nada
                        }
                });
            }

            function sendFondosExternos () {

                $('#modalFormFE').modal('show');

                $('#feFechaCaja').val($("#txtFechaCierreDiario").val())
            }

            function saveFondosExternos () {

                console.log($('#frmCajaFE').serialize())

                let param = $('#frmCajaFE').serialize();

                let fecha = $("#feFechaCaja").val();

                let fechaDia = $("#txtFechaCierreDiario").val()

                let caja = $("#txtCajaAct option:selected").val();

                param += "&fecha=" + fecha;
                param += "&fechaDia=" + fechaDia;
                param += "&caja=" + caja;


                $.ajax({
                    url: "<?php echo Class_config::get('urlApp') ?>/?controller=FondosExternos&&action=Save",
                    type: 'POST',
                    data: param,
                    cache: false,
                    dataType: 'json',
                    success: function(data) {
                        ///Nada
                        if (data.ok) {
                            alert('Ingreso a Fondo Externo registrado correctamente.')
                            location.reload();
                        } else {
                            alert('Ocurrió un inconveniente, comunicarse a soporte.')
                        }
                    }
                });
            }
            
        </script>