<?php

require_once("./includes/IDWDB.php");
require_once("./includes/PEAR/DB.php");

class DWDBRevisionFileLink extends IDWDB
{
	function DWDBRevisionFileLink($dbconnection, $id)
	{
		$this->_table = "files";
		parent::IDWDB($dbconnection);
		if (isset($id)) {
			if (isval($id)) {
				$res =& $dbconnection->query('SELECT * FROM revisions_files_link where revisions_id = \' . $id . \'');
				while ($res->fetchInto($row)) {
					$this->fields[$i] = $row['files_id'];
					$i++;
				}
			}
		}
	}

}

?>