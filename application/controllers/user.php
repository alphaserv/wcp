<?php

class User extends MX_Controller
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->library('auth');
		$this->load->library('parser');
		$this->load->library('template');
	}
	function index()
	{
		$this->login();
	}
	
	function login($username = '')
	{
		$this->load->helper('form');

		$notice = '';
		$info = '';
		if($this->auth->get_current_user()->is_logged_in())
			return $this->template->build('user/login_form', array('notice' => 'you are already logged in !'));
	
		if($_SERVER['REQUEST_METHOD'] == 'POST') #TODO:cross site request check
			if(!$this->auth->login($_POST['username'], $_POST['password']))
				$notice = 'could not login, username or password incorrect';
			else
				$info = 'successfully logged in';
		
		$this->template->build('user/login_form', array('notice' => $notice, 'info' => $info));
	}

}
