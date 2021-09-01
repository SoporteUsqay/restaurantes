<?php include 'Application/Views/template/header.php'; ?>
<body>

    <?php
    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showContent();
    ?>    
    <div class="container">
        <div class="jumbotron">

            <!--<br>-->
            <br>
            <br>
            <!--<div class="row">-->

                <!--<div class="col-lg-12">-->


                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">Ingreso de Productos</h3>
                        </div>
                        <div class="panel-body">

                            <div class="row">
                                <!--<form method="post" id="formFiltroProductos">-->
                                <div class="col-lg-4">
                                    Buscar en:
                                    <input class="form-control" id="txtValorFiltroProductos">
                                </div>
                                <div class="col-lg-3">
                                    Categoria
                                    <select class="form-control" id="cmbCategoriaFiltro" > </select>
                                </div>
                                <div class="col-lg-1">
                                    <br>
                                    <button type="button"  onclick="buscarProducto()"class="btn btn-info"><span class="glyphicon glyphicon-search"></span></button>
                                </div>

                                <div class="col-lg-2">
                                    <br>
                                    <input type="radio"  name="busqueda" id="chkTipoFiltroProducto1" value="1"  checked="true"> Descripcion
                                </div>
                                <div class="col-lg-2">
                                    <br>
                                    <input type="radio"  name="busqueda" id="chkTipoFiltroProducto2" value="2" > stock Menores a
                                </div>
                                <div class="col-lg-2">
                                    <br>
                                    <input type="radio"  name="busqueda" id="chkTipoFiltroProducto3" value="3" > stock Mayores a
                                </div>
                                <!--</form>-->
                            </div>

                        </div>
                    </div>
        <!--url: '<?php echo Class_config::get('urlApp') ?>/?controller=Product&&action=FiltroProductos',-->

                    <table id="tblProductosFiltro" class="easyui-datagrid table " style="height:200px;font-size: 11px"
                           data-options="
                           iconCls: 'icon-search',
                           url: '<?php echo Class_config::get('urlApp') ?>/?controller=Product&&action=FiltroProductos',
                           singleSelect: true, 
                           method:'get'
                           ">
                        <thead>
                            <tr>
                                <th data-options="field:'id',hidden:'true'">Id</th>
                                <th data-options="field:'descripcion',editor:'text',width:245">Producto</th>
                                <th data-options="field:'precioVenta',editor:'text'" >Precio Venta</th>
                                <th data-options="field:'stock',editor:'text'" >Stock</th>
                            </tr>
                        </thead>
                    </table>
                    <br>
                    <form id="frmAddCantidadProuducto">
                        <div class="row">
                            <div class="col-lg-6">
                                Comentario
                                <textarea class="form-control" name="comentario"></textarea>
                            </div> 
                            <div class="col-lg-2">
                                Cantidad <input class="form-control" name="cantidad">
                            </div> 
                            <div class="col-lg-4"><br>
                                <button type="button" class="btn btn-success" onclick="addCantidadProductoCantidad(1)">Ingresar</button>
                                <button type="button" class="btn btn-success" onclick="addCantidadProductoCantidad(2)">Quitar</button>

                            </div> 
                        </div>
                    </form>

                </div>
            </div>
<!--        </div>

    </div>-->
</body>
<script>


    function buscarProducto() {

        $('#tblProductosFiltro').datagrid('load', {
            categoria: $('#cmbCategoriaFiltro').val(),
            valor: $('#txtValorFiltroProductos').val(),
            tipo: $('input:radio[name=busqueda]:checked').val()

        });
    }

    function addCantidadProductoCantidad($tipo) {
        var row = $('#tblProductosFiltro').datagrid('getSelected');
//          var producto;
        if (row) {
            $.ajax({
                url: "<?php echo Class_config::get('urlApp') ?>/?controller=Product&&action=addCantidadProductos&pkProduct=" + row.id + "&tipo=" + $tipo,
                type: 'GET',
                data: $('#frmAddCantidadProuducto').serialize(),
                dataType: 'html',
                success: function (datae) {
//                    if (datae = !"false")
//                        console.log();;
                    buscarProducto();
                    $('#frmAddCantidadProuducto').form('clear')

                }

            });
        }
        else {
            $.messager.alert('Error', "Debe seleccionar un producto de la lista");
        }

    }
</script>
