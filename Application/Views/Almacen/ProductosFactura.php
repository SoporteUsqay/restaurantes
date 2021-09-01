<?php include 'Application/Views/template/header.php'; ?>
<body>
    <?php
        $objViewMenu = new Application_Views_IndexView();
        $objViewMenu->showContent();
        ?>
    <div class="row">
        <!--        <div class="col-lg-3">
                    <table id="tblListadoProductosAlmacenFacturaProduct" class="easyui-datagrid" title="Listado de los productos" style="width:max-content;height:500px;font-size: 10px"
                           data-options="
                           singleSelect: true, 
                           url: '<?php echo Class_config::get('urlApp') ?>/?controller=Almacen&&action=ListProduct',
                           method:'get'">
                        <thead>
                            <tr>
                                <th data-options="field:'id',hidden:'true'">Id</th>
                                <th data-options="field:'descripcion',editor:'text'">Producto</th>
        
                            </tr>
                        </thead>
                    </table>
                   
                </div>-->
        <div class="col-lg-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    Productos por Factura

                </div>
                <div class="panel-body">
                    <form id="frmFacturaProvedor">

                        <div class="row">

                            <div class="col-lg-2">
                                Codigo AutoGenerado
                                <input class="form-control" id="txtCofigoFacturaProvedor" readonly="true" name="pkFactura">
                            </div>
                            <div class="col-lg-2">
                                Nro Factura
                                <input class="form-control" id="txtNumeroFacturaProvedor" name="nroFactura" required>
                            </div>

                            <div class="col-lg-3">
                                Empresa Proveedora
                                <select class="form-control" id="txtEmpresaProvedoraFP" onchange=" _loadProductosProvedor('txtProductoFacturaProvedor','txtEmpresaProvedoraFP');" required></select>
                            </div>

                            <div class="col-lg-2">
                                Fecha
                                <input name="fecha" class="form-control" id="txtFechaFacturaProveedor" required>
                            </div>
                            <div class="col-lg-3">
                                <br>
                                <button onclick="saveFacturaProvedor()" type="button" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span>Agregar</button>
                                <button type="reset" class="btn btn-danger">Limpiar</button>
                                <button onclick="filtrarFacturas('tblfacturasProvedor', 'txtNumeroFacturaProvedor', 'txtFechaFacturaProveedor', 'txtEmpresaProvedoraFP')" title="Busca los detalles de una factura, para ello debe de completar los campos anteriores" type="button" class="btn btn-info"><span class="glyphicon glyphicon-search"></span></button>

                            </div>
                        </div>

                    </form>
                </div>
            </div>
            <ul class="nav nav-tabs" id="MyTabFacturaProvedor">
                <li class="active"><a href="#facturas" data-toggle="tab">Facturas</a></li>
                <li><a href="#detalleFacturas" data-toggle="tab">Detalle Facturas</a></li>
            </ul>   
            <!--url: '<?php echo Class_config::get('urlApp') ?>/?controller=ProductosFactura&&action=ListProductosFacturaItem',-->

            <div class="tab-content">
                <div class="tab-pane active" id="facturas">
                    <table id="tblfacturasProvedor" class="easyui-datagrid" style="width:auto;height:270px;font-size: 12px"
                           data-options="
                           iconCls: 'icon-search',
                           singleSelect: true, 
                           
                           method:'get',
                           onClickRow:loadFormFacturaProvedor,
                           method:'POST',
                           onDblClickRow:filtrarDetalleFacturas
                           ">
                        <thead>
                            <tr> <th data-options="field:'pkFactura',">Id</th>
                                <th data-options="field:'razonSocial'">Proveedor</th>
                                <th data-options="field:'ruc'">Documento</th>
                                <th data-options="field:'fecha'">Fecha</th>
                                <th data-options="field:'nroFactura'">nroFactura</th>

                                <th data-options="field:'estado'">Estado</th>
                                <th data-options="field:'total'">Total</th>


                            </tr>
                        </thead>
                    </table>

                </div>
                <div class="tab-pane" id="detalleFacturas">

                    <table id="tbldetallefacturas" class="easyui-datagrid"  style="width:600px;height:270px;font-size: 12px"
                           data-options="

                           ">
                        <thead>
                            <tr>
                                <th data-options="field:'id',hidden:'true'">Id</th>
                                <th data-options="field:'pkProducto',hidden:'true'">PkProdicto</th>
                                <th data-options="field:'descripcion',editor:'text'">Producto</th>
                                <th data-options="field:'cantidad',editor:'text'">Cantidad</th>
                                <th data-options="field:'valorUnidad',editor:'text'">Unidad</th>
                                <th data-options="field:'cantidadTotal',editor:'text'">Cantidad T</th>
                                
                                <th data-options="field:'precioUnitario',editor:'text'">Precio</th>
                                <th data-options="field:'subTotal',editor:'text'">SubTotal</th>
                            

                            </tr>
                        </thead>
                    </table>

                </div>
            </div>
            <div class="panel panel-info">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <button type="button" onclick="$('#windowFormDetalleFacturaProvedor').window('open')" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span>Agregar</button>
                            <button type="button" class="btn btn-success"><span class="glyphicon glyphicon-pencil"></span>Modificar</button>
                            <button type="button" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span>Quitar</button>

                        </div>
                        <div class="col-lg-6">

                        </div>
                    </div>
                </div>
            </div>
            <div id="windowFormDetalleFacturaProvedor" class="easyui-window" title="Gestion de Compras" data-options="modal:true,closed:true" style="width:550px;height:450px;padding:10px;">
                <form class="form-horizontal" id="frmDetalleFacturaProvedor">
                    <div class="form-group">
                        <label for="txtProductoFacturaProvedor" class="col-sm-2 control-label">Producto</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="pkProducto" id="txtProductoFacturaProvedor" placeholder="Producto">

                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="txtCantidadFacturaCompra" class="col-sm-2 control-label">Cantidad</label>
                        <div class="col-sm-10">
                            <input name="cantidad" onblur="$('#txtSubTotalDetalleFacturaProvedor').val(multiplicacion($('#txtPrecioUnitarioFacturaProvedor').val(), $('#txtTotalCompradoFacturaProvedor').val()));
                                    $('#txtTotalCompradoFacturaProvedor').val(multiplicacion($('#txtCantidadFacturaCompra').val(), $('#txtPrecioUnitario').val()))" type="text" class="form-control" id="txtCantidadFacturaCompra" placeholder="Cantidad">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="txtPrecioUnitario" class="col-sm-2 control-label">Unidad de Compra</label>
                        <div class="col-sm-10">
                            <select name="valorUnidad" onchange="$('#txtTotalCompradoFacturaProvedor').val(multiplicacion($('#txtCantidadFacturaCompra').val(), $('#txtPrecioUnitario').val()));
                                    $('#txtSubTotalDetalleFacturaProvedor').val(multiplicacion($('#txtPrecioUnitarioFacturaProvedor').val(), $('#txtCantidadFacturaCompra').val()))" class="form-control" id="txtPrecioUnitario" placeholder="Es la unidad de compra ejemplo. Caja de x12, caja de x6 ">

                                <option value="1" selected="true">X 1</option>
                                <option value="2">X 2</option>
                                <option value="3">X 3</option>
                                <option value="6">X 6</option>
                                <option value="12">X 12</option>
                                <option value="15">X 15</option>
                                <option value="24">X 24</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="txtTotalCompradoFacturaProvedor" class="col-sm-2 control-label">Total Comprado</label>
                        <div class="col-sm-10">
                            <input name="cantidadTotal" onblur="$('#txtSubTotalDetalleFacturaProvedor').val(multiplicacion($('#txtPrecioUnitarioFacturaProvedor').val(), $('#txtCantidadFacturaCompra').val()))"  readonly="true" type="text" class="form-control" id="txtTotalCompradoFacturaProvedor" placeholder="Resultado de la Unidad de compra X la cantidad">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="txtPrecioUnitarioFacturaProvedor" class="col-sm-2 control-label">Precio Unitario</label>
                        <div class="col-sm-10">
                            <input name="precioUnitario" onblur="$('#txtSubTotalDetalleFacturaProvedor').val(multiplicacion($('#txtPrecioUnitarioFacturaProvedor').val(), $('#txtCantidadFacturaCompra').val()))" type="text" class="form-control" id="txtPrecioUnitarioFacturaProvedor" placeholder="Precio ">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="txtSubTotalDetalleFacturaProvedor" class="col-sm-2 control-label">Sub Total</label>
                        <div class="col-sm-10">
                            <input name="total" type="text" class="form-control" id="txtSubTotalDetalleFacturaProvedor" placeholder=" ">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="button" class="btn btn-default" onclick="addDetallesFacturaProvedor()">Guardar</button>
                            <button type="reset" class="btn btn-danger">Limpiar</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</body>
<script>
    date('txtFechaFacturaProveedor');
   
  
    $('#txtCantidadFacturaCompra').numeric({negative: false});
    function multiplicacion($valor1, $valor2) {
        return $valor1 * $valor2;
    }
    nRuc('txtRucFacturaProvedor');

    function saveFacturaProvedor() {
        if (validateCamposVacios('txtEmpresaProvedoraFP') !== '0') {
            $.ajax({
                url: "<?php echo Class_config::get('urlApp') ?>/?controller=FacturaProvedor&&action=SaveFacturaProvedor",
                data: {fecha: $('#txtFechaFacturaProveedor').val(),
                    nroFactura: $('#txtNumeroFacturaProvedor').val(),
                    pkProvedor: $('#txtEmpresaProvedoraFP').val(),
                },
                type: 'GET',
                dataType: 'html',
                success: function(data) {
                    console.log(data);
                    if (data === "false")
                        alert("No se ha podido registrar");
                    else {
                        alert("Se ha registrado correctamente");
                        $('#txtCofigoFacturaProvedor').val(data);
                    }
                }
            });
        }
    }
    function addDetallesFacturaProvedor() {
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=DetalleFacturaProvedor&&action=AddDetalleFacturaProvedor&pkFactura=" + $('#txtCofigoFacturaProvedor').val(),
            data: $('#frmDetalleFacturaProvedor').serialize(),
            type: 'GET',
            dataType: 'html',
            success: function(data) {
                console.log(data);
                if (data === "false")
                    alert("No se ha podido registrar");
                else {
                    alert("Se ha registrado correctamente");
                    $('#frmDetalleFacturaProvedor').form('clear');
                }
            }
        });
    }
    function filtrarFacturas($tabla, $nroFactura, $fecha, $pkProvedor) {
        $('#' + $tabla).datagrid({
            url: '<?php echo Class_config::get('urlApp') ?>/?controller=FacturaProvedor&&action=ListFacture&nroFactura=' + $("#" + $nroFactura).val() +
                    '&fecha=' + $('#' + $fecha).val() + '&pkProvedor=' + $('#' + $pkProvedor).val()
        }
        );
    }
    function filtrarDetalleFacturas() {
        $('#tbldetallefacturas').datagrid({
            url: '<?php echo Class_config::get('urlApp') ?>/?controller=DetalleFacturaProvedor&&action=List&pkFactura=' + $('#txtCofigoFacturaProvedor').val()
        }
        );
        $('#MyTabFacturaProvedor a:last').tab('show')
//        $('#' + $tabla).datagrid('load', {nroFactura: $("#" + $nroFactura).val()
//            , fecha: $('#' + $fecha).val(),
//            pkProvedor: $('#' + $pkProvedor).val(),
//        });

    }
    function loadFormFacturaProvedor() {
//        console.log('Click');
        var row = $('#tblfacturasProvedor').datagrid('getSelected');
        if (row) {
//            $('#dlg').dialog('open').dialog('setTitle', 'Edit User');
            $('#frmFacturaProvedor').form('load', row);
//            url = 'update_user.php?id=' + row.id;
        }
    }
//    function 
</script>