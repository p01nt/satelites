<?php
	require_once dirname(__FILE__) . '/../etc/boot.php';

	$template = 'index.tpl';
	$smarty->caching = true;
	if (isset($argv[1]) && $argv[1] == 'update') {
		$smarty->caching = false;
	}

	$satelites = new SatelitesCollection();

	$timer->interval();
	$smarty->assign('satelites', $satelites);
	$timer->interval();
	$smarty->assign('timer', $timer);

	$smarty->display($template);
?>
