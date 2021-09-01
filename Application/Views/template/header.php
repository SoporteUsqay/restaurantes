<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php if(isset($titulo_importante)):?>
    <title><?php echo $titulo_importante; ?></title>
    <?php else:?>
    <title><?php echo Class_config::get('nameApplication') ?></title>
    <?php endif;?>
    <link rel="stylesheet" type="text/css" href="Public/jquery-easyui/easyui/themes/default/easyui.css">
    <link rel="icon" href="logo.ico"/>
    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="Public/jquery-ui-1.10.4.custom/css/jquery-ui.min.css">

    <link href="Public/Bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="Public/Bootstrap/media/css/jquery.dataTables.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="Public/css/style.css">

    <script src="Public/js/Chart.js" type="text/javascript"></script>

    <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery-1.10.2.js"></script>
    <script type="text/javascript" src="Public/jquery-ui-1.10.4.custom/js/jquery-ui-1.10.4.custom.js"></script>
    <script type="text/javascript" src="Public/jquery-easyui/easyui/jquery.easyui.min.js"></script>

    <script type="text/javascript"src="Public/jquery-ui-1.10.4.custom/js/jquery.numeric.js"></script>
    <script type="text/javascript"src="Public/jquery-ui-1.10.4.custom/js/jquery.maskedinput.js"></script>
    <script type="text/javascript"src="Public/jquery-ui-1.10.4.custom/js/jquery.validate.min.js"></script>
   

    <script type="text/javascript"src="Public/scripts/body.js.php"></script>
    <script type="text/javascript"src="Public/scripts/listGeneral.js.php"></script>
    <script type="text/javascript"src="Public/scripts/Validation.js.php"></script>
    <script  type="text/javascript" src="Public/Bootstrap/js/bootstrap.min.js"></script>
    <script  type="text/javascript" src="Public/Bootstrap/media/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="Public/Usqay/css/clc.css">
    <link rel="stylesheet" href="Public/Usqay/css/usqay.css">
    <link rel="stylesheet" href="ReportesGraficos/Highcharts/api/css/font-awesome.css">
    

  </head>


  <script>

    function openCambiarClave() {
      $("#WindowCambiarPassword").window('open');
    }

  </script>

  

  <script>


    function CambiarPassword() {

      //        if ($("#frmCambioPassword").form('validate') == true) {
      if ($("#txtNuevoClave").val() == $("#txtRepetirClave").val()) {

        var param = {usuario: $('#txtUsuario').val(), clave: $('#txtNuevoClave').val(), repetirclave: $('#txtRepetirClave').val()};

        $.ajax({
          type: "GET",
          url: "<?php echo Class_config::get('urlApp') ?>/?controller=User&&action=CambiarClave",
          data: param, // Adjuntar los campos del formulario enviado.
          dataType: 'html',
          success: function (data)

          {
            if (data == "true") {
              //                            $('#dlg-Cliente').dialog('close'); // close the dialog
              //                            $('#tblClientes').datagrid('reload');
            }
            else {
              $.messager.show({
                title: 'Estado',
                msg: "Se ha actualizado su password correctamente"
              });
            }
            $('#WindowCambiarPassword').dialog('close');
            //
          }

        });
      }
      else {

        $.messager.show({
          title: 'Error',
          msg: "las claves deben ser iguales"
        });
      }


    }



  </script>
