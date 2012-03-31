<?php

require_once("./includes/PEAR/DB.php");

Class DWDB {
	private $_dbname;
	private $_dbuser;
	private $_dbpass;
	private $_dbtype;
	private $_dbhost;
	private $_dbconnection;
	
	function DWDB($dbtype, $dbhost, $dbuser, $dbpass, $dbname)
	{
		$this->_dbtype = $dbtype;
		$this->_dbhost = $dbhost;
		$this->_dbuser = $dbuser;
		$this->_dbpass = $dbpass;
		$this->_dbname = $dbname;
		
		$dsn = array(
				'phptype'  => $this->_dbtype,
			    'username' => $this->_dbuser,
				'password' => $this->_dbpass,
				'hostspec' => $this->_dbhost,
				'database' => $this->_dbname,
		);

		$options = array(
					'debug'       => 2,
					'portability' => DB_PORTABILITY_ALL,
		);
		
		$dbconnection =& DB::connect($dsn, $options);
		if (PEAR::isError($dbconnection)) {
			die($dbconnection->getMessage());
		}
		$this->_dbconnection = $dbconnection;
	}
	function getConnection() {
		return ($this->_dbconnection);
	}
	
	
}

?>