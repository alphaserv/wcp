<?php

class user_
{
	//private $CI;
	private $user_id;
	
	private $logged_in;
	
	private $username;
	private $email;
	
	private $cached_settings;
	private $cached_access;
	
	function __construct($id)
	{
	
		$this->user_id = $id;
		if($id == -1)
			$this->logged_in = false;
		else
		{
			$this->logged_in = true;
			$this->update_genic();
		}
	}
	
	function get_user_id()
	{
		return $this->user_id;
	}
	function is_logged_in()
	{
		return (bool)$this->logged_in;
	}
	
	function get_acces_to(string $name)
	{
		static $CI;
		if(!isset($CI)) $CI =& get_instance();
		#decbin($name)
		return $CI->user_m->user_access($this->user_id, $name);
	}
	
	function get_setting(string $name)
	{
		static $CI;

		if(isset($this->cached_settings[$name]))
			return $this->cached_settings[$name]; #do not encrypt cache (assuming we don't have a 16-core server)

		if(!isset($CI)) $CI =& get_instance();
		
		#cache
		$this->cached_settings[$name] = $this->CI->userdata_m->get($this->user_id, $name, true);
		return $this->cached_settings[$name];
	}
	
	function set_setting(string $name, $value)
	{
		static $CI;
		$this->cached_settings[$name] = $value;
	
		if(!isset($CI)) $CI =& get_instance();
		
		#save in db
		$this->CI->userdata_m->set($this->user_id, $name, $value);
	}
	
	function update_genic()
	{
		if(!$this->logged_in)
			throw new exception ('cannot update data of non-logged in user');
		
	}
	
	function destroy()
	{
		#logout
	}
	
	function save()
	{
		User_lib::$currentuser =& $this;
	}
}

class User_lib
{
	private $CI;
	
	#the user wich is visiting the page
	public static $currentuser;
	
	public static $users;
	
	function __construct(/*int*/ $id = -1)
	{
		$this->CI =& get_instance();
		$this->CI->load->library('session');
		$usr = unserialize($this->CI->session->userdata('User'));
		
		if(!$usr)
			static::$currentuser = new user_($id);
		else
		{
			static::$currentuser =& $usr;
			$id = static::$currentuser->get_user_id();
		}
		
		static::$users[$id] =& static::$currentuser;
		
	}
	
	function __destruct()
	{
		#save in cache
		$this->writedown();
	}
	
	function &get_user(int $id)
	{
		return static::$users[$id];
	}
	
	function &get_current_user()
	{
		return static::$currentuser;
	}

	function change_user(user_ &$new_user)
	{
		$id = $new_user->get_user_id();
		$this->CI->session->set_userdata('User', serialize(static::$currentuser));
		static::$currentuser =& $new_user;
		static::$users[$id] =& static::$currentuser;
	}
	
	function writedown()
	{
		$this->CI->session->set_userdata('User', serialize(static::$currentuser));
	}
}
