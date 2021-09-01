<?php
if (isset($_GET['cliente'])) {
  setcookie("cliente", $_GET['cliente'], time() + 360000, '/');
}
$db = new SuperDataBase();
$objUserSystem = new UserLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
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
  <link rel="icon" href="logo.ico"/>
  <link rel="stylesheet" href="Public/Usqay/css/usqay.css">
  <link rel="stylesheet" type="text/css" href="Public/select2/css/select2.css" rel="stylesheet">
  <link rel="stylesheet" href="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.css">
  <style>   
        button,
        button:focus{
            outline: none !important;
        }
        
      #contenTipoPlato{
        overflow-x: hidden;
        overflow-y: scroll;
        height: 310px;
        text-align: center;
        position: relative;
      }
      #divProductos{
        overflow-x: hidden;
        overflow-y: scroll;
        height: 310px;
        text-align: center;
        position: relative;
      }
      #divMensajes{
        overflow-x: hidden;
        overflow-y: scroll;
        height: 360px;
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
            border-radius: 3% !important;
            margin: 5px;
            width: 170px;
            height: 80px;
            font-weight: bold;
        }
        .usqay-btn-exx:hover{
            color: #ef4d4d !important;
        }

        #tabla_pedidos_wrapper{
          min-height: 280px;
        }

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

        /*Clase Admin*/
        .admin{
          display:none;
        }

        /*Activar solo en mobile*/
        /*.modal-dialog {
          width: 100%;
          height: 100%;
          margin: 0;
          padding: 0;
        }

        .modal-content {
          height: auto;
          min-height: 100%;
          border-radius: 0;
        }
        */

        /*Para ocultar pedidos anulados*/
        .anulado{
          display:none !important;
        }
  </style>
</head>
<body onload="loadPedidoPk('<?php echo $_GET['pkPedido'] ?>')">
  <input type="hidden" id="terminal" value="<?php echo$_COOKIE['t'] ?>"/>
  <div class="navbar navbar-inverse navbar-fixed-top" role="navigation" style="background-color: #00395a !important; border-color: #00395a !important; border-bottom: #ef4d4d solid 5px !important;">
    <div class="container-fluid">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
          <span class="sr-only">Ver Menu</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a style="width:110px;margin-top: -2px;padding:15px 5px;" class="navbar-brand" href="<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome"><img src="<?php echo Class_config::get('urlApp') ?>/Public/images/usqay-large-inverse.svg" style="width:100%;"/></a>
      </div>
      <div class="navbar-collapse collapse">
        <ul class="nav navbar-nav navbar-right btn-xs">
          <li class="active"><a style="background-color: #00395a !important;" href="<?php echo Class_config::get('urlApp') ?>/?controller=User&action=CloseSession"><span class="glyphicon glyphicon-off"></span> Salir</a></li>
        </ul>
      </div><!-- /.nav-collapse -->
    </div><!-- /.container -->
  </div><!-- /.navbar -->

  <!--Inicio Modal-->
<div class='modal fade' id='modal_envio_anim' tabindex='-1' role='dialog' data-keyboard="false" data-backdrop="static">
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

<!-- inicio modal Pago multiple -->
<div id="modal_multiple_pago" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="modal_multiple" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content row">
      <div class="modal-header" style="font-size:24px;font-weight:bold;height:55px;">
        <span class="pull-left" id="titulo_modal_multiple">...</span>
        <span class="pull-right" id="monto_modal_multiple">...</span>
      </div>
      <div class="modal-body">
        <!--Para busqueda en sunat/reniec-->
        <div id="datos_cliente_busqueda" class="row" style="display:none;">
        <div class="col-lg-12" style="font-size: 22px;margin-top: -15px;margin-bottom: 10px;">
          <span class="label label-info">Datos del Cliente</span>
        </div>
      
          <div class='control-group col-lg-12'>
              <center>
                  <p><img src="Public/images/pacman.gif"></p>
                  <p><b>Buscando...</b></p>
              </center>
          </div>
        </div>
        <!--datos cliente-->
        <div id="datos_cliente_pago" class="row">
        <div class="col-lg-12" style="font-size: 22px;margin-top: -15px;margin-bottom: 10px;">
          <span class="label label-info">Datos del Cliente</span>
        </div>
          <div class='control-group col-lg-6'>
              <label>Documento Cliente</label>
              <div class="input-group">
                  <input id="documento_pago" maxlength="11" name="documento_pago" class="form-control" placeholder="Ingrese Su DNI/RUC - Presione 'Enter para iniciar la busqueda'" aria-describedby="boton-sunat">
                  <span id="boton-sunat" class="input-group-addon" onclick="window.open(&quot;http://www.sunat.gob.pe/cl-ti-itmrconsruc/FrameCriterioBusquedaMovil.jsp&quot;,&quot;Consulta Sunat&quot;,&quot;width=600,height=600,top=10,left=20,resizable=no,scrollbars=yes,menubar=no,toolbar=no,status=no,location=no&quot;)"><img src="//localhost/usqay/Public/images/sunat.png" style="height: 20px; width: 20px;"></span>
              </div>
          </div>
          <div class='control-group col-lg-6'>
              <label>Cliente</label>
              <input class="form-control" name="cliente_pago" id="cliente_pago" placeholder="Ingrese su Nombre/Razon Social">
          </div>
          <div class='col-lg-12'>
            <!--para solucionar orden elementos-->
          </div>
          <div class='control-group col-lg-6'>
              <label>Direccion</label>
              <input class="form-control" name="direccion_pago" id="direccion_pago" placeholder="Ingrese su Dirección">
          </div>

          <div class='control-group col-lg-6'>
              <label>Correo Electrónico</label>
              <input id="correo_pago" name="correo_pago" class="form-control" placeholder="Ingrese Su Correo Electronico">
          </div>
          <div class='col-lg-12'>
            <hr/>
          </div>
        </div>
        <!--fin datos cliente-->
        <!--Descuento-->
        <div id="descuento_pago" class="row">
          <div class="col-lg-12" style="font-size: 22px;margin-top: -15px;margin-bottom: 10px;">
            <span class="label label-info">Descuento</span>
          </div>
          <div class='control-group col-lg-6'>
              <label>Predeterminados</label>
              <select class="form-control" id="descuento_prefijado_pago" name="descuento_prefijado_pago" onchange="actualiza_descuento(1)">
              <option value="" selected>Ninguno</option>            
              <?php
              $query_dsc = "SELECT * FROM descuento_prefijado where estado = 1;";
              $result_dsc = $db->executeQuery($query_dsc);
              while ($row = $db->fecth_array($result_dsc)) {
                $simbolo = "S/";
                if(intval($row["tipo"]) === 1){
                  $simbolo = "%";
                }
                echo "<option value='".$row["tipo"]."|".$row["monto"]."|".$row["maximo"]."'>".$row["nombre"]." ".$row["monto"].$simbolo."</option>";
              }
              ?>
              </select> 
          </div>
          <div class='control-group col-lg-6'>
                <label>Porcentaje Descuento</label>
                <div class="input-group">
                <input aria-describedby="basic-addon1" value="0" id="porcentaje_descuento_pago" name="porcentaje_descuento_pago" class="form-control" type="number" min="0" max="99" onchange="actualiza_descuento(2)">
                <span class="input-group-addon" id="basic-addon1">%</span>
                </div>
            </div>
            <div class='control-group col-lg-6'>
                <label>Total Descuento</label>
                <div class="input-group">
                <span class="input-group-addon" id="dsc_mon"></span>
                <input aria-describedby="dsc_mon" value="0" id="monto_descuento_pago" name="monto_descuento_pago" class="form-control" type="number" onchange="actualiza_descuento(3)" min="0">
                </div>
            </div>
          <div class='control-group col-lg-6'>
              <br/>
              <button type='button' class='btn btn-primary pull-right' onclick='aplicar_descuento()'>Aplicar</button>
          </div>
        </div>
        <!--Fin descuento-->
        <!--Medios de pago-->
        <div id="final_pago" class="row">
          <div class='col-lg-12'>
            <hr/>
          </div>
          <div class="col-lg-12" style="font-size: 22px;margin-top: -15px;margin-bottom: 10px;">
            <span class="label label-info">Medios de Pago</span>
          </div>
          <div class='control-group col-lg-3'>
              <label>Medio</label>
              <select class="form-control" id="medio_pago" name="medio_pago" onchange="actualiza_pago(1)">
              <?php
              $query_pag = "SELECT * FROM medio_pago where estado = 1;";
              $result_pag = $db->executeQuery($query_pag);
              while ($row = $db->fecth_array($result_pag)) {
                echo "<option value='".$row["id"]."|".$row["moneda"]."'>".$row["nombre"]."</option>";
              }
              ?>
              </select> 
          </div>
          <div class='control-group col-lg-3'>
              <label>Moneda</label>
              <select class="form-control" id="moneda_pago" name="moneda_pago" onchange="actualiza_pago(2)">
              <?php
              $query_mon = "SELECT * FROM moneda where estado >0;";
              $result_mon = $db->executeQuery($query_mon);
              while ($row = $db->fecth_array($result_mon)) {
                $actual = array_map('utf8_decode', $row);
                echo "<option value='".$actual["id"]."'>".utf8_encode($actual["simbolo"])."</option>";
              }
              ?>
              </select> 
          </div>
          <div class='control-group col-lg-2'>
              <label>Monto</label>
              <input value="0" id="monto_pago" name="monto_pago" class="form-control" type="number" onchange="actualiza_pago(3)" min="0">
          </div>
          <div class='control-group col-lg-2' id="div_operacion">
              <label>Operacion</label>
              <input id="operacion_pago" name="operacion_pago" class="form-control" type="text" onchange="actualiza_pago(4)" disabled>
          </div>
          <div class='control-group col-lg-2'>
              <br/>
              <button type='button' class='btn btn-primary pull-right' onclick='agrega_pago()'>Agregar</button>
          </div>
          <div class='col-lg-12'>
            <hr/>
          </div>
        </div>
        <!--Fin medios de pago-->
        <div id="montos_finales" class="row" style="font-size:24px;font-weight:bold;height:55px;">
          <div class='control-group col-lg-4' style="text-align:center;">
          A Pagar: <span id="monto_por_pagar">...</span>
          </div>
          <div class='control-group col-lg-4' style="text-align:center;">
          Pagado: <span id="pagado">...</span>
          </div>
          <div class='control-group col-lg-4' style="text-align:center;">
          Vuelto: <span id="vuelto">...</span>
          </div>
          <div class='col-lg-12'>
            <hr/>
          </div>
        </div>
        <!--Formas de pago-->
        <div id="formas_pago" class="row">
        <div class='col-lg-12'>
          <table class="display responsive" id="tabla_pagos" style="width:100%;">
          <thead>
            <tr>
			        <th>Origen</th>
              <th>ID_MEDIO</th>
              <th>ID_MONEDA</th>
              <th>ID_TRABAJADOR</th>
              <th>Operacion</th>
              <th>Medio</th>
              <th>Moneda</th>
              <th>Monto</th>
              <th></th>
            </tr>
          </thead>
          <tbody>       
          </tbody>
          </table>
        </div>
        </div>
        <!--Fin formas de pago-->
      </div>
      <div class="modal-footer">
        <div class="form-group pull-left">
            <input type="checkbox" id="check_pago" class="pull-left">
            <label class="pull-left" style="margin-left:10px;">Pagado externamente</label>
            <br/>
            <input type="checkbox" id="check_consumo" class="pull-left">
            <label class="pull-left" style="margin-left:10px;">Por Consumo</label>
        </div>
        <button class="btn btn-default btn-lg" onclick="aborta_pago()">Cancelar</button>
        <button class="btn btn-danger btn-lg" onclick="paga_final()">Pagar</button>
      </div>
    </div>
  </div>
</div>
<!-- fin modal Pago Mutiple -->

<!-- inicio modal Sin Pago -->
<div id="modal_sin_pago" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="titulo_sin_pago">...</h4>
      </div>
      <div class="modal-body">
        <!--Cuando se busca cliente-->
        <form class="form-horizontal" id="busqueda_sin_pago" style="display:none;">
          <center>
              <p><img src="Public/images/pacman.gif"></p>
              <p><b>Buscando...</b></p>
          </center>
        </form>
        <!--Form real-->
        <form class="form-horizontal" id="frm_sin_pago">
          <div class="form-group">
            <label for="tipo_cliente_sin_pago" class="col-sm-2 control-label">Tipo de cliente</label>
            <div class="col-sm-8">
              <select class="form-control" id="tipo_cliente_sin_pago" name="tipo_cliente_sin_pago" onchange="cambia_tipo_cliente_sin_pago()">
                <option value="1">Natural</option>
                <option value="2">Juridico</option>             
              </select>
            </div>
          </div>
          <div class="form-group">
            <label id="lbl_documento_sin_pago" for="documento_sin_pago" class="col-sm-2 control-label">DNI</label>
            <div class="col-sm-10">
              <input id='documento_sin_pago' name="documento_sin_pago" class="form-control" placeholder="Ingrese Su documento - Presione 'Enter para iniciar la busqueda'">
            </div>
          </div>
          <div class="form-group">
            <label id="lbl_cliente_sin_pago" for="cliente_sin_pago" class="col-sm-2 control-label">Cliente</label>
            <div class="col-sm-10">
              <input class="form-control"  name="cliente_sin_pago" id="cliente_sin_pago" placeholder="">
            </div>
          </div>
          <div class="form-group">
            <label id="lbl_direccion_sin_pago" for="direccion_sin_pago" class="col-sm-2 control-label">Direccion</label>
            <div class="col-sm-10">
              <input class="form-control" name="direccion_sin_pago" id="direccion_sin_pago" placeholder="">
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-default btn-lg" onclick="aborta_sin_pago();">Cancelar</button>
        <button class="btn btn-danger btn-lg" onclick="cancela_sin_pago();">Aceptar</button>
      </div>
    </div>
  </div>
</div>
<!-- fin modal Sin Pago -->

<input id="documentoCliente" style="display: none;">
<input id="salon" style="display: none;">

<br><br><br><br>

<div class="panel-ventas row">
  <div class="col-lg-6">
    <input style="display: none" id="txtCombrobante" type="text" readonly="true">
    <div class="panel panel-pedido">
    <div class="panel-heading">
        <span class="glyphicon glyphicon-home" aria-hidden="true"></span> <label id="lblnSalon"></label> - 
        <span class="glyphicon glyphicon-inbox" aria-hidden="true"></span> <label id="lblnMesa"></label> - 
        <span class="glyphicon glyphicon-briefcase" aria-hidden="true"></span> <label id="lblnMoso"></label>
    </div>
    <div class="panel-body">
      <table class="display responsive" id="tabla_pedidos" data-unique-id="id" style="width:100%;">
          <thead>
          <tr>
              <th>Pedido</th>
              <th>Precio</th>
              <th>Cant.</th>
              <th>Importe</th>
              <th>Estado</th>
              <th>Mozo</th>
              <th>PkPlato</th>
              <th>PkDetalle</th>
              <th>Tiempo</th>
          </tr>
          </thead>
          <tbody>
          </tbody>                      
      </table>
    <table class="table" style="font-size: 15px;background: #e8e8e8; color:#73777a; text-align: center !important;">
        <tr>
          <td style="width:30%;"># Items : <label id="lblItems2019">0</label></td>
          <td style="width:30%;"></td>
          <td style="width:30%;">Total S/ <label id="lblTotal2019">0.00</label></td>      
        </tr>
      </table>
    </div>
    </div>
  </div><!-- fin col-lg-6 -->

  <div class="col-lg-6">
      <table class="table">
        <tr>
          <td valign="middle" align="center">
          <button class="botonesIcon btn-pedido btn usqay-btn-ex btn-lg admin"
                    title="Cancela el pedido con ticket" 
                    onclick="modal_pagar_2019(0);">
                <img src="Public/images/iconos2017/efectivo.png" style="margin-bottom: 10px;max-height: 35px !important;"><br/>
                Ticket           
            </button>

            <button class="botonesIcon btn-pedido btn usqay-btn-ex btn-lg admin"
                    onclick="modal_pagar_2019(1)"
                    title="Generar comprobante - Boleta">
                <img src="Public/images/iconos2017/boleta.png" style="margin-bottom: 10px;max-height: 35px !important;"><br/>
                Boleta
            </button>

            <button class="botonesIcon btn-pedido btn usqay-btn-ex btn-lg admin"
                    onclick="modal_pagar_2019(2)">
            <img src="Public/images/iconos2017/factura.png" style="margin-bottom: 10px;max-height: 35px !important;"><br/>
            Factura
            </button>

            <button class="botonesIcon btn-pedido btn usqay-btn-ex btn-lg admin"
                    onclick="sin_pago(2)">
                <img src="Public/images/iconos2017/consumo.png" style="margin-bottom: 10px;max-height: 35px !important;"><br/>
                CONSUMO
            </button>         
                    
            <button class="botonesIcon btn-pedido btn usqay-btn-ex btn-lg " title="Imprimir la cuenta"
                    onclick="openImprimirCuenta()" >
             <img src="Public/images/iconos2017/precuenta.png" style="margin-bottom: 10px;max-height: 35px !important;"><br/>   
             PreCuenta
            </button>
          </td>
        </tr>
      </table>
    </div>
  </div>
  <!--fin dE PEDIDOS-->
</div>
</div>
<input id="TxtPkMesa" style="display: none">
<script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery-1.10.2.js"></script>
<script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery-ui-1.10.4.custom.js"></script>
<script type="text/javascript" src="Public/jquery-easyui/easyui/jquery.easyui.min.js"></script>
<script type="text/javascript"src="Public/jquery-ui-1.10.4.custom/js/jquery.numeric.js"></script>
<script type="text/javascript"src="Public/jquery-ui-1.10.4.custom/js/jquery.maskedinput.js"></script>
<script  type="text/javascript" src="Public/Bootstrap/js/bootstrap.js"></script>
<script  type="text/javascript" src="Public/Bootstrap/js/bootstrap.min.js"></script>
<script  type="text/javascript" src="Public/select2/js/select2.js"></script>
<script src="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.js"></script>
<script>
//Arrays para monedas y tipo de cambio
var monedas = new Array();
var cambios = new Array();
<?php
//Primero obtenemos fecha cierre
$fecha_cierre = null; 
$query_cierre = "Select * from cierrediario where pkCierreDiario = 1";
$result_cierre = $db->executeQuery($query_cierre);
if ($row = $db->fecth_array($result_cierre)) {
	$fecha_cierre = $row["fecha"];
}

//Ahora obtenemos tipos de cambio
$query_cambio = "Select * from tipo_cambio where fecha_cierre = '".$fecha_cierre."'";
$result_cambio = $db->executeQuery($query_cambio);
while($row = $db->fecth_array($result_cambio)){
	echo "cambios[".$row["moneda"]."] = ".$row["cambio"].";";
}

//Ahora obtenemos monedas
$query_mon = "SELECT * FROM moneda where estado >0;";
$result_mon = $db->executeQuery($query_mon);
while ($row = $db->fecth_array($result_mon)) {
	$actual = array_map('utf8_decode', $row);
	echo "var actual = new Array();";
	echo "actual['id'] = ".$actual["id"].";";
	echo "actual['nombre'] = '".$actual["nombre"]."';";
	echo "actual['simbolo'] = '".utf8_encode($actual["simbolo"])."';";
	echo "monedas.push(actual);";
}

?>
//Validamos esta variable para que no cierre ninguna mesa
var pk_mesa_cookie = "CREDITO";
var simbolo_nacional = monedas[0]["simbolo"];
</script>
<script type="text/javascript"  src="Public/scripts/Pedidos/Mensajes2cN.js.php"></script>
</body>
</html>
