<?php
	define('BASE', realpath(dirname(__FILE__) . '/..'));

	error_reporting(E_ALL);
	ini_set('display_errors', 'on');
	$timer = new Timer();

	$config = parse_ini_file(BASE . '/etc/config.ini', true);

	ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . BASE . '/libs/smarty');
	define('SMARTY_DIR', BASE . '/libs/smarty/');
	$smarty = new Smarty();
	$smarty->template_dir = BASE . '/templates/';
	$smarty->compile_dir = BASE . '/tmp/templates_c/';
	$smarty->cache_dir = BASE . '/tmp/cache/';

	$db = DB::getInstance();
	$db->set_master_host($config['master']['host'], $config['master']['user'], $config['master']['password']);
	$db->set_database($config['database']['name']);

	$memcache = Memcache::getInstance();
	foreach ($config['memcached'] as $name => $ip) {
		$memcache->addServer($ip);
	}

	function __autoload($classname) {
		if (defined('DISABLE_AUTOLOAD')) {
			return;
		}

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
		$cli = false;
		if (isset($_SERVER['SHELL'])) {
			$cli = true;
		}

		if (!$cli) {
			echo '<pre>';
		}

		var_dump($var);

		if (!$cli) {
			echo '</pre>';
		}

		if ($die) {
			die();
		}
	}
?>
