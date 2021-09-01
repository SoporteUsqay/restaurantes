<?php
include_once('../recursos/componentes/MasterConexion.php');
$objconn = new MasterConexion();
if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'addmodule':
            $objconn->consulta_simple("Insert into trabajador_modulo value(NULL,'".$_POST["modulo"]."','".$_POST["trabajador"]."')");
            break;
        
        case 'removemodule':
            $objconn->consulta_simple("SET SQL_SAFE_UPDATES = 0;");
            $objconn->consulta_simple("Delete from trabajador_modulo where id_modulo = '".$_POST["modulo"]."' AND id_trabajador = '".$_POST["trabajador"]."'");
            break;
        
        case 'addsubmodule':
            $objconn->consulta_simple("Insert into trabajador_submodulo value(NULL,'".$_POST["submodulo"]."','".$_POST["trabajador"]."')");
            break;
        
        case 'removesubmodule':
            $objconn->consulta_simple("SET SQL_SAFE_UPDATES = 0;");
            $objconn->consulta_simple("Delete from trabajador_submodulo where id_submodulo = '".$_POST["submodulo"]."' AND id_trabajador = '".$_POST["trabajador"]."'");
            break;

    }
}?>