<?php
class SatelitesCollection implements Iterator {
	protected $position = 0;
	protected $array = array();
	protected $allow_wrong_whois = false;

	protected $only = array();
	
	public function __construct() {
		$db = DB::getInstance();
		$rows = $db->select()->from('satelites')->fetch();

		foreach ($rows as $row) {
			$this->array[] = new Satelite($row->id);
		}
	}

	public function onlyOthers() {
		$this->only = array();
		$this->only['others'] = true;
		return $this;
	}

	public function onlyMy() {
		$this->only = array();
		$this->only['my'] = true;
		return $this;
	}
	
	public function onlyFree() {
		$this->only = array();
		$this->only['free'] = true;
		return $this;
	}

	public function allowWrongWhois() {
		$this->allow_wrong_whois = true;
		return $this;
	}

	protected function __disableWrongWhois() {
		$this->allow_wrong_whois = false;
		return $this;
	}

	public function countOthers() {
		$amount = 0;
		foreach ($this->array as $item) {
			if ($item->isNotMy()) {
				$amount ++;
			}
		}

		return $amount;
	}
	
	public function countMy() {
		$amount = 0;
		foreach ($this->array as $item) {
			if ($item->isMy()) {
				$amount ++;
			}
		}

		return $amount;
	}

	public function countFree() {
		$amount = 0;
		foreach ($this->array as $item) {
			if ($item->isFree()) {
				$amount ++;
			}
		}

		return $amount;
	}

	public function countWrongIP() {
		$amount = 0;
		foreach ($this->array as $item) {
			if ($item->isMy() && !$item->ns()->isMyIP()) {
				$amount ++;
			}
		}

		return $amount;
	}
	
	public function countWrongNS() {
		$amount = 0;
		foreach ($this->array as $item) {
			if ($item->isMy() && !$item->whois()->isMyNS()) {
				$amount ++;
			}
		}

		return $amount;
	}

	public function countWrongNeedNS() {
		$amount = 0;
		foreach ($this->array as $item) {
			if ($item->isMy() && !$item->ns()->isMyIPOnNeedNS()) {
				$amount ++;
			}
		}

		return $amount;
	}

	public function countWrongWhois() {
		$amount = 0;
		foreach ($this->array as $item) {
			if ($item->whois()->isWrong()) {
				$amount ++;
			}
		}

		return $amount;
	}

	public function countTotal() {
		return count($this->array);
	}

	public function current() {
		return $this->array[$this->position];
	}
	
	public function next() {
		++ $this->position;
	}
	
	public function key() {
		return $this->position;
	}
	
	public function valid() {
		if (!isset($this->array[$this->position])) {
			return false;
		}

		$item = $this->array[$this->position];

		if (($item->whois()->isWrong() && !$this->allow_wrong_whois) || (isset($this->only['others']) && ($item->isFree() || $item->isMy())) || (isset($this->only['my']) && ($item->isFree() || !$item->isMy())) || (isset($this->only['free']) && !$item->isFree())) {
			$this->next();
			return $this->valid();
		}

		return isset($this->array[$this->position]);
	}
	
	public function rewind() {
		$this->position = 0;
	}
}
?>
