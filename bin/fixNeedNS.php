#!/usr/local/bin/php
<?php
	require_once dirname(__FILE__) . '/../etc/boot.php';

	$satelites = new SatelitesCollection();
	foreach ($satelites as $satelite) {
		dd($satelite->getName());
		$satelite->ns()->renewNeedNS();
	}
?>
