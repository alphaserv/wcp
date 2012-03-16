<?php
namespace server;

class MC_Server extends Gameserver
{
	private $mcquery;
	private $mcrcon;
	
	function __construct()
	{
		parent::__construct();
		$this->mcquery = new \MinecraftQuery();
		$this->mcrcon = new \MinecraftRcon();
		
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
		catch (\MinecraftQueryException $e)
		{
			#if($e->getMessage() != 'Can\'t open connection.' && $e->getMessage() != "Failed to receive challenge.")
			#	throw new exception($e->getMessage());
			
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
