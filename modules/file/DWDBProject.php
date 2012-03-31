<?php

require_once("./includes/IDWDB.php");
require_once("./includes/PEAR/DB.php");

class DWDBProject extends IDWDB
{
	function DWDBProject($dbconnection, $id)
	{
		$this->_table = "projects";
		parent::IDWDB($dbconnection);
		if (isset($id)) {
			if (is_int($id))
				$res =& $dbconnection->query('SELECT * FROM projects where id = \' . $id . \'');
			else
				$res =& $dbconnection->query('SELECT * FROM projects where name = \' . $id . \'');
			$this->fields =& $res->fetchRow();
		}
	}
	
	static function exist($db, $id)
	{
		if (is_int($id))
			$res =& $db->query('SELECT * FROM projects where id = \'' . $id . '\'');
		else
			$res =& $db->query('SELECT * FROM projects where name = \'' . $id . '\'');
		if ($res->numRows() > 0)
			return true;
		return false;
		
	}
	
}

?>