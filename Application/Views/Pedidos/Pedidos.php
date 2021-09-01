<!--<link rel="stylesheet" type="text/css" href="Public/jquery-easyui/easyui/themes/default/easyui.css">-->
<!--<link rel="stylesheet" type="text/css" href="Public/jquery-easyui/easyui/themes/icon.css">-->
<!-- Bootstrap core CSS -->
<!--<link rel="stylesheet" href="Public/jquery-ui-1.10.4.custom/css/jquery-ui.min.css">-->

<!--<link href="Public/Bootstrap/css/bootstrap.min.css" rel="stylesheet">-->
<!--<link rel="stylesheet" type="text/css" href="Public/css/style.css">-->


<!--<script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery-1.10.2.js"></script>
<script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery-ui-1.10.4.custom.js"></script>
<script type="text/javascript" src="Public/jquery-easyui/easyui/jquery.easyui.min.js"></script> -->
<!--<script type="text/javascript" src="Public/jquery-easyui/easyui/datagrid-filter.js"></script>-->
<script type="text/javascript"src="Public/jquery-ui-1.10.4.custom/js/jquery.numeric.js"></script>
<script type="text/javascript"src="Public/jquery-ui-1.10.4.custom/js/jquery.maskedinput.js"></script>
<!--<script type="text/javascript"src="Public/scripts/body.js.php"></script>
<script type="text/javascript"src="Public/scripts/listGeneral.js.php"></script>
<script type="text/javascript"src="Public/scripts/Validation.js.php"></script>
<script  type="text/javascript" src="Public/Bootstrap/js/bootstrap.min.js"></script>-->
<script src="Public/scripts/Pedidos/Mensajes.js.php"></script>

<style>
    .descuento
    .reset
    
</style>
<script>
    (function ($) {
        function pagerFilter(data) {
            if ($.isArray(data)) {    // is array
                data = {
                    total: data.length,
                    rows: data
                }
            }
            var dg = $(this);
            var state = dg.data('datagrid');
            var opts = dg.datagrid('options');
            if (!state.allRows) {
                state.allRows = (data.rows);
            }
            var start = (opts.pageNumber - 1) * parseInt(opts.pageSize);
            var end = start + parseInt(opts.pageSize);
            data.rows = $.extend(true, [], state.allRows.slice(start, end));
            return data;
        }

        var loadDataMethod = $.fn.datagrid.methods.loadData;
        $.extend($.fn.datagrid.methods, {
            clientPaging: function (jq) {
                return jq.each(function () {
                    var dg = $(this);
                    var state = dg.data('datagrid');
                    var opts = state.options;
                    opts.loadFilter = pagerFilter;
                    var onBeforeLoad = opts.onBeforeLoad;
                    opts.onBeforeLoad = function (param) {
                        state.allRows = null;
                        return onBeforeLoad.call(this, param);
                    }
                    dg.datagrid('getPager').pagination({
                        onSelectPage: function (pageNum, pageSize) {
                            opts.pageNumber = pageNum;
                            opts.pageSize = pageSize;
                            $(this).pagination('refresh', {
                                pageNumber: pageNum,
                                pageSize: pageSize
                            });
                            dg.datagrid('loadData', state.allRows);
                        }
                    });
                    $(this).datagrid('loadData', state.data);
                    if (opts.url) {
                        $(this).datagrid('reload');
                    }
                });
            },
            loadData: function (jq, data) {
                jq.each(function () {
                    $(this).data('datagrid').allRows = null;
                });
                return loadDataMethod.call($.fn.datagrid.methods, jq, data);
            },
            getAllRows: function (jq) {
                return jq.data('datagrid').allRows;
            }
        })
    })(jQuery);
    function getData() {
        var rows = [];
        for (var i = 1; i <= 800; i++) {
            var amount = Math.floor(Math.random() * 1000);
            var price = Math.floor(Math.random() * 1000);
            rows.push({
                inv: 'Inv No ' + i,
                date: $.fn.datebox.defaults.formatter(new Date()),
                name: 'Name ' + i,
                amount: amount,
                price: price,
                cost: amount * price,
                note: 'Note ' + i
            });
        }
        return rows;
    }


</script>
<!--<script type="text/javascript" src="Public/jquery-easyui/easyui/jquery.easyui.min.js"></script>--> 
<div id="WindowPedidosRestaurantes"onload="visualizaTipoUsuario()" class="easyui-window" title="Pedidos" data-options="modal:true,closed:true,maximizable:true" style="width: 1325px;height:630px;padding:1px;">

    <!--<div class="row">-->
    <div class="col-lg-6">

        <input style="display: none" id="txtCombrobante" type="text" readonly="true">
        <!--        <div class="panel panel-info">
                    <div class="panel-heading">Pedidos</div>
                    <div class="panel-body">-->


        <div class="row-border" >
            <div class="col-lg-6" style="font-size: 13px">
                <div class="row" >
                    <?php
                    $objUserSystem = new UserLogin();
                    ?>
                    <div class="letra col-lg-4">
                        Cajero :
                    </div>
                    <div class="col-lg-8">
                        <input readonly="true" class="form-control" value="<?php echo $objUserSystem->get_lastnames() . " " . $objUserSystem->get_names() ?>">
                    </div>

                </div>
                <br>
                <div class="row">
                    <div class="letra col-lg-4">
                        Mozo :
                    </div>
                    <div class="col-lg-8">
                        <input readonly="true" id="txtNameMozo" class="form-control">
                    </div>

                </div>
            </div>
            <div class="col-lg-6">
                <div class="row">
                    <div class="letra col-lg-6">
                        Numero de Mesa
                    </div>
                    <div class="letra col-lg-6">
                        <label id="lblnMesa" class="total "
                        <?php // echo $objPedido->getNMesa()    ?>
                    </label>
                </div>

            </div>

            <div class="row">
                <div class="letra col-lg-6">
                    N° Personas
                </div>
                <div class="col-lg-6">

                    <input title="Cantidad de Personas Ocupando la mesa" id="txtnPesonas"  onblur="updateNPersonas()"  class="form-control easyui-tooltip numerico" data-toggle="tooltip" data-placement="top" type="text" maxlength="2">
                </div>

            </div>
            <div class="row">
                <div class="letra col-lg-6">
                    <button title="Elegir un mensaje para el pedido que seleccione"  onclick="showMessages()" class="btn btn-default easyui-tooltip" data-placement="bottom"><span class="glyphicon glyphicon-envelope"></span> Mensaje</button>

                </div>
                <div class="col-lg-6">
                    <button title="Eliminar" class="btn btn-default"><span class="glyphicon glyphicon-trash"></span> Quitar</button>

                </div>

            </div>
        </div>
    </div>


    <!--FIN DE ROW-->
    <table id="tblcomprobante" class="easyui-datagrid" title="Listado de Pedidos" style="width:max-content;height:200px"
           data-options="
           iconCls: 'icon-edit',
           singleSelect: true,
           url: '<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ListPedidosItem',
           method:'get',
           fitColumns: true,
           collapsible: true,
           onClickCell: onClickCell,
           onLoadSuccess:sumaTotal,
           onClickRow:loadMessage,
           onAfterEdit:saveChanges
           ">
        <thead>
            <tr>
                <th data-options="field:'ck',checkbox:true,cheked:true"></th>
                <th data-options="field:'pedido',width:250,editor:'text'">Pedido</th>
                <th data-options="field:'precio'">Precio</th>
                <th data-options="field:'cantidad',align:'right',editor:{type:'numberbox',options:{precision:1}}">Cantidad</th>
                <th data-options="field:'importe',align:'right',editor:{type:'numberbox',options:{precision:2}}">Importe</th>
                <th data-options="field:'mensaje',align:'right',hidden:'true'">Mensaje</th>
                <th data-options="field:'pkPedido',align:'right',hidden:'true'">Id</th>
                <th data-options="field:'Destado',align:'right',styler:cellStylerPedido">Estado</th>
                <th data-options="field:'estado',align:'right',hidden:'true'">Estado</th>
                <th data-options="field:'mozo',align:'right',hidden:'true'">Id</th>
<!--                                    <th data-options="field:'attr1',width:250,editor:'text'">Attribute</th>
                <th data-options="field:'status',width:60,align:'center',editor:{type:'checkbox',options:{on:'P',off:''}}">Status</th>-->
            </tr>
        </thead>
    </table>
    <div class="row-border">

        <div class="col-lg-7">
            <?php
            switch (UserLogin::get_pkTypeUsernames()) {
                case 3:
                case 4:
//                    echo"ento";
                case 5:
                case 6:
                    break;

                default :

                    echo '<button class="botonesIcon btn btn-success" title="Cancela en efecctivo el pedido de la mesa" onclick="openModal()"><span class="glyphicon glyphicon-saved" ></span> Cancelar</button>' .
                    '<button class="botonesIcon  btn btn-success" onclick="_anulaPedido(\'tblcomprobante \')" title="Anula un item que el cliente ya no desea llevar o consumir"><span class="glyphicon glyphicon-remove"></span> Anulacion</button>' .
                    '<button class="botonesIcon  btn btn-success"  title="Cancelar con tarjeta o efectivo la cuenta" onclick="openModalCancelCuentaTarjeta()"><span class="glyphicon glyphicon-credit-card"></span> Tarjeta</button>' .
                    '<button class="botonesIcon  btn btn-success" onclick="openModalGenerarComprobante()"  title="Generar comprobante - Boleta"><span class="glyphicon glyphicon-file"></span>Boleta</button>' .
                    '<button class="botonesIcon btn btn-success" onclick="openModalGenerarComprobanteFactura()"><span class="glyphicon glyphicon-file"></span> Factura</button>';

                    break;
            }
            ?>
            <button class="botonesIcon  btn btn-success" title="Imprimir la cuenta" onclick="openImprimirCuenta()" ><span class="glyphicon glyphicon-usd"></span>Pre-Cuenta</button>
            <button class="botonesIcon  btn btn-success" title="Recarga los pedidos registrados" onclick="loadPedido($('#txtMesaApertura').val())"><span class="glyphicon glyphicon-refresh"></span> Recargar</button>
            <button class="botonesIcon  btn btn-success" onclick="openModalChangeMesa()" title="Cambiar el pedido a otra mesa desocupada"><span class="glyphicon glyphicon-warning-sign"></span> Cambio de mesa</button>
            <button class="botonesIcon btn btn-success" onclick="confirImpresion(1)"><span class="glyphicon glyphicon-print"></span> Imprimir cocina</button>
            <button class="botonesIcon btn btn-success" onclick="confirImpresion(2)"><span class="glyphicon glyphicon-print"></span> Imprimir Cantina</button>
            <button class="botonesIcon btn btn-success" onclick="confirmCancelaMesa()"><span class="glyphicon glyphicon-remove"></span> Cerrar Mesa</button>
            <button class="botonesIcon btn btn-success" onclick="$('#w').dialog('open')"><span class="glyphicon glyphicon-file"></span> Pagar al credito</button>

        </div>
        <div class="col-lg-5">
            <div class="row">
                <div class="col-lg-12">
                    <label>Mensaje:</label>
                    <textarea id="TxtMensaje" class="form-control">
                                            
                    </textarea> 
                </div>
            </div>


            <center>
                <label>Sub Total:</label><label id="lblSubTotal"> 00.00</label> <br>
                <label>Igv:</label> <label id="lblIgv">00.00</label><br>
                <label>Total:</label><br>
                S/.<label class="total" id="lblTotal">00.00</label><br>

                <div class="form-group descuento">
                    <label class="descuento" for="descuento">Descuento:</label>
                    <input  name="descuento"  onchange="sumaTotal()" class="form-control numerico descuento" id="txtDescuento" type="text" value="0">

                </div>


            </center>
        </div>
    </div>
    <!--        </div>
        </div>-->
    <!--</div>-->
    <!--fin dE PEDIDOS-->
</div>
<div class="col-lg-6">
    <!--<div id="p" class="easyui-panel" title="Buscar Productos" style="height:750px;padding:10px;" >-->

    <div class="row">
        <div class="col-lg-7">
<!--url: '<?php // echo Class_config::get('urlApp')                                                ?>/?controller=Pedidos&&action=ListAllProduct',-->

            <script type="text/javascript" src="Public/jquery-easyui/easyui/datagrid-filter.js"></script>
            <table id="tblProductos" class="easyui-datagrid" title="Listado de Todo los productos" style="width:auto;height:590px;font-size: 12px"
                   data-options="
                   iconCls: 'icon-search',
                   singleSelect: true, 
                   method:'get',
                   pagination:true,
                   pageSize:50,
                   onClickRow:mostraralerta,
                   ">
                <thead>
                    <tr>
                        <th data-options="field:'id',hidden:'true'">Id</th>
                        <th data-options="field:'descripcion',editor:'text'">Producto</th>
                        <th data-options="field:'precio',editor:'text'" >Precio</th>
                        <th data-options="field:'tpedido',editor:'text',hidden:'true'" >Precio</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="col-lg-5">
            <div class="panel panel-info">
                <div class="panel-heading">Categorias</div>
                <div class="panel-body">
                    <div class="btn-group">
                        <?php
                        $db = new SuperDataBase();
                        $sucursal = UserLogin::get_pkSucursal();
                        $query = "SELECT  distinct(c.pkCategoria),c.descripcion  FROM plato_sucursal p inner join (tipos t inner join categoria c on c.pkCategoria=t.pkCategoria) on t.pkTipo=p.pkTipo where p.pkSucursal='$sucursal' group by t.pkTipo
                                        union SELECT  distinct(c.pkCategoria),c.descripcion  FROM producto_sucursal p inner join (tipos t inner join categoria c on c.pkCategoria=t.pkCategoria) on t.pkTipo=p.pkTipo where p.pkSucursal='$sucursal' group by t.pkTipo;
";
                        $resul = $db->executeQuery($query);
                        while ($row = $db->fecth_array($resul)) {
                            echo '<div class="btn-group btn-group-lg">';
                            echo '<button class="btn btn-default" onclick="javascript:loadItemsTipoPlato(' . $row['pkCategoria'] . ')">' . $row['descripcion'] . '</button>';
                            echo'</div>';
//                                                    $conta++;
                            echo "<br>";
                        }
                        ?>

                    </div>
                </div>

            </div>

            <!--</div>-->
            <!--</div>-->
            <div class="panel panel-info">
                <div class="panel-heading">Tipos</div>
                <div class="panel-body">


                    <div class="btn-group btn-group-lg">
                        <div id="contenTipoPlato">


                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
</div> 

<div buttons="#ft" id="ModalCantidadPedir" title="Cantidad de pedidos" class="easyui-dialog" data-options="modal:true,closed:true"   style="width:450px;height:230px;padding:1px;">
    <center><h4 class="modal-title" id="TitleCantidadPedir"></h4></center> 
    <!--<div id="ModalCantidadPedir" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">-->
    <!--<div class="row">-->
    <div class="col-lg-5">
        <input id="txtPkPedido" style="display: none">
        <input id="txtPrecioSend" style="display: none">
        <input id="txtTipoSend" style="display: none">

        <center>  <input min="0"  id="txtCantidadSend" style="width: 80px;height: 80px;text-align: center; font-size: 15px" class="form-control numerico" value="1" type="number"> </center>
    </div>
    <div class="col-lg-5">
        <button class="btn btn-default btn-lg" onclick="sumNumber(1, 'txtCantidadSend')"><span class="glyphicon glyphicon-plus"></span></button><br>
        <button class="btn btn-default btn-lg" onclick="minNumber(1, 'txtCantidadSend')"><span class="glyphicon glyphicon-minus"></span></button>
        <script>

            $("#txtCantidadSend").numeric({negative: false});
            function sumNumber($valor, $id) {
                $valor = parseInt($valor);
                var $total = parseInt($('#' + $id).val());
                $('#' + $id).val(($total + $valor));
            }
            function minNumber($valor, $id) {

                $valor = parseInt($valor);
                var $total = parseInt($('#' + $id).val());
                if ($total > 1) {

                    $('#' + $id).val(($total - $valor));
                }
            }
        </script>
        <!--</div>-->
    </div>
</div>
<div id="ft" style="padding:5px;">
    <button type="button" class="btn btn-default" onclick="$('#ModalCantidadPedir').window('close')">Cancelar</button>
    <button type="button" class="btn btn-primary" onclick="addPedido()" >Realizar el Pedido</button>
</div>
<!--PK MESA-->
<input id="TxtPkMesa" style="display: none">

<div id="ModalChangeMesa" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="lblTitleCambioMesa"></h4>
            </div>
            <div class="modal-body">
                <div>
                    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                        <?php
//                        $db = new SuperDataBase();
//                        $query = "SELECT * FROM salon s;";
//                        $resultSalones = $db->executeQuery($query);
//                        while ($rowSalones = $db->fecth_array($resultSalones)) {
//                            echo '<div class="panel panel-default">';
//                            echo ' <div class="panel-heading" role="tab" id="' . $rowSalones["pkSalon"] . '">';
//                            echo '<h4 class="panel-title">';
//                            echo' <a data-toggle="collapse" data-parent="#accordion" href="#' . $rowSalones["pkSalon"] . '" aria-expanded="true" aria-controls="' . $rowSalones["pkSalon"] . '">';
//                            echo $rowSalones['nombre'];
//                            echo'</a>';
//                            echo "</h4>";
//
//                            echo'</div>';
//                            echo '<div id="' . $rowSalones["pkSalon"] . '" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">';
////                            echo $_GET['mesa'];
////                        $query = "SELECT * FROM mesas m  where pkSalon=" . $rowSalones['pkSalon'] . " order by nmesa;";
//                           echo '<div class="panel-body">';
//                            $query = "SELECT * FROM mesas m  where pkMesa<>" . $_GET['mesa'] . " and pkSalon=" . $rowSalones['pkSalon'] . " order by nmesa;";
//
//                            $resultMesas = $db->executeQuery($query);
//
//                            echo '<div class="row">';
//                            $contaMesasOcupadas = 0;
//                            while ($rowMesas = $db->fecth_array($resultMesas)) {
//
//                                if ($rowMesas['estado'] == "1") {
//                                    echo '<div class="col-md-2">';
//                                    echo'<a href="?controller=Pedidos&&action=ShowPedidos&mesa=' . $rowMesas['pkMesa'] . '"  class="mesas btn btn-lg btn-danger"><br>' . $rowMesas['nmesa'] . '</a><br>';
//                                } else {
//                                    //Mesas Disponibles
//                                    echo '<div class="col-md-2">';
////                                    echo'<a  href="?controller=Pedidos&&action=ShowPedidos&mesa=' . $rowMesas['pkMesa'] . '"  class="mesas btn btn-lg btn-success"><br>' . $rowMesas['nmesa'] . '</a><br>';
//                                    echo'<button onclick="msfConfirmar(\'' . $rowMesas['pkMesa'] . '\', \'' . $rowMesas['nmesa'] . '\')" class="mesas btn btn-lg btn-success"><br>' . $rowMesas['nmesa'] . '</button><br>';
//                                }
////                                if ($conta % 6 == 0) {
//                                echo '<br>';
////                                }
//                                echo '</div>';
////                                $conta++;
//                            }
//                            echo '</div>'; /* fin de row */
//                            echo'</div>';
//                            echo'</div>';
//                            echo'</div>';
//                            echo'</div>';
//                            echo'</div>';
//                        }
                        ?>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- /container -->

<!--    <div id="footer">
        <div class="container">

            Salir
            Mesas Ocupadas
            Fecha
            Hora
            <p class="text-muted">Place sticky footer content here.</p>
        </div>
    </div>-->
<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->


<style>
    .buttonsMessges {
        width: 170px;height: 50px;
        font-weight: bold;
        font-size: 18px;
    }
</style>

<!--Modal para ver los mensajes a los pedidos-->
<!--<div id="modalMensaje" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidde="true">-->
<div id="modalMensaje" class="easyui-dialog" data-options="modal:true,closed:true" title="Mensaje" style="width:900px;height:450px;padding:5px">
    <!--<div class="modal-dialog modal-lg">-->
    <!--<div class="modal-content">-->

    <!--<div class="modal-content">-->

    <!--<div class="row">-->
    <div class="col-lg-8">
        <input style="display: none" id="txtIdPedido">

        <?php
        $db = new SuperDataBase();
        $query = "SELECT * FROM mensaje m;";
        $result = $db->executeQuery($query);
        $array = array();
        $contado = 1;
        while ($row = $db->fecth_array($result)) {
            echo '<button class="buttonsMessges" onclick="saveMessagePedido(\'' . utf8_encode($row['descripcion']) . ' \')">' . utf8_encode($row['descripcion']) . '</button>';
            if ($contado % 3 == 0)
                echo "<br>";
            $contado++;
        }
        ?>
    </div>
    <div class="col-lg-4">
        Nuevo Mensaje
        <textarea  class="form-control" id="textAreaMessage">
             
        </textarea>
        <button onclick="saveMessagePedido($('#textAreaMessage').val())" class="btn btn-default btn-group-lg"><span class="glyphicon glyphicon-ok">Guardar</span></button>
    </div>
    <!--</div>-->
    <!--</div>-->
    <!--</div>-->
    <!--</div>-->
</div>

<!--Modal para cancelar la compra-->
<!--<div id="modalCancelCuenta" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">-->
<div id="modalCancelCuenta" class="easyui-dialog" data-options="modal:true,closed:true, buttons:'#dlg-buttonsCancelarCuenta'" title="Cancelar Cuenta" style="width:500px;height:280px;padding:5px">

    <!--<div class="modal-dialog">-->
    <!--        <div class="modal-content">-->

    <!--<div class="modal-content">-->
    <div class="panel panel-default">

        <div class="panel-body">
            <div class="row">
                <div class="col-lg-4">
                    <span style="font-size: 15px">Pago:</span> 
                </div>
                <div class="col-lg-4">
                    <input type="text" class="form-control numerico " id="inputPago" placeholder="00.00">

                </div>

            </div>
            <div class="row">
                <div class="col-lg-4">
                    <span style="font-size: 15px">Total:</span>
                </div>
                <div class="col-lg-4">
                    <label  class="reset" style="font-size: 15px"   id="lblTotalCancel">00.00</label>

                </div>

            </div>
            <div class="row">
                <div class="col-lg-4">
                    <span style="font-size: 15px">  Vuelto:</span>
                </div>
                <div class="col-lg-4">

                    <label class="reset" style="font-size: 15px" id="lblVuelto">00.00</label>

                </div>

            </div>
        </div>
    </div>

    <!--</div>-->

    <!--</div>-->

    <!--</div>-->
</div>
<div id="dlg-buttonsCancelarCuenta">
    <div class="alert alert-danger fade in" role="alert">
        Presione 3 Veces Enter
    </div>
</div>
<script>

    var contador = 0;
    $('#inputPago').keypress(function (event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
            contador++;
            switch (contador) {
                case 1:
                    calculaVuelto();
                    break;
                case 2:
                    break;
                case 3:
                    CancelaPedido();
                    contador = 0;
                    break;
            }
        }
    });
</script>
<!--Modal para cancelar la compra con tarjeta-->
<!--<div id="modalCancelCuentaCTarjeta" class="modal fade" tabindex="-1" role="dialog"  >-->
<div id="modalCancelCuentaCTarjeta" class="easyui-dialog" data-options="modal:true,closed:true, buttons:'#dlg-buttonsCancelarCuentaConTarjeta'" title="Pago con Tarjeta" style="width:600px;height:550px;padding:5px">

    <!--<div class="modal-dialog">-->
    <!--<div class="modal-content">-->

    <!--<div class="modal-content" style="padding: 25px">-->
    <table style="font-size: 25px;width: 500px;height: 400px"   border="0">
        <tr>
            <td>
                Total: 
            </td>
            <td> <label  class="col-sm-2 control-label" id="lblTotalCancelCuenta">00.00</label></td>
        </tr>
        <tr>
            <td>Tarjeta:</td>
            <td> <select type="text" class="form-control" id="cmbTipoTarjeta" >
                    <option>VISA</option>
                    <option>MASTERCARD</option>
                </select>
            </td>
        </tr>

        <tr>
            <td> <input id="chkpagoEfectivoTarjeta" onclick="checkedTarjeta()" type="checkbox"> Monto de Tarjeta: </td>
            <td>
                <input id="txtMontoCTarjeta" onkeyup="restarNumeros()" class="form-control numerico" disabled="true"> 
            </td>
        </tr>
        <tr>
            <td>
                Moto en efectivo: 
            </td>
            <td>
                <label class="col-sm-2 control-label reset" id="lblMontoEfectivo">00.00</label>
            </td>
        </tr>
    </table>

    <!--</div>-->
    <!--</div>-->

    <!--</div>-->

    <!--</div>-->
</div>
<div id="dlg-buttonsCancelarCuentaConTarjeta">
    <!--<label class="text-danger">Presione Enter</label>-->
    <button class="btn btn-default" onclick="CancelaPedidoConTarjeta()">Aceptar</button>
</div>
<!--<div id="modalCancelGnerarComprobante" class="modal fade" tabindex="-1" role="dialog"  >-->
<div id="modalCancelGnerarComprobante" class="easyui-dialog" data-options="modal:true,closed:true, buttons:'#dlg-buttonsGenerarComprobante'" title="Generando Comprobante - Boleta" style="width:600px;height:620px;padding:5px">

    <!--<div class="modal-dialog">-->
    <!--<div class="modal-content">-->

    <!--<div class="modal-content" style="padding: 20px">-->
    <form id="frmPagoBoleta">
        <div class="row" style="font-size: 25px;">
            <div class="col-sm-4">
                Total:
            </div>
            <div class="col-sm-8">
                <label  class="col-sm-2 control-label reset"  id="lblTotalCancelCuentaBoleta">00.00</label>
            </div>
            <br>
            <div class="col-sm-4">
                Tipo de pago
            </div>
            <div class="col-sm-8">
                <div class="radio">
                    <label><input type="radio" name="tipoPago1" value="1" checked="true" onclick="checkEfectivoTarjeta('tipoPago1', 'divTarjetasBoleta')">Efectivo</label>
                    <label><input type="radio"  name="tipoPago1" value="2" onclick="checkEfectivoTarjeta('tipoPago1', 'divTarjetasBoleta')">Con Tarjeta</label>
                </div>

            </div>
            <div class="col-sm-4">
                Cliente
            </div>
            <div class="col-sm-8">
                <input class="form-control reset" id="txtDniCliente" name="dni" placeholder="Ingrese Su DNI- Presione 'Enter para iniciar la busqueda'">
            </div>
            <div class="col-sm-8">
                <input id="txtNombres" class="form-control reset"name="nombres" placeholder="Ingrese el nombre del cliente">
            </div>
            <div class="col-sm-4">

            </div>
            <div class="col-sm-8">
                <input class="form-control reset"name="apellidos" id="txtApellidos" placeholder="Ingrese el apellido del cliente">
            </div>
            <div class="col-lg-4">
                Moto en efectivo: 
            </div>
            <div class="col-sm-8">
                <!--<br>-->
                <input id="txtMontoEfectivo" name="total_efectivo" class="form-control numerico" style="height: 80px;width: 120px">
            </div>
            <div class="col-lg-4">
                Vuelto: 
            </div>
            <div class="col-sm-8">
                <input class="form-control numerico" id="txtvueltoBoleta" style="height: 80px;width: 120px">
            </div>
        </div>
        <div id="divTarjetasBoleta" hidden="true">
            <div class="row" style="font-size: 25px;">
                <div class="col-sm-4">
                    Tarjeta:
                </div>
                <div class="col-sm-8">
                    <select name="nombreTarjeta"  type="text" class="form-control" id="cmbTipoTarjeta" >
                        <option>VISA</option>
                        <option>MASTERCARD</option>
                    </select>
                </div>
            </div>
            <div class="row" style="font-size: 25px;">
                <div class="col-sm-4">
                    <input id="chkpagoEfectivoTarjetaBoleta" onclick="checkedTarjeta2('chkpagoEfectivoTarjetaBoleta', 'txtMontoCTarjetaBoleta')" type="checkbox"> Monto de Tarjeta: 
                </div>
                <div class="col-sm-6">
                    <input id="txtMontoCTarjetaBoleta" onkeyup="restarNumeros2('lblTotalCancelCuentaBoleta', 'txtMontoEfectivo', 'txtMontoCTarjetaBoleta')" class="form-control numerico" disabled="true"> 
                </div>
            </div>
        </div>
    </form>
    <!--    <div id="dlg-buttonsGenerarComprobante">
            <button class="btn btn-default" onclick="CancelaPedidoComprobante('frmPagoBoleta', 1)">Aceptar</button>
        </div>-->
</div>
<div id="dlg-buttonsGenerarComprobante">
    <div class="alert alert-danger fade in" role="alert">
        Presione 3 Veces Enter
    </div>
</div>
<script>

    var contador = 0;
    $('#txtMontoEfectivo').keypress(function (event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
            contador++;
            switch (contador) {
                case 1:
                    calculaVuelto();
                    break;
                case 2:
                    break;
                case 3:
                    CancelaPedidoComprobante('frmPagoBoleta', "1");
                    contador = 0;
                    break;
            }
        }
    });</script>
<!--<div id="modalCancelGnerarComprobanteFactura" class="modal fade" tabindex="-1" role="dialog"  >-->
<div id="modalCancelGnerarComprobanteFactura" class="easyui-dialog" data-options="modal:true,closed:true, buttons:'#dlg-buttonsGenerarComprobanteFactura'" title="Generando Comprobante - Factura" style="width:600px;height:620px;padding:5px">

    <!--<div class="modal-dialog">-->
    <!--<div class="modal-content">-->

    <!--<div class="modal-content" style="padding: 20px">-->
    <form id="frmPagoFactura">
        <div class="row" style="font-size: 25px;">
            <div class="col-sm-4">
                Total:
            </div>
            <div class="col-sm-8">
                <label  class="col-sm-2 control-label reset" id="lblTotalCancelCuentaFactura">00.00</label>
            </div>
            <br>
            <div class="col-sm-4">
                Tipo de pago
            </div>
            <div class="col-sm-8">
                <div class="radio">
                    <label><input type="radio" name="tipoPago" value="1" checked="true" onclick="checkEfectivoTarjeta('tipoPago', 'divTarjetasFactura')">Efectivo</label>
                    <label><input type="radio" name="tipoPago" value="2" onclick="checkEfectivoTarjeta('tipoPago', 'divTarjetasFactura')">Con Tarjeta</label>
                </div>

            </div>
            <div class="col-sm-4">
                Cliente
            </div>
            <div class="col-sm-8">
                <input id='txtRucFactura' name="ruc" class="form-control numerico" placeholder="Ingrese Su Ruc- Presione 'Enter para iniciar la busqueda'">
            </div>
            <div class="col-sm-8">
                <input class="form-control" name="razonSocial" id="txtRazonSocialFactura" placeholder="Ingrese la Razon Social">
            </div>
            <div class="col-sm-4">

            </div>
            <div class="col-sm-8">
                <input class="form-control"name="direccion" id="txtDireccionFactura" placeholder="Ingrese la Dirección">
            </div>
            <div class="col-sm-8">
                <input id='txtCorreo' name="correo" class="form-control numerico" placeholder="Ingrese Su Correo Electronico'">
            </div>
            <div class="col-lg-4">
                Moto en efectivo: 
            </div>

            <div class="col-sm-8">
                <!--<br>-->
                <input id="txtMontoEfectivoFactura" name="total_efectivo" class="form-control" style="height: 80px;width: 120px">
            </div>
            <div class="col-lg-4">
                Vuelto: 
            </div>
            <div class="col-sm-8">
                <!--<br>-->
                <input class="form-control"  id="txtvueltoFactura" style="height: 80px;width: 120px">
            </div>
        </div>
        <div id="divTarjetasFactura" hidden="true">
            <div class="row" style="font-size: 25px;">
                <div class="col-sm-4">
                    Tarjeta:
                </div>
                <div class="col-sm-8">
                    <select name="nombreTarjeta" type="text" class="form-control" id="cmbTipoTarjeta" >
                        <option>VISA</option>
                        <option>MASTERCARD</option>
                    </select>
                </div>
            </div>
            <div class="row" style="font-size: 25px;">
                <div class="col-sm-4">
                    <input id="chkpagoEfectivoTarjetaFactura" onclick="checkedTarjeta2('chkpagoEfectivoTarjetaFactura', 'txtMontoCTarjeaFactura')" type="checkbox"> Monto de Tarjeta: 
                </div>
                <div class="col-sm-6">
                    <input id="txtMontoCTarjeaFactura" name="total_tarjeta" onkeyup="restarNumeros2('lblTotalCancelCuentaFactura', 'txtMontoEfectivoFactura', 'txtMontoCTarjeaFactura')" class="form-control" disabled="true"> 

                </div>

                <!--<button ></button>-->

            </div>
        </div>

    </form>
    <!--</div>-->


    <!--</div>-->
    <!--    <div id="dlg-buttonsGenerarComprobanteFactura">
            <label class="text-danger">Presione Enter</label>
            <button type="button" class="btn btn-default" onclick="CancelaPedidoComprobante('frmPagoFactura', 2)">Aceptar</button>
        </div>-->
    <!--</div>-->

    <!--</div>-->
</div>
<div id="dlg-buttonsGenerarComprobanteFactura">
    <div class="alert alert-danger fade in" role="alert">
        Presione 3 Veces Enter
    </div>
</div>
<script>

    var contador = 0;
    $('#txtMontoEfectivoFactura').keypress(function (event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
            contador++;
            switch (contador) {
                case 1:
                    calculaVuelto();
                    break;
                case 2:
                    break;
                case 3:
                    CancelaPedidoComprobante('frmPagoFactura', "2");
                    contador = 0;
                    break;
            }
        }
    });</script>
<div id="w" class="easyui-dialog" title="Pagando a credito" data-options=" modal:true,closed:true,buttons:'#dlg-buttonsCreditoCuenta'" style="width:600px;height:350px;padding:5px;">
    <form class="form-horizontal">
        <div class="form-group">
            <label for="tipo_cliente" class="col-sm-2 control-label">Tipo de cliente</label>
            <div class="col-sm-8">
                <select  class="form-control" id="cmbTipoClienteCredito" name="tipo_cliente" onchange="onSelectTipoCliente($('#cmbTipoClienteCredito').val())">
                    <option value="2">
                        Juridico
                    </option>
                    <option value="1">
                        Natural
                    </option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label id="lbldocredito" for="ruc" class="col-sm-2 control-label">Documento</label>
            <div class="col-sm-10">
                <input id='txtDocumentoPC' name="ruc" class="form-control" placeholder="Ingrese Su documento- Presione 'Enter para iniciar la busqueda'">

            </div>
        </div>
        <div class="form-group">
            <label id="lblrcredito" for="razonSocial" class="col-sm-2 control-label">Razon Social</label>
            <div class="col-sm-10">
                <input class="form-control"  name="razonSocial" id="txtValor1PC" placeholder="">
            </div>
        </div>
        <div class="form-group">
            <label id="lbldcredito" for="direccion" class="col-sm-2 control-label">Direccion</label>
            <div class="col-sm-10">
                <input class="form-control" name="direccion" id="txtValor2PC" placeholder="">
            </div>
        </div>
    </form>
    <div id="dlg-buttonsCreditoCuenta">
        <!--<label class="text-danger">Presione Enter</label>-->
        <button type="button" class="btn btn-default" onclick="cancelaPedidoCredito()">Aceptar</button>
    </div>
</div>
<script>
    $('#txtDocumentoPC').keypress(function (event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
            if ($('#cmbTipoClienteCredito').val() === "2") {
                searchCustomerRuc($('#txtDocumentoPC').val(), "txtValor1PC", "txtValor2PC");
            }
            else
                searchCustomerDNI($('#txtDocumentoPC').val(), "txtValor1PC", "txtValor2PC");
        }
    });
    function onSelectTipoCliente($value) {
        if ($value === "1") {
            $('#lbldocredito').html("DNI");
            $('#lblrcredito').html("Nombbres");
            $('#lbldcredito').html("Apellidos");

        }
        else {
            $('#lbldocredito').html("Documento");
            $('#lblrcredito').html("Razon Social");
            $('#lbldcredito').html("Direccion");

        }
    }
    $(function () {

    });
    $(".numerico").numeric({negative: false});
//    $('#txtDescuento').numeric({negative: false});
    $('#txtMontoEfectivoFactura').keypress(function (event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
//            contador++;
//            switch (contador) {
//                case 1:
            calculaVuelto2('txtMontoEfectivoFactura', 'lblTotal', 'txtvueltoFactura');
//                    break;
//                case 2:
//                    break;
//                case 3:
//                    CancelaPedido();
//                    contador = 0;
//                    break;
//            }
        }
    });
    $('#txtMontoEfectivo').keypress(function (event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
//            contador++;
//            switch (contador) {
//                case 1:
            calculaVuelto2('txtMontoEfectivo', 'lblTotal', 'txtvueltoBoleta');
//                    break;
//                case 2:
//                    break;
//                case 3:
//                    CancelaPedido();
//                    contador = 0;
//                    break;
//            }
        }
    });
    /**
     * Cargar un pedido
     * */
    function loadPedido($mesa) {
        var param = {'mesa': $mesa};
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ListPedidos",
            type: 'GET',
            data: param,
            beforeSend: function (xhr) {
                xhr.overrideMimeType("text/plain; charset=x-user-defined");
                $.messager.progress({
                    title: 'Por Favor Espere',
                    msg: 'Cargando informacion...'
                });
            },
            dataType: 'json',
            success: function (data) {
                $.messager.progress('close');
                for (var i = 0; i < data.length; i++) {
                    $("#lblnMesa").html(data[i].nmesa);
                    $("#txtnPesonas").val(data[i].npersonas);
                    $("#TxtPkMesa").val(data[i].pkMesa);
//                    $("#txtNameMozo").val(data[i].mozo);
                    $("#txtCombrobante").val(data[i].nComprobante);
                    $("#txtDescuento").val(data[i].descuento);
//                                        $('<button class"btn btn-default btn-group-lg" onclick="doSearchTipo(' + data[i].pkTipoPlato + ')"> <br>' + data[i].descripcion + '</button>').appendTo($div);
                }
                loadDetalles();
            }

        });
    }

    function _anulaPedido($nameGrid) {
        var row = $('#' + $nameGrid).datagrid('getSelected');
        if (row) {
//            $('#alerta').alert();
            console.log(row.cantidad);
            console.log(row.pkPedido);
//           // $_POST['pkDetallepedido'], $_POST['fkPedido'], $_POST['tipo'], $_POST['cantidad'
//             var param = {'pkDetallepedido': <?php // echo $_GET['pkDetallepedido']                                                                                             ?>};
//             var param = {'fkPedido': <?php // echo $_GET['fkPedido']                                                                                             ?>};
//             var param = {'tipo': <?php // echo $_GET['tipo']                                                                                             ?>};
//             var param = {'cantidad': <?php // echo $_GET['cantidad']                                                                                             ?>};
            // anulamos el pedido y traemos por ajax que se muetre los pedidos actualizados      
            var param = {'pkDetallepedido': row.pkPedido, tipo: 1};
            $.ajax({
                url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=DeletePedido",
                type: 'GET',
                data: param,
                dataType: 'html',
                success: function (data) {

                    loadDetalles()

                            ;
                }
            });
        }
        else {
            $.messager.alert('Error', '¡Debe Seleecionar un Item del Listado de Pedidos!', 'error');
        }
    }

    //guarda el mensaje 
    function _saveMensaje() {
        var param = {'descripcion': $("#textAreaMessage").val()};
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Mensaje&&action=Save",
            type: 'POST',
            data: param,
            cache: true,
            dataType: 'json',
            success: function (data) {
//                $('#msgSuccesRegister').alert();
                console.log(data);
            }
        });
//        $('#tblRecepcionDocumentos').datagrid('load');

    }
    function openImprimirCuenta() {
        var url = "<?php echo Class_config::get('urlApp') ?>/impresionCuenta_" + "<?php echo UserLogin::get_pkSucursal() ?>" + ".php?mesa=" + $("#txtMesaApertura").val() + "&pkPedido=" + $("#txtCombrobante").val();

        var myWindow = window.open(url, "", 'width=300,height=500');
//        myWindow.document.write("<div");
        myWindow.focus();




    }

    function checkEfectivoTarjeta($id, $idDiv) {
        var value2 = $('input:radio[name=' + $id + ']:checked').val();
        console.log(value2);
        if (value2 === "1") {
            $('#' + $idDiv).hide();
        }
        else {
            $('#' + $idDiv).show();
        }
    }
    nRuc('txtRucFactura');
    $('#txtRucFactura').keypress(function (event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
            console.log("presiono enteeer");
            searchCustomerRuc($('#txtRucFactura').val(), "txtRazonSocialFactura", "txtDireccionFactura");
//            $("#txtRazonSocialCaja").val(data[0].companyName);
//                $("#txtDireccionCaja").val(data[0].address);
        }
    });
    $('#txtDniCliente').keypress(function (event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
//            console.log("presiono enteeer");
            searchCustomerDNI($('#txtDniCliente').val(), "txtNombres", "txtApellidos");
//            $("#txtRazonSocialCaja").val(data[0].companyName);
//                $("#txtDireccionCaja").val(data[0].address);
        }
    });
    function searchCustomerRuc($document, $idRazon, $idDireccion) {
        $("#" + $idRazon).val("");
        $("#" + $idDireccion).val("");
        var param = {'document': $document
        };
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Cliente&&action=ClienteRuc",
            type: 'POST',
            data: param,
            cache: true,
            dataType: 'json',
            success: function (data) {


//                $("#txtRazonSocialCaja").val(data[0].companyName);
//                $("#txtDireccionCaja").val(data[0].address);
                $("#" + $idRazon).val(data[0].companyName);
                $("#" + $idDireccion).val(data[0].address);
            }
        });
    }
    ;
    function searchCustomerDNI($document, $nombres, $apellidos) {
        $("#" + $nombres).val("");
        $("#" + $apellidos).val("");
        var param = {'document': $document
        };
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Cliente&&action=ClienteDni",
            type: 'POST',
            data: param,
            cache: true,
            dataType: 'json',
            success: function (data) {


//                $("#txtRazonSocialCaja").val(data[0].companyName);
//                $("#txtDireccionCaja").val(data[0].address);
                $("#" + $nombres).val(data[0].nombres);
                $("#" + $apellidos).val(data[0].apellidos);
            }
        });
    }
    ;
    function CancelaPedidoComprobante($form, tipoComprobant) {
        var urlImpresion = "";
        var $id = "tipoPago1";
        var TipoPago = $('input:radio[name=' + $id + ']:checked').val();
        if ($('#tblcomprobante').datagrid('getSelections').length > 0) {
            $.ajax({
                url: "<?php echo Class_config::get('urlApp') ?>/?controller=Comprobante&&action=CancelarPedidoComprobanteSeleccion&descuento=" + $('#txtDescuento').val()
                        + "&total_venta=" + $('#lblTotal').html() + "&pkMesa=" + $("#TxtPkMesa").val() + "&pkComprobante=" + $('#txtCombrobante').val() + "&total_efectivo=" + $("#txtMontoEfectivo").val() + "&tipoPago1=" + TipoPago + "&tipo_comprobante=" + tipoComprobant,
                type: 'POST',
                data: $('#' + $form).serialize(),
                dataType: 'html',
                success: function (data) {
                    for (var i = 0; i < $('#tblcomprobante').datagrid('getSelections').length; i++) {
                        var params = {comprobante: data,
                            pedido: $('#tblcomprobante').datagrid('getSelections')[i].pkPedido};
                        $.ajax({
                            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Comprobante&&action=AddDetalle",
                            type: 'POST',
                            data: params,
                            dataType: 'html',
                            success: function (data2) {
                                console.log(data2);
                            }
                        });
                    }
        if (tipoComprobant === "2") {
                        urlImpresion = "<?php echo Class_config::get('urlApp') ?>/ImpresionFactura_<?php echo UserLogin::get_pkSucursal() ?>.php?usuario=" + $("#txtRucFactura").val() + "&rsocial=" + $("#txtRazonSocialFactura").val() + "&mesa=" + $("#txtMesaApertura").val() + "&pkPedido=" + data + "&dire=" + $("#txtDireccionFactura").val();
                        $('#modalCancelGnerarComprobanteFactura').dialog('close');
        }
        else {
                        urlImpresion = "<?php echo Class_config::get('urlApp') ?>/ImpresionBoleta_<?php echo UserLogin::get_pkSucursal() ?>.php?usuario=" + $("#txtDniCliente").val() + "&nombre=" + $("#txtNombres").val() + "&mesa=" + $("#txtMesaApertura").val() + "&pkPedido=" + data + "&apelli=" + $("#txtApellidos").val();
                        $('#modalCancelGnerarComprobante').dialog('close');
                    }
                    var windowI = window.open(urlImpresion, "_blank");
                    windowI.focus();
                    loadDetalles();

        }
            });
        }
        else {
            $.ajax({
                url: "<?php echo Class_config::get('urlApp') ?>/?controller=Comprobante&&action=CancelarPedidoComprobante&descuento=" + $('#txtDescuento').val()
                        + "&total_venta=" + $('#lblTotal').html() + "&pkMesa=" + $("#TxtPkMesa").val() + "&pkComprobante=" + $('#txtCombrobante').val() + "&total_efectivo=" + $("#txtMontoEfectivo").val() + "&tipoPago1=" + TipoPago + "&tipo_comprobante=" + tipoComprobant,
                type: 'POST',
                data: $('#' + $form).serialize(),
                dataType: 'html',
                success: function (data) {
                    for (var i = 0; i < $('#tblcomprobante').datagrid('getRows').length; i++) {
                        var params = {comprobante: data,
                            pedido: $('#tblcomprobante').datagrid('getRows')[i].pkPedido};
                        $.ajax({
                            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Comprobante&&action=AddDetalle",
                            type: 'POST',
                            data: params,
                            dataType: 'html',
                            success: function (data) {
                                console.log("se ha geneerado correctamente" + i);
                            }

                        });

                    }
                    if (tipoComprobant === "2") {
                        urlImpresion = "<?php echo Class_config::get('urlApp') ?>/ImpresionFactura_<?php echo UserLogin::get_pkSucursal() ?>.php?usuario=" + $("#txtRucFactura").val() + "&rsocial=" + $("#txtRazonSocialFactura").val() + "&mesa=" + $("#txtMesaApertura").val() + "&pkPedido=" + data + "&dire=" + $("#txtDireccionFactura").val();
                }
                    else {
                        urlImpresion = "<?php echo Class_config::get('urlApp') ?>/ImpresionBoleta_<?php echo UserLogin::get_pkSucursal() ?>.php?usuario=" + $("#txtDniCliente").val() + "&nombre=" + $("#txtNombres").val() + "&mesa=" + $("#txtMesaApertura").val() + "&pkPedido=" + data + "&apelli=" + $("#txtApellidos").val();
                    }
                    window.open(urlImpresion, "_blank");
                    window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome";
                }
            });





        }
        }

    $.extend($.fn.datagrid.methods, {
        editCell: function (jq, param) {
            return jq.each(function () {
                var opts = $(this).datagrid('options');
                var fields = $(this).datagrid('getColumnFields', true).concat($(this).datagrid('getColumnFields'));
                for (var i = 0; i < fields.length; i++) {
                    var col = $(this).datagrid('getColumnOption', fields[i]);
                    col.editor1 = col.editor;
                    if (fields[i] != param.field) {
                        col.editor = null;
                    }
                }
                $(this).datagrid('beginEdit', param.index);
                for (var i = 0; i < fields.length; i++) {
                    var col = $(this).datagrid('getColumnOption', fields[i]);
                    col.editor = col.editor1;
                }
            });
        }
    });
    var editIndex = undefined;
    function endEditing() {

        if (editIndex == undefined) {
            return true
        }
        if ($('#tblcomprobante').datagrid('validateRow', editIndex)) {
            $('#tblcomprobante').datagrid('endEdit', editIndex);
            editIndex = undefined;
            return true;
        } else {

            return false;
        }
    }
    function onClickCell(index, field) {
        //                           console.log(field);
        if (endEditing() && field == "cantidad"  || field === "pedido") {
            $('#tblcomprobante').datagrid('selectRow', index)
                    .datagrid('editCell', {index: index, field: field});
            editIndex = index;
        }
    }
    function saveChanges(index, data, changes) {
        //                                console.log(data.pkPedido);
        var param = {'pkPedido': data.pkPedido,
            cantidad: data.cantidad, precio: data.precio, pedido: data.pedido};
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=UpdatePedido",
            type: 'POST',
            data: param,
            //                                dataType: 'json',
            success: function (data2) {
                loadDetalles();
                //                                     data.importe = data.cantidad * data.precio;
            }

        });
    }
    function confirImpresion($tipo) {
        var descripcion = "";
        if ($tipo === 1) {
            descripcion = " para cocina";
        }
        else {
            descripcion = " para cantina";
        }
        $.messager.confirm('Imprimiendo ' + descripcion, '¿Desea imprimir el ticket?', function (r) {
            if (r) {
                alert('confirmed: ' + r);
            }
        });
    }
    function cellStylerPedido(value, row, index) {
        if (value === "Solicitado") {
            return 'background-color:red;color:white;';
        }
    }
    $('#tblcomprobante').datagrid({singleSelect: false});
    function visualizaTipoUsuario() {
        var tipe = "<?php echo UserLogin::get_pkTypeUsernames(); ?>";
        switch (tipe) {
             case "1":
             case "2":    
                $(".descuento").show();
                break;
            default:
                $(".descuento").hide();
                break;
        }
    }
</script>
