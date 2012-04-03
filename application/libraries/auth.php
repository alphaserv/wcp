<?php

class Auth
{
	private $CI;
	
	function __construct()
	{
		$this->CI =& get_instance();

		$this->CI->load->model('user_m');
		$this->CI->load->model('userdata_m');
		$this->CI->load->library('user_lib');
		
		$this->CI->load->library('parser');
		
		$this->current_user =& $this->CI->user_lib->get_current_user();
		
		$this->CI->parser->add_data(array('auth' => &$this));
		
		$this->get_current_user()->init($this);
	}
	
	function &get_user(int $id)
	{
		return $this->CI->user_lib->get_user($id);
	}
	
	function &get_current_user()
	{
		return $this->current_user;
	}
	
	function get_access(string $actionname, int $user_id = null)
	{
		if($user_id === null)
			$user_id =& User_lib::$currentuser->get_user_id();
		
		$access = (int)$this->CI->user_m->get_access_to($actionname, $user_id);
		
		#if($access #TODO:finish
	}

	function login($username, $pass)
	{
		if ($this->checkbanned())
			throw new exception('cannot login, you are banned');
			
		$return = $this->CI->user_m->try_login($username, $pass);
		if(!$return[0])
			return false;
		
		$this->CI->user_lib->change_user(new user_($return[1]));
		return true;
	}
	function logout()
	{
		$user =& $this->get_current_user();
		
		if(!$user->logged_in)
			throw new exception('already logged out'); #TODO: make user error
			
		$user->destroy();
		unset($user); #destroy
		$user = new user_(-1);
		
		$this->CI->user_lib->clear();
	}
	function register ($email, $pass, $as_pass, $username)
	{
		$priv = (int)$this->CI->config->item('default_priv');
		$activation = (int)$this->CI->config->item('activation_type');
		
		$this->CI->db->trans_begin(); #for testing registering functionality
		
		$res = $this->CI->user_m->register ($email, $pass, $as_pass, $username, $activation, $priv);

		$this->CI->db->trans_status();
		if($res['activation_type'] == user_m::ACTIVATION_TYPE_EMAIL || $res['activation_type'] == user_m::ACTIVATION_TYPE_BOTH)
			$this->send_activation_email($email, $res['key']);
		
		$this->CI->db->trans_commit();#commit queries here
		return true;
	}
	
	function send_activation_email($email, $key)
	{
		$this->CI->load->library('email');

		#clear everything to make shure no conflicts will occur
		$this->CI->email->clear(true);

		#support for noobish os (microsucks winsucks)
		$this->CI->email->set_newline("\r\n");

		#set where the email is from
		$this->CI->email->from($this->CI->config->item('mail_from'), $this->CI->config->item('mail_from_name'));
		
		#set the reply to adress if not the same
		if ($this->CI->config->item('mail_drom') != $this->CI->config->item('mail_reply_to'))
			$this->CI->email->reply_to($this->CI->config->item('mail_reply_to'), $this->CI->config->item('mail_reply_to_name'));
		
		#set where to send the email to
		$this->CI->email->to($email);
		
		#set the subject of the message
		$this->CI->email->subject($this->CI->config->item('mail_activation_subject'));
		
		#load the message from an view
		$this->CI->email->message($this->CI->load->view('mail/activation', array( 'key' => $key), true));
		
		#load an alternative message for email clients wich don't support html formated email
		$this->CI->email->set_alt_message($this->CI->load->view('mail/alt_activation', array( 'key' => $key), true));
		
		#send the email
		if(!$this->CI->email->send())
		{
			$this->CI->db->trans_rollback();
			log_error('error', $this->CI->email->print_debugger());
			throw new exception('could not mail');
		}	
		
		return true;
	
	}
	
	function activate ($key)
	{
		$a = $this->CI->user_m->activate($key);
		if ($a === true && $this->CI->user_m->check_activations(5))
			return true;
	}

	function checkbanned()
	{
		/*
			description: function to check if an user is ipbanned
			return boolean:
				true when banned
				false when not banned
			arguments:
				none
		*/
		#TODO: add check query ere
		return false;
	}
	function cron()#cronjob
	{
		$this->CI->user_m->check_activations(50);
	}
}
