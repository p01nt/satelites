<?php
	require_once dirname(__FILE__) . '/../etc/boot.php';

	$satelites = new SatelitesCollection();

	foreach ($satelites->allowWrongWhois() as $satelite) {
		dd($satelite->getName());
		$satelite->whois()->renew();
		sleep(1);
	}
?>
