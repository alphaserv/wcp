<?php
namespace server;

abstract class Gameserver
{
	public $game;

	public $host;
	public $port;
	public $queryportdiff;
	public $rconportdiff;
	
	private $rcon_pass;
	
	public $status;
	
	public $timeout;
	
	function __construct()
	{
		$this->host = 'localhost';
		
		$this->port = 0;
		$this->rconportdiff = 0;
		$this->queryportdiff = 0;
		
		$this->timeout = 3;
	}
	
	function set_port($port)
	{
		$this->port = $port;
	}
	
	function is_online()
	{
		if($this->status & STATUS_NOT_CONNECTED) #lazy try to connect
			$this->connect();
			
		return $this->status & STATUS_ONLINE;
	}
	
	function set_rcon_pass($pass)
	{
		$this->pass = $pass;
	}
	
	#serverinfo
	abstract function connect();
	abstract function disconnect();
	abstract function get_info();
	abstract function get_players();
	
	#rcon
	abstract function rconnect();
	abstract function rcommand($command);
	abstract function rdisconnect();
	
	#depricated
	abstract function query();
}
