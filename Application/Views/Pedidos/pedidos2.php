<?php
error_reporting(E_ALL);
if (isset($_GET['cliente'])) {
  setcookie("cliente", $_GET['cliente'], time() + 360000, '/');
}
$db = new SuperDataBase();
?>
<!DOCTYPE html>
<?php
$objUserSystem = new UserLogin();
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
  <link rel="icon" href="logo.ico"/>
  <link rel="stylesheet" href="Public/Usqay/css/usqay.css">
  <link rel="stylesheet" type="text/css" href="Public/select2/css/select2.css" rel="stylesheet">
  <link rel="stylesheet" href="reportes/recursos/js/plugins/tablas/jquery.dataTables.min.css">
  <?php if(isset($_COOKIE["APP"])):?>
  <!--Estilos para APP-->
  <style>
  #contenTipoPlato{  
    overflow-x: hidden;
    overflow-y: hidden;
    text-align: center;
    position: relative;
  }

  #divProductos{
    overflow-x: hidden;
    overflow-y: hidden;
    text-align: center;
    position: relative;
  }

  .modal-dialog {
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
  </style>
  <?php else:?>
  <!--Estilos normales-->
  <style>
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
  </style>
  <?php endif;?>  
  <style> 

      button,
      button:focus{
          outline: none !important;
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
            border-radius: 10% !important;
            width: 135px;
            margin: 5px;
            height: 135px;
            font-size: 13px;
            font-weight: 800;
        }
        .usqay-btn-c {
            border-radius: 15% !important;
        }
        .usqay-btn-red{
            background-color: #ef4d4d !important;
        }
        .usqay-btn:hover{
            color: #ef4d4d !important;
        }
        .usqay-btn-red:hover{
            color: #ef4d4d !important;
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
            color: #ef6a00 !important;
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
            color: #ef6a00 !important;
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

        /*Para mostrar pedidos anulados*/
        .anulado{
          color: #000;
          background-color: #ffc5c5 !important;
        }

        .usqay-permiso-show {
          display: initial;
        }

        .usqay-permiso-no-show {
          display: none;
        }

        .container-motivo {
          padding: 1em;
          margin: 0.2em 0;
          border: 1px solid #f5f5f5;
          background: #e0ecff;
          display: block;
          border-radius: 20px;
          color: #005a93;
          text-align: center;
          cursor: pointer;
        }
  </style>
</head>

<body style="background-color: #f2f4f5;">
 <input type="hidden" id="terminal" value="<?php echo $_COOKIE['t'] ?>"/>
  <div class="navbar navbar-inverse navbar-fixed-top" role="navigation" style="background-color: #00395a !important; border-color: #00395a !important; border-bottom: #ef6a00 solid 5px !important;">
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

<!--Inicio Modal Ausente-->
<div class='modal fade' id='modal_idle' tabindex='-1' role='dialog' data-keyboard="false" data-backdrop="static" style="z-index: 999999 !important;">
  <div class='modal-dialog' style="display: table;">
    <div style="color:#FFF;text-align:center; font-size:32px; font-weight:bold;display: table-cell;vertical-align: middle;">
    <span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
    <br/>
    Bloqueado por Inactividad
    <br/>
    <button id="btn_desbloquear" type="button" class="btn btn-success"><b>Presiona aqui para desbloquear</b></button>
    </div>
  </div>
</div>
<!--Fin Modal-->

  <!--Inicio Modal Pacman-->
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

<!--Inicio Modal Menu-->
<div class='modal fade' id='modal_contenido_menu' tabindex='-1' role='dialog' data-keyboard="false" data-backdrop="static">
    <div class='modal-dialog modal-lg'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title' id='myModalLabel'>Arma el Menu</h4>
            </div>
            <div class='modal-body'>
                <table class="table table-striped">
                    <tbody id="componente">
                        
                    </tbody> 
                </table>
            </div>
            <div class="modal-footer">
                <span style="float: left !important;font-size: 20px; font-weight: bold;">Total: S/ <span id="total_menu">0.00</span></span>
                <!-- Aqui poner total-->
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="agrega_menu()">Agregar</button>
            </div>
        </div>
    </div>
</div>
<!--Fin Modal-->

<!--Inicio Modal Teclado-->
<div id="ModalCantidadPedir" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="txt_cantidad_pedir" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content row" style="margin:0px;">
      <div class="modal-body row">
            <div id="calc-board" class="col-lg-6">
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
              <form style="text-align: center;">
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
                  <a  style="padding-top:15px;background-color:#ef6a00 !important;color:#FFF !important;" onclick="cancela_alerta()" href="#" class="btn btn-cal"><span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span></a>
                  <a  style="padding-top:15px;background-color:#ef6a00 !important;color:#FFF !important;" onclick="resetP()" href="#" class="btn btn-cal"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                  <a style="padding-top:15px;background-color:#009839 !important;color:#FFF !important;" href="#" class="btn btn-cal" onclick="addPedido()"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></a>
                </div>
              </form>

          </div>
          <div class="col-lg-6" style="padding-top:35px;">
              Agrega un mensaje
              <div class="row">
                <div class="col-xs-10">
                  <textarea class="form-control teclado" id="txt_mensaje_send"></textarea>
                </div>
                <div class="col-xs-2">
                  <a style="background-color:#ef6a00 !important;color:#FFF !important;font-size: 25px;line-height: 39px;height: 50px;width: 50px;margin-left: -10px;" onclick="limpiar_mensaje_s()" href="#" class="btn"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
                </div>
              </div>   
              <div style="text-align: center; height:400px; overflow-x:hidden; overflow-y:scroll;margin-top:5px;">
              <?php
              $query = "SELECT m.* FROM mensaje m, accion_caja ac where ac.pk_accion = m.pkMensaje AND ac.tipo_accion = 'MSG' AND ac.caja = '".$_COOKIE['c']."'";
              $result = $db->executeQuery($query);
              $array = array();
              while ($row = $db->fecth_array($result)) {
                echo '<button class="btn usqay-btn-exx" onclick="addMessagePedidoS(\'' . utf8_encode($row['descripcion']) . '\')">' . utf8_encode($row['descripcion']) . '</button>';
              }
              ?>
              </div>
          </div>
    </div> 
  </div>
</div>
</div>
<!--Fin Modal-->

<!--Datos de versiones anteriores se mantienen por seguridad-->
<input id="documentoCliente" style="display: none;">
<input id="salon" style="display: none;">

<!-- inicio modal Pago multiple -->
<div id="modal_multiple_pago" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="modal_multiple" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content row" style="margin:0px;">
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
        <div class="row">
          <div id="descuento_pago" class="col-lg-6">
            <div class="col-lg-12" style="font-size: 22px;margin-bottom: 10px; margin-top:-15px;">
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
            <div class='control-group col-lg-6' style="text-align:right;">
                <br/>
                <button type='button' class='btn btn-primary' onclick='aplicar_descuento()'>Aplicar</button>
            </div>
          </div>
          <!--Fin descuento-->
          <!--Propinas-->
          <div id="propina_pago" class="col-lg-6">
            <div class="col-lg-12" style="font-size: 22px;margin-bottom: 10px;">
              <span class="label label-info">Propinas</span>
            </div>
            <div class='control-group col-lg-6'>
                <label>Trabajador</label>
                <select style="width:100%;" id="trabajador_propina_pago" name="trabajador_propina_pago" onchange="actualiza_propina(1)">
                <?php
                $query_tra = "SELECT * FROM trabajador where estado = 0;";
                $result_tra = $db->executeQuery($query_tra);
                while ($row = $db->fecth_array($result_tra)) {
                  echo "<option value='".$row["pkTrabajador"]."'>".$row["nombres"]." ".$row["apellidos"]."</option>";
                }
                ?>              
                </select> 
            </div>
            <div class='control-group col-lg-6'>
                <label>Medio</label>
                <select class="form-control" id="medio_propina_pago" name="medio_propina_pago" onchange="actualiza_propina(2)">
                <?php
                $query_pag = "SELECT * FROM medio_pago where estado = 1;";
                $result_pag = $db->executeQuery($query_pag);
                while ($row = $db->fecth_array($result_pag)) {
                  echo "<option value='".$row["id"]."|".$row["moneda"]."'>".$row["nombre"]."</option>";
                }
                ?>
                </select> 
            </div>
            <div class='control-group col-lg-4'>
              <label>Moneda</label>
              <select class="form-control" id="moneda_propina_pago" name="moneda_propina_pago" onchange="actualiza_propina(3)">
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
            <div class='control-group col-lg-4'>
                <label>Monto</label>
                <input value="0" id="monto_propina_pago" name="monto_propina_pago" class="form-control" type="number" onchange="actualiza_propina(4)">
            </div>
            <div class='control-group col-lg-4' style="text-align:right;">
                <br/>
                <button type='button' class='btn btn-primary' onclick='agregar_propina()' min="0">Agregar</button>
            </div>
          </div>
        </div>
        <!--Fin propinas-->
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
          <div class='control-group col-lg-2' style="text-align:right;">
              <br/>
              <button type='button' class='btn btn-primary' onclick='agrega_pago()'>Agregar</button>
          </div>
          <div class='col-lg-12'>
            <hr/>
          </div>
        </div>
        <!--Fin medios de pago-->
        <div id="montos_finales" class="row" style="font-size:24px;font-weight:bold;height:55px;">
          <div class='control-group col-lg-4 col-xs-4' style="text-align:center;">
          A Pagar: <span id="monto_por_pagar">...</span>
          </div>
          <div class='control-group col-lg-4 col-xs-4' style="text-align:center;">
          Pagado: <span id="pagado">...</span>
          </div>
          <div class='control-group col-lg-4 col-xs-4' style="text-align:center;">
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
          <tbody id="table_detraccion">
            <?php
              $porcentaje_detraccion = 0;
              $query_detraccion = "Select * from cloud_config where parametro = 'porcentaje_detraccion'"; 
              $res_detraccion = $db->executeQueryEx($query_detraccion);
              if ($row_detraccion = $db->fecth_array($res_detraccion)) {
                $query_detraccion2 = "select * from porcentaje_detraccion where id = '${row_detraccion['valor']}'";
                $res_detraccion2 = $db->executeQueryEx($query_detraccion2);
                if ($row_detraccion2 = $db->fecth_array($res_detraccion2)) {
                  $porcentaje_detraccion = floatval($row_detraccion2['porcentaje']);
                }
              }
            ?>
            <script>
              var porcentaje_detraccion = <?php echo $porcentaje_detraccion; ?>;
            </script>
            <?php if ($porcentaje_detraccion): ?>
            <tr class="odd">
                <td class="text-left">
                  <input type="checkbox" id="check_detraccion" >
                </td>
                <td></td>
                <td>
                  <label for="">DETRACCIÓN (<?php echo $porcentaje_detraccion; ?>%)</label>
                </td>
                <td>
                  S/ 
                </td>
                <td>
                  <label id="lblTotalDetraccion" for=""><strong id="totalDetraccion">0</strong></label>
                </td>
                <td></td>
            </tr>
            <?php endif ?>
          </tbody>
          </table>
        </div>
        </div>
        <!--Fin formas de pago-->

        <!-- <div class="form-group">
            <p><input type="checkbox" id="check_detraccion" class="pull-left">
            <label class="pull-left" for="check_detraccion" style="margin-left:10px;">Venta con Detracción (<?php echo $porcentaje_detraccion; ?>%)</label></p>
            <label id="lblTotalDetraccion" hidden for="" class="pull-right">Total Detracción: S/ <strong id="totalDetraccion">15.00</strong></label>
        </div> -->

      </div>
      <div class="modal-footer">
        <div class="form-group pull-left">
            <p><input type="checkbox" id="check_consumo" class="pull-left">
            <label class="pull-left" style="margin-left:10px;">Por Consumo</label></p>
            <br/>
            <p><input type="checkbox" id="check_propina" class="pull-left" <?php if(isset($_COOKIE["fpropina"])){echo "checked";}?>>
            <label class="pull-left" style="margin-left:10px;">Facturar Propina</label></p>
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
          <div class="form-group">
            <label id="lbl_comentario_sin_pago" for="comentario_sin_pago" class="col-sm-2 control-label">Comentario</label>
            <div class="col-sm-10">
              <input class="form-control" name="comentario_sin_pago" id="comentario_sin_pago" placeholder="">
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

<!--Inicio del modal cambia mesa-->
<div id="ModalChangeMesa" class="modal fade " tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="lblTitleCambioMesa"></h4>
      </div>
      <div class="modal-body" id="contenBodyMesas">
      </div>
    </div>
  </div>
</div>
<!-- fin modal Cambio de mesa -->

<!-- Mensajes para pedidos -->
<div id="modalMensaje" class="modal fade " tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="">Selecciona el Mensaje</h4>
      </div>
      <div class="modal-body" >
        <div class="row">
          <div class="col-lg-8" id="divMensajes">
            <input style="display: none" id="txtIdPedido">
            <?php
            $query = "SELECT m.* FROM mensaje m, accion_caja ac where ac.pk_accion = m.pkMensaje AND ac.tipo_accion = 'MSG' AND ac.caja = '".$_COOKIE['c']."'";
            $result = $db->executeQuery($query);
            $array = array();
            while ($row = $db->fecth_array($result)) {
              echo '<button class="btn usqay-btn-exx" onclick="addMessagePedido(\'' . utf8_encode($row['descripcion']) . '\')">' . utf8_encode($row['descripcion']) . '</button>';
            }
            ?>
          </div>
          <div class="col-lg-4">
            Mensaje Actual
            <textarea class="form-control teclado" id="textAreaMessage" val=""></textarea>
            <p></p>
            <button onclick="saveMessagePedido($('#textAreaMessage').val())" class="btn btn-success btn-lg">
              Guardar
            </button>
            <a style="background-color:#ef6a00 !important;color:#FFF !important;font-size:18px;line-height:22px;" onclick="limpiar_mensaje()" href="#" class="btn btn-lg"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-danger btn-lg" onclick="$('#modalMensaje').modal('hide');">Cancelar</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Gino Lluen - 2018 -->
<div id="modDatosCliente" class="modal fade">
    <div class="modal-dialog" >
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h2 class="modal-title" id="modalAperturaMesaDeliveryTitle">Datos del cliente</h2>
            </div>
            <div class="modal-body ">
                <form id="frmClientePLllevar" >
                    <table style="width: 100%;">
                        <tr id="rowTelefono">
                            <td>Teléfono</td>
                            <td><input type="number" name="telefonoCliente" class="form-control reset" id="txtPhoneCliente" placeholder="Ingrese el teléfono - Presione Enter para iniciar la búsqueda"></td>
                        </tr>
                        <tr id="rowCliente">
                            <td>Cliente</td>
                            <td><input name="nombreCliente" class="form-control reset" id="txtNameCliente" placeholder="Ingrese el nombre del cliente"></td>
                        </tr>
                        <tr id="rowDocumento">
                            <td>DNI/RUC</td>
                            <td><input type="number" name="documentoCliente" class="form-control reset" id="txtDocCliente" placeholder="Ingrese el ID del cliente"></td>
                        </tr>
                        <tr id="rowDireccion">
                            <td>Dirección</td>
                            <td><input name="direccionCliente" class="form-control reset" id="txtDireccionCliente" placeholder="Ingrese la direccion del cliente"></td>
                        </tr>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="guardaCliente()">Guardar Datos</button>
            </div>
        </div>
    </div>
</div>

<!-- Platos para cambio :: Gino Lluen 2018 TU PAPI -->
<div id="modalPlatoCambio" class="modal fade " tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="">Cambio de Plato</h4>
      </div>
      <div class="modal-body">
            <input style="display: none" id="txtIdPedidoCP">
            <label id="porg">Plato: </label><br/>
            <label id="corg">Cantidad: </label><br/>
            <label id="morg">Precio unitario: </label><br/>
            <label>Total: S/<span id="torg"></span></label><br/>
            <hr/>
            <p></p>
            <label>Plato Cambio:</label>
            <select id="pkch" style="width:100%;">
            <?php
            $query0 = "Select * from plato where estado = 0";
            $result0 = $db->executeQuery($query0);
            while ($row0 = $db->fecth_array($result0)) {
              echo "<option value='".$row0["pkPlato"]."'>".utf8_encode($row0["descripcion"])." - S/ ".$row0["precio_venta"]."</option>";
            }
            ?>
            </select><br/>
            <label>Cantidad Cambio:</label>
            <input id="cach" type="number" value="1" step="1" min="1" style="width: 100%;padding: 4px;border-radius: 3px; border: solid 1px #aaa;"><br/>
            <p></p>
            <input type="checkbox" name="agrupar" value="IEZ" id="agrch" checked="">  <b>¿Agrupar?</b><br>
            <p></p>
            <button class="btn btn-success" onclick="hacer_magia_cambio();">Agregar</button>
            <hr/>
            <p></p>
            <table class="table table-bordered">
            <thead>
              <tr>
                <th>#</th>
                <th>Plato</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Total</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="tabla_cambio">
              
            </tbody>
            </table>
            <label>Total Cambio: S/<span id="tchan">0.00</span></label>
            
      </div>
        <div class="modal-footer">
          <button class="btn btn-danger btn-lg" onclick="termina_magia()">Cancelar</button>
          <button class="btn btn-success btn-lg" onclick="confirma_magia()">Terminar</button>
        </div>
      </div>
    </div>
  </div>
</div>

<br><br><br><br>
<div class="panel-ventas">
  <?php 
  $wide_div = 6;
  if(isset($_COOKIE["APP"])):
  $wide_div = 12;
  ?>
  <!--Si es movil mostramos tab panel -->
  <ul class="nav nav-tabs" role="tablist" style="margin:-10px 15px 0px 15px;">
    <li role="presentation" class="active" style="width:50%;font-weight: bold; text-align: center;">
      <a href="#carta" aria-controls="carta" role="tab" data-toggle="tab">Carta</a>
    </li>
    <li role="presentation" style="width:50%;font-weight: bold; text-align: center;" onclick="loadDetalles();">
      <a href="#pedido" aria-controls="pedido" role="tab" data-toggle="tab">Pedido</a>
    </li>
  </ul>
  <!-- Fin tab panel movil -->
  <?php endif;?>

  <?php if(isset($_COOKIE["APP"])):?>
  <!--DIV inicio tabs -->
  <div class="tab-content">
  <!--DIV inicio tabs -->
  <?php endif;?>

  <?php if(isset($_COOKIE["APP"])):?>
  <div role="tabpanel" class="tab-pane active" id="carta">
  <?php endif;?>
  <!--Panel Carta-->
  <div class="col-lg-<?php echo $wide_div;?>">
    <div class="row">
      <div class="col-lg-12">
        <!-- panel que lista categorias -->
        <input id="TxtdescripcionProduct" type="text" class="form-control" placeholder="Ingresa el nombre del plato o producto, luego presiona ENTER">
        <div class="panel panel-pedido" id="contenedor_tipos">
          <div class="panel-heading" id="panel-productos">
            Categorias
          </div>
          <div class="panel-body" id="contenTipoPlato">
      
          </div>
        </div>

        <!-- panel que lista productos -->
        <div class="panel panel-pedido" id="contenedor_platos" <?php if(isset($_COOKIE["APP"])):?>style="display:none;"<?php endif;?>>
          <div id="panel-productos" class="panel-heading">
              Platos y Productos
          </div>
          <div class="panel-body" id="divProductos">

          </div>
        </div>
      </div>
    </div>
  </div>
  <!--Panel Carta-->
  <?php if(isset($_COOKIE["APP"])):?>
  </div>
  <?php endif;?>

  <?php if(isset($_COOKIE["APP"])):?>
  <div role="tabpanel" class="tab-pane" id="pedido">
  <?php endif;?>
  <!--Panel Pedido-->
  <div class="col-lg-<?php echo $wide_div;?>">
    <input style="display: none" id="txtCombrobante" type="text" readonly="true">
    <div class="panel panel-pedido">
            <div class="panel-heading">
                <span class="glyphicon glyphicon-home" aria-hidden="true"></span> <label id="lblnSalon"></label> - 
                <span class="glyphicon glyphicon-inbox" aria-hidden="true"></span> <label id="lblnMesa"></label> - 
                <span class="glyphicon glyphicon-briefcase" aria-hidden="true"></span> <label id="lblnMoso"></label>
            </div>
            <div class="panel-heading" style="height: auto !important;">
                <span class="glyphicon glyphicon-user" aria-hidden="true"></span> <b>Cliente: </b>
                <label id="lblNombreCliente"></label>
                <input type="hidden" id="tipoPedidoC"/>
                <input type="hidden" id="idClienteC"/>
                <input type="hidden" id="documentoClienteC"/>
                <input type="hidden" id="nombresClienteC"/>
                <input type="hidden" id="telefonoClienteC"/>
                <input type="hidden" id="direccionClienteC"/>
                <a href="#" onclick="editaClienteP()" style="margin-left:5px !important;"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
                <br/>
                <span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span> <b>Personas en Mesa:</b> <input id="txtnPesonas"  onblur="updateNPersonas()" type="number" min="1" style="border-radius: 3px; text-align: center; border: 1px solid #eaf2ff;width:60px;">
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
          <td style="width:30%;">  
              <button onclick="editaPrecio()" class="btn btn-default admin <?php echo (UserLogin::havePermission('btn-edit-price')); ?>" data-placement="bottom"><span class="glyphicon glyphicon-pencil"></span></button>
              <button onclick="cambiaPlato()" class="btn btn-default admin <?php echo (UserLogin::havePermission('btn-change-plate')); ?>" data-placement="bottom"><span class="glyphicon glyphicon-retweet"></span></button> 
              <button onclick="loadDetalles()" class="btn btn-default <?php echo (UserLogin::havePermission('btn-refresh')); ?>" data-placement="bottom"><span class="glyphicon glyphicon-refresh"></span></button>
              <button onclick="showMessages()" class="btn btn-default <?php echo (UserLogin::havePermission('btn-messages')); ?>" data-placement="bottom"><span class="glyphicon glyphicon-envelope"></span></button>
          </td>
          <td style="width:30%;">Total S/ <label id="lblTotal2019">0.00</label></td>      
        </tr>
      </table>

      <table class="table">
        <tr>
          <td valign="middle" align="center">
            <!--Clase Admin muestra botones importantes solo para Usuarios con mas permisos -->
            <button class="botonesIcon btn-pedido btn usqay-btn-ex btn-lg admin <?php echo (UserLogin::havePermission('btn-ticket')); ?>"
                    title="Cancela el pedido con ticket" 
                    onclick="modal_pagar_2019(0);">
                <img src="Public/images/iconos2017/efectivo.png" style="margin-bottom: 10px;max-height: 35px !important;"><br/>
                Ticket           
            </button>

            <button class="botonesIcon btn-pedido btn usqay-btn-ex btn-lg admin <?php echo (UserLogin::havePermission('btn-boleta')); ?>"
                    onclick="modal_pagar_2019(1)"
                    title="Generar comprobante - Boleta">
                <img src="Public/images/iconos2017/boleta.png" style="margin-bottom: 10px;max-height: 35px !important;"><br/>
                Boleta
            </button>

            <button class="botonesIcon btn-pedido btn usqay-btn-ex btn-lg admin <?php echo (UserLogin::havePermission('btn-factura')); ?>"
                    onclick="modal_pagar_2019(2)">
            <img src="Public/images/iconos2017/factura.png" style="margin-bottom: 10px;max-height: 35px !important;"><br/>
            Factura
            </button>

            <button class="botonesIcon btn-pedido btn usqay-btn-ex btn-lg <?php echo (UserLogin::havePermission('btn-precuenta')); ?>" title="Imprimir la cuenta"
                    onclick="openImprimirCuenta()" >
             <img src="Public/images/iconos2017/precuenta.png" style="margin-bottom: 10px;max-height: 35px !important;"><br/>   
             PreCuenta
            </button>

            <button class="botonesIcon btn-pedido btn usqay-btn-ex btn-lg admin <?php echo (UserLogin::havePermission('btn-credito')); ?>"
                    onclick="sin_pago(1)">
                <img src="Public/images/iconos2017/credito.png" style="margin-bottom: 10px;max-height: 35px !important;"><br/>
                CRÉDITO
            </button>
            <button class="botonesIcon btn-pedido btn usqay-btn-ex btn-lg admin <?php echo (UserLogin::havePermission('btn-consumo')); ?>"
                    onclick="sin_pago(2)">
                <img src="Public/images/iconos2017/consumo.png" style="margin-bottom: 10px;max-height: 35px !important;"><br/>
                CONSUMO
            </button>      
              
            <button class="botonesIcon btn-pedido btn usqay-btn-ex btn-lg admin <?php echo (UserLogin::havePermission('btn-cambio-mesa')); ?>"
                  onclick="openModalChangeMesa(<?php echo $_GET['pkMesa']; ?>);"
                  title="Cambiar el pedido a otra mesa desocupada">
                <img src="Public/images/iconos2017/cambio_mesa.png" style="margin-bottom: 10px;max-height: 35px !important;"><br/>
                Cambio Mesa
            </button>
                          
            <button class="botonesIcon btn-pedido btn usqay-btn-ex btn-lg admin <?php echo (UserLogin::havePermission('btn-liberar-mesa')); ?>" onclick="_showLiberarMesa()">
            <img src="Public/images/iconos2017/liberar_mesa.png" style="margin-bottom: 10px;max-height: 35px !important;"><br/>  
            Libera Mesa
            </button>
             
            <button class="botonesIcon  btn usqay-btn-ex btn-lg btn-pedido <?php echo (UserLogin::havePermission('btn-anular-todo')); ?>" style="background-color:#ef6a00 !important;" onclick="_showAnulaPedido()"
                    title="Anula todo el item seleccionado">Anular Todo</button>
            
            <button class="botonesIcon  btn usqay-btn-ex btn-lg btn-pedido <?php echo (UserLogin::havePermission('btn-anular-uno')); ?>" style="background-color:#a9a10a !important;" onclick="_showAnulaPedido1()"
                    title="Anula una unidad del item seleccionado">Anular 1</button>
            
            <button class="botonesIcon btn-pedido btn usqay-btn-ex btn-lg  <?php echo (UserLogin::havePermission('btn-enviar-pedido')); ?>" style="background-color:#0086cf !important;" onclick="confirImpresion(2)">Enviar Pedido</button>
            
          </td>
        </tr>
      </table>
    </div>
  </div>
  <!--Panel Pedido-->
  <?php if(isset($_COOKIE["APP"])):?>
  </div>
  <?php endif;?>

  <?php if(isset($_COOKIE["APP"])):?>
  <!--DIV fin tabs-->
  </div>
  <?php endif;?>
  <!--DIV fin tabs-->
  <!--fin dE PEDIDOS-->
</div>
</div>

<!-- Platos para cambio :: Gino Lluen 2018 TU PAPI -->
<div id="modMotivoAnulacionPedido" class="modal fade " tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title" id="">Motivo de Anulación</h4>
      </div>
      <div class="modal-body">

        <div class="row">
        
          <div class="col-xs-12 col-md-8">
            <div class="form-group">
              <label for="">Motivo</label>
              <input type="text" class="form-control" id="usqay-input-razon-anulacion">  
            </div>
          </div>
        
          <div class="col-xs-12 col-md-4">

            <button class="btn btn-danger btn-lg btn-block" style="margin-top: 15px" onclick="_resolverAnulacion()">ANULAR</button>

            <!-- <div class="form-group">
              <label for="">Motivo</label>
              <input type="text" class="form-control" id="usqay-input-razon-anulacion">  
            </div> -->
          </div>
        </div>

        <div class="row">

        <hr>

          <?php 
            $query_motivos_anulacion = "select * from motivo_anulacion_predefinido";

            $res_motivo = $db->executeQuery($query_motivos_anulacion);

            while($row = $db->fecth_array($res_motivo)):
          ?>

          <div class="col-xs-12 col-md-6">
              <div class="container-motivo" onclick="$('#usqay-input-razon-anulacion').val('<?php echo $row['nombre']; ?>')">
                <?php echo $row['nombre']; ?>
              </div>
          </div>


          <?php 
            endwhile;
          ?>
        
        </div>


            
      <!-- </div>
        <div class="modal-footer">
          <button class="btn btn-default btn-lg" onclick="$('#modMotivoAnulacionPedido').modal('hide')">Cancelar</button>
          <button class="btn btn-danger btn-lg" onclick="confirma_magia()">ANULAR</button>
        </div>
      </div> -->
    </div>
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

//variable con id de mesa
echo "var pk_mesa_cookie = ".$_GET['pkMesa'].";";

if(isset($_COOKIE["APP"])){
  echo "var app = 1;";
}else{
  echo "var app = 0;";
}

if(isset($_COOKIE["SV"])){
  echo "var verificacion = 1;";
}else{
  echo "var verificacion = 1;";
}
?>
var simbolo_nacional = monedas[0]["simbolo"];
</script>
<script type="text/javascript"  src="Public/scripts/Pedidos/Mensajes2N.js.php"></script>
</body>
</html>
