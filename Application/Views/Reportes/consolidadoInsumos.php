<?php include 'Application/Views/template/header.php'; ?>

<body>
    <?php
    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showContent();
    ?>   

    <div class="container">

        <br /><br /><br />
        <h1>Consolidado de Salida de Insumos</h1>
        <div class="panel panel-primary" id='pfecha'>
            <div class="panel-heading">
                <h3 class="panel-title">Filtros por fechas</h3>
            </div>
            <div class="panel-body">

                <div class='control-group' id="dinicio">
                    <label>Fecha Inicio</label>
                    <input id="txtfechaini" type="text" class='form-control' placeholder='AAAA-MM-DD' id='fecha_inicio' name='fecha_inicio' value="<?php echo date('Y-m-d') ?>"/>
                    <br>
                    <button onclick="buscar()" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Buscar</button>
                    <button style="float: right" onclick="consolidadoInsumosExcel()" class="btn btn-success">Exportar a Excel</button>
                </div>
            </div>
        </div>


        <table id="tblInsumos" title="Insumos" class="display dataTable no-footer" >
            <thead>
                <tr>
                    <th style="visibility: hidden; display: none">ID</th>
                    <th>Descripcion</th>        
                    <th>Total</th>
                    <th>Unidad de Medida</th>            
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>        

    <script type="text/javascript" src="Application/Views/Reportes/js/ReporteConsolidadoInsumos.js.php" ></script>

</body>