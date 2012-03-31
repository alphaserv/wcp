<?php

abstract class IDWDB
{
	protected $_table;
	protected $_db;
	public $fields = array();
	
	function IDWDB($db)
	{
		$this->_db = $db;
	}
	
	function save()
	{
		if (isset($this->_fields["id"]))
			$res = $this->_db->autoExecute($this->_table, $this->fields,
								DB_AUTOQUERY_UPDATE, "id = " . $this->fields["id"]);
		else
		{
			$this->_fields["id"] = "NULL";
			$res = $this->_db->autoExecute($this->_table, $this->fields,
								DB_AUTOQUERY_INSERT);
		}	

		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
	}
	
	abstract static function exist($db, $id);
}

?>