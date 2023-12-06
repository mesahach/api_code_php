<?php
spl_autoload_register('myAutoLoader');

function myAutoLoader($className)
{
	// $url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	// if (stripos($url, 'includes') == TRUE) {
	// 	$path = '../Classes/';
	// } else {
	// 	$path = 'Classes/';
	// }

	$path = '../Classes/';

	$extension = 'Class.php';
	require_once $path . $className . $extension;
}