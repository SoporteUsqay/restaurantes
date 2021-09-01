<?php

/**
 * Clase que va permitir sacar la ubicacion y cargar automaticamente un archivo
 */
spl_autoload_register(function($className) {
//function __autoload($className){
    $className = str_replace('_', DIRECTORY_SEPARATOR, $className).'.php';
    //echo $className;
    require_once($className);
//}
});

?>
