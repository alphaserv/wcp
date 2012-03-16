<?php
namespace server;

class MC_Cached_Server extends Gameserver
{
	
	function __construct()
	{
		parent::__construct();
	}
	
	function init_data($data)
	{
		$this->data = $data;
	}
	
	function __get($key)
	{
		return $this->data[$key];
	}
	
	function connect() {}
	function disconnect() {}
	
	function get_info()
	{
		return $this->data['info'];
	}
	function get_players()
	{
		return $this->data['players'];
	}
}
