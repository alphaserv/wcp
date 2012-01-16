<?php

class Auth
{
	private $CI;
	
	static const access_read = 1;
	static const access_write = 2;
	static const access_delete = 4;
	static const access_create = 8;
	static const access_manage = 16;
	
	function __construct()
	{
		$this->CI->load->model('user_m');
		$this->CI->load->model('userdata_m');
		$this->CI->load->library('user_lib');
		$this->CI =& get_instance();
	}
	
	function &get_user(int $id)
	{
		return $this->CI->user_lib->get_user($id);
	}
	
	function &get_current_user()
	{
		return $this->CI->user_lib->get_current_user($id);
	}
	
	function get_access(string $actionname, int $user_id = null)
	{
		if($user_id === null)
			$user_id =& User_lib::$currentuser->get_user_id();
		
		$access = (int)$this->CI->user_m->get_access_to($actionname, $user_id);
		
		if($access
	}

			}
				
		}
	}
	function user()
	{
		return $this->CI->user->getuser();
	}
	function login($username, $pass, $remember = false)
	{
		if ($this->checkbanned())
			#fake errors if banned
			return array('code' => -100, 'error' => 'username or password incorrect');
			
		$res = $this->CI->as_user->login($username, $pass);
		if ($res == -1 || $res == -2)
			if ($res == -1)
				return array('code' => $res, 'error' => 'sql failure');
			else
				return array('code' => $res, 'error' => 'username or password incorrect');
		else
		{
			$user =& $this->CI->user->getuser();
			$user->id = $res['id'];
			$user->email = $res['email'];
			$user->pass = $res['pass'];
			$user->privilege = $res['priv'];
			$user->userdata = $this->CI->as_user->fetch_userdata($res['id']);
			if($remember)
				return $this->CI->as_user->remember_user($row['id']);
			else
				return true;
		}
	}
	function logout()
	{
		/*
			Description:
				Destroy the session of the user
			Return (bool):
				Successfully logged out
				-true yes
				-false no (not logged in)
			Arguments:
				none
		*/
		$user =& $this->CI->user->getuser();
		if ($user->privilege <= 1)
			#already logged out
			return false;
		else
		{
			$user->destroy_user();
			return true;
		}
	}
	function register ($email, $pass, $as_pass, $username)
	{
		$priv = (int)$this->CI->config->item('default_priv');
		$activation = (int)$this->CI->config->item('activation_type');
		$res = $this->CI->as_user->register ($email, $pass, $as_pass, $username, $activation, $priv);
		if(!is_array($res))
		{
			if ($res == -4)
				return array('code' => $res, 'error' => 'sql failure');
			elseif ($res == -1)
				return array('code' => $res, 'error' => 'passwords are the same.');
			elseif ($res == -2)
				return array('code' => $res, 'error' => 'that name or email address is already in use.');
			elseif ($res == -3)
				return array('code' => $res, 'error' => 'that name is registered but just not activated.');
		}
		else
			return true;
	}
	function activate ($key)
	{
		$a = $this->CI->as_user->activate($key);
		if ($a === true && $this->CI->as_user->check_activations())
			return true;
		elseif($a == -1)
			return array('code' => 1, 'error' => 'activation key not found');
		else
			return array('code' => 0, 'error' => 'sql failure');
	}
	function restrict($priv = 0, $group = false, $reverse = false)
	{
		/*
			Description:
				Restrict access
			Return (bool):
				Does it have the correct power
				-true yes
				-false no
			Arguments:
				- $priv the minimum privelege an user should have. NOTE: the privilege for logged in users is 50 the one for non logged in people is 1
				- $group the group an user should be in. NOTE: not yet implementend
		*/
		
		#retrieve an user from session
		$user =& $this->CI->user->getuser();
		if ($user->privilege >= $priv)
			return !$reverse;
		else
			return $reverse;
	}
	function restrictpage ($restrict = false, $action = 'you don\'t have the privilege to see the contents of this page')
	{
		if(!$restrict)
			return;
		
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
		$this->CI->as_user->check_activations();
	}
}
