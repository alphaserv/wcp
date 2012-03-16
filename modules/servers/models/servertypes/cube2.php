<?php
namespace server;

class Sauer_Server extends Gameserver
{
	private $socket;
	
	function __construct()
	{
		parent::__construct();
	
		require_once dirname(__file__).'/cube2/buffer.php';
	
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
		
		#print_r($this);
		
		$this->socket = stream_socket_client('udp://'.$this->host.':'.($this->port + $this->queryportdiff), $errno, $errstr, 5);

		if($this->socket === false)
		{
			#could not connect
			$this->status = 0;
			$this->status |= STATUS_OFFLINE;
			return false;
		}
		
		return true;
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
		# print_r($buffer->stack);

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
		#print_r($buffer->stack);

		#stuff we don't need:
		$buffer->getint(); #0
		$buffer->getint(); #-2
		$buffer->getint(); #-1
		$buffer->getint(); #protocol version
		
		$result = $buffer->getint(); #error, always 0
		
		#print_r((int)$result);
		
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
		if(isset($this->connection) && $this->connection)
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
