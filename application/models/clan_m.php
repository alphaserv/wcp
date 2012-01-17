<?php

class Clan_m
{
	function clan_exists ($clantag)
	{
		$result = $this->db->query('SELECT `tag` FROM `clans` WHERE `tag` = ?', array($clantag));
		
		if(!$result)
			throw new exception('could not receive clan list.');
		
		if($result->num_rows() > 0)
		{
			$result->free_result();
			return true;
		}
		
		return false;
	}
	
	function reservedclantag()
	{
		#check if a name uses a reserved clantag
		#TODO
	}
}
