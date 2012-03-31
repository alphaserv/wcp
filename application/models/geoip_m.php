<?php

class Geoip_m extends CI_Model
{

	function ip_to_country_code($ip = null)
	{
		if($ip == null) $ip = $this->input->ip_address();
		
		$ulip = sprintf("%u", ip2long($ip));
		
		$result = $this->db->get_where('ref_iptocountry', array('ip_from <' => $ulip, 'ip_to >' => $ulip));
		
		if($row = $query->row())
		{
			return $row->country_code;
		}
	}
}
