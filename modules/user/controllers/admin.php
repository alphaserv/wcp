<?php

class Admin extends MX_Controller
{
	private $data = '';
	
	function __construct()
	{
	
		$this->load->library('parser');
		$this->load->library('template');
		
		$this->data->menu = array();
		$this->data->menu['users'] = array();
		$this->data->menu['groups'] = array();
		$this->data->menu['roles'] = array();
		$this->data->menu['users & roles'] = array();
		$this->data->menu['groups & roles'] = array();
	}
	
	function index()
	{
		$this->data->menu['users'][] = 'selected';
		$this->data->main_content .= '
			main
		';
		
		$this->template
			->set_title('user management')
			->build('adminview', $this->data);
	}
}
