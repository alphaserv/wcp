<?php
class site_settings extends CI_Model
{
	function get_setting($name, $default)
	{
		static $settings;
		if(!isset($settings))
			$settings = $this->fetch_settings();
		
		if(!isset($settings[$name]))
		{
			$this->add_setting($name, $default);
			$settings[$name] = $default;
		}
		
		return $settings[$name];
	}
	
	function set_setting($name, $value)
	{
		return $this->db->query('
			UPDATE
				web_site
			SET
				web_site.value = ?
			WHERE
				web_site.name = ?', array($this->_parse_value($value), $name));
	}
	
	function add_setting($name, $default, $template_path = '')
	{
		$default = $this->_parse_value($default);
		return $this->db->query('
			INSERT INTO
				web_site
				(
					name,
					value,
					path,
					default_value
				)
			VALUES
				(
					?,
					?,
					?,
					?
				);', array($name, $default, $template_path, $default));
	}
	
	function reset_setting($name)
	{
		return $this->db->query('
			UPDATE
				web_site
			SET
				web_site.value = web_site.default_value
			WHERE
				web_site.name = ?', array($name));
	}
	
	function fetch_settings()
	{
		$result = $this->db->query('
			SELECT
				name,
				value
			FROM web_site;')->result_object();

		$return = array();
		foreach($result as $row)
		{
			if(strpos($row->value, ';') !== false)
				$return[$row->name] = explode(';', $row->value);
			else
				$return[$row->name] = $row->value;
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

function as_setting($name, $default)
{
	static $CI;
	
	if(!isset($CI))
		$CI =& get_instance();
		
	return $CI->site_settings->get_setting($name, $default);
}
