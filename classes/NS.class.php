<?php
class NS {
	protected $name = '';
	protected $data = '';
	protected $ip = '';
	
	public function __construct($name) {
		$this->name = $name;
	}

	public function resolve() {
		return false;
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
}
?>
