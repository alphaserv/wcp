<?php

class Userdata_m extends CI_Model
{
	function __construct()
	{
		$this->load->model('user_m');
	}
	
	private function get_user_id()
	{
		return $this->session->userdata('local_user')->id;
	}
	
	public function get_account_data(int $user_id, $variable = null)
	{
		if($variable === null)
			$result = $this->db->query('SELECT `module`, `name`, `data` FROM user_data WHERE `user_id` = ? AND module = \'web\';', array($user_id));
		else
			$result = $this->db->query('SELECT `module`, `name`, `data` FROM user_data WHERE `user_id` = ? AND name = ? AND module = \'web\'', array($user_id, $variable));
		
		if (!$result)
			throw new exception('could not retreive user from database.');
		elseif($result->num_rows() != 1)
			throw new exception('could not query database'); #TODO: make this an user exception?
			
		if($variable !== null)
			return $result->row();
		else
			return $result->result();
	}
	
	/*
		function fetch_userdata($id)
	{
		/ *
			description: function to receive userdata from the database and cache it local
			return mixed:
				array on success
				negative errorcode on failure
			arguments:
				- $id the user id of the user
		* /

		$result = $this->db->query('SELECT `module`, `name`, `data` FROM user_data WHERE `user_id` = ?;', array($id));
		
		#check if errors have occured
		if(!$result)
			return -1;
		
		if ($query->num_rows() < 1)
				return -2; #no results
		
		#initialise the return array
		$return = array();
		
		#loop trough values
		foreach($result->result() as $i => $row)
		{
			#return the data formatted in two ways
			$return[$row->module][$row->name] = $row->data;
			$return['raw'][] = array(
				'module' => $row->module,
				'name' => $row->name,
				'data' => $row->data,
				'i' => $i
			);
		}
		
		#clean up
		$query->free_result();
		
		#return the return array
		return $return;
	}
	function update_userdata($id, $module, $name, $data)
	{
		#description: pretty simple function to update an user field
		return $this->db->query('UPDATE user_data SET data = ? WHERE user_id = ? AND module = ? AND name = ?', array($data, $id, $module, $name));
	}	*/

}
