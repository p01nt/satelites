<?php
class NS {
	protected $name = '';
	protected $ip = '';
	protected $data = array();
	protected static $nservers = array();
	
	public function __construct($name) {
		$this->name = $name;
		$this->setServers();
	}

	public function setServers() {
		self::$nservers = array('ns1.klets.name', 'ns2.klets.name', 'ns1.artlab-idiot.com', 'ns2.artlab-idiot.com');
	}

	public function getIP() {
		// exec('nslookup ' . $this->name, $this->data);
		return false;
	}

	public function printIP() {
		return $this->getIP();
	}

	public function isMyIP() {
		return $this->isIP('77.120.103.11');
	}

	public function isIP($ip) {
		if ($this->ip == $ip) {
			return true;
		}

		return false;
	}

	public function isMyIPOnNeedNS() {
		return count($this->getBadNeedNS()) == 0;
	}

	public function getBadNeedNS() {
		$servers = array();
		foreach (self::$nservers as $server) {
			$this->__updateNSData($server);
			$data = $this->data[$server];

			if (preg_match('/REFUSED$/', $data[3])) {
				$servers[] = $server;
			}
		}

		return $servers;
	}

	public function __updateNSData($server = null) {
		$memcache = Memcache::getInstance();

		$var = 'ns_' . $this->name . '_' . $server;
		if ($memcache->exists($var)) {
		    $this->data[$server] = $memcache->get($var);
			return $this->data[$server];
		}

		$this->__renewNSData($server);
	}

	public function __renewNSData($server = null) {
		if (isset($server)) {
			$cmd = 'nslookup ' . $this->name . ' ' . $server;
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

	public function printBadNeedNS() {
		$servers = $this->getBadNeedNS();

		if (empty($servers)) {
			return 'ok';
		}

		return implode(', ', $servers);
	}
}
?>
