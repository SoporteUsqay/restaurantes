<div id="Report_VTotales" class="easyui-window" data-options="modal:true,closed:true"  title="Ventas Semanales - Mensuales - Anuales" style="width:1200px;height:680px;padding:5px;">
    <ul class="nav nav-tabs" id="myTabReportSemanal_Mes_Anual">
        <li class="active"><a href="#GraficVentasSemanales" data-toggle="tab">Semanal</a></li>
        <li><a href="#GraficVentasMensuales" data-toggle="tab">Mensual</a></li>
        <li><a href="#GraficVentasAnuales" data-toggle="tab">Anual</a></li>
    </ul> 
    <div class="tab-content">
        <!--grafica SEMANAL-->
        <div class="tab-pane" id="GraficVentasSemanales">
            <div style="padding: 13px;font-size: 15px">
                <table>
                    <tr>
                        <td>&nbsp;Año</td>
                        <td class="col-sm-2">
                            <select name="anio" id="cmbGraficAnioSemana" class="form-control">
                                <option><?php echo date('Y');?></option>
                                <option><?php echo date('Y', strtotime('-1 year'));?></option>
                                <option><?php echo date('Y', strtotime('-2 year'));?></option>
                                <option><?php echo date('Y', strtotime('-3 year'));?></option>
                            </select>
                        </td>
                        
                        <td>&nbsp;Mes:</td>                   
                        <td class="col-sm-2">
                            <select id="cmbGraficDataMesSemana" class="form-control">
                                <!--<option value="1">Todos</option>-->
                                <option value="1">Enero</option>
                                <option value="2">Febrero</option>
                                <option value="3">Marzo</option>
                                <option value="4">Abril</option>
                                <option value="5">Mayo</option>
                                <option value="6">Junio</option>
                                <option value="7">Julio</option>
                                <option value="8">Agosto</option>
                                <option value="9">Septiembre</option>
                                <option value="10">Octubre</option>
                                <option value="11">Noviembre</option>
                                <option value="12">Diciembre</option>
                            </select>
                        </td>
                        
                        <td>&nbsp;Semana</td>
                        <td class="col-sm-2">
                            <select id="cmbGraficSemana" class="form-control" required="true" >
                                <option value="1">Semana 1</option>
                                <option value="2">Semana 2</option>
                                <option value="3">Semana 3</option>
                                <option value="4">Semana 4</option>
                                <option value="5">Semana 5</option>
                            </select>
                        </td>
                        
                        <td>&nbsp;Clase</td>
                        <td class="col-sm-2">
                            <select id="cmbGraficClaseSemana" class="form-control" required="true" onchange="_listProducto_Categorias('cmbGraficClaseSemana', 'cmbGraficCategoriaSemana')">
                                <!--<option value="1">Todos</option>-->
                                <option value="0">Eliga una opcion</option>
                                <option value="2">Platos</option>
                                <option value="3">Productos</option>
                            </select>
                        </td>
                        <td>&nbsp;Categoria</td>
                        <td class="col-sm-2">
                            <select id="cmbGraficCategoriaSemana" class="form-control" required="true" onchange="_loadTiposCategoria('cmbGraficCategoriaSemana', 'cmbGraficTipoSemana')">
                            </select>
                            <!--onchange="_loadTiposCategoria('cmbFiltroCategoriaProductDia', 'cmbFiltroTipoProductDia')"-->
                        </td>
                        <td>&nbsp;Tipo&nbsp;</td>
                        <td class="col-sm-5">
                            <select id="cmbGraficTipoSemana" class="form-control">
                            </select>
                        </td>
                        
                        <td><button id="" type="button" class="btn btn-primary" onclick="createGraficSemanal()"><span class="glyphicon glyphicon-search"> </span></button>

                        
                    </tr>
                </table>                             
            </div>
            
            <div id="divGraficVentasSemanales" style="width: 1000px;height: 600px;">
                <canvas id="canvassemanal" height="550" width="1200"></canvas>
            </div>
        </div>
        <!--grafica MENSUAL-->
        <div class="tab-pane" id="GraficVentasMensuales">
            <div style="padding: 13px;font-size: 15px">
                <table>
                    <tr>
                        <td>&nbsp&nbsp;Año;&nbsp;</td>
                        <td class="col-sm-2">
                            <select name="anio" id="cmbGraficAnioMensual" class="form-control">
                                <option><?php echo date('Y');?></option>
                                <option><?php echo date('Y', strtotime('-1 year'));?></option>
                                <option><?php echo date('Y', strtotime('-2 year'));?></option>
                                <option><?php echo date('Y', strtotime('-3 year'));?></option>
                            </select>
                        </td>
                        
                        <td>&nbsp;Mes:&nbsp;</td>
                        <!--<td><input class="form-control" value="<?php echo date('Y-m-d') ?>" type="text" id="txtFechaGo"></td>-->
                        <td class="col-sm-2">
                            <select id="cmbGraficDataMensual" class="form-control">
                                <!--<option value="1">Todos</option>-->
                                <option value="1">Enero</option>
                                <option value="2">Febrero</option>
                                <option value="3">Marzo</option>
                                <option value="4">Abril</option>
                                <option value="5">Mayo</option>
                                <option value="6">Junio</option>
                                <option value="7">Julio</option>
                                <option value="8">Agosto</option>
                                <option value="9">Septiembre</option>
                                <option value="10">Octubre</option>
                                <option value="11">Noviembre</option>
                                <option value="12">Diciembre</option>
                            </select>
                        </td>
                        
                        <td>&nbsp;Clase&nbsp;</td>
                        <td class="col-sm-2">
                            <select id="cmbGraficClaseMensual" class="form-control" required="true" onchange="_listProducto_Categorias('cmbGraficClaseMensual', 'cmbGraficCategoriaMensual')">
                                <option value="1">Todos</option>
                                <option value="2">Platos</option>
                                <option value="3">Productos</option>
                            </select>
                        </td>
                        <td>&nbsp;Categoria&nbsp;</td>
                        <td class="col-sm-3">
                            <select id="cmbGraficCategoriaMensual" class="form-control" required="true" onchange="_loadTiposCategoria('cmbGraficCategoriaMensual', 'cmbGraficTipoMensual')">
                            </select>
                            <!--onchange="_loadTiposCategoria('cmbFiltroCategoriaProductDia', 'cmbFiltroTipoProductDia')"-->
                        </td>
                        <td>&nbsp &nbsp;Tipo&nbsp&nbsp;</td>
                        <td class="col-sm-3">
                            <select id="cmbGraficTipoMensual" class="form-control">
                            </select>
                        </td>
                        
                        <td><button id="" type="button" class="btn btn-primary" onclick="createGraficMensual()"><span class="glyphicon glyphicon-search">generar grafica </span></button>

                        
                    </tr>
                </table>
            </div>
        </div>
         <!--grafica anual-->
        <div class="tab-pane" id="GraficVentasAnuales">
            <div style="padding: 13px;font-size: 15px">
                <table>
                    <tr>
                        <td>&nbsp&nbsp;Año;&nbsp;</td>
                        <td class="col-sm-2">
                            <select name="anio" id="cmbGraficAnual" class="form-control">
                                <option><?php echo date('Y');?></option>
                                <option><?php echo date('Y', strtotime('-1 year'));?></option>
                                <option><?php echo date('Y', strtotime('-2 year'));?></option>
                                <option><?php echo date('Y', strtotime('-3 year'));?></option>
                            </select>
                        </td>
                        
                        <td>&nbsp;Clase&nbsp;</td>
                        <td class="col-sm-2">
                            <select id="cmbGraficClaseAnual" class="form-control" required="true" onchange="_listProducto_Categorias('cmbGraficClaseAnual', 'cmbGraficCategoriaAnual')">
                                <option value="1">Todos</option>
                                <option value="2">Platos</option>
                                <option value="3">Productos</option>
                            </select>
                        </td>
                        <td>&nbsp;Categoria&nbsp;</td>
                        <td class="col-sm-2">
                            <select id="cmbGraficCategoriaAnual" class="form-control" required="true" onchange="_loadTiposCategoria('cmbGraficCategoriaAnual', 'cmbGraficTipoAnual')">
                            </select>
                            <!--onchange="_loadTiposCategoria('cmbFiltroCategoriaProductDia', 'cmbFiltroTipoProductDia')"-->
                        </td>
                        <td>&nbsp &nbsp;Tipo&nbsp&nbsp;</td>
                        <td class="col-sm-3">
                            <select id="cmbGraficTipoAnual" class="form-control">
                            </select>
                        </td>
                       
                        <td><button id="" type="button" class="btn btn-primary" onclick="createGraficAnual()"><span class="glyphicon glyphicon-search">generar grafica </span></button>

                    </tr>
                </table>
            </div>
        </div>
        
    </div>
</div>

<script>
    
    function createGraficSemanal() {
        $("#divGraficVentasSemanales").empty();
        var div = $("#divGraficVentasSemanales");
        $('<canvas id="canvassemanal" height="450" width="1000">').appendTo(div);

        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Report&&action=ListaVentaSemanales",
            data: {
                filtromes: $('#cmbGraficDataMesSemana').val(),             
                filtroAnio: $('#cmbGraficAnioSemana').val(),
                filtrosemana: $('#cmbGraficSemana').val(),
                clase: $('#cmbGraficClaseSemana').val(),
                Idcategoria: $('#cmbGraficCategoriaSemana').val(),
                tipo_categoria: $('#cmbGraficTipoSemana').val()
            },
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                var pedido = new Array;
                var cantidad = new Array;
                var total = 0;
                for (var i = 0; i < data.length; i++) {
//                     console.log(data[i].mes)
                    var row = data[i];
                    pedido[i] = data[i].dia;
//                    total = total + parseFloat(row.importe);
                    cantidad[i] = data[i].Importe;

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

                var myLine = new Chart(document.getElementById("canvassemanal").getContext("2d")).Bar(barChartData);

            }

        });

    }
    
    
    
    
    
    
    
    
</script>


