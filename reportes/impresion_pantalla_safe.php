<?php
$titulo_pagina = 'Pedidos a la órden';
$titulo_sistema = 'Usqay2';

include_once('recursos/componentes/MasterConexion.php');
$conn = new MasterConexion();

//Cookie para saber tipo de trabajador
//echo $_COOKIE["TYP"];

$salones = $conn->consulta_matriz("Select * from salon");
require_once('recursos/componentes/header.php');
?>

<link rel="stylesheet" href="recursos/js/plugins/tablas/jquery.dataTables.min.css">
</form>
</div>
</div>
</div>

<style>
    body {
        background-color: #fff;
    }

    .btn {
        box-shadow: none;
    }

    .btn:hover,
    .btn:focus {
        box-shadow: none;
    }

    .border-none {
        border: none;
    }

    .bg-success {
        background-color: #02b875 !important;
    }

    .bg-secondary {
        background-color: #adb5bd !important;
    }

    .bg-info {
        background-color: #17a2b8 !important;
    }

    .bg-warning {
        background-color: #f0ad4e !important;
    }

    .bg-danger {
        background-color: #d9534f !important;
    }

    .bg-light {
        background-color: #f8f9fa !important;
    }

    .text-success {
        color: #02b875 !important;
    }

    .text-secondary {
        color: #adb5bd !important;
    }

    .text-info {
        color: #17a2b8 !important;
    }

    .text-warning {
        color: #f0ad4e !important;
    }

    .text-danger {
        color: #d9534f !important;
    }

    .text-light {
        color: #f8f9fa !important;
    }

    .outline-success {
        border: solid 1px #02b875 !important;
    }

    .outline-secondary {
        border: solid 1px #adb5bd !important;
    }

    .outline-info {
        border: solid 1px #17a2b8 !important;
    }

    .outline-warning {
        border: solid 1px #f0ad4e !important;
    }

    .outline-danger {
        border: solid 1px #d9534f !important;
    }

    .outline-light {
        border: solid 1px #f8f9fa !important;
    }
</style>

<nav class="navbar navbar-default" role="navigation" style="margin-top:-100px;">
    <div class="container-fluid" style="height:100%;">
        <div class="row">
            <div class="col-xs-12">
                <form class="navbar-form navbar-left text-center">
                    <div style="margin-bottom:.5rem;" class="input-group">
                        <span class="input-group-addon"> <i class="glyphicon glyphicon-home"></i></span>
                        <select id="salon" name="salon" class="form-control">
                            <option value="0">TODOS LOS SALONES</option>
                        <?php 
                            foreach($salones as $dt){
                                echo('<option value="'.$dt['pkSalon'].'">'.$dt['nombre'].'</option>');
                            }
                        ?>
                        </select>
                    </div>
                    <div style="margin-bottom:.5rem;" class="btn-group">
                        <button id="btn_vista_normal" type="button" class="btn btn-primary"><i class="glyphicon glyphicon-list"></i></button>
                        <button id="btn_vista_mesas" type="button" class="btn btn-default"><i class="glyphicon glyphicon-th-large"></i></button>
                    </div>
                    <div style="margin-bottom:.5rem;" class="btn-group">
                        <button type="button" class="btn btn-default" title="Historial" data-toggle="modal" data-target="#historialModal"><i class="glyphicon glyphicon-list-alt"></i> Historial</button>
                        <button type="button" class="btn btn-default" title="Configuración" data-toggle="modal" data-target="#configuracionesModal"><i class="glyphicon glyphicon-cog"></i> Configuración</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</nav>
<div class="container">
    <!-- <h3>PEDIDOS <span class="text-uppercase text-muted" style="font-size: 15px; display:none;">prom. de despacho: <span id="tiempo_promedio_despacho"></span> min</span></h3> -->
    <div style="overflow:auto;">
        <div id="pedidos" class="row" style="padding:15px; height:70vh;">

        </div>
    </div>
</div>


<!-- Modal Configuraciones -->
<div class="modal fade" id="configuracionesModal" tabindex="-1" role="dialog" aria-labelledby="configuracionesLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="configuracionesLabel"><span class="glyphicon glyphicon-cog"></span> Configuraciones</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <ul id="myTab" class="nav nav-tabs">
                            <li class="active"><a href="#tipos_tab" data-toggle="tab">Tipos</a></li>
                            <li><a href="#promdespacho_tab" data-toggle="tab">Prom. despacho</a></li>
                            <li><a href="#tiempos_tab" data-toggle="tab">Tiempos</a></li>
                            <li><a href="#impresion_tab" data-toggle="tab">Impresion</a></li>
                            <li><a href="#historial_tab" data-toggle="tab">Historial</a></li>
                        </ul>
                        <div id="myTabContent" class="tab-content">
                            <div class="tab-pane fade in active" id="tipos_tab">
                                <h5 class="text-uppercase text-bold"><b>Tipos para mostrar:</b></h5>
                                <div id="tipos">

                                </div>
                            </div>
                            <div class="tab-pane fade" id="promdespacho_tab">
                                <h5 class="text-uppercase text-bold"><b>Promedio de despacho:</b></h5>
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1">Ultimas</span>
                                    <input type="number" step="1" min="1" class="form-control" id="numero_ultimas_despachadas_input">
                                </div>
                            </div>
                            <div class="tab-pane fade col-xs-12" id="tiempos_tab">
                                <h5 class="text-uppercase text-bold"><b>Tiempos en minutos:</b></h5>
                                <form class="row">
                                    <div class="form-group">
                                        <label for="verde_inicio">Verde desde</label>
                                        <input type="number" step="1" min="0" class="form-control" id="verde_inicio">
                                    </div>
                                    <div class="form-group">
                                        <label for="verde_fin">Verde hasta</label>
                                        <input type="number" step="1" min="0" class="form-control" id="verde_fin">
                                    </div>
                                    <div class="form-group">
                                        <label for="naranja_inicio">Naranja desde</label>
                                        <input type="number" step="1" min="0" class="form-control" id="naranja_inicio">
                                    </div>
                                    <div class="form-group">
                                        <label for="naranja_fin">Naranja hasta</label>
                                        <input type="number" step="1" min="0" class="form-control" id="naranja_fin">
                                    </div>
                                    <div class="form-group">
                                        <label for="rojo_inicio">Rojo desde</label>
                                        <input type="number" step="1" min="0" class="form-control" id="rojo_inicio">
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade col-xs-12" id="impresion_tab">
                                <h5 class="text-uppercase text-bold"><b>¿Imprimir?</b></h5>
                                <form class="row">
                                    <div class="col-xs-12">
                                        Imprimir Entregas <input type="checkbox" id="check_entrega"
                                        <?php if(isset($_COOKIE["impresion_pantalla"])){
                                            echo "checked";                                                
                                        }?> onchange="actualiza_entrega();">
                                    </div>
                                    <div class="col-xs-12">
                                        Imprimir Anulaciones <input type="checkbox" id="check_anulacion"
                                        <?php if(isset($_COOKIE["anulacion_pantalla"])){
                                            echo "checked";                                                
                                        }?> onchange="actualiza_anulacion();">
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="historial_tab">
                                <h5 class="text-uppercase text-bold"><b>Historial de pedidos del día:</b></h5>
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1">N° Items</span>
                                    <input type="number" step="1" min="1" class="form-control" id="numItemsHistorial">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" style="box-shadow:none;" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" style="box-shadow:none;" id="btn-guardar-config">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Historial -->
<div class="modal fade" id="historialModal" tabindex="-1" role="dialog" aria-labelledby="historialLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="historialLabel"><span class="glyphicon glyphicon-list-alt"></span> Historial de pedidos del día</h4>
            </div>
            <div class="modal-body" style="overflow:scroll; max-height: 70vh;">
                <div id="historialPedidos" class="row">
                    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" disabled id="pagPrevHistorial" class="btn btn-primary"><span class="glyphicon glyphicon-backward"></span> Anterior</button>
                <button type="button" id="pagNextHistorial" class="btn btn-primary"><span class="glyphicon glyphicon-forward"></span> Siguiente</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="recursos/js/jquery.js"></script>
<script src="recursos/js/jquery-ui.js"></script>
<script src="recursos/js/bootstrap.min.js"></script>
<script src="recursos/js/offcanvas.js"></script>
<script src="../Public/select2/js/select2.js"></script>
<script src="recursos/btable/bootstrap-table.min.js"></script>
<script src="recursos/btable/bootstrap-table-group-by.js"></script>
<script src="recursos/js/plugins/tablas/jquery.dataTables.min.js"></script>
<script>
    history.pushState(null, "", 'impresion_pantalla.php');

    $(document).ready(function() {

        $("body").attr("style", "background-color: #e2e2e2");

        $('<audio id="notificacionAudio"><source src="notify.ogg" type="audio/ogg"><source src="notify.mp3" type="audio/mpeg"><source src="notify.wav" type="audio/wav"></audio>').appendTo('body');


        if(<?php echo $_COOKIE["TYP"]?> != 1){
            $("#configuraciones").css("display", "none");
        }

        if (localStorage.getItem("pedidos_actuales") == null) {
            localStorage.setItem("pedidos_actuales", "[]");
        }
        if (localStorage.getItem("mesas") == null) {
            localStorage.setItem("mesas", "[]");
        }
        if (localStorage.getItem("pedidos_anulados") == null) {
            localStorage.setItem("pedidos_anulados", "[]");
        }
        if (localStorage.getItem("ultimas_despachadas") == null) {
            localStorage.setItem("ultimas_despachadas", "[]");
        }
        if (localStorage.getItem("opcion_vista") == null) {
            localStorage.setItem("opcion_vista", "general");
        }

        if (localStorage.getItem("numero_ultimas_despachadas") == null) {
            localStorage.setItem("numero_ultimas_despachadas", 10);
        }
        if (localStorage.getItem("tiempo_verde") == null) {
            localStorage.setItem("tiempo_verde", "[0,10]");
        }
        if (localStorage.getItem("tiempo_naranja") == null) {
            localStorage.setItem("tiempo_naranja", "[10,15]");
        }
        if (localStorage.getItem("tiempo_rojo") == null) {
            localStorage.setItem("tiempo_rojo", "[15]");
        }
        if (localStorage.getItem("numItemsHistorial") == null) {
            localStorage.setItem("numItemsHistorial", 30);
        }
        localStorage.setItem("historialInicio", 0);
        localStorage.setItem("historialFin", 30);

        $("#numero_ultimas_despachadas_input").val(localStorage.getItem("numero_ultimas_despachadas"));
        $("#numItemsHistorial").val(localStorage.getItem("numItemsHistorial"));

        let tiempo_verde = jQuery.parseJSON(localStorage.getItem("tiempo_verde"));
        let tiempo_naranja = jQuery.parseJSON(localStorage.getItem("tiempo_naranja"));
        let tiempo_rojo = jQuery.parseJSON(localStorage.getItem("tiempo_rojo"));

        $("#verde_inicio").val(tiempo_verde[0]);
        $("#verde_fin").val(tiempo_verde[1]);
        $("#naranja_inicio").val(tiempo_naranja[0]);
        $("#naranja_fin").val(tiempo_naranja[1]);
        $("#rojo_inicio").val(tiempo_rojo[0]);

        cargarTipos();

        setInterval(cargarTipos, 7000);

        $("#btn_vista_normal").click(function(){            
            localStorage.setItem("opcion_vista", "por_mesa");
            $(this).removeClass('btn-default').addClass('btn-primary');            
            $("#btn_vista_mesas").removeClass('btn-primary').addClass('btn-default');
            cargarTipos();
        })

        $("#btn_vista_mesas").click(function(){            
            localStorage.setItem("opcion_vista", "general");
            $(this).removeClass('btn-default').addClass('btn-primary');
            $("#btn_vista_normal").removeClass('btn-primary').addClass('btn-default');
            cargarTipos();
        })
        $("#numero_ultimas_despachadas_input").change(function(e) {
            e.preventDefault();
            if ($(this).val() > 1) {
                localStorage.setItem("numero_ultimas_despachadas", $(this).val());
            }
        });

        $("#numItemsHistorial").change(function(e) {
            e.preventDefault();
            if ($(this).val() > 1) {
                localStorage.setItem("numItemsHistorial", $(this).val());
            }
        });

        $("#verde_inicio").change(function(e){
            e.preventDefault();
            if($(this).val() > 0){
                tiempo_verde[0] = parseInt($(this).val());
            }
        });
        $("#verde_fin").change(function(e){
            e.preventDefault();
            if($(this).val() > 0){
                tiempo_verde[1] = parseInt($(this).val());
            }
        });

        $("#naranja_inicio").change(function(e){
            e.preventDefault();
            if($(this).val() > 0){
                tiempo_naranja[0] =parseInt( $(this).val());
            }
        });

        $("#naranja_fin").change(function(e){
            e.preventDefault();
            if($(this).val() > 0){
                tiempo_naranja[1] = parseInt($(this).val());
            }
        });

        $("#rojo_inicio").change(function(e){
            e.preventDefault();
            if($(this).val() > 0){
                tiempo_rojo[0] = parseInt($(this).val());
            }
        });

        $("#btn-guardar-config").click(function(){
            localStorage.setItem("tiempo_verde", JSON.stringify(tiempo_verde));
            localStorage.setItem("tiempo_naranja", JSON.stringify(tiempo_naranja));
            localStorage.setItem("tiempo_rojo", JSON.stringify(tiempo_rojo));
            $("#configuracionesModal").modal("hide");
        });

        $('#historialModal').on('show.bs.modal', function (e) {
            $("#pagPrevHistorial").attr("disabled");
            $("#pagNextHistorial").removeAttr("disabled");
            localStorage.setItem("historialInicio", 0);
            cargarHistorial();
        })

        $("#pagNextHistorial").click(function(){
            let historialInicio = parseInt(localStorage.getItem("historialInicio"));
            let numItems = parseInt(localStorage.getItem("numItemsHistorial"));
            
            $("#pagPrevHistorial").removeAttr("disabled");

            localStorage.setItem("historialInicio", historialInicio+numItems);
            cargarHistorial();
        });

        $("#pagPrevHistorial").click(function(){
            let historialInicio = parseInt(localStorage.getItem("historialInicio"));
            let numItems = parseInt(localStorage.getItem("numItemsHistorial"));

            $("#pagNextHistorial").removeAttr("disabled");

            if(historialInicio > 0){
                localStorage.setItem("historialInicio", (historialInicio-numItems));
            }else{
                $(this).attr("disabled", "true");
            }
            cargarHistorial();
        });


    });

    function cargarTipos() {

        actualizarTiempoPromedio();

        $.ajax({
            url: window.location.origin + "/usqay/?controller=Pedidos&&action=obtieneTipoPlatoPantalla",
            method: 'POST',
            success: function(data) {
                let tipos_seleccionados = jQuery.parseJSON(localStorage.getItem("tipos_seleccionados"));
                result = jQuery.parseJSON(data);
                let tempHtml = "";

                result.forEach(tipo => {

                    let checked = false;

                    if (tipos_seleccionados != null) {
                        tipos_seleccionados.forEach(t => {
                            if (t[1] == tipo["id"]) {
                                checked = true;
                            }
                        });
                    } else {
                        localStorage.setItem("tipos_seleccionados", "[]");
                    }

                    if (checked) {
                        tempHtml += "<div class='checkbox'><label><input type='checkbox' checked onclick='verificarTiposSeleccionados()' id='checkTipo_" + tipo["id"] + "' value='" + tipo["id"] + "'>" + tipo["descripcion"] + "</label></div>";
                    } else {
                        tempHtml += "<div class='checkbox'><label><input type='checkbox' onclick='verificarTiposSeleccionados()' id='checkTipo_" + tipo["id"] + "' value='" + tipo["id"] + "'>" + tipo["descripcion"] + "</label></div>";
                    }
                });
                $("#tipos").html(tempHtml);
                cargarPedidos();
            },
            error: function(error) {
                console.error(JSON.stringify(error))
            }
        });
    }

    function verificarTiposSeleccionados() {
        let selected = [];
        $('#tipos input:checked').each(function() {
            selected.push([$(this).attr('id'), $(this).val()]);
        });
        let tipos_stringify = JSON.stringify(selected);
        localStorage.setItem("tipos_seleccionados", tipos_stringify);
    }

    function cargarPedidos() {

        let tipos_seleccionados = jQuery.parseJSON(localStorage.getItem("tipos_seleccionados"));

        //VARIABLES PARA AJAX    
        let tipos = "";

        tipos_seleccionados.forEach(t => {
            tipos += t[1] + ",";
        });

        (tipos.length > 0) ? tipos = tipos.slice(0, -1): tipos = null;

        $.ajax({
            url: window.location.origin + "/usqay/?controller=Pedidos&&action=obtienePedidosPantalla",
            data: {
                tipos: tipos
            },
            method: 'POST',
            success: function(data) {

                let pedidos_actuales = jQuery.parseJSON(localStorage.getItem("pedidos_actuales"));
                let mesas = jQuery.parseJSON(localStorage.getItem("mesas"));

                pedidos_activos = jQuery.parseJSON(data);

                
                pedidos_activos.forEach(pedido => {
                    let i = pedidos_actuales.findIndex(x => x.id === pedido["id"]);
                    if (i == -1) {
                        if(pedido["tipo_trabajador"] == 1){
                            pedidos_actuales.unshift(pedido);
                        }else{
                            pedidos_actuales.push(pedido);
                        }
                        $('#notificacionAudio')[0].play();
                    }else{
                        if(pedidos_actuales[i]["mensaje"] != pedido.mensaje){
                            pedidos_actuales[i]["msj"] = 1;
                        }else{
                            pedidos_actuales[i]["msj"] = 0;
                        }
                        pedidos_actuales[i]["mensaje"] = pedido.mensaje;
                        pedidos_actuales[i]["personas"] = pedido.personas;
                        pedidos_actuales[i]["mesa"] = pedido.mesa;
                        // console.log(pedido.personas);
                        // console.log(pedidos_actuales[i]);
                    }

                    if (mesas.findIndex(x => x.mesa === pedido["salon"]+" - "+pedido["mesa"]) == -1) {
                        mesas.push({mesa : pedido["salon"]+" - "+pedido["mesa"], salon: pedido['pkSalon']});
                    }
                });

                localStorage.setItem("pedidos_actuales", JSON.stringify(pedidos_actuales));
                localStorage.setItem("mesas", JSON.stringify(mesas));

                if(localStorage.getItem("opcion_vista") == "general"){
                    dibujarPedidos(pedidos_activos);
                }else if(localStorage.getItem("opcion_vista") == "por_mesa"){
                    dibujarPedidosPorMesa(pedidos_activos);
                }
            },
            error: function(error) {
                console.error(JSON.stringify(error))
            }
        });
    }

    function dibujarPedidos(pedidos_activos) {

        let pedidos_actuales = jQuery.parseJSON(localStorage.getItem("pedidos_actuales"));
        let pedidos_anulados = jQuery.parseJSON(localStorage.getItem("pedidos_anulados"));
        let tempHtml = "";
        let heightContainer = 100;
        let salon = $("#salon").val();

        pedidos_actuales.forEach(pedido => {
            let i = pedidos_activos.findIndex(x => x.id === pedido["id"]);
            if ( i > -1) {
                if(salon == 0 || pedido['pkSalon'] == salon){
                    tempHtml += "<div style='width:50vh; display:inline; float:left; margin-left:5px;'>";
                    tempHtml += "<div id='P" + pedido["id"] + "' class='panel panel-default' style='min-height: 250px; height:auto;'> <div class='panel-heading' style='" + aplicarColor(pedido["tiempo"]) + "'>";
                    tempHtml += "<p class='panel-title' style='font-size: 19px;'><b>" + pedido["plato"] + "</b></p></div>";
                    tempHtml += "<div class='panel-body'>";
                    tempHtml += "<h3><b>CANTIDAD: " + pedido["cantidad"] + "</b></h3>";
                    tempHtml += "<h3><b>N° PERSONAS: " + pedido["personas"] + "</b></h3>";
                    tempHtml += "<h4>TIEMPO: <span id='T" + pedido["id"] + "' hora-inicio='" + pedido["tiempo"] + "'></span></h4>";
                    tempHtml += "<h4><br>" + pedido["nombre_trabajador"] + "</h4>";
                    tempHtml += "<h3><b>SALON: " + pedido["salon"]+ "</b></h4>";
                    tempHtml += "<h3><b>" + pedido["mesa"] + "</b></h4>";
                    if (pedido["mensaje"] != null) {
                        if (pedidos_actuales[i]["msj"] != 0) {
                            pintar = "style='color: green;'";
                        }else{
                            pintar = "style='color: rgb(234,38,18);'";
                        }
                        
                        tempHtml += "<h3 class='text-uppercase' "+pintar+"><b>MENSAJE: " + pedido["mensaje"] + "</b></h3>";
                    }
                    tempHtml += "<br><button type='button' class='btn btn-success btn-block text-uppercase' style='box-shadow: none;' onclick='despachar(" + pedido["id"] + ")'>Despachar</button>";

                    tempHtml += "</div> </div> </div>";
                    heightContainer += 50;
                }                
            } else {
                if (pedidos_anulados.findIndex(x => x.id === pedido["id"]) == -1) {
                    pedidos_anulados.push(pedido);
                }
            }
        });

        pedidos_anulados.forEach(pedido => {
            if(salon == 0 || pedido['pkSalon'] == salon){
                tempHtml += "<div style='width:50vh; display:inline; float:left; margin-left:5px;'>";
                tempHtml += "<div class='panel panel-default' style='min-height: 250px; height:auto;'> <div class='panel-heading'>";
                tempHtml += "<p class='panel-title' style='font-size: 19px;'>" + pedido["plato"] + "</p></div><div class='panel-body'>";
                tempHtml += "<h3><b class='text-muted'>CANTIDAD: " + pedido["cantidad"] + "</b></h3>";
                tempHtml += "<h4 class='text-muted'>" + pedido["nombre_trabajador"] + "</h4><br>";
                tempHtml += "<h3><b class='text-muted'>SALON: " + pedido["salon"] + "<br>" + pedido["mesa"] + "</b></h3>";
                tempHtml += "<h4 class='text-uppercase' style='color:red;'><b>Pedido anulado</b></h4>";
                tempHtml += "<br><button type='button' class='btn btn-danger btn-block text-uppercase' style='box-shadow: none;' onclick='quitarPedido(" + pedido["id"] + ")'>Quitar</button>";
                tempHtml += "</div> </div> </div>";
                heightContainer += 50;
            }          
        });

        localStorage.setItem("pedidos_anulados", JSON.stringify(pedidos_anulados));

        $("#pedidos").css("width", heightContainer+"vh");
        $("#pedidos").html(tempHtml);
        inicializa_contadores();
    }

    function dibujarPedidosPorMesa(pedidos_activos){
        
        let pedidos_actuales = jQuery.parseJSON(localStorage.getItem("pedidos_actuales"));
        let pedidos_anulados = jQuery.parseJSON(localStorage.getItem("pedidos_anulados"));
        let mesas = jQuery.parseJSON(localStorage.getItem("mesas"));
        let tempHtml = "";
        let heightContainer = 100;

        let salon = $("#salon").val();

        mesas.forEach(mesa => {
            if(salon == 0 || mesa.salon == salon){
                let tempHtml2 = "";
                let algoParaMostrar = false;
                tempHtml2 += "<div style='width:50vh; display:inline; float:left; margin-left:5px; height:70vh; overflow:auto;'>";
                tempHtml2 += "<div class='panel panel-default' style='border-color: #00395A'>";
                tempHtml2 += "<div class='panel-heading' style='background-color: #00395A; color: #fff;'>SALON: "+mesa.mesa+"</div>";
                tempHtml2 += "<div class='panel-body'>";

                tempHtml2 += "<div class='list-group'>";

                pedidos_actuales.forEach(pedido => {
                    if(pedido["salon"]+" - "+pedido["mesa"] == mesa.mesa){
                        if(pedidos_activos.findIndex(x => x.id === pedido["id"]) > -1) {
                            
                            tempHtml2 += "<div class='list-group-item' style='" + aplicarColor(pedido["tiempo"]) + "'>";
                            tempHtml2 += "<div data-toggle='collapse' data-target='#detallePedido"+pedido["id"]+"' aria-expanded='false'><h6 class='list-group-item-heading' style='color: #fff'><b>"+pedido["plato"]+"</b></h6>";
                            tempHtml2 += "<p style='font-size: 12px;' class='list-group-item-text'><b>CANTIDAD: </b> "+pedido["cantidad"]+"</p>";
                            tempHtml2 += "<p style='font-size: 12px;' class='list-group-item-text'><b>TIEMPO: </b><span id='T" + pedido["id"] + "' hora-inicio='" + pedido["tiempo"] + "'></span></p></div>";
                            
                            tempHtml2 += "<div class='collapse' id='detallePedido"+pedido["id"]+"'><p style='font-size: 12px;' class='list-group-item-text'><b>USUARIO: </b> "+pedido["nombre_trabajador"]+"</p>";

                            if (pedido["mensaje"] != null) {
                            tempHtml2 += "<p style='font-size: 12px;' class='list-group-item-text'><b>MENSAJE: </b> "+pedido["mensaje"]+"</p>";
                            }
                            tempHtml2 += "<p style='font-size: 12px;' class='list-group-item-text'><a type='button' class='btn btn-default btn-sm btn-block text-uppercase' onclick='despachar("+pedido["id"]+")'>Despachar</a></p>";
                            tempHtml2 += "</div></div>";
                            algoParaMostrar = true;
                            heightContainer += 50;
                            
                        }else {
                            if(pedidos_anulados.findIndex(x => x.id === pedido["id"]) == -1) {
                                pedidos_anulados.push(pedido);
                            }
                        }
                    }                
                });

                pedidos_anulados.forEach(pedido => {
                    if(pedido["salon"]+" - "+pedido["mesa"] == mesa.mesa){
                    
                        tempHtml2 += "<div class='list-group-item'>";
                        tempHtml2 += "<div data-toggle='collapse' data-target='#detallePedido"+pedido["id"]+"' aria-expanded='false'><h6 class='list-group-item-heading text-muted'><b>"+pedido["plato"]+"</b></h6>";
                        tempHtml2 += "<p style='font-size: 12px;' class='list-group-item-text text-muted'><b>ANULADO</b></p>";
                        tempHtml2 += "<p style='font-size: 12px;' class='list-group-item-text text-muted'><b>CANTIDAD: </b> "+pedido["cantidad"]+"</p></div>";
                        
                        tempHtml2 += "<div class='collapse' id='detallePedido"+pedido["id"]+"'><p style='font-size: 12px;' class='list-group-item-text text-muted'><b>USUARIO: </b> "+pedido["nombre_trabajador"]+"</p>";

                        if (pedido["mensaje"] != null) {
                        tempHtml2 += "<p style='font-size: 12px;' class='list-group-item-text text-muted'><b>MENSAJE: </b> "+pedido["mensaje"]+"</p>";
                        }
                        tempHtml2 += "<p style='font-size: 12px;' class='list-group-item-text'><a type='button' class='btn btn-default btn-sm btn-block text-uppercase' onclick='quitarPedido("+pedido["id"]+")'>Quitar</a></p>";
                        tempHtml2 += "</div></div>";
                        algoParaMostrar = true;
                        heightContainer += 50;
                    }
                    
                });

                tempHtml2 +="</div></div></div></div>"

                if(algoParaMostrar){
                    tempHtml += tempHtml2;
                }
            }
        });

        localStorage.setItem("pedidos_anulados", JSON.stringify(pedidos_anulados));

        $("#pedidos").css("width", heightContainer+"vh");
        $("#pedidos").html(tempHtml);
        inicializa_contadores();
    }

    function quitarPedido(id) {
        let pedidos_actuales = jQuery.parseJSON(localStorage.getItem("pedidos_actuales"));
        let index = pedidos_actuales.findIndex(x => x.id === id.toString());
        pedidos_actuales.splice(index, 1);
        localStorage.setItem("pedidos_actuales", JSON.stringify(pedidos_actuales));

        let pedidos_anulados = jQuery.parseJSON(localStorage.getItem("pedidos_anulados"));
        let index2 = pedidos_anulados.findIndex(x => x.id === id.toString());
        pedidos_anulados.splice(index2, 1);
        console.log(index, pedidos_anulados);
        localStorage.setItem("pedidos_anulados", JSON.stringify(pedidos_anulados));
        cargarPedidos();
    }

    function inicializa_contadores() {

        let pedidos_actuales = jQuery.parseJSON(localStorage.getItem("pedidos_actuales"));

        pedidos_actuales.forEach(pedido => {
            setInterval(counterstrike, 1000, "T" + pedido["id"]);
        });
    }

    function counterstrike(id) {
        var startDateTime = new Date($("#" + id).attr("hora-inicio"));
        var startStamp = startDateTime.getTime();

        var newDate = new Date();
        var newStamp = newDate.getTime();

        newDate = new Date();
        newStamp = newDate.getTime();
        var diff = Math.round((newStamp - startStamp) / 1000);

        var d = Math.floor(diff / (24 * 60 * 60));
        diff = diff - (d * 24 * 60 * 60);
        var h = Math.floor(diff / (60 * 60));
        diff = diff - (h * 60 * 60);
        var m = Math.floor(diff / (60));
        diff = diff - (m * 60);
        var s = diff;

        h = h + d * 24;

        $("#" + id).html(h.toString().padStart(2, "0") + ":" + m.toString().padStart(2, "0") + ":" + s.toString().padStart(2, "0"));
    }

    function aplicarColor(time) {

        var startDateTime = new Date(time);
        var startStamp = startDateTime.getTime();

        var newDate = new Date();
        var newStamp = newDate.getTime();

        newDate = new Date();
        newStamp = newDate.getTime();
        var diff = Math.round((newStamp - startStamp) / 1000);

        var d = Math.floor(diff / (24 * 60 * 60));
        diff = diff - (d * 24 * 60 * 60);
        var h = Math.floor(diff / (60 * 60));
        diff = diff - (h * 60 * 60);
        var m = Math.floor(diff / (60));
        diff = diff - (m * 60);
        var s = diff;
        h = h + d * 24;

        let style = "";

        let tiempo_verde = jQuery.parseJSON(localStorage.getItem("tiempo_verde"));
        let tiempo_naranja = jQuery.parseJSON(localStorage.getItem("tiempo_naranja"));
        let tiempo_rojo = jQuery.parseJSON(localStorage.getItem("tiempo_rojo"));

        if (h == 0) {
            if (m >= tiempo_verde[0] && m < tiempo_verde[1]) {
                style = "background-color: #02b875; color: #fff"; //VERDE
            } else if (m >= tiempo_naranja[0] && m < tiempo_naranja[1]) {
                style = "background-color: #f0ad4e; color: #fff"; //NARANJA
            } else if (m >= tiempo_rojo[0]) {
                style = "background-color: #d9534f; color: #fff"; //ROJO
            }
        } else {
            style = "background-color: #d9534f; color: #fff"; // ROJO
        }

        return style;
    }

    function despachar(id) {

        let tiempo = $("#" + "T" + id).html();
        let tiempoArray = tiempo.split(":");

        let ultimas_despachadas = jQuery.parseJSON(localStorage.getItem("ultimas_despachadas"));
        let numero_ultimas_despachadas = parseInt(localStorage.getItem("numero_ultimas_despachadas"));

        if (ultimas_despachadas.length <= numero_ultimas_despachadas) {
            ultimas_despachadas.unshift([tiempoArray[0], tiempoArray[1]]);
        } else {
            ultimas_despachadas.unshift([tiempoArray[0], tiempoArray[1]]);
            ultimas_despachadas.splice(numero_ultimas_despachadas - 1, 1);
        }

        $.ajax({
            url: window.location.origin + "/usqay/?controller=Pedidos&&action=entregaPedidoPantalla",
            data: {
                id: id
            },
            method: 'POST',
            success: function(data) {
                let pedidos_actuales = jQuery.parseJSON(localStorage.getItem("pedidos_actuales"));
                let index = pedidos_actuales.findIndex(x => x.id === id.toString());
                pedidos_actuales.splice(index, 1);
                localStorage.setItem("pedidos_actuales", JSON.stringify(pedidos_actuales));
                localStorage.setItem("ultimas_despachadas", JSON.stringify(ultimas_despachadas));
                cargarPedidos();
            },
            error: function(error) {
                console.error(JSON.stringify(error))
            }
        });
    }

    function actualizarTiempoPromedio() {

        let ultimas_despachadas = jQuery.parseJSON(localStorage.getItem("ultimas_despachadas"));

        let suma = 0;
        let num = ultimas_despachadas.length;
        let prom = 0;

        ultimas_despachadas.forEach(ud => {
            if (parseInt(ud[0]) == 0) {
                suma += parseInt(ud[1]);
            } else {
                suma += parseInt(ud[0] * 60);
                suma += parseInt(ud[1]);
            }
        });
        prom = parseInt(suma / num)
        $("#tiempo_promedio_despacho").html(prom);
    }

    function cargarHistorial(){
        
        let inicio = localStorage.getItem("historialInicio");
        let numItems = localStorage.getItem("numItemsHistorial");
        let tipos_seleccionados = jQuery.parseJSON(localStorage.getItem("tipos_seleccionados"));

        //VARIABLES PARA AJAX
        let tipos = "";

        tipos_seleccionados.forEach(t => {
            tipos += t[1] + ",";
        });
        $.ajax({
            url: window.location.origin + "/usqay/?controller=Pedidos&&action=obtieneHistorialDelDiaPantalla",
            data: {
                tipos: tipos,
                inicio: inicio,
                fin: numItems
            },
            method: 'POST',
            success: function(data) {

                pedidos = jQuery.parseJSON(data);

                let tempHtml = "<div class='list-group'>";

                if(pedidos.length < numItems){
                    $("#pagNextHistorial").attr("disabled", "true");
                }
                pedidos.forEach(pedido => {

                    tempHtml += "<div onclick='rejectPedido("+pedido["id"]+")' class='list-group-item'>";
                    
                    tempHtml += "<h5 class='list-group-item-heading'><b>"+pedido["id"]+" - "+pedido["plato"]+"</b></h5>";
                    if(pedido["mensaje"] != null){
                        tempHtml += "<p class='list-group-item-text'><b>Mensaje: </b>"+pedido["mensaje"]+"</p>";
                    }

                    tempHtml += "<p class='list-group-item-text'><b>Emitido por: </b>"+pedido["nombre_trabajador"]+"</p>";
                    tempHtml += "<p class='list-group-item-text'><b>Cantidad: </b>"+pedido["cantidad"]+"</p>";
                    tempHtml += "<p class='list-group-item-text'><b>Salon: </b>"+pedido["salon"]+"</p>";
                    tempHtml += "<p class='list-group-item-text'><b>Mesa: </b>"+pedido["mesa"]+"</p>";
                    
                    if(pedido["estado"] == 3){
                        tempHtml += '<span class="label bg-danger">Eliminado</span>';
                    }else{
                        tempHtml += '<span class="label bg-success">Despachado</span>';
                    }
                    tempHtml += "</div>"

                });
                tempHtml += "</div>"

                $("#historialPedidos").html(tempHtml);

            },
            error: function(error) {
                console.error(JSON.stringify(error))
            }
        });
    }

    function actualiza_entrega(){
        if($('#check_entrega').is(":checked")) {
            $.ajax({
                url: window.location.origin + "/usqay/?controller=Pedidos&&action=cookieImpresionPantalla",
                data: {
                    f: 'imp',
                    e: 1
                },
                method: 'POST'
            });
        }else{
            $.ajax({
                url: window.location.origin + "/usqay/?controller=Pedidos&&action=cookieImpresionPantalla",
                data: {
                    f: 'imp',
                    e: 0
                },
                method: 'POST'
            });
        }
    }

    function actualiza_anulacion(){
        if($('#check_anulacion').is(":checked")) {
            $.ajax({
                url: window.location.origin + "/usqay/?controller=Pedidos&&action=cookieImpresionPantalla",
                data: {
                    f: 'anu',
                    e: 1
                },
                method: 'POST'
            });
        }else{
            $.ajax({
                url: window.location.origin + "/usqay/?controller=Pedidos&&action=cookieImpresionPantalla",
                data: {
                    f: 'anu',
                    e: 0
                },
                method: 'POST'
            });
        }
    }

</script>