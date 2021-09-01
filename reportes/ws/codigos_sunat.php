<?php
include_once('../recursos/componentes/MasterConexion.php');
$objconn = new MasterConexion();
if (isset($_POST['term'])) {
    $query = "(Select * from codigo_sunat where descripcion like '%".$_POST["term"]."%') UNION (Select * from codigo_sunat where id = '".$_POST["term"]."')";
    $r1 = $objconn->consulta_matriz_ex($query);
    $respuesta = array();
    $respuesta["results"] = $r1;
    echo json_encode($respuesta);
}else{
    $query = "Select * from codigo_sunat where destacado = 1";
    $r1 = $objconn->consulta_matriz_ex($query);
    $respuesta = array();
    $respuesta["results"] = $r1;
    echo json_encode($respuesta);
}