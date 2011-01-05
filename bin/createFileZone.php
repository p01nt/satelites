<?php
	require_once dirname(__FILE__) . '/../etc/boot.php';

	$satelites = new SatelitesCollection();
	foreach ($satelites as $satelite) {
		$satelite->ns()->renewNeedNS();
		if (!$satelite->ns()->isMyIPOnNeedNS()) {
			dd($satelite->getName());
			$row = $satelite->ns()->zone();
			
			$conf = '/var/named/etc/namedb/named.conf';
			file_put_contents($conf, file_get_contents($conf) . "\n" . $row);

			$content = $satelite->ns()->zoneFile();
			$file = '/var/named/etc/namedb/master/satelites/' . $satelite->getName();
			file_put_contents($file, $content);
		}
	}
?>
