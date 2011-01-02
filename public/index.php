<?php
	require_once dirname(__FILE__) . '/../etc/boot.php';
	$satelites = new SatelitesCollection();

	$smarty->assign('satelites', $satelites);
	$smarty->display('index.tpl');
?>
