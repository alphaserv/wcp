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
	
	function login()
	{
		$this->load->helper('form');

		$notice = '';
		$info = '';
		if($this->auth->get_current_user()->is_logged_in())
			return $this->template->build('user/login_form', array('notice' => 'you are already logged in !'));
	
		if($_SERVER['REQUEST_METHOD'] == 'POST' && $this->input->post('username') && $this->input->post('password')) #TODO:cross site request check
			if(!$this->auth->login($_POST['username'], $_POST['password']))
				$notice = 'could not login, username or password incorrect';
			else
				$info = 'successfully logged in';
		
		$this->template->build('user/login_form', array('notice' => $notice, 'info' => $info));
	}
	
	function register()
	{
		$this->load->helper('form');
		$this->load->library('form_validation');

		$notice = '';
		$info = '';
		if($this->auth->get_current_user()->is_logged_in())
			return $this->template->build('user/login_form', array('notice' => 'you are already logged in !'));
			
		$this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[5]|max_length[12]|xss_clean');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
		
		$this->form_validation->set_rules('alphaserv_username', 'FULL ingame name', 'trim|required|min_length[5]|max_length[12]|xss_clean');

		$this->form_validation->set_rules('alphaserv_password', 'ingame password', 'trim|required|matches[alphaserv_password2]');
		$this->form_validation->set_rules('alphaserv_password2', 'Retype password', 'trim|required');

		$this->form_validation->set_rules('password', 'Password', 'trim|required|matches[retype_password]');
		$this->form_validation->set_rules('retype_password', 'Retype password', 'trim|required');

		if ($this->form_validation->run('signup') == FALSE)
			$this->template->build('user/register_form', array('notice' => $notice, 'info' => $info));
		else #TODO:try catch user errors
			if($this->auth->register ($this->input->post('email'), $this->input->post('password'), $this->input->post('alphaserv_password'), $this->input->post('username')))
				$this->template->build('user/register_form', array('info' => 'successfully registered'));
	
	}

}
