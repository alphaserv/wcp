<?php

class Template
{
	private $title = '';
	private $title_seperator = ' | ';
	
	private $body = array();
	
	private $theme = 'templatea';
	private $layout = 'pc';
	private $template_path;
	
	function __construct()
	{
	
	}
	
	function set_theme($theme)
	{
		$this->theme = $theme;
		return $this;
	}
	
	function set_layout($layout)
	{
		$this->layout = $layout;
		return $this;
	}
	
	function set_title_seperator($seperator)
	{
		$this->title_seperator = $seperator;
		return $this;
	}
	
	function set_title($title)
	{
		if(is_array($title))
			$title = implode($this->title_seperator, $title);
		
		if($this->title !== '')
			$title = implode($this->title_seperator, array($this->title, $title));
		
		$this->title = $title;
		return $this;
	}
	
	function add_body($viewdata, $tagdata = array())
	{
		$this->body[] = array($viewdata, $tagdata);
		return $this;
	}	
/*	function build($data = array())
	{
		if (!is_array($data))
			$data = (array) $data;
		
		$CI =& get_instance();
		
		foreach($this->body as $body)
		{
			$CI->parser->parse_string($body[0], array_merge($data, $body[1]), true);
		}
	}*/
	function build($view, $data = array())
	{
		if (!is_array($data))
			$data = (array) $data;

		$CI =& get_instance();
		
		$data['template'] = (array)$this;

		$this->template_path = FCPATH.'templates/'.$this->theme.'/';
		
		require_once ($this->template_path.'theme.php');

		$themename = ucfirst($this->theme);
		$template = new $themename;
		
		foreach($template->partials as $partial)
		{
			if(defined('OMG_DEBUG'))
				echo 'adding partial ', $partial;
			$data['template']['partial'][$partial] = $CI->parser->parse_string(file_get_contents($this->template_path.'partials/'.$partial.'.php'), $data, true);
			$data['template_partial_'.$partial] = $data['template']['partial'][$partial];
		}

		$data['main'] = $CI->parser->parse($view, $data, true);

		echo $CI->parser->parse_string(file_get_contents($this->template_path.'layouts/'.$this->layout.'.php'), $data, true);
	}
}

class AS_Theme
{
	public $name;
	public $description;
	public $preview;
	public $partials = array();
	public $layouts = array();
}
