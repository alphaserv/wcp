<?php

class System_m extends CI_model
{
	public $os;
	public $host;
	public $php_version;
	public $php_version_info;
	public $cpu_type;
	
	public $mem;
	public $mempeak;
	
	public $memlimit;

	public $real_mem;
	public $real_mempeak;
	
	public $user;	
	
	public $pid;
	
	public $version;
	public $zend_version;
	
	function __construct()
	{
		$this->update();
	}

	function update()
	{
		$this->os = php_uname('s');
		$this->host = php_uname('n');
		$this->php_version = php_uname('r');
		$this->php_version_info = php_uname('v');
		$this->cpu_type = php_uname('m');
		
		$this->mem = memory_get_usage();
		$this->mempeak = memory_get_peak_usage();

		$this->memlimit = ini_get('memory_limit');

		$this->real_mem = memory_get_usage(true);
		$this->real_mempeak = memory_get_peak_usage(true);
		
		$this->user = get_current_user();
		
		$this->pid = getmypid();
		
		$this->version = phpversion();
		$this->zend_version = zend_version();
		
		$this->php_settings = $this->php_settings();
	}
	
	function _recursive_row($array)
	{
		$return = '';
		foreach($array as $setting => $value)
		{
			if(is_array($value))
			{
				$return .='<tr><td>'.$setting.' (array)</td><td>';
				$return .= '<table>';
					
				$return .= $this->_recursive_row($value);
				
				$return .= '</table>';
				$return .= '</td></tr>';
			}
			else
				$return .= '<tr><td>'.$setting.'</td><td>'.$value.'</td></tr>';	
		}
		
		return $return;
	}
	
	function __tostring()
	{
		
		$array = (array) $this;
		
		$return = '<table>';
		$return .= $this->_recursive_row($array);
		return $return.'</table>';
	}
	
	function php_settings()
	{
		return ini_get_all();
	}
}

