<?php

class Servers extends MX_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('mc_status'); #for minecraft servers
		$this->load->model('server_m');
		
		$this->load->library('template');
		$this->load->library('Parser');
		$this->load->library('form');
		$this->load->model('site_settings');
		$this->load->helper('url');
		$this->load->helper('quick_escape');
		#$this->load->model('db_server_m'); #not finished
	}
	function index()
	{
		$serverlist = array();
		
		$server = $this->server_m->newserver('minecraft');
		$server->host = 'alphaserv-web.tk';
		$online = 
		$server->connect();
	
		$serverlist[] = array('game' => 'minecraft', 'host' => $server->host, 'port'=> $server->port, 'online' => (int)$online, 'players' => $server->get_players(), 'info' => $server->get_info());
		unset($server);
		
		$server = $this->server_m->newserver('sauerbraten');
		$server->host = 'nooblounge.net';
		$server->port = 10030;
		$online = $server->connect();
		
		$serverlist[] = array('game' => 'sauerbraten', 'host' => $server->host, 'port'=> $server->port, 'online' => (int)$online, 'players' => $server->get_players(), 'info' => $server->get_info());

		$server = $this->server_m->newserver('sauerbraten');
		$server->host = 'psl.sauerleague.org';
		$server->port = 10000;
		$online = $server->connect();
		
		$serverlist[] = array('game' => 'sauerbraten', 'host' => $server->host, 'port'=> $server->port, 'online' => (int)$online, 'players' => $server->get_players(), 'info' => $server->get_info());

		$server = $this->server_m->newserver('sauerbraten');
		$server->host = 'psl.sauerleague.org';
		$server->port = 20000;
		$online = $server->connect();
		
		$serverlist[] = array('game' => 'sauerbraten', 'host' => $server->host, 'port'=> $server->port, 'online' => (int)$online, 'players' => $server->get_players(), 'info' => $server->get_info());
		
		$server = $this->server_m->newserver('sauerbraten');
		$server->host = 'psl.sauerleague.org';
		$server->port = 30000;
		$online = $server->connect();
		
		$serverlist[] = array('game' => 'sauerbraten', 'host' => $server->host, 'port'=> $server->port, 'online' => (int)$online, 'players' => $server->get_players(), 'info' => $server->get_info());
		
		$server = $this->server_m->newserver('sauerbraten');
		$server->host = 'psl.sauerleague.org';
		$server->port = 40000;
		$online = $server->connect();
		
		$serverlist[] = array('game' => 'sauerbraten', 'host' => $server->host, 'port'=> $server->port, 'online' => (int)$online, 'players' => $server->get_players(), 'info' => $server->get_info());



		#$server = new AS_SauerServer();
		#$server->host = 'localhost';
		#$server = new AS_McServer();
		#$server->host = 'alphaserv-web.tk';
		//$server->port = 28785;
		/*
		$server->host = 'nooblounge.net';
		$server->port = 10030;
		$server->host = 'psl.sauerleague.org';
		$server->port = 10000;
		*/
		
		$serverlist['servers'] = quick_html_escape($serverlist);

		switch($this->router->content_type)
		{
			default:
			case 'html':
				$this->template
					->set_title('servers')
					->build('overvieuw', $serverlist);
				break;
				
			case 'json':
				$this->output->set_output(json_encode($serverlist));
				break;
			
			case 'xml':
				$this->output->set_status_header(400);
				$this->output->set_output('<pages><page status="400" message="this page is not available for xml-ajax (yet?)" /> </pages>');
				break;
		}
	}

}

