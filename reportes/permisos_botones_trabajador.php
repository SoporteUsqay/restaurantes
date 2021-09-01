<?php
$titulo_pagina = 'Permisos Trabajador';
$titulo_sistema = 'Usqay2';

include_once('recursos/componentes/MasterConexion.php');
$conn = new MasterConexion();
$sucursal = "SU009";
$trabajador = "0";
if(isset($_GET["s"])){
    $sucursal = $_GET["s"];
}
if(isset($_GET["idt"])){
    $trabajador = $_GET["idt"];
}

$datos_sucursal = $conn->consulta_arreglo("Select * from sucursal where pkSucursal = '" . $sucursal . "'");
require_once('recursos/componentes/header.php'); 

$permisos = [
    [
        'btn-edit-price',
        'Botón de Editar Precio',
        '<button class="btn btn-default data-placement="bottom"><span class="glyphicon glyphicon-pencil"></span></button>'
    ],
    [
        'btn-change-plate',
        'Botón de Cambiar Plato',
        '<button class="btn btn-default" data-placement="bottom"><span class="glyphicon glyphicon-retweet"></span></button>'
    ],
    [
        'btn-refresh',
        'Botón de Actualizar Pedido',
        '<button class="btn btn-default" data-placement="bottom"><span class="glyphicon glyphicon-refresh"></span></button>'
    ],
    [
        'btn-messages',
        'Botón de Mensajes',
        '<button class="btn btn-default" data-placement="bottom" ><span class="glyphicon glyphicon-envelope"></span></button>'
    ],
    [
        'btn-anular-todo',
        'Botón de Anular Todo',
        '<button class="btn" data-placement="bottom" style="background-color:#ef6a00 !important;color: #fff">Anular Todos</button>'
    ],
    [
        'btn-anular-uno',
        'Botón de Anular 1',
        '<button class="btn" data-placement="bottom" style="background-color:#a9a10a !important;color: #fff">Anular 1</button>'
    ],
    [
        'btn-enviar-pedido',
        'Botón de Enviar Pedido',
        '<button class="btn" data-placement="bottom" style="background-color:#0086cf !important;color: #fff">Enviar Pedido</button>'
    ],
    [
        'btn-liberar-mesa',
        'Botón de Liberar Mesa',
        '<button class="btn usqay-btn-ex" data-placement="bottom"><img src="../Public/images/iconos2017/liberar_mesa.png" style="margin-bottom: 10px;max-height: 35px !important;"><br/></button>'
    ],
    [
        'btn-cambio-mesa',
        'Botón de Cambio de Mesa',
        '<button class="btn usqay-btn-ex" data-placement="bottom"><img src="../Public/images/iconos2017/cambio_mesa.png" style="margin-bottom: 10px;max-height: 35px !important;"><br/></button>'
    ],
    [
        'btn-consumo',
        'Botón de Consumo',
        '<button class="btn usqay-btn-ex" data-placement="bottom"><img src="../Public/images/iconos2017/consumo.png" style="margin-bottom: 10px;max-height: 35px !important;"><br/></button>'
    ],
    [
        'btn-credito',
        'Botón de Credito',
        '<button class="btn usqay-btn-ex" data-placement="bottom"><img src="../Public/images/iconos2017/credito.png" style="margin-bottom: 10px;max-height: 35px !important;"><br/></button>'
    ],
    [
        'btn-precuenta',
        'Botón de Pre Cuenta',
        '<button class="btn usqay-btn-ex" data-placement="bottom"><img src="../Public/images/iconos2017/precuenta.png" style="margin-bottom: 10px;max-height: 35px !important;"><br/></button>'
    ],
    [
        'btn-factura',
        'Botón de Factura',
        '<button class="btn usqay-btn-ex" data-placement="bottom"><img src="../Public/images/iconos2017/factura.png" style="margin-bottom: 10px;max-height: 35px !important;"><br/></button>'
    ],
    [
        'btn-boleta',
        'Botón de Boleta',
        '<button class="btn usqay-btn-ex" data-placement="bottom"><img src="../Public/images/iconos2017/boleta.png" style="margin-bottom: 10px;max-height: 35px !important;"><br/></button>'
    ],
    [
        'btn-ticket',
        'Botón de Ticket',
        '<button class="btn usqay-btn-ex" data-placement="bottom"><img src="../Public/images/iconos2017/efectivo.png" style="margin-bottom: 10px;max-height: 35px !important;"><br/></button>'
    ],

];

?>

<style>
.usqay-btn-ex {
    color: #FFF;
    background-color: #00395a;
    white-space: normal;
    overflow: hidden;
    text-overflow: ellipsis;
    border-radius: 5% !important;
    font-size: 13px !important;
}
</style>

<div class="panel panel-primary" id='pplatos'>
    <div class="panel-heading">
        <h3 class="panel-title">Asigne los Permisos de Botones al Trabajador</h3>
    </div>
    <div class="panel-body">
        
        <?php 

            foreach ($permisos as $permiso) {

        ?>
            <div class="form-group" style="margin-left:1em">

            <input type="checkbox" id="mod<?php echo $permiso[0];?>" onchange="change_module('<?php echo $permiso[0];?>')" 
                <?php 

                    $db = new SuperDataBase();

                    $query = "select count(*) as cantidad from trabajador_permisos where pkTrabajador = $trabajador and permiso = '$permiso[0]'";

                    $res = $db->executeQueryEx($query);

                    while ($row = $db->fecth_array($res)) {
                        if ($row['cantidad'] > 0) {
                            echo 'checked ';
                        }
                    }
                ?>>
            
            <label for="" style="margin: 0 1em">
            <?php echo $permiso[2];?>
            </label>
            <label for=""><?php echo $permiso[1];?></label>
            
            </div>
        <?php } ?>

        
        


        <div class='control-group'>
            <p></p>
            <button type='button' class='btn btn-primary' onclick='terminar()'>Terminar</button>
        </div>
    </div>
</div>
</form>

<div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%' border="0">
        <thead>
        </thead>
        <tbody>
<?php
$nombre_tabla = 'permisos';
require_once('recursos/componentes/footer.php');
?>

<script>
    function terminar(){
        location.href = "../?controller=WorkPeople&action=Personal";
    }
    
    function change_module(permiso) {

        let url = "<?php echo Class_config::get('urlApp') ?> /?controller=WorkPeople&action=ChangePermisoBoton"

        let action = "remove";

        if($("#mod"+permiso).is(':checked')) {
            action = "add"
        }

        $.post(url, {
            trabajador_id: "<?php echo $trabajador ?>",
            permiso,
            action
        }, function(data) {
            
        }, 'json');
       
    }

</script>


                            