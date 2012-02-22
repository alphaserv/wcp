<?php

class Auth_install
{
	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->database();
		$this->CI->load->dbforge();
	}
	
	function install()
	{
		$this->install_tables();
		$this->install_data();
	}
	
	function install_tables()
	{
		$tables = include dirname(__file__).'/default_tables.php';
		
		foreach($tables as $name => $table)
		{
			$meta = array();
			
			if(isset($table['META']))
			{
				$meta = $table['META'];
				unset($table['META']);
			}
			
			$this->CI->dbforge->add_field($table);
			
			if(isset($meta['KEYS']))
				foreach($meta['KEYS'] as $field => $keytype)
					switch($keytype)
					{
						case 'PRIMARY':
							$this->CI->dbforge->add_key($field, true);
							break;
							
						case 'UNIQUE':
						case 'KEY':
							$this->CI->dbforge->add_key($field)
							break;
					}

			
			$this->CI->dbforge->create_table($name);
			
			unset($meta);
		}
	}
	
	function install_data()
	{
		$data = include dirname(__file__).'/default_data.php';
		
		foreach($data as $tablename => $table)
			$this->CI->db->insert_batch($tablename, $table); 
	}
	
	function uninstall()
	{
		$this->backup();
		$this->uninstall_data();
		$this->uninstall_tables();
	}
	
	function uninstall_tables()
	{
		$tables = include dirname(__file__).'/default_tables.php';
		
		foreach($tables as $tablename => $_)
			$this->CI->dbforge->drop_table($tablename);
	}

	function uninstall_data()
	{
		$tables = include dirname(__file__).'/default_tables.php';
		
		foreach($tables as $tablename => $_)
			$this->CI->db->truncate($tablename);
	}
	
	function backup()
	{
		$this->CI->load->dbutil();

		$backup =& $this->CI->dbutil->backup(); 
/*
		$this->CI->load->helper('file');
		write_file(dirname(__file__).'/backup.gz', $backup); 
*/		
		$this->load->helper('download');
		force_download('backup.gz', $backup);
	}
}
