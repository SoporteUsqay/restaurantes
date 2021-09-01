<ul class="nav nav-tabs">
    <li class="active"><a href="#home" data-toggle="tab">Registrar</a></li>
    <li><a href="#profile" data-toggle="tab">Listar</a></li>
    <!--<li><a href="#messages" data-toggle="tab">Messages</a></li>-->
    <!--<li><a href="#settings" data-toggle="tab">Settings</a></li>-->
</ul>

<div class="tab-content">

    <!--INICIO DE PRIMERA PESTAÑA-->
    <div class="tab-pane active" id="home">
        <?php
        $db = new SuperDataBase();
        $query = "SELECT * FROM categoria c;";
        $resul = $db->executeQuery($query);

        while ($row = $db->fecth_array($resul)) {
//            echo '<div class="col-md-4">';
            echo '<button onclick="javascript:loadItemsTipoPlato(' . $row['pkCategoria'] . ')"><br><center>' . $row['description'] . '</center></button>';
//            echo'</div>';
        }
        ?>
        <br>
        <div id="contenTipoPlato">

        </div>
        <div>
            <form>
                Titulo del item<input type="text" class="form-control" >
                Descripcion del item<textarea class="form-control" ></textarea>
                Precio del Tamañao Personal<input class="form-control"  type="text">
                Precio del Tamañao Mediano<input type="text" class="form-control" >
                Precio del Tamañao Familiar<input type="text" class="form-control" >
<!--                Precio del Tamañao Familiar<input type="text">-->
                Subir una imagen<input type="file" class="form-control" >
                <input type="submit" value="Guardar">
            </form>
        </div>

    </div>
    <!--FIN DE PRIMERA PESTAÑA-->

    <div class="tab-pane" id="profile">...</div>
    <div class="tab-pane" id="messages">...</div>
    <div class="tab-pane" id="settings">...</div>
</div>

<script>
    $(function() {
        $('#myTab a:last').tab('show');
    });

    function loadItemsTipoPlato($pkCategoria) {
        var param = {'pkCategoria': $pkCategoria};
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=TipoPlato&&action=List",
            type: 'POST',
            data: param,
            dataType: 'json',
            success: function(data) {
                console.log(data);
                $div = $('#contenTipoPlato');
                $div.empty();
                for (var i = 0; i < data.length; i++) {
                    $('<button onclick=""> <br>' + data[i].descripcion + '</button>').appendTo($div);


                }
                $('<div id="form"></div>').appendTo($div);
            }

        });


    }

</script>