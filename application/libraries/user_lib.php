<?php

class user
{
	private $CI;
	private $user_id;
	
	private $logged_in;
	
	private $username;
	private $email;
	
	private $cached_settings;
	private $cached_access;
	
	function __construct(int $id)
	{
		$this->CI =& get_instance();
		
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
	
	function get_acces_to(string $name)
	{
		#decbin($name)
		return $this->CI->user_m->user_access($this->user_id, $name);
	}
	
	function get_setting(string $name)
	{
		if(isset($this->cached_settings[$name]))
			return $this->cached_settings[$name]; #do not encrypt cache (assuming we don't have a 16-core server)
		
		#cache
		$this->cached_settings[$name] = $this->CI->userdata_m->get($this->user_id, $name, true);
		return $this->cached_settings[$name];
	}
	
	function set_setting(string $name, $value)
	{
		$this->cached_settings[$name] = $value;
		
		#save in db
		$this->CI->userdata_m->set($this->user_id, $name, $value);
	}
	
	function update_genic()
	{
		if(!$this->logged_in)
			throw new exception ('cannot update data of non-logged in user');
		
	}
}

class User_lib
{
	private $CI;
	
	#the user wich is visiting the page
	public static $currentuser;
	
	public static $users;
	
	function __construct(int $id = -1)
	{
		static $executed;
		
		if(isset($executed))
			throw new exception('trying to create a second '.__CLASS__.' instance.');
		else
			$executed = true;
			
		$this->CI =& get_instance();
		
		$usr = $this->CI->session->userdata('User');
		
		if(!$usr)
			static::$currentuser = new user($id);
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
		$this->CI->session->set_userdata('User', static::$currentuser);
	}
	
	function &get_user(int $id)
	{
		return static::$users[$id];
	}
	
	function &get_current_user()
	{
		return static::$currentuser;
	}

}
