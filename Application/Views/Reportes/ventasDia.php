<?php
include 'Application/Views/template/header.php';
require_once('Components/Config.inc.php');
$sucursal = UserLogin::get_pkSucursal();
?>
<body>

    <?php
    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showContent();
    ?>    
    <div class="container">


        <br>
        <br>
        <br>

        <div style="background: #000000;">
            <table class="table" style=" color: #ffffff">
                <tr>
                    <td>
                        Fecha
                        <input class="form-control" type="text" id="dateInto" value="<?php echo date('Y-m-d') ?>">
                        <input class="form-control" type='hidden' id='pksucursal' name='id_sucursal' value='<?php echo $sucursal; ?>'/>
                    </td>
                    <td>
                        Estado
                        <select id="cmbFilterEstadoVentas" class="form-control" onchange="loadTableSale2()">
                            <option value="0">Todo</option>
                            <option value="1">Efectivo</option>
                            <option value="2">Tarjeta</option>
                            <option value="3">Anuladas</option>
                            <option value="4">Credito</option>
                            <option value="3">Consumo</option>
                        </select>    
                    </td>
                    <td>
                        Mozo
                        <select id="cmbFilterMozo" class="form-control" onchange="loadTableSale2()">
                            <option value="0">Todo</option>

                        </select>
                    </td>
                    <td>
                        <br>
                        <!--</div>-->
                        <!--<div class="col-sm-2">-->
                        <button class="btn btn-primary" onclick="loadTableSale2()"><span class="glyphicon glyphicon-search"></span></button> 

                        <button class="btn btn-success" onclick="ventasdiariasPDF()"><span class="glyphicon glyphicon-arrow-down"></span>PDF</button>

<!--                        <button class="btn btn-success" onclick="ventasdiariasEXCEL()"><span class="glyphicon glyphicon-arrow-down"></span>EXCEL</button> -->
                        <!--<button class="btn btn-danger" onclick="EliminaVentaDia()"><span class="glyphicon glyphicon-remove"></span>Anular Venta</button>--> 

                        <!--</div>-->
                    </td>
                </tr>
            </table>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">Consolidado de ventas</div>
            <div class="panel-body">
                <table class="table">
                    <tr>
                        <td>
                            Total Efectivo  
                        </td>
                        <td>
                            <label id="lblTotalPedido2">00.00 </label> 
                        </td>
                        <td>
                            Total Tarjeta
                        </td>
                        <td>
                            <label id="lblTTarjeta">0</label>  
                        </td>
                        <td>
                            T. Descuento
                        </td>
                        <td>
                            <label id="lblDescuento"></label>

                        </td>
                    </tr>
                    <tr>
                        <td>
                            Cant. Ventas
                        </td>
                        <td>
                            <label id="lblnPedidosCantidad">0</label>
                        </td>
                        <td>
                            T. MasterCard 
                        </td>
                        <td>
                            <label id="lblMaster">0</label>  
                        </td>
                        <td>
                            T. Visa 
                        </td>
                        <td>
                            <label id="lblVisa">0</label>   
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <!--FIN filtro-->
        <div class="row-border">
            <div class="col-lg-12">
                <ul class="nav nav-tabs" id="myTabReportSaleDa2te">
                    <li class="active"><a href="#pedidos2" data-toggle="tab">Pedidos</a></li>
                    <li><a href="#detalle_pedido2" data-toggle="tab">Detalles</a></li>

                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="pedidos2">
                        <table id="tblReporSaleDate1dia" class="table table-hover">
                            <thead>
                                <tr>
                                    <th data-options="field:'pkComprobante'">Id</th>

                                    <th >Mesa</th>
                                    <th >N.Person</th>
                                    <th >H.Entrada</th>
                                    <th >Descuento</th>
                                    <th >Total</th>
                                    <th >T. Tarjeta</th>
                                    <th>T. Efectivo</th>
                                    <th>Tarjeta</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>     
                        </table>


                    </div>
                    <!---->

                    <div class="tab-pane" id="detalle_pedido2">
                        <table id="tblPedido2" class="table table-hover" title="Listado de Productos" style="width:800px;height:auto"
                               data-options="
                               iconCls: 'icon-edit',
                               singleSelect: true,

                               method:'get',
                               onLoadSuccess:loadTotal2Pedido,
                               url: '<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ListPedidosItem2',
                               collapsible: true
                               ">
                            <thead>
                                <tr>
                                    <th data-options="field:'pedido',width:250">Pedido</th>
                                    <th data-options="field:'precio',editor:'text'">Precio</th>
                                    <th data-options="field:'cantidad',align:'right',editor:{type:'numberbox',options:{precision:1}}">Cantidad</th>
                                    <th data-options="field:'importe',align:'right',editor:'numberbox'">Importe</th>
                                    <th data-options="field:'mensaje',align:'right',hidden:'true'">Mensaje</th>
                                    <th data-options="field:'pkPedido',align:'right',hidden:'true'">Id</th>
        <!--                                    <th data-options="field:'attr1',width:250,editor:'text'">Attribute</th>
                                    <th data-options="field:'status',width:60,align:'center',editor:{type:'checkbox',options:{on:'P',off:''}}">Status</th>-->
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>                           
                        </table>

                        <div class="row" style="font-size: 20px">
                            <div class="col-md-6">Numero Detalles:</div>
                            <div class="col-md-6">Total : </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>
        <!--</div>-->
    </div>

</body>
<script>
    $(document).ready(function() {

        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=WorkPeople&action=ListTrabadores', {'departament': $('#cmbRegisterDeparment').val()}, function(data) {

//           $('#cmbFilterMozo option').remove();
            $('#cmbFilterMozo').append("<option value=\"0\">Seleccione una opcion</option>");
            for (var i = 0; i < data.length; i++) {

                $('#cmbFilterMozo').append("<option>" + data[i].nombres + "</option>")
//            $('#txtModifyUserType').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
//            $('#cmbWorkStationModifyPermissions').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
            }

        });
    });


    function loadTableSale2() {


        var params =
                {dateGo: $('#dateInto').val(),
                    estado: $('#cmbFilterEstadoVentas').val()
                };
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Report&&action=ListSaleDate", /* Llamamos a tu archivo */
            data: params, /* Ponemos los parametros de ser necesarios */
            type: "POST",
            contentType: "application/x-www-form-urlencoded",
            dataType: "json", /* Esto es lo que indica que la respuesta será un objeto JSon */
            success: function(data) {
                /* Supongamos que #contenido es el tbody de tu tabla */
                /* Inicializamos tu tabla */
                $("#tblReporSaleDate1dia tbody").empty();
                /* Vemos que la respuesta no este vacía y sea una arreglo */
//                if (data != null && $.isArray(data)) {
                /* Recorremos tu respuesta con each */
                var string = "";

                var cantidad = 0;
                var total = 0;
                var tefectivo = 0;
                var Visa = 0;
                var master = 0;
                var descuento = 0;
                $.each(data, function(index, value) {
                    if ("3" !== $('#cmbFilterEstadoVentas').val()) {
                        string = "<td><a class='label label-danger' onclick='EliminaVentaDia(\"" + value.pkComprobante + "\"," + value.pkMesa + ")'><span class='glyphicon glyphicon-remove'></span>Anular Venta</a></td>";

                    }
                    cantidad++;
                    total = total + parseFloat(value.total_venta);
                    tefectivo = tefectivo + parseFloat(value.total_efectivo);
                    /* Vamos agregando a nuestra tabla las filas necesarias */
                    $("#tblReporSaleDate1dia tbody").append("<tr><td>" + value.pkComprobante + "</td><td>" + value.nmesa + "</td><td>" + value.npersonas + "</td><td>" + value.horaEntrada + "</td><td>" + value.descuento + "</td><td>" + value.total_venta + "</td> <td>" + value.totalTarjeta + "</td><td>" + value.total_efectivo + "</td><td>" + value.tipo_tarjeta + "</td>  <td><a href='javascript:void(0)' onclick='loadTablePedidos2(\"" + value.pkComprobante + "\")'><span class='label label-success'>Detalles</span></a>" + string + "</td></tr>");
                    descuento = descuento + parseFloat(value.descuento);
                    if (value.tipo_tarjeta === "VISA") {
                        Visa = Visa + parseFloat(value.totalTarjeta);
                    }
                    else {
                        master = master + parseFloat(value.totalTarjeta);
                    }
                });
//                }
                $('#lbltotalVenta2').html(total);
                $('#lblDescuento').html(descuento);
                $('#lblMaster').html(master);
                $('#lblVisa').html(Visa);
                $('#lblTTarjeta').html(Visa + master);
                $('#lblnPedidosCantidad').html(cantidad);
                $('#lblTotalPedido2').html(tefectivo);

            }

        });
//         $("#tblReporSaleDate1dia").DataTable();
    }
    $("#dateInto").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true});

    function loadTablePedidos2($id) {

        $('#myTabReportSaleDa2te a:last').tab('show');
        var params =
                {comprobante: $id
                };
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ListPedidosItem2", /* Llamamos a tu archivo */
            data: params, /* Ponemos los parametros de ser necesarios */
            type: "GET",
            contentType: "application/x-www-form-urlencoded",
            dataType: "json", /* Esto es lo que indica que la respuesta será un objeto JSon */
            success: function(data) {
                /* Supongamos que #contenido es el tbody de tu tabla */
                /* Inicializamos tu tabla */
                $("#tblPedido2 tbody").empty();
                /* Vemos que la respuesta no este vacía y sea una arreglo */
//                if (data != null && $.isArray(data)) {
                /* Recorremos tu respuesta con each */
                var total = 0;
                $.each(data, function(index, value) {
//                    if (value.totalTarjeta === "null") {
//                        value.totalTarjeta = "0";
//                    }
                    total = total + parseFloat(value.importe);
                    /* Vamos agregando a nuestra tabla las filas necesarias */
                    $("#tblPedido2 tbody").append("<tr><td>" + value.pedido + "</td><td>" + value.precio + "</td><td>" + value.cantidad + "</td><td>" + value.importe + "</td><td>" + value.mensaje + "</td> <td>" + value.pkPedido + "</td></tr>");
                });
                $("#lblTotalPedido2").html(total);
            }

//            }
        });
    }

    function ventasdiariasEXCEL()
    {
        var url = "<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/' . "SRestaurant"; ?>/xls_ReporteVentasDia.php?dateGo=" + $("#dateInto").val() + "&IdSucursal=" + $("#pksucursal").val() + "&estado=" + $("#cmbFilterEstadoVentas").val();
        window.open(url, '_blank');


    }


    function ventasdiariasPDF() {
        var url = "<?php echo Class_config::get('urlApp') ?>/?controller=Report&action=ReportePDFVentasDia&dateGo=" + $("#dateInto").val();
        window.open(url, '_blank');
    }
    
    function EliminaVentaDia(id,pkMesa) {
        $.messager.confirm('Confirmacion', 'Desea Eliminar esta Venta(s)', function(r) {
            if (r) {
                $.post('<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&action=AnulaPedido', {id:id}, function() {
                    var url = "<?php echo Class_config::get('urlApp') ?>/impresionCuenta_<?php echo UserLogin::get_pkSucursal() ?>.php?mesa=" + pkMesa + "&pkPedido=" + id;
                    window.open(url, "", 'width=1,height=1');
                    location.reload();
                }, 'html');
            }
        });
    }
</script>
