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
        <center><h3>Listado de promociones</h3></center>
        <a href="?controller=Sale&action=ShowRegisterPromociones">
            
            <label class="label label-success"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>Registrar</label> </a>
        
        <br>
        <table class="table table-condensed">
            <thead>
                <tr>
                    <th>Id</th>
                    <th >Promoción</th>
                    <th >Cantidad</th>
                    <th >Precio Vena</th>
                    
                </tr>
            </thead>
            <tbody>
<?php
                            $db = new SuperDataBase();
                            $query = "SELECT id, p.pkPlato, pkProducto, cantidad, pkPromocion, upper(p.descripcion)as descripcion, precioVenta FROM detalle_producto d inner join  (plato_sucursal pl inner join plato p on p.pkPlato=pl.pkPlato ) on pl.pkPlato=d.pkPromocion group by pkPromocion;";
                            $result = $db->executeQuery($query);
                            while ($row = $db->fecth_array($result)) {
                                echo "<tr>";
                                echo "<td>";
                                echo $row['pkPromocion'];
                                echo "</td>";
                                echo "<td>";
                                echo $row['descripcion'];
                                echo "</td>";
                                echo "<td>";
                                echo $row['cantidad'];
                                echo "</td>";
                                echo "<td>";
                                echo $row['precioVenta'];
                                echo "</td>";
                                echo "<td>";
                                echo "<a>Ver Detalles</a>";
                                echo "</td>";
                                echo "<td>";
                                echo "<a><span class='glyphicon glyphicon-remove'></span></a>";
                                echo "</td>";
                                echo "<td>";
                                echo "<a><span class='glyphicon glyphicon-pencil'></span></a>";
                                echo "</td>";
                                
                                echo "</tr>";
                            }
                            ?>
            </tbody>     
        </table>
    </div>

   
   
</body>

<script>
    
    $(function () {
//        var available = [
//            "ActionScript",
//            "AppleScript",
//            "Asp",
//            "BASIC",
//            "C",
//            "C++",
//            "Clojure",
//            "COBOL",
//            "ColdFusion",
//            "Erlang",
//            "Fortran",
//            "Groovy",
//            "Haskell",
//            "Java",
//            "JavaScript",
//            "Lisp",
//            "Perl",
//            "PHP",
//            "Python",
//            "Ruby",
//            "Scala",
//            "Scheme"
//        ];
        ;
//        $("#txtProductosPedido").autocomplete({
//            source: available
//        });
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ListAllProduct",
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                $("#txtProductosPedido").autocomplete({
                    source: data,
                    select: function (event, ui) {
                        $("#txtProductosPedido").val(ui.item.descripcion);
//                        console.log(ui.item.descripcion);
                        $("#txtProductosPedido-id").val(ui.item.id);
                        //                        $("#txtRegisterEmployed-description").html(ui.item.desc);
                        //                        $("#txtRegisterEmployed-icon").attr("src", "images/" + ui.item.icon);

                        return false;
                    }
                });
                //           
            }

        });
    });

</script>
<style>
    .ui-widget-content{
        z-index: 999999;
    }
</style>