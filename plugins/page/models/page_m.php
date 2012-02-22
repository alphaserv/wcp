<?php

class page_not_found_exception extends Exception {}

class page_m extends CI_Model
{
	function get_page($path)
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
		
		return $this->checkrefer($result->first_row(), false);
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
