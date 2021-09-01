<!doctype html>
<html class="no-js">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">

  <title><?php echo Class_config::get('nameApplication') ?></title>

  <link href="./././Public/Bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="./././Public/Bootstrap/css/signin.css" rel="stylesheet">
  <link rel="stylesheet" href="Public/Usqay/css/usqay.css">
  <link rel="icon" href="logo.ico"/>

  <script src="./././Public/js/libs/modernizr-2.6.2.min.js"></script>
  <script type="text/javascript" src="./././Public/jquery-ui-1.10.4.custom/js/jquery-1.10.2.js"></script>
  <script type="text/javascript" src="./././Public/scripts/login.js.php"></script>
</head>
<body onload="">

  <div class="panel panel-primary">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Iniciar el día</h4>
        </div>

        <form action="?controller=Caja&&action=SaveMontoInicial2" method="post">
          <div class="modal-body">
            <div class="row">
              <div class="col-lg-6">
                <label for="cantidad">Ingrese el monto inicial del dia</label>
                <input
                class="form-control" name="cantidad"  id=""
                title="<?php echo Class_message::get('TxtEnterYourUser') ?>" placeholder="" />
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit"  class="btn btn-primary">Siguiente</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</body>
</html>
