<?php
class Admin_User
{
	public function login_user($data)
	{
		$correct = Db::query_row('SELECT * FROM admin_users WHERE username = "'.Db::clean($data['user_name']).'" AND password = "'.Db::clean($data['password']).'"');
		
		//var_dump($correct);
		
		$_SESSION['admin'] = null;
		
		if($correct)
		{
			$_SESSION['admin']['username'] = $correct['username'];
			$_SESSION['admin']['id'] = $correct['id'];
			$_SESSION['admin']['user_type'] = $correct['user_type'];
			$_SESSION['admin']['login_hash'] = $this->calc_hash($correct['username'], _SITE_SALT, $correct['id'], $correct['user_type']);
			$_SESSION['admin']['level_id'] = $correct['authorization_levels_id'];
			
			$_SESSION['KCFINDER'] = array();
			$_SESSIN['KCFINDER']['disabled'] = false;

			
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function is_logged()
	{
		$logged = false;
		
		if( is_array($_SESSION['admin']) && isset($_SESSION['admin']) && $this->calc_hash($_SESSION['admin']['username'], _SITE_SALT, $_SESSION['admin']['id'], $_SESSION['admin']['user_type']) == $_SESSION['admin']['login_hash'])
		{
			$logged = $_SESSION['admin']['user_type'];
		}
		
		return $logged;
	}
	
	public function logout()
	{
		$_SESSION['admin'] = null;
		$_SESSION['KCFINDER'] = null;
	}
	
	private function calc_hash($username, $salt, $id, $type)
	{
		return md5($username.$salt.$id.$type);
	}
}