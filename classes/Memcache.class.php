<?php
class Memcache {
	static protected $memcached;

	static public function getInstance() {
		return new self();
	}

	protected function __construct() {
		if (!isset(self::$memcached)) {
			self::$memcached = new Memcached();
		}
	}

	public function addServer($host = '127.0.0.1', $port = '11211') {
		$result = self::$memcached->addServer($host, $port);
		return $result;
	}

	public function exists($var) {
		$value = self::$memcached->get($var);

		return !empty($value);
	}

	public function get($var) {
		$result = self::$memcached->get($var);

		return $result;
	}

	public function set($var, $value) {
		$value = self::$memcached->set($var, $value);
		return $value;
	}

	public function stats() {
		return self::$memcached->getStats();
	}

	public function __call($method, $args) {
		return call_user_func_array(array(self::$memcached, $method), $args);
	}
}
?>
