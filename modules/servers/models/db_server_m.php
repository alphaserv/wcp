<?php

class Db_server_m extends CI_Model
{
	const games = array(
		'minecraft' => function() {return new server\Sauer_Cached_Server; }
		'sauerbraten' => function() {return new server\MC_Cached_Server; }
	);
	static $servers;
	function __construct()
	{
		parent::__construct();
		
		require_once dirname(__file__).'/servertypes/std.php';
		require_once dirname(__file__).'/servertypes/mc.php';
		require_once dirname(__file__).'/servertypes/cube2.php';
	}
	
	function new_cached_server($game, $data)
	{
		$server =& self::games[$game]();
		$server->init_data($data);
		self::$servers[] =& $server;
		return $server;
	}
	
	function local_servers()
	{
		$result = $this->db->query('
			SELECT
				game,
				name,
				description,
				lastupdated,
				cache,
				host,
				DATEDIFF(second, NOW(),lastupdated) as diff
			FROM
				web_serverlist');
		
		foreach($result->result_object() as $row)
			if($row->diff > 30) #update every 30 sec
				$this->update_server($row);
			elseif
				$this->new_cached_server($row->game, (array)$row);
	}
}
