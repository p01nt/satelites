<?php
class SatelitesCollection implements Iterator {
	private $position = 0;
	private $array = array();
	
	public function __construct() {
		$db = DB::get();
		$rows = $db->select()->from('satelites')->fetch();
		foreach ($rows as $row) {
			$this->array[] = new Satelite($row->id);
		}

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
		return isset($this->array[$this->position]);
	}
	
	public function rewind() {
		$this->position = 0;
	}
}
?>
