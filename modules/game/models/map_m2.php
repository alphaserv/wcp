<?php

class Session_dependic
{
	private $CI;
	private $_cache = array();
	
	function has_map($mapname)
	{
		return (bool)$this->CI->userdata('_MAP_'.$mapname.'_');
	}
	
	function __construct($map = null)
	{
		$this->CI =& get_instance();
		
		if ($map !== null)
			$this->_cache[$map] = $this->CI->session->userdata('_MAP_'.$map.'_');
	}
	
	function __destruct()
	{
		$this->save();
	}
	
	function save()
	{
		foreach($this->_cache as $mapname => $map)
			$this->CI->session->set_userdata('_MAP_'.$mapname.'_', $map);	
	}
	
	function load($mapname)
	{
		$this->_cache[$mapname] = $this->CI->session->userdata('_MAP_'.$mapname.'_');
	}
	
	function store_block($mapname, $x, $y, $block)
	{
		$this->_cache[$mapname][$x][$y] = $block;
	}
	
	function get_block($mapname, $x, $y)
	{
		if (!isset($this->_cache[$mapname]))
			$this->_cache[$mapname] = $this->CI->session->userdata('_MAP_'.$mapname.'_');

		return $this->_cache[$mapname][$x][$y];
	}
	
	function get_map_array($mapname)
	{
		if (!isset($this->_cache[$mapname]))
			$this->_cache[$mapname] = $this->CI->session->userdata('_MAP_'.$mapname.'_');
		
		return $this->_cache[$mapname];
	}
	
	function get_textures($mapname)
	{
		$this->CI->session->userdata('_MAP_'.$mapname.'_TEXTURES_');
	}
	
	function set_textures($mapname, $textures)
	{
		$this->CI->session->set_userdata('_MAP_'.$mapname.'_TEXTURES_', $textures);
	}
	
	function get_scripts($mapname)
	{
		throw new exception('LAZY CODER: not implemented (yet?)');
	}
}

class texture
{
	public $url;
	
	function __construct($url = null)
	{
		if($url !== null)
			$this->url = $url;
	}
}

class block
{
	public $texture_id;
	public $name;
//	public $collision;//future?
	public $event_handlers = array();
	
	function __construct($tid, $name)
	{
		$this->texture_id = $tid;
		$this->name = $name;
	}
}

class map_part
{
	public $block_id;
	
	public $event_handlers = array();
	
	function __construct($bid)
	{
		$this->block_id = $bid;
	}
	
	function render(&$map, $x, $y)
	{
		return '<div data-map-x='.$x.' data-map-y='.$y.'><img src="'+ $map->textures[$map->map_blocks[$this->block_id]->texture_id]->url.'" title="'. $map->map_blocks[$this->block_id]->name.'" alt="'.$map->map_blocks[$this->block_id]->name."\n";
	}
}

class map_m2 extends CI_Model
{
	private $textures = array();
	private $default_texture_id;
	
	private $map_blocks = array();
	private $default_block_id;
	
	private $map = array();
	private $mapname;


	
	private $storage;
	
	function __construct($map = 'LOL no arg support in constructor of model', $load = true, $storage = null)
	{
		if ($storage === null)
			$this->storage = new Session_dependic($load ? $map : null);
	}
	
	function save()
	{
		$this->storage->save();
	}
	
	function load()
	{
		$this->storage->load($this->mapname);
	}
	
	function can_load()
	{
		return $this->storage->has_map($this->mapname);
	}
	
	function new_block($name, $texture_id = null)
	{
		if($texture_id == null)
			$texture_id = $this->default_texture_id;
		$this->map_blocks[] = new block($texture_id, $name);
		
		return count($this->map_blocks)-1;
	}
	
	function new_element_from_block($block_id)
	{
		return new map_part($block_id);
	}
	
	function create_new_map($x_size = 15, $x_height = 15, $textures = null)
	{
		$this->textures = array();
		$this->matereals = array();
		$this->map = array();
		$this->mapname = uniqid();
		
		for($x = 0; $x <= $x_size; $x++)
		{
			$this->map[$x] = array();
			for($y = 0; $y <= $y_size; $y++)
				$this->map[$x][$y] =& $this->newblock($this->default_mat);
		}
		
		if($textures !== null)
			$this->init_textures($textures);
	}
	
	function init_blocks()
	{
		$this->map_blocks[0] = $this->new_block('default block');
		$this->default_block_id = 0;
	}
	
	function init_textures($textures = array())
	{
		if(count($textures) === 0)
			$this->textures = $this->storage->get_textures($this->mapname);
		else
			foreach($textures as $i => $texture)
				if(is_string($texture))
					$this->textures[(int)$i] = new texture($texture);
				elseif($texture !== null)
					$this->textures[(int)$i] = $texture;
					
		
		$this->default_texture_id = 0;
		
		$this->storage->set_textures($this->mapname, $this->textures);
	}
	
	function get_textures()
	{
		return $this->textures;
	}
	
	function print_r()
	{
		print_r($this);
	}
}
