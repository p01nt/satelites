#!/usr/local/bin/php
<?php
	require_once dirname(__FILE__) . '/../etc/boot.php';

	$satelites = new SatelitesCollection();

	foreach ($satelites->onlyMy() as $satelite) {
		if (!$satelite->ns()->isMyIP()) {
			dd($satelite->getName());
			$satelite->ns()->updateIP();
		}
	}
?>
