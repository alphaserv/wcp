<?php

class block
{
	public $img = 'http://selectsg.com/yahoo_site_admin/assets/images/FillDirt_160203017.2160845_std.jpg';
	public $name = 'empty block';
	
	static function create_spawn()
	{
		return function()
		{
			$a = __CLASS__;
			return new $a();
		};
	}
	
	function render($x, $y)
	{
		return '<div data-map-x='.$x.' data-map-y='.$y.'><img src="'.$this->img.'" title="'.$this->name.'" alt="'.$this->name.'" /></div>'.PHP_EOL;
	}
	
	function click(&$map_m, $arg)
	{
		#$arg[0] = action
		#$arg[1] = x
		#$arg[2] = y
		$block = $map_m->new_block(map_m::BLOCK_GRASS);
		$map_m->set_block($arg[1], $arg[2], $block);
		
		return array(array('change', $arg[1], $arg[2], $block));
	}
}

class grass_block extends block
{

	static function create_spawn()
	{
		return function()
		{
			$a = __CLASS__;
			return new $a();
		};
	}

	public $img = 'http://www.gocamping.com.au/catalogue/files/grass_stock.jpg';
	public $name = 'grass';
}

class map_m extends CI_Model
{
	//v2
	private $textures = array('http://www.gocamping.com.au/catalogue/files/grass_stock.jpg', 'http://selectsg.com/yahoo_site_admin/assets/images/FillDirt_160203017.2160845_std.jpg');
	
	function map_textures()
	{
		return $this->textures;
	}
	
	//v1
	const BLOCK_EMPTY = 0;
	const BLOCK_GRASS = 1;
	
	private $map = array();
	private $blocks = array();
	
	private $x = 15;
	private $y = 15;
	
	function __construct()
	{
		parent::__construct();
		$this->load->library("session");
		
		$this->_initblocks();
		
		if(($this->map = $this->session->userdata("_MAP_")) == false)
			$this->clear_map();
	}
	
	#create spawners of all available blocks
	function _initblocks()
	{
		$this->blocks[self::BLOCK_EMPTY] = block::create_spawn();
		$this->blocks[self::BLOCK_GRASS] = grass_block::create_spawn();
	}
	
	#create new empty block
	function _empty_block()
	{
		return $this->new_block(self::BLOCK_EMPTY);
	}
	
	function new_block($type)
	{
		$a = $this->blocks[$type];
		return $a();
	}
	
	#create new empty map
	function clear_map()
	{
		#only one block for the whole map =)
		$empty_one = $this->_empty_block();
		for($x = 0; $x <= $this->x; $x++)
		{
			$this->map[$x] = array();
			for($y = 0; $y <= $this->y; $y++)
				$this->map[$x][$y] =& $empty_one;
		}	
	}
	
	function get_map()
	{
		return $this->map;
	}
	
	function &get_block($x, $y)
	{
		return $this->map[$x][$y];
	}
	
	function set_block($x, $y, &$block)
	{
		$this->map[$x][$y] =& $block;
		return $this;
	}
	
	function valid_block($x = -1, $y = -1)
	{
		return isset($this->map[$x]) && isset($this->map[$x][$y]) && isset($this->map[$x][$y]->name) && isset($this->map[$x][$y]->img);
	}
	
	#render html
	function render()
	{
		$out = '';
		foreach($this->map as $x => $row)
			foreach($row as $y => $block)
				$out .= $block->render($x, $y);
		
		return $out;
	}
}
