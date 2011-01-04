<?php
class DB {
	private $query = array();
	private $fields = array();
	private $link = null;
	static private $master = array('host' => null, 'user' => null, 'password' => null);
	static private $database;

	private function __construct() {
		$this->__connect();
	}

	static public function getInstance() {
		return new self();
	}

	private function __connect() {
		if (!empty(self::$database)) {
			$this->link = mysql_connect(self::$master['host'], self::$master['user'], self::$master['password']);
			$result = mysql_select_db(self::$database, $this->link);
			if (!$result) {
				// @TODO exeption
			}
		}
	}

	public function set_master_host($host, $user, $password) {
		self::$master['host'] = $host;
		self::$master['user'] = $user;
		self::$master['password'] = $password;
	}

	public function set_database($database) {
		self::$database = $database;
	}

	public function query() {
		$query = implode(' ', $this->query);
		$result = mysql_query($query, $this->link);
		
		if (!$result)
		{
			//@TODO nice exeption 
			dd(mysql_error());
		}

		return $result;
	}

	public function select()
	{
		$this->query[] = 'select';
		return $this;
	}

	public function from($table) {
		$this->table = $table;
		return 	$this;
	}

	public function fetch() {
		if (empty($this->fields)) {
			$this->query[] = '*';
		}

		$this->query[] = 'from `' . $this->table . '`';

		$result = $this->query();

		$rows = array();
		while ($row = mysql_fetch_object($result)) {
			$rows[] = $row;
		}

		return $rows;
	}
}
?>
