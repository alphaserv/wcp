<?php

class page_not_found_exception extends Exception {}

class page_m extends CI_Model
{
	function get_page($path, $mayrefer = false)
	{
		$result = $this->db->query('
			SELECT
				id,
				uri,
				title,
				content,
				date,
				makeup,
				BIN(public) as public
			FROM
				web_pages
			WHERE
				uri = ?
			AND
				public = b\'1\'
			LIMIT
				1;', array($path));
		
		if(!$result)
			throw new exception('could not retrieve page from database, internal error');	
		
		if($result->num_rows() != 1)
			throw new page_not_found_exception;
		
		if($mayrefer)
			return $this->checkrefer($result->first_row(), false);
		else
			return $result->first_row();
	}
	
	function get_page_by_id($id, $mayrefer = true)
	{
		$result = $this->db->query('
			SELECT
				id,
				uri,
				title,
				content,
				date,
				makeup,
				BIN(public) as public
			FROM
				web_pages
			WHERE
				id = ?
			AND
				public = b\'1\'
			LIMIT
				1;', array($id));
		
		if(!$result)
			throw new exception('could not retrieve page from database, internal error');	
		
		if($result->num_rows() != 1)
			throw new page_not_found_exception;
		
		if($mayrefer)
			return $this->checkrefer($result->first_row(), false);
		else
			return $result->first_row();
	}
	
	function checkrefer(&$element, $relocate = true)
	{
		$content = explode(':', $element->content);
		if(isset($content[0]) && $content[0] == 'REFER' && isset($content[0]))
			if($relocate)
			{
				redirect($content[1], 301);
				exit;
			}
			else
				return $this->get_page($content[1]);
		else
			return $element;
	}
}
