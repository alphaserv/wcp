<?php
$sauermaster_host = 'sauerbraten.org';
$sauermaster_port = 28787;
class Servers extends MX_Controller
{
	function __construct()
	{
		parent::__construct();
//		$this->load->library('serverinfo');
	}
	function index($max_servers = 15)
	{
		$this->gameq->get_from_db();
		$this->gameq->list_players($max_servers);
	}
	function update()
	{
		$this->gameq->get_serverlist();
		echo 'done';
	}
	
	function _serverinfo()
	{
	
	}
	
	function _master_list($masterserver = NULL, $port = NULL, $game = 'sauerbraten')
	{
		$result = $this->db->query('SELECT port, ip, masterserver, masterport FROM serverlist WHERE masterserver = ? AND masterport = ?');
		
		#no results
		if(!$result || $result->num_rows() == 0)
			return false;
		
		$return = array();		
		foreach ($result->result() as $row)
			$this->servers[(string)$row->ip.':'.(int)$row->port] = array((string)$game, (string)$row->ip, (int)$row->port);
		
		return $return;
	}
	
	function _sort_servers(&$data)
	{
		usort($data, $this->sort_servers);
		return $data;
	}
	function mc()
	{
		$strHost = "localhost";
		$strPort = "25565";
		 
		//opent een socket verbinding
		$socket = @fsockopen($strHost, $strPort, $erno, $erst, 5);

		@$this->load->driver('cache', array('adapter' => 'file'));
		/*TODO:
			find out why this causes the following error:
				A PHP Error was encountered

				Severity: Notice

				Message: Undefined property: Page::$cache

				Filename: MX/Loader.php

				Line Number: 165
		*/
		
		$cache =& get_instance()->cache;
		
		if($socket !== FALSE)
		{
				echo 'server is ONLINE'.PHP_EOL;
		
			if ( ! $status = $this->cache->get('mccache'))
			{
		
				echo 'Saving to the cache!<br />'.PHP_EOL;

				$buffer      = '';
				fwrite($socket,"\xFE");
			
				while (!feof($socket))
					$buffer .= fgets($socket, 1024);
			
				fclose($socket);
			
				//print_r resultaat
				print_r($buffer);
			
				echo PHP_EOL;
				$data = SubStr( $buffer, 3 );
				print_r($data);
				echo PHP_EOL;
				$data = iconv( 'UTF-16BE', 'UTF-8', $data );
				print_r($data);
				echo PHP_EOL;
				$data = Explode( "\xA7", $data );
				$data[0] = (string)$data[0];
				$data[1] = (int)$data[1];
				$data[2] = (int)$data[2];
				print_r($data);
				echo PHP_EOL;
		
				$status = $data;
				// Save into the cache for 5 minutes
				$this->cache->save('mccache', $status, 60);
			}
		
			print_r( $status);
		}
		else
		{
			echo 'server is OFFLINE'.PHP_EOL;
			if($this->cache->get('mccache'))
				$cache->delete('mccache');
		}
	}
	function statusbar()
	{
		$this->load->model('server_m');
		$server = new AS_MCServer;
		$server->host = 'localhost';
		$server->port = 25565;
		$this->benchmark->mark('start query');
		$list = $this->server_m->getstatus(array($server));
		$this->benchmark->mark('stop query');
		
		$this->load->library('parser');
		
		return $this->parser->parse('servers/status_bar', array('serverlist_status' => $list), true);
	}
}

