<?php

define('STATUS_ONLINE', 1);			#server is online
define('STATUS_OFFLINE', 2);		#server is offline
define('STATUS_NOT_CONNECTED', 4);	#no tries made to connect

class AS_Player
{
	public $name;
}

class server_m extends CI_Model
{
	static $games;
	static $servers;
	function __construct()
	{
		parent::__construct();
		
		require_once dirname(__file__).'/servertypes/std.php';
		require_once dirname(__file__).'/servertypes/mc.php';
		require_once dirname(__file__).'/servertypes/cube2.php';
		
		self::$games = array(
			'minecraft' => function() {return new server\MC_Server; },
			'sauerbraten' => function() { return new server\Sauer_Server; }
		);
	}
	
	function newserver($game)
	{
		$server = self::$games[$game];
		$server =& $server();
		self::$servers[] =& $server;
		return $server;
	}
}
