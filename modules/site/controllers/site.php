<?php

class Site extends MX_Controller
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
		
		#cache!!!!!
		$this->db->cache_on();
			$result = $this->db->query('SELECT id, name, value, path FROM web_site WHERE path = ?', array(implode('::', $parse['segments'])));
		
			if(!$result || $result->num_rows() != 1)
				throw new exception('could not receive setting: "'.implode('::', $parse['segments']).'"');
		
			$setting = $result->result_object();
			$setting = $setting[0]->value;
		
		$this->db->cache_off();
		
		if(defined('OMG_DEBUG'))
			print_r($parse);
		return $setting;
	}
}
