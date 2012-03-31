<?php

require_once("./includes/DWDBProject.php");

class DWProject
{
	static function add($db, $name, $description)
	{
		if (DWDBProject::exist($db, $name))
		{
			echo "project already exist<br>";
			return false;
		}
		$proadd = new DWDBProject($db, null);
		$proadd->fields['name'] = $name;
		$proadd->fields['description'] = $description;
		$proadd->save();
		mkdir ("./projects/" . $name ."/", 0700);
		if (!copy("./__entry__.php", "./projects/$name/__entry__.php")) {
			echo "failed to copy ...\n";
		}
		return true;
	}
}
?>