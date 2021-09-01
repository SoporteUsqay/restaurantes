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

                <div class="form-group col-md-3">
                    <label for="">Fecha</label>
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                        <input name="fecha" aria-describedby="basic-addon1" class="form-control date" id="txtFechaCierreDiario" value="<?php echo $fecha ?>" onchange="carga_cortes()">
                    </div>
                </div>

                <div class="form-group col-md-3" <?php echo $estilo_nivel;?>>
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

                <div class="form-group col-md-3" id="divcorte">
                    <label for="">Corte</label>
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1">
                            <span class="glyphicon glyphicon-flash"></span>
                        </span>
                        <select aria-describedby="basic-addon1" name="fecha" class="form-control" id="txtCorteAct" onchange="carga_data_corte()">
                        </select>
                    </div>
                </div>

                <div class="form-group col-md-3" style="text-align:center;">
                        <a onclick="openImprimirCierreCuentaDiario()" class="btn btn-primary" id="btnImprimirTicket" style='margin-right:5px;margin-top:5px;'><span class='glyphicon glyphicon-print' aria-hidden='true'></span> Imprimir</a>

                        <a onclick="corte()" class="btn btn-primary" id="btnCut" style='margin-right:5px;margin-top:5px;'><span class="glyphicon glyphicon-flash" aria-hidden="true"></span> Corte</a>

                        <a onclick="generaPDF2019()" class="btn btn-primary" id="btnPDF" style='margin-right:5px;margin-top:5px;'><span class="glyphicon glyphicon-save" aria-hidden="true"></span> PDF</a>

                        <a onclick="enviaMail2019()" class="btn btn-primary" id="btnEmail" style='margin-right:5px;margin-top:5px;'><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> E-Mail</a>
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
            <div class="panel-body row">
                
                <div class="alert alert-success alert-dismissible" role="alert" style="display: none;" id="exito">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  Operación Realizada con Éxito
                </div>

                <div class="alert alert-danger alert-dismissible" role="alert" style="display: none;" id="fracaso">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  Hubo un error, reintenta
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

                <div class="col-lg-12 col-xs-12" style="font-size: 22px;margin-top: -5px;margin-bottom: 15px;">
                    <span class="label label-success">TOTALES</span>
                </div>

                <?php foreach($medios as $med):?>
                <div class="form-group col-lg-3">
                    <label><?php echo $med["nombre"];?></label>
                    <input readonly type="number" class="form-control" id="tot_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>" value="0.00">
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

                <div class="col-lg-12 col-xs-12" style="font-size: 22px;margin-top: -5px;margin-bottom: 15px;">
                    <span class="label label-success">RESUMEN INGRESOS Y SALIDAS ADICIONALES</span>
                </div>

                <?php foreach($tipos_gastos as $tip):?>
                    <?php foreach($monedas as $mo):?>
                        <div class="form-group col-lg-3">
                            <label><?php echo $tip["nombre"];?> <?php echo $mo["simbolo"];?></label>
                            <input readonly type="number" class="form-control" id="tip_<?php echo $tip["id"];?>_<?php echo $mo["id"];?>" value="0.00">
                        </div>
                    <?php endforeach;?>
                <?php endforeach;?>

                <div class='col-lg-12 col-xs-12'>
                    <hr/>
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
                        <?php foreach($monedas as $mo):?>
                        $("#ini_<?php echo $mo["id"]; ?>").val(parseFloat(data.ini_<?php echo $mo["id"]; ?>).toFixed(2));   
                        <?php endforeach;?>
                        <?php foreach($tipos_gastos as $tip):?>
                            <?php foreach($monedas as $mo):?>
                                $("#tip_<?php echo $tip["id"]; ?>_<?php echo $mo["id"]; ?>").val(parseFloat(data.tip_<?php echo $tip["id"]; ?>_<?php echo $mo["id"]; ?>).toFixed(2));
                            <?php endforeach;?> 
                        <?php endforeach;?>
                        <?php foreach($medios as $med):?>
                            $("#tot_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>").val(parseFloat(data.tot_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>).toFixed(2));                
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
                        <?php foreach($monedas as $mo):?>
                        $("#ini_<?php echo $mo["id"]; ?>").val(parseFloat(data.ini_<?php echo $mo["id"]; ?>).toFixed(2));   
                        <?php endforeach;?>
                        <?php foreach($tipos_gastos as $tip):?>
                            <?php foreach($monedas as $mo):?>
                                $("#tip_<?php echo $tip["id"]; ?>_<?php echo $mo["id"]; ?>").val(parseFloat(data.tip_<?php echo $tip["id"]; ?>_<?php echo $mo["id"]; ?>).toFixed(2));
                            <?php endforeach;?> 
                        <?php endforeach;?>
                        <?php foreach($medios as $med):?>
                        $("#tot_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>").val(parseFloat(data.tot_<?php echo $med["id_medio"]."_".$med["id_moneda"] ?>).toFixed(2));                
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
                        'caja' : caja
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
                    'caja' : caja
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
            
        </script>