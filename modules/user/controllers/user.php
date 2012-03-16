<?php

class User extends MX_Controller
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
		
	}
	
	function index()
	{
		$this->settings();
	}
	
	function settings()
	{
		if(!$this->auth->get_current_user()->is_logged_in())
		{
			header('Refresh: 5;URL='.site_url('user/login'));
			show_error('please login before changing usersettings. redirection in 5 seconds');
		}
		else
		{
			$uid = $this->auth->get_current_user()->get_user_id();
			$user = $this->user_m->get_user($uid);
			
			$this->form
				->open('user/settings')

				->fieldset('Basic account settings')
				->text('username', 'your username', 'trim|required|min_length[2]|max_length[12]|xss_clean', $user->name)
				->text('email', 'your email adress', 'trim|required|valid_email', $user->email)

				->text('ingame_pass', 'your ingame password', 'trim|required|min_length[3]', $user->pass)
				
				->fieldset('Change your password')
				->password('pass', 'your password', 'trim|required')
				->password('pass_retype', 'retype your password', 'trim|required|matches[pass]')
			
			->submit()
			
			->fieldset('ingame names');
			
			foreach($this->user_m->get_names($uid) as $name)
				$this->form->html('<div>'.$name->name.' '. anchor('user/nickname/update/'.$name->id, 'Change'). ' '. anchor('user/nickname/delete/'.$name->id, 'Delete').'</div>');


		$data['form'] = $this->form->get();
		$data['errors'] = $this->form->errors;
		
		$this->template
			->set_title('Login')
			->add_head('<link href="'.base_url('static/form.css').'" rel="stylesheet" type="text/css" />')
			->build('contact/contact_view', $data);
		}
	}
	
	function nickname($action, $id)
	{
		$name = $this->user_m->get_name($id);
	
		if($action == 'delete')
		{
			$this->form
				->open($this->router->uri->uri_string)
				->text('alphaserv_username', 'your <strong class="uppercase">full</strong> ingame name', 'trim|required|min_length[2]|max_length[12]|xss_clean', $name->name)
		
		}
	}
	
	function login()
	{

		$this->form
			->open('user/login')
			->text('username', 'your username', 'trim|required')
			->password('password', 'your password', 'trim|required');

		if(1 == (int)$this->site_settings->get_setting('login_captcha', '0'))
			$this->form->recaptcha('Please enter the captcha code');
		
		$this->form
			->model('user_form_m', 'login')
			->onsuccess('redirect', '/')
			->submit();

		$data['form'] = $this->form->get();
		$data['errors'] = $this->form->errors;
			
		$this->template
			->set_title('Login')
			->add_head('<link href="'.base_url('static/form.css').'" rel="stylesheet" type="text/css" />')
			->build('contact/contact_view', $data);
	}
	
	function register()
	{
		$this->form
			->open('user/register')
			->text('username', 'your username', 'trim|required|min_length[2]|max_length[12]|xss_clean')
			->text('email', 'your email adress', 'trim|required|valid_email')

			->text('alphaserv_username', 'your <strong class="uppercase">full</strong> ingame name', 'trim|required|min_length[2]|max_length[12]|xss_clean')
			->text('alphaserv_password', 'your ingame password', 'trim|required|min_length[3]')
			->text('alphaserv_password2', 'your ingame password', 'trim|required|min_length[3]|matches[alphaserv_password]')
			
			->password('password', 'Password', 'trim|required')
			->password('password2', 'Password', 'trim|required|matches[password]');

		if(1 == (int)$this->site_settings->get_setting('register_captcha', '1'))
			$this->form->recaptcha('Please enter the captcha code');
		
		$this->form
			->model('user_form_m', 'register')
			#->onsuccess('redirect', '/user/login')
			->submit();

		$data['form'] = $this->form->get();
		$data['errors'] = $this->form->errors;
			
		$this->template
			->set_title('Login')
			->add_head('<link href="'.base_url('static/form.css').'" rel="stylesheet" type="text/css" />')
			->build('contact/contact_view', $data);
	
	}
	
	function activate($key = -1)
	{
	
		if($key == -1)
		{
			$this->form
				->open('user/activate')
				->text('key', 'the key wich you received by mail', 'trim|required')
				->model('user_form_m', 'activate')
				#->onsuccess('redirect', '/user/login')
				->submit();

			$data['form'] = $this->form->get();
			$data['errors'] = $this->form->errors;
			
			$this->template
				->set_title('Activate your account')
				->add_head('<link href="'.base_url('static/form.css').'" rel="stylesheet" type="text/css" />')
				->build('contact/contact_view', $data);
		}
		else
		{
			try
			{
				$this->user_m->set_activation($key);
				$this->user_m->check_activations();
			}
			catch(Exception $e)
			{
				show_error($e->getMessage());
			}
		}
	}

}
