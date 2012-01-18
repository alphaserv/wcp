<?php
class Language
{
	static $user_lang;
	
	function __destruct()
	{
		if(defined('OMG_DEBUG'))
		{
			$CI =& get_instance();
			echo $CI->session->userdata('language_code');
			print_r($CI->config->item('lang_handler'));
			print_r($CI->config->item('load_lang_handler'));
		}
	}
	function __construct()
	{
   		$CI =& get_instance();
		$CI->load->library('session');
		$supported = $CI->config->item('supported_languages');


		#enable $_GET
		parse_str($_SERVER['QUERY_STRING'], $_GET);
		
		if(($lang = $CI->session->userdata('language_code')) === false || isset($_GET['changelanguage']))
		{
			#try to detect language
			
			#lang in url as ?lang=*LANGUAGE*
			if(isset($_GET['lang']) && !empty($_GET['lang']))
				$lang = substr($_GET['lang'], 0, 2);
			
			#language posted in a form
			elseif(($lang = $CI->input->post('lang')) !== false)
				$lang = substr($lang, 0, 2);
			
			#language in a cookie
			elseif(($lang = $CI->input->cookie('lang')) !== false)
				$lang = substr($lang, 0, 2);
			
			#no other options, check headers
			elseif(($lang = $CI->input->server('HTTP_ACCEPT_LANGUAGE')) !== false)
			{
				$accept = explode(',', $lang);
				$ok = false;
				foreach ($accept as $lang)
				{
					$lang = substr($lang, 0, 2);

					// Check its in the array. If so, break the loop, we have one!
					if(isset($supported[$lang]))
					{
						$ok = true;
						break;
					}
				}
				if(!$ok)
					$lang = $this->lang_from_geoip();
    		}
    		else
    			$lang = $this->lang_from_geoip();
    			
    		if(!$lang || empty($lang) || !isset($supported[$lang]))
    			$lang = $CI->config->item('default_language');
    		
    		$CI->session->set_userdata('language_code', $lang);
    	}
    	
    	#create lang object TODO:make this more simple to use
    	
    	$function = function($key, $forcelang = null, $nodboverride = false) use($lang, $supported)
    	{
    		$CI =& get_instance();
    		
    		$clang = ($forcelang == null) ? $lang : $forcelang;
			
			$string = $CI->lang->line($key);
			
			if($supported[$clang]['use_database'] && !$nodboverride)
			{
				#cache!!!!!
				$CI->db->cache_on();
					
					$result = $CI->db->query('SELECT value FROM web_lang WHERE name = ? AND language = ?', array($key, $clang));
						
					if(!$result)
						throw new exception('dberror on language');
					elseif($result->num_rows() == 1)
					{
						$row = $result->row();
						$string = $row->value;
					}

				$CI->db->cache_off();
			}
			
			if(isset($supported[$clang]['string'][$key]))
				$string = $supported[$clang]['string'][$key];
				
			return trim($string);    	
    	};
    	    	
    	$CI->config->set_item('lang_handler', $function);
    	
    	$load_function = function($file) use($lang, $supported)
    	{
    		return $this->lang->load($file, ((isset($supported[$lang]['folder_name'])) ? $supported[$lang]['folder_name'] : $lang));
    	};
    	
    	$CI->config->set_item('load_lang_handler', $load_function);
    }
    
	function lang_from_geoip()
	{
		#TODO: implement
		return 'en';
	}
}
