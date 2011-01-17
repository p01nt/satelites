#!/usr/local/bin/php
<?php
	require_once dirname(__FILE__) . '/../etc/boot.php';

	$satelites = new SatelitesCollection();

	foreach ($satelites->allowWrongWhois() as $satelite) {
		if (!$satelite->blog()->issetDatabase()) {
			dd($satelite->getName());
			$satelite->blog()->createDatabase();
		}
	}
?>
