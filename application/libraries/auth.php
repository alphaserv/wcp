<?php

class Auth
{
	private $CI;
	
	const access_read = 1;
	const access_write = 2;
	const access_delete = 4;
	const access_create = 8;
	const access_manage = 16;
	
	function __construct()
	{
		$this->CI =& get_instance();

		$this->CI->load->model('user_m');
		$this->CI->load->model('userdata_m');
		$this->CI->load->library('user_lib');
	}
	
	function &get_user(int $id)
	{
		return $this->CI->user_lib->get_user($id);
	}
	
	function &get_current_user()
	{
		return $this->CI->user_lib->get_current_user();
	}
	
	function get_access(string $actionname, int $user_id = null)
	{
		if($user_id === null)
			$user_id =& User_lib::$currentuser->get_user_id();
		
		$access = (int)$this->CI->user_m->get_access_to($actionname, $user_id);
		
		#if($access #TODO:finish
	}

	function login($username, $pass)
	{
		if ($this->checkbanned())
			throw new excpetion('cannot login, you are banned');
			
		$return = $this->CI->user_m->try_login($username, $pass);
		if(!$return[0])
			return false;
		
		$this->CI->user_lib->change_user(new user_($return[1]));
		return true;
	}
	function logout()
	{
		$user =& $this->get_current_user();
		if($user->user_id == -1)
			throw new exception('already logged out'); #TODO: make user error
		$user->destroy();
		unset($user); #destroy
		$user = new user_(-1);
	}
	function register ($email, $pass, $as_pass, $username)
	{
		$priv = (int)$this->CI->config->item('default_priv');
		$activation = (int)$this->CI->config->item('activation_type');
		$res = $this->CI->user_m->register ($email, $pass, $as_pass, $username, $activation, $priv);
		
		#TODO: send email?
		return true;
	}
	function activate ($key)
	{
		$a = $this->CI->user_m->activate($key);
		if ($a === true && $this->CI->user_m->check_activations(5))
			return true;
	}

	function checkbanned()
	{
		/*
			description: function to check if an user is ipbanned
			return boolean:
				true when banned
				false when not banned
			arguments:
				none
		*/
		#TODO: add check query ere
		return false;
	}
	function cron()#cronjob
	{
		$this->CI->user_m->check_activations(50);
	}
}
