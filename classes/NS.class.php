<?php
class NS {
	protected $name = '';
	protected $my_ip = '77.120.103.96';
	protected $ip = '0.0.0.0';
	protected $data = array();
	protected $data_ip = '';
	protected static $nservers = array();
	
	public function __construct($name) {
		$this->name = $name;
		$this->setServers();
	}

	public function setServers() {
		self::$nservers = array('ns1.klets.name', 'ns2.klets.name', 'ns1.artlab-idiot.com', 'ns2.artlab-idiot.com');
	}
	
	public function getServers() {
		return self::$nservers;
	}

	protected function __updateIP() {
		$memcache = Memcache::getInstance();

		$var = 'ip_' . $this->name;
		if ($memcache->exists($var)) {
		    $this->data_ip = $memcache->get($var);
			return $this->data_ip;
		}

		$this->__renewIP();
	}

	protected function __renewIP() {
		$cmd = 'nslookup ' . $this->name;

		$this->data_ip = array();
		exec($cmd, $this->data_ip);

		$memcache = Memcache::getInstance();
		$var = 'ip_' . $this->name;
		$memcache->set($var, $this->data_ip);

		return $this->data_ip;
	}

	public function getIP() {
		$this->__updateIP();

		if (preg_match('/^Address: (\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})$/', $this->data_ip[5], $matches)) {
			$this->ip = $matches[1];
		}

		return $this->ip;
	}

	public function isMyIP() {
		return $this->isIP($this->my_ip);
	}

	public function isIP($ip) {
		$this->getIP();

		if ($this->ip == $ip) {
			return true;
		}

		return false;
	}

	public function isMyIPOnNeedNS() {
		return count($this->getBadNeedNS($this->my_ip)) == 0;
	}

	public function printMyBadNeedNS() {
		return $this->printBadNeedNS($this->my_ip);
	}

	public function getBadNeedNS($ip) {
		$servers = array();
		foreach (self::$nservers as $server) {
			$this->__updateNSData($server);
			$data = $this->data[$server];

			if (preg_match('/(REFUSED|SERVFAIL|NXDOMAIN)$/', $data[3])) {
				$servers[] = $server;
			}

			if (!preg_match('/^Address: ' . $ip . '$/', $data[4])) {
				$servers[] = $server;
			}
		}

		return $servers;
	}

	protected function __updateNSData($server = null) {
		$memcache = Memcache::getInstance();

		$var = 'ns_' . $this->name . '_' . $server;
		if ($memcache->exists($var)) {
		    $this->data[$server] = $memcache->get($var);
			return $this->data[$server];
		}

		$this->__renewNSData($server);
	}

	protected function __renewNSData($server = null) {
		if (isset($server)) {
			$cmd = 'nslookup ' . $this->name . ' ' . $server;
			$data = array();
			exec($cmd, $data);
			$this->data[$server] = $data;

			$memcache = Memcache::getInstance();
			$var = 'ns_' . $this->name . '_' . $server;
			$memcache->set($var, $this->data[$server]);
		} else {
			exec('nslookup ' . $this->name, $data);
		}

		return $data;

	}

	public function renewNeedNS () {
		foreach (self::$nservers as $server) {
			$this->__renewNSData($server);
		}
	}

	public function printBadNeedNS($ip) {
		$servers = $this->getBadNeedNS($ip);

		if (empty($servers)) {
			return 'ok';
		}

		return implode(', ', $servers);
	}

	public function zone() {
		$content = file_get_contents(BASE . '/samples/named.row');
		$content = str_replace('%%satelite%%', $this->name, $content);
		return $content;
	}

	public function zoneFile() {
		$content = file_get_contents(BASE . '/samples/named.file');
		$content = str_replace('%%satelite%%', $this->name, $content);
		return $content;
	}
}
?>
