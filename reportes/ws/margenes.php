<?php
include_once('../recursos/componentes/MasterConexion.php');
$objconn = new MasterConexion();
if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'addmargen':
            //Primero preguntamos
            $hay = $objconn->consulta_arreglo("Select * from margenes_impresion where impresora = '".str_replace('\\', '\\\\',$_POST["impresora"])."'");
            if(is_array($hay)){
                $objconn->consulta_simple("Update margenes_impresion set ratio = '".$_POST["ratio"]."' where id = '".$hay["id"]."'");
            }else{
                $objconn->consulta_simple("Insert into margenes_impresion values(NULL,'".str_replace('\\', '\\\\',$_POST["impresora"])."','".$_POST["ratio"]."')");
            }            
            echo json_encode(1);
            break;
        
        case 'getmargen':
            //Primero preguntamos
            $hay = $objconn->consulta_arreglo("Select * from margenes_impresion where impresora = '".str_replace('\\', '\\\\',$_POST["impresora"])."'");
            if(is_array($hay)){
                echo json_encode($hay["ratio"]);
            }else{
                echo json_encode("0.0");
            }            
            break;
    }
}?>