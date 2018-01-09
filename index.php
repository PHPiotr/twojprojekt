<?php
require_once 'configs/configuration.php';

function __autoload($class) {
    require_once LIBRARY . "{$class}.class.php";
}

$app = new Bootstrap();
$app->run();

