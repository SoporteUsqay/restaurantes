<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title>Usqay - Manual de Usuario</title>
        <meta charset="UTF-8">
        <!-- Bootstrap core CSS -->
        <link href="recursos/css/bootstrap.min.css" rel="stylesheet">
        <link href="recursos/css/bootstrap-overrides.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="recursos/css/offcanvas.css" rel="stylesheet">
        <link href="recursos/css/jquery-ui.css" rel="stylesheet">
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../Public/Usqay/css/usqay.css">
        <link rel="icon" href="../logo.ico"/>
                <style>
            html{
                height: 100%;
            }
            
            body{
                overflow: hidden !important;
                margin: 0px;
                height: 100%;
            }
            
        </style>
        <script src="recursos/js/jquery.js"></script>
        <script src="recursos/js/jquery-ui.js"></script>
        <script src="recursos/js/plugins/datatables/jquery-datatables.js"></script>
        <script src="recursos/js/plugins/datatables/dataTables.tableTools.js"></script>
        <script src="recursos/js/bootstrap.min.js"></script>
        <script src="recursos/js/offcanvas.js"></script>
    </head>
    <body>
    <?php 
    require_once '../Components/Config.inc.php';
    require_once '../Application/Views/template/menuLeft.tpl.php';
    ?> 
    <object data="manual.pdf" style="width:100%; height:105%;margin-top:-15px;"></object>

    </body>
    </html>

