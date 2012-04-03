<?php
class user_settings extends CI_Model
{
	function get_setting($name, $default, $uid = null)
	{
		if($uid == null)
		{
			$uid = $this->auth->get_current_user()->user_id;
		}
		
		static $settings;
		if(!isset($settings[$uid]))
			$settings[$uid] = $this->fetch_settings($uid);
		
		if(!isset($settings[$uid][$name]))
		{
			$this->add_setting($name, $default, $uid);
			$settings[$uid][$name] = $default;
		}
		
		return $settings[$uid][$name];
	}
	
	function name_by_id($id)
	{
		$res = $this->db->query('SELECT name FROM user_data WHERE id = ?', array($id));
		
		if($res && $res->num_rows() > 0)
			return $res->first_row()->name;
		else
			throw new exception('could not find setting');
	}

	function form_change_setting(&$form, $data)
	{
		try
		{
			$data['name'] = $this->name_by_id($data['id']);
			return $this->set_setting($data['name'], $data['value']);	
		}
		catch(Exception $e)
		{
			$form->add_error($e->getMessage());
			return false;
		}
	}
	
	function set_setting($name, $value, $uid = null)
	{
		if($uid == null)
		{
			$uid = $this->auth->get_current_user()->user_id;
		}
		
		return $this->db->query('
			UPDATE
				user_data
			SET
				user_data.data = ?
			WHERE
				user_data.name = ?
			AND
				user_data.user_id = ?
			AND
				user_data.module = "settings"', array($this->_parse_value($value), $name, $uid));
	}
	
	function add_setting($name, $value, $uid = null)
	{
		if($uid == null)
		{
			$uid = $this->auth->get_current_user()->user_id;
		}
		
		$default = $this->_parse_value($value);
		return $this->db->query('
			INSERT INTO
				user_data
				(
					user_id,
					module,
					name,
					data,
				)
			VALUES
				(
					?,
					"settings",
					?,
					?
				);', array($uid, $name, $value));
	}
	
	
	function fetch_settings($uid = null, $raw = false)
	{
		if($uid == null)
		{
			$uid = $this->auth->get_current_user()->user_id;
		}
		
		$result = $this->db->query('
			SELECT
				id,
				name,
				data AS value
			FROM
				user_data
			WHERE
				user_id = ?
			
			;', array($uid))->result_object();

		$return = array();
		foreach($result as $row)
		{
			if(!$raw)
				if(strpos($row->value, ';') !== false)
					$return[$row->name] = explode(';', $row->value);
				else
					$return[$row->name] = $row->value;
			else
				$return[$row->name] = $row;
		}
		
		return $return;
	}
	
	private function _parse_value($value)
	{
		if(is_array($value))
			return implode(';', $value);
		else
			return $value;
	}
}

function as_user_setting($name, $default, $uid = null)
{
	static $CI;
	
	if(!isset($CI))
		$CI =& get_instance();
		
	return $CI->user_settings->get_setting($name, $default, $uid);
}
