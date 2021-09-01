<?php include 'Application/Views/template/header.php'; ?>
<body>
    <?php
    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showContent();
    ?>

    <div class="container">
        <br>
        <br>
        <br> 
        <ul class="nav nav-tabs" id="myTabReportSProductosMes">
        <li class="active"><a href="#tabListadoProductos" data-toggle="tab">Productos</a></li>
        <li ><a href="#dataGridRISProductos" data-toggle="tab">Datos</a></li>

        <!--<li><a href="#messages" data-toggle="tab">Messages</a></li>-->
        <!--<li><a href="#settings" data-toggle="tab">Settings</a></li>-->
    </ul> 
    <div class="tab-content">
        <div class="tab-pane active" id="tabListadoProductos">
            <div class="col-lg-4">
                Buscar en:
                <input class="form-control" id="txtnombreProducto" onkeyup="buscar_Productos()">
            </div>
            <div class="col-lg-4">
                Categoria:
                <select onchange="buscar_Productos()" class="form-control" id="cmbCategoriaProductos2">

                </select>
            </div>

            <div class="col-lg-1">
                <br>
                <button type="button"  onclick="buscar_Productos()" class="btn btn-info"><span class="glyphicon glyphicon-search"></span></button>
            </div>
            <br>
             <!--url: '<?php echo Class_config::get('urlApp') ?>/?controller=Product&&action=BusquedaProductos'-->
                  
            <table id="tbl_listaProductos1" title="Productos" class="easyui-datagrid" style="width:620px;height:420px"
                   data-options="
                   
                   singleSelect:true,
                   pagination:true,
                   method:'get',
                   rownumbers:true                 
                   "
                   >
                <thead>
                    <tr>
                        <!--<th data-options="field:'pkWorkPeople',hidden:'true'">ID</th>-->
                        <th data-options="field:'pkProductoSucursal',hidden:'true'">ID</th>
                        <th data-options="field:'pkProducto',editor:'text',width:55">ID</th>
                        <th data-options="field:'descripcion_producto',editor:'text',width:245">Producto</th>
                        <th data-options="field:'precioVenta',editor:'text'" >P. Venta</th>
                        <th data-options="field:'precioCompra',editor:'text'" >P. Compra</th>
                        <th data-options="field:'stock',editor:'text'" >Stock</th>
                        <th data-options="field:'nombcategoria',hidden:'true'" >categoria</th>
                        <th data-options="field:'pkTipocatg',hidden:'true'" >tipo</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="tab-pane" id="dataGridRISProductos">

            <div class="col-lg-6">
                <!--url: '<?php echo Class_config::get('urlApp') ?>/?controller=ProductosFactura&&action=ListProductosFacturaItem',-->
                       
                <table id="tblngresosProductos" class="easyui-datagrid" title="Ingreso de productos" style="width:auto;height:270px;font-size: 12px"
                       data-options="

                       singleSelect: true, 
                       method:'get'
                       ">
                    <thead>
                        <tr> <th data-options="field:'pkFactura',hidden:true">Id</th>
                            <th data-options="field:'razonSocial'">Producto</th>
                            <th data-options="field:'ruc'">Total Ingresado</th>
<!--                            <th data-options="field:'fecha'">Fecha</th>
                            <th data-options="field:'nroFactura'">nroFactura</th>

                            <th data-options="field:'estado'">Estado</th>
                            <th data-options="field:'total'">Total</th>-->


                        </tr>
                    </thead>
                </table>
            </div>
            <div class="col-lg-6">
                <!--url: '<?php echo Class_config::get('urlApp') ?>/?controller=ProductosFactura&&action=ListProductosFacturaItem',-->

                <table id="tblSalidaProductos" class="easyui-datagrid" title="Salida de productos" style="width:auto;height:270px;font-size: 12px"
                       data-options="

                       singleSelect: true, 
                       
                       method:'POST'">
                    <thead>
                        <tr> <th data-options="field:'pkFactura',hidden:true">Id</th>
                            <th data-options="field:'razonSocial'">Producto</th>
                            <th data-options="field:'ruc'">Total Salida</th>
                        </tr>
                    </thead>
                </table>
            </div>

        </div>
        
    </div>
</div>
