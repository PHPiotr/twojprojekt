<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require_once 'configs/configuration.php';

function __autoload($class) {
    require_once LIBRARY . "{$class}.class.php";
}

$app = new Bootstrap();
//$app->httpAuth();
$app->run();

