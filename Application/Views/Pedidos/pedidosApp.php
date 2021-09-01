<?php
if (isset($_GET['cliente'])) {
  setcookie("cliente", $_GET['cliente'], time() + 360000, '/');
}
?>
<!DOCTYPE html>
<?php
$objUserSystem = new UserLogin();
$db = new SuperDataBase();
?>
<html lang="en">
<head>
  <meta http-equiv="Expires" CONTENT="0">
  <meta http-equiv="Cache-Control" CONTENT="no-cache">
  <meta http-equiv="Pragma" CONTENT="no-cache">
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title><?php echo Class_config::get('nameApplication') ?></title>

  <link rel="stylesheet" type="text/css" href="Public/jquery-easyui/easyui/themes/default/easyui.css">
  <link rel="stylesheet" type="text/css" href="Public/jquery-easyui/easyui/themes/icon.css">
  <!-- Bootstrap core CSS -->
  <link rel="stylesheet" href="Public/jquery-ui-1.10.4.custom/css/jquery-ui.min.css">
  <link href="Public/Bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="Public/css/style.css">
  <link href="Public/jquery-ui-1.10.4.custom/css/jquery.keyboard.css" rel="stylesheet">
  <link rel="icon" href="logo.ico"/>
  <link rel="stylesheet" href="Public/Usqay/css/usqay.css">
  <link rel="stylesheet" type="text/css" href="Public/css/perfect-scrollbars.css">
  <style>   
        button,
        button:focus{
            outline: none !important;
        }
        
      #navegacionMenu{
        overflow-x: hidden;
        overflow-y: hidden;
        text-align: center;
        position: relative;
      }
      #divMensajes{
        overflow-x: hidden;
        overflow-y: hidden;
        text-align: center;
        position: relative;
      }
      .usqay-btn{
            color: #FFF;
            background-color: #0086cf;
            white-space: normal;
            overflow: hidden;
            text-overflow: ellipsis;
            border-radius: 50% !important;
            width: 135px;
            margin: 5px;
            height: 135px;
            font-size: 13px;
            font-weight: 800;
        }
        .usqay-btn-red{
            background-color: #ef4d4d !important;
        }
        .usqay-btn:hover{
            color: #ef4d4d !important;
        }
        .usqay-btn-red:hover{
            color: #000 !important;
        }
        .usqay-color{
            color: #FFF;
            background-color: #00395a;
        }
        .usqay-color:hover{
            color: #ef4d4d !important;
        }
        .panel-pedido #panel-productos {
           height:auto !important;
        }
        
        .usqay-btn-ex{
            color: #FFF;
            background-color: #00395a;
            white-space: normal;
            overflow: hidden;
            text-overflow: ellipsis;
            border-radius: 5% !important;
            margin: 5px;
            width: 130px;
            height: 80px;
            font-size: 13px !important;
        }
        .usqay-btn-ex:hover{
            color: #ef4d4d !important;
        }
        
        .usqay-btn-exx{
            color: #FFF;
            background-color: #00395a;
            white-space: normal;
            overflow: hidden;
            text-overflow: ellipsis;
            margin: 5px;
            width: 135px;
            height: 80px;
            font-weight: bold;
        }
        .usqay-btn-exx:hover{
            color: #ef4d4d !important;
        }
  </style>

  <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery-1.10.2.js"></script>
  <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery-ui-1.10.4.custom.js"></script>
  <script type="text/javascript" src="Public/jquery-easyui/easyui/jquery.easyui.min.js"></script>
  <script type="text/javascript"src="Public/jquery-ui-1.10.4.custom/js/jquery.numeric.js"></script>
  <script type="text/javascript"src="Public/jquery-ui-1.10.4.custom/js/jquery.maskedinput.js"></script>
  <script type="text/javascript"src="Public/scripts/listGeneral.js.php"></script>
  <script type="text/javascript"src="Public/scripts/Validation.js.php"></script>
  <script  type="text/javascript" src="Public/Bootstrap/js/bootstrap.js"></script>
  <script  type="text/javascript" src="Public/Bootstrap/js/bootstrap.min.js"></script>
  <script type="text/javascript"  src="Public/scripts/Pedidos/MensajesApp.js.php"></script>
  <script type="text/javascript"  src="Public/scripts/perfect-scrollbars.js"></script>
</head>

<body onload="openMesa(<?php echo $_GET['pkMesa'] ?>);nobackbutton();" style="background-color: #f2f4f5;">
  <input type="hidden" id="terminal" value="<?php echo $_COOKIE['t'] ?>"/>
  <div class="navbar navbar-inverse navbar-fixed-top" role="navigation" style="background-color: #00395a !important; border-color: #00395a !important; border-bottom: #ef4d4d solid 5px !important;">
    <div class="container-fluid">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
          <span class="sr-only">Ver Menu</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a style="width:110px;margin-top: -2px;padding:15px 5px;" class="navbar-brand" href="<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome&m=<?php echo date("U");?>"><img src="<?php echo Class_config::get('urlApp') ?>/Public/images/usqay-large-inverse.svg" style="width:100%;"/></a>
      </div>
      <div class="navbar-collapse collapse">
        <ul class="nav navbar-nav navbar-right btn-xs">
          <li class="active"><a style="background-color: #00395a !important;" href="<?php echo Class_config::get('urlApp') ?>/?controller=User&action=CloseSession"><span class="glyphicon glyphicon-off"></span> Salir</a></li>
        </ul>
      </div><!-- /.nav-collapse -->
    </div><!-- /.container -->
  </div><!-- /.navbar -->

  <!--Inicio Modal-->
<div class='modal fade' id='modal_envio_anim' tabindex='-1' role='dialog' data-keyboard="false" data-backdrop="static" style="z-index: 999999 !important;">
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title' id='myModalLabel'>Procesando</h4>
            </div>
            <div class='modal-body'>
                <center>
                    <img src="Public/images/pacman.gif">
                </center>
            </div>
        </div>
    </div>
</div>
<!--Fin Modal-->


<style>
.labelTotal{
  font-family: Comic Sans;
  color: #000000;
  size: 20px;
  font-size: 25px;
}
.descuento
.reset
.letraPago{
  font-size: 25px;
}
.tipos {
    font-weight: bold !important;
    font-size: 20px !important;
    padding: 5px !important;
}
</style>

<input id="documentoCliente" style="display: none;">
<input id="salon" style="display: none;">

<div class="col-lg-12" style="padding-left: 5px !important; padding-right: 5px !important; margin-top: 65px !important;">
  <!-- Nav tabs -->
    <div><label id="lblnMesa"></label>  <label>
    <?php 
    $query_moso = "Select t.nombres, t.apellidos from pedido p, detallepedido dp, trabajador t where p.pkMesa = '".$_GET["pkMesa"]."' AND p.estado = 0 AND dp.pkPediido = p.pkPediido AND dp.pkMozo = t.pkTrabajador LIMIT 1";
    $r= $db->executeQuery($query_moso);
    if ($rw = $db->fecth_array($r)) {
        echo " - ".$rw['nombres']." ".$rw["apellidos"];
    }
    ?>
    </label></div>
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active" style="width:50%;font-weight: bold; text-align: center;">
        <a href="#carta" aria-controls="carta" role="tab" data-toggle="tab">Carta</a>
    </li>
    <li role="presentation" id="tabPed" style="width:50%;font-weight: bold; text-align: center;">
        <a href="#pedido" aria-controls="pedido" role="tab" data-toggle="tab">Pedido</a>
    </li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
      <div role="tabpanel" class="tab-pane active" id="carta">
        <!-- panel que lista categorias -->
        <div class="panel panel-pedido" id="cartaSys">
          <div class="panel-heading">
            <input id="TxtdescripcionProduct" type="text" class="form-control" placeholder=" Ingrese la descripcion del producto, luego presiones 'ENTER'">
          </div>
          <div class="row" style="font-weight:bold;font-size:16px;padding-left:30px;padding-top:10px;" id="navegacionActual">Categorías</div>
          <div class="panel-body" id="navegacionMenu" style="height: 100% !important;">

          </div>
        </div>
        <div class="panel panel-pedido" id="contenidoMenu" style="display:none;padding:15px;">
        <h4><b>Arma el menú</b></h4>
        <hr/>
        <div class="row" id="componente" style="margin: 0px !important;">

        </div> 
        <p></p>
        <div class="row" style="height: 10px !important;"></div>
        <div>
        <hr/>
        <span style="float: left !important;font-size: 20px; font-weight: bold;">Total: S/ <span id="total_menu">0.00</span></span>
        <!-- Aqui poner total-->
        <button type="button" class="btn btn-primary" onclick="agrega_menu()"  style="float:right !important;margin-left:5px;">Agregar</button>
        <button type="button" class="btn btn-danger" onclick="regresa_menu()" style="float:right !important;margin-left:5px;">Cancelar</button>
        </div>
        </div>
        <div class="panel" id="tecladoCantidad" style="display:none;padding:15px !important;">
        <span style="font-size:18px;font-weight:bold;" id="d_nombre_plato">...</span>
        <br/>
        <span style="font-size:18px;font-weight:bold;" id="d_precio_plato">...</span>
        <br/>
        Cantidad
        <br/>
        <input style="height: 30px;margin-bottom: 10px;" id="txtCantidadSend" min="1"  value="1" class="form-control">
        <input id="txtPkPedido" style="display: none">
        <input id="txtPrecioSend" style="display: none">
        <input id="txtTipoSend" style="display: none">
            <script>
            contador = 0;
            function Cantidad2($val, $id) {
              console.log(contador);
              if (contador === 0) {
                $('#' + $id).val("");
              }

              var $text = $('#' + $id).val();
              $('#' + $id).val($text + $val);
              contador++;
            }

            function resetP(){
                $("#txtCantidadSend").val("1");
            }
            </script>
              <div id="calc-board" style="text-align: center;">
                <form>
                  <div class="row-fluid">
                    <a  onclick="Cantidad2('1', 'txtCantidadSend');" href="#" class="btn btn-cal">1</a>
                    <a  onclick="Cantidad2('2', 'txtCantidadSend');" href="#" class="btn btn-cal">2</a>
                    <a  onclick="Cantidad2('3', 'txtCantidadSend');" href="#" class="btn btn-cal">3</a>
                  </div>
                  <div class="row-fluid">
                    <a href="#"  onclick="Cantidad2('4', 'txtCantidadSend');" class="btn btn-cal">4</a>
                    <a href="#"  onclick="Cantidad2('5', 'txtCantidadSend');" class="btn btn-cal">5</a>
                    <a href="#"  onclick="Cantidad2('6', 'txtCantidadSend');" class="btn btn-cal">6</a>
                  </div>
                  <div class="row-fluid">
                    <a href="#" onclick="Cantidad2('7', 'txtCantidadSend');" class="btn btn-cal">7</a>
                    <a href="#"  onclick="Cantidad2('8', 'txtCantidadSend');" class="btn btn-cal">8</a>
                    <a href="#" onclick="Cantidad2('9', 'txtCantidadSend');" class="btn btn-cal">9</a>
                  </div>
                  <div class="row-fluid">
                    <a href="#" class="btn btn-cal"> </a>
                    <a  onclick="Cantidad2('0', 'txtCantidadSend');" href="#" class="btn btn-cal">0</a>
                    <a href="#" class="btn btn-cal"> </a>
                  </div>
                  <div class="row-fluid">
                      <a  style="padding-top:15px;background-color:#ef4d4d !important;color:#FFF !important;" onclick="regresarP()" href="#" class="btn btn-cal"><span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span></a>
                    <a  style="padding-top:15px;background-color:#ef4d4d !important;color:#FFF !important;" onclick="resetP()" href="#" class="btn btn-cal"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                    <a style="padding-top:15px;background-color:#009839 !important;color:#FFF !important;" href="#" class="btn btn-cal" onclick="addPedido()"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></a>
                  </div>
                </form>
            </div>
            
        </div>
    </div>
      <div role="tabpanel" class="tab-pane" id="pedido">
      <div class="panel" id="panelOperativo">
          <input style="display: none" id="txtCombrobante" type="text" readonly="true">
          <div class="panel">
              <table class="table table-bordered ">
                <tr>
                  <td>Número Personas:</td>
                  <td> <input placeholder="Numero de personas en la mesa" title="Cantidad de Personas Ocupando la mesa" id="txtnPesonas"  onblur="updateNPersonas()"  class="form-control easyui-tooltip numerico" data-toggle="tooltip" data-placement="top" type="text" maxlength="2">
                  </td>
                  <td>
                    <button title="Elegir un mensaje para el pedido que seleccione"  onclick="showMessages()" class="btn btn-default easyui-tooltip" data-placement="bottom"><span class="glyphicon glyphicon-envelope"></span> Mensaje</button>
                  </td>
                </tr>
                <tr>
                    <td>
                    Cliente:
                    </td>
                    <td>
                    <label id="lblNombreCliente"></label>
                    <input type="hidden" id="tipoPedidoC"/>
                    <input type="hidden" id="idClienteC"/>
                    <input type="hidden" id="documentoClienteC"/>
                    <input type="hidden" id="nombresClienteC"/>
                    <input type="hidden" id="telefonoClienteC"/>
                    <input type="hidden" id="direccionClienteC"/>
                    </td>
                    <td>
                    <a href="#" onclick="editaClienteP()" style="margin-left:5px !important;"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
                    </td>
                </tr>
              </table>
              <table id="tblcomprobante" class="easyui-datagrid" title="Listado de Pedidos" style="font-size: 20px;width:max-content;height:200px"
              data-options="
              iconCls: 'icon-edit',
              singleSelect: true,
              url: '<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ListPedidosItem',
              method:'get',
              fitColumns: true,
              onClickCell: onClickCell,
              onLoadSuccess:sumaTotal,
              onClickRow:loadMessage,
              onAfterEdit:saveChanges
              ">
              <thead>
                <tr>
                  <th data-options="field:'ck',checkbox:true,cheked:true"></th>
                  <th data-options="field:'pedido',width:250,editor:'text'">Pedido</th>
                  <th data-options="field:'precio',editor:{type:'numberbox',options:{precision:1}}">Precio</th>
                  <th data-options="field:'cantidad',align:'right',editor:{type:'numberbox',options:{precision:1}}">Cant.</th>
                  <th data-options="field:'importe',align:'right',editor:{type:'numberbox',options:{precision:1}}">Importe</th>
                  <th data-options="field:'mensaje',align:'right',hidden:'true'">Mensaje</th>
                  <th data-options="field:'pkPedido',align:'right',hidden:'true'">Id</th>
                  <th data-options="field:'Destado',align:'right',styler:cellStylerPedido">Estado</th>
                  <th data-options="field:'Tipo',align:'right',hidden:'true'">Tipo</th>
                  <th data-options="field:'pkProducto',align:'right',hidden:'true'">pkProducto</th>
                  <th data-options="field:'estado',align:'right',hidden:'true'">Estado</th>
                  <th data-options="field:'tipoPedido',align:'right',hidden:'true'">Para</th>
                  <th data-options="field:'mozo',align:'right',hidden:'true'">Id</th>
                </tr>
              </thead>
            </table>

            <table class="table" style="font-size: 15px;background: #e8e8e8; color:#73777a">
              <tr>
                <td class="descnuento">Descuento Global</td>
                <td class="descuento" colspan="2">
                  <input name="descuento" onchange="sumaTotal()" class="form-control numerico descuento"
                  id="txtDescuento" type="text" value="0">
                </td>
              </tr>
              <tr>
                <td>Sub Total<br><label id="lblSubTotal">S/ 00.00</label></td>
                <td>DSC pre IGV<br><label id="lblDesC">S/ 00.00</label></td>
                <td>Sub Total c/DSC<br><label id="lblSubTotalD">S/ 00.00</label></td>
                <td>IGV<br><label id="lblIgv">S/ 00.00</label></td>
                <td>Total<br><label id="lblTotal">S/ 00.00</label></td>
              </tr>
            </table>

            <table class="table">
              <tr>
                <td valign="middle" align="center">
                  <button class="botonesIcon btn-pedido btn usqay-btn-ex btn-lg descuento"
                          title="Cancela en efecctivo el pedido de la mesa" 
                          onclick="openModal();">
                      <img src="Public/images/iconos2017/efectivo.png" style="margin-bottom: 10px;max-height: 35px !important;"><br/>
                      Efectivo           
                  </button>
                  <button class="botonesIcon btn-pedido btn usqay-btn-ex btn-lg descuento"
                          title="Cancelar con tarjeta o efectivo la cuenta" 
                          onclick="openModalCancelCuentaTarjeta();">
                      <img src="Public/images/iconos2017/tarjeta.png" style="margin-bottom: 10px;max-height: 35px !important;"><br/>
                      Tarjeta
                  </button>
                  <button class="botonesIcon btn-pedido btn usqay-btn-ex btn-lg descuento"
                          onclick="openCredito();">
                      <img src="Public/images/iconos2017/credito.png" style="margin-bottom: 10px;max-height: 35px !important;"><br/>
                      CRÉDITO
                  </button>
                  <button class="botonesIcon btn-pedido btn usqay-btn-ex btn-lg descuento"
                          onclick="openConsumo();">
                      <img src="Public/images/iconos2017/consumo.png" style="margin-bottom: 10px;max-height: 35px !important;"><br/>
                      Consumo
                  </button>
                    <button class="botonesIcon btn-pedido btn usqay-btn-ex btn-lg descuento caja2"
                          onclick="openModalChangeMesa(<?php echo $_GET['pkMesa']; ?>);"
                          title="Cambiar el pedido a otra mesa desocupada">
                        <img src="Public/images/iconos2017/cambio_mesa.png" style="margin-bottom: 10px;max-height: 35px !important;"><br/>
                        Cambio Mesa
                    </button>
                  <button class="botonesIcon btn-pedido btn usqay-btn-ex btn-lg descuento"
                          onclick="openModalGenerarComprobante();"
                          title="Generar comprobante - Boleta">
                      <img src="Public/images/iconos2017/boleta.png" style="margin-bottom: 10px;max-height: 35px !important;"><br/>
                      Boleta
                  </button>
                  <button class="botonesIcon btn-pedido btn usqay-btn-ex btn-lg descuento"
                          onclick="openModalGenerarComprobanteFactura();">
                  <img src="Public/images/iconos2017/factura.png" style="margin-bottom: 10px;max-height: 35px !important;"><br/>
                  Factura
                  </button>
                  <button class="botonesIcon btn-pedido btn usqay-btn-ex btn-lg " title="Imprimir la cuenta"
                          onclick="openImprimirCuenta()" >
                   <img src="Public/images/iconos2017/precuenta.png" style="margin-bottom: 10px;max-height: 35px !important;"><br/>   
                   PreCuenta
                  </button>
                  <button class="botonesIcon btn-pedido btn usqay-btn-ex btn-lg" title="Recarga los pedidos registrados"
                          onclick="loadPedido(<?php echo $_REQUEST['pkMesa'] ?>)">
                  <img src="Public/images/iconos2017/recargar.png" style="margin-bottom: 10px;max-height: 35px !important;"><br/>  
                  Recargar
                  </button>
                  <button class="botonesIcon btn-pedido btn usqay-btn-ex btn-lg descuento caja" onclick="confirmCancelaMesa()">
                  <img src="Public/images/iconos2017/liberar_mesa.png" style="margin-bottom: 10px;max-height: 35px !important;"><br/>  
                  Libera Mesa
                  </button>
                  <button class="botonesIcon btn-pedido btn usqay-btn-ex btn-lg " style="background-color:#0086cf !important;" onclick="confirImpresion(2)">Enviar Pedido</button>

                  <button class="botonesIcon  btn usqay-btn-ex btn-lg btn-pedido" style="background-color:#ef4d4d !important;" onclick="_anulaPedido('tblcomprobante')"
                          title="Anula un item que el cliente ya no desea llevar o consumir">Anular</button>           
                </td>
              </tr>
            </table>
          </div>
      </div>
      <div class="panel row" id="panelMensajes" style="display:none;margin:0px !important;">
          <div class="col-lg-6">
            <p></p>
            <p><b>Pedido:</b> <span id="nombre_producto"></span></p>
            <p><b>Mensaje Actual:</b> <span id="mensaje_producto"></span></p>
            <hr/>
            <b>Nuevo mensaje</b>
            <p></p>
            <textarea  class="form-control teclado" id="textAreaMessage"></textarea>
            <p></p>
            <button onclick="saveMessagePedido($('#textAreaMessage').val())" class="btn btn-default">
              Guardar
            </button>
            <button onclick="regresarMensajes()" class="btn btn-danger">
                <b>Cancelar</b>
            </button>
            <hr/>
          </div>
          <div class="col-lg-6" id="divMensajes" style="height: 100% !important;">
            <b>Mensajes Predeterminados</b>
            <p></p>
            <input style="display: none" id="txtIdPedido">
            <?php
            $query = "SELECT * FROM mensaje m;";
            $result = $db->executeQuery($query);
            $array = array();
            while ($row = $db->fecth_array($result)) {
              echo '<button class="usqay-btn-exx btn btn-lg" onclick="addMessagePedido(\'' . utf8_encode($row['descripcion']) . '\')">' . utf8_encode($row['descripcion']) . '</button>';
            }
            ?>
            <p></p>
          </div>
          
      </div>
      <div class="panel row" id="panelCancelCuenta" style="display:none;margin:0px !important;padding: 15px !important;">
          <h4><b>Pago con efectivo</b></h4>
          <hr/>
            <div class="row" >
            <div class="col-lg-6"><span class="labelTotal letraPago">Pago:</span></div>
            <div class="col-lg-6"><input type="text" class="form-control numerico " value="0" id="inputPago" onchange="" placeholder="00.00"></div>
            </div>
            <p></p>
            <div class="row labelTotal">
            <div class="col-lg-6"><span class="labelTotal letraPago">Total:</span></div>
            <div class="col-lg-6"><label class="reset labelTotal" id="lblTotalCancel">00.00</label></div>
            </div>
            <p></p>
            <div class="row letraPago">
            <div class="col-lg-6"><span class="labelTotal letraPago">Vuelto:</span></div>
            <div class="col-lg-6"><label class="reset labelTotal"id="lblVuelto">00.00</label></div>
            </div>
            <p></p>
            <div style="text-align: center !important;">
            <a onclick="SumaBilletesMonto(200, 'inputPago')">  <img width="130" height="80" src="Public/images/200soles_1995.jpg"></a>
            <a onclick="SumaBilletesMonto(100, 'inputPago')"><img width="130" height="80" src="Public/images/100_soles.jpg"></a>
            <a onclick="SumaBilletesMonto(50, 'inputPago')"> <img width="130" height="80" src="Public/images/50_soles.jpg"></a>
            <a onclick="SumaBilletesMonto(20, 'inputPago')"> <img width="130" height="80" src="Public/images/20_nuevos_soles.jpg"></a>
            <a onclick="SumaBilletesMonto(10, 'inputPago')"> <img width="130" height="80" src="Public/images/10_nuevos_soles.jpg"></a>
            </div>
            <p></p>
            <div class="row" style="height: 10px !important;"></div>
            <div id="dlg-buttonsCancelarCuenta">
                <center>
                <button onclick="regresaCancelaPedido();" class="btn btn-lg">Regresar</button>
                <button onclick="CancelaPedido();" class="btn btn-danger btn-lg"><b>Presione aqui para pagar</b></button>
                </center>
            </div>
      </div>
      <div class="panel row" id="panelCancelCuentaCTarjeta" style="display:none;margin:0px !important;padding: 15px !important;">
        <h4><b>Pago con tarjeta</b></h4>
        <hr/>
        <form id="frmPagoTarjeta">
        <table  class="table table-bordered labelTotal letraPago"  border="0">
          <tr>
            <td>Total:</td>
            <td><label class="control-label" id="lblTotalCancelCuenta">00.00</label></td>
          </tr>
          <tr>
            <td>Tarjeta:</td>
            <td>
              <select type="text" class="form-control" id="cmbTipoTarjeta">
                <option>VISA</option>
                <option>MASTERCARD</option>
              </select>
            </td>
          </tr>
          <tr>
            <td><input id="chkpagoEfectivoTarjeta" onclick="checkedTarjeta()" type="checkbox"> Monto de Tarjeta:</td>
            <td><input id="txtMontoCTarjeta" onkeyup="restarNumeros()" class="form-control numerico" disabled="true"></td>
          </tr>
          <tr>
            <td>Monto en efectivo:</td>
            <td><label class="col-sm-2 control-label reset" id="lblMontoEfectivo">00.00</label></td>
          </tr>
        </table>
        </form>
        <p></p>
        <div class="row" style="height: 10px !important;"></div>
        <div>
        <center>
            <button class="btn btn-default btn-lg" onclick="regresaCancelaPedidoConTarjeta();">Cancelar</button>
            <button class="btn btn-danger btn-lg" onclick="CancelaPedidoConTarjeta();">Pagar</button>
        </center>
        </div>
      </div>
      <div class="panel row" id="panelPagoCredito" style="display:none;margin:0px !important;padding: 15px !important;">
          <h4><b>Pago al crédito</b></h4>
          <hr/>
          <form class="form-horizontal">
          <div class="form-group">
            <label for="tipo_cliente" class="col-sm-2 control-label">Tipo de cliente</label>
            <div class="col-sm-8">
              <select class="form-control" id="cmbTipoClienteCredito" name="tipo_cliente" onchange="onSelectTipoCliente($('#cmbTipoClienteCredito').val())">
                <option value="2">Juridico</option>
                <option value="1">Natural</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label id="lbldocredito" for="ruc" class="col-sm-2 control-label">Documento</label>
            <div class="col-sm-10">
              <input id='txtDocumentoPC' name="ruc" class="form-control" placeholder="Ingrese Su documento- Presione 'Enter para iniciar la busqueda'">
            </div>
          </div>
          <div class="form-group">
            <label id="lblrcredito" for="razonSocial" class="col-sm-2 control-label">Razon Social</label>
            <div class="col-sm-10">
              <input class="form-control"  name="razonSocial" id="txtValor1PC" placeholder="">
            </div>
          </div>
          <div class="form-group">
            <label id="lbldcredito" for="direccion" class="col-sm-2 control-label">Direccion</label>
            <div class="col-sm-10">
              <input class="form-control" name="direccion" id="txtValor2PC" placeholder="">
            </div>
          </div>
        </form>
          <p></p>
        <div class="row" style="height: 10px !important;"></div>
        <div>
        <center>
            <button class="btn btn-default btn-lg" onclick="regresacancelaPedidoCredito();">Regresar</button>
            <button class="btn btn-danger btn-lg" onclick="cancelaPedidoCredito();">Aceptar</button>
        </center>
      </div>
      </div>
      <div class="panel row" id="panelPagoConsumo" style="display:none;margin:0px !important;padding: 15px !important;">
          <h4><b>Cuenta por consumo</b></h4>
          <hr/>
          <form class="form-horizontal">
            <div class="form-group">
              <label for="tipo_cliente" class="col-sm-2 control-label">Tipo de cliente</label>
              <div class="col-sm-8">
                <select  class="form-control" id="cmbTipoClienteCuenta" name="tipo_cliente" onchange="onSelectTipoClienteCuenta($('#cmbTipoClienteCuenta').val())">
                  <option value="2">
                    Juridico
                  </option>
                  <option value="1">
                    Natural
                  </option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label id="lbldocuenta" for="ruc" class="col-sm-2 control-label">Documento</label>
              <div class="col-sm-10">
                <input id='txtDocumentoPCuenta' name="ruc" class="form-control" placeholder="Ingrese Su documento- Presione 'Enter para iniciar la busqueda'">
              </div>
            </div>
            <div class="form-group">
              <label id="lblrcuenta" for="razonSocial" class="col-sm-2 control-label">Razon Social</label>
              <div class="col-sm-10">
                <input class="form-control"  name="razonSocial" id="txtValor1PCuenta" placeholder="">
              </div>
            </div>
            <div class="form-group">
              <label id="lbldcuenta" for="direccion" class="col-sm-2 control-label">Direccion</label>
              <div class="col-sm-10">
                <input class="form-control" name="direccion" id="txtValor2PCuenta" placeholder="">
              </div>
            </div>
          </form>
          <p></p>
        <div class="row" style="height: 10px !important;"></div>
        <div>
        <center>
            <button class="btn btn-default btn-lg" onclick="regresacancelaPedidoCACuenta();">Regresar</button>
            <button class="btn btn-danger btn-lg" onclick="cancelaPedidoCACuenta();">Aceptar</button>
        </center>
      </div>
  </div>
    <div class="panel row" id="panelCambioMesa" style="display:none;margin:0px !important;padding: 15px !important;">
        <h4><b>Cambio de mesa</b></h4>
        <hr/>
        <div id="contenidoCambioMesa" style="width: 100%; height: 100%;">

        </div>
        <div class="row" style="height: 10px !important;"></div>
        <div>
          <center>
              <button class="btn btn-default btn-lg" onclick="regresaCambioMesa();">Regresar</button>
          </center>
        </div>
    </div>
      <div class="panel row" id="panelBoleta" style="display:none;margin:0px !important;padding: 15px !important;">
          <h4><b>Boleta</b></h4>
          <hr/>
          <form id="frmPagoBoleta">
          <table  class="table table-bordered labelTotal" border="0">
            <tr>
              <td>Total</td>
              <td><label  class="control-label reset"  id="lblTotalCancelCuentafrmPagoBoleta">00.00</label></td>
            </tr>
            <tr>
              <td>Tipo de pago</td>
              <td>
                <input type="radio" name="tipoPago1" value="1" checked="true" onclick="checkEfectivoTarjeta('tipoPago1', 'divTarjetasBoleta')">Efectivo<br/>
                <input type="radio"  name="tipoPago1" value="2" onclick="checkEfectivoTarjeta('tipoPago1', 'divTarjetasBoleta')">Con Tarjeta
              </td>
            </tr>
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
              <td><input class="form-control reset"name="apellidos" id="txtApellidos" placeholder="Ingrese el apellido del cliente"></td>
            </tr>
            <tr>
              <td>Monto en efectivo</td>
              <td><input id="txtMontoEfectivo" name="total_efectivo" class="form-control numerico"></td>
            </tr>
            <tr>
              <td>Vuelto</td>
              <td><input class="form-control numerico" id="txtvueltoBoleta"></td>
            </tr>
            <tr class="divTarjetasBoleta" hidden="true">
              <td>Tarjeta</td>
              <td>
                <select name="nombreTarjeta"  type="text" class="form-control" id="cmbTipoTarjeta">
                  <option>VISA</option>
                  <option>MASTERCARD</option>
                </select>
              </td>
            </tr>
            <tr class="divTarjetasBoleta" hidden="true">
              <td>
                <input id="chkpagoEfectivoTarjetaBoleta" onclick="checkedTarjeta2('chkpagoEfectivoTarjetaBoleta', 'txtMontoCTarjetaBoleta')" type="checkbox"> Monto de Tarjeta:
              </td>
              <td>
                <input id="txtMontoCTarjetaBoleta" onkeyup="restarNumeros2('lblTotalCancelCuentafrmPagoBoleta', 'txtMontoEfectivo', 'txtMontoCTarjetaBoleta')" class="form-control numerico" disabled="true">
              </td>
            </tr>
            <tr style="display: none;">
              <td>Bolelta impresa:</td>
              <td><select id="cmbConsumo" name="consumo" class="form-control">
                <option value="1">DETALLADO</option>
                <option value="2">X CONSUMO</option>
              </select></td>
            </tr>
          </table>
        </form>
        <div class="alert alert-danger alert-dismissable" style="display:none;" id="merror_boleta">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          Error...
        </div>
        <div class="alert alert-success alert-dismissable" style="display:none;" id="msuccessfrmPagoBoleta">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          Se esta Generando la impresión, espere por favor ...
        </div>
          <p></p>
        <div class="row" style="height: 10px !important;"></div>
        <div>
        <center>
            <button class="btn btn-default btn-lg" onclick="regresaCancelaPedidoComprobante();">Regresar</button>
            <button class="btn btn-danger btn-lg" onclick="CancelaPedidoComprobante('frmPagoBoleta', '1', 'tipoPago1');">Pagar</button>
        </center>
        </div>
     </div>
      <div class="panel row" id="panelFactura" style="display:none;margin:0px !important;padding: 15px !important;">
        <h4><b>Factura</b></h4>
        <hr/>
        <form id="frmPagoFactura">
          <table class="table table-bordered labelTotal">
            <tr>
              <td>Total</td>
              <td><label class="control-label reset" id="lblTotalCancelCuentafrmPagoFactura">00.00</label></td>
            </tr>
            <tr>
              <td>Tipo de pago</td>
              <td>
                <input type="radio" name="tipoPago" value="1" checked="true" onclick="checkEfectivoTarjeta('tipoPago', 'divTarjetasFactura')">Efectivo
                <br/>
                <input type="radio" name="tipoPago" value="2" onclick="checkEfectivoTarjeta('tipoPago', 'divTarjetasFactura')">Con Tarjeta
              </td>
            </tr>
            <tr>
              <td>Cliente</td>
              <td>
                <input id='txtRucFactura' maxlength="11" name="ruc" class="form-control numerico" placeholder="Ingrese Su Ruc- Presione 'Enter para iniciar la busqueda'">
              </td>
            </tr>
            <tr>
              <td></td>
              <td><input class="form-control" name="razonSocial" id="txtRazonSocialFactura" placeholder="Ingrese la Razon Social"></td>
            </tr>
            <tr>
              <td></td>
              <td><input class="form-control"name="direccion" id="txtDireccionFactura" placeholder="Ingrese la Dirección"></td>
            </tr>
            <tr>
              <td>Monto en efectivo</td>
              <td><input id="txtMontoEfectivoFactura" name="total_efectivo" class="form-control"></td>
            </tr>
            <tr>
              <td>Vuelto</td>
              <td><input class="form-control"  id="txtvueltoFactura"></td>
            </tr>
            <tr class="divTarjetasFactura" hidden="true">
              <td>Tarjeta</td>
              <td>
                <select name="nombreTarjeta" type="text" class="form-control" id="cmbTipoTarjeta" >
                  <option>VISA</option>
                  <option>MASTERCARD</option>
                </select>
              </td>
            </tr>
            <tr class="divTarjetasFactura" hidden="true">
              <td>
                <input id="chkpagoEfectivoTarjetaFactura" onclick="checkedTarjeta2('chkpagoEfectivoTarjetaFactura', 'txtMontoCTarjetaFactura')" type="checkbox"> Monto de Tarjeta:
              </td>
              <td>
                <input id="txtMontoCTarjetaFactura" name="total_tarjeta" onkeyup="restarNumeros2('lblTotalCancelCuentafrmPagoFactura', 'txtMontoEfectivoFactura', 'txtMontoCTarjetaFactura')" class="form-control" disabled="true">
              </td>
            </tr>
            <tr style="display: none;">
              <td>Factura impresa:</td>
              <td><select id="cmbConsumoFactura" name="consumo" class="form-control">
                <option value="1">DETALLADO</option>
                <option value="2">X CONSUMO</option>
              </select></td>
            </tr>
          </table>
        </form>
        <div class="alert alert-danger alert-dismissable" style="display:none;" id="merror_factura">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          Error...
        </div>
        <div class="alert alert-success alert-dismissable" style="display:none;" id="msuccessfrmPagoFactura">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          Se esta Generando la impresión, espere por favor ...
        </div>
        <p></p>
        <div class="row" style="height: 10px !important;"></div>
        <div>
        <center>
            <button class="btn btn-default btn-lg" onclick="regresaCancelaPedidoComprobanteF();">Regresar</button>
            <button class="btn btn-danger btn-lg" onclick="CancelaPedidoComprobante('frmPagoFactura', '2', 'tipoPago');">Pagar</button>
        </center>
        </div>
     </div>
     <!-- Gino Lluen - 2018 -->
    <div class="panel row" id="panelDatosCliente" style="display:none;margin:0px !important;padding: 15px !important;">
        <h4><b>Datos del Cliente</b></h4>
        <hr/>
            <table style="width: 100%;">
                <tr id="rowTelefono">
                    <td>Teléfono</td>
                    <td><input name="telefonoCliente" class="form-control reset" id="txtPhoneCliente" placeholder="Ingrese el teléfono - Presione Enter para iniciar la búsqueda"></td>
                </tr>
                <tr id="rowCliente">
                    <td>Cliente</td>
                    <td><input name="nombreCliente" class="form-control reset" id="txtNameCliente" placeholder="Ingrese el nombre del cliente"></td>
                </tr>
                <tr id="rowDocumento">
                    <td>DNI/RUC</td>
                    <td><input name="documentoCliente" class="form-control reset" id="txtDocCliente" placeholder="Ingrese el ID del cliente"></td>
                </tr>
                <tr id="rowDireccion">
                    <td>Dirección</td>
                    <td><input name="direccionCliente" class="form-control reset" id="txtDireccionCliente" placeholder="Ingrese la direccion del cliente"></td>
                </tr>
            </table>
            <p></p>
            <div class="row" style="height: 10px !important;"></div>
            <div>
            <center>
                <button class="btn btn-default btn-lg" onclick="regresaeditaClienteP();">Regresar</button>
                <button class="btn btn-primary btn-lg" onclick="guardaCliente()">Actualizar</button>
            </center>
            </div>
    </div>

</div>
</div>
<input id="TxtPkMesa" style="display: none">
<style>
.buttonsMessges {
  width: 170px;
  height: 50px;
  font-weight: bold;
  font-size: 18px;
  margin-bottom: 3px;
  margin-right: 2px;
}
blink {
  animation-duration: 1s;
  animation-name: blink;
  animation-iteration-count: infinite;
  animation-timing-function: steps(2, start);
}
@keyframes blink {
  80% {
    visibility: hidden;
  }
}
</style>

<script>
var enviando = 0;
var contador = 0;
$('#inputPago').keypress(function(event) {
  var keycode = (event.keyCode ? event.keyCode : event.which);
  if (keycode == '13') {
    contador++;
    switch (contador) {
      case 1:
      calculaVuelto();
      break;
      case 2:
      break;
      case 3:
      CancelaPedido();
      contador = 0;
      break;
    }
  }
});


$('#txtMontoEfectivo').keypress(function(event) {
  var keycode = (event.keyCode ? event.keyCode : event.which);
  if (keycode == '13') {
      calculaVuelto();
  }
});</script>
<script>
$('#txtMontoEfectivoFactura').keypress(function(event) {
  var keycode = (event.keyCode ? event.keyCode : event.which);
  if (keycode == '13') {
      calculaVuelto();
  }
});</script>
<script>

$('#txtDocumentoPCuenta').keypress(function(event) {
  var keycode = (event.keyCode ? event.keyCode : event.which);
  if (keycode == '13') {
    if ($('#cmbTipoClienteCuenta').val() === "2") {
      searchCustomerRuc($('#txtDocumentoPCuenta').val(), "txtValor1PCuenta", "txtValor2PCuenta");
    }
    else
    searchCustomerDNI($('#txtDocumentoPCuenta').val(), "txtValor1PCuenta", "txtValor2PCuenta");
  }
});
$('#txtDocumentoPC').keypress(function(event) {
  var keycode = (event.keyCode ? event.keyCode : event.which);
  if (keycode == '13') {
    if ($('#cmbTipoClienteCredito').val() === "2") {
      searchCustomerRuc($('#txtDocumentoPC').val(), "txtValor1PC", "txtValor2PC");
    }
    else
    searchCustomerDNI($('#txtDocumentoPC').val(), "txtValor1PC", "txtValor2PC");
  }
});
function onSelectTipoCliente($value) {
  if ($value === "1") {
    $('#lbldocredito').html("DNI");
    $('#lblrcredito').html("Cliente");
    $('#lbldcredito').html("Direccion");
  }
  else {
    $('#lbldocredito').html("Documento");
    $('#lblrcredito').html("Razon Social");
    $('#lbldcredito').html("Direccion");
  }
}
function onSelectTipoClienteCuenta($value) {
  if ($value === "1") {
    $('#lbldocuenta').html("DNI");
    $('#lblrcuenta').html("Nombres");
    $('#lbldcuenta').html("Apellidos");
  }
  else {
    $('#lbldocuenta').html("Documento");
    $('#lblrcuenta').html("Razon Social");
    $('#lbldcuenta').html("Direccion");
  }
}
$(function() {
    $.ajaxSetup({ cache: false });
});
$(".numerico").numeric({negative: false});
$('#txtMontoEfectivoFactura').keypress(function(event) {
  var keycode = (event.keyCode ? event.keyCode : event.which);
  if (keycode == '13') {
    calculaVuelto2('txtMontoEfectivoFactura', 'lblTotal', 'txtvueltoFactura');
  }
});
$('#txtMontoEfectivo').keypress(function(event) {
  var keycode = (event.keyCode ? event.keyCode : event.which);
  if (keycode == '13') {
    calculaVuelto2('txtMontoEfectivo', 'lblTotal', 'txtvueltoBoleta');
  }
});

/**
* Cargar un pedido
* */

function loadPedido($mesa) {
  var param = {'mesa': $mesa};
  $.ajax({
    url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ListPedidos",
    type: 'GET',
    data: param,
    beforeSend: function(xhr) {
      xhr.overrideMimeType("text/plain; charset=x-user-defined");

    },
    dataType: 'json',
    success: function(data) {
      var pkComprobante;
      for (var i = 0; i < data.length; i++) {
        $("#lblnMesa").html(data[i].nmesa);
        $("#txtnPesonas").val(data[i].npersonas);
        $("#TxtPkMesa").val(data[i].pkMesa);
        $("#txtCombrobante").val(data[i].nComprobante);
        pkComprobante = data[i].nComprobante;
        $("#txtDescuento").val(data[i].descuento);
        $("#documentoCliente").val(data[i].documento);
        $("#salon").val(data[i].pkSalon);
      }
      loadDetalles();
      CargaCliente(pkComprobante);
    }

  });
}

function _anulaPedido($nameGrid) {
  var row = $('#' + $nameGrid).datagrid('getSelected');
  if (row) {
    var param = {'array': JSON.stringify($('#tblcomprobante').datagrid('getSelections')), 'terminal': $('#terminal').val()};
    $.ajax({
    url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=DeletePedido",
        type: 'POST',
        data: param,
        success: function() {
            loadDetalles();
        }
    });
  }
  else {
    $.messager.alert('Error', '¡Debe Seleecionar un Item del Listado de Pedidos!', 'error');
  }
}

function _saveMensaje() {
  $.ajax({
    url: "<?php echo Class_config::get('urlApp') ?>/?controller=Mensaje&&action=Save",
    type: 'POST',
    data: param,
    cache: true,
    dataType: 'json',
    success: function(data) {
      console.log(data);
    }
  });
}

function openImprimirCuenta() {
    var param = {'pkPedido': $("#txtCombrobante").val(), 'terminal': $('#terminal').val(), 'tipo':'CUENTA', 'aux': '<?php echo UserLogin::get_id();?>'};
    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ImprimeCuenta",
            type: 'POST',
            data: param,
            success: function() {
                ///Nada
            }
    });
}

function checkEfectivoTarjeta($id, $idDiv) {
var value2 = $('input:radio[name=' + $id + ']:checked').val();
if (value2 === "1") {
    $('.' + $idDiv).hide();
    if($id == "tipoPago1"){
    $("#txtMontoCTarjetaBoleta").val("");
    $("#txtMontoEfectivo").prop("readonly", false);
    }else{
    $("#txtMontoCTarjetaFactura").val("");
    $("#txtMontoEfectivoFactura").prop("readonly", false);
    }
  }
  else {
    $('.' + $idDiv).show();
    if($id == "tipoPago1"){
    $("#txtvueltoBoleta").val("");
    $("#txtMontoEfectivo").val("");
    $("#txtMontoEfectivo").prop("readonly", true);
    }else{
    $("#txtvueltoFactura").val();
    $("#txtMontoEfectivoFactura").val("");    
    $("#txtMontoEfectivoFactura").prop("readonly", true);
    }
  }
}

$('#txtRucFactura').keypress(function(event) {
  var keycode = (event.keyCode ? event.keyCode : event.which);
  if (keycode == '13') {
    searchCustomerRuc($('#txtRucFactura').val(), "txtRazonSocialFactura", "txtDireccionFactura");
  }
});

$('#txtDniCliente').keypress(function(event) {
  var keycode = (event.keyCode ? event.keyCode : event.which);
  if (keycode == '13') {
    searchCustomerDNI($('#txtDniCliente').val(), "txtNombres", "txtApellidos");
  }
});

function searchCustomerRuc($document, $idRazon, $idDireccion) {
  $("#" + $idRazon).val("");
  $("#" + $idDireccion).val("");
  var param = {'document': $document
};

$.ajax({
  url: "<?php echo Class_config::get('urlApp') ?>/?controller=Cliente&&action=ClienteRuc",
  type: 'POST',
  data: param,
  cache: true,
  dataType: 'json',
  success: function(data) {
      $.each( data, function( key, value ) {
        $("#" + $idRazon).val(value.companyName);
        $("#" + $idDireccion).val(value.address);
        return false;
      });
  }
});
}
;

function searchCustomerDNI($document, $nombres, $apellidos) {
  $("#" + $nombres).val("");
  $("#" + $apellidos).val("");
  var param = {'document': $document};
$.ajax({
  url: "<?php echo Class_config::get('urlApp') ?>/?controller=Cliente&&action=ClienteDni",
  type: 'POST',
  data: param,
  cache: true,
  dataType: 'json',
  success: function(data) {
    $.each( data, function( key, value ) {
        $("#" + $nombres).val(value.nombres);
        $("#" + $apellidos).val(value.apellidos);
        return false;
    });
  }
});
};

function regresaCancelaPedidoComprobante() {
    topFunction();
    $("#panelOperativo").show(0);
    $("#panelBoleta").hide(0); 
}

function regresaCancelaPedidoComprobanteF() {
    topFunction();
    $("#panelOperativo").show(0);
    $("#panelFactura").hide(0); 
}

function CancelaPedidoComprobante($form, tipoComprobant, $tipoPag) {
     //Primero verificamos para no cagarla
    //PARA FACTURA
    var ruc_f = $("#txtRucFactura").val();
    var razon_f = $("#txtRazonSocialFactura").val();
    var direccion_f = $("#txtDireccionFactura").val();
    //PARA BOLETA
    var dni_b = $("#txtDniCliente").val();
    var nombres_b = $("#txtNombres").val();
    var apellidos_b = $("#txtApellidos").val();
    var monto_b = $("#lblTotalCancelCuentafrmPagoBoleta").html();
    //Variable centinella
    var pasa = 0;
    var mensaje = "";
    //Verificamos
    if(parseInt(tipoComprobant) === 1){
        if(parseFloat(monto_b) > 700){
            if(dni_b !== "" && nombres_b !== "" && apellidos_b !== ""){
                if(dni_b.length === 8){
                    pasa = 1;
                }else{
                    mensaje = "El DNI debe ser de 8 digitos";
                }
            }else{
                mensaje = "Para montos mayores a 700 soles la boleta debe llevar datos";
            }
        }else{
            pasa = 1;
        }
    }
    
    if(parseInt(tipoComprobant) === 2){
        if(ruc_f !== "" && razon_f !== "" && direccion_f !== ""){
            if(ruc_f.length === 11){
                pasa = 1;
            }else{
                mensaje = "El RUC debe ser de 11 digitos";
            }
        }else{
            mensaje = "Es obligatorio poner los datos del cliente en una factura";
        }
    }
    
    if(pasa === 1){
        //Primero enviamos pedidos pendientes
        var tipo_comprobante = "";
        if(enviando === 0){
            enviando = 1;
            var totalf = $('#lblTotal').html();
            totalf = totalf.split(" ");
            totalf = totalf[1];
            $('#modal_envio_anim').modal('show');
            var param = {'array': JSON.stringify(array_table_envio()), 'terminal': $('#terminal').val()};
            $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=EnviaPedido",
                type: 'POST',
                data: param,
                success: function() {
                    //Ahora generamos la boleta o factura
                    $('body,html').animate({scrollTop: 0}, 800);
                    $('#msuccess' + $form).show('fast').delay(4000).hide('fast');

                    var $id = $tipoPag;

                    var TipoPago = $('input:radio[name=' + $id + ']:checked').val();
                    var totalTarjeta = "";

                    if (tipoComprobant === "1") {
                      totalTarjeta = $("#txtMontoCTarjetaBoleta").val();
                      tipo_comprobante = "BOLETA";
                    }
                    else if (tipoComprobant === "2") {
                      totalTarjeta = $('#txtMontoCTarjetaFactura').val();
                      tipo_comprobante = "FACTURA";
                    }

                    if (totalTarjeta === "") {
                      totalTarjeta = totalf;
                    }

                    var params = "&dni=" + $("#txtDniCliente").val() + "&nombre=" + $("#txtNombres").val() + "&mesa=" + $("#TxtPkMesa").val() + "&apelli=" + $("#txtApellidos").val();
                    params = params + "&ruc" + $("#txtRucFactura").val() + "&razonSocial=" + $("#txtRazonSocialFactura").val() + "&direccion=" + $("#txtDireccionFactura").val();
                    $.ajax({
                     url: "<?php echo Class_config::get('urlApp') ?>/?controller=Comprobante&&action=CancelarPedidoComprobante&descuento=" + $('#txtDescuento').val()+ "&total_venta=" + totalf + "&pkMesa=" + $("#TxtPkMesa").val() + "&pkComprobante=" + $('#txtCombrobante').val() + "&total_efectivo=" + $("#txtMontoEfectivo").val() + "&ttarjeta=" + totalTarjeta + "&tipoPago=" + TipoPago + "&tipo_comprobante=" + tipoComprobant + params,
                      type: 'POST',
                      data: $('#' + $form).serialize(),
                      dataType: 'html',
                      success: function(data) {
                        var params1 = {comprobante: data, array: JSON.stringify($('#tblcomprobante').datagrid('getRows'))};
                          $.ajax({
                            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Comprobante&&action=AddDetalle",
                            type: 'POST',
                            data: params1,
                            success: function(data_c) {
                                var json = JSON.parse(data_c);
                                if(json.exito == 1 || json.exito == 2){
                                    if(json.exito == 2){
                                        alert(json.mensaje);
                                    }
                                    //Finalmente ponemos en cola la impresion
                                    var param2 = {'pkPedido': data, 'terminal': $('#terminal').val(), 'tipo': tipo_comprobante, 'aux': '<?php echo UserLogin::get_id();?>,1'};
                                    $.ajax({
                                        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ImprimeCuenta",
                                            type: 'POST',
                                            data: param2,
                                            success: function() {
                                               window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome";
                                            }
                                    });
                                }else{
                                    alert(json.mensaje);
                                    enviando = 0;
                                    $('#modal_envio_anim').modal('hide');
                                }
                            }
                          });
                    }
                    }); 
                }
            });
        }
    }else{
        alert("Error: "+mensaje);
    }
  }

  $.extend($.fn.datagrid.methods, {
    editCell: function(jq, param) {
      return jq.each(function() {
        var opts = $(this).datagrid('options');
        var fields = $(this).datagrid('getColumnFields', true).concat($(this).datagrid('getColumnFields'));
        for (var i = 0; i < fields.length; i++) {
          var col = $(this).datagrid('getColumnOption', fields[i]);
          col.editor1 = col.editor;
          if (fields[i] != param.field) {
            col.editor = null;
          }
        }
        $(this).datagrid('beginEdit', param.index);
        for (var i = 0; i < fields.length; i++) {
          var col = $(this).datagrid('getColumnOption', fields[i]);
          col.editor = col.editor1;
        }
      });
    }
  });
  
  var editIndex = undefined;
  function endEditing() {
    if (editIndex == undefined) {
      return true
    }
    if ($('#tblcomprobante').datagrid('validateRow', editIndex)) {
      $('#tblcomprobante').datagrid('endEdit', editIndex);
      editIndex = undefined;
      return true;
    } else {

      return false;
    }
  }
  
  function onClickCell(index, field) {

    if (endEditing() && field === "pedido") {

      $('#tblcomprobante').datagrid('selectRow', index)
      .datagrid('editCell', {index: index, field: field});
      editIndex = index;
    }
  }
  
  function saveChanges(index, data, changes) {
    var param = {'pkPedido': data.pkPedido,
    cantidad: data.cantidad, precio: data.precio, pedido: data.pedido, estado: data.estado};
    $.ajax({
      url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=UpdatePedido",
      type: 'POST',
      data: param,
      success: function(data2) {
        loadDetalles();
      }
    });
  }
  
  //Con esta funcion de mierda se envian pedidos
  //Esta mal implementada
  function confirImpresion($tipo) {
    if(enviando === 0){
        enviando = 1;
        $('#modal_envio_anim').modal('show');
        var param = {'array': JSON.stringify(te_la_envio_toda()), 'terminal': $('#terminal').val()};
        $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=EnviaPedido",
            type: 'POST',
            data: param,
            cache: false,
            success: function() {
                if ("<?php echo UserLogin::get_pkTypeUsernames(); ?>" !== '4') {
                    enviando = 0;
                    $('#modal_envio_anim').modal('hide');
                    loadDetalles();
                }
                else
                {
                    window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome";
                }
            }
        });
    }
  }
  
  function cellStylerPedido(value, row, index) {
    if (value === "Solicitado") {
      return 'background-color:red;color:white;';
    }
  }
  $('#tblcomprobante').datagrid({singleSelect: false});

  function visualizaTipoUsuario() {

    var tipe = "<?php echo UserLogin::get_pkTypeUsernames(); ?>";
    switch (tipe) {
      case "1":
      case "2":
      $(".descuento").show();
      $(".mozo").hide();
      break;
      case "4":
      case "10":
      $(".mozo").show();
      $(".descuento").hide();
      if ($('#salon').val() === "43" || $('#salon').val() === "44") {
        $('#tblcomprobante').datagrid('hideColumn', 'tipoPedido');
      }
      else {
        $('#tblcomprobante').datagrid('showColumn', 'tipoPedido');
      }
      break;
      case "8":
      case "9":
      $(".caja").hide();
      $(".mozo").hide();
      $(".caja2").hide();
      break;
      default:
      $(".descuento").hide();
      $(".mozo").hide();
      break;
    }

  }
  function CambiarMesa($mesaActual) {
    popupwindow("<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ShowMesas&mesaAnterior=<?php echo $_GET['pkMesa'] ?>&pkPedido=" + $('#txtCombrobante').val() + "&array=" + JSON.stringify($('#tblcomprobante').datagrid('getSelections')), "Cambiando de mesa", 1300, 900);
  }

  function popupwindow(url, title, w, h) {
    var left = (screen.width / 2) - (w / 2);
    var top = (screen.height / 2) - (h / 2);
    return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + w + ', height=' + h + ', top=' +
           top + ', left=' + left);
  }

  function mesaOpen($pkMozo, $pkMesa, $estado) {
    window.location.href = '<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ChangeMesa&mesaAnterior=<?php echo $_GET['pkMesa'] ?>&pkPedido=' + $('#txtCombrobante').val() + '&mesaActual=' + $pkMesa + '&array=' + JSON.stringify($('#tblcomprobante').datagrid('getSelections')) + '&estado=' + $estado;
  }
  
  function validaMozoApertura($pkMozo, $mesa, $estado) {
    if ('<?php echo UserLogin::get_pkTypeUsernames() ?>' === "4") {
      if ($pkMozo === "<?php echo UserLogin::get_idTrabajador() ?>") {
        window.location.href = '<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ChangeMesa&mesaAnterior=<?php echo $_GET['pkMesa'] ?>&mesaActual=' + $mesa + '&pkPedido=' + $('#txtCombrobante').val() + '&array=' + JSON.stringify($('#tblcomprobante').datagrid('getSelections')) + '&estado=' + $estado;
      }
      else {
        alert("Usted no puede ingresar a esta mesa");
      }
    }
    else {
      window.location.href = '<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ChangeMesa&mesaAnterior=<?php echo $_GET['pkMesa'] ?>&mesaActual=' + $mesa + '&pkPedido=' + $('#txtCombrobante').val() + '&array=' + JSON.stringify($('#tblcomprobante').datagrid('getSelections')) + '&estado=' + $estado;
    }
  }
    
  function updatePedidoLLevar() {
    if ($('#salon').val() === "43" || $('#salon').val() === "44") {
      console.log("probando")
    } else {
      $.post("<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&action=UpdateTipoPedido", {array: JSON.stringify(array_table_envio())},
      function(data) {
        loadDetalles();
      });
    }
  }
  
  function array_table_envio() {
    var array = new Array();
    var contador = 0;
    if ($('#tblcomprobante').datagrid('getSelections').length > 0) {
      for (var i = 0; i < $('#tblcomprobante').datagrid('getSelections').length; i++) {
        if ($('#tblcomprobante').datagrid('getSelections')[i].estado === "0") {
          array[contador] = {
            'pkPedido': $('#tblcomprobante').datagrid('getSelections')[0].pkPedido,
            cantidad: $('#tblcomprobante').datagrid('getSelections')[i].cantidad,
            tipo: $('#tblcomprobante').datagrid('getSelections')[i].tipo,
            estado: $('#tblcomprobante').datagrid('getSelections')[i].estado,
            mensaje: $('#tblcomprobante').datagrid('getSelections')[i].mensaje,
            pkProducto: $('#tblcomprobante').datagrid('getSelections')[i].pkProducto
          };
          contador++;
        }
      }
    } else {
      for (var i = 0; i < $('#tblcomprobante').datagrid('getRows').length; i++) {
        if ($('#tblcomprobante').datagrid('getRows')[i].estado === "0") {
          array[contador] = {
            'pkPedido': $('#tblcomprobante').datagrid('getRows')[i].pkPedido,
            cantidad: $('#tblcomprobante').datagrid('getRows')[i].cantidad,
            tipo: $('#tblcomprobante').datagrid('getRows')[i].tipo,
            estado: $('#tblcomprobante').datagrid('getRows')[i].estado,
            mensaje: $('#tblcomprobante').datagrid('getRows')[i].mensaje,
            pkProducto: $('#tblcomprobante').datagrid('getRows')[i].pkProducto,
            Tipo: $('#tblcomprobante').datagrid('getRows')[i].Tipo
          };
          contador++;
        }
      }
    }
    return array;
  }
  
  function te_la_envio_toda() {
    var array = new Array();
    var contador = 0;
    for (var i = 0; i < $('#tblcomprobante').datagrid('getRows').length; i++) {
      if ($('#tblcomprobante').datagrid('getRows')[i].estado === "0") {
        array[contador] = {
          'pkPedido': $('#tblcomprobante').datagrid('getRows')[i].pkPedido,
          cantidad: $('#tblcomprobante').datagrid('getRows')[i].cantidad,
          tipo: $('#tblcomprobante').datagrid('getRows')[i].tipo,
          estado: $('#tblcomprobante').datagrid('getRows')[i].estado,
          mensaje: $('#tblcomprobante').datagrid('getRows')[i].mensaje,
          pkProducto: $('#tblcomprobante').datagrid('getRows')[i].pkProducto,
          Tipo: $('#tblcomprobante').datagrid('getRows')[i].Tipo
        };
        contador++;
      }
    }
    return array;
  }
  
  
    //Adicionales para guardar datos de clientes - 2018
    function CargaCliente(pkComprobante){
      var param = {'pedido': pkComprobante};
      $.ajax({
      url: "<?php echo Class_config::get('urlApp') ?>/?controller=Cliente&&action=getPedido",
      type: 'POST',
      dataType: 'json',
      data: param,
      success: function(data) {
          if(parseInt(data.tipop) == 1){
              $("#lblNombreCliente").html(data.cliente);
              $("#tipoPedidoC").val(data.tipop);
              $("#idClienteC").val(data.id);
              //Guardamos Data
              $("#nombresClienteC").val(data.cliente);
          }else{
              $("#lblNombreCliente").html(data.nombres+" - "+data.direccion+" - "+data.telefono);
              $("#tipoPedidoC").val(data.tipop);
              $("#idClienteC").val(data.id);
              //Guardamos Data
              $("#documentoClienteC").val(data.documento);
              $("#nombresClienteC").val(data.nombres);
              $("#telefonoClienteC").val(data.telefono);
              $("#direccionClienteC").val(data.direccion);       
          }
      }
      });
    }

    function editaClienteP(){
      var tipop = $("#tipoPedidoC").val();
      if(parseInt(tipop) == 1){
          $("#txtNameCliente").val($("#nombresClienteC").val());
          $("#rowNombre").show(0);
          $("#rowTelefono").hide(0);
          $("#rowDocumento").hide(0);
          $("#rowDireccion").hide(0);
      }else{
          $("#txtNameCliente").val($("#nombresClienteC").val());
          $("#txtPhoneCliente").val($("#telefonoClienteC").val());
          $("#txtDocCliente").val($("#documentoClienteC").val());
          $("#txtDireccionCliente").val($("#direccionClienteC").val());
          $("#rowNombre").show(0);
          $("#rowTelefono").show(0);
          $("#rowDocumento").show(0);
          $("#rowDireccion").show(0);
      }
      $("#panelOperativo").hide(0);
      $("#panelDatosCliente").show(0);
    }

    function guardaCliente(){
      var tipop = $("#tipoPedidoC").val();
      if(parseInt(tipop) == 1){       
          $("#nombresClienteC").val($("#txtNameCliente").val());
          $("#lblNombreCliente").html($("#nombresClienteC").val());
      }else{
          $("#nombresClienteC").val($("#txtNameCliente").val());
          $("#telefonoClienteC").val($("#txtPhoneCliente").val());
          $("#documentoClienteC").val($("#txtDocCliente").val());
          $("#direccionClienteC").val($("#txtDireccionCliente").val());
          $("#lblNombreCliente").html($("#nombresClienteC").val()+" - "+$("#direccionClienteC").val()+" - "+$("#telefonoClienteC").val());     
      }

      var param = {'pedido': $('#txtCombrobante').val(), 'tipop': tipop , 'nombres': $("#txtNameCliente").val(), 'telefono': $("#txtPhoneCliente").val(), 'documento' : $("#txtDocCliente").val(), 'direccion' : $("#txtDireccionCliente").val()}; 

      $.ajax({
      url: "<?php echo Class_config::get('urlApp') ?>/?controller=Cliente&&action=updatePedido",
      type: 'POST',
      dataType: 'json',
      data: param,
      success: function() {
          //Nada wey
      }
      });
      $("#panelDatosCliente").hide(0);
      $("#panelOperativo").show(0);     
    }
    
    function regresaeditaClienteP(){
        $("#panelDatosCliente").hide(0);
        $("#panelOperativo").show(0);  
    }
  </script>
  <script src="Public/jquery-ui-1.10.4.custom/js/jquery.keyboard.js"></script>

</body>
</html>
