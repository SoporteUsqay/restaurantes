<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo $titulo_pagina;?></title>

    <!-- Bootstrap core CSS -->
    <link href="recursos/css/bootstrap.min.css" rel="stylesheet">
    <link href="recursos/css/bootstrap-overrides.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="recursos/css/offcanvas.css" rel="stylesheet">
    <link href="recursos/css/jquery-ui.css" rel="stylesheet">

    <link rel="stylesheet" href="../Public/Usqay/css/usqay.css">
    <link href="../Public/select2/css/select2.css" rel="stylesheet">
    <link rel="icon" href="../logo.ico"/>

    <link rel="stylesheet" href="recursos/btable/bootstrap-table.min.css">
    <link rel="stylesheet" href="recursos/btable/bootstrap-table-group-by.css">
    <style>
        .dataTables_filter{
            margin-right: 10px !important;
        }
    </style>
  </head>

  <body>
    <?php
    $p_reportes = 1;
    require_once '../Components/Config.inc.php'; 
    require_once '../Application/Views/template/menuLeft.tpl.php';
    ?> 

    <div class="container">
      <div class="row row-offcanvas row-offcanvas-right">
        <div class="col-xs-12 col-sm-12">
           <form role="form" id="frmall" class="form-horizontal" method="post">
