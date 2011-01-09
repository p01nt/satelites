<?php
	require_once dirname(__FILE__) . '/../etc/boot.php';

	$satelites = new SatelitesCollection();

	foreach ($satelites->allowWrongWhois() as $satelite) {
		if ($satelite->blog()->wrongURL() || $satelite->blog()->wrongHomeURL()) {
			dd($satelite->getName());
			$satelite->blog()->updateURL();
			$satelite->blog()->updateHomeURL();
		}
	}
?>
