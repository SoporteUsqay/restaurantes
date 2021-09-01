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
        <ul class="nav nav-tabs" id="myTabReportSProductosMes">
            <li class="active"><a href="#dataProductMes" data-toggle="tab">Datos</a></li>
            <li><a href="#GraficProductMes" data-toggle="tab">Grafico</a></li>
            <!--<li><a href="#messages" data-toggle="tab">Messages</a></li>-->
            <!--<li><a href="#settings" data-toggle="tab">Settings</a></li>-->
        </ul> 

        <div class="tab-content">
            <div class="tab-pane active" id="dataProductMes">
                <div style="padding: 13px;font-size: 15px">
                    <table>
                        <tr>
                            <td>Mes&nbsp;</td>
                            <!--<td><input id="TxtdateIntoSProductosMes" value="<?php echo date('Y-m-d') ?>" type="text" class="form-control"></td>-->
                            <td class="col-sm-2">
                                <select id="cmbFiltroDataMes" class="form-control">
                                    <!--<option value="1">Todos</option>-->
                                    <option value="01">Enero</option>
                                    <option value="02">Febrero</option>
                                    <option value="03">Marzo</option>
                                    <option value="04">Abril</option>
                                    <option value="05">Mayo</option>
                                    <option value="06">Junio</option>
                                    <option value="07">Julio</option>
                                    <option value="08">Agosto</option>
                                    <option value="09">Septiembre</option>
                                    <option value="10">Octubre</option>
                                    <option value="11">Noviembre</option>
                                    <option value="12">Diciembre</option>
                                </select>
                            </td>

                            <td>&nbsp&nbsp;Año&nbsp;</td>
                            <td class="col-sm-2">
                                <select name="anio" id="cmbFiltroDataAnio" class="form-control">
                                    <option><?php echo date('Y'); ?></option>
                                    <option><?php echo date('Y', strtotime('-1 year')); ?></option>
                                    <option><?php echo date('Y', strtotime('-2 year')); ?></option>
                                    <option><?php echo date('Y', strtotime('-3 year')); ?></option>
                                </select>
                            </td>

                            <td>&nbsp&nbsp;Clase&nbsp&nbsp;</td>
                            <td class="col-sm-2">
                                <select id="cmbFiltroClaseProductoMes" class="form-control" required="true" onchange="_listProducto_Categorias('cmbFiltroClaseProductoMes', 'cmbFiltroCategoriaProductMes')">
                                    <option value="0">Seleccione</option>
                                    <option value="2">Platos</option>
                                    <option value="3">Productos</option>
                                </select>
                            </td>
                            <td>&nbsp &nbsp;Categoria&nbsp&nbsp;</td>
                            <td class="col-sm-3">
                                <select id="cmbFiltroCategoriaProductMes" class="form-control" required="true" onchange="_loadTiposCategoria('cmbFiltroCategoriaProductMes', 'cmbFiltroTipoProductMes')">
                                </select>
                                <!--onchange="_loadTiposCategoria('cmbFiltroCategoriaProductDia', 'cmbFiltroTipoProductDia')"-->
                            </td>
                            <td>&nbsp &nbsp;Tipo&nbsp&nbsp;</td>
                            <td class="col-sm-6">
                                <select id="cmbFiltroTipoProductMes" class="form-control">
                                </select>
                            </td>
                            <td>&nbsp;<button onclick="loadTableSaleOuyputProductMes()" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span></button></td>

                        <!--<td>&nbsp;<button class="btn btn-danger"><span class="glyphicon glyphicon-print"> Imprimir</span></button></td>-->

                        </tr>
                    </table>
                    <br>

                    <td>&nbsp;<button class="btn btn-success"> Exportar a Excel</span></button></td>
                    <td>&nbsp;<button class="btn btn-success" onclick="exportarPDFMes()"> Exportar a Pdf</span></button></td>

                    <br>
                    <br>
                    <!--url: '<?php echo Class_config::get('urlApp') ?>/?controller=Report&&action=ListOutputProductMes',-->

                    <table id="tblReportSProductosMes" class="easyui-datagrid" title="Productos Vendidos" style="width:800px;height:400px"
                           data-options="
                           iconCls: 'icon-search',
                           rownumbers:true,singleSelect:true,
                           url: '<?php echo Class_config::get('urlApp') ?>/?controller=Report&&action=ListOutputProductMes',
                           method:'get'
                           ">
                        <thead>
                            <tr>
                                <th data-options="field:'categoria',editor:'text'">Categoria</th>
                                <th data-options="field:'tipo',editor:'text'">Tipo</th>
                                <th data-options="field:'pedido',editor:'text'">Pedido</th>
                                <th data-options="field:'Cantidad',editor:'text'">cantidad</th>
                                <th data-options="field:'Precio',editor:'text'">Precio</th>                                 
                                <th data-options="field:'Importe',editor:'text'" >importe</th>
                                <th data-options="field:'Hora',editor:'text'" >Hora pedido</th>

                            </tr>
                        </thead>

                    </table>

                </div>

            </div>

            <div class="tab-pane" id="GraficProductMes">
                <div style="padding: 13px;font-size: 15px">
                    <table>
                        <tr>
                            <td>&nbsp&nbsp;Mes:&nbsp&nbsp;</td>
                            <!--<td><input class="form-control" value="<?php echo date('Y-m-d') ?>" type="text" id="txtFechaGo"></td>-->
                            <td class="col-sm-2">
                                <select id="cmbFiltroGraficDataMes" class="form-control" >
                                    <!--<option value="1">Todos</option>-->
                                    <option value="01">Enero</option>
                                    <option value="02">Febrero</option>
                                    <option value="03">Marzo</option>
                                    <option value="04">Abril</option>
                                    <option value="05">Mayo</option>
                                    <option value="06">Junio</option>
                                    <option value="07">Julio</option>
                                    <option value="08">Agosto</option>
                                    <option value="09">Septiembre</option>
                                    <option value="10">Octubre</option>
                                    <option value="11">Noviembre</option>
                                    <option value="12">Diciembre</option>
                                </select>
                            </td>

                            <td>&nbsp&nbsp;Año&nbsp&nbsp;&nbsp;</td>
                            <td class="col-sm-2">
                                <select name="anio" id="cmbFiltroGraficAnio" class="form-control">
                                    <option><?php echo date('Y'); ?></option>
                                    <option><?php echo date('Y', strtotime('-1 year')); ?></option>
                                    <option><?php echo date('Y', strtotime('-2 year')); ?></option>
                                    <option><?php echo date('Y', strtotime('-3 year')); ?></option>
                                </select>
                            </td>

                            <td>&nbsp&nbsp;&nbsp;Clase&nbsp&nbsp;&nbsp;</td>
                            <td class="col-sm-2">
                                <select id="cmbGraficClaseProductoMes" class="form-control" required="true" onchange="_listProducto_Categorias('cmbGraficClaseProductoMes', 'cmbGraficCategoriaProductMes')">
                                    <option value="1">Todos</option>
                                    <option value="2">Platos</option>
                                    <option value="3">Productos</option>
                                </select>
                            </td>
                            <td>&nbsp &nbsp;Categoria&nbsp&nbsp;</td>
                            <td class="col-sm-3">
                                <select id="cmbGraficCategoriaProductMes" class="form-control" required="true" onchange="_loadTiposCategoria('cmbGraficCategoriaProductMes', 'cmbGraficTipoProductMes')">
                                </select>
                                <!--onchange="_loadTiposCategoria('cmbFiltroCategoriaProductDia', 'cmbFiltroTipoProductDia')"-->
                            </td>
                            <td>&nbsp &nbsp;Tipo&nbsp&nbsp;</td>
                            <td class="col-sm-6">
                                <select id="cmbGraficTipoProductMes" class="form-control">
                                </select>
                            </td>
                            <td><button id="" type="button" class="btn btn-primary" onclick="createGraficmes()"><span class="glyphicon glyphicon-search"> </span></button>


                        </tr>
                    </table>
                </div>

                <div id="divGraficProductMes" style="width: 1000px;height: 600px;">
                    <canvas id="canvasmes" height="550" width="1200"></canvas>
                </div>

            </div>


        </div>
    </div>
    <?php // echo date('m')?>
    <script>
        
$('#cmbFiltroGraficDataMes').val('<?php echo date('m')?>');
$('#cmbFiltroDataMes').val('<?php echo date('m')?>');
        //     _listTipos('cmbFiltroDataProductMes');
        _listTipos('cmbFiltroGraficProductMes');

        function exportarPDFMes() {
            var url = "<?php echo Class_config::get('urlApp') ?>/pdf_Psalida_xmes.php?mes=" + $("#cmbFiltroDataMes").val() + "&anio=" + $("#cmbFiltroDataAnio").val() + "&clase=" + $("#cmbFiltroClaseProductoMes").val() + "&Idcategoria=" + $("#cmbFiltroCategoriaProductMes").val() + "&tipo_categoria=" + $("#cmbFiltroTipoProductMes").val();
            window.open(url, '_blank');
        }


        function loadTableSaleOuyputProductMes() {

            $('#tblReportSProductosMes').datagrid('load',{
                dateGo: $('#cmbFiltroDataMes').val(),
                filtroAnio: $('#cmbFiltroDataAnio').val(),
                clase: $('#cmbFiltroClaseProductoMes').val(),
                Idcategoria: $('#cmbFiltroCategoriaProductMes').val(),
                tipo_categoria: $('#cmbFiltroTipoProductMes').val()

            });
        }


        function createGraficmes() {
            $("#divGraficProductMes").empty();
            var div = $("#divGraficProductMes");
            $('<canvas id="canvasmes" height="450" width="1000">').appendTo(div);

            $.ajax({
                url: "<?php echo Class_config::get('urlApp') ?>/?controller=Report&&action=ListOutputProductMes",
                data: {
                    dateGo: $('#cmbFiltroGraficDataMes').val(),
                    filtroAnio: $('#cmbFiltroGraficAnio').val(),
                    clase: $('#cmbGraficClaseProductoMes').val(),
                    Idcategoria: $('#cmbGraficCategoriaProductMes').val(),
                    tipo_categoria: $('#cmbGraficTipoProductMes').val()
                },
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    var pedido = new Array;
                    var cantidad = new Array;
                    for (var i = 0; i < data.length; i++) {
                        //                     console.log(data[i].mes)
                        pedido[i] = data[i].pedido;
                        cantidad[i] = data[i].Cantidad;

                    }
                    var barChartData = {
                        labels: pedido,
                        datasets: [
                            {
                                label: "My First dataset",
                                fillColor: "rgba(151,187,205,0.5)",
                                strokeColor: "rgba(151,187,205,0.8)",
                                highlightFill: "rgba(151,187,205,0.75)",
                                highlightStroke: "rgba(151,187,205,1)",
                                data: cantidad
                            }
                        ]

                    };

                    var myLine = new Chart(document.getElementById("canvasmes").getContext("2d")).Bar(barChartData);

                }

            });

        }


    </script>
