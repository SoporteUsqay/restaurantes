<?php
$titulo_pagina = 'Facturacion Personalizada';
$titulo_sistema = 'Usqay2';

include_once('recursos/componentes/MasterConexion.php');
$conn = new MasterConexion();

$impuesto_bolsa = 0;
$res = $conn->consulta_arreglo("Select * from cloud_config where parametro = 'icbper'");
$impuesto_bolsa = floatval($res["valor"]);

$simbolo_nacional = null;
$res = $conn->consulta_arreglo("Select * from moneda where estado = 2");
$simbolo_nacional = $res["simbolo"];

require_once('recursos/componentes/header.php'); 
?>
<link rel="stylesheet" href="recursos/btable/bootstrap-table.min.css">

<h1>Facturacion Personalizada</h1>
<div class="row">
    <div class="col-xs-12 col-md-6">
        <div class="panel panel-primary" id='panel_general'>
            <div class="panel-heading">
                <h3 class="panel-title">Información General</h3>
            </div>
            <div class="panel-body">
                <div class='control-group'>
                    <label>Tipo Comprobante</label>
                    <br/><input type="radio" name="tipo_comprobante" value="1" checked="true">Boleta
                    <br/><input type="radio" name="tipo_comprobante" value="2">Factura
                </div>

                <div class='control-group'>
                    <label>Documento</label>
                    <div class="input-group">
                        <input type="number" id="documento" max="11" name="documento" class="form-control" placeholder="Ingrese Su DNI/RUC - Presione 'Enter para iniciar la busqueda'">
                        <span class="input-group-addon" onclick='window.open("http://www.sunat.gob.pe/cl-ti-itmrconsruc/FrameCriterioBusquedaMovil.jsp","Consulta Sunat","width=600,height=600,top=10,left=20,resizable=no,scrollbars=yes,menubar=no,toolbar=no,status=no,location=no")'><img src="<?php echo '//' . $_SERVER['HTTP_HOST'] . '/usqay' ?>/Public/images/sunat.png" style="height: 20px; width: 20px;"></span>
                    </div>

                </div>
                

                <div class='control-group'>
                    <label>Cliente</label>
                    <input class="form-control" name="cliente" id="cliente" placeholder="Ingrese su Nombre/Razon Social">
                </div>

                <div class='control-group'>
                    <label>Direccion</label>
                    <input class="form-control" name="direccion" id="direccion" placeholder="Ingrese su Dirección">
                </div>

                <div class='control-group'>
                    <label>Correo Electrónico</label>
                    <input type="email" id="correo" name="correo" class="form-control" placeholder="Ingrese Su Correo Electronico">
                </div>

            </div>
        </div>
    </div>
    <div class="col-xs-12 col-md-6">
        <div class="panel panel-primary" id='panel_detalles'>
            <div class="panel-heading">
                <h3 class="panel-title">Detalles de la factura</h3>
            </div>
            <div class="panel-body">
                <div class='control-group'>
                    <label>Item</label>
                    <select class="form-control" id="id_plato">
                        <?php 
                        $platos = $conn->consulta_matriz("Select p.*, pc.id_tipo_impuesto from plato p, plato_codigo_sunat pc where estado = 0 AND pc.id_plato = p.pkPlato");
                            if(is_array($platos)){
                                foreach ($platos as $tp){
                                    if(intval($tp["id_tipo_impuesto"]) === 5){
                                        echo "<option value='".$tp["pkPlato"]."'>".utf8_encode($tp["descripcion"])." - ".$simbolo_nacional." ".(floatval($tp["precio_venta"])+$impuesto_bolsa)."</option>";
                                    }else{
                                        echo "<option value='".$tp["pkPlato"]."'>".utf8_encode($tp["descripcion"])." - ".$simbolo_nacional." ".$tp["precio_venta"]."</option>";
                                    }
                                    
                                }
                            }
                        ?>
                    </select>
                </div>
                <div class='control-group'>
                    <label>Precio Unitario</label>
                    <input type="number" value="1" id="precio" class="form-control" onchange="calcula_total()">
                </div>
                <div class='control-group'>
                    <label>Cantidad</label>
                    <input type="number" value="1" id="cantidad" class="form-control" onchange="calcula_total()">
                </div>
                <div class='control-group'>
                    <label>Total</label>
                    <input type="number" value="1" id="total" class="form-control" readonly>
                </div>
                <p></p>
                <button type="button" class="btn btn-success btn-lg" onclick="agregar()">Agregar</button>

            </div>
        </div>
    </div>
</div>
</form>    
<div class='contenedor-tabla'>
    <table class="table" id="detalles" data-unique-id="id">
        <thead>
        <tr>
            <th data-field="id">Id</th>
            <th data-field="producto">Item</th>
            <th data-field="precio_venta">Precio Unitario</th>
            <th data-field="cantidad">Cantidad</th>
            <th data-field="subtotal">SubTotal</th>
            <th data-field="operate"
            data-align="center"
            data-formatter="operateFormatter"
            data-events="operateEvents"></th>
        </tr>
        </thead>
        <tbody>
        </tbody>                      
    </table>
</div> <!--contenedor tabla-->
<p></p>
<p></p>
<div class="row">
    <div class="col-xs-12 col-md-6" style="font-size: 22px; font-weight: bold;">
        <div class="row">
            <div class='col-lg-12'>
            Total: <?php echo $simbolo_nacional; ?> <span id="txt_total_venta">0.00</span>
            </div>
        </div>
        <div class="row">
            <div class='col-lg-3'>
            Descuento:
            </div>
            <div class='col-lg-3' style='padding:0px;'>
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon2"><?php echo $simbolo_nacional;?></span>
                    <input aria-describedby="basic-addon2" value="0" id="monto_descuento" name="monto_descuento" class="form-control" type="number" min="0" onchange="actualiza_descuento(1)">       
                </div>
            </div>
            <div class='col-lg-3' style='padding:0px 0px 0px 5px;'>
                <div class="input-group">
                    <input aria-describedby="basic-addon1" value="0" id="porcentaje_descuento" name="porcentaje_descuento" class="form-control" type="number" min="0" max="99" onchange="actualiza_descuento(2)">
                    <span class="input-group-addon" id="basic-addon1">%</span>
                </div>
            </div>   
        </div>
        <div class="row">
        <tbody id="table_detraccion">
            <?php
              $porcentaje_detraccion = 0;
              $query_detraccion = "Select * from cloud_config where parametro = 'porcentaje_detraccion'"; 
              $res_detraccion = $db->executeQueryEx($query_detraccion);
              if ($row_detraccion = $db->fecth_array($res_detraccion)) {
                $query_detraccion2 = "select * from porcentaje_detraccion where id = '${row_detraccion['valor']}'";
                $res_detraccion2 = $db->executeQueryEx($query_detraccion2);
                if ($row_detraccion2 = $db->fecth_array($res_detraccion2)) {
                  $porcentaje_detraccion = floatval($row_detraccion2['porcentaje']);
                }
              }
            ?>
            <script>
              var porcentaje_detraccion = <?php echo $porcentaje_detraccion; ?>;
            </script>
            <tr class="odd">
                <td class="text-left">
                  <input type="checkbox" id="check_detraccion" >
                </td>
                <td></td>
                <td>
                  <label for="">DETRACCIÓN (<?php echo $porcentaje_detraccion; ?>%)</label>
                </td>
                <td>
                  S/ 
                </td>
                <td>
                  <label id="lblTotalDetraccion" for=""><strong id="totalDetraccion">0</strong></label>
                </td>
                <td></td>
              </tr>
          </tbody>
        </div>
        <div class="row">
            <div class='col-lg-12'>
            Total Final: <?php echo $simbolo_nacional; ?> <span id="txt_total_final">0.00</span>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-md-6" style="text-align: right;">
        <button type="button" class="btn btn-danger btn-lg" onclick="emitir()">Emitir</button>
    </div>
</div>
<p></p>
<p></p>
<!--Modal para bloquear input-->
<div class='modal fade' id='modal_envio_anim' tabindex='-1' role='dialog' data-keyboard="false" data-backdrop="static" style="z-index: 999999 !important;">
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title' id='myModalLabel'>Procesando</h4>
            </div>
            <div class='modal-body'>
                <center>
                    <img src="<?php echo '//' . $_SERVER['HTTP_HOST'] . '/usqay' ?>/Public/images/pacman.gif">
                </center>
            </div>
        </div>
    </div>
</div>
</div><!--/row-->
<hr>
</div><!--/.container-->
    

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="recursos/js/jquery.js"></script>
    <script src="recursos/js/bootstrap.min.js"></script>
    <script src="recursos/js/offcanvas.js"></script>
    <script src="../Public/select2/js/select2.js"></script>
    <script src="recursos/btable/bootstrap-table.min.js"></script>
    <script>
     
     var total_venta = 0;
     var descuento_porcentaje = 0;
     var descuento = 0;
     var total_con_descuento = 0;

     var $table = $('#detalles');
     var data = [];
     $table.bootstrapTable({data: data});
     
     var productoSeleccionado = {
        id: null,
        producto: null,
        precio_venta: null,
        cantidad: null,
        subtotal: null
     };
        
    function calcula_total(){
        var precio = parseFloat($("#precio").val());
        var cantidad = parseFloat($("#cantidad").val());
        var total = precio*cantidad;
        $("#total").val(total);
    }
    
    function operateFormatter(value, row, index) {
        return [
            '<a class="btn btn-danger btn-sm remove" href="javascript:void(0)" title="Remove">',
            '<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>',
            '</a>'

        ].join('');
    }
    
    window.operateEvents = {
        'click .remove': function (e, value, row, index) {
            $('#detalles').bootstrapTable('remove', {
                field: 'id',
                values: [row.id]
            });
            
            total_venta = total_venta - Number(row.subtotal);
            $("#txt_total_venta").html(total_venta);
            actualiza_descuento(1);
        }
    };
    
    function agregar(){
        var actual = $("#id_plato option:selected").text();
        var trozo = actual.split("- <?php echo $simbolo_nacional;?>");
            
        productoSeleccionado.id = $("#id_plato option:selected").val();
        productoSeleccionado.producto = trozo[0];
        productoSeleccionado.precio_venta = parseFloat($("#precio").val()).toFixed(2);
        productoSeleccionado.cantidad = parseFloat($("#cantidad").val()).toFixed(2);
        productoSeleccionado.subtotal = parseFloat($("#total").val()).toFixed(2);

        $table.bootstrapTable('insertRow', {
            index: 1,
            row: productoSeleccionado
        });
        
        total_venta = Number(total_venta) + Number($("#total").val());
        total_venta = parseFloat(total_venta).toFixed(2);
        $("#txt_total_venta").html(total_venta);
        actualiza_descuento(1);


        productoSeleccionado = {
            id: null,
            producto: null,
            precio_venta: null,
            cantidad: null,
            subtotal: null
        };

        console.log(productoSeleccionado);
    }

    function my_round(value, decimals) {
        return Number(Math.round(value+'e'+decimals)+'e-'+decimals);
    }

    function actualiza_descuento(typ){
        var t_porcentaje = parseFloat($("#porcentaje_descuento").val()).toFixed(2);
        var t_monto = parseFloat($("#monto_descuento").val()).toFixed(2);
        var maximo_descuento = my_round((total_venta*0.99),2);

        if(t_porcentaje >= 99){
            t_porcentaje = 99;
        }
        if(t_monto >= maximo_descuento){
            t_monto = maximo_descuento;
        }

        switch(typ){
            case 1:
            total_con_descuento = parseFloat(total_venta) - t_monto;
            total_con_descuento = my_round(total_con_descuento,2);
            var nuevo_porcentaje = ((t_monto*100)/total_venta).toFixed(2);
            $("#porcentaje_descuento").val(nuevo_porcentaje);
            $("#monto_descuento").val(t_monto);
            descuento_porcentaje = nuevo_porcentaje;
            descuento = t_monto;
            $("#txt_total_final").html(total_con_descuento);
            break;

            case 2:
            var descuento_calculo = ((total_venta*t_porcentaje)/100).toFixed(2);
            total_con_descuento = parseFloat(total_venta) - descuento_calculo;
            total_con_descuento = my_round(total_con_descuento,2);
            $("#porcentaje_descuento").val(t_porcentaje);
            $("#monto_descuento").val(descuento_calculo);
            descuento_porcentaje = t_porcentaje;
            descuento = descuento_calculo;
            $("#txt_total_final").html(total_con_descuento);
            break;
        }
    }
    
    jQuery.fn.reset = function () {
        $(this).each(function () {
            this.reset();
        });
    };

    var total_detraccion = 0;

    $(document).ready(function () {
        $('#id_plato').select2({dropdownParent: $('#panel_detalles')});
        history.pushState(null, "", 'facturacion_personalizada.php'); 
        
        $('#id_plato').on('select2:select', function (e) {
            var actual = $("#id_plato option:selected").text();
            var trozo = actual.split("S/");
            var precio_actual = parseFloat(trozo[1]);
            $("#precio").val(precio_actual);
            $("#cantidad").val(1);
            $("#total").val(precio_actual);
        });

        var actual = $("#id_plato option:selected").text();
        var trozo = actual.split("S/");
        var precio_actual = parseFloat(trozo[1]);
        $("#precio").val(precio_actual);
        $("#cantidad").val(1);
        $("#total").val(precio_actual);

        $('#check_detraccion').on('change', () => {

        let isChecked = $('#check_detraccion').prop('checked');

        if(isChecked){
            total_detraccion = my_round(total_con_descuento * porcentaje_detraccion / 100, 2);
            $('#totalDetraccion').html(total_detraccion)
            // $('#check_detraccion').hide(0)
        } else {
            total_detraccion = 0;
            $('#totalDetraccion').html(0)
        }
        })
    });
    
    $('#documento').keypress(function(event) {
      var keycode = (event.keyCode ? event.keyCode : event.which);
      if (keycode == '13') {
        $('#modal_envio_anim').modal('show');
        $("#cliente").val("");
        $("#direccion").val("");
        $("#correo").val("");

        var param = {'document': $('#documento').val()};

        if ($('#documento').val().length == 8) {
            $.ajax({
                url: "<?php echo '//' . $_SERVER['HTTP_HOST'] . '/usqay' ?>/?controller=Cliente&&action=ClienteDni",
                type: 'POST',
                data: param,
                cache: false,
                dataType: 'json',
                success: function(data1) {
                    var hay = 0;
                    $.each( data1, function( key1, value1 ) {
                        $("#cliente").val(value1.nombres);
                        $("#direccion").val(value1.direccion);
                        $("#correo").val(value1.email);
                        hay = 1;
                        $('#modal_envio_anim').modal('hide');
                        return false;
                    });

                }
            });
        } else {
            $.ajax({
                url: "<?php echo '//' . $_SERVER['HTTP_HOST'] . '/usqay' ?>/?controller=Cliente&&action=ClienteRuc",
                type: 'POST',
                data: param,
                cache: false,
                dataType: 'json',
                success: function(data0) {
                    $('#modal_envio_anim').modal('hide');
                    $.each( data0, function( key0, value0 ) {
                        $("#cliente").val(value0.companyName);
                        $("#direccion").val(value0.address);
                        $("#correo").val(value0.email);
                        return false;
                    });
                }
            });
        }
      }
    });
    
    function emitir(){
        //Tipo comprobante
        var tipo_comprobante = $("input[name='tipo_comprobante']:checked").val();
        //Primero verificamos para no cagarla
        //Datos Cliente
        var documento = $("#documento").val();
        var cliente = $("#cliente").val();
        var direccion = $("#direccion").val();
        var correo = $("#correo").val();
        //Variable centinella
        var pasa = 0;
        var mensaje = "";
        //Verificamos
        if(parseFloat($("#txt_total_venta").html())>0){
            pasa = 1;
        }else{
            pasa = 0;
            mensaje = "El comprobante debe tener items para ser emitido";
        }
        
        if(parseInt(tipo_comprobante) === 1){
            if(parseFloat($("#txt_total_venta").html()) > 700){
                if(documento !== "" && cliente !== "" && direccion !== ""){
                    if(documento.length === 8 || documento.length === 11){
                        pasa = 1;
                    }else{
                        mensaje = "El DNI debe ser de 8 digitos";
                    }
                }else{
                    mensaje = "Para montos mayores a 700 soles la boleta debe llevar datos";
                }
            }else{
                if(documento.length > 1){
                    if(documento.length === 8 || documento.length === 11){
                        pasa = 1;
                    }else{
                        mensaje = "El DNI debe ser de 8 digitos";
                        pasa = 0;
                    }
                }else{
                    pasa = 1;
                }
            }
        }
        
        if(parseInt(tipo_comprobante) === 2){
            if(documento !== "" && cliente !== "" && direccion !== ""){
                if(documento.length === 11){
                    pasa = 1;
                }else{
                    mensaje = "El RUC debe ser de 11 digitos";
                }
            }else{
                mensaje = "Es obligatorio poner los datos del cliente en una factura";
            }
        }

        if(pasa === 1){
            var tipo_impresion = "BOLETA";
            if(parseInt(tipo_comprobante) === 2){
                tipo_impresion = "FACTURA";
            }
        
            var r = confirm("Se va a emitir una "+tipo_impresion+" por el monto de <?php echo $simbolo_nacional;?>"+total_con_descuento+". ¿Continuar?");
            if (r == true) {
                $('#modal_envio_anim').modal('show');
                var param = {tipo_comprobante: tipo_comprobante, documento: documento, cliente: cliente, direccion: direccion, correo: correo, productos: JSON.stringify($table.bootstrapTable('getData')), total: total_con_descuento, descuento_monto: descuento, descuento_porcentaje: descuento_porcentaje, total_detraccion: total_detraccion};
                $.ajax({
                  url: "<?php echo '//' . $_SERVER['HTTP_HOST'] . '/usqay' ?>/?controller=Comprobante&&action=Personalizado",
                  type: 'POST',
                  data: param,
                  success: function(data1) {
                    var json = JSON.parse(data1);
                    if(json.exito == 1){
                        //Finalmente ponemos en cola la impresion
                        var param2 = {'pkPedido': json.id_comprobante, 'terminal': '<?php echo $_COOKIE['t'] ?>', 'tipo': tipo_impresion, 'aux': '<?php echo $_SESSION['id'];?>,1'};
                        $.ajax({
                            url: "<?php echo '//' . $_SERVER['HTTP_HOST'] . '/usqay' ?>/?controller=Pedidos&&action=ImprimeCuenta",
                                type: 'POST',
                                data: param2,
                                success: function() {
                                   window.location.reload();
                                }
                        });
                    }else{
                        alert(json.mensaje);
                        $('#modal_envio_anim').modal('hide');
                    }
                  }
                });
            }
        }else{
            alert("Error: "+mensaje);
        }
    }

    </script>
  </body>
</html>

                            