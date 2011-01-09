<?php
class DB {
	protected $query = array();
	protected $count = false;
	protected $fields = array();
	protected $set = array();
	protected $where = array();
	protected $limit = 0;
	protected $offset = 0;
	static protected $link = null;
	static protected $master = array('host' => null, 'user' => null, 'password' => null);
	static protected $database;

	protected function __construct() {
		$this->__connect();
	}

	static public function getInstance() {
		return new self();
	}

	protected function __connect() {
		if (!empty(self::$link)) {
			return self::$link;
		}

		if (!empty(self::$database)) {
			self::$link = mysql_connect(self::$master['host'], self::$master['user'], self::$master['password']);
			$result = mysql_select_db(self::$database, self::$link);
			if (!$result) {
				// @TODO exeption
			}

			return self::$link;
		}

		return;
	}

	public function set_master_host($host, $user, $password) {
		self::$master['host'] = $host;
		self::$master['user'] = $user;
		self::$master['password'] = $password;
	}

	public function set_database($database) {
		self::$database = $database;
	}

	public function query($query = '') {
		if (empty($query)) {
			$query = implode(' ', $this->query);
		}

		$result = mysql_query($query, self::$link);
		
		if (!$result)
		{
			//@TODO nice exeption 
			dd(mysql_error());
		}

		return $result;
	}

	public function select() {
		$this->query[] = 'select';

		return $this;
	}

	public function update($table) {
		$this->query[] = 'update';
		$this->table = $table;

		return 	$this;
	}

	public function count() {
		$this->count = true;

		return $this;
	}

	public function from($table) {
		$this->table = $table;

		return 	$this;
	}

	public function set($field, $value) {
		$this->set[] = $field . ' = ' . $this->__escape($value);

		return $this;
	}

	public function limit($offset, $limit = false) {
		if (empty($limit)) {
			$limit = $offset;
			$offset = 0;
		}

		$this->limit = $limit;
		$this->offset = $offset;

		return $this;
	}

	public function databases() {
		$this->query[] = 'show databases';

		return $this;
	}

	public function where($field, $expression, $value) {
		$this->where[] = $field . ' ' . $expression . ' ' . $this->__escape($value);
		
		return $this;
	}

	public function fetch() {
		if ($this->query[0] == 'select') {
			if ($this->count) {
				$this->query[] = 'count(*) as `amount`';
			} else {
				if (empty($this->fields)) {
		 			$this->query[] = '*';
				}
			}

			$this->query[] = 'from';
		}

		if ($this->query[0] == 'select' || $this->query[0] == 'update') {
			$this->query[] = $this->table;
		}

		if ($this->query[0] == 'update') {
			$this->query[] = 'set';
			$this->query[] = implode(', ', $this->set);
		}

		if ($this->query[0] == 'select' || $this->query[0] == 'update') {
			if (!empty($this->where)) {
				$this->query[] = 'where';
				$this->query[] = implode(' and ', $this->where);
			}

			if (!empty($this->limit)) {
				$this->query[] = 'limit ' . $this->offset . ', ' . $this->limit;
			}
		}

		$result = $this->query();

		if ($this->query[0] == 'update') {
			return true;
		}

		$rows = array();
		while ($row = mysql_fetch_object($result)) {
			$rows[] = $row;
		}

		if ($this->count) { 
			return array_shift($rows)->amount;
		}

		return $rows;
	}

	public function fetchOne() {
		return array_shift($this->fetch());
	}

	protected function __escape($value) {
		$escaped_value = $value;
		if (is_string($escaped_value)) {
			$escaped_value = '\'' . $escaped_value . '\'';
		}

		return $escaped_value;
	}
}
?>
