<?php

class Posts_m extends CI_Model
{
	function get_messages($topic_id)
	{
		return $this->db->query('
			SELECT
				forum_messages.id,
				forum_messages.date,
				forum_messages.topic_id,
				forum_messages.subject,
				forum_messages.body,
				users.name
			
			FROM
				forum_messages,
				users
			
			WHERE
				forum_messages.topic_id = ?
			
			AND
				forum_messages.user_id = users.id;
			', array($topic_id))->result_object();
	}

}
