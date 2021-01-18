<?php
class App {

	protected $cfg  = [];
	protected $app  = [];
	protected $func = [];

	public function __construct() {
		$this->app = (object)[];
	}

	public function config() {
		$args = func_get_args();
		$name = strtolower(array_shift($args));
		$this->cfg[$name] = $args;
		return $this;
	}

	public function route($uri, $callback) {
		$uri = str_replace('/', '\/', $uri);
		$this->func[$uri] = $callback;
		return $this;
	}

	protected function exeConfig() {
		// ModRewrite
		$this->cfg['mod_rewrite'] = array_key_exists('mod_rewrite', $this->cfg)
			? $this->cfg['mod_rewrite'] = $this->cfg['mod_rewrite'][0]
			: $this->cfg['mod_rewrite'] = false;
		
		// Url
		$this->app->url = array_key_exists('HTTP_X_FORWARDED_PROTO', $_SERVER)
			? $_SERVER['HTTP_X_FORWARDED_PROTO'] // CloudFlare || Proxy
			: $_SERVER['REQUEST_SCHEME'];
		$this->app->url.= '://'.preg_replace(
			'/\/{2,}/',
			'/',
			$_SERVER['SERVER_NAME'].str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])).'/'
		);

		// DataBase MySQLi
		if(array_key_exists('mysqli', $this->cfg)) {
			$this->app->db = new mysqli(
				$this->cfg['mysqli'][0],
				$this->cfg['mysqli'][1],
				$this->cfg['mysqli'][2],
				$this->cfg['mysqli'][3]
			);
			if($this->app->db->connect_errno) {
				die('<b>Error MySQLi:</b> '.$this->app->db->connect_error);
			}
			if( ! $this->app->db->set_charset('utf8')) {
				die('<b>Error MySQLi:</b> '.$this->app->db->error);
			}
		}

		// DataBase SQLite
		if(array_key_exists('sqlite', $this->cfg)) {
			$this->app->db = new SQLite3($this->cfg['sqlite'][0]);
			$this->app->db->busyTimeout(500);
		}
	}

	public function __destruct() {
		$this->exeConfig();
		$uri = $this->cfg['mod_rewrite']
			? substr($_SERVER['REQUEST_URI'], strlen($_SERVER['SCRIPT_NAME'])-9)
			: substr($_SERVER['REQUEST_URI'], strlen($_SERVER['SCRIPT_NAME'])+1);
		foreach($this->func as $re => $callback) {
			if(preg_match("/^{$re}(?:\/)?(?:\?.*)?$/i", $uri, $args)) {
				array_shift($args);
				return call_user_func_array($callback, array_merge([(object) $this->app], array_values($args)));
			}
		}
		http_response_code(404);
		if(array_key_exists('404', $this->cfg)) {
			if(is_file($this->cfg['404'][0])) {
				require $this->cfg['404'][0];
			}
		}
	}

}
