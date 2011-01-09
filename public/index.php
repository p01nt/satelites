<?php
	require_once dirname(__FILE__) . '/../etc/boot.php';
	$satelites = new SatelitesCollection();

	$timer->interval();
	$smarty->assign('satelites', $satelites);
	$timer->interval();
	$smarty->assign('timer', $timer);

	$smarty->caching = true;
	$smarty->display('index.tpl');
?>
