<?php
class Satelite {
	protected $name = '';
	protected $whois;
	protected $ns;
	protected $blog;

	public function __construct($name) {
		$this->name = $name;

		$this->whois = new Whois($this->name);
		$this->ns = new NS($this->name);
		$this->blog = $this->__getSubInstance('Blog');
	}

	private function __getSubInstance($class) {
		$memcache = Memcache::getInstance();
		$var = 'class_' . $class . '_' . $this->name;
		if ($memcache->exists($var)) {
			return $memcache->get($var);
		}
		
		$result = new $class($this->name);
		$memcache->set($var, $result);

		return $result;
	}

	public function getName() {
		return $this->name;
	}

	public function whois() {
		return $this->whois;
	}

	public function ns() {
		return $this->ns;
	}

	public function blog() {
		return $this->blog;
	}

	public function isMy() {
		return !$this->whois()->isWrong() && $this->whois()->isRegistred() && $this->whois()->isMy();
	}

	public function isNotMy() {
		return !$this->whois()->isWrong() && $this->whois()->isRegistred() && !$this->whois()->isMy();
	}
	
	public function isFree() {
		return !$this->whois()->isWrong() && !$this->whois()->isRegistred();
	}

}
?>
