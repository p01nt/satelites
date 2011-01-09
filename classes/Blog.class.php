<?php
class Blog {
	protected $name = '';
	protected $min_posts = 100;
	protected $posts_per_page = 3;

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

		$db = DB::getInstance();
		$rows = $db->databases()->fetch();

		$result = array();
		foreach ($rows as $row) {
			$name = $row->Database;
			$result[$name] = $name;
		}

		$memcache->set($var, $result);

		return $result;
	}

	public function issetDatabase(){
		$database = $this->getDBName();

		$rows = $this->__getDBList();
		if (isset($rows[$database])) {
			return true;
		}

		return false;
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
		return $this->posts_per_page;
	}

	public function correctPostsPerPage() {
		return $this->getNeedPostsPerPage() == $this->getPostsPerPage();
	}

	public function getURL() {
		if (!$this->issetDatabase()) {
			return false;
		}

		$db = DB::getInstance();
		$row = $db->select()->from($this->getDBName() . '.`wp_options`')->where('option_name', '=', 'siteurl')->fetchOne();
		return $row->option_value;
	}

	public function getHomeURL() {
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

		return true;
	}

	public function updateHomeURL() {
		$db = DB::getInstance();
		$row = $db->update($this->getDBName() . '.`wp_options`')->set('option_value', $this->getNeedURL())->where('option_name', '=', 'home')->fetch();

		return true;
	}

	public function getTitle() {
		$db = DB::getInstance();
		$row = $db->select()->from($this->getDBName() . '.`wp_options`')->where('option_name', '=', 'blogname')->fetchOne();
		return $row->option_value;
	}

	public function getDescription() {
		$db = DB::getInstance();
		$row = $db->select()->from($this->getDBName() . '.`wp_options`')->where('option_name', '=', 'blogdescription')->fetchOne();
		return $row->option_value;
	}

	public function getPostsPerPage() {
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

		return true;
	}

	public function generatePosts($limit = 100) {
		$db = DB::getInstance();
		$result = $db->query('insert into `' . $this->getDBName() . '`.`wp_posts` (`post_title`, `post_content`) select `title`, `body` from `satelites_data`.`posts` order by rand() limit ' . $limit);

		return true;
	}
}
?>
