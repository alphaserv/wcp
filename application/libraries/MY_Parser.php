<?php defined('BASEPATH') or die('No direct script access allowed');

class MY_Parser extends CI_Parser {

	private $CI;
	
	private $data;

	function add_data($data)
	{
		if (!is_array($data))
			$data = (array) $data;
		
		$this->data = array_merge($data, (array)$this->data);
		
		return $this;
	}

	function __construct()
	{
		$this->CI =& get_instance();
	}
	
	function parse($file, $data = array(), $return = false, $is_include = false)
	{
		$string = $this->CI->load->view($file, $data, true);
		return $this->parse_string($string, $data = array(), $return, $is_include = false);
	}
	function parse_string($string, $data = array(), $return = false, $is_include = false)
	{
		$this->CI->benchmark->mark('parse_string_start');
		if (!is_array($data))
			$data = (array) $data;

		//get view variables
		$data = array_merge($data, $this->CI->load->_ci_cached_vars);
		$this->data = array_merge($data, (array)$this->data);

		foreach($this->data as $key => $value)
			if($value == null) $data[$key] = '';#bugfix replace null with ''
		
		//load tag library
		$this->CI->load->library('tags');
		
		//tag prefix
		$this->CI->tags->set_trigger('as:');
		$parsed = $this->CI->tags->parse($string, $data, array($this, 'parser_callback'));

		// echo results?
		if (!$return)
			$this->CI->output->append_output($parsed['content']);
		else
			return $parsed['content'];
	}
	
	function parser_callback($path)
	{
		if(defined('OMG_DEBUG'))
		{
			echo '----------[ DEBUG ]----------'.PHP_EOL;
			print_r($path);
			echo '----------[ DEBUG ]----------'.PHP_EOL;		
		}
		
		$this->CI->benchmark->mark('callback: '.implode(':', $path['segments']));
		
		#recursive parse inner
		if(!empty($path['content']))
			$path['parsed_content'] = $this->parse_string($path['content'], $this->data, true);
			
			#try if the variable is an array
		elseif(isset($this->data[$path['segments'][0]]))
		{
			$data = $this->data;

			foreach($path['segments'] as $segment)
			{
				if(defined('OMG_DEBUG')) 
					echo PHP_EOL.'segment: '.$segment.PHP_EOL;
					
				if(array_key_exists($segment, $data))
					if(end($path['segments']) != $segment)
						$data = (array)$data[$segment];
					elseif(isset($path['attributes']['type']))
						switch($path['attributes']['type'])
						{
							case 'isempty':
								$data = empty($data[$segment]);
							break;
							
							case 'bool':
								$data = (bool)$data[$segment];
							break;

							case 'int':
								$data = (int)$data[$segment];
							break;
						
							default:
							case 'string':
								$data = (string)$data[$segment];
							break;
						}
					else
						$data = $data[$segment];
					
				elseif(defined('OMG_DEBUG'))
					echo PHP_EOL.$segment.' not found! '.print_r($data, true).' see: '.$data[$segment].'!';
			}

			return (string)$data;

		}
		elseif($path['segments'][0] == 'widget')
		{
			$this->CI->load->library('widgets');
			
			if(isset($path['segments'][1]))
				$id = $path['segments'][1];
			elseif(isset($path['attributes']['id']))
				$id = $path['attributes']['id'];
			else
				throw new exception('box without id');
			
			return (string) $this->CI->widgets->parse_box($id);
		}
		
		$controller = modules::load(implode('/', $path['segments']));
		
		ob_start();//catch echos TODO:fix
		$return = '';
		if(!is_object($controller))
			throw new exception('could not find controller: '.$path['full_tag']);
//		$controller->__construct();
		elseif(method_exists($controller, '_tagcall'))
			$return .= $controller->_tagcall($path);
		elseif(method_exists($controller, '_remap'))
			$return .= $controller->_remap();//TODO
		else
			$return .= call_user_func_array(array($controller, end($path['segments'])), $path['attributes']);
		$return .= ob_get_contents();
		ob_end_clean();
		
		#parse returned content
		$return = $this->parse_string($return, $this->data, true);
		
		$this->CI->benchmark->mark('end of callback');
		
		return $return;
	}
}
/*
class tag_std
{
	function if_($a)
	{
		if($a);
	}
}
*/
