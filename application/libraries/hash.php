<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Hash {
	public function hash($code = false)
	{
		if (!$code) return;
		#thanks to http://www.phphulp.nl/ for the idea
		
		$this->CI =& get_instance();
		
		$array = str_split($code); #string => array
		$string = '';
		foreach($array as $row)
			$string .= sha1($this->CI->config->item('salt').$code.$this->CI->config->item('pepper'));
		
		$string = md5($string);
		return $string;
	}
	public static function s_hash($code)
	{
		$res = new Hash();
		return $res->hash($code);
	}
}
