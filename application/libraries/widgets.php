<?php

class widgets extends CI_Library
{

	public function parse_box($id)
	{
		
	}
	
	public function parse_plugin($name, $arguments)
	{
	
		if(!is_array($arguments))
		{
			$arguments = explode(';', $arguments);
			
			foreach($arguments as $i = $argument)
				$arguments[$i] = explode('=', $argument);
		}
		
		
	}
	
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
		
		foreach($path as $file)
			if(!file_exists($file))
				throw new exception('Could not locate widget')
			else
				$widget_path .= $file.'/';
		
		return $widget_path;
	}
	
	private function find($path)
	{
		$this->locate($path);
		$classname = ucfirst(end($pat)).'_Widget';
		
		return new $classname;
	}
	
	private function run($path, $arguments)
	{
		$widget = find($path);
	}	
}
