<?php 
$titulo_importante = "Monto Inicial";
include 'Application/Views/template/header.php'; ?>
<body>

    <?php
    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showContent();

    include_once('reportes/recursos/componentes/MasterConexion.php');
    $conn = new MasterConexion();

    $monedas = $conn->consulta_matriz("Select * from moneda where estado > 0");
    ?>
    <div class="container-fluid">    
    <br><br>
    <br><br>

        <div class="panel panel-primary">
        <div class="panel-heading">
        <h3 class="panel-title">Monto Inicial y Tipo de Cambio</h3>
        </div>
        <div class="panel-body row">
        <form id="ini_form">
        <?php 
        if(is_array($monedas)):
            foreach($monedas as $mon):?>
            <div class='control-group col-lg-6'>
                <label>Monto Inicial <?php echo $mon["simbolo"];?></label>
                <input class="form-control" id="inicial_<?php echo $mon["id"];?>" name="inicial_<?php echo $mon["id"];?>" type="numeric" value="0">
            </div>
        <?php
            endforeach;
        endif;
        ?>
        <?php 
        if(is_array($monedas)):
            foreach($monedas as $mon):
                if(intval($mon["id"])>1):?>
            <div class='control-group col-lg-6'>
                <label>Cambio <?php echo $mon["simbolo"];?></label>
                <input class="form-control" id="moneda_<?php echo $mon["id"];?>" name="moneda_<?php echo $mon["id"];?>" type="numeric" value="0">
            </div>
        <?php
                endif;
            endforeach;
        endif;
        ?>
        </form>
        <div class='control-group col-lg-6'>
            <br/>
            <button type='button' class='btn btn-primary' onclick='registraMontoInicialDiario()'>Guardar</button>
        </div>

        </div>
        </div>
        </div>

<script>
MosotrarMonotInicialDiario();
    function MosotrarMonotInicialDiario() {
        $.ajax({
            type: "GET",
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=GastosDiarios&&action=ListMontoInicial",
            dataType: 'json',
            success: function (data)
            {
                <?php 
                if(is_array($monedas)){
                    foreach($monedas as $mon){
                        echo '$("#inicial_'.$mon["id"].'").val(data.ini_'.$mon["id"].');';
                    }
                }
                echo"
                ";
                ?>
                <?php 
                if(is_array($monedas)){
                    foreach($monedas as $mon){
                        if(intval($mon["id"])>1){
                            echo '$("#moneda_'.$mon["id"].'").val(data.mon_'.$mon["id"].');';
                        }
                    }
                }
                echo"
                ";
                ?>
            }
        });
    }

    
    function registraMontoInicialDiario() {
        $.ajax({
            type: "GET",
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=GastosDiarios&&action=SaveMontoInicial",
            data: $("#ini_form").serialize(), // Adjuntar los campos del formulario enviado.
            dataType: 'html',
            success: function (data)
            {
                $.messager.show({
                    title: 'Estado',
                    msg: "Se ha Registrado el monto inicial Correctamente"
                });
                location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Caja&&action=ShowCierreDiario";
            }

        });
    }
</script>