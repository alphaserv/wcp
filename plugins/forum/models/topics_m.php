<?php

class Topics_m extends CI_Model
{

	function __construct()
	{
		parent::__construct();
	}
	
	function get_topics($show_hidden = false, $offset = array(0, 30))
	{
		$sql = '
			SELECT
				forum_topics.id,
				forum_topics.title,
				forum_topics.description,
				forum_topics.hidden,
				forum_topics.creation_date,
				forum_topics.lastmessage,
				forum_topics.category_id,
				users.name AS starter,
				forum_categories.name as category
			
			FROM
				forum_topics,
				users,
				forum_categories
				
			WHERE
				forum_topics.category_id = forum_categories.id ';
			
		$sql .= ($show_hidden)? '' : 'AND forum_topics.hidden = b\'0\'';
			
		$sql .= '
			AND
				forum_topics.starter_id = users.id
				
			LIMIT '.((int)$offset[0]).', '.((int)$offset[1]);
			
			
		return $this->db->query($sql)->result_object();
	}

	function get_topic($id)
	{
		$sql = '
			SELECT
				forum_topics.id,
				forum_topics.title,
				forum_topics.description,
				forum_topics.hidden,
				forum_topics.creation_date,
				forum_topics.lastmessage,
				forum_topics.category_id,
				users.name AS starter,
				forum_categories.name as category
			
			FROM
				forum_topics,
				users,
				forum_categories
				
			WHERE
				forum_topics.id = ?
				
			AND
				forum_topics.category_id = forum_categories.id
				
			AND
				forum_topics.starter_id = users.id';
			
		return $this->db->query($sql)->result_object();	
	}
	
	function get_topics_from_category($category_id, $show_hidden = false, $offset = array(0, 30))
	{
		$sql = '
			SELECT
				forum_topics.id,
				forum_topics.title,
				forum_topics.description,
				forum_topics.hidden,
				forum_topics.creation_date,
				forum_topics.lastmessage,
				users.name AS starter,
				forum_categories.name as category
			
			FROM
				forum_topics,
				users,
				forum_categories
				
			WHERE
				forum_topics.category_id = forum_categories.id 
			AND
				forum_topics.category_id = ?
			';
			
		$sql .= ($show_hidden)? '' : 'AND forum_topics.hidden = b\'0\'';
			
		$sql .= '
			AND
				forum_topics.starter_id = users.id
				
			LIMIT '.((int)$offset[0]).', '.((int)$offset[1]);
			
			
		return $this->db->query($sql, array($category_id))->result_object();
	}
	
	function newtopic(&$form, $data)
	{
		$form->add_error('subject', 'you SUCK');
		print_r($data);
		
		$sql = 'INSERT INTO forum_topics
				(
					id,
					title,
					description,
					starter_id,
					hidden,
					creation_date,
					lastmessage,
					category_id
				)
				VALUES
				(
					NULL,
					?,
					?,
					?,
					b'0',
					NOW(),
					NOW(),
					?
				);';
		
		$this->db->query($sql, array($data['title'], (trim($data['description']) == '')? substr($data['message'], 0, 20) : $data['description'], $this->auth->get_current_user()->get_user_id()));
	}	
	
	
}
