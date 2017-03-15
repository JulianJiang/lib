<?php
	ini_set('display_errors','On');
	error_reporting(E_ALL);
	require dirname(dirname(__FILE__)) . '/vendor/autoload.php';
	require dirname(dirname(__FILE__)) . '/src/HuoLib/Driver/RedisDriver.php';
require dirname(dirname(__FILE__)) . '/src/HuoLib/Driver/MongoDriver.php';
	use HuoLib\Driver\RedisDriver;
	$redisDriver = new RedisDriver();
	var_dump($redisDriver);

	use HuoLib\Driver\MongoDriver;
	$redisDriver = new MongoDriver();
	var_dump($redisDriver);die();
?>