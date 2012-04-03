<?php

class widgets
{

	private $CI;
	
	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->library('tags');
		
		include dirname(__FILE__).'/widget.php';
	}
	
	public function parse_box($id)
	{
		$res = $this->CI->db->query('
			SELECT
				box
			FROM
				web_widget
			WHERE
				id = ?', array($id));
		
		if($res->num_rows() < 1)
			return ($this->CI->db->query('INSERT INTO web_widget (id) VALUES (?)', $id))? '' : '';

		$string = $res->first_row()->box;
		
		$parser = new Tags;
			
		$this->CI->tags->set_trigger('as:');
		
		$parsed = $this->CI->tags->parse($string, array(), array($this, 'box_callback'));
		return $parsed['content'];
	}
	
	public function box_callback($path)
	{
		#why is this needed ? :(
		$path['content'] = str_replace(array_keys($path['skip_content']), $path['skip_content'], $path['content']);
		
		switch($path['segments'][0])
		{
			case 'setting':
				break;
			
			case 'module':
				#parse with default template parser
				$data = $this->CI->parser->parse_string($path['content'], array(), true);
		
				return $data;
				break;
			
			default:
				return $this->run($path['segments'], $path['attributes'], $path['content']);
				break;
		}	
	}
	
	/*
	public function parse_plugin($name, $arguments)
	{
	
		if(!is_array($arguments))
		{
			$arguments = explode(';', $arguments);
			
			foreach($arguments as $i = $argument)
				$arguments[$i] = explode('=', $argument);
		}
		
		
	}*/
	
	private function locate($path)
	{
		/**
		* $path looks like this:
		* 	$path = array(
		*		'first',
		*		'second',
		*		'third'
		*	);
		**/
		
		$widget_path = FCPATH.'/widget/';
		$postfix = 'widget.php';
		
		foreach($path as $file)
			if(!file_exists($widget_path.$file))
				throw new exception('Could not locate widget [path] = '.$widget_path.' [file]  = '.$file);
			else
				$widget_path .= $file.'/';
		
		return $widget_path.$postfix;
	}
	
	private function find($path)
	{
		include_once $this->locate($path);
		$classname = ucfirst(end($path)).'_Widget';
		
		return new $classname;
	}
	
	private function run($path, $arguments, $inner)
	{
		$widget = $this->find($path);
		
		return $widget->call($arguments, $inner);
	}	
}
