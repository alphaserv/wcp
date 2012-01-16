<?php

class Helpers extends MX_Controller
{
	function __construct()
	{
		$this->load->database();
	}
	function _remap()
	{
		show_404();
	}
	function _tagcall($parse)
	{
		#remove segment #0
		$parse['segments'][0] = null;
		unset($parse['segments'][0]);
		
		#let array start @ 0
		$parse['segments'] = array_values($parse['segments']);
		
		switch($parse['segments'][0])
		{
			case 'lang':
					if(defined('OMG_DEBUG'))
						print_r($parse['attributes']);
					
					#cache!!!!!
					$this->db->cache_on();
					
					$result = $this->db->query('SELECT id, name, language, value FROM web_lang WHERE name = ? AND language = ?', array($parse['attributes']['string'], 'en'));
						
					if(!$result || $result->num_rows() != 1)
						throw new exception('could not receive lang: "'.$parse['attributes']['string'].'"');
		
					$setting = $result->result_object();
					$setting = $setting[0]->value;
					
					$this->db->cache_off();
					
					return $setting;
				break;
			
			default:
				throw new exception('could not find hlper type');
				break;
		
		}
	}
}
