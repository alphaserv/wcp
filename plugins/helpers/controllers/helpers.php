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
					
					#closure
					$handler = $this->config->item('lang_handler');
					return $handler($parse['attributes']['string'], ((isset($parse['attributes']['forcelang']) && $parse['attributes']['forcelang'] != 0) ? $parse['attributes']['forcelang'] : null), ((isset($parse['attributes']['nodboverride']) && $parse['attributes']['nodboverride'] == 1) ? true : false));
					/*
					#cache!!!!!
					$this->db->cache_on();
					
					$result = $this->db->query('SELECT id, name, language, value FROM web_lang WHERE name = ? AND language = ?', array($parse['attributes']['string'], 'en'));
						
					if(!$result || $result->num_rows() != 1)
						throw new exception('could not receive lang: "'.$parse['attributes']['string'].'"');
		
					$setting = $result->result_object();
					$setting = $setting[0]->value;
					
					$this->db->cache_off();
					
					return $setting;*/
				break;
			
			case 'url':
				$this->load->helper('url');
				
				if(!isset($parse['segments'][1]))
					$parse['segments'][1] = 'base';
				
				switch($parse['segments'][1])
				{
					default:
					case 'base':
						return base_url($parse['attributes']['url']);
						break;
					case 'site':
						return site_url($parse['attributes']['url']);
						break;
					
					case 'current':
						return current_url();
						break;
						
					case 'string':
						return uri_string();
						break;
						
					case 'index_page':
						return index_page();
						break;
					
					case 'anchor':
					case 'anchor_popup':
					case 'mailto':
					case 'safe_mailto':
					case 'auto_link':
					case 'url_title':
					case 'prep_url':
						throw new exception($parse['segments']['1'].' is not supported yet: TOO_LAZY_TO_IMPLEMENT error');
						break;
					
					case 'redirect':
						throw new exception('Template redirect blocked.');
						break;
					
				}
				
				break;
				
			default:
				throw new exception('could not find hlper type');
				break;
		
		}
	}
}
