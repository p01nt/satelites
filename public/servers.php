<?php
	require_once dirname(__FILE__) . '/../etc/boot.php';
	dd($memcache->stats());
?>
