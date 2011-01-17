<?php
	$host = $_SERVER['HTTP_HOST'];
	
	$database = 'satelites_' . $host;
	$database = str_replace('.', '_', $database);
	$database = str_replace('-', '_', $database);
	define('DB_NAME', $database);
?>
