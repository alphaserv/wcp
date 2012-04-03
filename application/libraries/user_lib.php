<?php

class user_
{
	//private $CI_;
	public $user_id;
	
	public $logged_in;
	
	private $cached_settings;
	private $cached_access;
	
	public function __construct($id)
	{
		$this->user_id = $id;
	}
	
	public function init(&$auth)
	{
		if($this->user_id == null || $this->user_id == -1)
			$this->logged_in = false;
		else
		{
			$this->logged_in = true;
			$this->update_genic($auth);
		}	
	}
	
	public function get_user_id()
	{
		return $this->user_id;
	}
	
	public function is_logged_in()
	{
		return (bool)$this->logged_in;
	}
	
	public function get_acces_to($name, $on = -1)
	{
		static $CI_;
		if(!isset($CI_)) $CI_ =& get_instance();

		return $CI_->user_m->user_access_to($this->user_id, $name, $on);
	}
	
	public function get_setting(string $name)
	{
		static $CI_;

		if(isset($this->cached_settings[$name]))
			return $this->cached_settings[$name]; #do not encrypt cache (assuming we don't have a 16-core server)

		if(!isset($CI_)) $CI_ =& get_instance();
		
		#cache
		$this->cached_settings[$name] = $this->CI_->userdata_m->get($this->user_id, $name, true);
		return $this->cached_settings[$name];
	}
	
	public function set_setting(string $name, $value)
	{
		static $CI_;
		$this->cached_settings[$name] = $value;
	
		if(!isset($CI_)) $CI_ =& get_instance();
		
		#save in db
		$this->CI_->userdata_m->set($this->user_id, $name, $value);
	}
	
	public function update_genic(&$auth)
	{
		if(!$this->logged_in)
			throw new exception ('cannot update data of non-logged in user');

		if(!get_instance()->user_m->checklastactivity($this->user_id))
		{
			$auth->logout();
		}	
	}
	
	public function destroy()
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
	private $CI_;
	
	#the user wich is visiting the page
	public static $currentuser;
	
	public static $users;
	
	public function __construct()
	{
		$this->CI_ =& get_instance();
		$this->CI_->load->library('session');
		#extra unserialize to prevent errors becouse the session class is loaded before this class
		$usr = unserialize($this->CI_->session->userdata('User'));
		
		if(!$usr)
			$this->clear();
		else
		{
			static::$currentuser =& $usr;
			$id = static::$currentuser->get_user_id();
		}
		
		static::$users[$id] =& static::$currentuser;
		
	}
	
	public function __destruct()
	{
		#save in cache
		$this->writedown();
	}
	
	public function &get_user(int $id)
	{
		return static::$users[$id];
	}
	
	public function &get_current_user()
	{
		return static::$currentuser;
	}
	
	public function change_user(user_ &$new_user)
	{
		$id = $new_user->get_user_id();
		$this->CI_->session->set_userdata('User', serialize(static::$currentuser));
		static::$currentuser =& $new_user;
		static::$users[$id] =& static::$currentuser;
		
	}
	
	public function clear()
	{
		static::$currentuser = new user_(-1);
		$this->CI_->session->set_userdata('User', serialize(static::$currentuser));
	}
	
	public function writedown()
	{
		$this->CI_->session->set_userdata('User', serialize(static::$currentuser));
	}
}
