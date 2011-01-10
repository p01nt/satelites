<?php
class Whois {
	protected $name = '';
	protected $data = '';
	protected $nservers = array();

	public function __construct($name) {
		$this->name = $name;
		$this->__update();
	}

	private function __update() {
		$memcache = Memcache::getInstance();

		$var = 'whois_' . $this->name;
		if ($memcache->exists($var)) {
			$this->data = $memcache->get($var);
			return;
		}

		$this->__renew();
	}

	protected function __renew() {
		$memcache = Memcache::getInstance();

		$this->data = array();
		exec('whois ' . $this->name, $this->data);
		$var = 'whois_' . $this->name;
		$memcache->set($var, $this->data);
	}

	public function renew() {
		return $this->__renew();
	}

	public function isRegistred() {
		return $this->isRegister();
	}

	public function isRegister() {
		if (!isset($this->data[4])) {
			dd($this->data, 1);
		}

		if ($this->data[4] == '% No entries found for ' . $this->name) {
			return false;
		}

		return true;
	}

	public function isMy() {
		//@TODO dynamic username from db
		return $this->isOwner('PIKA4-UANIC');
	}

	public function isOwner($owner) {
		if (!$this->isRegister()) {
			return false;
		}

		if (!preg_match('/admin-c: +' . $owner. '/', $this->data[9])) {
			return false;
		}

		if (!preg_match('/tech-c: +' . $owner. '/', $this->data[10])) {
			return false;
		}

		return true;
	}

	public function getAdminC() {
		if (preg_match('/^admin-c: +(.*?)$/', $this->data[9], $matches)) {
			return $matches[1];
		}

		return false;
	}

	public function getTechC() {
		if (preg_match('/^tech-c: +(.*?)$/', $this->data[10], $matches)) {
			return $matches[1];
		}

		return false;
	}

	public function getOwner() {
		return false;
	}

	public function isNS($my) {
		$current = $this->getNS();
		return count($current) == count(array_intersect($current, $my));
	}

	public function printNS() {
		return implode(', ', $this->getNS());
	}

	public function getNS() {
		$this->nservers = array();
		foreach ($this->data as $line) {
			if (preg_match('/^nserver: +(.*?)$/', $line, $matches)) {
				$this->nservers[] =  $matches[1];
			}
		}

		return $this->nservers;
	}

	public function isMyNS() {
		$ns = new NS($this->name);
		$list = $ns->getServers();

		return $this->isNS($list);
	}

	public function isWrong() {
		if (!isset($this->data[4])) {
			return false;
		}

		if (preg_match('/^Requests limit exceeded. Please try again later.$/', $this->data[4])) {
			return true;
		}

		return false;
	}
}
?>
