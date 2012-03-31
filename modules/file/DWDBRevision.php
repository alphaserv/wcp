<?php

require_once("./includes/IDWDB.php");
require_once("./includes/PEAR/DB.php");

class DWDBRevision extends IDWDB
{
	function DWDBRevision($dbconnection, $id, $idproject, $idrevision)
	{
		$this->_table = "files";
		parent::IDWDB($dbconnection);
		if (isset($id)) {
			if (isval($id)) {
				$res =& $dbconnection->query('SELECT * FROM revisions where id = \' . $id . \'');
				$this->fields =& $res->fetchRow();
			}
		}
		if ((!isset($id)) && (isset($idproject)) && (isset($idrevision))) {
				$res =& $dbconnection->query('SELECT * FROM revisions where revision = \' . $idrevision . \' and projects_id = \' . $idproject . \'');
				$this->fields =& $res->fetchRow();
	
		}
	}
	
}

?>