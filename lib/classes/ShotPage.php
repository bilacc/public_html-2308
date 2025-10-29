<?php
class ShotPage
{
	private $lng_add, $page_to_shoot;
	private $routes = Array();
	private $segments = Array();
	public $data;
	
	public function __construct()
	{
		$this->lng_add = get_conf('multi_language');
		$this->extract_uri();
		$this->language_redirect();
		$this->load_lang();
		$this->page_to_shoot();
		$this->shot_page();
	}
	
	protected function extract_uri()
	{
		
		$uri = ( isset($_GET['url']) ) ? explode("/", $_GET['url']) : Array() ;
		
		if($uri[0] == 'oglas'){
			$current_url = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			
			$is_link = Db::query('SELECT * FROM items WHERE redirect_url = "'.$current_url.'"');
			if($is_link){
				$_SESSION['oglas_id'] = $is_link['id'];
			}else{
				$_SESSION['oglas_id'] = 0;
			}
			// unset($_SESSION['link']);
			$_SESSION['link'] = $current_url;

		}else{
			// unset($_SESSION['link']);
		}

			if( count($uri) > 0 )
			{
				$i = 0;
				foreach($uri as $k => $v)
				{
					if( $this->lng_add )
					{
						if( $i == 0 )
						{
							define('_LNG', $v);
							$_SESSION['lng'] = _LNG;
						}
						else
						{
							if( $v != '' )
							{
								define('_su'.$i, $v);
								$this->segments['_su'.$i] = $v;
							}
						}
					}
					else
					{
						if( $v != '' )
						{
							define('_su'.($i+1), $v);
							$this->segments['_su'.($i+1)] = $v;
						}
					}
					
					$i++;
				}
			}
		
	}
	
	protected function page_to_shoot()
	{
		


		if( ! defined('_su1') )
		{
			// var_dump(_su1);exit;
			if( $this->lng_add && is_file( 'pages/home_'._LNG.'.php' ) )
				$this->page_to_shoot = 'home_'._LNG.'.php';
			else
				$this->page_to_shoot = 'home.php';
		}
		else
		{
			if( is_file( 'pages/'._su1.'.php' ) && ! is_file( 'pages/'._su2.'.php' ) )
				$this->page_to_shoot = _su1.'.php';
			else if( defined('_su2') && is_file('pages/'._su2.'.php') )
				$this->page_to_shoot = _su2.'.php';
			else if( defined('_su2') && defined('_su3') && is_file('pages/'._su3.'.php') )
				$this->page_to_shoot = _su3.'.php';
			
			// ako je site višejezični onda traži fajlove na osnovu definiranih konstanti za urlove
			else if( $this->lng_add && array_key_exists(_su1, $this->routes) && is_file( 'pages/'.$this->routes[_su1].'.php' ) )
				$this->page_to_shoot = $this->routes[_su1].'.php';
			else if( $this->lng_add && array_key_exists(_su1, $this->routes) && is_file( 'pages/'.$this->routes[_su1].'_'._LNG.'.php' ) )
				$this->page_to_shoot = $this->routes[_su1].'_'._LNG.'.php';
			else if( $this->lng_add && array_key_exists(_su2, $this->routes) && is_file( 'pages/'.$this->routes[_su2].'_'._LNG.'.php' ) )
				$this->page_to_shoot = $this->routes[_su2].'_'._LNG.'.php';
			
			else if( $shot = $this->get_conf_routes() )
				$this->page_to_shoot = $shot.'.php';
			else
				$this->page_to_shoot = '404.php';
		}
	}
	
	protected function get_conf_routes()
	{
		$routes = get_conf('routes');
		
		if( count($routes) > 0 )
		{
			$r = array();
			foreach($routes as $k => $v)
			{
				$r[$k.':'.$v] = substr_count($k, '/');
			}
			
			arsort($r, SORT_NUMERIC);
			
			foreach($r as $k1 => $v1)
			{
				list($k, $v) = explode(':', $k1);
				
				if( strpos($k, '/') !== false && isset($this->segments['_su2']) )
				{
					$s = explode('/', $k);
					$c = count($s)-1;
					
					$shot = true;
					for($i=0; $i<=$c; $i++)
					{
						if( ! isset($this->segments['_su'.($i+1)]) || $this->segments['_su'.($i+1)] != $s[$i] )
							$shot = false;
					}
					
					if( $shot && is_file('pages/'.$v.'.php') )
						return $v;
				}
				else
				{
					if( $k == _su1 )
						return $v;
				}
			}
			
			return false;
		}
		else
		{
			return false;
		}
	}
	
	protected function language_redirect()
	{
		if( $this->lng_add )
		{
			$languages = get_conf('languages');
			if( ( ! defined('_LNG') || ! in_array(_LNG, $languages) ) && is_array($languages) )
			{
				if( isset($_COOKIE['site_lng']) && in_array($_COOKIE['site_lng'], $languages) )
				{
					header('Location: '._SITE_URL.$_COOKIE['site_lng'].'/');
				}
				else
				{
					setcookie('site_lng', $languages[0], time()+(60*60*24*365), '/');
					header('Location: '._SITE_URL.$languages[0].'/');
				}
			}
			else
			{
				if( ! isset($_COOKIE['site_lng']) || _LNG != $_COOKIE['site_lng'] )
				{
					setcookie('site_lng', _LNG, time()+(60*60*24*365), '/');
				}
			}
		}
	}
	
	protected function load_lang()
	{
		if( $this->lng_add && defined('_LNG') )
		{
			if( is_file('lib/lang_'._LNG.'.txt') )
				$handle = @fopen('lib/lang_'._LNG.'.txt', "r");
			
			if( $handle )
			{
				while ( $buffer = fgets($handle) )
				{
					if( $buffer != '' && substr($buffer,0,1) == '_' )
					{
						$pos = strpos($buffer, '=');
						
						if( $pos !== false )
						{
							$const = trim(substr($buffer,0,$pos));
							$val = trim(substr($buffer,($pos+1)));
							
							if( substr($const, 0, 5) === '_URL_' )
							{
								$this->routes[$val] = mb_strtolower(substr($const,5));
							}
							
							define($const, $val);
						}
					}
				}
				
				fclose ($handle);
			}
		}
	}
	
	protected function shot_page()
	{
		global $user;
		global $shop;
		
		if( ! isset($_COOKIE[_STORE_COOKIE_NAME]) )
		{
			setcookie(_STORE_COOKIE_NAME, md5(session_id()._STORE_SALT), time()+60*60*24*30, '/');
		}
		
		ob_start();
		
		include ('pages/'.$this->page_to_shoot);
		$bullet['title'] = ( isset($title) ) ? $title : '' ;
		$bullet['keywords'] = ( isset($keywords) ) ? $keywords : $bullet['title'];
		$bullet['description'] = ( isset($description) ) ? $description : $bullet['title'];
		
		$bullet['fb_title'] = ( isset($fb_title) ) ? $fb_title : false ;
		$bullet['fb_img'] = ( isset($fb_img) ) ? $fb_img : false ;

		$bullet['bg'] = ( isset($bg) ) ? $bg : false ;
		
		$bullet['header'] = ( isset($header) ) ? $header : 'header' ;
		$bullet['footer'] = ( isset($footer) ) ? $footer : 'footer' ;
		
		$bullet['content'] = ob_get_contents();
		
		ob_end_clean();
		
		$this->data = $bullet;
	}
}