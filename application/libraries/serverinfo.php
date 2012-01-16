<?php #if(!defined(' #what was it again? :D

abstract class AS_Gameserver
{
	public $game;

	public $host;
	public $port;
	public $status;
	
	public $cacheing = true;
	public $cache_ttl = 30;
	
	abstract public function query();
}

class ASBuffer {
	public $stack = array();

	function getc()
	{ 
		return array_shift($this->stack);
	}

	function getint()
	{  
		$c = $this->getc();
		if ($c == 0x80)
		{
			$n = $this->getc(); 
			$n |= $this->getc() << 8; 
			return $n;
		}
		else if ($c == 0x81)
		{
			$n = $this->getc();
			$n |= $this->getc() << 8;
			$n |= $this->getc() << 16;
			$n |= $this->getc() << 24;
			return $n;
		}
		return $c;
	}

	function getstring($len=10000)
	{
		$r = ""; $i = 0; 
		while (true)
		{ 
			$c = $this->getint();
			if ($c == 0) return $r;
			$r .= chr($c);
		} 
	}
};

class AS_Cube2Server extends AS_Gameserver
{
	private $connection;
	private $queyport; #port + 1
	
	public function query()
	{
		echo 'connecting...';
		$CI =& get_instance();
		
		$this->queryport = $this->port+1;
		
		$this->connection = stream_socket_client('udp://'.$this->host.':'.$this->port, $errno, $errstr, 5);
		$CI->load->driver('cache', array('adapter' => 'file'));
		
		$returnarray = array();
		if($this->connection !== FALSE)
		{
			echo 'connected...';
			$returnarray['online'] = 1;
					
			if ( ! $returnarray['status'] = $CI->cache->get('sauercache_'.$this->host.$this->port))
			{
		
				#send signed char 3 times
				fwrite($this->connection, pack("ccc", 0, 1, '-1'));
				
				if(($data = fread($this->connection, 50)) === false)
					throw new exception('could not read');
			
				echo ';;;'.PHP_EOL;
				print_r($data);
				echo ';;;'.PHP_EOL;
				print_r(unpack("C*", $data));
				echo ';;;'.PHP_EOL;
				fclose($this->connection);
				die();
			/*
			$this->buffer->stack = unpack("C*", $data);
		
			for ($i = 0; $i <= 7; $i++)
			{
				if ($this->buffer->getint() == 0)
				{ // no error packet
					for ($i=1;$i<=5;$i++)
						$this->buffer->getint();
					
					break;
				}
			}

			$cn_players = array();
			
			for ($i = 7; $i < 100; $i++)
			{
				$tmp = $this->buffer->getint();
				if ($tmp == NULL) continue;
				$cn_players[$i-7] = $tmp;
			}
			
			$players = array();
			foreach($cn_players as $n_player)
				$players[] = new onlineplayer ($this);
			
			$this->players = $players;
				
				$buffer      = '';
				fwrite($this->connection,"\xFE");
			
				while (!feof($this->connection))
					$buffer .= fgets($this->connection, 1024);
			
				fclose($this->connection);
			
				$data = SubStr( $buffer, 3 );
				$data = iconv( 'UTF-16BE', 'UTF-8', $data );
				$data = Explode( "\xA7", $data );
				
				$returnarray['status']['servername'] = trim(preg_replace('/[^(\x20-\x7F)]* /','', (string)$data[0]));
				$returnarray['status']['playercount'] = (int)(string)$data[1];
				$returnarray['status']['maxplayers'] = (int)(string)$data[2];
				$CI->cache->save('sauercache_'.$this->host.$this->port, $returnarray['status'], 60);*/
			}
		}
		else
		{
			throw new exception('could not connect');
			$returnarray['online'] = 0;
			if($CI->cache->get('sauercache_'.$this->host.$this->port))
				$CI->cache->delete('sauercache_'.$this->host.$this->port);
		}
		$returnarray['game'] = (array)$this;
		return $returnarray;
	}
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
					
			if ( ! $returnarray['status'] = $CI->cache->get('mccache_'.$this->host.$this->port))
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
				$CI->cache->save('mccache_'.$this->host.$this->port, $returnarray['status'], 60);
			}
		}
		else
		{
			$returnarray['online'] = 0;
			if($CI->cache->get('mccache_'.$this->host.$this->port))
				$CI->cache->delete('mccache_'.$this->host.$this->port);
		}
		$returnarray['game'] = (array)$this;
		return $returnarray;
	}
}
require (dirname(dirname(__file__)).'/third_party/gameq/GameQ.php');

//namespace serverinfo
//{

	class buf {
		public $stack = array();
		function getc() { 
			return array_shift($this->stack);
		}
		function getint() {  
		    //$deb++;
			$c = $this->getc();
			if ($c == 0x80) { 
				$n = $this->getc(); 
				$n |= $this->getc() << 8; 
				return $n;
			}
			else if ($c == 0x81) {
				$n = $this->getc();
				$n |= $this->getc() << 8;
				$n |= $this->getc() << 16;
				$n |= $this->getc() << 24;
				return $n;
			}
			return $c;
		}
		function getstring($len=10000) {
			$r = ""; $i = 0; 
			while (true) { 
				$c = $this->getint();
				if ($c == 0) return $r;
				$r .= chr($c);
			} 
		}
	};
	
function ext_get_player($s, $b) {
	$g = fread($s, 50);
	$b->stack = unpack("C*", $g);

	for ($i = 1; $i <= 7; $i++) $b->getint();

	$player["cn"] = $b->getint();
	$player["ping"] = $b->getint();
	$player["name"] = $b->getstring();
	$player["team"] = $b->getstring();
	$player["frags"] = $b->getint();

	if ($player["frags"] >= 200) 
		$player["frags"] -= 256;

	$player["flags"] = $b->getint();
	$player["deaths"] = $b->getint();
	$player["teamkills"] = $b->getint();
	$player["acc"] = $b->getint();
	$player["health"] = $b->getint();

	if ($player["health"] >= 200) 
		$player["health"] -= 256;
			
	$player["armour"] = $b->getint();
	$player["gun"] = $b->getint();
	$player["priv"] = $b->getint();
	$player["state"] = $b->getint();
			
	$ip = $b->getint();
	$ip .= '.'.$b->getint();
	$ip .= '.'.$b->getint().'.255';	
	$player["ip"] = $ip;
	
	return $player;
}

function server_info($ip, $port) {

}
//};

class serverinfo
{
	private $GQ;
	private $CI;
	private $servers = array();
	
	public $master_host = 'sauerbraten.org';
	public $master_port = 28787;
	
	function __construct()
	{
		$this->GQ = new GameQ();
		$this->CI =& get_instance();
//		$this->CI->load->library('cache');
//		$this->CI->load->driver('cache', array('adapter' => 'apc', 'backup' => 'memcached'));
	}
	
	public function addserver($ip, $port = 28786, $game = "sauerbraten")
	{
		#sauerbraten info port is <port>+1
		if ($game === "sauerbraten") $port++;
		
		$this->servers[(string)$ip.':'.(int)$port] = array((string)$game, (string)$ip, (int)$port);
	}

	public function setoption($key = null, $value = null)
	{
		$this->GQ->setOption($key, $value);
	}
	
	public function add_filter($name, $options = array())
	{
		$this->GQ->setFilter($name, $options);
	}
	
	public function requestdata()
	{
		$this->GQ->addServers($this->servers);
		$this->Servers = null;
		
		try
		{
			$result = $this->GQ->requestData();
			return $result;
		}
		catch (GameQ_Exception $e)
		{
			echo 'An error occurred.';
		}
	}
	
	public function get_from_db()
	{
		$result = $this->CI->db->query('SELECT port, ip FROM serverlist');
		
		#no results
		if(!$result || $result->num_rows() == 0)
			return false;
		
		foreach ($result->result() as $row)
			$this->addserver($row->ip, $row->port, 'sauerbraten');

	}
	public function update_serverlist($ip, $port, $game = 'sauerbraten')
	{
		$returning = array();
		switch ($game)
		{
			case 'sauerbraten':
				
				$i = 0;
				
				do
				{
					$socket = fsockopen($ip, $port, $errno, $errstr);
				
				}
				while($i < 0 && !$socket);
				
				if(!$socket)
					throw new exception ('could not update from masterserver: Unable to connect #'.(int)$errno);
				
				fwrite ($socket , 'list'."\n" );
				
				$data = '';
				
				while(!feof($socket))
					$data .= fread($socket, 100000);
				
				fclose($socket);
				
				$this->rawdata = $data;
				
				foreach(explode("\n", $data) as $i => $row)
				{
					$row = explode(' ', $row);
					if(!isset($row[1]))
						continue;
					
					if(!isset($row[2]))
						$row[2] = 28786;
					
					$returning[] = array($row[1], $row[2], $game, $ip, $port);
				}
				
				break;
			default:
				throw new exception('could not update from masterserver: Unsupported game');
				break;
		}
		return $returning;
	}
	
	public function update_mysql($data)
	{
		$errors = false;
		foreach($data as $i => $row)
		{
		
			if($this->CI->db->query('SELECT ip, port FROM serverlist WHERE ip = ? AND port = ? AND game = ? AND master_ip = ? AND master_port = ?', $row)->num_rows() < 1)
				if(!$this->CI->db->query('INSERT INTO serverlist (ip, port, game, master_ip, master_port) VALUES (?, ?, ?, ?, ?)', $row))
					$errors = true;
		}
		
		if($errors)
			throw new exception ('could not save masterlist: Error on query');
	}
	
	public function fetch_mysql()
	{
		$result = $this->CI->db->query('SELECT ip, port, game, master_ip, master_port FROM serverlist');

		if(!$result)
			throw new exception ('could not update from the database: Error on query');
		
		if($result->num_rows() < 1)
			throw new exception ('could not update from the database: No rows returned');
		
		return $result->result_array();
	}
	
	#advanced player query
	public function sauer_ext_query($ip, $port)
	{
		
		$players = array();
		
		$s = stream_socket_client("udp://".$ip.":".$port);
		
		fwrite($s, pack("ccc", 0, 1, '-1')); 
	$b = new buf();
	$g = fread($s, 50);
	
	if (!$g) 
		return false;
	
	$x = unpack("C*", $g);
	$b->stack = unpack("C*", $g);
		
	$cn_players = array();
	for ($i = 0; $i <= 7; $i++) 
		if ($b->getint() == 0) { // no error packet
			for ($i=1;$i<=5;$i++) $b->getint();
			break;
		}
	
	for ($i = 7; $i < 100; $i++) {
		$tmp = $b->getint();
		if ($tmp == NULL) continue; 
		$cn_players[$i-7] = $tmp;
	}
		
	if ($x[5] == 103 || $x[5] == 104)  // make sure we are compatible with this server
	{
		if (sizeof($x) > 7) 
			foreach($cn_players as $n_player)
				$players[] = ext_get_player($s, $b);
	}
	elseif($x[5] == 105)
	{
		if (sizeof($x) > 7) 
			foreach($cn_players as $n_player)
				$players[] = ext_get_player($s, $b);
	}
	else
		echo 'not compatible';
    
    fclose($s);
	
	return $players;
//		return serverinfo::server_info($ip, $port);
		try
		{
			return server_info($ip, $port);
		}
		catch(Exception $e)
		{
			echo $e->getMessage();
			return false;
		};
		
	}
	
	public function list_players($max_servers = 15)
	{
		echo '<style>td{border-width:1;border-style:solid;border-color:grey;</style>';
		echo '<table>';
		echo '<tr><td>cn</td><td>ping</td><td>name</td><td>team</td><td>servername</td><td>map</td></tr>';

		$sort_servers = function ($a, $b)
			{
				if(!isset($a['num_players']) || !isset($b['num_players']))
					return 0;
				if($a['num_players'] < $b['num_players'])
					return 1;
				else
					return -1;
			};
		$i = 0;
		$list = $this->requestdata();
		usort($list, $sort_servers);
		foreach ($list as $ipport => $server)
		{
			//TODO: modular seperation, KUCH..
			$i++;
			if($max_servers == $i) break;
			$sort_cn = function ($a, $b)
				{
					if(!isset($a['cn']) || !isset($b['cn']))
						return 0;
					if($a['cn'] < $b['cn'])
						return -1;
					else
						return 1;
				};
			echo '<tr>';
			echo '<td>-</td>';
			echo '<td>-</td>';
			echo '<td>-</td>';
			echo '<td style="max-height:10px;">';
			print_r($server);
			echo '</td>';
			
			if(!isset($row['team'])) $row['team'] = '<none>';
			if(!isset($server['servername'])) $server['servername'] = '<none>';
			if(!isset($server['map'])) $server['map'] = '<none>';
			
			echo '<td><strong style="display:inline;">',$server['servername'],'</strong></td>';
			echo '<td>',$server['map'],'</td>';
			echo '</tr>';
			if(isset($server['num_players']) && $server['num_players'] > 0)
				$returned = $this->sauer_ext_query($server['gq_address'], $server['gq_port']);
			else
				$returned = array();
			
			usort($returned, $sort_cn);
			foreach($returned as $key => $row)
			{
				#for each connected
				echo '<tr>';
				echo '<td>',$row['cn'],'</td>';
				echo '<td>',$row['ping'],'</td>';
				echo '<td>',$row['name'],'</td>';
				echo '<td>',$row['team'],'</td>';
				echo '<td>',$server['servername'],'</td>';
				echo '<td>',$server['map'],'</td>';
				echo '</tr>';
			}
		}
		echo '</table>';
	}
};
