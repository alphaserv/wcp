<?php
die('?!');
class MY_Session extends CI_Session
{
	private $CI;
	
	function __construct()
	{
		parent::__construct();
		$this->CI =& get_instance();
	}
	
	function account_data($name = null)
	{
		if(!isset($this->CI->userdata_m))
			$this->CI->load->model('userdata_m');
		
		$this->CI->userdata_m->get_account_data($name);
	}
	function set_account_data($name, $valuable)
	{
		if(!isset($this->CI->userdata_m))
			$this->CI->load->model('userdata_m');
		
		$this->CI->userdata_m->set_account_data($name, $value);
	}
	
	#direct acces api
	function __get($variable)
	{
		if(!isset($this->{$variable}))
			return $this->userdata($variable);
		else
			return $this->{$variable};
	}
	function __set($variable, $value)
	{
		if(!isset($this->{$variable}))
			return $this->set_userdata($variable, $value);
		else
			$this->{$variable} = $value;
	}

	#encryption support (let our users be happy :)
	function _serialize($data)
	{
		if (!is_array($data))
			$data = array($data, 'NOARRAY');
		
		foreach ($data as $key => $val)
		{
			if (is_string($val))
			{
				#prevent "hack attempt"
				$data[$key] = str_replace('{{', '{{epic}}', $val);
				$data[$key] = str_replace('}}', '{{fail}}', $val);
				
				#escape slashes
				$data[$key] = str_replace('\\', '{{slash}}', $val);
			}
		}
		
		if($data[1] == 'NOARRAY')
			$data = $data[0];

		return get_instance()->encrypt->encode(serialize($data));
	}

	function _unserialize($data)
	{
		$data = @unserialize(strip_slashes(get_instance()->encrypt->decode($data)));

		if (!is_array($data))
			$data = array($data, 'NOARRAY');
		
		foreach ($data as $key => $val)
			if (is_string($val))
			{
				$data[$key] = str_replace('{{epic}}', '{{', $val);
				$data[$key] = str_replace('{{fail}}', '}}', $val);
				$data[$key] = str_replace('{{slash}}', '\\', $val);
			}

		if($data[1] == 'NOARRAY')
			$data = $data[0];

		return $data;
	}
}
