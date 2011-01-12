#!/usr/local/bin/php
<?php
	$cache_dir = dirname(__FILE__) . '/../tmp/cache';
	system('rm -f ' . $cache_dir . '/*');
?>
