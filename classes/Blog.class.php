<?php
class Blog {
	protected $name = '';
	protected $min_posts = 100;
	protected $need_posts_per_page = 3;

	protected $title = null;
	protected $description = null;
	protected $url = null;
	protected $home_url = null;
	protected $posts_per_page = null;
	protected $posts_amount = null;
	protected $isset_db = null;

	public function __construct($name) {
		$this->name = $name;
	}

	protected function __getDBList() {
		$memcache = Memcache::getInstance();
		$var = 'databases_list';

		if ($memcache->exists($var)) {
			$result = $memcache->get($var);
			
			return $result;
		}

		return $this->__renewDBList();
	}

	protected function __renewDBList() {
		$db = DB::getInstance();
		$rows = $db->databases()->fetch();

		$result = array();
		foreach ($rows as $row) {
			$name = $row->Database;
			$result[$name] = $name;
		}

		$memcache = Memcache::getInstance();
		$var = 'databases_list';
		$memcache->set($var, $result);

		return $result;
	}

	public function issetDatabase(){
		if (isset($this->isset_db)) {
			return $this->isset_db;
		}

		$database = $this->getDBName();
		$rows = $this->__getDBList();
		if (isset($rows[$database])) {
			$this->isset_db = true;
			$this->__storeObject();

			return true;
		}

		$this->isset_db = false;
		$this->__storeObject();

		return $this->isset_db;
	}

	public function updateDatabase() {
		$this->isset_db = null;
		$this->__renewDBList();
		$this->issetDatabase();

		return true;
	}

	public function getDBName() {
		$name = $this->name;
		
		$name = str_replace('.', '_', $name);
		$name = str_replace('-', '_', $name);
		$name = 'satelites_' . $name;

		return $name;
	}

	public function getDefaultDBName() {
		return 'satelites_default';
	}

	public function createDatabase() {
		$cmd = 'mysql -e \'create database ' . $this->getDBName() . '\'';
		exec($cmd);

		$cmd = 'mysqldump ' . $this->getDefaultDBName() . ' | mysql ' . $this->getDBName();
		exec($cmd);

		return true;
	}

	public function noEnoughPosts() {
		return $this->postsAmount() < $this->min_posts;
	}

	public function postsAmount() {
		if (empty($this->posts_amount)) {
			$this->posts_amount = $this->__renewPostsAmount();
			$this->__storeObject();
		}

		return $this->posts_amount;
	}

	private function __renewPostsAmount() {
		if (!$this->issetDatabase()) {
			return 0;
		}

		$db = DB::getInstance();
		$result = $db->select()->count()->from($this->getDBName() . '.`wp_posts`')->fetch();
		return $result;
	}

	public function wrongURL() {
		return !$this->correctURL();
	}

	public function wrongHomeURL() {
		return !$this->correctHomeURL();
	}

	public function wrongTitle() {
		return !$this->correctTitle();
	}

	public function wrongDescription() {
		return !$this->correctDescription();
	}

	public function wrongPostsPerPage() {
		return !$this->correctPostsPerPage();
	}

	public function correctURL() {
		return $this->getNeedURL() == $this->getURL();
	}

	public function correctHomeURL() {
		return $this->getNeedURL() == $this->getHomeURL();
	}

	public function correctTitle() {
		return false;
	}

	public function correctDescription() {
		return false;
	}

	public function getNeedURL() {
		$url = 'http://' . $this->name;
		return $url;
	}

	public function getNeedPostsPerPage() {
		return $this->need_posts_per_page;
	}

	public function correctPostsPerPage() {
		return $this->getNeedPostsPerPage() == $this->getPostsPerPage();
	}

	public function getURL() {
		if (!isset($this->url)) {
			$this->url = $this->__renewURL();
			$this->__storeObject();
		}

		return $this->url;
	}

	private function __renewURL() {
		if (!$this->issetDatabase()) {
			return false;
		}

		$db = DB::getInstance();
		$row = $db->select()->from($this->getDBName() . '.`wp_options`')->where('option_name', '=', 'siteurl')->fetchOne();
		return $row->option_value;
	}

	public function getHomeURL() {
		if (!isset($this->home_url)) {
			$this->home_url = $this->__renewHomeURL();
			$this->__storeObject();
		}

		return $this->home_url;
	}

	private function __renewHomeURL() {
		if (!$this->issetDatabase()) {
			return false;
		}

		$db = DB::getInstance();
		$row = $db->select()->from($this->getDBName() . '.`wp_options`')->where('option_name', '=', 'home')->fetchOne();
		return $row->option_value;
	}

	public function updateURL() {
		$db = DB::getInstance();
		$row = $db->update($this->getDBName() . '.`wp_options`')->set('option_value', $this->getNeedURL())->where('option_name', '=', 'siteurl')->fetch();

		$this->url = null;
		$this->getURL();

		return true;
	}

	public function updateHomeURL() {
		$db = DB::getInstance();
		$row = $db->update($this->getDBName() . '.`wp_options`')->set('option_value', $this->getNeedURL())->where('option_name', '=', 'home')->fetch();

		$this->home_url = null;
		$this->getHomeURL();

		return true;
	}

	public function getTitle() {
		if (empty($this->title)) {
			$this->title = $this->__renewTitle();
			$this->__storeObject();
		}

		return $this->title;
	}

	private function __renewTitle() {
		if (!$this->issetDatabase()) {
			return false;
		}

		$db = DB::getInstance();
		$row = $db->select()->from($this->getDBName() . '.`wp_options`')->where('option_name', '=', 'blogname')->fetchOne();
		return $row->option_value;
	}

	public function getDescription() {
		if (empty($this->description)) {
			$this->description = $this->__renewDescription();
			$this->__storeObject();
		}

		return $this->description;
	}

	private function __renewDescription() {
		if (!$this->issetDatabase()) {
			return false;
		}

		$db = DB::getInstance();
		$row = $db->select()->from($this->getDBName() . '.`wp_options`')->where('option_name', '=', 'blogdescription')->fetchOne();
		return $row->option_value;
	}

	public function getPostsPerPage() {
		if (empty($this->posts_per_page)) {
			$this->posts_per_page = $this->__renewPostsPerPage();
			$this->__storeObject();
		}

		return $this->posts_per_page;
	}

	private function __renewPostsPerPage() {
		if (!$this->issetDatabase()) {
			return false;
		}

		$db = DB::getInstance();
		$row = $db->select()->from($this->getDBName() . '.`wp_options`')->where('option_name', '=', 'posts_per_page')->fetchOne();
		return $row->option_value;
	}

	public function updatePostsPerPage() {
		$db = DB::getInstance();
		$row = $db->update($this->getDBName() . '.`wp_options`')->set('option_value', $this->getNeedPostsPerPage())->where('option_name', '=', 'posts_per_page')->fetch();

		$this->posts_per_page = null;
		$this->getPostsPerPage(); 

		return true;
	}

	public function generatePosts($limit = 100) {
		$db = DB::getInstance();
		$result = $db->query('insert into `' . $this->getDBName() . '`.`wp_posts` (`post_title`, `post_content`) select `title`, `body` from `satelites_data`.`posts` order by rand() limit ' . $limit);

		$this->posts_amount = null;
		$this->postsAmount();

		return true;
	}

	private function __storeObject() {
		$memcache = Memcache::getInstance();
		$var = 'class_' . __CLASS__ . '_' . $this->name;
		$memcache->set($var, $this);
	}
}
?>
