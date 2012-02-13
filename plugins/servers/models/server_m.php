<?php

define('STATUS_ONLINE', 1);			#server is online
define('STATUS_OFFLINE', 2);		#server is offline
define('STATUS_NOT_CONNECTED', 4);	#no tries made to connect

class AS_Player
{
	public $name;
}

abstract class AS_Gameserver
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

class AS_MCServer extends AS_Gameserver
{
	private $mcquery;
	private $mcrcon;
	
	function __construct()
	{
		parent::__construct();
		$this->mcquery = new MinecraftQuery();
		$this->mcrcon = new MinecraftRcon();
		
		$this->status = 0;
		$this->status |= STATUS_NOT_CONNECTED;
		
		$this->port = 25565;
		$this->rconportdiff = 10;
		$this->queryportdiff = 0;
		
		$this->game = 'minecraft';
	}
	
	#serverinfo
	function connect()
	{
		if($this->status & STATUS_ONLINE || $this->status & STATUS_OFFLINE)
			throw new exception ('already connected to server');
		
		elseif(! $this->status & STATUS_NOT_CONNECTED)
			throw new exception ('data corruption, already tried?');
		
		try
		{
			$this->mcquery->connect($this->host, $this->port + $this->queryportdiff, $this->timeout);
		}
		
		catch ( MinecraftQueryException $e)
		{
			if($e->getMessage() != 'Can\'t open connection.')
				throw new exception($e->getMessage());
			
			$this->status = 0;
			$this->status |= STATUS_OFFLINE;
			return;
		}
		
		$this->status = 0;
		$this->status |= STATUS_ONLINE;
	}
	
	#auto closing after connect so nothing to do
	function disconnect() {}
	
	function get_info()
	{
		return $this->mcquery->GetInfo();
	}
	function get_players()
	{
		return $this->mcquery->GetPlayers();
	}
	
	#rcon
	function rconnect()
	{
		try
		{
			$this->mcrcon->connect($this->ip, $this->port + $this->rconportdiff, $this->rcon_pass, $this->timeout);
		}
		catch( MinecraftRconException $e )
		{
			throw new exception($e->getMessage());
		}
	}
	function rcommand($command)
	{
		try
		{
			return $this->mcrcon->Command($command);
		}
		catch( MinecraftRconException $e )
		{
			throw new exception($e->getMessage());
		}
	}
	function rdisconnect()
	{
		$this->mcrcon->Disconnect();
	}
	
	function query() { throw new exception('query(): is depreicated');}
}

#buffer class 
class AS_Buffer {
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

class AS_SauerServer extends AS_Gameserver
{
	private $socket;
	
	function __construct()
	{
		parent::__construct();
	
		$this->status = 0;
		$this->status |= STATUS_NOT_CONNECTED;
		
		$this->port = 28785;
		$this->rconportdiff = 0;
		$this->queryportdiff = 1;
		
		$this->game = 'sauerbraten justice';
		
		#$buffer = new AS_Buffer();
	}
	

	#serverinfo
	function connect()
	{
		if($this->status & STATUS_ONLINE || $this->status & STATUS_OFFLINE)
			throw new exception ('already connected to server');
		
		elseif(! $this->status & STATUS_NOT_CONNECTED)
			throw new exception ('data corruption, already tried?');
		
		print_r($this);
		
		$this->socket = stream_socket_client('udp://'.$this->host.':'.($this->port + $this->queryportdiff), $errno, $errstr, 5);

		if($this->socket === false)
		{
			#could not connect
			$this->status = 0;
			$this->status |= STATUS_OFFLINE;
			return;
		}
	}
	
	function disconnect()
	{
	
	}
	
	function get_info()
	{
	
	}
	
	/*private */function is_hopmod_server()
	{
		#check if a server uses hopmod, NOTE: alphaserv wil return true too
		# 0 prefix, alsways? XXX: confirm
		#-2 EXT_HOPMOD, makes it return error + ext_hopmod in case of a hopmod serverm error with not a hopmod server
		fwrite($this->socket, pack("cc", 0, -2));

		$data = fread($this->socket, 50);
		$buffer = new AS_Buffer();
		$buffer->stack = unpack("c*", $data);
		print_r($buffer->stack);

		#stuff we don't need:
		$buffer->getint(); #0
		$buffer->getint(); #-2
		$buffer->getint(); #-1
		$buffer->getint(); #protocol version
		
		$error = $buffer->getint(); #error, always 0
		$return = $buffer->getint(); #does not exist on non hopmod
		
		if($error !== 1) #WTF
			throw new exception ('protocol unkown');
		
		if(count($buffer->stack) == 1)
			#not hopmod :/
			return false;
		
		#maby hopmod, let's check
		elseif($return == -2)
			return true; #yay hopmod
			
		return false;
	}
	
	/*private */function is_alphaserv_server()
	{
		fwrite($this->socket, pack("cc", 0, -3));

		$data = fread($this->socket, 50);
		
		$buffer = new AS_Buffer();
		$buffer->stack = unpack("c*", $data);
		print_r($buffer->stack);

		#stuff we don't need:
		$buffer->getint(); #0
		$buffer->getint(); #-2
		$buffer->getint(); #-1
		$buffer->getint(); #protocol version
		
		$result = $buffer->getint(); #error, always 0
		
		print_r((int)$result);
		
		if($result === 1) #not alphaserv
			return false;
		elseif($result === 11)
			return true; #alphaserv !!!!
		else
			throw new exception('servermod conflict, servermod uses the same int to detect if it is that servermod, please contact the creator (killme) to fix this');
	}
	
	function get_players()
	{
		#get player info,
		# 0 prefix, always needed?
		# 1 EXT_PLAYERSTATS
		# -1 means all players
		fwrite($this->connection, pack("ccc", 0, 1, '-1'));
	}
	
	#rcon
	function rconnect()
	{
		throw new exception ('not supported');
	}
	
	function rcommand($command)
	{
		throw new exception ('not supported');
	}
	
	function rdisconnect()
	{
		throw new exception ('not supported');
	}
	
	#depricated
	function query() { throw new exception('query(): is depreicated');}
}

class server_m extends CI_Model
{

}
