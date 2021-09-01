<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="shortcut icon" href="../../assets/ico/favicon.ico">

        <title><?php echo Class_config::get('nameApplication') ?></title>
        <!--<link rel="stylesheet" type="text/css" href="Public/jquery-easyui/easyui/themes/default/easyui.css">-->
        <!--<link rel="stylesheet" type="text/css" href="Public/jquery-easyui/easyui/themes/icon.css">-->
        <link rel="stylesheet" href="Public/jquery-ui-1.10.4.custom/css/jquery-ui.min.css">

        <link href="Public/Bootstrap/css/bootstrap.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="Public/css/style.css">
        <!--<link rel="stylesheet" type="text/css" href="Public/css/style2.css">-->
        <!--<link rel="stylesheet" type="text/css" href="Public/jquery-easyui/easyui/demo.css">-->

        <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery-1.10.2.js"></script>
        <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery-ui-1.10.4.custom.js"></script>
        <!--<script src="//code.jquery.com/jquery-1.10.2.js"></script>-->
        <!--<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>-->
        <script type="text/javascript"src="Public/jquery-ui-1.10.4.custom/js/jquery.numeric.js"></script>
        <script type="text/javascript"src="Public/jquery-ui-1.10.4.custom/js/jquery.maskedinput.js"></script>
        <script type="text/javascript"src="Public/scripts/body.js.php"></script>
        <script type="text/javascript"src="Public/scripts/listGeneral.js.php"></script>
        <script type="text/javascript"src="Public/scripts/Validation.js.php"></script>
        <script  type="text/javascript" src="Public/Bootstrap/js/bootstrap.min.js"></script>

    --></head>
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
        <a href="?controller=Sale&action=ShowPromociones" >

            <label class="label label-success"><span class="glyphicon glyphicon-repeat" aria-hidden="true"></span> Atras</label> </a>
        <h3>Registrando una nueva promocion</h3>
        <form id='frmPromociones'>
            <!--Fecha Inicial <input name='fechaInicial' type="text" class="form-control date"> 
            Hora <input class="form-control" type="datetime">-->
            <!--Fecha Fin <input name='fechaFin' type="text" class="form-control date">
            Hora <input class="form-control" type="datetime">-->
            <div class="prom col-md-12 row">
                <div class="col-sm-2 col-md-2">
                    <label>NOMBRE PROMOCION</label>
                </div>
                <div class="col-sm-10 col-md-10">
                    <!--<input type="text" class="form-control" name="nomb_promo"/>-->
                     
            <input name='nomb_promo' class="form-control" id="txtDescripcionOferta">
           
                </div>
            </div>
            <div class="prom col-md-12 row">
                <div class="col-sm-2 col-md-2">
                    <label>Categoria</label>
                </div> 
            <div class="col-sm-10 col-md-10">
                <select class="form-control" id="cmbRegisterCategoriaPromocion" name="categoria" onchange="_loadTiposCategoria1('cmbRegisterCategoriaPromocion', 'cmbRegisterTipoPromocion')">
               
                </select>
            </div>
            
            
            </div>
            <div class=" prom col-md-12 row">
                <div class="col-sm-2 col-md-2">
                    <label>Tipo</label>
                </div> 
            <div class="col-sm-10 col-md-10">
                <select class="form-control" name="pkTipo" id="cmbRegisterTipoPromocion">
               
                </select>
            </div>
            
            
            </div>
            
           
            <div class="prom col-md-12 row">
                <div class="col-md-2">
                <label>PRODUCTO</label> 
                </div>
                <div class="inpus col-md-10">
                    <input class=" form-control " id="txtProductosPedido">
                    <input  type="text" name="txtProductosPedido-id" id="txtProductosPedido-id" hidden="true" style="display:none">
                    <input  type="text" name="txtProductosPedido-precio" id="txtProductosPedido-precio" hidden="true" style="display:none">
                    <input type="text" name="txtProductosPedido-descripcion" id="txtProductosPedido-descripcion" hidden="true" style="display:none">
                </div>
            </div>
            <br /><br />
            <div class="prom col-md-12 row">
                <div class="col-md-2">
                    <label>CANTIDAD</label>
                </div>
                <div class="inpus2 col-md-4">
                    <input class="form-control " placeholder="Agrege la Cantidad" id="txtcantidad"/>
                </div>
                <div class="col-md-6">
                   <a href="javascript:void(0)" onclick="agregarTabla()"><label>Agregar</label> </a>
                </div>
            </div>
            
            <table class="table table-hover" id="tablePedido">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th >Cantidad</th>
                        <th >Descripcion</th>
                        <th >Precio Actual</th>

                    </tr>
                </thead>
                <tbody>

                </tbody>    
            </table>
            <!--<button type="button" onclick="vertable()">Ver tabla</button>-->
            Precio de oferta <input name='precioVenta' class="form-control" type="number">
            <!--                        Tipo de Oferta<br>
                                    <input type="checkbox"> por precio 
                                    <input type="checkbox"> por cantidad  
                                    <input type="checkbox"> por cantidad y precio  -->
        </form>
        <button class="btn btn-primary" onclick="vertable()">Guardar</button>
    </div>


</body>

<script>
     _listCategoriasSucursal('cmbRegisterCategoriaPromocion');
    $(".date").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true});
    $(function () {

        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ListAllProduct",
            type: 'POST',
            dataType: 'json',
            success: function (data) {
//                var arr = Object.keys(data).map(function(k) { return data[k] });
//               
//                var obj = $.parseJSON(data);
//                console.log(arr +" "+data);
                $("#txtProductosPedido").autocomplete({
                    source: data,
                    messages: {
                        noResults: '',
                        results: function () {
                        }
                    },
                    select: function (event, ui) {
                        $("#txtProductosPedido").val(ui.item.label);
//                        console.log(ui.item.descripcion);
                        $("#txtProductosPedido-id").val(ui.item.value);
                        $("#txtProductosPedido-precio").val(ui.item.precio);
                        $("#txtProductosPedido-descripcion").val(ui.item.descripcion);
                        //                        $("#txtRegisterEmployed-description").html(ui.item.desc);
                        //                        $("#txtRegisterEmployed-icon").attr("src", "images/" + ui.item.icon);

                        return false;
                    }
                });
                //           
            }

        });
    });
    function agregarTabla() {
        var table = $('#tablePedido tbody');
        table.append("<tr><td>" + $("#txtProductosPedido-id").val() + "</td><td>" + $("#txtcantidad").val() + "</td><td>" + $("#txtProductosPedido-descripcion").val() + "</td><td>" + $("#txtProductosPedido-precio").val() + "</td></tr>");
        $('#txtDescripcionOferta').val($('#txtProductosPedido-descripcion').val() + "+" + $("#txtDescripcionOferta").val());
    }
    function guardarPromocion() {
          $("#tablePedido tbody tr").each(function (index) 
        {
            var campo1, campo2, campo3;
            $(this).children("td").each(function (index2) 
            {
                switch (index2) 
                {
                    case 0: campo1 = $(this).text();
                            break;
                    case 1: campo2 = $(this).text();
                            break;
                    case 2: campo3 = $(this).text();
                            break;
                }
                $(this).css("background-color", "#ECF8E0");
            })
            alert(campo1 + ' - ' + campo2 + ' - ' + campo3);
        })

//    $.post("<?php echo Class_config::get('urlApp') ?>?/controller=Sale&action=GuardarPromocion",$('#frmPromociones').serialize(), function (data) {
//     
//    });
    }
    function vertable(){
        contador=0;
        var $array= new Array();
        //Se crear el array para almacenar los datos de la tabla
        $("#tablePedido tbody tr").each(function (index) 
        {
            var campo1, campo2;
            $(this).children("td").each(function (index2) 
            {
                switch (index2) 
                {
                    case 0: campo1 = $(this).text();
                            break;
                    case 1: campo2 = $(this).text();
                            break;
                }
                $(this).css("background-color", "#ECF8E0");
            })
           // alert(campo1 + ' - ' + campo2);
          
           $array[contador]={"codigo":campo1,"cantidad":campo2};
            contador++;
        })
   //     window.open("<?php echo Class_config::get('urlApp')?>/probando.php?array="+JSON.stringify($array),"_blank");
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Sale&action=GuardarPromocion&array="+JSON.stringify($array),
            type: 'GET',
            data: $('#frmPromociones').serialize(),
            dataType: 'html',
            success: function (data) {
                alert("Se ha registrado su promocion");
//                window.location.href ="<?php echo Class_config::get('urlApp')?>/?controller=Sale&action=ShowPromociones"
                
            }
        });
    }
</script>
