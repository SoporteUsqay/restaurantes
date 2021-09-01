<div id="Report_TVenta2" class="easyui-window" data-options="modal:true,closed:true"  title="Reportes Diario de Ventas" style="width:1000px;height:600px;padding:5px;">
    <div class="row">
        <div class="col-lg-6">
            <div class="form-horizontal">
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label" style="color: blue">Fecha </label>
                    <div class="col-sm-8">
                        entro<input class="form-control" type="text" id="dateInto" value="">
                    </div>
                    <div class="col-sm-2">
                        <button class="btn btn-default" onclick="loadTableSale()"><span class="glyphicon glyphicon-search"></span> Buscar</button> 
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">

            <!--<div class="col-sm-2">-->
            <button class="btn btn-default"><span class="glyphicon glyphicon-print"></span> Imprimir</button> 
            <!--</div>-->
            <!--<div class="col-sm-2">-->
            <button class="btn btn-default"><span class="glyphicon glyphicon-print"></span> Imprimir</button> 
            <!--</div>-->
        </div>


    </div>
    <br>
    <!--<center>-->
    <div class="row-border">
        <div class="col-lg-2">
            <br><br> Fecha <?php echo date('Y-m-d') ?>
        </div>
        <div class="col-lg-6">
            <h3  class="text-center">Reporte de Ventas Diarias</h3>
        </div>
        <div class="col-lg-4">
            <br>
            <br>
            Usuario: <?php
            $objUserSystem = new UserLogin();
            echo $objUserSystem->get_names() . " " . $objUserSystem->get_names();
            ?>

        </div>

    </div>
    <br>
    <div class="row-border">
        <div class="col-lg-12">
            <ul class="nav nav-tabs" id="myTabReportSaleDate">
                <li class="active"><a href="#pedidos" data-toggle="tab">Pedidos</a></li>
                <li><a href="#detalle_pedido" data-toggle="tab">Detalles</a></li>
                <!--<li><a href="#messages" data-toggle="tab">Messages</a></li>-->
                <!--<li><a href="#settings" data-toggle="tab">Settings</a></li>-->
            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="pedidos">
                    <table id="tblReporSaleDate" class="easyui-datagrid" title="Ventas" style="width:auto;height:300px"
                           data-options="
                           iconCls: 'icon-search',
                           singleSelect: true,
                           method:'get',
                           pagination:'true',
                           autoRowHeight:false,
                           onDblClickRow:loadTablePedidos,
                           onLoadSuccess:loadTotal

                           ">
                        <thead>
                            <tr>
                                <th data-options="field:'pkComprobante'">Id</th>
                                <th data-options="field:'descripcion',editor:'text'">Mozo</th>
                                <th data-options="field:'nmesa',editor:'text'">Mesa</th>
                                <th data-options="field:'user',editor:'text'" >Usuario</th>
                                <th data-options="field:'tpago',editor:'text'" >Tipo Pago</th>
                                <th data-options="field:'descuento',editor:'text'" >Descuento</th>
                                <th data-options="field:'total_venta',editor:'text'" >Total</th>
                                <th data-options="field:'totalTarjeta',editor:'text'" >Total Tarj</th>
                                <th data-options="field:'tcomprobante',editor:'text'" >T.Comproba</th>
                            </tr>
                        </thead>

                    </table>
                    <div class="row" style="font-size: 20px">
                        <div class="col-md-6">Numero Pagos: <label id="lblnPedidos">0</label></div>
                        <div class="col-md-6"> Total Vendido: <label id="lbltotalVenta">00.00</label></div>
                    </div>
                    <table>
                        <tr>
                            <td>Cant.Tarjeta</td>
                            <td><label id="lblCantPagTarj">0</label></td>
                            <td>Total Tarjeta</td><td><label id="lblTotalPagoTarjeta"></label></td>
                        </tr>
                        <tr>
                            <td>Efectivo</td>
                            <td> <label id="lblTotalEfectivo"></label></td>
                        </tr>
                    </table>
                </div>
                <!--url: '<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ListPedidosItem',-->

                <div class="tab-pane" id="detalle_pedido">
                    <table id="tblPedido" class="easyui-datagrid" title="Listado de Productos" style="width:800px;height:auto"
                           data-options="
                           iconCls: 'icon-edit',
                           singleSelect: true,
                           pagination:true,
                           method:'get',
                           onLoadSuccess:loadTotalPedido,
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
                    </table>

                    <div class="row" style="font-size: 20px">
                        <div class="col-md-6">Numero Detalles: <label id="lblnPedidosDetalle">0</label></div>
                        <div class="col-md-6">Total : <label id="lblTotalPedido">00.00 </label></div>
                    </div>

                </div>

            </div>


        </div>
    </div>

</div>
<script>

    $(function () {
//        $('#tblReporSaleDate').datagrid({data: getData()}).datagrid('clientPaging');
//        $('#tblPedido').datagrid({data: getData()}).datagrid('clientPaging');
    });
    $("#dateInto").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true});
  

    function loadTableSale() {
        $('#tblReporSaleDate').datagrid({data: getData(),
            url: '<?php echo Class_config::get('urlApp') ?>/?controller=Report&&action=ListSaleDate',
            queryParams: {dateGo: $('#dateInto').val()}
        }).datagrid('clientPaging');
    }
    function loadTablePedidos(index, field) {
        var row = $('#tblReporSaleDate').datagrid('getSelected');
        if (row) {
            $('#myTabReportSaleDate a:last').tab('show')
            $('#tblPedido').datagrid('load', {
                comprobante: row.pkComprobante
            });
        }

    }
    function loadTotal() {
        var total = 0;
        var ss = [];
        var rows = $('#tblReporSaleDate').datagrid('getRows');
        var cantBoleta = 0;
        var cantTarjeta = 0;
        var totalTarjeta = 0;
        for (var i = 0; i < rows.length; i++) {
            var row = rows[i];
//            ss.push('<span>' + row.precio + ":" + row.importe + ":" + row.pedido + '</span>');
            total = total + parseFloat(row.total_venta);
            if (row.tpago == "EFECTIVO") {
                cantBoleta++;
            }
            else {
                cantTarjeta++;
                totalTarjeta = totalTarjeta + parseFloat(row.totalTarjeta);
            }
        }
        $("#lblCantPagTarj").html(cantTarjeta);
        $("#lblTotalPagoTarjeta").html(totalTarjeta);
        $("#lblnPedidos").html(rows.length);
        $("#lbltotalVenta").html(total.toFixed(2));
        $("#lblTotalEfectivo").html((total.toFixed(2) - totalTarjeta));
    }
    function loadTotalPedido() {
        var total = 0;
        var ss = [];

        var rows = $('#tblPedido').datagrid('getRows');
        for (var i = 0; i < rows.length; i++) {
            var row = rows[i];
//            ss.push('<span>' + row.precio + ":" + row.importe + ":" + row.pedido + '</span>');
            total = total + parseFloat(row.importe);
            console.log(row.tcomprobante);

        }

        $("#lblnPedidosDetalle").html(rows.length);

        $("#lblTotalPedido").html(total.toFixed(2));
    }
</script>
<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

