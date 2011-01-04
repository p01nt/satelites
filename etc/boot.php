<?php
	define('BASE', dirname(__FILE__) . '/..');

	error_reporting(E_ALL);
	ini_set('display_errors', 'on');

	$config = parse_ini_file(BASE . '/etc/config.ini', true);

	ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . BASE . '/libs/smarty');
	define('SMARTY_DIR', BASE . '/libs/smarty/');
	$smarty = new Smarty();
	$smarty->template_dir = BASE . '/templates/';
	$smarty->compile_dir = BASE . '/tmp/templates_c/';

	$db = DB::getInstance();
	$db->set_master_host($config['master']['host'], $config['master']['user'], $config['master']['password']);
	$db->set_database($config['database']['name']);

	$memcache = Memcache::getInstance();
	$memcache->addServer();

	function __autoload($classname) {
		$file = BASE . '/classes/' . $classname . '.class.php';

		if (preg_match('/^Smarty_/', $classname)) {
			$file = SMARTY_DIR . 'sysplugins/' . strtolower($classname) . '.php';
		}

		if (file_exists($file)) {
			require_once $file;
		} else {
			dd($file);
		}

		return;
	}

	function dd($var, $die = false) {
		echo '<pre>';
		var_dump($var);
		echo '</pre>';
		if ($die) {
			die();
		}
	}
?>
