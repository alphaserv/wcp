<?php

class Content_handlers
{
	static $handlers;
	
	static function add_handler($name, &$handler)
	{
		$handlers[$name] =& $handler;
	}
	
	static function call($name, $content)
	{
		return $handlers[$name]($content);
	}
}

class ubb_tag_parser
{
	private $tag;
	
	function __construct()
	{
		$CI =& get_instance();
		$CI->load->library('tags');
		$this->tag = new Tags(array(
			'trigger' => '',
			'l_delim' => '[',
			'r_delim' => ']',
			'trigger' => 'admin'
		));
	}
	
	function __invoke($content)
	{
		
	}
	
}
