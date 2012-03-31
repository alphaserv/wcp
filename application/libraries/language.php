<?php
class Language
{
	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->database();
    }
    function detect_language()
    {
		$this->CI->load->database();
		$this->CI->load->library('session');


		#enable $_GET
		parse_str($this->CI->input->server('QUERY_STRING'), $_GET);
		
		if(($lang = $this->CI->session->userdata('language_code')) === false || isset($_GET['changelanguage']) || isset($_GET['lang']))
		{
			#try to detect language

			#lang in url as ?lang=*LANGUAGE*
			if(isset($_GET['lang']) && !empty($_GET['lang']))
				$lang = substr($_GET['lang'], 0, 2);
			
			#language posted in a form
			elseif(($lang = $this->CI->input->post('lang')) !== false)
				$lang = substr($lang, 0, 2);
			
			#language in a cookie
			elseif(($lang = $this->CI->input->cookie('lang')) !== false)
				$lang = substr($lang, 0, 2);
			
			#no other options, check headers
			elseif(($lang = $this->CI->input->server('HTTP_ACCEPT_LANGUAGE')) !== false)
			{
				$accept = explode(',', $lang);
				$ok = false;
				foreach ($accept as $lang)
				{
					$lang = substr($lang, 0, 2);

					#Check its in the array. If so, break the loop, we have one!
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

   			
    		if(!$lang || empty($lang) || !$this->is_supported_language($lang))
    			$lang = $this->CI->config->item('default_language');

    		$this->CI->session->set_userdata('language_code', $lang);
    	}
    	
    	return $lang;
    }
    
    function is_supported_language($code)
    {
    	#TODO: database
		static $supported;
		
		if(!isset($supported))
			$supported = $this->CI->config->item('supported_languages');
		
		return isset($supported[$code]);
    }
    
    function get_lang_code()
    {
    	static $language;
    	
    	if(!isset($language))
    		$language = $this->detect_language();
    		
    	return $language;
    }
    
    function get_string($key, $default, $noinstall = false)
    {
    	static $strings;
    	$lang = $this->get_lang_code();

    	if(!isset($strings))
		{
			$result = $this->CI->db->query('SELECT name, value FROM web_lang WHERE language = ?', array($lang))->result_object();

			foreach($result as $row)
				$strings[$row->name] = $row->value;
		}
		
		if(!isset($strings[$key]))
		{
			$result = $this->CI->db->query('SELECT name, value FROM web_lang WHERE name = ? AND language = "en"', array($key));
			
			if($result->num_rows() < 1 && !$noinstall)
			{
				$result = $this->CI->db->query('
					INSERT INTO
						web_lang
						(
							name,
							value,
							language
						)
					VALUES
						(
							?,
							?,
							?
						);', array($key, $default, 'en'));
				return $default;
			}
			elseif($result->num_rows() > 0)
			{
				return $result->first_row()->value;
			}
			else
				return false;
		}
		else
			return $strings[$key];
    }
    
	function lang_from_geoip()
	{
		#$this->CI->load->model('geoip_m');
		#return $this->CI->geoip_m->ip_to_country_code();
		return 'en';
	}
}

function lang_string($name, $default)
{
	static $CI;
	if(!isset($CI))
		$CI =& get_instance();
	
	return $CI->language->get_string($name, $default);
}
