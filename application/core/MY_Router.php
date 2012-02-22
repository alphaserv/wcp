<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/* load the MX_Router class */
require APPPATH."third_party/MX/Router.php";

class MY_Router extends MX_Router
{
	public $content_type;
	public $pathstring;
	
	public function _validate_request($segments)
	{
		static $types;
		$this->pathstring = '/';
		if (count($segments) == 0) return $segments;
		
		if(!isset($types)) $types = $this->config->item('supported_types');
		
		$segment_info = pathinfo(end($segments));
		
		$content_type_request = '';
				
		if(!isset($segment_info['extension']) || !isset($types[$segment_info['extension']]))
			$content_type_request = $this->config->item('default_type');
		else
		{
			$content_type_request = $types[$segment_info['extension']];
			$segments[count($segments) - 1] = $segment_info['filename'];
		}
		
		$this->content_type = $content_type_request;
		
		unset ($content_type_request);
		
		$this->pathstring = '/'.implode('/', $segments);
		
		/* locate module controller */
		if ($located = $this->locate($segments)) return $located;
		
		/* use a default 404_override controller */
		if (isset($this->routes['404_override']) AND $this->routes['404_override']) {
			$segments = explode('/', $this->routes['404_override']);
			if ($located = $this->locate($segments)) return $located;
		}
		
		/* no controller found */
		show_404();
	}
}
