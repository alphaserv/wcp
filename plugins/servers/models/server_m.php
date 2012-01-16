<?php

abstract class AS_Gameserver
{
	public $game;

	public $host;
	public $port;
	public $status;
	
	public $cacheing = true;
	public $cache_ttl = 30;
	
	abstract function query();
}

class AS_MCServer extends AS_Gameserver
{
	private $connection;
	
	public function query()
	{
		$CI =& get_instance();
		
		$this->connection = @fsockopen($this->host, $this->port, $erno, $erst, 5);
		$CI->load->driver('cache', array('adapter' => 'file'));
		
		$returnarray = array();
		if($this->connection !== FALSE)
		{
			$returnarray['online'] = 1;
					
			if ( ! $returnarray['status'] = $CI->cache->get('mccache'))
			{
		
				$buffer      = '';
				fwrite($this->connection,"\xFE");
			
				while (!feof($this->connection))
					$buffer .= fgets($this->connection, 1024);
			
				fclose($this->connection);
			
				$data = SubStr( $buffer, 3 );
				$data = iconv( 'UTF-16BE', 'UTF-8', $data );
				$data = Explode( "\xA7", $data );
				
				$returnarray['status']['servername'] = trim(preg_replace('/[^(\x20-\x7F)]*/','', (string)$data[0]));
				$returnarray['status']['playercount'] = (int)(string)$data[1];
				$returnarray['status']['maxplayers'] = (int)(string)$data[2];
				$CI->cache->save('mccache', $returnarray['status'], 60);
			}
		}
		else
		{
			$returnarray['online'] = 0;
			if($CI->cache->get('mccache'))
				$CI->cache->delete('mccache');
		}
		$returnarray['game'] = (array)$this;
		return $returnarray;
	}
}

class server_m extends CI_Model
{
	function getstatus($list = array())
	{
		$statuslist = array();
		foreach($list as $server)
			$statuslist[] = $this->queryserver($server);
		return $statuslist;
	}
	
	function queryserver(AS_Gameserver $server)
	{
		return $server->query();
	}
}
