<?php

class Stats extends MX_Controller
{
	function __construct()
	{
		parent::__construct();

		$this->load->model('site_settings');
		
		$this->load->helper('url');
		
		$this->load->library('session');
		$this->load->library('auth');
		$this->load->library('parser');
		$this->load->library('template');
		$this->load->library('form');
		$this->load->library('table');
	}
	
	function index()
	{
		$res = $this->db->query('
			SELECT
				users.name,
				
				stats_totals.frags,
				stats_totals.deaths,
				stats_totals.suicides,
				stats_totals.misses,
				stats_totals.shots,
				stats_totals.hits_made,
				stats_totals.hits_get,
				stats_totals.tk_made,
				stats_totals.tk_get,
				stats_totals.flags_returned,
				stats_totals.flags_stolen,
				stats_totals.flags_gone,
				stats_totals.flags_scored,
				stats_totals.total_scored
			FROM
				stats_totals,
				users
			WHERE
				stats_totals.user_id = users.id
			AND
				stats_totals.frags > 0
			ORDER BY
				stats_totals.frags
			DESC');
	
		$this->table->set_heading(array('name', 'frags', 'deaths', 'suicides', 'misses', 'shots', 'hits_made', 'hits_get', 'tk_made', 'tk_get', 'flags_returned', 'flags_Get', 'flags_gone', 'flags_scored', 'total_scored'));
	
		$this->template
			->set_title('Stats - totals')
			->build('contact/contact_view', array('main' => $this->table->generate($res->result_array(), true)));
	}

}
