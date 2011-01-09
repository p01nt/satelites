<?php
class Timer {
	protected $start = 0;
	protected $intervals = array(); 

	public function __construct() {
		$this->start = microtime(true);
		$this->first_start = $this->start;
	}

	public function interval() {
		$now = microtime(true);
		$backtrace = debug_backtrace();
		
		$interval = array();
		$interval['file'] = $backtrace[0]['file'];
		$interval['line'] = $backtrace[0]['line'];

		$interval['time'] = $this->__prepare($now - $this->start);

		$this->intervals[] = $interval;

		$this->start = $now;

		return true;
	}

	public function results() {
		return $this->intervals;
	}

	public function getTotal() {
		$now = microtime(true);
		
		return $this->__prepare($now - $this->first_start);
	}

	protected function __prepare($time) {
		$time = sprintf('%0.4f', $time);
		return $time;
	}
}
?>
