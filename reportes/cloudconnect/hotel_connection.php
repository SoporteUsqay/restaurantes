<?php
require_once '../recursos/componentes/MasterConexion.php';
$conn = new MasterConexion();

//Obtenemos Pedidos
$creditos = $conn->consulta_matriz("Select * from pedido where estado = 4 AND documento = '".$_REQUEST["doc"]."' ");

echo json_encode($creditos);