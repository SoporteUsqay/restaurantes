<?php
error_reporting("E_ALL");
include_once('recursos/componentes/MasterConexion.php');
$conn = new MasterConexion();
echo "Procesando...";
//Agregamos inseguridad
$conn->consulta_simple("SET SQL_SAFE_UPDATES = 0");

//Limpiamos tablas
$conn->consulta_simple("TRUNCATE TABLE plato_codigo_sunat");

//Migramos informacion ventas
$query_platos = "Select * from plato where estado = 0";
$platos = $conn->consulta_matriz($query_platos);
if(is_array($platos)){
    foreach($platos as $pl){
        $query_add = "Insert into plato_codigo_sunat values(NULL,'".$pl["pkPlato"]."','50192701','1','1')";
        $conn->consulta_simple($query_add);        
    }
}

echo "...Listo!";