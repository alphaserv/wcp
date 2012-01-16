<?php

class page_m extends CI_Model
{
	function gethomepage()
	{
		$result = $this->db->query('SELECT `id`, `uri`, `title`, `min_priv`, `revision`, `content`, `edit_priv`, `date`, BIN(`is_homepage`) as `is_homepage` FROM web_pages WHERE is_homepage = "1" LIMIT 1');
		
		if(!$result)
			throw new exception('could not retrieve page from database, internal error');
		
		if($result->num_rows() != 1)
			throw new exception('could not retrieve page from database, no homepage set');
		
		return $result->first_row();
	}
	
	function getpage($title, $id = null)
	{
		if(!$title && $id !== (int)$id)
			throw new exception('getpage requires a title argument or an id argument');
		
		if($title)
			$result = $this->db->query('SELECT `id`, `uri`, `title`, `min_priv`, `revision`, `content`, `edit_priv`, `date`, BIN(`is_homepage`) as `is_homepage` FROM web_pages WHERE title = ? ORDER BY revision DESC LIMIT 1', array($id));
		else
			$result = $this->db->query('SELECT `id`, `uri`, `title`, `min_priv`, `revision`, `content`, `edit_priv`, `date`, BIN(`is_homepage`) as `is_homepage` FROM web_pages WHERE id = ? LIMIT 1', array($id));

		if(!$result)
			throw new exception('could not retrieve page from database, internal error');
		
		if($result->num_rows() != 1)
			throw new exception('could not retrieve page from database, no homepage set');
		
		return $result->first_row();
	}
	function search($data)
	{
		$data = '"%'.$this->db->escape_like_str($data).'%"';
		
		if(!$title && $id !== (int)$id)
			throw new exception('getpage requires a title argument or an id argument');
		
		$result = $this->db->query('SELECT `id`, `uri`, `title`, `min_priv`, `revision`, `content`, `edit_priv`, `date`, BIN(`is_homepage`) as `is_homepage` FROM web_pages WHERE uri LIKE '.$data.' OR title LIKE '.$data.' OR content LIKE '.$data);
		
		if(!$result)
			throw new exception('could not retrieve page from database, internal error');
		
		if($result->num_rows() != 1)
			throw new exception('could not retrieve page from database, no homepage set');
		
		return $result->result_object();
	}
}
