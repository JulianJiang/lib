<?php
ini_set('display_errors','On');
error_reporting(E_ALL);
require dirname(dirname(__FILE__)) . '/vendor/autoload.php';
require dirname(dirname(__FILE__)) . '/src/HuoLib/Tool/CrawDriver.php';
use HuoLib\Tool\CrawDriver;

$craw = new CrawDriver("http://www.hznzcn.com/product-322295.html");
$result = $craw->craw();
var_dump($result);
?>