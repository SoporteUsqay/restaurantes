<link rel="stylesheet" href="Public/jquery-ui-1.10.4.custom/css/jquery-ui.min.css">
</head>
<style>
    button,
    button:focus{
        outline: none !important;
    }
    .container2 {
        max-width: 600px;
        margin: 0 auto;
        padding-top: 10px;
        padding-left: 125px;
        height: 400px;
        text-align: center;
        float: left;
    }
    .container h1 {
        font-size: 40px;
        -webkit-transition-duration: 1s;
        transition-duration: 1s;
        -webkit-transition-timing-function: ease-in-put;
        transition-timing-function: ease-in-put;
        font-weight: 200;
        padding-bottom: 15px;
    }
    .cal button {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        outline: 0;
        background-color: white;
        border: 0;
        padding: 10px 15px;
        color: #53e3a6;
        border-radius: 3px;
        width: 73px;
        cursor: pointer;
        font-size: 38px;
        -webkit-transition-duration: 0.25s;
        transition-duration: 0.25s;
    }
    .cal button:hover {
        background-color: #f5f7f9;
    }
    .calEnter button {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        outline: 0;
        background-color: white;
        border: 0;
        padding: 10px 15px;
        color: #53e3a6;
        border-radius: 3px;
        width: 240px;
        cursor: pointer;
        font-size: 38px;
        -webkit-transition-duration: 0.25s;
        transition-duration: 0.25s;
    }
    .calEnter button:hover {
        background-color: #f5f7f9;
    }
    .btn-usqay-miau{
        background-color: #ef4d4d !important;
        color: #FFF !important;
        border-radius: 13px;
    }
    .btn-usqay-off-miau{
        background-color: #009839 !important;
        color: #FFF !important;
        border-radius: 13px;
    }
    .collapse-usqay-miau{
        background-color: #efeeee !important;
        border-radius: 13px;
    }
    .usqay-btn-miau{
        color: #FFF;
        background-color: #00395a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        border-radius: 50%;
        width: 150px;
        margin: 5px;
        height: 150px;
    }

    .usqay-btn-disable-miau{
        color: #FFF;
        background-color: #606060;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        border-radius: 50%;
        width: 150px;
        margin: 5px;
        height: 150px;
    }
    .usqay-btn-red-miau{
        background-color: #ef4d4d !important;
    }
    .usqay-btn-miau:hover{
        color: #ef4d4d !important;
    }
    .usqay-btn-red-miau:hover{
        color: #000 !important;
    }
    .usqay-color-miau{
        color: #FFF;
        background-color: #00395a;
    }
    .usqay-color-miau:hover{
        color: #ef4d4d !important;
    }
</style>
<script>
function visualizaTipoUsuarioSalon() {
  /*var tipe = "<?php echo UserLogin::get_pkTypeUsernames(); ?>";
  switch (tipe) {
    case "4":
    $('.43').hide();
    $('.44').hide();
    break;
    default:
    $('.43').show();
    $('.44').show();
    break;
  }*/
}

function cambia_sel(id_in){
    $(".btn-usqay-group-miau").removeClass("btn-usqay-off-miau").removeClass("btn-usqay-miau").addClass("btn-usqay-off-miau");
    $("#btn-salon-"+id_in).removeClass("btn-usqay-off-miau").addClass("btn-usqay-miau");
    $(".collapse-usqay-miau").removeClass("in");
    $("#collapse"+id_in).addClass("in");
}

 $( document ).ready(function() {
     $('.cnt-btn a').on('click',function(e){
         if($("#accordion").children('div').children('.panel-collapse').hasClass('in')){
             e.preventDefault();
             e.stopPropagation();
         }
     });
 });
</script>
<body>
  <div class="row">
    <div  class="col-md-12 cnt-btn" style="padding:10px;margin-top:-25px;">
    <center>
    <?php
    $db = new SuperDataBase();
    $sucursal = UserLogin::get_pkSucursal();
    $query = "SELECT * FROM salon s where pkSucursal='$sucursal' and estado = 0;";
    $resultSalones = $db->executeQuery($query);
    $toff = "";
    while ($rowSalones = $db->fecth_array($resultSalones)) {
        echo '<a class="' . $rowSalones[0] . ' btn btn-lg btn-usqay'.$toff.'-miau btn-usqay-group-miau" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse' . $rowSalones[0] . '" aria-miaupanded="false" aria-controls="collapse' . $rowSalones[0] . '" style="margin:5px;font-size:32px;padding:15px;" id="btn-salon-' . $rowSalones[0] . '" onclick="cambia_sel('.$rowSalones[0].')">' . $rowSalones['nombre'] . '</a>';
        $toff = "-off";
    }
    ?>
    </center>
    </div>
    <div class="col-md-12">
      <div id="divAperturarMesas">
        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="false">
          <?php
          $contadorAbiertos = 0;
          $db = new SuperDataBase();
          $sucursal = UserLogin::get_pkSucursal();
          $query = "SELECT * FROM salon s where pkSucursal='$sucursal' and estado=0;";
          $contador = 0;
          $resultSalones = $db->executeQuery($query);
          while ($rowSalones = $db->fecth_array($resultSalones)) {
            $in = "";
            if ($contador < 1) {
              $in = " in ";
            }
            echo'<div class="' . $rowSalones[0] . '">
            <div id="collapse' . $rowSalones[0] . '" class="collapse-usqay-miau panel-collapse collapse ' . $in . '" role="tabpanel" aria-labelledby="heading' . $rowSalones[0] . '">
            <div style="padding: 15px;">';
            if(intval($rowSalones["pkSalon"]) == 43 || intval($rowSalones["pkSalon"]) == 44){
              //Cuando es Delivery o Llevar
              echo'<button id="btnMesaTMP" hfre="#" onclick="concreta_cambio_mesa(\'' . $rowSalones['pkSalon'] . '\',\'TMP\', \'1\')" class="usqay-btn-miau btn btn-lg">Agregar<br/><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span></button>';

              $query = "SELECT * FROM mesas m  where  pkSalon=" . $rowSalones['pkSalon'] . " and estado<>3 order by pkMesa ASC;";
              $resultMesas = $db->executeQuery($query);
              while ($rowMesas = $db->fecth_array($resultMesas)) {
                //Cuando son salones normales
                $queryPedido = "SELECT pkPediido,idUser FROM pedido where estado = 0 AND pkMesa = '".$rowMesas["pkMesa"]."' order by pkPediido DESC LIMIT 1";
                $resultPedido = $db->executeQuery($queryPedido);
                while ($rowPedido = $db->fecth_array($resultPedido)) {
                    $pedido = $rowPedido['pkPediido'];
                    $id_usuario = $rowPedido["idUser"];
                    $queryTrabajador = "SELECT nombres,pkTrabajador from trabajador where pkTrabajador = '".$id_usuario."'";
                    $nombres = "";
                    $resultTrabajador = $db->executeQuery($queryTrabajador);
                    while ($rowTrabajador = $db->fecth_array($resultTrabajador)) {
                        $nombres = strtoupper($rowTrabajador['nombres']);
                    }
                    
                    //Obtenemos cliente
                    $nombre_cliente = "";
                    $query01 = "Select * from cliente_externo where id_pedido = '".$pedido."'";
                    $query02 = "Select * from pedido_cliente where pkPediido = '".$pedido."'";
                    $r1 = $db->executeQuery($query01);
                    $r2 = $db->executeQuery($query02);
                    $res = null;
                    if($row01 = $db->fecth_array($r1)){
                        $nombre_cliente = '<br/><span class="glyphicon glyphicon-user" aria-hidden="true"></span> '.strtoupper($row01['nombres_y_apellidos']);
                    }else{
                        if($row02 = $db->fecth_array($r2)){
                            if($row02['nombre_cliente'] <> ""){
                                $nombre_cliente = '<br/><span class="glyphicon glyphicon-user" aria-hidden="true"></span> '.strtoupper($row02['nombre_cliente']);
                            }
                        } 
                    }
  
                    if($rowMesas["pkMesa"] == $_REQUEST["mesaAnterior"]){
                      echo'<button id="btnMesa' . $rowMesas['pkMesa'] . '" onclick="#" class="usqay-btn-disable-miau btn btn-lg">' . $rowMesas['nmesa'] . '<br/><span class="glyphicon glyphicon-briefcase" aria-hidden="true"></span> '.$nombres.$nombre_cliente.'</button>';
                    }else{
                      echo'<button id="btnMesa' . $rowMesas['pkMesa'] . '" onclick="concreta_cambio_mesa(\'' . $rowSalones['pkSalon'] . '\',\'' . $rowMesas['pkMesa'] . '\', \'1\')" class="usqay-btn-miau btn btn-lg usqay-btn-red-miau">' . $rowMesas['nmesa'] . '<br/><span class="glyphicon glyphicon-briefcase" aria-hidden="true"></span> '.$nombres.$nombre_cliente.'</button>';
                    }
                }
                
              }
            }else{
              $query = "SELECT * FROM mesas m  where  pkSalon=" . $rowSalones['pkSalon'] . " and estado<>3 order by pkMesa ASC";
              $resultMesas = $db->executeQuery($query);
              while ($rowMesas = $db->fecth_array($resultMesas)) {
                //Cuando son salones normales
                $queryPedido = "SELECT pkPediido,idUser FROM pedido where estado = 0 AND pkMesa = '".$rowMesas["pkMesa"]."' order by pkPediido DESC LIMIT 1";
                $resultPedido = $db->executeQuery($queryPedido);
                $hay_pedido = 0;
                while ($rowPedido = $db->fecth_array($resultPedido)) {
                    $hay_pedido = 1;
                    $pedido = $rowPedido['pkPediido'];
                    $id_usuario = $rowPedido["idUser"];
                    $queryTrabajador = "SELECT nombres,pkTrabajador from trabajador where pkTrabajador = '".$id_usuario."'";
                    $nombres = "";
                    $resultTrabajador = $db->executeQuery($queryTrabajador);
                    while ($rowTrabajador = $db->fecth_array($resultTrabajador)) {
                        $nombres = strtoupper($rowTrabajador['nombres']);
                    }
                    
                    //Obtenemos cliente
                    $nombre_cliente = "";
                    $query01 = "Select * from cliente_externo where id_pedido = '".$pedido."'";
                    $query02 = "Select * from pedido_cliente where pkPediido = '".$pedido."'";
                    $r1 = $db->executeQuery($query01);
                    $r2 = $db->executeQuery($query02);
                    $res = null;
                    if($row01 = $db->fecth_array($r1)){
                        $nombre_cliente = '<br/><span class="glyphicon glyphicon-user" aria-hidden="true"></span> '.strtoupper($row01['nombres_y_apellidos']);
                    }else{
                        if($row02 = $db->fecth_array($r2)){
                            if($row02['nombre_cliente'] <> ""){
                                $nombre_cliente = '<br/><span class="glyphicon glyphicon-user" aria-hidden="true"></span> '.strtoupper($row02['nombre_cliente']);
                            }
                        } 
                    }
  
                    if($rowMesas["pkMesa"] == $_REQUEST["mesaAnterior"]){
                      echo'<button id="btnMesa' . $rowMesas['pkMesa'] . '" onclick="#" class="usqay-btn-disable-miau btn btn-lg">' . $rowMesas['nmesa'] . '<br/><span class="glyphicon glyphicon-briefcase" aria-hidden="true"></span> '.$nombres.$nombre_cliente.'</button>';
                    }else{
                      echo'<button id="btnMesa' . $rowMesas['pkMesa'] . '" onclick="concreta_cambio_mesa(\'' . $rowSalones['pkSalon'] . '\',\'' . $rowMesas['pkMesa'] . '\', \'1\')" class="usqay-btn-miau btn btn-lg usqay-btn-red-miau">' . $rowMesas['nmesa'] . '<br/><span class="glyphicon glyphicon-briefcase" aria-hidden="true"></span> '.$nombres.$nombre_cliente.'</button>';
                    }
                }
                
                //Si no hay pedido perdido mostramos vacia
                if($hay_pedido === 0){
                  if($rowMesas["pkMesa"] == $_REQUEST["mesaAnterior"]){
                    echo'<button id="btnMesa' . $rowMesas['pkMesa'] . '" hfre="#" onclick="#" class="usqay-btn-disable-miau btn btn-lg">' . $rowMesas['nmesa'] . '</button>';
                  }else{
                    echo'<button id="btnMesa' . $rowMesas['pkMesa'] . '" hfre="#" onclick="concreta_cambio_mesa(\''.$rowSalones['pkSalon'].'\',\'' . $rowMesas['pkMesa'] . '\', \'0\')" class="usqay-btn-miau btn btn-lg">' . $rowMesas['nmesa'] . '</button>';
                  }
                }
              }
            }
            echo'</div>
            </div>
            </div>';
            $contador++;
          }
          ?>
        </div>
      </div>

    </div>
    
  </div>

  <div id="modaConfirmAperturaMesa" class="modal fade">
    <div class="modal-dialog" >
      <div class="modal-content" >
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h3 class="modal-title" id="modaConfirmAperturaMesaTitle">Confirmar Apertura de Mesa</h3>
        </div>
        <div class="modal-body ">
          <input id="txtMesaApertura" style="display: none">
          <h4>Indique la cantidad de personas a ocupar -  <label id="lblNmesa"></label> </h4>

          <form id="frmAperturaMesa" >
            <input name="nmesa" id="txtCantidaPersonas" class="form-control" value="0">
          </form>
          <div class="container2">
            <form class="cal">
              <div style="float:left">
                <input  onclick="Cantidad('1', 'txtCantidaPersonas');" type="button" value="1" name="1" >
                <input  onclick="Cantidad('4', 'txtCantidaPersonas');" type="button" value="4" name="4" >
                <input  onclick="Cantidad('7', 'txtCantidaPersonas');" type="button" value="7" name="7" >
              </div>
              <div style="float:left; padding-left:10px">
                <input  onclick="Cantidad('2', 'txtCantidaPersonas');" type="button" value="2" name="2" >
                <input  onclick="Cantidad('5', 'txtCantidaPersonas');" type="button" value="5" name="5" >
                <input  onclick="Cantidad('8', 'txtCantidaPersonas');" type="button" value="8" name="8" >
                <input  onclick="Cantidad('0');" type="button" value="0" name="0" >
              </div>
              <div style="float:left; padding-left:10px">
                <input  onclick="Cantidad('3', 'txtCantidaPersonas');" type="button" value="3" name="3" >
                <input  onclick="Cantidad('6', 'txtCantidaPersonas');" type="button" value="6" name="6" >
                <input  onclick="Cantidad('9', 'txtCantidaPersonas');" type="button" value="9" name="9" >
              </div>
            </form>
          </div>

        </div>
        <!-- dialog buttons -->
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-primary" onclick="CargarMesa()">Aperturar mesa</button>
        </div>
      </div>
    </div>
  </div>
  <div id="modalAperturaMesaDelivery" class="modal fade">
    <div class="modal-dialog" >
      <div class="modal-content" >
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h2 class="modal-title" id="modalAperturaMesaDeliveryTitle">Datos del cliente</h2>
        </div>
        <!-- dialog body -->
        <div class="modal-body ">
          <form id="frmClientePLllevar" >
            <table>
              <tr>
                <td>Cliente</td>
                <td><input class="form-control reset" id="txtDniCliente" name="dni" placeholder="Ingrese Su DNI- Presione 'Enter para iniciar la busqueda'"></td>
              </tr>
              <tr>
                <td></td>
                <td><input id="txtNombres" class="form-control reset"name="nombres" placeholder="Ingrese el nombre del cliente"></td>
              </tr>
              <tr>
                <td></td>
                <td><input style="display: none" class="form-control reset"name="apellidos" id="txtApellidos" placeholder="Ingrese el apellido del cliente"></td>
              </tr>
            </tr>
          </table>
        </form>
      </div>
      <!-- dialog buttons -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="CargarMesa()">Aperturar mesa</button>
      </div>
    </div>
  </div>
</div>

<?php 

 
$db = new SuperDataBase();

$query =  "SELECT descripcionInsumo, stockMinimo FROM insumos ";
$result = $db->executeQuery($query);


?>

<div id="modalAlertaStock" class="modal fade">
    <div class="modal-dialog" >
      <div class="modal-content" >
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
          <h2 class="modal-title" id="modalAperturaMesaDeliveryTitle">Alerta Stock Minimo</h2>
        </div>
        <!-- dialog body -->
        <div class="modal-body ">
             <h1>Te estas quedando sin stock, verifica tus insumos</h1>
             <h3>Producto <?php echo $row['descripcionInsumo']; ?></h3>
             <h3>Stock Minimo <?php echo $row['stockMinimo']; ?></h3>
        </div>
      <!-- dialog buttons -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>

<script>

  if($row['stockMinimo'] < 5){
    $('#modalAlertaStock').modal('hide');
  }else{
    $('#modalAlertaStock').modal('close');
  }
 


visualizaTipoUsuarioSalon();
function msfConfirmar($mesa, $nombre, $pkSalon) {
  $("#lblNmesa").html($nombre);
  $("#txtMesaApertura").val($mesa);
  if ($pkSalon === "44" || $pkSalon === "43") {
    $('#modalAperturaMesaDelivery').modal('show');
  }
  else
  $('#modaConfirmAperturaMesa').modal('show');
}

</script>
</body>

</html>
