<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$config['securityCode'] = 'df023d239d0k2d90223d23lj237dh2hd23';
$config['nameApplication'] = 'usqay';
$config['descriptionApplication'] = 'Sistema de Gestion de Restaurante';
$config['timeClose'] = 2000;
$config['timeOpen'] = 4000;
$config['pkInsumo'] = 4;

$config['urlApp'] = '//' . $_SERVER['HTTP_HOST'] . '/' . $config['nameApplication'];
//$config['urlApp'] = '//' . $_SERVER['HTTP_HOST'] . '';
$config['igv'] = 18;

$config['mode'] = "2";

$config['hostNameDataBase'] = 'localhost';
$config['userDataBase'] = 'root';
$config['nameDataBase'] = 'restaurantes';
$config['passwordDataBase'] = ' ';
$config['portDataBase'] = 3306;
$config['DataBase'] = 'Mysql';

/*
 * @var Lang Contiene el Lenguaje Actual de la Aplicacion
 */

$lenguage = "";
if (isset($_GET['lang'])) {
    $lenguage = $_GET['lang'];
}
$urlLanguage = "";
switch ($lenguage) {
    case 'es':
        $urlLanguage = "Spanish";
        break;
    case 'en':
        $urlLanguage = "English";
        break;
    default:
        $urlLanguage = "Spanish";
        break;
}
$config['Lang'] = $urlLanguage;
$config['nameEnterprise'] = '';
/*
 * @var $pathLang Contiene el nombre del archivo a seleccionar
 */
$config['nameCMS'] = 'Bootstrap';

$pathLang = $config['Lang'] . '.php';

require_once 'Library/Framework/config.php';
require_once 'Library/Framework/Class.php';
require_once 'Library/Framework/Sesion.php';

require_once 'Library/Framework/db/' . $config['DataBase'] . '.php';
require_once 'Library/Framework/message.php';
require_once 'Library/Framework/App.php';

require_once 'Messages/' . $pathLang;
