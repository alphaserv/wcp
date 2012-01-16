<?php

class Page extends MX_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('page_m', 'page');
		$this->load->library('template');
		$this->load->library('parser');
	}
	function index()
	{
		echo 'GO!'.PHP_EOL;
		$this->load->library('serverinfo');
		$serv = new AS_Cube2Server;
		$serv->host = 'psl.sauerleague.org';
		$serv->port = 10000;
		$serv->query();
		
		#TODO: custom layout support
#		$page = $this->page->gethomepage();
#		$this->template->build('page/pageview', array('page' => $page));
		/*
		if(false)
			$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
		else
			$this->load->driver('cache', array('adapter' => 'file'));
		
		if ( ! $foo = $this->cache->get('foo'))
		{
		
			echo 'Saving to the cache!<br />';
			$foo = 'foobarbaz!';
			
			// Save into the cache for 5 minutes
			$this->cache->save('foo', $foo, 300);
		}
		
		echo $foo;*/
	}
	function title($title = null)
	{
		if(!$title)
			$this->index();
		
		$this->db->cache_on();
		$page = $this->page->getpage($title);
		$this->db->cache_off();
		$this->template->build('page/pageview', array('page' => $page));
	}
	function id($title = null)
	{
		if(!$page)
			$this->index();
		
		$this->db->cache_on();
		print_r($this->page->getpage($title));
		$this->db->cache_off();
	}
	function search()
	{
		$words = implode('%', func_get_args());
		
		if(empty($words))
			$this->index();
		
		$this->db->cache_on();
		print_r($this->page->search($words));
		$this->db->cache_off();
		
		$this->template->build('page/indexview', array('page' => $page));
	}
}
