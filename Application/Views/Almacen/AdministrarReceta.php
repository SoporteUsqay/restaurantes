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
<!--url: '<?php echo Class_config::get('urlApp') ?>/?controller=Sale&&action=FiltroPlatos',-->
        <form>
            Plato
            <input id="nombrePlato" class="form-control">
            <input id="idPlato" style="display: none">
            Insumo
            <input id="nombrePlato" class="form-control">
            <input id="idPlato" style="display: none">
            Cantidad
            <input type="number" id="cantidad" class="form-control">
            <button class="btn btn-primary">Agregar</button>
        </form>
        <table id="tblPlatosReceta" title="" class="display">
            <thead>
                <tr>
                    <th data-options="field:'pkPlatoSucursal',hidden:'true',width:45">ID</th>
                    <th data-options="field:'pkPlato',editor:'text',width:55">ID</th>
                    <th data-options="field:'descripcion_plato',editor:'text',width:245">Plato</th>
                    <th data-options="field:'precioVenta',editor:{type:'numberbox',options:{precision:1}}" >P. Venta</th>
                    <th data-options="field:'nomcategoria',hidden:'true'" >categoria</th>
                    <th data-options="field:'nomTipo',hidden:'true'" >tipo</th>
                    <th data-options="field:'categoria',hidden:'true'" >IDcategoria</th>
                    <th data-options="field:'pkTipo_platos',hidden:'true'" >IDtipo</th>
                </tr>
            </thead>
        </table>

        <div id="toolbarPlatosReceta">
            <a href="javascript:void(0)" onclick="AddNuevoPlatoListado()" class="easyui-linkbutton" iconCls="icon-add" plain="true" >Filtro</a>
            <!--        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="editPlato()">Editar Plato</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="elimPlato()">Elminar Plato</a>-->
        </div>
        <div role="tabpanel">

            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Descripcion</a></li>
                <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Ingredientes</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="home">
                    <form>
                        Preparacion:
                        <textarea class="form-control">
                        
                        </textarea>
                    </form>
                </div>
                <div role="tabpanel" class="tab-pane" id="profile">


                </div>

            </div>

        </div>



    </div>
</body>
<script>
    function AddNuevoPlatoListado() {
        $('#dlg-Plato').window('open').dialog('setTitle', 'Ingresando nuevo Plato');
        $('#frmPlato').form('clear');
        url = '<?php echo Class_config::get('urlApp') ?>?controller=Sale&&action=Saveplato';
    }
//    _listCategorias('cmbCategoriaFiltroPlatos');
//    _listCategorias('cmbRegisterCategoria');

    function buscarPlato() {
//        console.log("Entro a buscarplatos");

        $('#tblPlatosReceta').datagrid('load', {
            categoria: $('#cmbCategoriaFiltroPlatos').val(),
            valor: $('#txtValorFiltroPlatos').val()

        });
    }

    function editPlato() {
        var row = $('#tblPlatosReceta').datagrid('getSelected');
        if (row) {
            $('#dlg-Plato').dialog('open').dialog('setTitle', 'Editando Plato');
            $('#frmPlato').form('load', row);

            _loadTiposCategoria('cmbRegisterCategoria', 'cmbRegisterTipo');

            url = '<?php echo Class_config::get('urlApp') ?>?controller=Sale&&action=UpdatePlato&pkPlato=' + row.pkPlato;

            setTimeout(function () {
                console.log("entro al setTime");
                $('#cmbRegisterTipo').val(row.pkTipo_platos);
            }, 1500);
        } else {
            $.messager.alert('Alerta', 'Error, no ha seleccionado ningun plato en la tabla', 'error');
        }
    }

    function savePlato() {
//            console.log($("#frmEmpleados").form('validate'));
        if ($("#frmPlato").form('validate') == true) {
            $.ajax({
                type: "GET",
                url: url,
                data: $("#frmPlato").serialize(), // Adjuntar los campos del formulario enviado.
                dataType: 'html',
                success: function (data)

                {
                    if (data == "true") {
                        $('#dlg-Plato').dialog('close'); // close the dialog
                        $('#tblPlatosReceta').datagrid('reload');
                    }
                    else {
                        $.messager.show({
                            title: 'estado',
                            msg: "Se ha registrado el plato correctamente"
                        });
                    }
                    loadPlatos();
                }
            });
        }
        else {

            $.messager.show({
                title: 'Error',
                msg: "No se han Completado los campos requeridos"
            });
        }
    }

//    function addNuevoPlato() {
//        var row = $('#tblPlatosReceta').datagrid('getSelected');
//        var param = {tipo: $('#cmbRegisterTipo').combobox('getValue'), precio: $('#txtprecioPlato').val(),
//            descripcion: $('#txtnuevoPlato').val()};
//        $.ajax({
//            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Sale&&action=addPlatoNuevo",
//            type: 'GET',
//            data: param,
//            dataType: 'html',
//            success: function (data) {
//
//                $.messager.show({tittle: 'Estado', msg: "Se ha registrado el plato correctamente"});
//
//
//                $('#frmPlato').form('clear');
//                $('#dlg-Plato').window('close');
//                buscarProducto();
//
//            }
//
//        });
//
//    }


    function elimPlato() {
        var row = $('#tblPlatosReceta').datagrid('getSelected');
        if (row) {

            //console.log(row.precioVenta);
            console.log(row.pkPlatoSucursal);
//            $('#dlg-Plato').dialog('open').dialog('setTitle', 'Editando Plato');
//            $('#frmPlato').form('load', row);

            var param = {'pkPlato': row.pkPlatoSucursal};

            $.ajax({
                url: "<?php echo Class_config::get('urlApp') ?>/?controller=Sale&&action=deletePlato",
                type: 'GET',
                data: param,
                dataType: 'html',
                success: function (data)
                {
                    $.messager.show({
                        title: 'Estado',
                        msg: "Se ha eliminado el Plato Correctamente"
                    });
//                buscarProducto();
//                $('#frmIngresoPlato').form('clear')
                    loadPlatos();

                }

            });

        } else {
            $.messager.alert('Alerta', 'Error, no ha seleccionado ningun plato en la tabla', 'error');
        }
    }
    function loadPlatos() {
        $('#tblPlatosReceta').datagrid('load', {
            //comprobante: $("#txtCombrobante").val(),
        });
    }


</script> 

