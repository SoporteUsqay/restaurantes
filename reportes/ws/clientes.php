<?php
include_once('../recursos/componentes/MasterConexion.php');
$objconn = new MasterConexion();
if (isset($_POST['term'])) {
    $query = "(Select id, 'GENERICO' as dsp, nombre from cliente_generico where nombre like '%".$_POST["term"]."%') UNION (Select documento as id, documento as dsp, nombres as nombre from person where nombres like '%".$_POST["term"]."%') UNION (Select ruc as id, ruc as dsp, razonSocial as nombre from persona_juridica where razonSocial like '%".$_POST["term"]."%') UNION (Select documento as id, documento as dsp, nombres as nombre from person where documento = '".$_POST["term"]."') UNION (Select ruc as id, ruc as dsp, razonSocial as nombre from persona_juridica where ruc = '".$_POST["term"]."')";
    $r1 = $objconn->consulta_matriz_ex($query);
    $respuesta = array();
    $respuesta["results"] = $r1;
    echo json_encode($respuesta);
}else{
    $query = "Select ruc as id, ruc as dsp, razonSocial as nombre from persona_juridica";
    $r1 = $objconn->consulta_matriz_ex($query);
    $respuesta = array();
    $respuesta["results"] = $r1;
    echo json_encode($respuesta);
}