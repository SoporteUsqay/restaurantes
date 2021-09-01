<?php include 'Application/Views/template/header.php'; ?>
<body>

    <?php
    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showContent();
    ?>    
    <div class="container">
        <!--<div class="jumbotron">-->

        <br>
        <br>
        <br>   
        <div style="background: #000000;">


            <table class="table" style="color: #ffffff;">
                <tr>
                    <td>
                        <label for="inputPago" class="control-label">Fecha Inicio </label>
                        <input class="form-control" type="text" id="dateInto2Day" value="<?php echo date('Y-m-d', strtotime('-1 month')) ?>">
                    </td>
                    <td>
                        <label for="inputPago"  class="control-label">Fecha Fin</label>
                        <input class="form-control" type="text" id="dateEnd2Day" value="<?php echo date('Y-m-d') ?>">

                    </td>
                    <td>
                        <br>
                        <button class="btn btn-primary" onclick="loadTableSale2day()"><span class="glyphicon glyphicon-search"></span> Buscar</button> 
                        <button class="btn btn-success"><span class="glyphicon glyphicon-print"></span> Imprimir</button> 

                    </td>
                </tr>
            </table>
        </div>
        <ul class="nav nav-tabs" id="myTabReportSale2Date">
            <li class="active"><a href="#tabPedido2Day" data-toggle="tab">Pedidos</a></li>
            <li><a href="#detalle_pedido2day" data-toggle="tab">Detalles</a></li>

        </ul>
    <!--url: '<?php echo Class_config::get('urlApp') ?>/?controller=Report&&action=ListSale2Date',-->

        <div class="tab-content">
            <div class="tab-pane active" id="tabPedido2Day">
                <table id="tblReporSale2Date" class="easyui-datagrid" title="Ventas" style="width:auto;height:auto"
                       data-options="
                       iconCls: 'icon-search',
                       singleSelect: true,
                       method:'get',
                       pagination:'true',
                       autoRowHeight:false,
                       onDblClickRow:loadTablePedidos2day,
                       onLoadSuccess:loadTotal2day,
                       pageSize:10
                       ">
                    <thead>
                        <tr>
                            <th data-options="field:'pkComprobante'">Id</th>
                            <th data-options="field:'nmesa',editor:'text'">Mesa</th>
                            <th data-options="field:'npersonas',editor:'text'">N.Person</th>
                            <th data-options="field:'horaEntrada',editor:'text'">H.Entrada</th>
                            <th data-options="field:'descuento',editor:'text'" >Descuento</th>
                            <th data-options="field:'total_venta',editor:'text'" >Total</th>
                        </tr>
                    </thead>

                </table>
                <div class="row" style="font-size: 20px">
                    <div class="col-md-6" style="color: sienna">Numero pedidos: <label id="lblnPedidos2day" style="color: salmon">0</label></div>
                    <div class="col-md-6" style="color: sienna"> Total Vendido: <label id="lbltotalVenta2day" style="color: salmon">00.00</label></div>
                </div>

            </div>
            <!---->

            <div class="tab-pane" id="detalle_pedido2day">
                <table id="tblPedido2dayDetail" class="easyui-datagrid" title="Listado de Productos" style="width:800px;height:auto"
                       data-options="
                       iconCls: 'icon-edit',
                       singleSelect: true,
                       pagination:true,
                       method:'get',
                       onLoadSuccess:loadTotal2dayPedido,
                       url: '<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ListPedidosItem',
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
                    <div class="col-md-6" style="color: sienna">Numero Detalles: <label id="lblnPedidos2dayDetalle" style="color: salmon">0</label></div>
                    <div class="col-md-6" style="color: sienna"> Total : <label id="lblTotalPedido2day" style="color: salmon">00.00 </label></div>
                </div>
            </div>
        </div>


    </div>

    <script>

        $(function () {
            //        $('#tblReporSale2Date').datagrid({data: getData()}).datagrid('clientPaging');
            //        $('#tblPedido2dayDetail').datagrid({data: getData()}).datagrid('clientPaging');
        });
        $

        function loadTableSale2day() {
            $('#tblReporSale2day').datagrid({
                url: '<?php echo Class_config::get('urlApp') ?>/?controller=Report&&action=ListSale2day',
                queryParams: {dateGo: $('#dateInto2Day').val(),
                    dateEnd: $('#dateEnd2Day').val()
                }
            }).datagrid('clientPaging');


        }

        function loadTablePedidos2day(index, field) {
            var row = $('#tblReporSale2Date').datagrid('getSelected');
            if (row) {
                $('#myTabReportSale2Date a:last').tab('show')
                $('#tblPedido2dayDetail').datagrid('load', {
                    comprobante: row.pkComprobante,
                });
            }

        }
        function loadTotal2day() {
            var total = 0;
            var ss = [];
            var rows = $('#tblReporSale2Date').datagrid('getRows');
            for (var i = 0; i < rows.length; i++) {
                var row = rows[i];
                //            ss.push('<span>' + row.precio + ":" + row.importe + ":" + row.pedido + '</span>');
                total = total + parseFloat(row.total_venta);
            }
            $("#lblnPedidos2day").html(rows.length);
            $("#lbltotalVenta2day").html(total.toFixed(2));
        }
        function loadTotal2dayPedido() {
            var total = 0;
            var ss = [];
            var rows = $('#tblPedido2dayDetail').datagrid('getRows');
            for (var i = 0; i < rows.length; i++) {
                var row = rows[i];
                //            ss.push('<span>' + row.precio + ":" + row.importe + ":" + row.pedido + '</span>');
                total = total + parseFloat(row.importe);
            }
            $("#lblnPedidos2dayDetalle").html(rows.length);
            $("#lblTotalPedido2day").html(total.toFixed(2));
        }
    </script>

