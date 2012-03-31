<?php

class Clan_m extends CI_Model
{
	function fetch_clantags()
	{
		return $this->db->query('SELECT id, tag FROM clans')->result_object();
	}
	function clan_exists ($clantag)
	{
		$result = $this->db->query('SELECT tag FROM clans WHERE tag = ?', array($clantag));
		
		if(!$result)
			throw new exception('could not receive clan list.');
		
		if($result->num_rows() > 0)
		{
			$result->free_result();
			return true;
		}
		
		return false;
	}
	
	function reservedclantag($name)
	{
		#check if a name uses a reserved clantag
		
		foreach($this->fetch_clantags() as $clan)
			if(preg_match('#'.$clan->tag.'#', $name))
				return $clan->id;
	
		return -1;
	}
	
	function has_reserved_clantag($name)
	{
		return $this->reservedclantag($name);
	}
	
	function is_in_clan($user_id, $clan_id)
	{
		#TODO: create
		return true;
	}
}
