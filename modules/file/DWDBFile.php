<?php

require_once("./includes/IDWDB.php");
require_once("./includes/PEAR/DB.php");

class DWDBFile extends IDWDB
{
	function DWDBFile($dbconnection, $id)
	{
		$this->_table = "files";
		parent::IDWDB($dbconnection);
		if (isset($id)) {
			if (isval($id)) {
				$res =& $dbconnection->query('SELECT * FROM files where id = \' . $id . \'');
				$this->fields =& $res->fetchRow();
			}
		}
	}

}

?>