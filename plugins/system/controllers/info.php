<?php

class Info extends MX_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('system_m');
	}
	
	function index()
	{
		echo $this->system_m;	
	}

}
