<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Output extends CI_Output
{
	function encode_output($output, $type)
	{
		switch($type)
		{
			case 'json':
				$this->set_content_type('text/json');
				return json_encode($output);
				break;
			
			default:
				print_r(func_get_args());
				break;
		}
	}
}

